<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $table = 'subscription_plans';

    protected $fillable = [
        'name',
        'description',
        'duration_days',
        'is_lifetime',
        'base_price',
        'sale_price',
        'sale_ends_at',
        'trial_days',
        'is_featured',
        'is_active',
        'sort_order',
        'accent_color',
        'layout_style',
        'recommended_text',
        'marketing_bullets',
        'entitlements',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'is_lifetime' => 'boolean',
        'base_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'sale_ends_at' => 'datetime',
        'trial_days' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'marketing_bullets' => 'array',
        'entitlements' => 'array',
    ];

    public function getIsOnSaleAttribute(): bool
    {
        if ($this->sale_price === null || (float) $this->sale_price >= (float) $this->base_price) {
            return false;
        }

        if ($this->sale_ends_at !== null && $this->sale_ends_at->isPast()) {
            return false;
        }

        return true;
    }

    public function getCurrentPriceAttribute(): float
    {
        return $this->is_on_sale ? (float) $this->sale_price : (float) $this->base_price;
    }

    public function getDiscountPercentageAttribute(): int
    {
        if (!$this->is_on_sale || (float) $this->base_price <= 0) {
            return 0;
        }

        return (int) round(((float) $this->base_price - (float) $this->sale_price) / (float) $this->base_price * 100);
    }

    public function getHasTrialAttribute(): bool
    {
        return $this->trial_days !== null && (int) $this->trial_days > 0;
    }

    public function coupons()
    {
        return $this->hasMany(BillingCoupon::class, 'subscription_plan_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(MemberSubscription::class, 'subscription_plan_id');
    }

    public function orders()
    {
        return $this->hasMany(BillingOrder::class, 'subscription_plan_id');
    }
}
