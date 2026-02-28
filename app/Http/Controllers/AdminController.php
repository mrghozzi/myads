<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Setting;
use App\Models\State;
use App\Models\Notification;
use App\Models\ForumTopic;
use App\Models\Directory;
use App\Models\Product;

use App\Models\Banner;
use App\Models\Link;
use App\Models\Visit;
use App\Models\Status;
use App\Models\ForumCategory;
use App\Models\DirectoryCategory;
use App\Models\News;
use App\Models\Ad;
use App\Models\Report;
use App\Models\Emoji;
use App\Models\Menu;
use App\Models\Option;
use App\Models\Knowledgebase;
use App\Services\PluginManager;
use App\Services\ThemeManager;

class AdminController extends Controller
{
    public function index()
    {
        // Version Check
        $currentVersion = \App\Http\Controllers\AdminUpdatesController::CURRENT_VERSION;
        
        // Sync version in DB (optional but good for consistency)
        try {
            $versionParts = explode('.', $currentVersion);
            $versionName = implode('-', $versionParts);
            $dbVersion = Option::where('o_type', 'version')->first();
            if ($dbVersion && $dbVersion->o_valuer != $currentVersion) {
                $dbVersion->update(['o_valuer' => $currentVersion, 'name' => $versionName]);
            }
        } catch (\Exception $e) {}

        $latestVersion = Cache::remember('latest_version', 3600, function () {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'MyAds-Updater/1.0',
                    'Accept'     => 'application/vnd.github.v3+json',
                ])->timeout(5)->get('https://api.github.com/repos/mrghozzi/myads/releases/latest');
                
                if ($response->successful()) {
                    $data = $response->json();
                    return ltrim($data['tag_name'] ?? '', 'v');
                }
            } catch (\Exception $e) {
                return null;
            }
            return null;
        });

        // Stats
        $stats = [
            'users' => User::count(),
            'users_online' => User::where('online', '>', time() - 240)->count(),
            'posts' => Status::count(),
            'topics' => ForumTopic::count(),
            'listings' => Directory::count(),
            'products' => Product::withoutGlobalScope('store')->where('o_type', 'store')->count(),
            'banners' => [
                'total' => Banner::count(),
                'views' => Banner::sum('vu'),
                'clicks' => Banner::sum('clik'),
            ],
            'links' => [
                'total' => Link::count(),
                'clicks' => Link::sum('clik'),
                // Assuming views might not be tracked separately for links or stored in clik
                'views' => 0, // Placeholder if column doesn't exist
            ],
            'visits' => [
                'total' => Visit::count(),
            ],
            'reactions' => [
                'total' => \App\Models\Like::count(),
            ],
            'followers' => \App\Models\Like::where('type', 1)->count(),
            'reports' => [
                'pending' => Report::where('statu', 1)->count(),
            ],
            'last_user' => User::orderBy('id', 'desc')->first(),
            'last_post' => Status::with('user')->orderBy('id', 'desc')->first(),
        ];

        // Chart Data for Dashboard
        $chartData = [
            'distribution' => [
                'labels' => [
                    __('messages.bannads'),
                    __('messages.textads'),
                    __('messages.exvisit'),
                ],
                'data' => [
                    $stats['banners']['total'],
                    $stats['links']['total'],
                    $stats['visits']['total'],
                ],
            ],
            'engagement' => [
                'labels' => [
                    __('messages.bannads') . ' ' . __('messages.Views'),
                    __('messages.bannads') . ' ' . __('messages.clicks'),
                    __('messages.textads') . ' ' . __('messages.clicks'),
                ],
                'data' => [
                    $stats['banners']['views'],
                    $stats['banners']['clicks'],
                    $stats['links']['clicks'],
                ],
            ],
        ];
        
        return view('theme::admin.index', compact('stats', 'currentVersion', 'latestVersion', 'chartData'));
    }

    public function settings()
    {
        $settings = Setting::firstOrFail();
        return view('theme::admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $settings = Setting::firstOrFail();
        
        $request->validate([
            'titer' => 'required|string',
            'url' => 'required|url',
        ]);

        $settings->update($request->all());

        return redirect()->route('admin.settings')->with('success', __('settings_updated'));
    }

    public function systemSettings()
    {
        return view('theme::admin.system_settings');
    }

    public function updateSystemSettings(Request $request)
    {
        $data = $request->only([
            'FACEBOOK_CLIENT_ID', 'FACEBOOK_CLIENT_SECRET',
            'GOOGLE_CLIENT_ID', 'GOOGLE_CLIENT_SECRET',
            'MAIL_HOST', 'MAIL_PORT', 'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_ENCRYPTION', 'MAIL_FROM_ADDRESS'
        ]);

        $this->writeEnv($data);

        // Clear config cache to apply changes
        try {
            \Illuminate\Support\Facades\Artisan::call('config:clear');
        } catch (\Exception $e) {
            // May fail on some shared hosts
        }

        return redirect()->route('admin.settings.system')->with('success', __('System settings updated successfully and .env file updated.'));
    }

    public function cookieNoticeSettings()
    {
        // Load cookie notice settings from options table (o_type = 'cookie_notice')
        $options = Option::where('o_type', 'cookie_notice')->get()->keyBy('name');
        
        $cookieSettings = [
            'enabled' => $options->has('enabled') ? $options['enabled']->o_valuer : '0',
            'position' => $options->has('position') ? $options['position']->o_valuer : 'bottom',
            'bg_color' => $options->has('bg_color') ? $options['bg_color']->o_valuer : '#1a1a2e',
            'text_color' => $options->has('text_color') ? $options['text_color']->o_valuer : '#ffffff',
            'btn_bg' => $options->has('btn_bg') ? $options['btn_bg']->o_valuer : '#615dfa',
            'btn_text' => $options->has('btn_text') ? $options['btn_text']->o_valuer : '#ffffff',
        ];

        return view('theme::admin.cookie_notice', compact('cookieSettings'));
    }

    public function updateCookieNoticeSettings(Request $request)
    {
        $request->validate([
            'enabled' => 'nullable|in:1',
            'position' => 'required|in:bottom,top,bottom_left,bottom_right',
            'bg_color' => 'required|string',
            'text_color' => 'required|string',
            'btn_bg' => 'required|string',
            'btn_text' => 'required|string',
        ]);

        $settings = [
            'enabled' => $request->has('enabled') ? '1' : '0',
            'position' => $request->position,
            'bg_color' => $request->bg_color,
            'text_color' => $request->text_color,
            'btn_bg' => $request->btn_bg,
            'btn_text' => $request->btn_text,
        ];

        foreach ($settings as $key => $value) {
            Option::updateOrCreate(
                ['o_type' => 'cookie_notice', 'name' => $key],
                ['o_valuer' => $value]
            );
        }

        return redirect()->back()->with('success', __('messages.cookie_settings_saved'));
    }

    /**
     * Write key-value pairs to the .env file.
     */
    private function writeEnv($data = [])
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $env = file_get_contents($path);
        } else {
            // Copy from .env.example if .env doesn't exist
            $examplePath = base_path('.env.example');
            if (file_exists($examplePath)) {
                copy($examplePath, $path);
                $env = file_get_contents($path);
            } else {
                $env = '';
            }
        }

        foreach ($data as $key => $value) {
            $value = $value ?? '';
            // Escape value if it contains special characters
            if ($value !== '' && (strpos($value, ' ') !== false || strpos($value, '#') !== false || strpos($value, '=') !== false)) {
                $value = '"' . $value . '"';
            }

            // Check if key exists
            if (preg_match("/^" . preg_quote($key, '/') . "=/m", $env)) {
                $env = preg_replace("/^" . preg_quote($key, '/') . "=.*/m", "{$key}={$value}", $env);
            } else {
                $env .= "\n{$key}={$value}";
            }
        }

        file_put_contents($path, $env);
    }

    public function users(Request $request)
    {
        $query = User::orderBy('id', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            // Filter by Role (Admin or Member)
            if ($request->role == 'admin') {
                $query->where('ucheck', 1);
            } elseif ($request->role == 'member') {
                $query->where('ucheck', '!=', 1);
            }
        }

        if ($request->has('online')) {
            // Filter by Online Status (Active in last 240 seconds)
            if ($request->online == '1') {
                $query->where('online', '>', time() - 240);
            } elseif ($request->online == '0') {
                $query->where('online', '<=', time() - 240);
            }
        }

        if ($request->has('verified')) {
            // Filter by Email Verification Status
            if ($request->verified == '1') {
                $query->where('ucheck', 1);
            } elseif ($request->verified == '0') {
                $query->where('ucheck', '!=', 1);
            }
        }

        $users = $query->paginate(20);
        return view('theme::admin.users', compact('users'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        
        // Fetch slug from options table
        $slugOption = Option::where('o_type', 'user')
                            ->where('o_order', $id)
                            ->first();
        $slug = $slugOption ? $slugOption->o_valuer : '';

        return view('theme::admin.user_edit', compact('user', 'slug'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'slug' => 'required|string|max:255',
            'ucheck' => 'required|in:0,1',
            'pts' => 'required|integer',
            'vu' => 'required|integer',
            'nvu' => 'required|integer',
            'nlink' => 'required|integer',
        ]);

        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'ucheck' => $request->ucheck,
            'pts' => $request->pts,
            'vu' => $request->vu,
            'nvu' => $request->nvu,
            'nlink' => $request->nlink,
        ]);

        // Update Slug
        $slug = Str::slug($request->slug);
        Option::updateOrCreate(
            ['o_type' => 'user', 'o_order' => $id],
            [
                'name' => $request->username,
                'o_valuer' => $slug
            ]
        );

        return redirect()->back()->with('success', __('User updated successfully'));
    }

    public function updateUserPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $user->update([
            'pass' => Hash::make($request->password)
        ]);

        return redirect()->back()->with('success', __('Password updated successfully'));
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return redirect()->route('admin.users')->with('success', __('User deleted successfully'));
    }

    public function banners(Request $request)
    {
        $query = Banner::with('user')->orderBy('id', 'desc');
        
        if ($request->has('user_id')) {
            $query->where('uid', $request->user_id);
        }

        $banners = $query->paginate(20);
        return view('theme::admin.banners', compact('banners'));
    }

    public function editBanner($id)
    {
        $banner = Banner::findOrFail($id);
        return view('theme::admin.banner_edit', compact('banner'));
    }

    public function updateBanner(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);
        $oldStatus = $banner->statu;
        
        $request->validate([
            'name' => 'required|string',
            'url' => 'required|url',
            'img' => 'required|string',
            'px' => 'required|integer',
            'statu' => 'required|in:1,2',
        ]);

        $banner->update([
            'name' => $request->name,
            'url' => $request->url,
            'img' => $request->img,
            'px' => $request->px,
            'statu' => $request->statu,
        ]);

        // Notification if status changed
        if ($oldStatus != $request->statu) {
            $nurl = "b_edit?id=" . $id;
            $name = ($request->statu == 1) ? __('your_ad_has_been_activated') : __('your_ad_as_been_blocked');
            
            Notification::create([
                'uid' => $banner->uid,
                'name' => $name,
                'nurl' => $nurl,
                'logo' => 'overview',
                'time' => time(),
                'state' => 1
            ]);
        }

        return redirect()->route('admin.banners')->with('success', __('banner_updated'));
    }

    public function deleteBanner($id)
    {
        $banner = Banner::findOrFail($id);
        
        // Notification
        $nurl = "b_list";
        Notification::create([
            'uid' => $banner->uid,
            'name' => __('your_ad_has_been_deleted'),
            'nurl' => $nurl,
            'logo' => 'delete',
            'time' => time(),
            'state' => 1
        ]);

        $banner->delete();
        
        return redirect()->route('admin.banners')->with('success', __('banner_deleted'));
    }

    public function stats(Request $request)
    {
        $query = State::orderBy('id', 'desc');
        $type = $request->ty ?? 'banner';
        $title = '';

        if ($type == 'banner') {
            $title = __('bannads') . ' ' . __('hits');
            $query->where('t_name', 'banner');
        } elseif ($type == 'vu') { // 'vu' in state table means clicks for banners in old system context (weird naming)
             // Let's verify with state.php logic
             // state.php: if ty=vu -> bannads hits.
             // b_list.php: link to ty=vu for clicks.
             // So ty=vu means clicks for banners.
             // But wait, state.php line 40: t_name='{$ty2}'. ty2 is $_GET['ty'].
             // So if url is ?ty=vu, then t_name='vu'.
             $title = __('bannads') . ' ' . __('hits');
             $query->where('t_name', 'vu');
        } elseif ($type == 'link') {
            $title = __('textads');
            $query->where('t_name', 'link');
        } elseif ($type == 'clik') {
            $title = __('textads') . ' ' . __('hits');
            $query->where('t_name', 'clik');
        }

        if ($request->has('id')) {
            $query->where('pid', $request->id);
        } elseif ($request->has('st')) {
            $query->where('sid', $request->st);
        }

        $stats = $query->paginate(20);
        return view('theme::admin.stats', compact('stats', 'title'));
    }

    public function links(Request $request)
    {
        $query = Link::with('user')->orderBy('id', 'desc');
        
        if ($request->has('user_id')) {
            $query->where('uid', $request->user_id);
        }

        $links = $query->paginate(20);
        return view('theme::admin.links', compact('links'));
    }

    public function updateLink(Request $request, $id)
    {
        $link = Link::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string',
            'url' => 'required|url',
            'statu' => 'required|in:1,2',
        ]);

        $link->update($request->all());

        return redirect()->back()->with('success', __('link_updated'));
    }

    public function deleteLink($id)
    {
        $link = Link::findOrFail($id);
        $link->delete();
        
        return redirect()->back()->with('success', __('link_deleted'));
    }

    public function visits()
    {
        $visits = Visit::with('user')->orderBy('id', 'desc')->paginate(20);
        return view('theme::admin.visits', compact('visits'));
    }

    public function updateVisit(Request $request, $id)
    {
        $visit = Visit::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string',
            'url' => 'required|url',
            'tims' => 'required|in:1,2,3,4',
            'statu' => 'required|in:1,2',
        ]);

        $visit->update($request->all());

        return redirect()->back()->with('success', __('visit_updated'));
    }

    public function deleteVisit($id)
    {
        $visit = Visit::findOrFail($id);
        $visit->delete();
        
        return redirect()->back()->with('success', __('visit_deleted'));
    }

    // Forum Categories
    public function forumCategories()
    {
        $categories = ForumCategory::orderBy('ordercat', 'asc')->paginate(20);
        return view('theme::admin.forum_categories', compact('categories'));
    }

    public function storeForumCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'icons' => 'required|string',
            'ordercat' => 'required|integer',
            'txt' => 'nullable|string',
        ]);

        ForumCategory::create($request->all());

        return redirect()->back()->with('success', __('category_created'));
    }

    public function updateForumCategory(Request $request, $id)
    {
        $category = ForumCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string',
            'icons' => 'required|string',
            'ordercat' => 'required|integer',
            'txt' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->back()->with('success', __('category_updated'));
    }

    public function deleteForumCategory($id)
    {
        $category = ForumCategory::findOrFail($id);
        $category->delete();
        
        return redirect()->back()->with('success', __('category_deleted'));
    }

    // Directory Categories
    public function directoryCategories()
    {
        $categories = DirectoryCategory::with('parent')->orderBy('sub', 'asc')->orderBy('ordercat', 'asc')->paginate(20);
        $parents = DirectoryCategory::where('sub', 0)->get();
        return view('theme::admin.directory_categories', compact('categories', 'parents'));
    }

    public function storeDirectoryCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'sub' => 'required|integer',
            'ordercat' => 'required|integer',
            'statu' => 'nullable|integer',
            'txt' => 'nullable|string',
            'metakeywords' => 'nullable|string',
        ]);

        DirectoryCategory::create($request->all());

        return redirect()->back()->with('success', __('category_created'));
    }

    public function updateDirectoryCategory(Request $request, $id)
    {
        $category = DirectoryCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string',
            'sub' => 'required|integer',
            'ordercat' => 'required|integer',
            'statu' => 'nullable|integer',
            'txt' => 'nullable|string',
            'metakeywords' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->back()->with('success', __('category_updated'));
    }

    public function deleteDirectoryCategory($id)
    {
        $category = DirectoryCategory::findOrFail($id);
        $category->delete();
        
        return redirect()->back()->with('success', __('category_deleted'));
    }

    // News Management
    public function news()
    {
        $news = News::orderBy('id', 'desc')->get();
        $emojis = Emoji::orderBy('id', 'asc')->get();
        return view('theme::admin.news', compact('news', 'emojis'));
    }

    public function storeNews(Request $request)
    {
        if (!$request->has('text') && $request->has('txt')) {
            $request->merge(['text' => $request->input('txt')]);
        }
        $request->validate([
            'name' => 'required|string',
            'text' => 'required|string',
        ]);

        $text = $request->input('text');
        $news = new News();
        $news->name = $request->name;
        $news->text = $text;
        $news->date = time(); // Old system uses timestamp
        $news->save();

        return redirect()->back()->with('success', __('news_created'));
    }

    public function updateNews(Request $request, $id)
    {
        $news = News::findOrFail($id);
        if (!$request->has('text') && $request->has('txt')) {
            $request->merge(['text' => $request->input('txt')]);
        }
        $request->validate([
            'name' => 'required|string',
            'text' => 'required|string',
        ]);

        $news->update([
            'name' => $request->name,
            'text' => $request->input('text'),
        ]);

        return redirect()->back()->with('success', __('news_updated'));
    }

    public function deleteNews($id)
    {
        $news = News::findOrFail($id);
        $news->delete();
        
        return redirect()->back()->with('success', __('news_deleted'));
    }

    // Knowledgebase Management
    public function knowledgebase(Request $request)
    {
        // If searching or filtering, we might want to return a list view, 
        // but for now let's pass data for the Help Center layout
        
        // Get all unique categories (o_mode)
        $categories = Knowledgebase::select('o_mode')
            ->distinct()
            ->whereNotNull('o_mode')
            ->where('o_mode', '!=', '')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'name' => $item->o_mode,
                    'count' => Knowledgebase::where('o_mode', $item->o_mode)->count(),
                    'articles' => Knowledgebase::where('o_mode', $item->o_mode)->orderBy('id', 'desc')->take(5)->get()
                ];
            });

        $latestArticles = Knowledgebase::orderBy('id', 'desc')->take(5)->get();
        $totalArticles = Knowledgebase::count();
        
        // If we are searching, we might pass the search results
        $searchResults = null;
        if ($request->has('search')) {
            $searchResults = Knowledgebase::where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('o_valuer', 'like', '%' . $request->search . '%')
                  ->paginate(20);
        } elseif ($request->has('category')) {
            $searchResults = Knowledgebase::where('o_mode', $request->category)
                  ->orderBy('id', 'desc')
                  ->paginate(20);
        }

        return view('theme::admin.knowledgebase', compact('categories', 'latestArticles', 'totalArticles', 'searchResults'));
    }

    public function storeKnowledgebase(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'o_valuer' => 'required|string',
            'o_mode' => 'nullable|string', // Category
        ]);

        Knowledgebase::create($request->all());

        return redirect()->back()->with('success', __('article_created'));
    }

    public function updateKnowledgebase(Request $request, $id)
    {
        $article = Knowledgebase::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string',
            'o_valuer' => 'required|string',
            'o_mode' => 'nullable|string',
        ]);

        $article->update($request->all());

        return redirect()->back()->with('success', __('article_updated'));
    }

    public function deleteKnowledgebase($id)
    {
        $article = Knowledgebase::findOrFail($id);
        $article->delete();
        
        return redirect()->back()->with('success', __('article_deleted'));
    }

    // Site Ads Management
    public function siteAds()
    {
        $ads = Ad::orderBy('id', 'asc')->get();
        // Map IDs to Names
        $names = [
            1 => 'Home Page',
            2 => 'User Dashboard',
            3 => 'Header Exchange',
            4 => 'Forum',
            5 => 'Topic',
            6 => 'Footer'
        ];
        
        return view('theme::admin.site_ads', compact('ads', 'names'));
    }

    public function updateSiteAd(Request $request, $id)
    {
        $ad = Ad::findOrFail($id);
        
        $request->validate([
            'code_ads' => 'nullable',
        ]);

        $code = $request->input("code_ads.$id");
        if ($code === null) {
            $code = $request->input('code_ads');
        }

        $ad->update([
            'code_ads' => $code
        ]);

        return redirect()->back()->with('success', __('ad_updated'));
    }

    public function updateSiteAds(Request $request)
    {
        $request->validate([
            'code_ads' => 'array',
            'code_ads.*' => 'nullable|string',
        ]);

        $codes = $request->input('code_ads', []);
        foreach ($codes as $id => $code) {
            if (!is_numeric($id)) {
                continue;
            }
            Ad::where('id', $id)->update([
                'code_ads' => $code,
            ]);
        }

        return redirect()->back()->with('success', __('ad_updated'));
    }

    // Reports Management
    public function reports()
    {
        if (request()->filled('wtid')) {
            $report = Report::find(request()->get('wtid'));
            if ($report) {
                $report->statu = 0;
                $report->save();
            }
            return redirect()->route('admin.reports');
        }

        $reports = Report::with('reporter')->orderBy('id', 'desc')->paginate(20);
        return view('theme::admin.reports', compact('reports'));
    }

    public function storeReport(Request $request)
    {
        $request->validate([
            'txt' => 'required|string',
            's_type' => 'required|integer',
            'tp_id' => 'required|integer',
        ]);

        $report = Report::create([
            'uid' => Auth::id() ?? 0,
            'txt' => $request->txt,
            's_type' => $request->s_type,
            'tp_id' => $request->tp_id,
            'statu' => 1,
        ]);

        return response()->json(['success' => true, 'id' => $report->id]);
    }

    public function deleteReport($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();
        
        return redirect()->back()->with('success', __('report_deleted'));
    }

    // Emojis Management
    public function emojis()
    {
        $emojis = Emoji::orderBy('id', 'desc')->paginate(20);
        return view('theme::admin.emojis', compact('emojis'));
    }

    public function storeEmoji(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'img' => 'required|string',
        ]);

        Emoji::create($request->all());

        return redirect()->back()->with('success', __('emoji_created'));
    }

    public function deleteEmoji($id)
    {
        $emoji = Emoji::findOrFail($id);
        $emoji->delete();
        
        return redirect()->back()->with('success', __('emoji_deleted'));
    }

    // Menu Management
    public function menus()
    {
        $menus = Menu::orderBy('id_m', 'desc')->paginate(20);
        return view('theme::admin.menus', compact('menus'));
    }

    public function storeMenu(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'dir' => 'required|string',
        ]);

        Menu::create($request->all());

        return redirect()->back()->with('success', __('menu_created'));
    }

    public function updateMenu(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string',
            'dir' => 'required|string',
        ]);

        $menu->update($request->all());

        return redirect()->back()->with('success', __('menu_updated'));
    }

    public function deleteMenu($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        
        return redirect()->back()->with('success', __('menu_deleted'));
    }

    // Widgets Management
    public function widgets()
    {
        $widgets = Option::where('o_type', 'box_widget')
            ->orderBy('o_parent', 'asc')
            ->orderBy('o_order', 'asc')
            ->get();
        $places = [
            '1' => 'portal_left',
            '2' => 'portal_right',
            '3' => 'forum_left',
            '4' => 'forum_right',
            '5' => 'directory_left',
            '6' => 'directory_right',
            '7' => 'profile_left',
            '8' => 'profile_right'
        ];
        return view('theme::admin.widgets', compact('widgets', 'places'));
    }

    public function widgetForm(Request $request)
    {
        $type = $request->get('type');
        $allowedTypes = ['widget_html', 'widget_members', 'widget_stats_box'];
        if (!in_array($type, $allowedTypes, true)) {
            abort(404);
        }
        $places = [
            '1' => 'portal_left',
            '2' => 'portal_right',
            '3' => 'forum_left',
            '4' => 'forum_right',
            '5' => 'directory_left',
            '6' => 'directory_right',
            '7' => 'profile_left',
            '8' => 'profile_right'
        ];
        $allowedPlaceIds = $type === 'widget_html' ? ['1','2','3','4','5','6','7','8'] : ['1','2','3','4','5','6'];
        return view('theme::admin.widgets_form', [
            'mode' => 'create',
            'widget' => null,
            'type' => $type,
            'places' => $places,
            'allowedPlaceIds' => $allowedPlaceIds,
        ]);
    }

    public function widgetEditForm($id)
    {
        $widget = Option::where('o_type', 'box_widget')->where('id', $id)->firstOrFail();
        $places = [
            '1' => 'portal_left',
            '2' => 'portal_right',
            '3' => 'forum_left',
            '4' => 'forum_right',
            '5' => 'directory_left',
            '6' => 'directory_right',
            '7' => 'profile_left',
            '8' => 'profile_right'
        ];
        $allowedPlaceIds = $widget->o_mode === 'widget_html' ? ['1','2','3','4','5','6','7','8'] : ['1','2','3','4','5','6'];
        return view('theme::admin.widgets_form', [
            'mode' => 'edit',
            'widget' => $widget,
            'type' => $widget->o_mode,
            'places' => $places,
            'allowedPlaceIds' => $allowedPlaceIds,
        ]);
    }

    public function storeWidget(Request $request)
    {
        $type = $request->input('o_mode');
        $allowedPlaceIds = $type === 'widget_html' ? '1,2,3,4,5,6,7,8' : '1,2,3,4,5,6';
        $request->validate([
            'name' => 'required|string',
            'o_parent' => 'required|integer|in:' . $allowedPlaceIds,
            'o_order' => 'required|integer',
            'o_valuer' => 'nullable|string', // Content
            'o_mode' => 'required|in:widget_html,widget_members,widget_stats_box',
        ]);

        $data = $request->only(['name', 'o_parent', 'o_order', 'o_valuer', 'o_mode']);
        $data['o_type'] = 'box_widget';
        if ($type !== 'widget_html') {
            $data['o_valuer'] = '';
        }

        Option::create($data);

        return redirect()->back()->with('success', __('widget_created'));
    }

    public function updateWidget(Request $request, $id)
    {
        $widget = Option::where('o_type', 'box_widget')->where('id', $id)->firstOrFail();
        $allowedPlaceIds = $widget->o_mode === 'widget_html' ? '1,2,3,4,5,6,7,8' : '1,2,3,4,5,6';
        $request->validate([
            'name' => 'required|string',
            'o_parent' => 'required|integer|in:' . $allowedPlaceIds,
            'o_order' => 'required|integer',
            'o_valuer' => 'nullable|string',
        ]);

        $data = $request->only(['name', 'o_parent', 'o_order', 'o_valuer']);
        if ($widget->o_mode !== 'widget_html') {
            $data['o_valuer'] = '';
        }
        $widget->update($data);

        return redirect()->back()->with('success', __('widget_updated'));
    }

    public function reorderWidgets(Request $request)
    {
        $order = $request->input('order', []);
        if (!is_array($order) || empty($order)) {
            return response()->json(['status' => 'error'], 422);
        }
        $ids = array_values(array_filter($order, fn ($id) => is_numeric($id)));
        if (empty($ids)) {
            return response()->json(['status' => 'error'], 422);
        }
        foreach ($ids as $index => $id) {
            Option::where('o_type', 'box_widget')->where('id', $id)->update(['o_order' => $index]);
        }
        return response()->json(['status' => 'ok']);
    }

    public function deleteWidget($id)
    {
        $widget = Option::where('o_type', 'box_widget')->where('id', $id)->firstOrFail();
        $widget->delete();
        
        return redirect()->back()->with('success', __('widget_deleted'));
    }

    // Languages Management
    public function languages()
    {
        $langDir = base_path('lang');
        
        // Auto-sync existing folders to the database
        if (File::exists($langDir)) {
            $directories = File::directories($langDir);
            foreach ($directories as $dir) {
                $code = basename($dir);
                // Check if this code exists in DB
                $exists = Option::where('o_type', 'languages')->where('o_valuer', $code)->exists();
                if (!$exists) {
                    $name = strtoupper($code);
                    if ($code === 'en') $name = 'English';
                    if ($code === 'ar') $name = 'Arabic';
                    if ($code === 'fr') $name = 'French';
                    if ($code === 'es') $name = 'Spanish';
                    if ($code === 'pt') $name = 'Portuguese';
                    if ($code === 'de') $name = 'German';
                    if ($code === 'it') $name = 'Italian';
                    if ($code === 'tr') $name = 'Turkish';
                    if ($code === 'fa') $name = 'Persian';
                    
                    Option::create([
                        'name' => $name,
                        'o_valuer' => $code,
                        'o_type' => 'languages',
                        'o_order' => 0
                    ]);
                }
            }
        }

        $languages = Option::where('o_type', 'languages')->orderBy('id', 'desc')->paginate(20);
        
        foreach ($languages as $lang) {
            $lang->has_folder = File::exists(base_path("lang/{$lang->o_valuer}"));
        }
        
        return view('theme::admin.languages', compact('languages'));
    }

    public function storeLanguage(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'o_valuer' => 'required|string', // Language Code
        ]);

        $data = $request->all();
        $code = strtolower(trim($data['o_valuer']));
        $data['o_valuer'] = $code;
        $data['o_type'] = 'languages';
        $data['o_order'] = 0;

        Option::create($data);

        $newLangPath = base_path("lang/{$code}");
        $enLangPath = base_path('lang/en');
        if (!File::exists($newLangPath) && File::exists($enLangPath)) {
            File::copyDirectory($enLangPath, $newLangPath);
        }

        return redirect()->back()->with('success', __('messages.language_created') ?? 'Language created successfully');
    }

    public function updateLanguage(Request $request, $id)
    {
        $language = Option::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string',
            'o_valuer' => 'required|string',
        ]);

        $language->update($request->all());

        return redirect()->back()->with('success', __('messages.language_updated') ?? 'Language updated successfully');
    }

    public function deleteLanguage($id)
    {
        $language = Option::findOrFail($id);
        
        $langPath = base_path("lang/{$language->o_valuer}");
        if (File::exists($langPath) && $language->o_valuer !== 'en' && $language->o_valuer !== 'ar') {
            File::deleteDirectory($langPath);
        }
        
        $language->delete();
        
        return redirect()->back()->with('success', __('messages.language_deleted') ?? 'Language deleted successfully');
    }

    public function editLanguageTerms($id)
    {
        $language = Option::findOrFail($id);
        $code = $language->o_valuer;
        $filePath = base_path("lang/{$code}/messages.php");
        
        $terms = [];
        if (File::exists($filePath)) {
            $terms = require $filePath;
        }

        // We load English as fallback to show keys
        $defaultTerms = [];
        $defaultPath = base_path("lang/en/messages.php");
        if (File::exists($defaultPath)) {
            $defaultTerms = require $defaultPath;
        }

        return view('theme::admin.language_terms', compact('language', 'terms', 'defaultTerms'));
    }

    public function updateLanguageTerms(Request $request, $id)
    {
        $language = Option::findOrFail($id);
        $code = $language->o_valuer;
        $filePath = base_path("lang/{$code}/messages.php");
        
        $terms = $request->input('terms', []);
        
        $content = "<?php\n\nreturn [\n";
        foreach ($terms as $key => $value) {
            $key = addslashes($key);
            $value = addslashes($value ?? '');
            $content .= "    '{$key}' => '{$value}',\n";
        }
        $content .= "];\n";

        if (!File::exists(dirname($filePath))) {
            File::makeDirectory(dirname($filePath), 0755, true);
        }

        File::put($filePath, $content);

        return redirect()->back()->with('success', __('messages.terms_updated') ?? 'Terms updated successfully');
    }

    public function exportLanguage($id)
    {
        $language = Option::findOrFail($id);
        $code = $language->o_valuer;
        $langPath = base_path("lang/{$code}");
        
        if (!File::exists($langPath)) {
            return redirect()->back()->withErrors(__('messages.language_folder_not_found') ?? 'Language folder not found');
        }

        $zipFileName = "language_{$code}.zip";
        $zipPath = storage_path("app/public/{$zipFileName}");
        
        $zip = new \ZipArchive;
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $files = File::allFiles($langPath);
            foreach ($files as $file) {
                $zip->addFile($file->getRealPath(), $file->getRelativePathname());
            }
            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    // Products
    public function products(Request $request)
    {
        $products = Product::withoutGlobalScope('store')
            ->where('o_type', 'store')
            ->with(['user', 'type'])
            ->orderBy('id', 'desc')
            ->paginate(20);
            
        return view('theme::admin.products', compact('products'));
    }

    public function deleteProduct(Request $request)
    {
        $id = $request->input('id');
        if (!$id) return redirect()->back()->with('error', __('messages.missing_product_id') ?? 'Missing Product ID.');

        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->find($id);
        if (!$product) return redirect()->back()->with('error', __('messages.product_not_found') ?? 'Product not found.');

        DB::transaction(function () use ($product, $id) {
            // Delete related status
            Status::where('tp_id', $id)->where('s_type', 7867)->delete();
            
            // Delete related options (comments, files, type, reactions, etc.)
            Option::where('o_parent', $id)->whereIn('o_type', ['s_coment', 'store_file', 'store_type', 'data_reaction', 'hest_pts'])->delete();

            // Delete product
            $product->delete();
        });

        return redirect()->back()->with('success', __('messages.product_deleted') ?? 'Product deleted successfully.');
    }

    // Plugins Management
    public function plugins(PluginManager $pluginManager)
    {
        $plugins = $pluginManager->getAllPlugins();
        $updates = $pluginManager->checkForUpdates();
        return view('theme::admin.plugins', compact('plugins', 'updates'));
    }

    public function activatePlugin(Request $request, PluginManager $pluginManager)
    {
        $request->validate(['slug' => 'required|string']);
        
        if ($pluginManager->activate($request->slug)) {
            return redirect()->back()->with('success', __('Plugin activated successfully'));
        }
        
        return redirect()->back()->with('error', __('Failed to activate plugin'));
    }

    public function deactivatePlugin(Request $request, PluginManager $pluginManager)
    {
        $request->validate(['slug' => 'required|string']);
        
        if ($pluginManager->deactivate($request->slug)) {
            return redirect()->back()->with('success', __('Plugin deactivated successfully'));
        }
        
        return redirect()->back()->with('error', __('Failed to deactivate plugin'));
    }

    public function deletePlugin(Request $request, PluginManager $pluginManager)
    {
        $request->validate(['slug' => 'required|string']);
        
        if ($pluginManager->delete($request->slug)) {
            return redirect()->back()->with('success', __('Plugin deleted successfully'));
        }
        
        return redirect()->back()->with('error', __('Failed to delete plugin'));
    }

    public function uploadPlugin(Request $request, PluginManager $pluginManager)
    {
        $request->validate([
            'plugin_zip' => 'required|file|mimes:zip',
        ]);

        $result = $pluginManager->install($request->file('plugin_zip'));

        if ($result === true) {
            return redirect()->back()->with('success', __('Plugin installed successfully'));
        }

        return redirect()->back()->with('error', $result);
    }

    public function upgradePlugin(Request $request, PluginManager $pluginManager)
    {
        $request->validate(['slug' => 'required|string']);

        $result = $pluginManager->upgrade($request->slug);

        if ($result === true) {
            return redirect()->back()->with('success', __('Plugin upgraded successfully'));
        }

        return redirect()->back()->with('error', $result);
    }

    // Themes Management
    public function themes()
    {
        $themeManager = new ThemeManager();
        $themes = $themeManager->getAllThemes();
        return view('theme::admin.themes.index', compact('themes'));
    }

    public function activateTheme(Request $request)
    {
        $request->validate([
            'slug' => 'required|string'
        ]);

        $themeManager = new ThemeManager();
        if ($themeManager->activate($request->slug)) {
            return redirect()->back()->with('success', __('Theme activated successfully.'));
        }

        return redirect()->back()->with('error', __('Theme activation failed.'));
    }
}
