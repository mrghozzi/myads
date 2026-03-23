<?php

namespace App\Services;

use App\Models\Directory;
use App\Models\DirectoryCategory;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\Knowledgebase;
use App\Models\News;
use App\Models\Page;
use App\Models\Product;
use App\Models\SeoDailyMetric;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class SeoMetricsService
{
    public function canTrack(): bool
    {
        return Schema::hasTable('seo_daily_metrics');
    }

    public function record(Request $request, Response $response, array $context, ?string $scopeKey = null): void
    {
        if (!$this->canTrack()) {
            return;
        }

        if (!$request->isMethod('GET')) {
            return;
        }

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');
        if ($contentType !== '' && !str_contains($contentType, 'text/html')) {
            return;
        }

        $statusCode = (int) $response->getStatusCode();
        if (!in_array($statusCode, [200, 404], true)) {
            return;
        }

        $scopeKey = $scopeKey ?: (string) ($context['scope_key'] ?? $request->route()?->getName() ?? $request->path() ?: 'unknown');

        if ($this->isPrivateScope($scopeKey)) {
            return;
        }

        $metricDate = now()->toDateString();
        $contentTypeKey = $context['content_type'] ?? null;
        $contentId = isset($context['content_id']) ? (int) $context['content_id'] : null;
        $scopeType = ($contentTypeKey && $contentId) ? 'content' : 'route';

        $metric = SeoDailyMetric::query()->firstOrCreate(
            [
                'metric_date' => $metricDate,
                'scope_type' => $scopeType,
                'scope_key' => $scopeKey,
                'content_type' => $contentTypeKey,
                'content_id' => $contentId,
            ],
            [
                'page_views' => 0,
                'unique_visitors' => 0,
                'bot_hits' => 0,
                'status_404' => 0,
            ]
        );

        if ($statusCode === 404) {
            $metric->increment('status_404');
            return;
        }

        $userAgent = (string) $request->userAgent();
        if ($this->isBot($userAgent)) {
            $metric->increment('bot_hits');
            return;
        }

        $metric->increment('page_views');

        $cacheKey = $this->uniqueVisitorCacheKey($request, $scopeKey, $metricDate);
        if (Cache::add($cacheKey, true, now()->addDay())) {
            $metric->increment('unique_visitors');
        }
    }

    public function chartSets(array $days = [7, 30, 90]): array
    {
        $sets = [];

        foreach ($days as $dayCount) {
            $sets[(string) $dayCount] = $this->series($dayCount);
        }

        return $sets;
    }

    public function series(int $days): array
    {
        if (!$this->canTrack()) {
            return [
                'labels' => [],
                'page_views' => [],
                'unique_visitors' => [],
                'bot_hits' => [],
            ];
        }

        $start = now()->subDays(max($days - 1, 0))->toDateString();

        $rows = SeoDailyMetric::query()
            ->selectRaw('metric_date, SUM(page_views) as page_views, SUM(unique_visitors) as unique_visitors, SUM(bot_hits) as bot_hits')
            ->whereDate('metric_date', '>=', $start)
            ->groupBy('metric_date')
            ->orderBy('metric_date')
            ->get()
            ->keyBy(static fn (SeoDailyMetric $metric) => $metric->metric_date->toDateString());

        $labels = [];
        $pageViews = [];
        $uniqueVisitors = [];
        $botHits = [];

        for ($cursor = Carbon::parse($start); $cursor->lte(now()); $cursor->addDay()) {
            $dateKey = $cursor->toDateString();
            $row = $rows->get($dateKey);

            $labels[] = $cursor->format('M d');
            $pageViews[] = (int) ($row->page_views ?? 0);
            $uniqueVisitors[] = (int) ($row->unique_visitors ?? 0);
            $botHits[] = (int) ($row->bot_hits ?? 0);
        }

        return [
            'labels' => $labels,
            'page_views' => $pageViews,
            'unique_visitors' => $uniqueVisitors,
            'bot_hits' => $botHits,
        ];
    }

    public function topScopes(int $limit = 8, int $days = 30): Collection
    {
        if (!$this->canTrack()) {
            return collect();
        }

        $start = now()->subDays(max($days - 1, 0))->toDateString();

        return SeoDailyMetric::query()
            ->selectRaw('scope_key, SUM(page_views) as page_views, SUM(unique_visitors) as unique_visitors, SUM(bot_hits) as bot_hits')
            ->whereDate('metric_date', '>=', $start)
            ->groupBy('scope_key')
            ->orderByDesc('page_views')
            ->limit($limit)
            ->get();
    }

    public function topContentPages(int $limit = 8, int $days = 30): Collection
    {
        if (!$this->canTrack()) {
            return collect();
        }

        $start = now()->subDays(max($days - 1, 0))->toDateString();

        return SeoDailyMetric::query()
            ->selectRaw('content_type, content_id, scope_key, SUM(page_views) as page_views, SUM(unique_visitors) as unique_visitors')
            ->whereDate('metric_date', '>=', $start)
            ->whereNotNull('content_type')
            ->whereNotNull('content_id')
            ->groupBy('content_type', 'content_id', 'scope_key')
            ->orderByDesc('page_views')
            ->limit($limit)
            ->get()
            ->map(function (SeoDailyMetric $metric) {
                return [
                    'label' => $this->resolveContentLabel($metric->content_type, (int) $metric->content_id, $metric->scope_key),
                    'scope_key' => $metric->scope_key,
                    'page_views' => (int) $metric->page_views,
                    'unique_visitors' => (int) $metric->unique_visitors,
                ];
            });
    }

    private function resolveContentLabel(?string $contentType, int $contentId, string $fallback): string
    {
        return match ($contentType) {
            'page' => Page::query()->whereKey($contentId)->value('title') ?: $this->translateScopeLabel($fallback),
            'news' => News::query()->whereKey($contentId)->value('name') ?: $this->translateScopeLabel($fallback),
            'forum_category' => ForumCategory::query()->whereKey($contentId)->value('name') ?: $this->translateScopeLabel($fallback),
            'forum_topic' => ForumTopic::query()->whereKey($contentId)->value('name') ?: $this->translateScopeLabel($fallback),
            'directory_category' => DirectoryCategory::query()->whereKey($contentId)->value('name') ?: $this->translateScopeLabel($fallback),
            'directory' => Directory::query()->whereKey($contentId)->value('name') ?: $this->translateScopeLabel($fallback),
            'product' => Product::withoutGlobalScope('store')->whereKey($contentId)->value('name') ?: $this->translateScopeLabel($fallback),
            'knowledgebase' => Knowledgebase::query()->whereKey($contentId)->value('name') ?: $this->translateScopeLabel($fallback),
            'user' => User::query()->whereKey($contentId)->value('username') ?: $this->translateScopeLabel($fallback),
            default => $this->translateScopeLabel($fallback),
        };
    }

    private function translateScopeLabel(string $scopeKey): string
    {
        $translated = __('messages.seo_scope_' . $scopeKey);

        return $translated === 'messages.seo_scope_' . $scopeKey ? $scopeKey : $translated;
    }

    private function uniqueVisitorCacheKey(Request $request, string $scopeKey, string $metricDate): string
    {
        $hash = hash_hmac(
            'sha256',
            implode('|', [
                $metricDate,
                $scopeKey,
                (string) $request->ip(),
                (string) $request->userAgent(),
            ]),
            (string) config('app.key', 'myads')
        );

        return 'seo_unique_visitor:' . $hash;
    }

    private function isBot(string $userAgent): bool
    {
        if ($userAgent === '') {
            return false;
        }

        return (bool) preg_match('/bot|crawl|slurp|spider|preview|facebookexternalhit|mediapartners|google-structured-data/i', $userAgent);
    }

    private function isPrivateScope(string $scopeKey): bool
    {
        foreach ([
            'admin.',
            'messages.',
            'notifications.',
            'ads.',
            'visits.',
            'dashboard',
            'settings',
            'profile.edit',
            'profile.history',
            'forum.create',
            'forum.edit',
            'directory.create',
            'directory.edit',
            'store.create',
            'store.update',
            'kb.edit',
            'kb.pending',
            'kb.history',
            'report.',
        ] as $prefix) {
            if ($scopeKey === $prefix || str_starts_with($scopeKey, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
