<?php

namespace App\Services;

use App\Models\SeoSetting;
use Illuminate\Support\Facades\File;

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

    public function write(?SeoSetting $settings = null): void
    {
        File::put(base_path('robots.txt'), $this->render($settings));
    }

    public function ensureDefaultFile(): void
    {
        $path = base_path('robots.txt');

        if (!File::exists($path)) {
            $this->write();
            return;
        }

        $contents = trim((string) File::get($path));
        $sitemapUrl = url('/sitemap.xml');
        $sitemapLine = 'Sitemap: ' . $sitemapUrl;

        // If it's the default "Disallow: /" or if the Sitemap line is missing/wrong, update it.
        if ($contents === 'User-agent: *' . PHP_EOL . 'Disallow: /' || 
            !str_contains($contents, 'Sitemap:') || 
            !str_contains($contents, $sitemapUrl)) {
            $this->write();
        }
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
