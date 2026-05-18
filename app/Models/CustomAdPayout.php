<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomAdPayout extends Model
{
    use HasFactory;

    public const TYPE_DAILY = 'daily';
    public const TYPE_REFUND = 'refund';

    protected $fillable = [
        'deal_id',
        'publisher_id',
        'advertiser_id',
        'type',
        'amount',
        'payout_date',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payout_date' => 'date',
        'meta' => 'array',
    ];

    public function deal()
    {
        return $this->belongsTo(CustomAdDeal::class, 'deal_id');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'publisher_id');
    }

    public function advertiser()
    {
        return $this->belongsTo(User::class, 'advertiser_id');
    }
}
