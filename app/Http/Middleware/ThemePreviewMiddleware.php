<?php

namespace App\Http\Middleware;

use App\Providers\ThemeServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThemePreviewMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $previewCandidate = null;
        $shouldQueueCookie = false;
        $shouldClearCookie = false;

        // 1. Check if query parameter explicitly requests or resets preview
        if ($request->has('preview_theme')) {
            $val = trim((string) $request->query('preview_theme'));
            if ($val === 'reset' || $val === 'clear' || $val === '0' || $val === '') {
                $shouldClearCookie = true;
            } else {
                $previewCandidate = $val;
                $shouldQueueCookie = true;
            }
        } elseif ($request->has('theme_preview') && $request->has('theme')) {
            $previewCandidate = trim((string) $request->query('theme'));
            $shouldQueueCookie = true;
        } elseif ($request->cookie('preview_theme')) {
            $previewCandidate = trim((string) $request->cookie('preview_theme'));
        }

        if ($previewCandidate && preg_match('/^[a-z0-9\-_]+$/i', $previewCandidate)) {
            $cleanTheme = strtolower($previewCandidate);
            if (is_dir(base_path("themes/{$cleanTheme}"))) {
                ThemeServiceProvider::applyTheme($cleanTheme);
            }
        }

        $response = $next($request);

        // Manage preview cookie
        if ($shouldClearCookie) {
            $response->headers->clearCookie('preview_theme');
        } elseif ($shouldQueueCookie && $previewCandidate && preg_match('/^[a-z0-9\-_]+$/i', $previewCandidate)) {
            $response->headers->setCookie(cookie('preview_theme', strtolower($previewCandidate), 120, null, null, false, false));
        }

        return $response;
    }
}

