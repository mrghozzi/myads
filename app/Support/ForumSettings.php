<?php

namespace App\Support;

use App\Models\Option;

class ForumSettings
{
    public const OPTION_TYPE = 'forum_settings';

    public const DEFAULTS = [
        'topics_per_page' => 21,
        'attachments_enabled' => 1,
        'max_attachments_per_topic' => 5,
        'max_attachment_size_kb' => 10240,
        'allowed_attachment_extensions' => 'jpg,jpeg,png,gif,webp,pdf,zip,rar,7z,doc,docx,xls,xlsx,ppt,pptx,txt,csv',
        'show_role_badges' => 1,
    ];

    public static function all(): array
    {
        $settings = self::DEFAULTS;

        $rows = Option::where('o_type', self::OPTION_TYPE)->get(['name', 'o_valuer']);
        foreach ($rows as $row) {
            if (!array_key_exists($row->name, self::DEFAULTS)) {
                continue;
            }
            $settings[$row->name] = $row->o_valuer;
        }

        return [
            'topics_per_page' => max(1, (int) ($settings['topics_per_page'] ?? self::DEFAULTS['topics_per_page'])),
            'attachments_enabled' => (int) ($settings['attachments_enabled'] ?? self::DEFAULTS['attachments_enabled']) === 1 ? 1 : 0,
            'max_attachments_per_topic' => max(1, (int) ($settings['max_attachments_per_topic'] ?? self::DEFAULTS['max_attachments_per_topic'])),
            'max_attachment_size_kb' => max(1, (int) ($settings['max_attachment_size_kb'] ?? self::DEFAULTS['max_attachment_size_kb'])),
            'allowed_attachment_extensions' => self::sanitizeExtensions((string) ($settings['allowed_attachment_extensions'] ?? self::DEFAULTS['allowed_attachment_extensions'])),
            'show_role_badges' => (int) ($settings['show_role_badges'] ?? self::DEFAULTS['show_role_badges']) === 1 ? 1 : 0,
        ];
    }

    public static function get(string $key, mixed $fallback = null): mixed
    {
        $settings = self::all();
        return $settings[$key] ?? $fallback;
    }

    public static function save(array $values): void
    {
        $sanitized = self::normalizeIncoming($values);

        foreach ($sanitized as $name => $value) {
            Option::updateOrCreate(
                ['o_type' => self::OPTION_TYPE, 'name' => $name],
                ['o_valuer' => (string) $value]
            );
        }
    }

    public static function allowedExtensions(): array
    {
        $csv = (string) self::get('allowed_attachment_extensions', self::DEFAULTS['allowed_attachment_extensions']);
        return self::extensionsFromCsv($csv);
    }

    public static function normalizeIncoming(array $values): array
    {
        return [
            'topics_per_page' => max(1, (int) ($values['topics_per_page'] ?? self::DEFAULTS['topics_per_page'])),
            'attachments_enabled' => !empty($values['attachments_enabled']) ? 1 : 0,
            'max_attachments_per_topic' => max(1, (int) ($values['max_attachments_per_topic'] ?? self::DEFAULTS['max_attachments_per_topic'])),
            'max_attachment_size_kb' => max(1, (int) ($values['max_attachment_size_kb'] ?? self::DEFAULTS['max_attachment_size_kb'])),
            'allowed_attachment_extensions' => self::sanitizeExtensions((string) ($values['allowed_attachment_extensions'] ?? self::DEFAULTS['allowed_attachment_extensions'])),
            'show_role_badges' => !empty($values['show_role_badges']) ? 1 : 0,
        ];
    }

    private static function sanitizeExtensions(string $csv): string
    {
        $extensions = self::extensionsFromCsv($csv);
        if (empty($extensions)) {
            return self::DEFAULTS['allowed_attachment_extensions'];
        }

        return implode(',', $extensions);
    }

    private static function extensionsFromCsv(string $csv): array
    {
        $items = array_filter(array_map('trim', explode(',', strtolower($csv))));
        $items = array_map(static fn (string $item): string => preg_replace('/[^a-z0-9]/', '', $item) ?? '', $items);
        $items = array_values(array_unique(array_filter($items)));

        return $items;
    }
}