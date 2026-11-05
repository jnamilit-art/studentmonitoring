<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Behavior;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TeacherController extends Controller
{
    public function dashboard(): Response
    {
        $teacher = Auth::user();
        $latestAnnouncement = Announcement::query()
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->first();
        $recentGrades = Grade::query()
            ->with('student')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
        $recentAttendance = Attendance::query()
            ->with('student')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();
        $recentBehavior = Behavior::query()
            ->with('student')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return Inertia::render('Teacher/Dashboard', [
            'teacher' => [
                'name' => $teacher?->name,
                'grade_level' => $teacher?->grade_level,
            ],
            'stats' => [
                'students' => Student::query()->count(),
                'grades' => Grade::query()->count(),
                'attendance' => Attendance::query()->count(),
                'announcements' => Announcement::query()->count(),
            ],
            'latestAnnouncement' => $latestAnnouncement ? [
                'title' => $latestAnnouncement->title,
                'content' => $latestAnnouncement->content,
                'date' => optional($latestAnnouncement->date)->format('F d, Y'),
            ] : null,
            'recentGrades' => $recentGrades->map(fn (Grade $grade) => [
                'id' => $grade->id,
                'student_name' => $grade->student?->name,
                'subject' => $grade->subject,
                'grading_period' => (int) $grade->grading_period,
                'grade' => $grade->grade,
            ]),
            'recentAttendance' => $recentAttendance->map(fn (Attendance $attendance) => [
                'id' => $attendance->id,
                'student_name' => $attendance->student?->name,
                'date' => optional($attendance->date)->format('Y-m-d'),
                'status' => $attendance->status,
            ]),
            'recentBehavior' => $recentBehavior->map(fn (Behavior $behavior) => [
                'id' => $behavior->id,
                'student_name' => $behavior->student?->name,
                'date' => optional($behavior->date)->format('Y-m-d'),
                'note' => $behavior->note,
                'teacher' => $behavior->teacher,
            ]),
        ]);
    }

    public function manageAnnouncements(): Response
    {
        $announcements = Announcement::orderBy('date', 'desc')->get();

        return Inertia::render('Admin/Announcements', [
            'announcements' => $announcements->map(fn (Announcement $announcement) => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'content' => $announcement->content,
                'date' => optional($announcement->date)->format('F d, Y'),
            ]),
        ]);
    }

    public function manageGrades(): Response
    {
        $students = Student::orderBy('name')->get();
        $grades = Grade::with('student')->orderBy('created_at', 'desc')->get();

        return Inertia::render('Admin/Grades', [
            'students' => $students->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->name,
            ]),
            'grades' => $grades->map(fn (Grade $grade) => [
                'id' => $grade->id,
                'student_id' => $grade->student_id,
                'student_name' => $grade->student?->name,
                'subject' => $grade->subject,
                'grading_period' => (int) $grade->grading_period,
                'grade' => $grade->grade,
            ]),
        ]);
    }

    public function manageAttendance(): Response
    {
        $students = Student::orderBy('name')->get();
        $attendances = Attendance::with('student')->orderBy('date', 'desc')->limit(10)->get();

        return Inertia::render('Admin/Attendance', [
            'students' => $students->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->name,
            ]),
            'attendances' => $attendances->map(fn (Attendance $attendance) => [
                'id' => $attendance->id,
                'student_id' => $attendance->student_id,
                'student_name' => $attendance->student?->name,
                'date' => optional($attendance->date)->format('Y-m-d'),
                'status' => $attendance->status,
            ]),
        ]);
    }

    public function manageBehavior(): Response
    {
        $students = Student::orderBy('name')->get();
        $behaviors = Behavior::with('student')->orderBy('date', 'desc')->limit(10)->get();

        return Inertia::render('Admin/Behavior', [
            'students' => $students->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->name,
            ]),
            'behaviors' => $behaviors->map(fn (Behavior $behavior) => [
                'id' => $behavior->id,
                'student_id' => $behavior->student_id,
                'student_name' => $behavior->student?->name,
                'date' => optional($behavior->date)->format('Y-m-d'),
                'note' => $behavior->note,
                'teacher' => $behavior->teacher,
            ]),
        ]);
    }

    public function manageStudents(): Response
    {
        $students = Student::orderBy('name')->get();

        return Inertia::render('Admin/Students', [
            'students' => $students->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'parent_email' => $student->parent_email,
            ]),
        ]);
    }
}
