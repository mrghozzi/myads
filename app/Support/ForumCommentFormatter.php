<?php

namespace App\Support;

use Illuminate\Support\Str;

class ForumCommentFormatter
{
    public static function format(?string $text): string
    {
        $value = trim((string) $text);
        if ($value === '') {
            return '';
        }

        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $value = self::linkifyHashtags($value);

        $html = (string) Str::markdown($value, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return trim($html);
    }

    private static function linkifyHashtags(string $text): string
    {
        return (string) preg_replace_callback(
            '/(^|[\s(>])#([\p{L}\p{N}_]{1,60})/u',
            static function (array $matches): string {
                $prefix = $matches[1];
                $tag = $matches[2];
                $encoded = rawurlencode($tag);

                return $prefix . '[#' . $tag . '](/tag/' . $encoded . ')';
            },
            $text
        );
    }
}
