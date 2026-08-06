<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\Option;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class CacheWarmupService
{
    /**
     * Cache keys warmed during the selective warmup process.
     */
    public const CACHE_KEY_SITE_SETTINGS = 'myads_core_site_settings';
    public const CACHE_KEY_SYSTEM_OPTIONS = 'myads_core_system_options';
    public const CACHE_KEY_PRIMARY_MENUS = 'myads_core_primary_menus';
    public const CACHE_KEY_WARMUP_TIMESTAMP = 'myads_last_cache_warmup_at';

    /**
     * Selectively warm core application data right after cache purge.
     *
     * @return array Summary of pre-warmed entries.
     */
    public function warmupCoreData(): array
    {
        $warmed = [];

        // 1. Warm Site Settings
        try {
            if (Schema::hasTable('setting')) {
                $setting = Setting::first();
                if ($setting) {
                    Cache::put(self::CACHE_KEY_SITE_SETTINGS, $setting, 86400);
                    $warmed['site_settings'] = true;
                }
            }
        } catch (\Throwable $e) {
            $warmed['site_settings'] = false;
        }

        // 2. Warm System Options (Key/Value store)
        try {
            if (Schema::hasTable('options')) {
                $options = Option::query()
                    ->whereIn('o_type', ['system', 'plugin', 'config', 'ads'])
                    ->orWhereNull('o_type')
                    ->get();
                Cache::put(self::CACHE_KEY_SYSTEM_OPTIONS, $options, 3600);
                $warmed['system_options'] = count($options);
            }
        } catch (\Throwable $e) {
            $warmed['system_options'] = 0;
        }

        // 3. Warm Core Menus
        try {
            if (Schema::hasTable('menu')) {
                $menus = Menu::query()->orderBy('position', 'asc')->get();
                Cache::put(self::CACHE_KEY_PRIMARY_MENUS, $menus, 3600);
                $warmed['primary_menus'] = count($menus);
            }
        } catch (\Throwable $e) {
            $warmed['primary_menus'] = 0;
        }

        // 4. Record Warmup Timestamp
        Cache::put(self::CACHE_KEY_WARMUP_TIMESTAMP, now()->toIso8601String(), 86400);
        $warmed['timestamp'] = now()->toIso8601String();

        return $warmed;
    }

    /**
     * Retrieve pre-warmed site settings or fallback to DB.
     */
    public static function getSiteSettings(): ?Setting
    {
        if (Cache::has(self::CACHE_KEY_SITE_SETTINGS)) {
            return Cache::get(self::CACHE_KEY_SITE_SETTINGS);
        }

        try {
            if (Schema::hasTable('setting')) {
                $setting = Setting::first();
                if ($setting) {
                    Cache::put(self::CACHE_KEY_SITE_SETTINGS, $setting, 86400);
                }
                return $setting;
            }
        } catch (\Throwable) {}

        return null;
    }
}
