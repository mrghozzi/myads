<?php

namespace App\Services\Billing\Gateways;

use App\Models\BillingOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LemonSqueezyGateway extends AbstractBillingGateway
{
    public function key(): string
    {
        return 'lemon_squeezy';
    }

    public function label(): string
    {
        return __('messages.billing_gateway_lemon_squeezy');
    }

    public function createCheckout(BillingOrder $order): array
    {
        $config = $this->ensureEnabled();
        $this->requireConfig($config, 'api_key');
        $this->requireConfig($config, 'store_id');
        $this->requireConfig($config, 'variant_id');

        $decimals = $this->decimalPlacesFor($order);
        $unitAmount = (int) round((float) $order->display_amount * (10 ** $decimals));
        $planName = (string) data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan'));

        $requestPayload = [
            'data' => [
                'type' => 'checkouts',
                'attributes' => [
                    'custom_price' => $unitAmount,
                    'product_options' => [
                        'name' => $planName,
                        'redirect_url' => route('billing.return', ['gateway' => $this->key(), 'order' => $order->id]),
                    ],
                    'checkout_data' => [
                        'custom' => [
                            'local_order_id' => (string) $order->id,
                        ],
                    ],
                ],
                'relationships' => [
                    'store' => [
                        'data' => [
                            'type' => 'stores',
                            'id' => (string) $config['store_id'],
                        ],
                    ],
                    'variant' => [
                        'data' => [
                            'type' => 'variants',
                            'id' => (string) $config['variant_id'],
                        ],
                    ],
                ],
            ],
        ];

        $payload = $this->assertSuccessful(
            $this->http()
                ->withToken((string) $config['api_key'])
                ->accept('application/vnd.api+json')
                ->contentType('application/vnd.api+json')
                ->post('https://api.lemonsqueezy.com/v1/checkouts', $requestPayload)
        );

        $checkoutUrl = trim((string) data_get($payload, 'data.attributes.url', ''));
        $sessionId = trim((string) data_get($payload, 'data.id', ''));

        if ($checkoutUrl === '' || $sessionId === '') {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_gateway_response'),
            ]);
        }

        return [
            'checkout_url' => $checkoutUrl,
            'gateway_checkout_reference' => $sessionId,
            'status' => BillingOrder::STATUS_PENDING_CHECKOUT,
            'meta' => [
                'provider' => 'lemon_squeezy',
                'checkout_id' => $sessionId,
            ],
        ];
    }

    public function handleReturn(Request $request, BillingOrder $order): array
    {
        $config = $this->ensureEnabled();
        $this->requireConfig($config, 'api_key');

        $sessionId = (string) $order->gateway_checkout_reference;
        if ($sessionId === '') {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_return_state'),
            ]);
        }

        $payload = $this->assertSuccessful(
            $this->http()
                ->withToken((string) $config['api_key'])
                ->accept('application/vnd.api+json')
                ->get('https://api.lemonsqueezy.com/v1/checkouts/' . $sessionId)
        );

        if ((string) data_get($payload, 'data.attributes.checkout_data.custom.local_order_id') !== (string) $order->id) {
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

        if ($secret === '' || !$this->isValidWebhookSignature((string) $request->header('X-Signature', ''), $request->getContent(), $secret)) {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_webhook_signature'),
            ]);
        }

        $eventName = (string) $request->header('X-Event-Name', '');
        
        if (!in_array($eventName, ['order_created'], true)) {
            return null;
        }

        $object = data_get($payload, 'data', []);
        if (!is_array($object)) {
            return null;
        }

        return array_merge(
            $this->normalizeTransaction($object),
            [
                'event_type' => $eventName,
                'local_order_id' => (int) data_get($object, 'attributes.custom_data.local_order_id', 0),
                'gateway_checkout_reference' => (string) data_get($object, 'id', ''),
            ]
        );
    }

    public function normalizeTransaction(array $payload, ?BillingOrder $order = null): array
    {
        // $payload is either checkout payload (from handleReturn) or order payload (from webhook)
        $type = (string) data_get($payload, 'data.type', data_get($payload, 'type', ''));
        $attributes = (array) data_get($payload, 'data.attributes', data_get($payload, 'attributes', []));
        $decimals = $this->decimalPlacesFor($order);

        $status = BillingOrder::STATUS_PENDING_CHECKOUT;
        
        if ($type === 'orders' || $type === 'order_created') {
            $orderStatus = (string) data_get($attributes, 'status', '');
            if (in_array($orderStatus, ['paid', 'pending'], true)) {
                $status = BillingOrder::STATUS_PAID;
            } else {
                $status = BillingOrder::STATUS_FAILED;
            }
        } elseif ($type === 'checkouts') {
            // Checkouts themselves don't have a direct 'paid' status in attributes the same way,
            // they rely on webhooks to convert checkouts to orders. So if returning from checkout, it's pending checkout
            // unless we want to query orders for this checkout, but let's keep it simple. The webhook will handle the completion.
            $status = BillingOrder::STATUS_PENDING_CHECKOUT;
        }

        $amountTotal = (int) data_get($attributes, 'total', data_get($attributes, 'custom_price', 0));
        $currency = strtoupper((string) data_get($attributes, 'currency', $order?->currency_code ?? 'USD'));

        return [
            'status' => $status,
            'external_transaction_id' => (string) data_get($payload, 'data.id', data_get($payload, 'id', '')),
            'gateway_checkout_reference' => (string) data_get($payload, 'data.id', data_get($payload, 'id', '')),
            'amount' => $this->normalizeMinorAmount($amountTotal, $decimals),
            'currency_code' => $currency,
            'meta' => [
                'provider_object' => $payload,
                'lemon_squeezy_status' => data_get($attributes, 'status', ''),
            ],
        ];
    }

    private function isValidWebhookSignature(string $header, string $payload, string $secret): bool
    {
        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $header);
    }
}
