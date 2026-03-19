<?php

namespace App\Support;

use Illuminate\Support\Str;

class SmartAdTargeting
{
    public static function normalizeCountryCodes(null|string|array $value): array
    {
        $items = is_array($value)
            ? $value
            : self::decodeList($value);

        return collect($items ?: [])
            ->map(fn ($item) => strtoupper(trim((string) $item)))
            ->filter(fn ($item) => preg_match('/^[A-Z]{2}$/', $item) === 1)
            ->unique()
            ->values()
            ->all();
    }

    public static function normalizeDeviceTypes(null|string|array $value): array
    {
        $items = is_array($value)
            ? $value
            : self::decodeList($value);

        return collect($items ?: [])
            ->map(fn ($item) => strtolower(trim((string) $item)))
            ->filter(fn ($item) => in_array($item, ['desktop', 'mobile', 'tablet'], true))
            ->unique()
            ->values()
            ->all();
    }

    public static function encodeList(array $values): ?string
    {
        $values = array_values(array_filter($values, fn ($item) => $item !== null && $item !== ''));

        if ($values === []) {
            return null;
        }

        return json_encode($values, JSON_UNESCAPED_UNICODE);
    }

    public static function decodeList(null|string $value): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        if (is_array($decoded)) {
            return array_values(array_filter($decoded, fn ($item) => $item !== null && $item !== ''));
        }

        return preg_split('/[\s,\|;\r\n]+/', $value, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }

    public static function buildTopicTokens(array $parts, int $limit = 40): array
    {
        return collect($parts)
            ->filter(fn ($part) => is_string($part) && trim($part) !== '')
            ->flatMap(function ($part) {
                $normalized = html_entity_decode((string) $part, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $normalized = mb_strtolower($normalized, 'UTF-8');
                $normalized = preg_replace('/[^\pL\pN]+/u', ' ', $normalized) ?? '';

                return preg_split('/\s+/u', trim($normalized), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            })
            ->map(fn ($token) => Str::limit($token, 40, ''))
            ->filter(fn ($token) => mb_strlen($token, 'UTF-8') >= 2)
            ->reject(fn ($token) => in_array($token, ['the', 'and', 'for', 'with', 'your', 'this', 'that', 'from', 'about', 'site', 'page', 'ads'], true))
            ->unique()
            ->take($limit)
            ->values()
            ->all();
    }

    public static function formatTargets(array $values): string
    {
        return $values === [] ? __('messages.All') : implode(', ', $values);
    }
}
