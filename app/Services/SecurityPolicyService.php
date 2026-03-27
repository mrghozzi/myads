<?php

namespace App\Services;

use App\Services\Contracts\UrlSafetyInspectorInterface;
use App\Support\SecuritySettings;
use Illuminate\Support\Str;

class SecurityPolicyService
{
    public function __construct(
        private readonly UrlSafetyInspectorInterface $urlSafetyInspector
    ) {
    }

    public function textViolation(?string $text, string $context): ?string
    {
        $text = trim((string) ($text ?? ''));
        if ($text === '' || !$this->linkSafetyEnabledFor($context)) {
            return null;
        }

        foreach ($this->extractUrlsFromText($text) as $url) {
            $violation = $this->urlSafetyInspector->firstViolation(
                $url,
                SecuritySettings::blockedDomains(),
                SecuritySettings::blockedUrlPatterns()
            );

            if ($violation !== null) {
                return $this->messageForViolation($violation);
            }
        }

        return null;
    }

    public function urlViolation(?string $url, string $context, bool $allowRelative = false): ?string
    {
        $url = trim((string) ($url ?? ''));
        if ($url === '' || !$this->linkSafetyEnabledFor($context)) {
            return null;
        }

        if ($allowRelative && preg_match('/^[a-z][a-z0-9+\-.]*:\/\//i', $url) !== 1 && preg_match('/^www\./i', $url) !== 1) {
            return null;
        }

        $violation = $this->urlSafetyInspector->firstViolation(
            $url,
            SecuritySettings::blockedDomains(),
            SecuritySettings::blockedUrlPatterns()
        );

        return $violation !== null ? $this->messageForViolation($violation) : null;
    }

    public function usernameViolation(string $username): ?string
    {
        if ((int) SecuritySettings::get('block_spam_usernames', 0) !== 1) {
            return null;
        }

        $normalized = $this->normalizeIdentifier($username);
        if ($normalized === '') {
            return null;
        }

        foreach (SecuritySettings::blockedUsernames() as $blockedUsername) {
            if ($normalized === $this->normalizeIdentifier($blockedUsername)) {
                return __('messages.security_username_blocked');
            }
        }

        return null;
    }

    public function emailViolation(string $email): ?string
    {
        $domain = mb_strtolower((string) Str::after($email, '@'), 'UTF-8');
        if ($domain === '') {
            return null;
        }

        foreach (SecuritySettings::blockedEmailDomains() as $blockedDomain) {
            if ($domain === $blockedDomain || str_ends_with($domain, '.' . $blockedDomain)) {
                return __('messages.security_email_domain_blocked');
            }
        }

        return null;
    }

    private function extractUrlsFromText(string $text): array
    {
        preg_match_all('/((?:https?:\/\/|www\.)[^\s<>"\'`]+)/iu', $text, $matches);

        return array_values(array_unique($matches[1] ?? []));
    }

    private function linkSafetyEnabledFor(string $context): bool
    {
        if ((int) SecuritySettings::get('link_safety_enabled', 0) !== 1) {
            return false;
        }

        return match ($context) {
            'posts' => (int) SecuritySettings::get('link_safety_apply_posts', 0) === 1,
            'comments' => (int) SecuritySettings::get('link_safety_apply_comments', 0) === 1,
            'messages' => (int) SecuritySettings::get('link_safety_apply_messages', 0) === 1,
            'ads' => (int) SecuritySettings::get('link_safety_apply_ads', 0) === 1,
            default => false,
        };
    }

    private function normalizeIdentifier(string $value): string
    {
        $value = Str::of($value)->ascii()->lower()->replaceMatches('/[^a-z0-9]+/', '')->value();

        return trim($value);
    }

    private function messageForViolation(string $violation): string
    {
        return match ($violation) {
            'blacklisted_domain' => __('messages.security_domain_blocked'),
            'blacklisted_pattern' => __('messages.security_url_pattern_blocked'),
            'shortener_not_allowed' => __('messages.security_shortener_blocked'),
            default => __('messages.security_url_not_allowed'),
        };
    }
}
