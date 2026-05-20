<?php

namespace App\Services\Billing\Gateways;

use App\Models\BillingOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FlouciGateway extends AbstractBillingGateway
{
    public function key(): string
    {
        return 'flouci';
    }

    public function label(): string
    {
        return __('messages.billing_gateway_flouci');
    }

    public function createCheckout(BillingOrder $order): array
    {
        $config = $this->ensureEnabled();
        $this->requireConfig($config, 'public_key', 'secret_key');

        $decimals = strtoupper($order->currency_code) === 'TND' ? 3 : $this->decimalPlacesFor($order);
        $unitAmount = (int) round((float) $order->display_amount * (10 ** $decimals));

        $payload = $this->assertSuccessful(
            $this->http()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['public_key'] . ':' . $config['secret_key'],
                ])
                ->post('https://developers.flouci.com/api/v2/generate_payment', [
                    'amount' => $unitAmount,
                    'success_link' => route('billing.return', ['gateway' => $this->key(), 'order' => $order->id]),
                    'fail_link' => route('billing.orders.show', $order->id),
                    'webhook' => route('billing.webhook', ['gateway' => $this->key()]),
                    'developer_tracking_id' => (string) $order->id,
                ])
        );

        $checkoutUrl = trim((string) data_get($payload, 'result.link', ''));
        $paymentId = trim((string) data_get($payload, 'result.payment_id', ''));

        if ($checkoutUrl === '' || $paymentId === '') {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_gateway_response'),
            ]);
        }

        return [
            'checkout_url' => $checkoutUrl,
            'gateway_checkout_reference' => $paymentId,
            'status' => BillingOrder::STATUS_PENDING_CHECKOUT,
            'meta' => [
                'provider' => 'flouci',
                'flouci_payment_id' => $paymentId,
            ],
        ];
    }

    public function handleReturn(Request $request, BillingOrder $order): array
    {
        $config = $this->ensureEnabled();
        $this->requireConfig($config, 'public_key', 'secret_key');

        $paymentId = trim((string) $request->query('payment_id', ''));
        if ($paymentId === '') {
            $paymentId = (string) $order->gateway_checkout_reference;
        }

        if ($paymentId === '') {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_return_state'),
            ]);
        }

        $payload = $this->assertSuccessful(
            $this->http()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $config['public_key'] . ':' . $config['secret_key'],
                ])
                ->get('https://developers.flouci.com/api/v2/verify_payment/' . $paymentId)
        );

        return $this->normalizeTransaction($payload, $order);
    }

    public function handleWebhook(Request $request): ?array
    {
        $config = $this->config();
        
        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return null;
        }

        $paymentId = trim((string) data_get($payload, 'payment_id', ''));
        if ($paymentId === '') {
            return null;
        }

        $verifyPayload = $this->assertSuccessful(
            $this->http()
                ->withHeaders([
                    'Authorization' => 'Bearer ' . ($config['public_key'] ?? '') . ':' . ($config['secret_key'] ?? ''),
                ])
                ->get('https://developers.flouci.com/api/v2/verify_payment/' . $paymentId)
        );

        $normalized = $this->normalizeTransaction($verifyPayload);

        return array_merge($normalized, [
            'event_type' => 'webhook_received',
            'local_order_id' => (int) data_get($payload, 'developer_tracking_id', data_get($verifyPayload, 'result.developer_tracking_id', 0)),
            'gateway_checkout_reference' => $paymentId,
        ]);
    }

    public function normalizeTransaction(array $payload, ?BillingOrder $order = null): array
    {
        $apiSuccess = (bool) data_get($payload, 'success', false);
        $result = data_get($payload, 'result', []);
        
        $flouciStatus = strtoupper((string) data_get($result, 'status', ''));
        $paymentId = (string) data_get($result, 'payment_id', '');

        $status = BillingOrder::STATUS_PENDING_CHECKOUT;
        if ($apiSuccess && $flouciStatus === 'SUCCESS') {
            $status = BillingOrder::STATUS_PAID;
        } elseif ($flouciStatus === 'FAILURE' || $flouciStatus === 'EXPIRED') {
            $status = BillingOrder::STATUS_FAILED;
        }

        $decimals = strtoupper($order?->currency_code ?? '') === 'TND' ? 3 : $this->decimalPlacesFor($order);
        $amountMinor = (int) data_get($result, 'amount', 0);
        $amount = $this->normalizeMinorAmount($amountMinor, $decimals);

        return [
            'status' => $status,
            'external_transaction_id' => $paymentId,
            'gateway_checkout_reference' => $paymentId,
            'amount' => $amount > 0 ? $amount : ($order?->display_amount ?: 0.0),
            'currency_code' => $order?->currency_code ?? 'TND',
            'meta' => [
                'provider_object' => $payload,
                'flouci_payment_id' => $paymentId,
                'flouci_status' => $flouciStatus,
                'api_success' => $apiSuccess,
            ],
        ];
    }
}
