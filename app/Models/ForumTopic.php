<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasPrivacy;

class ForumTopic extends Model
{
    use HasFactory, HasPrivacy;

    protected $table = 'forum';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'name',
        'txt',
        'cat',
        'statu',
        'date',
        'reply',
        'vu',
        'is_pinned',
        'pinned_at',
        'pinned_by',
        'is_locked',
        'locked_at',
        'locked_by',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'pinned_at' => 'integer',
        'locked_at' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function category()
    {
        return $this->belongsTo(ForumCategory::class, 'cat');
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class, 'tid');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'sid')->where('type', 2);
    }

    public function imageOption()
    {
        return $this->hasOne(Option::class, 'o_parent', 'id')->where('o_type', 'image_post');
    }

    public function getImageUrlAttribute()
    {
        return $this->imageOption ? $this->imageOption->o_valuer : null;
    }

    public function pinnedBy()
    {
        return $this->belongsTo(User::class, 'pinned_by');
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function attachments()
    {
        return $this->hasMany(ForumAttachment::class, 'topic_id')->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }
}
