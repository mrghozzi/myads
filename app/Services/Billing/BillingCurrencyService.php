<?php

namespace App\Services\Billing;

use App\Models\BillingCurrency;
use App\Services\V420SchemaService;
use App\Support\SubscriptionSettings;
use Illuminate\Support\Collection;

class BillingCurrencyService
{
    public function __construct(
        private readonly V420SchemaService $schema,
        private readonly BillingGatewayRegistry $gateways
    ) {
    }

    public function all(bool $activeOnly = false): Collection
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return collect();
        }

        try {
            return BillingCurrency::query()
                ->when($activeOnly, fn ($query) => $query->where('is_active', true))
                ->orderByDesc('is_base')
                ->orderBy('sort_order')
                ->orderBy('code')
                ->get();
        } catch (\Throwable) {
            return collect();
        }
    }

    public function findByCode(string $code): ?BillingCurrency
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return null;
        }

        try {
            return BillingCurrency::query()
                ->where('code', strtoupper(trim($code)))
                ->first();
        } catch (\Throwable) {
            return null;
        }
    }

    public function find(int $id): ?BillingCurrency
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return null;
        }

        try {
            return BillingCurrency::query()->find($id);
        } catch (\Throwable) {
            return null;
        }
    }

    public function baseCurrency(): BillingCurrency
    {
        $configuredCode = SubscriptionSettings::get('base_currency_code', 'USD');

        $currency = $this->findByCode((string) $configuredCode)
            ?: $this->all()->firstWhere('is_base', true)
            ?: $this->all(true)->first();

        if ($currency instanceof BillingCurrency) {
            return $currency;
        }

        return new BillingCurrency([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1,
            'decimal_places' => 2,
            'is_active' => true,
            'is_base' => true,
            'sort_order' => 0,
        ]);
    }

    public function convertFromBase(float $baseAmount, string $currencyCode): array
    {
        $currency = $this->findByCode($currencyCode) ?: $this->baseCurrency();
        $rate = max(0.000001, (float) $currency->exchange_rate);
        $decimals = max(0, (int) $currency->decimal_places);

        return [
            'currency' => $currency,
            'exchange_rate' => $rate,
            'display_amount' => round($baseAmount * $rate, $decimals),
        ];
    }

    public function supportedForGateway(string $gatewayKey): Collection
    {
        $gateway = $this->gateways->get($gatewayKey);

        return $this->all(true)
            ->filter(fn (BillingCurrency $currency) => $gateway->supportsCurrency((string) $currency->code))
            ->values();
    }

    public function store(array $values): BillingCurrency
    {
        $payload = $this->normalizeIncoming($values);
        $currency = BillingCurrency::query()->create($payload);

        if ($currency->is_base) {
            $this->setBaseCurrency($currency);
        }

        return $currency->refresh();
    }

    public function update(BillingCurrency $currency, array $values): BillingCurrency
    {
        $currency->fill($this->normalizeIncoming($values, $currency));
        $currency->save();

        if ($currency->is_base) {
            $this->setBaseCurrency($currency);
        }

        return $currency->refresh();
    }

    public function delete(BillingCurrency $currency): void
    {
        if ($currency->is_base) {
            throw new \RuntimeException(__('messages.billing_base_currency_delete_forbidden'));
        }

        $currency->delete();
    }

    public function setBaseCurrency(BillingCurrency $currency): void
    {
        if (!$this->schema->supports('subscriptions_billing')) {
            return;
        }

        try {
            BillingCurrency::query()->update(['is_base' => false]);
            $currency->forceFill(['is_base' => true, 'is_active' => true, 'exchange_rate' => 1])->save();
            SubscriptionSettings::save(array_merge(SubscriptionSettings::all(), [
                'base_currency_code' => strtoupper((string) $currency->code),
            ]));
        } catch (\Throwable) {
            // Ignore partial-upgrade write failures.
        }
    }

    private function normalizeIncoming(array $values, ?BillingCurrency $current = null): array
    {
        return [
            'code' => strtoupper(trim((string) ($values['code'] ?? $current?->code ?? 'USD'))),
            'name' => trim((string) ($values['name'] ?? $current?->name ?? '')),
            'symbol' => trim((string) ($values['symbol'] ?? $current?->symbol ?? '')),
            'exchange_rate' => max(0.000001, round((float) ($values['exchange_rate'] ?? $current?->exchange_rate ?? 1), 6)),
            'decimal_places' => max(0, min(4, (int) ($values['decimal_places'] ?? $current?->decimal_places ?? 2))),
            'is_active' => !empty($values['is_active']) ? 1 : 0,
            'is_base' => !empty($values['is_base']) ? 1 : 0,
            'sort_order' => max(0, (int) ($values['sort_order'] ?? $current?->sort_order ?? 0)),
        ];
    }
}
