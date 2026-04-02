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
    public function index()
    {
        abort_unless(auth()->user()?->canManageAdministrators(), 403);

        $schema = app(V420SchemaService::class);
        $featureAvailable = $schema->supports('site_admins');
        $upgradeNotice = $schema->notice('site_admins', __('messages.site_admins'));

        $admins = $featureAvailable
            ? SiteAdmin::with(['user', 'creator'])
                ->orderByDesc('is_super')
                ->orderBy('user_id')
                ->paginate(20)
            : new LengthAwarePaginator([], 0, 20, request()->integer('page', 1), [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);

        $users = User::orderBy('username')->get();
        $permissionModules = AdminAccessService::MODULES;

        return view('admin::admin.admins', compact('admins', 'users', 'permissionModules', 'featureAvailable', 'upgradeNotice'));
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
