<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $user = $user->fresh();

        // Check if user has any of the required roles (using actual stored role)
        if (! in_array($user->role, $roles, true)) {
            // If user doesn't have the required role, don't redirect - just deny access
            abort(403);
        }

        return $next($request);
    }
}
