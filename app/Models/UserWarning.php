<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWarning extends Model
{
    use HasFactory;

    protected $table = 'user_warnings';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'moderator_id',
        'reason',
        'details',
        'points_deducted',
        'strike_level',
        'created_at',
    ];

    protected $casts = [
        'points_deducted' => 'integer',
        'strike_level' => 'integer',
        'created_at' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }
}
