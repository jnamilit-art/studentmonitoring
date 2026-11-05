<?php

use App\Models\School;
use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('authenticated users are redirected from role login pages to their dashboard', function (string $role, string $loginPath, string $dashboardRoute) {
    $user = User::factory()->create(['role' => $role]);

    $response = $this->actingAs($user)->get($loginPath);

    $response->assertRedirect(route($dashboardRoute));
})->with([
    ['parent', '/parent/login', 'parent.dashboard'],
    ['teacher', '/teacher/login', 'teacher.dashboard'],
    ['admin', '/admin/login', 'admin.dashboard'],
]);

test('each role login only authenticates its matching account type', function (string $role, string $loginPath, string $dashboardRoute) {
    $user = User::factory()->create(['role' => $role]);

    $response = $this->post($loginPath, [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route($dashboardRoute));
})->with([
    ['parent', '/parent/login', 'parent.dashboard'],
    ['teacher', '/teacher/login', 'teacher.dashboard'],
    ['admin', '/admin/login', 'admin.dashboard'],
]);

test('teacher login replaces a parent session and opens the teacher dashboard', function () {
    $parent = User::factory()->create(['role' => 'parent']);
    $teacher = User::factory()->create(['role' => 'teacher']);

    $response = $this->actingAs($parent)->post('/teacher/login', [
        'email' => $teacher->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($teacher);
    $response->assertRedirect(route('teacher.dashboard'));
});

test('super admins remain in the super admin area', function () {
    $superAdmin = User::factory()->create(['role' => 'super_admin']);

    $this->actingAs($superAdmin)
        ->get('/dashboard')
        ->assertRedirect(route('super_admin.dashboard'));

    $this->actingAs($superAdmin)->get('/parent/dashboard')->assertForbidden();
    $this->actingAs($superAdmin)->get('/teacher/dashboard')->assertForbidden();
    $this->actingAs($superAdmin)->get('/admin/dashboard')->assertForbidden();
});

test('super admins can open their school management pages', function () {
    $superAdmin = User::factory()->create(['role' => 'super_admin']);
    $school = School::create([
        'name' => 'Test School',
        'subdomain' => 'test-school',
    ]);

    $this->actingAs($superAdmin)
        ->get(route('super_admin.schools'))
        ->assertOk();

    $this->actingAs($superAdmin)
        ->get(route('super_admin.school.details', $school))
        ->assertOk();
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users return to their role login page when they logout', function (string $role, string $loginRoute) {
    $user = User::factory()->create(['role' => $role]);

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect(route($loginRoute));
})->with([
    ['parent', 'parent.login'],
    ['teacher', 'teacher.login'],
    ['admin', 'admin.login'],
]);
