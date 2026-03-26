<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$envPath = dirname(__DIR__) . '/.env';
$envExamplePath = dirname(__DIR__) . '/.env.example';
$installedPath = dirname(__DIR__) . '/storage/installed';
$envWasCopied = false;

if (! file_exists($envPath) && file_exists($envExamplePath)) {
    copy($envExamplePath, $envPath);
    $envWasCopied = true;
}

if (file_exists($envPath)) {
    $env = file_get_contents($envPath);
    $exampleEnv = file_exists($envExamplePath) ? file_get_contents($envExamplePath) : '';

    preg_match('/^APP_KEY=(.*)$/m', $env, $currentKeyMatch);
    preg_match('/^APP_KEY=(.*)$/m', $exampleEnv, $exampleKeyMatch);

    $currentKey = isset($currentKeyMatch[1]) ? trim($currentKeyMatch[1]) : '';
    $exampleKey = isset($exampleKeyMatch[1]) ? trim($exampleKeyMatch[1]) : '';
    $needsFreshKey = $envWasCopied
        || $currentKey === ''
        || (! file_exists($installedPath) && $exampleKey !== '' && $currentKey === $exampleKey);

    if ($needsFreshKey) {
        $key = 'base64:' . base64_encode(random_bytes(32));

        if (preg_match('/^APP_KEY=.*$/m', $env)) {
            $env = preg_replace('/^APP_KEY=.*$/m', "APP_KEY={$key}", $env);
        } else {
            $env = rtrim($env) . PHP_EOL . "APP_KEY={$key}" . PHP_EOL;
        }

        file_put_contents($envPath, $env);
    }
    
    // Manually load the environment variables early to ensure they are available
    // if automatic discovery in Application::configure fails in this environment
    if (file_exists($envPath)) {
        try {
            $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
            $dotenv->load();
        } catch (\Throwable $e) {
            // Silently ignore if loader fails
        }
    }
}

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
            \App\Http\Middleware\TrackSeoMetrics::class,
        ]);
        
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // Exclude installer routes from CSRF verification
        // (session may not persist during fresh install on some hosting)
        $middleware->validateCsrfTokens(except: [
            'install',
            'install/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Force the application to use our themed error views
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $e) {
            $code = $e->getStatusCode();
            if (view()->exists("theme::errors.{$code}")) {
                return response()->view("theme::errors.{$code}", [], $code);
            }
        });

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
