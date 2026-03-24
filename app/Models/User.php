<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Services\AdminAccessService;
use App\Services\V420SchemaService;

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
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!app(V420SchemaService::class)->supports('site_admins')) {
            return false;
        }

        $entry = $this->relationLoaded('siteAdminEntry')
            ? $this->getRelation('siteAdminEntry')
            : $this->siteAdminEntry()->first();

        if (!$entry || !$entry->is_active) {
            return false;
        }

        if ($entry->has_full_access) {
            return true;
        }

        return in_array('community', (array) $entry->permissions, true);
    }

    public function hasAdminAccess(): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!app(V420SchemaService::class)->supports('site_admins')) {
            return false;
        }

        $entry = $this->relationLoaded('siteAdminEntry')
            ? $this->getRelation('siteAdminEntry')
            : $this->siteAdminEntry()->first();

        return (bool) ($entry && $entry->is_active);
    }

    public function isSuperAdmin(): bool
    {
        if ((int) $this->id === 1) {
            return true;
        }

        if (!app(V420SchemaService::class)->supports('site_admins')) {
            return false;
        }

        $entry = $this->relationLoaded('siteAdminEntry')
            ? $this->getRelation('siteAdminEntry')
            : $this->siteAdminEntry()->first();

        return (bool) ($entry && $entry->is_active && $entry->is_super);
    }

    public function canAccessAdminModule(string $module): bool
    {
        return app(AdminAccessService::class)->canAccess($this, null, $module);
    }

    public function canManageAdministrators(): bool
    {
        return app(AdminAccessService::class)->canManageAdministrators($this);
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

    public function siteAdminEntry()
    {
        return $this->hasOne(SiteAdmin::class, 'user_id');
    }

    public function privacySetting()
    {
        return $this->hasOne(UserPrivacySetting::class, 'user_id');
    }

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class, 'user_id');
    }

    public function badgeShowcase()
    {
        return $this->hasMany(BadgeShowcase::class, 'user_id')->orderBy('sort_order');
    }
}
