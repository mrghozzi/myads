<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YtVideo extends Model
{
    use HasFactory;

    protected $table = 'yt_videos';

    protected $fillable = [
        'user_id',
        'youtube_id',
        'title',
        'thumbnail_url',
        'duration_required',
        'reward_points',
        'total_budget',
        'remaining_budget',
        'status',
    ];

    protected $casts = [
        'duration_required' => 'integer',
        'reward_points' => 'decimal:4',
        'total_budget' => 'decimal:4',
        'remaining_budget' => 'decimal:4',
    ];

    /**
     * Get the user that owns the video campaign.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the view logs for the video.
     */
    public function viewLogs()
    {
        return $this->hasMany(YtViewLog::class, 'video_id');
    }
}
