<?php

namespace App\Http\Controllers;

use App\Services\DeveloperEligibilityService;
use App\Models\DeveloperApp;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\DeveloperScopeCatalog;

class DeveloperPlatformController extends Controller
{
    public function index(DeveloperEligibilityService $eligibilityService)
    {
        $eligible = false;
        $reason = '';
        $apps = [];

        if (auth()->check()) {
            $check = $eligibilityService->checkEligibility(auth()->user());
            $eligible = $check['eligible'];
            $reason = $check['reason'] ?? '';

            if ($eligible) {
                $apps = DeveloperApp::where('user_id', auth()->id())->get();
            }
        }

        return view('theme::developer.index', compact('eligible', 'reason', 'apps'));
    }

    public function apps(DeveloperEligibilityService $eligibilityService)
    {
        if (!auth()->check()) return redirect()->route('login');

        $check = $eligibilityService->checkEligibility(auth()->user());
        if (!$check['eligible']) {
            return redirect()->route('developer.index')->with('error', __('messages.dev_not_eligible'));
        }

        $apps = DeveloperApp::where('user_id', auth()->id())->get();
        return view('theme::developer.apps.index', compact('apps'));
    }

    public function create(DeveloperEligibilityService $eligibilityService)
    {
        if (!auth()->check()) return redirect()->route('login');

        $check = $eligibilityService->checkEligibility(auth()->user());
        if (!$check['eligible']) {
            return redirect()->route('developer.index')->with('error', __('messages.dev_not_eligible'));
        }

        $scopes = DeveloperScopeCatalog::getAllScopes();

        return view('theme::developer.apps.create', compact('scopes'));
    }

    public function store(Request $request, DeveloperEligibilityService $eligibilityService)
    {
        if (!auth()->check()) return redirect()->route('login');

        $check = $eligibilityService->checkEligibility(auth()->user());
        if (!$check['eligible']) {
            return redirect()->route('developer.index')->with('error', __('messages.dev_not_eligible'));
        }

        $request->validate([
            'name' => 'required|string|max:191',
            'domain' => 'required|url|max:191',
            'description' => 'required|string|max:1000',
            'redirect_uris' => 'required|string', // Comma separated
            'requested_scopes' => 'array',
        ]);

        $redirectUris = array_map('trim', explode(',', $request->redirect_uris));
        
        $clientId = bin2hex(random_bytes(16));
        $clientSecret = bin2hex(random_bytes(32));

        $app = DeveloperApp::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'domain' => $request->domain,
            'description' => $request->description,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uris' => $redirectUris,
            'requested_scopes' => $request->requested_scopes ?? [],
            'status' => 'draft', // By default draft
        ]);

        return redirect()->route('developer.apps.show', $app->id)->with('success', __('messages.dev_app_created'));
    }

    public function show(DeveloperApp $app)
    {
        if ($app->user_id !== auth()->id()) {
            abort(403);
        }

        $scopes = DeveloperScopeCatalog::getAllScopes();

        return view('theme::developer.apps.show', compact('app', 'scopes'));
    }

    public function update(Request $request, DeveloperApp $app)
    {
        if ($app->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:191',
            'domain' => 'required|url|max:191',
            'description' => 'required|string|max:1000',
            'redirect_uris' => 'required|string',
            'requested_scopes' => 'array',
        ]);

        $redirectUris = array_map('trim', explode(',', $request->redirect_uris));

        $sensitiveFieldsChanged = 
            $app->domain !== $request->domain ||
            $app->redirect_uris !== $redirectUris ||
            $app->requested_scopes !== ($request->requested_scopes ?? []);

        $app->name = $request->name;
        $app->domain = $request->domain;
        $app->description = $request->description;
        $app->redirect_uris = $redirectUris;
        $app->requested_scopes = $request->requested_scopes ?? [];

        if ($sensitiveFieldsChanged && in_array($app->status, ['active', 'rejected'])) {
            $app->status = 'pending_review';
        }

        $app->save();

        return back()->with('success', __('messages.dev_app_updated'));
    }

    public function submit(DeveloperApp $app)
    {
        if ($app->user_id !== auth()->id()) {
            abort(403);
        }

        if ($app->status === 'draft') {
            $app->update(['status' => 'pending_review']);
        }

        return back()->with('success', __('messages.dev_app_submitted'));
    }

    public function rotateSecret(DeveloperApp $app)
    {
        if ($app->user_id !== auth()->id()) {
            abort(403);
        }

        $clientSecret = bin2hex(random_bytes(32));
        $app->update(['client_secret' => $clientSecret]);

        return back()->with('success', __('messages.dev_app_secret_rotated'));
    }
}
