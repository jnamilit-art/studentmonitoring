<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $parentEmails = DB::table('students')
            ->whereNotNull('parent_email')
            ->pluck('parent_email')
            ->map(fn ($email) => strtolower(trim($email)))
            ->filter()
            ->unique()
            ->values();

        $users = DB::table('users')
            ->whereNull('role')
            ->orderBy('id')
            ->get(['id', 'email']);

        $adminAssigned = DB::table('users')->where('role', 'admin')->exists();

        foreach ($users as $user) {
            $email = strtolower(trim((string) $user->email));
            $role = $parentEmails->contains($email) ? 'parent' : 'teacher';

            if (! $adminAssigned && $role !== 'parent') {
                $role = 'admin';
                $adminAssigned = true;
            }

            DB::table('users')
                ->where('id', $user->id)
                ->update(['role' => $role]);
        }
    }

    public function down(): void
    {
        // Backfill only; no rollback.
    }
};
