<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AdminLoginController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/AdminLogin', [
            'status' => session('status'),
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $request->authenticate();

        $request->session()->regenerate();

        // Refresh the user model to ensure effective_role is calculated
        $user = $request->user()->fresh();

        if (! $user->isAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'This account does not have admin access.',
            ]);
        }

        // Check if user is accessing a school subdomain
        $currentSchool = app()->bound('current_school') ? app('current_school') : null;

        if ($currentSchool && $user->school_id !== $currentSchool->id) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'This account does not belong to this school.',
            ]);
        }

        return redirect()->route('admin.dashboard');
    }
}
