<?php

namespace App\Services;

class SocialValidationService
{
    /**
     * Normalize a social link for a given platform.
     * Returns the full normalized URL, or null if invalid.
     */
    public function normalizeSocialLink(string $platform, string $value): ?string
    {
        // Remove @ if handle
        $handle = ltrim($value, '@');
        
        // Patterns and base URLs
        $config = $this->getPlatformsConfig();

        if (!isset($config[$platform])) {
            return null;
        }

        // If it's a URL, extract the handle/id and rebuild
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            if (preg_match($config[$platform]['pattern'], $value, $matches)) {
                $id = $matches[1];
                // Special case for YouTube @
                if ($platform === 'youtube' && !str_contains($value, 'channel/')) {
                    return 'https://www.youtube.com/@' . ltrim($id, '@');
                }
                return $config[$platform]['base'] . $id;
            }
            return null; // URL didn't match the platform
        }

        // It's just a handle
        if (preg_match('/^[a-zA-Z0-9\._\-]+$/', $handle)) {
            if ($platform === 'youtube') {
                return 'https://www.youtube.com/@' . $handle;
            }
            if ($platform === 'threads' && !str_starts_with($handle, '@')) {
                return $config[$platform]['base'] . $handle;
            }
            return $config[$platform]['base'] . $handle;
        }

        return null;
    }

    /**
     * Get list of supported platforms.
     */
    public function getSupportedPlatforms(): array
    {
        return array_keys($this->getPlatformsConfig());
    }

    private function getPlatformsConfig(): array
    {
        return [
            'facebook' => [
                'base' => 'https://www.facebook.com/',
                'pattern' => '/(?:facebook\.com|fb\.com)\/(?:profile\.php\?id=)?([a-zA-Z0-9\.]+)/i'
            ],
            'twitter' => [
                'base' => 'https://x.com/',
                'pattern' => '/(?:twitter\.com|x\.com)\/([a-zA-Z0-9_]+)/i'
            ],
            'vkontakte' => [
                'base' => 'https://vk.com/',
                'pattern' => '/vk\.com\/([a-zA-Z0-9_\.]+)/i'
            ],
            'linkedin' => [
                'base' => 'https://www.linkedin.com/in/',
                'pattern' => '/linkedin\.com\/(?:in|company)\/([a-zA-Z0-9\-\_]+)/i'
            ],
            'instagram' => [
                'base' => 'https://www.instagram.com/',
                'pattern' => '/instagram\.com\/([a-zA-Z0-9_\.]+)/i'
            ],
            'youtube' => [
                'base' => 'https://www.youtube.com/',
                'pattern' => '/youtube\.com\/(?:@|c\/|user\/|channel\/)?([a-zA-Z0-9\-\_]+)/i'
            ],
            'threads' => [
                'base' => 'https://www.threads.net/@',
                'pattern' => '/threads\.net\/@?([a-zA-Z0-9_\.]+)/i'
            ],
            'reddit' => [
                'base' => 'https://www.reddit.com/user/',
                'pattern' => '/reddit\.com\/user\/([a-zA-Z0-9_\-]+)/i'
            ],
            'github' => [
                'base' => 'https://github.com/',
                'pattern' => '/github\.com\/([a-zA-Z0-9_\-]+)/i'
            ],
            'adstn' => [
                'base' => 'https://www.adstn.ovh/u/',
                'pattern' => '/adstn\.ovh\/u\/([a-zA-Z0-9_\-]+)/i'
            ],
            'tiktok' => [
                'base' => 'https://www.tiktok.com/@',
                'pattern' => '/tiktok\.com\/@([a-zA-Z0-9_\.]+)/i'
            ],
            'discord' => [
                'base' => 'https://discord.gg/',
                'pattern' => '/(?:discord\.gg\/|discord\.com\/invite\/)([a-zA-Z0-9\._\-]+)/i'
            ],
        ];
    }
}
