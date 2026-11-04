<?php

namespace App\Services;

use App\Models\SupportTopic;
use App\Support\SecuritySettings;
use Illuminate\Support\Str;

class SupportDeflectionService
{
    public function __construct(
        private readonly HelpContentService $helpService
    ) {
    }

    /**
     * Check if AI Support Deflection is enabled.
     */
    public function isEnabled(): bool
    {
        return (bool) SecuritySettings::get('support_ai_deflection_enabled', 1);
    }

    /**
     * Find relevant Help Center articles and solved support topics matching user query.
     *
     * @param string $query User input subject and/or message description
     * @param string|null $category Optional category slug or department
     * @param int $limit Max results to return
     * @return array
     */
    public function deflect(string $query, ?string $category = null, int $limit = 4): array
    {
        if (!$this->isEnabled()) {
            return [
                'enabled' => false,
                'has_matches' => false,
                'articles' => [],
                'topics' => [],
                'top_match' => null,
            ];
        }

        $rawQuery = trim($query);
        if (mb_strlen($rawQuery) < 3) {
            return [
                'enabled' => true,
                'has_matches' => false,
                'articles' => [],
                'topics' => [],
                'top_match' => null,
            ];
        }

        $normalizedQuery = $this->normalizeText($rawQuery);
        $keywords = $this->extractKeywords($rawQuery);

        $matchedArticles = $this->searchHelpArticles($rawQuery, $normalizedQuery, $keywords, $limit);
        $matchedTopics = $this->searchSolvedTopics($rawQuery, $normalizedQuery, $keywords, $limit);

        // Determine top overall match
        $allMatches = array_merge($matchedArticles, $matchedTopics);
        usort($allMatches, fn($a, $b) => ($b['score'] ?? 0) <=> ($a['score'] ?? 0));
        $topMatch = $allMatches[0] ?? null;

        return [
            'enabled' => true,
            'has_matches' => !empty($allMatches),
            'query' => $rawQuery,
            'articles' => array_slice($matchedArticles, 0, $limit),
            'topics' => array_slice($matchedTopics, 0, $limit),
            'top_match' => $topMatch,
            'count' => count($allMatches),
        ];
    }

    /**
     * Search Help Center articles using normalized keywords and scoring.
     */
    private function searchHelpArticles(string $rawQuery, string $normalizedQuery, array $keywords, int $limit): array
    {
        $currentLocale = app()->getLocale();
        $isArabicQuery = (bool) preg_match('/\p{Arabic}/u', $rawQuery);

        // If the query is written in Arabic but current locale is English (or vice versa), fetch articles in query's locale
        if ($isArabicQuery && $currentLocale !== 'ar') {
            app()->setLocale('ar');
            $allArticles = $this->helpService->getArticles();
            app()->setLocale($currentLocale);
        } elseif (!$isArabicQuery && $currentLocale === 'ar') {
            app()->setLocale('en');
            $allArticles = $this->helpService->getArticles();
            app()->setLocale($currentLocale);
        } else {
            $allArticles = $this->helpService->getArticles();
        }

        $results = [];

        foreach ($allArticles as $art) {
            $title = $art['title'] ?? '';
            $summary = $art['summary'] ?? '';
            $tags = $art['tags'] ?? [];
            $categorySlug = $art['category'] ?? '';

            $normTitle = $this->normalizeText($title);
            $normSummary = $this->normalizeText($summary);
            $normTags = array_map([$this, 'normalizeText'], $tags);

            $score = 0;

            // Full query phrase match
            if (!empty($normalizedQuery) && str_contains($normTitle, $normalizedQuery)) {
                $score += 30;
            } elseif (!empty($normalizedQuery) && str_contains($normSummary, $normalizedQuery)) {
                $score += 15;
            }

            // Keyword token matches
            foreach ($keywords as $kw) {
                if (str_contains($normTitle, $kw)) {
                    $score += 10;
                }
                if (str_contains($normSummary, $kw)) {
                    $score += 4;
                }
                foreach ($normTags as $t) {
                    if (str_contains($t, $kw)) {
                        $score += 6;
                    }
                }
            }

            if ($score >= 6) {
                $categoryData = $this->helpService->getCategory($categorySlug);
                $results[] = [
                    'type' => 'article',
                    'title' => $title,
                    'summary' => Str::limit($summary, 140),
                    'category' => $categoryData['title'] ?? ucfirst(str_replace('-', ' ', $categorySlug)),
                    'category_slug' => $categorySlug,
                    'url' => route('help.article', ['category' => $categorySlug, 'article' => $art['slug']]),
                    'score' => $score,
                    'icon' => $categoryData['icon'] ?? 'fa-solid fa-circle-question',
                ];
            }
        }

        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($results, 0, $limit);
    }

    /**
     * Search community solved support topics.
     */
    private function searchSolvedTopics(string $rawQuery, string $normalizedQuery, array $keywords, int $limit): array
    {
        try {
            $topics = SupportTopic::query()
                ->with(['category', 'bestReply'])
                ->where(function ($q) {
                    $q->where('status', 'resolved')
                        ->orWhereNotNull('best_reply_id');
                })
                ->latest('updated_at')
                ->take(50)
                ->get();

            $results = [];

            foreach ($topics as $topic) {
                $title = $topic->title ?? '';
                $content = $topic->content ?? '';
                $solution = $topic->bestReply ? ($topic->bestReply->content ?? '') : '';

                $normTitle = $this->normalizeText($title);
                $normContent = $this->normalizeText($content);
                $normSolution = $this->normalizeText($solution);

                $score = 0;

                if (!empty($normalizedQuery) && str_contains($normTitle, $normalizedQuery)) {
                    $score += 35;
                } elseif (!empty($normalizedQuery) && str_contains($normSolution, $normalizedQuery)) {
                    $score += 20;
                }

                foreach ($keywords as $kw) {
                    if (str_contains($normTitle, $kw)) {
                        $score += 12;
                    }
                    if (str_contains($normSolution, $kw)) {
                        $score += 6;
                    }
                    if (str_contains($normContent, $kw)) {
                        $score += 3;
                    }
                }

                if ($score >= 8) {
                    $results[] = [
                        'type' => 'topic',
                        'title' => $title,
                        'summary' => Str::limit(strip_tags($solution ?: $content), 140),
                        'category' => $topic->category ? $topic->category->name : __('support.solved_topic'),
                        'category_slug' => $topic->category ? $topic->category->slug : '',
                        'url' => route('support.topic', ['id' => $topic->id, 'slug' => $topic->slug ?? 'topic']),
                        'score' => $score,
                        'is_official_solution' => (bool) $topic->best_reply_id,
                        'icon' => 'fa-solid fa-circle-check text-success',
                    ];
                }
            }

            usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
            return array_slice($results, 0, $limit);
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Normalize text for both Arabic and English searching.
     */
    public function normalizeText(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');

        // Remove Arabic diacritics / tashkeel
        $text = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $text);

        // Normalize Arabic letters
        $text = str_replace(
            ['أ', 'إ', 'آ', 'ٱ', 'ة', 'ى', 'ؤ', 'ئ', 'گ', 'چ', 'پ'],
            ['ا', 'ا', 'ا', 'ا', 'ه', 'ي', 'و', 'ي', 'ك', 'ج', 'ب'],
            $text
        );

        // Strip punctuation and special characters
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);

        // Collapse multiple spaces
        $text = preg_replace('/\s+/u', ' ', $text);

        return trim((string) $text);
    }

    /**
     * Extract meaningful keywords by filtering out common stop words.
     */
    public function extractKeywords(string $text): array
    {
        $normalized = $this->normalizeText($text);
        $tokens = explode(' ', $normalized);

        $stopWords = [
            // Arabic stop words
            'في', 'من', 'على', 'عن', 'الى', 'إلى', 'مع', 'هذا', 'هذه', 'ذلك', 'تلك',
            'التي', 'الذي', 'الذين', 'اللاتي', 'كيف', 'طريقة', 'مشكلة', 'اريد', 'أريد',
            'هل', 'لماذا', 'ما', 'ماذا', 'عندي', 'لدي', 'حدث', 'خطأ', 'خلل', 'طلب',
            'مساعدة', 'استفسار', 'سؤال', 'تذكرة', 'دعم', 'فني', 'ارجو', 'أرجو', 'ممكن',
            'السلام', 'عليكم', 'شكرا', 'لو', 'سمحت', 'أين', 'اين', 'متى',
            // English stop words
            'how', 'to', 'the', 'a', 'an', 'is', 'are', 'was', 'were', 'and', 'or',
            'in', 'on', 'at', 'by', 'for', 'with', 'about', 'can', 'could', 'would',
            'should', 'please', 'help', 'issue', 'problem', 'error', 'need', 'want',
            'why', 'what', 'where', 'when', 'my', 'your', 'our', 'it', 'its', 'this',
            'that', 'these', 'those', 'i', 'you', 'we', 'they', 'have', 'has', 'having',
        ];

        $stopWordsMap = array_flip($stopWords);

        $cleanTokens = [];
        foreach ($tokens as $token) {
            $token = trim($token);
            if (mb_strlen($token) >= 3 && !isset($stopWordsMap[$token])) {
                $cleanTokens[] = $token;
            }
        }

        return array_values(array_unique($cleanTokens));
    }
}
