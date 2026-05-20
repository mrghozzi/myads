<?php

namespace App\Services\Billing;

use App\Services\Billing\Gateways\BankTransferGateway;
use App\Services\Billing\Gateways\BillingGatewayInterface;
use App\Services\Billing\Gateways\FlouciGateway;
use App\Services\Billing\Gateways\LemonSqueezyGateway;
use App\Services\Billing\Gateways\PaddleGateway;
use App\Services\Billing\Gateways\PayPalGateway;
use App\Services\Billing\Gateways\StripeGateway;
use App\Services\Billing\Gateways\TabbyGateway;
use App\Support\SubscriptionGatewaySettings;
use Illuminate\Support\Collection;

class BillingGatewayRegistry
{
    public function __construct(
        private readonly StripeGateway $stripe,
        private readonly PayPalGateway $paypal,
        private readonly BankTransferGateway $bankTransfer,
        private readonly LemonSqueezyGateway $lemonSqueezy,
        private readonly PaddleGateway $paddle,
        private readonly TabbyGateway $tabby,
        private readonly FlouciGateway $flouci
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
            'lemon_squeezy' => $this->lemonSqueezy,
            'paddle' => $this->paddle,
            'tabby' => $this->tabby,
            'flouci' => $this->flouci,
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
