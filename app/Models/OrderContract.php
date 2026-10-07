<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderContract extends Model
{
    use HasFactory;

    public const STATUS_AWARDED = 'awarded';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_request_id',
        'order_offer_id',
        'client_user_id',
        'provider_user_id',
        'status',
        'pricing_model',
        'quoted_amount',
        'currency_code',
        'delivery_days',
        'snapshot_payload',
        'delivery_note',
        'delivery_attachment_path',
        'delivery_attachment_name',
        'revision_note',
        'revision_count',
        'revision_requested_at',
        'completion_note',
        'awarded_at',
        'started_at',
        'delivered_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'quoted_amount' => 'decimal:2',
        'snapshot_payload' => 'array',
        'revision_count' => 'integer',
        'awarded_at' => 'datetime',
        'started_at' => 'datetime',
        'delivered_at' => 'datetime',
        'revision_requested_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(OrderRequest::class, 'order_request_id');
    }

    public function offer()
    {
        return $this->belongsTo(OrderOffer::class, 'order_offer_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_user_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_user_id');
    }

    public function displayStatus(): string
    {
        return __('messages.order_status_' . $this->status);
    }

    public function hasDeliveryAttachment(): bool
    {
        return !empty($this->delivery_attachment_path);
    }

    public function hasRevision(): bool
    {
        return (int) $this->revision_count > 0 && !empty($this->revision_note);
    }

    public function getDeadlineAttribute(): ?\Carbon\Carbon
    {
        if ($this->started_at && $this->delivery_days) {
            return (clone $this->started_at)->addDays((int) $this->delivery_days);
        }

        return null;
    }

    public function isOverdue(): bool
    {
        $deadline = $this->deadline;
        if (!$deadline || in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED], true)) {
            return false;
        }

        return now()->isAfter($deadline);
    }
}
