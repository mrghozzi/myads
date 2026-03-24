<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasPrivacy;

class Like extends Model
{
    use HasFactory, HasPrivacy;

    protected $table = 'like';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'sid',
        'type', // 1=Follow User, 2=Forum Topic, 22=Directory Site
        'time_t',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'sid');
    }
}
