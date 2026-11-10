<?php

namespace App\Providers {

    use Illuminate\Support\ServiceProvider;
    use Illuminate\Support\Facades\View;
    use Illuminate\Support\Facades\Config;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Support\Facades\File;
    use App\Models\Setting;

    class ThemeServiceProvider extends ServiceProvider
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
            $activeTheme = 'default';

            try {
                if (Schema::hasTable('setting')) {
                    $setting = Setting::first();
                    if ($setting && !empty($setting->styles)) {
                        $activeTheme = $setting->styles;
                    }
                }
            } catch (\Throwable $e) {
                // Fallback to default
            }

            // Support live previewing other themes when requested via query, cookie, or container request
            $previewTheme = self::resolvePreviewTheme();

            if ($previewTheme) {
                $activeTheme = $previewTheme;
            }

            // Safe fallback if active theme directory is missing
            if (!is_dir(base_path("themes/{$activeTheme}")) && is_dir(base_path("themes/default"))) {
                $activeTheme = 'default';
            }

            self::applyTheme($activeTheme);
        }

        /**
         * Resolve any requested preview theme from query, cookie, or container request.
         */
        public static function resolvePreviewTheme(): ?string
        {
            $candidate = null;

            if (isset($_GET['preview_theme']) && is_string($_GET['preview_theme'])) {
                $candidate = trim($_GET['preview_theme']);
            } elseif (isset($_GET['theme_preview']) && isset($_GET['theme']) && is_string($_GET['theme'])) {
                $candidate = trim($_GET['theme']);
            } elseif (isset($_REQUEST['preview_theme']) && is_string($_REQUEST['preview_theme'])) {
                $candidate = trim($_REQUEST['preview_theme']);
            } elseif (isset($_COOKIE['preview_theme']) && is_string($_COOKIE['preview_theme'])) {
                $candidate = trim($_COOKIE['preview_theme']);
            }

            if (!$candidate) {
                try {
                    if (app()->bound('request')) {
                        $req = request();
                        if ($req->has('preview_theme')) {
                            $candidate = (string) $req->query('preview_theme', $req->input('preview_theme'));
                        } elseif ($req->has('theme_preview') && $req->has('theme')) {
                            $candidate = (string) $req->query('theme', $req->input('theme'));
                        } elseif ($req->cookie('preview_theme')) {
                            $candidate = (string) $req->cookie('preview_theme');
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore container resolution edge cases
                }
            }

            if ($candidate && preg_match('/^[a-z0-9\-_]+$/i', $candidate)) {
                $clean = strtolower($candidate);
                if (is_dir(base_path("themes/{$clean}")) && File::exists(base_path("themes/{$clean}/theme.json"))) {
                    return $clean;
                }
            }

            return null;
        }

        /**
         * Apply and bind a theme dynamically to Laravel's View finder and globals.
         */
        public static function applyTheme(string $activeTheme): void
        {
            $cleanTheme = trim($activeTheme);
            $themePath = base_path("themes/{$cleanTheme}");

            if (!is_dir($themePath) || !File::exists("{$themePath}/theme.json")) {
                $cleanTheme = 'default';
                $themePath = base_path("themes/default");
            }

            $GLOBALS['MYADS_ACTIVE_THEME'] = $cleanTheme;

            $viewsPath = "{$themePath}/views";
            $manifestFile = "{$themePath}/theme.json";
            $themeData = File::exists($manifestFile) ? (json_decode(File::get($manifestFile), true) ?: []) : [];

            // Parent/Child Theme Inheritance
            $parentTheme = null;
            if (!empty($themeData['parent']) && is_string($themeData['parent']) && $themeData['parent'] !== $cleanTheme) {
                $candidateParent = trim($themeData['parent']);
                if (is_dir(base_path("themes/{$candidateParent}"))) {
                    $parentTheme = $candidateParent;
                }
            }
            $GLOBALS['MYADS_PARENT_THEME'] = $parentTheme;

            // Build hierarchical view search paths (Child > Parent > Default)
            $themePaths = [];

            // 1. Active (Child/Custom) theme views (Priority 1)
            if (is_dir($viewsPath)) {
                $themePaths[] = $viewsPath;
            }

            // 2. Parent theme views (Priority 2)
            if (!empty($parentTheme) && is_dir(base_path("themes/{$parentTheme}/views"))) {
                $themePaths[] = base_path("themes/{$parentTheme}/views");
            }

            // 3. Default theme fallback (Priority 3)
            if ($cleanTheme !== 'default' && $parentTheme !== 'default' && is_dir(base_path("themes/default/views"))) {
                $themePaths[] = base_path("themes/default/views");
            }

            View::replaceNamespace('theme', $themePaths);

            // Preserve top precedence for any active plugins that registered view overrides for 'theme'
            $pluginsDir = base_path('plugins');
            if (File::isDirectory($pluginsDir)) {
                $activePluginDirs = \Illuminate\Support\Facades\Cache::get('myads_active_plugin_dirs', []);
                foreach ($activePluginDirs as $pDir) {
                    $override = $pluginsDir . '/' . $pDir . '/views/overrides/theme';
                    if (File::isDirectory($override)) {
                        View::prependNamespace('theme', $override);
                    }
                }
            }

            // Share theme data globally
            View::share('theme_name', $cleanTheme);
            View::share('theme_path', $themePath);
            View::share('parent_theme', $parentTheme);
            View::share('theme_manifest', $themeData);
        }
    }
}

namespace {
    // Global Helper for Admin Assets
    if (!function_exists('admin_asset')) {
        function admin_asset($path)
        {
            static $cachedAdminTheme = null;

            if ($cachedAdminTheme === null) {
                $adminTheme = 'default';
                try {
                    $schema = app()->bound(App\Services\V420SchemaService::class)
                        ? app(App\Services\V420SchemaService::class)
                        : null;

                    $hasOptionsTable = $schema ? $schema->hasTable('options') : \Illuminate\Support\Facades\Schema::hasTable('options');

                    if ($hasOptionsTable) {
                        $adminThemeOpt = \App\Models\Option::where('o_type', 'admin_settings')->where('name', 'theme')->first();
                        if ($adminThemeOpt && !empty($adminThemeOpt->o_valuer)) {
                            $adminTheme = $adminThemeOpt->o_valuer;
                        }
                    }
                } catch (\Throwable $e) {
                    // Fallback to default
                }
                $cachedAdminTheme = $adminTheme;
            }

            return url("admin_themes/{$cachedAdminTheme}/assets/{$path}");
        }
    }
}
