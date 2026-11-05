<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'role',
        'grade_level',
        'school_id',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'effective_role',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getEffectiveRoleAttribute(): string
    {
        if ($this->role === 'super_admin') {
            return 'super_admin';
        }

        if ($this->role === 'admin') {
            return 'admin';
        }

        if ($this->role === 'teacher') {
            return 'teacher';
        }

        if ($this->role === 'parent') {
            return 'parent';
        }

        return Student::query()->whereRaw('LOWER(TRIM(parent_email)) = ?', [strtolower(trim($this->email))])->exists()
            ? 'parent'
            : 'teacher';
    }

    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->effective_role, $roles, true);
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'super_admin' => 'super_admin.dashboard',
            'admin' => 'admin.dashboard',
            'teacher' => 'teacher.dashboard',
            'parent' => 'parent.dashboard',
            default => 'home',
        };
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}
