<?php

namespace App\Services\Billing\Gateways;

use App\Models\BillingOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BankTransferGateway extends AbstractBillingGateway
{
    public function key(): string
    {
        return 'bank_transfer';
    }

    public function label(): string
    {
        return __('messages.billing_gateway_bank_transfer');
    }

    public function createCheckout(BillingOrder $order): array
    {
        $config = $this->ensureEnabled();

        return [
            'checkout_url' => route('billing.orders.show', $order->id),
            'gateway_checkout_reference' => null,
            'status' => BillingOrder::STATUS_PENDING_RECEIPT,
            'meta' => [
                'provider' => 'bank_transfer',
                'instructions' => (string) ($config['instructions'] ?? ''),
            ],
        ];
    }

    public function handleReturn(Request $request, BillingOrder $order): array
    {
        return $this->normalizeTransaction([
            'status' => BillingOrder::STATUS_PENDING_REVIEW,
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
        $status = (string) ($payload['status'] ?? BillingOrder::STATUS_PENDING_REVIEW);

        if (!in_array($status, [
            BillingOrder::STATUS_PENDING_RECEIPT,
            BillingOrder::STATUS_PENDING_REVIEW,
            BillingOrder::STATUS_PAID,
            BillingOrder::STATUS_REJECTED,
        ], true)) {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_gateway_response'),
            ]);
        }

        return [
            'status' => $status,
            'external_transaction_id' => '',
            'gateway_checkout_reference' => '',
            'amount' => (float) ($payload['amount'] ?? $order?->display_amount ?? 0),
            'currency_code' => strtoupper((string) ($payload['currency_code'] ?? $order?->currency_code ?? 'USD')),
            'meta' => [
                'provider' => 'bank_transfer',
            ],
        ];
    }
}
