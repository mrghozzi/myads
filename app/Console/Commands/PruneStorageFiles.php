<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Prune expired file-based cache and session files.
 *
 * Laravel's file cache driver does NOT automatically remove expired files,
 * causing disk usage to grow unbounded on shared hosting. This command
 * scans and removes expired cache files and stale session files.
 *
 * @since v4.4.4
 */
class PruneStorageFiles extends Command
{
    protected $signature = 'myads:prune-storage
                            {--sessions : Only prune session files}
                            {--cache : Only prune cache files}';

    protected $description = 'Remove expired cache files and stale session files to free disk space.';

    public function handle(): int
    {
        $pruneCache = !$this->option('sessions');
        $pruneSessions = !$this->option('cache');

        if ($pruneCache) {
            $this->pruneExpiredCacheFiles();
        }

        if ($pruneSessions) {
            $this->pruneStaleSessionFiles();
        }

        return Command::SUCCESS;
    }

    /**
     * Remove expired cache files from the file cache store.
     */
    private function pruneExpiredCacheFiles(): void
    {
        $cachePath = storage_path('framework/cache/data');

        if (!is_dir($cachePath)) {
            $this->line('Cache directory not found — skipping.');
            return;
        }

        $this->info('Scanning cache files for expired entries...');

        $deleted = 0;
        $total = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($cachePath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                // Remove empty directories
                $dirPath = $file->getRealPath();
                if ($dirPath !== false && $this->isEmptyDir($dirPath)) {
                    @rmdir($dirPath);
                }
                continue;
            }

            if (!$file->isFile()) {
                continue;
            }

            $total++;

            if ($this->isCacheFileExpired($file->getRealPath())) {
                @unlink($file->getRealPath());
                $deleted++;
            }
        }

        $this->info("Cache: scanned {$total} files, removed {$deleted} expired files.");
    }

    /**
     * Remove session files that haven't been modified beyond the session lifetime.
     */
    private function pruneStaleSessionFiles(): void
    {
        $sessionPath = storage_path('framework/sessions');

        if (!is_dir($sessionPath)) {
            $this->line('Sessions directory not found — skipping.');
            return;
        }

        $this->info('Scanning session files for stale entries...');

        // Session lifetime in minutes (default 120 = 2 hours)
        $lifetimeMinutes = (int) config('session.lifetime', 120);
        $staleThreshold = time() - ($lifetimeMinutes * 60 * 2); // 2x lifetime for safety

        $deleted = 0;
        $total = 0;

        foreach (File::files($sessionPath) as $file) {
            $total++;

            if ($file->getMTime() < $staleThreshold) {
                @unlink($file->getRealPath());
                $deleted++;
            }
        }

        $this->info("Sessions: scanned {$total} files, removed {$deleted} stale files.");
    }

    /**
     * Check if a Laravel file cache entry has expired.
     *
     * Laravel file cache stores data as: [expiry_timestamp]\n[serialized_data]
     */
    private function isCacheFileExpired(string $path): bool
    {
        try {
            $handle = fopen($path, 'r');
            if ($handle === false) {
                return false;
            }

            // Read just the first line (expiry timestamp)
            $firstLine = fgets($handle);
            fclose($handle);

            if ($firstLine === false) {
                return true; // Corrupted file
            }

            $expiry = (int) trim($firstLine);

            // expiry of 0 means "forever"
            if ($expiry === 0) {
                return false;
            }

            return $expiry < time();
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Check if a directory is empty.
     */
    private function isEmptyDir(string $dir): bool
    {
        $handle = opendir($dir);
        if ($handle === false) {
            return false;
        }

        while (($entry = readdir($handle)) !== false) {
            if ($entry !== '.' && $entry !== '..') {
                closedir($handle);
                return false;
            }
        }

        closedir($handle);
        return true;
    }

    /**
     * Probabilistic storage cleanup — call from middleware.
     * 1-in-200 chance of running, with 6-hour cooldown.
     */
    public static function maybePrune(): void
    {
        if (random_int(1, 200) !== 1) {
            return;
        }

        $cacheKey = 'storage_prune_last_run';
        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            return;
        }

        \Illuminate\Support\Facades\Cache::put($cacheKey, time(), 21600); // 6 hours

        try {
            // Quick inline cleanup without full command infrastructure
            static::quickPruneCacheFiles();
            static::quickPruneSessionFiles();
        } catch (\Throwable) {
            // Silently fail
        }
    }

    /**
     * Quick inline cache file pruning for web requests.
     *
     * Keep both scanned and deleted counts bounded. On shared hosting a cache
     * directory can contain hundreds of thousands of files, so scanning until
     * 500 expired entries are found can become the CPU spike we are trying to
     * avoid.
     */
    private static function quickPruneCacheFiles(): void
    {
        $cachePath = storage_path('framework/cache/data');
        if (!is_dir($cachePath)) {
            return;
        }

        $scanLimit = (int) env('MYADS_INLINE_CACHE_PRUNE_SCAN_LIMIT', 750);
        $deleteLimit = (int) env('MYADS_INLINE_CACHE_PRUNE_DELETE_LIMIT', 150);
        $scanned = 0;
        $deleted = 0;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($cachePath, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($scanned >= $scanLimit || $deleted >= $deleteLimit) {
                break;
            }

            if (!$file->isFile()) {
                continue;
            }

            $scanned++;

            try {
                $handle = fopen($file->getRealPath(), 'r');
                if ($handle === false) {
                    continue;
                }

                $firstLine = fgets($handle);
                fclose($handle);

                if ($firstLine === false) {
                    @unlink($file->getRealPath());
                    $deleted++;
                    continue;
                }

                $expiry = (int) trim($firstLine);
                if ($expiry !== 0 && $expiry < time()) {
                    @unlink($file->getRealPath());
                    $deleted++;
                }
            } catch (\Throwable) {
                continue;
            }
        }
    }

    /**
     * Quick inline session file pruning for web requests.
     */
    private static function quickPruneSessionFiles(): void
    {
        $sessionPath = storage_path('framework/sessions');
        if (!is_dir($sessionPath)) {
            return;
        }

        $lifetimeMinutes = (int) config('session.lifetime', 120);
        $staleThreshold = time() - ($lifetimeMinutes * 60 * 2);
        $scanLimit = (int) env('MYADS_INLINE_SESSION_PRUNE_SCAN_LIMIT', 300);
        $deleteLimit = (int) env('MYADS_INLINE_SESSION_PRUNE_DELETE_LIMIT', 100);
        $scanned = 0;
        $deleted = 0;

        foreach (\Illuminate\Support\Facades\File::files($sessionPath) as $file) {
            if ($scanned >= $scanLimit || $deleted >= $deleteLimit) {
                break;
            }

            $scanned++;

            if ($file->getMTime() < $staleThreshold) {
                @unlink($file->getRealPath());
                $deleted++;
            }
        }
    }
}
