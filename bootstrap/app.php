<?php

use App\Models\Student;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\DetectSubdomain::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request): string {
            if ($request->is('parent') || $request->is('parent/*')) {
                return route('parent.login');
            }

            if ($request->is('teacher') || $request->is('teacher/*')) {
                return route('teacher.login');
            }

            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            if ($request->is('super-admin') || $request->is('super-admin/*')) {
                return route('super_admin.login');
            }

            return route('login');
        });

        $middleware->redirectUsersTo(function (Request $request): string {
            return route($request->user()->dashboardRoute());
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
