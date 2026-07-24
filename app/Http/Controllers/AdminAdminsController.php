<?php

namespace App\Http\Controllers;

use App\Models\SiteAdmin;
use App\Models\User;
use App\Services\AdminAccessService;
use App\Services\V420SchemaService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminAdminsController extends Controller
{
    public function searchUsers(Request $request)
    {
        abort_unless(auth()->user()?->canManageAdministrators(), 403);

        $schema = app(V420SchemaService::class);
        if (!$schema->supports('site_admins')) {
            return response()->json([]);
        }

        $query = trim((string) $request->query('q', ''));

        $existingAdminUserIds = SiteAdmin::pluck('user_id')->all();

        $userQuery = User::whereNotIn('id', $existingAdminUserIds);

        if ($query !== '') {
            $userQuery->where(function ($q) use ($query) {
                $q->where('username', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            });
        }

        $users = $userQuery->select('id', 'username', 'email', 'img')
            ->orderBy('username')
            ->limit(20)
            ->get();

        return response()->json($users->map(function ($u) {
            return [
                'id' => (int) $u->id,
                'username' => $u->username,
                'email' => $u->email,
                'avatar' => $u->img ? asset($u->img) : asset('themes/default/assets/images/avatar/1.png'),
            ];
        }));
    }

    public function index(Request $request)
    {
        abort_unless(auth()->user()?->canManageAdministrators(), 403);

        $schema = app(V420SchemaService::class);
        $featureAvailable = $schema->supports('site_admins');
        $upgradeNotice = $schema->notice('site_admins', __('messages.site_admins'));

        $query = SiteAdmin::with(['user', 'creator']);

        // Search Filter
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Access Level Filter
        if ($request->filled('level')) {
            $level = $request->input('level');
            if ($level === 'super') {
                $query->where(function ($q) {
                    $q->where('is_super', 1)->orWhere('user_id', 1);
                });
            } elseif ($level === 'full') {
                $query->where('has_full_access', 1)->where('is_super', 0)->where('user_id', '!=', 1);
            } elseif ($level === 'limited') {
                $query->where('has_full_access', 0)->where('is_super', 0)->where('user_id', '!=', 1);
            }
        }

        // Status Filter
        if ($request->has('status') && $request->input('status') !== '' && $request->input('status') !== null) {
            $query->where('is_active', (int) $request->input('status'));
        }

        $admins = $featureAvailable
            ? $query->orderByDesc('is_super')
                    ->orderBy('user_id')
                    ->paginate(15)
                    ->withQueryString()
            : new LengthAwarePaginator([], 0, 15, request()->integer('page', 1), [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);

        // Summary Statistics
        $stats = [
            'total' => $featureAvailable ? SiteAdmin::count() : 0,
            'super' => $featureAvailable ? SiteAdmin::where('is_super', 1)->orWhere('user_id', 1)->count() : 0,
            'full' => $featureAvailable ? SiteAdmin::where('has_full_access', 1)->where('is_super', 0)->where('user_id', '!=', 1)->count() : 0,
            'active' => $featureAvailable ? SiteAdmin::where('is_active', 1)->count() : 0,
        ];

        // Optimized candidate users query (Exclude existing admins to avoid loading all users)
        $existingAdminUserIds = $featureAvailable ? SiteAdmin::pluck('user_id')->all() : [];
        $users = User::whereNotIn('id', $existingAdminUserIds)
            ->select('id', 'username', 'email', 'img')
            ->orderBy('username')
            ->limit(100)
            ->get();

        $permissionModules = AdminAccessService::MODULES;

        return view('admin::admin.admins', compact('admins', 'users', 'permissionModules', 'featureAvailable', 'upgradeNotice', 'stats'));
    }

    public function toggleStatus(int $siteAdmin)
    {
        abort_unless(auth()->user()?->canManageAdministrators(), 403);

        $schema = app(V420SchemaService::class);
        if (!$schema->supports('site_admins')) {
            return redirect()->route('admin.admins')
                ->with('error', $schema->blockedActionMessage('site_admins', __('messages.site_admins')));
        }

        $admin = SiteAdmin::findOrFail($siteAdmin);

        if ($admin->is_super || (int) $admin->user_id === 1) {
            return redirect()->route('admin.admins')
                ->with('error', __('messages.super_admin_status_locked') ?? 'Super Administrator status cannot be toggled.');
        }

        $admin->update([
            'is_active' => !$admin->is_active,
        ]);

        return redirect()->route('admin.admins')
            ->with('success', __('messages.admin_status_updated') ?? 'Administrator status updated successfully.');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()?->canManageAdministrators(), 403);

        $schema = app(V420SchemaService::class);
        if (!$schema->supports('site_admins')) {
            return redirect()->route('admin.admins')
                ->with('error', $schema->blockedActionMessage('site_admins', __('messages.site_admins')));
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:site_admins,user_id',
            'has_full_access' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'in:' . implode(',', AdminAccessService::MODULES),
        ]);

        SiteAdmin::create([
            'user_id' => (int) $validated['user_id'],
            'is_super' => (int) $validated['user_id'] === 1,
            'has_full_access' => $request->boolean('has_full_access'),
            'permissions' => $request->boolean('has_full_access') ? [] : array_values(array_unique($validated['permissions'] ?? [])),
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.admins')->with('success', __('messages.admin_added_successfully'));
    }

    public function update(Request $request, int $siteAdmin)
    {
        abort_unless(auth()->user()?->canManageAdministrators(), 403);

        $schema = app(V420SchemaService::class);
        if (!$schema->supports('site_admins')) {
            return redirect()->route('admin.admins')
                ->with('error', $schema->blockedActionMessage('site_admins', __('messages.site_admins')));
        }

        $siteAdmin = SiteAdmin::findOrFail($siteAdmin);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|unique:site_admins,user_id,' . $siteAdmin->id,
            'has_full_access' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'in:' . implode(',', AdminAccessService::MODULES),
        ]);

        $siteAdmin->update([
            'user_id' => (int) $validated['user_id'],
            'is_super' => ((int) $validated['user_id'] === 1) || $siteAdmin->is_super,
            'has_full_access' => $request->boolean('has_full_access'),
            'permissions' => $request->boolean('has_full_access') ? [] : array_values(array_unique($validated['permissions'] ?? [])),
            'is_active' => $siteAdmin->is_super ? true : $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.admins')->with('success', __('messages.admin_updated_successfully'));
    }

    public function destroy(int $siteAdmin)
    {
        abort_unless(auth()->user()?->canManageAdministrators(), 403);

        $schema = app(V420SchemaService::class);
        if (!$schema->supports('site_admins')) {
            return redirect()->route('admin.admins')
                ->with('error', $schema->blockedActionMessage('site_admins', __('messages.site_admins')));
        }

        $siteAdmin = SiteAdmin::findOrFail($siteAdmin);

        if ($siteAdmin->is_super || (int) $siteAdmin->user_id === 1) {
            return redirect()->route('admin.admins')->with('error', __('messages.super_admin_cannot_be_removed'));
        }

        $siteAdmin->delete();

        return redirect()->route('admin.admins')->with('success', __('messages.admin_removed_successfully'));
    }
}
