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
        // Define plugins path
        $pluginsPath = base_path('plugins');

        if (!File::exists($pluginsPath)) {
            return;
        }

        try {
            if (!Schema::hasTable('options')) {
                return;
            }
            
            // Get active plugins from DB
            $activePlugins = \App\Models\Option::where('o_type', 'plugins')
                                 ->where('o_valuer', '1')
                                 ->pluck('name')
                                 ->toArray();
                                 
            if (empty($activePlugins)) {
                return;
            }
            
            // Resolve plugin directories
            $pluginManager = new \App\Services\PluginManager();
            $plugins = $pluginManager->getAllPlugins();
            $activeDirs = [];
            
            foreach ($plugins as $plugin) {
                if ($plugin['is_active']) {
                    $activeDirs[] = $plugin['directory'];
                }
            }
        } catch (\Exception $e) {
            return;
        }

        foreach ($activeDirs as $dirName) {
            $pluginDir = $pluginsPath . '/' . $dirName;
            $bootFile = $pluginDir . '/boot.php';
            $routesFile = $pluginDir . '/routes.php';
            $viewsDir = $pluginDir . '/views';
            $migrationsDir = $pluginDir . '/database/migrations';

            // 1. Load Boot File
            if (File::exists($bootFile)) {
                require_once $bootFile;
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
