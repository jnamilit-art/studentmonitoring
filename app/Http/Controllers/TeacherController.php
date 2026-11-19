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
    private function getTeacherStudentsQuery()
    {
        $teacher = Auth::user();
        $school = app()->bound('current_school') ? app('current_school') : null;
        $schoolId = $school?->id ?? $teacher?->school_id;

        $query = Student::query();

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        if ($teacher?->grade_level) {
            $query->where('grade_level', $teacher->grade_level);
        }

        return $query;
    }

    public function dashboard(): Response
    {
        $teacher = Auth::user();
        $studentsQuery = $this->getTeacherStudentsQuery();
        $studentIds = (clone $studentsQuery)->pluck('id');
        $studentsCount = (clone $studentsQuery)->count();

        $latestAnnouncement = Announcement::query()
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->first();

        $recentGrades = Grade::query()
            ->whereIn('student_id', $studentIds)
            ->with('student')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentAttendance = Attendance::query()
            ->whereIn('student_id', $studentIds)
            ->with('student')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $recentBehavior = Behavior::query()
            ->whereIn('student_id', $studentIds)
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
                'students' => $studentsCount,
                'grades' => Grade::whereIn('student_id', $studentIds)->count(),
                'attendance' => Attendance::whereIn('student_id', $studentIds)->count(),
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
        $students = $this->getTeacherStudentsQuery()->orderBy('name')->get();
        $studentIds = $students->pluck('id');
        $grades = Grade::with('student')
            ->whereIn('student_id', $studentIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Grades', [
            'students' => $students->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'grade_level' => $student->grade_level,
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
        $students = $this->getTeacherStudentsQuery()->orderBy('name')->get();
        $studentIds = $students->pluck('id');
        $attendances = Attendance::with('student')
            ->whereIn('student_id', $studentIds)
            ->orderBy('date', 'desc')
            ->limit(20)
            ->get();

        return Inertia::render('Admin/Attendance', [
            'students' => $students->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'grade_level' => $student->grade_level,
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
        $students = $this->getTeacherStudentsQuery()->orderBy('name')->get();
        $studentIds = $students->pluck('id');
        $behaviors = Behavior::with('student')
            ->whereIn('student_id', $studentIds)
            ->orderBy('date', 'desc')
            ->limit(20)
            ->get();

        return Inertia::render('Admin/Behavior', [
            'students' => $students->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'grade_level' => $student->grade_level,
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
        $teacher = Auth::user();
        $students = $this->getTeacherStudentsQuery()->orderBy('name')->get();

        return Inertia::render('Admin/Students', [
            'students' => $students->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => $student->name,
                'parent_email' => $student->parent_email,
                'student_email' => $student->student_email,
                'grade_level' => $student->grade_level,
            ]),
            'teacherGradeLevel' => $teacher?->grade_level,
            'gradeLevels' => \App\Models\GradeLevel::orderBy('title')->pluck('title'),
        ]);
    }
}
