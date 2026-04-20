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

    protected $fillable = [
        'owner_id',
        'slug',
        'name',
        'short_description',
        'description',
        'rules_markdown',
        'privacy',
        'status',
        'avatar_path',
        'cover_path',
        'is_featured',
        'members_count',
        'posts_count',
        'last_activity_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'members_count' => 'integer',
        'posts_count' => 'integer',
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
}
