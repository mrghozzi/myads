<?php

namespace App\Services;

use App\Models\Option;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use ZipArchive;

class ReleaseUpdateService
{
    public const SESSION_OPTION_TYPE = 'system_update_session';

    private const SESSION_ROOT = 'myads_updates';
    private const RUNNING_STALE_SECONDS = 900;
    private const DOWNLOAD_PROGRESS_BYTES = 1048576;

    private const STAGES = [
        'initialize' => [
            'label' => 'update_stage_initialize',
            'icon' => 'shield',
        ],
        'download' => [
            'label' => 'update_stage_download',
            'icon' => 'download-cloud',
        ],
        'extract' => [
            'label' => 'update_stage_extract',
            'icon' => 'archive',
        ],
        'package_preflight' => [
            'label' => 'update_stage_package_preflight',
            'icon' => 'search',
        ],
        'enable_maintenance' => [
            'label' => 'update_stage_enable_maintenance',
            'icon' => 'tool',
        ],
        'finalize' => [
            'label' => 'update_stage_finalize',
            'icon' => 'upload-cloud',
        ],
        'cleanup' => [
            'label' => 'update_stage_cleanup',
            'icon' => 'check-circle',
        ],
    ];

    public function __construct(
        private readonly GamificationService $gamification,
        private readonly UpdateSafetyService $updateSafety,
        private readonly MaintenanceModeManager $maintenanceMode
    ) {
    }

    public function applyLatestRelease(string $currentVersion, ?array $releaseData = null): string
    {
        if (! is_array($releaseData)) {
            throw new RuntimeException(__('messages.update_fetch_failed'));
        }

        if ($this->activeSession() !== null) {
            throw new RuntimeException(__('messages.update_session_already_running'));
        }

        $session = $this->makeSession($currentVersion, $releaseData, null);
        $this->saveSession($session);

        do {
            $state = $this->runNextStep((string) $session['token'], null);

            if (($state['status'] ?? '') === 'failed') {
                throw new RuntimeException((string) ($state['error'] ?: __('messages.update_failed')));
            }
        } while (($state['status'] ?? '') !== 'completed');

        return (string) ($state['target_version'] ?? $session['target_version']);
    }

    public function startSession(string $currentVersion, array $releaseData, ?User $actor): array
    {
        if ($this->activeSession() !== null) {
            throw new RuntimeException(__('messages.update_session_already_running'));
        }

        $session = $this->makeSession($currentVersion, $releaseData, $actor);
        $this->saveSession($session);

        return $this->formatSession($session);
    }

    public function runNextStep(string $token, ?User $actor): array
    {
        $session = $this->loadSession($token);

        if (in_array($session['status'] ?? '', ['completed', 'cancelled'], true)) {
            return $this->formatSession($session);
        }

        if (($session['status'] ?? '') === 'running' && ! $this->isRunningSessionStale($session)) {
            throw new RuntimeException(__('messages.update_session_busy'));
        }

        if (($session['status'] ?? '') === 'running') {
            $session = $this->failSession($session, __('messages.update_session_stale_retry'), false);
        }

        $stage = $this->nextExecutableStage($session);
        if ($stage === null) {
            $session['status'] = 'completed';
            $session['completed_at'] = time();
            $session['current_stage'] = 'cleanup';
            $session['stage_percent'] = 100;
            $session['detail'] = __('messages.update_session_completed');
            $this->saveSession($session);

            return $this->formatSession($session);
        }

        $session['status'] = 'running';
        $session['current_stage'] = $stage;
        $session['stage_percent'] = 0;
        $session['error'] = null;
        $session['updated_at'] = time();
        $this->markStage($session, $stage, 'running', 0, $this->stageStartDetail($stage));
        $this->saveSession($session);

        try {
            $this->executeStage($stage, $session, $actor);
            $this->markStage($session, $stage, 'completed', 100, $this->currentStageDetail($session, $stage) ?: $this->stageDoneDetail($stage, $session));

            $nextStage = $this->nextExecutableStage($session);
            if ($nextStage === null) {
                $session['status'] = 'completed';
                $session['completed_at'] = time();
                $session['current_stage'] = $stage;
                $session['stage_percent'] = 100;
                $session['detail'] = __('messages.update_session_completed');
            } else {
                $session['status'] = 'pending';
                $session['current_stage'] = $nextStage;
                $session['stage_percent'] = 0;
                $session['detail'] = $this->stageStartDetail($nextStage);
            }

            $session['updated_at'] = time();
            $this->saveSession($session);
        } catch (\Throwable $exception) {
            $session = $this->failSession($session, $exception->getMessage(), true, $stage, $actor);
        }

        return $this->formatSession($session);
    }

    public function status(string $token): array
    {
        return $this->formatSession($this->loadSession($token));
    }

    public function activeSessionForResponse(): ?array
    {
        $session = $this->activeSession();

        return $session ? $this->formatSession($session) : null;
    }

    public function cancelSession(string $token, ?User $actor): array
    {
        $session = $this->loadSession($token);

        if (! $this->canCancel($session)) {
            throw new RuntimeException(__('messages.update_session_cancel_forbidden'));
        }

        if (($session['status'] ?? '') === 'running' && ! $this->isRunningSessionStale($session)) {
            throw new RuntimeException(__('messages.update_session_busy'));
        }

        $this->deleteTempRoot($session);

        $session['status'] = 'cancelled';
        $session['cancelled_at'] = time();
        $session['cancelled_by'] = (int) ($actor?->getKey() ?? 0);
        $session['detail'] = __('messages.update_session_cancelled');
        $session['updated_at'] = time();
        $this->saveSession($session);

        return $this->formatSession($session);
    }

    private function makeSession(string $currentVersion, array $releaseData, ?User $actor): array
    {
        $latestVersion = ltrim((string) ($releaseData['tag_name'] ?? ''), 'v');
        if (! version_compare($latestVersion, $currentVersion, '>')) {
            throw new RuntimeException(__('messages.already_up_to_date'));
        }

        $downloadUrl = $this->getAssetDownloadUrl($releaseData);
        if (! $downloadUrl) {
            throw new RuntimeException(__('messages.no_download_url'));
        }

        $token = Str::lower(Str::random(32));
        $root = storage_path('app' . DIRECTORY_SEPARATOR . self::SESSION_ROOT . DIRECTORY_SEPARATOR . $token);
        $now = time();

        $stages = [];
        foreach (self::STAGES as $key => $definition) {
            $stages[] = [
                'key' => $key,
                'status' => $key === 'initialize' ? 'completed' : 'pending',
                'percent' => $key === 'initialize' ? 100 : 0,
                'detail' => $key === 'initialize' ? __('messages.update_stage_initialize_done') : '',
                'started_at' => $key === 'initialize' ? $now : null,
                'finished_at' => $key === 'initialize' ? $now : null,
                'error' => null,
                'icon' => $definition['icon'],
            ];
        }

        return [
            'token' => $token,
            'status' => 'pending',
            'current_stage' => 'download',
            'current_version' => $currentVersion,
            'target_version' => $latestVersion,
            'release_tag' => (string) ($releaseData['tag_name'] ?? ''),
            'release_name' => (string) ($releaseData['name'] ?? $releaseData['tag_name'] ?? ''),
            'release_data' => $releaseData,
            'download_url' => $downloadUrl,
            'download_size' => $this->getAssetSize($releaseData),
            'bytes_done' => 0,
            'bytes_total' => $this->getAssetSize($releaseData),
            'stage_percent' => 0,
            'detail' => __('messages.update_session_started'),
            'error' => null,
            'started_by' => (int) ($actor?->getKey() ?? 0),
            'started_at' => $now,
            'updated_at' => $now,
            'completed_at' => null,
            'failed_at' => null,
            'cancelled_at' => null,
            'cancelled_by' => null,
            'maintenance_started' => false,
            'paths' => [
                'root' => $root,
                'zip' => $root . DIRECTORY_SEPARATOR . 'package.zip',
                'extract' => $root . DIRECTORY_SEPARATOR . 'extracted',
                'inner' => null,
            ],
            'package_preflight' => null,
            'file_count' => null,
            'copied_files' => 0,
            'stages' => $stages,
        ];
    }

    private function executeStage(string $stage, array &$session, ?User $actor): void
    {
        match ($stage) {
            'download' => $this->downloadArchive($session),
            'extract' => $this->extractArchive($session),
            'package_preflight' => $this->runPackagePreflight($session),
            'enable_maintenance' => $this->enableMaintenance($session, $actor),
            'finalize' => $this->finalizeUpdate($session),
            'cleanup' => $this->cleanupUpdate($session, $actor),
            default => throw new RuntimeException(__('messages.update_session_unknown_stage', ['stage' => $stage])),
        };
    }

    private function downloadArchive(array &$session): void
    {
        $downloadUrl = (string) ($session['download_url'] ?? '');
        if (! filter_var($downloadUrl, FILTER_VALIDATE_URL)) {
            throw new RuntimeException(__('messages.no_download_url'));
        }

        $zipPath = (string) data_get($session, 'paths.zip');
        if ($zipPath === '') {
            throw new RuntimeException(__('messages.update_package_root_missing'));
        }

        File::ensureDirectoryExists(dirname($zipPath), 0755, true);
        if (File::exists($zipPath)) {
            File::delete($zipPath);
        }

        $lastPersistedAt = microtime(true);
        $lastPersistedBytes = 0;

        $response = Http::withoutVerifying()->withHeaders([
            'User-Agent' => 'MyAds-Updater/1.0',
        ])->timeout(300)->withOptions([
            'sink' => $zipPath,
            'allow_redirects' => ['max' => 10],
            'progress' => function ($downloadTotal, $downloadedBytes) use (&$session, &$lastPersistedAt, &$lastPersistedBytes): void {
                $downloadTotal = (int) $downloadTotal;
                $downloadedBytes = (int) $downloadedBytes;
                $now = microtime(true);
                if (
                    $downloadedBytes - $lastPersistedBytes < self::DOWNLOAD_PROGRESS_BYTES
                    && $now - $lastPersistedAt < 1.0
                ) {
                    return;
                }

                $lastPersistedAt = $now;
                $lastPersistedBytes = $downloadedBytes;

                $total = $downloadTotal > 0 ? $downloadTotal : (int) ($session['bytes_total'] ?? 0);
                $percent = $total > 0 ? (int) min(99, floor(($downloadedBytes / $total) * 100)) : 50;

                $this->updateStageProgress(
                    $session,
                    'download',
                    $percent,
                    __('messages.update_stage_download_detail', [
                        'downloaded' => $this->formatBytes($downloadedBytes),
                        'total' => $total > 0 ? $this->formatBytes($total) : __('messages.update_bytes_unknown'),
                    ]),
                    $downloadedBytes,
                    $total > 0 ? $total : null,
                    true
                );
            },
        ])->get($downloadUrl);

        if (! $response->successful()) {
            if (File::exists($zipPath)) {
                File::delete($zipPath);
            }

            throw new RuntimeException(__('messages.download_failed') . ' (HTTP ' . $response->status() . ')');
        }

        if (! File::exists($zipPath) && $response->body() !== '') {
            File::put($zipPath, $response->body());
        }

        if (! File::exists($zipPath) || File::size($zipPath) <= 0) {
            throw new RuntimeException(__('messages.download_failed'));
        }

        $size = File::size($zipPath);
        $this->updateStageProgress(
            $session,
            'download',
            100,
            __('messages.update_stage_download_done', ['size' => $this->formatBytes($size)]),
            $size,
            $size,
            true
        );
    }

    private function extractArchive(array &$session): void
    {
        $zipPath = (string) data_get($session, 'paths.zip');
        $extractPath = (string) data_get($session, 'paths.extract');

        if (! File::exists($zipPath)) {
            throw new RuntimeException(__('messages.download_failed'));
        }

        File::deleteDirectory($extractPath);
        File::makeDirectory($extractPath, 0755, true);

        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== true) {
            throw new RuntimeException(__('messages.zip_open_failed'));
        }

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = (string) $zip->getNameIndex($index);
            $this->assertSafeZipEntry($name);
        }

        $this->updateStageProgress($session, 'extract', 35, __('messages.update_stage_extract_detail'), null, null, true);

        $zip->extractTo($extractPath);
        $zip->close();

        $inner = $this->resolvePackageRoot($extractPath);
        $session['paths']['inner'] = $inner;
        $session['file_count'] = count(File::allFiles($inner));

        $this->updateStageProgress(
            $session,
            'extract',
            100,
            __('messages.update_stage_extract_done', ['count' => (int) $session['file_count']]),
            null,
            null,
            true
        );
    }

    private function runPackagePreflight(array &$session): void
    {
        $inner = (string) data_get($session, 'paths.inner');
        if ($inner === '' || ! File::isDirectory($inner)) {
            throw new RuntimeException(__('messages.update_package_root_missing'));
        }

        $migrationPath = $inner . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        if (! File::exists($migrationPath)) {
            File::makeDirectory($migrationPath, 0755, true);
        }

        $this->updateStageProgress($session, 'package_preflight', 30, __('messages.update_stage_package_preflight_detail'), null, null, true);

        $releaseReport = $this->updateSafety->run([$migrationPath]);
        $session['package_preflight'] = [
            'is_safe' => $releaseReport->isSafe(),
            'pending_migrations' => $releaseReport->pendingMigrations,
            'destructive_migrations' => $releaseReport->destructiveMigrations,
            'checks' => $releaseReport->checks,
        ];

        if (! $releaseReport->isSafe()) {
            throw new RuntimeException(__('messages.update_blocked_preflight', [
                'details' => implode(' ', $releaseReport->failureMessages()),
            ]));
        }

        $this->updateStageProgress(
            $session,
            'package_preflight',
            100,
            __('messages.update_stage_package_preflight_done', [
                'count' => count($releaseReport->pendingMigrations),
            ]),
            null,
            null,
            true
        );
    }

    private function enableMaintenance(array &$session, ?User $actor): void
    {
        $this->maintenanceMode->enable($actor, 'admin_update_start');
        $session['maintenance_started'] = true;

        $this->updateStageProgress($session, 'enable_maintenance', 100, __('messages.update_stage_enable_maintenance_done'), null, null, true);
    }

    private function finalizeUpdate(array &$session): void
    {
        $inner = (string) data_get($session, 'paths.inner');
        if ($inner === '' || ! File::isDirectory($inner)) {
            throw new RuntimeException(__('messages.update_package_root_missing'));
        }

        $files = File::allFiles($inner);
        $total = max(count($files), 1);
        $copied = 0;
        $lastPersistedAt = microtime(true);

        foreach ($files as $file) {
            $sourcePath = (string) $file->getRealPath();
            $relativePath = $this->relativePath($inner, $sourcePath);
            $this->assertSafeRelativePath($relativePath);

            $targetPath = base_path(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath));
            $targetDir = dirname($targetPath);

            if (! File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            File::copy($sourcePath, $targetPath);
            $copied++;
            $session['copied_files'] = $copied;

            $now = microtime(true);
            if ($copied === $total || $copied % 25 === 0 || $now - $lastPersistedAt >= 1.0) {
                $lastPersistedAt = $now;
                $percent = 5 + (int) floor(($copied / $total) * 45);
                $this->updateStageProgress(
                    $session,
                    'finalize',
                    min(55, $percent),
                    __('messages.update_stage_finalize_copying_files', [
                        'copied' => $copied,
                        'total' => $total,
                    ]),
                    null,
                    null,
                    true
                );
            }
        }

        $updateScript = base_path('requests' . DIRECTORY_SEPARATOR . 'update.php');
        if (File::exists($updateScript)) {
            $this->updateStageProgress($session, 'finalize', 60, __('messages.update_stage_finalize_running_script'), null, null, true);
            include_once $updateScript;
        }

        $this->updateStageProgress($session, 'finalize', 72, __('messages.update_stage_finalize_running_migrations'), null, null, true);
        Artisan::call('migrate', ['--force' => true]);

        $this->updateStageProgress($session, 'finalize', 88, __('messages.update_stage_finalize_clearing_cache'), null, null, true);
        Artisan::call('optimize:clear');

        $this->updateStageProgress($session, 'finalize', 100, __('messages.update_stage_finalize_done'), null, null, true);
    }

    private function cleanupUpdate(array &$session, ?User $actor): void
    {
        $this->updateStageProgress($session, 'cleanup', 20, __('messages.update_stage_cleanup_repairing'), null, null, true);
        $this->gamification->repairQuestData();

        $this->updateStageProgress($session, 'cleanup', 45, __('messages.update_stage_cleanup_clearing_caches'), null, null, true);
        $this->clearUpdateCaches((string) ($session['current_version'] ?? ''));

        $this->updateStageProgress($session, 'cleanup', 70, __('messages.update_stage_cleanup_removing_temp'), null, null, true);
        $this->deleteTempRoot($session);

        $this->updateStageProgress($session, 'cleanup', 90, __('messages.update_stage_cleanup_disabling_maintenance'), null, null, true);
        $this->maintenanceMode->disable($actor, 'admin_update_success');

        $this->updateStageProgress($session, 'cleanup', 100, __('messages.update_stage_cleanup_done'), null, null, true);
    }

    private function failSession(
        array $session,
        string $message,
        bool $persist,
        ?string $stage = null,
        ?User $actor = null
    ): array {
        $stage = $stage ?: (string) ($session['current_stage'] ?? 'download');
        $session['status'] = 'failed';
        $session['error'] = $message;
        $session['detail'] = $message;
        $session['failed_at'] = time();
        $session['updated_at'] = time();
        $this->markStage($session, $stage, 'failed', (int) ($session['stage_percent'] ?? 0), $message, $message);

        if ($this->stageRequiresMaintenanceRecovery($stage, $session) && ! $this->maintenanceMode->isEnabled()) {
            $this->maintenanceMode->enable($actor, 'admin_update_failure_recovery');
            $session['maintenance_started'] = true;
        }

        if ($persist) {
            $this->saveSession($session);
        }

        return $session;
    }

    private function stageRequiresMaintenanceRecovery(string $stage, array $session): bool
    {
        if (! empty($session['maintenance_started'])) {
            return true;
        }

        return array_search($stage, array_keys(self::STAGES), true) >= array_search('enable_maintenance', array_keys(self::STAGES), true);
    }

    private function nextExecutableStage(array $session): ?string
    {
        foreach ($session['stages'] ?? [] as $stage) {
            if (in_array($stage['status'] ?? '', ['pending', 'failed'], true)) {
                return (string) $stage['key'];
            }
        }

        return null;
    }

    private function markStage(
        array &$session,
        string $stage,
        string $status,
        int $percent,
        string $detail = '',
        ?string $error = null
    ): void {
        foreach ($session['stages'] as &$item) {
            if (($item['key'] ?? '') !== $stage) {
                continue;
            }

            $now = time();
            $item['status'] = $status;
            $item['percent'] = max(0, min(100, $percent));
            $item['detail'] = $detail;
            $item['error'] = $error;

            if ($status === 'running' && empty($item['started_at'])) {
                $item['started_at'] = $now;
            }

            if (in_array($status, ['completed', 'failed'], true)) {
                $item['finished_at'] = $now;
            }

            break;
        }
        unset($item);

        $session['current_stage'] = $stage;
        $session['stage_percent'] = max(0, min(100, $percent));
        $session['detail'] = $detail;
        $session['updated_at'] = time();
    }

    private function updateStageProgress(
        array &$session,
        string $stage,
        int $percent,
        string $detail,
        ?int $bytesDone = null,
        ?int $bytesTotal = null,
        bool $persist = false
    ): void {
        if ($bytesDone !== null) {
            $session['bytes_done'] = $bytesDone;
        }

        if ($bytesTotal !== null) {
            $session['bytes_total'] = $bytesTotal;
        }

        $this->markStage($session, $stage, 'running', $percent, $detail);

        if ($persist) {
            $this->saveSession($session);
        }
    }

    private function saveSession(array $session): void
    {
        $session['updated_at'] = time();

        Option::updateOrCreate(
            [
                'name' => (string) $session['token'],
                'o_type' => self::SESSION_OPTION_TYPE,
            ],
            [
                'o_valuer' => json_encode($session, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'o_parent' => (int) ($session['started_by'] ?? 0),
                'o_order' => (int) ($session['updated_at'] ?? time()),
                'o_mode' => (string) ($session['status'] ?? 'pending'),
            ]
        );
    }

    private function loadSession(string $token): array
    {
        $row = Option::where('o_type', self::SESSION_OPTION_TYPE)
            ->where('name', $token)
            ->first();

        if (! $row) {
            throw new RuntimeException(__('messages.update_session_not_found'));
        }

        $session = json_decode((string) $row->o_valuer, true);
        if (! is_array($session)) {
            throw new RuntimeException(__('messages.update_session_not_found'));
        }

        return $session;
    }

    private function activeSession(): ?array
    {
        $rows = Option::where('o_type', self::SESSION_OPTION_TYPE)
            ->whereIn('o_mode', ['pending', 'running', 'failed'])
            ->orderByDesc('id')
            ->get();

        foreach ($rows as $row) {
            $session = json_decode((string) $row->o_valuer, true);
            if (! is_array($session)) {
                continue;
            }

            if (in_array($session['status'] ?? '', ['pending', 'running', 'failed'], true)) {
                return $session;
            }
        }

        return null;
    }

    private function formatSession(array $session): array
    {
        $currentStage = (string) ($session['current_stage'] ?? 'initialize');
        $stages = array_map(function (array $stage): array {
            $key = (string) ($stage['key'] ?? '');
            $definition = self::STAGES[$key] ?? ['label' => $key, 'icon' => 'circle'];

            return array_merge($stage, [
                'label' => __($definition['label']),
                'icon' => $definition['icon'],
            ]);
        }, $session['stages'] ?? []);

        $session['stages'] = $stages;
        $session['stage_label'] = __(self::STAGES[$currentStage]['label'] ?? $currentStage);
        $session['percent'] = $this->overallPercent($session);
        $session['can_retry'] = ($session['status'] ?? '') === 'failed';
        $session['can_cancel'] = $this->canCancel($session);

        unset($session['release_data'], $session['download_url'], $session['paths']);

        return $session;
    }

    private function overallPercent(array $session): int
    {
        $stages = $session['stages'] ?? [];
        $total = max(count($stages), 1);
        $completed = 0;
        $activeFraction = 0.0;
        $currentStage = (string) ($session['current_stage'] ?? '');

        foreach ($stages as $stage) {
            if (($stage['status'] ?? '') === 'completed') {
                $completed++;
                continue;
            }

            if (($stage['key'] ?? '') === $currentStage) {
                $activeFraction = ((int) ($stage['percent'] ?? 0)) / 100;
            }

            break;
        }

        if (($session['status'] ?? '') === 'completed') {
            return 100;
        }

        return (int) min(99, round((($completed + $activeFraction) / $total) * 100));
    }

    private function canCancel(array $session): bool
    {
        if (in_array($session['status'] ?? '', ['completed', 'cancelled'], true)) {
            return false;
        }

        if (! empty($session['maintenance_started'])) {
            return false;
        }

        $protectedStages = ['enable_maintenance', 'finalize', 'cleanup'];
        foreach ($session['stages'] ?? [] as $stage) {
            if (! in_array($stage['key'] ?? '', $protectedStages, true)) {
                continue;
            }

            if (($stage['status'] ?? 'pending') !== 'pending') {
                return false;
            }
        }

        return true;
    }

    private function isRunningSessionStale(array $session): bool
    {
        return time() - (int) ($session['updated_at'] ?? 0) > self::RUNNING_STALE_SECONDS;
    }

    private function stageStartDetail(string $stage): string
    {
        return match ($stage) {
            'download' => __('messages.update_stage_download_start'),
            'extract' => __('messages.update_stage_extract_detail'),
            'package_preflight' => __('messages.update_stage_package_preflight_detail'),
            'enable_maintenance' => __('messages.update_stage_enable_maintenance_detail'),
            'finalize' => __('messages.update_stage_finalize_detail'),
            'cleanup' => __('messages.update_stage_cleanup_detail'),
            default => __('messages.update_stage_initialize_done'),
        };
    }

    private function currentStageDetail(array $session, string $stage): string
    {
        foreach ($session['stages'] ?? [] as $item) {
            if (($item['key'] ?? '') === $stage) {
                return (string) ($item['detail'] ?? '');
            }
        }

        return '';
    }

    private function stageDoneDetail(string $stage, array $session): string
    {
        return match ($stage) {
            'download' => __('messages.update_stage_download_done', [
                'size' => $this->formatBytes((int) ($session['bytes_done'] ?? 0)),
            ]),
            'extract' => __('messages.update_stage_extract_done', ['count' => (int) ($session['file_count'] ?? 0)]),
            'package_preflight' => __('messages.update_stage_package_preflight_done', [
                'count' => count((array) data_get($session, 'package_preflight.pending_migrations', [])),
            ]),
            'enable_maintenance' => __('messages.update_stage_enable_maintenance_done'),
            'finalize' => __('messages.update_stage_finalize_done'),
            'cleanup' => __('messages.update_stage_cleanup_done'),
            default => __('messages.update_stage_initialize_done'),
        };
    }

    private function clearUpdateCaches(string $currentVersion): void
    {
        Cache::forget('github_latest_release');
        Cache::forget('system_version_checked');

        if ($currentVersion !== '') {
            Cache::forget('system_version_checked_' . str_replace('.', '-', $currentVersion));
        }
    }

    private function deleteTempRoot(array $session): void
    {
        $root = (string) data_get($session, 'paths.root', '');
        if ($root === '' || ! File::exists($root)) {
            return;
        }

        $base = storage_path('app' . DIRECTORY_SEPARATOR . self::SESSION_ROOT);
        File::ensureDirectoryExists($base, 0755, true);

        $realBase = realpath($base);
        $realRoot = realpath($root);

        if ($realBase === false || $realRoot === false) {
            return;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $realBase = strtolower($realBase);
            $realRoot = strtolower($realRoot);
        }

        $realBase = rtrim($realBase, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $realRoot = rtrim($realRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (! str_starts_with($realRoot, $realBase)) {
            throw new RuntimeException(__('messages.update_package_root_missing'));
        }

        File::deleteDirectory($root);
    }

    private function resolvePackageRoot(string $extractPath): string
    {
        $directories = array_values(array_filter(
            File::directories($extractPath),
            static fn (string $directory): bool => ! str_starts_with(basename($directory), '.')
        ));
        $rootFiles = array_values(array_filter(
            File::files($extractPath),
            static fn ($file): bool => ! str_starts_with($file->getFilename(), '.')
        ));

        if ($rootFiles === [] && count($directories) === 1) {
            return $directories[0];
        }

        if (File::isDirectory($extractPath)) {
            return $extractPath;
        }

        throw new RuntimeException(__('messages.update_package_root_missing'));
    }

    private function assertSafeZipEntry(string $name): void
    {
        $normalized = str_replace('\\', '/', $name);

        if (
            $normalized === ''
            || str_contains($normalized, "\0")
            || str_starts_with($normalized, '/')
            || preg_match('/^[A-Za-z]:\//', $normalized) === 1
            || in_array('..', explode('/', trim($normalized, '/')), true)
        ) {
            throw new RuntimeException(__('messages.update_zip_unsafe_path', ['path' => $name]));
        }
    }

    private function assertSafeRelativePath(string $path): void
    {
        $normalized = str_replace('\\', '/', $path);

        if (
            $normalized === ''
            || str_contains($normalized, "\0")
            || str_starts_with($normalized, '/')
            || preg_match('/^[A-Za-z]:\//', $normalized) === 1
            || in_array('..', explode('/', trim($normalized, '/')), true)
        ) {
            throw new RuntimeException(__('messages.update_zip_unsafe_path', ['path' => $path]));
        }
    }

    private function relativePath(string $root, string $path): string
    {
        $root = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $root), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        if (! str_starts_with($path, $root)) {
            throw new RuntimeException(__('messages.update_package_root_missing'));
        }

        return str_replace(DIRECTORY_SEPARATOR, '/', substr($path, strlen($root)));
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $index = 0;
        $value = (float) $bytes;

        while ($value >= 1024 && $index < count($units) - 1) {
            $value /= 1024;
            $index++;
        }

        return number_format($value, $index === 0 ? 0 : 2) . ' ' . $units[$index];
    }

    private function getAssetDownloadUrl(array $releaseData): ?string
    {
        $assets = $releaseData['assets'] ?? [];
        foreach ($assets as $asset) {
            if (str_ends_with((string) ($asset['name'] ?? ''), '.zip')) {
                return $asset['browser_download_url'] ?? null;
            }
        }

        return $releaseData['zipball_url'] ?? null;
    }

    private function getAssetSize(array $releaseData): ?int
    {
        $assets = $releaseData['assets'] ?? [];
        foreach ($assets as $asset) {
            if (str_ends_with((string) ($asset['name'] ?? ''), '.zip')) {
                return isset($asset['size']) ? (int) $asset['size'] : null;
            }
        }

        return null;
    }
}
