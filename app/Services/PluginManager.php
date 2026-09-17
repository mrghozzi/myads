<?php

namespace App\Services;

use App\Models\Option;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use ZipArchive;

class PluginManager
{
    protected $pluginPath;
    protected ExtensionPackageUpgrader $packageUpgrader;

    public function __construct(?ExtensionPackageUpgrader $packageUpgrader = null)
    {
        $this->pluginPath = base_path('plugins');
        $this->packageUpgrader = $packageUpgrader ?? app(ExtensionPackageUpgrader::class);
    }

    /**
     * Get all plugins (active and inactive).
     *
     * @return array
     */
    public function getAllPlugins()
    {
        $plugins = [];
        if (!File::exists($this->pluginPath)) {
            File::makeDirectory($this->pluginPath, 0755, true);
        }

        $directories = $this->pluginDirectories();

        foreach ($directories as $directory) {
            $jsonFile = $directory . '/plugin.json';
            if (File::exists($jsonFile)) {
                $pluginData = json_decode(File::get($jsonFile), true);
                if ($pluginData) {
                    $pluginData['directory'] = basename($directory);
                    $pluginData['path'] = $directory;
                    
                    $pluginData['thumbnail'] = $pluginData['thumbnail'] ?? null;
                    $pluginData['latest_url'] = $pluginData['latest'] ?? null;
                    $pluginData['min_myads'] = $pluginData['min_myads'] ?? null;
                    $pluginData['max_myads'] = $pluginData['max_myads'] ?? null;
                    $pluginData['ADStn_url'] = $pluginData['ADStn_url'] ?? null;
                    $pluginData['settings_url'] = $pluginData['settings_url'] ?? $pluginData['settings'] ?? null;
                    $pluginData['admin_menu'] = $pluginData['admin_menu'] ?? null;
                    $pluginData['boot_error'] = Cache::get("plugin_boot_error_{$pluginData['directory']}");
                    
                    $compat = self::checkCompatibility($pluginData['min_myads'], $pluginData['max_myads'], $pluginData['name'] ?? $pluginData['slug'] ?? '');
                    $pluginData['is_compatible'] = $compat['is_compatible'];
                    $pluginData['compatibility_status'] = $compat['status'];
                    $pluginData['compatibility_message'] = $compat['message'];
                    
                    // Check status in DB
                    $option = Option::where('name', $pluginData['slug'])
                                  ->where('o_type', 'plugins')
                                  ->first();
                    
                    $pluginData['is_active'] = $option && $option->o_valuer == 1;
                    $pluginData['installed'] = (bool) $option;

                    $plugins[] = $pluginData;
                }
            }
        }

        return $plugins;
    }

    /**
     * Activate a plugin.
     *
     * @param string $slug
     * @return bool
     */
    public function activate($slug)
    {
        // Verify plugin exists
        $dirName = $this->findDirectoryBySlug($slug);
        if (!$dirName) {
            return false;
        }

        $pluginDir = $this->pluginPath . '/' . $dirName;
        $manifestPath = $pluginDir . '/plugin.json';
        if (!File::exists($manifestPath)) {
            return false;
        }

        $pluginData = json_decode(File::get($manifestPath), true) ?: [];

        // Check min_myads compatibility
        if (!empty($pluginData['min_myads'])) {
            if (version_compare(\App\Support\SystemVersion::CURRENT, $pluginData['min_myads'], '<')) {
                throw new \RuntimeException(__('messages.plugin_requires_newer_myads', [
                    'plugin' => $pluginData['name'] ?? $slug,
                    'min' => $pluginData['min_myads'],
                    'current' => \App\Support\SystemVersion::CURRENT,
                ]));
            }
        }

        // Check max_myads compatibility
        if (!empty($pluginData['max_myads'])) {
            $normalizedMax = self::normalizeMaxVersion($pluginData['max_myads']);
            if (version_compare(\App\Support\SystemVersion::CURRENT, $normalizedMax, '>')) {
                throw new \RuntimeException(__('messages.plugin_exceeds_max_myads', [
                    'plugin' => $pluginData['name'] ?? $slug,
                    'max' => $pluginData['max_myads'],
                    'current' => \App\Support\SystemVersion::CURRENT,
                ]));
            }
        }

        // Check required plugins
        if (!empty($pluginData['requires_plugins']) && is_array($pluginData['requires_plugins'])) {
            foreach ($pluginData['requires_plugins'] as $reqSlug) {
                $reqActive = Option::where('name', $reqSlug)->where('o_type', 'plugins')->where('o_valuer', '1')->exists();
                if (!$reqActive) {
                    throw new \RuntimeException(__('messages.plugin_requires_missing_plugin', [
                        'plugin' => $pluginData['name'] ?? $slug,
                        'required' => $reqSlug,
                    ]));
                }
            }
        }

        // Run migrations if plugin has database/migrations directory
        $this->runMigrations($slug);

        Option::updateOrCreate(
            ['name' => $slug, 'o_type' => 'plugins'],
            ['o_valuer' => 1]
        );

        \Illuminate\Support\Facades\Cache::forget('myads_active_plugin_dirs');
        \Illuminate\Support\Facades\Cache::forget('myads_active_plugin_admin_menus');
        \Illuminate\Support\Facades\Cache::forget("plugin_boot_error_{$dirName}");

        // Execute activate.php if present
        $activateFile = $pluginDir . '/activate.php';
        if (File::exists($activateFile)) {
            try {
                require $activateFile;
            } catch (\Throwable $e) {
                Log::warning("Plugin [{$slug}] activate.php warning: " . $e->getMessage());
            }
        }

        // Fire lifecycle action hook
        \App\Helpers\Hooks::do_action('plugin_activated', $slug, $pluginData);

        return true;
    }

    /**
     * Run migrations for a plugin if it has any.
     *
     * @param string $slug
     * @return bool
     */
    public function runMigrations($slug): bool
    {
        $dirName = $this->findDirectoryBySlug($slug);
        if (!$dirName) {
            return false;
        }

        $migrationsDir = $this->pluginPath . '/' . $dirName . '/database/migrations';
        if (!File::isDirectory($migrationsDir)) {
            return true;
        }

        $relativePath = 'plugins/' . $dirName . '/database/migrations';

        try {
            $exitCode = \Illuminate\Support\Facades\Artisan::call('migrate', [
                '--path' => $relativePath,
                '--force' => true,
            ]);

            return $exitCode === 0;
        } catch (\Throwable $e) {
            Log::error("Failed to run migrations for plugin [{$slug}]: " . $e->getMessage(), [
                'slug' => $slug,
                'directory' => $dirName,
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Deactivate a plugin.
     *
     * @param string $slug
     * @return bool
     */
    public function deactivate($slug)
    {
        $dirName = $this->findDirectoryBySlug($slug);
        $pluginDir = $dirName ? $this->pluginPath . '/' . $dirName : null;

        $option = Option::where('name', $slug)->where('o_type', 'plugins')->first();
        if ($option) {
            $option->update(['o_valuer' => 0]);
            \Illuminate\Support\Facades\Cache::forget('myads_active_plugin_dirs');
            \Illuminate\Support\Facades\Cache::forget('myads_active_plugin_admin_menus');

            // Execute deactivate.php if present
            if ($pluginDir && File::exists($pluginDir . '/deactivate.php')) {
                try {
                    require $pluginDir . '/deactivate.php';
                } catch (\Throwable $e) {
                    Log::warning("Plugin [{$slug}] deactivate.php warning: " . $e->getMessage());
                }
            }

            // Fire lifecycle action hook
            \App\Helpers\Hooks::do_action('plugin_deactivated', $slug);

            return true;
        }
        return false;
    }

    /**
     * Delete a plugin.
     *
     * @param string $slug
     * @return bool
     */
    public function delete($slug)
    {
        $dirName = $this->findDirectoryBySlug($slug);
        if (!$dirName) return false;

        $pluginDir = $this->pluginPath . '/' . $dirName;
        
        // Prevent deletion if active
        $option = \App\Models\Option::where('name', $slug)
                      ->where('o_type', 'plugins')
                      ->first();
        
        if ($option && $option->o_valuer == 1) {
            return __('messages.plugin_delete_active_forbidden');
        }

        // Execute uninstall.php if present (to clean up plugin tables/options)
        $uninstallFile = $pluginDir . '/uninstall.php';
        if (File::exists($uninstallFile)) {
            try {
                require $uninstallFile;
            } catch (\Throwable $e) {
                Log::warning("Plugin [{$slug}] uninstall.php warning: " . $e->getMessage());
            }
        }

        // Fire lifecycle action hook
        \App\Helpers\Hooks::do_action('plugin_deleted', $slug);

        // Remove from DB
        if ($option) {
            $option->delete();
        }

        // Remove files
        \Illuminate\Support\Facades\Cache::forget('myads_active_plugin_admin_menus');
        return File::deleteDirectory($pluginDir);
    }

    /**
     * Install a plugin from ZIP.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return bool|string True on success, error message string on failure
     */
    public function install($file)
    {
        $security = app(\App\Services\Security\FileUploadSecurityService::class);
        $validation = $security->validatePluginArchive($file->getPathname());

        if (!$validation['valid']) {
            $errorKey = $validation['error'] ?? 'messages.plugin_zip_open_failed';
            return __($errorKey);
        }

        $manifestData = $validation['manifest'];
        $slug = $manifestData['slug'];

        // Check min_myads
        if (!empty($manifestData['min_myads'])) {
            if (version_compare(\App\Support\SystemVersion::CURRENT, $manifestData['min_myads'], '<')) {
                return __('messages.plugin_requires_newer_myads', [
                    'plugin' => $manifestData['name'] ?? $slug,
                    'min' => $manifestData['min_myads'],
                    'current' => \App\Support\SystemVersion::CURRENT,
                ]);
            }
        }

        // Check max_myads
        if (!empty($manifestData['max_myads'])) {
            $normalizedMax = self::normalizeMaxVersion($manifestData['max_myads']);
            if (version_compare(\App\Support\SystemVersion::CURRENT, $normalizedMax, '>')) {
                return __('messages.plugin_exceeds_max_myads', [
                    'plugin' => $manifestData['name'] ?? $slug,
                    'max' => $manifestData['max_myads'],
                    'current' => \App\Support\SystemVersion::CURRENT,
                ]);
            }
        }

        $targetPath = $this->pluginPath . '/' . $slug;
        if (File::exists($targetPath)) {
            return __('messages.plugin_already_exists');
        }

        $tempExtractPath = storage_path('app/temp_plugins/' . uniqid());
        try {
            $zip = new ZipArchive;
            if ($zip->open($file->getPathname()) === TRUE) {
                $zip->extractTo($tempExtractPath);
                $zip->close();

                // Find root folder containing plugin.json
                $rootFolder = $tempExtractPath;
                if (!File::exists($tempExtractPath . '/plugin.json')) {
                    $directories = File::directories($tempExtractPath);
                    if (count($directories) === 1 && File::exists($directories[0] . '/plugin.json')) {
                        $rootFolder = $directories[0];
                    }
                }

                File::moveDirectory($rootFolder, $targetPath);
                return true;
            }
            return __('messages.plugin_zip_open_failed');
        } finally {
            if (File::exists($tempExtractPath)) {
                File::deleteDirectory($tempExtractPath);
            }
        }
    }

    /**
     * Check for updates for all plugins.
     * Returns an array of available updates.
     */
    /**
     * Check for updates for all plugins.
     * Returns an array of available updates.
     */
    public function checkForUpdates()
    {
        return Cache::remember('plugin_updates', 3600, function () {
            $updates = [];
            $plugins = $this->getAllPlugins();
            $startTime = microtime(true);
            $maxTimeSeconds = 3.0;

            foreach ($plugins as $plugin) {
                if ((microtime(true) - $startTime) >= $maxTimeSeconds) {
                    break;
                }

                $updateUrl = $plugin['latest_url'] ?? $plugin['update_url'] ?? null;
                $checked = false;
                
                if ($updateUrl && filter_var($updateUrl, FILTER_VALIDATE_URL)) {
                    $checked = true;
                    try {
                        // Handle GitHub Latest Release URL
                        if (preg_match('/github\.com\/([^\/]+)\/([^\/]+)\/releases\/latest/i', $updateUrl, $matches)) {
                            $owner = $matches[1];
                            $repo = $matches[2];
                            $apiUrl = "https://api.github.com/repos/{$owner}/{$repo}/releases/latest";
                            
                            $response = http_secure()
                                           ->withHeaders(['User-Agent' => 'MyAds-Plugin-Manager'])
                                           ->connectTimeout(2)
                                           ->timeout(2)
                                           ->get($apiUrl);
                            
                            if ($response->successful()) {
                                $remoteData = $response->json();
                                $remoteVersion = ltrim($remoteData['tag_name'] ?? '', 'v');
                                
                                if (!empty($remoteVersion) && version_compare($remoteVersion, $plugin['version'], '>')) {
                                    $updates[$plugin['slug']] = [
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
                                if (isset($remoteData['version']) && version_compare($remoteData['version'], $plugin['version'], '>')) {
                                    $updates[$plugin['slug']] = [
                                        'new_version' => $remoteData['version'],
                                        'download_url' => $remoteData['download_url'] ?? '',
                                        'changelog' => $remoteData['changelog'] ?? '',
                                    ];
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                        Log::error("Failed to check updates for plugin {$plugin['slug']}: " . $e->getMessage());
                    }
                }

                if (!$checked) {
                    $adstnProduct = $plugin['ADStn_url'] ?? null;
                    if ($adstnProduct) {
                        try {
                            $adstnUrl = filter_var($adstnProduct, FILTER_VALIDATE_URL)
                                ? $adstnProduct
                                : 'https://www.adstn.ovh/api/marketplace/extensions/plugins';

                            $remoteSlug = filter_var($adstnProduct, FILTER_VALIDATE_URL)
                                ? $plugin['slug']
                                : $adstnProduct;

                            $licenseKeyOption = Option::where('o_type', 'plugin_license')
                                ->where('name', $plugin['slug'] . '_key')
                                ->first();
                            $licenseKey = $licenseKeyOption ? $licenseKeyOption->o_valuer : '';

                            $response = http_secure()
                                           ->connectTimeout(2)
                                           ->timeout(3)
                                           ->post($adstnUrl, [
                                               'slug'        => $remoteSlug,
                                               'version'     => $plugin['version'],
                                               'license_key' => $licenseKey,
                                               'domain'      => request()->getHost(),
                                           ]);

                            if ($response->successful()) {
                                $remoteData = $response->json();
                                $remoteVersion = $remoteData['version'] ?? null;
                                if ($remoteVersion && version_compare($remoteVersion, $plugin['version'], '>')) {
                                    $updates[$plugin['slug']] = [
                                        'new_version' => $remoteVersion,
                                        'download_url' => $remoteData['download_url'] ?? '',
                                        'changelog' => $remoteData['changelog'] ?? '',
                                    ];
                                }
                            }
                        } catch (\Throwable $e) {
                            Log::error("Failed to check ADStn updates for plugin {$plugin['slug']}: " . $e->getMessage());
                        }
                    }
                }
            }
            return $updates;
        });
    }

    /**
     * Upgrade a plugin.
     *
     * @param string $slug
     * @return bool|string
     */
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

        // Secure download URL parameters if ADStn plugin
        $pluginDir = $this->pluginPath . '/' . $directory;
        $jsonFile = $pluginDir . '/plugin.json';
        if (File::exists($jsonFile)) {
            $pluginData = json_decode(File::get($jsonFile), true);
            if ($pluginData && !empty($pluginData['ADStn_url'])) {
                $licenseKeyOption = Option::where('o_type', 'plugin_license')
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

        $result = $this->packageUpgrader->upgradeFromDownload(
            type: 'plugin',
            slug: $slug,
            downloadUrl: $downloadUrl,
            extensionsPath: $this->pluginPath,
            metadataFile: 'plugin.json',
            cacheKey: 'plugin_updates',
            currentVersion: \App\Http\Controllers\AdminUpdatesController::CURRENT_VERSION,
            existingDirectory: $directory
        );

        if ($result === true) {
            $option = Option::where('name', $slug)->where('o_type', 'plugins')->first();
            if ($option && $option->o_valuer == 1) {
                try {
                    $this->runMigrations($slug);
                } catch (\Throwable $e) {
                    Log::error("Failed to run migrations during upgrade for plugin [{$slug}]: " . $e->getMessage());
                }
            }
        }

        return $result;
    }

    /**
     * Install a plugin from a marketplace URL.
     *
     * @param string $slug
     * @param string $downloadUrl
     * @return bool|string
     */
    public function installFromMarketplace($slug, $downloadUrl)
    {
        return $this->packageUpgrader->upgradeFromDownload(
            type: 'plugin',
            slug: $slug,
            downloadUrl: $downloadUrl,
            extensionsPath: $this->pluginPath,
            metadataFile: 'plugin.json',
            cacheKey: 'plugin_updates',
            currentVersion: \App\Http\Controllers\AdminUpdatesController::CURRENT_VERSION,
            mustExist: false
        );
    }

    /**
     * Boot active plugins.

     * Should be called in AppServiceProvider or a dedicated PluginServiceProvider.
     */
    public function boot()
    {
        if (!File::exists($this->pluginPath)) return;

        $activePlugins = Option::where('o_type', 'plugins')
                             ->where('o_valuer', '1')
                             ->pluck('name')
                             ->toArray();

        foreach ($activePlugins as $slug) {
            $dirName = $this->findDirectoryBySlug($slug);
            if ($dirName) {
                $bootFile = $this->pluginPath . '/' . $dirName . '/boot.php';
                if (File::exists($bootFile)) {
                    require_once $bootFile;
                }
                
                // Load routes if exists
                $routesFile = $this->pluginPath . '/' . $dirName . '/routes.php';
                if (File::exists($routesFile)) {
                    require_once $routesFile;
                }
            }
        }
    }

    /**
     * Find directory name by plugin slug (in case directory name differs, though we try to enforce match).
     * Expensive operation if we don't enforce slug=dirname, but safer.
     */
    protected function findDirectoryBySlug($slug)
    {
        // First check if directory exists with slug name
        if (File::exists($this->pluginPath . '/' . $slug . '/plugin.json')) {
            $json = json_decode(File::get($this->pluginPath . '/' . $slug . '/plugin.json'), true);
            if ($json && isset($json['slug']) && $json['slug'] === $slug) {
                return $slug;
            }
        }

        // Fallback: scan all
        $directories = $this->pluginDirectories();
        foreach ($directories as $directory) {
            $jsonFile = $directory . '/plugin.json';
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
    protected function pluginDirectories(): array
    {
        return array_values(array_filter(
            File::directories($this->pluginPath),
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
                $message = __('messages.plugin_requires_newer_myads', [
                    'plugin' => $name ?? '',
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
                $message = __('messages.plugin_exceeds_max_myads', [
                    'plugin' => $name ?? '',
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

    /**
     * Get all admin menus registered by active plugins.
     *
     * @return array
     */
    public static function getActiveAdminMenus(): array
    {
        return Cache::remember('myads_active_plugin_admin_menus', 3600, function () {
            $menus = [];
            $manager = app(self::class);
            $activePlugins = collect($manager->getAllPlugins())->where('is_active', true);

            foreach ($activePlugins as $plugin) {
                if (!empty($plugin['admin_menu']) && is_array($plugin['admin_menu'])) {
                    $menu = $plugin['admin_menu'];
                    $title = $menu['title'] ?? $menu['name'] ?? $plugin['name'];
                    $url = $menu['url'] ?? $plugin['settings_url'] ?? null;
                    $icon = $menu['icon'] ?? 'feather-grid';

                    if ($url) {
                        $menus[] = [
                            'slug' => $plugin['slug'],
                            'title' => $title,
                            'url' => str_starts_with($url, 'http') ? $url : url($url),
                            'icon' => $icon,
                        ];
                    }
                } elseif (!empty($plugin['settings_url'])) {
                    $menus[] = [
                        'slug' => $plugin['slug'],
                        'title' => $plugin['name'],
                        'url' => str_starts_with($plugin['settings_url'], 'http') ? $plugin['settings_url'] : url($plugin['settings_url']),
                        'icon' => $plugin['icon'] ?? 'feather-settings',
                    ];
                }
            }

            return $menus;
        });
    }
}
