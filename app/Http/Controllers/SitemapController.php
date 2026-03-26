<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\DirectoryCategory;
use App\Models\ForumTopic;
use App\Models\Knowledgebase;
use App\Models\News;
use App\Models\Page;
use App\Models\Product;
use App\Models\SeoRule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class SitemapController extends Controller
{
    protected int $chunkSize = 10000;

    private ?array $disabledScopes = null;

    private array $excludedContentIds = [];

    public function index()
    {
        try {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            $xml .= '<?xml-stylesheet type="text/xsl" href="' . e(asset('sitemap.xsl')) . '"?>' . PHP_EOL;
            $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

            foreach ($this->sectionIndex() as $type => $pages) {
                for ($i = 1; $i <= $pages; $i++) {
                    $xml .= '  <sitemap>' . PHP_EOL;
                    $xml .= '    <loc>' . e(url("sitemap/{$type}/{$i}.xml")) . '</loc>' . PHP_EOL;
                    $xml .= '    <lastmod>' . date('c') . '</lastmod>' . PHP_EOL;
                    $xml .= '  </sitemap>' . PHP_EOL;
                }
            }

            $xml .= '</sitemapindex>';

            return response($xml)->header('Content-Type', 'text/xml');
        } catch (\Throwable $e) {
            // Return empty sitemap index on failure instead of 500
            $xml = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>';
            return response($xml)->header('Content-Type', 'text/xml');
        }
    }

    public function section($type, $page = 1)
    {
        if (!array_key_exists($type, $this->sectionIndex())) {
            abort(404);
        }

        $page = max(1, (int) $page);
        $offset = ($page - 1) * $this->chunkSize;

        return response()->stream(function () use ($type, $offset) {
            echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            echo '<?xml-stylesheet type="text/xsl" href="' . e(asset('sitemap.xsl')) . '"?>' . PHP_EOL;
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

            match ($type) {
                'static' => $this->streamStaticEntries(),
                'pages' => $this->streamModelEntries(
                    $this->publishedPagesQuery()->offset($offset)->limit($this->chunkSize)->get(),
                    fn (Page $page) => route('page.show', $page->slug),
                    fn (Page $page) => $page->updated_at,
                    'weekly',
                    '0.8'
                ),
                'news' => $this->streamModelEntries(
                    $this->publishedNewsQuery()->offset($offset)->limit($this->chunkSize)->get(),
                    fn (News $article) => route('news.show', $article->id),
                    fn (News $article) => $article->date,
                    'daily',
                    '0.7'
                ),
                'forum_categories' => $this->streamModelEntries(
                    $this->publishedForumCategoriesQuery()->offset($offset)->limit($this->chunkSize)->get(),
                    fn (\App\Models\ForumCategory $category) => route('forum.category', $category->id),
                    fn () => null,
                    'weekly',
                    '0.6'
                ),
                'topics' => $this->streamModelEntries(
                    $this->publishedTopicsQuery()->offset($offset)->limit($this->chunkSize)->get(),
                    fn (ForumTopic $topic) => route('forum.topic', $topic->id),
                    fn (ForumTopic $topic) => $topic->date,
                    'daily',
                    '0.6'
                ),
                'directory_categories' => $this->streamModelEntries(
                    $this->publishedDirectoryCategoriesQuery()->offset($offset)->limit($this->chunkSize)->get(),
                    fn (DirectoryCategory $category) => route('directory.category', $category->id),
                    fn () => null,
                    'weekly',
                    '0.6'
                ),
                'directories' => $this->streamModelEntries(
                    $this->publishedDirectoriesQuery()->offset($offset)->limit($this->chunkSize)->get(),
                    fn (Directory $listing) => route('directory.show', $listing->id),
                    fn (Directory $listing) => $listing->date,
                    'weekly',
                    '0.6'
                ),
                'products' => $this->streamModelEntries(
                    $this->publishedProductsQuery()->offset($offset)->limit($this->chunkSize)->get(),
                    fn (Product $product) => route('store.show', $product->name),
                    fn () => null,
                    'weekly',
                    '0.7'
                ),
                'knowledgebase_indexes' => $this->streamModelEntries(
                    $this->knowledgebaseIndexesQuery()->offset($offset)->limit($this->chunkSize)->get(),
                    fn (Product $product) => route('kb.index', $product->name),
                    fn () => null,
                    'weekly',
                    '0.6'
                ),
                'knowledgebases' => $this->streamModelEntries(
                    $this->publishedKnowledgebaseArticlesQuery()->offset($offset)->limit($this->chunkSize)->get(),
                    fn (Knowledgebase $article) => route('kb.show', ['name' => $article->o_mode, 'article' => $article->name]),
                    fn () => null,
                    'monthly',
                    '0.6'
                ),
                'users' => $this->streamModelEntries(
                    $this->publishedUsersQuery()->offset($offset)->limit($this->chunkSize)->get(),
                    fn (User $user) => route('profile.show', $user->username),
                    fn () => null,
                    'weekly',
                    '0.5'
                ),
                default => abort(404),
            };

            echo '</urlset>';
        }, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function generate()
    {
        $path = public_path('sitemap.xml');
        if (File::exists($path)) {
            File::delete($path);
        }

        return redirect()->back()->with('success', 'Sitemap configuration updated. It is now served dynamically.');
    }

    private function sectionIndex(): array
    {
        $sections = [];

        if ($this->staticEntries()->isNotEmpty()) {
            $sections['static'] = 1;
        }

        try {
            $sectionCounts = [
                'pages' => $this->safeCount($this->publishedPagesQuery()),
                'news' => $this->safeCount($this->publishedNewsQuery()),
                'forum_categories' => $this->safeCount($this->publishedForumCategoriesQuery()),
                'topics' => $this->safeCount($this->publishedTopicsQuery()),
                'directory_categories' => $this->safeCount($this->publishedDirectoryCategoriesQuery()),
                'directories' => $this->safeCount($this->publishedDirectoriesQuery()),
                'products' => $this->safeCount($this->publishedProductsQuery()),
                'knowledgebase_indexes' => $this->safeCount($this->knowledgebaseIndexesQuery()),
                'knowledgebases' => $this->safeCount($this->publishedKnowledgebaseArticlesQuery()),
                'users' => $this->safeCount($this->publishedUsersQuery()),
            ];

            foreach ($sectionCounts as $type => $count) {
                if ($count > 0) {
                    $sections[$type] = (int) ceil($count / $this->chunkSize);
                }
            }
        } catch (\Throwable $e) {
            // Return whatever we have so far
        }

        return $sections;
    }

    private function safeCount($query): int
    {
        try {
            return $query->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function staticEntries(): Collection
    {
        $defaultLastmod = $this->siteLastModified();
        $entries = collect([
            [
                'scope_key' => 'home',
                'loc' => url('/'),
                'lastmod' => $defaultLastmod,
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'scope_key' => 'portal',
                'loc' => route('portal.index'),
                'lastmod' => $defaultLastmod,
                'changefreq' => 'daily',
                'priority' => '0.9',
            ],
            [
                'scope_key' => 'news_index',
                'loc' => route('news.index'),
                'lastmod' => $this->latestTimestamp(News::query()->where('statu', 1), 'date') ?? $defaultLastmod,
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'scope_key' => 'forum_index',
                'loc' => route('forum.index'),
                'lastmod' => $this->latestTimestamp(ForumTopic::query()->where('statu', 1), 'date') ?? $defaultLastmod,
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'scope_key' => 'directory_index',
                'loc' => route('directory.index'),
                'lastmod' => $this->latestTimestamp(Directory::query()->where('statu', 1), 'date') ?? $defaultLastmod,
                'changefreq' => 'daily',
                'priority' => '0.8',
            ],
            [
                'scope_key' => 'store_index',
                'loc' => route('store.index'),
                'lastmod' => $defaultLastmod,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'scope_key' => 'privacy_page',
                'loc' => route('privacy'),
                'lastmod' => $defaultLastmod,
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
            [
                'scope_key' => 'terms_page',
                'loc' => route('terms'),
                'lastmod' => $defaultLastmod,
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
        ]);

        return $entries
            ->reject(fn (array $entry) => $this->scopeDisabled($entry['scope_key']))
            ->values();
    }

    private function streamStaticEntries(): void
    {
        foreach ($this->staticEntries() as $entry) {
            $this->writeUrlEntry(
                $entry['loc'],
                $entry['lastmod'] ?? null,
                $entry['changefreq'] ?? 'weekly',
                $entry['priority'] ?? '0.7'
            );
        }
    }

    private function streamModelEntries(iterable $items, callable $locResolver, callable $lastmodResolver, string $changefreq, string $priority): void
    {
        foreach ($items as $item) {
            $this->writeUrlEntry(
                $locResolver($item),
                $lastmodResolver($item),
                $changefreq,
                $priority
            );
        }
    }

    private function writeUrlEntry(string $loc, mixed $lastmod = null, string $changefreq = 'weekly', string $priority = '0.7'): void
    {
        echo '  <url>' . PHP_EOL;
        echo '    <loc>' . e($loc) . '</loc>' . PHP_EOL;

        $formattedLastmod = $this->formatLastmod($lastmod);
        if ($formattedLastmod !== null) {
            echo '    <lastmod>' . e($formattedLastmod) . '</lastmod>' . PHP_EOL;
        }

        echo '    <changefreq>' . e($changefreq) . '</changefreq>' . PHP_EOL;
        echo '    <priority>' . e($priority) . '</priority>' . PHP_EOL;
        echo '  </url>' . PHP_EOL;
    }

    private function publishedPagesQuery(): Builder
    {
        $query = Page::query()->published();

        return $this->applyScopeFilters($query, 'page_show', 'page');
    }

    private function publishedNewsQuery(): Builder
    {
        $query = News::query()->where('statu', 1);

        return $this->applyScopeFilters($query, 'news_show', 'news');
    }

    private function publishedForumCategoriesQuery(): Builder
    {
        $query = \App\Models\ForumCategory::query()->where('visibility', 0);

        return $this->applyScopeFilters($query, 'forum_index', 'forum_category');
    }

    private function publishedTopicsQuery(): Builder
    {
        $query = ForumTopic::query()
            ->where('statu', 1)
            ->visible(); // Respect author privacy

        // Filter by category visibility
        $query->where(function ($q) {
            $q->where('cat', 0) // Home/Uncategorized topics (implicitly public)
              ->orWhereHas('category', function ($sub) {
                  $sub->where('visibility', 0); // Public forum sections
              });
        });

        return $this->applyScopeFilters($query, 'forum_topic', 'forum_topic');
    }

    private function publishedDirectoryCategoriesQuery(): Builder
    {
        $query = DirectoryCategory::query()->where('statu', 1);

        return $this->applyScopeFilters($query, 'directory_category', 'directory_category');
    }

    private function publishedDirectoriesQuery(): Builder
    {
        $query = Directory::query()->where('statu', 1);

        return $this->applyScopeFilters($query, 'directory_show', 'directory');
    }

    private function publishedProductsQuery(): Builder
    {
        $query = Product::visible()->withoutGlobalScope('store')->where('o_type', 'store');

        return $this->applyScopeFilters($query, 'store_show', 'product');
    }

    private function knowledgebaseIndexesQuery(): Builder
    {
        $query = Product::visible()
            ->withoutGlobalScope('store')
            ->where('o_type', 'store')
            ->whereIn('name', Knowledgebase::query()->select('o_mode')->distinct());

        return $this->applyScopeFilters($query, 'kb_index', 'product');
    }

    private function publishedKnowledgebaseArticlesQuery(): Builder
    {
        $query = Knowledgebase::query();

        return $this->applyScopeFilters($query, 'kb_show', 'knowledgebase');
    }

    private function publishedUsersQuery(): Builder
    {
        $query = User::query()
            ->whereIn('id', function ($sub) {
                $sub->select('user_id')
                    ->from('user_privacy_settings')
                    ->where('profile_visibility', 'public');
            });

        return $this->applyScopeFilters($query, 'profile_show', 'user');
    }

    private function applyScopeFilters(Builder $query, string $scopeKey, string $contentType): Builder
    {
        if ($this->scopeDisabled($scopeKey)) {
            $query->whereRaw('1 = 0');

            return $query;
        }

        $excludedIds = $this->excludedContentIds($scopeKey, $contentType);
        if ($excludedIds !== []) {
            $query->whereNotIn($query->getModel()->getQualifiedKeyName(), $excludedIds);
        }

        return $query;
    }

    private function scopeDisabled(string $scopeKey): bool
    {
        if (!Schema::hasTable('seo_rules')) {
            return false;
        }

        if ($this->disabledScopes === null) {
            $this->disabledScopes = SeoRule::query()
                ->where('is_active', true)
                ->where('indexable', false)
                ->whereNull('content_type')
                ->whereNull('content_id')
                ->pluck('scope_key')
                ->flip()
                ->all();
        }

        return isset($this->disabledScopes[$scopeKey]);
    }

    private function excludedContentIds(string $scopeKey, string $contentType): array
    {
        if (!Schema::hasTable('seo_rules')) {
            return [];
        }

        $cacheKey = $scopeKey . ':' . $contentType;
        if (!array_key_exists($cacheKey, $this->excludedContentIds)) {
            $this->excludedContentIds[$cacheKey] = SeoRule::query()
                ->where('scope_key', $scopeKey)
                ->where('content_type', $contentType)
                ->where('is_active', true)
                ->where('indexable', false)
                ->whereNotNull('content_id')
                ->pluck('content_id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return $this->excludedContentIds[$cacheKey];
    }

    private function formatLastmod(mixed $value): ?string
    {
        if ($value instanceof Carbon) {
            return $value->toAtomString();
        }

        if (is_numeric($value) && (int) $value > 0) {
            return Carbon::createFromTimestamp((int) $value)->toAtomString();
        }

        if (is_string($value) && trim($value) !== '') {
            try {
                return Carbon::parse($value)->toAtomString();
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    private function siteLastModified(): ?string
    {
        return $this->latestTimestamp(Page::query()->published(), 'updated_at')
            ?? $this->latestTimestamp(News::query()->where('statu', 1), 'date')
            ?? $this->latestTimestamp(ForumTopic::query()->where('statu', 1), 'date')
            ?? $this->latestTimestamp(Directory::query()->where('statu', 1), 'date')
            ?? now()->toAtomString();
    }

    private function latestTimestamp(Builder $query, string $column): ?string
    {
        $table = $query->getModel()->getTable();
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
            return null;
        }

        $value = $query->max($column);

        return $this->formatLastmod($value);
    }

    public function robots()
    {
        $sitemapUrl = url('/sitemap.xml');
        $content = "User-agent: *\nAllow: /\n\nSitemap: {$sitemapUrl}";

        return response($content, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
