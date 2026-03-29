<?php

namespace App\Services;

use App\Models\SeoSetting;

class RobotsTxtService
{
    public function render(?SeoSetting $settings = null): string
    {
        $settings ??= SeoSetting::current();

        if (!$settings->allow_indexing) {
            return "User-agent: *\nDisallow: /\nSitemap: " . url('/sitemap.xml') . "\n";
        }

        $lines = ['User-agent: *'];

        $allowPaths = $this->normalizePaths($settings->robots_allow_paths, ['/']);
        foreach ($allowPaths as $path) {
            $lines[] = 'Allow: ' . $path;
        }

        $defaultDisallow = [
            '/admin',
            '/login',
            '/register',
            '/password/reset',
            '/messages',
            '/notification',
            '/profile/edit',
            '/settings',
        ];

        $disallowPaths = array_values(array_unique(array_merge(
            $defaultDisallow,
            $this->normalizePaths($settings->robots_disallow_paths)
        )));

        foreach ($disallowPaths as $path) {
            $lines[] = 'Disallow: ' . $path;
        }

        $lines[] = 'Sitemap: ' . url('/sitemap.xml');

        foreach ($this->normalizeRawLines($settings->robots_extra) as $line) {
            $lines[] = $line;
        }

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    public function blocksAll(?SeoSetting $settings = null): bool
    {
        $rendered = $this->render($settings);

        return str_contains($rendered, "Disallow: /\n")
            && !str_contains($rendered, "Allow: /\n");
    }

    private function normalizePaths(?string $value, array $defaults = []): array
    {
        $lines = $this->normalizeRawLines($value);

        if ($lines === []) {
            return $defaults;
        }

        $paths = [];
        foreach ($lines as $line) {
            $path = '/' . ltrim(trim($line), '/');
            $paths[] = $path === '//' ? '/' : $path;
        }

        return array_values(array_unique($paths));
    }

    private function normalizeRawLines(?string $value): array
    {
        $value = trim((string) $value);

        if ($value === '') {
            return [];
        }

        $lines = preg_split('/\r\n|\r|\n/', $value) ?: [];

        return array_values(array_filter(array_map('trim', $lines), static fn (string $line) => $line !== ''));
    }
}
