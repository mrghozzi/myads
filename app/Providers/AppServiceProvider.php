<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\SeoSetting;
use App\Models\Setting;
use App\Services\Contracts\UrlSafetyInspectorInterface;
use App\Services\LocalUrlSafetyInspector;
use App\Services\RobotsTxtService;
use App\Services\SecurityPolicyService;
use App\Services\SecuritySessionService;
use App\Services\SecurityThrottleService;
use App\Services\SeoManager;
use App\Services\V420SchemaService;
use App\Support\SeoHeadSanitizer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        $this->app->singleton(UrlSafetyInspectorInterface::class, fn () => new LocalUrlSafetyInspector());
        $this->app->singleton(SecurityPolicyService::class, fn ($app) => new SecurityPolicyService(
            $app->make(UrlSafetyInspectorInterface::class)
        ));
        $this->app->singleton(SecurityThrottleService::class, fn () => new SecurityThrottleService());
        $this->app->singleton(SecuritySessionService::class, fn ($app) => new SecuritySessionService(
            $app->make(V420SchemaService::class)
        ));
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
    }
}
