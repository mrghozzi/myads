<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumComment extends Model
{
    use HasFactory;

    protected $table = 'f_coment';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'tid',
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
}
