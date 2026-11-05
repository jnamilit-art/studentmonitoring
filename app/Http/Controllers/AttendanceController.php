<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Attendance;
use App\Models\Student;
use App\Services\ParentNotifier;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:Present,Absent,Tardy',
        ]);

        $attendance = Attendance::create($request->only('student_id', 'date', 'status'));

        app(ParentNotifier::class)->attendanceLogged($attendance);

        return redirect()->route('teacher.attendance')->with('success', 'Attendance logged successfully. Parent email sent.');
    }

    public function edit(Attendance $attendance): Response
    {
        $students = Student::orderBy('name')->get();

        return Inertia::render('Admin/AttendanceEdit', [
            'attendance' => [
                'id' => $attendance->id,
                'student_id' => $attendance->student_id,
                'date' => optional($attendance->date)->format('Y-m-d'),
                'status' => $attendance->status,
            ],
            'students' => $students->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->name,
            ]),
        ]);
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:Present,Absent,Tardy',
        ]);

        $attendance->update($request->only('student_id', 'date', 'status'));

        app(ParentNotifier::class)->attendanceLogged($attendance);

        return redirect()->route('teacher.attendance')->with('success', 'Attendance updated successfully. Parent email sent.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('teacher.attendance')->with('success', 'Attendance deleted successfully.');
    }
}

