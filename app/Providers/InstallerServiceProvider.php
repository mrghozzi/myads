<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class InstallerServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        $installerViewsPath = base_path('installer/views');
        $installerRoutesPath = base_path('routes/installer.php');

        // SECURITY: Only register if installer directory and routes exist.
        // On production installed systems where installer/ is deleted for security,
        // this gracefully skips registration and prevents fatal missing stream errors.
        if (!file_exists($installerViewsPath) || !file_exists($installerRoutesPath)) {
            return;
        }

        // Always load installer views
        $this->loadViewsFrom($installerViewsPath, 'installer');

        // Middleware to exclude from installer routes (they require DB tables)
        $excludeMiddleware = [
            \App\Http\Middleware\UpdateUserOnline::class,
            \App\Http\Middleware\CheckSystemVersion::class,
        ];

        // Always load all installer routes.
        // InstallerGuard middleware handles access control (blocks fresh install
        // routes after installation, but always allows /install/update).
        Route::middleware('web')
            ->withoutMiddleware($excludeMiddleware)
            ->group($installerRoutesPath);
    }
}


