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
        'is_pinned',
        'is_best_answer',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_best_answer' => 'boolean',
    ];

    public function isPinned(): bool
    {
        return (bool) $this->is_pinned;
    }

    public function isBestAnswer(): bool
    {
        return (bool) $this->is_best_answer;
    }

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

