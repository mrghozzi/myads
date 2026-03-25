<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\SeoSetting;
use App\Models\Setting;
use App\Models\Menu;
use App\Services\RobotsTxtService;
use App\Services\SeoManager;
use App\Services\V420SchemaService;
use App\Support\SeoHeadSanitizer;
use App\Services\PluginManager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SeoHeadSanitizer::class, fn () => new SeoHeadSanitizer());
        $this->app->singleton(SeoManager::class, fn ($app) => new SeoManager($app->make(SeoHeadSanitizer::class)));
        $this->app->singleton(RobotsTxtService::class, fn () => new RobotsTxtService());
        $this->app->singleton(V420SchemaService::class, fn () => new V420SchemaService());
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
        } catch (\Throwable $e) {
            // Fallback to default
        }

        View::share('site_settings', $setting);
        View::share('site_menus', $menus);
        try {
            View::share('seo_settings', SeoSetting::current());
        } catch (\Throwable $e) {
            View::share('seo_settings', new SeoSetting(SeoSetting::defaults()));
        }

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

        View::composer('theme::layouts.master', function ($view) {
            $view->with('seo', app(SeoManager::class)->resolve(request()));
        });

        try {
            app(RobotsTxtService::class)->ensureDefaultFile();
        } catch (\Throwable $e) {
            // Ignore write failures on restricted hosts.
        }
    }
}
