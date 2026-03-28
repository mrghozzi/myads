<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class ReleaseUpdateService
{
    public function __construct(
        private readonly GamificationService $gamification,
        private readonly UpdateSafetyService $updateSafety
    ) {
    }

    public function applyLatestRelease(string $currentVersion, ?array $releaseData = null): string
    {
        if (! is_array($releaseData)) {
            throw new \RuntimeException(__('messages.update_fetch_failed'));
        }

        $latestVersion = ltrim((string) ($releaseData['tag_name'] ?? ''), 'v');
        if (! version_compare($latestVersion, $currentVersion, '>')) {
            throw new \RuntimeException(__('messages.already_up_to_date'));
        }

        $downloadUrl = $this->getAssetDownloadUrl($releaseData);
        if (! $downloadUrl) {
            throw new \RuntimeException(__('messages.no_download_url'));
        }

        $tempZipPath = storage_path('app/myads_update.zip');
        $tempExtractPath = storage_path('app/myads_update_extracted');

        try {
            $this->downloadUpdateArchive($downloadUrl, $tempZipPath);

            File::deleteDirectory($tempExtractPath);
            File::makeDirectory($tempExtractPath, 0755, true);

            $zip = new \ZipArchive;
            if ($zip->open($tempZipPath) !== true) {
                throw new \RuntimeException(__('messages.zip_open_failed'));
            }

            $zip->extractTo($tempExtractPath);
            $zip->close();

            $directories = File::directories($tempExtractPath);
            $innerFolder = count($directories) > 0 ? $directories[0] : $tempExtractPath;

            $releaseReport = $this->updateSafety->run([
                $innerFolder . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations',
            ]);

            if (! $releaseReport->isSafe()) {
                throw new \RuntimeException(__('messages.update_blocked_preflight', [
                    'details' => implode(' ', $releaseReport->failureMessages()),
                ]));
            }

            $basePath = base_path();

            foreach (File::allFiles($innerFolder) as $file) {
                $relativePath = str_replace($innerFolder . DIRECTORY_SEPARATOR, '', $file->getRealPath());
                $targetPath = $basePath . DIRECTORY_SEPARATOR . $relativePath;
                $targetDir = dirname($targetPath);

                if (! File::exists($targetDir)) {
                    File::makeDirectory($targetDir, 0755, true);
                }

                File::copy($file->getRealPath(), $targetPath);
            }

            $updateScript = base_path('requests/update.php');
            if (File::exists($updateScript)) {
                include_once $updateScript;
            }

            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('optimize:clear');

            $this->gamification->repairQuestData();

            Cache::forget('github_latest_release');
            Cache::forget('system_version_checked');

            return $latestVersion;
        } finally {
            if (File::exists($tempZipPath)) {
                File::delete($tempZipPath);
            }

            if (File::exists($tempExtractPath)) {
                File::deleteDirectory($tempExtractPath);
            }
        }
    }

    private function downloadUpdateArchive(string $downloadUrl, string $tempZipPath): void
    {
        try {
            $response = Http::withoutVerifying()->withHeaders([
                'User-Agent' => 'MyAds-Updater/1.0',
            ])->timeout(300)->withOptions([
                'sink' => $tempZipPath,
                'allow_redirects' => ['max' => 10],
            ])->get($downloadUrl);

            if (! $response->successful()) {
                if (File::exists($tempZipPath)) {
                    File::delete($tempZipPath);
                }

                throw new \RuntimeException(__('messages.download_failed') . ' (HTTP ' . $response->status() . ')');
            }
        } catch (\RuntimeException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            if (File::exists($tempZipPath)) {
                File::delete($tempZipPath);
            }

            throw new \RuntimeException(__('messages.download_failed') . ' ' . $exception->getMessage(), previous: $exception);
        }
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
}
