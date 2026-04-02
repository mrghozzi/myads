<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class ExtensionManifestReader
{
    /**
     * @return array<string, string>|null
     */
    public function readFromSource(string $source, string $metadataFile): ?array
    {
        $tempPath = null;

        try {
            $zipPath = $this->resolveZipPath($source);
            if ($zipPath === null) {
                return null;
            }

            $tempPath = $zipPath['temp'] ? $zipPath['path'] : null;

            return $this->readMetadataFromZip($zipPath['path'], $metadataFile);
        } catch (\Throwable) {
            return null;
        } finally {
            if ($tempPath && File::exists($tempPath)) {
                File::delete($tempPath);
            }
        }
    }

    /**
     * @return array{path: string, temp: bool}|null
     */
    private function resolveZipPath(string $source): ?array
    {
        if (filter_var($source, FILTER_VALIDATE_URL)) {
            return $this->downloadRemoteZip($source);
        }

        $relativePath = ltrim($source, '/\\');
        $candidates = [
            base_path($relativePath),
            public_path($relativePath),
        ];

        foreach ($candidates as $candidate) {
            if (File::exists($candidate) && File::isFile($candidate)) {
                return ['path' => $candidate, 'temp' => false];
            }
        }

        if (Storage::exists($source)) {
            return ['path' => Storage::path($source), 'temp' => false];
        }

        return null;
    }

    /**
     * @return array{path: string, temp: bool}|null
     */
    private function downloadRemoteZip(string $source): ?array
    {
        $response = Http::withHeaders([
            'User-Agent' => 'MyAds-Extension-Marketplace',
        ])->timeout(20)->get($source);

        if (! $response->successful()) {
            return null;
        }

        $tempDirectory = storage_path('app/temp_marketplace_manifests');
        if (! File::exists($tempDirectory)) {
            File::makeDirectory($tempDirectory, 0755, true);
        }

        $tempPath = $tempDirectory . DIRECTORY_SEPARATOR . Str::lower(Str::random(24)) . '.zip';
        File::put($tempPath, $response->body());

        return ['path' => $tempPath, 'temp' => true];
    }

    /**
     * @return array<string, string>|null
     */
    private function readMetadataFromZip(string $zipPath, string $metadataFile): ?array
    {
        $zip = new ZipArchive();

        if ($zip->open($zipPath) !== true) {
            return null;
        }

        try {
            $manifestPath = $this->resolveManifestPath($zip, $metadataFile);
            if ($manifestPath === null) {
                return null;
            }

            $rawManifest = $zip->getFromName($manifestPath);
            if ($rawManifest === false) {
                return null;
            }

            $metadata = json_decode($rawManifest, true);
            if (! is_array($metadata)) {
                return null;
            }

            $normalized = [
                'name' => trim((string) ($metadata['name'] ?? '')),
                'slug' => trim((string) ($metadata['slug'] ?? '')),
                'version' => trim((string) ($metadata['version'] ?? '')),
                'author' => trim((string) ($metadata['author'] ?? '')),
                'description' => trim((string) ($metadata['description'] ?? '')),
                'min_myads' => trim((string) ($metadata['min_myads'] ?? '')),
            ];

            if ($normalized['name'] === '' || $normalized['slug'] === '' || $normalized['version'] === '') {
                return null;
            }

            return $normalized;
        } finally {
            $zip->close();
        }
    }

    private function resolveManifestPath(ZipArchive $zip, string $metadataFile): ?string
    {
        $rootManifest = null;
        $nestedCandidates = [];
        $topLevelRoots = [];

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $entryName = (string) $zip->getNameIndex($index);
            $entryName = str_replace('\\', '/', $entryName);
            $entryName = trim($entryName, '/');

            if ($entryName === '' || $this->isHiddenEntry($entryName)) {
                continue;
            }

            $segments = explode('/', $entryName);
            $topLevelRoots[$segments[0]] = true;

            if ($entryName === $metadataFile) {
                $rootManifest = $entryName;
                continue;
            }

            if (count($segments) === 2 && $segments[1] === $metadataFile) {
                $nestedCandidates[] = $entryName;
            }
        }

        if ($rootManifest !== null && $nestedCandidates === []) {
            return $rootManifest;
        }

        if (count($topLevelRoots) === 1 && count($nestedCandidates) === 1) {
            return $nestedCandidates[0];
        }

        return null;
    }

    private function isHiddenEntry(string $entryName): bool
    {
        $segments = explode('/', $entryName);

        foreach ($segments as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..') {
                return true;
            }

            if (str_starts_with($segment, '.') || str_starts_with($segment, '__MACOSX')) {
                return true;
            }
        }

        return false;
    }
}
