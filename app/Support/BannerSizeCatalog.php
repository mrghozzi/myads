<?php

namespace App\Support;

class BannerSizeCatalog
{
    private const SIZES = [
        '468x60' => [
            'label' => '468x60',
            'legacy' => '468',
            'width' => 468,
            'height' => 60,
        ],
        '728x90' => [
            'label' => '728x90',
            'legacy' => '728',
            'width' => 728,
            'height' => 90,
        ],
        '300x250' => [
            'label' => '300x250',
            'legacy' => '300',
            'width' => 300,
            'height' => 250,
        ],
        '160x600' => [
            'label' => '160x600',
            'legacy' => '160',
            'width' => 160,
            'height' => 600,
        ],
    ];

    public static function all(): array
    {
        return self::SIZES;
    }

    public static function ordered(): array
    {
        $ordered = [];

        foreach (self::SIZES as $value => $meta) {
            $ordered[] = array_merge($meta, ['value' => $value]);
        }

        return $ordered;
    }

    public static function default(): string
    {
        return '468x60';
    }

    public static function normalize(null|string|int $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = strtolower(trim((string) $value));
        $normalized = str_replace(' ', '', $normalized);

        if (isset(self::SIZES[$normalized])) {
            return $normalized;
        }

        foreach (self::SIZES as $canonical => $meta) {
            if ($normalized === $meta['legacy']) {
                return $canonical;
            }
        }

        return null;
    }

    public static function isSupported(null|string|int $value): bool
    {
        return self::normalize($value) !== null;
    }

    public static function queryCandidates(null|string|int $value): array
    {
        $canonical = self::normalize($value);

        if ($canonical === null) {
            return [];
        }

        $legacy = self::SIZES[$canonical]['legacy'] ?? null;

        return array_values(array_unique(array_filter([$canonical, $legacy])));
    }

    public static function width(null|string|int $value): int
    {
        $canonical = self::normalize($value) ?? self::default();

        return self::SIZES[$canonical]['width'];
    }

    public static function height(null|string|int $value): int
    {
        $canonical = self::normalize($value) ?? self::default();

        return self::SIZES[$canonical]['height'];
    }

    public static function legacyAlias(null|string|int $value): ?string
    {
        $canonical = self::normalize($value);

        if ($canonical === null) {
            return null;
        }

        return self::SIZES[$canonical]['legacy'] ?? null;
    }
}
