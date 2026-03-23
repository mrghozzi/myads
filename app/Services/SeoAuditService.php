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
    ) {
    }

    public function dashboard(): array
    {
        $settings = SeoSetting::current();
        $robotsPreview = $this->robotsTxt->render($settings);
        $indexableUrls = $this->indexableUrlCount();
        $missingDescriptions = $this->missingDescriptionsCount();
        $missingOgImages = $this->missingOgImagesCount($settings);
        $noindexRules = Schema::hasTable('seo_rules')
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
                'users' => User::query()->count(),
                'posts' => Status::query()->count(),
                'news' => News::query()->count(),
                'pages' => Page::query()->published()->count(),
                'topics' => ForumTopic::query()->where('statu', 1)->count(),
                'listings' => Directory::query()->where('statu', 1)->count(),
                'products' => Product::withoutGlobalScope('store')->where('o_type', 'store')->count(),
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
        $count += Page::query()->published()->count();
        $count += News::query()->where('statu', 1)->count();
        $count += ForumTopic::query()->where('statu', 1)->count();
        $count += \App\Models\DirectoryCategory::query()->where('statu', 1)->count();
        $count += Directory::query()->where('statu', 1)->count();
        $count += Product::withoutGlobalScope('store')->where('o_type', 'store')->count();
        $count += Product::withoutGlobalScope('store')
            ->where('o_type', 'store')
            ->whereIn('name', Knowledgebase::query()->select('o_mode')->distinct())
            ->count();
        $count += Knowledgebase::query()->count();
        $count += User::query()->count();

        if (Schema::hasTable('seo_rules')) {
            $count -= SeoRule::query()
                ->where('indexable', false)
                ->where(function ($query) {
                    $query->whereNull('content_type')->orWhereNull('content_id');
                })
                ->count();
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
            + Product::withoutGlobalScope('store')->where('o_type', 'store')->where(function ($query) {
                $query->whereNull('o_mode')->orWhere('o_mode', '');
            })->count();
    }
}
