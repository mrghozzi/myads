<?php

namespace App\Services;

use App\Models\Directory;
use App\Models\ForumTopic;
use App\Models\Knowledgebase;
use App\Models\News;
use App\Models\Page;
use App\Models\Product;
use App\Models\SeoRule;
use App\Models\SeoSetting;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class SeoAuditService
{
    public function __construct(
        private readonly RobotsTxtService $robotsTxt,
        private readonly SeoMetricsService $metrics,
        private readonly V420SchemaService $schema,
    ) {
    }

    public function dashboard(): array
    {
        $settings = SeoSetting::current();
        $robotsPreview = $this->robotsTxt->render($settings);
        $indexableUrls = $this->indexableUrlCount();
        $missingDescriptions = $this->missingDescriptionsCount();
        $missingOgImages = $this->missingOgImagesCount($settings);
        $noindexRules = $this->schema->hasTable('seo_rules')
            ? SeoRule::query()->where('indexable', false)->count()
            : 0;
        $canonicalCoverage = $settings->canonical_mode === 'disabled' ? 0 : $indexableUrls;
        $score = 100;
        $issues = [];

        if ($this->robotsTxt->blocksAll($settings)) {
            $score -= 45;
            $issues[] = [
                'severity' => 'critical',
                'title' => __('messages.seo_issue_robots_blocking_title'),
                'action' => __('messages.seo_issue_robots_blocking_action'),
            ];
        }

        if ($missingDescriptions > 0) {
            $score -= min(20, $missingDescriptions);
            $issues[] = [
                'severity' => 'warning',
                'title' => __('messages.seo_issue_missing_descriptions_title'),
                'action' => __('messages.seo_issue_missing_descriptions_action'),
            ];
        }

        if ($missingOgImages > 0) {
            $score -= 15;
            $issues[] = [
                'severity' => 'warning',
                'title' => __('messages.seo_issue_missing_og_title'),
                'action' => __('messages.seo_issue_missing_og_action'),
            ];
        }

        if ($settings->ga4_enabled && trim((string) $settings->ga4_measurement_id) === '') {
            $score -= 10;
            $issues[] = [
                'severity' => 'warning',
                'title' => __('messages.seo_issue_ga4_title'),
                'action' => __('messages.seo_issue_ga4_action'),
            ];
        }

        $score = max(5, $score);

        return [
            'settings' => $settings,
            'score' => $score,
            'robots_preview' => $robotsPreview,
            'checks' => [
                [
                    'label' => __('messages.seo_check_robots'),
                    'value' => $this->robotsTxt->blocksAll($settings)
                        ? __('messages.seo_status_blocked')
                        : __('messages.seo_health_healthy'),
                    'healthy' => !$this->robotsTxt->blocksAll($settings),
                    'hint' => __('messages.seo_check_robots_hint'),
                ],
                [
                    'label' => __('messages.seo_check_sitemap'),
                    'value' => __('messages.seo_status_urls', ['count' => $indexableUrls]),
                    'healthy' => $indexableUrls > 0,
                    'hint' => __('messages.seo_check_sitemap_hint'),
                ],
                [
                    'label' => __('messages.seo_check_canonical'),
                    'value' => __('messages.seo_status_covered', ['count' => $canonicalCoverage]),
                    'healthy' => $canonicalCoverage > 0,
                    'hint' => __('messages.seo_check_canonical_hint'),
                ],
                [
                    'label' => __('messages.seo_check_descriptions'),
                    'value' => __('messages.seo_status_missing', ['count' => $missingDescriptions]),
                    'healthy' => $missingDescriptions === 0,
                    'hint' => __('messages.seo_check_descriptions_hint'),
                ],
                [
                    'label' => __('messages.seo_check_og_image'),
                    'value' => __('messages.seo_status_missing', ['count' => $missingOgImages]),
                    'healthy' => $missingOgImages === 0,
                    'hint' => __('messages.seo_check_og_image_hint'),
                ],
                [
                    'label' => __('messages.seo_check_noindex_rules'),
                    'value' => __('messages.seo_status_custom', ['count' => $noindexRules]),
                    'healthy' => true,
                    'hint' => __('messages.seo_check_noindex_rules_hint'),
                ],
            ],
            'issues' => $issues,
            'summary_cards' => [
                'users' => $this->safeCount(User::query()),
                'posts' => $this->safeCount(Status::query()),
                'news' => $this->safeCount(News::query()),
                'pages' => $this->safeCount(Page::query()->published()),
                'topics' => $this->safeCount(ForumTopic::query()->where('statu', 1)),
                'listings' => $this->safeCount(Directory::query()->where('statu', 1)),
                'products' => $this->safeCount(Product::withoutGlobalScope('store')->where('o_type', 'store')),
                'indexable_urls' => $indexableUrls,
            ],
            'chart_sets' => $this->metrics->chartSets(),
            'top_scopes' => $this->metrics->topScopes()->toArray(),
            'top_pages' => $this->metrics->topContentPages()->toArray(),
        ];
    }

    public function indexableUrlCount(): int
    {
        $count = 0;

        $count += 8;
        $count += $this->safeCount(Page::query()->published());
        $count += $this->safeCount(News::query()->where('statu', 1));
        $count += $this->safeCount(ForumTopic::query()->where('statu', 1));
        $count += $this->safeCount(\App\Models\DirectoryCategory::query()->where('statu', 1));
        $count += $this->safeCount(Directory::query()->where('statu', 1));
        $count += $this->safeCount(Product::withoutGlobalScope('store')->where('o_type', 'store'));
        
        try {
            $count += Product::withoutGlobalScope('store')
                ->where('o_type', 'store')
                ->whereIn('name', Knowledgebase::query()->select('o_mode')->distinct())
                ->count();
        } catch (\Throwable $e) {}

        $count += $this->safeCount(Knowledgebase::query());
        $count += $this->safeCount(User::query());

        if ($this->schema->hasTable('seo_rules')) {
            try {
                $count -= SeoRule::query()
                    ->where('indexable', false)
                    ->where(function ($query) {
                        $query->whereNull('content_type')->orWhereNull('content_id');
                    })
                    ->count();
            } catch (\Throwable $e) {}
        }

        return max(0, $count);
    }

    public function missingDescriptionsCount(): int
    {
        return Page::query()->published()->where(function ($query) {
            $query->whereNull('meta_description')->orWhere('meta_description', '');
        })->count()
            + News::query()->where(function ($query) {
                $query->whereNull('text')->orWhere('text', '');
            })->count()
            + ForumTopic::query()->where(function ($query) {
                $query->whereNull('txt')->orWhere('txt', '');
            })->count()
            + Directory::query()->where(function ($query) {
                $query->whereNull('txt')->orWhere('txt', '');
            })->count()
            + Product::withoutGlobalScope('store')->where('o_type', 'store')->where(function ($query) {
                $query->whereNull('o_valuer')->orWhere('o_valuer', '');
            })->count()
            + Knowledgebase::query()->where(function ($query) {
                $query->whereNull('o_valuer')->orWhere('o_valuer', '');
            })->count();
    }

    public function missingOgImagesCount(SeoSetting $settings): int
    {
        $hasGlobalDefault = trim((string) $settings->default_og_image) !== '';

        if ($hasGlobalDefault) {
            return 0;
        }

        return News::query()->where(function ($query) {
            $query->whereNull('img')->orWhere('img', '');
        })->count()
            + $this->safeCount(Product::withoutGlobalScope('store')->where('o_type', 'store')->where(function ($query) {
                $query->whereNull('o_mode')->orWhere('o_mode', '');
            }));
    }

    private function safeCount($query): int
    {
        try {
            $table = $query->getModel()->getTable();
            if (!$this->schema->hasTable($table)) {
                return 0;
            }

            return $query->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
