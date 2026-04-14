<?php

namespace App\Services\Billing\Gateways;

use App\Models\BillingOrder;
use App\Support\SubscriptionGatewaySettings;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

abstract class AbstractBillingGateway implements BillingGatewayInterface
{
    protected function config(bool $masked = false): array
    {
        return SubscriptionGatewaySettings::for($this->key(), $masked);
    }

    public function supportsCurrency(string $currencyCode): bool
    {
        $currencyCode = strtoupper(trim($currencyCode));
        $supported = $this->config()['supported_currencies'] ?? [];

        return $supported === [] || in_array($currencyCode, $supported, true);
    }

    public function maskConfig(array $config): array
    {
        return SubscriptionGatewaySettings::maskPayload($this->key(), $config);
    }

    protected function ensureEnabled(): array
    {
        $config = $this->config();

        if (empty($config['enabled'])) {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_gateway_disabled'),
            ]);
        }

        return $config;
    }

    protected function requireConfig(array $config, string ...$keys): void
    {
        foreach ($keys as $key) {
            if (trim((string) ($config[$key] ?? '')) === '') {
                throw ValidationException::withMessages([
                    'gateway' => __('messages.billing_gateway_missing_credentials'),
                ]);
            }
        }
    }

    protected function http()
    {
        return Http::acceptJson()->timeout(25);
    }

    protected function assertSuccessful(Response $response): array
    {
        if ($response->failed()) {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_gateway_unavailable'),
            ]);
        }

        $payload = $response->json();

        if (!is_array($payload)) {
            throw ValidationException::withMessages([
                'gateway' => __('messages.billing_invalid_gateway_response'),
            ]);
        }

        return $payload;
    }

    protected function normalizeMinorAmount(int $amount, int $decimals = 2): float
    {
        return round($amount / (10 ** max(0, $decimals)), max(0, $decimals));
    }

    protected function decimalPlacesFor(?BillingOrder $order): int
    {
        return (int) data_get($order?->meta, 'currency_decimal_places', 2);
    }
}
