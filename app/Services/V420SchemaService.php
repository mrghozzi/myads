<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;

class V420SchemaService
{
    public const FEATURE_TABLES = [
        'site_admins' => ['site_admins'],
        'privacy' => ['user_privacy_settings'],
        'link_previews' => ['status_link_previews'],
        'reposts' => ['status_reposts'],
        'mentions' => ['status_mentions'],
        'point_history' => ['point_transactions'],
        'badges' => ['badges', 'user_badges', 'badge_showcase'],
        'quests' => ['quests', 'quest_progress'],
        'security_ip_bans' => ['security_ip_bans'],
        'security_sessions' => ['security_member_sessions'],
    ];

    private array $tableCache = [];
    private array $columnCache = [];
    private array $featureCache = [];

    public function hasTable(string $table): bool
    {
        if (array_key_exists($table, $this->tableCache)) {
            return $this->tableCache[$table];
        }

        try {
            return $this->tableCache[$table] = Schema::hasTable($table);
        } catch (\Throwable) {
            return $this->tableCache[$table] = false;
        }
    }

    public function supports(string $feature): bool
    {
        if (array_key_exists($feature, $this->featureCache)) {
            return $this->featureCache[$feature];
        }

        $tables = self::FEATURE_TABLES[$feature] ?? [$feature];
        foreach ($tables as $table) {
            if (!$this->hasTable($table)) {
                return $this->featureCache[$feature] = false;
            }
        }

        return $this->featureCache[$feature] = true;
    }

    public function hasColumn(string $table, string $column): bool
    {
        $key = $table . '.' . $column;

        if (array_key_exists($key, $this->columnCache)) {
            return $this->columnCache[$key];
        }

        try {
            return $this->columnCache[$key] = Schema::hasColumn($table, $column);
        } catch (\Throwable) {
            return $this->columnCache[$key] = false;
        }
    }

    public function missingTablesFor(string|array $featureOrTables): array
    {
        $tables = is_array($featureOrTables)
            ? $featureOrTables
            : (self::FEATURE_TABLES[$featureOrTables] ?? [$featureOrTables]);

        return collect($tables)
            ->filter(fn (string $table) => !$this->hasTable($table))
            ->values()
            ->all();
    }

    public function notice(string|array $featureOrTables, string $featureLabel): ?array
    {
        $missingTables = $this->missingTablesFor($featureOrTables);
        if ($missingTables === []) {
            return null;
        }

        return [
            'title' => __('messages.upgrade_incomplete_title'),
            'message' => __('messages.upgrade_incomplete_message', [
                'feature' => $featureLabel,
                'tables' => implode(', ', $missingTables),
            ]),
            'tables' => $missingTables,
        ];
    }

    public function blockedActionMessage(string|array $featureOrTables, string $featureLabel): string
    {
        return __('messages.upgrade_action_blocked_feature', [
            'feature' => $featureLabel,
        ]);
    }
}
