<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Grade;
use App\Models\Student;
use App\Services\ParentNotifier;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject' => 'required|string',
            'grading_period' => 'required|integer|between:1,4',
            'grade' => 'required|numeric|min:0|max:100',
        ]);

        $grade = Grade::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'subject' => $request->subject,
                'grading_period' => $request->grading_period,
            ],
            [
                'grade' => $request->grade,
            ]
        );

        app(ParentNotifier::class)->gradePosted($grade);

        return redirect()->route('teacher.grades')->with('success', 'Grade updated successfully. Parent email sent.');
    }

    public function edit(Grade $grade): Response
    {
        $students = Student::orderBy('name')->get();

        return Inertia::render('Admin/GradesEdit', [
            'grade' => [
                'id' => $grade->id,
                'student_id' => $grade->student_id,
                'subject' => $grade->subject,
                'grading_period' => (int) $grade->grading_period,
                'grade' => $grade->grade,
            ],
            'students' => $students->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->name,
            ]),
        ]);
    }

    public function update(Request $request, Grade $grade)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject' => 'required|string',
            'grading_period' => 'required|integer|between:1,4',
            'grade' => 'required|numeric|min:0|max:100',
        ]);

        $grade->update([
            'student_id' => $request->student_id,
            'subject' => $request->subject,
            'grading_period' => $request->grading_period,
            'grade' => $request->grade,
        ]);

        app(ParentNotifier::class)->gradePosted($grade);

        return redirect()->route('teacher.grades')->with('success', 'Grade updated successfully. Parent email sent.');
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();
        return redirect()->route('teacher.grades')->with('success', 'Grade deleted successfully.');
    }
}

