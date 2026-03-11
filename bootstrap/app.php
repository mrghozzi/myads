<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\UpdateUserOnline::class,
            \App\Http\Middleware\CheckSystemVersion::class,
        ]);
        
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Redirect to installer if APP_KEY is missing (fresh install)
        $exceptions->renderable(function (\Illuminate\Encryption\MissingAppKeyException $e) {
            // Auto-generate APP_KEY and redirect to installer
            $envPath = base_path('.env');
            if (!file_exists($envPath) && file_exists(base_path('.env.example'))) {
                copy(base_path('.env.example'), $envPath);
            }
            if (file_exists($envPath)) {
                $env = file_get_contents($envPath);
                $key = 'base64:' . base64_encode(random_bytes(32));
                if (preg_match('/^APP_KEY=$/m', $env)) {
                    $env = preg_replace('/^APP_KEY=$/m', "APP_KEY={$key}", $env);
                } elseif (preg_match('/^APP_KEY=.*$/m', $env)) {
                    // Key exists but might be invalid, replace it
                    $env = preg_replace('/^APP_KEY=.*$/m', "APP_KEY={$key}", $env);
                } else {
                    $env .= "\nAPP_KEY={$key}";
                }
                file_put_contents($envPath, $env);
            }
            return redirect('/install');
        });
    })->create();
