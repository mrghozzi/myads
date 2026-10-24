<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\HelpRating;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;

class HelpContentService
{
    /**
     * Get the dynamic site title from the database.
     * Ensures "myads" is never hardcoded.
     */
    public function getSiteName(): string
    {
        static $cachedName = null;
        if ($cachedName !== null) {
            return $cachedName;
        }

        try {
            $settingTitle = Setting::query()->value('titer');
            $cachedName = !empty($settingTitle) ? trim($settingTitle) : config('app.name', 'Platform');
        } catch (\Throwable $e) {
            $cachedName = config('app.name', 'Platform');
        }

        return $cachedName;
    }

    /**
     * Replace site name placeholders in any string or structure.
     */
    public function replacePlaceholders(string $text): string
    {
        $site = $this->getSiteName();
        return str_replace([':site', ':site_name', '{site}', '{site_name}'], $site, $text);
    }

    /**
     * Get all help categories with localized content.
     */
    public function getCategories(): array
    {
        $categories = [
            'getting-started' => [
                'slug' => 'getting-started',
                'icon' => 'fa-solid fa-rocket',
                'color' => '#615dfa',
                'bg_gradient' => 'linear-gradient(135deg, rgba(97, 93, 250, 0.15), rgba(35, 210, 226, 0.15))',
                'title' => __('help.cat_getting_started_title', ['site' => $this->getSiteName()]),
                'description' => __('help.cat_getting_started_desc', ['site' => $this->getSiteName()]),
            ],
            'ad-exchange' => [
                'slug' => 'ad-exchange',
                'icon' => 'fa-solid fa-rectangle-ad',
                'color' => '#f59e0b',
                'bg_gradient' => 'linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(239, 68, 68, 0.15))',
                'title' => __('help.cat_ad_exchange_title', ['site' => $this->getSiteName()]),
                'description' => __('help.cat_ad_exchange_desc', ['site' => $this->getSiteName()]),
            ],
            'earn-traffic' => [
                'slug' => 'earn-traffic',
                'icon' => 'fa-solid fa-coins',
                'color' => '#10b981',
                'bg_gradient' => 'linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(5, 150, 105, 0.15))',
                'title' => __('help.cat_earn_traffic_title', ['site' => $this->getSiteName()]),
                'description' => __('help.cat_earn_traffic_desc', ['site' => $this->getSiteName()]),
            ],
            'video-shorts' => [
                'slug' => 'video-shorts',
                'icon' => 'fa-solid fa-film',
                'color' => '#ef4444',
                'bg_gradient' => 'linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(217, 70, 239, 0.15))',
                'title' => __('help.cat_video_shorts_title', ['site' => $this->getSiteName()]),
                'description' => __('help.cat_video_shorts_desc', ['site' => $this->getSiteName()]),
            ],
            'community-forums' => [
                'slug' => 'community-forums',
                'icon' => 'fa-solid fa-comments',
                'color' => '#8b5cf6',
                'bg_gradient' => 'linear-gradient(135deg, rgba(139, 92, 246, 0.15), rgba(97, 93, 250, 0.15))',
                'title' => __('help.cat_community_forums_title', ['site' => $this->getSiteName()]),
                'description' => __('help.cat_community_forums_desc', ['site' => $this->getSiteName()]),
            ],
            'store-marketplace' => [
                'slug' => 'store-marketplace',
                'icon' => 'fa-solid fa-store',
                'color' => '#06b6d4',
                'bg_gradient' => 'linear-gradient(135deg, rgba(6, 182, 212, 0.15), rgba(59, 130, 246, 0.15))',
                'title' => __('help.cat_store_marketplace_title', ['site' => $this->getSiteName()]),
                'description' => __('help.cat_store_marketplace_desc', ['site' => $this->getSiteName()]),
            ],
            'directory' => [
                'slug' => 'directory',
                'icon' => 'fa-solid fa-globe',
                'color' => '#3b82f6',
                'bg_gradient' => 'linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(16, 185, 129, 0.15))',
                'title' => __('help.cat_directory_title', ['site' => $this->getSiteName()]),
                'description' => __('help.cat_directory_desc', ['site' => $this->getSiteName()]),
            ],
            'referrals' => [
                'slug' => 'referrals',
                'icon' => 'fa-solid fa-users-gear',
                'color' => '#ec4899',
                'bg_gradient' => 'linear-gradient(135deg, rgba(236, 72, 153, 0.15), rgba(168, 85, 247, 0.15))',
                'title' => __('help.cat_referrals_title', ['site' => $this->getSiteName()]),
                'description' => __('help.cat_referrals_desc', ['site' => $this->getSiteName()]),
            ],
            'billing-memberships' => [
                'slug' => 'billing-memberships',
                'icon' => 'fa-solid fa-crown',
                'color' => '#eab308',
                'bg_gradient' => 'linear-gradient(135deg, rgba(234, 179, 8, 0.15), rgba(249, 115, 22, 0.15))',
                'title' => __('help.cat_billing_memberships_title', ['site' => $this->getSiteName()]),
                'description' => __('help.cat_billing_memberships_desc', ['site' => $this->getSiteName()]),
            ],
            'developer-api' => [
                'slug' => 'developer-api',
                'icon' => 'fa-solid fa-code',
                'color' => '#14b8a6',
                'bg_gradient' => 'linear-gradient(135deg, rgba(20, 184, 166, 0.15), rgba(35, 210, 226, 0.15))',
                'title' => __('help.cat_developer_api_title', ['site' => $this->getSiteName()]),
                'description' => __('help.cat_developer_api_desc', ['site' => $this->getSiteName()]),
            ],
        ];

        // Enrich with article counts
        foreach ($categories as $slug => &$cat) {
            $cat['article_count'] = count($this->getArticles($slug));
        }

        return $categories;
    }

    /**
     * Get single category details.
     */
    public function getCategory(string $slug): ?array
    {
        $categories = $this->getCategories();
        return $categories[$slug] ?? null;
    }

    /**
     * Get all articles or filtered by category.
     */
    public function getArticles(?string $categorySlug = null): array
    {
        $site = $this->getSiteName();
        $articles = [];

        $allArticles = [
            // Category: Getting Started
            [
                'slug' => 'account-registration-and-verification',
                'category' => 'getting-started',
                'read_time' => 3,
                'featured' => true,
                'badge' => 'recommended',
                'icon' => 'fa-solid fa-user-shield',
                'route_link' => url('/profile'),
                'route_label' => __('help.btn_manage_profile', ['site' => $site]),
                'title' => __('help.art_reg_title', ['site' => $site]),
                'summary' => __('help.art_reg_summary', ['site' => $site]),
                'steps' => __('help.art_reg_steps', ['site' => $site]),
                'pro_tip' => __('help.art_reg_tip', ['site' => $site]),
                'note' => __('help.art_reg_note', ['site' => $site]),
                'tags' => ['register', 'email verification', '2FA', 'security', 'account'],
            ],
            [
                'slug' => 'profile-customization-and-privacy',
                'category' => 'getting-started',
                'read_time' => 3,
                'featured' => false,
                'badge' => 'essential',
                'icon' => 'fa-solid fa-id-card',
                'route_link' => url('/profile/edit'),
                'route_label' => __('help.btn_edit_profile', ['site' => $site]),
                'title' => __('help.art_profile_title', ['site' => $site]),
                'summary' => __('help.art_profile_summary', ['site' => $site]),
                'steps' => __('help.art_profile_steps', ['site' => $site]),
                'pro_tip' => __('help.art_profile_tip', ['site' => $site]),
                'note' => __('help.art_profile_note', ['site' => $site]),
                'tags' => ['profile', 'avatar', 'cover', 'privacy', 'bio'],
            ],
            [
                'slug' => 'pts-points-wallet-and-transfers',
                'category' => 'getting-started',
                'read_time' => 4,
                'featured' => true,
                'badge' => 'popular',
                'icon' => 'fa-solid fa-wallet',
                'route_link' => url('/home'),
                'route_label' => __('help.btn_view_wallet', ['site' => $site]),
                'title' => __('help.art_pts_title', ['site' => $site]),
                'summary' => __('help.art_pts_summary', ['site' => $site]),
                'steps' => __('help.art_pts_steps', ['site' => $site]),
                'pro_tip' => __('help.art_pts_tip', ['site' => $site]),
                'note' => __('help.art_pts_note', ['site' => $site]),
                'tags' => ['PTS', 'points', 'transfer', 'wallet', 'balance', 'vouchers'],
            ],
            [
                'slug' => 'daily-quests-and-achievement-badges',
                'category' => 'getting-started',
                'read_time' => 4,
                'featured' => false,
                'badge' => 'rewards',
                'icon' => 'fa-solid fa-trophy',
                'route_link' => url('/quests'),
                'route_label' => __('help.btn_view_quests', ['site' => $site]),
                'title' => __('help.art_quests_title', ['site' => $site]),
                'summary' => __('help.art_quests_summary', ['site' => $site]),
                'steps' => __('help.art_quests_steps', ['site' => $site]),
                'pro_tip' => __('help.art_quests_tip', ['site' => $site]),
                'note' => __('help.art_quests_note', ['site' => $site]),
                'tags' => ['quests', 'badges', 'rewards', 'gamification', 'daily tasks'],
            ],

            // Category: Ad Exchange
            [
                'slug' => 'banner-advertising-guide',
                'category' => 'ad-exchange',
                'read_time' => 5,
                'featured' => true,
                'badge' => 'advertisers',
                'icon' => 'fa-solid fa-image',
                'route_link' => route('ads.index'),
                'route_label' => __('help.btn_manage_banners', ['site' => $site]),
                'title' => __('help.art_banner_title', ['site' => $site]),
                'summary' => __('help.art_banner_summary', ['site' => $site]),
                'steps' => __('help.art_banner_steps', ['site' => $site]),
                'pro_tip' => __('help.art_banner_tip', ['site' => $site]),
                'note' => __('help.art_banner_note', ['site' => $site]),
                'tags' => ['banners', 'advertising', 'impressions', 'clicks', 'CTR', '728x90'],
            ],
            [
                'slug' => 'member-to-member-custom-ads',
                'category' => 'ad-exchange',
                'read_time' => 5,
                'featured' => true,
                'badge' => 'monetization',
                'icon' => 'fa-solid fa-handshake',
                'route_link' => url('/ads/custom'),
                'route_label' => __('help.btn_custom_ads', ['site' => $site]),
                'title' => __('help.art_custom_ads_title', ['site' => $site]),
                'summary' => __('help.art_custom_ads_summary', ['site' => $site]),
                'steps' => __('help.art_custom_ads_steps', ['site' => $site]),
                'pro_tip' => __('help.art_custom_ads_tip', ['site' => $site]),
                'note' => __('help.art_custom_ads_note', ['site' => $site]),
                'tags' => ['custom ads', 'publishers', 'embed script', 'daily payout', 'deals'],
            ],
            [
                'slug' => 'smart-ads-targeting-ab-testing',
                'category' => 'ad-exchange',
                'read_time' => 4,
                'featured' => false,
                'badge' => 'advanced',
                'icon' => 'fa-solid fa-crosshairs',
                'route_link' => url('/ads/smart'),
                'route_label' => __('help.btn_smart_ads', ['site' => $site]),
                'title' => __('help.art_smart_ads_title', ['site' => $site]),
                'summary' => __('help.art_smart_ads_summary', ['site' => $site]),
                'steps' => __('help.art_smart_ads_steps', ['site' => $site]),
                'pro_tip' => __('help.art_smart_ads_tip', ['site' => $site]),
                'note' => __('help.art_smart_ads_note', ['site' => $site]),
                'tags' => ['smart ads', 'geo targeting', 'A/B testing', 'heatmaps', 'conversion'],
            ],
            [
                'slug' => 'anti-fraud-and-viewability-shield',
                'category' => 'ad-exchange',
                'read_time' => 4,
                'featured' => false,
                'badge' => 'security',
                'icon' => 'fa-solid fa-shield-virus',
                'route_link' => route('ads.index'),
                'route_label' => __('help.btn_anti_fraud', ['site' => $site]),
                'title' => __('help.art_anti_fraud_title', ['site' => $site]),
                'summary' => __('help.art_anti_fraud_summary', ['site' => $site]),
                'steps' => __('help.art_anti_fraud_steps', ['site' => $site]),
                'pro_tip' => __('help.art_anti_fraud_tip', ['site' => $site]),
                'note' => __('help.art_anti_fraud_note', ['site' => $site]),
                'tags' => ['anti fraud', 'viewability', 'IAB standard', 'bot shield', 'invalid clicks'],
            ],

            // Category: Earn Traffic
            [
                'slug' => 'surf-to-earn-traffic-exchange',
                'category' => 'earn-traffic',
                'read_time' => 3,
                'featured' => true,
                'badge' => 'earn-pts',
                'icon' => 'fa-solid fa-compass',
                'route_link' => route('visits.index'),
                'route_label' => __('help.btn_start_surfing', ['site' => $site]),
                'title' => __('help.art_surf_title', ['site' => $site]),
                'summary' => __('help.art_surf_summary', ['site' => $site]),
                'steps' => __('help.art_surf_steps', ['site' => $site]),
                'pro_tip' => __('help.art_surf_tip', ['site' => $site]),
                'note' => __('help.art_surf_note', ['site' => $site]),
                'tags' => ['surf to earn', 'traffic exchange', 'timer', 'earn points', 'website visits'],
            ],
            [
                'slug' => 'youtube-views-exchange-guide',
                'category' => 'earn-traffic',
                'read_time' => 3,
                'featured' => false,
                'badge' => 'youtube',
                'icon' => 'fa-brands fa-youtube',
                'route_link' => url('/youtube'),
                'route_label' => __('help.btn_yt_exchange', ['site' => $site]),
                'title' => __('help.art_youtube_title', ['site' => $site]),
                'summary' => __('help.art_youtube_summary', ['site' => $site]),
                'steps' => __('help.art_youtube_steps', ['site' => $site]),
                'pro_tip' => __('help.art_youtube_tip', ['site' => $site]),
                'note' => __('help.art_youtube_note', ['site' => $site]),
                'tags' => ['youtube', 'views exchange', 'video promotion', 'earn PTS', 'watch time'],
            ],

            // Category: Video & Shorts
            [
                'slug' => 'video-hub-upload-and-streaming',
                'category' => 'video-shorts',
                'read_time' => 4,
                'featured' => true,
                'badge' => 'cinema',
                'icon' => 'fa-solid fa-play',
                'route_link' => route('video.index'),
                'route_label' => __('help.btn_explore_video_hub', ['site' => $site]),
                'title' => __('help.art_video_hub_title', ['site' => $site]),
                'summary' => __('help.art_video_hub_summary', ['site' => $site]),
                'steps' => __('help.art_video_hub_steps', ['site' => $site]),
                'pro_tip' => __('help.art_video_hub_tip', ['site' => $site]),
                'note' => __('help.art_video_hub_note', ['site' => $site]),
                'tags' => ['video hub', 'streaming', 'upload video', 'categories', 'spotlight hero'],
            ],
            [
                'slug' => 'theater-mode-ambient-glow-and-shortcuts',
                'category' => 'video-shorts',
                'read_time' => 3,
                'featured' => false,
                'badge' => 'cinema-ui',
                'icon' => 'fa-solid fa-tv',
                'route_link' => route('video.index'),
                'route_label' => __('help.btn_try_theater_mode', ['site' => $site]),
                'title' => __('help.art_theater_title', ['site' => $site]),
                'summary' => __('help.art_theater_summary', ['site' => $site]),
                'steps' => __('help.art_theater_steps', ['site' => $site]),
                'pro_tip' => __('help.art_theater_tip', ['site' => $site]),
                'note' => __('help.art_theater_note', ['site' => $site]),
                'tags' => ['theater mode', 'ambient glow', 'shortcuts', 'PiP', 'fullscreen'],
            ],
            [
                'slug' => 'clips-and-shorts-discovery',
                'category' => 'video-shorts',
                'read_time' => 3,
                'featured' => false,
                'badge' => 'reels',
                'icon' => 'fa-solid fa-mobile-screen',
                'route_link' => url('/clips'),
                'route_label' => __('help.btn_watch_clips', ['site' => $site]),
                'title' => __('help.art_clips_title', ['site' => $site]),
                'summary' => __('help.art_clips_summary', ['site' => $site]),
                'steps' => __('help.art_clips_steps', ['site' => $site]),
                'pro_tip' => __('help.art_clips_tip', ['site' => $site]),
                'note' => __('help.art_clips_note', ['site' => $site]),
                'tags' => ['clips', 'shorts', '9:16', 'vertical video', 'reels', 'double tap'],
            ],

            // Category: Community & Forums
            [
                'slug' => 'social-feed-posting-and-media',
                'category' => 'community-forums',
                'read_time' => 4,
                'featured' => false,
                'badge' => 'social',
                'icon' => 'fa-solid fa-square-rss',
                'route_link' => url('/portal'),
                'route_label' => __('help.btn_social_feed', ['site' => $site]),
                'title' => __('help.art_social_feed_title', ['site' => $site]),
                'summary' => __('help.art_social_feed_summary', ['site' => $site]),
                'steps' => __('help.art_social_feed_steps', ['site' => $site]),
                'pro_tip' => __('help.art_social_feed_tip', ['site' => $site]),
                'note' => __('help.art_social_feed_note', ['site' => $site]),
                'tags' => ['feed', 'posting', 'galleries', 'link preview', 'saved posts', 'mentions'],
            ],
            [
                'slug' => 'forum-structure-categories-and-filters',
                'category' => 'community-forums',
                'read_time' => 4,
                'featured' => true,
                'badge' => 'discussions',
                'icon' => 'fa-solid fa-list-check',
                'route_link' => url('/forum'),
                'route_label' => __('help.btn_explore_forum', ['site' => $site]),
                'title' => __('help.art_forum_title', ['site' => $site]),
                'summary' => __('help.art_forum_summary', ['site' => $site]),
                'steps' => __('help.art_forum_steps', ['site' => $site]),
                'pro_tip' => __('help.art_forum_tip', ['site' => $site]),
                'note' => __('help.art_forum_note', ['site' => $site]),
                'tags' => ['forum', 'discussions', 'zero reload', 'filtering', 'categories', 'tags'],
            ],
            [
                'slug' => 'multi-editor-suite-markdown-and-quill',
                'category' => 'community-forums',
                'read_time' => 4,
                'featured' => false,
                'badge' => 'formatting',
                'icon' => 'fa-solid fa-pen-nib',
                'route_link' => url('/forum'),
                'route_label' => __('help.btn_try_editors', ['site' => $site]),
                'title' => __('help.art_editor_title', ['site' => $site]),
                'summary' => __('help.art_editor_summary', ['site' => $site]),
                'steps' => __('help.art_editor_steps', ['site' => $site]),
                'pro_tip' => __('help.art_editor_tip', ['site' => $site]),
                'note' => __('help.art_editor_note', ['site' => $site]),
                'tags' => ['editors', 'markdown', 'quill', 'sceditor', 'formatting', 'preview'],
            ],
            [
                'slug' => 'best-answer-pinned-comments-and-reputation',
                'category' => 'community-forums',
                'read_time' => 3,
                'featured' => false,
                'badge' => 'reputation',
                'icon' => 'fa-solid fa-circle-check',
                'route_link' => url('/forum'),
                'route_label' => __('help.btn_forum_best_answer', ['site' => $site]),
                'title' => __('help.art_best_answer_title', ['site' => $site]),
                'summary' => __('help.art_best_answer_summary', ['site' => $site]),
                'steps' => __('help.art_best_answer_steps', ['site' => $site]),
                'pro_tip' => __('help.art_best_answer_tip', ['site' => $site]),
                'note' => __('help.art_best_answer_note', ['site' => $site]),
                'tags' => ['best answer', 'pinned comments', 'reputation', '+10 PTS', 'solved'],
            ],

            // Category: Store & Marketplace
            [
                'slug' => 'digital-store-buying-and-selling',
                'category' => 'store-marketplace',
                'read_time' => 5,
                'featured' => true,
                'badge' => 'marketplace',
                'icon' => 'fa-solid fa-bag-shopping',
                'route_link' => url('/store'),
                'route_label' => __('help.btn_browse_store', ['site' => $site]),
                'title' => __('help.art_store_title', ['site' => $site]),
                'summary' => __('help.art_store_summary', ['site' => $site]),
                'steps' => __('help.art_store_steps', ['site' => $site]),
                'pro_tip' => __('help.art_store_tip', ['site' => $site]),
                'note' => __('help.art_store_note', ['site' => $site]),
                'tags' => ['digital store', 'scripts', 'plugins', 'licenses', 'downloads', 'reviews'],
            ],
            [
                'slug' => 'product-knowledgebase-wiki',
                'category' => 'store-marketplace',
                'read_time' => 4,
                'featured' => false,
                'badge' => 'wiki',
                'icon' => 'fa-solid fa-book-open',
                'route_link' => url('/store'),
                'route_label' => __('help.btn_view_wiki', ['site' => $site]),
                'title' => __('help.art_wiki_title', ['site' => $site]),
                'summary' => __('help.art_wiki_summary', ['site' => $site]),
                'steps' => __('help.art_wiki_steps', ['site' => $site]),
                'pro_tip' => __('help.art_wiki_tip', ['site' => $site]),
                'note' => __('help.art_wiki_note', ['site' => $site]),
                'tags' => ['knowledgebase', 'wiki', 'documentation', 'markdown', 'mermaid', 'product kb'],
            ],
            [
                'slug' => 'freelance-services-marketplace-orders',
                'category' => 'store-marketplace',
                'read_time' => 5,
                'featured' => true,
                'badge' => 'freelance',
                'icon' => 'fa-solid fa-briefcase',
                'route_link' => url('/orders'),
                'route_label' => __('help.btn_orders_market', ['site' => $site]),
                'title' => __('help.art_orders_title', ['site' => $site]),
                'summary' => __('help.art_orders_summary', ['site' => $site]),
                'steps' => __('help.art_orders_steps', ['site' => $site]),
                'pro_tip' => __('help.art_orders_tip', ['site' => $site]),
                'note' => __('help.art_orders_note', ['site' => $site]),
                'tags' => ['freelance', 'services', 'orders', 'tenders', 'milestones', 'delivery'],
            ],

            // Category: Directory
            [
                'slug' => 'submitting-and-verifying-sites-in-directory',
                'category' => 'directory',
                'read_time' => 4,
                'featured' => false,
                'badge' => 'webmasters',
                'icon' => 'fa-solid fa-folder-plus',
                'route_link' => url('/directory'),
                'route_label' => __('help.btn_submit_directory', ['site' => $site]),
                'title' => __('help.art_directory_sub_title', ['site' => $site]),
                'summary' => __('help.art_directory_sub_summary', ['site' => $site]),
                'steps' => __('help.art_directory_sub_steps', ['site' => $site]),
                'pro_tip' => __('help.art_directory_sub_tip', ['site' => $site]),
                'note' => __('help.art_directory_sub_note', ['site' => $site]),
                'tags' => ['directory', 'submit site', 'ownership verification', 'meta tag', 'pricing models'],
            ],
            [
                'slug' => 'directory-upvotes-and-live-svg-badges',
                'category' => 'directory',
                'read_time' => 3,
                'featured' => false,
                'badge' => 'seo-boost',
                'icon' => 'fa-solid fa-arrow-up-right-dots',
                'route_link' => url('/directory'),
                'route_label' => __('help.btn_directory_upvotes', ['site' => $site]),
                'title' => __('help.art_directory_badge_title', ['site' => $site]),
                'summary' => __('help.art_directory_badge_summary', ['site' => $site]),
                'steps' => __('help.art_directory_badge_steps', ['site' => $site]),
                'pro_tip' => __('help.art_directory_badge_tip', ['site' => $site]),
                'note' => __('help.art_directory_badge_note', ['site' => $site]),
                'tags' => ['upvotes', 'SVG badges', 'directory badge', 'featured badge', 'SEO'],
            ],

            // Category: Referrals
            [
                'slug' => 'referral-link-and-30-day-tracking',
                'category' => 'referrals',
                'read_time' => 4,
                'featured' => true,
                'badge' => 'affiliate',
                'icon' => 'fa-solid fa-link',
                'route_link' => url('/ads/referrals'),
                'route_label' => __('help.btn_get_referral_link', ['site' => $site]),
                'title' => __('help.art_referral_link_title', ['site' => $site]),
                'summary' => __('help.art_referral_link_summary', ['site' => $site]),
                'steps' => __('help.art_referral_link_steps', ['site' => $site]),
                'pro_tip' => __('help.art_referral_link_tip', ['site' => $site]),
                'note' => __('help.art_referral_link_note', ['site' => $site]),
                'tags' => ['referrals', 'affiliate link', '30 day cookie', 'welcome bonus', '+10 PTS'],
            ],
            [
                'slug' => 'affiliate-tiers-and-milestone-badges',
                'category' => 'referrals',
                'read_time' => 3,
                'featured' => false,
                'badge' => 'milestones',
                'icon' => 'fa-solid fa-medal',
                'route_link' => route('badges.all'),
                'route_label' => __('help.btn_view_referral_badges', ['site' => $site]),
                'title' => __('help.art_referral_tiers_title', ['site' => $site]),
                'summary' => __('help.art_referral_tiers_summary', ['site' => $site]),
                'steps' => __('help.art_referral_tiers_steps', ['site' => $site]),
                'pro_tip' => __('help.art_referral_tiers_tip', ['site' => $site]),
                'note' => __('help.art_referral_tiers_note', ['site' => $site]),
                'tags' => ['affiliate tiers', 'ambassador badges', 'referral legend', 'rewards'],
            ],

            // Category: Billing & Memberships
            [
                'slug' => 'vip-membership-plans-and-benefits',
                'category' => 'billing-memberships',
                'read_time' => 4,
                'featured' => true,
                'badge' => 'vip',
                'icon' => 'fa-solid fa-gem',
                'route_link' => url('/billing/plans'),
                'route_label' => __('help.btn_view_plans', ['site' => $site]),
                'title' => __('help.art_vip_title', ['site' => $site]),
                'summary' => __('help.art_vip_summary', ['site' => $site]),
                'steps' => __('help.art_vip_steps', ['site' => $site]),
                'pro_tip' => __('help.art_vip_tip', ['site' => $site]),
                'note' => __('help.art_vip_note', ['site' => $site]),
                'tags' => ['vip', 'membership', 'premium', 'discount', 'free credits', 'glowing badge'],
            ],
            [
                'slug' => 'payment-methods-and-deposit-guide',
                'category' => 'billing-memberships',
                'read_time' => 4,
                'featured' => false,
                'badge' => 'payments',
                'icon' => 'fa-solid fa-credit-card',
                'route_link' => url('/billing/deposit'),
                'route_label' => __('help.btn_deposit_funds', ['site' => $site]),
                'title' => __('help.art_payments_title', ['site' => $site]),
                'summary' => __('help.art_payments_summary', ['site' => $site]),
                'steps' => __('help.art_payments_steps', ['site' => $site]),
                'pro_tip' => __('help.art_payments_tip', ['site' => $site]),
                'note' => __('help.art_payments_note', ['site' => $site]),
                'tags' => ['payments', 'stripe', 'paypal', 'bank wire', 'lemon squeezy', 'receipts'],
            ],

            // Category: Developer Platform
            [
                'slug' => 'oauth-apps-and-api-tokens',
                'category' => 'developer-api',
                'read_time' => 5,
                'featured' => false,
                'badge' => 'developers',
                'icon' => 'fa-solid fa-terminal',
                'route_link' => route('developer.index'),
                'route_label' => __('help.btn_developer_hub', ['site' => $site]),
                'title' => __('help.art_api_title', ['site' => $site]),
                'summary' => __('help.art_api_summary', ['site' => $site]),
                'steps' => __('help.art_api_steps', ['site' => $site]),
                'pro_tip' => __('help.art_api_tip', ['site' => $site]),
                'note' => __('help.art_api_note', ['site' => $site]),
                'tags' => ['developer', 'oauth 2.0', 'api tokens', 'rest api', 'scopes', 'webhooks'],
            ],
        ];

        if ($categorySlug !== null) {
            $filtered = array_filter($allArticles, function ($art) use ($categorySlug) {
                return $art['category'] === $categorySlug;
            });
            return array_values($filtered);
        }

        return $allArticles;
    }

    /**
     * Get single article with full rating stats and adjacent navigation.
     */
    public function getArticle(string $categorySlug, string $articleSlug, ?int $userId = null, ?string $ipAddress = null): ?array
    {
        $articles = $this->getArticles();
        $found = null;
        $index = -1;

        foreach ($articles as $i => $art) {
            if ($art['category'] === $categorySlug && $art['slug'] === $articleSlug) {
                $found = $art;
                $index = $i;
                break;
            }
        }

        if (!$found) {
            return null;
        }

        // Attach category metadata
        $found['category_data'] = $this->getCategory($categorySlug);

        // Attach rating stats from database
        $found['rating'] = HelpRating::getStats($found['slug'], $userId, $ipAddress);

        // Previous and Next articles
        $found['prev_article'] = $index > 0 ? $articles[$index - 1] : null;
        $found['next_article'] = $index < count($articles) - 1 ? $articles[$index + 1] : null;

        // Related guides (same category or similar tags, excluding self)
        $related = array_filter($articles, function ($a) use ($found) {
            return $a['slug'] !== $found['slug'] && ($a['category'] === $found['category']);
        });
        $found['related'] = array_slice(array_values($related), 0, 3);

        return $found;
    }

    /**
     * Search articles by keyword.
     */
    public function search(string $query): array
    {
        $q = trim(mb_strtolower($query));
        if ($q === '') {
            return [];
        }

        $all = $this->getArticles();
        $matches = [];

        foreach ($all as $art) {
            $score = 0;
            $title = mb_strtolower($art['title']);
            $summary = mb_strtolower($art['summary']);
            $tags = array_map('mb_strtolower', $art['tags']);

            if (str_contains($title, $q)) {
                $score += 10;
            }
            if (str_contains($summary, $q)) {
                $score += 5;
            }
            foreach ($tags as $t) {
                if (str_contains($t, $q)) {
                    $score += 4;
                }
            }

            if ($score > 0) {
                $art['search_score'] = $score;
                $art['category_data'] = $this->getCategory($art['category']);
                $matches[] = $art;
            }
        }

        usort($matches, function ($a, $b) {
            return $b['search_score'] <=> $a['search_score'];
        });

        return $matches;
    }

    /**
     * Get featured tutorials for homepage.
     */
    public function getFeaturedArticles(int $limit = 6): array
    {
        $all = $this->getArticles();
        $featured = array_filter($all, fn($a) => $a['featured'] ?? false);

        if (count($featured) < $limit) {
            return array_slice($all, 0, $limit);
        }

        return array_slice(array_values($featured), 0, $limit);
    }
}
