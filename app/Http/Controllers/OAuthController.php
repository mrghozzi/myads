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
            return redirect()->route('login', ['next' => $request->fullUrl()]);
        }

        $app = DeveloperApp::where('client_id', $request->client_id)->first();

        if (!$app || !$app->isActive()) {
            return response('Invalid or inactive client_id', 400);
        }

        if (!in_array($request->redirect_uri, $app->redirect_uris)) {
            return response('Invalid redirect_uri', 400);
        }

        $requestedScopes = explode(' ', $request->scope);
        $validScopes = array_intersect($requestedScopes, $app->requested_scopes);

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

        if ($request->action === 'reject') {
            $redirect = $request->redirect_uri . '?error=access_denied&state=' . urlencode($request->state);
            return redirect($redirect);
        }

        $app = DeveloperApp::where('client_id', $request->client_id)->first();

        if (!$app || !$app->isActive() || !in_array($request->redirect_uri, $app->redirect_uris)) {
            return response('Invalid client or redirect URI', 400);
        }

        $requestedScopes = explode(' ', $request->scope);
        $validScopes = array_intersect($requestedScopes, $app->requested_scopes);

        $authCode = $this->oauthService->generateAuthorizationCode($app, auth()->user(), $request->redirect_uri, $validScopes);

        $redirect = $request->redirect_uri . '?code=' . urlencode($authCode->code) . '&state=' . urlencode($request->state);
        return redirect($redirect);
    }

    public function token(Request $request)
    {
        $request->validate([
            'grant_type' => 'required|in:authorization_code,refresh_token',
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
        ]);

        $app = DeveloperApp::where('client_id', $request->client_id)
            ->where('client_secret', $request->client_secret)
            ->first();

        if (!$app || !$app->isActive()) {
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
}
