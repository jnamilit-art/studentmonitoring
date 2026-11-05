<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SuperAdminController extends Controller
{
    public function dashboard(): Response
    {
        $schools = School::withCount(['users', 'students'])
            ->orderBy('name')
            ->get();

        $totalSchools = $schools->count();
        $activeSchools = $schools->where('is_active', true)->count();
        $totalUsers = User::count();
        $totalStudents = Student::count();

        return Inertia::render('SuperAdmin/Dashboard', [
            'stats' => [
                'total_schools' => $totalSchools,
                'active_schools' => $activeSchools,
                'total_users' => $totalUsers,
                'total_students' => $totalStudents,
            ],
            'schools' => $schools->map(fn (School $school) => [
                'id' => $school->id,
                'name' => $school->name,
                'subdomain' => $school->subdomain,
                'address' => $school->address,
                'phone' => $school->phone,
                'email' => $school->email,
                'is_active' => $school->is_active,
                'users_count' => $school->users_count,
                'students_count' => $school->students_count,
                'created_at' => $school->created_at->format('Y-m-d H:i:s'),
            ]),
        ]);
    }

    public function schools(): Response
    {
        $schools = School::withCount(['users', 'students'])
            ->orderBy('name')
            ->get();

        return Inertia::render('SuperAdmin/Schools', [
            'schools' => $schools->map(fn (School $school) => [
                'id' => $school->id,
                'name' => $school->name,
                'subdomain' => $school->subdomain,
                'address' => $school->address,
                'phone' => $school->phone,
                'email' => $school->email,
                'is_active' => $school->is_active,
                'users_count' => $school->users_count,
                'students_count' => $school->students_count,
                'created_at' => $school->created_at->format('Y-m-d H:i:s'),
            ]),
        ]);
    }

    public function storeSchool(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:50|alpha_dash|unique:schools,subdomain',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        School::create($validated);

        return back()->with('success', 'School created successfully.');
    }

    public function updateSchool(Request $request, School $school): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'subdomain' => 'sometimes|required|string|max:50|alpha_dash|unique:schools,subdomain,'.$school->id,
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $school->update($validated);

        return back()->with('success', 'School updated successfully.');
    }

    public function destroySchool(School $school): \Illuminate\Http\RedirectResponse
    {
        $school->delete();

        return back()->with('success', 'School deleted successfully.');
    }

    public function schoolDetails(School $school): Response
    {
        $school->load(['users', 'students']);

        return Inertia::render('SuperAdmin/SchoolDetails', [
            'school' => [
                'id' => $school->id,
                'name' => $school->name,
                'address' => $school->address,
                'phone' => $school->phone,
                'email' => $school->email,
                'is_active' => $school->is_active,
                'created_at' => $school->created_at->format('Y-m-d H:i:s'),
            ],
            'users' => $school->users->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->effective_role,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
            ]),
            'students' => $school->students->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'parent_email' => $student->parent_email,
                'created_at' => $student->created_at->format('Y-m-d H:i:s'),
            ]),
        ]);
    }
}
