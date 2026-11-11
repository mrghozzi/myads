<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_new_follower',
        'email_new_comment',
        'email_new_message',
        'email_mention',
        'email_repost',
        'email_reaction',
        'email_forum_reply',
        'email_marketplace_update',
        'push_new_follower',
        'push_new_comment',
        'push_new_message',
        'push_mention',
        'push_reaction',
        'push_forum_reply',
    ];

    protected $casts = [
        'email_new_follower' => 'boolean',
        'email_new_comment' => 'boolean',
        'email_new_message' => 'boolean',
        'email_mention' => 'boolean',
        'email_repost' => 'boolean',
        'email_reaction' => 'boolean',
        'email_forum_reply' => 'boolean',
        'email_marketplace_update' => 'boolean',
        'push_new_follower' => 'boolean',
        'push_new_comment' => 'boolean',
        'push_new_message' => 'boolean',
        'push_mention' => 'boolean',
        'push_reaction' => 'boolean',
        'push_forum_reply' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
