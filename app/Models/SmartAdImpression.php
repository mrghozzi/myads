<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartAdImpression extends Model
{
    use HasFactory;

    protected $table = 'smart_ad_impressions';

    public $timestamps = false;

    protected $fillable = [
        'smart_ad_id',
        'publisher_id',
        'visitor_key',
        'country_code',
        'device_type',
        'placement',
        'served_at',
    ];
}
