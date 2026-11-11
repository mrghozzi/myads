<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\SubscriptionPlan;
use App\Services\V420SchemaService;
use App\Support\GroupSettings;
use Illuminate\Http\Request;

class AdminGroupController extends Controller
{
    public function __construct(
        private readonly V420SchemaService $schema,
        private readonly \App\Services\GroupMembershipService $memberships,
        private readonly \App\Services\GroupAccessService $access,
        private readonly \App\Services\NotificationService $notifications
    ) {
    }

    public function index(Request $request)
    {
        $filter = (string) $request->query('status', 'all');
        $category = (string) $request->query('category', 'all');
        $search = trim((string) $request->query('search', ''));

        $allowedFilters = [
            'all',
            Group::STATUS_PENDING_REVIEW,
            Group::STATUS_ACTIVE,
            Group::STATUS_SUSPENDED,
            Group::STATUS_REJECTED,
        ];

        if (!in_array($filter, $allowedFilters, true)) {
            $filter = 'all';
        }

        $groups = collect();
        $stats = [
            'total' => 0,
            'active' => 0,
            'pending' => 0,
            'total_members' => 0,
            'total_posts' => 0,
        ];

        if ($this->schema->supports('groups')) {
            $stats = [
                'total' => Group::count(),
                'active' => Group::where('status', Group::STATUS_ACTIVE)->count(),
                'pending' => Group::where('status', Group::STATUS_PENDING_REVIEW)->count(),
                'total_members' => Group::sum('members_count'),
                'total_posts' => Group::sum('posts_count'),
            ];

            $groups = Group::query()
                ->with(['owner'])
                ->when($filter !== 'all', fn($query) => $query->where('status', $filter))
                ->when($category !== 'all', fn($query) => $query->where('category', $category))
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%")
                            ->orWhereHas('owner', fn($ownerQ) => $ownerQ->where('username', 'like', "%{$search}%"));
                    });
                })
                ->orderByRaw("FIELD(status, 'pending_review', 'active', 'suspended', 'rejected')")
                ->orderByDesc('last_activity_at')
                ->paginate(20)
                ->withQueryString();
        }

        $this->noindex([
            'scope_key' => 'admin_groups',
            'resource_title' => __('messages.admin_groups_title'),
            'description' => __('messages.admin_groups_description'),
        ]);

        return view('admin::admin.groups.index', [
            'groups' => $groups,
            'filter' => $filter,
            'category' => $category,
            'search' => $search,
            'stats' => $stats,
            'categories' => Group::CATEGORIES,
            'schemaReady' => $this->schema->supports('groups'),
        ]);
    }

    public function updateStatus(Request $request, Group $group)
    {
        $request->validate([
            'status' => 'required|in:active,pending_review,suspended,rejected',
        ]);

        $group->update([
            'status' => (string) $request->input('status'),
        ]);

        return back()->with('success', __('messages.admin_groups_status_updated'));
    }

    public function toggleFeatured(Group $group)
    {
        $group->update([
            'is_featured' => !$group->is_featured,
        ]);

        return back()->with('success', __('messages.admin_groups_featured_updated'));
    }

    public function settings()
    {
        $settings = GroupSettings::all();
        $plans = collect();

        if ($this->schema->supports('subscriptions_billing')) {
            $plans = SubscriptionPlan::query()
                ->where('is_active', true)
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }

        $this->noindex([
            'scope_key' => 'admin_groups_settings',
            'resource_title' => __('messages.admin_groups_settings_title'),
            'description' => __('messages.admin_groups_settings_description'),
        ]);

        return view('admin::admin.groups.settings', [
            'settings' => $settings,
            'plans' => $plans,
            'schemaReady' => $this->schema->supports('groups'),
            'billingReady' => $this->schema->supports('subscriptions_billing'),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'nullable|boolean',
            'creation_policy' => 'required|in:all_members,approval,paid_plan',
            'eligible_plan_ids' => 'nullable|array',
            'eligible_plan_ids.*' => 'integer',
        ]);

        GroupSettings::save($validated);

        return back()->with('success', __('messages.admin_groups_settings_saved'));
    }

    public function edit(Group $group)
    {
        $this->noindex([
            'scope_key' => 'admin_groups_edit',
            'resource_title' => __('messages.groups_edit_title') . ' - ' . $group->name,
            'description' => __('messages.admin_groups_description'),
        ]);

        $categories = Group::CATEGORIES;

        return view('admin::admin.groups.edit', compact('group', 'categories'));
    }

    public function update(Request $request, Group $group)
    {
        $oldOwnerId = $group->owner_id;
        $newOwnerId = (int) $request->input('owner_id');

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'category' => 'nullable|string|max:64',
            'short_description' => 'nullable|string|max:280',
            'description' => 'nullable|string|max:10000',
            'rules_markdown' => 'nullable|string|max:15000',
            'privacy' => 'required|in:public,private_request',
            'posting_permission' => 'required|in:all_members,moderators_only',
            'join_fee_pts' => 'nullable|integer|min:0|max:100000',
            'owner_id' => 'required|exists:users,id',
        ]);

        // Remove owner_id from validated to update it separately via transferOwnership if needed
        $attributes = collect($validated)->except('owner_id')->toArray();
        $group->update($attributes);

        if ($oldOwnerId !== $newOwnerId) {
            $newOwner = \App\Models\User::find($newOwnerId);
            $oldOwner = \App\Models\User::find($oldOwnerId);

            // Ensure new owner is at least a member
            $this->memberships->join($group, $newOwner);

            // transferOwnership will update group->owner_id and sync roles
            $this->memberships->transferOwnership($group, $newOwner, \Illuminate\Support\Facades\Auth::user());

            // Notify old owner (new owner is already notified inside transferOwnership)
            $this->notifications->send(
                $oldOwnerId,
                __('messages.groups_notification_ownership_lost', ['group' => $group->name]),
                route('groups.show', $group)
            );
        } else {
            // Settings changed but owner didn't change, notify current owner
            $this->notifications->send(
                $group->owner_id,
                __('messages.groups_notification_settings_updated_by_admin', ['group' => $group->name]),
                route('groups.show', $group)
            );
        }

        return back()->with('success', __('messages.admin_groups_updated'));
    }

    public function destroy(Group $group)
    {
        $this->memberships->deleteGroup($group, \Illuminate\Support\Facades\Auth::user());

        return redirect()->route('admin.groups.index')->with('success', __('messages.groups_deleted_success'));
    }

    public function clearMedia(Request $request, Group $group)
    {
        $type = (string) $request->input('type');
        if ($type === 'avatar') {
            if ($group->avatar_path && file_exists(base_path($group->avatar_path)) && !str_contains($group->avatar_path, 'group.png')) {
                @unlink(base_path($group->avatar_path));
            }
            $group->update(['avatar_path' => null]);
        } elseif ($type === 'cover') {
            if ($group->cover_path && file_exists(base_path($group->cover_path)) && !str_contains($group->cover_path, 'cover')) {
                @unlink(base_path($group->cover_path));
            }
            $group->update(['cover_path' => null]);
        }

        return back()->with('success', __('messages.media_cleared_success') ?? 'Media cleared successfully');
    }

    public function banMember(Group $group, \App\Models\GroupMembership $membership)
    {
        $this->memberships->ban($membership, \Illuminate\Support\Facades\Auth::user());

        return back()->with('success', __('messages.groups_member_banned'));
    }

    public function members(Group $group)
    {
        $members = $group->memberships()
            ->with('user')
            ->orderByRaw("FIELD(role, 'owner', 'moderator', 'member')")
            ->paginate(50);

        $this->noindex([
            'scope_key' => 'admin_groups_members',
            'resource_title' => __('messages.members') . ' - ' . $group->name,
            'description' => __('messages.admin_groups_description'),
        ]);

        return view('admin::admin.groups.members', compact('group', 'members'));
    }

    public function updateMemberRole(Request $request, Group $group, \App\Models\GroupMembership $membership)
    {
        $validated = $request->validate([
            'role' => 'required|in:moderator,member',
        ]);

        try {
            $this->memberships->updateRole($membership, (string) $validated['role'], \Illuminate\Support\Facades\Auth::user());
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', __('messages.error_occurred'));
        }

        return back()->with('success', __('messages.groups_member_role_updated'));
    }

    public function removeMember(Group $group, \App\Models\GroupMembership $membership)
    {
        if ($membership->role === 'owner') {
            return back()->with('error', 'Cannot remove owner. Transfer ownership first.');
        }

        $userId = $membership->user_id;
        $membership->delete();

        $this->notifications->send(
            $userId,
            __('messages.groups_notification_removed', ['group' => $group->name]),
            route('groups.index')
        );

        return back()->with('success', __('messages.groups_member_removed_success'));
    }
}
