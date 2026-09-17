<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class GuestPageCacheMiddleware
{
    /**
     * Cache duration in seconds for guest micro-caching (30 to 60s).
     */
    protected const CACHE_TTL_SECONDS = 45;

    /**
     * List of path prefixes eligible for guest micro-caching.
     */
    protected const CACHEABLE_PATHS = [
        '/',
        'portal',
        'directory',
        'store',
        'terms',
        'privacy',
        'about',
        'badges',
        'news',
        'video',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Only cache safe GET/HEAD requests
        if (!$request->isMethodSafe()) {
            return $next($request);
        }

        // 2. Only cache for guests (authenticated users have personal feeds, unread badges, notifications)
        if (Auth::check()) {
            return $next($request);
        }

        // 3. Skip if request has sensitive authentication or transaction query parameters
        if ($request->hasAny(['token', 'code', 'auth', 'redirect', 'session', 'signature', 'state'])) {
            return $next($request);
        }

        // 4. Verify route eligibility
        if (!$this->isCacheablePath($request)) {
            return $next($request);
        }

        $version = (int) Cache::get('myads_guest_cache_version', 1);
        $cacheKey = 'myads_guest_p_' . $version . '_' . md5($request->fullUrl() . '_' . app()->getLocale());

        // Check if response is cached
        $cachedData = Cache::get($cacheKey);
        if ($cachedData && is_array($cachedData) && isset($cachedData['content'])) {
            $content = $cachedData['content'];
            $contentType = $cachedData['content_type'] ?? 'text/html; charset=UTF-8';
            $etag = 'W/"' . md5($content) . '"';

            // ETag conditional check (304 Not Modified)
            if ($request->header('If-None-Match') === $etag) {
                return response('', 304)
                    ->header('ETag', $etag)
                    ->header('Cache-Control', 'public, max-age=' . self::CACHE_TTL_SECONDS);
            }

            return response($content, 200)
                ->header('Content-Type', $contentType)
                ->header('ETag', $etag)
                ->header('X-MyAds-MicroCache', 'HIT')
                ->header('Cache-Control', 'public, max-age=' . self::CACHE_TTL_SECONDS . ', stale-while-revalidate=30');
        }

        /** @var Response $response */
        $response = $next($request);

        // Only cache successful standard HTML responses
        if ($response->getStatusCode() === 200 && !$response->headers->has('X-No-Cache')) {
            $contentType = $response->headers->get('Content-Type', '');
            if (str_contains(strtolower($contentType), 'text/html')) {
                $content = $response->getContent();
                $etag = 'W/"' . md5($content) . '"';

                Cache::put($cacheKey, [
                    'content' => $content,
                    'content_type' => $contentType,
                ], self::CACHE_TTL_SECONDS);

                // ETag conditional check (304 Not Modified)
                if ($request->header('If-None-Match') === $etag) {
                    return response('', 304)
                        ->header('ETag', $etag)
                        ->header('Cache-Control', 'public, max-age=' . self::CACHE_TTL_SECONDS);
                }

                $response->headers->set('ETag', $etag);
                $response->headers->set('X-MyAds-MicroCache', 'MISS');
                $response->headers->set('Cache-Control', 'public, max-age=' . self::CACHE_TTL_SECONDS . ', stale-while-revalidate=30');
            }
        }

        return $response;
    }

    /**
     * Check if request path matches eligible caching prefixes.
     */
    protected function isCacheablePath(Request $request): bool
    {
        $path = trim($request->path(), '/');

        if ($path === '' || $path === '/') {
            return true;
        }

        foreach (self::CACHEABLE_PATHS as $prefix) {
            $prefix = trim($prefix, '/');
            if ($prefix !== '' && ($path === $prefix || str_starts_with($path, $prefix . '/'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Flush all cached guest pages instantaneously by bumping version tag.
     */
    public static function flush(): void
    {
        $current = (int) Cache::get('myads_guest_cache_version', 1);
        Cache::forever('myads_guest_cache_version', $current + 1);
    }
}
