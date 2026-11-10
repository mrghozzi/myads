<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use ZipArchive;

class ThemeManager
{
    protected $themePath;
    protected ExtensionPackageUpgrader $packageUpgrader;

    public function __construct(?ExtensionPackageUpgrader $packageUpgrader = null)
    {
        $this->themePath = base_path('themes');
        $this->packageUpgrader = $packageUpgrader ?? app(ExtensionPackageUpgrader::class);
    }

    /**
     * Get all themes.
     *
     * @return array
     */
    public function getAllThemes()
    {
        $themes = [];
        if (!File::exists($this->themePath)) {
            File::makeDirectory($this->themePath, 0755, true);
        }

        $directories = $this->themeDirectories();
        $activeThemeSlug = $this->getActiveThemeSlug();

        foreach ($directories as $directory) {
            $jsonFile = $directory . '/theme.json';
            
            if (File::exists($jsonFile)) {
                $themeData = json_decode(File::get($jsonFile), true);
                
                if ($themeData) {
                    $themeData['directory'] = basename($directory);
                    $themeData['path'] = $directory;
                    $themeData['latest_url'] = $themeData['latest'] ?? null;
                    $themeData['min_myads'] = $themeData['min_myads'] ?? null;
                    $themeData['max_myads'] = $themeData['max_myads'] ?? null;
                    $themeData['ADStn_url'] = $themeData['ADStn_url'] ?? null;
                    $themeData['siteweb'] = $themeData['siteweb'] ?? null;
                    $themeData['author_url'] = $themeData['author_url'] ?? null;
                    
                    // Parent/Child Theme Support
                    $themeData['parent'] = $themeData['parent'] ?? null;
                    $themeData['is_child'] = !empty($themeData['parent']);
                    $themeData['parent_name'] = null;

                    // Template files count in views/
                    $viewsDir = $directory . '/views';
                    $themeData['template_count'] = File::isDirectory($viewsDir) ? count(File::allFiles($viewsDir)) : 0;
                    
                    $compat = self::checkCompatibility($themeData['min_myads'], $themeData['max_myads'], $themeData['name'] ?? $themeData['slug'] ?? '');
                    $themeData['is_compatible'] = $compat['is_compatible'];
                    $themeData['compatibility_status'] = $compat['status'];
                    $themeData['compatibility_message'] = $compat['message'];
                    
                    // Determine thumbnail filename
                    if (!isset($themeData['thumbnail'])) {
                        $screenshotPath = $directory . '/screenshot.png';
                        if (File::exists($screenshotPath)) {
                            $themeData['thumbnail'] = 'screenshot.png';
                        }
                    }

                    if (isset($themeData['thumbnail']) && File::exists($directory . '/' . $themeData['thumbnail'])) {
                        $themeData['screenshot'] = route('admin.themes.thumbnail', $themeData['slug']);
                        $themeData['has_screenshot'] = true;
                    } else {
                        $themeData['screenshot'] = null;
                        $themeData['has_screenshot'] = false;
                    }

                    $themeData['is_active'] = ($themeData['slug'] === $activeThemeSlug);
                    
                    $themes[] = $themeData;
                }
            }
        }

        // Resolve parent names across themes
        $themeMap = [];
        foreach ($themes as $t) {
            $themeMap[$t['slug']] = $t['name'] ?? $t['slug'];
        }

        foreach ($themes as &$t) {
            if (!empty($t['parent'])) {
                $t['parent_name'] = $themeMap[$t['parent']] ?? ucfirst($t['parent']);
            }
        }
        unset($t);

        return $themes;
    }

    /**
     * Get the active theme slug.
     *
     * @return string|null
     */
    public function getActiveThemeSlug()
    {
        $setting = Setting::first();
        return $setting ? $setting->styles : 'default';
    }

    /**
     * Activate a theme.
     *
     * @param string $slug
     * @return bool
     * @throws \RuntimeException
     */
    public function activate($slug)
    {
        // Find theme directory by slug
        $themeDir = $this->findDirectoryBySlug($slug);
        
        if (!$themeDir) {
            return false;
        }

        $fullThemeDir = $this->themePath . '/' . $themeDir;
        $manifestPath = $fullThemeDir . '/theme.json';
        $themeData = [];
        if (File::exists($manifestPath)) {
            $themeData = json_decode(File::get($manifestPath), true) ?: [];
            $compat = self::checkCompatibility($themeData['min_myads'] ?? null, $themeData['max_myads'] ?? null, $themeData['name'] ?? $slug);
            if (!$compat['is_compatible']) {
                throw new \RuntimeException($compat['message']);
            }

            // Verify parent theme exists if child theme
            if (!empty($themeData['parent']) && is_string($themeData['parent']) && $themeData['parent'] !== $slug) {
                $parentSlug = trim($themeData['parent']);
                if (!$this->findDirectoryBySlug($parentSlug)) {
                    throw new \RuntimeException(__('messages.theme_parent_missing', [
                        'theme' => $themeData['name'] ?? $slug,
                        'parent' => $parentSlug,
                    ]));
                }
            }
        }

        // Security & Integrity: Validate PHP syntax before activation
        $syntaxErrors = $this->validateThemeSyntax($fullThemeDir);
        if (!empty($syntaxErrors)) {
            $errDetail = implode(', ', $syntaxErrors);
            $msg = __('messages.theme_syntax_error', ['theme' => $themeData['name'] ?? $slug, 'error' => $errDetail]);
            throw new \RuntimeException($msg !== 'messages.theme_syntax_error' ? $msg : "Theme [{$slug}] contains PHP syntax errors: {$errDetail}");
        }

        // Publish assets to public directory
        $this->publishThemeAssets($slug);

        $setting = \App\Models\Setting::first();
        if ($setting) {
            $setting->update(['styles' => $slug]);

            // Clear compiled view cache
            try {
                \Illuminate\Support\Facades\Artisan::call('view:clear');
            } catch (\Throwable $e) {
                // Ignore CLI artisan issues in web context
            }

            \Illuminate\Support\Facades\Cache::forget('theme_updates');

            // Fire lifecycle action hook
            \App\Helpers\Hooks::do_action('theme_activated', $slug, $themeData);

            return true;
        }

        return false;
    }

    /**
     * Install a theme from ZIP archive.
     *
     * @param \Illuminate\Http\UploadedFile|string $file
     * @return bool|string True on success, error message string on failure
     */
    public function install($file)
    {
        $security = app(\App\Services\Security\FileUploadSecurityService::class);
        $filePath = is_string($file) ? $file : $file->getPathname();
        $validation = $security->validateThemeArchive($filePath);

        if (!$validation['valid']) {
            $errorKey = $validation['error'] ?? 'messages.theme_zip_open_failed';
            return __($errorKey);
        }

        $manifestData = $validation['manifest'];
        $slug = $manifestData['slug'];

        // Check min_myads
        if (!empty($manifestData['min_myads'])) {
            if (version_compare(\App\Support\SystemVersion::CURRENT, $manifestData['min_myads'], '<')) {
                return __('messages.theme_requires_newer_myads', [
                    'theme' => $manifestData['name'] ?? $slug,
                    'min' => $manifestData['min_myads'],
                    'current' => \App\Support\SystemVersion::CURRENT,
                ]);
            }
        }

        // Check max_myads
        if (!empty($manifestData['max_myads'])) {
            $normalizedMax = self::normalizeMaxVersion($manifestData['max_myads']);
            if (version_compare(\App\Support\SystemVersion::CURRENT, $normalizedMax, '>')) {
                return __('messages.theme_exceeds_max_myads', [
                    'theme' => $manifestData['name'] ?? $slug,
                    'max' => $manifestData['max_myads'],
                    'current' => \App\Support\SystemVersion::CURRENT,
                ]);
            }
        }

        $targetPath = $this->themePath . '/' . $slug;
        if (File::exists($targetPath)) {
            return __('messages.theme_already_exists');
        }

        $tempExtractPath = storage_path('app/temp_themes/' . uniqid());
        try {
            $zip = new ZipArchive;
            if ($zip->open($filePath) === TRUE) {
                $zip->extractTo($tempExtractPath);
                $zip->close();

                // Find root folder containing theme.json
                $rootFolder = $tempExtractPath;
                if (!File::exists($tempExtractPath . '/theme.json')) {
                    $directories = File::directories($tempExtractPath);
                    if (count($directories) === 1 && File::exists($directories[0] . '/theme.json')) {
                        $rootFolder = $directories[0];
                    }
                }

                // Verify syntax of any PHP files before final installation
                $syntaxErrors = $this->validateThemeSyntax($rootFolder);
                if (!empty($syntaxErrors)) {
                    $errDetail = implode(', ', $syntaxErrors);
                    return __('messages.theme_syntax_error', [
                        'theme' => $manifestData['name'] ?? $slug,
                        'error' => $errDetail,
                    ]);
                }

                File::moveDirectory($rootFolder, $targetPath);

                // Publish assets if theme provides assets/
                $this->publishThemeAssets($slug);

                \Illuminate\Support\Facades\Cache::forget('theme_updates');
                try {
                    \Illuminate\Support\Facades\Artisan::call('view:clear');
                } catch (\Throwable $e) {
                    // Ignore
                }

                \App\Helpers\Hooks::do_action('theme_installed', $slug, $manifestData);

                return true;
            }
            return __('messages.theme_zip_open_failed');
        } finally {
            if (File::exists($tempExtractPath)) {
                File::deleteDirectory($tempExtractPath);
            }
        }
    }

    /**
     * Publish theme assets to public/themes/{slug}/assets.
     *
     * @param string $slug
     * @return void
     */
    public function publishThemeAssets(string $slug): void
    {
        $themeDir = $this->themePath . '/' . $slug;
        $themeAssets = $themeDir . '/assets';
        $publicTarget = public_path("themes/{$slug}/assets");

        if (File::isDirectory($themeAssets)) {
            if (!File::isDirectory($publicTarget)) {
                File::makeDirectory($publicTarget, 0755, true);
            }
            File::copyDirectory($themeAssets, $publicTarget);
        }

        // Also copy screenshot.png if present
        $screenshotFile = $themeDir . '/screenshot.png';
        if (File::exists($screenshotFile)) {
            $publicScreenshot = public_path("themes/{$slug}/screenshot.png");
            if (!File::isDirectory(dirname($publicScreenshot))) {
                File::makeDirectory(dirname($publicScreenshot), 0755, true);
            }
            File::copy($screenshotFile, $publicScreenshot);
        }
    }

    /**
     * Validate PHP syntax of theme files to prevent parse error crashes.
     *
     * @param string $themeDir
     * @return array<int, string>
     */
    public function validateThemeSyntax(string $themeDir): array
    {
        $errors = [];
        $filesToCheck = [];

        if (!File::isDirectory($themeDir)) {
            return $errors;
        }

        $allFiles = File::allFiles($themeDir);
        foreach ($allFiles as $file) {
            $ext = strtolower($file->getExtension());
            if ($ext === 'php') {
                $filesToCheck[] = $file->getPathname();
            }
        }

        foreach ($filesToCheck as $filePath) {
            try {
                $code = File::get($filePath);
                token_get_all($code, TOKEN_PARSE);
            } catch (\ParseError $e) {
                $relName = str_replace($themeDir, '', $filePath);
                $errors[] = basename($themeDir) . $relName . ' (Line ' . $e->getLine() . '): ' . $e->getMessage();
            } catch (\Throwable $e) {
                // Ignore non-parse runtime issues
            }
        }

        return $errors;
    }

    /**
     * Scaffold a new starter or child theme.
     *
     * @param array $data
     * @return array
     */
    public function createStarterTheme(array $data): array
    {
        $name = trim($data['name'] ?? '');
        $rawSlug = trim($data['slug'] ?? '');
        $slug = strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '', $rawSlug ?: \Illuminate\Support\Str::slug($name)));
        $parent = trim($data['parent'] ?? 'default');
        $author = trim($data['author'] ?? (auth()->check() ? auth()->user()->username : 'MYADS'));
        $description = trim($data['description'] ?? "A modern starter theme for MYADS based on {$parent}.");
        $version = trim($data['version'] ?? '1.0.0');

        if (empty($slug)) {
            return ['success' => false, 'message' => __('messages.invalid_theme_slug')];
        }

        if ($slug === 'default') {
            return ['success' => false, 'message' => __('messages.theme_slug_reserved')];
        }

        if (File::exists($this->themePath . '/' . $slug)) {
            return ['success' => false, 'message' => __('messages.theme_already_exists')];
        }

        $themeDir = $this->themePath . '/' . $slug;
        File::makeDirectory($themeDir . '/views', 0755, true);
        File::makeDirectory($themeDir . '/assets/css', 0755, true);
        File::makeDirectory($themeDir . '/assets/js', 0755, true);

        // Build theme.json manifest
        $manifest = [
            'name' => $name ?: ucfirst($slug),
            'slug' => $slug,
            'version' => $version,
            'parent' => $parent ?: 'default',
            'description' => $description,
            'author' => $author,
            'min_myads' => '4.7.0',
            'siteweb' => url('/'),
            'thumbnail' => 'screenshot.png',
        ];

        File::put($themeDir . '/theme.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        // Starter theme CSS
        $starterCss = "/* Theme: {$manifest['name']} - Inherits from: {$parent} */\n"
            . ":root {\n"
            . "    /* Customize theme CSS variables here */\n"
            . "    --theme-accent-color: #6366f1;\n"
            . "}\n";
        File::put($themeDir . '/assets/css/theme.css', $starterCss);

        // Starter theme JS
        $starterJs = "// Theme: {$manifest['name']}\n"
            . "document.addEventListener('DOMContentLoaded', function () {\n"
            . "    console.log('Theme [{$manifest['name']}] initialized.');\n"
            . "});\n";
        File::put($themeDir . '/assets/js/theme.js', $starterJs);

        // Starter sample view override
        File::makeDirectory($themeDir . '/views/partials', 0755, true);
        $sampleBlade = "{{-- Starter Child Theme Partial for {$manifest['name']} --}}\n"
            . "<div class=\"theme-badge-notice d-none\"><!-- Custom theme partial for {$manifest['name']} --></div>\n";
        File::put($themeDir . '/views/partials/theme_badge.blade.php', $sampleBlade);

        // Publish assets to public/
        $this->publishThemeAssets($slug);

        \App\Helpers\Hooks::do_action('theme_created', $slug, $manifest);

        return [
            'success' => true,
            'slug' => $slug,
            'path' => $themeDir,
            'manifest' => $manifest,
        ];
    }

    /**
     * Delete a theme.
     *
     * @param string $slug
     * @return bool|string True on success, error message on failure
     */
    public function delete($slug)
    {
        if ($slug === 'default') {
            return __('messages.theme_delete_default_forbidden');
        }

        $themeDir = $this->findDirectoryBySlug($slug);
        if (!$themeDir) {
            return __('messages.extension_not_installed');
        }

        $activeThemeSlug = \App\Models\Setting::first()->styles ?? 'default';
        if ($slug === $activeThemeSlug) {
            return __('messages.theme_delete_active_forbidden');
        }

        // Check if any other installed theme depends on this theme as a parent
        $allThemes = $this->getAllThemes();
        foreach ($allThemes as $t) {
            if (!empty($t['parent']) && $t['parent'] === $slug) {
                return __('messages.theme_delete_parent_dependency', [
                    'theme' => $slug,
                    'child' => $t['name'] ?? $t['slug'],
                ]);
            }
        }

        $path = $this->themePath . '/' . $themeDir;
        $deleted = File::deleteDirectory($path);

        // Also delete public assets if present
        $publicPath = public_path("themes/{$slug}");
        if (File::isDirectory($publicPath)) {
            File::deleteDirectory($publicPath);
        }

        \Illuminate\Support\Facades\Cache::forget('theme_updates');
        \App\Helpers\Hooks::do_action('theme_deleted', $slug);

        return $deleted ? true : __('messages.theme_delete_failed');
    }

    /**
     * Check for updates for all themes.
     */
    public function checkForUpdates()
    {
        return Cache::remember('theme_updates', 3600, function () {
            $updates = [];
            $themes = $this->getAllThemes();
            $startTime = microtime(true);
            $maxTimeSeconds = 3.0;

            foreach ($themes as $theme) {
                if ((microtime(true) - $startTime) >= $maxTimeSeconds) {
                    break;
                }

                $updateUrl = $theme['latest_url'] ?? $theme['update_url'] ?? null;
                
                if ($updateUrl && filter_var($updateUrl, FILTER_VALIDATE_URL)) {
                    try {
                        // Handle GitHub Latest Release URL
                        if (preg_match('/github\.com\/([^\/]+)\/([^\/]+)\/releases\/latest/i', $updateUrl, $matches)) {
                            $owner = $matches[1];
                            $repo = $matches[2];
                            $apiUrl = "https://api.github.com/repos/{$owner}/{$repo}/releases/latest";
                            
                            $response = http_secure()
                                           ->withHeaders(['User-Agent' => 'MyAds-Theme-Manager'])
                                           ->connectTimeout(2)
                                           ->timeout(2)
                                           ->get($apiUrl);
                            
                            if ($response->successful()) {
                                $remoteData = $response->json();
                                $remoteVersion = ltrim($remoteData['tag_name'] ?? '', 'v');
                                
                                if (!empty($remoteVersion) && version_compare($remoteVersion, $theme['version'], '>')) {
                                    $updates[$theme['slug']] = [
                                        'new_version' => $remoteVersion,
                                        'download_url' => $remoteData['zipball_url'] ?? '',
                                        'changelog' => $remoteData['body'] ?? '',
                                        'github_url' => $remoteData['html_url'] ?? '',
                                    ];
                                }
                            }
                        } else {
                            // Standard JSON update check
                            $response = http_secure()
                                           ->connectTimeout(2)
                                           ->timeout(2)
                                           ->get($updateUrl);
                            if ($response->successful()) {
                                $remoteData = $response->json();
                                if (isset($remoteData['version']) && version_compare($remoteData['version'], $theme['version'], '>')) {
                                    $updates[$theme['slug']] = [
                                        'new_version' => $remoteData['version'],
                                        'download_url' => $remoteData['download_url'] ?? '',
                                        'changelog' => $remoteData['changelog'] ?? '',
                                    ];
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                        Log::error("Failed to check updates for theme {$theme['slug']}: " . $e->getMessage());
                    }
                }

                $adstnProduct = $theme['ADStn_url'] ?? null;
                if ($adstnProduct) {
                    try {
                        $adstnUrl = filter_var($adstnProduct, FILTER_VALIDATE_URL)
                            ? $adstnProduct
                            : 'https://www.adstn.ovh/api/marketplace/extensions/themes';

                        $remoteSlug = filter_var($adstnProduct, FILTER_VALIDATE_URL)
                            ? $theme['slug']
                            : $adstnProduct;

                        $licenseKeyOption = \App\Models\Option::where('o_type', 'theme_license')
                            ->where('name', $theme['slug'] . '_key')
                            ->first();
                        $licenseKey = $licenseKeyOption ? $licenseKeyOption->o_valuer : '';

                        $response = http_secure()
                                       ->connectTimeout(2)
                                       ->timeout(3)
                                       ->post($adstnUrl, [
                                           'slug'        => $remoteSlug,
                                           'version'     => $theme['version'],
                                           'license_key' => $licenseKey,
                                           'domain'      => request()->getHost(),
                                       ]);

                        if ($response->successful()) {
                            $remoteData = $response->json();
                            $remoteVersion = $remoteData['version'] ?? null;
                            if ($remoteVersion && version_compare($remoteVersion, $theme['version'], '>')) {
                                $updates[$theme['slug']] = [
                                    'new_version' => $remoteVersion,
                                    'download_url' => $remoteData['download_url'] ?? '',
                                    'changelog' => $remoteData['changelog'] ?? '',
                                ];
                            }
                        }
                    } catch (\Throwable $e) {
                        Log::error("Failed to check ADStn updates for theme {$theme['slug']}: " . $e->getMessage());
                    }
                }
            }
            return $updates;
        });
    }

    public function upgrade($slug)
    {
        $directory = $this->findDirectoryBySlug($slug);
        if (!$directory) {
            return __('messages.extension_not_installed');
        }

        $updates = $this->checkForUpdates();
        if (!isset($updates[$slug]) || empty($updates[$slug]['download_url'])) {
            return __('messages.extension_no_update_available');
        }

        $downloadUrl = $updates[$slug]['download_url'];

        // Secure download URL parameters if ADStn theme
        $themeDir = $this->themePath . '/' . $directory;
        $jsonFile = $themeDir . '/theme.json';
        if (File::exists($jsonFile)) {
            $themeData = json_decode(File::get($jsonFile), true);
            if ($themeData && !empty($themeData['ADStn_url'])) {
                $licenseKeyOption = \App\Models\Option::where('o_type', 'theme_license')
                    ->where('name', $slug . '_key')
                    ->first();
                $licenseKey = $licenseKeyOption ? $licenseKeyOption->o_valuer : '';
                $domain = request()->getHost();

                if (!str_contains($downloadUrl, 'license_key=')) {
                    $separator = str_contains($downloadUrl, '?') ? '&' : '?';
                    $downloadUrl .= $separator . http_build_query([
                        'license_key' => $licenseKey,
                        'domain'      => $domain,
                        'slug'        => $slug
                    ]);
                }
            }
        }

        return $this->packageUpgrader->upgradeFromDownload(
            type: 'theme',
            slug: $slug,
            downloadUrl: $downloadUrl,
            extensionsPath: $this->themePath,
            metadataFile: 'theme.json',
            cacheKey: 'theme_updates',
            currentVersion: \App\Http\Controllers\AdminUpdatesController::CURRENT_VERSION,
            existingDirectory: $directory
        );
    }

    public function installFromMarketplace($slug, $downloadUrl)
    {
        return $this->packageUpgrader->upgradeFromDownload(
            type: 'theme',
            slug: $slug,
            downloadUrl: $downloadUrl,
            extensionsPath: $this->themePath,
            metadataFile: 'theme.json',
            cacheKey: 'theme_updates',
            currentVersion: \App\Http\Controllers\AdminUpdatesController::CURRENT_VERSION,
            mustExist: false
        );
    }

    /**
     * Find theme directory name by slug.
     *
     * @param string $slug
     * @return string|null
     */
    public function findDirectoryBySlug($slug)
    {
        $directories = $this->themeDirectories();
        
        foreach ($directories as $directory) {
            $jsonFile = $directory . '/theme.json';
            if (File::exists($jsonFile)) {
                $data = json_decode(File::get($jsonFile), true);
                if ($data && isset($data['slug']) && $data['slug'] === $slug) {
                    return basename($directory);
                }
            }
        }
        
        return null;
    }

    /**
     * @return array<int, string>
     */
    protected function themeDirectories(): array
    {
        return array_values(array_filter(
            File::directories($this->themePath),
            fn (string $directory): bool => ! str_starts_with(basename($directory), '.')
        ));
    }

    /**
     * Check version compatibility against min_myads and max_myads.
     *
     * @param string|null $minVersion
     * @param string|null $maxVersion
     * @param string|null $name
     * @param string|null $currentVersion
     * @return array{is_compatible: bool, status: string, message: string}
     */
    public static function checkCompatibility(?string $minVersion, ?string $maxVersion, ?string $name = null, ?string $currentVersion = null): array
    {
        $current = $currentVersion ?? \App\Support\SystemVersion::CURRENT;
        $isCompatible = true;
        $status = 'compatible';
        $message = __('messages.compatible_with_version', ['version' => $current]);

        if (!empty($minVersion)) {
            $normalizedMin = trim($minVersion);
            if (version_compare($current, $normalizedMin, '<')) {
                $isCompatible = false;
                $status = 'incompatible_min';
                $message = __('messages.theme_requires_newer_myads', [
                    'theme' => $name ?? '',
                    'min' => $minVersion,
                    'current' => $current,
                ]);
            }
        }

        if ($isCompatible && !empty($maxVersion)) {
            $normalizedMax = self::normalizeMaxVersion($maxVersion);
            if (version_compare($current, $normalizedMax, '>')) {
                $isCompatible = false;
                $status = 'incompatible_max';
                $message = __('messages.theme_exceeds_max_myads', [
                    'theme' => $name ?? '',
                    'max' => $maxVersion,
                    'current' => $current,
                ]);
            }
        }

        return [
            'is_compatible' => $isCompatible,
            'status' => $status,
            'message' => $message,
        ];
    }

    /**
     * Normalize max version for comparison (supports e.g. 4.6, 4.6.x, 4.6.* -> 4.6.999).
     */
    public static function normalizeMaxVersion(string $version): string
    {
        $version = trim($version);
        $version = ltrim($version, '^~=v');

        if (preg_match('/^\d+$/', $version)) {
            return $version . '.999.999';
        }
        if (preg_match('/^\d+\.\d+$/', $version)) {
            return $version . '.999';
        }
        if (str_ends_with(strtolower($version), '.x') || str_ends_with($version, '.*')) {
            $base = substr($version, 0, -2);
            if (!str_contains($base, '.')) {
                return $base . '.999.999';
            }
            return $base . '.999';
        }
        return $version;
    }
}
