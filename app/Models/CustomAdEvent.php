<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomAdEvent extends Model
{
    use HasFactory;

    public const TYPE_IMPRESSION = 'impression';
    public const TYPE_CLICK = 'click';

    public const REASON_RAPID_CLICK = 'RAPID_CLICK';
    public const REASON_HIDDEN_TAB = 'HIDDEN_TAB';
    public const REASON_BOT_FINGERPRINT = 'BOT_FINGERPRINT';
    public const REASON_UNVIEWABLE = 'UNVIEWABLE';
    public const REASON_DWELL_TOO_SHORT = 'DWELL_TOO_SHORT';
    public const REASON_RATE_LIMIT = 'RATE_LIMIT';
    public const REASON_HEADLESS = 'HEADLESS_BROWSER';
    public const REASON_REPLAY_ATTACK = 'REPLAY_ATTACK';

    public $timestamps = false;

    protected $fillable = [
        'placement_id',
        'deal_id',
        'creative_id',
        'publisher_id',
        'advertiser_id',
        'event_type',
        'visitor_key',
        'country_code',
        'device_type',
        'referrer',
        'ip_hash',
        'user_agent',
        'is_flagged',
        'flag_reason',
        'occurred_at',
    ];

    protected $casts = [
        'is_flagged' => 'boolean',
        'occurred_at' => 'datetime',
    ];

    public function placement()
    {
        return $this->belongsTo(CustomAdPlacement::class, 'placement_id');
    }

    public function deal()
    {
        return $this->belongsTo(CustomAdDeal::class, 'deal_id');
    }

    public function creative()
    {
        return $this->belongsTo(CustomAdCreative::class, 'creative_id');
    }
}
