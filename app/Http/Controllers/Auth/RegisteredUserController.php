<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(Request $request): Response|RedirectResponse
    {
        if ($request->user()?->hasAnyRole(['parent', 'admin'])) {
            return redirect()->route('parent.dashboard');
        }

        if ($request->user()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return Inertia::render('Auth/Register', [
            'students' => Student::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'student_id' => 'nullable|exists:students,id',
        ]);

        $school = app()->bound('current_school') ? app('current_school') : null;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'parent',
            'school_id' => $school?->id,
        ]);

        event(new Registered($user));

        if ($request->student_id) {
            $student = Student::find($request->student_id);

            if ($student) {
                $student->update(['parent_email' => $user->email]);
            }
        }

        Auth::login($user);

        $message = $request->student_id
            ? 'Registration successful!'
            : 'Registration successful! Please contact the administrator to link your account to a student.';

        return redirect()->route('dashboard')
            ->with('info', $message);
    }
}
