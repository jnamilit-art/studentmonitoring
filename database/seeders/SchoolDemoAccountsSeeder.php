<?php

namespace Database\Seeders;

use App\Models\GradeLevel;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SchoolDemoAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');
        $gradeLevel = GradeLevel::query()->orderBy('title')->value('title') ?? 'Grade 7';

        if (! GradeLevel::query()->where('title', $gradeLevel)->exists()) {
            GradeLevel::query()->create(['title' => $gradeLevel]);
        }

        foreach (School::query()->orderBy('id')->get() as $school) {
            $slug = Str::lower($school->subdomain);

            $admin = User::updateOrCreate(
                ['email' => "admin.{$slug}@pilot.school"],
                [
                    'name' => $school->name.' Admin',
                    'role' => 'admin',
                    'school_id' => $school->id,
                    'password' => $password,
                    'email_verified_at' => now(),
                ]
            );

            $teacher = User::updateOrCreate(
                ['email' => "teacher.{$slug}@pilot.school"],
                [
                    'name' => $school->name.' Teacher',
                    'role' => 'teacher',
                    'grade_level' => $gradeLevel,
                    'school_id' => $school->id,
                    'password' => $password,
                    'email_verified_at' => now(),
                ]
            );

            $parent = User::updateOrCreate(
                ['email' => "parent.{$slug}@pilot.school"],
                [
                    'name' => $school->name.' Parent',
                    'role' => 'parent',
                    'school_id' => $school->id,
                    'password' => $password,
                    'email_verified_at' => now(),
                ]
            );

            Student::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'parent_email' => $parent->email,
                ],
                [
                    'name' => $school->name.' Demo Student',
                ]
            );

            unset($admin, $teacher);
        }
    }
}
