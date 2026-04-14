<?php

namespace App\Support;

use App\Models\Option;

class SubscriptionSettings
{
    public const OPTION_TYPE = 'subscription_settings';

    public const DEFAULTS = [
        'enabled' => 0,
        'base_currency_code' => 'USD',
    ];

    private static ?array $cached = null;

    public static function all(): array
    {
        if (self::$cached !== null) {
            return self::$cached;
        }

        $settings = self::DEFAULTS;

        try {
            $rows = Option::query()
                ->where('o_type', self::OPTION_TYPE)
                ->get(['name', 'o_valuer']);
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

    public static function isEnabled(): bool
    {
        return (bool) self::get('enabled', 0);
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
        return self::normalize([
            'enabled' => !empty($values['enabled']) ? 1 : 0,
            'base_currency_code' => strtoupper(trim((string) ($values['base_currency_code'] ?? self::DEFAULTS['base_currency_code']))),
        ]);
    }

    private static function normalize(array $settings): array
    {
        $currencyCode = strtoupper(trim((string) ($settings['base_currency_code'] ?? self::DEFAULTS['base_currency_code'])));

        return [
            'enabled' => !empty($settings['enabled']) ? 1 : 0,
            'base_currency_code' => $currencyCode !== '' ? $currencyCode : self::DEFAULTS['base_currency_code'],
        ];
    }
}
