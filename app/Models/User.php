<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Services\AdminAccessService;
use App\Services\V420SchemaService;
use App\Support\SecuritySettings;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    private static ?bool $publicUidColumnAvailable = null;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'public_uid',
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

    protected static function booted(): void
    {
        static::creating(function (self $user): void {
            if (!self::supportsPublicUidColumn()) {
                return;
            }

            if (trim((string) $user->public_uid) !== '') {
                return;
            }

            $user->public_uid = self::generatePublicUid();
        });
    }

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

    public function avatarUrl(): string
    {
        if (!$this->img) {
            return asset('upload/avatar.png');
        }

        if (Str::startsWith($this->img, ['http://', 'https://'])) {
            return $this->img;
        }

        return asset($this->img);
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

    public function publicRouteIdentifier(): string
    {
        if ($this->usesPublicMemberIds() && trim((string) $this->public_uid) !== '') {
            return (string) $this->public_uid;
        }

        return (string) $this->getKey();
    }

    public function usesPublicMemberIds(): bool
    {
        return self::supportsPublicUidColumn()
            && (bool) SecuritySettings::get('public_member_ids_enabled', 0);
    }

    public static function resolvePublicIdentifier(string|int $identifier): ?self
    {
        $value = trim((string) $identifier);
        if ($value === '') {
            return null;
        }

        $publicEnabled = (bool) SecuritySettings::get('public_member_ids_enabled', 0);

        if (ctype_digit($value)) {
            if (!$publicEnabled) {
                // Only allow finding by numeric ID if public IDs are NOT enabled
                $user = self::find((int) $value);
                if ($user) {
                    return $user;
                }
            }
            // If public IDs are enabled, we do NOT allow resolving by numeric ID 
            // even if the ID exists. We fall through to check public_uid 
            // in case the public_uid itself is numeric.
        }

        if (!self::supportsPublicUidColumn()) {
            return null;
        }

        return self::where('public_uid', strtoupper($value))->first();
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

    public function billingOrders()
    {
        return $this->hasMany(BillingOrder::class, 'user_id');
    }

    public function memberSubscriptions()
    {
        return $this->hasMany(MemberSubscription::class, 'user_id');
    }

    public static function generatePublicUid(): string
    {
        do {
            $candidate = strtoupper(Str::random(12));
        } while (self::query()->where('public_uid', $candidate)->exists());

        return $candidate;
    }

    private static function supportsPublicUidColumn(): bool
    {
        if (self::$publicUidColumnAvailable !== null) {
            return self::$publicUidColumnAvailable;
        }

        return self::$publicUidColumnAvailable = app(V420SchemaService::class)->hasColumn('users', 'public_uid');
    }

    public function profileBadgeColor(): string
    {
        if (!\App\Support\SubscriptionSettings::isEnabled()) {
            return '#e7e8ee';
        }

        // For super-admin (ID=1), we can use a shorter cache or a manual override if needed
        $cacheTime = $this->id === 1 ? 5 : 60;

        return \Illuminate\Support\Facades\Cache::remember(
            "user_{$this->id}_profile_badge_color_v3",
            now()->addMinutes($cacheTime),
            function () {
                if (class_exists(\App\Services\Billing\SubscriptionEntitlementService::class)) {
                    $badge = app(\App\Services\Billing\SubscriptionEntitlementService::class)->activeProfileBadgeForUserId($this->id);
                    
                    if (!empty($badge['color'])) {
                        return $badge['color'];
                    }
                }

                // Fallback for ID=1 (Super Admin) if no plan is assigned
                if ($this->id === 1) {
                    return '#fbbf24'; // Premium Gold for Super Admin
                }

                return '';
            }
        );
    }
}
