<?php

namespace App\Http\Middleware;

use App\Support\SecuritySettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminPasswordConfirmation
{
    public const SESSION_KEY = 'security.admin_password_confirmed_at';

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->isEnabled()) {
            return $next($request);
        }

        if ($request->routeIs('admin.confirm-password.*')) {
            return $next($request);
        }

        $confirmedAt = (int) $request->session()->get(self::SESSION_KEY, 0);
        $ttlSeconds = max(60, (int) SecuritySettings::get('admin_password_confirmation_ttl_minutes', 30) * 60);

        if ($confirmedAt > 0 && (time() - $confirmedAt) < $ttlSeconds) {
            return $next($request);
        }

        $request->session()->put('url.intended', $request->fullUrl());

        return redirect()->route('admin.confirm-password.form');
    }

    public static function markConfirmed(Request $request): void
    {
        $request->session()->put(self::SESSION_KEY, time());
    }

    private function isEnabled(): bool
    {
        return (bool) SecuritySettings::get('admin_password_confirmation_enabled', 0);
    }
}
