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
    public function generateAuthorizationCode(DeveloperApp $app, User $user, string $redirectUri, array $scopes): array
    {
        $plainCode = Str::random(40);
        $payload = [
            'developer_app_id' => $app->id,
            'user_id' => $user->id,
            'code' => hash('sha256', $plainCode), // Store hashed
            'redirect_uri' => $redirectUri,
            'scopes' => $scopes,
            'expires_at' => now()->addMinutes(10), // Short lived
            'used' => false,
        ];

        try {
            $record = DeveloperAuthorizationCode::create($payload);
        } catch (\Illuminate\Database\QueryException $e) {
            // Self-heal missing columns if an older database schema is present
            if (str_contains($e->getMessage(), 'Unknown column') || str_contains($e->getMessage(), 'no such column')) {
                foreach ([
                    "ALTER TABLE developer_authorization_codes ADD COLUMN expires_at TIMESTAMP NULL",
                    "ALTER TABLE developer_authorization_codes ADD COLUMN scopes JSON NULL",
                    "ALTER TABLE developer_authorization_codes ADD COLUMN used TINYINT(1) DEFAULT 0",
                    "ALTER TABLE developer_authorization_codes ADD COLUMN redirect_uri TEXT NULL",
                    "ALTER TABLE developer_authorization_codes ADD COLUMN code VARCHAR(255) NULL",
                    "ALTER TABLE developer_authorization_codes ADD COLUMN user_id BIGINT UNSIGNED NULL",
                    "ALTER TABLE developer_authorization_codes ADD COLUMN developer_app_id BIGINT UNSIGNED NULL",
                ] as $sql) {
                    try {
                        \Illuminate\Support\Facades\DB::statement($sql);
                    } catch (\Throwable $ignored) {}
                }
                $record = DeveloperAuthorizationCode::create($payload);
            } else {
                throw $e;
            }
        }

        return [
            'record' => $record,
            'plain_code' => $plainCode,
        ];
    }

    /**
     * Verify an authorization code and exchange it for tokens.
     */
    public function exchangeCodeForTokens(DeveloperApp $app, string $code, string $redirectUri): ?array
    {
        $hashedCode = hash('sha256', $code);

        $authCode = DeveloperAuthorizationCode::where('developer_app_id', $app->id)
            ->where('code', $hashedCode)
            ->where('used', false)
            ->first();

        if (!$authCode || $authCode->isExpired()) {
            return null;
        }

        $targetUri = trim($redirectUri);
        $storedUri = trim((string) $authCode->redirect_uri);
        if ($targetUri !== $storedUri && urldecode($targetUri) !== urldecode($storedUri)) {
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

        try {
            $accessToken = DeveloperAccessToken::create([
                'developer_app_id' => $app->id,
                'user_id' => $userId,
                'access_token' => hash('sha256', $accessTokenPlain),
                'scopes' => $scopes,
                'expires_at' => now()->addDays(30), // e.g. 30 days
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'Unknown column') || str_contains($e->getMessage(), 'no such column')) {
                foreach ([
                    "ALTER TABLE developer_access_tokens ADD COLUMN expires_at TIMESTAMP NULL",
                    "ALTER TABLE developer_access_tokens ADD COLUMN scopes JSON NULL",
                    "ALTER TABLE developer_access_tokens ADD COLUMN revoked TINYINT(1) DEFAULT 0",
                ] as $sql) {
                    try {
                        \Illuminate\Support\Facades\DB::statement($sql);
                    } catch (\Throwable $ignored) {}
                }
                $accessToken = DeveloperAccessToken::create([
                    'developer_app_id' => $app->id,
                    'user_id' => $userId,
                    'access_token' => hash('sha256', $accessTokenPlain),
                    'scopes' => $scopes,
                    'expires_at' => now()->addDays(30),
                ]);
            } else {
                throw $e;
            }
        }

        try {
            DeveloperRefreshToken::create([
                'developer_access_token_id' => $accessToken->id,
                'refresh_token' => hash('sha256', $refreshTokenPlain),
                'expires_at' => now()->addDays(90), // e.g. 90 days
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'Unknown column') || str_contains($e->getMessage(), 'no such column')) {
                foreach ([
                    "ALTER TABLE developer_refresh_tokens ADD COLUMN expires_at TIMESTAMP NULL",
                    "ALTER TABLE developer_refresh_tokens ADD COLUMN revoked TINYINT(1) DEFAULT 0",
                ] as $sql) {
                    try {
                        \Illuminate\Support\Facades\DB::statement($sql);
                    } catch (\Throwable $ignored) {}
                }
                DeveloperRefreshToken::create([
                    'developer_access_token_id' => $accessToken->id,
                    'refresh_token' => hash('sha256', $refreshTokenPlain),
                    'expires_at' => now()->addDays(90),
                ]);
            } else {
                throw $e;
            }
        }

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
