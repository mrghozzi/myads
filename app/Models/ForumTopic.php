<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumTopic extends Model
{
    use HasFactory;

    protected $table = 'forum';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'name',
        'txt',
        'cat',
        'statu',
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
}
