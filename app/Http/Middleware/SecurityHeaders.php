<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds security-related HTTP headers to all responses.
 *
 * Headers set:
 *  - X-Content-Type-Options: nosniff — prevents MIME-type sniffing
 *  - X-Frame-Options: SAMEORIGIN — blocks clickjacking via iframes
 *  - X-XSS-Protection: 1; mode=block — enables browser XSS filter
 *  - Referrer-Policy: strict-origin-when-cross-origin — limits referrer leakage
 *  - Permissions-Policy — restricts access to sensitive browser features
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        return $response;
    }
}
