<?php

namespace App\Http\Middleware;

use App\Services\SecuritySessionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TrackMemberSecuritySession
{
    public function __construct(private readonly SecuritySessionService $sessions)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('install') || $request->is('install/*') || $request->is('up')) {
            return $next($request);
        }

        try {
            if (Auth::check() && $this->sessions->currentSessionIsRevoked($request)) {
                $this->sessions->markLogout($request, Auth::user());
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => __('messages.security_session_revoked'),
                ]);
            }
        } catch (\Throwable) {
            // Silence session tracking errors if DB is not ready
        }

        $response = $next($request);

        try {
            if (Auth::check()) {
                $this->sessions->touchCurrent($request, Auth::user());
            }
        } catch (\Throwable) {
            // Silence session tracking errors if DB is not ready
        }

        return $response;
    }
}
