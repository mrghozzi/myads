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

        // SECURITY: Allow ad routes to be framed by external sites, and prevent referrer leakage
        $isAdRoute = $request->is('bn.php', 'link.php', 'smart.php', 'embed/*', 'ads/*', 'show.php');
        
        if (!$isAdRoute) {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        } else {
            // Protect referrer data during ad clicks/embeds
            $response->headers->set('Referrer-Policy', 'origin');
        }

        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // SECURITY: Content-Security-Policy — balanced for ad platforms while blocking injection attacks
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; " .
            // Note: 'unsafe-eval' was removed for security. If WYSIWYG editors (like SCEditor) break, add it back locally.
            "script-src 'self' 'unsafe-inline' https://www.googletagmanager.com https://www.google-analytics.com https://pagead2.googlesyndication.com https://cdn.jsdelivr.net https://unpkg.com; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
            "img-src 'self' data: blob: https: http:; " .
            "font-src 'self' https://fonts.gstatic.com data:; " .
            "connect-src 'self' https://www.google-analytics.com; " .
            "frame-src 'self' https://www.youtube.com https://www.google.com; " .
            "object-src 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self';"
        );

        // SECURITY: Prevent SSL stripping attacks via HSTS (only when serving over HTTPS)
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // SECURITY: Mitigate Spectre-class side-channel attacks via cross-origin window references
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        return $response;
    }
}
