<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('students', 'grade_level')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('grade_level')->nullable()->after('school_id');
            });
        }

        // 1. Backfill orphaned parents whose email matches student parent_email
        $orphanedParents = DB::table('users')->where('role', 'parent')->whereNull('school_id')->get();
        foreach ($orphanedParents as $parent) {
            $student = DB::table('students')
                ->whereRaw('LOWER(TRIM(parent_email)) = ?', [strtolower(trim($parent->email))])
                ->whereNotNull('school_id')
                ->first();

            if ($student) {
                DB::table('users')->where('id', $parent->id)->update(['school_id' => $student->school_id]);
            }
        }

        // 2. If there is only one school in the system, link all non-super-admin users and students to it
        $schools = DB::table('schools')->get();
        if ($schools->count() === 1) {
            $onlySchoolId = $schools->first()->id;

            DB::table('users')
                ->whereNull('school_id')
                ->where('role', '!=', 'super_admin')
                ->update(['school_id' => $onlySchoolId]);

            DB::table('students')
                ->whereNull('school_id')
                ->update(['school_id' => $onlySchoolId]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('students', 'grade_level')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('grade_level');
            });
        }
    }
};
