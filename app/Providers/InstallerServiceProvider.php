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
        // Use array session driver during installation if APP_KEY is empty
        // This avoids encryption errors when the app hasn't been set up yet
        if (empty(env('APP_KEY')) || env('APP_KEY') === 'base64:dGhpc0lzQURlZmF1bHRLZXlGb3JJbnN0YWxsYXRpb24=') {
            config(['session.driver' => 'array']);
        }

        // Always load installer views
        $this->loadViewsFrom(base_path('installer/views'), 'installer');

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
            ->group(base_path('routes/installer.php'));
    }
}


