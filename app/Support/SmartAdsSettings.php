<?php

namespace App\Support;

use App\Models\Option;

class SmartAdsSettings
{
    public const OPTION_TYPE = 'smart_ads';
    public const POINTS_DIVISOR_NAME = 'smart_ads_points_divisor';
    public const DEFAULT_POINTS_DIVISOR = 4.0;

    public static function pointsDivisor(): float
    {
        $stored = Option::where('o_type', self::OPTION_TYPE)
            ->where('name', self::POINTS_DIVISOR_NAME)
            ->value('o_valuer');

        $value = is_numeric($stored) ? (float) $stored : self::DEFAULT_POINTS_DIVISOR;

        return $value > 0 ? $value : self::DEFAULT_POINTS_DIVISOR;
    }
}
