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

        // Check for query parameter override (for local development)
        if ($request->has('school_subdomain')) {
            $subdomain = $request->get('school_subdomain');
        } else {
            // Extract subdomain from host
            $subdomain = $this->getSubdomain($host, $mainDomain);
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
