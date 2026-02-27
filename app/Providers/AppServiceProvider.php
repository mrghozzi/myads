<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use App\Models\Menu;
use App\Services\PluginManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix for shared hosting with 1000-byte max key length (utf8mb4)
        Schema::defaultStringLength(191);

        $theme = 'default';
        $menus = [];
        $setting = null;
        
        try {
            if (Schema::hasTable('setting')) {
                $setting = Setting::first();
                if ($setting && !empty($setting->styles)) {
                    $theme = $setting->styles;
                }
            }

            if (Schema::hasTable('menu')) {
                $menus = Menu::all();
            }
        } catch (\Exception $e) {
            // Fallback to default if DB connection fails or table missing
        }

        View::share('site_settings', $setting);
        View::share('site_menus', $menus);

        // Fetch all available languages
        $langDir = base_path('lang');
        $availableLanguages = [];
        if (\Illuminate\Support\Facades\File::exists($langDir)) {
            $dirs = \Illuminate\Support\Facades\File::directories($langDir);
            foreach ($dirs as $dir) {
                $code = basename($dir);
                $filePath = $dir . '/messages.php';
                if (\Illuminate\Support\Facades\File::exists($filePath)) {
                    try {
                        $content = include $filePath;
                        $availableLanguages[] = (object)[
                            'code' => $code,
                            'name' => $content['language'] ?? strtoupper($code)
                        ];
                    } catch (\Exception $e) {
                        // Skip corrupted language files
                    }
                }
            }
        }
        View::share('available_languages', $availableLanguages);

        View::addNamespace('theme', base_path("themes/$theme/views"));
    }
}
