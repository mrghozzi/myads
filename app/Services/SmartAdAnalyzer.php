<?php

namespace App\Services;

use App\Support\SmartAdTargeting;
use Illuminate\Support\Facades\Http;

class SmartAdAnalyzer
{
    private const URL_LIMIT = 2048;

    public function analyze(string $url): array
    {
        $response = Http::withHeaders([
            'User-Agent' => 'MyAds-SmartAds/1.0',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        ])->timeout(10)->get($url);

        if (!$response->successful()) {
            throw new \RuntimeException('Unable to analyze the destination page.');
        }

        $html = $this->normalizeHtmlToUtf8((string) $response->body(), $response->header('Content-Type'));
        $document = new \DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);
        @$document->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();
        libxml_use_internal_errors($internalErrors);

        $xpath = new \DOMXPath($document);

        $title = $this->cleanText($this->firstValue($xpath, [
            '//meta[@property="og:title"]/@content',
            '//title/text()',
            '//h1[1]/text()',
        ]));

        $description = $this->cleanText($this->firstValue($xpath, [
            '//meta[@name="description"]/@content',
            '//meta[@property="og:description"]/@content',
            '//meta[@name="twitter:description"]/@content',
            '//h2[1]/text()',
            '//p[1]/text()',
        ]));

        $keywords = $this->cleanText($this->firstValue($xpath, [
            '//meta[@name="keywords"]/@content',
        ]));

        $headings = $this->cleanText(implode(' ', array_filter([
            $this->firstValue($xpath, ['//h1[1]/text()']),
            $this->firstValue($xpath, ['//h2[1]/text()']),
            $this->firstValue($xpath, ['//h3[1]/text()']),
        ])));

        $bodyExcerpt = $this->cleanText($this->firstValue($xpath, [
            '//article//text()',
            '//main//text()',
            '//body//text()',
        ]), 320);

        $sourceImage = $this->resolveAssetUrl($url, $this->firstValue($xpath, [
            '//meta[@property="og:image"]/@content',
            '//meta[@name="twitter:image"]/@content',
            '//img[1]/@src',
        ]));

        return [
            'source_title' => $title,
            'source_description' => $description,
            'source_image' => $this->limitUrl($sourceImage),
            'extracted_keywords' => implode(', ', SmartAdTargeting::buildTopicTokens([
                $title,
                $description,
                $keywords,
                $headings,
                $bodyExcerpt,
            ])),
        ];
    }

    private function firstValue(\DOMXPath $xpath, array $queries): string
    {
        foreach ($queries as $query) {
            $nodes = $xpath->query($query);
            if ($nodes && $nodes->length > 0) {
                $value = trim((string) $nodes->item(0)?->nodeValue);
                if ($value !== '') {
                    return $value;
                }
            }
        }

        return '';
    }

    private function cleanText(string $value, int $limit = 180): string
    {
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? '';

        if ($limit > 0 && mb_strlen($value, 'UTF-8') > $limit) {
            $value = mb_substr($value, 0, $limit, 'UTF-8');
        }

        return trim($value);
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

    private function resolveAssetUrl(string $baseUrl, string $assetUrl): ?string
    {
        $assetUrl = trim($assetUrl);

        if ($assetUrl === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $assetUrl)) {
            return $assetUrl;
        }

        $parsed = parse_url($baseUrl);
        if (!$parsed || empty($parsed['scheme']) || empty($parsed['host'])) {
            return null;
        }

        $scheme = $parsed['scheme'];
        $host = $parsed['host'];
        $port = isset($parsed['port']) ? ':' . $parsed['port'] : '';

        if (str_starts_with($assetUrl, '//')) {
            return $scheme . ':' . $assetUrl;
        }

        if (str_starts_with($assetUrl, '/')) {
            return $scheme . '://' . $host . $port . $assetUrl;
        }

        $path = $parsed['path'] ?? '/';
        $directory = rtrim(str_replace('\\', '/', dirname($path)), '/');
        $directory = $directory === '.' ? '' : $directory;

        return $scheme . '://' . $host . $port . $directory . '/' . ltrim($assetUrl, '/');
    }

    private function limitUrl(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '' || mb_strlen($value, 'UTF-8') > self::URL_LIMIT) {
            return null;
        }

        return $value;
    }
}
