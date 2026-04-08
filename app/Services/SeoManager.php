<?php

namespace App\Services;

use App\Models\SeoRule;
use App\Models\SeoSetting;
use App\Models\Setting;
use App\Support\SeoHeadSanitizer;
use App\Support\SeoPayload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SeoManager
{
    public const SUPPORTED_SCOPES = [
        'home' => 'Home',
        'portal' => 'Portal',
        'news_index' => 'News Index',
        'news_show' => 'News Article',
        'forum_index' => 'Forum Index',
        'forum_category' => 'Forum Category',
        'forum_topic' => 'Forum Topic',
        'directory_index' => 'Directory Index',
        'directory_category' => 'Directory Category',
        'directory_show' => 'Directory Listing',
        'store_index' => 'Store Index',
        'store_show' => 'Store Product',
        'kb_index' => 'Knowledgebase Index',
        'kb_show' => 'Knowledgebase Article',
        'page_show' => 'Dynamic Page',
        'profile_show' => 'Public Profile',
        'privacy_page' => 'Privacy Page',
        'terms_page' => 'Terms Page',
    ];

    private array $context = [];

    private ?SeoPayload $payload = null;

    public function __construct(
        private readonly SeoHeadSanitizer $headSanitizer,
    ) {
    }

    public function setContext(array $context): void
    {
        $this->context = array_merge($this->context, $context);
        $this->payload = null;
    }

    public function context(): array
    {
        return $this->context;
    }

    public function supportedScopes(): array
    {
        return self::SUPPORTED_SCOPES;
    }

    public function resolve(?Request $request = null): SeoPayload
    {
        $request ??= request();

        if ($this->payload instanceof SeoPayload) {
            return $this->payload;
        }

        $siteSettings = Schema::hasTable('setting') ? Setting::query()->first() : null;
        $seoSettings = SeoSetting::current();
        $context = array_merge($this->inferContext($request), $this->context);
        $scopeKey = (string) ($context['scope_key'] ?? ($request->route()?->getName() ?? 'page'));
        $tokens = $this->buildTokens($context, $siteSettings, $request);
        $rule = $this->resolveRule($scopeKey, $context);
        $indexable = $this->determineIndexable($request, $context, $rule);
        $robots = $indexable
            ? trim((string) ($context['robots'] ?? $rule?->robots ?? $seoSettings->default_robots ?: 'index,follow'))
            : 'noindex,nofollow';
        $canonicalUrl = $this->resolveCanonicalUrl($request, $context, $rule, $seoSettings);
        $title = $this->resolveTitle($context, $rule, $tokens, $siteSettings, $seoSettings);
        $description = $this->resolveDescription($context, $rule, $tokens, $siteSettings, $seoSettings);
        $keywords = $this->resolveKeywords($context, $rule, $seoSettings);
        $image = $this->resolveImage($context, $rule, $seoSettings);
        $lastmod = $this->formatLastmod($context['lastmod'] ?? null);
        $breadcrumbs = $this->normalizeBreadcrumbs($context['breadcrumbs'] ?? []);
        $schemaType = (string) ($context['schema_type'] ?? $rule?->schema_type ?? $this->inferSchemaType($scopeKey));

        $og = array_filter([
            'title' => (string) ($context['og_title'] ?? $rule?->og_title ?? $title),
            'description' => (string) ($context['og_description'] ?? $rule?->og_description ?? $description),
            'type' => $schemaType === 'Article' ? 'article' : 'website',
            'url' => $canonicalUrl,
            'image' => $image,
            'site_name' => $tokens['site'],
            'locale' => str_replace('-', '_', $tokens['locale']),
        ], static fn ($value) => $value !== null && $value !== '');

        $twitter = array_filter([
            'card' => (string) ($context['twitter_card'] ?? $rule?->twitter_card ?? $seoSettings->default_twitter_card),
            'title' => $og['title'] ?? $title,
            'description' => $og['description'] ?? $description,
            'image' => $image,
        ], static fn ($value) => $value !== null && $value !== '');

        $schemaBlocks = $this->buildSchemaBlocks(
            $schemaType,
            $title,
            $description,
            $canonicalUrl,
            $image,
            $lastmod,
            $breadcrumbs,
            $tokens,
            $context
        );

        $headSnippets = $this->buildHeadSnippets($seoSettings, $indexable);

        return $this->payload = new SeoPayload(
            title: $title,
            description: $description,
            keywords: $keywords,
            canonical_url: $canonicalUrl,
            robots: $robots,
            og: $og,
            twitter: $twitter,
            schema_blocks: $schemaBlocks,
            head_snippets: $headSnippets,
            indexable: $indexable,
            lastmod: $lastmod,
        );
    }

    private function inferContext(Request $request): array
    {
        $routeName = $request->route()?->getName() ?? '';
        $scopeKey = match ($routeName) {
            'index' => 'home',
            'portal.index' => 'portal',
            'news.index' => 'news_index',
            'news.show' => 'news_show',
            'forum.index' => 'forum_index',
            'forum.category' => 'forum_category',
            'forum.topic' => 'forum_topic',
            'directory.index' => 'directory_index',
            'directory.category', 'directory.category.legacy' => 'directory_category',
            'directory.show', 'directory.show.short' => 'directory_show',
            'store.index' => 'store_index',
            'store.show' => 'store_show',
            'kb.index' => 'kb_index',
            'kb.show' => 'kb_show',
            'page.show' => 'page_show',
            'privacy' => 'privacy_page',
            'terms' => 'terms_page',
            'profile.show' => 'profile_show',
            default => $routeName !== '' ? $routeName : ($request->path() !== '/' ? $request->path() : 'home'),
        };

        $indexable = true;

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            $indexable = false;
        }

        if ($this->isPrivateRoute($routeName)) {
            $indexable = false;
        }

        $allowedQuery = ['lang', 'page'];
        $queryKeys = array_keys($request->query());
        $hasNonCanonicalQuery = collect($queryKeys)->contains(
            static fn (string $key) => !in_array($key, $allowedQuery, true)
        );

        if ($hasNonCanonicalQuery) {
            $indexable = false;
        }

        return [
            'scope_key' => $scopeKey,
            'indexable' => $indexable,
        ];
    }

    private function isPrivateRoute(string $routeName): bool
    {
        foreach ([
            'admin.',
            'messages.',
            'notifications.',
            'ads.',
            'visits.',
            'dashboard',
            'settings',
            'login',
            'login.post',
            'register',
            'register.post',
            'password.',
            'profile.edit',
            'profile.history',
            'profile.followers',
            'profile.following',
            'forum.edit',
            'forum.update',
            'directory.edit',
            'directory.update',
            'store.create',
            'store.update',
            'store.update.store',
            'kb.edit',
            'kb.pending',
            'kb.history',
            'report.',
        ] as $prefix) {
            if ($routeName === $prefix || str_starts_with($routeName, $prefix)) {
                return true;
            }
        }

        return false;
    }

    private function resolveRule(string $scopeKey, array $context): ?SeoRule
    {
        if (!Schema::hasTable('seo_rules')) {
            return null;
        }

        $query = SeoRule::query()
            ->where('scope_key', $scopeKey)
            ->where('is_active', true);

        $contentType = $context['content_type'] ?? null;
        $contentId = $context['content_id'] ?? null;

        if ($contentType && $contentId) {
            $specific = (clone $query)
                ->where('content_type', $contentType)
                ->where('content_id', $contentId)
                ->latest('id')
                ->first();

            if ($specific) {
                return $specific;
            }
        }

        return $query
            ->whereNull('content_type')
            ->whereNull('content_id')
            ->latest('id')
            ->first();
    }

    private function determineIndexable(Request $request, array $context, ?SeoRule $rule): bool
    {
        if (array_key_exists('indexable', $context)) {
            return (bool) $context['indexable'];
        }

        if ($rule && $rule->indexable !== null) {
            return (bool) $rule->indexable;
        }

        if ($request->route()?->getName()) {
            return !$this->isPrivateRoute($request->route()->getName());
        }

        return true;
    }

    private function resolveCanonicalUrl(Request $request, array $context, ?SeoRule $rule, SeoSetting $settings): ?string
    {
        $explicit = $context['canonical_url'] ?? $rule?->canonical_url ?? null;

        if (is_string($explicit) && trim($explicit) !== '') {
            return $this->absoluteUrl($explicit);
        }

        if ($settings->canonical_mode === 'disabled') {
            return null;
        }

        $query = Arr::only($request->query(), ['lang', 'page']);
        $currentUrl = $request->url();

        if ($query === []) {
            return $currentUrl;
        }

        return $currentUrl . '?' . http_build_query($query);
    }

    private function resolveTitle(array $context, ?SeoRule $rule, array $tokens, ?Setting $siteSettings, SeoSetting $seoSettings): string
    {
        $explicit = trim((string) ($context['title'] ?? ''));
        if ($explicit !== '') {
            return $explicit;
        }

        if ($rule && trim((string) $rule->title) !== '') {
            return $this->applyTemplate($rule->title, $tokens);
        }

        $siteTitle = $tokens['site'];
        $resourceTitle = trim((string) ($context['resource_title'] ?? ''));
        if ($resourceTitle !== '') {
            return $resourceTitle . ' - ' . $siteTitle;
        }

        $fallback = trim((string) ($seoSettings->default_title ?: $siteSettings?->titer ?: config('app.name', 'MyAds')));

        return $fallback !== '' ? $fallback : 'MyAds';
    }

    private function resolveDescription(array $context, ?SeoRule $rule, array $tokens, ?Setting $siteSettings, SeoSetting $seoSettings): ?string
    {
        $explicit = $this->normalizeText($context['description'] ?? null);
        if ($explicit !== null) {
            return Str::limit($explicit, 170, '');
        }

        if ($rule && trim((string) $rule->description) !== '') {
            return Str::limit($this->applyTemplate($rule->description, $tokens), 170, '');
        }

        $fallback = $this->normalizeText($context['excerpt'] ?? null)
            ?? $this->normalizeText($seoSettings->default_description)
            ?? $this->normalizeText($siteSettings?->description);

        return $fallback ? Str::limit($fallback, 170, '') : null;
    }

    private function resolveKeywords(array $context, ?SeoRule $rule, SeoSetting $seoSettings): ?string
    {
        return $this->normalizeText(
            $context['keywords']
            ?? $rule?->keywords
            ?? $seoSettings->default_keywords
        );
    }

    private function resolveImage(array $context, ?SeoRule $rule, SeoSetting $seoSettings): ?string
    {
        return $this->absoluteUrl(
            $context['image']
            ?? $rule?->og_image_url
            ?? $seoSettings->default_og_image
        );
    }

    private function buildHeadSnippets(SeoSetting $settings, bool $indexable): array
    {
        $snippets = [];

        if (trim((string) $settings->google_site_verification) !== '') {
            $snippets[] = '<meta name="google-site-verification" content="' . e($settings->google_site_verification) . '">';
        }

        if (trim((string) $settings->bing_site_verification) !== '') {
            $snippets[] = '<meta name="msvalidate.01" content="' . e($settings->bing_site_verification) . '">';
        }

        if (trim((string) $settings->yandex_site_verification) !== '') {
            $snippets[] = '<meta name="yandex-verification" content="' . e($settings->yandex_site_verification) . '">';
        }

        $sanitized = $this->headSanitizer->sanitize($settings->head_snippets);
        if ($sanitized !== '') {
            $snippets[] = $sanitized;
        }

        if ($indexable && $settings->ga4_enabled && preg_match('/^G-[A-Z0-9]+$/', (string) $settings->ga4_measurement_id)) {
            $measurementId = trim((string) $settings->ga4_measurement_id);
            $snippets[] = '<script async src="https://www.googletagmanager.com/gtag/js?id=' . e($measurementId) . '"></script>';
            $snippets[] = '<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","' . e($measurementId) . '");</script>';
        }

        return $snippets;
    }

    private function buildSchemaBlocks(
        string $schemaType,
        string $title,
        ?string $description,
        ?string $canonicalUrl,
        ?string $image,
        ?string $lastmod,
        array $breadcrumbs,
        array $tokens,
        array $context
    ): array {
        $blocks = [];

        if ($schemaType === 'WebSite') {
            $blocks[] = array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $tokens['site'],
                'url' => url('/'),
                'description' => $description,
                'inLanguage' => $tokens['locale'],
            ]);

            $blocks[] = array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => $tokens['site'],
                'url' => url('/'),
                'logo' => $image,
            ]);
        } elseif ($schemaType === 'Article') {
            $blocks[] = array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $title,
                'description' => $description,
                'image' => $image ? [$image] : null,
                'datePublished' => $lastmod,
                'dateModified' => $lastmod,
                'mainEntityOfPage' => $canonicalUrl,
                'author' => [
                    '@type' => 'Organization',
                    'name' => $tokens['site'],
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => $tokens['site'],
                ],
            ]);
        } elseif ($schemaType === 'DiscussionForumPosting') {
            $blocks[] = array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'DiscussionForumPosting',
                'headline' => $title,
                'articleBody' => $description,
                'datePublished' => $lastmod,
                'dateModified' => $lastmod,
                'mainEntityOfPage' => $canonicalUrl,
                'author' => [
                    '@type' => 'Person',
                    'name' => (string) ($context['author_name'] ?? $tokens['site']),
                ],
                'image' => $image ? [$image] : null,
            ]);
        } else {
            $blocks[] = array_filter([
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => $title,
                'description' => $description,
                'url' => $canonicalUrl,
                'dateModified' => $lastmod,
                'inLanguage' => $tokens['locale'],
            ]);
        }

        if ($breadcrumbs !== []) {
            $blocks[] = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => array_map(
                    static fn (array $breadcrumb, int $index) => [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'name' => $breadcrumb['name'],
                        'item' => $breadcrumb['url'],
                    ],
                    $breadcrumbs,
                    array_keys($breadcrumbs)
                ),
            ];
        }

        return array_values(array_filter($blocks));
    }

    private function buildTokens(array $context, ?Setting $siteSettings, Request $request): array
    {
        $siteTitle = trim((string) ($siteSettings?->titer ?: config('app.name', 'MyAds')));

        return [
            'site' => $siteTitle !== '' ? $siteTitle : 'MyAds',
            'title' => trim((string) ($context['resource_title'] ?? $context['title'] ?? '')),
            'description' => trim((string) ($context['description'] ?? $context['excerpt'] ?? '')),
            'category' => trim((string) ($context['category_name'] ?? '')),
            'username' => trim((string) ($context['username'] ?? '')),
            'locale' => str_replace('_', '-', app()->getLocale()),
            'url' => $request->fullUrl(),
        ];
    }

    private function applyTemplate(string $template, array $tokens): string
    {
        $result = $template;

        foreach ($tokens as $key => $value) {
            $result = str_replace('{' . $key . '}', (string) $value, $result);
        }

        return trim($result);
    }

    private function inferSchemaType(string $scopeKey): string
    {
        return match ($scopeKey) {
            'home' => 'WebSite',
            'news_show' => 'Article',
            'forum_topic' => 'DiscussionForumPosting',
            default => 'WebPage',
        };
    }

    private function normalizeBreadcrumbs(array $breadcrumbs): array
    {
        return array_values(array_filter(array_map(static function ($breadcrumb) {
            if (!is_array($breadcrumb)) {
                return null;
            }

            $name = trim((string) ($breadcrumb['name'] ?? ''));
            $url = trim((string) ($breadcrumb['url'] ?? ''));

            if ($name === '' || $url === '') {
                return null;
            }

            return [
                'name' => $name,
                'url' => $url,
            ];
        }, $breadcrumbs)));
    }

    private function formatLastmod(mixed $value): ?string
    {
        if ($value instanceof Carbon) {
            return $value->toAtomString();
        }

        if (is_numeric($value)) {
            return Carbon::createFromTimestamp((int) $value)->toAtomString();
        }

        if (is_string($value) && trim($value) !== '') {
            return Carbon::parse($value)->toAtomString();
        }

        return null;
    }

    private function normalizeText(mixed $value): ?string
    {
        $value = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $value)) ?? '');

        return $value !== '' ? $value : null;
    }

    private function absoluteUrl(mixed $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        return asset(ltrim($value, '/'));
    }
}
