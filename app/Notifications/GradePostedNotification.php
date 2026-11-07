<?php

namespace App\Notifications;

use App\Models\Grade;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GradePostedNotification extends Notification
{
    public function __construct(
        public Grade $grade,
        public bool $isStudent = false
    ) {
        $this->grade->loadMissing('student');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $studentName = $this->grade->student?->name ?? 'Student';

        $periodLabels = [
            1 => '1st Grading Period',
            2 => '2nd Grading Period',
            3 => '3rd Grading Period',
            4 => '4th Grading Period',
        ];
        $periodText = $periodLabels[(int) $this->grade->grading_period] ?? 'Grading Period '.$this->grade->grading_period;

        $greeting = $this->isStudent
            ? "Hello {$studentName},"
            : "Hello Parent / Guardian of {$studentName},";

        $introLine = $this->isStudent
            ? "Your teacher has updated your grade in the Student Monitoring System:"
            : "A teacher has posted an updated grade for {$studentName} in the Student Monitoring System:";

        return (new MailMessage)
            ->subject("Grade Update: {$studentName} — {$this->grade->subject} ({$this->grade->grade})")
            ->greeting($greeting)
            ->line($introLine)
            ->line("**Student Name:** {$studentName}")
            ->line("**Subject:** {$this->grade->subject}")
            ->line("**Period:** {$periodText}")
            ->line("**Grade:** {$this->grade->grade}")
            ->action('View Academic Records', url('/parent/grades'))
            ->line('If you have any questions regarding this grade, please reach out to your school or teacher.')
            ->salutation('Best regards, School Monitoring System');
    }
}
