<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UpdateUserOnline
{
    public function handle(Request $request, Closure $next)
    {
        // Skip during installation (DB may not exist yet)
        if ($request->is('install') || $request->is('install/*')) {
            return $next($request);
        }

        try {
            if (Auth::check()) {
                $userId = Auth::id();
                $currentTime = time();
                $user = Auth::user();
                if ($user && (!$user->online || $user->online < ($currentTime - 60))) {
                    User::where('id', $userId)->update(['online' => $currentTime]);
                    $user->online = $currentTime;
                }
            }
        } catch (\Exception $e) {
            // Ignore DB errors (e.g. during fresh installation)
        }

        return $next($request);
    }
}
