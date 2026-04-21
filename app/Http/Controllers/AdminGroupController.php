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
        if ($this->schema->supports('groups')) {
            $groups = Group::query()
                ->with(['owner'])
                ->when($filter !== 'all', fn ($query) => $query->where('status', $filter))
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

        return view('admin::admin.groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group)
    {
        $oldOwnerId = $group->owner_id;

        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'short_description' => 'nullable|string|max:280',
            'description' => 'nullable|string|max:10000',
            'rules_markdown' => 'nullable|string|max:15000',
            'privacy' => 'required|in:public,private_request',
            'owner_id' => 'required|exists:users,id',
        ]);

        $group->update($validated);

        if ((int) $oldOwnerId !== (int) $group->owner_id) {
            $newOwner = \App\Models\User::find($group->owner_id);
            // Ensure new owner is at least a member
            $this->memberships->join($group, $newOwner);
            // Transfer role
            $this->memberships->transferOwnership($group, $newOwner, \App\Models\User::find($oldOwnerId));

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
            return back()->with('error', $e->getMessage());
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
