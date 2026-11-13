<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemberSubscription;
use App\Models\SubscriptionPlan;
use App\Services\Billing\BillingCouponService;
use App\Services\Billing\BillingCurrencyService;
use App\Services\Billing\SubscriptionEntitlementService;
use App\Services\Billing\SubscriptionLifecycleService;
use App\Services\Billing\SubscriptionPlanService;
use App\Services\V420SchemaService;
use App\Support\SubscriptionSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingApiController extends Controller
{
    public function __construct(
        private readonly V420SchemaService $schema,
        private readonly SubscriptionPlanService $plans,
        private readonly SubscriptionLifecycleService $lifecycle,
        private readonly SubscriptionEntitlementService $entitlements,
        private readonly BillingCurrencyService $currencies,
        private readonly BillingCouponService $coupons
    ) {
    }

    /**
     * Return active subscription plans formatted for mobile client.
     */
    public function plans(Request $request): JsonResponse
    {
        if (!$this->schema->supports('subscriptions_billing') || !SubscriptionSettings::isEnabled()) {
            return response()->json([
                'enabled' => false,
                'message' => __('messages.billing_system_disabled_notice'),
                'plans' => [],
            ]);
        }

        $user = $request->user();
        $activePlans = $this->plans->activePlans($request->query('search', ''));
        $activeSub = $user ? $this->entitlements->activeSubscriptionFor($user->id) : null;

        $plansData = $activePlans->map(function (SubscriptionPlan $plan) use ($activeSub) {
            $marketingBullets = is_array($plan->marketing_bullets) ? $plan->marketing_bullets : [];
            $entitlements = is_array($plan->entitlements) ? $plan->entitlements : [];
            $benefits = $this->entitlements->memberBenefitLines($entitlements);

            $now = now();
            $saleSeconds = ($plan->is_on_sale && $plan->sale_ends_at)
                ? max(0, $now->diffInSeconds($plan->sale_ends_at, false))
                : null;

            return [
                'id' => $plan->id,
                'name' => $plan->name,
                'description' => $plan->description ?? '',
                'duration_days' => $plan->duration_days,
                'is_lifetime' => (bool) $plan->is_lifetime,
                'base_price' => (float) $plan->base_price,
                'sale_price' => $plan->sale_price !== null ? (float) $plan->sale_price : null,
                'current_price' => (float) $plan->current_price,
                'is_on_sale' => (bool) $plan->is_on_sale,
                'discount_percentage' => $plan->discount_percentage,
                'sale_ends_at' => $plan->sale_ends_at?->toIso8601String(),
                'sale_seconds_remaining' => $saleSeconds,
                'has_trial' => (bool) $plan->has_trial,
                'trial_days' => (int) ($plan->trial_days ?? 0),
                'is_featured' => (bool) $plan->is_featured,
                'recommended_text' => $plan->recommended_text ?? '',
                'accent_color' => $plan->accent_color ?? '#615dfa',
                'layout_style' => $plan->layout_style ?? 'glassmorphism',
                'marketing_bullets' => $marketingBullets,
                'benefits' => $benefits,
                'is_current_plan' => $activeSub ? ($activeSub->subscription_plan_id === $plan->id) : false,
                'checkout_url' => url('/billing/checkout/' . $plan->id),
                'trial_url' => url('/plans/' . $plan->id . '/trial'),
            ];
        });

        return response()->json([
            'enabled' => true,
            'current_subscription' => $activeSub ? [
                'id' => $activeSub->id,
                'plan_id' => $activeSub->subscription_plan_id,
                'plan_name' => $activeSub->plan_name,
                'status' => $activeSub->status,
                'is_trial' => (bool) $activeSub->is_trial,
                'starts_at' => $activeSub->starts_at?->toIso8601String(),
                'ends_at' => $activeSub->ends_at?->toIso8601String(),
                'days_left' => $activeSub->ends_at ? max(0, (int) now()->diffInDays($activeSub->ends_at, false)) : null,
            ] : null,
            'plans' => $plansData,
        ]);
    }

    /**
     * Return user's active subscription status, queued subscription, and plan details.
     */
    public function subscription(Request $request): JsonResponse
    {
        if (!$this->schema->supports('subscriptions_billing') || !SubscriptionSettings::isEnabled()) {
            return response()->json([
                'enabled' => false,
                'has_active_subscription' => false,
                'subscription' => null,
                'message' => __('messages.billing_system_disabled_notice'),
            ]);
        }

        $user = $request->user();
        $state = $this->lifecycle->syncUserSubscriptions($user->id);
        /** @var MemberSubscription|null $active */
        $active = $state['active'] ?? null;
        /** @var MemberSubscription|null $queued */
        $queued = $state['queued'] ?? null;

        if (!$active) {
            return response()->json([
                'enabled' => true,
                'has_active_subscription' => false,
                'subscription' => null,
                'queued_subscription' => null,
                'plans_url' => url('/plans'),
                'billing_url' => url('/billing'),
            ]);
        }

        $daysLeft = $active->ends_at ? max(0, (int) now()->diffInDays($active->ends_at, false)) : null;
        $isExpiringSoon = $daysLeft !== null && $daysLeft <= 7;
        $profileBadge = $this->entitlements->activeProfileBadgeForUserId($user->id);

        return response()->json([
            'enabled' => true,
            'has_active_subscription' => true,
            'subscription' => [
                'id' => $active->id,
                'plan_id' => $active->subscription_plan_id,
                'plan_name' => $active->plan_name,
                'status' => $active->status,
                'is_trial' => (bool) $active->is_trial,
                'starts_at' => $active->starts_at?->toIso8601String(),
                'ends_at' => $active->ends_at?->toIso8601String(),
                'days_left' => $daysLeft,
                'is_expiring_soon' => $isExpiringSoon,
                'profile_badge' => $profileBadge,
                'entitlements' => $active->entitlements_snapshot ?? [],
            ],
            'queued_subscription' => $queued ? [
                'id' => $queued->id,
                'plan_id' => $queued->subscription_plan_id,
                'plan_name' => $queued->plan_name,
                'status' => $queued->status,
                'starts_at' => $queued->starts_at?->toIso8601String(),
                'ends_at' => $queued->ends_at?->toIso8601String(),
            ] : null,
            'plans_url' => url('/plans'),
            'billing_url' => url('/billing'),
        ]);
    }

    /**
     * Validate a promo coupon code from mobile.
     */
    public function validateCoupon(Request $request): JsonResponse
    {
        if (!$this->schema->supports('subscriptions_billing') || !SubscriptionSettings::isEnabled()) {
            return response()->json(['valid' => false, 'message' => __('messages.billing_system_disabled_notice')], 403);
        }

        $code = trim((string) ($request->input('coupon_code') ?: $request->input('code')));
        $planId = (int) $request->input('plan_id');

        if ($code === '') {
            return response()->json(['valid' => false, 'message' => __('messages.billing_coupon_empty')], 422);
        }

        $planModel = $this->plans->find($planId, true);
        if (!$planModel) {
            return response()->json(['valid' => false, 'message' => __('messages.no_data')], 404);
        }

        $result = $this->coupons->validate($code, $planModel, $request->user());

        return response()->json($result);
    }

    /**
     * Claim a free trial on a plan.
     */
    public function claimTrial(Request $request): JsonResponse
    {
        if (!$this->schema->supports('subscriptions_billing') || !SubscriptionSettings::isEnabled()) {
            return response()->json(['success' => false, 'message' => __('messages.billing_system_disabled_notice')], 403);
        }

        $planId = (int) ($request->input('plan_id') ?: $request->input('plan'));
        $planModel = $this->plans->find($planId, true);

        if (!$planModel || !$planModel->has_trial) {
            return response()->json(['success' => false, 'message' => __('messages.no_data')], 404);
        }

        try {
            $subscription = $this->lifecycle->startFreeTrial($request->user(), $planModel);

            return response()->json([
                'success' => true,
                'message' => __('messages.billing_trial_started_success', [
                    'plan' => $planModel->name,
                    'days' => $planModel->trial_days,
                ]),
                'subscription' => [
                    'id' => $subscription->id,
                    'plan_id' => $subscription->subscription_plan_id,
                    'plan_name' => $subscription->plan_name,
                    'is_trial' => true,
                    'starts_at' => $subscription->starts_at?->toIso8601String(),
                    'ends_at' => $subscription->ends_at?->toIso8601String(),
                ],
            ]);
        } catch (\DomainException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
