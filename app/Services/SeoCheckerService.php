<?php

namespace App\Services;

use App\Services\Security\OutboundUrlSecurityService;

class SeoCheckerService
{
    public function __construct(
        private readonly ?OutboundUrlSecurityService $urlSecurity = null
    ) {
    }

    /**
     * Perform the SEO analysis on the given URL.
     *
     * @param string $url
     * @return array
     */
    public function analyzeUrl(string $url): array
    {
        $security = $this->urlSecurity ?? app(OutboundUrlSecurityService::class);
        $validation = $security->validateUrl($url, true);
        if (!$validation['valid']) {
            throw new \InvalidArgumentException($validation['error'] ?: 'Unsafe target URL.');
        }

        $startTime = microtime(true);
        $html = $this->fetchHtml($url);
        $endTime = microtime(true);
        
        $loadTime = round(($endTime - $startTime), 2); // seconds
        
        $errors = $this->analyzeHtmlForErrors($html);
        $backlinks = $this->fetchBacklinks($url);
        
        // Extract title and description
        $dom = new \DOMDocument();
        @$dom->loadHTML($html ?: '');
        
        $title = '';
        $nodes = $dom->getElementsByTagName('title');
        if ($nodes->length > 0) {
            $title = $nodes->item(0)->nodeValue;
        }

        $description = '';
        $metas = $dom->getElementsByTagName('meta');
        foreach ($metas as $meta) {
            if (strtolower($meta->getAttribute('name')) === 'description') {
                $description = $meta->getAttribute('content');
                break;
            }
        }

        // Get IP
        $host = parse_url($url, PHP_URL_HOST);
        $ip = $host ? gethostbyname($host) : 'N/A';
        
        return [
            'url' => $url,
            'title' => trim($title),
            'description' => trim($description),
            'ip' => $ip,
            'speed' => [
                'time_seconds' => $loadTime
            ],
            'errors' => $errors,
            'backlinks' => $backlinks,
            'timestamp' => now()->toDateTimeString(),
        ];
    }

    /**
     * Fetch HTML safely with SSRF protection and hop-by-hop redirect validation.
     */
    private function fetchHtml(string $url): ?string
    {
        $security = $this->urlSecurity ?? app(OutboundUrlSecurityService::class);
        $result = $security->safeRequest(
            $url,
            ['User-Agent' => 'MYADS-SEO-Checker/1.0'],
            10,
            3,
            2097152,
            'GET',
            true
        );

        return $result['ok'] && $result['body'] !== '' ? $result['body'] : null;
    }

    /**
     * Analyze HTML for common SEO programmatic errors.
     */
    private function analyzeHtmlForErrors(?string $html): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html ?: '');

        $h1s = $dom->getElementsByTagName('h1');
        $missing_h1 = $h1s->length === 0;

        $images = $dom->getElementsByTagName('img');
        $images_without_alt = [];
        foreach ($images as $img) {
            if (!$img->hasAttribute('alt') || empty(trim($img->getAttribute('alt')))) {
                $images_without_alt[] = $img->getAttribute('src') ?: 'unknown';
            }
        }

        return [
            'missing_h1' => $missing_h1,
            'images_without_alt' => $images_without_alt,
        ];
    }

    /**
     * Fetch backlinks (Simulated placeholder for now).
     */
    private function fetchBacklinks(string $url): array
    {
        // Without an API like Ahrefs, we cannot accurately fetch backlinks.
        // Returning a simulated response.
        return [
            'count' => rand(10, 500),
            'trust_flow' => rand(10, 100),
            'citation_flow' => rand(10, 100),
        ];
    }
}
