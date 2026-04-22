<?php

namespace App\Services;

use App\Models\DeveloperApp;
use App\Models\DeveloperAuthorization;
use App\Models\DeveloperAuthorizationCode;
use App\Models\DeveloperAccessToken;
use App\Models\DeveloperRefreshToken;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DeveloperOAuthService
{
    /**
     * Generate an authorization code for the user and app.
     */
    public function generateAuthorizationCode(DeveloperApp $app, User $user, string $redirectUri, array $scopes): DeveloperAuthorizationCode
    {
        $code = Str::random(40);

        return DeveloperAuthorizationCode::create([
            'developer_app_id' => $app->id,
            'user_id' => $user->id,
            'code' => hash('sha256', $code), // Store hashed
            'redirect_uri' => $redirectUri,
            'scopes' => $scopes,
            'expires_at' => now()->addMinutes(10), // Short lived
            'used' => false,
        ]);
    }

    /**
     * Verify an authorization code and exchange it for tokens.
     */
    public function exchangeCodeForTokens(DeveloperApp $app, string $code, string $redirectUri): ?array
    {
        $hashedCode = hash('sha256', $code);

        $authCode = DeveloperAuthorizationCode::where('developer_app_id', $app->id)
            ->where('code', $hashedCode)
            ->where('redirect_uri', $redirectUri)
            ->where('used', false)
            ->first();

        if (!$authCode || $authCode->isExpired()) {
            return null;
        }

        // Mark as used
        $authCode->update(['used' => true]);

        // Ensure authorization record exists
        $authorization = DeveloperAuthorization::firstOrCreate(
            ['user_id' => $authCode->user_id, 'developer_app_id' => $app->id],
            ['scopes' => $authCode->scopes]
        );

        // Update scopes if changed
        if ($authorization->scopes !== $authCode->scopes) {
            $authorization->update(['scopes' => $authCode->scopes]);
        }

        return $this->generateTokens($app, $authCode->user_id, $authCode->scopes);
    }

    /**
     * Exchange a refresh token for new tokens.
     */
    public function refreshTokens(DeveloperApp $app, string $refreshToken): ?array
    {
        $hashedToken = hash('sha256', $refreshToken);

        $tokenRecord = DeveloperRefreshToken::where('refresh_token', $hashedToken)
            ->where('revoked', false)
            ->first();

        if (!$tokenRecord || $tokenRecord->isExpired()) {
            return null;
        }

        $accessToken = $tokenRecord->accessToken;

        if (!$accessToken || $accessToken->developer_app_id !== $app->id || $accessToken->revoked) {
            return null;
        }

        // Revoke the old tokens
        $accessToken->update(['revoked' => true]);
        $tokenRecord->update(['revoked' => true]);

        return $this->generateTokens($app, $accessToken->user_id, $accessToken->scopes);
    }

    /**
     * Generate access and refresh tokens.
     */
    protected function generateTokens(DeveloperApp $app, int $userId, array $scopes): array
    {
        $accessTokenPlain = Str::random(60);
        $refreshTokenPlain = Str::random(60);

        $accessToken = DeveloperAccessToken::create([
            'developer_app_id' => $app->id,
            'user_id' => $userId,
            'access_token' => hash('sha256', $accessTokenPlain),
            'scopes' => $scopes,
            'expires_at' => now()->addDays(30), // e.g. 30 days
        ]);

        DeveloperRefreshToken::create([
            'developer_access_token_id' => $accessToken->id,
            'refresh_token' => hash('sha256', $refreshTokenPlain),
            'expires_at' => now()->addDays(90), // e.g. 90 days
        ]);

        return [
            'access_token' => $accessTokenPlain,
            'token_type' => 'Bearer',
            'expires_in' => 30 * 24 * 60 * 60,
            'refresh_token' => $refreshTokenPlain,
            'scope' => implode(' ', $scopes),
        ];
    }

    /**
     * Verify an access token.
     */
    public function verifyAccessToken(string $plainToken): ?DeveloperAccessToken
    {
        $hashedToken = hash('sha256', $plainToken);

        $token = DeveloperAccessToken::where('access_token', $hashedToken)
            ->where('revoked', false)
            ->first();

        if ($token && !$token->isExpired()) {
            return $token;
        }

        return null;
    }
}
