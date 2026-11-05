<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $user = $request->user();
        $currentSchool = app()->bound('current_school') ? app('current_school') : null;

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->effective_role,
                    'grade_level' => $user->grade_level,
                ] : null,
            ],
            'appDomain' => config('app.domain', 'localhost'),
            'currentSchool' => $currentSchool ? [
                'id' => $currentSchool->id,
                'name' => $currentSchool->name,
                'subdomain' => $currentSchool->subdomain,
            ] : null,
            'flash' => [
                'success' => $request->session()->get('success'),
                'info' => $request->session()->get('info'),
                'error' => $request->session()->get('error'),
            ],
        ];
    }
}
