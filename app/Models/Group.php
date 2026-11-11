<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    public const PRIVACY_PUBLIC = 'public';
    public const PRIVACY_PRIVATE = 'private_request';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_REJECTED = 'rejected';

    public const POSTING_ALL = 'all_members';
    public const POSTING_MODERATORS = 'moderators_only';

    public const CATEGORIES = [
        'general' => 'messages.groups_cat_general',
        'technology' => 'messages.groups_cat_technology',
        'business' => 'messages.groups_cat_business',
        'programming' => 'messages.groups_cat_programming',
        'marketing' => 'messages.groups_cat_marketing',
        'design' => 'messages.groups_cat_design',
        'gaming' => 'messages.groups_cat_gaming',
        'trading' => 'messages.groups_cat_trading',
    ];

    public static function categoryLabel(?string $category): string
    {
        $key = self::CATEGORIES[$category] ?? 'messages.groups_cat_general';
        return str_starts_with($key, 'messages.') ? __($key) : __('messages.' . $key);
    }

    protected $fillable = [
        'owner_id',
        'slug',
        'name',
        'category',
        'short_description',
        'description',
        'rules_markdown',
        'privacy',
        'posting_permission',
        'join_fee_pts',
        'status',
        'avatar_path',
        'cover_path',
        'is_featured',
        'members_count',
        'posts_count',
        'pinned_post_id',
        'last_activity_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'members_count' => 'integer',
        'posts_count' => 'integer',
        'join_fee_pts' => 'integer',
        'pinned_post_id' => 'integer',
        'last_activity_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function memberships()
    {
        return $this->hasMany(GroupMembership::class, 'group_id');
    }

    public function activeMemberships()
    {
        return $this->memberships()->where('status', GroupMembership::STATUS_ACTIVE);
    }

    public function pendingMemberships()
    {
        return $this->memberships()->where('status', GroupMembership::STATUS_PENDING);
    }

    public function bannedMemberships()
    {
        return $this->memberships()->where('status', GroupMembership::STATUS_BANNED);
    }

    public function pinnedPost()
    {
        return $this->belongsTo(Status::class, 'pinned_post_id');
    }

    public function topics()
    {
        return $this->hasMany(ForumTopic::class, 'group_id');
    }

    public function statuses()
    {
        return $this->hasMany(Status::class, 'group_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePublic($query)
    {
        return $query->where('privacy', self::PRIVACY_PUBLIC);
    }

    public function isPublic(): bool
    {
        return (string) $this->privacy === self::PRIVACY_PUBLIC;
    }

    public function avatarUrl(): string
    {
        if ($this->avatar_path) {
            return asset($this->avatar_path);
        }
        return asset('upload/group.png');
    }

    public function coverUrl(): string
    {
        if ($this->cover_path) {
            return asset($this->cover_path);
        }
        return asset('themes/default/assets/img/cover/01.jpg');
    }
}
