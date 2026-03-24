<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusMention extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mentioned_user_id',
        'status_id',
        'comment_type',
        'comment_id',
        'username',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mentionedUser()
    {
        return $this->belongsTo(User::class, 'mentioned_user_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}
