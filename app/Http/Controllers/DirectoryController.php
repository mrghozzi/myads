<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\DirectoryCategory;
use App\Models\DirectoryUpvote;
use App\Models\DirectoryClaim;
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
use Illuminate\Support\Str;

class DirectoryController extends Controller
{
    public function index()
    {
        $categoryBoard = $this->buildDirectoryBoard();
        $directoryStats = $this->buildDirectoryStats();

        $filters = [
            'search' => request('search'),
            'pricing' => request('pricing'),
            'sort' => request('sort', 'latest'),
        ];

        try {
            ['activities' => $activities, 'cards' => $cards] = $this->buildDirectoryFeed(null, $filters);
        } catch (\Throwable $e) {
            $activities = new LengthAwarePaginator([], 0, 15);
            $cards = collect();
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'html' => view('theme::directory.partials.feed_items', compact('cards'))->render(),
                'next_page_url' => $activities->nextPageUrl(),
                'total_count' => $activities->total(),
            ]);
        }

        $featuredSites = Directory::where('statu', 1)
            ->where(function ($q) {
                $q->where('is_featured', 1)->orWhere('upvotes_count', '>', 0);
            })
            ->orderBy('is_featured', 'desc')
            ->orderBy('upvotes_count', 'desc')
            ->take(4)
            ->get();

        $this->seo([
            'scope_key' => 'directory_index',
            'resource_title' => __('messages.seo_directory_title'),
            'description' => __('messages.seo_directory_description'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.directory'), 'url' => route('directory.index')],
            ],
        ]);

        return view('theme::directory.index', compact('categoryBoard', 'directoryStats', 'activities', 'cards', 'filters', 'featuredSites'));
    }

    public function category($id)
    {
        try {
            $category = DirectoryCategory::findOrFail($id);
            $subCategories = DirectoryCategory::where('sub', $id)
                ->where('statu', 1)
                ->orderBy('ordercat', 'asc')
                ->get();

            $filters = [
                'search' => request('search'),
                'pricing' => request('pricing'),
                'sort' => request('sort', 'latest'),
            ];

            ['activities' => $activities, 'cards' => $cards] = $this->buildDirectoryFeed((int) $id, $filters);
            $categorySummary = $this->buildCategorySummary($category, $subCategories);
            $categoryBoard = $this->buildDirectoryBoard();
        } catch (\Throwable $e) {
            abort(404);
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'html' => view('theme::directory.partials.feed_items', compact('cards'))->render(),
                'next_page_url' => $activities->nextPageUrl(),
                'total_count' => $activities->total(),
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

        return view('theme::directory.category', compact('category', 'subCategories', 'activities', 'cards', 'categorySummary', 'categoryBoard', 'filters'));
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
            $hasUpvoted = isset($metrics['upvotedIds'][$listing->id]);

            $detail = DirectoryPresenter::presentListing($listing, $activity, [
                'reactions_count' => (int) $metrics['reactionCounts']->get($listing->id, 0),
                'comments_count' => (int) $metrics['commentCounts']->get($listing->id, 0),
                'current_reaction' => $metrics['currentReactions']->get($listing->id),
                'has_upvoted' => $hasUpvoted,
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

    public function showBySlug($id, $slug = null)
    {
        if (is_numeric($id)) {
            return $this->show($id);
        }

        $listing = Directory::where('slug', $id)->first();
        if ($listing) {
            return $this->show($listing->id);
        }

        $parts = explode('-', $id);
        if (is_numeric($parts[0])) {
            $listing = Directory::find($parts[0]);
            if ($listing) {
                return $this->show($listing->id);
            }
        }

        abort(404);
    }

    public function categoryBySlug($id, $slug = null)
    {
        if (is_numeric($id)) {
            return $this->category($id);
        }

        $cat = DirectoryCategory::where('slug', $id)->first();
        if ($cat) {
            return $this->category($cat->id);
        }

        $parts = explode('-', $id);
        if (is_numeric($parts[0])) {
            $cat = DirectoryCategory::find($parts[0]);
            if ($cat) {
                return $this->category($cat->id);
            }
        }

        abort(404);
    }

    public function upvote($id)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $listing = Directory::findOrFail($id);
        $userId = (int) Auth::id();

        $existing = DirectoryUpvote::where('directory_id', $listing->id)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            $existing->delete();
            $listing->decrement('upvotes_count');
            $upvoted = false;
        } else {
            DirectoryUpvote::create([
                'directory_id' => $listing->id,
                'user_id' => $userId,
                'created_at' => time(),
            ]);
            $listing->increment('upvotes_count');
            $upvoted = true;

            try {
                app(\App\Services\GamificationService::class)->recordEvent($userId, 'directory_site_upvoted');
            } catch (\Throwable $e) {
                // Ignore gamification failures
            }
        }

        $listing->refresh();

        return response()->json([
            'success' => true,
            'upvoted' => $upvoted,
            'upvotes_count' => max(0, (int) $listing->upvotes_count),
        ]);
    }

    public function badge($id)
    {
        $listing = Directory::findOrFail($id);
        $upvotes = (int) ($listing->upvotes_count ?? 0);
        $badgeText = $upvotes > 0 ? ("▲ " . $upvotes . " UPVOTES") : "FEATURED";

        $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="220" height="34" viewBox="0 0 220 34" fill="none">
  <defs>
    <linearGradient id="bg_grad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#1f2937" />
      <stop offset="100%" stop-color="#111827" />
    </linearGradient>
    <linearGradient id="accent_grad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#6366f1" />
      <stop offset="100%" stop-color="#4f46e5" />
    </linearGradient>
  </defs>
  <rect width="220" height="34" rx="8" fill="url(#bg_grad)" stroke="#374151" stroke-width="1"/>
  <rect x="120" y="3" width="97" height="28" rx="6" fill="url(#accent_grad)"/>
  <text x="14" y="21" font-family="-apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif" font-size="11" font-weight="700" fill="#9ca3af" letter-spacing="0.5">MYADS</text>
  <text x="64" y="21" font-family="-apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif" font-size="11" font-weight="600" fill="#f3f4f6">DIR</text>
  <text x="168" y="21" text-anchor="middle" font-family="-apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif" font-size="11" font-weight="700" fill="#ffffff" letter-spacing="0.3">' . htmlspecialchars($badgeText) . '</text>
</svg>';

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=600');
    }

    public function claim(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $listing = Directory::findOrFail($id);
        $user = Auth::user();

        if ((int) $listing->uid === (int) $user->id || (int) $listing->claimed_by === (int) $user->id) {
            return response()->json([
                'success' => true,
                'verified' => true,
                'message' => __('messages.already_owner_of_site') ?? 'أنت بالفعل مالك أو موثق هذا الموقع.',
            ]);
        }

        $token = 'myads-verify-' . hash('crc32', $listing->id . '-' . $user->id);

        $verified = false;
        try {
            $response = Http::timeout(5)->get($listing->url);
            if ($response->successful()) {
                $html = $response->body();
                if (str_contains($html, $token) || str_contains($html, 'myads-verification')) {
                    $verified = true;
                }
            }
        } catch (\Throwable $e) {
            // Http check failed or timed out
        }

        if ($verified) {
            $listing->update([
                'is_verified' => 1,
                'claimed_by' => $user->id,
            ]);

            DirectoryClaim::create([
                'directory_id' => $listing->id,
                'user_id' => $user->id,
                'verification_method' => 'meta_tag',
                'verification_token' => $token,
                'status' => 'approved',
                'notes' => 'Auto-verified via meta tag',
                'created_at' => time(),
                'updated_at' => time(),
            ]);

            return response()->json([
                'success' => true,
                'verified' => true,
                'message' => __('messages.site_verified_success') ?? 'تم توثيق ملكيتك للموقع بنجاح!',
            ]);
        }

        DirectoryClaim::updateOrCreate(
            ['directory_id' => $listing->id, 'user_id' => $user->id],
            [
                'verification_method' => 'meta_tag',
                'verification_token' => $token,
                'status' => 'pending',
                'created_at' => time(),
            ]
        );

        return response()->json([
            'success' => true,
            'verified' => false,
            'token' => $token,
            'meta_tag' => '<meta name="myads-verification" content="' . $token . '">',
            'message' => __('messages.site_verification_pending') ?? 'يرجى وضع كود الميتا في صفحة موقعك الرئيسية ثم إعادة المحاولة للتوثيق الفوري.',
        ]);
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
            'pricing_type' => 'nullable|string|in:free,freemium,paid,open_source,free_trial',
            'features' => 'nullable|string',
        ]);

        try {
            $slug = Str::slug($request->name) ?: ('site-' . time());
            if (Directory::where('slug', $slug)->exists()) {
                $slug .= '-' . Str::random(4);
            }

            $listing = Directory::create([
                'uid' => auth()->id(),
                'name' => $request->name,
                'slug' => $slug,
                'url' => $request->url,
                'cat' => $request->categ,
                'pricing_type' => $request->pricing_type ?: 'free',
                'features' => $request->features ?? '',
                'txt' => $request->txt ?? '',
                'metakeywords' => $request->tag ?? '',
                'date' => time(),
                'statu' => 1,
                'vu' => 0,
                'upvotes_count' => 0,
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
                ->withErrors(['error' => __('messages.error_occurred')])
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
            'pricing_type' => 'nullable|string|in:free,freemium,paid,open_source,free_trial',
            'features' => 'nullable|string',
        ]);

        $updateData = [
            'name' => $request->name,
            'url' => $request->url,
            'cat' => $request->categ,
            'txt' => $request->txt ?? '',
            'metakeywords' => $request->tag ?? '',
            'pricing_type' => $request->pricing_type ?: 'free',
            'features' => $request->features ?? '',
        ];

        if (empty($listing->slug)) {
            $updateData['slug'] = Str::slug($request->name) ?: ('site-' . $listing->id);
        }

        $listing->update($updateData);

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

            DirectoryUpvote::where('directory_id', $id)->delete();
            DirectoryClaim::where('directory_id', $id)->delete();

            $listing->delete();
        });

        return response()->json(['success' => true]);
    }

    private function buildDirectoryFeed(?int $categoryId = null, array $filters = []): array
    {
        $query = Status::visible()
            ->with('user')
            ->where('s_type', 1)
            ->where('date', '<', time());

        $hasFilters = $categoryId !== null || !empty($filters['search']) || !empty($filters['pricing']);
        $sort = $filters['sort'] ?? 'latest';

        if ($hasFilters || in_array($sort, ['trending', 'popular', 'featured'])) {
            $dirQuery = Directory::where('statu', 1);

            if ($categoryId !== null) {
                $subCategoryIds = DirectoryCategory::where('sub', $categoryId)->where('statu', 1)->pluck('id');
                $allCategoryIds = collect([$categoryId])->merge($subCategoryIds)->all();
                $dirQuery->whereIn('cat', $allCategoryIds);
            }

            if (!empty($filters['search'])) {
                $search = trim((string) $filters['search']);
                $dirQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('txt', 'like', "%{$search}%")
                      ->orWhere('metakeywords', 'like', "%{$search}%")
                      ->orWhere('url', 'like', "%{$search}%");
                });
            }

            if (!empty($filters['pricing']) && in_array($filters['pricing'], ['free', 'freemium', 'paid', 'open_source', 'free_trial'])) {
                $dirQuery->where('pricing_type', $filters['pricing']);
            }

            if ($sort === 'trending') {
                $dirQuery->orderBy('upvotes_count', 'desc')->orderBy('date', 'desc');
            } elseif ($sort === 'popular') {
                $dirQuery->orderBy('vu', 'desc')->orderBy('date', 'desc');
            } elseif ($sort === 'featured') {
                $dirQuery->orderBy('is_featured', 'desc')->orderBy('upvotes_count', 'desc')->orderBy('date', 'desc');
            } else {
                $dirQuery->orderBy('date', 'desc');
            }

            $directoryIds = $dirQuery->pluck('id');
            $query->whereIn('tp_id', $directoryIds->all());

            if (in_array($sort, ['trending', 'popular', 'featured']) && $directoryIds->isNotEmpty()) {
                $orderCases = [];
                $bindings = [];
                foreach ($directoryIds->values() as $index => $id) {
                    $orderCases[] = "WHEN ? THEN {$index}";
                    $bindings[] = $id;
                }
                $query->orderByRaw("CASE tp_id " . implode(' ', $orderCases) . " ELSE 999999 END", $bindings);
            } else {
                $query->orderBy('date', 'desc');
            }
        } else {
            $query->orderBy('date', 'desc');
        }

        $activities = $query->paginate(15);
        $directories = $this->attachDirectoryContent($activities);
        $metrics = $this->buildListingMetrics($directories->keys());

        $cards = collect();

        foreach ($activities as $activity) {
            $hasUpvoted = isset($metrics['upvotedIds'][$activity->tp_id]);
            $cards->push(DirectoryPresenter::presentFeedItem($activity, [
                'reactions_count' => (int) $metrics['reactionCounts']->get($activity->tp_id, 0),
                'comments_count' => (int) $metrics['commentCounts']->get($activity->tp_id, 0),
                'current_reaction' => $metrics['currentReactions']->get($activity->tp_id),
                'has_upvoted' => $hasUpvoted,
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
                'upvotedIds' => collect(),
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
        $upvotedIds = collect();

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

            $upvotedIds = DirectoryUpvote::where('user_id', Auth::id())
                ->whereIn('directory_id', $listingIds)
                ->pluck('directory_id')
                ->flip();
        }

        return [
            'reactionCounts' => $reactionCounts,
            'commentCounts' => $commentCounts,
            'currentReactions' => $currentReactions,
            'upvotedIds' => $upvotedIds,
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
            $preview = app(\App\Services\LinkPreviewService::class)->fetch($url);

            return response()->json([
                'title' => $preview['title'] ?? '',
                'description' => $preview['description'] ?? '',
                'tags' => $preview['keywords'] ?? ''
            ]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => __('messages.error_occurred')], 500);
        }
    }

    public function fetchImage($id)
    {
        $listing = Directory::findOrFail($id);
        
        $preview = app(\App\Services\LinkPreviewService::class)->fetch($listing->url);
        $newUrl = null;
        
        if (isset($preview['status_code']) && $preview['status_code'] >= 400) {
            $newUrl = theme_asset('img/dir_link.png');
        } else {
            $newUrl = $preview['image_url'] ?? null;
        }

        if ($newUrl !== $listing->prominent_image_url) {
            $listing->update(['prominent_image_url' => $newUrl]);
            cache()->put('directory_image_' . $listing->id, $newUrl, 86400);
        }

        return response()->json([
            'image_url' => $newUrl
        ]);
    }

    private function canManageListing(?Directory $listing): bool
    {
        if (!$listing || !Auth::check()) {
            return false;
        }

        return (int) Auth::id() === (int) $listing->uid || Auth::user()->isAdmin();
    }
}
