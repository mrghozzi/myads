<?php

namespace App\Services;

use App\Models\SecurityMemberSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SecuritySessionService
{
    public function __construct(private readonly V420SchemaService $schema)
    {
    }

    public function isSupported(): bool
    {
        return $this->schema->supports('security_sessions');
    }

    public function trackLogin(Request $request, User $user, string $startedVia = 'login'): void
    {
        if (!$this->isSupported()) {
            return;
        }

        $sessionId = (string) $request->session()->getId();
        if ($sessionId === '') {
            return;
        }

        $now = now();
        $payload = [
            'user_id' => (int) $user->getKey(),
            'started_via' => $startedVia,
            'ip_address' => $this->truncate($request->ip(), 45),
            'user_agent' => $this->truncate((string) $request->userAgent(), 1000),
            'started_at' => $now,
            'last_seen_at' => $now,
            'ended_at' => null,
            'revoked_at' => null,
            'revoked_by' => null,
        ];

        try {
            SecurityMemberSession::query()->updateOrCreate(
                ['session_id' => $sessionId],
                $payload
            );
        } catch (\Throwable) {
            return;
        }

        $this->enforceMaxActiveSessions($user, $sessionId);
    }

    public function touchCurrent(Request $request, ?User $user = null): void
    {
        if (!$this->isSupported()) {
            return;
        }

        $user ??= Auth::user();
        if (!$user) {
            return;
        }

        $sessionId = (string) $request->session()->getId();
        if ($sessionId === '') {
            return;
        }

        try {
            $session = SecurityMemberSession::query()
                ->where('session_id', $sessionId)
                ->where('user_id', (int) $user->getKey())
                ->first();

            if (!$session) {
                $this->trackLogin($request, $user, 'web');
                return;
            }

            $session->forceFill([
                'ip_address' => $this->truncate($request->ip(), 45),
                'user_agent' => $this->truncate((string) $request->userAgent(), 1000),
                'last_seen_at' => now(),
            ])->save();
        } catch (\Throwable) {
            return;
        }
    }

    public function currentSessionIsRevoked(Request $request, ?User $user = null): bool
    {
        if (!$this->isSupported()) {
            return false;
        }

        $user ??= Auth::user();
        if (!$user) {
            return false;
        }

        try {
            return SecurityMemberSession::query()
                ->where('session_id', (string) $request->session()->getId())
                ->where('user_id', (int) $user->getKey())
                ->whereNotNull('revoked_at')
                ->exists();
        } catch (\Throwable) {
            return false;
        }
    }

    public function markLogout(Request $request, ?User $user = null): void
    {
        if (!$this->isSupported()) {
            return;
        }

        $user ??= Auth::user();
        $sessionId = (string) $request->session()->getId();
        if ($sessionId === '') {
            return;
        }

        try {
            $query = SecurityMemberSession::query()->where('session_id', $sessionId);
            if ($user) {
                $query->where('user_id', (int) $user->getKey());
            }

            $query->update([
                'ended_at' => now(),
            ]);
        } catch (\Throwable) {
            return;
        }
    }

    public function revoke(SecurityMemberSession $session, ?User $revokedBy = null): void
    {
        if (!$this->isSupported()) {
            return;
        }

        try {
            $session->forceFill([
                'revoked_at' => now(),
                'revoked_by' => $revokedBy?->getKey(),
                'ended_at' => $session->ended_at ?: now(),
            ])->save();
        } catch (\Throwable) {
            return;
        }
    }

    public function revokeOtherActiveSessions(User $user, string $exceptSessionId): void
    {
        if (!$this->isSupported()) {
            return;
        }

        try {
            SecurityMemberSession::query()
                ->active()
                ->where('user_id', (int) $user->getKey())
                ->where('session_id', '!=', $exceptSessionId)
                ->update([
                    'revoked_at' => now(),
                    'ended_at' => DB::raw('COALESCE(ended_at, CURRENT_TIMESTAMP)'),
                ]);
        } catch (\Throwable) {
            return;
        }
    }

    public function enforceMaxActiveSessions(User $user, ?string $exceptSessionId = null): void
    {
        if (!$this->isSupported()) {
            return;
        }

        $maxSessions = (int) \App\Support\SecuritySettings::get('max_active_sessions_per_user', 5);
        if ($maxSessions <= 0) {
            return;
        }

        try {
            $sessions = SecurityMemberSession::query()
                ->active()
                ->where('user_id', (int) $user->getKey())
                ->orderByDesc('last_seen_at')
                ->orderByDesc('started_at')
                ->get();

            if ($sessions->count() <= $maxSessions) {
                return;
            }

            $protectedIds = $exceptSessionId ? [$exceptSessionId] : [];
            $overflow = $sessions
                ->reject(fn (SecurityMemberSession $session) => in_array($session->session_id, $protectedIds, true))
                ->sortBy([
                    ['last_seen_at', 'asc'],
                    ['started_at', 'asc'],
                ])
                ->take(max(0, $sessions->count() - $maxSessions));

            foreach ($overflow as $session) {
                $this->revoke($session);
            }
        } catch (\Throwable) {
            return;
        }
    }

    private function truncate(?string $value, int $length): ?string
    {
        if ($value === null) {
            return null;
        }

        return mb_substr($value, 0, $length);
    }
}
