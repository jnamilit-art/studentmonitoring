<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementPostedNotification extends Notification
{
    public function __construct(public Announcement $announcement, public bool $wasUpdated = false)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $date = optional($this->announcement->date)->format('F d, Y') ?? now()->format('F d, Y');

        return (new MailMessage)
            ->subject('School announcement: '.$this->announcement->title)
            ->greeting('Hello,')
            ->line($this->wasUpdated
                ? 'A school announcement was updated on '.$date.'.'
                : 'A new school announcement was posted on '.$date.'.')
            ->line($this->announcement->title)
            ->line($this->announcement->content)
            ->action('View announcements', url('/parent/announcements'))
            ->line('Thank you for staying connected with the school.');
    }
}
