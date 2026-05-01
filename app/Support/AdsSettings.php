<?php

namespace App\Support;

use App\Models\Option;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

class AdsSettings
{
    public const OPTION_TYPE = 'ads_settings';
    public const BRAND_NAME = 'brand_name';
    public const IP_VISIBILITY = 'ip_visibility';

    public const IP_VISIBILITY_NONE = 'none';
    public const IP_VISIBILITY_ADMINS = 'admins';
    public const IP_VISIBILITY_PAID_ALL = 'paid_all';
    public const IP_VISIBILITY_EVERYONE = 'everyone';
    public const IP_VISIBILITY_PLAN_PREFIX = 'plan_';

    public static function brandName(): string
    {
        try {
            if (Schema::hasTable('options')) {
                $stored = trim((string) Option::where('o_type', self::OPTION_TYPE)
                    ->where('name', self::BRAND_NAME)
                    ->value('o_valuer'));

                if ($stored !== '') {
                    return $stored;
                }
            }

            if (Schema::hasTable('setting')) {
                $siteTitle = trim((string) Setting::query()->value('titer'));
                if ($siteTitle !== '') {
                    return $siteTitle;
                }
            }
        } catch (\Throwable) {
            // Fall through to config fallback for incomplete installs.
        }

        return trim((string) config('app.name', 'MyAds')) ?: 'MyAds';
    }

    public static function ipVisibility(): string
    {
        try {
            if (Schema::hasTable('options')) {
                return trim((string) Option::where('o_type', self::OPTION_TYPE)
                    ->where('name', self::IP_VISIBILITY)
                    ->value('o_valuer')) ?: self::IP_VISIBILITY_EVERYONE;
            }
        } catch (\Throwable) {}

        return self::IP_VISIBILITY_EVERYONE;
    }

    public static function canSeeIp($user): bool
    {
        if (!$user) {
            return false;
        }

        $visibility = self::ipVisibility();

        if ($visibility === self::IP_VISIBILITY_NONE) {
            return false;
        }

        // Admins (ID 1 or SiteAdmin entry)
        if ($user->id === 1 || (method_exists($user, 'isAdmin') && $user->isAdmin())) {
            return true;
        }

        if ($visibility === self::IP_VISIBILITY_ADMINS) {
            return false;
        }

        if ($visibility === self::IP_VISIBILITY_EVERYONE) {
            return true;
        }

        // Paid Subscriptions Check
        $entitlements = app(\App\Services\Billing\SubscriptionEntitlementService::class);
        $activeSub = $entitlements->activeSubscriptionFor($user);

        if (!$activeSub) {
            return false;
        }

        if ($visibility === self::IP_VISIBILITY_PAID_ALL) {
            return true;
        }

        if (str_starts_with($visibility, self::IP_VISIBILITY_PLAN_PREFIX)) {
            $planId = (int) str_replace(self::IP_VISIBILITY_PLAN_PREFIX, '', $visibility);
            return (int) $activeSub->subscription_plan_id === $planId;
        }

        return false;
    }
}
