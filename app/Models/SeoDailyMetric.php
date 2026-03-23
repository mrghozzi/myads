<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoDailyMetric extends Model
{
    use HasFactory;

    protected $table = 'seo_daily_metrics';

    protected $fillable = [
        'metric_date',
        'scope_type',
        'scope_key',
        'content_type',
        'content_id',
        'page_views',
        'unique_visitors',
        'bot_hits',
        'status_404',
    ];

    protected $casts = [
        'metric_date' => 'date',
    ];
}
