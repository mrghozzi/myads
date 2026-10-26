<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportReply extends Model
{
    use HasFactory;

    protected $table = 'support_replies';

    protected $fillable = [
        'topic_id',
        'user_id',
        'content',
        'is_staff',
        'is_solution',
    ];

    protected $casts = [
        'is_staff' => 'boolean',
        'is_solution' => 'boolean',
    ];

    public function topic()
    {
        return $this->belongsTo(SupportTopic::class, 'topic_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
