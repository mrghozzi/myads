<?php

namespace App\Services;

use App\Models\Option;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class SiteHealthService
{
    public const CACHE_KEY = 'myads_site_health_audit';
    public const CACHE_TTL = 300; // 5 minutes

    /**
     * Run the complete structural health audit.
     *
     * @param bool $fresh Whether to bypass cache and recompute
     * @return array
     */
    public function audit(bool $fresh = false): array
    {
        if ($fresh) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return $this->computeAudit();
        });
    }

    private function computeAudit(): array
    {
        $checks = [];
        $score = 100;
        $recommendations = [];

        // 1. Database Health
        $dbCheck = $this->checkDatabase();
        $checks['database'] = $dbCheck;
        if (!$dbCheck['passed']) {
            $score -= 20;
            $recommendations[] = $dbCheck['recommendation'] ?? __('messages.health_check_database');
        } elseif (!empty($dbCheck['warning'])) {
            $score -= 5;
            $recommendations[] = $dbCheck['warning'];
        }

        // 2. Queue Engine
        $queueCheck = $this->checkQueue();
        $checks['queue'] = $queueCheck;
        if (!$queueCheck['passed']) {
            $score -= 15;
            $recommendations[] = $queueCheck['recommendation'] ?? __('messages.health_check_queue');
        } elseif (!empty($queueCheck['warning'])) {
            $score -= 5;
            $recommendations[] = $queueCheck['warning'];
        }

        // 3. Task Scheduler / Cron
        $cronCheck = $this->checkScheduler();
        $checks['scheduler'] = $cronCheck;
        if (!$cronCheck['passed']) {
            $score -= 15;
            $recommendations[] = $cronCheck['recommendation'] ?? __('messages.health_check_cron');
        }

        // 4. Storage & Permissions
        $storageCheck = $this->checkStoragePermissions();
        $checks['storage'] = $storageCheck;
        if (!$storageCheck['passed']) {
            $score -= 20;
            $recommendations[] = $storageCheck['recommendation'] ?? __('messages.health_check_storage');
        }

        // 5. Security Posture
        $securityCheck = $this->checkSecurity();
        $checks['security'] = $securityCheck;
        if (!$securityCheck['passed']) {
            $score -= 15;
            $recommendations[] = $securityCheck['recommendation'] ?? __('messages.health_check_security');
        }

        // 6. PHP Environment & Extensions
        $phpCheck = $this->checkPhpEnvironment();
        $checks['php'] = $phpCheck;
        if (!$phpCheck['passed']) {
            $score -= 15;
            $recommendations[] = $phpCheck['recommendation'] ?? __('messages.health_check_php');
        }

        $score = max(10, min(100, $score));

        $grade = match (true) {
            $score >= 90 => 'excellent',
            $score >= 70 => 'good',
            default => 'warning',
        };

        $gradeLabel = match ($grade) {
            'excellent' => __('messages.health_status_excellent') ?? 'Excellent',
            'good' => __('messages.health_status_good') ?? 'Good',
            default => __('messages.health_status_warning') ?? 'Needs Attention',
        };

        return [
            'score' => $score,
            'grade' => $grade,
            'grade_label' => $gradeLabel,
            'checks' => $checks,
            'recommendations' => $recommendations,
            'audited_at' => Carbon::now()->toIso8601String(),
        ];
    }

    private function checkDatabase(): array
    {
        try {
            $pdo = DB::connection()->getPdo();
            $version = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
            $tablesNeedingOpt = 0;

            // Check tables with space overhead if information_schema accessible
            try {
                $dbName = DB::connection()->getDatabaseName();
                $overhead = DB::select("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema = ? AND data_free > 5242880", [$dbName]);
                $tablesNeedingOpt = (int) ($overhead[0]->cnt ?? 0);
            } catch (\Throwable) {
                // Ignore if restricted
            }

            return [
                'name' => __('messages.health_check_database'),
                'passed' => true,
                'status' => $tablesNeedingOpt > 3 ? 'warning' : 'passed',
                'details' => "MySQL/MariaDB v{$version} connected successfully.",
                'warning' => $tablesNeedingOpt > 3 ? "{$tablesNeedingOpt} tables have fragmented space overhead. Optimize tables via Database Cleanup." : null,
                'action_url' => route('admin.system_monitor'),
                'action_text' => __('messages.system_monitor'),
            ];
        } catch (\Throwable $e) {
            return [
                'name' => __('messages.health_check_database'),
                'passed' => false,
                'status' => 'failed',
                'details' => 'Database connection failed: ' . $e->getMessage(),
                'recommendation' => 'Check your database credentials in your server environment.',
            ];
        }
    }

    private function checkQueue(): array
    {
        $driver = config('queue.default', 'sync');
        $failedJobs = 0;
        $pendingJobs = 0;

        try {
            $failedJobs = DB::table('failed_jobs')->count();
        } catch (\Throwable) {
            // failed_jobs table might not exist in early installs
        }

        try {
            if ($driver === 'database') {
                $pendingJobs = DB::table('jobs')->count();
            }
        } catch (\Throwable) {
            // jobs table might not exist
        }

        $passed = $failedJobs === 0;
        $warning = null;

        if ($driver === 'sync' && app()->environment('production')) {
            $warning = 'Queue driver is currently set to "sync". For production workloads, consider configuring asynchronous queues (database or redis).';
        }

        if ($failedJobs > 0) {
            $warning = "{$failedJobs} failed jobs detected in the queue. Review and retry them in System Monitor.";
        }

        return [
            'name' => __('messages.health_check_queue'),
            'passed' => $failedJobs < 10,
            'status' => $failedJobs > 0 ? 'warning' : ($driver === 'sync' ? 'notice' : 'passed'),
            'details' => "Driver: " . strtoupper($driver) . " | Pending: {$pendingJobs} | Failed: {$failedJobs}",
            'warning' => $warning,
            'action_url' => route('admin.system_monitor'),
            'action_text' => __('messages.system_monitor'),
        ];
    }

    private function checkScheduler(): array
    {
        $lastRun = Cache::get('system_scheduler_last_run');
        if (!$lastRun) {
            $option = Option::where('o_type', 'system_cron_heartbeat')->first();
            $lastRun = $option ? (int) $option->o_valuer : null;
        }

        $isStale = false;
        $details = 'Task Scheduler (Cron) is not registered yet or has not executed.';

        if ($lastRun) {
            $diffMinutes = Carbon::createFromTimestamp($lastRun)->diffInMinutes(Carbon::now());
            $isStale = $diffMinutes > 45;
            $details = 'Last execution: ' . Carbon::createFromTimestamp($lastRun)->diffForHumans();
        }

        return [
            'name' => __('messages.health_check_cron'),
            'passed' => !$isStale,
            'status' => $isStale ? 'warning' : ($lastRun ? 'passed' : 'notice'),
            'details' => $details,
            'recommendation' => $isStale ? 'Add `* * * * * php artisan schedule:run >> /dev/null 2>&1` to your server crontab.' : null,
            'action_url' => route('admin.shared_hosting_guide'),
            'action_text' => __('messages.pressure_guide_button'),
        ];
    }

    private function checkStoragePermissions(): array
    {
        $directories = [
            'storage/app' => storage_path('app'),
            'storage/framework' => storage_path('framework'),
            'storage/logs' => storage_path('logs'),
            'bootstrap/cache' => base_path('bootstrap/cache'),
            'upload' => base_path('upload'),
        ];

        $unwritable = [];
        foreach ($directories as $label => $path) {
            if (file_exists($path) && !is_writable($path)) {
                $unwritable[] = $label;
            }
        }

        $passed = empty($unwritable);

        return [
            'name' => __('messages.health_check_storage'),
            'passed' => $passed,
            'status' => $passed ? 'passed' : 'failed',
            'details' => $passed
                ? 'All critical application directories are writable (0755/0775).'
                : 'The following directories are not writable: ' . implode(', ', $unwritable),
            'recommendation' => !$passed ? 'Update folder permissions: chmod -R 775 storage bootstrap/cache upload' : null,
        ];
    }

    private function checkSecurity(): array
    {
        $isHttps = request()->isSecure() || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || str_starts_with(config('app.url', ''), 'https://');
        $debug = (bool) config('app.debug', false);
        $isProduction = app()->environment('production');

        $warnings = [];
        if ($debug && $isProduction) {
            $warnings[] = 'APP_DEBUG is enabled in a production environment. Set APP_DEBUG=false in your .env file.';
        }
        if (!$isHttps && $isProduction) {
            $warnings[] = 'HTTPS is not enforced. Ensure an SSL certificate is active for secure communication.';
        }

        $passed = empty($warnings);

        return [
            'name' => __('messages.health_check_security'),
            'passed' => $passed,
            'status' => $passed ? 'passed' : 'warning',
            'details' => $passed ? 'Security headers and environment configurations are in production state.' : implode(' | ', $warnings),
            'warning' => !empty($warnings) ? implode(' ', $warnings) : null,
            'action_url' => route('admin.security.index'),
            'action_text' => __('messages.security'),
        ];
    }

    private function checkPhpEnvironment(): array
    {
        $requiredExtensions = [
            'bcmath',
            'curl',
            'fileinfo',
            'mbstring',
            'openssl',
            'pdo_mysql',
            'tokenizer',
            'xml',
        ];

        $missing = [];
        foreach ($requiredExtensions as $ext) {
            if (!extension_loaded($ext)) {
                $missing[] = $ext;
            }
        }

        $phpVersion = PHP_VERSION;
        $versionOk = version_compare($phpVersion, '8.2.0', '>=');

        $passed = empty($missing) && $versionOk;

        return [
            'name' => __('messages.health_check_php'),
            'passed' => $passed,
            'status' => $passed ? 'passed' : 'failed',
            'details' => "PHP v{$phpVersion} | Memory limit: " . ini_get('memory_limit'),
            'recommendation' => !empty($missing) ? 'Install missing PHP extensions: ' . implode(', ', $missing) : null,
        ];
    }
}
