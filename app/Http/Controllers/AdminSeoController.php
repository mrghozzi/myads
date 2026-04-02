<?php

namespace App\Http\Controllers;

use App\Models\SeoRule;
use App\Models\SeoSetting;
use App\Services\RobotsTxtService;
use App\Services\SeoAuditService;
use App\Services\SeoManager;
use App\Support\SeoHeadSanitizer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminSeoController extends Controller
{
    public function __construct(
        private readonly SeoAuditService $audit,
        private readonly RobotsTxtService $robotsTxt,
        private readonly SeoManager $seoManager,
        private readonly SeoHeadSanitizer $headSanitizer,
    ) {
    }

    public function index()
    {
        $dashboard = $this->audit->dashboard();
        $dashboard['top_scopes'] = collect($dashboard['top_scopes'])
            ->map(function (array $item) {
                $item['label'] = $this->scopeLabel((string) ($item['scope_key'] ?? ''));

                return $item;
            })
            ->values()
            ->all();
        $dashboard['top_pages'] = collect($dashboard['top_pages'])
            ->map(function (array $item) {
                $item['scope_label'] = $this->scopeLabel((string) ($item['scope_key'] ?? ''));

                return $item;
            })
            ->values()
            ->all();

        return view('admin::admin.seo.index', [
            'dashboard' => $dashboard,
            'chartWindow' => (string) request('days', '30'),
        ]);
    }

    public function settings()
    {
        return view('admin::admin.seo.settings', [
            'settings' => SeoSetting::currentPersisted(),
            'canonicalModes' => $this->canonicalModes(),
            'twitterCards' => $this->twitterCards(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'default_title' => ['nullable', 'string', 'max:255'],
            'default_description' => ['nullable', 'string', 'max:2000'],
            'default_keywords' => ['nullable', 'string', 'max:1000'],
            'default_robots' => ['nullable', 'string', 'max:255'],
            'canonical_mode' => ['required', Rule::in(['strip_tracking', 'disabled'])],
            'default_og_image' => ['nullable', 'string', 'max:2048'],
            'default_twitter_card' => ['required', Rule::in(['summary_large_image', 'summary'])],
            'ga4_measurement_id' => ['nullable', 'string', 'max:32', 'regex:/^G-[A-Z0-9]+$/i'],
        ]);

        if ($request->boolean('ga4_enabled') && blank($request->input('ga4_measurement_id'))) {
            return back()
                ->withErrors(['ga4_measurement_id' => $this->message('seo_validation_ga4_required', 'A GA4 Measurement ID is required when Google Analytics is enabled.')])
                ->withInput();
        }

        $settings = SeoSetting::currentPersisted();
        $settings->fill([
            'default_title' => $this->nullableString($validated['default_title'] ?? null),
            'default_description' => $this->nullableString($validated['default_description'] ?? null),
            'default_keywords' => $this->nullableString($validated['default_keywords'] ?? null),
            'default_robots' => $this->nullableString($validated['default_robots'] ?? null) ?: SeoSetting::defaults()['default_robots'],
            'canonical_mode' => $validated['canonical_mode'],
            'default_og_image' => $this->nullableString($validated['default_og_image'] ?? null),
            'default_twitter_card' => $validated['default_twitter_card'],
            'ga4_enabled' => $request->boolean('ga4_enabled'),
            'ga4_measurement_id' => $this->nullableString($validated['ga4_measurement_id'] ?? null),
        ])->save();

        return redirect()
            ->route('admin.seo.settings')
            ->with('success', $this->message('seo_flash_settings_updated', 'SEO defaults were updated successfully.'));
    }

    public function head()
    {
        $settings = SeoSetting::currentPersisted();

        return view('admin::admin.seo.head', [
            'settings' => $settings,
            'sanitizedPreview' => $this->headSanitizer->sanitize($settings->head_snippets),
        ]);
    }

    public function updateHead(Request $request)
    {
        $validated = $request->validate([
            'google_site_verification' => ['nullable', 'string', 'max:255'],
            'bing_site_verification' => ['nullable', 'string', 'max:255'],
            'yandex_site_verification' => ['nullable', 'string', 'max:255'],
            'head_snippets' => ['nullable', 'string', 'max:20000'],
        ]);

        $settings = SeoSetting::currentPersisted();
        $settings->fill([
            'google_site_verification' => $this->nullableString($validated['google_site_verification'] ?? null),
            'bing_site_verification' => $this->nullableString($validated['bing_site_verification'] ?? null),
            'yandex_site_verification' => $this->nullableString($validated['yandex_site_verification'] ?? null),
            'head_snippets' => $this->nullableString($this->headSanitizer->sanitize($validated['head_snippets'] ?? null)),
        ])->save();

        return redirect()
            ->route('admin.seo.head')
            ->with('success', $this->message('seo_flash_head_updated', 'Search verification tags and head snippets were updated.'));
    }

    public function rules()
    {
        return view('admin::admin.seo.rules', [
            'settings' => SeoSetting::currentPersisted(),
            'rules' => SeoRule::query()
                ->orderBy('scope_key')
                ->orderByRaw('CASE WHEN content_type IS NULL THEN 0 ELSE 1 END')
                ->orderByDesc('id')
                ->get(),
            'supportedScopes' => $this->localizedSupportedScopes(),
            'schemaTypes' => $this->schemaTypes(),
            'twitterCards' => $this->twitterCards(),
        ]);
    }

    public function storeRule(Request $request)
    {
        $rule = new SeoRule();
        $this->fillRuleFromRequest($rule, $request);

        return redirect()
            ->route('admin.seo.rules')
            ->with('success', $this->message('seo_flash_rule_created', 'SEO rule created successfully.'));
    }

    public function updateRule(Request $request, SeoRule $rule)
    {
        $this->fillRuleFromRequest($rule, $request);

        return redirect()
            ->route('admin.seo.rules')
            ->with('success', $this->message('seo_flash_rule_updated', 'SEO rule updated successfully.'));
    }

    public function destroyRule(SeoRule $rule)
    {
        $rule->delete();

        return redirect()
            ->route('admin.seo.rules')
            ->with('success', $this->message('seo_flash_rule_deleted', 'SEO rule deleted successfully.'));
    }

    public function indexing()
    {
        $settings = SeoSetting::currentPersisted();
        $dashboard = $this->audit->dashboard();

        return view('admin::admin.seo.indexing', [
            'settings' => $settings,
            'dashboard' => $dashboard,
            'robotsPreview' => $this->robotsTxt->render($settings),
            'sitemapPreview' => $this->sitemapPreview(),
        ]);
    }

    public function updateIndexing(Request $request)
    {
        $validated = $request->validate([
            'robots_allow_paths' => ['nullable', 'string', 'max:5000'],
            'robots_disallow_paths' => ['nullable', 'string', 'max:5000'],
            'robots_extra' => ['nullable', 'string', 'max:5000'],
        ]);

        $settings = SeoSetting::currentPersisted();
        $settings->fill([
            'allow_indexing' => $request->boolean('allow_indexing'),
            'robots_allow_paths' => $this->nullableString($validated['robots_allow_paths'] ?? null) ?: SeoSetting::defaults()['robots_allow_paths'],
            'robots_disallow_paths' => $this->nullableString($validated['robots_disallow_paths'] ?? null) ?: SeoSetting::defaults()['robots_disallow_paths'],
            'robots_extra' => $this->nullableString($validated['robots_extra'] ?? null),
        ])->save();

        return redirect()
            ->route('admin.seo.indexing')
            ->with('success', $this->message('seo_flash_indexing_updated', 'Indexing controls and robots.txt were updated successfully.'));
    }

    private function fillRuleFromRequest(SeoRule $rule, Request $request): void
    {
        $validated = $request->validate([
            'scope_key' => ['required', Rule::in(array_keys($this->seoManager->supportedScopes()))],
            'content_type' => ['nullable', 'string', 'max:100'],
            'content_id' => ['nullable', 'integer', 'min:1'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'keywords' => ['nullable', 'string', 'max:1000'],
            'robots' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'string', 'max:2048'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:1000'],
            'og_image_url' => ['nullable', 'string', 'max:2048'],
            'twitter_card' => ['nullable', Rule::in(array_keys($this->twitterCards()))],
            'schema_type' => ['nullable', Rule::in(array_keys($this->schemaTypes()))],
            'indexable_mode' => ['required', Rule::in(['inherit', 'index', 'noindex'])],
        ]);

        if (filled($validated['content_type'] ?? null) && blank($validated['content_id'] ?? null)) {
            throw ValidationException::withMessages([
                'content_id' => $this->message('seo_validation_content_id_required', 'A content ID is required when a content type is provided.'),
            ]);
        }

        $rule->fill([
            'scope_key' => $validated['scope_key'],
            'content_type' => $this->nullableString($validated['content_type'] ?? null),
            'content_id' => filled($validated['content_type'] ?? null) ? (int) $validated['content_id'] : null,
            'title' => $this->nullableString($validated['title'] ?? null),
            'description' => $this->nullableString($validated['description'] ?? null),
            'keywords' => $this->nullableString($validated['keywords'] ?? null),
            'robots' => $this->nullableString($validated['robots'] ?? null),
            'canonical_url' => $this->nullableString($validated['canonical_url'] ?? null),
            'og_title' => $this->nullableString($validated['og_title'] ?? null),
            'og_description' => $this->nullableString($validated['og_description'] ?? null),
            'og_image_url' => $this->nullableString($validated['og_image_url'] ?? null),
            'twitter_card' => $this->nullableString($validated['twitter_card'] ?? null),
            'schema_type' => $this->nullableString($validated['schema_type'] ?? null),
            'indexable' => match ($validated['indexable_mode']) {
                'index' => true,
                'noindex' => false,
                default => null,
            },
            'is_active' => $request->boolean('is_active', true),
        ]);

        $rule->save();
    }

    private function sitemapPreview(): string
    {
        try {
            return (string) app(SitemapController::class)->index()->getContent();
        } catch (\Throwable $e) {
            return $this->message('seo_sitemap_preview_unavailable', 'Unable to render sitemap preview right now.');
        }
    }

    private function schemaTypes(): array
    {
        return [
            'WebPage' => $this->message('seo_schema_webpage', 'WebPage'),
            'WebSite' => $this->message('seo_schema_website', 'WebSite'),
            'Article' => $this->message('seo_schema_article', 'Article'),
            'DiscussionForumPosting' => $this->message('seo_schema_discussion', 'DiscussionForumPosting'),
        ];
    }

    private function twitterCards(): array
    {
        return [
            'summary_large_image' => $this->message('seo_twitter_summary_large', 'Summary Large Image'),
            'summary' => $this->message('seo_twitter_summary', 'Summary'),
        ];
    }

    private function canonicalModes(): array
    {
        return [
            'strip_tracking' => $this->message('seo_canonical_strip_tracking', 'Strip tracking and noisy query params'),
            'disabled' => $this->message('seo_canonical_disabled', 'Disable canonical tags'),
        ];
    }

    private function localizedSupportedScopes(): array
    {
        return collect($this->seoManager->supportedScopes())
            ->mapWithKeys(fn (string $label, string $key) => [$key => $this->message('seo_scope_' . $key, $label)])
            ->all();
    }

    private function scopeLabel(string $scopeKey): string
    {
        return $this->localizedSupportedScopes()[$scopeKey] ?? $scopeKey;
    }

    private function message(string $key, string $fallback, array $replace = []): string
    {
        $translated = __('messages.' . $key, $replace);

        return $translated === 'messages.' . $key ? $fallback : $translated;
    }

    private function nullableString(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
