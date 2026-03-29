<?php

namespace App\Support;

use Illuminate\Support\Str;

class ContentFormatter
{
    public static function format(?string $text): string
    {
        $value = trim((string) $text);
        if ($value === '') {
            return '';
        }

        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $value = self::linkifyMentions(self::linkifyHashtags($value));

        return trim((string) Str::markdown($value, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]));
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
}
