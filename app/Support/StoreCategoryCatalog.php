<?php

namespace App\Support;

final class StoreCategoryCatalog
{
    public const SCRIPT = 'script';
    public const THEMES = 'themes';
    public const TEMPLATES = 'templates';
    public const PLUGINS = 'plugins';
    public const GRAPHICS = 'graphics';
    public const AUDIO = 'audio';
    public const VIDEO = 'video';
    public const EBOOKS = 'ebooks';
    public const SOFTWARE = 'software';
    public const COURSES = 'courses';

    /**
     * @return array<int, string>
     */
    public static function selectable(): array
    {
        return [
            self::SCRIPT,
            self::THEMES,
            self::PLUGINS,
            self::GRAPHICS,
            self::AUDIO,
            self::VIDEO,
            self::EBOOKS,
            self::SOFTWARE,
            self::COURSES,
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
            self::GRAPHICS => [self::GRAPHICS],
            self::AUDIO => [self::AUDIO],
            self::VIDEO => [self::VIDEO],
            self::EBOOKS => [self::EBOOKS],
            self::SOFTWARE => [self::SOFTWARE],
            self::COURSES => [self::COURSES],
            default => [],
        };
    }
}
