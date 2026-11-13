<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingCoupon extends Model
{
    use HasFactory;

    protected $table = 'billing_coupons';

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'subscription_plan_id',
        'max_uses',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'subscription_plan_id' => 'integer',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function orders()
    {
        return $this->hasMany(BillingOrder::class, 'billing_coupon_id');
    }

    public function isValidForPlan(?int $planId): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        if ($this->subscription_plan_id !== null && (int) $this->subscription_plan_id !== (int) $planId) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $baseAmount): array
    {
        $discountAmount = 0.0;

        if ($this->discount_type === 'percent') {
            $discountAmount = round($baseAmount * ($this->discount_value / 100), 2);
        } else {
            $discountAmount = round((float) $this->discount_value, 2);
        }

        $discountAmount = min($baseAmount, max(0.0, $discountAmount));
        $finalAmount = max(0.0, round($baseAmount - $discountAmount, 2));

        return [
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
        ];
    }
}
