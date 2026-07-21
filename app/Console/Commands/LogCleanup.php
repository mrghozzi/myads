<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Clean up old log files from storage/logs.
 *
 * Handles both daily-rotated logs (laravel-YYYY-MM-DD.log) and the legacy
 * monolithic laravel.log file. For the monolithic file, truncation is used
 * when it exceeds a configurable size threshold.
 *
 * @since v4.4.7
 */
class LogCleanup extends Command
{
    protected $signature = 'myads:log-cleanup
                            {--days=7 : Delete rotated log files older than this many days}
                            {--max-size=10 : Truncate monolithic laravel.log if it exceeds this size in MB}
                            {--force : Run even if auto-cleanup is disabled}';

    protected $description = 'Remove old rotated log files and truncate oversized monolithic logs.';

    public function handle(): int
    {
        $retentions = \App\Services\DatabaseMaintenanceService::retentionDays();

        $daysOption = $this->option('days');
        $days = ($daysOption !== '7' && $daysOption !== null)
            ? max(1, (int) $daysOption)
            : ($retentions['logs'] ?? 7);

        $maxSizeOption = $this->option('max-size');
        $maxSizeMB = ($maxSizeOption !== '10' && $maxSizeOption !== null)
            ? max(1, (int) $maxSizeOption)
            : ($retentions['max_log_size_mb'] ?? 10);

        $logsPath = storage_path('logs');

        if (!is_dir($logsPath)) {
            $this->warn('Logs directory not found — skipping.');
            return Command::SUCCESS;
        }

        $sizeBefore = $this->directorySize($logsPath);

        $this->pruneRotatedLogs($logsPath, $days);
        $this->truncateMonolithicLog($logsPath, $maxSizeMB);
        $this->pruneTemporaryLogs($logsPath);

        $sizeAfter = $this->directorySize($logsPath);
        $freed = max(0, $sizeBefore - $sizeAfter);

        $this->info(sprintf(
            'Log cleanup complete. Before: %s, After: %s, Freed: %s',
            $this->formatBytes($sizeBefore),
            $this->formatBytes($sizeAfter),
            $this->formatBytes($freed)
        ));

        return Command::SUCCESS;
    }

    /**
     * Remove rotated daily log files older than the retention period.
     * Matches files like: laravel-2026-07-21.log
     */
    private function pruneRotatedLogs(string $logsPath, int $days): void
    {
        $threshold = now()->subDays($days);
        $deleted = 0;

        foreach (File::files($logsPath) as $file) {
            $name = $file->getFilename();

            // Match daily rotated logs: laravel-YYYY-MM-DD.log
            if (!preg_match('/^laravel-(\d{4}-\d{2}-\d{2})\.log$/', $name, $matches)) {
                continue;
            }

            try {
                $fileDate = \Carbon\Carbon::createFromFormat('Y-m-d', $matches[1]);
                if ($fileDate && $fileDate->lt($threshold)) {
                    @unlink($file->getRealPath());
                    $deleted++;
                }
            } catch (\Throwable) {
                // Skip files with unparseable dates
                continue;
            }
        }

        if ($deleted > 0) {
            $this->info("Rotated logs: removed {$deleted} files older than {$days} days.");
        }
    }

    /**
     * Truncate the monolithic laravel.log if it exceeds the max size.
     *
     * Keeps the last portion of the file (most recent entries) equal to
     * 20% of the max size, so recent errors are still visible.
     */
    private function truncateMonolithicLog(string $logsPath, int $maxSizeMB): void
    {
        $logFile = $logsPath . DIRECTORY_SEPARATOR . 'laravel.log';

        if (!file_exists($logFile)) {
            return;
        }

        $currentSize = filesize($logFile);
        $maxBytes = $maxSizeMB * 1024 * 1024;

        if ($currentSize <= $maxBytes) {
            return;
        }

        $this->info(sprintf(
            'Monolithic laravel.log is %s (limit: %sMB) — truncating...',
            $this->formatBytes($currentSize),
            $maxSizeMB
        ));

        // Keep the last 20% of the max size (most recent entries)
        $keepBytes = (int) ($maxBytes * 0.2);

        try {
            $handle = fopen($logFile, 'r');
            if ($handle === false) {
                $this->error('Could not open laravel.log for reading.');
                return;
            }

            // Seek to the position we want to keep from
            fseek($handle, $currentSize - $keepBytes);

            // Skip to the next complete line
            fgets($handle);

            // Read the remaining content
            $tail = fread($handle, $keepBytes);
            fclose($handle);

            if ($tail === false) {
                $tail = '';
            }

            // Prepend a marker so admins know truncation happened
            $marker = sprintf(
                "[%s] system.INFO: === Log truncated by myads:log-cleanup. Previous size: %s ===\n",
                now()->toDateTimeString(),
                $this->formatBytes($currentSize)
            );

            file_put_contents($logFile, $marker . $tail);

            $newSize = filesize($logFile);
            $this->info(sprintf(
                'Truncated laravel.log: %s → %s',
                $this->formatBytes($currentSize),
                $this->formatBytes($newSize)
            ));
        } catch (\Throwable $e) {
            $this->error('Failed to truncate laravel.log: ' . $e->getMessage());
        }
    }

    /**
     * Remove temporary log files (error_temp.log, temp_tail.log, etc.)
     */
    private function pruneTemporaryLogs(string $logsPath): void
    {
        $tempPatterns = ['error_temp.log', 'temp_tail.log'];
        $deleted = 0;

        foreach (File::files($logsPath) as $file) {
            $name = $file->getFilename();

            if (in_array($name, $tempPatterns, true)) {
                @unlink($file->getRealPath());
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("Removed {$deleted} temporary log files.");
        }
    }

    /**
     * Calculate the total size of a directory.
     */
    private function directorySize(string $path): int
    {
        $size = 0;

        foreach (File::files($path) as $file) {
            $size += $file->getSize();
        }

        return $size;
    }

    /**
     * Format bytes into a human-readable string.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    // ─────────────────────────────────────────────────────────────
    // Static methods for probabilistic inline cleanup (web requests)
    // ─────────────────────────────────────────────────────────────

    /**
     * Probabilistic log cleanup — call from ad-serving middleware.
     * 1-in-200 chance, with 12-hour cooldown.
     */
    public static function maybePrune(): void
    {
        if (random_int(1, 200) !== 1) {
            return;
        }

        $cacheKey = 'log_prune_last_run';
        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            return;
        }

        \Illuminate\Support\Facades\Cache::put($cacheKey, time(), 43200); // 12 hours

        try {
            static::quickPruneLogs();
        } catch (\Throwable) {
            // Silently fail — maintenance should never break ad serving
        }
    }

    /**
     * Quick inline log pruning for web requests.
     *
     * - Deletes rotated logs older than 7 days
     * - Truncates monolithic laravel.log if > 10MB
     */
    private static function quickPruneLogs(): void
    {
        $logsPath = storage_path('logs');
        if (!is_dir($logsPath)) {
            return;
        }

        $retentions = \App\Services\DatabaseMaintenanceService::retentionDays();
        $retentionDays = $retentions['logs'] ?? 7;
        $maxSizeMB = $retentions['max_log_size_mb'] ?? 10;

        $threshold = now()->subDays($retentionDays);

        // 1. Prune old rotated logs
        foreach (\Illuminate\Support\Facades\File::files($logsPath) as $file) {
            $name = $file->getFilename();

            if (!preg_match('/^laravel-(\d{4}-\d{2}-\d{2})\.log$/', $name, $matches)) {
                continue;
            }

            try {
                $fileDate = \Carbon\Carbon::createFromFormat('Y-m-d', $matches[1]);
                if ($fileDate && $fileDate->lt($threshold)) {
                    @unlink($file->getRealPath());
                }
            } catch (\Throwable) {
                continue;
            }
        }

        // 2. Truncate monolithic log if too large
        $logFile = $logsPath . DIRECTORY_SEPARATOR . 'laravel.log';
        if (!file_exists($logFile)) {
            return;
        }

        $maxBytes = $maxSizeMB * 1024 * 1024;
        $currentSize = filesize($logFile);

        if ($currentSize <= $maxBytes) {
            return;
        }

        // Keep last 2MB of the most recent entries
        $keepBytes = 2 * 1024 * 1024;

        try {
            $handle = fopen($logFile, 'r');
            if ($handle === false) {
                return;
            }

            fseek($handle, $currentSize - $keepBytes);
            fgets($handle); // Skip to next complete line

            $tail = fread($handle, $keepBytes);
            fclose($handle);

            if ($tail === false) {
                $tail = '';
            }

            $marker = sprintf(
                "[%s] system.INFO: === Log auto-truncated (was %s). Old entries archived. ===\n",
                now()->toDateTimeString(),
                round($currentSize / 1048576, 1) . 'MB'
            );

            file_put_contents($logFile, $marker . $tail);
        } catch (\Throwable) {
            // Silently fail
        }
    }
}
