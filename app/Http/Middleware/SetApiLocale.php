<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('Accept-Language') ?? $request->header('X-Locale');

        if ($locale) {
            // Extract the base language code (e.g., "en-US,en;q=0.9" -> "en")
            $locale = substr($locale, 0, 2);
            
            // Check if the locale is supported, assuming 'ar' and 'en' for now, or fallback to config
            $supportedLocales = ['en', 'ar', 'de', 'es', 'fa', 'fr', 'it', 'pt', 'tr'];
            
            if (in_array($locale, $supportedLocales)) {
                app()->setLocale($locale);
            }
        }

        return $next($request);
    }
}
