<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ParentLoginController extends Controller
{
    public function create(Request $request): Response|RedirectResponse
    {
        if ($request->user()?->isParent()) {
            return redirect()->route('parent.dashboard');
        }

        if ($request->user()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return Inertia::render('Auth/ParentLogin', [
            'status' => session('status'),
            'students' => Student::query()
                ->orderBy('name')
                ->get(['id', 'name']),
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

        if (! $user->isParent()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'This account does not have parent access.',
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

        return redirect()->route('parent.dashboard');
    }
}
