<?php

namespace App\Services;

use App\Models\ForumTopic;
use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\MemberSubscription;
use App\Models\User;
use App\Support\GroupSettings;
use App\Support\SubscriptionSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GroupAccessService
{
    public function __construct(
        private readonly V420SchemaService $schema
    ) {
    }

    public function featureEnabled(): bool
    {
        return $this->schema->supports('groups') && GroupSettings::isEnabled();
    }

    public function membership(?Group $group, ?User $user): ?GroupMembership
    {
        if (!$group || !$user || !$this->featureEnabled()) {
            return null;
        }

        if ($group->relationLoaded('memberships')) {
            return $group->memberships->firstWhere('user_id', $user->id);
        }

        return GroupMembership::query()
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->first();
    }

    public function hasActiveMembership(?Group $group, ?User $user): bool
    {
        $membership = $this->membership($group, $user);

        return (bool) ($membership && $membership->status === GroupMembership::STATUS_ACTIVE);
    }

    public function isOwner(?Group $group, ?User $user): bool
    {
        if (!$group || !$user) {
            return false;
        }

        return (int) $group->owner_id === (int) $user->id;
    }

    public function isModerator(?Group $group, ?User $user): bool
    {
        $membership = $this->membership($group, $user);

        return (bool) ($membership
            && $membership->status === GroupMembership::STATUS_ACTIVE
            && in_array($membership->role, [GroupMembership::ROLE_OWNER, GroupMembership::ROLE_MODERATOR], true));
    }

    public function canManageGroup(?Group $group, ?User $user): bool
    {
        if (!$group || !$user) {
            return false;
        }

        return $user->isAdmin() || $this->isModerator($group, $user);
    }

    public function canChangeRoles(?Group $group, ?User $user): bool
    {
        if (!$group || !$user) {
            return false;
        }

        return $user->isAdmin() || $this->isOwner($group, $user);
    }

    public function canViewGroupShell(Group $group, ?User $user): bool
    {
        if (!$this->featureEnabled()) {
            return false;
        }

        if ($user?->isAdmin()) {
            return true;
        }

        if ((string) $group->status === Group::STATUS_ACTIVE) {
            return true;
        }

        return $this->isOwner($group, $user);
    }

    public function canViewGroupContent(Group $group, ?User $user): bool
    {
        if (!$this->featureEnabled()) {
            return false;
        }

        if ($user?->isAdmin()) {
            return true;
        }

        if ((string) $group->status !== Group::STATUS_ACTIVE) {
            return $this->isOwner($group, $user);
        }

        if ((string) $group->privacy === Group::PRIVACY_PUBLIC) {
            return true;
        }

        return $this->hasActiveMembership($group, $user);
    }

    public function canPostToGroup(Group $group, ?User $user): bool
    {
        if (!$user || !$this->featureEnabled()) {
            return false;
        }

        if ($user->isAdmin()) {
            return (string) $group->status === Group::STATUS_ACTIVE;
        }

        return (string) $group->status === Group::STATUS_ACTIVE
            && $this->hasActiveMembership($group, $user);
    }

    public function ensureCanViewGroupShell(Group $group, ?User $user): void
    {
        if (!$this->canViewGroupShell($group, $user)) {
            abort(404);
        }
    }

    public function ensureCanViewGroupContent(Group $group, ?User $user): void
    {
        if (!$this->canViewGroupContent($group, $user)) {
            abort(403);
        }
    }

    public function ensureCanPostToGroup(Group $group, ?User $user): void
    {
        if (!$this->canPostToGroup($group, $user)) {
            abort(403);
        }
    }

    public function ensureCanInteractWithTopic(ForumTopic $topic, ?User $user): void
    {
        if (!(int) $topic->group_id) {
            return;
        }

        $topic->loadMissing('group');
        if (!$topic->group) {
            abort(404);
        }

        $this->ensureCanPostToGroup($topic->group, $user);
    }

    public function applyVisibleGroupScope(Builder $query, ?User $user, string $column = 'group_id', bool $includeUngrouped = true): Builder
    {
        if (!$this->featureEnabled() || !$this->schema->hasColumn($query->getModel()->getTable(), $column)) {
            return $query;
        }

        if ($user?->isAdmin()) {
            return $query;
        }

        $visibleGroupIds = $this->visibleGroupIdsFor($user);

        return $query->where(function (Builder $visibilityQuery) use ($column, $includeUngrouped, $visibleGroupIds) {
            if ($includeUngrouped) {
                $visibilityQuery->whereNull($column);
            }

            if ($visibleGroupIds !== []) {
                $visibilityQuery->orWhereIn($column, $visibleGroupIds);
            }
        });
    }

    public function applyMyGroupsScope(Builder $query, User $user, string $column = 'group_id'): Builder
    {
        if (!$this->featureEnabled() || !$this->schema->hasColumn($query->getModel()->getTable(), $column)) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdmin()) {
            return $query->whereNotNull($column);
        }

        $groupIds = $this->activeMembershipGroupIdsFor($user);
        if ($groupIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($column, $groupIds);
    }

    public function visibleGroupIdsFor(?User $user): array
    {
        if (!$this->featureEnabled()) {
            return [];
        }

        $publicGroupIds = Group::query()
            ->where('status', Group::STATUS_ACTIVE)
            ->where('privacy', Group::PRIVACY_PUBLIC)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (!$user || $user->isAdmin()) {
            return $publicGroupIds;
        }

        return array_values(array_unique(array_merge(
            $publicGroupIds,
            $this->activeMembershipGroupIdsFor($user)
        )));
    }

    public function activeMembershipGroupIdsFor(User $user): array
    {
        if (!$this->featureEnabled()) {
            return [];
        }

        return GroupMembership::query()
            ->where('user_id', $user->id)
            ->where('status', GroupMembership::STATUS_ACTIVE)
            ->pluck('group_id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function creationEligibility(User $user): array
    {
        $settings = GroupSettings::all();
        $policy = (string) ($settings['creation_policy'] ?? GroupSettings::POLICY_APPROVAL);

        if (!$this->featureEnabled()) {
            return [
                'allowed' => false,
                'policy' => $policy,
                'initial_status' => Group::STATUS_PENDING_REVIEW,
                'subscription' => null,
                'message' => __('messages.groups_feature_disabled'),
            ];
        }

        if ($policy === GroupSettings::POLICY_ALL_MEMBERS) {
            return [
                'allowed' => true,
                'policy' => $policy,
                'initial_status' => Group::STATUS_ACTIVE,
                'subscription' => null,
                'message' => null,
            ];
        }

        if ($policy === GroupSettings::POLICY_APPROVAL) {
            return [
                'allowed' => true,
                'policy' => $policy,
                'initial_status' => Group::STATUS_PENDING_REVIEW,
                'subscription' => null,
                'message' => null,
            ];
        }

        if (!$this->schema->supports('subscriptions_billing') || !SubscriptionSettings::isEnabled()) {
            return [
                'allowed' => false,
                'policy' => $policy,
                'initial_status' => Group::STATUS_ACTIVE,
                'subscription' => null,
                'message' => __('messages.groups_paid_plan_unavailable'),
            ];
        }

        $subscription = MemberSubscription::query()
            ->where('user_id', $user->id)
            ->where('status', MemberSubscription::STATUS_ACTIVE)
            ->latest('ends_at')
            ->first();

        if (!$subscription) {
            return [
                'allowed' => false,
                'policy' => $policy,
                'initial_status' => Group::STATUS_ACTIVE,
                'subscription' => null,
                'message' => __('messages.groups_paid_plan_required'),
            ];
        }

        $eligiblePlanIds = (array) ($settings['eligible_plan_ids'] ?? []);
        if ($eligiblePlanIds !== [] && !in_array((int) $subscription->subscription_plan_id, $eligiblePlanIds, true)) {
            return [
                'allowed' => false,
                'policy' => $policy,
                'initial_status' => Group::STATUS_ACTIVE,
                'subscription' => $subscription,
                'message' => __('messages.groups_paid_plan_required'),
            ];
        }

        return [
            'allowed' => true,
            'policy' => $policy,
            'initial_status' => Group::STATUS_ACTIVE,
            'subscription' => $subscription,
            'message' => null,
        ];
    }

    public function pendingRequestCountFor(Group $group): int
    {
        if (!$this->featureEnabled()) {
            return 0;
        }

        return GroupMembership::query()
            ->where('group_id', $group->id)
            ->where('status', GroupMembership::STATUS_PENDING)
            ->count();
    }

    public function publicIndexableGroups(): Collection
    {
        if (!$this->featureEnabled()) {
            return collect();
        }

        return Group::query()
            ->where('status', Group::STATUS_ACTIVE)
            ->where('privacy', Group::PRIVACY_PUBLIC)
            ->get();
    }
}
