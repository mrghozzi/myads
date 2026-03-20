<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'email',
        'pass',
        'img',
        'ucheck',
        'online',
        // 'active', // Column 'active' not found in DB
        'pts',
        'vu',
        'nvu',
        'nlink',
        'nsmart',
        'sig',
        'email_verified_at',
    ];

    protected $hidden = [
        'pass',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->pass;
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    // Accessors for compatibility
    public function getNameAttribute()
    {
        return $this->username;
    }

    public function getAvatarAttribute()
    {
        return $this->img;
    }
    
    public function isAdmin()
    {
        return $this->id == 1;
    }

    public function isOnline()
    {
        return $this->online > (time() - 240);
    }

    public function forumRoleLabel(?int $categoryId = null): string
    {
        if ($this->isAdmin()) {
            return __('messages.forum_role_admin');
        }

        $moderator = $this->getActiveForumModerator();
        if (!$moderator) {
            return __('messages.forum_role_member');
        }

        if ($moderator->is_global) {
            return __('messages.forum_role_global_moderator');
        }

        if ($categoryId !== null) {
            return $moderator->categories->contains('id', $categoryId)
                ? __('messages.forum_role_section_moderator')
                : __('messages.forum_role_member');
        }

        return $moderator->categories->isNotEmpty()
            ? __('messages.forum_role_section_moderator')
            : __('messages.forum_role_member');
    }

    public function canModerateForum(?string $permission = null, ?int $categoryId = null): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $moderator = $this->getActiveForumModerator();
        if (!$moderator) {
            return false;
        }

        if ($permission !== null && !$moderator->hasPermission($permission)) {
            return false;
        }

        if ($moderator->is_global) {
            return true;
        }

        if ($categoryId === null) {
            return true;
        }

        return $moderator->categories->contains('id', $categoryId);
    }

    private function getActiveForumModerator(): ?ForumModerator
    {
        $moderator = $this->relationLoaded('forumModerator')
            ? $this->getRelation('forumModerator')
            : $this->forumModerator()->first();

        if (!$moderator || !$moderator->is_active) {
            return null;
        }

        if (!$moderator->relationLoaded('categories')) {
            $moderator->load('categories:id');
        }

        return $moderator;
    }

    // Relationships
    public function topics()
    {
        return $this->hasMany(ForumTopic::class, 'uid');
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class, 'uid');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'o_parent');
    }

    public function listings()
    {
        return $this->hasMany(Directory::class, 'uid');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class, 'uid');
    }

    public function smartAds()
    {
        return $this->hasMany(SmartAd::class, 'uid');
    }

    public function forumModerator()
    {
        return $this->hasOne(ForumModerator::class, 'user_id');
    }
}
