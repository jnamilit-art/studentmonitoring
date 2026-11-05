<?php

namespace App\Notifications;

use App\Models\Attendance;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceLoggedNotification extends Notification
{
    public function __construct(public Attendance $attendance)
    {
        $this->attendance->loadMissing('student');
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $studentName = $this->attendance->student?->name ?? 'your child';
        $date = optional($this->attendance->date)->format('F d, Y') ?? 'today';
        $status = $this->attendance->status;

        $subject = match ($status) {
            'Absent' => "{$studentName} was marked absent on {$date}",
            'Tardy' => "{$studentName} was marked tardy on {$date}",
            default => "Attendance update for {$studentName}",
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello,')
            ->line("An attendance record was logged for {$studentName}.")
            ->line('Date: '.$date)
            ->line('Status: '.$status)
            ->action('View attendance', url('/parent/attendance'))
            ->line('Please contact the school if you have questions about this record.');
    }
}
