<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMobileWebViewToken
{
    /**
     * Handle an incoming request from mobile webviews passing Sanctum bearer tokens.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('web')->check()) {
            $bearerToken = $request->bearerToken();

            if (!empty($bearerToken)) {
                try {
                    $tokenModel = PersonalAccessToken::findToken($bearerToken);

                    if ($tokenModel && $tokenModel->tokenable instanceof User) {
                        // Check if token has expired (if expiration is configured)
                        $expiration = config('sanctum.expiration');
                        $isExpired = $expiration && $tokenModel->created_at->lte(now()->subMinutes($expiration));

                        if (!$isExpired) {
                            $user = $tokenModel->tokenable;
                            Auth::guard('web')->login($user);
                            $request->setUserResolver(fn () => $user);
                        }
                    }
                } catch (\Throwable) {
                    // Fail silently to continue standard web guest pipeline
                }
            }
        }

        return $next($request);
    }
}
