<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YtViewLog extends Model
{
    use HasFactory;

    protected $table = 'yt_views_log';
    
    public $timestamps = false; // We only have watched_at

    protected $fillable = [
        'user_id',
        'video_id',
        'ip_address',
        'watched_at',
    ];

    protected $casts = [
        'watched_at' => 'datetime',
    ];

    /**
     * Get the user who watched the video.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the video that was watched.
     */
    public function video()
    {
        return $this->belongsTo(YtVideo::class, 'video_id');
    }
}
