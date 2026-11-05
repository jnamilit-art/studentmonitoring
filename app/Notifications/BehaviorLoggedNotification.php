<?php

namespace App\Notifications;

use App\Models\Behavior;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BehaviorLoggedNotification extends Notification
{
    public function __construct(public Behavior $behavior)
    {
        $this->behavior->loadMissing('student');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $studentName = $this->behavior->student?->name ?? 'your child';
        $date = optional($this->behavior->date)->format('F d, Y') ?? 'today';
        $teacher = $this->behavior->teacher ?: 'a teacher';

        return (new MailMessage)
            ->subject("Behavior note for {$studentName}")
            ->greeting('Hello,')
            ->line("A behavior note was added for {$studentName} on {$date}.")
            ->line('Teacher: '.$teacher)
            ->line('Note: '.$this->behavior->note)
            ->action('View behavior notes', url('/parent/behavior'))
            ->line('Please log in for full details and previous notes.');
    }
}
