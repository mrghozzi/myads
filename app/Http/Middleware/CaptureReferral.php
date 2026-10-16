<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CaptureReferral
{
    /**
     * Handle an incoming request.
     * Captures incoming referral query parameter ('ref') and sets a 30-day cookie and session.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('ref')) {
            $ref = trim((string) $request->query('ref'));

            // Sanitize: allow alphanumeric, underscore, hyphen, dot, length up to 64
            if ($ref !== '' && $ref !== '0' && strlen($ref) <= 64 && preg_match('/^[a-zA-Z0-9_\-\.]+$/u', $ref)) {
                // Queue 30-day cookie (43200 minutes)
                Cookie::queue('ref', $ref, 60 * 24 * 30);

                if ($request->hasSession()) {
                    $request->session()->put('ref', $ref);
                }
            }
        }

        return $next($request);
    }
}
