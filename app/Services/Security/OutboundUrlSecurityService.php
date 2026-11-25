<?php

namespace App\Services\Security;

use Illuminate\Support\Facades\Http;

class OutboundUrlSecurityService
{
    private const BLOCKED_HOST_SUFFIXES = [
        '.localhost',
        '.local',
        '.internal',
        '.localdomain',
        '.home.arpa',
    ];

    private const BLOCKED_EXACT_HOSTS = [
        'localhost',
        'ip6-localhost',
        'ip6-loopback',
        'metadata.google.internal',
        'metadata',
    ];

    /**
     * Validate an external HTTP(S) URL against SSRF vectors (schemes, userinfo,
     * local hostnames, IPv4/IPv6 private/reserved ranges, IPv4-mapped IPv6, integer IPs, and DNS resolution).
     *
     * @return array{valid: bool, error: ?string, url: string, host: string, scheme: string, ips: array<int, string>}
     */
    public function validateUrl(string $url, bool $allowUnresolvedInTests = false): array
    {
        $normalizedUrl = trim($url);
        if (
            $normalizedUrl === ''
            || str_contains($normalizedUrl, "\0")
            || preg_match('/[\x00-\x1F\x7F]/', $normalizedUrl) === 1
        ) {
            return $this->invalid($normalizedUrl, 'Invalid or empty URL.');
        }

        $parts = parse_url($normalizedUrl);
        if ($parts === false) {
            return $this->invalid($normalizedUrl, 'Malformed URL.');
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return $this->invalid($normalizedUrl, 'Only HTTP and HTTPS URLs are allowed.');
        }

        if (!empty($parts['user']) || !empty($parts['pass'])) {
            return $this->invalid($normalizedUrl, 'Embedded URL credentials are not allowed.');
        }

        $host = $this->normalizeHost((string) ($parts['host'] ?? ''));
        if ($host === '') {
            return $this->invalid($normalizedUrl, 'Missing URL hostname.');
        }

        if ($this->isBlockedHostname($host)) {
            return $this->invalid($normalizedUrl, 'Local and internal hostnames are blocked.', $host, $scheme);
        }

        if ($this->isIpLiteralOrEncoded($host)) {
            $normalizedIp = $this->extractLiteralIp($host);
            if ($normalizedIp === null || !$this->isPublicIp($normalizedIp)) {
                return $this->invalid($normalizedUrl, 'Private, loopback, link-local, or reserved IP addresses are blocked.', $host, $scheme);
            }

            return [
                'valid' => true,
                'error' => null,
                'url' => $normalizedUrl,
                'host' => $host,
                'scheme' => $scheme,
                'ips' => [$normalizedIp],
            ];
        }

        $resolvedIps = $this->resolveHostIps($host);
        if ($resolvedIps === []) {
            if ($allowUnresolvedInTests && function_exists('app') && app()->runningUnitTests()) {
                return [
                    'valid' => true,
                    'error' => null,
                    'url' => $normalizedUrl,
                    'host' => $host,
                    'scheme' => $scheme,
                    'ips' => [],
                ];
            }

            return $this->invalid($normalizedUrl, 'Unable to resolve target hostname safely.', $host, $scheme);
        }

        foreach ($resolvedIps as $resolvedIp) {
            if (!$this->isPublicIp($resolvedIp)) {
                return $this->invalid(
                    $normalizedUrl,
                    'Target hostname resolves to a restricted internal or private IP address.',
                    $host,
                    $scheme
                );
            }
        }

        return [
            'valid' => true,
            'error' => null,
            'url' => $normalizedUrl,
            'host' => $host,
            'scheme' => $scheme,
            'ips' => $resolvedIps,
        ];
    }

    /**
     * Perform an outbound HTTP(S) GET/HEAD request with hop-by-hop redirect SSRF validation
     * and TLS verification enforced on external connections.
     *
     * @param array<string, string> $headers
     * @return array{ok: bool, status: int, body: string, final_url: string, content_type: ?string, error: ?string}
     */
    public function safeRequest(
        string $url,
        array $headers = [],
        int $timeout = 10,
        int $maxRedirects = 3,
        int $maxBytes = 2097152,
        string $method = 'GET',
        bool $allowUnresolvedInTests = false
    ): array {
        $currentUrl = trim($url);
        $method = strtoupper($method) === 'HEAD' ? 'HEAD' : 'GET';

        for ($hop = 0; $hop <= $maxRedirects; $hop++) {
            $validation = $this->validateUrl($currentUrl, $allowUnresolvedInTests);
            if (!$validation['valid']) {
                return [
                    'ok' => false,
                    'status' => 0,
                    'body' => '',
                    'final_url' => $currentUrl,
                    'content_type' => null,
                    'error' => $validation['error'],
                ];
            }

            try {
                $pending = Http::withHeaders($headers)
                    ->timeout(max(1, $timeout))
                    ->connectTimeout(min(5, max(1, $timeout)))
                    ->withOptions(['allow_redirects' => false]);

                if (function_exists('app') && (app()->environment(['local', 'testing']) || app()->runningUnitTests())) {
                    $pending = $pending->withoutVerifying();
                }

                $response = $method === 'HEAD'
                    ? $pending->head($currentUrl)
                    : $pending->get($currentUrl);
            } catch (\Throwable $e) {
                return [
                    'ok' => false,
                    'status' => 0,
                    'body' => '',
                    'final_url' => $currentUrl,
                    'content_type' => null,
                    'error' => $e->getMessage(),
                ];
            }

            $status = $response->status();
            if (in_array($status, [301, 302, 303, 307, 308], true)) {
                $location = trim((string) $response->header('Location'));
                if ($location === '' || $hop === $maxRedirects) {
                    return [
                        'ok' => false,
                        'status' => $status,
                        'body' => '',
                        'final_url' => $currentUrl,
                        'content_type' => $response->header('Content-Type'),
                        'error' => 'Too many or empty redirects.',
                    ];
                }

                $nextUrl = $this->resolveRedirectUrl($currentUrl, $location);
                if ($nextUrl === null) {
                    return [
                        'ok' => false,
                        'status' => $status,
                        'body' => '',
                        'final_url' => $currentUrl,
                        'content_type' => $response->header('Content-Type'),
                        'error' => 'Invalid redirect target.',
                    ];
                }

                $currentUrl = $nextUrl;
                continue;
            }

            $body = $method === 'HEAD' ? '' : (string) $response->body();
            if ($maxBytes > 0 && strlen($body) > $maxBytes) {
                $body = substr($body, 0, $maxBytes);
            }

            return [
                'ok' => $response->successful(),
                'status' => $status,
                'body' => $body,
                'final_url' => $currentUrl,
                'content_type' => $response->header('Content-Type'),
                'error' => $response->successful() ? null : ('HTTP ' . $status),
            ];
        }

        return [
            'ok' => false,
            'status' => 0,
            'body' => '',
            'final_url' => $currentUrl,
            'content_type' => null,
            'error' => 'Exceeded redirect limit.',
        ];
    }

    public function isPublicIp(string $ip): bool
    {
        $ip = trim($ip, "[] \t\n\r\0\x0B");
        if ($ip === '') {
            return false;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return false;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
            $long = ip2long($ip);
            if ($long === false) {
                return false;
            }

            $unsigned = (int) sprintf('%u', $long);
            $blockedCidrs = [
                ['0.0.0.0', 8],       // "This host on this network"
                ['10.0.0.0', 8],      // RFC 1918
                ['100.64.0.0', 10],   // CGNAT RFC 6598
                ['127.0.0.0', 8],     // Loopback
                ['169.254.0.0', 16],  // Link-local / Cloud metadata
                ['172.16.0.0', 12],   // RFC 1918
                ['192.0.0.0', 24],    // IETF Protocol Assignments
                ['192.0.2.0', 24],    // TEST-NET-1
                ['192.168.0.0', 16],  // RFC 1918
                ['198.18.0.0', 15],   // Benchmarking
                ['198.51.100.0', 24], // TEST-NET-2
                ['203.0.113.0', 24],  // TEST-NET-3
                ['224.0.0.0', 4],     // Multicast
                ['240.0.0.0', 4],     // Reserved / Broadcast
            ];

            foreach ($blockedCidrs as [$network, $prefix]) {
                $netLong = (int) sprintf('%u', ip2long($network));
                $mask = $prefix === 0 ? 0 : ((-1 << (32 - $prefix)) & 0xFFFFFFFF);
                if (($unsigned & $mask) === ($netLong & $mask)) {
                    return false;
                }
            }

            return true;
        }

        // IPv6 inspection including IPv4-mapped IPv6 (::ffff:127.0.0.1)
        $packed = @inet_pton($ip);
        if ($packed === false || strlen($packed) !== 16) {
            return false;
        }

        // Check IPv4-mapped IPv6 (::ffff:0:0/96) and IPv4-compatible (::/96)
        $prefix12 = substr($packed, 0, 12);
        $v4MappedPrefix = str_repeat("\x00", 10) . "\xff\xff";
        $v4CompatPrefix = str_repeat("\x00", 12);
        if ($prefix12 === $v4MappedPrefix || $prefix12 === $v4CompatPrefix) {
            $embeddedV4 = inet_ntop(substr($packed, 12, 4));
            return $embeddedV4 !== false && $this->isPublicIp($embeddedV4);
        }

        // Unspecified :: or Loopback ::1
        if ($packed === str_repeat("\x00", 16) || $packed === (str_repeat("\x00", 15) . "\x01")) {
            return false;
        }

        $firstByte = ord($packed[0]);
        // Unique Local Addresses fc00::/7
        if (($firstByte & 0xFE) === 0xFC) {
            return false;
        }
        // Link-local fe80::/10
        $secondByte = ord($packed[1]);
        if ($firstByte === 0xFE && ($secondByte & 0xC0) === 0x80) {
            return false;
        }
        // Multicast ff00::/8
        if ($firstByte === 0xFF) {
            return false;
        }

        return true;
    }

    public function normalizeHost(string $host): string
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

    private function isBlockedHostname(string $host): bool
    {
        if (in_array($host, self::BLOCKED_EXACT_HOSTS, true)) {
            return true;
        }

        foreach (self::BLOCKED_HOST_SUFFIXES as $suffix) {
            if (str_ends_with($host, $suffix)) {
                return true;
            }
        }

        return false;
    }

    public function isIpLiteralOrEncoded(string $host): bool
    {
        return $this->extractLiteralIp($host) !== null;
    }

    public function extractLiteralIp(string $host): ?string
    {
        $host = trim($host, "[] \t\n\r\0\x0B");
        if ($host === '') {
            return null;
        }

        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return $host;
        }

        $long = ip2long($host);
        if ($long !== false) {
            return long2ip($long);
        }

        return $this->parseAtonIpv4($host);
    }

    private function parseAtonIpv4(string $host): ?string
    {
        $parts = explode('.', $host);
        $count = count($parts);
        if ($count < 1 || $count > 4) {
            return null;
        }

        $values = [];
        foreach ($parts as $part) {
            if ($part === '' || preg_match('/^(?:0x[0-9a-f]+|0[0-7]*|[1-9][0-9]*)$/i', $part) !== 1) {
                return null;
            }

            if (str_starts_with(strtolower($part), '0x')) {
                $hex = substr($part, 2);
                if (strlen($hex) > 8) {
                    return null;
                }
                $val = (int) hexdec($hex);
            } elseif (str_starts_with($part, '0') && strlen($part) > 1) {
                if (strlen($part) > 12) {
                    return null;
                }
                $val = (int) octdec($part);
            } else {
                if (strlen($part) > 10 || (float) $part > 4294967295) {
                    return null;
                }
                $val = (int) $part;
            }

            $values[] = $val;
        }

        $lastIndex = $count - 1;
        for ($i = 0; $i < $lastIndex; $i++) {
            if ($values[$i] < 0 || $values[$i] > 255) {
                return null;
            }
        }

        $maxTail = (1 << (8 * (4 - $lastIndex))) - 1;
        if ($values[$lastIndex] < 0 || $values[$lastIndex] > $maxTail) {
            return null;
        }

        $ipInt = 0;
        for ($i = 0; $i < $lastIndex; $i++) {
            $ipInt |= ($values[$i] << (8 * (3 - $i)));
        }
        $ipInt |= $values[$lastIndex];

        return long2ip($ipInt & 0xFFFFFFFF);
    }

    /**
     * @return array<int, string>
     */
    private function resolveHostIps(string $host): array
    {
        $ips = [];

        if (function_exists('dns_get_record')) {
            $records = @dns_get_record($host, DNS_A | DNS_AAAA);
            if (is_array($records)) {
                foreach ($records as $record) {
                    if (!empty($record['ip'])) {
                        $ips[] = (string) $record['ip'];
                    }
                    if (!empty($record['ipv6'])) {
                        $ips[] = (string) $record['ipv6'];
                    }
                }
            }
        }

        $v4Records = @gethostbynamel($host);
        if (is_array($v4Records)) {
            foreach ($v4Records as $ip) {
                $ips[] = (string) $ip;
            }
        }

        return array_values(array_unique(array_filter($ips, static fn ($ip) => $ip !== '')));
    }

    private function resolveRedirectUrl(string $baseUrl, string $location): ?string
    {
        if ($location === '' || str_contains($location, "\0")) {
            return null;
        }

        $parsed = parse_url($location);
        if ($parsed !== false && !empty($parsed['scheme'])) {
            return $location;
        }

        $base = parse_url($baseUrl);
        if ($base === false || empty($base['scheme']) || empty($base['host'])) {
            return null;
        }

        $scheme = $base['scheme'];
        $host = $base['host'];
        $port = isset($base['port']) ? ':' . $base['port'] : '';

        if (str_starts_with($location, '//')) {
            return $scheme . ':' . $location;
        }

        if (str_starts_with($location, '/')) {
            return $scheme . '://' . $host . $port . $location;
        }

        $basePath = (string) ($base['path'] ?? '/');
        $dir = str_ends_with($basePath, '/') ? $basePath : (dirname($basePath) === '\\' || dirname($basePath) === '/' ? '/' : dirname($basePath) . '/');

        return $scheme . '://' . $host . $port . $dir . $location;
    }

    /**
     * @return array{valid: bool, error: ?string, url: string, host: string, scheme: string, ips: array<int, string>}
     */
    private function invalid(string $url, string $error, string $host = '', string $scheme = ''): array
    {
        return [
            'valid' => false,
            'error' => $error,
            'url' => $url,
            'host' => $host,
            'scheme' => $scheme,
            'ips' => [],
        ];
    }
}
