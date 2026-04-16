<?php

namespace App\Services\Billing;

use App\Models\MemberSubscription;
use App\Models\User;
use App\Services\PointLedgerService;
use App\Services\V420SchemaService;
use Illuminate\Support\Facades\DB;

class SubscriptionEntitlementService
{
    public function __construct(
        private readonly V420SchemaService $schema,
        private readonly PointLedgerService $ledger
    ) {
    }

    public function activeSubscriptionFor(User|int $user): ?MemberSubscription
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return null;
        }

        $userId = $user instanceof User ? (int) $user->id : (int) $user;

        try {
            return MemberSubscription::query()
                ->where('user_id', $userId)
                ->where('status', MemberSubscription::STATUS_ACTIVE)
                ->where(function ($query) {
                    $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
                })
                ->latest('activated_at')
                ->first();
        } catch (\Throwable) {
            return null;
        }
    }

    public function queuedSubscriptionFor(User|int $user): ?MemberSubscription
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return null;
        }

        $userId = $user instanceof User ? (int) $user->id : (int) $user;

        try {
            return MemberSubscription::query()
                ->where('user_id', $userId)
                ->where('status', MemberSubscription::STATUS_QUEUED)
                ->orderBy('starts_at')
                ->first();
        } catch (\Throwable) {
            return null;
        }
    }

    public function entitlementsForSubscription(?MemberSubscription $subscription): array
    {
        return array_merge([
            'profile_badge_label' => '',
            'profile_badge_color' => '#615dfa',
            'subscription_verified_badge' => false,
            'bonus_pts' => 0,
            'bonus_nvu' => 0,
            'bonus_nlink' => 0,
            'bonus_nsmart' => 0,
            'status_promotion_discount_pct' => 0,
            'extra_included_benefits' => [],
        ], (array) ($subscription?->entitlements_snapshot ?? []));
    }

    public function activeDiscountPercentageForUserId(int $userId): float
    {
        $entitlements = $this->entitlementsForSubscription($this->activeSubscriptionFor($userId));

        return max(0, min(95, (float) ($entitlements['status_promotion_discount_pct'] ?? 0)));
    }

    public function activeProfileBadgeForUserId(int $userId): ?array
    {
        $entitlements = $this->entitlementsForSubscription($this->activeSubscriptionFor($userId));
        $label = trim((string) ($entitlements['profile_badge_label'] ?? ''));

        if ($label === '') {
            // Hardcoded fallback for Super Admin (ID=1) to ensure they show a premium badge
            if ($userId === 1 && \App\Support\SubscriptionSettings::isEnabled()) {
                return [
                    'label' => 'Super Admin',
                    'color' => '#fbbf24', // Gold
                ];
            }
            return null;
        }

        return [
            'label' => $label,
            'color' => trim((string) ($entitlements['profile_badge_color'] ?? '#615dfa')) ?: '#615dfa',
        ];
    }

    public function hasSubscriptionVerifiedBadgeForUserId(int $userId): bool
    {
        $entitlements = $this->entitlementsForSubscription($this->activeSubscriptionFor($userId));

        return (bool) ($entitlements['subscription_verified_badge'] ?? false);
    }

    public function memberBenefitLines(array $entitlements): array
    {
        $normalized = array_merge($this->entitlementsForSubscription(null), $entitlements);
        $benefits = [];

        if (trim((string) ($normalized['profile_badge_label'] ?? '')) !== '') {
            $benefits[] = __('messages.billing_profile_badge_benefit', [
                'label' => $normalized['profile_badge_label'],
            ]);
        }

        if (!empty($normalized['subscription_verified_badge'])) {
            $benefits[] = __('messages.billing_verified_badge_benefit');
        }

        if ((float) ($normalized['bonus_pts'] ?? 0) > 0) {
            $benefits[] = __('messages.billing_bonus_pts_benefit', ['amount' => $normalized['bonus_pts']]);
        }

        if ((float) ($normalized['bonus_nvu'] ?? 0) > 0) {
            $benefits[] = __('messages.billing_bonus_nvu_benefit', ['amount' => $normalized['bonus_nvu']]);
        }

        if ((float) ($normalized['bonus_nlink'] ?? 0) > 0) {
            $benefits[] = __('messages.billing_bonus_nlink_benefit', ['amount' => $normalized['bonus_nlink']]);
        }

        if ((float) ($normalized['bonus_nsmart'] ?? 0) > 0) {
            $benefits[] = __('messages.billing_bonus_nsmart_benefit', ['amount' => $normalized['bonus_nsmart']]);
        }

        if ((float) ($normalized['status_promotion_discount_pct'] ?? 0) > 0) {
            $benefits[] = __('messages.billing_discount_benefit', ['amount' => $normalized['status_promotion_discount_pct']]);
        }

        foreach ((array) ($normalized['extra_included_benefits'] ?? []) as $benefit) {
            $benefit = trim((string) $benefit);
            if ($benefit !== '') {
                $benefits[] = $benefit;
            }
        }

        return $benefits;
    }

    public function applyActivationBenefits(User|int $user, MemberSubscription $subscription): void
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return;
        }

        if ($subscription->benefits_applied_at !== null) {
            return;
        }

        DB::transaction(function () use ($subscription): void {
            /** @var MemberSubscription $locked */
            $locked = MemberSubscription::query()->lockForUpdate()->findOrFail($subscription->id);

            if ($locked->benefits_applied_at !== null) {
                return;
            }

            $locked->forceFill([
                'benefits_applied_at' => now(),
            ])->save();
        });

        $this->forgetUserBadgeCaches($user instanceof User ? (int) $user->id : (int) $user);
    }

    public function grantPurchaseBenefits(User|int $user, array $entitlements, int $referenceId, string $planName): void
    {
        $userModel = $user instanceof User
            ? User::query()->lockForUpdate()->findOrFail($user->id)
            : User::query()->lockForUpdate()->findOrFail((int) $user);

        $bonusPts = max(0, round((float) ($entitlements['bonus_pts'] ?? 0), 2));
        if ($bonusPts > 0) {
            $this->ledger->award(
                $userModel,
                $bonusPts,
                'subscription_bonus',
                'subscription_bonus_pts',
                'billing_order',
                $referenceId,
                ['plan_name' => $planName],
                !$this->schema->supports('point_history')
            );
        }

        $userModel->nvu = (float) $userModel->nvu + max(0, (float) ($entitlements['bonus_nvu'] ?? 0));
        $userModel->nlink = (float) $userModel->nlink + max(0, (float) ($entitlements['bonus_nlink'] ?? 0));
        $userModel->nsmart = (float) $userModel->nsmart + max(0, (float) ($entitlements['bonus_nsmart'] ?? 0));
        $userModel->save();
    }

    public function forgetUserBadgeCaches(int $userId): void
    {
        \Illuminate\Support\Facades\Cache::forget("user_{$userId}_profile_badge_color_v3");
        \Illuminate\Support\Facades\Cache::forget("user_{$userId}_verified_badge_v1");
    }
}
