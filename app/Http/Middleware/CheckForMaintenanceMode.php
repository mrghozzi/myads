<?php

namespace App\Http\Middleware;

use App\Services\MaintenanceModeManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class CheckForMaintenanceMode
{
    public function __construct(
        private readonly MaintenanceModeManager $maintenanceMode
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $hasEmergencyAccess = $this->maintenanceMode->emergencyAccessAllowed($request);

        if ($hasEmergencyAccess) {
            $cookieValue = $this->maintenanceMode->emergencyAccessCookieValue();
            if ($cookieValue !== null && (string) $request->cookie(MaintenanceModeManager::BYPASS_COOKIE, '') !== $cookieValue) {
                Cookie::queue(
                    MaintenanceModeManager::BYPASS_COOKIE,
                    $cookieValue,
                    480,
                    null,
                    null,
                    $request->isSecure(),
                    true,
                    false,
                    'lax'
                );
            }
        }

        if (! $this->maintenanceMode->isEnabled()) {
            return $next($request);
        }

        if ($hasEmergencyAccess || $this->shouldAllowRequest($request)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('messages.maintenance_page_title'),
                'details' => __('messages.maintenance_default_message'),
            ], 503, [
                'Retry-After' => $this->maintenanceMode->retryAfter(),
            ]);
        }

        return response()
            ->view('theme::errors.503', [
                'maintenanceSettings' => $this->maintenanceMode->settings(),
            ], 503)
            ->header('Retry-After', $this->maintenanceMode->retryAfter());
    }

    private function shouldAllowRequest(Request $request): bool
    {
        if ($request->user()?->hasAdminAccess()) {
            return true;
        }

        return $request->routeIs(
            'login',
            'login.post',
            'logout',
            'password.*',
            'social.*',
            'captcha.generate'
        ) || $request->is('install', 'install/*');
    }
}
