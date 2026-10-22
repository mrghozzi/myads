<?php

namespace App\Http\Middleware;

use App\Support\SystemVersion;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Option;
use Illuminate\Support\Facades\Cache;

class CheckSystemVersion
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $stversion = SystemVersion::CURRENT;
        $version_name = SystemVersion::name();
        $cacheKey = 'system_version_checked_' . $version_name;

        // Use cache to avoid hitting DB on every request (e.g., check once per hour or session)
        // But for development/first run, we want it to run.
        // Let's cache the "checked" status for 60 minutes.
        if (!Cache::has($cacheKey)) {
            try {
                // Check DB version
                $option = Option::where('o_type', 'version')->first();

                if ($option) {
                    if ($option->o_valuer != $stversion) {
                        $option->update([
                            'o_valuer' => $stversion,
                            'name' => $version_name // Update name too as per old script logic
                        ]);
                    }
                } else {
                    // Insert if missing
                    Option::create([
                        'name' => $version_name,
                        'o_valuer' => $stversion,
                        'o_type' => 'version',
                        'o_parent' => 0,
                        'o_order' => 0,
                        'o_mode' => '0'
                    ]);
                }

                // Synchronize public/sitemap.xsl version tag automatically
                $sitemapXslPath = public_path('sitemap.xsl');
                if (file_exists($sitemapXslPath) && is_writable($sitemapXslPath)) {
                    $xslContent = file_get_contents($sitemapXslPath);
                    $newXsl = preg_replace(
                        '/Powered by MyAds SEO Engine \| v[0-9\.]+/',
                        'Powered by MyAds SEO Engine | ' . SystemVersion::tag(),
                        $xslContent
                    );
                    if ($newXsl && $newXsl !== $xslContent) {
                        @file_put_contents($sitemapXslPath, $newXsl);
                    }
                }

                Cache::put($cacheKey, true, 60 * 60); // Cache for 1 hour

            } catch (\Exception $e) {
                // Ignore DB errors (e.g. during migration)
            }
        }

        return $next($request);
    }
}
