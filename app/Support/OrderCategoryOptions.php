<?php

namespace App\Support;

use App\Models\Option;
use Illuminate\Support\Collection;

class OrderCategoryOptions
{
    public const OPTION_TYPE = 'order_category';

    /**
     * @return array<int, array{slug: string, fallback: string, key: string}>
     */
    public static function defaults(): array
    {
        return [
            ['slug' => 'uncategorized', 'fallback' => 'Uncategorized', 'key' => 'order_category_uncategorized'],
            ['slug' => 'development', 'fallback' => 'Development', 'key' => 'order_category_development'],
            ['slug' => 'design', 'fallback' => 'Design', 'key' => 'order_category_design'],
            ['slug' => 'marketing', 'fallback' => 'Marketing', 'key' => 'order_category_marketing'],
            ['slug' => 'seo', 'fallback' => 'SEO', 'key' => 'order_category_seo'],
            ['slug' => 'content', 'fallback' => 'Content', 'key' => 'order_category_content'],
            ['slug' => 'support', 'fallback' => 'Support', 'key' => 'order_category_support'],
        ];
    }

    public static function ensureDefaults(): void
    {
        foreach (self::defaults() as $index => $category) {
            Option::query()->firstOrCreate(
                [
                    'o_type' => self::OPTION_TYPE,
                    'o_valuer' => $category['slug'],
                ],
                [
                    'name' => $category['fallback'],
                    'o_parent' => 0,
                    'o_order' => $index + 1,
                    'o_mode' => $category['key'],
                ]
            );
        }
    }

    public static function all(): Collection
    {
        self::ensureDefaults();

        return Option::query()
            ->where('o_type', self::OPTION_TYPE)
            ->orderBy('o_order')
            ->get()
            ->map(function (Option $option) {
                $translationKey = $option->o_mode ?: null;
                $translated = $translationKey ? __('messages.' . $translationKey) : null;

                return (object) [
                    'slug' => (string) $option->o_valuer,
                    'label' => $translated && $translated !== 'messages.' . $translationKey
                        ? $translated
                        : ($option->name ?: ucfirst((string) $option->o_valuer)),
                ];
            });
    }

    public static function label(?string $slug): string
    {
        if (!$slug) {
            return __('messages.order_category_uncategorized');
        }

        $match = self::all()->firstWhere('slug', $slug);

        return $match?->label ?: __('messages.order_category_uncategorized');
    }
}
