<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = ['name', 'parent_email', 'school_id'];

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function behaviors(): HasMany
    {
        return $this->hasMany(Behavior::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
