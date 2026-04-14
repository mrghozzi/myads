<?php

namespace App\Services\Billing\Gateways;

use App\Models\BillingOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PayPalGateway extends AbstractBillingGateway
{
    public function key(): string
    {
        return 'paypal';
    }

    public function label(): string
    {
        return __('messages.billing_gateway_paypal');
    }

    public function createCheckout(BillingOrder $order): array
    {
        $config = $this->ensureEnabled();
        $this->requireConfig($config, 'client_id', 'secret_key');

        $payload = $this->assertSuccessful(
            $this->http()
                ->withToken($this->accessToken($config))
                ->post($this->baseUrl($config) . '/v2/checkout/orders', [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'reference_id' => $order->order_number,
                        'custom_id' => (string) $order->id,
                        'description' => (string) data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan')),
                        'amount' => [
                            'currency_code' => (string) $order->currency_code,
                            'value' => number_format((float) $order->display_amount, 2, '.', ''),
                        ],
                    ]],
                    'application_context' => [
                        'brand_name' => config('app.name', 'MYADS'),
                        'return_url' => route('billing.return', ['gateway' => $this->key(), 'order' => $order->id]),
                        'cancel_url' => route('billing.orders.show', $order->id),
                        'user_action' => 'PAY_NOW',
                    ],
                ])
        );

        $approvalUrl = collect((array) data_get($payload, 'links', []))
            ->firstWhere('rel', 'approve')['href'] ?? '';
        $checkoutReference = trim((string) data_get($payload, 'id', ''));

        if (trim((string) $approvalUrl) === '' || $checkoutReference === '') {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_gateway_response'),
            ]);
        }

        return [
            'checkout_url' => $approvalUrl,
            'gateway_checkout_reference' => $checkoutReference,
            'status' => BillingOrder::STATUS_PENDING_CHECKOUT,
            'meta' => [
                'provider' => 'paypal',
                'paypal_order_id' => $checkoutReference,
            ],
        ];
    }

    public function handleReturn(Request $request, BillingOrder $order): array
    {
        $config = $this->ensureEnabled();
        $this->requireConfig($config, 'client_id', 'secret_key');

        $paypalOrderId = trim((string) $request->query('token', ''));
        if ($paypalOrderId === '') {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_return_state'),
            ]);
        }

        $token = $this->accessToken($config);
        $captureResponse = $this->http()
            ->withToken($token)
            ->post($this->baseUrl($config) . '/v2/checkout/orders/' . $paypalOrderId . '/capture');

        if ($captureResponse->failed()) {
            $payload = $this->assertSuccessful(
                $this->http()
                    ->withToken($token)
                    ->get($this->baseUrl($config) . '/v2/checkout/orders/' . $paypalOrderId)
            );
        } else {
            $payload = $this->assertSuccessful($captureResponse);
        }

        $customId = (string) data_get($payload, 'purchase_units.0.custom_id', '');
        if ($customId !== '' && $customId !== (string) $order->id) {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_return_state'),
            ]);
        }

        return $this->normalizeTransaction($payload, $order);
    }

    public function handleWebhook(Request $request): ?array
    {
        $config = $this->config();
        if (!$this->verifyWebhook($request, $config)) {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_webhook_signature'),
            ]);
        }

        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return null;
        }

        $eventType = (string) data_get($payload, 'event_type', '');
        if (!in_array($eventType, ['CHECKOUT.ORDER.APPROVED', 'PAYMENT.CAPTURE.COMPLETED', 'PAYMENT.CAPTURE.DENIED'], true)) {
            return null;
        }

        $resource = data_get($payload, 'resource', []);
        if (!is_array($resource)) {
            return null;
        }

        $localOrderId = (int) (
            data_get($resource, 'custom_id')
            ?: data_get($resource, 'purchase_units.0.custom_id')
            ?: 0
        );

        return array_merge(
            $this->normalizeTransaction($resource),
            [
                'event_type' => $eventType,
                'local_order_id' => $localOrderId,
                'gateway_checkout_reference' => (string) (
                    data_get($resource, 'supplementary_data.related_ids.order_id')
                    ?: data_get($resource, 'id')
                    ?: ''
                ),
            ]
        );
    }

    public function normalizeTransaction(array $payload, ?BillingOrder $order = null): array
    {
        $captureStatus = strtoupper((string) (
            data_get($payload, 'status')
            ?: data_get($payload, 'purchase_units.0.payments.captures.0.status')
            ?: ''
        ));

        $status = match ($captureStatus) {
            'COMPLETED' => BillingOrder::STATUS_PAID,
            'DECLINED', 'FAILED', 'VOIDED' => BillingOrder::STATUS_FAILED,
            default => BillingOrder::STATUS_PENDING_CHECKOUT,
        };

        return [
            'status' => $status,
            'external_transaction_id' => (string) (
                data_get($payload, 'purchase_units.0.payments.captures.0.id')
                ?: data_get($payload, 'id')
                ?: ''
            ),
            'gateway_checkout_reference' => (string) (
                data_get($payload, 'id')
                ?: data_get($payload, 'supplementary_data.related_ids.order_id')
                ?: ''
            ),
            'amount' => (float) (
                data_get($payload, 'purchase_units.0.payments.captures.0.amount.value')
                ?: data_get($payload, 'purchase_units.0.amount.value')
                ?: $order?->display_amount
                ?: 0
            ),
            'currency_code' => strtoupper((string) (
                data_get($payload, 'purchase_units.0.payments.captures.0.amount.currency_code')
                ?: data_get($payload, 'purchase_units.0.amount.currency_code')
                ?: $order?->currency_code
                ?: 'USD'
            )),
            'meta' => [
                'capture_status' => $captureStatus,
                'paypal_order_id' => data_get($payload, 'id'),
            ],
        ];
    }

    private function accessToken(array $config): string
    {
        $payload = $this->assertSuccessful(
            $this->http()
                ->withBasicAuth((string) $config['client_id'], (string) $config['secret_key'])
                ->asForm()
                ->post($this->baseUrl($config) . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ])
        );

        $token = trim((string) data_get($payload, 'access_token', ''));
        if ($token === '') {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_gateway_response'),
            ]);
        }

        return $token;
    }

    private function verifyWebhook(Request $request, array $config): bool
    {
        $webhookId = trim((string) ($config['webhook_id'] ?? ''));
        if ($webhookId === '') {
            return false;
        }

        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return false;
        }

        $verification = $this->assertSuccessful(
            $this->http()
                ->withToken($this->accessToken($config))
                ->post($this->baseUrl($config) . '/v1/notifications/verify-webhook-signature', [
                    'auth_algo' => (string) $request->header('Paypal-Auth-Algo', ''),
                    'cert_url' => (string) $request->header('Paypal-Cert-Url', ''),
                    'transmission_id' => (string) $request->header('Paypal-Transmission-Id', ''),
                    'transmission_sig' => (string) $request->header('Paypal-Transmission-Sig', ''),
                    'transmission_time' => (string) $request->header('Paypal-Transmission-Time', ''),
                    'webhook_id' => $webhookId,
                    'webhook_event' => $payload,
                ])
        );

        return strtoupper((string) data_get($verification, 'verification_status', '')) === 'SUCCESS';
    }

    private function baseUrl(array $config): string
    {
        return ($config['mode'] ?? 'sandbox') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }
}
