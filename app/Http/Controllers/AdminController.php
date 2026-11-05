<?php

namespace App\Http\Controllers;

use App\Models\GradeLevel;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function dashboard(): Response
    {
        $user = auth()->user();
        $currentSchool = app()->bound('current_school') ? app('current_school') : null;

        // If on a school subdomain, use that school; otherwise use user's assigned school
        $school = $currentSchool ?? ($user->school_id ? School::find($user->school_id) : null);

        // Get users for this school only
        $usersQuery = User::query();
        if ($school) {
            $usersQuery->where('school_id', $school->id);
        }
        $users = $usersQuery->orderBy('name')->get();

        // Get students for this school only
        $studentsQuery = Student::query();
        if ($school) {
            $studentsQuery->where('school_id', $school->id);
        }
        $students = $studentsQuery->count();

        return Inertia::render('Admin/Dashboard', [
            'school' => $school ? [
                'id' => $school->id,
                'name' => $school->name,
                'subdomain' => $school->subdomain,
            ] : null,
            'counts' => [
                'parents' => $users->filter->isParent()->count(),
                'teachers' => $users->filter->isTeacher()->count(),
                'students' => $students,
            ],
        ]);
    }

    public function users(): Response
    {
        $user = auth()->user();
        $currentSchool = app()->bound('current_school') ? app('current_school') : null;
        $school = $currentSchool ?? ($user->school_id ? School::find($user->school_id) : null);

        $usersQuery = User::query();
        if ($school) {
            $usersQuery->where('school_id', $school->id);
        }
        $users = $usersQuery->orderBy('name')->get();

        return Inertia::render('SuperAdmin/Users', [
            'users' => $users->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->effective_role,
                'stored_role' => $user->role,
            ]),
        ]);
    }

    public function teachers(): Response
    {
        $user = auth()->user();
        $currentSchool = app()->bound('current_school') ? app('current_school') : null;
        $school = $currentSchool ?? ($user->school_id ? School::find($user->school_id) : null);

        $gradeLevels = GradeLevel::query()
            ->orderBy('title')
            ->pluck('title')
            ->values();

        $teachersQuery = User::query()->where('role', 'teacher');
        if ($school) {
            $teachersQuery->where('school_id', $school->id);
        }
        $teachers = $teachersQuery->orderBy('name')
            ->get(['id', 'name', 'email', 'grade_level'])
            ->map(fn (User $teacher) => [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'email' => $teacher->email,
                'grade_level' => $teacher->grade_level,
            ])
            ->values();

        return Inertia::render('SuperAdmin/Teachers', [
            'teachers' => $teachers,
            'gradeLevels' => $gradeLevels,
        ]);
    }

    public function gradeLevels(): Response
    {
        $user = auth()->user();
        $currentSchool = app()->bound('current_school') ? app('current_school') : null;
        $school = $currentSchool ?? ($user->school_id ? School::find($user->school_id) : null);

        $gradeLevels = GradeLevel::query()
            ->orderBy('title')
            ->get()
            ->map(fn (GradeLevel $gradeLevel) => [
                'id' => $gradeLevel->id,
                'title' => $gradeLevel->title,
                'teachers' => User::query()
                    ->where('role', 'teacher')
                    ->where('grade_level', $gradeLevel->title)
                    ->when($school, fn ($query) => $query->where('school_id', $school->id))
                    ->orderBy('name')
                    ->get(['id', 'name', 'email'])
                    ->map(fn (User $teacher) => [
                        'id' => $teacher->id,
                        'name' => $teacher->name,
                        'email' => $teacher->email,
                    ])
                    ->values(),
            ])
            ->values();

        $unassignedTeachersQuery = User::query()
            ->where('role', 'teacher')
            ->where(function ($query) {
                $query->whereNull('grade_level')->orWhere('grade_level', '');
            });
        if ($school) {
            $unassignedTeachersQuery->where('school_id', $school->id);
        }
        $unassignedTeachers = $unassignedTeachersQuery->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(fn (User $teacher) => [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'email' => $teacher->email,
            ])
            ->values();

        return Inertia::render('SuperAdmin/GradeLevels', [
            'gradeLevels' => $gradeLevels,
            'unassignedTeachers' => $unassignedTeachers,
        ]);
    }
}
