<?php

namespace App\Support;

final class StoreCategoryCatalog
{
    public const SCRIPT = 'script';
    public const THEMES = 'themes';
    public const TEMPLATES = 'templates';
    public const PLUGINS = 'plugins';

    /**
     * @return array<int, string>
     */
    public static function selectable(): array
    {
        return [
            self::SCRIPT,
            self::THEMES,
            self::PLUGINS,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function legacy(): array
    {
        return [
            self::TEMPLATES,
        ];
    }

    public static function normalize(?string $category): ?string
    {
        $value = trim((string) $category);

        if ($value === '') {
            return null;
        }

        return match ($value) {
            self::TEMPLATES => self::THEMES,
            default => $value,
        };
    }

    /**
     * @return array<int, string>
     */
    public static function acceptedInputValues(): array
    {
        return array_values(array_unique(array_merge(
            self::selectable(),
            self::legacy()
        )));
    }

    /**
     * @return array<int, string>
     */
    public static function namesForFilter(?string $category): array
    {
        return match (self::normalize($category)) {
            self::SCRIPT => [self::SCRIPT],
            self::PLUGINS => [self::PLUGINS],
            self::THEMES => [self::THEMES, self::TEMPLATES],
            default => [],
        };
    }
}
