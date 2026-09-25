<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SECURITY: Block access to sensitive files and paths at the application level.
 *
 * This provides defence-in-depth against .env exposure and similar attacks.
 * Web-server rules (.htaccess / nginx config) are the first line of defence,
 * but they can be misconfigured, disabled, or bypassed on certain hosts.
 * This middleware is the second line of defence, catching any requests that
 * slip through the web-server layer.
 *
 * Blocked paths include:
 *  - .env, .env.*, env (with or without leading dot/slash)
 *  - composer.json, composer.lock
 *  - artisan
 *  - phpunit.xml
 *  - .git, .github, .editorconfig
 *  - storage/, bootstrap/, config/, database/, app/, routes/, vendor/
 */
class BlockSensitivePaths
{
    /**
     * Sensitive file patterns (matched against the request path without leading slash).
     * Uses case-insensitive matching to prevent bypass via URL encoding or case tricks.
     */
    private const BLOCKED_PATTERNS = [
        // .env files — the most critical protection
        '#^\.?env($|\.)#i',
        // Composer files
        '#^composer\.(json|lock)$#i',
        // Laravel artisan
        '#^artisan$#i',
        // PHPUnit config
        '#^phpunit\.xml$#i',
        // Git metadata
        '#^\.git(|attributes|ignore|hub|modules)(/|$)#i',
        // Editor config
        '#^\.editorconfig$#i',
        // Internal directories that should never be web-accessible
        '#^(storage|bootstrap|config|database|app|routes|tests|vendor|brain|~)(/|$)#i',
        // .env.example, .env.testing, etc.
        '#^\.env\..+$#i',
        // Security policy (not secret, but not meant as a route)
        '#^SECURITY\.md$#i',
        // Any dotfile at root
        '#^\.[^/]+$#i',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $path = ltrim($request->path(), '/');

        // Normalize: strip any URL-encoded sequences that could bypass filters
        $decodedPath = urldecode($path);

        foreach (self::BLOCKED_PATTERNS as $pattern) {
            if (preg_match($pattern, $path) || preg_match($pattern, $decodedPath)) {
                // Log the blocked attempt for security auditing
                \Log::warning('BlockSensitivePaths: Blocked access attempt', [
                    'path' => $path,
                    'ip'   => $request->ip(),
                    'ua'   => $request->userAgent(),
                ]);

                abort(403, 'Access Denied');
            }
        }

        return $next($request);
    }
}
