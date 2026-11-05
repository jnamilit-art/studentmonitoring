<?php

namespace App\Notifications;

use App\Models\Grade;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GradePostedNotification extends Notification
{
    public function __construct(public Grade $grade)
    {
        $this->grade->loadMissing('student');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $studentName = $this->grade->student?->name ?? 'your child';

        return (new MailMessage)
            ->subject("New grade posted for {$studentName}")
            ->greeting('Hello,')
            ->line("A grade was posted for {$studentName} in the Student Monitoring System.")
            ->line('Subject: '.$this->grade->subject)
            ->line('Grading period: '.$this->grade->grading_period)
            ->line('Grade: '.$this->grade->grade)
            ->action('View grades', url('/parent/grades'))
            ->line('If you did not expect this message, you can ignore it.');
    }
}
