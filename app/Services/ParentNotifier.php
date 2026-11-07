<?php

namespace App\Services;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Behavior;
use App\Models\Grade;
use App\Models\Student;
use App\Notifications\AnnouncementPostedNotification;
use App\Notifications\AttendanceLoggedNotification;
use App\Notifications\BehaviorLoggedNotification;
use App\Notifications\GradePostedNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class ParentNotifier
{
    public function gradePosted(Grade $grade): void
    {
        $grade->loadMissing('student');

        if (! $grade->student) {
            return;
        }

        // Send notification to Parent
        if ($parentEmail = $grade->student->parent_email) {
            $this->sendToEmail(strtolower(trim((string) $parentEmail)), new GradePostedNotification($grade, false));
        }

        // Send notification to Student / Child
        if ($studentEmail = $grade->student->student_email) {
            $this->sendToEmail(strtolower(trim((string) $studentEmail)), new GradePostedNotification($grade, true));
        }
    }

    public function attendanceLogged(Attendance $attendance): void
    {
        $attendance->loadMissing('student');

        $this->sendToParent($attendance->student, new AttendanceLoggedNotification($attendance));
    }

    public function behaviorLogged(Behavior $behavior): void
    {
        $behavior->loadMissing('student');

        $this->sendToParent($behavior->student, new BehaviorLoggedNotification($behavior));
    }

    public function announcementPosted(Announcement $announcement): void
    {
        $this->sendAnnouncementToParents($announcement);
    }

    public function announcementUpdated(Announcement $announcement): void
    {
        $this->sendAnnouncementToParents($announcement, true);
    }

    private function sendAnnouncementToParents(Announcement $announcement, bool $wasUpdated = false): void
    {
        $school = app()->bound('current_school') ? app('current_school') : null;

        $emails = Student::query()
            ->when($school, fn ($query) => $query->where('school_id', $school->id))
            ->whereNotNull('parent_email')
            ->pluck('parent_email')
            ->map(fn ($email) => strtolower(trim((string) $email)))
            ->filter()
            ->unique()
            ->values();

        foreach ($emails as $email) {
            $this->sendToEmail($email, new AnnouncementPostedNotification($announcement, $wasUpdated));
        }
    }

    private function sendToParent(?Student $student, object $notification): void
    {
        if (! $student) {
            return;
        }

        $email = strtolower(trim((string) $student->parent_email));

        $this->sendToEmail($email, $notification);
    }

    private function sendToEmail(string $email, object $notification): void
    {
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        // 1. If BREVO_API_KEY is configured, send via Brevo's HTTPS API (Port 443)
        // This is 100% reliable on Render, where raw SMTP ports (587, 465, 25) are blocked
        $brevoApiKey = env('BREVO_API_KEY');
        if (! empty($brevoApiKey) && method_exists($notification, 'toMail')) {
            try {
                /** @var MailMessage $mailMessage */
                $mailMessage = $notification->toMail((object) ['email' => $email]);
                $html = (string) $mailMessage->render();

                $fromEmail = config('mail.from.address', env('MAIL_FROM_ADDRESS', 'justingeonamilit@gmail.com'));
                $fromName = config('mail.from.name', env('MAIL_FROM_NAME', 'Student Monitoring System'));

                $response = Http::withHeaders([
                    'api-key' => $brevoApiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->timeout(12)->post('https://api.brevo.com/v3/smtp/email', [
                    'sender' => [
                        'name' => $fromName,
                        'email' => $fromEmail,
                    ],
                    'to' => [
                        ['email' => $email],
                    ],
                    'subject' => $mailMessage->subject,
                    'htmlContent' => $html,
                ]);

                if ($response->successful()) {
                    Log::info('Email sent successfully via Brevo HTTPS API.', [
                        'email' => $email,
                        'subject' => $mailMessage->subject,
                        'message_id' => $response->json('messageId'),
                    ]);
                    return;
                }

                Log::warning('Brevo API response error: ' . $response->body(), ['email' => $email]);
            } catch (Throwable $e) {
                Log::warning('Failed sending via Brevo API: ' . $e->getMessage(), ['email' => $email]);
            }
        }

        // 2. Standard Laravel Notification fallback
        try {
            Notification::route('mail', $email)->notify($notification);
        } catch (Throwable $exception) {
            Log::warning('Failed to send email notification.', [
                'email' => $email,
                'notification' => $notification::class,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
