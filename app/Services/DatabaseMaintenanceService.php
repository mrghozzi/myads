<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Self-cleaning database maintenance service.
 *
 * Performs probabilistic garbage collection on large analytics/tracking
 * tables so that shared-hosting users who never set up cron schedulers
 * still get automatic cleanup.
 *
 * @since v4.4.4
 */
class DatabaseMaintenanceService
{
    /**
     * Default retention periods (days) for each table.
     * Admins may override these via the `db_retention_*` options.
     */
    private const DEFAULT_RETENTION = [
        'state'                  => 14,
        'banner_impressions'     => 30,
        'smart_ad_impressions'   => 30,
        'seo_daily_metrics'      => 90,
        'custom_ad_events'       => 60,
        'logs'                   => 7,
        'max_log_size_mb'        => 10,
    ];

    /**
     * Maximum rows to delete per cleanup run (chunked) to avoid
     * long-running queries on shared hosting.
     */
    private const CHUNK_SIZE = 2000;

    /**
     * Minimum interval between cleanup runs (seconds).
     * Prevents running cleanup too often even if random chance triggers it.
     */
    private const MIN_INTERVAL_SECONDS = 3600; // 1 hour

    /**
     * Probabilistic check — call this on every ad-serving request.
     * Has a 1-in-100 chance of actually performing cleanup.
     */
    public static function maybePrune(): void
    {
        if (random_int(1, 100) !== 1) {
            return;
        }

        static::runIfDue();
    }

    /**
     * Run cleanup only if enough time has passed since the last run.
     */
    public static function runIfDue(): void
    {
        $cacheKey = 'db_maintenance_last_run';

        if (Cache::has($cacheKey)) {
            return;
        }

        // Mark as running immediately to prevent concurrent runs
        Cache::put($cacheKey, time(), self::MIN_INTERVAL_SECONDS);

        try {
            static::pruneAll();
        } catch (\Throwable) {
            // Silently fail — maintenance should never break ad serving
        }
    }

    /**
     * Run a full cleanup pass (called by cron command or probabilistic trigger).
     */
    public static function pruneAll(): void
    {
        $retentions = static::retentionDays();

        static::pruneByUnixTimestamp('state', 'r_date', $retentions['state']);
        static::pruneByUnixTimestamp('banner_impressions', 'served_at', $retentions['banner_impressions']);
        static::pruneByUnixTimestamp('smart_ad_impressions', 'served_at', $retentions['smart_ad_impressions']);
        static::pruneByDateColumn('seo_daily_metrics', 'metric_date', $retentions['seo_daily_metrics']);
        static::pruneByTimestampColumn('custom_ad_events', 'created_at', $retentions['custom_ad_events']);

        // Also clean archive tables (they have no automatic cleanup)
        static::pruneByUnixTimestamp('banner_impressions_archive', 'served_at', $retentions['banner_impressions'] * 3);
        static::pruneByUnixTimestamp('smart_ad_impressions_archive', 'served_at', $retentions['smart_ad_impressions'] * 3);
    }

    /**
     * Get retention days for each table (admin-configurable via options).
     */
    public static function retentionDays(): array
    {
        $defaults = self::DEFAULT_RETENTION;

        try {
            $options = DB::table('options')
                ->where('o_name', 'LIKE', 'db_retention_%')
                ->pluck('o_value', 'o_name')
                ->toArray();
        } catch (\Throwable) {
            return $defaults;
        }

        return [
            'state'                => (int) ($options['db_retention_state'] ?? $defaults['state']),
            'banner_impressions'   => (int) ($options['db_retention_banner_impressions'] ?? $defaults['banner_impressions']),
            'smart_ad_impressions' => (int) ($options['db_retention_smart_ad_impressions'] ?? $defaults['smart_ad_impressions']),
            'seo_daily_metrics'    => (int) ($options['db_retention_seo_daily_metrics'] ?? $defaults['seo_daily_metrics']),
            'custom_ad_events'     => (int) ($options['db_retention_custom_ad_events'] ?? $defaults['custom_ad_events']),
            'logs'                 => (int) ($options['db_retention_logs'] ?? 7),
            'max_log_size_mb'      => (int) ($options['db_max_log_size_mb'] ?? 10),
        ];
    }

    /**
     * Check if auto-cleanup is enabled (default: true).
     */
    public static function isAutoCleanupEnabled(): bool
    {
        try {
            $option = DB::table('options')
                ->where('o_name', 'db_auto_cleanup_enabled')
                ->value('o_value');

            return $option === null || $option === '1' || $option === 'true';
        } catch (\Throwable) {
            return true;
        }
    }

    /**
     * Delete old rows from a table using a Unix timestamp column.
     */
    private static function pruneByUnixTimestamp(string $table, string $column, int $days): void
    {
        if (!static::tableExists($table)) {
            return;
        }

        $threshold = time() - ($days * 86400);

        DB::table($table)
            ->where($column, '<', $threshold)
            ->orderBy($column)
            ->limit(self::CHUNK_SIZE)
            ->delete();
    }

    /**
     * Delete old rows from a table using a DATE column.
     */
    private static function pruneByDateColumn(string $table, string $column, int $days): void
    {
        if (!static::tableExists($table)) {
            return;
        }

        $threshold = now()->subDays($days)->toDateString();

        DB::table($table)
            ->where($column, '<', $threshold)
            ->orderBy($column)
            ->limit(self::CHUNK_SIZE)
            ->delete();
    }

    /**
     * Delete old rows from a table using a DATETIME/TIMESTAMP column.
     */
    private static function pruneByTimestampColumn(string $table, string $column, int $days): void
    {
        if (!static::tableExists($table)) {
            return;
        }

        $threshold = now()->subDays($days);

        DB::table($table)
            ->where($column, '<', $threshold)
            ->orderBy($column)
            ->limit(self::CHUNK_SIZE)
            ->delete();
    }

    /**
     * Cached table existence check.
     */
    private static function tableExists(string $table): bool
    {
        static $cache = [];

        if (isset($cache[$table])) {
            return $cache[$table];
        }

        try {
            $cache[$table] = Schema::hasTable($table);
        } catch (\Throwable) {
            $cache[$table] = false;
        }

        return $cache[$table];
    }

    /**
     * Get table sizes in bytes (for admin dashboard).
     *
     * @return array<string, array{rows: int, size_mb: float}>
     */
    public static function tableSizes(): array
    {
        $tables = ['state', 'banner_impressions', 'smart_ad_impressions', 'seo_daily_metrics', 'custom_ad_events'];
        $result = [];

        $isSqlite = DB::connection()->getDriverName() === 'sqlite';

        foreach ($tables as $table) {
            if (!static::tableExists($table)) {
                $result[$table] = ['rows' => 0, 'size_mb' => 0.0];
                continue;
            }

            try {
                $rows = DB::table($table)->count();

                if ($isSqlite) {
                    $result[$table] = ['rows' => $rows, 'size_mb' => 0.0];
                } else {
                    $dbName = DB::connection()->getDatabaseName();
                    $stats = DB::selectOne(
                        "SELECT ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS size_mb
                         FROM information_schema.TABLES
                         WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?",
                        [$dbName, $table]
                    );
                    $result[$table] = [
                        'rows'    => $rows,
                        'size_mb' => (float) ($stats->size_mb ?? 0),
                    ];
                }
            } catch (\Throwable) {
                $result[$table] = ['rows' => 0, 'size_mb' => 0.0];
            }
        }

        return $result;
    }
}
