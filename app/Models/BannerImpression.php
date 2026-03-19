<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BannerImpression extends Model
{
    use HasFactory;

    protected $table = 'banner_impressions';
    public $timestamps = false;

    protected $fillable = [
        'banner_id',
        'publisher_id',
        'visitor_key',
        'served_at',
    ];
}
