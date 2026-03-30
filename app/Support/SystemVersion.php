<?php

namespace App\Support;

final class SystemVersion
{
    public const CURRENT = '4.2.2';

    private function __construct()
    {
    }

    public static function name(): string
    {
        return str_replace('.', '-', self::CURRENT);
    }

    public static function tag(): string
    {
        return 'v' . self::CURRENT;
    }
}
