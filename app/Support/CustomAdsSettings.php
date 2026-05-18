<?php

namespace App\Support;

use App\Models\Option;
use Illuminate\Support\Facades\Schema;

class CustomAdsSettings
{
    public const OPTION_TYPE = 'custom_ads_settings';

    public const ENABLED = 'enabled';
    public const MARKETPLACE_ENABLED = 'marketplace_enabled';
    public const REQUIRE_REVIEW = 'require_review';
    public const MIN_TOTAL_PTS = 'min_total_pts';
    public const MIN_DAILY_PTS = 'min_daily_pts';
    public const MAX_DURATION_DAYS = 'max_duration_days';

    public const DEFAULTS = [
        self::ENABLED => '1',
        self::MARKETPLACE_ENABLED => '1',
        self::REQUIRE_REVIEW => '0',
        self::MIN_TOTAL_PTS => '1',
        self::MIN_DAILY_PTS => '1',
        self::MAX_DURATION_DAYS => '30',
    ];

    public static function all(): array
    {
        $values = self::DEFAULTS;

        try {
            if (Schema::hasTable('options')) {
                Option::query()
                    ->where('o_type', self::OPTION_TYPE)
                    ->get(['name', 'o_valuer'])
                    ->each(function (Option $option) use (&$values) {
                        $values[(string) $option->name] = (string) $option->o_valuer;
                    });
            }
        } catch (\Throwable) {
            return self::DEFAULTS;
        }

        return $values;
    }

    public static function get(string $name): string
    {
        return self::all()[$name] ?? (self::DEFAULTS[$name] ?? '');
    }

    public static function enabled(): bool
    {
        return self::get(self::ENABLED) === '1';
    }

    public static function marketplaceEnabled(): bool
    {
        return self::get(self::MARKETPLACE_ENABLED) === '1';
    }

    public static function requireReview(): bool
    {
        return self::get(self::REQUIRE_REVIEW) === '1';
    }

    public static function minTotalPts(): float
    {
        return max(0, (float) self::get(self::MIN_TOTAL_PTS));
    }

    public static function minDailyPts(): float
    {
        return max(0, (float) self::get(self::MIN_DAILY_PTS));
    }

    public static function maxDurationDays(): int
    {
        return max(1, (int) self::get(self::MAX_DURATION_DAYS));
    }

    public static function persist(array $values): void
    {
        foreach (self::DEFAULTS as $name => $default) {
            Option::updateOrCreate(
                [
                    'o_type' => self::OPTION_TYPE,
                    'name' => $name,
                ],
                [
                    'o_valuer' => (string) ($values[$name] ?? $default),
                    'o_parent' => 0,
                    'o_order' => 0,
                    'o_mode' => null,
                ]
            );
        }
    }
}
