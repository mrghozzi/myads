<?php

namespace App\Support;

final class VideoCategoryCatalog
{
    public const ALL = 'all';
    public const TRENDING = 'trending';
    public const GAMING = 'gaming';
    public const TECH = 'tech';
    public const ENTERTAINMENT = 'entertainment';
    public const EDUCATION = 'education';
    public const TUTORIALS = 'tutorials';
    public const MUSIC = 'music';
    public const PODCASTS = 'podcasts';
    public const SPORTS = 'sports';
    public const LIFESTYLE = 'lifestyle';
    public const NEWS = 'news';
    public const SHORTS = 'shorts';

    /**
     * Complete collection of modern video categories / genres.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function categories(): array
    {
        return [
            self::ALL => [
                'id' => self::ALL,
                'slug' => self::ALL,
                'name' => __('messages.all_videos') ?? 'All Videos',
                'name_ar' => 'الكل',
                'icon' => 'fa-solid fa-border-all',
                'color' => '#6366f1',
                'gradient' => 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)',
                'keywords' => [],
            ],
            self::TRENDING => [
                'id' => self::TRENDING,
                'slug' => self::TRENDING,
                'name' => __('messages.trending_videos') ?? 'Trending',
                'name_ar' => 'الأكثر رواجاً',
                'icon' => 'fa-solid fa-fire',
                'color' => '#f59e0b',
                'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #ef4444 100%)',
                'keywords' => ['trending', 'popular', 'viral', 'رائج', 'شائع'],
            ],
            self::SHORTS => [
                'id' => self::SHORTS,
                'slug' => self::SHORTS,
                'name' => __('messages.shorts_clips') ?? 'Shorts & Clips',
                'name_ar' => 'مقاطع قصيرة',
                'icon' => 'fa-solid fa-bolt',
                'color' => '#ef4444',
                'gradient' => 'linear-gradient(135deg, #ef4444 0%, #ec4899 100%)',
                'keywords' => ['short', 'shorts', 'clip', 'clips', 'reel', 'reels', 'قصيرة', 'ريلز'],
            ],
            self::TECH => [
                'id' => self::TECH,
                'slug' => self::TECH,
                'name' => __('messages.video_cat_tech') ?? 'Technology & AI',
                'name_ar' => 'تكنولوجيا وتقنية',
                'icon' => 'fa-solid fa-microchip',
                'color' => '#06b6d4',
                'gradient' => 'linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%)',
                'keywords' => ['tech', 'technology', 'ai', 'software', 'coding', 'programming', 'code', 'python', 'laravel', 'app', 'web', 'برمجة', 'تقنية', 'تكنولوجيا', 'ذكاء اصطناعي', 'تطوير', 'كمبيوتر'],
            ],
            self::GAMING => [
                'id' => self::GAMING,
                'slug' => self::GAMING,
                'name' => __('messages.video_cat_gaming') ?? 'Gaming',
                'name_ar' => 'ألعاب وفيديو جيم',
                'icon' => 'fa-solid fa-gamepad',
                'color' => '#8b5cf6',
                'gradient' => 'linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%)',
                'keywords' => ['game', 'games', 'gaming', 'gameplay', 'playstation', 'xbox', 'nintendo', 'gta', 'fortnite', 'minecraft', 'لعبة', 'ألعاب', 'قيمنق', 'قيمبلير'],
            ],
            self::ENTERTAINMENT => [
                'id' => self::ENTERTAINMENT,
                'slug' => self::ENTERTAINMENT,
                'name' => __('messages.video_cat_entertainment') ?? 'Entertainment',
                'name_ar' => 'ترفيه وكوميديا',
                'icon' => 'fa-solid fa-masks-theater',
                'color' => '#f97316',
                'gradient' => 'linear-gradient(135deg, #f97316 0%, #e11d48 100%)',
                'keywords' => ['entertainment', 'comedy', 'funny', 'humor', 'movie', 'film', 'trailer', 'سينما', 'ترفيه', 'كوميديا', 'مضحك', 'فيلم', 'مسلسل'],
            ],
            self::TUTORIALS => [
                'id' => self::TUTORIALS,
                'slug' => self::TUTORIALS,
                'name' => __('messages.video_cat_tutorials') ?? 'Tutorials & How-To',
                'name_ar' => 'شروحات وتعليمات',
                'icon' => 'fa-solid fa-chalkboard-user',
                'color' => '#10b981',
                'gradient' => 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                'keywords' => ['tutorial', 'tutorials', 'how to', 'guide', 'learn', 'step by step', 'شرح', 'شروحات', 'كيفية', 'دليل', 'طريقة'],
            ],
            self::EDUCATION => [
                'id' => self::EDUCATION,
                'slug' => self::EDUCATION,
                'name' => __('messages.video_cat_education') ?? 'Education & Science',
                'name_ar' => 'تعليم ومعرفة',
                'icon' => 'fa-solid fa-graduation-cap',
                'color' => '#14b8a6',
                'gradient' => 'linear-gradient(135deg, #14b8a6 0%, #0d9488 100%)',
                'keywords' => ['education', 'science', 'history', 'course', 'study', 'university', 'documentary', 'تعليم', 'دراسة', 'علوم', 'تاريخ', 'وثائقي'],
            ],
            self::MUSIC => [
                'id' => self::MUSIC,
                'slug' => self::MUSIC,
                'name' => __('messages.video_cat_music') ?? 'Music & Beats',
                'name_ar' => 'موسيقى وألحان',
                'icon' => 'fa-solid fa-music',
                'color' => '#ec4899',
                'gradient' => 'linear-gradient(135deg, #ec4899 0%, #d946ef 100%)',
                'keywords' => ['music', 'song', 'songs', 'audio', 'beat', 'remix', 'soundtrack', 'guitar', 'piano', 'موسيقى', 'أغنية', 'لحن', 'عزف'],
            ],
            self::PODCASTS => [
                'id' => self::PODCASTS,
                'slug' => self::PODCASTS,
                'name' => __('messages.video_cat_podcasts') ?? 'Podcasts & Talks',
                'name_ar' => 'بودكاست وحوارات',
                'icon' => 'fa-solid fa-podcast',
                'color' => '#a855f7',
                'gradient' => 'linear-gradient(135deg, #a855f7 0%, #7c3aed 100%)',
                'keywords' => ['podcast', 'podcasts', 'interview', 'talk', 'conversation', 'talkshow', 'بودكاست', 'حوار', 'لقاء', 'مقابلة'],
            ],
            self::SPORTS => [
                'id' => self::SPORTS,
                'slug' => self::SPORTS,
                'name' => __('messages.video_cat_sports') ?? 'Sports & Fitness',
                'name_ar' => 'رياضة ولياقة',
                'icon' => 'fa-solid fa-futbol',
                'color' => '#22c55e',
                'gradient' => 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)',
                'keywords' => ['sport', 'sports', 'football', 'soccer', 'fitness', 'workout', 'gym', 'رياضة', 'كرة', 'لياقة', 'تمرين'],
            ],
            self::LIFESTYLE => [
                'id' => self::LIFESTYLE,
                'slug' => self::LIFESTYLE,
                'name' => __('messages.video_cat_lifestyle') ?? 'Lifestyle & Vlogs',
                'name_ar' => 'يوميات وفلوغات',
                'icon' => 'fa-solid fa-camera-retro',
                'color' => '#f43f5e',
                'gradient' => 'linear-gradient(135deg, #f43f5e 0%, #fb7185 100%)',
                'keywords' => ['vlog', 'vlogs', 'lifestyle', 'travel', 'food', 'cooking', 'daily', 'فلوق', 'يوميات', 'سفر', 'طبخ'],
            ],
            self::NEWS => [
                'id' => self::NEWS,
                'slug' => self::NEWS,
                'name' => __('messages.video_cat_news') ?? 'News & Media',
                'name_ar' => 'أخبار وإعلام',
                'icon' => 'fa-solid fa-newspaper',
                'color' => '#3b82f6',
                'gradient' => 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)',
                'keywords' => ['news', 'media', 'report', 'world', 'breaking', 'أخبار', 'تقرير', 'إعلام', 'عاجل'],
            ],
        ];
    }

    /**
     * Get all categories as an iterable array of objects for views.
     *
     * @return array<int, object>
     */
    public static function all(): array
    {
        $list = [];
        $isArabic = app()->getLocale() === 'ar';
        foreach (self::categories() as $cat) {
            $displayName = $isArabic ? ($cat['name_ar'] ?? $cat['name']) : $cat['name'];
            $list[] = (object) array_merge($cat, [
                'display_name' => $displayName,
            ]);
        }
        return $list;
    }

    /**
     * Find category by slug.
     */
    public static function find(?string $slug): ?object
    {
        if (empty($slug)) {
            return null;
        }

        $slug = strtolower(trim($slug));
        $cats = self::categories();

        if (isset($cats[$slug])) {
            $isArabic = app()->getLocale() === 'ar';
            $cat = $cats[$slug];
            $cat['display_name'] = $isArabic ? ($cat['name_ar'] ?? $cat['name']) : $cat['name'];
            return (object) $cat;
        }

        return null;
    }

    /**
     * Infer the best matching video category from title, description, or tags.
     */
    public static function inferCategory(?string $text, ?string $title = null, int $sType = 10): object
    {
        if ($sType === 14) {
            return self::find(self::SHORTS) ?? (object) self::categories()[self::SHORTS];
        }

        $combined = strtolower(trim(($title ?? '') . ' ' . ($text ?? '')));
        if ($combined === '') {
            return self::find(self::TECH) ?? (object) self::categories()[self::TECH];
        }

        $bestScore = 0;
        $bestSlug = self::TECH;

        foreach (self::categories() as $slug => $cat) {
            if ($slug === self::ALL || $slug === self::TRENDING) {
                continue;
            }

            $score = 0;
            foreach ($cat['keywords'] as $keyword) {
                if (str_contains($combined, strtolower($keyword))) {
                    $score += 2;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestSlug = $slug;
            }
        }

        return self::find($bestSlug) ?? (object) self::categories()[$bestSlug];
    }

    /**
     * Filter a Status query by video category keywords, tags, or backwards-compatible forum cat.
     */
    public static function applyCategoryFilter($query, $categoryFilter): void
    {
        if (empty($categoryFilter)) {
            return;
        }

        // If numeric ID was passed (e.g. from existing unit test asserting forumTopic cat ID)
        if (is_numeric($categoryFilter)) {
            $query->whereHas('forumTopic', function ($ft) use ($categoryFilter) {
                $ft->where('cat', (int) $categoryFilter);
            });
            return;
        }

        $slug = strtolower(trim((string) $categoryFilter));
        if ($slug === self::ALL) {
            return;
        }

        if ($slug === self::SHORTS || $slug === 'clips') {
            $query->where('s_type', 14);
            return;
        }

        $cat = self::find($slug);
        if (!$cat) {
            // Fallback for custom forum category slug if matched in legacy tests
            $query->whereHas('forumTopic', function ($ft) use ($slug) {
                $ft->whereHas('category', fn($c) => $c->where('slug', $slug));
            });
            return;
        }

        // Filter status query based on category keywords
        $keywords = $cat->keywords ?? [];
        if (!empty($keywords)) {
            $query->where(function ($q) use ($keywords, $slug) {
                foreach ($keywords as $kw) {
                    $q->orWhere('txt', 'like', '%' . $kw . '%')
                      ->orWhereHas('forumTopic', function ($ft) use ($kw) {
                          $ft->where('name', 'like', '%' . $kw . '%')
                             ->orWhere('txt', 'like', '%' . $kw . '%');
                      });
                }
                // Also check if forumTopic category has similar slug or name
                $q->orWhereHas('forumTopic.category', function ($fc) use ($slug) {
                    $fc->where('slug', 'like', '%' . $slug . '%')
                       ->orWhere('name', 'like', '%' . $slug . '%');
                });
            });
        }
    }
}
