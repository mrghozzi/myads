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
        private readonly V420SchemaService $schema
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
}
