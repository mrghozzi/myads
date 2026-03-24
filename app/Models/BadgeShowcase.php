<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BadgeShowcase extends Model
{
    use HasFactory;

    protected $table = 'badge_showcase';

    protected $fillable = [
        'user_id',
        'badge_id',
        'sort_order',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function badge()
    {
        return $this->belongsTo(Badge::class, 'badge_id');
    }
}
