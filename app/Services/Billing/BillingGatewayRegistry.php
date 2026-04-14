<?php

namespace App\Services\Billing;

use App\Services\Billing\Gateways\BankTransferGateway;
use App\Services\Billing\Gateways\BillingGatewayInterface;
use App\Services\Billing\Gateways\PayPalGateway;
use App\Services\Billing\Gateways\StripeGateway;
use App\Support\SubscriptionGatewaySettings;
use Illuminate\Support\Collection;

class BillingGatewayRegistry
{
    public function __construct(
        private readonly StripeGateway $stripe,
        private readonly PayPalGateway $paypal,
        private readonly BankTransferGateway $bankTransfer
    ) {
    }

    /**
     * @return array<string,BillingGatewayInterface>
     */
    public function all(): array
    {
        return [
            'stripe' => $this->stripe,
            'paypal' => $this->paypal,
            'bank_transfer' => $this->bankTransfer,
        ];
    }

    public function get(string $key): BillingGatewayInterface
    {
        $gateways = $this->all();

        if (!isset($gateways[$key])) {
            throw new \InvalidArgumentException('Unsupported billing gateway.');
        }

        return $gateways[$key];
    }

    public function enabled(): Collection
    {
        return collect($this->all())
            ->filter(fn (BillingGatewayInterface $gateway) => !empty(SubscriptionGatewaySettings::for($gateway->key())['enabled']))
            ->values();
    }

    public function enabledKeys(): array
    {
        return $this->enabled()->map(fn (BillingGatewayInterface $gateway) => $gateway->key())->all();
    }

    public function definitionsForAdmin(): array
    {
        $definitions = [];

        foreach ($this->all() as $key => $gateway) {
            $config = SubscriptionGatewaySettings::for($key);

            $definitions[$key] = [
                'key' => $key,
                'label' => $gateway->label(),
                'config' => $gateway->maskConfig($config),
                'supports_mode' => array_key_exists('mode', SubscriptionGatewaySettings::DEFAULTS[$key]),
                'supports_manual_review' => $key === 'bank_transfer',
                'supported_currencies' => $config['supported_currencies'] ?? [],
            ];
        }

        return $definitions;
    }
}
