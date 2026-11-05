<?php

use App\Models\Student;
use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('parent registration screen can be rendered', function () {
    $response = $this->get('/parent/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('parents can register and link a student', function () {
    $student = Student::create([
        'name' => 'Linked Student',
        'parent_email' => 'old-parent@example.com',
    ]);

    $response = $this->post('/parent/register', [
        'name' => 'Parent User',
        'email' => 'parent@example.com',
        'student_id' => $student->id,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    expect(User::where('email', 'parent@example.com')->value('role'))->toBe('parent');
    expect($student->fresh()->parent_email)->toBe('parent@example.com');
});
