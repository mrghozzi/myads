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

            // Support live previewing other themes when requested via query, request, or $_GET
            $previewTheme = null;
            if (isset($_GET['preview_theme']) && is_string($_GET['preview_theme'])) {
                $previewTheme = trim($_GET['preview_theme']);
            } elseif (isset($_GET['theme_preview']) && isset($_GET['theme']) && is_string($_GET['theme'])) {
                $previewTheme = trim($_GET['theme']);
            } elseif (isset($_REQUEST['preview_theme']) && is_string($_REQUEST['preview_theme'])) {
                $previewTheme = trim($_REQUEST['preview_theme']);
            }

            if (!$previewTheme) {
                try {
                    if ($this->app->bound('request')) {
                        $req = $this->app->make('request');
                        if ($req && $req->has('preview_theme')) {
                            $previewTheme = (string) $req->query('preview_theme', $req->input('preview_theme'));
                        } elseif ($req && $req->has('theme_preview') && $req->has('theme')) {
                            $previewTheme = (string) $req->query('theme', $req->input('theme'));
                        }
                    }
                } catch (\Throwable $e) {
                    // Ignore container resolution edge cases
                }
            }

            // Validate requested preview theme
            if ($previewTheme && preg_match('/^[a-z0-9\-_]+$/i', $previewTheme) && is_dir(base_path("themes/{$previewTheme}/views"))) {
                $activeTheme = $previewTheme;
            }

            // Safe fallback if active theme directory is missing
            if (!is_dir(base_path("themes/{$activeTheme}/views")) && is_dir(base_path("themes/default/views"))) {
                $activeTheme = 'default';
            }

            $GLOBALS['MYADS_ACTIVE_THEME'] = $activeTheme;

            $themePath = base_path("themes/{$activeTheme}");
            $viewsPath = "{$themePath}/views";
            $manifestFile = "{$themePath}/theme.json";
            $themeData = File::exists($manifestFile) ? (json_decode(File::get($manifestFile), true) ?: []) : [];

            // Parent/Child Theme Inheritance
            $parentTheme = null;
            if (!empty($themeData['parent']) && is_string($themeData['parent']) && $themeData['parent'] !== $activeTheme) {
                $candidateParent = trim($themeData['parent']);
                if (is_dir(base_path("themes/{$candidateParent}/views"))) {
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
            if ($activeTheme !== 'default' && $parentTheme !== 'default' && is_dir(base_path("themes/default/views"))) {
                $themePaths[] = base_path("themes/default/views");
            }

            View::replaceNamespace('theme', $themePaths);

            // Share theme data globally
            View::share('theme_name', $activeTheme);
            View::share('theme_path', $themePath);
            View::share('parent_theme', $parentTheme);
            View::share('theme_manifest', $themeData);
        }
    }
}

namespace {
    // Global Helper for Theme Assets with Child/Parent inheritance
    if (!function_exists('theme_asset')) {
        function theme_asset($path)
        {
            $cleanPath = ltrim((string) $path, '/\\');
            $activeTheme = $GLOBALS['MYADS_ACTIVE_THEME'] ?? null;

            if (!$activeTheme) {
                try {
                    if (isset($_GET['preview_theme']) && is_dir(base_path('themes/' . $_GET['preview_theme']))) {
                        $activeTheme = (string) $_GET['preview_theme'];
                    } elseif (\Illuminate\Support\Facades\Schema::hasTable('setting')) {
                        $setting = \App\Models\Setting::first();
                        if ($setting && !empty($setting->styles)) {
                            $activeTheme = $setting->styles;
                        }
                    }
                } catch (\Throwable $e) {
                    // Fallback to default
                }
            }

            $activeTheme = $activeTheme ?: 'default';
            $parentTheme = $GLOBALS['MYADS_PARENT_THEME'] ?? null;

            // Check if active theme has the asset physically in public/themes/{activeTheme}/assets/ or themes/{activeTheme}/assets/
            if (file_exists(public_path("themes/{$activeTheme}/assets/{$cleanPath}")) || file_exists(base_path("themes/{$activeTheme}/assets/{$cleanPath}"))) {
                return asset("themes/{$activeTheme}/assets/{$cleanPath}");
            }

            // Check parent theme asset if defined
            if (!empty($parentTheme)) {
                if (file_exists(public_path("themes/{$parentTheme}/assets/{$cleanPath}")) || file_exists(base_path("themes/{$parentTheme}/assets/{$cleanPath}"))) {
                    return asset("themes/{$parentTheme}/assets/{$cleanPath}");
                }
            }

            // Check default theme asset as ultimate fallback
            if ($activeTheme !== 'default' && $parentTheme !== 'default') {
                if (file_exists(public_path("themes/default/assets/{$cleanPath}")) || file_exists(base_path("themes/default/assets/{$cleanPath}"))) {
                    return asset("themes/default/assets/{$cleanPath}");
                }
            }

            return asset("themes/{$activeTheme}/assets/{$cleanPath}");
        }
    }

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
