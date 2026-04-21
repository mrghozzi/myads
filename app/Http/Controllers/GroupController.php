<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMembership;
use App\Models\ForumTopic;
use App\Models\Option;
use App\Models\Status;
use App\Services\GroupAccessService;
use App\Services\GroupDiscoveryService;
use App\Services\GroupMembershipService;
use App\Services\StatusActivityService;
use App\Services\SecurityPolicyService;
use App\Services\SecurityThrottleService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    public function __construct(
        private readonly GroupAccessService $access,
        private readonly GroupMembershipService $memberships,
        private readonly GroupDiscoveryService $discovery
    ) {
    }

    public function index(Request $request)
    {
        $this->ensureFeatureEnabled();

        $search = trim((string) $request->query('search', ''));
        $user = Auth::user();
        $groups = $this->discovery->discover($user, $search);
        $myGroups = $this->discovery->myGroups($user);
        $creationEligibility = $user ? $this->access->creationEligibility($user) : null;

        $this->seo([
            'scope_key' => 'groups_index',
            'resource_title' => __('messages.groups_title'),
            'description' => $search !== ''
                ? __('messages.groups_search_description')
                : __('messages.groups_discover_description'),
            'indexable' => $search === '',
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.groups_title'), 'url' => route('groups.index')],
            ],
        ]);

        return view('theme::groups.index', compact('groups', 'myGroups', 'search', 'creationEligibility'));
    }

    public function create()
    {
        $this->ensureFeatureEnabled();

        $user = Auth::user();
        $eligibility = $this->access->creationEligibility($user);
        if (!$eligibility['allowed']) {
            return redirect()
                ->route('groups.index')
                ->with('error', $eligibility['message'] ?: __('messages.groups_creation_denied'));
        }

        $this->noindex([
            'scope_key' => 'groups_create',
            'resource_title' => __('messages.groups_create_title'),
            'description' => __('messages.groups_create_description'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.groups_title'), 'url' => route('groups.index')],
                ['name' => __('messages.groups_create_title'), 'url' => route('groups.create')],
            ],
        ]);

        return view('theme::groups.create', compact('eligibility'));
    }

    public function store(Request $request)
    {
        $this->ensureFeatureEnabled();

        $user = Auth::user();
        $eligibility = $this->access->creationEligibility($user);
        if (!$eligibility['allowed']) {
            return back()
                ->with('error', $eligibility['message'] ?: __('messages.groups_creation_denied'))
                ->withInput();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => 'nullable|string|max:140|unique:groups,slug',
            'privacy' => 'required|in:public,private_request',
            'short_description' => 'nullable|string|max:280',
            'description' => 'nullable|string|max:10000',
            'rules_markdown' => 'nullable|string|max:15000',
        ]);

        $group = $this->memberships->createGroup(
            $user,
            $validated,
            (string) $eligibility['initial_status']
        );

        $message = $group->status === Group::STATUS_PENDING_REVIEW
            ? __('messages.groups_created_pending_review')
            : __('messages.groups_created_success');

        return redirect()
            ->route('groups.show', $group)
            ->with('success', $message);
    }

    public function edit(Group $group)
    {
        $this->ensureFeatureEnabled();

        $user = Auth::user();
        if (!$this->access->canManageGroup($group, $user)) {
            abort(403);
        }

        $this->noindex([
            'scope_key' => 'groups_edit',
            'resource_title' => __('messages.groups_edit_title'),
            'description' => __('messages.groups_edit_description'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.groups_title'), 'url' => route('groups.index')],
                ['name' => $group->name, 'url' => route('groups.show', $group)],
                ['name' => __('messages.Settings'), 'url' => route('groups.edit', $group)],
            ],
        ]);

        return view('theme::groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $this->ensureFeatureEnabled();

        $user = Auth::user();
        if (!$this->access->canManageGroup($group, $user)) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'slug' => 'nullable|string|max:140|unique:groups,slug,' . $group->id,
            'privacy' => 'required|in:public,private_request',
            'short_description' => 'nullable|string|max:280',
            'description' => 'nullable|string|max:10000',
            'rules_markdown' => 'nullable|string|max:15000',
            'avatar' => 'nullable|image|max:4096',
            'cover' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'group_avatar_' . $group->id . '_' . time() . '.' . ($file->getClientOriginalExtension() ?: 'jpg');
            $file->move(base_path('upload'), $filename);
            $group->avatar_path = 'upload/' . $filename;
        }

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            $filename = 'group_cover_' . $group->id . '_' . time() . '.' . ($file->getClientOriginalExtension() ?: 'jpg');
            $file->move(base_path('upload'), $filename);
            $group->cover_path = 'upload/' . $filename;
        }

        $group->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?: $group->slug,
            'privacy' => $validated['privacy'],
            'short_description' => $validated['short_description'],
            'description' => $validated['description'],
            'rules_markdown' => $validated['rules_markdown'],
        ]);

        return redirect()
            ->route('groups.show', $group)
            ->with('success', __('messages.groups_updated_success'));
    }

    public function show(Request $request, Group $group, StatusActivityService $activityService)
    {
        $this->ensureFeatureEnabled();

        $user = Auth::user();
        $this->access->ensureCanViewGroupShell($group, $user);

        $group->loadMissing(['owner']);

        $tab = (string) $request->query('tab', 'overview');
        if (!in_array($tab, ['overview', 'feed', 'discussions', 'members'], true)) {
            $tab = 'overview';
        }

        $membership = $this->access->membership($group, $user);
        $canViewContent = $this->access->canViewGroupContent($group, $user);
        $canManageGroup = $this->access->canManageGroup($group, $user);
        $canPostToGroup = $this->access->canPostToGroup($group, $user);

        $activities = $this->emptyPaginator('feed_page', 10);
        $discussions = $this->emptyPaginator('discussion_page', 10);
        $members = $this->emptyPaginator('members_page', 20);
        $pendingMemberships = collect();

        if ($canViewContent) {
            $activities = Status::visible($user)
                ->with(['group'])
                ->where('group_id', $group->id)
                ->whereIn('s_type', [100, 4])
                ->whereHas('forumTopic', function ($query) {
                    $query->whereIn('name', $this->reservedFeedTopicNames());
                })
                ->orderBy('date', 'desc')
                ->paginate(10, ['*'], 'feed_page');

            $activityService->decorateMany($activities);

            $discussions = ForumTopic::visible($user)
                ->with(['user', 'group'])
                ->where('group_id', $group->id)
                ->whereNotIn('name', array_merge($this->reservedFeedTopicNames(), ['repost']))
                ->orderByDesc('is_pinned')
                ->orderBy('date', 'desc')
                ->paginate(10, ['*'], 'discussion_page');

            $members = GroupMembership::query()
                ->with(['user'])
                ->where('group_id', $group->id)
                ->where('status', GroupMembership::STATUS_ACTIVE)
                ->orderByRaw("CASE role WHEN 'owner' THEN 0 WHEN 'moderator' THEN 1 ELSE 2 END")
                ->orderBy('approved_at', 'asc')
                ->paginate(20, ['*'], 'members_page');

            if ($canManageGroup) {
                $pendingMemberships = GroupMembership::query()
                    ->with(['user'])
                    ->where('group_id', $group->id)
                    ->where('status', GroupMembership::STATUS_PENDING)
                    ->orderBy('requested_at', 'asc')
                    ->get();
            }
        }

        $indexable = $group->status === Group::STATUS_ACTIVE && $group->privacy === Group::PRIVACY_PUBLIC;
        if ($indexable) {
            $this->seo([
                'scope_key' => 'group_show',
                'content_type' => 'group',
                'content_id' => $group->id,
                'resource_title' => $group->name,
                'description' => $group->short_description ?: Str::limit(strip_tags((string) $group->description), 170),
                'breadcrumbs' => [
                    ['name' => __('messages.home'), 'url' => url('/')],
                    ['name' => __('messages.groups_title'), 'url' => route('groups.index')],
                    ['name' => $group->name, 'url' => route('groups.show', $group)],
                ],
            ]);
        } else {
            $this->noindex([
                'scope_key' => 'group_show_private',
                'resource_title' => $group->name,
                'description' => $group->short_description ?: __('messages.groups_private_shell_description'),
            ]);
        }

        $cover = $group->cover_path ?: 'upload/cover.jpg';
        $avatar = $group->avatar_path ?: 'upload/avatar.png';

        return view('theme::groups.show', compact(
            'group',
            'tab',
            'membership',
            'canViewContent',
            'canManageGroup',
            'canPostToGroup',
            'activities',
            'discussions',
            'members',
            'pendingMemberships',
            'cover',
            'avatar'
        ));
    }

    public function join(Group $group)
    {
        $this->ensureFeatureEnabled();

        $user = Auth::user();
        $this->access->ensureCanViewGroupShell($group, $user);

        if ($group->status !== Group::STATUS_ACTIVE) {
            return back()->with('error', __('messages.groups_join_blocked'));
        }

        $membership = $this->memberships->join($group, $user);

        $message = $membership->status === GroupMembership::STATUS_ACTIVE
            ? __('messages.groups_joined_success')
            : __('messages.groups_join_request_sent');

        return back()->with('success', $message);
    }

    public function leave(Group $group)
    {
        $this->ensureFeatureEnabled();

        try {
            $this->memberships->leave($group, Auth::user());
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', __('messages.groups_left_success'));
    }

    public function approveMembership(Group $group, GroupMembership $membership)
    {
        $this->ensureFeatureEnabled();
        $this->ensureMembershipBelongsToGroup($group, $membership);

        $this->memberships->approve($membership, Auth::user());

        return back()->with('success', __('messages.groups_member_approved'));
    }

    public function rejectMembership(Group $group, GroupMembership $membership)
    {
        $this->ensureFeatureEnabled();
        $this->ensureMembershipBelongsToGroup($group, $membership);

        $this->memberships->reject($membership, Auth::user());

        return back()->with('success', __('messages.groups_member_rejected'));
    }

    public function updateRole(Request $request, Group $group, GroupMembership $membership)
    {
        $this->ensureFeatureEnabled();
        $this->ensureMembershipBelongsToGroup($group, $membership);

        $validated = $request->validate([
            'role' => 'required|in:moderator,member',
        ]);

        try {
            $this->memberships->updateRole($membership, (string) $validated['role'], Auth::user());
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', __('messages.groups_role_updated'));
    }

    public function storeDiscussion(
        Request $request,
        Group $group,
        SecurityPolicyService $securityPolicy,
        SecurityThrottleService $securityThrottle
    ) {
        $this->ensureFeatureEnabled();

        $user = Auth::user();
        $this->access->ensureCanPostToGroup($group, $user);

        $request->validate([
            'name' => 'required|string|max:255',
            'txt' => 'required|string|max:15000',
            'type' => 'nullable|in:2,4',
            'img' => 'nullable|image|max:4096',
        ]);

        if ($cooldownMessage = $securityThrottle->actionMessage($user, 'forum_topic')) {
            return back()->withErrors(['discussion' => $cooldownMessage])->withInput();
        }

        $contentToInspect = trim((string) $request->input('name') . "\n" . (string) $request->input('txt'));
        if ($violation = $securityPolicy->textViolation($contentToInspect, 'posts')) {
            return back()->withErrors(['discussion' => $violation])->withInput();
        }

        $time = time();

        DB::beginTransaction();
        try {
            $topic = ForumTopic::create([
                'uid' => $user->id,
                'name' => (string) $request->input('name'),
                'txt' => (string) $request->input('txt'),
                'cat' => 0,
                'group_id' => $group->id,
                'statu' => 1,
                'date' => $time,
                'reply' => 0,
                'vu' => 0,
            ]);

            $statusType = (int) $request->input('type', 2) === 4 ? 4 : 2;
            if ($statusType === 4 && $request->hasFile('img')) {
                $file = $request->file('img');
                $filename = 'group_topic_' . $topic->id . '_' . time() . '_' . Str::random(10) . '.' . ($file->getClientOriginalExtension() ?: 'jpg');
                $file->move(base_path('upload'), $filename);

                Option::create([
                    'name' => (string) $time,
                    'o_valuer' => 'upload/' . $filename,
                    'o_type' => 'image_post',
                    'o_parent' => $topic->id,
                    'o_order' => $user->id,
                    'o_mode' => 'file',
                ]);
            } else {
                $statusType = 2;
            }

            Status::create([
                'uid' => $user->id,
                'tp_id' => $topic->id,
                'group_id' => $group->id,
                's_type' => $statusType,
                'date' => $time,
                'txt' => null,
                'statu' => 1,
            ]);

            DB::commit();
            $securityThrottle->hitAction($user, 'forum_topic');
            $this->memberships->touchActivity($group, $time);

            return redirect()
                ->route('forum.topic', $topic->id)
                ->with('success', __('messages.groups_discussion_created'));
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->withErrors(['discussion' => $e->getMessage()])->withInput();
        }
    }

    private function ensureFeatureEnabled(): void
    {
        if (!$this->access->featureEnabled()) {
            abort(404);
        }
    }

    private function ensureMembershipBelongsToGroup(Group $group, GroupMembership $membership): void
    {
        if ((int) $membership->group_id !== (int) $group->id) {
            abort(404);
        }
    }

    private function reservedFeedTopicNames(): array
    {
        return ['text', 'link', 'gallery'];
    }

    private function emptyPaginator(string $pageName, int $perPage): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, $perPage, 1, [
            'path' => request()->url(),
            'pageName' => $pageName,
            'query' => request()->query(),
        ]);
    }
}
