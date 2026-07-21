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
            // Support both 2-letter (en, sr, ja) and 5-letter (zh_CN, zh_TW) locale format
            $rawLocale = str_replace('-', '_', explode(',', $locale)[0]);
            
            $supportedLocales = ['en', 'ar', 'de', 'es', 'fa', 'fr', 'it', 'ja', 'pt', 'ru', 'sr', 'tr', 'zh_CN', 'zh_TW'];
            
            if (in_array($rawLocale, $supportedLocales)) {
                app()->setLocale($rawLocale);
            } else {
                $baseLocale = substr($rawLocale, 0, 2);
                if (in_array($baseLocale, $supportedLocales)) {
                    app()->setLocale($baseLocale);
                }
            }
        }

        return $next($request);
    }
}
