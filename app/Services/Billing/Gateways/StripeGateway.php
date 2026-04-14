<?php

namespace App\Services\Billing\Gateways;

use App\Models\BillingOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StripeGateway extends AbstractBillingGateway
{
    public function key(): string
    {
        return 'stripe';
    }

    public function label(): string
    {
        return __('messages.billing_gateway_stripe');
    }

    public function createCheckout(BillingOrder $order): array
    {
        $config = $this->ensureEnabled();
        $this->requireConfig($config, 'secret_key');

        $decimals = $this->decimalPlacesFor($order);
        $unitAmount = (int) round((float) $order->display_amount * (10 ** $decimals));
        $planName = (string) data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan'));
        $planDescription = trim((string) data_get($order->plan_snapshot, 'description', ''));

        $requestPayload = [
            'mode' => 'payment',
            'success_url' => route('billing.return', ['gateway' => $this->key(), 'order' => $order->id]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('billing.orders.show', $order->id),
            'client_reference_id' => (string) $order->id,
            'metadata[local_order_id]' => (string) $order->id,
            'metadata[order_number]' => $order->order_number,
            'line_items[0][price_data][currency]' => strtolower((string) $order->currency_code),
            'line_items[0][price_data][unit_amount]' => $unitAmount,
            'line_items[0][price_data][product_data][name]' => $planName,
            'line_items[0][quantity]' => 1,
        ];

        if ($planDescription !== '') {
            $requestPayload['line_items[0][price_data][product_data][description]'] = $planDescription;
        }

        $payload = $this->assertSuccessful(
            $this->http()
                ->withToken((string) $config['secret_key'])
                ->asForm()
                ->post('https://api.stripe.com/v1/checkout/sessions', $requestPayload)
        );

        $checkoutUrl = trim((string) data_get($payload, 'url', ''));
        $sessionId = trim((string) data_get($payload, 'id', ''));

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
                'provider' => 'stripe',
                'checkout_session_id' => $sessionId,
            ],
        ];
    }

    public function handleReturn(Request $request, BillingOrder $order): array
    {
        $config = $this->ensureEnabled();
        $this->requireConfig($config, 'secret_key');

        $sessionId = trim((string) $request->query('session_id', ''));
        if ($sessionId === '') {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_return_state'),
            ]);
        }

        $payload = $this->assertSuccessful(
            $this->http()
                ->withToken((string) $config['secret_key'])
                ->get('https://api.stripe.com/v1/checkout/sessions/' . $sessionId, [
                    'expand' => ['payment_intent'],
                ])
        );

        if ((string) data_get($payload, 'metadata.local_order_id') !== (string) $order->id) {
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

        if ($secret === '' || !$this->isValidWebhookSignature((string) $request->header('Stripe-Signature', ''), $request->getContent(), $secret)) {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_webhook_signature'),
            ]);
        }

        $type = (string) data_get($payload, 'type', '');
        if (!in_array($type, ['checkout.session.completed', 'checkout.session.async_payment_succeeded', 'checkout.session.expired'], true)) {
            return null;
        }

        $object = data_get($payload, 'data.object', []);
        if (!is_array($object)) {
            return null;
        }

        return array_merge(
            $this->normalizeTransaction($object),
            [
                'event_type' => $type,
                'local_order_id' => (int) data_get($object, 'metadata.local_order_id', 0),
                'gateway_checkout_reference' => (string) data_get($object, 'id', ''),
            ]
        );
    }

    public function normalizeTransaction(array $payload, ?BillingOrder $order = null): array
    {
        $paymentStatus = (string) data_get($payload, 'payment_status', '');
        $checkoutStatus = (string) data_get($payload, 'status', '');
        $decimals = $this->decimalPlacesFor($order);

        $status = match (true) {
            in_array($paymentStatus, ['paid', 'no_payment_required'], true) => BillingOrder::STATUS_PAID,
            $checkoutStatus === 'expired' => BillingOrder::STATUS_FAILED,
            default => BillingOrder::STATUS_PENDING_CHECKOUT,
        };

        return [
            'status' => $status,
            'external_transaction_id' => (string) (data_get($payload, 'payment_intent.id')
                ?: data_get($payload, 'payment_intent')
                ?: data_get($payload, 'id')
                ?: ''),
            'gateway_checkout_reference' => (string) data_get($payload, 'id', ''),
            'amount' => $this->normalizeMinorAmount((int) data_get($payload, 'amount_total', 0), $decimals),
            'currency_code' => strtoupper((string) data_get($payload, 'currency', $order?->currency_code ?? 'USD')),
            'meta' => [
                'payment_status' => $paymentStatus,
                'checkout_status' => $checkoutStatus,
                'provider_object' => data_get($payload, 'object'),
            ],
        ];
    }

    private function isValidWebhookSignature(string $header, string $payload, string $secret): bool
    {
        $parts = [];
        foreach (explode(',', $header) as $segment) {
            $pair = explode('=', trim($segment), 2);
            if (count($pair) === 2) {
                $parts[$pair[0]][] = $pair[1];
            }
        }

        $timestamp = (int) ($parts['t'][0] ?? 0);
        $signatures = $parts['v1'] ?? [];
        if ($timestamp <= 0 || $signatures === []) {
            return false;
        }

        if (abs(time() - $timestamp) > 300) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);

        foreach ($signatures as $signature) {
            if (hash_equals($expected, $signature)) {
                return true;
            }
        }

        return false;
    }
}
