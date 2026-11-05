<?php

namespace App\Helpers;

use App\Models\School;

class SchoolHelper
{
    /**
     * Get the current school based on subdomain
     */
    public static function currentSchool(): ?School
    {
        return app()->bound('current_school') ? app('current_school') : null;
    }

    /**
     * Check if currently on a school subdomain
     */
    public static function onSchoolSubdomain(): bool
    {
        return self::currentSchool() !== null;
    }
}
