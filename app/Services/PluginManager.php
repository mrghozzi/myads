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
        $pluginDir = $this->pluginPath . '/' . $this->findDirectoryBySlug($slug);
        if (!File::exists($pluginDir . '/plugin.json')) {
            return false;
        }

        Option::updateOrCreate(
            ['name' => $slug, 'o_type' => 'plugins'],
            ['o_valuer' => 1]
        );

        return true;
    }

    /**
     * Deactivate a plugin.
     *
     * @param string $slug
     * @return bool
     */
    public function deactivate($slug)
    {
        $option = Option::where('name', $slug)->where('o_type', 'plugins')->first();
        if ($option) {
            $option->update(['o_valuer' => 0]);
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

        // Remove from DB
        if ($option) {
            $option->delete();
        }

        // Remove files
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
        $zip = new ZipArchive;
        if ($zip->open($file->getPathname()) === TRUE) {
            // Extract to temp folder to check structure
            $tempExtractPath = storage_path('app/temp_plugins/' . uniqid());
            $zip->extractTo($tempExtractPath);
            $zip->close();

            // Find plugin.json
            $pluginJsonPath = null;
            $rootFolder = null;

            // Check if plugin.json is in root or subfolder
            if (File::exists($tempExtractPath . '/plugin.json')) {
                $pluginJsonPath = $tempExtractPath . '/plugin.json';
                $rootFolder = $tempExtractPath;
            } else {
                $directories = File::directories($tempExtractPath);
                if (count($directories) === 1 && File::exists($directories[0] . '/plugin.json')) {
                    $pluginJsonPath = $directories[0] . '/plugin.json';
                    $rootFolder = $directories[0];
                }
            }

            if (!$pluginJsonPath) {
                File::deleteDirectory($tempExtractPath);
                return __('messages.plugin_zip_structure_invalid');
            }

            $pluginData = json_decode(File::get($pluginJsonPath), true);
            if (!$pluginData || !isset($pluginData['slug'])) {
                File::deleteDirectory($tempExtractPath);
                return __('messages.plugin_manifest_slug_required');
            }

            // Move to plugins directory
            $targetPath = $this->pluginPath . '/' . $pluginData['slug']; // Use slug as directory name for consistency
            
            if (File::exists($targetPath)) {
                File::deleteDirectory($tempExtractPath);
                return __('messages.plugin_already_exists');
            }

            File::moveDirectory($rootFolder, $targetPath);
            File::deleteDirectory($tempExtractPath); // Clean up temp root if it was a subfolder move

            return true;
        }
        return __('messages.plugin_zip_open_failed');
    }

    /**
     * Check for updates for all plugins.
     * Returns an array of available updates.
     */
    public function checkForUpdates()
    {
        return Cache::remember('plugin_updates', 3600, function () {
            $updates = [];
            $plugins = $this->getAllPlugins();

            foreach ($plugins as $plugin) {
                $updateUrl = $plugin['latest_url'] ?? $plugin['update_url'] ?? null;
                
                if ($updateUrl && filter_var($updateUrl, FILTER_VALIDATE_URL)) {
                    try {
                        // Handle GitHub Latest Release URL
                        if (preg_match('/github\.com\/([^\/]+)\/([^\/]+)\/releases\/latest/i', $updateUrl, $matches)) {
                            $owner = $matches[1];
                            $repo = $matches[2];
                            $apiUrl = "https://api.github.com/repos/{$owner}/{$repo}/releases/latest";
                            
                            $response = Http::withHeaders(['User-Agent' => 'MyAds-Plugin-Manager'])
                                           ->timeout(5)
                                           ->get($apiUrl);
                            
                            if ($response->successful()) {
                                $remoteData = $response->json();
                                $remoteVersion = ltrim($remoteData['tag_name'], 'v');
                                
                                if (version_compare($remoteVersion, $plugin['version'], '>')) {
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
                            $response = Http::timeout(5)->get($updateUrl);
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
                    } catch (\Exception $e) {
                        Log::error("Failed to check updates for plugin {$plugin['slug']}: " . $e->getMessage());
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

        return $this->packageUpgrader->upgradeFromDownload(
            type: 'plugin',
            slug: $slug,
            downloadUrl: $updates[$slug]['download_url'],
            extensionsPath: $this->pluginPath,
            metadataFile: 'plugin.json',
            cacheKey: 'plugin_updates',
            currentVersion: \App\Http\Controllers\AdminUpdatesController::CURRENT_VERSION,
            existingDirectory: $directory
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
}
