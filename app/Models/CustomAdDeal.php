<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomAdDeal extends Model
{
    use HasFactory;

    public const SOURCE_REQUEST = 'request';
    public const SOURCE_INVITE = 'invite';

    public const PAYMENT_PTS_DAILY = 'pts_daily';
    public const PAYMENT_EXTERNAL = 'external';

    public const STATUS_PENDING = 'pending';
    public const STATUS_INVITED = 'invited';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'placement_id',
        'publisher_id',
        'advertiser_id',
        'initiated_by_id',
        'source',
        'payment_type',
        'status',
        'daily_pts',
        'total_pts',
        'reserved_pts',
        'paid_pts',
        'refunded_pts',
        'external_amount',
        'external_currency',
        'external_note',
        'terms',
        'starts_at',
        'ends_at',
        'accepted_at',
        'cancelled_at',
        'last_paid_on',
        'impressions',
        'clicks',
    ];

    protected $casts = [
        'daily_pts' => 'decimal:2',
        'total_pts' => 'decimal:2',
        'reserved_pts' => 'decimal:2',
        'paid_pts' => 'decimal:2',
        'refunded_pts' => 'decimal:2',
        'external_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'accepted_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'last_paid_on' => 'date',
        'impressions' => 'integer',
        'clicks' => 'integer',
    ];

    public function placement()
    {
        return $this->belongsTo(CustomAdPlacement::class, 'placement_id');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }

    public function advertiser()
    {
        return $this->belongsTo(User::class, 'advertiser_id');
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiated_by_id');
    }

    public function creative()
    {
        return $this->hasOne(CustomAdCreative::class, 'deal_id');
    }

    public function events()
    {
        return $this->hasMany(CustomAdEvent::class, 'deal_id');
    }

    public function payouts()
    {
        return $this->hasMany(CustomAdPayout::class, 'deal_id');
    }

    public function remainingReservedPts(): float
    {
        return max(0, (float) $this->reserved_pts - (float) $this->paid_pts - (float) $this->refunded_pts);
    }

    public function durationDays(): int
    {
        if (!$this->starts_at || !$this->ends_at) {
            return 1;
        }

        return max(1, $this->starts_at->copy()->startOfDay()->diffInDays($this->ends_at->copy()->startOfDay()) + 1);
    }

    public function ctr(): float
    {
        return (int) $this->impressions > 0
            ? round(((int) $this->clicks / (int) $this->impressions) * 100, 2)
            : 0.0;
    }

    public function canBeAcceptedBy(User $user): bool
    {
        if (!in_array((string) $this->status, [self::STATUS_PENDING, self::STATUS_INVITED], true)) {
            return false;
        }

        if ((string) $this->source === self::SOURCE_INVITE) {
            return (int) $this->advertiser_id === (int) $user->id;
        }

        return (int) $this->publisher_id === (int) $user->id;
    }

    public function canBeManagedBy(User $user): bool
    {
        return (int) $this->publisher_id === (int) $user->id
            || (int) $this->advertiser_id === (int) $user->id;
    }
}
