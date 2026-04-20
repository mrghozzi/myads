<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

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
                    
                    // Determine thumbnail filename
                    if (!isset($themeData['thumbnail'])) {
                        $screenshotPath = $directory . '/screenshot.png';
                        if (File::exists($screenshotPath)) {
                            $themeData['thumbnail'] = 'screenshot.png';
                        }
                    }

                    if (isset($themeData['thumbnail']) && File::exists($directory . '/' . $themeData['thumbnail'])) {
                        $themeData['screenshot'] = route('admin.themes.thumbnail', $themeData['slug']);
                    } else {
                        $themeData['screenshot'] = null;
                    }

                    $themeData['is_active'] = ($themeData['slug'] === $activeThemeSlug);
                    
                    $themes[] = $themeData;
                }
            }
        }

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
     */
    public function activate($slug)
    {
        // Find theme directory by slug
        $themeDir = $this->findDirectoryBySlug($slug);
        
        if (!$themeDir) {
            return false;
        }

        $setting = \App\Models\Setting::first();
        if ($setting) {
            $setting->update(['styles' => $slug]);
            return true;
        }

        return false;
    }

    /**
     * Check for updates for all themes.
     */
    public function checkForUpdates()
    {
        return Cache::remember('theme_updates', 3600, function () {
            $updates = [];
            $themes = $this->getAllThemes();

            foreach ($themes as $theme) {
                $updateUrl = $theme['latest_url'] ?? $theme['update_url'] ?? null;
                
                if ($updateUrl && filter_var($updateUrl, FILTER_VALIDATE_URL)) {
                    try {
                        // Handle GitHub Latest Release URL
                        if (preg_match('/github\.com\/([^\/]+)\/([^\/]+)\/releases\/latest/i', $updateUrl, $matches)) {
                            $owner = $matches[1];
                            $repo = $matches[2];
                            $apiUrl = "https://api.github.com/repos/{$owner}/{$repo}/releases/latest";
                            
                            $response = Http::withHeaders(['User-Agent' => 'MyAds-Theme-Manager'])
                                           ->timeout(5)
                                           ->get($apiUrl);
                            
                            if ($response->successful()) {
                                $remoteData = $response->json();
                                $remoteVersion = ltrim($remoteData['tag_name'], 'v');
                                
                                if (version_compare($remoteVersion, $theme['version'], '>')) {
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
                            $response = Http::timeout(5)->get($updateUrl);
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
                    } catch (\Exception $e) {
                        Log::error("Failed to check updates for theme {$theme['slug']}: " . $e->getMessage());
                    }
                }
            }
            return $updates;
        });
    }

    public function delete($slug)
    {
        $themeDir = $this->findDirectoryBySlug($slug);
        if (!$themeDir) return false;

        $activeThemeSlug = \App\Models\Setting::first()->styles ?? 'default';
        if ($slug === $activeThemeSlug) {
            return "Cannot delete the active theme. Please switch to another theme first.";
        }

        $path = $this->themePath . '/' . $themeDir;
        return File::deleteDirectory($path);
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

        return $this->packageUpgrader->upgradeFromDownload(
            type: 'theme',
            slug: $slug,
            downloadUrl: $updates[$slug]['download_url'],
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
    protected function findDirectoryBySlug($slug)
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
}
