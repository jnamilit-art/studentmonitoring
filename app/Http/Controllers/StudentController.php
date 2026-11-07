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
        ]);

        $school = app()->bound('current_school') ? app('current_school') : null;

        Student::create([
            'name' => $request->name,
            'parent_email' => $request->parent_email,
            'student_email' => $request->student_email,
            'school_id' => $school?->id ?? $request->user()?->school_id,
        ]);

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
            ],
        ]);
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_email' => 'required|email',
            'student_email' => 'nullable|email',
        ]);

        $student->update([
            'name' => $request->name,
            'parent_email' => $request->parent_email,
            'student_email' => $request->student_email,
        ]);

        return redirect()->route('teacher.students')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('teacher.students')->with('success', 'Student deleted successfully.');
    }
}
