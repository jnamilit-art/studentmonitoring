<?php

namespace App\Http\Controllers;

use App\Models\GradeLevel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminGradeLevelController extends Controller
{
    public function update(Request $request, GradeLevel $gradeLevel): RedirectResponse
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('grade_levels', 'title')->ignore($gradeLevel->id),
            ],
        ]);

        $oldTitle = $gradeLevel->title;
        $newTitle = trim($validated['title']);

        DB::transaction(function () use ($gradeLevel, $oldTitle, $newTitle) {
            $gradeLevel->update([
                'title' => $newTitle,
            ]);

            User::query()
                ->where('role', 'teacher')
                ->where('grade_level', $oldTitle)
                ->update([
                    'grade_level' => $newTitle,
                ]);
        });

        return back();
    }

    public function destroy(GradeLevel $gradeLevel): RedirectResponse
    {
        DB::transaction(function () use ($gradeLevel) {
            User::query()
                ->where('role', 'teacher')
                ->where('grade_level', $gradeLevel->title)
                ->update([
                    'grade_level' => null,
                ]);

            $gradeLevel->delete();
        });

        return back();
    }
}
