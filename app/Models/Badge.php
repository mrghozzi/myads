<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name_key',
        'description_key',
        'icon',
        'color',
        'points_reward',
        'criteria_type',
        'criteria_target',
        'sort_order',
        'is_active',
        'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
    ];

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class, 'badge_id');
    }
}
