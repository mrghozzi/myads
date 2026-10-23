<?php

namespace App\Services;

use App\Models\DeveloperApp;
use App\Models\DeveloperWebhook;
use App\Models\DeveloperWebhookDelivery;
use App\Models\DeveloperAuthorization;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebhookDispatcherService
{
    /**
     * Standard list of supported webhook events and descriptions.
     */
    public static function getSupportedEvents(): array
    {
        return [
            'user.followed' => [
                'name' => 'messages.webhook_event_user_followed',
                'description' => 'Triggered when a member follows another user or app owner.',
                'category' => 'social',
            ],
            'post.created' => [
                'name' => 'messages.webhook_event_post_created',
                'description' => 'Triggered when a member publishes a new post or forum topic.',
                'category' => 'content',
            ],
            'post.reacted' => [
                'name' => 'messages.webhook_event_post_reacted',
                'description' => 'Triggered when a member likes or reacts to a post.',
                'category' => 'content',
            ],
            'message.sent' => [
                'name' => 'messages.webhook_event_message_sent',
                'description' => 'Triggered when a direct message is sent or received.',
                'category' => 'messaging',
            ],
            'order.created' => [
                'name' => 'messages.webhook_event_order_created',
                'description' => 'Triggered when a new service order proposal or request is created.',
                'category' => 'marketplace',
            ],
            'order.completed' => [
                'name' => 'messages.webhook_event_order_completed',
                'description' => 'Triggered when a service order is successfully completed and rated.',
                'category' => 'marketplace',
            ],
            'wallet.points_updated' => [
                'name' => 'messages.webhook_event_wallet_updated',
                'description' => 'Triggered when user Points (PTS) balance changes.',
                'category' => 'wallet',
            ],
        ];
    }

    /**
     * Dispatch an event to all subscribed webhooks.
     */
    public function dispatch(string $event, array $payload, ?int $userId = null, ?int $appId = null): void
    {
        try {
            $webhooksQuery = DeveloperWebhook::where('is_active', true);

            if ($appId) {
                $webhooksQuery->where('developer_app_id', $appId);
            } elseif ($userId) {
                // Find all apps authorized by this user or owned by this user
                $authorizedAppIds = DeveloperAuthorization::where('user_id', $userId)
                    ->pluck('developer_app_id')
                    ->toArray();

                $ownedAppIds = DeveloperApp::where('user_id', $userId)
                    ->pluck('id')
                    ->toArray();

                $candidateAppIds = array_unique(array_merge($authorizedAppIds, $ownedAppIds));

                if (empty($candidateAppIds)) {
                    return;
                }

                $webhooksQuery->whereIn('developer_app_id', $candidateAppIds);
            }

            $webhooks = $webhooksQuery->get();

            foreach ($webhooks as $webhook) {
                if ($webhook->isSubscribedTo($event)) {
                    $this->deliver($webhook, $event, $payload);
                }
            }
        } catch (\Throwable $e) {
            Log::error('[WebhookDispatcherService:dispatch] Error: ' . $e->getMessage(), [
                'event' => $event,
                'user_id' => $userId,
                'app_id' => $appId,
            ]);
        }
    }

    /**
     * Deliver a payload to a specific webhook endpoint.
     */
    public function deliver(DeveloperWebhook $webhook, string $event, array $data, int $attempt = 1): DeveloperWebhookDelivery
    {
        $delivery = new DeveloperWebhookDelivery([
            'developer_webhook_id' => $webhook->id,
            'developer_app_id' => $webhook->developer_app_id,
            'event' => $event,
            'attempts' => $attempt,
        ]);

        $eventId = 'evt_' . Str::random(24);
        $timestamp = time();

        $envelope = [
            'id' => $eventId,
            'event' => $event,
            'timestamp' => $timestamp,
            'app_id' => $webhook->developer_app_id,
            'data' => $data,
        ];

        $delivery->payload = $envelope;
        $jsonPayload = json_encode($envelope, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $signature = 'sha256=' . hash_hmac('sha256', $jsonPayload, $webhook->secret);

        $host = strtolower(parse_url($webhook->url, PHP_URL_HOST) ?? '');
        if ($host === '169.254.169.254' || str_starts_with($host, '169.254.')) {
            $delivery->status_code = 0;
            $delivery->error_message = 'Blocked: Link-local and cloud metadata endpoints are prohibited.';
            $delivery->duration_ms = 0;
            $delivery->delivered_at = now();
            $delivery->save();
            return $delivery;
        }

        if (app()->isProduction()) {
            if ($host === 'localhost' || str_ends_with($host, '.localhost') || (filter_var($host, FILTER_VALIDATE_IP) && filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false)) {
                $delivery->status_code = 0;
                $delivery->error_message = 'Blocked: Private or local destinations are prohibited in production.';
                $delivery->duration_ms = 0;
                $delivery->delivered_at = now();
                $delivery->save();
                return $delivery;
            }
        }
        $startTime = microtime(true);
        $responseBody = null;
        $errorMessage = null;

        try {
            $response = Http::timeout(6)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'MYADS-Webhook/4.6.4',
                    'X-MYADS-Signature-256' => $signature,
                    'X-MYADS-Event' => $event,
                    'X-MYADS-Delivery' => $eventId,
                ])
                ->withBody($jsonPayload, 'application/json')
                ->post($webhook->url);

            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            $statusCode = $response->status();
            $responseBody = Str::limit($response->body(), 2000);

            if ($response->successful()) {
                $webhook->update(['failure_count' => 0]);
            } else {
                $webhook->increment('failure_count');
                $errorMessage = 'HTTP ' . $statusCode . ': ' . Str::limit($response->body(), 200);
            }
        } catch (\Throwable $e) {
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            $statusCode = 0;
            $errorMessage = Str::limit($e->getMessage(), 500);
            $webhook->increment('failure_count');
        }

        $delivery->status_code = $statusCode;
        $delivery->response_body = $responseBody;
        $delivery->duration_ms = $durationMs;
        $delivery->error_message = $errorMessage;
        $delivery->delivered_at = now();
        $delivery->save();

        return $delivery;
    }

    /**
     * Send a test ping event to verify connectivity.
     */
    public function testPing(DeveloperWebhook $webhook): DeveloperWebhookDelivery
    {
        $testPayload = [
            'message' => 'Ping test from MYADS Developer Platform',
            'webhook_id' => $webhook->id,
            'url' => $webhook->url,
            'test' => true,
        ];

        return $this->deliver($webhook, 'ping', $testPayload);
    }

    /**
     * Redeliver an existing past webhook delivery.
     */
    public function redeliver(DeveloperWebhookDelivery $pastDelivery): DeveloperWebhookDelivery
    {
        $webhook = $pastDelivery->webhook;
        if (!$webhook) {
            throw new \RuntimeException('Webhook endpoint not found');
        }

        $payload = $pastDelivery->payload['data'] ?? $pastDelivery->payload ?? [];
        return $this->deliver($webhook, $pastDelivery->event, $payload, $pastDelivery->attempts + 1);
    }
}
