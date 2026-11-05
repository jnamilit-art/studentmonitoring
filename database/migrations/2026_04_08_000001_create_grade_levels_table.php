<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('grade_levels');

        Schema::create('grade_levels', function (Blueprint $table) {
            $table->id();
            $table->string('title', 191)->unique();
            $table->timestamps();
        });

        $now = now();
        $gradeLevels = [
            'Grade 1',
            'Grade 2',
            'Grade 3',
            'Grade 4',
            'Grade 5',
            'Grade 6',
        ];

        DB::table('grade_levels')->insert(
            collect($gradeLevels)->map(fn (string $title) => [
                'title' => $title,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all()
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_levels');
    }
};
