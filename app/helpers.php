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
        $cleanPath = ltrim((string) $path, '/\\');
        $activeTheme = $GLOBALS['MYADS_ACTIVE_THEME'] ?? null;

        if (!$activeTheme) {
            try {
                if (isset($_GET['preview_theme']) && is_string($_GET['preview_theme'])) {
                    $activeTheme = trim($_GET['preview_theme']);
                } elseif (isset($_COOKIE['preview_theme']) && is_string($_COOKIE['preview_theme'])) {
                    $activeTheme = trim($_COOKIE['preview_theme']);
                } elseif (app()->bound('request')) {
                    $req = request();
                    if ($req->has('preview_theme') && is_dir(base_path('themes/' . $req->query('preview_theme')))) {
                        $activeTheme = (string) $req->query('preview_theme');
                    } elseif ($req->has('theme_preview') && $req->has('theme') && is_dir(base_path('themes/' . $req->query('theme')))) {
                        $activeTheme = (string) $req->query('theme');
                    } elseif ($req->cookie('preview_theme') && is_dir(base_path('themes/' . $req->cookie('preview_theme')))) {
                        $activeTheme = (string) $req->cookie('preview_theme');
                    }
                }
            } catch (\Throwable $e) {
                // Ignore container resolution edge cases
            }
        }

        if (!$activeTheme) {
            try {
                $schema = app()->bound(App\Services\V420SchemaService::class)
                    ? app(App\Services\V420SchemaService::class)
                    : null;

                $hasSettingTable = $schema ? $schema->hasTable('setting') : Schema::hasTable('setting');

                if ($hasSettingTable) {
                    $setting = Setting::first();
                    if ($setting && !empty($setting->styles)) {
                        $activeTheme = $setting->styles;
                    }
                }
            } catch (\Throwable $e) {
                // Fallback to default
            }
        }

        $activeTheme = $activeTheme ?: 'default';
        $parentTheme = $GLOBALS['MYADS_PARENT_THEME'] ?? null;

        // 1. Check if active theme has the asset physically in public/themes/{activeTheme}/assets/ or themes/{activeTheme}/assets/
        if (file_exists(public_path("themes/{$activeTheme}/assets/{$cleanPath}")) || file_exists(base_path("themes/{$activeTheme}/assets/{$cleanPath}"))) {
            return asset("themes/{$activeTheme}/assets/{$cleanPath}");
        }

        // 2. Check parent theme asset if defined
        if (!empty($parentTheme)) {
            if (file_exists(public_path("themes/{$parentTheme}/assets/{$cleanPath}")) || file_exists(base_path("themes/{$parentTheme}/assets/{$cleanPath}"))) {
                return asset("themes/{$parentTheme}/assets/{$cleanPath}");
            }
        }

        // 3. Fallback to default theme asset
        if ($activeTheme !== 'default' && $parentTheme !== 'default') {
            if (file_exists(public_path("themes/default/assets/{$cleanPath}")) || file_exists(base_path("themes/default/assets/{$cleanPath}"))) {
                return asset("themes/default/assets/{$cleanPath}");
            }
        }

        return asset("themes/{$activeTheme}/assets/{$cleanPath}");
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
        if (auth()->check()) {
            try {
                if (class_exists(\App\Services\Billing\SubscriptionEntitlementService::class)
                    && app(\App\Services\Billing\SubscriptionEntitlementService::class)->hasAdFreeForUser(auth()->id())) {
                    return '';
                }
            } catch (\Throwable) {
                // Ignore entitlement check failures
            }
        }

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
            'User-Agent' => 'MyAds/' . (\App\Support\SystemVersion::CURRENT ?? '4.7.0'),
        ]);

        // SECURITY: Only disable SSL verification in local/testing environments.
        // Production MUST verify SSL to prevent MITM attacks during update checks.
        if (app()->environment('local', 'testing')) {
            $http = $http->withoutVerifying();
        } else {
            // On environments without a default system CA bundle (e.g. Windows/XAMPP),
            // auto-detect available local bundles if openssl.cafile / curl.cainfo are not set in php.ini.
            if (!ini_get('curl.cainfo') && !ini_get('openssl.cafile')) {
                $candidates = [
                    'E:/xampp/apache/bin/curl-ca-bundle.crt',
                    'C:/xampp/apache/bin/curl-ca-bundle.crt',
                    'D:/xampp/apache/bin/curl-ca-bundle.crt',
                    'C:/windows/system32/curl-ca-bundle.crt',
                    'C:/windows/curl-ca-bundle.crt',
                ];
                foreach ($candidates as $caPath) {
                    if (file_exists($caPath)) {
                        $http = $http->withOptions(['verify' => $caPath]);
                        break;
                    }
                }
            }
        }

        return $http;
    }
}

if (!function_exists('csp_nonce')) {
    /**
     * Get the cryptographically random CSP nonce for the current request.
     * Used for strict Nonce-based Content-Security-Policy script and style execution.
     *
     * @return string
     */
    function csp_nonce(): string
    {
        try {
            if (app()->bound('request')) {
                $nonce = request()->attributes->get('csp_nonce');
                if (!empty($nonce)) {
                    return (string) $nonce;
                }
            }
            if (function_exists('view') && view()->shared('cspNonce')) {
                return (string) view()->shared('cspNonce');
            }
        } catch (\Throwable) {
            // Safe fallback
        }
        return '';
    }
}

// Ensure Hook helper functions and facade are always loaded
if (!class_exists(\App\Helpers\Hooks::class, false)) {
    require_once __DIR__ . '/Helpers/Hooks.php';
}

