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

class StoreController extends Controller
{
    public function index(Request $request, ?string $script = null, ?string $category = null)
    {
        $category = StoreCategoryCatalog::normalize($category ?? $request->query('category'));
        $scriptName = $script ?? $request->query('script');
        $scriptId = null;

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

        $query = Product::visible()->orderBy('id', 'desc');

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

        $products = $query->paginate(12)->appends(['category' => $category, 'script' => $scriptName]);
        $user = Auth::user();

        // Count products per category (optionally filtered by script)
        $countQuery = function(array $names) use ($scriptId) {
            $q = Option::where('o_type', 'store_type')->whereIn('name', $names);
            if ($scriptId) {
                $q->where('o_mode', (string) $scriptId);
            }
            return $q->count();
        };

        $categoryCounts = [
            'script' => $countQuery(['script']),
            'themes' => $countQuery(StoreCategoryCatalog::namesForFilter('themes')),
            'plugins' => $countQuery(['plugins']),
        ];

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

        return view('theme::store.index', compact('products', 'user', 'category', 'categoryCounts', 'scriptName'));
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
        $product = Product::visible()->withoutGlobalScope('store')->where('o_type', 'store')->where('name', $name)->firstOrFail();
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

        // [v4.2.0] Check if suspended
        $isSuspended = $product->is_suspended;
        if ($isSuspended && !$canManageProduct) {
            abort(403, __('messages.product_suspended_notice'));
        }

        $this->seo([
            'scope_key' => 'store_show',
            'content_type' => 'product',
            'content_id' => $product->id,
            'resource_title' => $product->name,
            'description' => Str::limit(strip_tags((string) $product->o_valuer), 170, ''),
            'image' => $product->product_image,
            'lastmod' => $status?->date,
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.store'), 'url' => route('store.index')],
                ['name' => $product->name, 'url' => route('store.show', $product->name)],
            ],
        ]);

        return view('theme::store.show', compact('product', 'status', 'type', 'topic', 'latestFile', 'downloadHash', 'downloadCount', 'files', 'canManageProduct', 'isSuspended'));
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
            'name' => ['required', 'string', 'min:3', 'max:35', 'regex:/^[-a-zA-Z0-9_]+$/', 'unique:options,name,NULL,id,o_type,store'],
            'desc' => ['required', 'string', 'min:10', 'max:2400'],
            'vnbr' => ['required', 'string', 'min:2', 'max:12', 'regex:/^[-a-zA-Z0-9.]+$/'],
            'pts' => ['required', 'integer', 'min:0', 'max:999999'],
            'cat_s' => ['required', 'string', Rule::in(StoreCategoryCatalog::acceptedInputValues())],
            'sc_cat' => ['required', 'string'],
            'txt' => ['required', 'string', 'min:10'],
            'linkzip' => ['required', 'string'],
            'img' => ['required', 'string'],
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

            app(\App\Services\GamificationService::class)->recordEvent($user->id, 'product_created');

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

        if ($user->id == $product->o_parent) {
            return $this->processDownload($product);
        }

        $price = $product->o_order;
        if ($price > 0) {
            if ($user->pts < $price) {
                return redirect()->back()->with('error', __('not_enough_points'));
            }

            DB::transaction(function () use ($user, $product, $price) {
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
            });
        }

        return $this->processDownload($product);
    }

    public function downloadByHash(Request $request, $hash)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $short = Short::where('sho', $hash)->where('sh_type', 7867)->firstOrFail();
        $fileOption = ProductFile::where('id', $short->tp_id)->firstOrFail();
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->where('id', $fileOption->o_parent)->firstOrFail();
        $user = Auth::user();

        // [v4.2.0] Block download if suspended
        $canManage = $user->id == $product->o_parent || $user->isAdmin();
        if ($product->is_suspended && !$canManage) {
            return redirect()->route('store.show', $product->name)->with('error', __('messages.product_suspended_notice'));
        }

        if ($user->id != $product->o_parent) {
            $price = $product->o_order;
            if ($price > 0) {
                if ($user->pts < $price) {
                    return redirect()->back()->with('error', __('not_enough_points'));
                }
                DB::transaction(function () use ($user, $product, $price) {
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
                });
            }
        }

        return $this->deliverShort($short);
    }

    public function update($name)
    {
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->where('name', $name)->firstOrFail();
        if (!Auth::check() || (Auth::id() != $product->o_parent && !Auth::user()->isAdmin())) {
            return redirect()->route('store.show', $product->name);
        }
        $files = ProductFile::where('o_parent', $product->id)->orderBy('id', 'desc')->get();
        return view('theme::store.update', compact('product', 'files'));
    }

    /**
     * Update the main product topic body text (inline from /store/{name}).
     */
    public function updateTopic(Request $request, $name)
    {
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->where('name', $name)->firstOrFail();

        if (!Auth::check() || (Auth::id() != $product->o_parent && !Auth::user()->isAdmin())) {
            return response()->json(['success' => false, 'message' => __('messages.unauthorized')], 403);
        }

        $request->validate([
            'txt' => ['required', 'string', 'min:10'],
        ]);

        $type = Option::where('o_type', 'store_type')->where('o_parent', $product->id)->first();

        if ($type && $type->o_order) {
            $topic = ForumTopic::find($type->o_order);
            if ($topic) {
                $topic->update(['txt' => $request->input('txt')]);
            }
        }

        return response()->json(['success' => true, 'message' => __('messages.updated_successfully') ?? 'Updated successfully']);
    }

    /**
     * Update the main product details (o_valuer) (inline from /store/{name}).
     */
    public function updateDetails(Request $request, $name)
    {
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->where('name', $name)->firstOrFail();

        if (!Auth::check() || (Auth::id() != $product->o_parent && !Auth::user()->isAdmin())) {
            return response()->json(['success' => false, 'message' => __('messages.unauthorized')], 403);
        }

        $request->validate([
            'txt' => ['required', 'string', 'min:10'],
        ]);

        $product->update(['o_valuer' => $request->input('txt')]);

        return response()->json(['success' => true, 'message' => __('messages.updated_successfully') ?? 'Updated successfully']);
    }

    public function storeUpdate(Request $request, $name)
    {
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->where('name', $name)->firstOrFail();
        if (!Auth::check() || (Auth::id() != $product->o_parent && !Auth::user()->isAdmin())) {
            return redirect()->route('store.show', $product->name);
        }

        $request->validate([
            'vnbr'    => ['required', 'string', 'min:2', 'max:12', 'regex:/^[-a-zA-Z0-9.]+$/'],
            'desc'    => ['required', 'string', 'min:10', 'max:2400'],
            'linkzip' => ['required', 'string'],
            'pts'     => ['nullable', 'integer', 'min:0', 'max:999999'],
            'img'     => ['nullable', 'string'],
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

        // [v4.2.0] Trigger community status update
        \App\Models\Status::create([
            'uid'    => Auth::id(),
            'date'   => time(),
            's_type' => 7867,
            'tp_id'  => $product->id,
            'txt'    => 'update',
        ]);

        return redirect()->route('store.show', $product->name)->with('success', __('updated_successfully'));
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
            mkdir($destinationPath, 0777, true);
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
        $name = $request->input('sname');
        if (!$name) {
            return response('');
        }

        $length = strlen($name);
        if (!preg_match('/^[-a-zA-Z0-9_]+$/', $name)) {
            return response("<div class=\"alert alert-danger\" role=\"alert\"><strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp;".__('olanwas')."</div><input type=\"text\" style=\"visibility:hidden\" value=\"\" name=\"vname\" required>");
        }
        if ($length < 3 || $length > 35) {
            return response("<div class=\"alert alert-danger\" role=\"alert\"><strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp;".__('ttmbnlt')."</div><input type=\"text\" style=\"visibility:hidden\" value=\"\" name=\"vname\" required>");
        }
        $exists = Option::where('o_type', 'store')->where('name', $name)->exists();
        if ($exists) {
            return response("<div class=\"alert alert-danger\" role=\"alert\"><strong><i class=\"fa fa-exclamation-triangle\" aria-hidden=\"true\"></i></strong>&nbsp;".__('exists')."</div><input type=\"text\" style=\"visibility:hidden\" value=\"\" name=\"vname\" required>");
        }

        return response('<input type="text" style="visibility:hidden" value="1" name="vname" required>');
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

    public function knowledgebaseIndex(Request $request, $name)
    {
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->where('name', $name)->firstOrFail();
        $articles = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('o_order', 0)
            ->orderBy('id')
            ->get();
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
            ] + $shellData);
        }

        return view('theme::store.knowledgebase', [
            'product' => $product,
            'mode' => 'list',
            'articles' => $articles,
            'pendingCounts' => $pendingCounts,
            'articleAuthors' => $articleAuthors,
        ] + $shellData);
    }

    public function knowledgebaseShow($name, $article)
    {
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->where('name', $name)->firstOrFail();
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
        ] + $shellData);
    }

    public function knowledgebaseEdit($name, $article)
    {
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->where('name', $name)->firstOrFail();
        $kbArticle = Option::where('o_type', 'knowledgebase')
            ->where('o_mode', $product->name)
            ->where('name', $article)
            ->where('o_order', 0)
            ->first();

        if (!$kbArticle) {
            return redirect()->route('kb.index', ['name' => $product->name, 'st' => $article]);
        }

        $shellData = $this->buildKnowledgebaseShellData($product, $kbArticle);

        return view('theme::store.knowledgebase', [
            'product' => $product,
            'mode' => 'edit',
            'article' => $kbArticle,
            'articleName' => $kbArticle->name,
            'editorText' => old('txt', $kbArticle->o_valuer),
        ] + $shellData);
    }

    public function knowledgebasePending($name, $article)
    {
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->where('name', $name)->firstOrFail();
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
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->where('name', $name)->firstOrFail();
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

    public function knowledgebaseStore(Request $request)
    {
        $request->validate([
            'store' => 'required|string',
            'name' => 'nullable|string|max:150',
            'txt' => 'required|string|min:10',
            'capt' => 'required|string',
            'share_to_community' => 'nullable|boolean',
        ]);

        $captcha = session('kb_captcha');
        if (!$captcha || $request->input('capt') != $captcha) {
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

            $article = Option::create([
                'name' => $articleName,
                'o_valuer' => $request->input('txt'),
                'o_type' => 'knowledgebase',
                'o_parent' => $userId,
                'o_order' => $status,
                'o_mode' => $product->name,
            ]);

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
            return redirect()->route('kb.show', ['name' => $product->name, 'article' => $request->input('article')]);
        }

        DB::transaction(function () use ($product, $request, $entry) {
            Option::where('o_type', 'knowledgebase')
                ->where('o_mode', $product->name)
                ->where('name', $request->input('article'))
                ->where('o_order', 0)
                ->update(['o_order' => 2]);
            $entry->update(['o_order' => 0]);
            Option::where('o_type', 'knowledgebase')
                ->where('o_mode', $product->name)
                ->where('name', $request->input('article'))
                ->where('o_order', 1)
                ->delete();
        });

        return redirect()->route('kb.show', ['name' => $product->name, 'article' => $request->input('article')]);
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

        app(\App\Services\GamificationService::class)->recordEvent(Auth::id(), 'product_downloaded');

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
}
