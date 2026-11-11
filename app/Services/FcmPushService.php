<?php

namespace App\Services;

use App\Models\User;
use App\Models\Option;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmPushService
{
    /**
     * Send push notification to a specific user.
     *
     * @param User|int $recipient
     * @param string $title
     * @param string $body
     * @param array $data Additional data payload for deep-linking
     * @return bool
     */
    public function sendToUser(User|int $recipient, string $title, string $body, array $data = []): bool
    {
        $userId = $recipient instanceof User ? $recipient->id : (int) $recipient;

        // Retrieve active device token from options table (o_type = 'fcm_token', o_parent = $userId)
        $tokenOption = Option::where('o_type', 'fcm_token')
            ->where('o_parent', $userId)
            ->first();

        if (!$tokenOption || empty($tokenOption->o_valuer)) {
            return false;
        }

        return $this->sendToToken($tokenOption->o_valuer, $title, $body, $data);
    }

    /**
     * Send push notification directly to an FCM device token.
     *
     * @param string $token
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
     */
    public function sendToToken(string $token, string $title, string $body, array $data = []): bool
    {
        // 0. Check database options first
        $firebaseOptions = Option::where('o_type', 'firebase')->get()->keyBy('name');
        $mode = $firebaseOptions->get('mode')?->o_valuer ?? '';

        if ($mode === 'disabled') {
            Log::debug("FcmPushService: Push notifications are disabled in admin settings.");
            return false;
        }

        // 1. Check for Firebase Service Account (HTTP v1 API)
        if ($mode === 'fcm_v1' || $mode === '') {
            $serviceAccountPath = $firebaseOptions->get('credentials_path')?->o_valuer
                ?: config('services.firebase.credentials', env('FIREBASE_CREDENTIALS'));
            $projectId = $firebaseOptions->get('project_id')?->o_valuer
                ?: config('services.firebase.project_id', env('FIREBASE_PROJECT_ID'));

            if (!empty($serviceAccountPath) && file_exists($serviceAccountPath)) {
                return $this->sendViaHttpV1($token, $title, $body, $data, $serviceAccountPath, $projectId);
            }
        }

        // 2. Fallback: Legacy FCM Server Key
        $serverKey = $firebaseOptions->get('server_key')?->o_valuer
            ?: config('services.firebase.server_key', env('FCM_SERVER_KEY'));
        if (!empty($serverKey)) {
            return $this->sendViaLegacy($token, $title, $body, $data, $serverKey);
        }

        // Graceful no-op when Firebase credentials are not yet configured
        Log::debug("FcmPushService: FCM credentials not configured. Push skipped for token: " . substr($token, 0, 15) . "...");
        return false;
    }

    /**
     * Send push notification via FCM HTTP v1 API using OAuth2 service account token.
     */
    protected function sendViaHttpV1(string $token, string $title, string $body, array $data, string $credentialsPath, ?string $projectId): bool
    {
        try {
            $json = json_decode(file_get_contents($credentialsPath), true);
            if (!$json || !isset($json['client_email']) || !isset($json['private_key'])) {
                Log::warning("FcmPushService: Invalid service account file at {$credentialsPath}");
                return false;
            }

            $projectId = $projectId ?: ($json['project_id'] ?? null);
            if (!$projectId) {
                Log::warning("FcmPushService: Missing project_id for FCM HTTP v1");
                return false;
            }

            $accessToken = $this->getGoogleAccessToken($json);
            if (!$accessToken) {
                return false;
            }

            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            // Format string-string data payload
            $stringData = [];
            foreach ($data as $k => $v) {
                $stringData[(string) $k] = is_array($v) ? json_encode($v) : (string) $v;
            }

            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => array_merge([
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'title' => $title,
                        'body' => $body,
                    ], $stringData),
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'default_sound' => true,
                            'default_vibrate_timings' => true,
                            'channel_id' => 'myads_general',
                        ],
                    ],
                ],
            ];

            $response = Http::withToken($accessToken)
                ->post($url, $payload);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("FcmPushService HTTP v1 error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send push notification via legacy FCM endpoint.
     */
    protected function sendViaLegacy(string $token, string $title, string $body, array $data, string $serverKey): bool
    {
        try {
            $stringData = [];
            foreach ($data as $k => $v) {
                $stringData[(string) $k] = is_array($v) ? json_encode($v) : (string) $v;
            }

            $payload = [
                'to' => $token,
                'priority' => 'high',
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'sound' => 'default',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                ],
                'data' => array_merge([
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'title' => $title,
                    'body' => $body,
                ], $stringData),
            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("FcmPushService legacy error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate OAuth2 Access Token using firebase/php-jwt.
     */
    protected function getGoogleAccessToken(array $serviceAccount): ?string
    {
        try {
            $now = time();
            $payload = [
                'iss' => $serviceAccount['client_email'],
                'sub' => $serviceAccount['client_email'],
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            ];

            $jwt = \Firebase\JWT\JWT::encode($payload, $serviceAccount['private_key'], 'RS256');

            $res = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($res->successful()) {
                return $res->json('access_token');
            }

            Log::error("FcmPushService: OAuth token request failed: " . $res->body());
            return null;
        } catch (\Throwable $e) {
            Log::error("FcmPushService: Error generating JWT token: " . $e->getMessage());
            return null;
        }
    }
}

