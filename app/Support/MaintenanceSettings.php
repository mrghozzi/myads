<?php

namespace App\Support;

use App\Models\Option;

class MaintenanceSettings
{
    public const OPTION_TYPE = 'maintenance_settings';

    public const DEFAULTS = [
        'enabled' => 0,
        'message' => '',
        'logo_path' => '',
        'enabled_at' => 0,
        'last_changed_at' => 0,
        'enabled_by' => 0,
        'last_changed_by' => 0,
        'last_source' => '',
    ];

    public static function all(): array
    {
        $settings = self::DEFAULTS;

        try {
            $rows = Option::where('o_type', self::OPTION_TYPE)->get(['name', 'o_valuer']);
        } catch (\Throwable) {
            return self::normalize($settings);
        }

        foreach ($rows as $row) {
            if (! array_key_exists($row->name, $settings)) {
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
        $current = self::all();

        return self::normalize([
            'enabled' => array_key_exists('enabled', $values) ? $values['enabled'] : $current['enabled'],
            'message' => array_key_exists('message', $values) ? $values['message'] : $current['message'],
            'logo_path' => array_key_exists('logo_path', $values) ? $values['logo_path'] : $current['logo_path'],
            'enabled_at' => array_key_exists('enabled_at', $values) ? $values['enabled_at'] : $current['enabled_at'],
            'last_changed_at' => array_key_exists('last_changed_at', $values) ? $values['last_changed_at'] : $current['last_changed_at'],
            'enabled_by' => array_key_exists('enabled_by', $values) ? $values['enabled_by'] : $current['enabled_by'],
            'last_changed_by' => array_key_exists('last_changed_by', $values) ? $values['last_changed_by'] : $current['last_changed_by'],
            'last_source' => array_key_exists('last_source', $values) ? $values['last_source'] : $current['last_source'],
        ]);
    }

    private static function normalize(array $values): array
    {
        return [
            'enabled' => self::boolish($values['enabled'] ?? self::DEFAULTS['enabled']),
            'message' => self::stringish($values['message'] ?? self::DEFAULTS['message']),
            'logo_path' => self::stringish($values['logo_path'] ?? self::DEFAULTS['logo_path']),
            'enabled_at' => self::timestampish($values['enabled_at'] ?? self::DEFAULTS['enabled_at']),
            'last_changed_at' => self::timestampish($values['last_changed_at'] ?? self::DEFAULTS['last_changed_at']),
            'enabled_by' => self::nonNegativeInt($values['enabled_by'] ?? self::DEFAULTS['enabled_by']),
            'last_changed_by' => self::nonNegativeInt($values['last_changed_by'] ?? self::DEFAULTS['last_changed_by']),
            'last_source' => self::stringish($values['last_source'] ?? self::DEFAULTS['last_source']),
        ];
    }

    private static function boolish(mixed $value): int
    {
        return (int) (! empty($value));
    }

    private static function timestampish(mixed $value): int
    {
        return max(0, (int) $value);
    }

    private static function nonNegativeInt(mixed $value): int
    {
        return max(0, (int) $value);
    }

    private static function stringish(mixed $value): string
    {
        return trim((string) $value);
    }
}
