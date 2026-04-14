<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$cliArgs = $_SERVER['argv'] ?? [];
$requestedEnvironment = $_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: null;

foreach ($cliArgs as $argument) {
    if (is_string($argument) && str_starts_with($argument, '--env=')) {
        $requestedEnvironment = substr($argument, 6);
        break;
    }
}

$isTestingContext = defined('PHPUNIT_COMPOSER_INSTALL')
    || $requestedEnvironment === 'testing'
    || in_array('test', $cliArgs, true);

$envPath = dirname(__DIR__) . '/.env';
$envExamplePath = dirname(__DIR__) . '/.env.example';
$installedPath = dirname(__DIR__) . '/storage/installed';
$envWasCopied = false;

if (! $isTestingContext && ! file_exists($envPath) && file_exists($envExamplePath)) {
    copy($envExamplePath, $envPath);
    $envWasCopied = true;
}

if (! $isTestingContext && file_exists($envPath)) {
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
    // if automatic discovery in Application::configure fails in this environment.
    // Never do this during tests because it can override the isolated testing env.
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
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\BlockBannedIp::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\CheckForMaintenanceMode::class,
            \App\Http\Middleware\UpdateUserOnline::class,
            \App\Http\Middleware\TrackMemberSecuritySession::class,
            \App\Http\Middleware\CheckSystemVersion::class,
            \App\Http\Middleware\TrackSeoMetrics::class,
        ]);
        
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'admin.password.confirm' => \App\Http\Middleware\RequireAdminPasswordConfirmation::class,
        ]);

        // Exclude installer routes from CSRF verification
        // (session may not persist during fresh install on some hosting)
        $middleware->validateCsrfTokens(except: [
            'install',
            'install/*',
            'billing/webhook/*',
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

        $exceptions->render(function (\Throwable $e) {
            if (! \App\Support\DatabaseExceptionClassifier::shouldRenderServiceUnavailable($e)) {
                return null;
            }

            return response()
                ->view('errors.database-unavailable', [], 503)
                ->header('Retry-After', '300');
        });

        // Redirect to installer if APP_KEY is missing (fresh install)
        $exceptions->renderable(function (\Illuminate\Encryption\MissingAppKeyException $e) {
            if ($isTestingContext) {
                throw $e;
            }

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
