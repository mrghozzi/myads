<?php

namespace App\Support;

use App\Models\Option;

class CommunityFeedSettings
{
    public const OPTION_TYPE = 'community_feed_settings';

    public const DEFAULTS = [
        'freshness_base_score' => 700.0,
        'freshness_decay_exponent' => 1.35,
        'freshness_suppression_after_hours' => 72,
        'freshness_suppression_multiplier' => 0.18,
        'view_weight' => 0.20,
        'max_views_score' => 25.0,
        'total_reaction_weight' => 0.35,
        'total_comment_weight' => 0.45,
        'total_repost_weight' => 0.75,
        'recent_reaction_weight' => 4.0,
        'recent_comment_weight' => 6.0,
        'recent_repost_weight' => 8.0,
        'rapid_reaction_weight' => 2.0,
        'rapid_comment_weight' => 3.0,
        'rapid_repost_weight' => 4.0,
        'following_boost' => 24.0,
        'author_affinity_boost' => 10.0,
        'content_affinity_boost' => 8.0,
        'social_proof_boost' => 12.0,
        'rapid_window_hours' => 6,
        'trend_window_hours' => 24,
        'rescue_max_age_hours' => 168,
        'rescue_min_recent_reactions' => 5,
        'rescue_min_recent_comments' => 3,
        'rescue_min_recent_reposts' => 1,
        'repeat_author_penalty' => 12.0,
        'repeat_type_penalty' => 8.0,
        'fresh_candidate_hours' => 72,
        'fresh_candidate_limit' => 350,
        'rescue_candidate_limit' => 200,
        'cache_ttl_seconds' => 300,
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

    public static function signature(): string
    {
        return md5((string) json_encode(self::all(), JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION));
    }

    public static function clearCache(): void
    {
        self::$cached = null;
    }

    public static function normalizeIncoming(array $values): array
    {
        $defaults = self::DEFAULTS;

        $settings = [
            'freshness_base_score' => self::nonNegativeFloat($values['freshness_base_score'] ?? $defaults['freshness_base_score']),
            'freshness_decay_exponent' => self::positiveFloat($values['freshness_decay_exponent'] ?? $defaults['freshness_decay_exponent']),
            'freshness_suppression_after_hours' => self::positiveInt($values['freshness_suppression_after_hours'] ?? $defaults['freshness_suppression_after_hours']),
            'freshness_suppression_multiplier' => self::fractionFloat($values['freshness_suppression_multiplier'] ?? $defaults['freshness_suppression_multiplier']),
            'view_weight' => self::nonNegativeFloat($values['view_weight'] ?? $defaults['view_weight']),
            'max_views_score' => self::nonNegativeFloat($values['max_views_score'] ?? $defaults['max_views_score']),
            'total_reaction_weight' => self::nonNegativeFloat($values['total_reaction_weight'] ?? $defaults['total_reaction_weight']),
            'total_comment_weight' => self::nonNegativeFloat($values['total_comment_weight'] ?? $defaults['total_comment_weight']),
            'total_repost_weight' => self::nonNegativeFloat($values['total_repost_weight'] ?? $defaults['total_repost_weight']),
            'recent_reaction_weight' => self::nonNegativeFloat($values['recent_reaction_weight'] ?? $defaults['recent_reaction_weight']),
            'recent_comment_weight' => self::nonNegativeFloat($values['recent_comment_weight'] ?? $defaults['recent_comment_weight']),
            'recent_repost_weight' => self::nonNegativeFloat($values['recent_repost_weight'] ?? $defaults['recent_repost_weight']),
            'rapid_reaction_weight' => self::nonNegativeFloat($values['rapid_reaction_weight'] ?? $defaults['rapid_reaction_weight']),
            'rapid_comment_weight' => self::nonNegativeFloat($values['rapid_comment_weight'] ?? $defaults['rapid_comment_weight']),
            'rapid_repost_weight' => self::nonNegativeFloat($values['rapid_repost_weight'] ?? $defaults['rapid_repost_weight']),
            'following_boost' => self::nonNegativeFloat($values['following_boost'] ?? $defaults['following_boost']),
            'author_affinity_boost' => self::nonNegativeFloat($values['author_affinity_boost'] ?? $defaults['author_affinity_boost']),
            'content_affinity_boost' => self::nonNegativeFloat($values['content_affinity_boost'] ?? $defaults['content_affinity_boost']),
            'social_proof_boost' => self::nonNegativeFloat($values['social_proof_boost'] ?? $defaults['social_proof_boost']),
            'rapid_window_hours' => self::positiveInt($values['rapid_window_hours'] ?? $defaults['rapid_window_hours']),
            'trend_window_hours' => self::positiveInt($values['trend_window_hours'] ?? $defaults['trend_window_hours']),
            'rescue_max_age_hours' => self::positiveInt($values['rescue_max_age_hours'] ?? $defaults['rescue_max_age_hours']),
            'rescue_min_recent_reactions' => self::nonNegativeInt($values['rescue_min_recent_reactions'] ?? $defaults['rescue_min_recent_reactions']),
            'rescue_min_recent_comments' => self::nonNegativeInt($values['rescue_min_recent_comments'] ?? $defaults['rescue_min_recent_comments']),
            'rescue_min_recent_reposts' => self::nonNegativeInt($values['rescue_min_recent_reposts'] ?? $defaults['rescue_min_recent_reposts']),
            'repeat_author_penalty' => self::nonNegativeFloat($values['repeat_author_penalty'] ?? $defaults['repeat_author_penalty']),
            'repeat_type_penalty' => self::nonNegativeFloat($values['repeat_type_penalty'] ?? $defaults['repeat_type_penalty']),
            'fresh_candidate_hours' => self::positiveInt($values['fresh_candidate_hours'] ?? $defaults['fresh_candidate_hours']),
            'fresh_candidate_limit' => self::positiveInt($values['fresh_candidate_limit'] ?? $defaults['fresh_candidate_limit']),
            'rescue_candidate_limit' => self::positiveInt($values['rescue_candidate_limit'] ?? $defaults['rescue_candidate_limit']),
            'cache_ttl_seconds' => self::nonNegativeInt($values['cache_ttl_seconds'] ?? $defaults['cache_ttl_seconds']),
        ];

        $settings['trend_window_hours'] = max($settings['trend_window_hours'], $settings['rapid_window_hours']);
        $settings['freshness_suppression_after_hours'] = max($settings['freshness_suppression_after_hours'], $settings['trend_window_hours']);
        $settings['fresh_candidate_hours'] = max($settings['fresh_candidate_hours'], $settings['freshness_suppression_after_hours']);
        $settings['rescue_max_age_hours'] = max(
            $settings['rescue_max_age_hours'],
            $settings['freshness_suppression_after_hours'],
            $settings['trend_window_hours']
        );

        return self::normalize($settings);
    }

    private static function normalize(array $settings): array
    {
        return [
            'freshness_base_score' => self::nonNegativeFloat($settings['freshness_base_score'] ?? self::DEFAULTS['freshness_base_score']),
            'freshness_decay_exponent' => self::positiveFloat($settings['freshness_decay_exponent'] ?? self::DEFAULTS['freshness_decay_exponent']),
            'freshness_suppression_after_hours' => self::positiveInt($settings['freshness_suppression_after_hours'] ?? self::DEFAULTS['freshness_suppression_after_hours']),
            'freshness_suppression_multiplier' => self::fractionFloat($settings['freshness_suppression_multiplier'] ?? self::DEFAULTS['freshness_suppression_multiplier']),
            'view_weight' => self::nonNegativeFloat($settings['view_weight'] ?? self::DEFAULTS['view_weight']),
            'max_views_score' => self::nonNegativeFloat($settings['max_views_score'] ?? self::DEFAULTS['max_views_score']),
            'total_reaction_weight' => self::nonNegativeFloat($settings['total_reaction_weight'] ?? self::DEFAULTS['total_reaction_weight']),
            'total_comment_weight' => self::nonNegativeFloat($settings['total_comment_weight'] ?? self::DEFAULTS['total_comment_weight']),
            'total_repost_weight' => self::nonNegativeFloat($settings['total_repost_weight'] ?? self::DEFAULTS['total_repost_weight']),
            'recent_reaction_weight' => self::nonNegativeFloat($settings['recent_reaction_weight'] ?? self::DEFAULTS['recent_reaction_weight']),
            'recent_comment_weight' => self::nonNegativeFloat($settings['recent_comment_weight'] ?? self::DEFAULTS['recent_comment_weight']),
            'recent_repost_weight' => self::nonNegativeFloat($settings['recent_repost_weight'] ?? self::DEFAULTS['recent_repost_weight']),
            'rapid_reaction_weight' => self::nonNegativeFloat($settings['rapid_reaction_weight'] ?? self::DEFAULTS['rapid_reaction_weight']),
            'rapid_comment_weight' => self::nonNegativeFloat($settings['rapid_comment_weight'] ?? self::DEFAULTS['rapid_comment_weight']),
            'rapid_repost_weight' => self::nonNegativeFloat($settings['rapid_repost_weight'] ?? self::DEFAULTS['rapid_repost_weight']),
            'following_boost' => self::nonNegativeFloat($settings['following_boost'] ?? self::DEFAULTS['following_boost']),
            'author_affinity_boost' => self::nonNegativeFloat($settings['author_affinity_boost'] ?? self::DEFAULTS['author_affinity_boost']),
            'content_affinity_boost' => self::nonNegativeFloat($settings['content_affinity_boost'] ?? self::DEFAULTS['content_affinity_boost']),
            'social_proof_boost' => self::nonNegativeFloat($settings['social_proof_boost'] ?? self::DEFAULTS['social_proof_boost']),
            'rapid_window_hours' => self::positiveInt($settings['rapid_window_hours'] ?? self::DEFAULTS['rapid_window_hours']),
            'trend_window_hours' => self::positiveInt($settings['trend_window_hours'] ?? self::DEFAULTS['trend_window_hours']),
            'rescue_max_age_hours' => self::positiveInt($settings['rescue_max_age_hours'] ?? self::DEFAULTS['rescue_max_age_hours']),
            'rescue_min_recent_reactions' => self::nonNegativeInt($settings['rescue_min_recent_reactions'] ?? self::DEFAULTS['rescue_min_recent_reactions']),
            'rescue_min_recent_comments' => self::nonNegativeInt($settings['rescue_min_recent_comments'] ?? self::DEFAULTS['rescue_min_recent_comments']),
            'rescue_min_recent_reposts' => self::nonNegativeInt($settings['rescue_min_recent_reposts'] ?? self::DEFAULTS['rescue_min_recent_reposts']),
            'repeat_author_penalty' => self::nonNegativeFloat($settings['repeat_author_penalty'] ?? self::DEFAULTS['repeat_author_penalty']),
            'repeat_type_penalty' => self::nonNegativeFloat($settings['repeat_type_penalty'] ?? self::DEFAULTS['repeat_type_penalty']),
            'fresh_candidate_hours' => self::positiveInt($settings['fresh_candidate_hours'] ?? self::DEFAULTS['fresh_candidate_hours']),
            'fresh_candidate_limit' => self::positiveInt($settings['fresh_candidate_limit'] ?? self::DEFAULTS['fresh_candidate_limit']),
            'rescue_candidate_limit' => self::positiveInt($settings['rescue_candidate_limit'] ?? self::DEFAULTS['rescue_candidate_limit']),
            'cache_ttl_seconds' => self::nonNegativeInt($settings['cache_ttl_seconds'] ?? self::DEFAULTS['cache_ttl_seconds']),
        ];
    }

    private static function nonNegativeFloat(mixed $value): float
    {
        return max(0, round((float) $value, 4));
    }

    private static function positiveFloat(mixed $value): float
    {
        return max(0.0001, round((float) $value, 4));
    }

    private static function fractionFloat(mixed $value): float
    {
        return min(1, max(0, round((float) $value, 4)));
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
