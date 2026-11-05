<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    protected $fillable = ['student_id', 'subject', 'grading_period', 'grade'];

    protected $casts = [
        'grade' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
