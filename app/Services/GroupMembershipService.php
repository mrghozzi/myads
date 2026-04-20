<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupMembershipService
{
    public function __construct(
        private readonly GroupAccessService $access
    ) {
    }

    public function createGroup(User $owner, array $attributes, string $initialStatus = Group::STATUS_ACTIVE): Group
    {
        return DB::transaction(function () use ($owner, $attributes, $initialStatus) {
            $group = Group::create([
                'owner_id' => $owner->id,
                'slug' => $this->uniqueSlug((string) ($attributes['slug'] ?? $attributes['name'] ?? 'group')),
                'name' => trim((string) ($attributes['name'] ?? '')),
                'short_description' => trim((string) ($attributes['short_description'] ?? '')),
                'description' => trim((string) ($attributes['description'] ?? '')),
                'rules_markdown' => trim((string) ($attributes['rules_markdown'] ?? '')),
                'privacy' => (string) ($attributes['privacy'] ?? Group::PRIVACY_PUBLIC),
                'status' => $initialStatus,
                'avatar_path' => $attributes['avatar_path'] ?? null,
                'cover_path' => $attributes['cover_path'] ?? null,
                'is_featured' => !empty($attributes['is_featured']),
                'members_count' => 1,
                'posts_count' => 0,
                'last_activity_at' => now(),
            ]);

            GroupMembership::create([
                'group_id' => $group->id,
                'user_id' => $owner->id,
                'role' => GroupMembership::ROLE_OWNER,
                'status' => GroupMembership::STATUS_ACTIVE,
                'requested_at' => now(),
                'approved_at' => now(),
                'approved_by' => $owner->id,
            ]);

            return $group;
        });
    }

    public function join(Group $group, User $user): GroupMembership
    {
        return DB::transaction(function () use ($group, $user) {
            $membership = GroupMembership::query()
                ->firstOrNew([
                    'group_id' => $group->id,
                    'user_id' => $user->id,
                ]);

            $now = now();
            $membership->requested_at = $now;
            $membership->rejected_at = null;
            $membership->rejected_by = null;

            if ($group->privacy === Group::PRIVACY_PUBLIC) {
                $membership->role = $membership->role ?: GroupMembership::ROLE_MEMBER;
                $membership->status = GroupMembership::STATUS_ACTIVE;
                $membership->approved_at = $now;
                $membership->approved_by = $membership->approved_by ?: $group->owner_id;
            } else {
                $membership->role = $membership->role ?: GroupMembership::ROLE_MEMBER;
                $membership->status = GroupMembership::STATUS_PENDING;
                $membership->approved_at = null;
                $membership->approved_by = null;
            }

            $membership->save();
            $this->syncCounts($group);

            return $membership->fresh(['group', 'user']);
        });
    }

    public function leave(Group $group, User $user): void
    {
        if ((int) $group->owner_id === (int) $user->id) {
            throw new \RuntimeException(__('messages.groups_owner_cannot_leave'));
        }

        DB::transaction(function () use ($group, $user) {
            GroupMembership::query()
                ->where('group_id', $group->id)
                ->where('user_id', $user->id)
                ->delete();

            $this->syncCounts($group);
        });
    }

    public function approve(GroupMembership $membership, User $actor): GroupMembership
    {
        $membership->loadMissing('group');

        if (!$this->access->canManageGroup($membership->group, $actor)) {
            abort(403);
        }

        $membership->forceFill([
            'status' => GroupMembership::STATUS_ACTIVE,
            'approved_at' => now(),
            'approved_by' => $actor->id,
            'rejected_at' => null,
            'rejected_by' => null,
        ])->save();

        $this->syncCounts($membership->group);

        return $membership->fresh(['group', 'user']);
    }

    public function reject(GroupMembership $membership, User $actor): GroupMembership
    {
        $membership->loadMissing('group');

        if (!$this->access->canManageGroup($membership->group, $actor)) {
            abort(403);
        }

        $membership->forceFill([
            'status' => GroupMembership::STATUS_REJECTED,
            'approved_at' => null,
            'approved_by' => null,
            'rejected_at' => now(),
            'rejected_by' => $actor->id,
        ])->save();

        $this->syncCounts($membership->group);

        return $membership->fresh(['group', 'user']);
    }

    public function updateRole(GroupMembership $membership, string $role, User $actor): GroupMembership
    {
        $membership->loadMissing('group');

        if (!$this->access->canChangeRoles($membership->group, $actor)) {
            abort(403);
        }

        if ($membership->role === GroupMembership::ROLE_OWNER) {
            throw new \RuntimeException(__('messages.groups_owner_role_locked'));
        }

        if (!in_array($role, [GroupMembership::ROLE_MODERATOR, GroupMembership::ROLE_MEMBER], true)) {
            throw new \InvalidArgumentException('Invalid group role.');
        }

        $membership->update(['role' => $role]);

        return $membership->fresh(['group', 'user']);
    }

    public function syncCounts(Group $group): void
    {
        $schema = app(V420SchemaService::class);

        $membersCount = GroupMembership::query()
            ->where('group_id', $group->id)
            ->where('status', GroupMembership::STATUS_ACTIVE)
            ->count();

        $postsQuery = Status::query();
        if ($schema->hasColumn('status', 'group_id')) {
            $postsQuery->where('group_id', $group->id);
        } else {
            $postsQuery->whereRaw('1 = 0');
        }

        $postsCount = $postsQuery->count();
        $lastActivityTimestamp = (clone $postsQuery)->max('date');

        $group->forceFill([
            'members_count' => $membersCount,
            'posts_count' => $postsCount,
            'last_activity_at' => $lastActivityTimestamp
                ? Carbon::createFromTimestamp((int) $lastActivityTimestamp)
                : $group->last_activity_at,
        ])->save();
    }

    public function touchActivity(Group $group, ?int $timestamp = null): void
    {
        $group->forceFill([
            'last_activity_at' => Carbon::createFromTimestamp($timestamp ?: time()),
        ])->save();

        $this->syncCounts($group);
    }

    private function uniqueSlug(string $value): string
    {
        $base = Str::slug($value);
        if ($base === '') {
            $base = 'group';
        }

        $slug = $base;
        $suffix = 2;

        while (Group::query()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $suffix;
            $suffix++;
        }

        return $slug;
    }
}
