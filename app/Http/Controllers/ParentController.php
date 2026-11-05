<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Behavior;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ParentController extends Controller
{
    public function dashboard(): Response|\Illuminate\Http\RedirectResponse
    {
        $student = $this->findStudentForAuthenticatedParent();
        $gradeSummary = [];
        $recentAttendance = collect();
        $latestBehavior = null;

        if ($student) {
            $grades = Grade::where('student_id', $student->id)->get();
            foreach ($grades as $grade) {
                $latestGrade = Grade::where('student_id', $student->id)
                    ->where('subject', $grade->subject)
                    ->orderBy('grading_period', 'desc')
                    ->first();
                if ($latestGrade && $latestGrade->grade) {
                    $gradeSummary[$grade->subject] = $this->toLetterGrade($latestGrade->grade);
                }
            }

            $recentAttendance = Attendance::where('student_id', $student->id)
                ->orderBy('date', 'desc')
                ->limit(3)
                ->get();

            $latestBehavior = Behavior::where('student_id', $student->id)
                ->orderBy('date', 'desc')
                ->first();
        }

        $latestAnnouncement = Announcement::orderBy('date', 'desc')->first();

        return Inertia::render('Parent/Dashboard', [
            'student' => $student ? [
                'id' => $student->id,
                'name' => $student->name,
                'parent_email' => $student->parent_email,
            ] : null,
            'gradeSummary' => $gradeSummary,
            'recentAttendance' => $recentAttendance->map(fn (Attendance $attendance) => [
                'id' => $attendance->id,
                'date' => optional($attendance->date)->format('Y-m-d'),
                'status' => $attendance->status,
            ]),
            'latestBehavior' => $latestBehavior ? [
                'date' => optional($latestBehavior->date)->format('Y-m-d'),
                'note' => $latestBehavior->note,
                'teacher' => $latestBehavior->teacher,
            ] : null,
            'latestAnnouncement' => $latestAnnouncement ? [
                'title' => $latestAnnouncement->title,
                'content' => $latestAnnouncement->content,
                'date' => optional($latestAnnouncement->date)->format('F d, Y'),
            ] : null,
        ]);
    }

    public function grades(): Response|\Illuminate\Http\RedirectResponse
    {
        $student = $this->findStudentForAuthenticatedParent();

        if (!$student) {
            return redirect()->route('parent.dashboard')->with('error', 'No student found for your account.');
        }

        $grades = Grade::where('student_id', $student->id)->get();
        $gradesBySubject = [];

        foreach ($grades as $grade) {
            if (!isset($gradesBySubject[$grade->subject])) {
                $gradesBySubject[$grade->subject] = [null, null, null, null];
            }
            $gradesBySubject[$grade->subject][$grade->grading_period - 1] = $grade->grade;
        }

        return Inertia::render('Parent/Grades', [
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
            ],
            'gradesBySubject' => $gradesBySubject,
        ]);
    }

    public function attendance(): Response|\Illuminate\Http\RedirectResponse
    {
        $student = $this->findStudentForAuthenticatedParent();

        if (!$student) {
            return redirect()->route('parent.dashboard')->with('error', 'No student found for your account.');
        }

        $attendances = Attendance::where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->get();

        // Group by month
        $monthlyData = [];
        foreach ($attendances as $attendance) {
            if (! $attendance->date) {
                continue;
            }

            $month = $attendance->date->format('F Y');
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = ['present' => 0, 'absent' => 0];
            }
            if ($attendance->status === 'Present' || $attendance->status === 'Tardy') {
                $monthlyData[$month]['present']++;
            } else {
                $monthlyData[$month]['absent']++;
            }
        }

        $attendanceSummary = collect($monthlyData)->map(function (array $data, string $month) {
            $total = $data['present'] + $data['absent'];

            return [
                'month' => $month,
                'present' => $data['present'],
                'absent' => $data['absent'],
                'rate' => $total > 0 ? number_format(($data['present'] / $total) * 100, 1) : '100.0',
            ];
        })->values();

        return Inertia::render('Parent/Attendance', [
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
            ],
            'monthlyData' => $attendanceSummary,
        ]);
    }

    public function behavior(): Response|\Illuminate\Http\RedirectResponse
    {
        $student = $this->findStudentForAuthenticatedParent();

        if (!$student) {
            return redirect()->route('parent.dashboard')->with('error', 'No student found for your account.');
        }

        $behaviors = Behavior::where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->get();

        return Inertia::render('Parent/Behavior', [
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
            ],
            'behaviors' => $behaviors->map(fn (Behavior $behavior) => [
                'id' => $behavior->id,
                'date' => optional($behavior->date)->format('Y-m-d'),
                'note' => $behavior->note,
                'teacher' => $behavior->teacher,
            ]),
        ]);
    }

    public function announcements(): Response
    {
        $announcements = Announcement::orderBy('date', 'desc')->get();

        return Inertia::render('Parent/Announcements', [
            'announcements' => $announcements->map(fn (Announcement $announcement) => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'content' => $announcement->content,
                'date' => optional($announcement->date)->format('F d, Y'),
            ]),
        ]);
    }

    private function toLetterGrade($grade)
    {
        if ($grade >= 93) return 'A';
        if ($grade >= 90) return 'A-';
        if ($grade >= 87) return 'B+';
        if ($grade >= 83) return 'B';
        if ($grade >= 80) return 'B-';
        if ($grade >= 75) return 'C';
        return 'F';
    }

    private function findStudentForAuthenticatedParent(): ?Student
    {
        $email = Str::lower(trim((string) Auth::user()?->email));

        if ($email === '') {
            return null;
        }

        return Student::query()
            ->whereRaw('LOWER(TRIM(parent_email)) = ?', [$email])
            ->first();
    }
}
