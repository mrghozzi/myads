<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RemoteExtensionMarketplaceService
{
    private const BASE_URL = 'https://www.adstn.ovh';
    private const CACHE_TTL_SECONDS = 300;

    /**
     * @return array{type: string, items: array<int, array<string, string>>, error: ?string, browse_url: string}
     */
    public function catalog(string $type): array
    {
        $normalizedType = $this->normalizeType($type);

        return Cache::remember(
            $this->cacheKey($normalizedType),
            self::CACHE_TTL_SECONDS,
            fn (): array => $this->fetchFresh($normalizedType)
        );
    }

    /**
     * @return array{type: string, items: array<int, array<string, string>>, error: ?string, browse_url: string}
     */
    private function fetchFresh(string $type): array
    {
        $browseUrl = $this->browseUrl($type);
        $fallback = [
            'type' => $type,
            'items' => [],
            'error' => __('messages.marketplace_unavailable'),
            'browse_url' => $browseUrl,
        ];

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'User-Agent' => 'MyAds-Extension-Marketplace',
            ])->timeout(10)->get($this->feedUrl($type));

            if (! $response->successful()) {
                return $fallback;
            }

            $payload = $response->json();
            if (! is_array($payload) || ! isset($payload['items']) || ! is_array($payload['items'])) {
                return $fallback;
            }

            $items = [];
            foreach ($payload['items'] as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $productUrl = trim((string) ($item['product_url'] ?? ''));
                if (! $this->isHttpUrl($productUrl)) {
                    continue;
                }

                $imageUrl = trim((string) ($item['image_url'] ?? ''));
                $category = $this->normalizeCategory((string) ($item['category'] ?? ''));

                $items[] = [
                    'name' => trim((string) ($item['name'] ?? '')),
                    'slug' => trim((string) ($item['slug'] ?? '')),
                    'version' => trim((string) ($item['version'] ?? '')),
                    'author' => trim((string) ($item['author'] ?? '')),
                    'description' => trim((string) ($item['description'] ?? '')),
                    'min_myads' => trim((string) ($item['min_myads'] ?? '')),
                    'product_url' => $productUrl,
                    'image_url' => $this->isHttpUrl($imageUrl) ? $imageUrl : '',
                    'category' => $category,
                ];
            }

            return [
                'type' => $type,
                'items' => $items,
                'error' => null,
                'browse_url' => $browseUrl,
            ];
        } catch (\Throwable) {
            return $fallback;
        }
    }

    private function feedUrl(string $type): string
    {
        return rtrim(self::BASE_URL, '/') . '/api/marketplace/extensions/' . $type;
    }

    private function browseUrl(string $type): string
    {
        $normalizedType = $this->normalizeType($type);
        
        return rtrim(self::BASE_URL, '/') . '/store/myads/' . $normalizedType;
    }

    private function normalizeType(string $type): string
    {
        return match ($type) {
            'plugins' => 'plugins',
            default => 'themes',
        };
    }

    private function normalizeCategory(string $value): string
    {
        $category = trim($value);

        return in_array($category, ['plugins', 'themes', 'templates'], true) ? $category : '';
    }

    private function isHttpUrl(string $value): bool
    {
        if (! filter_var($value, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($value, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true);
    }

    private function cacheKey(string $type): string
    {
        return 'remote_extension_marketplace_' . $type;
    }
}
