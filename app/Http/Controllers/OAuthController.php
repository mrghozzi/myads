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

        if (!$app || !$app->isActive()) {
            return response('Invalid or inactive client_id', 400);
        }

        if (!$this->isRedirectUriValid($app, $request->redirect_uri)) {
            return response('Invalid redirect_uri', 400);
        }

        $requestedScopes = array_map(
            [DeveloperScopeCatalog::class, 'normalizeScopeId'],
            preg_split('/[\s,]+/', trim($request->scope))
        );
        $validScopes = array_values(array_intersect($requestedScopes, $app->requested_scopes ?? []));

        $scopeDetails = DeveloperScopeCatalog::getScopes($validScopes);

        return view('theme::oauth.authorize', compact('app', 'scopeDetails', 'request'));
    }

    public function authorizeResponse(Request $request)
    {
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

        if (!$app || !$app->isActive() || !$this->isRedirectUriValid($app, $request->redirect_uri)) {
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
        $validScopes = array_values(array_intersect($requestedScopes, $app->requested_scopes ?? []));

        $authData = $this->oauthService->generateAuthorizationCode($app, auth()->user(), $request->redirect_uri, $validScopes);

        $redirect = $this->buildRedirectUri($request->redirect_uri, [
            'code' => $authData['plain_code'],
            'state' => $request->state,
        ]);
        return redirect($redirect);
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

        if (!$app || !$app->isActive() || !hash_equals((string) $app->client_secret, (string) $request->client_secret)) {
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
}
