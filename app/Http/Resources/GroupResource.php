<?php

namespace App\Http\Resources;

use App\Services\GroupAccessService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $access = app(GroupAccessService::class);
        $membership = $user ? $access->membership($this->resource, $user) : null;

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'category' => $this->category ?? 'general',
            'category_name' => __(\App\Models\Group::CATEGORIES[$this->category] ?? 'messages.groups_cat_general'),
            'short_description' => $this->short_description,
            'description' => $this->description,
            'rules_markdown' => $this->rules_markdown,
            'privacy' => $this->privacy,
            'posting_permission' => $this->posting_permission ?? 'all_members',
            'join_fee_pts' => (int) ($this->join_fee_pts ?? 0),
            'status' => $this->status,
            'avatar_url' => $this->avatarUrl(),
            'cover_url' => $this->coverUrl(),
            'is_featured' => (bool) $this->is_featured,
            'members_count' => (int) $this->members_count,
            'posts_count' => (int) $this->posts_count,
            'pinned_post_id' => $this->pinned_post_id,
            'owner' => new UserResource($this->whenLoaded('owner')),
            'membership' => $membership ? [
                'status' => $membership->status,
                'role' => $membership->role,
                'is_active' => $membership->status === \App\Models\GroupMembership::STATUS_ACTIVE,
                'is_pending' => $membership->status === \App\Models\GroupMembership::STATUS_PENDING,
                'is_banned' => $membership->status === \App\Models\GroupMembership::STATUS_BANNED,
                'is_moderator' => in_array($membership->role, [\App\Models\GroupMembership::ROLE_OWNER, \App\Models\GroupMembership::ROLE_MODERATOR], true),
                'is_owner' => $membership->role === \App\Models\GroupMembership::ROLE_OWNER,
            ] : null,
            'permissions' => [
                'can_view_content' => $access->canViewGroupContent($this->resource, $user),
                'can_post' => $access->canPostToGroup($this->resource, $user),
                'can_manage' => $access->canManageGroup($this->resource, $user),
            ],
            'created_at' => $this->created_at?->toIso8601String(),
            'last_activity_at' => $this->last_activity_at?->diffForHumans(),
        ];
    }
}

