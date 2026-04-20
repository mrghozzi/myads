<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use ZipArchive;

class ExtensionPackageUpgrader
{
    public function upgradeFromDownload(
        string $type,
        string $slug,
        string $downloadUrl,
        string $extensionsPath,
        string $metadataFile,
        string $cacheKey,
        string $currentVersion,
        ?string $existingDirectory = null,
        bool $mustExist = true
    ): bool|string {
        $existingDirectory = $existingDirectory ?: $slug;
        $extensionsPath = rtrim($extensionsPath, DIRECTORY_SEPARATOR);
        $currentPath = $extensionsPath . DIRECTORY_SEPARATOR . $existingDirectory;
        $targetPath = $extensionsPath . DIRECTORY_SEPARATOR . $slug;
        $tempRoot = storage_path('app/temp_extensions/' . $type . '-' . $slug . '-' . Str::lower(Str::random(12)));
        $tempZipPath = $tempRoot . DIRECTORY_SEPARATOR . 'package.zip';
        $tempExtractPath = $tempRoot . DIRECTORY_SEPARATOR . 'extracted';
        $stagedPath = $tempRoot . DIRECTORY_SEPARATOR . 'staged' . DIRECTORY_SEPARATOR . $slug;
        $backupPath = $extensionsPath . DIRECTORY_SEPARATOR . '.backup-' . $slug . '-' . time() . '-' . Str::lower(Str::random(6));
        $backedUp = false;

        try {
            // Safety: Validate URL
            if (!filter_var($downloadUrl, FILTER_VALIDATE_URL)) {
                return __('messages.invalid_url') ?? 'Invalid download URL';
            }

            if ($mustExist && ! File::exists($currentPath)) {
                return __('messages.extension_not_installed');
            }

            if ($mustExist && $currentPath !== $targetPath && File::exists($targetPath)) {
                return __('messages.extension_target_conflict', ['slug' => $slug]);
            }

            if (!$mustExist && File::exists($targetPath)) {
                return __('messages.extension_already_exists') ?? 'Extension already exists';
            }


            File::makeDirectory($tempExtractPath, 0755, true);
            File::makeDirectory(dirname($stagedPath), 0755, true);

            $this->downloadArchive($downloadUrl, $tempZipPath, $type);
            $this->extractArchive($tempZipPath, $tempExtractPath);

            $packageRoot = $this->resolvePackageRoot($tempExtractPath, $metadataFile);
            $metadata = $this->loadMetadata($packageRoot, $metadataFile);

            $packageSlug = trim((string) ($metadata['slug'] ?? ''));
            if ($packageSlug === '') {
                return __('messages.extension_metadata_invalid', ['file' => $metadataFile]);
            }

            if ($packageSlug !== $slug) {
                return __('messages.extension_slug_mismatch', [
                    'expected' => $slug,
                    'found' => $packageSlug,
                ]);
            }

            $minMyads = trim((string) ($metadata['min_myads'] ?? ''));
            if ($minMyads !== '' && version_compare($currentVersion, $minMyads, '<')) {
                return __('messages.extension_requires_newer_myads', ['version' => $minMyads]);
            }

            if (! File::copyDirectory($packageRoot, $stagedPath)) {
                throw new RuntimeException(__('messages.extension_package_invalid', ['file' => $metadataFile]));
            }

            if ($mustExist) {
                if (! File::moveDirectory($currentPath, $backupPath)) {
                    return __('messages.extension_upgrade_failed_message', [
                        'message' => __('messages.extension_rollback_failed'),
                    ]);
                }
                $backedUp = true;
            }


            if (File::exists($targetPath) && ! File::deleteDirectory($targetPath)) {
                throw new RuntimeException(__('messages.extension_target_conflict', ['slug' => $slug]));
            }

            if (! File::moveDirectory($stagedPath, $targetPath)) {
                throw new RuntimeException(__('messages.extension_package_invalid', ['file' => $metadataFile]));
            }

            if (File::exists($backupPath)) {
                File::deleteDirectory($backupPath);
            }

            Cache::forget($cacheKey);

            return true;
        } catch (\Throwable $e) {
            if ($backedUp) {
                try {
                    if (File::exists($targetPath)) {
                        File::deleteDirectory($targetPath);
                    }

                    if (! File::moveDirectory($backupPath, $currentPath)) {
                        throw new RuntimeException(__('messages.extension_rollback_failed'));
                    }
                } catch (\Throwable $rollbackException) {
                    return __('messages.extension_upgrade_failed_message', [
                        'message' => $rollbackException->getMessage(),
                    ]);
                }
            }

            return __('messages.extension_upgrade_failed_message', [
                'message' => $e->getMessage(),
            ]);
        } finally {
            if (File::exists($tempRoot)) {
                File::deleteDirectory($tempRoot);
            }
        }
    }

    private function downloadArchive(string $downloadUrl, string $tempZipPath, string $type): void
    {
        if (! File::exists(dirname($tempZipPath))) {
            File::makeDirectory(dirname($tempZipPath), 0755, true);
        }

        $response = Http::withHeaders([
            'User-Agent' => 'MyAds-' . ucfirst($type) . '-Updater',
        ])->timeout(120)->get($downloadUrl);

        if (! $response->successful()) {
            throw new RuntimeException(__('messages.download_failed') . ' (HTTP ' . $response->status() . ')');
        }

        File::put($tempZipPath, $response->body());
    }

    private function extractArchive(string $tempZipPath, string $tempExtractPath): void
    {
        $zip = new ZipArchive;

        if ($zip->open($tempZipPath) !== true) {
            throw new RuntimeException(__('messages.zip_open_failed'));
        }

        $zip->extractTo($tempExtractPath);
        $zip->close();
    }

    private function resolvePackageRoot(string $tempExtractPath, string $metadataFile): string
    {
        $rootMetadataPath = $tempExtractPath . DIRECTORY_SEPARATOR . $metadataFile;
        $directories = array_values(array_filter(
            File::directories($tempExtractPath),
            fn (string $directory): bool => ! str_starts_with(basename($directory), '.')
        ));
        $rootFiles = array_values(array_filter(
            File::files($tempExtractPath),
            fn ($file): bool => ! str_starts_with($file->getFilename(), '.')
        ));

        $metadataDirectories = array_values(array_filter(
            $directories,
            fn (string $directory): bool => File::exists($directory . DIRECTORY_SEPARATOR . $metadataFile)
        ));

        if (File::exists($rootMetadataPath)) {
            if ($metadataDirectories !== []) {
                throw new RuntimeException(__('messages.extension_package_invalid', ['file' => $metadataFile]));
            }

            return $tempExtractPath;
        }

        if ($rootFiles === [] && count($directories) === 1 && count($metadataDirectories) === 1) {
            return $metadataDirectories[0];
        }

        throw new RuntimeException(__('messages.extension_package_invalid', ['file' => $metadataFile]));
    }

    /**
     * @return array<string, mixed>
     */
    private function loadMetadata(string $packageRoot, string $metadataFile): array
    {
        $metadataPath = $packageRoot . DIRECTORY_SEPARATOR . $metadataFile;
        $metadata = json_decode((string) File::get($metadataPath), true);

        if (! is_array($metadata)) {
            throw new RuntimeException(__('messages.extension_metadata_invalid', ['file' => $metadataFile]));
        }

        return $metadata;
    }
}
