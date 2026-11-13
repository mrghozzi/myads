<?php

namespace App\Services\Billing;

use App\Models\BillingCoupon;
use App\Models\BillingOrder;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\V420SchemaService;
use Illuminate\Pagination\LengthAwarePaginator;

class BillingCouponService
{
    public function __construct(
        private readonly V420SchemaService $schema
    ) {
    }

    public function validate(string $code, SubscriptionPlan $plan, ?User $user = null): array
    {
        $code = strtoupper(trim($code));
        if ($code === '') {
            return [
                'valid' => false,
                'message' => __('messages.billing_coupon_empty'),
            ];
        }

        try {
            $coupon = BillingCoupon::where('code', $code)->first();

            if (!$coupon) {
                return [
                    'valid' => false,
                    'message' => __('messages.billing_coupon_not_found'),
                ];
            }

            if (!$coupon->is_active) {
                return [
                    'valid' => false,
                    'message' => __('messages.billing_coupon_inactive'),
                ];
            }

            if ($coupon->expires_at && $coupon->expires_at->isPast()) {
                return [
                    'valid' => false,
                    'message' => __('messages.billing_coupon_expired'),
                ];
            }

            if ($coupon->max_uses !== null && $coupon->used_count >= $coupon->max_uses) {
                return [
                    'valid' => false,
                    'message' => __('messages.billing_coupon_max_reached'),
                ];
            }

            if ($coupon->subscription_plan_id !== null && (int) $coupon->subscription_plan_id !== (int) $plan->id) {
                return [
                    'valid' => false,
                    'message' => __('messages.billing_coupon_plan_mismatch'),
                ];
            }

            $baseAmount = (float) $plan->current_price;
            $calc = $coupon->calculateDiscount($baseAmount);

            return [
                'valid' => true,
                'coupon' => $coupon,
                'code' => $coupon->code,
                'discount_type' => $coupon->discount_type,
                'discount_value' => (float) $coupon->discount_value,
                'discount_amount' => $calc['discount_amount'],
                'final_amount' => $calc['final_amount'],
                'message' => __('messages.billing_coupon_applied_success', [
                    'code' => $coupon->code,
                    'discount' => $coupon->discount_type === 'percent' ? $coupon->discount_value . '%' : $coupon->discount_value,
                ]),
            ];
        } catch (\Throwable $e) {
            return [
                'valid' => false,
                'message' => __('messages.billing_coupon_error'),
            ];
        }
    }

    public function recordUsage(BillingCoupon $coupon): void
    {
        $coupon->increment('used_count');
    }

    public function paginateForAdmin(string $search = '', int $perPage = 15): LengthAwarePaginator
    {
        return BillingCoupon::query()
            ->with('plan')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('code', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function store(array $values): BillingCoupon
    {
        return BillingCoupon::create([
            'code' => strtoupper(trim((string) $values['code'])),
            'description' => trim((string) ($values['description'] ?? '')),
            'discount_type' => in_array($values['discount_type'] ?? 'percent', ['percent', 'fixed'], true) ? $values['discount_type'] : 'percent',
            'discount_value' => max(0, round((float) ($values['discount_value'] ?? 0), 2)),
            'subscription_plan_id' => !empty($values['subscription_plan_id']) ? (int) $values['subscription_plan_id'] : null,
            'max_uses' => !empty($values['max_uses']) ? max(1, (int) $values['max_uses']) : null,
            'expires_at' => !empty($values['expires_at']) ? $values['expires_at'] : null,
            'is_active' => !empty($values['is_active']),
        ]);
    }

    public function update(BillingCoupon $coupon, array $values): BillingCoupon
    {
        $coupon->update([
            'code' => strtoupper(trim((string) $values['code'])),
            'description' => trim((string) ($values['description'] ?? '')),
            'discount_type' => in_array($values['discount_type'] ?? 'percent', ['percent', 'fixed'], true) ? $values['discount_type'] : 'percent',
            'discount_value' => max(0, round((float) ($values['discount_value'] ?? 0), 2)),
            'subscription_plan_id' => !empty($values['subscription_plan_id']) ? (int) $values['subscription_plan_id'] : null,
            'max_uses' => !empty($values['max_uses']) ? max(1, (int) $values['max_uses']) : null,
            'expires_at' => !empty($values['expires_at']) ? $values['expires_at'] : null,
            'is_active' => !empty($values['is_active']),
        ]);

        return $coupon->fresh();
    }

    public function delete(BillingCoupon $coupon): bool
    {
        return (bool) $coupon->delete();
    }
}
