<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\DirectoryCategory;
use App\Models\Like;
use App\Models\Option;
use App\Models\Short;
use App\Models\Status;
use App\Support\DirectoryPresenter;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DirectoryController extends Controller
{
    public function index()
    {
        $categoryBoard = $this->buildDirectoryBoard();
        $directoryStats = $this->buildDirectoryStats();

        try {
            ['activities' => $activities, 'cards' => $cards] = $this->buildDirectoryFeed();
        } catch (\Throwable $e) {
            $activities = new LengthAwarePaginator([], 0, 15);
            $cards = collect();
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'html' => view('theme::directory.partials.feed_items', compact('cards'))->render(),
                'next_page_url' => $activities->nextPageUrl(),
            ]);
        }

        $this->seo([
            'scope_key' => 'directory_index',
            'resource_title' => __('messages.seo_directory_title'),
            'description' => __('messages.seo_directory_description'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.directory'), 'url' => route('directory.index')],
            ],
        ]);

        return view('theme::directory.index', compact('categoryBoard', 'directoryStats', 'activities', 'cards'));
    }

    public function category($id)
    {
        try {
            $category = DirectoryCategory::findOrFail($id);
            $subCategories = DirectoryCategory::where('sub', $id)
                ->where('statu', 1)
                ->orderBy('ordercat', 'asc')
                ->get();

            ['activities' => $activities, 'cards' => $cards] = $this->buildDirectoryFeed((int) $id);
            $categorySummary = $this->buildCategorySummary($category, $subCategories);
        } catch (\Throwable $e) {
            abort(404);
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'html' => view('theme::directory.partials.feed_items', compact('cards'))->render(),
                'next_page_url' => $activities->nextPageUrl(),
            ]);
        }

        $this->seo([
            'scope_key' => 'directory_category',
            'content_type' => 'directory_category',
            'content_id' => $category->id,
            'resource_title' => $category->name,
            'category_name' => $category->name,
            'description' => trim((string) ($category->txt ?: __('messages.seo_directory_category_description', ['category' => $category->name]))),
            'keywords' => $category->metakeywords,
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.directory'), 'url' => route('directory.index')],
                ['name' => $category->name, 'url' => route('directory.category.legacy', $category->id)],
            ],
        ]);

        return view('theme::directory.category', compact('category', 'subCategories', 'activities', 'cards', 'categorySummary'));
    }

    public function show($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }

        try {
            $listing = Directory::visible()->with(['user', 'category'])->findOrFail($id);
            $activity = Status::with('user')
                ->where('s_type', 1)
                ->where('tp_id', $listing->id)
                ->first();

            if (!$activity) {
                abort(404);
            }

            $activity->related_content = $listing;
            $metrics = $this->buildListingMetrics(collect([$listing->id]));
            $detail = DirectoryPresenter::presentListing($listing, $activity, [
                'reactions_count' => (int) $metrics['reactionCounts']->get($listing->id, 0),
                'comments_count' => (int) $metrics['commentCounts']->get($listing->id, 0),
                'current_reaction' => $metrics['currentReactions']->get($listing->id),
                'can_manage' => $this->canManageListing($listing),
            ]);

            $this->seo([
                'scope_key' => 'directory_show',
                'content_type' => 'directory',
                'content_id' => $listing->id,
                'resource_title' => $listing->name,
                'category_name' => $listing->category?->name,
                'description' => trim((string) ($listing->txt ?: __('messages.seo_directory_listing_description', ['title' => $listing->name]))),
                'keywords' => $listing->metakeywords,
                'lastmod' => $listing->date,
                'breadcrumbs' => [
                    ['name' => __('messages.home'), 'url' => url('/')],
                    ['name' => __('messages.directory'), 'url' => route('directory.index')],
                    ['name' => $listing->category?->name ?: __('messages.category_fallback'), 'url' => route('directory.category.legacy', $listing->cat)],
                    ['name' => $listing->name, 'url' => route('directory.show', $listing->id)],
                ],
            ]);

            return view('theme::directory.show', compact('listing', 'activity', 'detail'));
        } catch (\Throwable $e) {
            abort(404);
        }
    }

    public function showShort($id)
    {
        return redirect()->route('directory.show', $id);
    }

    public function redirectShort($hash)
    {
        if (strpos($hash, 'site-') === 0) {
            $realHash = substr($hash, 5);
            $short = Short::where('sho', $realHash)->where('sh_type', 1)->first();

            if (!$short) {
                return abort(404);
            }
        } else {
            $short = Short::where('sho', $hash)->where('sh_type', 1)->first();
        }

        if (!$short) {
            return abort(404);
        }

        $listing = Directory::find($short->tp_id);
        if ($listing) {
            $listing->increment('vu');
        }

        if (filter_var($short->url, FILTER_VALIDATE_URL) === false) {
            return abort(404);
        }

        return redirect($short->url);
    }

    public function create()
    {
        $categories = DirectoryCategory::where('statu', 1)
            ->orderBy('ordercat', 'asc')
            ->get();

        $mainCategories = $categories->where('sub', 0);
        $subCategories = $categories->where('sub', '!=', 0)->groupBy('sub');

        return view('theme::directory.create', compact('mainCategories', 'subCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'categ' => 'required|integer',
            'txt' => 'nullable|string',
            'tag' => 'nullable|string',
        ]);

        try {
            $listing = Directory::create([
                'uid' => auth()->id(),
                'name' => $request->name,
                'url' => $request->url,
                'cat' => $request->categ,
                'txt' => $request->txt ?? '',
                'metakeywords' => $request->tag ?? '',
                'date' => time(),
                'statu' => 1,
                'vu' => 0,
            ]);

            Status::create([
                'uid' => auth()->id(),
                'tp_id' => $listing->id,
                's_type' => 1,
                'date' => time(),
            ]);

            $hash = hash('crc32', $listing->url . $listing->id);
            Short::create([
                'uid' => auth()->id(),
                'url' => $listing->url,
                'sho' => $hash,
                'clik' => 0,
                'sh_type' => 1,
                'tp_id' => $listing->id,
            ]);

            app(\App\Services\GamificationService::class)->recordEvent(auth()->id(), 'directory_submission_created');

            return redirect()->route('directory.show', $listing->id)->with('success', __('WebsiteCreated'));
        } catch (\Throwable $e) {
            return redirect()->back()
                ->withErrors(['error' => __('messages.error_occurred') . ': ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $listing = Directory::findOrFail($id);

        if ($listing->uid != auth()->id() && !auth()->user()->isAdmin()) {
            return abort(403);
        }

        $categories = DirectoryCategory::where('statu', 1)
            ->orderBy('ordercat', 'asc')
            ->get();

        $mainCategories = $categories->where('sub', 0);
        $subCategories = $categories->where('sub', '!=', 0)->groupBy('sub');

        return view('theme::directory.edit', compact('listing', 'mainCategories', 'subCategories'));
    }

    public function update(Request $request, $id)
    {
        $listing = Directory::findOrFail($id);

        if ($listing->uid != auth()->id() && !auth()->user()->isAdmin()) {
            return abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'categ' => 'required|integer',
            'txt' => 'nullable|string',
            'tag' => 'nullable|string',
        ]);

        $listing->update([
            'name' => $request->name,
            'url' => $request->url,
            'cat' => $request->categ,
            'txt' => $request->txt ?? '',
            'metakeywords' => $request->tag ?? '',
        ]);

        $hash = hash('crc32', $listing->url . $listing->id);
        Short::updateOrCreate(
            ['tp_id' => $listing->id, 'sh_type' => 1],
            [
                'uid' => auth()->id(),
                'url' => $listing->url,
                'sho' => $hash,
            ]
        );

        return redirect()->route('directory.show', $listing->id)->with('success', __('EditWebsite'));
    }

    public function destroy(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return response()->json(['error' => 'Missing ID'], 400);
        }

        $listing = Directory::find($id);
        if (!$listing) {
            return response()->json(['error' => 'Not found'], 404);
        }

        if ($listing->uid != auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        DB::transaction(function () use ($listing, $id) {
            Status::where('tp_id', $id)->where('s_type', 1)->delete();

            Option::where('o_parent', $id)
                ->whereIn('o_type', ['d_coment', 'data_reaction'])
                ->delete();

            $listing->delete();
        });

        return response()->json(['success' => true]);
    }

    private function buildDirectoryFeed(?int $categoryId = null): array
    {
        $query = Status::visible()
            ->with('user')
            ->where('s_type', 1)
            ->where('date', '<', time())
            ->orderBy('date', 'desc');

        if ($categoryId !== null) {
            $directoryIds = Directory::where('cat', $categoryId)
                ->where('statu', 1)
                ->pluck('id');

            $query->whereIn('tp_id', $directoryIds->all());
        }

        $activities = $query->paginate(15);
        $directories = $this->attachDirectoryContent($activities);
        $metrics = $this->buildListingMetrics($directories->keys());

        $cards = collect();

        foreach ($activities as $activity) {
            $cards->push(DirectoryPresenter::presentFeedItem($activity, [
                'reactions_count' => (int) $metrics['reactionCounts']->get($activity->tp_id, 0),
                'comments_count' => (int) $metrics['commentCounts']->get($activity->tp_id, 0),
                'current_reaction' => $metrics['currentReactions']->get($activity->tp_id),
                'can_manage' => $this->canManageListing($activity->related_content),
            ]));
        }

        return [
            'activities' => $activities,
            'cards' => $cards->filter()->values(),
        ];
    }

    private function attachDirectoryContent(LengthAwarePaginator $activities): Collection
    {
        $listingIds = $activities->pluck('tp_id')
            ->filter()
            ->unique()
            ->values();

        if ($listingIds->isEmpty()) {
            return collect();
        }

        $directories = Directory::with(['user', 'category'])
            ->whereIn('id', $listingIds)
            ->get()
            ->keyBy('id');

        foreach ($activities as $activity) {
            $activity->related_content = $directories->get($activity->tp_id);
        }

        return $directories;
    }

    private function buildListingMetrics(Collection $listingIds): array
    {
        if ($listingIds->isEmpty()) {
            return [
                'reactionCounts' => collect(),
                'commentCounts' => collect(),
                'currentReactions' => collect(),
            ];
        }

        $reactionCounts = Like::query()
            ->select('sid', DB::raw('count(*) as aggregate'))
            ->where('type', 22)
            ->whereIn('sid', $listingIds)
            ->groupBy('sid')
            ->pluck('aggregate', 'sid');

        $commentCounts = Option::query()
            ->select('o_parent', DB::raw('count(*) as aggregate'))
            ->where('o_type', 'd_coment')
            ->whereIn('o_parent', $listingIds)
            ->groupBy('o_parent')
            ->pluck('aggregate', 'o_parent');

        $currentReactions = collect();

        if (Auth::check()) {
            $likes = Like::query()
                ->where('uid', Auth::id())
                ->where('type', 22)
                ->whereIn('sid', $listingIds)
                ->get(['id', 'sid']);

            $reactionOptions = Option::query()
                ->whereIn('o_parent', $likes->pluck('id'))
                ->where('o_type', 'data_reaction')
                ->pluck('o_valuer', 'o_parent');

            $currentReactions = $likes->mapWithKeys(static function ($like) use ($reactionOptions) {
                return [$like->sid => $reactionOptions->get($like->id, 'like')];
            });
        }

        return [
            'reactionCounts' => $reactionCounts,
            'commentCounts' => $commentCounts,
            'currentReactions' => $currentReactions,
        ];
    }

    private function buildDirectoryBoard(): Collection
    {
        try {
            $topCategories = DirectoryCategory::where('sub', 0)
                ->where('statu', 1)
                ->orderBy('ordercat', 'asc')
                ->get();

            if ($topCategories->isEmpty()) {
                return collect();
            }

            $children = DirectoryCategory::whereIn('sub', $topCategories->pluck('id'))
                ->where('statu', 1)
                ->orderBy('ordercat', 'asc')
                ->get()
                ->groupBy('sub');

            $categoryIds = $topCategories->pluck('id')
                ->merge($children->flatten()->pluck('id'))
                ->unique()
                ->values();

            $listingCounts = Directory::query()
                ->select('cat', DB::raw('count(*) as aggregate'))
                ->where('statu', 1)
                ->whereIn('cat', $categoryIds)
                ->groupBy('cat')
                ->pluck('aggregate', 'cat');

            return $topCategories->map(function (DirectoryCategory $category) use ($children, $listingCounts) {
                $childItems = $children->get($category->id, collect())->map(function (DirectoryCategory $child) use ($listingCounts) {
                    return [
                        'category' => $child,
                        'listing_count' => (int) $listingCounts->get($child->id, 0),
                    ];
                })->values();

                return [
                    'category' => $category,
                    'listing_count' => (int) $listingCounts->get($category->id, 0) + $childItems->sum('listing_count'),
                    'children' => $childItems,
                ];
            })->values();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private function buildDirectoryStats(): array
    {
        try {
            return [
                'listing_count' => Directory::where('statu', 1)->count(),
                'category_count' => DirectoryCategory::where('statu', 1)->where('sub', 0)->count(),
                'subcategory_count' => DirectoryCategory::where('statu', 1)->where('sub', '!=', 0)->count(),
            ];
        } catch (\Throwable $e) {
            return [
                'listing_count' => 0,
                'category_count' => 0,
                'subcategory_count' => 0,
            ];
        }
    }

    private function buildCategorySummary(DirectoryCategory $category, Collection $subCategories): array
    {
        $counts = Directory::query()
            ->select('cat', DB::raw('count(*) as aggregate'))
            ->where('statu', 1)
            ->whereIn('cat', collect([$category->id])->merge($subCategories->pluck('id'))->all())
            ->groupBy('cat')
            ->pluck('aggregate', 'cat');

        return [
            'category' => $category,
            'directory_url' => route('directory.index'),
            'listing_count' => (int) $counts->get($category->id, 0),
            'subcategory_count' => $subCategories->count(),
            'subcategories' => $subCategories->map(function (DirectoryCategory $subCategory) use ($counts) {
                return [
                    'category' => $subCategory,
                    'listing_count' => (int) $counts->get($subCategory->id, 0),
                    'url' => route('directory.category.legacy', $subCategory->id),
                ];
            })->values(),
        ];
    }

    public function fetchMetadata(Request $request)
    {
        $request->validate(['url' => 'required|url']);
        $url = $request->input('url');

        try {
            $response = Http::timeout(5)->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ])->get($url);

            if (!$response->successful()) {
                return response()->json(['error' => 'Could not fetch URL'], 400);
            }

            $html = $response->body();
            $data = [
                'title' => '',
                'description' => '',
                'tags' => ''
            ];

            // Extract title
            if (preg_match('/<title>(.*?)<\/title>/is', $html, $matches)) {
                $data['title'] = html_entity_decode(trim($matches[1]));
            }

            // Extract description
            if (preg_match('/<meta name="description" content="(.*?)"/is', $html, $matches)) {
                $data['description'] = html_entity_decode(trim($matches[1]));
            } elseif (preg_match('/<meta property="og:description" content="(.*?)"/is', $html, $matches)) {
                $data['description'] = html_entity_decode(trim($matches[1]));
            }

            // Extract keywords (tags)
            if (preg_match('/<meta name="keywords" content="(.*?)"/is', $html, $matches)) {
                $data['tags'] = html_entity_decode(trim($matches[1]));
            }

            return response()->json($data);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function canManageListing(?Directory $listing): bool
    {
        if (!$listing || !Auth::check()) {
            return false;
        }

        return (int) Auth::id() === (int) $listing->uid || Auth::user()->isAdmin();
    }
}
