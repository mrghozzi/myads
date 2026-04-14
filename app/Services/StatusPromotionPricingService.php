<?php

namespace App\Services;

use App\Models\Status;
use App\Services\Billing\SubscriptionEntitlementService;
use App\Support\StatusPromotionSettings;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class StatusPromotionPricingService
{
    public function __construct(
        private readonly V420SchemaService $schema,
        private readonly SubscriptionEntitlementService $entitlements
    ) {
    }

    public function quote(Status $status, string $objective, int $targetQuantity): array
    {
        $objective = trim(mb_strtolower($objective, 'UTF-8'));
        $settings = StatusPromotionSettings::all();

        if (!in_array($objective, ['views', 'comments', 'reactions', 'days'], true)) {
            throw ValidationException::withMessages([
                'objective' => __('messages.status_promotion_invalid_objective'),
            ]);
        }

        $minTarget = StatusPromotionSettings::minTargetFor($objective);
        $maxTarget = StatusPromotionSettings::maxTargetFor($objective);
        $targetQuantity = max(1, $targetQuantity);

        if ($targetQuantity < $minTarget || $targetQuantity > $maxTarget) {
            throw ValidationException::withMessages([
                'target_quantity' => __('messages.status_promotion_target_range', [
                    'min' => $minTarget,
                    'max' => $maxTarget,
                ]),
            ]);
        }

        $smartFactor = $this->smartFactor($status);
        $deliveryCap = $this->deliveryCapImpressions($objective, $targetQuantity);
        $durationDays = $this->estimatedDurationDays($objective, $targetQuantity, $deliveryCap);

        $chargedPts = match ($objective) {
            'views' => (int) max(1, ceil(($targetQuantity / 100) * (float) $settings['price_per_100_views_pts'] * $smartFactor)),
            'reactions' => (int) max(1, ceil($targetQuantity * (float) $settings['price_per_reaction_goal_pts'] * $smartFactor)),
            'comments' => (int) max(1, ceil($targetQuantity * (float) $settings['price_per_comment_goal_pts'] * $smartFactor)),
            'days' => (int) max(1, ceil($targetQuantity * (float) $settings['price_per_day_pts'] * $smartFactor)),
        };

        $discountPct = $this->entitlements->activeDiscountPercentageForUserId((int) $status->uid);
        if ($discountPct > 0) {
            $chargedPts = (int) max(1, ceil($chargedPts * (1 - ($discountPct / 100))));
        }

        return [
            'objective' => $objective,
            'target_quantity' => $targetQuantity,
            'smart_factor' => round($smartFactor, 2),
            'charged_pts' => $chargedPts,
            'subscription_discount_pct' => $discountPct,
            'delivery_cap_impressions' => $deliveryCap,
            'estimated_duration_days' => $durationDays,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addDays($durationDays),
        ];
    }

    public function smartFactor(Status $status): float
    {
        $factor = 1.00;
        $statusDate = Carbon::createFromTimestamp((int) $status->date);
        $ageDays = $statusDate->diffInDays(Carbon::now());

        if ($ageDays > 7) {
            $factor += 0.10;
        }

        if ($ageDays > 30) {
            $factor += 0.20;
        }

        $engagement = (int) $status->comments_count + (int) $status->reactions_count;
        if ($engagement >= 10) {
            $factor -= 0.05;
        }

        if ($this->hasVisualRichness($status)) {
            $factor -= 0.05;
        }

        return max(0.85, min(1.30, $factor));
    }

    public function deliveryCapImpressions(string $objective, int $targetQuantity): int
    {
        return match ($objective) {
            'views' => $targetQuantity,
            'reactions' => $targetQuantity * StatusPromotionSettings::estimateFor('reactions'),
            'comments' => $targetQuantity * StatusPromotionSettings::estimateFor('comments'),
            'days' => $targetQuantity * StatusPromotionSettings::estimateFor('days'),
            default => $targetQuantity,
        };
    }

    public function estimatedDurationDays(string $objective, int $targetQuantity, int $deliveryCap): int
    {
        if ($objective === 'days') {
            return max(1, $targetQuantity);
        }

        $estimatedViewsPerDay = max(1, (int) StatusPromotionSettings::get('estimated_views_per_day', 120));

        return max(1, (int) ceil($deliveryCap / $estimatedViewsPerDay));
    }

    private function hasVisualRichness(Status $status): bool
    {
        if (in_array((int) $status->s_type, [1, 4, 7867], true)) {
            return true;
        }

        if ($this->schema->supports('link_previews')) {
            $status->loadMissing('linkPreviewRecord');

            return $status->linkPreviewRecord !== null;
        }

        return false;
    }
}
