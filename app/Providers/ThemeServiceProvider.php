<?php

namespace App\Providers {

    use Illuminate\Support\ServiceProvider;
    use Illuminate\Support\Facades\View;
    use Illuminate\Support\Facades\Config;

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
            // For now, we hardcode the active theme. Later this will come from DB.
            $activeTheme = 'default'; 
            
            $themePath = base_path("themes/{$activeTheme}");
            $viewsPath = "{$themePath}/views";

            // Register the theme's view directory
            if (is_dir($viewsPath)) {
                $this->loadViewsFrom($viewsPath, 'theme');
                
                // Also allow accessing views without namespace if we want to override default laravel views
                // View::addLocation($viewsPath);
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
            // Assuming 'themes' is in the root and accessible via web
            // and index.php is also in root.
            $activeTheme = 'default'; // Should match the one in boot()
            return url("themes/{$activeTheme}/assets/{$path}");
        }
    }
}
