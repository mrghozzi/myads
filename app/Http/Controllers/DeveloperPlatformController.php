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
                'app_type' => 'nullable|string|in:confidential,public',
                'public_listed' => 'nullable',
                'category' => 'nullable|string|max:60',
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

        $redirectUris = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', (string) $request->redirect_uris))));
        
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
                'app_type' => $request->input('app_type', 'confidential'),
                'public_listed' => $request->boolean('public_listed'),
                'category' => $request->input('category'),
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
                'app_type' => 'nullable|string|in:confidential,public',
                'public_listed' => 'nullable',
                'category' => 'nullable|string|max:60',
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

        $redirectUris = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', (string) $request->redirect_uris))));
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
        $app->app_type = $request->input('app_type', $app->app_type ?? 'confidential');
        $app->public_listed = $request->boolean('public_listed');
        if ($request->has('category')) {
            $app->category = $request->input('category');
        }

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

    public function analytics(DeveloperApp $app)
    {
        if ((int) $app->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $now = now();
        $totalRequests = \App\Models\DeveloperApiLog::where('developer_app_id', $app->id)->count();
        $todayRequests = \App\Models\DeveloperApiLog::where('developer_app_id', $app->id)
            ->where('created_at', '>=', $now->copy()->startOfDay())
            ->count();

        $errorCount = \App\Models\DeveloperApiLog::where('developer_app_id', $app->id)
            ->where('status_code', '>=', 400)
            ->count();

        $errorRate = $totalRequests > 0 ? round(($errorCount / $totalRequests) * 100, 1) : 0;
        $avgDuration = (int) round(\App\Models\DeveloperApiLog::where('developer_app_id', $app->id)->avg('duration_ms') ?: 0);

        $topEndpoints = \App\Models\DeveloperApiLog::where('developer_app_id', $app->id)
            ->select('endpoint', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('endpoint')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $daysData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i)->format('Y-m-d');
            $count = \App\Models\DeveloperApiLog::where('developer_app_id', $app->id)
                ->whereDate('created_at', $date)
                ->count();
            $daysData[$date] = $count;
        }

        return view('theme::developer.apps.analytics', compact(
            'app',
            'totalRequests',
            'todayRequests',
            'errorRate',
            'avgDuration',
            'topEndpoints',
            'daysData'
        ));
    }

    public function logs(DeveloperApp $app)
    {
        if ((int) $app->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $logs = \App\Models\DeveloperApiLog::where('developer_app_id', $app->id)
            ->orderBy('id', 'desc')
            ->paginate(30);

        return view('theme::developer.apps.logs', compact('app', 'logs'));
    }

    public function playground()
    {
        $apps = auth()->check() ? DeveloperApp::where('user_id', auth()->id())->get() : collect();
        $tokens = auth()->check() ? \App\Models\DeveloperPersonalToken::where('user_id', auth()->id())->get() : collect();
        $scopes = DeveloperScopeCatalog::getAllScopes();

        return view('theme::developer.playground', compact('apps', 'tokens', 'scopes'));
    }

    public function openapi()
    {
        $scopes = DeveloperScopeCatalog::getAllScopes();
        $oauthScopes = [];
        foreach ($scopes as $scopeId => $meta) {
            $oauthScopes[$scopeId] = __($meta['name']);
        }

        $spec = [
            'openapi' => '3.1.0',
            'info' => [
                'title' => (config('app.name', 'MYADS')) . ' Developer API v1',
                'version' => '1.0.0',
                'description' => 'Complete REST API specification and OAuth 2.0 authorization endpoints for third-party integrations and developer applications.',
                'contact' => [
                    'name' => 'Developer Support',
                    'url' => url('/developer'),
                ],
            ],
            'servers' => [
                ['url' => url('/'), 'description' => 'Production Server'],
            ],
            'components' => [
                'securitySchemes' => [
                    'OAuth2' => [
                        'type' => 'oauth2',
                        'description' => 'Standard OAuth 2.0 Authorization Code Flow with PKCE support.',
                        'flows' => [
                            'authorizationCode' => [
                                'authorizationUrl' => url('/oauth/authorize'),
                                'tokenUrl' => url('/oauth/token'),
                                'refreshUrl' => url('/oauth/token'),
                                'scopes' => $oauthScopes,
                            ],
                        ],
                    ],
                    'BearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'JWT / PAT',
                        'description' => 'Personal Access Token or OAuth Access Token passed in the Authorization header.',
                    ],
                ],
            ],
            'security' => [
                ['OAuth2' => []],
                ['BearerAuth' => []],
            ],
            'paths' => [
                '/oauth/authorize' => [
                    'get' => [
                        'summary' => 'Request OAuth 2.0 Authorization Code',
                        'parameters' => [
                            ['name' => 'client_id', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'string']],
                            ['name' => 'redirect_uri', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'string', 'format' => 'uri']],
                            ['name' => 'response_type', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'string', 'enum' => ['code']]],
                            ['name' => 'scope', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'string']],
                            ['name' => 'state', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'string']],
                            ['name' => 'code_challenge', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'string']],
                            ['name' => 'code_challenge_method', 'in' => 'query', 'required' => false, 'schema' => ['type' => 'string', 'enum' => ['S256', 'plain']]],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Consent screen rendered'],
                            '302' => ['description' => 'Redirect to callback URI with code or error'],
                        ],
                    ],
                ],
                '/oauth/token' => [
                    'post' => [
                        'summary' => 'Exchange Authorization Code for Access & Refresh Tokens',
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/x-www-form-urlencoded' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'grant_type' => ['type' => 'string', 'enum' => ['authorization_code', 'refresh_token']],
                                            'client_id' => ['type' => 'string'],
                                            'client_secret' => ['type' => 'string'],
                                            'code' => ['type' => 'string'],
                                            'redirect_uri' => ['type' => 'string', 'format' => 'uri'],
                                            'code_verifier' => ['type' => 'string'],
                                            'refresh_token' => ['type' => 'string'],
                                        ],
                                        'required' => ['grant_type', 'client_id'],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'OAuth tokens returned successfully'],
                            '400' => ['description' => 'Invalid grant or verifier'],
                            '401' => ['description' => 'Invalid client credentials'],
                        ],
                    ],
                ],
                '/oauth/revoke' => [
                    'post' => [
                        'summary' => 'RFC 7009 Token Revocation',
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/x-www-form-urlencoded' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'token' => ['type' => 'string'],
                                            'token_type_hint' => ['type' => 'string', 'enum' => ['access_token', 'refresh_token']],
                                            'client_id' => ['type' => 'string'],
                                            'client_secret' => ['type' => 'string'],
                                        ],
                                        'required' => ['token', 'client_id'],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => [
                            '200' => ['description' => 'Token revoked successfully'],
                        ],
                    ],
                ],
                '/api/developer/v1/me' => [
                    'get' => [
                        'summary' => 'Get Authenticated Member Identity',
                        'security' => [['OAuth2' => ['user.identity.read']], ['BearerAuth' => []]],
                        'responses' => [
                            '200' => ['description' => 'User identity details'],
                            '401' => ['description' => 'Unauthorized'],
                            '403' => ['description' => 'Insufficient scope'],
                        ],
                    ],
                ],
                '/api/developer/v1/me/profile' => [
                    'get' => [
                        'summary' => 'Get Authenticated Member Profile',
                        'security' => [['OAuth2' => ['user.profile.read']], ['BearerAuth' => []]],
                        'responses' => ['200' => ['description' => 'Profile details']],
                    ],
                ],
                '/api/developer/v1/me/content' => [
                    'get' => [
                        'summary' => 'List Recent Member Posts',
                        'security' => [['OAuth2' => ['user.content.read']], ['BearerAuth' => []]],
                        'responses' => ['200' => ['description' => 'List of status posts']],
                    ],
                    'post' => [
                        'summary' => 'Create a New Community Post',
                        'security' => [['OAuth2' => ['user.content.write']], ['BearerAuth' => []]],
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => ['content' => ['type' => 'string', 'maxLength' => 10000]],
                                        'required' => ['content'],
                                    ],
                                ],
                            ],
                        ],
                        'responses' => ['200' => ['description' => 'Post created successfully']],
                    ],
                ],
                '/api/developer/v1/me/messages' => [
                    'get' => [
                        'summary' => 'List Recent Direct Messages',
                        'security' => [['OAuth2' => ['user.messages.read']], ['BearerAuth' => []]],
                        'responses' => ['200' => ['description' => 'Recent messages']],
                    ],
                    'post' => [
                        'summary' => 'Send Direct Message to Member',
                        'security' => [['OAuth2' => ['user.messages.write']], ['BearerAuth' => []]],
                        'responses' => ['200' => ['description' => 'Message sent successfully']],
                    ],
                ],
                '/api/developer/v1/me/notifications' => [
                    'get' => [
                        'summary' => 'List Member Notifications & Unread Count',
                        'security' => [['OAuth2' => ['user.notifications.read']], ['BearerAuth' => []]],
                        'responses' => ['200' => ['description' => 'Notifications collection']],
                    ],
                ],
                '/api/developer/v1/me/wallet' => [
                    'get' => [
                        'summary' => 'Retrieve Member Points (PTS) Balance',
                        'security' => [['OAuth2' => ['user.wallet.read']], ['BearerAuth' => []]],
                        'responses' => ['200' => ['description' => 'Points balance']],
                    ],
                ],
                '/api/developer/v1/store/products' => [
                    'get' => [
                        'summary' => 'Browse Store Digital Products',
                        'security' => [['OAuth2' => ['user.store.read']], ['BearerAuth' => []]],
                        'responses' => ['200' => ['description' => 'Digital products collection']],
                    ],
                ],
                '/api/developer/v1/me/orders' => [
                    'get' => [
                        'summary' => 'List Service Marketplace Orders',
                        'security' => [['OAuth2' => ['user.orders.read']], ['BearerAuth' => []]],
                        'responses' => ['200' => ['description' => 'Service orders']],
                    ],
                ],
            ],
        ];

        return response()->json($spec, 200, ['Content-Type' => 'application/json']);
    }
}
