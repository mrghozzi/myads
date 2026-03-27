<?php

namespace App\Support;

use App\Models\Option;

class SecuritySettings
{
    public const OPTION_TYPE = 'security_settings';

    public const DEFAULT_BLOCKED_USERNAMES = [
        'admin',
        'administrator',
        'webmaster',
        'support',
        'helpdesk',
        'seo',
        'marketing',
        'sales',
        'casino',
        'forex',
        'loan',
        'loans',
        'viagra',
        'crypto',
        'bitcoin',
        'bonus',
        'airdrop',
        'freegift',
        'sex',
        'escort',
    ];

    public const DEFAULTS = [
        'link_safety_enabled' => 1,
        'link_safety_apply_posts' => 1,
        'link_safety_apply_comments' => 1,
        'link_safety_apply_messages' => 1,
        'link_safety_apply_ads' => 1,
        'blacklist_domains' => '',
        'blacklist_url_patterns' => '',
        'block_spam_usernames' => 1,
        'blocked_usernames' => '',
        'blocked_email_domains' => '',
        'cooldown_post_seconds' => 20,
        'cooldown_comment_seconds' => 10,
        'cooldown_forum_topic_seconds' => 60,
        'cooldown_private_message_seconds' => 8,
        'registration_ip_daily_limit' => 3,
        'admin_password_confirmation_enabled' => 0,
        'admin_password_confirmation_ttl_minutes' => 30,
        'login_max_attempts_per_ip_15m' => 12,
        'login_max_attempts_per_account_15m' => 6,
        'max_active_sessions_per_user' => 5,
        'private_message_encryption_enabled' => 0,
        'public_member_ids_enabled' => 0,
    ];

    public static function all(): array
    {
        $settings = self::DEFAULTS;
        $settings['blocked_usernames'] = implode(PHP_EOL, self::DEFAULT_BLOCKED_USERNAMES);

        try {
            $rows = Option::where('o_type', self::OPTION_TYPE)->get(['name', 'o_valuer']);
        } catch (\Throwable) {
            return self::normalize($settings);
        }

        foreach ($rows as $row) {
            if (!array_key_exists($row->name, $settings)) {
                continue;
            }

            $settings[$row->name] = $row->o_valuer;
        }

        return self::normalize($settings);
    }

    public static function get(string $key, mixed $fallback = null): mixed
    {
        $settings = self::all();

        return $settings[$key] ?? $fallback;
    }

    public static function save(array $values): void
    {
        foreach (self::normalizeIncoming($values) as $name => $value) {
            Option::updateOrCreate(
                ['o_type' => self::OPTION_TYPE, 'name' => $name],
                ['o_valuer' => (string) $value]
            );
        }
    }

    public static function normalizeIncoming(array $values): array
    {
        $defaults = self::DEFAULTS;

        return self::normalize([
            'link_safety_enabled' => !empty($values['link_safety_enabled']) ? 1 : 0,
            'link_safety_apply_posts' => !empty($values['link_safety_apply_posts']) ? 1 : 0,
            'link_safety_apply_comments' => !empty($values['link_safety_apply_comments']) ? 1 : 0,
            'link_safety_apply_messages' => !empty($values['link_safety_apply_messages']) ? 1 : 0,
            'link_safety_apply_ads' => !empty($values['link_safety_apply_ads']) ? 1 : 0,
            'blacklist_domains' => self::sanitizeList((string) ($values['blacklist_domains'] ?? $defaults['blacklist_domains'])),
            'blacklist_url_patterns' => self::sanitizeList((string) ($values['blacklist_url_patterns'] ?? $defaults['blacklist_url_patterns'])),
            'block_spam_usernames' => !empty($values['block_spam_usernames']) ? 1 : 0,
            'blocked_usernames' => self::sanitizeList((string) ($values['blocked_usernames'] ?? implode(PHP_EOL, self::DEFAULT_BLOCKED_USERNAMES))),
            'blocked_email_domains' => self::sanitizeList((string) ($values['blocked_email_domains'] ?? $defaults['blocked_email_domains'])),
            'cooldown_post_seconds' => self::nonNegativeInt($values['cooldown_post_seconds'] ?? $defaults['cooldown_post_seconds']),
            'cooldown_comment_seconds' => self::nonNegativeInt($values['cooldown_comment_seconds'] ?? $defaults['cooldown_comment_seconds']),
            'cooldown_forum_topic_seconds' => self::nonNegativeInt($values['cooldown_forum_topic_seconds'] ?? $defaults['cooldown_forum_topic_seconds']),
            'cooldown_private_message_seconds' => self::nonNegativeInt($values['cooldown_private_message_seconds'] ?? $defaults['cooldown_private_message_seconds']),
            'registration_ip_daily_limit' => self::nonNegativeInt($values['registration_ip_daily_limit'] ?? $defaults['registration_ip_daily_limit']),
            'admin_password_confirmation_enabled' => !empty($values['admin_password_confirmation_enabled']) ? 1 : 0,
            'admin_password_confirmation_ttl_minutes' => self::nonNegativeInt($values['admin_password_confirmation_ttl_minutes'] ?? $defaults['admin_password_confirmation_ttl_minutes']),
            'login_max_attempts_per_ip_15m' => self::nonNegativeInt($values['login_max_attempts_per_ip_15m'] ?? $defaults['login_max_attempts_per_ip_15m']),
            'login_max_attempts_per_account_15m' => self::nonNegativeInt($values['login_max_attempts_per_account_15m'] ?? $defaults['login_max_attempts_per_account_15m']),
            'max_active_sessions_per_user' => self::nonNegativeInt($values['max_active_sessions_per_user'] ?? $defaults['max_active_sessions_per_user']),
            'private_message_encryption_enabled' => !empty($values['private_message_encryption_enabled']) ? 1 : 0,
            'public_member_ids_enabled' => !empty($values['public_member_ids_enabled']) ? 1 : 0,
        ]);
    }

    public static function blockedDomains(): array
    {
        return self::listFromValue((string) self::get('blacklist_domains', ''));
    }

    public static function blockedUrlPatterns(): array
    {
        return self::listFromValue((string) self::get('blacklist_url_patterns', ''));
    }

    public static function blockedUsernames(): array
    {
        return self::listFromValue((string) self::get('blocked_usernames', implode(PHP_EOL, self::DEFAULT_BLOCKED_USERNAMES)));
    }

    public static function blockedEmailDomains(): array
    {
        return self::listFromValue((string) self::get('blocked_email_domains', ''));
    }

    private static function normalize(array $settings): array
    {
        return [
            'link_safety_enabled' => self::boolish($settings['link_safety_enabled'] ?? self::DEFAULTS['link_safety_enabled']),
            'link_safety_apply_posts' => self::boolish($settings['link_safety_apply_posts'] ?? self::DEFAULTS['link_safety_apply_posts']),
            'link_safety_apply_comments' => self::boolish($settings['link_safety_apply_comments'] ?? self::DEFAULTS['link_safety_apply_comments']),
            'link_safety_apply_messages' => self::boolish($settings['link_safety_apply_messages'] ?? self::DEFAULTS['link_safety_apply_messages']),
            'link_safety_apply_ads' => self::boolish($settings['link_safety_apply_ads'] ?? self::DEFAULTS['link_safety_apply_ads']),
            'blacklist_domains' => self::sanitizeList((string) ($settings['blacklist_domains'] ?? '')),
            'blacklist_url_patterns' => self::sanitizeList((string) ($settings['blacklist_url_patterns'] ?? '')),
            'block_spam_usernames' => self::boolish($settings['block_spam_usernames'] ?? self::DEFAULTS['block_spam_usernames']),
            'blocked_usernames' => self::sanitizeList((string) ($settings['blocked_usernames'] ?? implode(PHP_EOL, self::DEFAULT_BLOCKED_USERNAMES))),
            'blocked_email_domains' => self::sanitizeList((string) ($settings['blocked_email_domains'] ?? '')),
            'cooldown_post_seconds' => self::nonNegativeInt($settings['cooldown_post_seconds'] ?? self::DEFAULTS['cooldown_post_seconds']),
            'cooldown_comment_seconds' => self::nonNegativeInt($settings['cooldown_comment_seconds'] ?? self::DEFAULTS['cooldown_comment_seconds']),
            'cooldown_forum_topic_seconds' => self::nonNegativeInt($settings['cooldown_forum_topic_seconds'] ?? self::DEFAULTS['cooldown_forum_topic_seconds']),
            'cooldown_private_message_seconds' => self::nonNegativeInt($settings['cooldown_private_message_seconds'] ?? self::DEFAULTS['cooldown_private_message_seconds']),
            'registration_ip_daily_limit' => self::nonNegativeInt($settings['registration_ip_daily_limit'] ?? self::DEFAULTS['registration_ip_daily_limit']),
            'admin_password_confirmation_enabled' => self::boolish($settings['admin_password_confirmation_enabled'] ?? self::DEFAULTS['admin_password_confirmation_enabled']),
            'admin_password_confirmation_ttl_minutes' => self::nonNegativeInt($settings['admin_password_confirmation_ttl_minutes'] ?? self::DEFAULTS['admin_password_confirmation_ttl_minutes']),
            'login_max_attempts_per_ip_15m' => self::nonNegativeInt($settings['login_max_attempts_per_ip_15m'] ?? self::DEFAULTS['login_max_attempts_per_ip_15m']),
            'login_max_attempts_per_account_15m' => self::nonNegativeInt($settings['login_max_attempts_per_account_15m'] ?? self::DEFAULTS['login_max_attempts_per_account_15m']),
            'max_active_sessions_per_user' => self::nonNegativeInt($settings['max_active_sessions_per_user'] ?? self::DEFAULTS['max_active_sessions_per_user']),
            'private_message_encryption_enabled' => self::boolish($settings['private_message_encryption_enabled'] ?? self::DEFAULTS['private_message_encryption_enabled']),
            'public_member_ids_enabled' => self::boolish($settings['public_member_ids_enabled'] ?? self::DEFAULTS['public_member_ids_enabled']),
        ];
    }

    private static function boolish(mixed $value): int
    {
        return (int) (!empty($value));
    }

    private static function nonNegativeInt(mixed $value): int
    {
        return max(0, (int) $value);
    }

    private static function sanitizeList(string $value): string
    {
        $items = self::listFromValue($value);

        return implode(PHP_EOL, $items);
    }

    private static function listFromValue(string $value): array
    {
        $lines = preg_split('/[\r\n,]+/', $value) ?: [];
        $items = array_map(static fn (string $item): string => trim(mb_strtolower($item, 'UTF-8')), $lines);
        $items = array_values(array_unique(array_filter($items, static fn (string $item): bool => $item !== '')));

        return $items;
    }
}
