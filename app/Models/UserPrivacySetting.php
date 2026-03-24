<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPrivacySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'profile_visibility',
        'about_visibility',
        'photos_visibility',
        'followers_visibility',
        'following_visibility',
        'points_history_visibility',
        'allow_direct_messages',
        'allow_mentions',
        'allow_reposts',
        'show_online_status',
    ];

    protected $casts = [
        'allow_direct_messages' => 'boolean',
        'allow_mentions' => 'boolean',
        'allow_reposts' => 'boolean',
        'show_online_status' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
