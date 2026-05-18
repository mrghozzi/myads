<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomAdEvent extends Model
{
    use HasFactory;

    public const TYPE_IMPRESSION = 'impression';
    public const TYPE_CLICK = 'click';

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
        'occurred_at',
    ];

    protected $casts = [
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
