<?php

namespace App\Services;

use App\Services\Contracts\UrlSafetyInspectorInterface;

class LocalUrlSafetyInspector implements UrlSafetyInspectorInterface
{
    private const KNOWN_SHORTENERS = [
        'adf.ly',
        'bit.ly',
        'buff.ly',
        'cutt.ly',
        'goo.gl',
        'is.gd',
        'ow.ly',
        'rb.gy',
        'rebrand.ly',
        'short.io',
        'shorturl.at',
        't.co',
        'tiny.cc',
        'tinyurl.com',
        'v.gd',
    ];

    public function firstViolation(string $url, array $blockedDomains = [], array $blockedPatterns = []): ?string
    {
        $normalizedUrl = trim($url);
        if ($normalizedUrl === '') {
            return 'invalid_url';
        }

        if (preg_match('/^www\./i', $normalizedUrl) === 1) {
            $normalizedUrl = 'https://' . $normalizedUrl;
        }

        $parts = parse_url($normalizedUrl);
        if ($parts === false) {
            return 'invalid_url';
        }

        $scheme = mb_strtolower((string) ($parts['scheme'] ?? ''), 'UTF-8');
        if (!in_array($scheme, ['http', 'https'], true)) {
            return 'invalid_scheme';
        }

        if (!empty($parts['user']) || !empty($parts['pass'])) {
            return 'credentials_not_allowed';
        }

        $host = $this->normalizeHost((string) ($parts['host'] ?? ''));
        if ($host === '') {
            return 'invalid_host';
        }

        if (
            $host === 'localhost'
            || str_ends_with($host, '.localhost')
            || str_ends_with($host, '.local')
            || str_ends_with($host, '.internal')
            || str_ends_with($host, '.localdomain')
        ) {
            return 'localhost_not_allowed';
        }

        if ($this->isIpOrEncodedPrivateAddress($host)) {
            return 'private_ip_not_allowed';
        }

        foreach ($blockedDomains as $blockedDomain) {
            $blockedDomain = $this->normalizeHost($blockedDomain);
            if ($blockedDomain === '') {
                continue;
            }

            if ($host === $blockedDomain || str_ends_with($host, '.' . $blockedDomain)) {
                return 'blacklisted_domain';
            }
        }

        foreach (self::KNOWN_SHORTENERS as $shortener) {
            if ($host === $shortener || str_ends_with($host, '.' . $shortener)) {
                return 'shortener_not_allowed';
            }
        }

        $lowerUrl = mb_strtolower($normalizedUrl, 'UTF-8');
        foreach ($blockedPatterns as $pattern) {
            $pattern = trim(mb_strtolower($pattern, 'UTF-8'));
            if ($pattern !== '' && str_contains($lowerUrl, $pattern)) {
                return 'blacklisted_pattern';
            }
        }

        return null;
    }

    private function normalizeHost(string $host): string
    {
        $host = trim(mb_strtolower($host, 'UTF-8'));
        $host = trim($host, "[] \t\n\r\0\x0B");

        if ($host === '') {
            return '';
        }

        if (function_exists('idn_to_ascii') && !str_contains($host, ':')) {
            $ascii = @idn_to_ascii($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);
            if (is_string($ascii) && $ascii !== '') {
                $host = mb_strtolower($ascii, 'UTF-8');
            }
        }

        return trim($host, '.');
    }

    private function isIpOrEncodedPrivateAddress(string $host): bool
    {
        $outbound = app(\App\Services\Security\OutboundUrlSecurityService::class);
        $ip = $outbound->extractLiteralIp($host);

        return $ip !== null && !$outbound->isPublicIp($ip);
    }

    private function isPublicIp(string $ip): bool
    {
        return app(\App\Services\Security\OutboundUrlSecurityService::class)->isPublicIp($ip);
    }
}
