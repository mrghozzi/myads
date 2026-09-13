<?php

namespace App\Http\Middleware;

use App\Models\SecurityIpBan;
use App\Services\V420SchemaService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockBannedIp
{
    public function __construct(private readonly V420SchemaService $schema)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('install') || $request->is('install/*') || $request->is('up')) {
            return $next($request);
        }

        if (!$this->schema->supports('security_ip_bans')) {
            return $next($request);
        }

        $ip = (string) $request->ip();
        if ($ip === '') {
            return $next($request);
        }

        $cacheKey = 'security_ip_banned_' . md5($ip);
        $isBanned = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($ip) {
            try {
                return SecurityIpBan::query()
                    ->where('ip_address', $ip)
                    ->where('is_active', true)
                    ->where(function ($query) {
                        $query->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    })
                    ->exists();
            } catch (\Throwable) {
                return false;
            }
        });

        if (!$isBanned) {
            return $next($request);
        }

        return response()->view('theme::errors.403', [
            'message' => __('messages.security_ip_banned'),
        ], 403);
    }
}
