<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SuperAdminLoginController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/SuperAdminLogin', [
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

        if (! $user->isSuperAdmin()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'This account does not have super admin access.',
            ]);
        }

        return redirect()->intended(route('super_admin.dashboard', absolute: false));
    }
}
