<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeveloperApp;
use App\Services\DeveloperOAuthService;
use App\Services\DeveloperScopeCatalog;

class OAuthController extends Controller
{
    protected DeveloperOAuthService $oauthService;

    public function __construct(DeveloperOAuthService $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    public function authorizeRequest(Request $request)
    {
        try {
            $request->validate([
                'client_id' => 'required|string',
                'redirect_uri' => 'required|url',
                'response_type' => 'required|in:code',
                'state' => 'required|string',
                'scope' => 'required|string',
            ]);

            if (!auth()->check()) {
                session()->put('url.intended', $request->fullUrl());
                return redirect()->guest(route('login', ['next' => $request->fullUrl()]));
            }

            $app = DeveloperApp::where('client_id', $request->client_id)->first();

            if (!$app || !$app->isUsableBy(auth()->user())) {
                return response('Invalid or inactive client_id', 400);
            }

            if (!$this->isRedirectUriValid($app, $request->redirect_uri)) {
                return response('Invalid redirect_uri', 400);
            }

            $requestedScopes = array_map(
                [DeveloperScopeCatalog::class, 'normalizeScopeId'],
                preg_split('/[\s,]+/', trim($request->scope))
            );
            $appScopes = (array) ($app->requested_scopes ?? []);
            $validScopes = array_values(array_intersect($requestedScopes, $appScopes));

            $scopeDetails = DeveloperScopeCatalog::getScopes($validScopes);

            return view('theme::oauth.authorize', compact('app', 'scopeDetails', 'request'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('[OAuthController::authorizeRequest] Exception: ' . $e->getMessage(), [
                'file' => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            if (view()->exists('theme::errors.500')) {
                return response()->view('theme::errors.500', [
                    'message' => __('messages.error_500_text'),
                ], 500);
            }

            return response(__('messages.error_500_text') ?: 'An error occurred during authorization. Please try again.', 500);
        }
    }

    public function authorizeResponse(Request $request)
    {
        try {
            $this->ensureDeveloperPlatformTables();

            $request->validate([
                'client_id' => 'required|string',
                'redirect_uri' => 'required|url',
                'state' => 'required|string',
                'scope' => 'required|string',
                'action' => 'required|in:accept,reject',
            ]);

            if (!auth()->check()) {
                abort(403);
            }

            $app = DeveloperApp::where('client_id', $request->client_id)->first();

            if (!$app || !$app->isUsableBy(auth()->user()) || !$this->isRedirectUriValid($app, $request->redirect_uri)) {
                return response('Invalid client or redirect URI', 400);
            }

            if ($request->action === 'reject') {
                $redirect = $this->buildRedirectUri($request->redirect_uri, [
                    'error' => 'access_denied',
                    'state' => $request->state,
                ]);
                return redirect($redirect);
            }

            $requestedScopes = array_map(
                [DeveloperScopeCatalog::class, 'normalizeScopeId'],
                preg_split('/[\s,]+/', trim($request->scope))
            );
            $appScopes = (array) ($app->requested_scopes ?? []);
            $validScopes = array_values(array_intersect($requestedScopes, $appScopes));

            $authData = $this->oauthService->generateAuthorizationCode($app, auth()->user(), $request->redirect_uri, $validScopes);

            $redirect = $this->buildRedirectUri($request->redirect_uri, [
                'code' => $authData['plain_code'],
                'state' => $request->state,
            ]);
            return redirect($redirect);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            \Log::error('[OAuthController::authorizeResponse] Exception: ' . $e->getMessage(), [
                'file' => $e->getFile() . ':' . $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            if (view()->exists('theme::errors.500')) {
                return response()->view('theme::errors.500', [
                    'message' => __('messages.error_500_text'),
                ], 500);
            }

            return response(__('messages.error_500_text') ?: 'An error occurred during authorization. Please try again.', 500);
        }
    }

    public function token(Request $request)
    {
        // Support HTTP Basic Authentication for client credentials (RFC 6749 Section 2.3.1)
        if (!$request->has('client_id') && $request->getUser()) {
            $request->merge([
                'client_id' => $request->getUser(),
                'client_secret' => $request->getPassword(),
            ]);
        }

        $request->validate([
            'grant_type' => 'required|in:authorization_code,refresh_token',
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
        ]);

        // SECURITY: Do not query by client_secret directly (leaks timing info).
        // Fetch by client_id only, then compare secret with constant-time hash_equals().
        $app = DeveloperApp::where('client_id', $request->client_id)->first();

        if (!$app || $app->status === 'suspended' || !hash_equals((string) $app->client_secret, (string) $request->client_secret)) {
            return response()->json(['error' => 'invalid_client'], 401);
        }

        if ($request->grant_type === 'authorization_code') {
            $request->validate([
                'code' => 'required|string',
                'redirect_uri' => 'required|url',
            ]);

            $tokens = $this->oauthService->exchangeCodeForTokens($app, $request->code, $request->redirect_uri);

            if (!$tokens) {
                return response()->json(['error' => 'invalid_grant'], 400);
            }

            return response()->json($tokens);
        }

        if ($request->grant_type === 'refresh_token') {
            $request->validate([
                'refresh_token' => 'required|string',
            ]);

            $tokens = $this->oauthService->refreshTokens($app, $request->refresh_token);

            if (!$tokens) {
                return response()->json(['error' => 'invalid_grant'], 400);
            }

            return response()->json($tokens);
        }

        return response()->json(['error' => 'unsupported_grant_type'], 400);
    }

    /**
     * Check whether the redirect URI is registered and allowed for the app.
     * Supports exact match, URL-decoded match, and trailing-slash normalization.
     */
    protected function isRedirectUriValid(DeveloperApp $app, string $redirectUri): bool
    {
        $allowedUris = (array) ($app->redirect_uris ?? []);
        $target = trim($redirectUri);
        $targetDecoded = urldecode($target);

        foreach ($allowedUris as $allowed) {
            $allowedTrimmed = trim((string) $allowed);
            if ($target === $allowedTrimmed) {
                return true;
            }
            if ($targetDecoded === urldecode($allowedTrimmed)) {
                return true;
            }
            // Normalize trailing slashes if neither URI contains a query component
            if (!str_contains($target, '?') && !str_contains($allowedTrimmed, '?')) {
                if (rtrim($target, '/') === rtrim($allowedTrimmed, '/')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Build the redirect URI by properly appending query parameters,
     * retaining any existing query string in the base URI (RFC 6749 Section 4.1.2).
     */
    protected function buildRedirectUri(string $baseUri, array $params): string
    {
        $separator = str_contains($baseUri, '?') ? '&' : '?';
        return $baseUri . $separator . http_build_query($params);
    }

    /**
     * Ensure all required OAuth 2.0 developer tables and columns exist in the database.
     */
    protected function ensureDeveloperPlatformTables(): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('developer_authorization_codes') ||
                !\Illuminate\Support\Facades\Schema::hasColumn('developer_authorization_codes', 'expires_at')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            }

            // Ensure developer_authorization_codes table and all columns exist
            if (!\Illuminate\Support\Facades\Schema::hasTable('developer_authorization_codes')) {
                \Illuminate\Support\Facades\Schema::create('developer_authorization_codes', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('developer_app_id');
                    $table->unsignedBigInteger('user_id');
                    $table->string('code')->unique();
                    $table->text('redirect_uri');
                    $table->json('scopes')->nullable();
                    $table->timestamp('expires_at')->nullable();
                    $table->boolean('used')->default(false);
                    $table->timestamps();
                });
            } else {
                \Illuminate\Support\Facades\Schema::table('developer_authorization_codes', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_authorization_codes', 'developer_app_id')) {
                        $table->unsignedBigInteger('developer_app_id')->after('id');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_authorization_codes', 'user_id')) {
                        $table->unsignedBigInteger('user_id')->after('developer_app_id');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_authorization_codes', 'code')) {
                        $table->string('code')->after('user_id');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_authorization_codes', 'redirect_uri')) {
                        $table->text('redirect_uri')->after('code');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_authorization_codes', 'scopes')) {
                        $table->json('scopes')->nullable()->after('redirect_uri');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_authorization_codes', 'expires_at')) {
                        $table->timestamp('expires_at')->nullable()->after('scopes');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_authorization_codes', 'used')) {
                        $table->boolean('used')->default(false)->after('expires_at');
                    }
                });
            }

            // Ensure developer_authorizations table and all columns exist
            if (!\Illuminate\Support\Facades\Schema::hasTable('developer_authorizations')) {
                \Illuminate\Support\Facades\Schema::create('developer_authorizations', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id');
                    $table->unsignedBigInteger('developer_app_id');
                    $table->json('scopes')->nullable();
                    $table->timestamps();
                    $table->unique(['user_id', 'developer_app_id']);
                });
            } else {
                \Illuminate\Support\Facades\Schema::table('developer_authorizations', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_authorizations', 'user_id')) {
                        $table->unsignedBigInteger('user_id')->after('id');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_authorizations', 'developer_app_id')) {
                        $table->unsignedBigInteger('developer_app_id')->after('user_id');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_authorizations', 'scopes')) {
                        $table->json('scopes')->nullable()->after('developer_app_id');
                    }
                });
            }

            // Ensure developer_access_tokens table and all columns exist
            if (!\Illuminate\Support\Facades\Schema::hasTable('developer_access_tokens')) {
                \Illuminate\Support\Facades\Schema::create('developer_access_tokens', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('developer_app_id');
                    $table->unsignedBigInteger('user_id');
                    $table->string('access_token')->unique();
                    $table->json('scopes')->nullable();
                    $table->timestamp('expires_at')->nullable();
                    $table->boolean('revoked')->default(false);
                    $table->timestamps();
                });
            } else {
                \Illuminate\Support\Facades\Schema::table('developer_access_tokens', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_access_tokens', 'developer_app_id')) {
                        $table->unsignedBigInteger('developer_app_id')->after('id');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_access_tokens', 'user_id')) {
                        $table->unsignedBigInteger('user_id')->after('developer_app_id');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_access_tokens', 'access_token')) {
                        $table->string('access_token')->after('user_id');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_access_tokens', 'scopes')) {
                        $table->json('scopes')->nullable()->after('access_token');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_access_tokens', 'expires_at')) {
                        $table->timestamp('expires_at')->nullable()->after('scopes');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_access_tokens', 'revoked')) {
                        $table->boolean('revoked')->default(false)->after('expires_at');
                    }
                });
            }

            // Ensure developer_refresh_tokens table and all columns exist
            if (!\Illuminate\Support\Facades\Schema::hasTable('developer_refresh_tokens')) {
                \Illuminate\Support\Facades\Schema::create('developer_refresh_tokens', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('developer_access_token_id');
                    $table->string('refresh_token')->unique();
                    $table->timestamp('expires_at')->nullable();
                    $table->boolean('revoked')->default(false);
                    $table->timestamps();
                });
            } else {
                \Illuminate\Support\Facades\Schema::table('developer_refresh_tokens', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_refresh_tokens', 'developer_access_token_id')) {
                        $table->unsignedBigInteger('developer_access_token_id')->after('id');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_refresh_tokens', 'refresh_token')) {
                        $table->string('refresh_token')->after('developer_access_token_id');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_refresh_tokens', 'expires_at')) {
                        $table->timestamp('expires_at')->nullable()->after('refresh_token');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_refresh_tokens', 'revoked')) {
                        $table->boolean('revoked')->default(false)->after('expires_at');
                    }
                });
            }

            // Ensure developer_apps table has all required columns
            if (\Illuminate\Support\Facades\Schema::hasTable('developer_apps')) {
                \Illuminate\Support\Facades\Schema::table('developer_apps', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_apps', 'redirect_uris')) {
                        $table->json('redirect_uris')->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_apps', 'requested_scopes')) {
                        $table->json('requested_scopes')->nullable();
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('developer_apps', 'widget_capabilities')) {
                        $table->json('widget_capabilities')->nullable();
                    }
                });
            }
        } catch (\Throwable $e) {
            \Log::error('[OAuthController::ensureDeveloperPlatformTables] ' . $e->getMessage());
        }
    }
}
