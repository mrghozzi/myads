<?php

namespace App\Support;

use App\Models\Option;
use Illuminate\Support\Facades\Schema;

class BannerServingSettings
{
    public const OPTION_TYPE = 'ads_settings';
    public const REPEAT_WINDOW_NAME = 'banner_repeat_window_minutes';
    public const DEFAULT_REPEAT_WINDOW_MINUTES = 1440;

    public const FALLBACK_TO_SEEN_NAME = 'banner_fallback_to_seen';
    public const DEFAULT_FALLBACK_TO_SEEN = true;

    public const PREVENT_CONCURRENT_NAME = 'banner_prevent_concurrent_duplicates';
    public const DEFAULT_PREVENT_CONCURRENT = true;

    public static function repeatWindowMinutes(): int
    {
        try {
            if (!Schema::hasTable('options')) {
                return self::DEFAULT_REPEAT_WINDOW_MINUTES;
            }

            $value = Option::where('o_type', self::OPTION_TYPE)
                ->where('name', self::REPEAT_WINDOW_NAME)
                ->value('o_valuer');

            if (!is_numeric($value)) {
                return self::DEFAULT_REPEAT_WINDOW_MINUTES;
            }

            return max(0, (int) $value);
        } catch (\Throwable) {
            return self::DEFAULT_REPEAT_WINDOW_MINUTES;
        }
    }

    public static function fallbackToSeenAds(): bool
    {
        try {
            if (!Schema::hasTable('options')) {
                return self::DEFAULT_FALLBACK_TO_SEEN;
            }

            $option = Option::where('o_type', self::OPTION_TYPE)
                ->where('name', self::FALLBACK_TO_SEEN_NAME)
                ->first();

            if (!$option) {
                return self::DEFAULT_FALLBACK_TO_SEEN;
            }

            return filter_var($option->o_valuer, FILTER_VALIDATE_BOOLEAN);
        } catch (\Throwable) {
            return self::DEFAULT_FALLBACK_TO_SEEN;
        }
    }

    public static function preventConcurrentDuplicates(): bool
    {
        try {
            if (!Schema::hasTable('options')) {
                return self::DEFAULT_PREVENT_CONCURRENT;
            }

            $option = Option::where('o_type', self::OPTION_TYPE)
                ->where('name', self::PREVENT_CONCURRENT_NAME)
                ->first();

            if (!$option) {
                return self::DEFAULT_PREVENT_CONCURRENT;
            }

            return filter_var($option->o_valuer, FILTER_VALIDATE_BOOLEAN);
        } catch (\Throwable) {
            return self::DEFAULT_PREVENT_CONCURRENT;
        }
    }
}
