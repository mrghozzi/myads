<?php

namespace App\Http\Controllers;

use App\Models\HelpRating;
use App\Models\Setting;
use App\Services\HelpContentService;
use App\Services\SeoManager;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class HelpController extends Controller
{
    protected HelpContentService $helpService;

    public function __construct(HelpContentService $helpService)
    {
        $this->helpService = $helpService;
    }

    /**
     * Get dynamic site name from database.
     */
    protected function getSiteName(): string
    {
        return $this->helpService->getSiteName();
    }

    /**
     * Display the Help Center homepage.
     */
    public function index(Request $request)
    {
        $siteName = $this->getSiteName();
        $categories = $this->helpService->getCategories();
        $featuredArticles = $this->helpService->getFeaturedArticles(6);
        $allArticles = $this->helpService->getArticles();
        $totalArticlesCount = count($allArticles);

        // Resolve SEO metadata
        $seo = app(SeoManager::class)->resolve($request);
        $pageTitle = __('help.help_center') . ' - ' . $siteName;

        return view('theme::help.index', compact(
            'siteName',
            'categories',
            'featuredArticles',
            'totalArticlesCount',
            'seo',
            'pageTitle'
        ));
    }

    /**
     * Display a specific category of tutorials.
     */
    public function category(Request $request, string $category)
    {
        $siteName = $this->getSiteName();
        $categoryData = $this->helpService->getCategory($category);

        if (!$categoryData) {
            return redirect()->route('help.index')->with('error', __('help.no_articles_in_category'));
        }

        $articles = $this->helpService->getArticles($category);
        $categories = $this->helpService->getCategories();

        // Attach rating stats to each article preview
        $userId = auth()->id();
        $ip = $request->ip();
        foreach ($articles as &$art) {
            $art['rating'] = HelpRating::getStats($art['slug'], $userId, $ip);
        }

        $seo = app(SeoManager::class)->resolve($request);
        $pageTitle = $categoryData['title'] . ' - ' . __('help.help_center') . ' - ' . $siteName;

        return view('theme::help.category', compact(
            'siteName',
            'categoryData',
            'articles',
            'categories',
            'seo',
            'pageTitle'
        ));
    }

    /**
     * Display a specific tutorial with ratings and pro tips.
     */
    public function article(Request $request, string $category, string $article)
    {
        $siteName = $this->getSiteName();
        $userId = auth()->id();
        $ip = $request->ip();

        $articleData = $this->helpService->getArticle($category, $article, $userId, $ip);

        if (!$articleData) {
            return redirect()->route('help.category', $category);
        }

        $categoryData = $articleData['category_data'];
        $categories = $this->helpService->getCategories();

        $seo = app(SeoManager::class)->resolve($request);
        $pageTitle = $articleData['title'] . ' - ' . __('help.help_center') . ' - ' . $siteName;

        $helpService = $this->helpService;

        return view('theme::help.article', compact(
            'siteName',
            'categoryData',
            'articleData',
            'categories',
            'helpService',
            'seo',
            'pageTitle'
        ));
    }

    /**
     * Search tutorials via AJAX or full page.
     */
    public function search(Request $request)
    {
        $siteName = $this->getSiteName();
        $query = (string) $request->input('q', '');
        $results = $this->helpService->search($query);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'query' => $query,
                'count' => count($results),
                'results' => array_map(function ($item) {
                    return [
                        'title' => $item['title'],
                        'summary' => Str::limit($item['summary'], 120),
                        'category_title' => $item['category_data']['title'] ?? '',
                        'icon' => $item['icon'],
                        'url' => route('help.article', ['category' => $item['category'], 'article' => $item['slug']]),
                        'read_time' => $item['read_time'],
                    ];
                }, $results),
            ]);
        }

        $categories = $this->helpService->getCategories();
        $seo = app(SeoManager::class)->resolve($request);
        $pageTitle = __('help.search_results_for', ['query' => $query]) . ' - ' . $siteName;

        return view('theme::help.search', compact(
            'siteName',
            'query',
            'results',
            'categories',
            'seo',
            'pageTitle'
        ));
    }

    /**
     * Handle "Was this helpful?" rating voting.
     */
    public function vote(Request $request): JsonResponse
    {
        $request->validate([
            'article_slug' => 'required|string|max:100',
            'is_helpful' => 'required|boolean',
        ]);

        $articleSlug = (string) $request->input('article_slug');
        $isHelpful = (bool) $request->input('is_helpful');
        $userId = auth()->id();
        $ip = $request->ip();

        $stats = HelpRating::recordVote($articleSlug, $isHelpful, $userId, $ip);

        return response()->json([
            'success' => true,
            'message' => __('help.feedback_thank_you', ['site' => $this->getSiteName()]),
            'stats' => $stats,
        ]);
    }
}
