<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ForumTopicResource;
use App\Http\Resources\GroupMembershipResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\StatusResource;
use App\Models\ForumTopic;
use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\Status;
use App\Services\GroupAccessService;
use App\Services\GroupDiscoveryService;
use App\Services\GroupMembershipService;
use App\Services\StatusActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GroupApiController extends Controller
{
    public function __construct(
        protected GroupAccessService $access,
        protected GroupMembershipService $memberships,
        protected GroupDiscoveryService $discovery
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $this->ensureFeatureEnabled();

        $search = trim((string) $request->query('search', ''));
        $category = (string) $request->query('category', 'all');
        $sort = (string) $request->query('sort', 'trending');
        $perPage = min(max((int) $request->query('per_page', 15), 1), 50);

        $groups = $this->discovery->discover($request->user(), $search, $category, $sort, $perPage);

        return response()->json([
            'success' => true,
            'data' => GroupResource::collection($groups),
            'meta' => [
                'current_page' => $groups->currentPage(),
                'last_page' => $groups->lastPage(),
                'total' => $groups->total(),
                'per_page' => $groups->perPage(),
                'categories' => collect(Group::CATEGORIES)->map(fn ($label, $key) => [
                    'key' => $key,
                    'name' => __($label),
                ])->values(),
            ],
        ]);
    }

    public function myGroups(Request $request): JsonResponse
    {
        $this->ensureFeatureEnabled();

        $user = $request->user();
        $groups = $this->discovery->myGroups($user);

        return response()->json([
            'success' => true,
            'data' => GroupResource::collection($groups),
        ]);
    }

    public function show(Request $request, string $identifier): JsonResponse
    {
        $this->ensureFeatureEnabled();

        $group = is_numeric($identifier)
            ? Group::with('owner')->findOrFail((int) $identifier)
            : Group::with('owner')->where('slug', $identifier)->firstOrFail();

        $this->access->ensureCanViewGroupShell($group, $request->user());

        return response()->json([
            'success' => true,
            'data' => new GroupResource($group),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->ensureFeatureEnabled();

        $user = $request->user();
        $eligibility = $this->access->creationEligibility($user);
        if (!$eligibility['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $eligibility['message'] ?: __('messages.groups_creation_denied'),
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => 'nullable|string|max:140|unique:groups,slug',
            'category' => 'nullable|string|max:64',
            'privacy' => 'required|in:public,private_request',
            'posting_permission' => 'required|in:all_members,moderators_only',
            'join_fee_pts' => 'nullable|integer|min:0|max:100000',
            'short_description' => 'nullable|string|max:280',
            'description' => 'nullable|string|max:10000',
            'rules_markdown' => 'nullable|string|max:15000',
        ]);

        $group = $this->memberships->createGroup(
            $user,
            $validated,
            (string) $eligibility['initial_status']
        );

        return response()->json([
            'success' => true,
            'message' => $group->status === Group::STATUS_PENDING_REVIEW
                ? __('messages.groups_created_pending_review')
                : __('messages.groups_created_success'),
            'data' => new GroupResource($group->load('owner')),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $this->ensureFeatureEnabled();

        $group = Group::findOrFail($id);
        $user = $request->user();

        if (!$this->access->canManageGroup($group, $user)) {
            return response()->json(['error' => __('messages.unauthorized')], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => 'nullable|string|max:140|unique:groups,slug,' . $group->id,
            'category' => 'nullable|string|max:64',
            'privacy' => 'required|in:public,private_request',
            'posting_permission' => 'required|in:all_members,moderators_only',
            'join_fee_pts' => 'nullable|integer|min:0|max:100000',
            'short_description' => 'nullable|string|max:280',
            'description' => 'nullable|string|max:10000',
            'rules_markdown' => 'nullable|string|max:15000',
        ]);

        $group->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('messages.groups_updated_success'),
            'data' => new GroupResource($group->fresh('owner')),
        ]);
    }

    public function join(Request $request, int $id): JsonResponse
    {
        $this->ensureFeatureEnabled();

        $group = Group::findOrFail($id);
        $user = $request->user();

        $this->access->ensureCanViewGroupShell($group, $user);

        if ($group->status !== Group::STATUS_ACTIVE) {
            return response()->json(['error' => __('messages.groups_join_blocked')], 422);
        }

        try {
            $membership = $this->memberships->join($group, $user);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        $message = $membership->status === GroupMembership::STATUS_ACTIVE
            ? __('messages.groups_joined_success')
            : __('messages.groups_join_request_sent');

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => new GroupMembershipResource($membership),
        ]);
    }

    public function leave(Request $request, int $id): JsonResponse
    {
        $this->ensureFeatureEnabled();

        $group = Group::findOrFail($id);

        try {
            $this->memberships->leave($group, $request->user());
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'success' => true,
            'message' => __('messages.groups_left_success'),
        ]);
    }

    public function feed(Request $request, int $id, StatusActivityService $activityService): JsonResponse
    {
        $this->ensureFeatureEnabled();

        $group = Group::findOrFail($id);
        $user = $request->user();

        $this->access->ensureCanViewGroupContent($group, $user);

        $perPage = min(max((int) $request->query('per_page', 15), 1), 30);

        $activities = Status::visible($user)
            ->with(['group', 'user'])
            ->where('group_id', $group->id)
            ->whereIn('s_type', [100, 4, 10, 11, 12, 13, 14])
            ->whereHas('forumTopic', function ($query) {
                $query->whereIn('name', ['text', 'link', 'gallery', 'video', 'audio', 'file', 'music', 'clips']);
            })
            ->orderBy('date', 'desc')
            ->paginate($perPage);

        $activityService->decorateMany($activities);

        return response()->json([
            'success' => true,
            'data' => StatusResource::collection($activities),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'total' => $activities->total(),
            ],
        ]);
    }

    public function discussions(Request $request, int $id): JsonResponse
    {
        $this->ensureFeatureEnabled();

        $group = Group::findOrFail($id);
        $user = $request->user();

        $this->access->ensureCanViewGroupContent($group, $user);

        $perPage = min(max((int) $request->query('per_page', 15), 1), 30);

        $topics = ForumTopic::visible($user)
            ->with(['user', 'group'])
            ->where('group_id', $group->id)
            ->whereNotIn('name', ['text', 'link', 'gallery', 'video', 'audio', 'file', 'music', 'clips', 'repost'])
            ->orderByDesc('is_pinned')
            ->orderBy('date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ForumTopicResource::collection($topics),
            'meta' => [
                'current_page' => $topics->currentPage(),
                'last_page' => $topics->lastPage(),
                'total' => $topics->total(),
            ],
        ]);
    }

    public function members(Request $request, int $id): JsonResponse
    {
        $this->ensureFeatureEnabled();

        $group = Group::findOrFail($id);
        $user = $request->user();

        $this->access->ensureCanViewGroupContent($group, $user);

        $status = (string) $request->query('status', GroupMembership::STATUS_ACTIVE);
        $canManage = $this->access->canManageGroup($group, $user);

        if (in_array($status, [GroupMembership::STATUS_PENDING, GroupMembership::STATUS_BANNED], true) && !$canManage) {
            return response()->json(['error' => __('messages.unauthorized')], 403);
        }

        $perPage = min(max((int) $request->query('per_page', 20), 1), 50);

        $members = GroupMembership::query()
            ->with(['user'])
            ->where('group_id', $group->id)
            ->where('status', $status)
            ->orderByRaw("CASE role WHEN 'owner' THEN 0 WHEN 'moderator' THEN 1 ELSE 2 END")
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => GroupMembershipResource::collection($members),
            'meta' => [
                'current_page' => $members->currentPage(),
                'last_page' => $members->lastPage(),
                'total' => $members->total(),
            ],
        ]);
    }

    public function approveMember(Request $request, int $id, int $membershipId): JsonResponse
    {
        $this->ensureFeatureEnabled();

        $group = Group::findOrFail($id);
        $membership = GroupMembership::where('id', $membershipId)->where('group_id', $group->id)->firstOrFail();

        $this->memberships->approve($membership, $request->user());

        return response()->json([
            'success' => true,
            'message' => __('messages.groups_member_approved'),
        ]);
    }

    public function rejectMember(Request $request, int $id, int $membershipId): JsonResponse
    {
        $this->ensureFeatureEnabled();

        $group = Group::findOrFail($id);
        $membership = GroupMembership::where('id', $membershipId)->where('group_id', $group->id)->firstOrFail();

        $this->memberships->reject($membership, $request->user());

        return response()->json([
            'success' => true,
            'message' => __('messages.groups_member_rejected'),
        ]);
    }

    public function banMember(Request $request, int $id, int $membershipId): JsonResponse
    {
        $this->ensureFeatureEnabled();

        $group = Group::findOrFail($id);
        $membership = GroupMembership::where('id', $membershipId)->where('group_id', $group->id)->firstOrFail();

        $this->memberships->ban($membership, $request->user());

        return response()->json([
            'success' => true,
            'message' => __('messages.groups_member_banned'),
        ]);
    }

    public function updateRole(Request $request, int $id, int $membershipId): JsonResponse
    {
        $this->ensureFeatureEnabled();

        $group = Group::findOrFail($id);
        $membership = GroupMembership::where('id', $membershipId)->where('group_id', $group->id)->firstOrFail();

        $validated = $request->validate([
            'role' => 'required|in:moderator,member',
        ]);

        $this->memberships->updateRole($membership, (string) $validated['role'], $request->user());

        return response()->json([
            'success' => true,
            'message' => __('messages.groups_role_updated'),
        ]);
    }

    public function togglePinPost(Request $request, int $id, int $statusId): JsonResponse
    {
        $this->ensureFeatureEnabled();

        $group = Group::findOrFail($id);
        $isPinned = $this->memberships->togglePinPost($group, $statusId, $request->user());

        return response()->json([
            'success' => true,
            'is_pinned' => $isPinned,
            'message' => $isPinned ? __('messages.groups_post_pinned') : __('messages.groups_post_unpinned'),
        ]);
    }

    private function ensureFeatureEnabled(): void
    {
        if (!$this->access->featureEnabled()) {
            abort(404, __('messages.groups_feature_disabled'));
        }
    }
}

