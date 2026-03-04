<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumModerator extends Model
{
    use HasFactory;

    protected $table = 'forum_moderators';

    protected $fillable = [
        'user_id',
        'is_global',
        'permissions',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_global' => 'boolean',
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories()
    {
        return $this->belongsToMany(ForumCategory::class, 'forum_moderator_categories', 'moderator_id', 'category_id');
    }

    public function hasPermission(string $permission): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions, true);
    }
}