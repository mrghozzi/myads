<?php

namespace App\Support;

use App\Models\Option;

class StatusPromotionSettings
{
    public const OPTION_TYPE = 'status_promotion_settings';

    public const DEFAULTS = [
        'enabled' => 1,
        'price_per_100_views_pts' => 2.0,
        'price_per_reaction_goal_pts' => 3.0,
        'price_per_comment_goal_pts' => 5.0,
        'price_per_day_pts' => 8.0,
        'estimated_views_per_reaction' => 25,
        'estimated_views_per_comment' => 40,
        'estimated_views_per_day' => 120,
        'min_views_target' => 100,
        'max_views_target' => 10000,
        'min_reactions_target' => 5,
        'max_reactions_target' => 500,
        'min_comments_target' => 2,
        'max_comments_target' => 200,
        'min_days_target' => 1,
        'max_days_target' => 30,
        'per_page_limit' => 2,
        'min_gap_between_promotions' => 6,
        'viewer_repeat_cooldown_hours' => 8,
    ];

    private static ?array $cached = null;

    public static function all(): array
    {
        if (self::$cached !== null) {
            return self::$cached;
        }

        $settings = self::DEFAULTS;

        try {
            $rows = Option::where('o_type', self::OPTION_TYPE)->get(['name', 'o_valuer']);
        } catch (\Throwable) {
            return self::$cached = self::normalize($settings);
        }

        foreach ($rows as $row) {
            if (!array_key_exists($row->name, $settings)) {
                continue;
            }

            $settings[$row->name] = $row->o_valuer;
        }

        return self::$cached = self::normalize($settings);
    }

    public static function get(string $key, mixed $fallback = null): mixed
    {
        $settings = self::all();

        return $settings[$key] ?? $fallback;
    }

    public static function save(array $values): void
    {
        $normalized = self::normalizeIncoming($values);

        foreach ($normalized as $name => $value) {
            Option::updateOrCreate(
                ['o_type' => self::OPTION_TYPE, 'name' => $name],
                [
                    'o_valuer' => (string) $value,
                    'o_parent' => 0,
                    'o_order' => 0,
                    'o_mode' => time(),
                ]
            );
        }

        self::$cached = $normalized;
    }

    public static function normalizeIncoming(array $values): array
    {
        $defaults = self::DEFAULTS;

        $settings = [
            'enabled' => !empty($values['enabled']) ? 1 : 0,
            'price_per_100_views_pts' => self::nonNegativeFloat($values['price_per_100_views_pts'] ?? $defaults['price_per_100_views_pts']),
            'price_per_reaction_goal_pts' => self::nonNegativeFloat($values['price_per_reaction_goal_pts'] ?? $defaults['price_per_reaction_goal_pts']),
            'price_per_comment_goal_pts' => self::nonNegativeFloat($values['price_per_comment_goal_pts'] ?? $defaults['price_per_comment_goal_pts']),
            'price_per_day_pts' => self::nonNegativeFloat($values['price_per_day_pts'] ?? $defaults['price_per_day_pts']),
            'estimated_views_per_reaction' => self::positiveInt($values['estimated_views_per_reaction'] ?? $defaults['estimated_views_per_reaction']),
            'estimated_views_per_comment' => self::positiveInt($values['estimated_views_per_comment'] ?? $defaults['estimated_views_per_comment']),
            'estimated_views_per_day' => self::positiveInt($values['estimated_views_per_day'] ?? $defaults['estimated_views_per_day']),
            'min_views_target' => self::positiveInt($values['min_views_target'] ?? $defaults['min_views_target']),
            'max_views_target' => self::positiveInt($values['max_views_target'] ?? $defaults['max_views_target']),
            'min_reactions_target' => self::positiveInt($values['min_reactions_target'] ?? $defaults['min_reactions_target']),
            'max_reactions_target' => self::positiveInt($values['max_reactions_target'] ?? $defaults['max_reactions_target']),
            'min_comments_target' => self::positiveInt($values['min_comments_target'] ?? $defaults['min_comments_target']),
            'max_comments_target' => self::positiveInt($values['max_comments_target'] ?? $defaults['max_comments_target']),
            'min_days_target' => self::positiveInt($values['min_days_target'] ?? $defaults['min_days_target']),
            'max_days_target' => self::positiveInt($values['max_days_target'] ?? $defaults['max_days_target']),
            'per_page_limit' => self::positiveInt($values['per_page_limit'] ?? $defaults['per_page_limit']),
            'min_gap_between_promotions' => self::nonNegativeInt($values['min_gap_between_promotions'] ?? $defaults['min_gap_between_promotions']),
            'viewer_repeat_cooldown_hours' => self::nonNegativeInt($values['viewer_repeat_cooldown_hours'] ?? $defaults['viewer_repeat_cooldown_hours']),
        ];

        foreach (['views', 'reactions', 'comments', 'days'] as $objective) {
            $minKey = 'min_' . $objective . '_target';
            $maxKey = 'max_' . $objective . '_target';
            if ($settings[$maxKey] < $settings[$minKey]) {
                $settings[$maxKey] = $settings[$minKey];
            }
        }

        return self::normalize($settings);
    }

    public static function minTargetFor(string $objective): int
    {
        return (int) self::get('min_' . $objective . '_target', 1);
    }

    public static function maxTargetFor(string $objective): int
    {
        return (int) self::get('max_' . $objective . '_target', 100);
    }

    public static function estimateFor(string $objective): int
    {
        return match ($objective) {
            'reactions' => (int) self::get('estimated_views_per_reaction', self::DEFAULTS['estimated_views_per_reaction']),
            'comments' => (int) self::get('estimated_views_per_comment', self::DEFAULTS['estimated_views_per_comment']),
            default => (int) self::get('estimated_views_per_day', self::DEFAULTS['estimated_views_per_day']),
        };
    }

    private static function normalize(array $settings): array
    {
        return [
            'enabled' => !empty($settings['enabled']) ? 1 : 0,
            'price_per_100_views_pts' => self::nonNegativeFloat($settings['price_per_100_views_pts'] ?? self::DEFAULTS['price_per_100_views_pts']),
            'price_per_reaction_goal_pts' => self::nonNegativeFloat($settings['price_per_reaction_goal_pts'] ?? self::DEFAULTS['price_per_reaction_goal_pts']),
            'price_per_comment_goal_pts' => self::nonNegativeFloat($settings['price_per_comment_goal_pts'] ?? self::DEFAULTS['price_per_comment_goal_pts']),
            'price_per_day_pts' => self::nonNegativeFloat($settings['price_per_day_pts'] ?? self::DEFAULTS['price_per_day_pts']),
            'estimated_views_per_reaction' => self::positiveInt($settings['estimated_views_per_reaction'] ?? self::DEFAULTS['estimated_views_per_reaction']),
            'estimated_views_per_comment' => self::positiveInt($settings['estimated_views_per_comment'] ?? self::DEFAULTS['estimated_views_per_comment']),
            'estimated_views_per_day' => self::positiveInt($settings['estimated_views_per_day'] ?? self::DEFAULTS['estimated_views_per_day']),
            'min_views_target' => self::positiveInt($settings['min_views_target'] ?? self::DEFAULTS['min_views_target']),
            'max_views_target' => self::positiveInt($settings['max_views_target'] ?? self::DEFAULTS['max_views_target']),
            'min_reactions_target' => self::positiveInt($settings['min_reactions_target'] ?? self::DEFAULTS['min_reactions_target']),
            'max_reactions_target' => self::positiveInt($settings['max_reactions_target'] ?? self::DEFAULTS['max_reactions_target']),
            'min_comments_target' => self::positiveInt($settings['min_comments_target'] ?? self::DEFAULTS['min_comments_target']),
            'max_comments_target' => self::positiveInt($settings['max_comments_target'] ?? self::DEFAULTS['max_comments_target']),
            'min_days_target' => self::positiveInt($settings['min_days_target'] ?? self::DEFAULTS['min_days_target']),
            'max_days_target' => self::positiveInt($settings['max_days_target'] ?? self::DEFAULTS['max_days_target']),
            'per_page_limit' => self::positiveInt($settings['per_page_limit'] ?? self::DEFAULTS['per_page_limit']),
            'min_gap_between_promotions' => self::nonNegativeInt($settings['min_gap_between_promotions'] ?? self::DEFAULTS['min_gap_between_promotions']),
            'viewer_repeat_cooldown_hours' => self::nonNegativeInt($settings['viewer_repeat_cooldown_hours'] ?? self::DEFAULTS['viewer_repeat_cooldown_hours']),
        ];
    }

    private static function nonNegativeFloat(mixed $value): float
    {
        return max(0, round((float) $value, 2));
    }

    private static function positiveInt(mixed $value): int
    {
        return max(1, (int) $value);
    }

    private static function nonNegativeInt(mixed $value): int
    {
        return max(0, (int) $value);
    }
}
