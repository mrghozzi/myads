<?php

namespace App\Support;

use Illuminate\Support\Str;

class ContentFormatter
{
    protected static ?array $blockedDomainsCache = null;
    protected static ?array $blockedPatternsCache = null;

    public static function format(?string $text): string
    {
        $value = trim((string) $text);
        if ($value === '') {
            return '';
        }

        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $value = self::linkifyBbcodeEmail(self::linkifyBbcodeUrl(self::linkifyMentions(self::linkifyHashtags($value))));

        $html = trim((string) Str::markdown($value, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]));

        return self::embedYouTubeLinks($html);
    }

    public static function formatForum(?string $text): string
    {
        $value = trim((string) $text);
        if ($value === '') {
            return '';
        }

        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $value = self::linkifyBbcodeEmail(self::linkifyBbcodeUrl(self::linkifyMentions(self::linkifyHashtags($value))));

        $html = trim((string) Str::markdown($value, [
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]));

        return self::embedYouTubeLinks($html);
    }

    public static function embedYouTubeLinks(string $html): string
    {
        return (string) preg_replace_callback(
            '/<a[^>]*href="([^"]*(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})[^"]*)"[^>]*>(.*?)<\/a>/i',
            static function (array $matches): string {
                $videoId = $matches[2];
                return '<div class="ratio ratio-16x9 my-3"><iframe src="https://www.youtube.com/embed/' . $videoId . '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>';
            },
            $html
        );
    }

    public static function extractMentionUsernames(?string $text): array
    {
        preg_match_all('/(^|[\s(>])@([A-Za-z0-9_]{2,30})/u', (string) $text, $matches);

        return collect($matches[2] ?? [])
            ->map(static fn ($username) => strtolower((string) $username))
            ->unique()
            ->values()
            ->all();
    }

    public static function linkifyHashtags(string $text): string
    {
        return (string) preg_replace_callback(
            '/(^|[\s(>])#([\p{L}\p{N}_]{1,60})/u',
            static function (array $matches): string {
                $prefix = $matches[1];
                $tag = $matches[2];
                return $prefix . '[#' . $tag . '](/tag/' . rawurlencode($tag) . ')';
            },
            $text
        );
    }

    public static function linkifyMentions(string $text): string
    {
        return (string) preg_replace_callback(
            '/(^|[\s(>])@([A-Za-z0-9_]{2,30})/u',
            static function (array $matches): string {
                $prefix = $matches[1];
                $username = $matches[2];
                return $prefix . '[@' . $username . '](/u/' . rawurlencode($username) . ')';
            },
            $text
        );
    }

    public static function linkifyBbcodeUrl(string $text): string
    {
        return (string) preg_replace_callback(
            '/\[url(?:=([^\]]+))?\](.*?)\[\/url\]/is',
            static function (array $matches): string {
                $url = trim(!empty($matches[1]) ? $matches[1] : $matches[2]);
                $anchorText = $matches[2];
                
                if (!preg_match('/^https?:\/\//i', $url)) {
                    return $anchorText;
                }
                
                if (self::$blockedDomainsCache === null) {
                    self::$blockedDomainsCache = \App\Support\SecuritySettings::blockedDomains();
                    self::$blockedPatternsCache = \App\Support\SecuritySettings::blockedUrlPatterns();
                }

                $inspector = app(\App\Services\Contracts\UrlSafetyInspectorInterface::class);
                $violation = $inspector->firstViolation(
                    $url,
                    self::$blockedDomainsCache,
                    self::$blockedPatternsCache
                );
                
                if ($violation !== null) {
                    return $anchorText;
                }
                
                return '[' . $anchorText . '](' . $url . ')';
            },
            $text
        );
    }

    public static function linkifyBbcodeEmail(string $text): string
    {
        return (string) preg_replace_callback(
            '/\[email(?:=([^\]]+))?\](.*?)\[\/email\]/is',
            static function (array $matches): string {
                $email = trim(!empty($matches[1]) ? $matches[1] : $matches[2]);
                $anchorText = $matches[2];
                
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return $anchorText;
                }
                
                return '[' . $anchorText . '](mailto:' . $email . ')';
            },
            $text
        );
    }
}
