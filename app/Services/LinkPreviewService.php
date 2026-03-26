<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LinkPreviewService
{
    public function fetch(string $url): array
    {
        $normalized = $this->normalizeUrl($url);
        $domain = parse_url($normalized, PHP_URL_HOST) ?: $normalized;

        $fallback = [
            'url' => $normalized,
            'normalized_url' => $normalized,
            'title' => $domain,
            'description' => null,
            'image_url' => null,
            'site_name' => $domain,
            'domain' => $domain,
            'status_code' => 0,
        ];

        try {
            $response = Http::timeout(10)
                ->withHeaders(['User-Agent' => 'MyAds-LinkPreview/4.2'])
                ->get($normalized);

            $fallback['status_code'] = $response->status();

            if (!$response->successful()) {
                return $fallback;
            }

            $html = (string) $response->body();
            if ($html === '') {
                return $fallback;
            }

            $meta = $this->parseHtml($html);

            return array_merge($fallback, array_filter([
                'title' => $meta['title'] ?? null,
                'description' => $meta['description'] ?? null,
                'image_url' => $meta['image_url'] ?? null,
                'site_name' => $meta['site_name'] ?? null,
            ], static fn ($value) => $value !== null && $value !== ''));
        } catch (\Throwable) {
            return $fallback;
        }
    }

    public function normalizeUrl(string $url): string
    {
        $value = trim($url);
        if ($value !== '' && !Str::startsWith($value, ['http://', 'https://'])) {
            $value = 'https://' . $value;
        }

        return $value;
    }

    private function parseHtml(string $html): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);

        return [
            'title' => $this->firstMetaContent($xpath, ['og:title', 'twitter:title']) ?: $this->nodeText($xpath, '//title'),
            'description' => $this->firstMetaContent($xpath, ['og:description', 'twitter:description', 'description']),
            'image_url' => $this->firstMetaContent($xpath, ['og:image', 'twitter:image']),
            'site_name' => $this->firstMetaContent($xpath, ['og:site_name']),
        ];
    }

    private function firstMetaContent(\DOMXPath $xpath, array $names): ?string
    {
        foreach ($names as $name) {
            $query = sprintf(
                "//meta[@property='%s']/@content | //meta[@name='%s']/@content",
                $name,
                $name
            );
            $value = $this->nodeText($xpath, $query);
            if ($value !== null && $value !== '') {
                return trim($value);
            }
        }

        return null;
    }

    private function nodeText(\DOMXPath $xpath, string $query): ?string
    {
        $nodes = $xpath->query($query);
        if (!$nodes || $nodes->length === 0) {
            return null;
        }

        return trim((string) $nodes->item(0)->nodeValue);
    }
}
