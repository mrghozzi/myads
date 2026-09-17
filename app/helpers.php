<?php

use App\Models\Setting;
use App\Models\Ad;
use Illuminate\Support\Facades\Schema;

if (!function_exists('theme_asset')) {
    /**
     * Generate an asset path for the current theme.
     * Optimized with static in-memory caching to eliminate redundant queries.
     *
     * @param  string  $path
     * @return string
     */
    function theme_asset($path)
    {
        static $resolvedTheme = null;

        if ($resolvedTheme === null) {
            $theme = $GLOBALS['MYADS_ACTIVE_THEME'] ?? null;

            if (!$theme) {
                try {
                    if (app()->bound('request')) {
                        $req = request();
                        if ($req->has('preview_theme') && is_dir(base_path('themes/' . $req->query('preview_theme')))) {
                            $theme = (string) $req->query('preview_theme');
                        } elseif ($req->has('theme_preview') && $req->has('theme') && is_dir(base_path('themes/' . $req->query('theme')))) {
                            $theme = (string) $req->query('theme');
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore container resolution edge cases
                }
            }

            if (!$theme) {
                try {
                    $schema = app()->bound(App\Services\V420SchemaService::class)
                        ? app(App\Services\V420SchemaService::class)
                        : null;

                    $hasSettingTable = $schema ? $schema->hasTable('setting') : Schema::hasTable('setting');

                    if ($hasSettingTable) {
                        $setting = Setting::first();
                        if ($setting && !empty($setting->styles)) {
                            $theme = $setting->styles;
                        }
                    }
                } catch (\Throwable $e) {
                    // Fallback to default
                }
            }

            $resolvedTheme = $theme ?: 'default';
            $GLOBALS['MYADS_ACTIVE_THEME'] = $resolvedTheme;
        }

        return asset("themes/{$resolvedTheme}/assets/{$path}");
    }
}

if (!function_exists('ads_site')) {
    /**
     * Get ad code by ID.
     * Optimized with in-memory caching to avoid database queries on every ad call.
     *
     * @param  int  $id
     * @return string
     */
    function ads_site($id)
    {
        static $adsCache = [];

        if (array_key_exists($id, $adsCache)) {
            return $adsCache[$id];
        }

        try {
            $schema = app()->bound(App\Services\V420SchemaService::class)
                ? app(App\Services\V420SchemaService::class)
                : null;

            $hasAdsTable = $schema ? $schema->hasTable('ads') : Schema::hasTable('ads');

            if ($hasAdsTable) {
                $ad = Ad::find($id);
                $adsCache[$id] = $ad ? ($ad->code_ads ?? '') : '';
                return $adsCache[$id];
            }
        } catch (\Throwable $e) {
            $adsCache[$id] = '';
            return '';
        }

        $adsCache[$id] = '';
        return '';
    }
}

if (!function_exists('locale_direction')) {
    /**
     * Resolve the visual direction for the active locale.
     */
    function locale_direction(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $normalized = strtolower((string) preg_replace('/[_-].*$/', '', $locale));

        return in_array($normalized, ['ar', 'fa'], true) ? 'rtl' : 'ltr';
    }
}

if (!function_exists('is_locale_rtl')) {
    /**
     * Determine whether the active locale should render right-to-left.
     */
    function is_locale_rtl(?string $locale = null): bool
    {
        return locale_direction($locale) === 'rtl';
    }
}

if (!function_exists('http_secure')) {
    /**
     * Return an Http PendingRequest that only skips SSL verification
     * in local/testing environments. In production, SSL is always verified
     * to prevent Man-in-the-Middle attacks.
     *
     * Usage: http_secure()->get($url) instead of Http::withoutVerifying()->get($url)
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    function http_secure(): \Illuminate\Http\Client\PendingRequest
    {
        $http = \Illuminate\Support\Facades\Http::withHeaders([
            'User-Agent' => 'MyAds/' . (\App\Support\SystemVersion::CURRENT ?? '4.6.0'),
        ]);

        // SECURITY: Only disable SSL verification in local/testing environments.
        // Production MUST verify SSL to prevent MITM attacks during update checks.
        if (app()->environment('local', 'testing')) {
            $http = $http->withoutVerifying();
        }

        return $http;
    }
}
