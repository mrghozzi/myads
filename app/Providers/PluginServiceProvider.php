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
            // 2. In normal environment: fetch active plugin slugs from database
            $activeSlugs = [];
            try {
                if (Schema::hasTable('options')) {
                    $activeSlugs = \App\Models\Option::where('o_type', 'plugins')
                                         ->where('o_valuer', '1')
                                         ->pluck('name')
                                         ->toArray();
                }
            } catch (\Throwable $e) {
                // Ignore DB connection errors during early bootstrap
            }

            if (empty($activeSlugs)) {
                return;
            }

            // Dynamically match plugin slugs to their directories via plugin.json
            $directories = File::directories($pluginsPath);
            foreach ($directories as $dir) {
                $jsonFile = $dir . '/plugin.json';
                if (File::exists($jsonFile)) {
                    $pluginData = json_decode(File::get($jsonFile), true);
                    if (!empty($pluginData['slug']) && in_array($pluginData['slug'], $activeSlugs, true)) {
                        $activeDirs[] = basename($dir);
                    }
                }
            }
        }

        // 3. Boot active plugins
        foreach ($activeDirs as $dirName) {
            $pluginDir = $pluginsPath . '/' . $dirName;
            $bootFile = $pluginDir . '/boot.php';
            $routesFile = $pluginDir . '/routes.php';
            $viewsDir = $pluginDir . '/views';
            $migrationsDir = $pluginDir . '/database/migrations';

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

            // 4. Load Migrations
            if (File::exists($migrationsDir)) {
                $this->loadMigrationsFrom($migrationsDir);
            }
        }
    }
}
