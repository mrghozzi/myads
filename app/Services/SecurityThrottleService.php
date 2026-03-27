<?php

namespace App\Services;

use App\Models\User;
use App\Support\SecuritySettings;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class SecurityThrottleService
{
    public function remainingActionCooldown(User|int $user, string $action): int
    {
        $seconds = $this->cooldownSeconds($action);
        if ($seconds <= 0) {
            return 0;
        }

        $userId = $user instanceof User ? (int) $user->getKey() : (int) $user;
        if ($userId <= 0) {
            return 0;
        }

        $key = $this->actionKey($userId, $action);

        return RateLimiter::tooManyAttempts($key, 1)
            ? RateLimiter::availableIn($key)
            : 0;
    }

    public function hitAction(User|int $user, string $action): void
    {
        $seconds = $this->cooldownSeconds($action);
        if ($seconds <= 0) {
            return;
        }

        $userId = $user instanceof User ? (int) $user->getKey() : (int) $user;
        if ($userId <= 0) {
            return;
        }

        RateLimiter::hit($this->actionKey($userId, $action), $seconds);
    }

    public function actionMessage(User|int $user, string $action): ?string
    {
        $remaining = $this->remainingActionCooldown($user, $action);
        if ($remaining <= 0) {
            return null;
        }

        return __('messages.security_action_cooldown', [
            'seconds' => $remaining,
        ]);
    }

    public function tooManyLoginAttempts(?string $login, ?string $ip): bool
    {
        return $this->loginRemainingSeconds($login, $ip) > 0;
    }

    public function loginRemainingSeconds(?string $login, ?string $ip): int
    {
        $window = 15 * 60;
        $remaining = 0;

        $ipLimit = (int) SecuritySettings::get('login_max_attempts_per_ip_15m', 12);
        $accountLimit = (int) SecuritySettings::get('login_max_attempts_per_account_15m', 6);

        if ($ipLimit > 0 && $ip) {
            $ipKey = $this->loginIpKey($ip);
            if (RateLimiter::tooManyAttempts($ipKey, $ipLimit)) {
                $remaining = max($remaining, RateLimiter::availableIn($ipKey));
            }
        }

        $loginValue = $this->normalizedLogin($login);
        if ($accountLimit > 0 && $loginValue !== '') {
            $accountKey = $this->loginAccountKey($loginValue);
            if (RateLimiter::tooManyAttempts($accountKey, $accountLimit)) {
                $remaining = max($remaining, RateLimiter::availableIn($accountKey));
            }
        }

        return min($remaining, $window);
    }

    public function hitLoginAttempt(?string $login, ?string $ip): void
    {
        $window = 15 * 60;
        $ipLimit = (int) SecuritySettings::get('login_max_attempts_per_ip_15m', 12);
        $accountLimit = (int) SecuritySettings::get('login_max_attempts_per_account_15m', 6);

        if ($ipLimit > 0 && $ip) {
            RateLimiter::hit($this->loginIpKey($ip), $window);
        }

        $loginValue = $this->normalizedLogin($login);
        if ($accountLimit > 0 && $loginValue !== '') {
            RateLimiter::hit($this->loginAccountKey($loginValue), $window);
        }
    }

    public function clearLoginAttempts(?string $login, ?string $ip): void
    {
        if ($ip) {
            RateLimiter::clear($this->loginIpKey($ip));
        }

        $loginValue = $this->normalizedLogin($login);
        if ($loginValue !== '') {
            RateLimiter::clear($this->loginAccountKey($loginValue));
        }
    }

    public function loginMessage(?string $login, ?string $ip): ?string
    {
        $remaining = $this->loginRemainingSeconds($login, $ip);
        if ($remaining <= 0) {
            return null;
        }

        return __('messages.security_login_throttled', [
            'seconds' => $remaining,
        ]);
    }

    public function canRegisterFromIp(?string $ip): bool
    {
        return $this->registrationRemainingSlots($ip) !== 0;
    }

    public function registrationRemainingSlots(?string $ip): ?int
    {
        $limit = (int) SecuritySettings::get('registration_ip_daily_limit', 3);
        if ($limit <= 0 || !$ip) {
            return null;
        }

        $count = (int) Cache::get($this->registrationIpKey($ip), 0);

        return max(0, $limit - $count);
    }

    public function hitRegistrationFromIp(?string $ip): void
    {
        $limit = (int) SecuritySettings::get('registration_ip_daily_limit', 3);
        if ($limit <= 0 || !$ip) {
            return;
        }

        $key = $this->registrationIpKey($ip);
        $ttl = max(60, now()->endOfDay()->diffInSeconds(now()));

        if (!Cache::has($key)) {
            Cache::put($key, 0, $ttl);
        }

        Cache::increment($key);
        Cache::put($key, (int) Cache::get($key, 0), $ttl);
    }

    public function registrationLimitMessage(?string $ip): ?string
    {
        $remaining = $this->registrationRemainingSlots($ip);
        if ($remaining === null || $remaining > 0) {
            return null;
        }

        return __('messages.security_registration_ip_limit_reached');
    }

    private function cooldownSeconds(string $action): int
    {
        return match ($action) {
            'post' => (int) SecuritySettings::get('cooldown_post_seconds', 20),
            'comment' => (int) SecuritySettings::get('cooldown_comment_seconds', 10),
            'forum_topic' => (int) SecuritySettings::get('cooldown_forum_topic_seconds', 60),
            'private_message' => (int) SecuritySettings::get('cooldown_private_message_seconds', 8),
            default => 0,
        };
    }

    private function actionKey(int $userId, string $action): string
    {
        return sprintf('security:cooldown:%s:%d', $action, $userId);
    }

    private function loginIpKey(string $ip): string
    {
        return sprintf('security:login:ip:%s', sha1($ip));
    }

    private function loginAccountKey(string $login): string
    {
        return sprintf('security:login:account:%s', sha1($login));
    }

    private function normalizedLogin(?string $login): string
    {
        return mb_strtolower(trim((string) $login));
    }

    private function registrationIpKey(string $ip): string
    {
        return sprintf('security:register:%s:%s', now()->format('Ymd'), sha1($ip));
    }
}
