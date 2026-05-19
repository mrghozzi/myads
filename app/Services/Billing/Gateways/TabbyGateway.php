<?php

namespace App\Services\Billing\Gateways;

use App\Models\BillingOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TabbyGateway extends AbstractBillingGateway
{
    public function key(): string
    {
        return 'tabby';
    }

    public function label(): string
    {
        return __('messages.billing_gateway_tabby');
    }

    public function createCheckout(BillingOrder $order): array
    {
        $config = $this->ensureEnabled();
        $this->requireConfig($config, 'public_key', 'secret_key');

        $decimals = $this->decimalPlacesFor($order);
        $amount = number_format((float) $order->display_amount, $decimals, '.', '');

        $planName = (string) data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan'));
        $user = $order->user;

        $phone = (string) data_get($order->meta, 'phone_number');
        if (trim($phone) === '') {
            throw ValidationException::withMessages([
                'phone_number' => __('messages.billing_phone_number_required'),
            ]);
        }

        $requestPayload = [
            'payment' => [
                'amount' => $amount,
                'currency' => strtoupper((string) $order->currency_code),
                'buyer' => [
                    'phone' => $phone,
                    'email' => (string) $user->email,
                    'name' => (string) $user->name,
                ],
                'order' => [
                    'reference_id' => (string) $order->id,
                    'items' => [
                        [
                            'title' => $planName,
                            'quantity' => 1,
                            'unit_price' => $amount,
                        ]
                    ],
                ],
            ],
            'lang' => app()->getLocale() === 'ar' ? 'ar' : 'en',
            'merchant_urls' => [
                'success' => route('billing.return', ['gateway' => $this->key(), 'order' => $order->id]),
                'cancel' => route('billing.orders.show', $order->id),
                'failure' => route('billing.orders.show', $order->id),
            ],
        ];

        $response = $this->http()
            ->withToken((string) $config['secret_key'])
            ->withHeaders(array_filter([
                'X-Merchant-Code' => (string) ($config['merchant_code'] ?? ''),
            ]))
            ->post($this->baseUrl() . '/api/v2/checkout', $requestPayload);

        $payload = $this->assertSuccessful($response);

        $checkoutUrl = data_get($payload, 'configuration.available_products.installments.0.web_url')
            ?: data_get($payload, 'available_products.0.web_url')
            ?: data_get($payload, 'available_products.0.webUrl')
            ?: data_get($payload, 'configuration.available_products.installments.0.webUrl');

        $paymentId = data_get($payload, 'payment.id') ?: data_get($payload, 'id');

        if (!$checkoutUrl || !$paymentId) {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_gateway_response'),
            ]);
        }

        return [
            'checkout_url' => $checkoutUrl,
            'gateway_checkout_reference' => $paymentId,
            'status' => BillingOrder::STATUS_PENDING_CHECKOUT,
            'meta' => [
                'provider' => 'tabby',
                'payment_id' => $paymentId,
                'phone_number' => $phone,
            ],
        ];
    }

    public function handleReturn(Request $request, BillingOrder $order): array
    {
        $config = $this->ensureEnabled();
        $this->requireConfig($config, 'secret_key');

        $paymentId = $request->query('payment_id') ?: $order->gateway_checkout_reference;

        if (!$paymentId) {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_return_state'),
            ]);
        }

        $paymentDetails = $this->fetchPaymentDetails((string) $paymentId);

        if (strtoupper((string) data_get($paymentDetails, 'status')) === 'AUTHORIZED') {
            $paymentDetails = $this->capturePayment((string) $paymentId, $order);
        }

        return $this->normalizeTransaction($paymentDetails, $order);
    }

    public function handleWebhook(Request $request): ?array
    {
        $config = $this->config();
        $secret = trim((string) ($config['webhook_secret'] ?? ''));

        if ($secret !== '') {
            $signature = $request->header('X-Tabby-Signature') ?: $request->header('x-tabby-signature');
            if (!$signature || !hash_equals($secret, $signature)) {
                throw ValidationException::withMessages([
                    'gateway' => __('messages.billing_invalid_webhook_signature'),
                ]);
            }
        }

        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return null;
        }

        $paymentId = data_get($payload, 'id') ?: data_get($payload, 'payment.id');
        if (!$paymentId) {
            return null;
        }

        $paymentDetails = $this->fetchPaymentDetails((string) $paymentId);

        $orderReferenceId = (int) data_get($paymentDetails, 'order.reference_id');
        $order = BillingOrder::query()->find($orderReferenceId);

        if ($order && strtoupper((string) data_get($paymentDetails, 'status')) === 'AUTHORIZED') {
            $paymentDetails = $this->capturePayment((string) $paymentId, $order);
        }

        return array_merge(
            $this->normalizeTransaction($paymentDetails, $order),
            [
                'event_type' => 'tabby_webhook',
                'local_order_id' => $orderReferenceId,
                'gateway_checkout_reference' => (string) $paymentId,
            ]
        );
    }

    public function normalizeTransaction(array $payload, ?BillingOrder $order = null): array
    {
        $statusStr = strtoupper((string) data_get($payload, 'status', ''));
        $decimals = $this->decimalPlacesFor($order);

        $status = match ($statusStr) {
            'CLOSED' => BillingOrder::STATUS_PAID,
            'AUTHORIZED' => BillingOrder::STATUS_PAID, // Treat authorized as paid because capture will trigger immediately
            'REJECTED' => BillingOrder::STATUS_FAILED,
            'EXPIRED' => BillingOrder::STATUS_FAILED,
            default => BillingOrder::STATUS_PENDING_CHECKOUT,
        };

        return [
            'status' => $status,
            'external_transaction_id' => (string) (data_get($payload, 'id') ?: ''),
            'gateway_checkout_reference' => (string) (data_get($payload, 'id') ?: ''),
            'amount' => $this->normalizeMinorAmount((int) round((float) data_get($payload, 'amount', 0) * (10 ** $decimals)), $decimals),
            'currency_code' => strtoupper((string) data_get($payload, 'currency', $order?->currency_code ?? 'USD')),
            'meta' => [
                'tabby_status' => $statusStr,
                'provider_object' => $payload,
            ],
        ];
    }

    private function fetchPaymentDetails(string $paymentId): array
    {
        $config = $this->config();
        $response = $this->http()
            ->withToken((string) $config['secret_key'])
            ->get($this->baseUrl() . '/api/v2/payments/' . $paymentId);

        return $this->assertSuccessful($response);
    }

    private function capturePayment(string $paymentId, BillingOrder $order): array
    {
        $config = $this->config();
        $decimals = $this->decimalPlacesFor($order);
        $amount = number_format((float) $order->display_amount, $decimals, '.', '');

        $response = $this->http()
            ->withToken((string) $config['secret_key'])
            ->post($this->baseUrl() . '/api/v2/payments/' . $paymentId . '/captures', [
                'amount' => $amount,
            ]);

        // If capture API call is successful, re-fetch or use capture response.
        // Let's assert successful response. If it fails, assertSuccessful will throw validation error.
        $this->assertSuccessful($response);

        // Fetch payment details again to get the CLOSED status
        return $this->fetchPaymentDetails($paymentId);
    }

    private function baseUrl(): string
    {
        $config = $this->config();
        $region = strtoupper(trim((string) ($config['region'] ?? 'UAE')));
        return $region === 'KSA' ? 'https://api.tabby.sa' : 'https://api.tabby.ai';
    }
}
