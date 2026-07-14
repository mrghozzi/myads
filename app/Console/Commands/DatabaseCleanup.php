<?php

namespace App\Console\Commands;

use App\Services\DatabaseMaintenanceService;
use Illuminate\Console\Command;

/**
 * Manual/scheduled database cleanup command.
 *
 * Prunes old records from analytics and tracking tables based on
 * configurable retention periods. Can be run manually or via cron.
 *
 * @since v4.4.4
 */
class DatabaseCleanup extends Command
{
    protected $signature = 'myads:db-cleanup
                            {--force : Skip the auto-cleanup enabled check}';

    protected $description = 'Prune old records from analytics tables (state, impressions, SEO metrics) to free database space.';

    public function handle(): int
    {
        if (!$this->option('force') && !DatabaseMaintenanceService::isAutoCleanupEnabled()) {
            $this->warn('Auto-cleanup is disabled in admin settings. Use --force to override.');

            return Command::SUCCESS;
        }

        $retentions = DatabaseMaintenanceService::retentionDays();
        $this->info('Starting database cleanup with the following retention periods:');

        foreach ($retentions as $table => $days) {
            $this->line("  {$table}: {$days} days");
        }

        $this->newLine();

        // Show table sizes before
        $this->info('Current table sizes:');
        $sizesBefore = DatabaseMaintenanceService::tableSizes();

        foreach ($sizesBefore as $table => $stats) {
            $this->line("  {$table}: {$stats['rows']} rows ({$stats['size_mb']} MB)");
        }

        $this->newLine();
        $this->info('Running cleanup...');

        DatabaseMaintenanceService::pruneAll();

        // Show table sizes after
        $this->newLine();
        $this->info('Table sizes after cleanup:');
        $sizesAfter = DatabaseMaintenanceService::tableSizes();

        foreach ($sizesAfter as $table => $stats) {
            $before = $sizesBefore[$table] ?? ['rows' => 0];
            $deleted = max(0, $before['rows'] - $stats['rows']);
            $this->line("  {$table}: {$stats['rows']} rows ({$stats['size_mb']} MB) — deleted {$deleted} rows");
        }

        $this->newLine();
        $this->info('Database cleanup completed successfully.');

        return Command::SUCCESS;
    }
}
