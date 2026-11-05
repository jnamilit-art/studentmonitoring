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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class ParentNotifier
{
    public function gradePosted(Grade $grade): void
    {
        $grade->loadMissing('student');

        $this->sendToParent($grade->student, new GradePostedNotification($grade));
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

        try {
            Notification::route('mail', $email)->notify($notification);
        } catch (Throwable $exception) {
            Log::warning('Failed to send parent email notification.', [
                'email' => $email,
                'notification' => $notification::class,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
