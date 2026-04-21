<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderOffer extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_AWARDED = 'awarded';
    public const STATUS_ARCHIVED = 'archived';
    public const STATUS_WITHDRAWN = 'withdrawn';

    protected $fillable = [
        'order_request_id',
        'user_id',
        'pricing_model',
        'quoted_amount',
        'currency_code',
        'delivery_days',
        'message',
        'status',
        'client_rating',
        'client_review',
        'legacy_option_id',
        'legacy_option_type',
        'awarded_at',
        'withdrawn_at',
        'rated_at',
    ];

    protected $casts = [
        'quoted_amount' => 'decimal:2',
        'client_rating' => 'integer',
        'awarded_at' => 'datetime',
        'withdrawn_at' => 'datetime',
        'rated_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(OrderRequest::class, 'order_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contract()
    {
        return $this->hasOne(OrderContract::class, 'order_offer_id');
    }

    public function scopeMarketplaceVisible($query)
    {
        return $query->whereNotIn('status', [self::STATUS_WITHDRAWN]);
    }

    public function scopeEditable($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function isAwarded(): bool
    {
        return $this->status === self::STATUS_AWARDED;
    }

    public function isEditable(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && (string) optional($this->order)->workflow_status === OrderRequest::WORKFLOW_OPEN;
    }

    public function isWithdrawn(): bool
    {
        return $this->status === self::STATUS_WITHDRAWN;
    }

    public function displayQuote(): string
    {
        if ($this->quoted_amount === null) {
            return __('messages.order_offer_negotiable');
        }

        return $this->currency_code . ' ' . number_format((float) $this->quoted_amount, 2);
    }

    public function displayDelivery(): string
    {
        if (!$this->delivery_days) {
            return __('messages.order_delivery_flexible');
        }

        return __('messages.order_delivery_days_value', ['days' => $this->delivery_days]);
    }

    public function displayStatus(): string
    {
        return __('messages.order_offer_status_' . $this->status);
    }
}
