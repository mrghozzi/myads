<?php

namespace App\Support;

use App\Models\Option;

class GroupSettings
{
    public const OPTION_TYPE = 'group_settings';

    public const POLICY_ALL_MEMBERS = 'all_members';
    public const POLICY_APPROVAL = 'approval';
    public const POLICY_PAID_PLAN = 'paid_plan';

    public const DEFAULTS = [
        'enabled' => 1,
        'creation_policy' => self::POLICY_APPROVAL,
        'eligible_plan_ids' => [],
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

            $settings[$row->name] = $row->name === 'eligible_plan_ids'
                ? json_decode((string) $row->o_valuer, true)
                : $row->o_valuer;
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
                    'o_valuer' => $name === 'eligible_plan_ids'
                        ? json_encode($value, JSON_UNESCAPED_UNICODE)
                        : (string) $value,
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
            'creation_policy' => (string) ($values['creation_policy'] ?? self::DEFAULTS['creation_policy']),
            'eligible_plan_ids' => $values['eligible_plan_ids'] ?? self::DEFAULTS['eligible_plan_ids'],
        ]);
    }

    public static function clearCache(): void
    {
        self::$cached = null;
    }

    private static function normalize(array $settings): array
    {
        $policy = (string) ($settings['creation_policy'] ?? self::DEFAULTS['creation_policy']);
        if (!in_array($policy, [
            self::POLICY_ALL_MEMBERS,
            self::POLICY_APPROVAL,
            self::POLICY_PAID_PLAN,
        ], true)) {
            $policy = self::DEFAULTS['creation_policy'];
        }

        $eligiblePlanIds = collect((array) ($settings['eligible_plan_ids'] ?? []))
            ->filter(fn ($id) => is_numeric($id) && (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        return [
            'enabled' => !empty($settings['enabled']) ? 1 : 0,
            'creation_policy' => $policy,
            'eligible_plan_ids' => $eligiblePlanIds,
        ];
    }
}
