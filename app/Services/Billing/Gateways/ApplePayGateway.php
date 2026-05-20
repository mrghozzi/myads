<?php

namespace App\Services\Billing\Gateways;

use App\Models\BillingOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApplePayGateway extends AbstractBillingGateway
{
    public function key(): string
    {
        return 'apple_pay';
    }

    public function label(): string
    {
        return __('messages.billing_gateway_apple_pay');
    }

    public function createCheckout(BillingOrder $order): array
    {
        $config = $this->ensureEnabled();

        $reference = 'ap_ref_' . bin2hex(random_bytes(8));

        return [
            'checkout_url' => route('billing.apple_pay.simulate', $order->id),
            'gateway_checkout_reference' => $reference,
            'status' => BillingOrder::STATUS_PENDING_CHECKOUT,
            'meta' => [
                'provider' => 'apple_pay',
                'apple_pay_reference' => $reference,
            ],
        ];
    }

    public function handleReturn(Request $request, BillingOrder $order): array
    {
        $this->ensureEnabled();

        $statusParam = $request->query('status');
        
        $status = BillingOrder::STATUS_PENDING_CHECKOUT;
        if ($statusParam === 'success') {
            $status = BillingOrder::STATUS_PAID;
        } elseif ($statusParam === 'failed') {
            $status = BillingOrder::STATUS_FAILED;
        }

        $transactionId = $request->query('transaction_id') ?: 'ap_tx_' . bin2hex(random_bytes(8));

        return $this->normalizeTransaction([
            'status' => $status,
            'transaction_id' => $transactionId,
            'amount' => $order->display_amount,
            'currency_code' => $order->currency_code,
        ], $order);
    }

    public function handleWebhook(Request $request): ?array
    {
        return null;
    }

    public function normalizeTransaction(array $payload, ?BillingOrder $order = null): array
    {
        $status = $payload['status'] ?? BillingOrder::STATUS_PENDING_CHECKOUT;
        $transactionId = $payload['transaction_id'] ?? '';

        return [
            'status' => $status,
            'external_transaction_id' => $transactionId,
            'gateway_checkout_reference' => $order?->gateway_checkout_reference ?? '',
            'amount' => (float) ($payload['amount'] ?? $order?->display_amount ?? 0.0),
            'currency_code' => $order?->currency_code ?? $payload['currency_code'] ?? 'USD',
            'meta' => [
                'provider' => 'apple_pay',
                'apple_pay_status' => $status === BillingOrder::STATUS_PAID ? 'success' : 'failed',
                'apple_pay_transaction_id' => $transactionId,
            ],
        ];
    }
}
