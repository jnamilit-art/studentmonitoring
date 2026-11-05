<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\GradeLevel;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class TeacherRegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/TeacherRegister', [
            'gradeLevels' => GradeLevel::query()
                ->orderBy('title')
                ->pluck('title')
                ->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'grade_level' => ['required', Rule::exists('grade_levels', 'title')],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $school = app()->bound('current_school') ? app('current_school') : null;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'teacher',
            'grade_level' => $request->grade_level,
            'school_id' => $school?->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('teacher.dashboard', absolute: false));
    }
}
