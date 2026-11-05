<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Behavior;
use App\Models\Student;
use App\Services\ParentNotifier;
use Illuminate\Http\Request;

class BehaviorController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'note' => 'required|string',
        ]);

        $behavior = Behavior::create([
            'student_id' => $request->student_id,
            'date' => $request->date,
            'note' => $request->note,
            'teacher' => $request->user()?->name ?? 'Teacher',
        ]);

        app(ParentNotifier::class)->behaviorLogged($behavior);

        return redirect()->route('teacher.behavior')->with('success', 'Behavior note logged successfully. Parent email sent.');
    }

    public function edit(Behavior $behavior): Response
    {
        $students = Student::orderBy('name')->get();

        return Inertia::render('Admin/BehaviorEdit', [
            'behavior' => [
                'id' => $behavior->id,
                'student_id' => $behavior->student_id,
                'date' => optional($behavior->date)->format('Y-m-d'),
                'note' => $behavior->note,
            ],
            'students' => $students->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->name,
            ]),
        ]);
    }

    public function update(Request $request, Behavior $behavior)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'note' => 'required|string',
        ]);

        $behavior->update($request->only('student_id', 'date', 'note'));

        app(ParentNotifier::class)->behaviorLogged($behavior);

        return redirect()->route('teacher.behavior')->with('success', 'Behavior note updated successfully. Parent email sent.');
    }

    public function destroy(Behavior $behavior)
    {
        $behavior->delete();
        return redirect()->route('teacher.behavior')->with('success', 'Behavior note deleted successfully.');
    }
}

