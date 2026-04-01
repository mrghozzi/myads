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

            $html = $this->normalizeHtmlToUtf8($html, $response->header('Content-Type'));
            $meta = $this->parseHtml($html);

            return array_merge($fallback, array_filter([
                'title' => $meta['title'] ?? null,
                'description' => $meta['description'] ?? null,
                'keywords' => $meta['keywords'] ?? null,
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
        $internalErrors = libxml_use_internal_errors(true);
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        $xpath = new \DOMXPath($dom);

        return [
            'title' => $this->firstMetaContent($xpath, ['og:title', 'twitter:title']) ?: $this->nodeText($xpath, '//title'),
            'description' => $this->firstMetaContent($xpath, ['og:description', 'twitter:description', 'description']),
            'keywords' => $this->firstMetaContent($xpath, ['keywords']),
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

    private function normalizeHtmlToUtf8(string $html, null|string|array $contentTypeHeader = null): string
    {
        if ($html === '') {
            return $html;
        }

        $charset = $this->detectCharset($html, $contentTypeHeader);

        if ($charset === 'UTF-8') {
            return $html;
        }

        try {
            $converted = mb_convert_encoding($html, 'UTF-8', $charset);

            if (is_string($converted) && $converted !== '') {
                return $converted;
            }
        } catch (\Throwable) {
            // Fall back to the raw HTML if conversion fails.
        }

        return $html;
    }

    private function detectCharset(string $html, null|string|array $contentTypeHeader = null): string
    {
        $headerValue = is_array($contentTypeHeader)
            ? implode(';', $contentTypeHeader)
            : (string) ($contentTypeHeader ?? '');

        if (preg_match('/charset\s*=\s*([a-z0-9._-]+)/i', $headerValue, $matches) === 1) {
            return $this->normalizeCharsetName($matches[1]);
        }

        if (preg_match('/<meta[^>]+charset\s*=\s*["\']?\s*([a-z0-9._-]+)/i', $html, $matches) === 1) {
            return $this->normalizeCharsetName($matches[1]);
        }

        if (preg_match('/<meta[^>]+http-equiv\s*=\s*["\']content-type["\'][^>]+content\s*=\s*["\'][^"\']*charset\s*=\s*([a-z0-9._-]+)/i', $html, $matches) === 1) {
            return $this->normalizeCharsetName($matches[1]);
        }

        if (mb_check_encoding($html, 'UTF-8')) {
            return 'UTF-8';
        }

        $detected = mb_detect_encoding($html, ['UTF-8', 'Windows-1256', 'Windows-1252', 'ISO-8859-1'], true);

        return $this->normalizeCharsetName($detected ?: 'UTF-8');
    }

    private function normalizeCharsetName(string $charset): string
    {
        $charset = strtoupper(trim($charset));

        return match ($charset) {
            'UTF8' => 'UTF-8',
            'WINDOWS1256' => 'Windows-1256',
            'WINDOWS1252' => 'Windows-1252',
            'ISO8859-1', 'LATIN1' => 'ISO-8859-1',
            default => $charset !== '' ? $charset : 'UTF-8',
        };
    }
}
