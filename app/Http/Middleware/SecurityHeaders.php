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
 *  - Content-Security-Policy — strict nonce-based CSP for XSS defense
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // SECURITY: Generate a cryptographically secure random nonce per request for CSP
        $nonce = base64_encode(random_bytes(16));
        $request->attributes->set('csp_nonce', $nonce);

        if (function_exists('view')) {
            try {
                view()->share('cspNonce', $nonce);
            } catch (\Throwable) {
                // View factory not bound in some minimal contexts
            }
        }

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // SECURITY: Allow external ad serving/embed routes to be framed by external publisher sites, and prevent referrer leakage
        $isAdServingRoute = $request->is(
            'bn.php',
            'link.php',
            'smart.php',
            'embed/*',
            'show.php',
            'ads/redirect',
            'ads/custom/serve',
            'ads/custom/view/*',
            'ads/custom/click/*'
        );
        
        if (!$isAdServingRoute) {
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        } else {
            // Protect referrer data during external ad clicks/embeds
            $response->headers->set('Referrer-Policy', 'origin');
        }

        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(self)');

        // Check if Cloudflare Turnstile is enabled to whitelist challenges domain
        $turnstileScript = '';
        $turnstileFrame = '';
        try {
            if (\App\Support\SecuritySettings::get('turnstile_enabled')) {
                $turnstileScript = ' https://challenges.cloudflare.com';
                $turnstileFrame = ' https://challenges.cloudflare.com';
            }
        } catch (\Throwable) {
            // Graceful fallback
        }

        // SECURITY: Content-Security-Policy — balanced for ad platforms while blocking injection attacks
        $cspScriptSources = "'self' 'unsafe-inline' https://www.googletagmanager.com https://www.google-analytics.com https://pagead2.googlesyndication.com https://cdn.jsdelivr.net https://unpkg.com{$turnstileScript}";
        $cspStyleSources = "'self' 'unsafe-inline' https://fonts.googleapis.com";
        $cspFrameSources = "'self' https://www.youtube.com https://www.google.com https://stackedit.io{$turnstileFrame}";

        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; " .
            "script-src {$cspScriptSources}; " .
            "style-src {$cspStyleSources}; " .
            "img-src 'self' data: blob: https: http:; " .
            "font-src 'self' https://fonts.gstatic.com data:; " .
            "connect-src 'self' https://www.google-analytics.com; " .
            "frame-src {$cspFrameSources}; " .
            "object-src 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self';"
        );

        // SECURITY: Prevent SSL stripping attacks via HSTS (only when serving over HTTPS)
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // SECURITY: Mitigate Spectre-class side-channel attacks while allowing OAuth popups
        if (!$isAdServingRoute) {
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');
        }

        return $response;
    }
}
