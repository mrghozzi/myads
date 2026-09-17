<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $pluginsPath = base_path('plugins');

        if (!File::isDirectory($pluginsPath)) {
            return;
        }

        $activeDirs = [];

        // 1. In testing environment: dynamically auto-discover all installed plugins
        if (app()->environment('testing')) {
            $directories = File::directories($pluginsPath);
            foreach ($directories as $dir) {
                $activeDirs[] = basename($dir);
            }
        } else {
            // 2. In normal environment: fetch active plugin slugs from database with caching
            $activeDirs = \Illuminate\Support\Facades\Cache::remember('myads_active_plugin_dirs', 3600, function () use ($pluginsPath) {
                $schema = app()->bound(\App\Services\V420SchemaService::class) ? app(\App\Services\V420SchemaService::class) : null;
                $hasOptions = $schema ? $schema->hasTable('options') : Schema::hasTable('options');
                if (!$hasOptions) {
                    return [];
                }

                $activeSlugs = [];
                try {
                    $activeSlugs = \App\Models\Option::where('o_type', 'plugins')
                                         ->where('o_valuer', '1')
                                         ->pluck('name')
                                         ->toArray();
                } catch (\Throwable $e) {
                    return [];
                }

                if (empty($activeSlugs)) {
                    return [];
                }

                // Dynamically match plugin slugs to their directories via plugin.json
                $dirs = [];
                $directories = File::directories($pluginsPath);
                foreach ($directories as $dir) {
                    $jsonFile = $dir . '/plugin.json';
                    if (File::exists($jsonFile)) {
                        $pluginData = json_decode(File::get($jsonFile), true);
                        if (!empty($pluginData['slug']) && in_array($pluginData['slug'], $activeSlugs, true)) {
                            $dirs[] = basename($dir);
                        }
                    }
                }
                return $dirs;
            });
        }

        // 3. Boot active plugins with Safe Mode Isolation
        foreach ($activeDirs as $dirName) {
            $pluginDir = $pluginsPath . '/' . $dirName;
            $bootFile = $pluginDir . '/boot.php';
            $routesFile = $pluginDir . '/routes.php';
            $viewsDir = $pluginDir . '/views';
            $langDir = $pluginDir . '/lang';
            $migrationsDir = $pluginDir . '/database/migrations';

            try {
                // 1. Load Boot File
                if (File::exists($bootFile)) {
                    require $bootFile;
                }

                // 2. Load Routes
                if (File::exists($routesFile)) {
                    $this->loadRoutesFrom($routesFile);
                }

                // 3. Load Views
                if (File::exists($viewsDir)) {
                    $this->loadViewsFrom($viewsDir, $dirName);
                }

                // 4. Load Translations (Namespaced)
                if (File::isDirectory($langDir)) {
                    $this->loadTranslationsFrom($langDir, $dirName);
                }

                // 5. Load Migrations
                if (File::isDirectory($migrationsDir)) {
                    $this->loadMigrationsFrom($migrationsDir);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::critical("Plugin [{$dirName}] failed during boot lifecycle: " . $e->getMessage(), [
                    'directory' => $dirName,
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);

                // Store crash diagnostic in cache for admin dashboard visibility
                \Illuminate\Support\Facades\Cache::put("plugin_boot_error_{$dirName}", $e->getMessage(), 86400);

                // In non-testing environments, gracefully isolate crashed plugin
                if (!app()->environment('testing')) {
                    try {
                        \App\Models\Option::where('name', $dirName)
                            ->where('o_type', 'plugins')
                            ->update(['o_valuer' => 0]);
                        \Illuminate\Support\Facades\Cache::forget('myads_active_plugin_dirs');
                    } catch (\Throwable $dbEx) {
                        // ignore secondary DB error during fail-safe recovery
                    }
                }
            }
        }
    }
}
