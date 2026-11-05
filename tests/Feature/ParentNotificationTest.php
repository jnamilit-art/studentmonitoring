<?php

use App\Models\Student;
use App\Models\User;
use App\Notifications\AnnouncementPostedNotification;
use App\Notifications\AttendanceLoggedNotification;
use App\Notifications\BehaviorLoggedNotification;
use App\Notifications\GradePostedNotification;
use Illuminate\Support\Facades\Notification;

test('posting a grade emails the parent gmail', function () {
    Notification::fake();

    $teacher = User::factory()->create(['role' => 'teacher']);
    $student = Student::create([
        'name' => 'Ana Santos',
        'parent_email' => 'parent.ana@gmail.com',
    ]);

    $this->actingAs($teacher)
        ->post(route('teacher.grades.store'), [
            'student_id' => $student->id,
            'subject' => 'Math',
            'grading_period' => 1,
            'grade' => 92,
        ])
        ->assertRedirect(route('teacher.grades'));

    Notification::assertSentOnDemand(GradePostedNotification::class, function ($notification, $channels, $notifiable) {
        return $notifiable->routes['mail'] === 'parent.ana@gmail.com'
            && $notification->grade->subject === 'Math';
    });
});

test('logging an absence emails the parent gmail', function () {
    Notification::fake();

    $teacher = User::factory()->create(['role' => 'teacher']);
    $student = Student::create([
        'name' => 'Luis Cruz',
        'parent_email' => 'parent.luis@gmail.com',
    ]);

    $this->actingAs($teacher)
        ->post(route('teacher.attendance.store'), [
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'status' => 'Absent',
        ])
        ->assertRedirect(route('teacher.attendance'));

    Notification::assertSentOnDemand(AttendanceLoggedNotification::class, function ($notification, $channels, $notifiable) {
        return $notifiable->routes['mail'] === 'parent.luis@gmail.com'
            && $notification->attendance->status === 'Absent';
    });
});

test('logging behavior emails the parent gmail', function () {
    Notification::fake();

    $teacher = User::factory()->create(['role' => 'teacher']);
    $student = Student::create([
        'name' => 'Mia Reyes',
        'parent_email' => 'parent.mia@gmail.com',
    ]);

    $this->actingAs($teacher)
        ->post(route('teacher.behavior.store'), [
            'student_id' => $student->id,
            'date' => now()->toDateString(),
            'note' => 'Excellent participation in class.',
        ])
        ->assertRedirect(route('teacher.behavior'));

    Notification::assertSentOnDemand(BehaviorLoggedNotification::class, function ($notification, $channels, $notifiable) {
        return $notifiable->routes['mail'] === 'parent.mia@gmail.com';
    });
});

test('posting an announcement emails parent gmail accounts', function () {
    Notification::fake();

    $teacher = User::factory()->create(['role' => 'teacher']);
    Student::create([
        'name' => 'Ana Santos',
        'parent_email' => 'shared.parent@gmail.com',
    ]);
    Student::create([
        'name' => 'Luis Cruz',
        'parent_email' => 'shared.parent@gmail.com',
    ]);

    $this->actingAs($teacher)
        ->post(route('teacher.announcements.store'), [
            'title' => 'Holiday schedule',
            'content' => 'Classes resume on Monday.',
        ])
        ->assertRedirect(route('teacher.announcements'));

    Notification::assertSentOnDemandTimes(AnnouncementPostedNotification::class, 1);
});

test('updating an announcement emails parent accounts', function () {
    Notification::fake();

    $teacher = User::factory()->create(['role' => 'teacher']);
    $announcement = \App\Models\Announcement::create([
        'title' => 'Original schedule',
        'content' => 'Classes are on Friday.',
        'date' => now(),
    ]);
    Student::create([
        'name' => 'Ana Santos',
        'parent_email' => 'parent.ana@gmail.com',
    ]);

    $this->actingAs($teacher)
        ->put(route('teacher.announcements.update', $announcement), [
            'title' => 'Updated schedule',
            'content' => 'Classes are now on Monday.',
        ])
        ->assertRedirect(route('teacher.announcements'));

    Notification::assertSentOnDemand(AnnouncementPostedNotification::class, function ($notification, $channels, $notifiable) {
        return $notifiable->routes['mail'] === 'parent.ana@gmail.com'
            && $notification->wasUpdated
            && $notification->announcement->title === 'Updated schedule';
    });
});
