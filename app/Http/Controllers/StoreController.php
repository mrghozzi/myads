<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\Short;
use App\Models\Option;
use App\Models\User;
use App\Models\Status;
use App\Models\ForumTopic;
use App\Models\Emoji;
use App\Services\KnowledgebaseCommunityService;
use App\Support\StoreCategoryCatalog;
use App\Models\KbCategory;
use App\Models\ProductReview;
use App\Models\ProductMedia;

class StoreController extends Controller
{
    public function findProductByName(string $name): Product
    {
        $decoded = urldecode($name);
        return Product::withoutGlobalScope('store')
            ->where('o_type', 'store')
            ->where(function ($q) use ($name, $decoded) {
                $q->where('name', $name)->orWhere('name', $decoded);
            })
            ->firstOrFail();
    }

    public function index(Request $request, ?string $script = null, ?string $category = null)
    {
        $category = StoreCategoryCatalog::normalize($category ?? $request->query('category'));
        $scriptName = $script ?? $request->query('script');
        $scriptId = null;

        $search = trim((string) $request->query('q', $request->query('search')));
        $sort = (string) $request->query('sort', 'latest');

        // Resolve script name to product ID for filtering
        if ($scriptName && $scriptName !== 'all') {
            $scriptProduct = Product::withoutGlobalScope('store')
                ->where('o_type', 'store')
                ->where('name', $scriptName)
                ->first();
            
            if ($scriptProduct) {
                $scriptId = $scriptProduct->id;
            }
        }

        $query = Product::visible()
            ->with(['user', 'type', 'sale', 'files', 'media'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        // Search by product name or description
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('options.name', 'LIKE', "%{$search}%")
                  ->orWhere('options.o_valuer', 'LIKE', "%{$search}%");
            });
        }

        // Apply sorting
        if ($sort === 'downloads') {
            $query->orderByDesc(
                \App\Models\Short::selectRaw('COALESCE(SUM(clik), 0)')
                    ->where('sh_type', 7867)
                    ->whereIn('tp_id', function ($sub) {
                        $sub->select('id')->from('options as files_opt')
                            ->whereColumn('files_opt.o_parent', 'options.id')
                            ->where('files_opt.o_type', 'store_file');
                    })
            )->orderBy('id', 'desc');
        } elseif ($sort === 'price_asc') {
            $query->orderBy('o_order', 'asc')->orderBy('id', 'desc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('o_order', 'desc')->orderBy('id', 'desc');
        } elseif ($sort === 'free') {
            $query->where('o_order', 0)->orderBy('id', 'desc');
        } elseif ($sort === 'paid') {
            $query->where('o_order', '>', 0)->orderBy('id', 'desc');
        } elseif ($sort === 'sale') {
            $query->whereHas('sale', function ($s) {
                $s->where('is_active', true);
            })->orderBy('id', 'desc');
        } elseif ($sort === 'rating') {
            $query->orderByDesc('reviews_avg_rating')->orderBy('id', 'desc');
        } else {
            // Default: newest promotion or date
            $query->orderByDesc(
                \App\Models\Status::select('date')
                    ->whereColumn('tp_id', 'options.id')
                    ->where('s_type', 7867)
                    ->orderByDesc('date')
                    ->limit(1)
            )->orderBy('id', 'desc');
        }

        $categoryNames = StoreCategoryCatalog::namesForFilter($category);
        if ($category !== null && $categoryNames === []) {
            $category = null;
        }

        if ($categoryNames !== [] || $scriptId !== null) {
            $typeQuery = Option::where('o_type', 'store_type');
            
            if ($categoryNames !== []) {
                $typeQuery->whereIn('name', $categoryNames);
            }
            
            if ($scriptId !== null) {
                $typeQuery->where('o_mode', (string) $scriptId);
            }
            
            $productIds = $typeQuery->pluck('o_parent')->toArray();
            $query->whereIn('id', $productIds);
        }

        $products = $query->paginate(12)->appends([
            'category' => $category,
            'script' => $scriptName,
            'q' => $search !== '' ? $search : null,
            'sort' => $sort !== 'latest' ? $sort : null,
        ]);
        $user = Auth::user();

        // Count products per category (optionally filtered by script)
        $countQuery = function(array $names) use ($scriptId) {
            $q = Option::where('o_type', 'store_type')->whereIn('name', $names);
            if ($scriptId) {
                $q->where('o_mode', (string) $scriptId);
            }
            return $q->count();
        };

        $categoryCounts = [];
        foreach (StoreCategoryCatalog::selectable() as $catKey) {
            $categoryCounts[$catKey] = $countQuery(StoreCategoryCatalog::namesForFilter($catKey));
        }

        $this->seo([
            'scope_key' => 'store_index',
            'resource_title' => $scriptName && $scriptName !== 'all' ? __('messages.store') . ' - ' . $scriptName : __('messages.store'),
            'description' => __('messages.seo_store_description'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.store'), 'url' => route('store.index')],
            ],
        ]);
        
        if ($scriptName && $scriptName !== 'all') {
             app(\App\Services\SeoManager::class)->setContext([
                'breadcrumbs' => [
                    ['name' => __('messages.home'), 'url' => url('/')],
                    ['name' => __('messages.store'), 'url' => route('store.index')],
                    ['name' => $scriptName, 'url' => route('store.script_category', ['script' => $scriptName, 'category' => 'all'])],
                ],
            ]);
        }

        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->boolean('ajax')) {
            $viewMode = (string) $request->query('view', 'grid');
            $html = view('theme::store.partials.products_grid', compact('products', 'user', 'category', 'categoryCounts', 'scriptName', 'search', 'sort', 'viewMode'))->render();

            if ($request->query('partial') === 'html') {
                return response($html);
            }

            $pagination = $products->links()->render();
            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination,
                'total' => $products->total(),
                'count' => $products->count(),
                'has_pages' => $products->hasPages(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'category' => $category,
                'script' => $scriptName,
                'search' => $search,
                'sort' => $sort,
                'view' => $viewMode,
            ]);
        }

        return view('theme::store.index', compact('products', 'user', 'category', 'categoryCounts', 'scriptName', 'search', 'sort'));
    }

    public function destroy(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Missing ID'], 400);
            }

            abort(404);
        }

        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->find($id);
        if (!$product) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Not found'], 404);
            }

            abort(404);
        }

        if ($product->o_parent != auth()->id() && !auth()->user()->isAdmin()) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            abort(403);
        }

        DB::transaction(function () use ($product, $id) {
            // Delete related status
            Status::where('tp_id', $id)->where('s_type', 7867)->delete();
            
            // Delete related options (comments, files, type, reactions, etc.)
            Option::where('o_parent', $id)->whereIn('o_type', ['s_coment', 'store_file', 'store_type', 'data_reaction', 'hest_pts'])->delete();

            // Delete product
            $product->delete();
        });

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('store.index')->with('success', __('messages.product_deleted'));
    }

    public function show($name)
    {
        $product = $this->findProductByName($name);
        $status = Status::where('s_type', 7867)->where('tp_id', $product->id)->first();
        if ($status) {
            $status->related_content = $product;
        }
        $type = Option::where('o_type', 'store_type')->where('o_parent', $product->id)->first();
        $topic = null;
        if ($type && $type->o_order) {
            $topic = ForumTopic::find($type->o_order);
        }
        
        // Fallback: If no topic linked via o_order, try finding by name or related content
        if (!$topic) {
            $topic = ForumTopic::where('name', $product->name)->first();
        }
        
        $latestFile = ProductFile::where('o_parent', $product->id)->orderBy('id', 'desc')->first();
        $downloadHash = null;
        if ($latestFile) {
            $downloadHash = hash('crc32', $latestFile->o_mode . $latestFile->id);
        }
        $fileIds = ProductFile::where('o_parent', $product->id)->pluck('id');
        $downloadCount = 0;
        if ($fileIds->isNotEmpty()) {
            $downloadCount = Short::where('sh_type', 7867)->whereIn('tp_id', $fileIds)->sum('clik');
        }
        $files = ProductFile::where('o_parent', $product->id)->orderBy('id', 'desc')->get();
        $canManageProduct = Auth::check() && (Auth::id() == $product->o_parent || Auth::user()->isAdmin());

        // Check if suspended or pending review
        $isSuspended = (bool) $product->is_suspended;
        $isPending = (bool) $product->is_pending;
        if (($isSuspended || $isPending) && !$canManageProduct) {
            abort(403, $isPending ? __('messages.pending_approval') : __('messages.product_suspended_notice'));
        }

        // Fetch license if purchased
        $license = null;
        if (Auth::check()) {
            $license = DB::table('product_licenses')
                ->where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->first();

            // If the user is the owner/creator of the product, auto-generate a free license if they don't have one
            if (!$license && Auth::id() == $product->o_parent) {
                $licenseKey = 'ADSTN-' . implode('-', str_split(strtoupper(Str::random(16)), 4));
                DB::table('product_licenses')->insert([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'license_key' => $licenseKey,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $license = DB::table('product_licenses')
                    ->where('user_id', Auth::id())
                    ->where('product_id', $product->id)
                    ->first();
            }
        }

        $categoryName = $type ? $type->name : '';
        $categoryLabel = $categoryName ? (__('messages.' . $categoryName) ?? $categoryName) : '';
        $owner = $product->user;

        $rawDescription = trim(strip_tags((string) ($product->o_valuer ?: ($topic?->txt ?? ''))));
        $rawDescription = preg_replace('/\s+/u', ' ', $rawDescription) ?? '';
        $cleanDescription = Str::limit($rawDescription, 160, '...');
        if (empty($cleanDescription)) {
            $cleanDescription = __('messages.seo_store_description') ?? ($product->name . ' - ' . __('messages.store'));
        }

        $price = (float) ($product->has_active_sale ? $product->sale_price : $product->o_order);

        $keywords = array_filter([
            $product->name,
            $categoryLabel,
            $categoryName,
            __('messages.store'),
            __('messages.download'),
            'store',
            'download',
            'product',
        ]);

        $this->seo([
            'scope_key' => 'store_show',
            'content_type' => 'product',
            'content_id' => $product->id,
            'resource_title' => $product->name,
            'description' => $cleanDescription,
            'image' => $product->product_image,
            'lastmod' => $status?->date ?? $product->updated_at?->timestamp,
            'category_name' => $categoryLabel ?: $categoryName,
            'author_name' => $owner?->username,
            'author_url' => $owner ? route('profile.show', $owner->username) : null,
            'price' => $price,
            'price_currency' => 'PTS',
            'download_count' => $downloadCount,
            'keywords' => implode(', ', array_unique($keywords)),
            'breadcrumbs' => array_values(array_filter([
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.store'), 'url' => route('store.index')],
                $categoryLabel ? ['name' => $categoryLabel, 'url' => route('store.script_category', ['script' => 'all', 'category' => $categoryName])] : null,
                ['name' => $product->name, 'url' => route('store.show', $product->name)],
            ])),
        ]);

        $reviews = $product->reviews()->with('user')->get();
        $screenshots = $product->screenshots;
        $averageRating = $product->average_rating;
        $reviewsCount = $product->reviews_count;
        $ratingBreakdown = $product->rating_breakdown;
        $liveDemoUrl = $product->live_demo_url;
        $videoPreviewUrl = $product->video_preview_url;
        $hasReviewed = Auth::check() ? $product->reviews()->where('user_id', Auth::id())->exists() : false;
        $userReview = Auth::check() ? $product->reviews()->where('user_id', Auth::id())->first() : null;
        $isVerifiedBuyer = Auth::check() ? DB::table('product_licenses')->where('user_id', Auth::id())->where('product_id', $product->id)->exists() : false;

        return view('theme::store.show', compact('product', 'status', 'type', 'topic', 'latestFile', 'downloadHash', 'downloadCount', 'files', 'canManageProduct', 'isSuspended', 'isPending', 'license', 'reviews', 'screenshots', 'averageRating', 'reviewsCount', 'ratingBreakdown', 'liveDemoUrl', 'videoPreviewUrl', 'hasReviewed', 'userReview', 'isVerifiedBuyer'));
    }


    public function create()
    {
        $emojis = Emoji::all();

        return view('theme::store.create', array_merge(
            $this->buildStoreCategorySelectorViewData(old('cat_s'), old('sc_cat')),
            compact('emojis')
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:70', 'regex:/^[\p{L}\p{N}\s_\-\.]+$/u', 'unique:options,name,NULL,id,o_type,store'],
            'desc' => ['required', 'string', 'min:10', 'max:2400'],
            'vnbr' => ['required', 'string', 'min:2', 'max:12', 'regex:/^[-a-zA-Z0-9.]+$/'],
            'pts' => ['required', 'integer', 'min:0', 'max:999999'],
            'cat_s' => ['required', 'string', Rule::in(StoreCategoryCatalog::acceptedInputValues())],
            'sc_cat' => ['required', 'string'],
            'txt' => ['required', 'string', 'min:10'],
            'linkzip' => ['required', 'string'],
            'img' => ['required', 'string'],
            'sale_price' => ['nullable', 'integer', 'min:0', 'lt:pts'],
            'sale_start' => ['nullable', 'date'],
            'sale_end' => ['nullable', 'date', 'after_or_equal:sale_start'],
            'demo_url' => ['nullable', 'string', 'max:2048'],
            'video_url' => ['nullable', 'string', 'max:2048'],
            'screenshots' => ['nullable'],
        ]);

        $user = Auth::user();

        return DB::transaction(function () use ($request, $user) {
            $product = Product::create([
                'name' => $request->name,
                'o_valuer' => $request->desc,
                'o_type' => 'store',
                'o_parent' => $user->id,
                'o_order' => $request->pts,
                'o_mode' => $request->img,
            ]);

            $topic = ForumTopic::create([
                'uid' => $user->id,
                'name' => $request->name,
                'txt' => $request->txt,
                'cat' => 0,
                'statu' => 1,
            ]);

            Option::create([
                'name' => StoreCategoryCatalog::normalize($request->cat_s) ?? $request->cat_s,
                'o_valuer' => '',
                'o_type' => 'store_type',
                'o_parent' => $product->id,
                'o_order' => $topic->id,
                'o_mode' => $request->sc_cat,
            ]);

            $fileOption = ProductFile::create([
                'name' => $request->vnbr,
                'o_valuer' => $request->desc,
                'o_type' => 'store_file',
                'o_parent' => $product->id,
                'o_order' => 0,
                'o_mode' => $request->linkzip,
            ]);

            $hash = hash('crc32', $request->linkzip . $fileOption->id);

            Short::create([
                'uid' => $user->id,
                'url' => $request->linkzip,
                'sho' => $hash,
                'clik' => 0,
                'sh_type' => 7867,
                'tp_id' => $fileOption->id,
            ]);

            Status::create([
                'uid' => $user->id,
                'date' => time(),
                's_type' => 7867,
                'tp_id' => $product->id,
            ]);

            if ($request->filled('sale_price')) {
                \App\Models\StoreSale::create([
                    'product_id' => $product->id,
                    'sale_price' => $request->sale_price,
                    'start_date' => $request->sale_start,
                    'end_date' => $request->sale_end,
                ]);
            }

            // Save demo_url if provided
            if ($request->filled('demo_url')) {
                ProductMedia::create([
                    'product_id' => $product->id,
                    'media_type' => 'demo_url',
                    'url' => $request->input('demo_url'),
                    'sort_order' => 0,
                ]);
            }

            // Save video_url if provided
            if ($request->filled('video_url')) {
                ProductMedia::create([
                    'product_id' => $product->id,
                    'media_type' => 'video',
                    'url' => $request->input('video_url'),
                    'sort_order' => 0,
                ]);
            }

            // Save screenshots if provided
            $screenshots = $request->input('screenshots');
            if (is_string($screenshots)) {
                $screenshots = json_decode($screenshots, true) ?: array_filter(array_map('trim', explode(',', $screenshots)));
            }
            if (is_array($screenshots)) {
                foreach ($screenshots as $idx => $sUrl) {
                    if (!empty($sUrl) && is_string($sUrl)) {
                        ProductMedia::create([
                            'product_id' => $product->id,
                            'media_type' => 'screenshot',
                            'url' => $sUrl,
                            'sort_order' => $idx,
                        ]);
                    }
                }
            }

            app(\App\Services\GamificationService::class)->recordEvent($user->id, 'product_created');

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => route('store.show', $product->name),
                    'message' => __('product_added_successfully'),
                ]);
            }

            return redirect()->route('store.show', $product->name)->with('success', __('product_added_successfully'));
        });
    }


    public function edit($id)
    {
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->where('id', $id)->firstOrFail();
        return redirect()->route('store.update', $product->name);
    }

    public function download(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $user = Auth::user();

        // [v4.2.0] Block download if suspended
        $canManage = $user->id == $product->o_parent || $user->isAdmin();
        if ($product->is_suspended && !$canManage) {
            return redirect()->back()->with('error', __('messages.product_suspended_notice'));
        }

        if ($this->ensureLicense($user, $product)) {
            return $this->processDownload($product);
        }

        return redirect()->back()->with('error', __('not_enough_points'));
    }

    public function downloadByHash(Request $request, $hash)
    {
        $short = Short::where('sho', $hash)->where('sh_type', 7867)->firstOrFail();
        $fileOption = ProductFile::where('id', $short->tp_id)->firstOrFail();
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->where('id', $fileOption->o_parent)->firstOrFail();
        $user = Auth::user();

        // [v4.2.0] Block download if suspended
        $canManage = $user && ($user->id == $product->o_parent || $user->isAdmin());
        if ($product->is_suspended && !$canManage) {
            return redirect()->route('store.show', $product->name)->with('error', __('messages.product_suspended_notice'));
        }

        if ($product->o_order > 0 && !Auth::check()) {
            return redirect()->route('login');
        }

        if ($this->ensureLicense($user, $product)) {
            return $this->deliverShort($short);
        }

        return redirect()->back()->with('error', __('not_enough_points'));
    }

    /**
     * Ensure a product license exists for the user. Deducts points and generates key if necessary.
     */
    private function ensureLicense($user, $product)
    {
        if (!$user) {
            return true; // guest can download free files
        }

        if ($user->id == $product->o_parent) {
            // Auto-generate free license for the owner if not exists
            $alreadyPurchased = DB::table('product_licenses')
                ->where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->exists();
            if (!$alreadyPurchased) {
                $licenseKey = 'ADSTN-' . implode('-', str_split(strtoupper(Str::random(16)), 4));
                DB::table('product_licenses')->insert([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'license_key' => $licenseKey,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            return true; // owner doesn't need points deduction
        }

        $alreadyPurchased = DB::table('product_licenses')
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();

        if ($alreadyPurchased) {
            return true;
        }

        $price = $product->current_price;
        if ($price > 0 && $user->pts < $price) {
            return false;
        }

        DB::transaction(function () use ($user, $product, $price) {
            if ($price > 0) {
                $user->decrement('pts', $price);
                $seller = User::find($product->o_parent);
                if ($seller) {
                    $seller->increment('pts', $price);
                }
                Option::create([
                    'name' => 'Store',
                    'o_valuer' => "-$price",
                    'o_type' => 'hest_pts',
                    'o_parent' => $user->id,
                    'o_order' => $product->o_parent,
                    'o_mode' => time(),
                ]);
                if ($seller) {
                    Option::create([
                        'name' => 'Store',
                        'o_valuer' => "$price",
                        'o_type' => 'hest_pts',
                        'o_parent' => $seller->id,
                        'o_order' => $user->id,
                        'o_mode' => time(),
                    ]);
                }
            }

            // Generate license key: ADSTN-XXXX-XXXX-XXXX
            $licenseKey = 'ADSTN-' . implode('-', str_split(strtoupper(Str::random(16)), 4));
            DB::table('product_licenses')->insert([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'license_key' => $licenseKey,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return true;
    }

    public function update($name)
    {
        $product = $this->findProductByName($name);
        if (!Auth::check() || (Auth::id() != $product->o_parent && !Auth::user()->isAdmin())) {
            return redirect()->route('store.show', $product->name);
        }
        $files = ProductFile::where('o_parent', $product->id)->orderBy('id', 'desc')->get();
        $screenshots = $product->screenshots;
        $liveDemoUrl = $product->live_demo_url;
        $videoPreviewUrl = $product->video_preview_url;
        return view('theme::store.update', compact('product', 'files', 'screenshots', 'liveDemoUrl', 'videoPreviewUrl'));
    }

    /**
     * Update the main product topic body text (inline from /store/{name}).
     */
    public function updateTopic(Request $request, $name)
    {
        $product = $this->findProductByName($name);

        if (!Auth::check() || (Auth::id() != $product->o_parent && !Auth::user()->isAdmin())) {
            return response()->json(['success' => false, 'message' => __('messages.unauthorized')], 403);
        }

        $request->validate([
            'txt' => ['required', 'string', 'min:10'],
        ]);

        $type = Option::where('o_type', 'store_type')->where('o_parent', $product->id)->first();
        $topic = null;

        if ($type && $type->o_order) {
            $topic = ForumTopic::find($type->o_order);
        }

        if (!$topic) {
            $topic = ForumTopic::where('name', $product->name)->first();
        }

        if ($topic) {
            $topic->update(['txt' => $request->input('txt')]);
            if ($type && !$type->o_order) {
                $type->update(['o_order' => $topic->id]);
            }
        } else {
            $topic = ForumTopic::create([
                'uid' => $product->o_parent,
                'name' => $product->name,
                'txt' => $request->input('txt'),
                'cat' => 0,
                'statu' => 1,
            ]);
            if ($type) {
                $type->update(['o_order' => $topic->id]);
            }
        }

        $product->update(['o_valuer' => $request->input('txt')]);

        return response()->json(['success' => true, 'message' => __('messages.updated_successfully') ?? 'Updated successfully']);
    }

    /**
     * Update the main product details (o_valuer) (inline from /store/{name}).
     */
    public function updateDetails(Request $request, $name)
    {
        $product = $this->findProductByName($name);

        if (!Auth::check() || (Auth::id() != $product->o_parent && !Auth::user()->isAdmin())) {
            return response()->json(['success' => false, 'message' => __('messages.unauthorized')], 403);
        }

        $request->validate([
            'txt' => ['required', 'string', 'min:10'],
        ]);

        $product->update(['o_valuer' => $request->input('txt')]);

        return response()->json(['success' => true, 'message' => __('messages.updated_successfully') ?? 'Updated successfully']);
    }

    /**
     * Update product media (screenshots, live demo URL, video preview, and optional cover image)
     * without publishing a new product version release.
     */
    public function updateMedia(Request $request, $name)
    {
        $product = $this->findProductByName($name);
        if (!Auth::check() || (Auth::id() != $product->o_parent && !Auth::user()->isAdmin())) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => __('messages.unauthorized')], 403);
            }
            return redirect()->route('store.show', $product->name);
        }

        $request->validate([
            'demo_url'    => ['nullable', 'string', 'max:2048'],
            'video_url'   => ['nullable', 'string', 'max:2048'],
            'screenshots' => ['nullable'],
            'img'         => ['nullable', 'string', 'max:2048'],
        ]);

        // Optional: update cover image
        if ($request->filled('img')) {
            $product->update(['o_mode' => $request->input('img')]);
        }

        // Update live demo URL
        if ($request->exists('demo_url')) {
            ProductMedia::where('product_id', $product->id)->where('media_type', 'demo_url')->delete();
            if ($request->filled('demo_url')) {
                ProductMedia::create([
                    'product_id' => $product->id,
                    'media_type' => 'demo_url',
                    'url'        => $request->input('demo_url'),
                ]);
            }
        }

        // Update video preview URL
        if ($request->exists('video_url')) {
            ProductMedia::where('product_id', $product->id)->where('media_type', 'video')->delete();
            if ($request->filled('video_url')) {
                ProductMedia::create([
                    'product_id' => $product->id,
                    'media_type' => 'video',
                    'url'        => $request->input('video_url'),
                ]);
            }
        }

        // Update screenshots gallery
        if ($request->exists('screenshots') || $request->has('has_screenshots_section')) {
            $screenshots = $request->input('screenshots', []);
            if (is_string($screenshots)) {
                $screenshots = json_decode($screenshots, true) ?: array_filter(array_map('trim', explode(',', $screenshots)));
            }
            if (!is_array($screenshots)) {
                $screenshots = [];
            }

            ProductMedia::where('product_id', $product->id)->where('media_type', 'screenshot')->delete();
            foreach ($screenshots as $idx => $sUrl) {
                if (!empty($sUrl) && is_string($sUrl)) {
                    ProductMedia::create([
                        'product_id' => $product->id,
                        'media_type' => 'screenshot',
                        'url'        => trim($sUrl),
                        'sort_order' => $idx,
                    ]);
                }
            }
        }

        $msg = __('messages.media_updated_successfully') ?? 'تم تحديث الوسائط والمعاينة الحية بنجاح';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    public function storeUpdate(Request $request, $name)
    {
        $product = $this->findProductByName($name);
        if (!Auth::check() || (Auth::id() != $product->o_parent && !Auth::user()->isAdmin())) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => __('messages.unauthorized')], 403);
            }
            return redirect()->route('store.show', $product->name);
        }

        $request->validate([
            'vnbr'       => ['required', 'string', 'min:2', 'max:12', 'regex:/^[-a-zA-Z0-9.]+$/'],
            'desc'       => ['required', 'string', 'min:10', 'max:65535'],
            'linkzip'    => ['required', 'string'],
            'pts'        => ['nullable', 'integer', 'min:0', 'max:999999'],
            'img'        => ['nullable', 'string'],
            'sale_price' => ['nullable', 'integer', 'min:0', 'lt:pts'],
            'sale_start' => ['nullable', 'date'],
            'sale_end'   => ['nullable', 'date', 'after_or_equal:sale_start'],
            'demo_url'   => ['nullable', 'string', 'max:2048'],
            'video_url'  => ['nullable', 'string', 'max:2048'],
            'screenshots' => ['nullable'],
        ]);

        $fileOption = ProductFile::create([
            'name'     => $request->vnbr,
            'o_valuer' => $request->desc,
            'o_type'   => 'store_file',
            'o_parent' => $product->id,
            'o_order'  => 0,
            'o_mode'   => $request->linkzip,
        ]);

        $hash = hash('crc32', $request->linkzip . $fileOption->id);

        Short::create([
            'uid'     => Auth::id(),
            'url'     => $request->linkzip,
            'sho'     => $hash,
            'clik'    => 0,
            'sh_type' => 7867,
            'tp_id'   => $fileOption->id,
        ]);

        // Optional: update cover image
        if ($request->filled('img')) {
            $product->update(['o_mode' => $request->img]);
        }

        // Optional: update price
        if ($request->filled('pts')) {
            $product->update(['o_order' => (int) $request->pts]);
        }

        // Update sale information
        if ($request->filled('sale_price')) {
            \App\Models\StoreSale::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'sale_price' => $request->sale_price,
                    'start_date' => $request->sale_start,
                    'end_date'   => $request->sale_end,
                ]
            );
        } else {
            \App\Models\StoreSale::where('product_id', $product->id)->delete();
        }

        // Update demo URL
        if ($request->has('demo_url')) {
            ProductMedia::where('product_id', $product->id)->where('media_type', 'demo_url')->delete();
            if ($request->filled('demo_url')) {
                ProductMedia::create([
                    'product_id' => $product->id,
                    'media_type' => 'demo_url',
                    'url' => $request->input('demo_url'),
                ]);
            }
        }

        // Update video URL
        if ($request->has('video_url')) {
            ProductMedia::where('product_id', $product->id)->where('media_type', 'video')->delete();
            if ($request->filled('video_url')) {
                ProductMedia::create([
                    'product_id' => $product->id,
                    'media_type' => 'video',
                    'url' => $request->input('video_url'),
                ]);
            }
        }

        // Update screenshots
        if ($request->exists('screenshots') || $request->has('has_screenshots_section')) {
            $screenshots = $request->input('screenshots', []);
            if (is_string($screenshots)) {
                $screenshots = json_decode($screenshots, true) ?: array_filter(array_map('trim', explode(',', $screenshots)));
            }
            if (!is_array($screenshots)) {
                $screenshots = [];
            }
            ProductMedia::where('product_id', $product->id)->where('media_type', 'screenshot')->delete();
            foreach ($screenshots as $idx => $sUrl) {
                if (!empty($sUrl) && is_string($sUrl)) {
                    ProductMedia::create([
                        'product_id' => $product->id,
                        'media_type' => 'screenshot',
                        'url'        => trim($sUrl),
                        'sort_order' => $idx,
                    ]);
                }
            }
        }

        // [v4.2.0] Trigger community status update
        \App\Models\Status::create([
            'uid'    => Auth::id(),
            'date'   => time(),
            's_type' => 7867,
            'tp_id'  => $product->id,
            'txt'    => 'update',
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('store.show', $product->name),
                'message' => __('updated_successfully'),
            ]);
        }

        return redirect()->route('store.show', $product->name)->with('success', __('updated_successfully'));
    }

    public function updatePrice(Request $request, $name)
    {
        $product = $this->findProductByName($name);
        if (!Auth::check() || (Auth::id() != $product->o_parent && !Auth::user()->isAdmin())) {
            return redirect()->route('store.show', $product->name);
        }


        $request->validate([
            'pts'        => ['nullable', 'integer', 'min:0', 'max:999999'],
            'sale_price' => ['nullable', 'integer', 'min:0', 'lt:pts'],
            'sale_start' => ['nullable', 'date'],
            'sale_end'   => ['nullable', 'date', 'after_or_equal:sale_start'],
        ]);

        if ($request->filled('pts')) {
            $product->update(['o_order' => (int) $request->pts]);
        }

        if ($request->filled('sale_price')) {
            \App\Models\StoreSale::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'sale_price' => $request->sale_price,
                    'start_date' => $request->sale_start,
                    'end_date'   => $request->sale_end,
                ]
            );
        } else {
            \App\Models\StoreSale::where('product_id', $product->id)->delete();
        }

        return redirect()->back()->with('success', __('updated_successfully'));
    }

    public function uploadZip(Request $request)
    {
        if (!$request->hasFile('fzip')) {
            return response($this->renderZipUploadFragment(null, __('zipfile')));
        }

        $request->validate([
            'fzip' => 'required|file|max:102400',
        ]);

        $file = $request->file('fzip');
        $extension = $file->getClientOriginalExtension();
        if (strtolower($extension) !== 'zip') {
            return response($this->renderZipUploadFragment(null, __('zipfile')));
        }

        $filename = time() . '_' . Str::random(8) . '.' . $extension;
        $destinationPath = base_path('upload');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        $file->move($destinationPath, $filename);
        $relativePath = 'upload/' . $filename;
        $displayName = $file->getClientOriginalName();
        return response($this->renderZipUploadFragment($relativePath, $displayName));
    }

    private function renderZipUploadFragment(?string $relativePath, string $displayName): string
    {
        if (!$relativePath) {
            return '<div class="store-source-upload-result is-error" data-upload-error="1"><div><p class="store-source-upload-result__name">' . e($displayName) . '</p></div></div>';
        }

        return '<div class="store-source-upload-result" data-upload-path="' . e($relativePath) . '" data-upload-name="' . e($displayName) . '">' .
            '<img src="' . e(theme_asset('img/fzip.png')) . '" alt="' . e(__('messages.file')) . '">' .
            '<div><p class="store-source-upload-result__name">' . e($displayName) . '</p>' .
            '<p class="store-source-upload-result__meta">' . e($relativePath) . '</p></div></div>';
    }

    public function verifyName(Request $request)
    {
        $name = trim((string) $request->input('sname'));
        if ($name === '') {
            return response('');
        }

        $length = mb_strlen($name, 'UTF-8');
        if (!preg_match('/^[\p{L}\p{N}\s_\-\.]+$/u', $name)) {
            $msg = __('olanwas') ?? 'Invalid characters';
            if ($request->expectsJson() || ($request->ajax() && $request->wantsJson())) {
                return response()->json(['valid' => false, 'message' => $msg]);
            }
            return response("<div class=\"alert alert-danger\" role=\"alert\"><strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp;".$msg."</div><input type=\"hidden\" value=\"\" name=\"vname\">");
        }
        if ($length < 3 || $length > 70) {
            $msg = __('ttmbnlt') ?? 'Name length must be between 3 and 70 characters';
            if ($request->expectsJson() || ($request->ajax() && $request->wantsJson())) {
                return response()->json(['valid' => false, 'message' => $msg]);
            }
            return response("<div class=\"alert alert-danger\" role=\"alert\"><strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp;".$msg."</div><input type=\"hidden\" value=\"\" name=\"vname\">");
        }
        $exists = Option::where('o_type', 'store')->where('name', $name)->exists();
        if ($exists) {
            $msg = __('exists') ?? 'This product name is already taken';
            if ($request->expectsJson() || ($request->ajax() && $request->wantsJson())) {
                return response()->json(['valid' => false, 'message' => $msg]);
            }
            return response("<div class=\"alert alert-danger\" role=\"alert\"><strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp;".$msg."</div><input type=\"hidden\" value=\"\" name=\"vname\">");
        }

        if ($request->expectsJson() || ($request->ajax() && $request->wantsJson())) {
            return response()->json(['valid' => true]);
        }
        return response('<input type="hidden" value="1" name="vname">');
    }


    public function loadCategories(Request $request)
    {
        return response()->view(
            'theme::store.partials.category-selector',
            $this->buildStoreCategorySelectorViewData(
                $request->input('cat_s'),
                $request->input('sc_cat')
            )
        );
    }

    private function sanitizeArticleContent(string $content): string
    {
        // Remove dangerous script, iframe, object, embed, applet tags and their content
        $cleaned = preg_replace('/<\s*(script|style|iframe|object|embed|applet)[^>]*>.*?<\s*\/\s*\1\s*>/is', '', $content);
        $cleaned = preg_replace('/<\s*(script|style|iframe|object|embed|applet)[^>]*>/is', '', $cleaned);
        // Remove inline event handlers (onerror, onload, onclick, onmouseover, etc.)
        $cleaned = preg_replace('/\s*on[a-z]+\s*=\s*(["\'][^"\']*["\']|[^\s>]+)/i', '', $cleaned);
        // Remove javascript: and vbscript: pseudoprotocols
        $cleaned = preg_replace('/(href|src)\s*=\s*(["\']\s*(?:javascript|vbscript|data):[^"\']*["\'])/i', '$1="#"', $cleaned);

        return $cleaned;
    }

    public function knowledgebasePortal(Request $request)
    {
        $productNames = Option::where('o_type', 'knowledgebase')
            ->where('o_order', 0)
            ->distinct()
            ->pluck('o_mode')
            ->filter()
            ->values();

        $products = Product::whereIn('name', $productNames)
            ->with(['user', 'statusOptions'])
            ->get()
            ->filter(function ($product) {
                return !($product->is_suspended ?? false);
            })
            ->values();

        $articleCounts = Option::where('o_type', 'knowledgebase')
            ->where('o_order', 0)
            ->whereIn('o_mode', $productNames)
            ->selectRaw('o_mode, COUNT(*) as total')
            ->groupBy('o_mode')
            ->pluck('total', 'o_mode');

        $kbCategories = collect();
        try {
            $kbCategories = KbCategory::withCount('articles')->orderBy('sort_order')->orderBy('name')->get();
        } catch (\Throwable $e) {
            $kbCategories = collect();
        }

        $totalArticles = Option::where('o_type', 'knowledgebase')->where('o_order', 0)->count();
        $totalContributors = Option::where('o_type', 'knowledgebase')
            ->where('o_order', 0)
            ->where('o_parent', '>', 0)
            ->distinct('o_parent')
            ->count('o_parent');

        $searchQuery = trim((string) ($request->query('q') ?? $request->query('search')));
        $searchResults = collect();

        if ($searchQuery !== '') {
            $searchResults = Option::where('o_type', 'knowledgebase')
                ->where('o_order', 0)
                ->where(function ($q) use ($searchQuery) {
                    $q->where('name', 'like', "%{$searchQuery}%")
                      ->orWhere('o_valuer', 'like', "%{$searchQuery}%");
                })
                ->with('kbCategory')
                ->orderByDesc('id')
                ->paginate(12)
                ->withQueryString();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'total' => $searchResults->total(),
                    'articles' => $searchResults->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'product' => $item->o_mode,
                            'url' => route('kb.show', ['name' => $item->o_mode, 'article' => $item->name]),
                            'category' => $item->kbCategory ? $item->kbCategory->name : null,
                            'snippet' => Str::limit(strip_tags((string) $item->o_valuer), 130),
                            'updated_at' => $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->diffForHumans() : null,
                        ];
                    }),
                ]);
            }
        }

        $recentArticles = Option::where('o_type', 'knowledgebase')
            ->where('o_order', 0)
            ->with('kbCategory')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->take(6)
            ->get();

        $this->seo([
            'scope_key' => 'kb_portal',
            'content_type' => 'knowledgebase',
            'content_id' => 0,
            'resource_title' => __('messages.kb_portal_title'),
            'description' => __('messages.kb_portal_desc'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.store'), 'url' => route('store.index')],
                ['name' => __('messages.knowledgebase'), 'url' => route('kb.portal')],
            ],
        ]);

        return view('theme::store.knowledgebase_portal', compact(
            'products',
            'articleCounts',
            'kbCategories',
            'totalArticles',
            'totalContributors',
            'recentArticles',
            'searchQuery',
            'searchResults'
        ));
    }

    public function knowledgebaseSearch(Request $request, $name)
    {
        $product = $this->findKnowledgebaseProduct($name);
        $q = trim((string) $request->query('q'));

        if ($q === '') {
            return response()->json([
                'success' => true,
                'query' => '',
                'count' => 0,
                'articles' => [],
                'results' => [],
            ]);
        }

        $articles = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('o_order', 0)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('o_valuer', 'like', "%{$q}%");
            })
            ->with('kbCategory')
            ->orderByDesc('id')
            ->take(10)
            ->get()
            ->map(function ($item) use ($product) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'url' => route('kb.show', ['name' => $product->name, 'article' => $item->name]),
                    'category' => $item->kbCategory ? $item->kbCategory->name : null,
                    'snippet' => Str::limit(strip_tags((string) $item->o_valuer), 120),
                    'updated_at' => $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->diffForHumans() : null,
                ];
            });

        return response()->json([
            'success' => true,
            'query' => $q,
            'count' => $articles->count(),
            'articles' => $articles,
            'results' => $articles,
        ]);
    }

    public function knowledgebaseFeedback(Request $request, $name)
    {
        $request->validate([
            'article_id' => 'required|integer',
            'vote' => 'required|in:up,down',
        ]);

        $product = $this->findKnowledgebaseProduct($name);
        $articleId = (int) $request->input('article_id');
        $vote = $request->input('vote');
        $voterKey = Auth::check() ? 'u_' . Auth::id() : 'ip_' . substr(md5($request->ip() . $request->userAgent()), 0, 16);
        $orderVal = ($vote === 'up') ? 1 : -1;

        $feedback = Option::where('o_type', 'kb_feedback')
            ->where('o_mode', (string) $articleId)
            ->where('name', $voterKey)
            ->first();

        if ($feedback) {
            if ($feedback->o_order == $orderVal) {
                $upCount = Option::where('o_type', 'kb_feedback')->where('o_mode', (string) $articleId)->where('o_order', 1)->count();
                $downCount = Option::where('o_type', 'kb_feedback')->where('o_mode', (string) $articleId)->where('o_order', -1)->count();
                return response()->json([
                    'success' => true,
                    'already' => true,
                    'userVote' => $vote,
                    'helpful' => $upCount,
                    'unhelpful' => $downCount,
                    'up' => $upCount,
                    'down' => $downCount,
                    'message' => __('messages.kb_feedback_already'),
                ]);
            }
            $feedback->update(['o_order' => $orderVal]);
        } else {
            Option::create([
                'name' => $voterKey,
                'o_valuer' => $vote,
                'o_type' => 'kb_feedback',
                'o_parent' => Auth::id() ?? 0,
                'o_order' => $orderVal,
                'o_mode' => (string) $articleId,
            ]);
        }

        $upCount = Option::where('o_type', 'kb_feedback')->where('o_mode', (string) $articleId)->where('o_order', 1)->count();
        $downCount = Option::where('o_type', 'kb_feedback')->where('o_mode', (string) $articleId)->where('o_order', -1)->count();

        return response()->json([
            'success' => true,
            'userVote' => $vote,
            'helpful' => $upCount,
            'unhelpful' => $downCount,
            'up' => $upCount,
            'down' => $downCount,
            'message' => __('messages.kb_feedback_thanks'),
        ]);
    }

    public function knowledgebaseIndex(Request $request, $name)
    {
        $product = $this->findKnowledgebaseProduct($name);

        $schema = app(\App\Services\V420SchemaService::class);
        $hasCategoryCol = $schema->hasColumn('options', 'kb_category_id');
        $hasUpdatedAtCol = $schema->hasColumn('options', 'updated_at');

        // Safely check if kb_categories table exists and is queryable in MySQL engine
        $kbCategories = collect();
        $hasCategoryTable = false;
        try {
            $kbCategories = KbCategory::orderBy('sort_order')->orderBy('name')->get();
            $hasCategoryTable = true;
        } catch (\Throwable $e) {
            $kbCategories = collect();
            $hasCategoryTable = false;
        }

        $selectedCategory = $request->query('category');
        $searchQuery = trim((string) ($request->query('q') ?? $request->query('search')));

        $articlesQuery = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('o_order', 0);

        // Apply real keyword search if present
        if ($searchQuery !== '') {
            $articlesQuery->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                  ->orWhere('o_valuer', 'like', "%{$searchQuery}%");
            });
        }

        // Apply category filter safely
        if ($hasCategoryCol) {
            if ($selectedCategory === 'uncategorized') {
                $articlesQuery->whereNull('kb_category_id');
            } elseif ($selectedCategory && is_numeric($selectedCategory)) {
                $articlesQuery->where('kb_category_id', (int) $selectedCategory);
            }

            if ($hasCategoryTable) {
                $articlesQuery->with('kbCategory');
            }
        }

        if ($hasUpdatedAtCol) {
            $articlesQuery->orderByDesc('updated_at');
        }
        $articlesQuery->orderByDesc('id');

        try {
            $articles = $articlesQuery->paginate(15)->withQueryString();
        } catch (\Throwable $e) {
            // Safe fallback if eager loading or custom column ordering fails
            $articles = Option::where('o_type', 'knowledgebase')
                ->where('o_mode', $product->name)
                ->where('o_order', 0)
                ->orderByDesc('id')
                ->paginate(15)
                ->withQueryString();
        }

        $pendingCounts = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('o_order', 1)
            ->selectRaw('name, COUNT(*) as total')
            ->groupBy('name')
            ->pluck('total', 'name');

        $articleAuthorIds = $articles->pluck('o_parent')
            ->filter(fn ($id) => (int) $id > 0)
            ->unique()
            ->values();

        $articleAuthors = $articleAuthorIds->isEmpty()
            ? collect()
            : User::whereIn('id', $articleAuthorIds)->get()->keyBy('id');

        // AJAX response for fast live filtering without page reload
        if ($request->ajax() && !$request->filled('st')) {
            return response()->json([
                'success' => true,
                'total' => $articles->total(),
                'articles' => $articles->map(function ($item) use ($product, $articleAuthors) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'url' => route('kb.show', ['name' => $product->name, 'article' => $item->name]),
                        'category' => $item->kbCategory ? $item->kbCategory->name : null,
                        'snippet' => Str::limit(strip_tags((string) $item->o_valuer), 140),
                        'author' => ($item->o_parent > 0 && isset($articleAuthors[$item->o_parent])) ? $articleAuthors[$item->o_parent]->username : __('messages.guest'),
                        'updated_at' => $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->diffForHumans() : null,
                    ];
                }),
                'pagination' => (string) $articles->links('pagination::bootstrap-5'),
            ]);
        }

        $shellData = $this->buildKnowledgebaseShellData($product);
        $articleName = $request->query('st');

        $this->seo([
            'scope_key' => 'kb_index',
            'content_type' => 'product',
            'content_id' => $product->id,
            'resource_title' => __('messages.seo_kb_title', ['product' => $product->name]),
            'description' => Str::limit(strip_tags((string) $product->o_valuer), 170, '') ?: __('messages.seo_kb_description', ['product' => $product->name]),
            'image' => $product->product_image,
            'indexable' => !$request->filled('st'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.store'), 'url' => route('store.index')],
                ['name' => $product->name, 'url' => route('store.show', $product->name)],
                ['name' => __('messages.knowledgebase'), 'url' => route('kb.index', $product->name)],
            ],
        ]);

        if ($request->has('create')) {
            return $this->knowledgebaseCreate($request, $name);
        }

        if ($articleName) {
            $exists = Option::where('o_type', 'knowledgebase')
                ->where('o_mode', $product->name)
                ->where('name', $articleName)
                ->where('o_order', 0)
                ->exists();
            if ($exists) {
                return redirect()->route('kb.show', ['name' => $product->name, 'article' => $articleName]);
            }
            return view('theme::store.knowledgebase', [
                'product' => $product,
                'mode' => 'create',
                'articles' => $articles,
                'pendingCounts' => $pendingCounts,
                'articleAuthors' => $articleAuthors,
                'articleName' => $articleName,
                'editorText' => old('txt'),
                'kbCategories' => $kbCategories,
                'selectedCategory' => $selectedCategory,
                'searchQuery' => $searchQuery,
            ] + $shellData);
        }

        return view('theme::store.knowledgebase', [
            'product' => $product,
            'mode' => 'list',
            'articles' => $articles,
            'pendingCounts' => $pendingCounts,
            'articleAuthors' => $articleAuthors,
            'kbCategories' => $kbCategories,
            'selectedCategory' => $selectedCategory,
            'searchQuery' => $searchQuery,
        ] + $shellData);
    }

    public function knowledgebaseCreate(Request $request, $name)
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'))->with('error', __('messages.login_required') ?? 'Please login to add a topic.');
        }

        $product = $this->findKnowledgebaseProduct($name);

        $kbCategories = collect();
        try {
            $kbCategories = KbCategory::orderBy('sort_order')->orderBy('name')->get();
        } catch (\Throwable $e) {
            $kbCategories = collect();
        }

        $articles = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('o_order', 0)
            ->with('kbCategory')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $pendingCounts = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('o_order', 1)
            ->selectRaw('name, COUNT(*) as total')
            ->groupBy('name')
            ->pluck('total', 'name');

        $articleAuthors = User::whereIn('id', $articles->pluck('o_parent')->filter()->unique())
            ->get()
            ->keyBy('id');

        $shellData = $this->buildKnowledgebaseShellData($product);
        $articleName = $request->query('st') ?? old('name', '');

        $this->seo([
            'scope_key' => 'kb_create',
            'content_type' => 'product',
            'content_id' => $product->id,
            'resource_title' => __('messages.add') . ' ' . __('messages.topic') . ' - ' . $product->name,
            'description' => Str::limit(strip_tags((string) $product->o_valuer), 170, '') ?: __('messages.seo_kb_description', ['product' => $product->name]),
            'image' => $product->product_image,
            'indexable' => false,
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.store'), 'url' => route('store.index')],
                ['name' => $product->name, 'url' => route('store.show', $product->name)],
                ['name' => __('messages.knowledgebase'), 'url' => route('kb.index', $product->name)],
                ['name' => __('messages.add') . ' ' . __('messages.topic'), 'url' => route('kb.create', $product->name)],
            ],
        ]);

        return view('theme::store.knowledgebase', [
            'product' => $product,
            'mode' => 'create',
            'articles' => $articles,
            'pendingCounts' => $pendingCounts,
            'articleAuthors' => $articleAuthors,
            'articleName' => $articleName,
            'editorText' => old('txt'),
            'kbCategories' => $kbCategories,
            'selectedCategory' => null,
            'searchQuery' => '',
        ] + $shellData);
    }

    public function knowledgebaseShow($name, $article)
    {
        $product = $this->findKnowledgebaseProduct($name);
        $kbArticle = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('name', $article)
            ->where('o_order', 0)
            ->first();

        if (!$kbArticle) {
            return redirect()->route('kb.index', ['name' => $product->name, 'st' => $article]);
        }

        $pendingCount = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('name', $article)
            ->where('o_order', 1)
            ->count();
        $shellData = $this->buildKnowledgebaseShellData($product, $kbArticle);

        // Feedback stats
        $upVotes = Option::where('o_type', 'kb_feedback')->where('o_mode', (string) $kbArticle->id)->where('o_order', 1)->count();
        $downVotes = Option::where('o_type', 'kb_feedback')->where('o_mode', (string) $kbArticle->id)->where('o_order', -1)->count();
        $voterKey = Auth::check() ? 'u_' . Auth::id() : 'ip_' . substr(md5(request()->ip() . request()->userAgent()), 0, 16);
        $userVote = Option::where('o_type', 'kb_feedback')->where('o_mode', (string) $kbArticle->id)->where('name', $voterKey)->value('o_order');

        // Reading time calculation (average 200 words/min)
        $wordCount = str_word_count(strip_tags((string) $kbArticle->o_valuer));
        $readingTime = max(1, (int) ceil($wordCount / 200));

        // Sibling articles navigation
        $siblingArticles = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('o_order', 0)
            ->orderBy('id')
            ->get(['id', 'name']);
        $currentIndex = $siblingArticles->search(fn ($item) => $item->id === $kbArticle->id);
        $prevArticle = ($currentIndex !== false && $currentIndex > 0) ? $siblingArticles[$currentIndex - 1] : null;
        $nextArticle = ($currentIndex !== false && $currentIndex < $siblingArticles->count() - 1) ? $siblingArticles[$currentIndex + 1] : null;

        // Process WikiLinks: [[Target Article]] or [[Target Article|Label]]
        $safeContent = $this->sanitizeArticleContent((string) $kbArticle->o_valuer);
        $processedContent = preg_replace_callback('/\[\[([^\]|]+)(?:\|([^\]]+))?\]\]/', function ($matches) use ($product) {
            $targetArticle = trim($matches[1]);
            $label = isset($matches[2]) && trim($matches[2]) !== '' ? trim($matches[2]) : $targetArticle;
            $targetUrl = route('kb.show', ['name' => $product->name, 'article' => $targetArticle]);
            return '<a class="kb-wikilink" href="' . e($targetUrl) . '"><i class="fa fa-book me-1"></i>' . e($label) . '</a>';
        }, $safeContent);

        $this->seo([
            'scope_key' => 'kb_show',
            'content_type' => 'knowledgebase',
            'content_id' => $kbArticle->id,
            'resource_title' => $kbArticle->name,
            'description' => Str::limit(strip_tags((string) $kbArticle->o_valuer), 170, ''),
            'image' => $product->product_image,
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.store'), 'url' => route('store.index')],
                ['name' => $product->name, 'url' => route('store.show', $product->name)],
                ['name' => __('messages.knowledgebase'), 'url' => route('kb.index', $product->name)],
                ['name' => $kbArticle->name, 'url' => route('kb.show', ['name' => $product->name, 'article' => $kbArticle->name])],
            ],
        ]);

        return view('theme::store.knowledgebase', [
            'product' => $product,
            'mode' => 'show',
            'article' => $kbArticle,
            'pendingCount' => $pendingCount,
            'upVotes' => $upVotes,
            'downVotes' => $downVotes,
            'userVote' => $userVote,
            'readingTime' => $readingTime,
            'prevArticle' => $prevArticle,
            'nextArticle' => $nextArticle,
            'processedContent' => $processedContent,
        ] + $shellData);
    }

    public function knowledgebaseEdit($name, $article)
    {
        $product = $this->findKnowledgebaseProduct($name);
        $kbArticle = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('name', $article)
            ->where('o_order', 0)
            ->first();

        if (!$kbArticle) {
            return redirect()->route('kb.index', ['name' => $product->name, 'st' => $article]);
        }

        $shellData = $this->buildKnowledgebaseShellData($product, $kbArticle);
        try {
            $kbCategories = KbCategory::orderBy('sort_order')->orderBy('name')->get();
        } catch (\Throwable $e) {
            $kbCategories = collect();
        }

        return view('theme::store.knowledgebase', [
            'product' => $product,
            'mode' => 'edit',
            'article' => $kbArticle,
            'articleName' => $kbArticle->name,
            'editorText' => old('txt', $kbArticle->o_valuer),
            'kbCategories' => $kbCategories,
        ] + $shellData);
    }

    public function knowledgebasePending($name, $article)
    {
        $product = $this->findKnowledgebaseProduct($name);
        $kbArticle = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('name', $article)
            ->where('o_order', 0)
            ->firstOrFail();
        $entries = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('name', $article)
            ->where('o_order', 1)
            ->orderBy('id')
            ->get();
        $shellData = $this->buildKnowledgebaseShellData($product, $kbArticle);
        $isAuthorized = $shellData['canManageCurrentArticle'];

        return view('theme::store.knowledgebase', [
            'product' => $product,
            'mode' => 'pending',
            'article' => $kbArticle,
            'entries' => $entries,
            'isAuthorized' => $isAuthorized,
        ] + $shellData);
    }

    public function knowledgebaseHistory($name, $article)
    {
        $product = $this->findKnowledgebaseProduct($name);
        $kbArticle = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('name', $article)
            ->where('o_order', 0)
            ->firstOrFail();
        $entries = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('name', $article)
            ->where('o_order', 2)
            ->orderBy('id')
            ->get();
        $shellData = $this->buildKnowledgebaseShellData($product, $kbArticle);
        $isAuthorized = $shellData['canManageCurrentArticle'];

        return view('theme::store.knowledgebase', [
            'product' => $product,
            'mode' => 'history',
            'article' => $kbArticle,
            'entries' => $entries,
            'isAuthorized' => $isAuthorized,
        ] + $shellData);
    }

    private function findKnowledgebaseProduct(string $name): Product
    {
        $decodedName = rawurldecode($name);
        $spaceName = str_replace('-', ' ', $name);

        $product = Product::withoutGlobalScope('store')
            ->where('o_type', 'store')
            ->where(function ($q) use ($name, $decodedName, $spaceName) {
                $q->where('name', $name)
                  ->orWhere('name', $decodedName)
                  ->orWhere('name', $spaceName);
            })
            ->first();

        if (!$product) {
            abort(404);
        }

        $canManageProduct = Auth::check() && (Auth::id() == $product->o_parent || (Auth::user() && Auth::user()->isAdmin()));
        if ($product->is_suspended && !$canManageProduct) {
            abort(403, __('messages.product_suspended_notice'));
        }

        return $product;
    }

    public function knowledgebaseStore(Request $request)
    {
        $request->validate([
            'store' => 'required|string',
            'name' => 'nullable|string|max:150',
            'txt' => 'required|string|min:10',
            'capt' => 'required|string',
            'share_to_community' => 'nullable|boolean',
            'kb_category_id' => 'nullable|integer|exists:kb_categories,id',
        ]);

        $captcha = session('kb_captcha');
        if (!$captcha || (string) $request->input('capt') !== (string) $captcha) {
            return redirect()->back()->withInput()->with('kb_error', __('invalid'));
        }
        session()->forget('kb_captcha');

        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->where('name', $request->input('store'))->firstOrFail();
        $articleName = $request->input('name');
        if (!$articleName) {
            return redirect()->back()->withInput()->with('kb_error', __('please_enter_name'));
        }
        $userId = Auth::id() ?? 0;
        $shareToCommunity = Auth::check() && $request->boolean('share_to_community');
        $hasPublishedArticle = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('name', $articleName)
            ->where('o_order', 0)
            ->exists();
        $existing = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('name', $articleName)
            ->orderBy('id')
            ->first();
        $isOwner = Auth::check() && (Auth::id() == $product->o_parent || Auth::user()->isAdmin() || ($existing && Auth::id() == $existing->o_parent));
        $status = $existing && !$isOwner ? 1 : 0;

        $article = DB::transaction(function () use ($product, $articleName, $request, $status, $userId) {
            if ($status === 0) {
                Option::where('o_type', 'knowledgebase')
                    ->where('o_mode', $product->name)
                    ->where('name', $articleName)
                    ->where('o_order', 0)
                    ->update(['o_order' => 2]);
            }

            $safeTxt = $this->sanitizeArticleContent($request->input('txt'));
            $article = Option::create([
                'name' => $articleName,
                'o_valuer' => $safeTxt,
                'o_type' => 'knowledgebase',
                'o_parent' => $userId,
                'o_order' => $status,
                'o_mode' => $product->name,
                'kb_category_id' => $request->input('kb_category_id'),
            ]);

            // Track last modification date for knowledgebase ordering
            if ($status === 0) {
                $article->update(['updated_at' => now()]);
            }

            app(\App\Services\GamificationService::class)->recordEvent($userId, 'kb_article_created');

            return $article;
        });

        if ($shareToCommunity && !$hasPublishedArticle && $status === 0) {
            app(KnowledgebaseCommunityService::class)->publish($product, $article, Auth::user());

            return redirect()
                ->route('kb.show', ['name' => $product->name, 'article' => $articleName])
                ->with('success', __('messages.knowledgebase_published_to_community'));
        }

        return redirect()->route('kb.show', ['name' => $product->name, 'article' => $articleName]);
    }

    public function knowledgebasePublishToCommunity(Request $request)
    {
        $request->validate([
            'store' => 'required|string',
            'article' => 'required|string',
        ]);

        $product = Product::withoutGlobalScope('store')
            ->where('o_type', 'store')
            ->where('name', $request->input('store'))
            ->firstOrFail();

        $article = $this->findPublishedKnowledgebaseArticle($product, $request->input('article'));

        app(KnowledgebaseCommunityService::class)->publish($product, $article, $request->user());

        return redirect()
            ->route('kb.show', ['name' => $product->name, 'article' => $article->name])
            ->with('success', __('messages.knowledgebase_published_to_community'));
    }

    public function knowledgebaseDeleteCommunityPost(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $status = Status::query()
            ->where('id', $request->input('id'))
            ->where('s_type', KnowledgebaseCommunityService::STATUS_TYPE)
            ->firstOrFail();

        if ((int) $status->uid !== (int) Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['error' => __('messages.unauthorized')], 403);
        }

        app(KnowledgebaseCommunityService::class)->deletePublishedStatus($status);

        return response()->json(['success' => true]);
    }

    public function knowledgebaseApprove(Request $request)
    {
        $request->validate([
            'store' => 'required|string',
            'article' => 'required|string',
            'entry' => 'required|integer',
        ]);

        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->where('name', $request->input('store'))->firstOrFail();
        $kbArticle = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('name', $request->input('article'))
            ->where('o_order', 0)
            ->first();
        $entry = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('name', $request->input('article'))
            ->where('id', $request->input('entry'))
            ->firstOrFail();
        $isAuthorized = Auth::check() && (Auth::id() == $product->o_parent || Auth::user()->isAdmin() || ($kbArticle && Auth::id() == $kbArticle->o_parent));
        if (!$isAuthorized) {
            if ($request->ajax()) {
                return response()->json(['error' => __('messages.unauthorized')], 403);
            }
            return redirect()->route('kb.show', ['name' => $product->name, 'article' => $request->input('article')]);
        }

        DB::transaction(function () use ($product, $request, $entry) {
            Option::where('o_type', 'knowledgebase')
                ->where('o_mode', $product->name)
                ->where('name', $request->input('article'))
                ->where('o_order', 0)
                ->update(['o_order' => 2]);
            $entry->update(['o_order' => 0, 'updated_at' => now()]);
            // Non-destructive: mark other pending edits as rejected (3) rather than deleting
            Option::where('o_type', 'knowledgebase')
                ->where('o_mode', $product->name)
                ->where('name', $request->input('article'))
                ->where('o_order', 1)
                ->where('id', '!=', $entry->id)
                ->update(['o_order' => 3]);
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.kb_suggestion_approved'),
            ]);
        }

        return redirect()->route('kb.show', ['name' => $product->name, 'article' => $request->input('article')])
            ->with('success', __('messages.kb_suggestion_approved'));
    }

    public function knowledgebaseReject(Request $request)
    {
        $request->validate([
            'store' => 'required|string',
            'article' => 'required|string',
            'entry' => 'required|integer',
        ]);

        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->where('name', $request->input('store'))->firstOrFail();
        $entry = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('name', $request->input('article'))
            ->where('id', $request->input('entry'))
            ->firstOrFail();

        $kbArticle = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('name', $request->input('article'))
            ->where('o_order', 0)
            ->first();

        $isAuthorized = Auth::check() && (
            Auth::id() == $product->o_parent
            || Auth::user()->isAdmin()
            || ($kbArticle && Auth::id() == $kbArticle->o_parent)
        );

        if (!$isAuthorized) {
            if ($request->ajax()) {
                return response()->json(['error' => __('messages.unauthorized')], 403);
            }
            return redirect()->route('kb.show', ['name' => $product->name, 'article' => $request->input('article')]);
        }

        $entry->update(['o_order' => 3]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.kb_suggestion_rejected'),
            ]);
        }

        return redirect()->route('kb.show', ['name' => $product->name, 'article' => $request->input('article')])
            ->with('success', __('messages.kb_suggestion_rejected'));
    }

    public function knowledgebaseCaptcha()
    {
        $first = rand(1, 10);
        $second = rand(1, 10);
        session(['kb_captcha' => $first + $second]);
        $text = $first . ' + ' . $second . ' = ';

        if (ob_get_level()) ob_end_clean();

        if (function_exists('imagecreatetruecolor')) {
            $width = 100;
            $height = 30;
            $image = \imagecreatetruecolor($width, $height);
            $background_color = \imagecolorallocate($image, 255, 255, 255);
            $text_color = \imagecolorallocate($image, 0, 0, 0);
            
            \imagefilledrectangle($image, 0, 0, $width, $height, $background_color);
            \imagestring($image, 5, 20, 7, $text, $text_color);
            
            ob_start();
            \imagepng($image);
            $png = ob_get_clean();
            \imagedestroy($image);

            return response($png, 200)->header('Content-Type', 'image/png');
        }

        // Fallback to SVG if GD is not installed
        $svg = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg width="100" height="30" xmlns="http://www.w3.org/2000/svg">
    <rect width="100%" height="100%" fill="white"/>
    <text x="20" y="22" font-family="monospace" font-size="20" fill="black" font-weight="bold">'.$text.'</text>
</svg>';
        return response($svg, 200)->header('Content-Type', 'image/svg+xml');
    }

    private function buildKnowledgebaseShellData(Product $product, ?Option $article = null): array
    {
        return [
            'articleTotal' => Option::where('o_type', 'knowledgebase')
                ->where('o_mode', $product->name)
                ->where('o_order', 0)
                ->count(),
            'pendingTotal' => Option::where('o_type', 'knowledgebase')
                ->where('o_mode', $product->name)
                ->where('o_order', 1)
                ->count(),
            'articleAuthor' => $this->resolveKnowledgebaseAuthor($article),
            'canManageCurrentArticle' => $this->canManageKnowledgebase($product, $article),
            'knowledgebaseCommunityPublishAction' => $article ? route('kb.community.publish') : null,
            'knowledgebaseCommunityPublishPayload' => $article
                ? ['store' => $product->name, 'article' => $article->name]
                : null,
            'knowledgebaseExternalShareUrl' => $article
                ? route('kb.show', ['name' => $product->name, 'article' => $article->name])
                : null,
            'knowledgebaseExternalShareTitle' => $article
                ? trim($article->name . ' - ' . $product->name)
                : null,
        ];
    }

    private function resolveKnowledgebaseAuthor(?Option $article): ?User
    {
        if (!$article || (int) $article->o_parent <= 0) {
            return null;
        }

        return User::find($article->o_parent);
    }

    private function canManageKnowledgebase(Product $product, ?Option $article): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::id() == $product->o_parent
            || Auth::user()->isAdmin()
            || ($article && Auth::id() == $article->o_parent);
    }

    private function findPublishedKnowledgebaseArticle(Product $product, string $articleName): Option
    {
        return Option::query()
            ->where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('name', $articleName)
            ->where('o_order', 0)
            ->firstOrFail();
    }

    private function processDownload($product)
    {
        $fileOption = ProductFile::where('o_parent', $product->id)->orderBy('id', 'desc')->first();
        if (!$fileOption) abort(404, 'File not found');

        $short = Short::where('tp_id', $fileOption->id)->where('sh_type', 7867)->first();
        if (!$short) abort(404, 'Download link not found');

        return $this->deliverShort($short);
    }

    private function deliverShort($short)
    {
        $short->increment('clik');

        if (!filter_var($short->url, FILTER_VALIDATE_URL)) {
            $relativePath = ltrim($short->url, '/');
            $basePath = base_path($relativePath);
            if (file_exists($basePath)) {
                return response()->download($basePath);
            }
            $publicPath = public_path($relativePath);
            if (file_exists($publicPath)) {
                return response()->download($publicPath);
            }
            if (Storage::exists($short->url)) {
                return Storage::download($short->url);
            }
            abort(404, 'File missing from storage');
        }

        app(\App\Services\GamificationService::class)->recordEvent((int) Auth::id(), 'product_downloaded');

        return redirect($short->url);
    }

    private function storeCategoryOptions()
    {
        return Option::where('o_type', 'storecat')
            ->whereIn('name', StoreCategoryCatalog::selectable())
            ->orderBy('id')
            ->get();
    }

    private function buildStoreCategorySelectorViewData(?string $selectedCategory = null, ?string $selectedSubCategory = null): array
    {
        $selectedStoreCategory = StoreCategoryCatalog::normalize($selectedCategory);
        $selectedStoreSubcategory = trim((string) $selectedSubCategory);
        $selectedStoreSubcategory = $selectedStoreSubcategory !== '' ? $selectedStoreSubcategory : null;

        return [
            'storeCategories' => $this->storeCategoryOptions(),
            'selectedStoreCategory' => $selectedStoreCategory,
            'selectedStoreSubcategory' => $selectedStoreSubcategory,
            'scriptProductOptions' => $this->scriptProductOptions($selectedStoreCategory),
            'scriptCategoryOptions' => $this->scriptCategoryOptions($selectedStoreCategory),
            'genericCategoryOptions' => $this->genericCategoryOptions($selectedStoreCategory),
        ];
    }

    private function scriptProductOptions(?string $selectedCategory)
    {
        if ($selectedCategory !== StoreCategoryCatalog::PLUGINS && $selectedCategory !== StoreCategoryCatalog::THEMES) {
            return collect();
        }

        $scriptTypes = Option::where('o_type', 'store_type')
            ->where('name', StoreCategoryCatalog::SCRIPT)
            ->orderBy('id')
            ->get(['o_parent']);

        if ($scriptTypes->isEmpty()) {
            return collect();
        }

        $scriptProducts = Product::withoutGlobalScope('store')
            ->where('o_type', 'store')
            ->whereIn('id', $scriptTypes->pluck('o_parent')->unique()->values())
            ->pluck('name', 'id');

        return $scriptTypes
            ->map(function (Option $scriptType) use ($scriptProducts) {
                $label = $scriptProducts->get((int) $scriptType->o_parent);

                if (!is_string($label) || $label === '') {
                    return null;
                }

                return [
                    'value' => (string) $scriptType->o_parent,
                    'label' => $label,
                ];
            })
            ->filter()
            ->values();
    }

    private function scriptCategoryOptions(?string $selectedCategory)
    {
        if ($selectedCategory !== StoreCategoryCatalog::SCRIPT) {
            return collect();
        }

        return Option::where('o_type', 'scriptcat')
            ->orderBy('id')
            ->get(['name'])
            ->map(function (Option $scriptCategory) {
                return [
                    'value' => $scriptCategory->name,
                    'label' => $scriptCategory->name,
                ];
            });
    }

    private function genericCategoryOptions(?string $selectedCategory)
    {
        $genericCategories = [
            StoreCategoryCatalog::GRAPHICS,
            StoreCategoryCatalog::AUDIO,
            StoreCategoryCatalog::VIDEO,
            StoreCategoryCatalog::EBOOKS,
            StoreCategoryCatalog::SOFTWARE,
            StoreCategoryCatalog::COURSES,
        ];

        if (!in_array($selectedCategory, $genericCategories)) {
            return collect();
        }

        return Option::where('o_type', $selectedCategory . 'cat')
            ->orderBy('o_order')
            ->get(['name'])
            ->map(function (Option $cat) {
                return [
                    'value' => $cat->name,
                    'label' => $cat->name,
                ];
            });
    }

    /**
     * AJAX Purchase Product Flow
     */
    public function purchaseProduct(Request $request, $id)
    {
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->findOrFail($id);
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => __('messages.login_required') ?? 'Please log in to purchase.'
            ], 401);
        }

        $alreadyPurchased = DB::table('product_licenses')
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();

        $latestFile = ProductFile::where('o_parent', $product->id)->orderBy('id', 'desc')->first();
        if (!$latestFile) {
            return response()->json([
                'success' => false,
                'message' => __('messages.file_not_found') ?? 'No file version is available for this product.'
            ], 404);
        }

        $downloadHash = hash('crc32', $latestFile->o_mode . $latestFile->id);
        $downloadUrl = route('store.download.hash', $downloadHash);

        if ($alreadyPurchased || $user->id == $product->o_parent) {
            return response()->json([
                'success' => true,
                'download_url' => $downloadUrl
            ]);
        }

        $basePrice = $product->current_price;
        $discountCode = null;
        $discountAmount = 0;
        $finalPrice = $basePrice;

        if ($request->filled('code')) {
            $discountCode = \App\Models\StoreDiscountCode::where('code', strtoupper($request->code))->first();
            if (!$discountCode) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.coupon_not_found') ?? 'Invalid coupon code.'
                ], 422);
            }

            $validation = $discountCode->isValidFor($product, $user);
            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.' . $validation['error']) ?? 'Coupon is not valid.'
                ], 422);
            }

            $calc = $discountCode->calculateDiscount($basePrice);
            $discountAmount = $calc['discount_amount'];
            $finalPrice = $calc['final_price'];
        }

        if ($user->pts < $finalPrice) {
            return response()->json([
                'success' => false,
                'message' => __('messages.not_enough_points') ?? 'Insufficient points balance.'
            ], 422);
        }

        DB::transaction(function () use ($user, $product, $finalPrice, $discountCode, $discountAmount) {
            if ($finalPrice > 0) {
                $user->decrement('pts', $finalPrice);
                $seller = User::find($product->o_parent);
                if ($seller) {
                    $seller->increment('pts', $finalPrice);
                }

                Option::create([
                    'name' => 'Store',
                    'o_valuer' => "-$finalPrice",
                    'o_type' => 'hest_pts',
                    'o_parent' => $user->id,
                    'o_order' => $product->o_parent,
                    'o_mode' => time(),
                ]);

                if ($seller) {
                    Option::create([
                        'name' => 'Store',
                        'o_valuer' => "$finalPrice",
                        'o_type' => 'hest_pts',
                        'o_parent' => $seller->id,
                        'o_order' => $user->id,
                        'o_mode' => time(),
                    ]);
                }
            }

            if ($discountCode) {
                $discountCode->increment('uses');
                \App\Models\StoreDiscountRedemption::create([
                    'user_id' => $user->id,
                    'discount_code_id' => $discountCode->id,
                    'product_id' => $product->id,
                    'points_saved' => $discountAmount,
                ]);
            }

            $licenseKey = 'ADSTN-' . implode('-', str_split(strtoupper(Str::random(16)), 4));
            DB::table('product_licenses')->insert([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'license_key' => $licenseKey,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => __('messages.purchase_success') ?? 'Purchase successful!',
            'download_url' => $downloadUrl
        ]);
    }

    public function discountsIndex(Request $request)
    {
        $discounts = \App\Models\StoreDiscountCode::where('user_id', Auth::id())
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('theme::store.discounts.index', compact('discounts'));
    }

    public function discountsCreate()
    {
        $products = Product::withoutGlobalScope('store')
            ->where('o_type', 'store')
            ->where('o_parent', Auth::id())
            ->get();
        $discount = new \App\Models\StoreDiscountCode();

        return view('theme::store.discounts.form', compact('products', 'discount'));
    }

    public function discountsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:store_discount_codes,code',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'scope' => 'required|in:all_my_products,one_of_my_products',
            'product_id' => 'required_if:scope,one_of_my_products|nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_uses' => 'nullable|integer|min:1',
        ]);

        $applies_to = $request->scope === 'one_of_my_products' ? 'product' : 'all';
        $target_value = $request->scope === 'one_of_my_products' ? $request->product_id : null;

        if ($target_value) {
            $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->findOrFail($target_value);
            if ($product->o_parent != Auth::id()) {
                abort(403);
            }
        }

        \App\Models\StoreDiscountCode::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'applies_to' => $applies_to,
            'target_value' => $target_value,
            'user_id' => Auth::id(),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'max_uses' => $request->max_uses,
            'uses' => 0,
            'is_active' => true,
        ]);

        return redirect()->route('store.discounts.index')->with('success', __('messages.discount_created_successfully') ?? 'Coupon created successfully.');
    }

    public function discountsEdit($id)
    {
        $discount = \App\Models\StoreDiscountCode::where('user_id', Auth::id())->findOrFail($id);
        $products = Product::withoutGlobalScope('store')
            ->where('o_type', 'store')
            ->where('o_parent', Auth::id())
            ->get();

        return view('theme::store.discounts.form', compact('discount', 'products'));
    }

    public function discountsUpdate(Request $request, $id)
    {
        $discount = \App\Models\StoreDiscountCode::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:store_discount_codes,code,' . $discount->id,
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'scope' => 'required|in:all_my_products,one_of_my_products',
            'product_id' => 'required_if:scope,one_of_my_products|nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_uses' => 'nullable|integer|min:1',
        ]);

        $applies_to = $request->scope === 'one_of_my_products' ? 'product' : 'all';
        $target_value = $request->scope === 'one_of_my_products' ? $request->product_id : null;

        if ($target_value) {
            $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->findOrFail($target_value);
            if ($product->o_parent != Auth::id()) {
                abort(403);
            }
        }

        $discount->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'applies_to' => $applies_to,
            'target_value' => $target_value,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'max_uses' => $request->max_uses,
            'is_active' => $request->has('is_active') ? (bool) $request->is_active : $discount->is_active,
        ]);

        return redirect()->route('store.discounts.index')->with('success', __('messages.discount_updated_successfully') ?? 'Coupon updated successfully.');
    }

    public function discountsDestroy($id)
    {
        $discount = \App\Models\StoreDiscountCode::where('user_id', Auth::id())->findOrFail($id);
        $discount->delete();

        return redirect()->route('store.discounts.index')->with('success', __('messages.discount_deleted_successfully') ?? 'Coupon deleted successfully.');
    }

    public function validateDiscount(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'product_id' => 'required|integer',
        ]);

        $discountCode = \App\Models\StoreDiscountCode::where('code', strtoupper($request->code))->first();
        if (!$discountCode) {
            return response()->json([
                'success' => false,
                'message' => __('messages.coupon_not_found') ?? 'Invalid coupon code.'
            ]);
        }

        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->findOrFail($request->product_id);
        $user = Auth::user();

        $validation = $discountCode->isValidFor($product, $user);
        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => __('messages.' . $validation['error']) ?? $validation['error']
            ]);
        }

        $calc = $discountCode->calculateDiscount($product->current_price);
        $discountText = $discountCode->discount_type === 'percent' ? $discountCode->discount_value . '%' : $discountCode->discount_value . ' PTS';

        return response()->json([
            'success' => true,
            'discount_amount' => $calc['discount_amount'],
            'final_price' => $calc['final_price'],
            'discount_text' => $discountText
        ]);
    }

    public function downloads($name)
    {
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->where('name', $name)->firstOrFail();
        
        if (!Auth::check() || (Auth::id() != $product->o_parent && !Auth::user()->isAdmin())) {
            return redirect()->route('store.show', $product->name);
        }

        $licenses = DB::table('product_licenses')
            ->where('product_id', $product->id)
            ->join('users', 'product_licenses.user_id', '=', 'users.id')
            ->select('product_licenses.*', 'users.username', 'users.img as avatar')
            ->orderBy('product_licenses.created_at', 'desc')
            ->paginate(20);

        $this->seo([
            'resource_title' => __('downloads') . ' - ' . $product->name,
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.store'), 'url' => route('store.index')],
                ['name' => $product->name, 'url' => route('store.show', $product->name)],
                ['name' => __('downloads'), 'url' => route('store.downloads', $product->name)],
            ],
        ]);

        return view('theme::store.downloads', compact('product', 'licenses'));
    }

    public function updates($name)
    {
        $product = $this->findProductByName($name);
        
        if (!Auth::check() || (Auth::id() != $product->o_parent && !Auth::user()->isAdmin())) {
            return redirect()->route('store.show', $product->name);
        }

        $files = ProductFile::where('o_parent', $product->id)->orderBy('id', 'desc')->paginate(20);

        $this->seo([
            'resource_title' => __('updates') . ' - ' . $product->name,
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.store'), 'url' => route('store.index')],
                ['name' => $product->name, 'url' => route('store.show', $product->name)],
                ['name' => __('updates'), 'url' => route('store.updates', $product->name)],
            ],
        ]);

        return view('theme::store.updates', compact('product', 'files'));
    }

    public function destroyUpdate(Request $request, $name, $fileId)
    {
        $product = $this->findProductByName($name);
        
        if (!Auth::check() || (Auth::id() != $product->o_parent && !Auth::user()->isAdmin())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $file = ProductFile::where('o_parent', $product->id)->findOrFail($fileId);
        
        DB::transaction(function () use ($file) {
            Short::where('tp_id', $file->id)->where('sh_type', 7867)->delete();
            $file->delete();
        });

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('store.updates', $product->name)->with('success', __('messages.deleted_successfully') ?? 'Deleted successfully.');
    }

    /**
     * User Library / My Purchases page.
     */
    public function myPurchases(Request $request)
    {
        $user = Auth::user();

        $purchases = DB::table('product_licenses')
            ->where('product_licenses.user_id', $user->id)
            ->join('options as products', function ($join) {
                $join->on('product_licenses.product_id', '=', 'products.id')
                     ->where('products.o_type', '=', 'store');
            })
            ->leftJoin('users as sellers', 'products.o_parent', '=', 'sellers.id')
            ->select(
                'product_licenses.id as license_id',
                'product_licenses.license_key',
                'product_licenses.created_at as purchased_at',
                'products.id as product_id',
                'products.name as product_name',
                'products.o_valuer as product_desc',
                'products.o_mode as product_image',
                'products.o_order as product_price',
                'sellers.id as seller_id',
                'sellers.username as seller_username'
            )
            ->orderBy('product_licenses.created_at', 'desc')
            ->paginate(12);

        // Transform collection to attach latest file hash, version, and category
        $purchases->getCollection()->transform(function ($item) {
            $latestFile = ProductFile::where('o_parent', $item->product_id)->orderBy('id', 'desc')->first();
            $item->latest_version = $latestFile ? $latestFile->name : 'v1.0';
            $item->download_hash = $latestFile ? hash('crc32', $latestFile->o_mode . $latestFile->id) : null;

            $typeOption = Option::where('o_type', 'store_type')->where('o_parent', $item->product_id)->first();
            $item->category = $typeOption ? $typeOption->name : null;

            return $item;
        });

        $this->seo([
            'scope_key' => 'store_my_purchases',
            'resource_title' => __('messages.my_purchases') . ' - ' . __('messages.store'),
            'description' => __('messages.my_purchases_desc'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.store'), 'url' => route('store.index')],
                ['name' => __('messages.my_purchases'), 'url' => route('store.my_purchases')],
            ],
        ]);

        return view('theme::store.my_purchases', compact('purchases', 'user'));
    }

    /**
     * Submit or update a 5-star product review.
     */
    public function storeReview(Request $request, $id)
    {
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->findOrFail($id);
        $user = Auth::user();

        $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'title'   => ['nullable', 'string', 'max:150'],
            'comment' => ['required', 'string', 'min:3', 'max:3000'],
        ]);

        $isVerifiedBuyer = DB::table('product_licenses')
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();

        $review = ProductReview::updateOrCreate(
            ['product_id' => $product->id, 'user_id' => $user->id],
            [
                'rating'            => (int) $request->rating,
                'title'             => $request->title,
                'comment'           => $request->comment,
                'is_verified_buyer' => $isVerifiedBuyer,
            ]
        );

        $product->load('reviews.user');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'          => true,
                'message'          => __('messages.review_submitted_successfully') ?? 'Review submitted successfully.',
                'review'           => [
                    'id'                => $review->id,
                    'rating'            => $review->rating,
                    'title'             => $review->title,
                    'comment'           => $review->comment,
                    'is_verified_buyer' => (bool) $review->is_verified_buyer,
                    'created_at'        => $review->created_at ? $review->created_at->diffForHumans() : 'Just now',
                    'user'              => [
                        'id'       => $user->id,
                        'username' => $user->username,
                        'avatar'   => $user->avatarUrl(),
                    ],
                ],
                'average_rating'   => $product->average_rating,
                'reviews_count'    => $product->reviews_count,
                'rating_breakdown' => $product->rating_breakdown,
            ]);
        }

        return redirect()->back()->with('success', __('messages.review_submitted_successfully'));
    }

    /**
     * Delete a product review.
     */
    public function destroyReview(Request $request, $id)
    {
        $review = ProductReview::findOrFail($id);
        $user = Auth::user();

        if ($user->id != $review->user_id && !$user->isAdmin()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => __('messages.unauthorized')], 403);
            }
            abort(403);
        }

        $review->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => __('messages.deleted_successfully')]);
        }

        return redirect()->back()->with('success', __('messages.deleted_successfully'));
    }

    /**
     * Upload screenshot image asset via AJAX.
     */
    public function uploadScreenshot(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $filename = 'ss_' . time() . '_' . Str::random(8) . '.' . $extension;

        $destinationPath = base_path('upload/screenshots');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $file->move($destinationPath, $filename);
        $relativePath = 'upload/screenshots/' . $filename;

        return response()->json([
            'success'  => true,
            'url'      => $relativePath,
            'full_url' => asset($relativePath),
        ]);
    }

    /**
     * Add a media asset (screenshot, video, or demo URL) for a product.
     */
    public function storeMedia(Request $request, $name)
    {
        $product = $this->findProductByName($name);
        if (!Auth::check() || (Auth::id() != $product->o_parent && !Auth::user()->isAdmin())) {
            return response()->json(['error' => __('messages.unauthorized')], 403);
        }

        $request->validate([
            'media_type' => ['required', 'string', 'in:screenshot,video,demo_url'],
            'url'        => ['required', 'string', 'max:2048'],
            'caption'    => ['nullable', 'string', 'max:255'],
        ]);

        $media = ProductMedia::create([
            'product_id' => $product->id,
            'media_type' => $request->media_type,
            'url'        => $request->url,
            'caption'    => $request->caption,
            'sort_order' => (int) $request->input('sort_order', 0),
        ]);

        return response()->json(['success' => true, 'media' => $media]);
    }

    /**
     * Delete a media asset.
     */
    public function destroyMedia(Request $request, $name, $id)
    {
        $product = $this->findProductByName($name);
        if (!Auth::check() || (Auth::id() != $product->o_parent && !Auth::user()->isAdmin())) {
            return response()->json(['error' => __('messages.unauthorized')], 403);
        }

        $media = ProductMedia::where('product_id', $product->id)->findOrFail($id);
        $media->delete();

        return response()->json(['success' => true]);
    }
}

