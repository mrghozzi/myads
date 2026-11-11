<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\ForumTopic;
use App\Models\Option;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupMembershipService
{
    public function __construct(
        protected GroupAccessService $access,
        protected NotificationService $notifications
    ) {
    }

    public function createGroup(User $owner, array $attributes, string $initialStatus = Group::STATUS_ACTIVE): Group
    {
        return DB::transaction(function () use ($owner, $attributes, $initialStatus) {
            $group = Group::create([
                'owner_id' => $owner->id,
                'slug' => $this->uniqueSlug((string) ($attributes['slug'] ?? $attributes['name'] ?? 'group')),
                'name' => trim((string) ($attributes['name'] ?? '')),
                'category' => (string) ($attributes['category'] ?? 'general'),
                'short_description' => trim((string) ($attributes['short_description'] ?? '')),
                'description' => trim((string) ($attributes['description'] ?? '')),
                'rules_markdown' => trim((string) ($attributes['rules_markdown'] ?? '')),
                'privacy' => (string) ($attributes['privacy'] ?? Group::PRIVACY_PUBLIC),
                'posting_permission' => (string) ($attributes['posting_permission'] ?? Group::POSTING_ALL),
                'join_fee_pts' => (int) ($attributes['join_fee_pts'] ?? 0),
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
        if ($this->access->isBanned($group, $user)) {
            throw new \RuntimeException(__('messages.groups_user_is_banned'));
        }

        return DB::transaction(function () use ($group, $user) {
            $membership = GroupMembership::query()
                ->firstOrNew([
                    'group_id' => $group->id,
                    'user_id' => $user->id,
                ]);

            if ($membership->status === GroupMembership::STATUS_ACTIVE) {
                return $membership;
            }

            // Check PTS join fee
            $joinFee = (int) ($group->join_fee_pts ?? 0);
            if ($joinFee > 0 && (int) $group->owner_id !== (int) $user->id) {
                if ((int) $user->pts < $joinFee) {
                    throw new \RuntimeException(__('messages.groups_insufficient_pts_for_join', ['pts' => $joinFee]));
                }

                $user->decrement('pts', $joinFee);
                $owner = User::find($group->owner_id);
                if ($owner) {
                    $owner->increment('pts', $joinFee);
                }

                Option::create([
                    'name' => 'GroupJoin',
                    'o_valuer' => "-$joinFee",
                    'o_type' => 'hest_pts',
                    'o_parent' => $user->id,
                    'o_order' => $group->id,
                    'o_mode' => time(),
                ]);

                if ($owner) {
                    Option::create([
                        'name' => 'GroupJoin',
                        'o_valuer' => "$joinFee",
                        'o_type' => 'hest_pts',
                        'o_parent' => $owner->id,
                        'o_order' => $user->id,
                        'o_mode' => time(),
                    ]);
                }
            }

            $now = now();
            $membership->requested_at = $now;
            $membership->rejected_at = null;
            $membership->rejected_by = null;
            $membership->banned_at = null;
            $membership->banned_by = null;

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

        $this->notifications->send(
            $membership->user_id,
            __('messages.groups_notification_role_updated', [
                'group' => $membership->group->name,
                'role' => __('messages.groups_role_' . $role)
            ]),
            route('groups.show', $membership->group)
        );

        return $membership->fresh(['group', 'user']);
    }

    public function transferOwnership(Group $group, User $newOwner, User $actor): void
    {
        if (!$this->access->isOwner($group, $actor) && !$actor->isAdmin()) {
            abort(403);
        }

        $newOwnerMembership = $this->access->membership($group, $newOwner);
        if (!$newOwnerMembership || $newOwnerMembership->status !== GroupMembership::STATUS_ACTIVE) {
            throw new \RuntimeException(__('messages.groups_transfer_target_not_member'));
        }

        DB::transaction(function () use ($group, $newOwner) {
            // Demote old owner to member
            GroupMembership::query()
                ->where('group_id', $group->id)
                ->where('user_id', $group->owner_id)
                ->update(['role' => GroupMembership::ROLE_MEMBER]);

            // Promote new owner
            GroupMembership::query()
                ->where('group_id', $group->id)
                ->where('user_id', $newOwner->id)
                ->update(['role' => GroupMembership::ROLE_OWNER]);

            // Update group record
            $group->update(['owner_id' => $newOwner->id]);
        });

        $this->notifications->send(
            $newOwner->id,
            __('messages.groups_notification_ownership_transferred', ['group' => $group->name]),
            route('groups.show', $group)
        );
    }

    public function ban(GroupMembership $membership, User $actor): GroupMembership
    {
        $membership->loadMissing('group');

        if (!$this->access->canBanMember($membership->group, $actor, $membership)) {
            abort(403);
        }

        $membership->forceFill([
            'status' => GroupMembership::STATUS_BANNED,
            'banned_at' => now(),
            'banned_by' => $actor->id,
            'approved_at' => null,
            'approved_by' => null,
        ])->save();

        $this->syncCounts($membership->group);

        $this->notifications->send(
            $membership->user_id,
            __('messages.groups_notification_banned', ['group' => $membership->group->name]),
            route('groups.index')
        );

        return $membership->fresh(['group', 'user']);
    }

    public function unban(GroupMembership $membership, User $actor): void
    {
        $membership->loadMissing('group');

        if (!$this->access->canManageGroup($membership->group, $actor)) {
            abort(403);
        }

        $membership->delete();
        $this->syncCounts($membership->group);
    }

    public function togglePinPost(Group $group, int $statusId, User $actor): bool
    {
        if (!$this->access->canPinPost($group, $actor)) {
            abort(403);
        }

        $status = Status::where('id', $statusId)->where('group_id', $group->id)->firstOrFail();

        $isPinnedNow = false;
        if ((int) $group->pinned_post_id === (int) $status->id) {
            $group->update(['pinned_post_id' => null]);
            $isPinnedNow = false;
        } else {
            $group->update(['pinned_post_id' => $status->id]);
            $isPinnedNow = true;
        }

        return $isPinnedNow;
    }

    public function deleteGroup(Group $group, User $actor): void
    {
        if (!$actor->isAdmin() && !$this->access->isOwner($group, $actor)) {
            abort(403);
        }

        DB::transaction(function () use ($group) {
            if ($group->avatar_path && file_exists(base_path($group->avatar_path)) && !str_contains($group->avatar_path, 'group.png')) {
                @unlink(base_path($group->avatar_path));
            }
            if ($group->cover_path && file_exists(base_path($group->cover_path)) && !str_contains($group->cover_path, 'cover')) {
                @unlink(base_path($group->cover_path));
            }

            Status::where('group_id', $group->id)->update(['group_id' => null]);
            ForumTopic::where('group_id', $group->id)->update(['group_id' => null]);
            GroupMembership::where('group_id', $group->id)->delete();
            $group->delete();
        });
    }

    public function saveGroupImage(\Illuminate\Http\UploadedFile $file, string $prefix, int $groupId, ?string $oldPath = null): string
    {
        $extension = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'jpg');
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            $extension = 'jpg';
        }

        $filename = "group_{$prefix}_{$groupId}_" . time() . '_' . Str::random(8) . '.' . $extension;
        $file->move(base_path('upload'), $filename);

        if ($oldPath && file_exists(base_path($oldPath)) && !str_contains($oldPath, 'group.png') && !str_contains($oldPath, 'cover')) {
            @unlink(base_path($oldPath));
        }

        return 'upload/' . $filename;
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
