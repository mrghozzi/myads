<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberSubscription extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_QUEUED = 'queued';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REJECTED = 'rejected';

    protected $table = 'member_subscriptions';

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'billing_order_id',
        'queued_from_subscription_id',
        'status',
        'plan_name',
        'plan_snapshot',
        'entitlements_snapshot',
        'starts_at',
        'ends_at',
        'activated_at',
        'benefits_applied_at',
        'completed_at',
        'meta',
    ];

    protected $casts = [
        'plan_snapshot' => 'array',
        'entitlements_snapshot' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'activated_at' => 'datetime',
        'benefits_applied_at' => 'datetime',
        'completed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function order()
    {
        return $this->belongsTo(BillingOrder::class, 'billing_order_id');
    }
}
