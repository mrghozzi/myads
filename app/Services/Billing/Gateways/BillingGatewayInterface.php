<?php

namespace App\Services\Billing\Gateways;

use App\Models\BillingOrder;
use Illuminate\Http\Request;

interface BillingGatewayInterface
{
    public function key(): string;

    public function label(): string;

    public function supportsCurrency(string $currencyCode): bool;

    public function createCheckout(BillingOrder $order): array;

    public function handleReturn(Request $request, BillingOrder $order): array;

    public function handleWebhook(Request $request): ?array;

    public function normalizeTransaction(array $payload, ?BillingOrder $order = null): array;

    public function maskConfig(array $config): array;
}
