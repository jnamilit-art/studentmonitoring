<?php

namespace App\Http\Middleware;

use App\Models\School;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectSubdomain
{
    protected Request $request;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->request = $request;
        $host = $request->getHost();
        $mainDomain = config('app.domain', 'localhost');

        // If accessing super-admin area without an explicit school query param, clear current_school
        if ($request->is('super-admin*') && ! $request->has('school_subdomain')) {
            app()->instance('current_school', null);
            return $next($request);
        }

        $subdomain = null;

        // 1. Check for query parameter override (persists to session)
        if ($request->filled('school_subdomain')) {
            $subdomain = $request->get('school_subdomain');
            if ($request->hasSession()) {
                $request->session()->put('active_school_subdomain', $subdomain);
            }
        }
        // 2. Extract subdomain from host
        elseif ($hostSubdomain = $this->getSubdomain($host, $mainDomain)) {
            $subdomain = $hostSubdomain;
            if ($request->hasSession()) {
                $request->session()->put('active_school_subdomain', $subdomain);
            }
        }
        // 3. Check session for previously selected school
        elseif ($request->hasSession() && $request->session()->has('active_school_subdomain')) {
            $subdomain = $request->session()->get('active_school_subdomain');
        }
        // 4. Authenticated user's assigned school
        elseif ($userSchool = $request->user()?->school) {
            $subdomain = $userSchool->subdomain;
            if ($request->hasSession()) {
                $request->session()->put('active_school_subdomain', $subdomain);
            }
        }

        if ($subdomain) {
            // Find school by subdomain (case-insensitive)
            try {
                $school = School::whereRaw('LOWER(subdomain) = ?', [strtolower($subdomain)])
                    ->where('is_active', true)
                    ->first();

                if ($school) {
                    $request->attributes->set('school', $school);
                    app()->instance('current_school', $school);
                } else {
                    app()->instance('current_school', null);
                }
            } catch (\Exception $e) {
                app()->instance('current_school', null);
            }
        } else {
            app()->instance('current_school', null);
        }

        return $next($request);
    }

    /**
     * Extract subdomain from host
     */
    private function getSubdomain(string $host, string $mainDomain): ?string
    {
        // If accessing via localhost or IP, no subdomain
        if (in_array($host, ['localhost', '127.0.0.1'])) {
            return null;
        }

        // If host matches main domain exactly, no subdomain
        if ($host === $mainDomain) {
            return null;
        }

        // Extract subdomain
        $parts = explode('.', $host);

        // Must have at least 3 parts (subdomain.domain.tld)
        if (count($parts) < 3) {
            return null;
        }

        // Check if the main domain matches
        $domain = implode('.', array_slice($parts, -2));
        if ($domain !== $mainDomain) {
            return null;
        }

        // Return the subdomain (first part)
        return $parts[0];
    }
}
