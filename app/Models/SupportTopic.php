<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTopic extends Model
{
    use HasFactory;

    protected $table = 'support_topics';

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'content',
        'status',
        'is_pinned',
        'is_locked',
        'views_count',
        'replies_count',
        'best_reply_id',
        'last_reply_at',
        'last_reply_user_id',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'views_count' => 'integer',
        'replies_count' => 'integer',
        'last_reply_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(SupportCategory::class, 'category_id');
    }

    public function replies()
    {
        return $this->hasMany(SupportReply::class, 'topic_id')->orderBy('is_solution', 'desc')->orderBy('created_at', 'asc');
    }

    public function bestReply()
    {
        return $this->belongsTo(SupportReply::class, 'best_reply_id');
    }

    public function lastReplyUser()
    {
        return $this->belongsTo(User::class, 'last_reply_user_id');
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved' || !is_null($this->best_reply_id);
    }
}
