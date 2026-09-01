<?php

namespace App\Providers {

    use Illuminate\Support\ServiceProvider;
    use Illuminate\Support\Facades\View;
    use Illuminate\Support\Facades\Config;
    use Illuminate\Support\Facades\Schema;
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

            if ($previewTheme && is_dir(base_path("themes/{$previewTheme}/views"))) {
                $activeTheme = $previewTheme;
            }

            $GLOBALS['MYADS_ACTIVE_THEME'] = $activeTheme;

            $themePath = base_path("themes/{$activeTheme}");
            $viewsPath = "{$themePath}/views";

            // Register the theme's view directory
            if (is_dir($viewsPath)) {
                $this->loadViewsFrom($viewsPath, 'theme');
            }

            // Share theme data globally
            View::share('theme_name', $activeTheme);
            View::share('theme_path', $themePath);
        }
    }
}

namespace {
    // Global Helper for Theme Assets
    if (!function_exists('theme_asset')) {
        function theme_asset($path)
        {
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

            return asset("themes/{$activeTheme}/assets/{$path}");
        }
    }

    // Global Helper for Admin Assets
    if (!function_exists('admin_asset')) {
        function admin_asset($path)
        {
            $adminTheme = 'default';
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('options')) {
                    $adminThemeOpt = \App\Models\Option::where('o_type', 'admin_settings')->where('name', 'theme')->first();
                    if ($adminThemeOpt && !empty($adminThemeOpt->o_valuer)) {
                        $adminTheme = $adminThemeOpt->o_valuer;
                    }
                }
            } catch (\Throwable $e) {
                // Fallback to default
            }

            return url("admin_themes/{$adminTheme}/assets/{$path}");
        }
    }
}

