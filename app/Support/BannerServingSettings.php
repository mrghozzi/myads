<?php

namespace App\Support;

use App\Models\Option;
use Illuminate\Support\Facades\Schema;

class BannerServingSettings
{
    public const OPTION_TYPE = 'ads_settings';
    public const REPEAT_WINDOW_NAME = 'banner_repeat_window_minutes';
    public const DEFAULT_REPEAT_WINDOW_MINUTES = 1440;

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
}
