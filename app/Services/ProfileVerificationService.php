<?php

namespace App\Services;

use App\Models\Like;
use App\Models\Option;
use App\Models\User;
use Illuminate\Support\Carbon;

class ProfileVerificationService
{
    private const TYPE = 'profile_verification';

    public function get(string $key, mixed $default = null): mixed
    {
        return Option::where('o_type', self::TYPE)->where('name', $key)->value('o_valuer') ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        Option::updateOrCreate(
            ['o_type' => self::TYPE, 'name' => $key],
            ['o_valuer' => (string) $value]
        );
    }

    public function settings(?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        return [
            'enabled' => (bool) $this->get('enabled', '0'),
            'require_verified_email' => (bool) $this->get('require_verified_email', '1'),
            'min_account_age_days' => max(0, (int) $this->get('min_account_age_days', 0)),
            'min_followers_count' => max(0, (int) $this->get('min_followers_count', 0)),
            'terms' => $this->termsForLocale($locale),
            'locale' => $locale,
        ];
    }

    public function supportedLocales(): array
    {
        return collect(glob(lang_path('*/messages.php')) ?: [])
            ->map(fn (string $path) => basename(dirname($path)))
            ->sort()
            ->values()
            ->all();
    }

    public function termsForLocale(string $locale): string
    {
        $locales = $this->supportedLocales();
        if (!in_array($locale, $locales, true)) {
            $locale = config('app.fallback_locale', 'en');
        }

        return (string) ($this->get('terms_' . $locale)
            ?? $this->get('terms_en')
            ?? $this->get('terms')
            ?? __('messages.verification_default_terms'));
    }

    public function eligibility(User $user): array
    {
        $settings = $this->settings();
        $createdAt = $user->created_at ? Carbon::parse($user->created_at) : null;
        $ageDays = $createdAt ? $createdAt->diffInDays(now()) : 0;
        $followers = Like::where('sid', $user->id)->where('type', 1)->whereHas('user')->count();

        return [
            'enabled' => $settings['enabled'],
            'email_verified' => !$settings['require_verified_email'] || (bool) $user->email_verified_at,
            'account_age' => $ageDays >= $settings['min_account_age_days'],
            'followers' => $followers >= $settings['min_followers_count'],
            'account_age_days' => $ageDays,
            'followers_count' => $followers,
            'eligible' => $settings['enabled']
                && (!$settings['require_verified_email'] || (bool) $user->email_verified_at)
                && $ageDays >= $settings['min_account_age_days']
                && $followers >= $settings['min_followers_count'],
        ];
    }
}
