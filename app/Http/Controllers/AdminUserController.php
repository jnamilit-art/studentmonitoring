<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function update(Request $request, User $user): RedirectResponse
    {
        // Prevent regular admins from modifying super admin users
        if ($user->isSuperAdmin() && ! Auth::user()->isSuperAdmin()) {
            return back()->with('error', 'You cannot modify super admin accounts.');
        }

        // Prevent regular admins from promoting users to super admin
        if (! Auth::user()->isSuperAdmin() && $request->has('role') && $request->role === 'super_admin') {
            return back()->with('error', 'You cannot promote users to super admin.');
        }

        $data = $request->validate([
            'role' => 'sometimes|required|in:parent,teacher,admin,super_admin',
            'email' => ['sometimes', 'required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'grade_level' => ['sometimes', 'nullable', Rule::exists('grade_levels', 'title')],
        ]);

        $updates = [];

        if (array_key_exists('role', $data)) {
            $updates['role'] = $data['role'];
        }

        if (array_key_exists('email', $data)) {
            $updates['email'] = $data['email'];
        }

        if (array_key_exists('grade_level', $data)) {
            $updates['grade_level'] = $data['grade_level'] ?: null;
        }

        if ($updates !== []) {
            $user->update($updates);
        }

        return back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        // Prevent regular admins from deleting super admin users
        if ($user->isSuperAdmin() && ! Auth::user()->isSuperAdmin()) {
            return back()->with('error', 'You cannot delete super admin accounts.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
