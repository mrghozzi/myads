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
        'is_featured',
        'is_active',
        'sort_order',
        'accent_color',
        'recommended_text',
        'marketing_bullets',
        'entitlements',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'is_lifetime' => 'boolean',
        'base_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'marketing_bullets' => 'array',
        'entitlements' => 'array',
    ];

    public function subscriptions()
    {
        return $this->hasMany(MemberSubscription::class, 'subscription_plan_id');
    }

    public function orders()
    {
        return $this->hasMany(BillingOrder::class, 'subscription_plan_id');
    }
}
