<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_email' => 'required|email',
            'student_email' => 'nullable|email',
            'grade_level' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $school = app()->bound('current_school') ? app('current_school') : null;
        $schoolId = $school?->id ?? $user?->school_id;

        if (! $schoolId && $request->session()->has('active_school_subdomain')) {
            $sessionSubdomain = $request->session()->get('active_school_subdomain');
            $sessionSchool = \App\Models\School::whereRaw('LOWER(subdomain) = ?', [strtolower($sessionSubdomain)])->first();
            $schoolId = $sessionSchool?->id;
        }

        if (! $schoolId && \App\Models\School::count() === 1) {
            $schoolId = \App\Models\School::first()->id;
        }

        $gradeLevel = $request->grade_level ?: $user?->grade_level;

        $student = Student::create([
            'name' => $request->name,
            'parent_email' => $request->parent_email,
            'student_email' => $request->student_email,
            'grade_level' => $gradeLevel,
            'school_id' => $schoolId,
        ]);

        // If parent user exists and has no school_id, link them to the student's school
        if ($schoolId && $request->parent_email) {
            \App\Models\User::whereRaw('LOWER(TRIM(email)) = ?', [strtolower(trim($request->parent_email))])
                ->whereNull('school_id')
                ->update(['school_id' => $schoolId]);
        }

        return redirect()->route('teacher.students')->with('success', 'Student added successfully.');
    }

    public function edit(Student $student): Response
    {
        return Inertia::render('Admin/StudentsEdit', [
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'parent_email' => $student->parent_email,
                'student_email' => $student->student_email,
                'grade_level' => $student->grade_level,
            ],
            'gradeLevels' => \App\Models\GradeLevel::orderBy('title')->pluck('title'),
        ]);
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_email' => 'required|email',
            'student_email' => 'nullable|email',
            'grade_level' => 'nullable|string|max:255',
        ]);

        $student->update([
            'name' => $request->name,
            'parent_email' => $request->parent_email,
            'student_email' => $request->student_email,
            'grade_level' => $request->grade_level ?? $student->grade_level,
        ]);

        return redirect()->route('teacher.students')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('teacher.students')->with('success', 'Student deleted successfully.');
    }
}
