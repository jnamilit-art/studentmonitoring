<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminGradeLevelController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BehaviorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/admin', function () {
    return redirect()->route('super_admin.login');
})->name('admin.redirect');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'role:parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('/dashboard', [ParentController::class, 'dashboard'])->name('dashboard');
    Route::get('/grades', [ParentController::class, 'grades'])->name('grades');
    Route::get('/attendance', [ParentController::class, 'attendance'])->name('attendance');
    Route::get('/behavior', [ParentController::class, 'behavior'])->name('behavior');
    Route::get('/announcements', [ParentController::class, 'announcements'])->name('announcements');
});

Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
    Route::get('/announcements', [TeacherController::class, 'manageAnnouncements'])->name('announcements');
    Route::get('/grades', [TeacherController::class, 'manageGrades'])->name('grades');
    Route::get('/attendance', [TeacherController::class, 'manageAttendance'])->name('attendance');
    Route::get('/behavior', [TeacherController::class, 'manageBehavior'])->name('behavior');
    Route::get('/students', [TeacherController::class, 'manageStudents'])->name('students');

    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    Route::post('/grades', [GradeController::class, 'store'])->name('grades.store');
    Route::get('/grades/{grade}/edit', [GradeController::class, 'edit'])->name('grades.edit');
    Route::put('/grades/{grade}', [GradeController::class, 'update'])->name('grades.update');
    Route::delete('/grades/{grade}', [GradeController::class, 'destroy'])->name('grades.destroy');

    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::put('/attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

    Route::post('/behavior', [BehaviorController::class, 'store'])->name('behavior.store');
    Route::get('/behavior/{behavior}/edit', [BehaviorController::class, 'edit'])->name('behavior.edit');
    Route::put('/behavior/{behavior}', [BehaviorController::class, 'update'])->name('behavior.update');
    Route::delete('/behavior/{behavior}', [BehaviorController::class, 'destroy'])->name('behavior.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/teachers', [AdminController::class, 'teachers'])->name('teachers');
    Route::get('/grade-levels', [AdminController::class, 'gradeLevels'])->name('grade-levels');
    Route::put('/grade-levels/{gradeLevel}', [AdminGradeLevelController::class, 'update'])->name('grade-levels.update');
    Route::delete('/grade-levels/{gradeLevel}', [AdminGradeLevelController::class, 'destroy'])->name('grade-levels.destroy');
});

Route::middleware(['auth', 'role:super_admin'])->prefix('super-admin')->name('super_admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/schools', [SuperAdminController::class, 'schools'])->name('schools');
    Route::post('/schools', [SuperAdminController::class, 'storeSchool'])->name('schools.store');
    Route::put('/schools/{school}', [SuperAdminController::class, 'updateSchool'])->name('schools.update');
    Route::delete('/schools/{school}', [SuperAdminController::class, 'destroySchool'])->name('schools.destroy');
    Route::get('/schools/{school}', [SuperAdminController::class, 'schoolDetails'])->name('school.details');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
