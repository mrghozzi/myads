<?php

namespace App\Http\Controllers;

use App\Services\DeveloperEligibilityService;
use App\Models\DeveloperApp;
use Illuminate\Database\QueryException;
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

        $scopes = DeveloperScopeCatalog::getAllScopes();
        $categories = DeveloperScopeCatalog::getCategories();

        return view('theme::developer.index', compact('eligible', 'reason', 'apps', 'scopes', 'categories'));
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
        \Log::error('[DevApp:store] Step 1: Method entered', [
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'has_csrf' => $request->has('_token'),
            'inputs' => $request->except(['_token', 'client_secret']),
        ]);

        if (!auth()->check()) {
            \Log::error('[DevApp:store] Aborting: user not authenticated');
            return redirect()->route('login');
        }

        $check = $eligibilityService->checkEligibility(auth()->user());
        if (!$check['eligible']) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.dev_not_eligible'),
                ], 403);
            }
            return redirect()->route('developer.index')->with('error', __('messages.dev_not_eligible'));
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:191',
                'domain' => 'required|string|max:191',
                'description' => 'required|string|max:1000',
                'redirect_uris' => 'required|string',
                'requested_scopes' => 'nullable|array',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                    'message' => implode(' | ', array_map(function($err) {
                        return is_array($err) ? implode(', ', $err) : $err;
                    }, $e->errors())),
                ], 422);
            }

            return back()->withInput()->withErrors($e->errors())->with('error', implode(' | ', array_map(function($err) {
                return is_array($err) ? implode(', ', $err) : $err;
            }, $e->errors())));
        }

        $redirectUris = array_values(array_filter(array_map('trim', explode(',', $request->redirect_uris))));
        
        $clientId = bin2hex(random_bytes(16));
        $clientSecret = bin2hex(random_bytes(32));

        $domain = trim($request->domain);
        if (!preg_match('~^https?://~i', $domain)) {
            $domain = 'https://' . $domain;
        }

        try {
            $app = DeveloperApp::create([
                'user_id' => auth()->id(),
                'name' => trim($request->name),
                'domain' => $domain,
                'description' => trim($request->description),
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uris' => $redirectUris,
                'requested_scopes' => is_array($request->requested_scopes) ? array_values(array_filter($request->requested_scopes)) : [],
                'status' => 'draft',
            ]);
        } catch (\Throwable $e) {
            \Log::error('Developer app creation failed: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'file' => $e->getFile() . ':' . $e->getLine(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.dev_app_creation_failed') . ' (' . $e->getMessage() . ')',
                ], 500);
            }

            return back()->withInput()->with('error', __('messages.dev_app_creation_failed'));
        }

        \Log::error('[DevApp:store] Step 6: App created successfully', ['app_id' => $app->id]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.dev_app_created'),
                'redirect' => route('developer.apps.show', $app->id),
            ]);
        }

        return redirect()->route('developer.apps.show', $app->id)->with('success', __('messages.dev_app_created'));
    }

    public function show(DeveloperApp $app)
    {
        if ((int) $app->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $scopes = DeveloperScopeCatalog::getAllScopes();

        return view('theme::developer.apps.show', compact('app', 'scopes'));
    }

    public function update(Request $request, DeveloperApp $app)
    {
        \Log::error('[DevApp:update] Step 1: Update method entered', [
            'app_id' => $app->id,
            'user_id' => auth()->id(),
        ]);

        if ((int) $app->user_id !== (int) auth()->id()) {
            abort(403);
        }

        try {
            $request->validate([
                'name' => 'required|string|max:191',
                'domain' => 'required|string|max:191',
                'description' => 'required|string|max:1000',
                'redirect_uris' => 'required|string',
                'requested_scopes' => 'nullable|array',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('[DevApp:update] Step 2: Validation FAILED', [
                'errors' => $e->errors(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                    'message' => implode(' | ', array_map(function($err) {
                        return is_array($err) ? implode(', ', $err) : $err;
                    }, $e->errors())),
                ], 422);
            }

            return back()->withInput()->withErrors($e->errors())->with('error', implode(' | ', array_map(function($err) {
                return is_array($err) ? implode(', ', $err) : $err;
            }, $e->errors())));
        }

        $redirectUris = array_values(array_filter(array_map('trim', explode(',', $request->redirect_uris))));
        $scopes = is_array($request->requested_scopes)
            ? array_values(array_filter($request->requested_scopes))
            : [];

        $domain = trim($request->domain);
        if (!preg_match('~^https?://~i', $domain)) {
            $domain = 'https://' . $domain;
        }

        $sensitiveFieldsChanged = 
            $app->domain !== $domain ||
            $app->redirect_uris !== $redirectUris ||
            $app->requested_scopes !== $scopes;

        $app->name = trim($request->name);
        $app->domain = $domain;
        $app->description = trim($request->description);
        $app->redirect_uris = $redirectUris;
        $app->requested_scopes = $scopes;

        if ($sensitiveFieldsChanged && in_array($app->status, ['active', 'rejected'])) {
            $app->status = 'pending_review';
        }

        try {
            $app->save();
        } catch (\Throwable $e) {
            \Log::error('[DevApp:update] Step 3: Save FAILED', [
                'app_id' => $app->id,
                'exception' => get_class($e),
                'error' => $e->getMessage(),
                'file' => $e->getFile() . ':' . $e->getLine(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.dev_app_update_failed') . ' (' . $e->getMessage() . ')',
                ], 500);
            }

            return back()->withInput()->with('error', __('messages.dev_app_update_failed'));
        }

        \Log::error('[DevApp:update] Step 4: App updated successfully', ['app_id' => $app->id]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.dev_app_updated'),
            ]);
        }

        return back()->with('success', __('messages.dev_app_updated'));
    }

    public function submit(DeveloperApp $app)
    {
        if ((int) $app->user_id !== (int) auth()->id()) {
            abort(403);
        }

        if ($app->status === 'draft') {
            $app->update(['status' => 'pending_review']);
        }

        return back()->with('success', __('messages.dev_app_submitted'));
    }

    public function rotateSecret(DeveloperApp $app)
    {
        if ((int) $app->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $clientSecret = bin2hex(random_bytes(32));
        $app->update(['client_secret' => $clientSecret]);

        return back()->with('success', __('messages.dev_app_secret_rotated'));
    }

    public function destroy(DeveloperApp $app)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if ((int) $app->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $app->delete();

        return redirect()->route('developer.apps.index')->with('success', __('messages.dev_app_deleted'));
    }

    public function guides()
    {
        $scopes = DeveloperScopeCatalog::getAllScopes();
        $categories = DeveloperScopeCatalog::getCategories();

        return view('theme::developer.guides', compact('scopes', 'categories'));
    }
}
