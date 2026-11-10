<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

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
        // 1. Register Hook Blade Directives
        Blade::directive('action', function ($expression) {
            return "<?php \\App\Helpers\\Hooks::do_action({$expression}); ?>";
        });

        Blade::directive('filter', function ($expression) {
            return "<?php echo \\App\Helpers\\Hooks::apply_filters({$expression}); ?>";
        });

        // 2. Authentication Lifecycle Hooks
        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Login::class, function ($event) {
            if (function_exists('do_action')) {
                do_action('user_logged_in', $event->user);
            }
        });

        \Illuminate\Support\Facades\Event::listen(\Illuminate\Auth\Events\Logout::class, function ($event) {
            if (function_exists('do_action')) {
                do_action('user_logged_out', $event->user);
            }
        });

        $pluginsPath = base_path('plugins');

        if (!File::isDirectory($pluginsPath)) {
            return;
        }

        $activeDirs = [];

        // 2. Discover active plugins
        if (app()->environment('testing')) {
            $directories = File::directories($pluginsPath);
            foreach ($directories as $dir) {
                $activeDirs[] = basename($dir);
            }
        } else {
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

                // Match active slugs against physical plugin directories via plugin.json
                $dirs = [];
                $directories = File::directories($pluginsPath);
                foreach ($directories as $dir) {
                    $jsonFile = $dir . '/plugin.json';
                    if (File::exists($jsonFile)) {
                        $pluginData = json_decode(File::get($jsonFile), true);
                        $slug = $pluginData['slug'] ?? basename($dir);
                        if (in_array($slug, $activeSlugs, true) || in_array(basename($dir), $activeSlugs, true)) {
                            $dirs[] = basename($dir);
                        }
                    }
                }
                return $dirs;
            });
        }

        // 3. Boot active plugins with PSR-4 Autoloading, View Overrides, and Safe Mode Isolation
        foreach ($activeDirs as $dirName) {
            $pluginDir = $pluginsPath . '/' . $dirName;
            $jsonFile = $pluginDir . '/plugin.json';
            $pluginData = File::exists($jsonFile) ? (json_decode(File::get($jsonFile), true) ?: []) : [];
            $pluginSlug = $pluginData['slug'] ?? $dirName;

            $bootFile = $pluginDir . '/boot.php';
            $helpersFile = $pluginDir . '/helpers.php';
            $routesFile = $pluginDir . '/routes.php';
            $viewsDir = $pluginDir . '/views';
            $overridesDir = $pluginDir . '/views/overrides';
            $langDir = $pluginDir . '/lang';
            $migrationsDir = $pluginDir . '/database/migrations';
            $srcDir = $pluginDir . '/src';

            try {
                // A. Automatic PSR-4 Autoloader
                $this->registerPluginAutoloader($pluginData, $dirName, $srcDir);

                // B. Load Helpers File (if present)
                if (File::exists($helpersFile)) {
                    require_once $helpersFile;
                }

                // C. Load Boot File
                if (File::exists($bootFile)) {
                    require $bootFile;
                }

                // D. Load Routes
                if (File::exists($routesFile)) {
                    $this->loadRoutesFrom($routesFile);
                }

                // E. Dynamic View Overrides (Limitless UI customizability without editing core code)
                if (File::isDirectory($overridesDir)) {
                    $themeOverrides = $overridesDir . '/theme';
                    $adminOverrides = $overridesDir . '/admin';

                    if (File::isDirectory($themeOverrides)) {
                        View::prependNamespace('theme', $themeOverrides);
                    }
                    if (File::isDirectory($adminOverrides)) {
                        View::prependNamespace('admin', $adminOverrides);
                    }
                    View::prependLocation($overridesDir);
                }

                // F. Load Plugin Views (accessible via both $dirName::view and $pluginSlug::view)
                if (File::isDirectory($viewsDir)) {
                    $this->loadViewsFrom($viewsDir, $dirName);
                    if ($pluginSlug !== $dirName) {
                        $this->loadViewsFrom($viewsDir, $pluginSlug);
                    }
                }

                // G. Load Translations (accessible via both $dirName and $pluginSlug)
                if (File::isDirectory($langDir)) {
                    $this->loadTranslationsFrom($langDir, $dirName);
                    if ($pluginSlug !== $dirName) {
                        $this->loadTranslationsFrom($langDir, $pluginSlug);
                    }
                }

                // H. Load Migrations
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
                        \App\Models\Option::where('name', $pluginSlug)
                            ->orWhere('name', $dirName)
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

    /**
     * Register a PSR-4 autoloader for a plugin.
     */
    protected function registerPluginAutoloader(array $pluginData, string $dirName, string $srcDir): void
    {
        if (!File::isDirectory($srcDir)) {
            return;
        }

        // 1. Check if custom PSR-4 mappings are defined in plugin.json
        if (!empty($pluginData['autoload']['psr-4']) && is_array($pluginData['autoload']['psr-4'])) {
            foreach ($pluginData['autoload']['psr-4'] as $prefix => $relativeSrc) {
                $targetPath = base_path('plugins/' . $dirName . '/' . trim($relativeSrc, '/\\'));
                $this->bindAutoloader($prefix, $targetPath);
            }
            return;
        }

        // 2. Automatic convention mapping:
        // StudlyCase of slug or dir: e.g. "myads-football-leagues" -> "MyAds\Plugins\FootballLeagues\" or "Plugins\MyadsFootballLeagues\"
        $studlyName = Str::studly(str_replace(['-', '_'], ' ', $dirName));
        $prefixes = [
            "MyAds\\Plugins\\{$studlyName}\\",
            "Plugins\\{$studlyName}\\",
        ];

        foreach ($prefixes as $prefix) {
            $this->bindAutoloader($prefix, $srcDir);
        }
    }

    /**
     * Bind a PSR-4 namespace prefix to a directory.
     */
    protected function bindAutoloader(string $prefix, string $baseDir): void
    {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, '/\\');

        spl_autoload_register(static function (string $class) use ($prefix, $baseDir): void {
            if (!str_starts_with($class, $prefix)) {
                return;
            }

            $relativeClass = substr($class, strlen($prefix));
            $file = $baseDir . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

            if (is_file($file)) {
                require_once $file;
            }
        });
    }
}
