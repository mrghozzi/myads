<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->hasTwoFactorEnabled()) {
            if (!session()->has('auth.2fa_verified')) {
                if (!$request->is('two-factor-challenge', 'two-factor-challenge/*', 'logout')) {
                    // Mark as pending if not already
                    if (!session()->has('auth.2fa_pending')) {
                        session(['auth.2fa_pending' => true]);
                        
                        // Send code if email type
                        if ($user->twoFactorType() === 'email' && !session()->has('auth.2fa_code')) {
                            app(\App\Http\Controllers\Auth\TwoFactorController::class)->sendEmailCode($user);
                        }
                    }
                    
                    return redirect()->route('two-factor.challenge');
                }
            }
        }

        return $next($request);
    }
}
