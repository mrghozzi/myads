<?php

namespace App\Support;

use App\Models\Option;
use Illuminate\Support\Facades\Schema;

class VisitExchangeSettings
{
    public const OPTION_TYPE = 'ads_settings';
    
    public const DAILY_LIMIT_NAME = 'visit_daily_limit';
    public const DEFAULT_DAILY_LIMIT = 50;

    public const POINTS_REWARD_NAME = 'visit_points_reward';
    public const DEFAULT_POINTS_REWARD = 5.0;

    public const VU_REWARD_NAME = 'visit_vu_reward';
    public const DEFAULT_VU_REWARD = 0.5;

    public static function dailyLimit(): int
    {
        try {
            if (!Schema::hasTable('options')) {
                return self::DEFAULT_DAILY_LIMIT;
            }

            $value = Option::where('o_type', self::OPTION_TYPE)
                ->where('name', self::DAILY_LIMIT_NAME)
                ->value('o_valuer');

            if (!is_numeric($value)) {
                return self::DEFAULT_DAILY_LIMIT;
            }

            return max(1, (int) $value);
        } catch (\Throwable) {
            return self::DEFAULT_DAILY_LIMIT;
        }
    }

    public static function pointsReward(): float
    {
        try {
            if (!Schema::hasTable('options')) {
                return self::DEFAULT_POINTS_REWARD;
            }

            $value = Option::where('o_type', self::OPTION_TYPE)
                ->where('name', self::POINTS_REWARD_NAME)
                ->value('o_valuer');

            if (!is_numeric($value)) {
                return self::DEFAULT_POINTS_REWARD;
            }

            return max(0.0, (float) $value);
        } catch (\Throwable) {
            return self::DEFAULT_POINTS_REWARD;
        }
    }

    public static function vuReward(): float
    {
        try {
            if (!Schema::hasTable('options')) {
                return self::DEFAULT_VU_REWARD;
            }

            $value = Option::where('o_type', self::OPTION_TYPE)
                ->where('name', self::VU_REWARD_NAME)
                ->value('o_valuer');

            if (!is_numeric($value)) {
                return self::DEFAULT_VU_REWARD;
            }

            return max(0.0, (float) $value);
        } catch (\Throwable) {
            return self::DEFAULT_VU_REWARD;
        }
    }
}
