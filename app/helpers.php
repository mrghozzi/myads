<?php

use App\Models\Setting;
use App\Models\Ad;
use Illuminate\Support\Facades\Schema;

if (!function_exists('theme_asset')) {
    /**
     * Generate an asset path for the current theme.
     *
     * @param  string  $path
     * @return string
     */
    function theme_asset($path)
    {
        $theme = 'default';

        try {
            // Attempt to get the theme from settings, cached if possible
            // For now, simple query, but in production should be cached
            if (Schema::hasTable('setting')) {
                $setting = Setting::first();
                if ($setting && !empty($setting->styles)) {
                    $theme = $setting->styles;
                }
            }
        } catch (\Throwable $e) {
            // Fallback to default
        }

        if (request()->has('preview_theme') && is_dir(base_path('themes/' . request()->query('preview_theme')))) {
            $theme = (string) request()->query('preview_theme');
        } elseif (request()->has('theme_preview') && request()->has('theme') && is_dir(base_path('themes/' . request()->query('theme')))) {
            $theme = (string) request()->query('theme');
        }

        return asset("themes/{$theme}/assets/{$path}");
    }
}

if (!function_exists('ads_site')) {
    /**
     * Get ad code by ID.
     *
     * @param  int  $id
     * @return string
     */
    function ads_site($id)
    {
        try {
            if (Schema::hasTable('ads')) {
                $ad = Ad::find($id);
                if ($ad) {
                    return $ad->code_ads;
                }
            }
        } catch (\Throwable $e) {
            return '';
        }
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
            'User-Agent' => 'MyAds/' . (\App\Support\SystemVersion::CURRENT ?? '4.5.3'),
        ]);

        // SECURITY: Only disable SSL verification in local/testing environments.
        // Production MUST verify SSL to prevent MITM attacks during update checks.
        if (app()->environment('local', 'testing')) {
            $http = $http->withoutVerifying();
        }

        return $http;
    }
}
