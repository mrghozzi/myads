<?php

namespace App\Services;

use App\Models\DeveloperApp;
use App\Models\DeveloperAuthorization;
use App\Models\DeveloperAuthorizationCode;
use App\Models\DeveloperAccessToken;
use App\Models\DeveloperRefreshToken;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DeveloperOAuthService
{
    /**
     * Get lowercase listing of existing columns in a table.
     */
    protected function getTableColumns(string $table): array
    {
        try {
            return array_map('strtolower', Schema::getColumnListing($table));
        } catch (\Throwable $e) {
            try {
                $cols = DB::select("SHOW COLUMNS FROM {$table}");
                return array_map('strtolower', array_column($cols, 'Field'));
            } catch (\Throwable $e2) {
                return [];
            }
        }
    }

    /**
     * Generate an authorization code for the user and app.
     */
    public function generateAuthorizationCode(DeveloperApp $app, User $user, string $redirectUri, array $scopes): array
    {
        $plainCode = Str::random(40);
        $existingColumns = $this->getTableColumns('developer_authorization_codes');

        // Attempt to add missing columns via direct SQL if possible
        foreach (['expires_at', 'scopes', 'used', 'redirect_uri', 'created_at', 'updated_at'] as $col) {
            if (!empty($existingColumns) && !in_array($col, $existingColumns)) {
                try {
                    $type = ($col === 'scopes') ? 'JSON NULL' : (($col === 'used') ? 'TINYINT(1) DEFAULT 0' : (($col === 'redirect_uri') ? 'TEXT NULL' : 'TIMESTAMP NULL'));
                    DB::statement("ALTER TABLE developer_authorization_codes ADD COLUMN {$col} {$type}");
                    $existingColumns[] = $col;
                } catch (\Throwable $ignored) {}
            }
        }

        $payload = [
            'developer_app_id' => $app->id,
            'user_id' => $user->id,
            'code' => hash('sha256', $plainCode), // Store hashed
            'redirect_uri' => $redirectUri,
            'scopes' => is_array($scopes) ? json_encode($scopes) : $scopes,
            'expires_at' => now()->addMinutes(10), // Short lived
            'used' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Filter payload strictly to columns that actually exist in the table
        if (!empty($existingColumns)) {
            $insertData = array_intersect_key($payload, array_flip($existingColumns));
        } else {
            $insertData = $payload;
        }

        try {
            $id = DB::table('developer_authorization_codes')->insertGetId($insertData);
            $record = DeveloperAuthorizationCode::find($id);
        } catch (\Illuminate\Database\QueryException $e) {
            // If unknown column error occurred, filter out the offending column dynamically and retry
            if (preg_match("/Unknown column '([^']+)'/i", $e->getMessage(), $m) || preg_match("/no such column: ([^\s]+)/i", $e->getMessage(), $m)) {
                $badCol = str_replace('developer_authorization_codes.', '', $m[1]);
                unset($insertData[$badCol]);
                unset($payload[$badCol]);
                try {
                    $id = DB::table('developer_authorization_codes')->insertGetId($insertData);
                    $record = DeveloperAuthorizationCode::find($id);
                } catch (\Throwable $e2) {
                    $bareData = [
                        'developer_app_id' => $app->id,
                        'user_id' => $user->id,
                        'code' => hash('sha256', $plainCode),
                        'redirect_uri' => $redirectUri,
                    ];
                    $id = DB::table('developer_authorization_codes')->insertGetId($bareData);
                    $record = DeveloperAuthorizationCode::find($id);
                }
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
            ->first();

        if (!$authCode) {
            return null;
        }

        // Check used state if column exists
        if (isset($authCode->used) && $authCode->used) {
            return null;
        }

        if ($authCode->isExpired()) {
            return null;
        }

        $targetUri = trim($redirectUri);
        $storedUri = trim((string) $authCode->redirect_uri);
        if ($targetUri !== $storedUri && urldecode($targetUri) !== urldecode($storedUri)) {
            return null;
        }

        // Mark as used safely
        try {
            DB::table('developer_authorization_codes')
                ->where('id', $authCode->id)
                ->update(['used' => true]);
        } catch (\Throwable $ignored) {}

        // Ensure authorization record exists safely
        try {
            $existingAuth = DeveloperAuthorization::where('user_id', $authCode->user_id)
                ->where('developer_app_id', $app->id)
                ->first();

            if (!$existingAuth) {
                $existingAuthColumns = $this->getTableColumns('developer_authorizations');
                $authData = [
                    'user_id' => $authCode->user_id,
                    'developer_app_id' => $app->id,
                    'scopes' => is_array($authCode->scopes) ? json_encode($authCode->scopes) : $authCode->scopes,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if (!empty($existingAuthColumns)) {
                    $authData = array_intersect_key($authData, array_flip($existingAuthColumns));
                }
                DB::table('developer_authorizations')->insert($authData);
            }
        } catch (\Throwable $ignored) {}

        $scopes = is_array($authCode->scopes) ? $authCode->scopes : (json_decode($authCode->scopes, true) ?? []);

        return $this->generateTokens($app, $authCode->user_id, $scopes);
    }

    /**
     * Exchange a refresh token for new tokens.
     */
    public function refreshTokens(DeveloperApp $app, string $refreshToken): ?array
    {
        $hashedToken = hash('sha256', $refreshToken);

        $tokenRecord = DeveloperRefreshToken::where('refresh_token', $hashedToken)
            ->first();

        if (!$tokenRecord) {
            return null;
        }

        if (isset($tokenRecord->revoked) && $tokenRecord->revoked) {
            return null;
        }

        if ($tokenRecord->isExpired()) {
            return null;
        }

        $accessToken = $tokenRecord->accessToken;

        if (!$accessToken || $accessToken->developer_app_id !== $app->id || (isset($accessToken->revoked) && $accessToken->revoked)) {
            return null;
        }

        // Revoke the old tokens safely
        try {
            DB::table('developer_access_tokens')
                ->where('id', $accessToken->id)
                ->update(['revoked' => true]);
        } catch (\Throwable $ignored) {}

        try {
            DB::table('developer_refresh_tokens')
                ->where('id', $tokenRecord->id)
                ->update(['revoked' => true]);
        } catch (\Throwable $ignored) {}

        $scopes = is_array($accessToken->scopes) ? $accessToken->scopes : (json_decode($accessToken->scopes, true) ?? []);

        return $this->generateTokens($app, $accessToken->user_id, $scopes);
    }

    /**
     * Generate access and refresh tokens.
     */
    protected function generateTokens(DeveloperApp $app, int $userId, array $scopes): array
    {
        $accessTokenPlain = Str::random(60);
        $refreshTokenPlain = Str::random(60);

        // Access token
        $existingTokenColumns = $this->getTableColumns('developer_access_tokens');

        foreach (['expires_at', 'scopes', 'revoked', 'created_at', 'updated_at'] as $col) {
            if (!empty($existingTokenColumns) && !in_array($col, $existingTokenColumns)) {
                try {
                    $type = ($col === 'scopes') ? 'JSON NULL' : (($col === 'revoked') ? 'TINYINT(1) DEFAULT 0' : 'TIMESTAMP NULL');
                    DB::statement("ALTER TABLE developer_access_tokens ADD COLUMN {$col} {$type}");
                    $existingTokenColumns[] = $col;
                } catch (\Throwable $ignored) {}
            }
        }

        $tokenPayload = [
            'developer_app_id' => $app->id,
            'user_id' => $userId,
            'access_token' => hash('sha256', $accessTokenPlain),
            'scopes' => is_array($scopes) ? json_encode($scopes) : $scopes,
            'expires_at' => now()->addDays(30),
            'revoked' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if (!empty($existingTokenColumns)) {
            $insertTokenData = array_intersect_key($tokenPayload, array_flip($existingTokenColumns));
        } else {
            $insertTokenData = $tokenPayload;
        }

        try {
            $accessTokenId = DB::table('developer_access_tokens')->insertGetId($insertTokenData);
        } catch (\Illuminate\Database\QueryException $e) {
            if (preg_match("/Unknown column '([^']+)'/i", $e->getMessage(), $m) || preg_match("/no such column: ([^\s]+)/i", $e->getMessage(), $m)) {
                $badCol = str_replace('developer_access_tokens.', '', $m[1]);
                unset($insertTokenData[$badCol]);
                $accessTokenId = DB::table('developer_access_tokens')->insertGetId($insertTokenData);
            } else {
                throw $e;
            }
        }

        // Refresh token
        $existingRefreshColumns = $this->getTableColumns('developer_refresh_tokens');

        foreach (['expires_at', 'revoked', 'created_at', 'updated_at'] as $col) {
            if (!empty($existingRefreshColumns) && !in_array($col, $existingRefreshColumns)) {
                try {
                    $type = ($col === 'revoked') ? 'TINYINT(1) DEFAULT 0' : 'TIMESTAMP NULL';
                    DB::statement("ALTER TABLE developer_refresh_tokens ADD COLUMN {$col} {$type}");
                    $existingRefreshColumns[] = $col;
                } catch (\Throwable $ignored) {}
            }
        }

        $refreshPayload = [
            'developer_access_token_id' => $accessTokenId,
            'refresh_token' => hash('sha256', $refreshTokenPlain),
            'expires_at' => now()->addDays(90),
            'revoked' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if (!empty($existingRefreshColumns)) {
            $insertRefreshData = array_intersect_key($refreshPayload, array_flip($existingRefreshColumns));
        } else {
            $insertRefreshData = $refreshPayload;
        }

        try {
            DB::table('developer_refresh_tokens')->insertGetId($insertRefreshData);
        } catch (\Illuminate\Database\QueryException $e) {
            if (preg_match("/Unknown column '([^']+)'/i", $e->getMessage(), $m) || preg_match("/no such column: ([^\s]+)/i", $e->getMessage(), $m)) {
                $badCol = str_replace('developer_refresh_tokens.', '', $m[1]);
                unset($insertRefreshData[$badCol]);
                DB::table('developer_refresh_tokens')->insertGetId($insertRefreshData);
            } else {
                throw $e;
            }
        }

        return [
            'access_token' => $accessTokenPlain,
            'token_type' => 'Bearer',
            'expires_in' => 30 * 24 * 60 * 60,
            'refresh_token' => $refreshTokenPlain,
            'scope' => is_array($scopes) ? implode(' ', $scopes) : (string) $scopes,
        ];
    }

    /**
     * Verify an access token.
     */
    public function verifyAccessToken(string $plainToken): ?DeveloperAccessToken
    {
        $hashedToken = hash('sha256', $plainToken);

        $token = DeveloperAccessToken::where('access_token', $hashedToken)
            ->first();

        if ($token && (!isset($token->revoked) || !$token->revoked) && !$token->isExpired()) {
            return $token;
        }

        return null;
    }
}
