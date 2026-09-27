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
        'post_promotions' => ['status_promotions'],
        'groups' => ['groups', 'group_memberships'],
        'subscriptions_billing' => [
            'subscription_plans',
            'member_subscriptions',
            'billing_orders',
            'billing_transactions',
            'billing_currencies',
        ],
    ];

    public const FEATURE_COLUMNS = [
        'site_admins' => [
            'site_admins' => ['user_id', 'is_active'],
        ],
    ];

    private array $tableCache = [];
    private array $columnCache = [];
    private array $featureCache = [];

    public function hasTable(string $table): bool
    {
        if (array_key_exists($table, $this->tableCache) && !app()->runningUnitTests()) {
            return $this->tableCache[$table];
        }

        // Cross-request cache layer (5 minutes) to avoid SHOW TABLES on every request.
        // Skipped during testing because tests may drop/recreate tables dynamically.
        $useFileCache = !app()->runningUnitTests();

        if ($useFileCache) {
            $cacheKey = 'schema_has_table:' . $table;
            $cached = \Illuminate\Support\Facades\Cache::get($cacheKey);
            if ($cached !== null) {
                return $this->tableCache[$table] = ($cached === 'yes');
            }
        }

        try {
            $exists = Schema::hasTable($table);
            $this->tableCache[$table] = $exists;

            if ($useFileCache) {
                \Illuminate\Support\Facades\Cache::put($cacheKey ?? '', $exists ? 'yes' : 'no', 300);
            }

            return $exists;
        } catch (\Throwable) {
            $this->tableCache[$table] = false;

            if ($useFileCache) {
                \Illuminate\Support\Facades\Cache::put($cacheKey ?? '', 'no', 60);
            }

            return false;
        }
    }

    public function supports(string $feature): bool
    {
        if (array_key_exists($feature, $this->featureCache) && !app()->runningUnitTests()) {
            return $this->featureCache[$feature];
        }

        $tables = self::FEATURE_TABLES[$feature] ?? [$feature];
        foreach ($tables as $table) {
            if (!$this->hasTable($table)) {
                return $this->featureCache[$feature] = false;
            }
        }

        // Auto-heal missing site_admins schema if table exists but required columns are missing
        if ($feature === 'site_admins' && $this->hasTable('site_admins') && !$this->hasColumn('site_admins', 'is_active')) {
            $this->ensureSiteAdminsSchema();
        }

        $requiredColumns = self::FEATURE_COLUMNS[$feature] ?? [];
        foreach ($requiredColumns as $table => $columns) {
            foreach ($columns as $column) {
                if (!$this->hasColumn($table, $column)) {
                    return $this->featureCache[$feature] = false;
                }
            }
        }

        return $this->featureCache[$feature] = true;
    }

    public function hasColumn(string $table, string $column): bool
    {
        $key = $table . '.' . $column;

        if (array_key_exists($key, $this->columnCache) && !app()->runningUnitTests()) {
            return $this->columnCache[$key];
        }

        try {
            return $this->columnCache[$key] = Schema::hasColumn($table, $column);
        } catch (\Throwable) {
            return $this->columnCache[$key] = false;
        }
    }

    public function ensureSiteAdminsSchema(): bool
    {
        try {
            if (!Schema::hasTable('site_admins')) {
                return false;
            }

            if (!Schema::hasColumn('site_admins', 'is_active')) {
                Schema::table('site_admins', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!Schema::hasColumn('site_admins', 'user_id')) {
                        $table->unsignedBigInteger('user_id')->nullable()->after('id');
                    }
                    if (!Schema::hasColumn('site_admins', 'is_super')) {
                        $table->boolean('is_super')->default(false)->after('user_id');
                    }
                    if (!Schema::hasColumn('site_admins', 'has_full_access')) {
                        $table->boolean('has_full_access')->default(false)->after('is_super');
                    }
                    if (!Schema::hasColumn('site_admins', 'permissions')) {
                        $table->json('permissions')->nullable()->after('has_full_access');
                    }
                    if (!Schema::hasColumn('site_admins', 'is_active')) {
                        $table->boolean('is_active')->default(true)->after('permissions');
                    }
                    if (!Schema::hasColumn('site_admins', 'created_by')) {
                        $table->unsignedBigInteger('created_by')->nullable()->after('is_active');
                    }
                    if (!Schema::hasColumn('site_admins', 'created_at')) {
                        $table->timestamp('created_at')->nullable()->after('created_by');
                    }
                    if (!Schema::hasColumn('site_admins', 'updated_at')) {
                        $table->timestamp('updated_at')->nullable()->after('created_at');
                    }
                });

                $this->columnCache = [];
                $this->featureCache = [];
                return true;
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Automatic site_admins schema repair failed: ' . $e->getMessage());
        }

        return false;
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

    public function missingColumnsFor(string $feature): array
    {
        $requiredColumns = self::FEATURE_COLUMNS[$feature] ?? [];
        $missing = [];

        foreach ($requiredColumns as $table => $columns) {
            foreach ($columns as $column) {
                if (!$this->hasColumn($table, $column)) {
                    $missing[] = "{$table}.{$column}";
                }
            }
        }

        return $missing;
    }

    public function notice(string|array $featureOrTables, string $featureLabel): ?array
    {
        $missingTables = $this->missingTablesFor($featureOrTables);
        $missingColumns = is_string($featureOrTables) ? $this->missingColumnsFor($featureOrTables) : [];

        $missingItems = array_merge($missingTables, $missingColumns);
        if ($missingItems === []) {
            return null;
        }

        return [
            'title' => __('messages.upgrade_incomplete_title'),
            'message' => __('messages.upgrade_incomplete_message', [
                'feature' => $featureLabel,
                'tables' => implode(', ', $missingItems),
            ]),
            'tables' => $missingItems,
        ];
    }

    public function blockedActionMessage(string|array $featureOrTables, string $featureLabel): string
    {
        return __('messages.upgrade_action_blocked_feature', [
            'feature' => $featureLabel,
        ]);
    }
}
