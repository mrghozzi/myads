<?php

namespace App\Http\Controllers;

use App\Models\DeveloperApp;
use App\Services\DeveloperPlatformSettings;
use Illuminate\Http\Request;

class AdminDeveloperController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        
        $query = DeveloperApp::with('user')->orderBy('id', 'desc');
        
        if ($status) {
            $query->where('status', $status);
        }

        $apps = $query->paginate(20);

        $stats = [
            'total' => DeveloperApp::count(),
            'pending' => DeveloperApp::where('status', 'pending_review')->count(),
            'active' => DeveloperApp::where('status', 'active')->count(),
        ];

        return view('admin::admin.developers.index', compact('apps', 'stats'));
    }

    public function settings(DeveloperPlatformSettings $settings)
    {
        return view('admin::admin.developers.settings', compact('settings'));
    }

    public function updateSettings(Request $request, DeveloperPlatformSettings $settings)
    {
        $request->validate([
            'enabled' => 'boolean',
            'require_admin_approval' => 'boolean',
            'min_account_age_days' => 'integer|min:0',
            'min_followers_count' => 'integer|min:0',
            'require_paid_plan' => 'boolean',
            'eligible_plan_ids' => 'array',
        ]);

        $settings->setAll([
            'enabled' => $request->has('enabled'),
            'require_admin_approval' => $request->has('require_admin_approval'),
            'min_account_age_days' => $request->min_account_age_days,
            'min_followers_count' => $request->min_followers_count,
            'require_paid_plan' => $request->has('require_paid_plan'),
            'eligible_plan_ids' => $request->eligible_plan_ids ?? [],
        ]);

        return back()->with('success', __('messages.settings_updated'));
    }

    public function show(DeveloperApp $app)
    {
        return view('admin::admin.developers.show', compact('app'));
    }

    public function updateStatus(Request $request, DeveloperApp $app)
    {
        $request->validate([
            'status' => 'required|in:active,rejected,suspended',
        ]);

        $app->update(['status' => $request->status]);

        return back()->with('success', __('messages.app_status_updated'));
    }
}
