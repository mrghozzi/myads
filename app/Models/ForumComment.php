<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasPrivacy;

class ForumComment extends Model
{
    use HasFactory, HasPrivacy;

    protected $table = 'f_coment';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'tid',
        'parent_id',
        'txt',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function topic()
    {
        return $this->belongsTo(ForumTopic::class, 'tid');
    }

    public function parent()
    {
        return $this->belongsTo(ForumComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(ForumComment::class, 'parent_id')->orderBy('id', 'asc');
    }
}

