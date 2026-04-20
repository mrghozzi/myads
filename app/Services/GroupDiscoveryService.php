<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\Like;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class GroupDiscoveryService
{
    public function __construct(
        private readonly GroupAccessService $access
    ) {
    }

    public function myGroups(?User $user): Collection
    {
        if (!$user || !$this->access->featureEnabled()) {
            return collect();
        }

        return Group::query()
            ->with(['owner'])
            ->whereIn('id', $this->access->activeMembershipGroupIdsFor($user))
            ->orderByDesc('last_activity_at')
            ->orderByDesc('members_count')
            ->limit(8)
            ->get();
    }

    public function discover(?User $user, string $search = '', int $perPage = 12): LengthAwarePaginator
    {
        $search = trim($search);
        $followingIds = $user
            ? Like::query()
                ->where('uid', $user->id)
                ->where('type', 1)
                ->pluck('sid')
                ->map(fn ($id) => (int) $id)
                ->all()
            : [];

        $query = Group::query()
            ->with(['owner'])
            ->withCount([
                'memberships as active_followed_members_count' => function ($membershipQuery) use ($followingIds) {
                    $membershipQuery
                        ->where('status', GroupMembership::STATUS_ACTIVE)
                        ->when(
                            $followingIds !== [],
                            fn ($query) => $query->whereIn('user_id', $followingIds),
                            fn ($query) => $query->whereRaw('1 = 0')
                        );
                },
            ]);

        if ($user?->isAdmin()) {
            // Admin can review every group.
        } elseif ($user) {
            $memberGroupIds = $this->access->activeMembershipGroupIdsFor($user);
            $query->where(function ($visibilityQuery) use ($memberGroupIds) {
                $visibilityQuery
                    ->where(function ($publicQuery) {
                        $publicQuery
                            ->where('status', Group::STATUS_ACTIVE)
                            ->where('privacy', Group::PRIVACY_PUBLIC);
                    });

                if ($memberGroupIds !== []) {
                    $visibilityQuery->orWhereIn('id', $memberGroupIds);
                }
            });
        } else {
            $query
                ->where('status', Group::STATUS_ACTIVE)
                ->where('privacy', Group::PRIVACY_PUBLIC);
        }

        if ($search !== '') {
            $query->where(function ($searchQuery) use ($search) {
                $searchQuery
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('short_description', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        return $query
            ->orderByDesc('is_featured')
            ->orderByDesc('active_followed_members_count')
            ->orderByDesc('last_activity_at')
            ->orderByDesc('members_count')
            ->paginate($perPage)
            ->withQueryString();
    }
}
