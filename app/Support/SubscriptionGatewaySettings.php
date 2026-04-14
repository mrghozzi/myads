<?php

namespace App\Support;

use App\Models\Option;
use Illuminate\Support\Facades\Crypt;

class SubscriptionGatewaySettings
{
    public const OPTION_TYPE = 'subscription_gateway_settings';
    private const ENCRYPTED_PREFIX = 'enc:';

    public const DEFAULTS = [
        'stripe' => [
            'enabled' => 0,
            'mode' => 'sandbox',
            'publishable_key' => '',
            'secret_key' => '',
            'webhook_secret' => '',
            'supported_currencies' => ['USD'],
        ],
        'paypal' => [
            'enabled' => 0,
            'mode' => 'sandbox',
            'client_id' => '',
            'secret_key' => '',
            'webhook_id' => '',
            'supported_currencies' => ['USD'],
        ],
        'bank_transfer' => [
            'enabled' => 0,
            'instructions' => '',
            'note' => '',
            'supported_currencies' => ['USD'],
        ],
    ];

    public const SECRET_FIELDS = [
        'stripe' => ['secret_key', 'webhook_secret'],
        'paypal' => ['secret_key'],
        'bank_transfer' => [],
    ];

    private static ?array $cached = null;

    public static function gatewayKeys(): array
    {
        return array_keys(self::DEFAULTS);
    }

    public static function all(bool $masked = false): array
    {
        $raw = self::loadRaw();
        $payload = [];

        foreach (self::gatewayKeys() as $gateway) {
            $payload[$gateway] = self::normalizeLoaded($gateway, $raw[$gateway] ?? [], $masked);
        }

        return $payload;
    }

    public static function for(string $gateway, bool $masked = false): array
    {
        $gateway = self::normalizeGateway($gateway);

        return self::all($masked)[$gateway];
    }

    public static function save(string $gateway, array $values): array
    {
        $gateway = self::normalizeGateway($gateway);
        $currentRaw = self::loadRaw()[$gateway] ?? [];
        $normalized = self::normalizeIncoming($gateway, $values, $currentRaw);

        Option::updateOrCreate(
            ['o_type' => self::OPTION_TYPE, 'name' => $gateway],
            [
                'o_valuer' => json_encode($normalized),
                'o_parent' => 0,
                'o_order' => 0,
                'o_mode' => time(),
            ]
        );

        self::$cached = null;

        return self::for($gateway);
    }

    public static function maskPayload(string $gateway, array $payload): array
    {
        $gateway = self::normalizeGateway($gateway);
        $masked = $payload;

        foreach (self::SECRET_FIELDS[$gateway] ?? [] as $field) {
            $masked[$field] = self::maskSecret(self::decryptValue($payload[$field] ?? ''));
        }

        return $masked;
    }

    private static function loadRaw(): array
    {
        if (self::$cached !== null) {
            return self::$cached;
        }

        $payload = [];

        try {
            $rows = Option::query()
                ->where('o_type', self::OPTION_TYPE)
                ->get(['name', 'o_valuer']);
        } catch (\Throwable) {
            return self::$cached = $payload;
        }

        foreach ($rows as $row) {
            $decoded = json_decode((string) $row->o_valuer, true);
            $payload[$row->name] = is_array($decoded) ? $decoded : [];
        }

        return self::$cached = $payload;
    }

    private static function normalizeLoaded(string $gateway, array $values, bool $masked): array
    {
        $settings = array_replace(self::DEFAULTS[$gateway], $values);
        $settings['enabled'] = !empty($settings['enabled']) ? 1 : 0;
        $settings['supported_currencies'] = self::normalizeCurrencies($settings['supported_currencies'] ?? []);

        if (array_key_exists('mode', self::DEFAULTS[$gateway])) {
            $settings['mode'] = in_array($settings['mode'] ?? 'sandbox', ['sandbox', 'live'], true)
                ? (string) $settings['mode']
                : 'sandbox';
        }

        foreach (self::SECRET_FIELDS[$gateway] ?? [] as $field) {
            $decrypted = self::decryptValue($settings[$field] ?? '');
            $settings[$field] = $masked ? self::maskSecret($decrypted) : $decrypted;
        }

        foreach ($settings as $key => $value) {
            if (is_string($value) && !in_array($key, self::SECRET_FIELDS[$gateway] ?? [], true)) {
                $settings[$key] = trim($value);
            }
        }

        return $settings;
    }

    private static function normalizeIncoming(string $gateway, array $values, array $currentRaw): array
    {
        $defaults = self::DEFAULTS[$gateway];
        $settings = array_replace($defaults, $currentRaw);

        $settings['enabled'] = !empty($values['enabled']) ? 1 : 0;

        if (array_key_exists('mode', $defaults)) {
            $mode = strtolower(trim((string) ($values['mode'] ?? $settings['mode'] ?? 'sandbox')));
            $settings['mode'] = in_array($mode, ['sandbox', 'live'], true) ? $mode : 'sandbox';
        }

        if (array_key_exists('supported_currencies', $defaults)) {
            $settings['supported_currencies'] = self::normalizeCurrencies($values['supported_currencies'] ?? $settings['supported_currencies'] ?? []);
        }

        foreach ($defaults as $key => $defaultValue) {
            if ($key === 'enabled' || $key === 'mode' || $key === 'supported_currencies') {
                continue;
            }

            if (in_array($key, self::SECRET_FIELDS[$gateway] ?? [], true)) {
                $submitted = trim((string) ($values[$key] ?? ''));

                if ($submitted !== '') {
                    $settings[$key] = self::encryptValue($submitted);
                } elseif (isset($settings[$key])) {
                    $settings[$key] = (string) $settings[$key];
                } else {
                    $settings[$key] = '';
                }

                continue;
            }

            $settings[$key] = trim((string) ($values[$key] ?? $settings[$key] ?? $defaultValue));
        }

        return $settings;
    }

    private static function normalizeCurrencies(array|string $values): array
    {
        if (is_string($values)) {
            $values = preg_split('/[\s,]+/', $values, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        }

        return collect($values)
            ->filter(fn ($value) => is_scalar($value) && trim((string) $value) !== '')
            ->map(fn ($value) => strtoupper(trim((string) $value)))
            ->unique()
            ->values()
            ->all();
    }

    private static function encryptValue(string $value): string
    {
        return self::ENCRYPTED_PREFIX . Crypt::encryptString($value);
    }

    private static function decryptValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        if (!str_starts_with($value, self::ENCRYPTED_PREFIX)) {
            return $value;
        }

        try {
            return Crypt::decryptString(substr($value, strlen(self::ENCRYPTED_PREFIX)));
        } catch (\Throwable) {
            return '';
        }
    }

    private static function maskSecret(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $length = mb_strlen($value);
        if ($length <= 8) {
            return str_repeat('*', $length);
        }

        return mb_substr($value, 0, 4) . str_repeat('*', max(4, $length - 8)) . mb_substr($value, -4);
    }

    private static function normalizeGateway(string $gateway): string
    {
        $gateway = trim(strtolower($gateway));

        if (!array_key_exists($gateway, self::DEFAULTS)) {
            throw new \InvalidArgumentException('Unsupported billing gateway.');
        }

        return $gateway;
    }
}
