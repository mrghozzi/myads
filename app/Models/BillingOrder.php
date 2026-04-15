<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BillingOrder extends Model
{
    use HasFactory;

    public const STATUS_PENDING_CHECKOUT = 'pending_checkout';
    public const STATUS_PENDING_RECEIPT = 'pending_receipt';
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_PAID = 'paid';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_FAILED = 'failed';

    protected $table = 'billing_orders';

    protected $fillable = [
        'order_number',
        'user_id',
        'subscription_plan_id',
        'member_subscription_id',
        'gateway',
        'status',
        'base_currency_code',
        'currency_code',
        'base_amount',
        'display_amount',
        'exchange_rate_snapshot',
        'gateway_checkout_reference',
        'gateway_reference',
        'receipt_path',
        'receipt_note',
        'admin_note',
        'paid_at',
        'approved_at',
        'rejected_at',
        'expires_at',
        'plan_snapshot',
        'meta',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'display_amount' => 'decimal:2',
        'exchange_rate_snapshot' => 'decimal:6',
        'paid_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'expires_at' => 'datetime',
        'plan_snapshot' => 'array',
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

    public function subscription()
    {
        return $this->belongsTo(MemberSubscription::class, 'member_subscription_id');
    }

    public function transactions()
    {
        return $this->hasMany(BillingTransaction::class, 'billing_order_id');
    }

    public function isAwaitingManualReview(): bool
    {
        return $this->status === self::STATUS_PENDING_REVIEW;
    }

    public function gatewayLabel(): string
    {
        $translationKey = match ((string) $this->gateway) {
            'stripe' => 'messages.billing_gateway_stripe',
            'paypal' => 'messages.billing_gateway_paypal',
            'bank_transfer' => 'messages.billing_gateway_bank_transfer',
            default => null,
        };

        if ($translationKey !== null) {
            $translation = __($translationKey);
            if ($translation !== $translationKey) {
                return $translation;
            }
        }

        return Str::headline(str_replace('_', ' ', (string) $this->gateway));
    }

    public function receiptUrl(): ?string
    {
        return filled($this->receipt_path)
            ? route('billing.orders.receipt.show', $this->id)
            : null;
    }
}
