<?php

namespace App\Support;

class ForumCommentFormatter
{
    public static function format(?string $text): string
    {
        return ContentFormatter::format($text);
    }
}
