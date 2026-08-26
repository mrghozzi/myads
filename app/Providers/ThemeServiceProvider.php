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

            // Support live previewing other themes when requested
            if (request()->has('preview_theme') && is_dir(base_path('themes/' . request()->query('preview_theme') . '/views'))) {
                $activeTheme = (string) request()->query('preview_theme');
            } elseif (request()->has('theme_preview') && request()->has('theme') && is_dir(base_path('themes/' . request()->query('theme') . '/views'))) {
                $activeTheme = (string) request()->query('theme');
            }

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
            $activeTheme = 'default';

            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('setting')) {
                    $setting = \App\Models\Setting::first();
                    if ($setting && !empty($setting->styles)) {
                        $activeTheme = $setting->styles;
                    }
                }
            } catch (\Throwable $e) {
                // Fallback to default
            }

            if (request()->has('preview_theme') && is_dir(base_path('themes/' . request()->query('preview_theme')))) {
                $activeTheme = (string) request()->query('preview_theme');
            } elseif (request()->has('theme_preview') && request()->has('theme') && is_dir(base_path('themes/' . request()->query('theme')))) {
                $activeTheme = (string) request()->query('theme');
            }

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

