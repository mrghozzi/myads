<?php

namespace App\Services\Billing\Gateways;

use App\Models\BillingOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaddleGateway extends AbstractBillingGateway
{
    public function key(): string
    {
        return 'paddle';
    }

    public function label(): string
    {
        return __('messages.billing_gateway_paddle');
    }

    public function createCheckout(BillingOrder $order): array
    {
        $config = $this->ensureEnabled();
        $this->requireConfig($config, 'api_key');
        $this->requireConfig($config, 'price_id');

        $planName = (string) data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan'));

        $requestPayload = [
            'items' => [
                [
                    'price_id' => (string) $config['price_id'],
                    'quantity' => 1,
                ],
            ],
            'custom_data' => [
                'local_order_id' => (string) $order->id,
                'order_number' => $order->order_number,
                'plan_name' => $planName,
            ],
            'checkout' => [
                'url' => route('billing.return', ['gateway' => $this->key(), 'order' => $order->id]),
            ],
        ];

        $payload = $this->assertSuccessful(
            $this->http()
                ->withToken((string) $config['api_key'])
                ->post($this->baseUrl($config) . '/transactions', $requestPayload)
        );

        $transactionId = trim((string) data_get($payload, 'data.id', ''));
        $checkoutUrl = trim((string) data_get($payload, 'data.checkout.url', ''));

        if ($transactionId === '' || $checkoutUrl === '') {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_gateway_response'),
            ]);
        }

        return [
            'checkout_url' => $checkoutUrl,
            'gateway_checkout_reference' => $transactionId,
            'status' => BillingOrder::STATUS_PENDING_CHECKOUT,
            'meta' => [
                'provider' => 'paddle',
                'paddle_transaction_id' => $transactionId,
            ],
        ];
    }

    public function handleReturn(Request $request, BillingOrder $order): array
    {
        $config = $this->ensureEnabled();
        $this->requireConfig($config, 'api_key');

        $transactionId = (string) $order->gateway_checkout_reference;
        if ($transactionId === '') {
            // Try to get from query string (_ptxn parameter)
            $transactionId = trim((string) $request->query('_ptxn', ''));
        }

        if ($transactionId === '') {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_return_state'),
            ]);
        }

        $payload = $this->assertSuccessful(
            $this->http()
                ->withToken((string) $config['api_key'])
                ->get($this->baseUrl($config) . '/transactions/' . $transactionId)
        );

        $customOrderId = (string) data_get($payload, 'data.custom_data.local_order_id', '');
        if ($customOrderId !== '' && $customOrderId !== (string) $order->id) {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_return_state'),
            ]);
        }

        return $this->normalizeTransaction($payload, $order);
    }

    public function handleWebhook(Request $request): ?array
    {
        $config = $this->config();
        $secret = trim((string) ($config['webhook_secret'] ?? ''));
        $payload = json_decode($request->getContent(), true);

        if (!is_array($payload)) {
            return null;
        }

        if ($secret === '' || !$this->isValidWebhookSignature((string) $request->header('Paddle-Signature', ''), $request->getContent(), $secret)) {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_webhook_signature'),
            ]);
        }

        $eventType = (string) data_get($payload, 'event_type', '');

        if (!in_array($eventType, ['transaction.completed', 'transaction.payment_failed', 'transaction.updated'], true)) {
            return null;
        }

        $data = data_get($payload, 'data', []);
        if (!is_array($data)) {
            return null;
        }

        return array_merge(
            $this->normalizeTransaction(['data' => $data]),
            [
                'event_type' => $eventType,
                'local_order_id' => (int) data_get($data, 'custom_data.local_order_id', 0),
                'gateway_checkout_reference' => (string) data_get($data, 'id', ''),
            ]
        );
    }

    public function normalizeTransaction(array $payload, ?BillingOrder $order = null): array
    {
        $data = (array) data_get($payload, 'data', $payload);
        $status = strtolower((string) data_get($data, 'status', ''));
        $decimals = $this->decimalPlacesFor($order);

        $billingStatus = match ($status) {
            'completed' => BillingOrder::STATUS_PAID,
            'canceled', 'past_due' => BillingOrder::STATUS_FAILED,
            default => BillingOrder::STATUS_PENDING_CHECKOUT,
        };

        // Paddle amounts in details.totals are in minor units (cents)
        $amountTotal = (int) data_get($data, 'details.totals.grand_total', data_get($data, 'details.totals.total', 0));
        $currency = strtoupper((string) data_get($data, 'currency_code', $order?->currency_code ?? 'USD'));

        return [
            'status' => $billingStatus,
            'external_transaction_id' => (string) data_get($data, 'id', ''),
            'gateway_checkout_reference' => (string) data_get($data, 'id', ''),
            'amount' => $this->normalizeMinorAmount($amountTotal, $decimals),
            'currency_code' => $currency,
            'meta' => [
                'provider_object' => $data,
                'paddle_status' => $status,
                'paddle_transaction_id' => (string) data_get($data, 'id', ''),
            ],
        ];
    }

    private function isValidWebhookSignature(string $header, string $payload, string $secret): bool
    {
        // Paddle-Signature format: ts=timestamp;h1=hash
        $parts = [];
        foreach (explode(';', $header) as $segment) {
            $pair = explode('=', trim($segment), 2);
            if (count($pair) === 2) {
                $parts[$pair[0]] = $pair[1];
            }
        }

        $timestamp = (string) ($parts['ts'] ?? '');
        $signature = (string) ($parts['h1'] ?? '');

        if ($timestamp === '' || $signature === '') {
            return false;
        }

        // Guard against replay attacks (5 minute tolerance)
        if (abs(time() - (int) $timestamp) > 300) {
            return false;
        }

        $signedPayload = $timestamp . ':' . $payload;
        $expected = hash_hmac('sha256', $signedPayload, $secret);

        return hash_equals($expected, $signature);
    }

    private function baseUrl(array $config): string
    {
        return ($config['mode'] ?? 'sandbox') === 'live'
            ? 'https://api.paddle.com'
            : 'https://sandbox-api.paddle.com';
    }
}
