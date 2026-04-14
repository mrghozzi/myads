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
            'bonus_pts' => 0,
            'bonus_nvu' => 0,
            'bonus_nlink' => 0,
            'bonus_nsmart' => 0,
            'status_promotion_discount_pct' => 0,
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
            return null;
        }

        return [
            'label' => $label,
            'color' => trim((string) ($entitlements['profile_badge_color'] ?? '#615dfa')) ?: '#615dfa',
        ];
    }

    public function applyActivationBenefits(User|int $user, MemberSubscription $subscription): void
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return;
        }

        if ($subscription->benefits_applied_at !== null) {
            return;
        }

        DB::transaction(function () use ($user, $subscription): void {
            /** @var MemberSubscription $locked */
            $locked = MemberSubscription::query()->lockForUpdate()->findOrFail($subscription->id);

            if ($locked->benefits_applied_at !== null) {
                return;
            }

            $userModel = $user instanceof User
                ? User::query()->lockForUpdate()->findOrFail($user->id)
                : User::query()->lockForUpdate()->findOrFail((int) $user);

            $this->grantPurchaseBenefits(
                $userModel,
                $this->entitlementsForSubscription($locked),
                $locked->id,
                (string) ($locked->plan_name ?: data_get($locked->plan_snapshot, 'name', __('messages.billing_subscription_plan')))
            );

            $locked->forceFill([
                'benefits_applied_at' => now(),
            ])->save();
        });
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
                'member_subscription',
                $referenceId,
                ['plan_name' => $planName],
                true
            );
        }

        $userModel->nvu = (float) $userModel->nvu + max(0, (float) ($entitlements['bonus_nvu'] ?? 0));
        $userModel->nlink = (float) $userModel->nlink + max(0, (float) ($entitlements['bonus_nlink'] ?? 0));
        $userModel->nsmart = (float) $userModel->nsmart + max(0, (float) ($entitlements['bonus_nsmart'] ?? 0));
        $userModel->save();
    }
}
