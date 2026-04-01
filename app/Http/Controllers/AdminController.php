<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Setting;
use App\Models\State;
use App\Models\Notification;
use App\Models\ForumTopic;
use App\Models\ForumModerator;
use App\Models\Directory;
use App\Models\Product;

use App\Models\Banner;
use App\Models\Link;
use App\Models\SmartAd;
use App\Models\Visit;
use App\Models\Status;
use App\Models\ForumCategory;
use App\Models\DirectoryCategory;
use App\Models\News;
use App\Models\Ad;
use App\Models\Report;
use App\Models\OrderRequest;
use App\Models\Emoji;
use App\Models\Menu;
use App\Models\Option;
use App\Models\Knowledgebase;
use App\Models\Page;
use App\Models\ProductFile;
use App\Services\GamificationService;
use App\Services\MaintenanceModeManager;
use App\Services\PluginManager;
use App\Services\ThemeManager;
use App\Support\BannerServingSettings;
use App\Support\BannerSizeCatalog;
use App\Support\ForumSettings;
use App\Support\SmartAdsSettings;
use App\Support\SmartAdTargeting;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    private const ADMIN_INVENTORY_FILTER_OPTION_TYPE = 'admin_inventory_filters';

    private const FORUM_PERMISSION_KEYS = [
        'pin_topics',
        'lock_topics',
        'edit_topics',
        'delete_topics',
        'delete_comments',
    ];

    public function __construct(
        private readonly GamificationService $gamification,
        private readonly MaintenanceModeManager $maintenanceMode
    ) {
    }

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

        $latestVersion = null;
        try {
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
        } catch (\Throwable $e) {
            $latestVersion = null;
        }

        // Stats - wrapped in try-catch for fresh installs or restricted hosting
        try {
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
                    'views' => 0,
                ],
                'smart_ads' => [
                    'total' => SmartAd::count(),
                    'impressions' => SmartAd::sum('impressions'),
                    'clicks' => SmartAd::sum('clicks'),
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
        } catch (\Throwable $e) {
            $stats = [
                'users' => 0, 'users_online' => 0, 'posts' => 0, 'topics' => 0,
                'listings' => 0, 'products' => 0,
                'banners' => ['total' => 0, 'views' => 0, 'clicks' => 0],
                'links' => ['total' => 0, 'clicks' => 0, 'views' => 0],
                'smart_ads' => ['total' => 0, 'impressions' => 0, 'clicks' => 0],
                'visits' => ['total' => 0],
                'reactions' => ['total' => 0],
                'followers' => 0,
                'reports' => ['pending' => 0],
                'last_user' => null, 'last_post' => null,
            ];
        }

        // Chart Data for Dashboard
        $chartData = [
            'distribution' => [
                'labels' => [
                    __('messages.bannads'),
                    __('messages.textads'),
                    __('messages.smart_ads'),
                    __('messages.exvisit'),
                ],
                'data' => [
                    $stats['banners']['total'],
                    $stats['links']['total'],
                    $stats['smart_ads']['total'],
                    $stats['visits']['total'],
                ],
            ],
            'engagement' => [
                'labels' => [
                    __('messages.bannads') . ' ' . __('messages.Views'),
                    __('messages.bannads') . ' ' . __('messages.clicks'),
                    __('messages.textads') . ' ' . __('messages.clicks'),
                    __('messages.smart_ads') . ' ' . __('messages.Views'),
                    __('messages.smart_ads') . ' ' . __('messages.clicks'),
                ],
                'data' => [
                    $stats['banners']['views'],
                    $stats['banners']['clicks'],
                    $stats['links']['clicks'],
                    $stats['smart_ads']['impressions'],
                    $stats['smart_ads']['clicks'],
                ],
            ],
        ];
        
        // --- Community Statistics Charts ---
        $days = 30;
        $labels = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $labels[] = now()->subDays($i)->format('M d');
        }

        $startDate = now()->subDays($days)->startOfDay()->timestamp;

        // Posts by type
        $postTypesRaw = Status::where('date', '>=', $startDate)
            ->whereIn('s_type', [100, 2, 4, 10, 7867, 6, 5])
            ->select(DB::raw('FROM_UNIXTIME(date, "%b %d") as day'), 's_type', DB::raw('count(*) as count'))
            ->groupBy('day', 's_type')
            ->get();

        $postTypes = ['100' => [], '2' => [], '4' => [], '10' => [], '7867' => [], '6' => [], '5' => []];
        foreach($postTypesRaw as $row) {
            $postTypes[$row->s_type][$row->day] = $row->count;
        }

        // Comments (Forum + Options-based)
        $forumComments = \App\Models\ForumComment::where('date', '>=', $startDate)->select(DB::raw('FROM_UNIXTIME(date, "%b %d") as day'), DB::raw('count(*) as count'))->groupBy('day')->pluck('count', 'day');
        $otherComments = Option::whereIn('o_type', ['d_coment', 's_coment', 'order_comment'])->where('o_order', '>=', $startDate)->select(DB::raw('FROM_UNIXTIME(o_order, "%b %d") as day'), DB::raw('count(*) as count'))->groupBy('day')->pluck('count', 'day');
        
        // Reactions
        $reactionsData = \App\Models\Like::where('time_t', '>=', $startDate)->whereIn('type', [2, 3, 22, 6, 1])->select(DB::raw('FROM_UNIXTIME(time_t, "%b %d") as day'), DB::raw('count(*) as count'))->groupBy('day')->pluck('count', 'day');

        $communityChartData = [
            'labels' => $labels,
            'posts' => [
                'text' => array_map(fn($l) => $postTypes['100'][$l] ?? 0, $labels),
                'link' => array_map(fn($l) => $postTypes['2'][$l] ?? 0, $labels),
                'gallery' => array_map(fn($l) => $postTypes['4'][$l] ?? 0, $labels),
                'forum' => array_map(fn($l) => ($postTypes['10'][$l] ?? 0), $labels), // 10 = New Topic
                'store' => array_map(fn($l) => $postTypes['7867'][$l] ?? 0, $labels),
                'orders' => array_map(fn($l) => $postTypes['6'][$l] ?? 0, $labels),
                'news' => array_map(fn($l) => $postTypes['5'][$l] ?? 0, $labels),
            ],
            'comments' => [
                'total' => array_map(fn($l) => ($forumComments[$l] ?? 0) + ($otherComments[$l] ?? 0), $labels),
                'forum' => array_map(fn($l) => $forumComments[$l] ?? 0, $labels),
                'store' => array_map(fn($l) => Option::where('o_type', 's_coment')->where('o_order', '>=', $startDate)->where(DB::raw('FROM_UNIXTIME(o_order, "%b %d")'), $l)->count(), $labels),
                'orders' => array_map(fn($l) => Option::where('o_type', 'order_comment')->where('o_order', '>=', $startDate)->where(DB::raw('FROM_UNIXTIME(o_order, "%b %d")'), $l)->count(), $labels),
                'directory' => array_map(fn($l) => Option::where('o_type', 'd_coment')->where('o_order', '>=', $startDate)->where(DB::raw('FROM_UNIXTIME(o_order, "%b %d")'), $l)->count(), $labels),
            ],
            'reactions' => [
                'total' => array_map(fn($l) => $reactionsData[$l] ?? 0, $labels),
                'forum' => array_map(fn($l) => \App\Models\Like::where('type', 2)->where('time_t', '>=', $startDate)->where(DB::raw('FROM_UNIXTIME(time_t, "%b %d")'), $l)->count(), $labels),
                'store' => array_map(fn($l) => \App\Models\Like::where('type', 3)->where('time_t', '>=', $startDate)->where(DB::raw('FROM_UNIXTIME(time_t, "%b %d")'), $l)->count(), $labels),
                'directory' => array_map(fn($l) => \App\Models\Like::where('type', 22)->where('time_t', '>=', $startDate)->where(DB::raw('FROM_UNIXTIME(time_t, "%b %d")'), $l)->count(), $labels),
                'orders' => array_map(fn($l) => \App\Models\Like::where('type', 6)->where('time_t', '>=', $startDate)->where(DB::raw('FROM_UNIXTIME(time_t, "%b %d")'), $l)->count(), $labels),
                'follows' => array_map(fn($l) => \App\Models\Like::where('type', 1)->where('time_t', '>=', $startDate)->where(DB::raw('FROM_UNIXTIME(time_t, "%b %d")'), $l)->count(), $labels),
            ],
        ];

        $reactionsSummary = Option::where('o_type', 'data_reaction')
            ->select('o_valuer as type', DB::raw('count(*) as count'))
            ->groupBy('o_valuer')
            ->get()
            ->pluck('count', 'type')
            ->toArray();

        // Ensure common types exist even if 0
        $commonReactions = ['like', 'love', 'funny', 'wow', 'sad', 'angry', 'dislike', 'happy'];
        foreach ($commonReactions as $cr) {
            if (!isset($reactionsSummary[$cr])) {
                $reactionsSummary[$cr] = 0;
            }
        }

        return view('theme::admin.index', compact('stats', 'currentVersion', 'latestVersion', 'chartData', 'communityChartData', 'reactionsSummary'));
    }

    public function settings()
    {
        $settings = Setting::firstOrFail();
        $bannerRepeatWindowMinutes = BannerServingSettings::repeatWindowMinutes();
        $smartAdsPointsDivisor = SmartAdsSettings::pointsDivisor();

        return view('theme::admin.settings', compact('settings', 'bannerRepeatWindowMinutes', 'smartAdsPointsDivisor'));
    }

    public function updateSettings(Request $request)
    {
        $settings = Setting::firstOrFail();
        
        $request->validate([
            'titer' => 'required|string',
            'url' => 'required|url',
            'banner_repeat_window_minutes' => 'nullable|integer|min:0|max:525600',
            'smart_ads_points_divisor' => 'nullable|numeric|min:0.1|max:1000',
        ]);

        $settings->update($request->all());

        Option::updateOrCreate(
            [
                'o_type' => BannerServingSettings::OPTION_TYPE,
                'name' => BannerServingSettings::REPEAT_WINDOW_NAME,
            ],
            [
                'o_valuer' => (string) ($request->input('banner_repeat_window_minutes', BannerServingSettings::DEFAULT_REPEAT_WINDOW_MINUTES)),
            ]
        );

        Option::updateOrCreate(
            [
                'o_type' => SmartAdsSettings::OPTION_TYPE,
                'name' => SmartAdsSettings::POINTS_DIVISOR_NAME,
            ],
            [
                'o_valuer' => (string) ($request->input('smart_ads_points_divisor', SmartAdsSettings::DEFAULT_POINTS_DIVISOR)),
            ]
        );

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
        $query = User::select('users.*');

        // Handle Sorting
        $sort = $request->input('sort', 'id');
        $direction = $request->input('direction', 'desc');
        
        $allowedSorts = ['id', 'username', 'online', 'pts', 'role'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'id';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        if ($sort === 'role') {
            if (app(\App\Services\V420SchemaService::class)->supports('site_admins')) {
                // Sort by role (super admin first, then site admins, then members)
                $query->orderByRaw('users.id = 1 DESC')
                      ->leftJoin('site_admins', 'users.id', '=', 'site_admins.user_id')
                      ->orderByRaw('site_admins.id IS NOT NULL DESC');
            } else {
                $query->orderByRaw('users.id = 1 DESC');
            }
        } else {
            $query->orderBy('users.' . $sort, $direction);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && $request->role != '') {
            // Filter by Role (Admin or Member)
            if ($request->role == 'admin') {
                $query->where(function($q) {
                    $q->where('id', 1);
                    if (app(\App\Services\V420SchemaService::class)->supports('site_admins')) {
                        $q->orWhereHas('siteAdminEntry');
                    }
                });
            } elseif ($request->role == 'member') {
                $query->where('id', '!=', 1);
                if (app(\App\Services\V420SchemaService::class)->supports('site_admins')) {
                    $query->whereDoesntHave('siteAdminEntry');
                }
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

        $users = $query->paginate(20)->appends($request->except('page'));
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
            'pts' => 'required|numeric',
            'vu' => 'required|numeric',
            'nvu' => 'required|numeric',
            'nlink' => 'required|numeric',
            'nsmart' => 'required|numeric',
        ]);

        // slugify input to ensure URL safety
        $slug = Str::slug($request->slug);

        $user->update([
            'username' => $slug, // Synchronize username with slug to fix profile link inconsistency
            'email' => $request->email,
            'ucheck' => $request->ucheck,
            'pts' => $request->pts,
            'vu' => $request->vu,
            'nvu' => $request->nvu,
            'nlink' => $request->nlink,
            'nsmart' => $request->nsmart,
        ]);

        // Update Slug Option
        Option::updateOrCreate(
            ['o_type' => 'user', 'o_order' => $id],
            [
                'name' => $slug,
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
        $filterState = $this->resolveAdminInventoryFilterState($request, 'banners', [
            'logic' => 'and',
            'keyword' => '',
            'username' => '',
            'user_id' => '',
            'status' => '',
            'id_min' => '',
            'id_max' => '',
            'views_min' => '',
            'views_max' => '',
            'clicks_min' => '',
            'clicks_max' => '',
            'size' => '',
        ]);

        $query = Banner::with('user')->orderBy('id', 'desc');

        $this->applyAdminInventoryFilters($query, $filterState, $this->bannerFilterCallbacks());

        $banners = $query->paginate(20)->withQueryString();

        return view('theme::admin.banners', [
            'banners' => $banners,
            'filterState' => $filterState,
            'filterFields' => $this->bannerFilterFields(),
            'resultsCount' => $banners->total(),
        ]);
    }

    public function adsHub(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $banners = Banner::with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(function (Banner $banner) {
                return (object) [
                    'type' => 'banner',
                    'id' => $banner->id,
                    'name' => $banner->name,
                    'owner' => $banner->user?->username,
                    'status' => $banner->statu,
                    'metric_primary' => $banner->vu,
                    'metric_secondary' => $banner->clik,
                    'edit_url' => route('admin.banners.edit', $banner->id),
                    'badge' => $banner->px,
                    'created_at' => $banner->created_at,
                ];
            });

        $links = Link::with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('txt', 'like', '%' . $search . '%');
            })
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(function (Link $link) {
                return (object) [
                    'type' => 'link',
                    'id' => $link->id,
                    'name' => $link->name,
                    'owner' => $link->user?->username,
                    'status' => $link->statu,
                    'metric_primary' => $link->clik,
                    'metric_secondary' => null,
                    'edit_url' => route('admin.links'),
                                'badge' => __('messages.textads'),
                    'created_at' => $link->created_at,
                ];
            });

        $smartAds = SmartAd::with('user')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('landing_url', 'like', '%' . $search . '%')
                    ->orWhere('headline_override', 'like', '%' . $search . '%')
                    ->orWhere('source_title', 'like', '%' . $search . '%');
            })
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(function (SmartAd $smartAd) {
                return (object) [
                    'type' => 'smart',
                    'id' => $smartAd->id,
                    'name' => $smartAd->displayTitle(),
                    'owner' => $smartAd->user?->username,
                    'status' => $smartAd->statu,
                    'metric_primary' => $smartAd->impressions,
                    'metric_secondary' => $smartAd->clicks,
                    'edit_url' => route('admin.smart_ads.edit', $smartAd->id),
                                'badge' => __('messages.smart_ad'),
                    'created_at' => $smartAd->created_at,
                ];
            });

        $items = $banners
            ->concat($links)
            ->concat($smartAds)
            ->sortByDesc(fn ($item) => optional($item->created_at)->getTimestamp() ?? 0)
            ->values();

        $summary = [
            'banners' => Banner::count(),
            'links' => Link::count(),
            'smart_ads' => SmartAd::count(),
        ];

        return view('theme::admin.ads_overview', compact('items', 'summary', 'search'));
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
            'px' => 'required|string',
            'statu' => 'required|in:1,2',
        ]);
        $bannerSize = $this->validatedBannerSize($request->input('px'));

        $banner->update([
            'name' => $request->name,
            'url' => $request->url,
            'img' => $request->img,
            'px' => $bannerSize,
            'statu' => $request->statu,
        ]);

        // Notification if status changed
        if ($oldStatus != $request->statu) {
            $nurl = 'ads/banners/' . $id . '/edit';
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
        } elseif ($type == 'smart') {
            $title = __('messages.smart_ads') . ' ' . __('messages.Views');
            $query->where('t_name', 'smart');
        } elseif ($type == 'smart_click') {
            $title = __('messages.smart_ads') . ' ' . __('messages.clicks');
            $query->where('t_name', 'smart_click');
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
        $filterState = $this->resolveAdminInventoryFilterState($request, 'links', [
            'logic' => 'and',
            'keyword' => '',
            'username' => '',
            'user_id' => '',
            'status' => '',
            'id_min' => '',
            'id_max' => '',
            'clicks_min' => '',
            'clicks_max' => '',
        ]);

        $query = Link::with('user')->orderBy('id', 'desc');

        $this->applyAdminInventoryFilters($query, $filterState, $this->linkFilterCallbacks());

        $links = $query->paginate(20)->withQueryString();

        return view('theme::admin.links', [
            'links' => $links,
            'filterState' => $filterState,
            'filterFields' => $this->linkFilterFields(),
            'resultsCount' => $links->total(),
        ]);
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

    public function smartAds(Request $request)
    {
        $filterState = $this->resolveAdminInventoryFilterState($request, 'smart_ads', [
            'logic' => 'and',
            'keyword' => '',
            'username' => '',
            'user_id' => '',
            'status' => '',
            'created_from' => '',
            'created_to' => '',
            'impressions_min' => '',
            'impressions_max' => '',
            'clicks_min' => '',
            'clicks_max' => '',
        ]);

        $query = SmartAd::with('user')->orderBy('id', 'desc');

        $this->applyAdminInventoryFilters($query, $filterState, $this->smartAdFilterCallbacks());

        $smartAds = $query->paginate(20)->withQueryString();

        return view('theme::admin.smart_ads', [
            'smartAds' => $smartAds,
            'filterState' => $filterState,
            'filterFields' => $this->smartAdFilterFields(),
            'resultsCount' => $smartAds->total(),
        ]);
    }

    public function editSmartAd($id)
    {
        $smartAd = SmartAd::with('user')->findOrFail($id);

        return view('theme::admin.smart_ad_edit', [
            'smartAd' => $smartAd,
            'deviceOptions' => [
                'desktop' => __('messages.smart_device_desktop'),
                'mobile' => __('messages.smart_device_mobile'),
                'tablet' => __('messages.smart_device_tablet'),
            ],
            'targetCountries' => implode(', ', $smartAd->targetCountries()),
            'selectedDevices' => $smartAd->targetDevices(),
        ]);
    }

    public function updateSmartAd(Request $request, $id)
    {
        $smartAd = SmartAd::findOrFail($id);

        $validated = $request->validate([
            'landing_url' => 'required|url|max:2048',
            'headline_override' => 'nullable|string|max:255',
            'description_override' => 'nullable|string|max:600',
            'image' => 'nullable|string|max:2048',
            'countries' => 'nullable|string|max:1000',
            'manual_keywords' => 'nullable|string|max:1000',
            'devices' => 'nullable|array',
            'devices.*' => 'in:desktop,mobile,tablet',
            'statu' => 'required|in:0,1,2',
        ]);

        $smartAd->update([
            'landing_url' => $validated['landing_url'],
            'headline_override' => trim((string) ($validated['headline_override'] ?? '')) ?: null,
            'description_override' => trim((string) ($validated['description_override'] ?? '')) ?: null,
            'image' => trim((string) ($validated['image'] ?? '')) ?: null,
            'countries' => SmartAdTargeting::encodeList(SmartAdTargeting::normalizeCountryCodes($validated['countries'] ?? '')),
            'devices' => SmartAdTargeting::encodeList(SmartAdTargeting::normalizeDeviceTypes($validated['devices'] ?? [])),
            'manual_keywords' => trim((string) ($validated['manual_keywords'] ?? '')) ?: null,
            'statu' => (int) $validated['statu'],
        ]);

        return redirect()->route('admin.smart_ads')->with('success', __('messages.smart_ad_admin_updated'));
    }

    public function deleteSmartAd($id)
    {
        $smartAd = SmartAd::findOrFail($id);
        $smartAd->delete();

        return redirect()->route('admin.smart_ads')->with('success', __('messages.smart_ad_admin_deleted'));
    }

    public function visits(Request $request)
    {
        $filterState = $this->resolveAdminInventoryFilterState($request, 'visits', [
            'logic' => 'and',
            'keyword' => '',
            'username' => '',
            'user_id' => '',
            'status' => '',
            'date_from' => '',
            'date_to' => '',
            'id_min' => '',
            'id_max' => '',
            'views_min' => '',
            'views_max' => '',
        ]);

        $query = Visit::with('user')->orderBy('id', 'desc');

        $this->applyAdminInventoryFilters($query, $filterState, $this->visitFilterCallbacks());

        $visits = $query->paginate(20)->withQueryString();

        return view('theme::admin.visits', [
            'visits' => $visits,
            'filterState' => $filterState,
            'filterFields' => $this->visitFilterFields(),
            'resultsCount' => $visits->total(),
        ]);
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

    private function resolveAdminInventoryFilterState(Request $request, string $page, array $defaults): array
    {
        if ($request->boolean('reset_filters')) {
            $this->forgetAdminInventoryFilterPreference($page);

            return $defaults;
        }

        $queryParams = $request->query();
        $filterKeys = array_keys($defaults);
        $hasExplicitFilters = collect($filterKeys)->contains(
            fn (string $key) => array_key_exists($key, $queryParams)
        );

        if (!$hasExplicitFilters) {
            return $this->normalizeAdminInventoryFilterState(
                array_merge($defaults, $this->loadAdminInventoryFilterPreference($page)),
                $defaults
            );
        }

        $state = $defaults;

        foreach ($filterKeys as $key) {
            if (array_key_exists($key, $queryParams)) {
                $state[$key] = $this->normalizeAdminInventoryFilterValue($queryParams[$key]);
            }
        }

        $state = $this->normalizeAdminInventoryFilterState($state, $defaults);

        if ($request->boolean('save_preference')) {
            $this->storeAdminInventoryFilterPreference($page, $state);
        }

        return $state;
    }

    private function normalizeAdminInventoryFilterState(array $state, array $defaults): array
    {
        $normalized = $defaults;

        foreach ($defaults as $key => $defaultValue) {
            $normalized[$key] = array_key_exists($key, $state)
                ? $this->normalizeAdminInventoryFilterValue($state[$key])
                : $defaultValue;
        }

        $normalized['logic'] = in_array($normalized['logic'], ['and', 'or'], true)
            ? $normalized['logic']
            : 'and';

        return $normalized;
    }

    private function normalizeAdminInventoryFilterValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($item) => $this->normalizeAdminInventoryFilterValue($item), $value);
        }

        return is_string($value) ? trim($value) : $value;
    }

    private function storeAdminInventoryFilterPreference(string $page, array $state): void
    {
        if (!$this->adminInventoryHasActiveFilters($state)) {
            $this->forgetAdminInventoryFilterPreference($page);

            return;
        }

        try {
            Option::updateOrCreate(
                [
                    'o_type' => self::ADMIN_INVENTORY_FILTER_OPTION_TYPE,
                    'name' => $page,
                    'o_parent' => (int) Auth::id(),
                ],
                [
                    'o_valuer' => json_encode($state, JSON_UNESCAPED_UNICODE),
                    'o_order' => (int) Auth::id(),
                    'o_mode' => 'inventory',
                ]
            );
        } catch (\Throwable) {
        }
    }

    private function loadAdminInventoryFilterPreference(string $page): array
    {
        try {
            $row = Option::query()
                ->where('o_type', self::ADMIN_INVENTORY_FILTER_OPTION_TYPE)
                ->where('name', $page)
                ->where('o_parent', (int) Auth::id())
                ->first();

            $decoded = json_decode((string) ($row?->o_valuer ?? ''), true);

            return is_array($decoded) ? $decoded : [];
        } catch (\Throwable) {
            return [];
        }
    }

    private function forgetAdminInventoryFilterPreference(string $page): void
    {
        try {
            Option::query()
                ->where('o_type', self::ADMIN_INVENTORY_FILTER_OPTION_TYPE)
                ->where('name', $page)
                ->where('o_parent', (int) Auth::id())
                ->delete();
        } catch (\Throwable) {
        }
    }

    private function adminInventoryHasActiveFilters(array $state): bool
    {
        foreach ($state as $key => $value) {
            if ($key !== 'logic' && $this->adminInventoryFilterHasValue($value)) {
                return true;
            }
        }

        return false;
    }

    private function adminInventoryFilterHasValue(mixed $value): bool
    {
        if (is_array($value)) {
            return collect($value)->contains(fn ($item) => $this->adminInventoryFilterHasValue($item));
        }

        return !($value === null || $value === '');
    }

    private function applyAdminInventoryFilters(\Illuminate\Database\Eloquent\Builder $query, array $state, array $callbacks): void
    {
        $activeFilters = [];

        foreach ($callbacks as $key => $callback) {
            $value = $state[$key] ?? null;

            if ($this->adminInventoryFilterHasValue($value)) {
                $activeFilters[] = [$callback, $value];
            }
        }

        if ($activeFilters === []) {
            return;
        }

        if (($state['logic'] ?? 'and') === 'or') {
            $query->where(function ($outerQuery) use ($activeFilters) {
                foreach ($activeFilters as [$callback, $value]) {
                    $outerQuery->orWhere(function ($nestedQuery) use ($callback, $value) {
                        $callback($nestedQuery, $value);
                    });
                }
            });

            return;
        }

        foreach ($activeFilters as [$callback, $value]) {
            $query->where(function ($nestedQuery) use ($callback, $value) {
                $callback($nestedQuery, $value);
            });
        }
    }

    private function parseAdminInventoryDate(?string $value, bool $endOfDay = false): ?Carbon
    {
        if (!$this->adminInventoryFilterHasValue($value)) {
            return null;
        }

        try {
            $date = Carbon::parse((string) $value);

            return $endOfDay ? $date->endOfDay() : $date->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    private function linkFilterCallbacks(): array
    {
        return [
            'keyword' => function ($query, string $value): void {
                $query->where(function ($nestedQuery) use ($value) {
                    $nestedQuery->where('name', 'like', '%' . $value . '%')
                        ->orWhere('url', 'like', '%' . $value . '%')
                        ->orWhere('txt', 'like', '%' . $value . '%');
                });
            },
            'username' => function ($query, string $value): void {
                $query->whereHas('user', function ($userQuery) use ($value) {
                    $userQuery->where('username', 'like', '%' . $value . '%');
                });
            },
            'user_id' => fn ($query, string $value): mixed => $query->where('uid', (int) $value),
            'status' => fn ($query, string $value): mixed => $query->where('statu', (int) $value),
            'id_min' => fn ($query, string $value): mixed => $query->where('id', '>=', (int) $value),
            'id_max' => fn ($query, string $value): mixed => $query->where('id', '<=', (int) $value),
            'clicks_min' => fn ($query, string $value): mixed => $query->where('clik', '>=', (int) $value),
            'clicks_max' => fn ($query, string $value): mixed => $query->where('clik', '<=', (int) $value),
        ];
    }

    private function bannerFilterCallbacks(): array
    {
        return [
            'keyword' => function ($query, string $value): void {
                $query->where(function ($nestedQuery) use ($value) {
                    $nestedQuery->where('name', 'like', '%' . $value . '%')
                        ->orWhere('url', 'like', '%' . $value . '%')
                        ->orWhere('img', 'like', '%' . $value . '%')
                        ->orWhere('px', 'like', '%' . $value . '%');
                });
            },
            'username' => function ($query, string $value): void {
                $query->whereHas('user', function ($userQuery) use ($value) {
                    $userQuery->where('username', 'like', '%' . $value . '%');
                });
            },
            'user_id' => fn ($query, string $value): mixed => $query->where('uid', (int) $value),
            'status' => fn ($query, string $value): mixed => $query->where('statu', (int) $value),
            'id_min' => fn ($query, string $value): mixed => $query->where('id', '>=', (int) $value),
            'id_max' => fn ($query, string $value): mixed => $query->where('id', '<=', (int) $value),
            'views_min' => fn ($query, string $value): mixed => $query->where('vu', '>=', (int) $value),
            'views_max' => fn ($query, string $value): mixed => $query->where('vu', '<=', (int) $value),
            'clicks_min' => fn ($query, string $value): mixed => $query->where('clik', '>=', (int) $value),
            'clicks_max' => fn ($query, string $value): mixed => $query->where('clik', '<=', (int) $value),
            'size' => fn ($query, string $value): mixed => $query->where('px', $value),
        ];
    }

    private function smartAdFilterCallbacks(): array
    {
        return [
            'keyword' => function ($query, string $value): void {
                $query->where(function ($nestedQuery) use ($value) {
                    $nestedQuery->where('landing_url', 'like', '%' . $value . '%')
                        ->orWhere('headline_override', 'like', '%' . $value . '%')
                        ->orWhere('source_title', 'like', '%' . $value . '%')
                        ->orWhere('source_description', 'like', '%' . $value . '%')
                        ->orWhere('manual_keywords', 'like', '%' . $value . '%');
                });
            },
            'username' => function ($query, string $value): void {
                $query->whereHas('user', function ($userQuery) use ($value) {
                    $userQuery->where('username', 'like', '%' . $value . '%');
                });
            },
            'user_id' => fn ($query, string $value): mixed => $query->where('uid', (int) $value),
            'status' => fn ($query, string $value): mixed => $query->where('statu', (int) $value),
            'created_from' => function ($query, string $value): void {
                $from = $this->parseAdminInventoryDate($value);

                if ($from) {
                    $query->where('created_at', '>=', $from);
                }
            },
            'created_to' => function ($query, string $value): void {
                $to = $this->parseAdminInventoryDate($value, true);

                if ($to) {
                    $query->where('created_at', '<=', $to);
                }
            },
            'impressions_min' => fn ($query, string $value): mixed => $query->where('impressions', '>=', (int) $value),
            'impressions_max' => fn ($query, string $value): mixed => $query->where('impressions', '<=', (int) $value),
            'clicks_min' => fn ($query, string $value): mixed => $query->where('clicks', '>=', (int) $value),
            'clicks_max' => fn ($query, string $value): mixed => $query->where('clicks', '<=', (int) $value),
        ];
    }

    private function visitFilterCallbacks(): array
    {
        return [
            'keyword' => function ($query, string $value): void {
                $query->where(function ($nestedQuery) use ($value) {
                    $nestedQuery->where('name', 'like', '%' . $value . '%')
                        ->orWhere('url', 'like', '%' . $value . '%');
                });
            },
            'username' => function ($query, string $value): void {
                $query->whereHas('user', function ($userQuery) use ($value) {
                    $userQuery->where('username', 'like', '%' . $value . '%');
                });
            },
            'user_id' => fn ($query, string $value): mixed => $query->where('uid', (int) $value),
            'status' => fn ($query, string $value): mixed => $query->where('statu', (int) $value),
            'date_from' => function ($query, string $value): void {
                $from = $this->parseAdminInventoryDate($value);

                if ($from) {
                    $query->where('tims', '>=', $from->timestamp);
                }
            },
            'date_to' => function ($query, string $value): void {
                $to = $this->parseAdminInventoryDate($value, true);

                if ($to) {
                    $query->where('tims', '<=', $to->timestamp);
                }
            },
            'id_min' => fn ($query, string $value): mixed => $query->where('id', '>=', (int) $value),
            'id_max' => fn ($query, string $value): mixed => $query->where('id', '<=', (int) $value),
            'views_min' => fn ($query, string $value): mixed => $query->where('vu', '>=', (int) $value),
            'views_max' => fn ($query, string $value): mixed => $query->where('vu', '<=', (int) $value),
        ];
    }

    private function linkFilterFields(): array
    {
        return [
            ['name' => 'keyword', 'type' => 'text', 'label' => __('messages.filter_keyword')],
            ['name' => 'username', 'type' => 'text', 'label' => __('messages.username')],
            ['name' => 'user_id', 'type' => 'number', 'label' => __('messages.user_id_label'), 'min' => 1],
            ['name' => 'status', 'type' => 'select', 'label' => __('messages.status'), 'options' => [
                '' => __('messages.all'),
                '1' => __('messages.active'),
                '2' => __('messages.inactive'),
            ]],
            ['name' => 'id_min', 'type' => 'number', 'label' => __('messages.filter_id_min'), 'min' => 1],
            ['name' => 'id_max', 'type' => 'number', 'label' => __('messages.filter_id_max'), 'min' => 1],
            ['name' => 'clicks_min', 'type' => 'number', 'label' => __('messages.filter_clicks_min'), 'min' => 0],
            ['name' => 'clicks_max', 'type' => 'number', 'label' => __('messages.filter_clicks_max'), 'min' => 0],
        ];
    }

    private function bannerFilterFields(): array
    {
        return [
            ['name' => 'keyword', 'type' => 'text', 'label' => __('messages.filter_keyword')],
            ['name' => 'username', 'type' => 'text', 'label' => __('messages.username')],
            ['name' => 'user_id', 'type' => 'number', 'label' => __('messages.user_id_label'), 'min' => 1],
            ['name' => 'status', 'type' => 'select', 'label' => __('messages.status'), 'options' => [
                '' => __('messages.all'),
                '1' => 'ON',
                '2' => 'OFF',
            ]],
            ['name' => 'id_min', 'type' => 'number', 'label' => __('messages.filter_id_min'), 'min' => 1],
            ['name' => 'id_max', 'type' => 'number', 'label' => __('messages.filter_id_max'), 'min' => 1],
            ['name' => 'views_min', 'type' => 'number', 'label' => __('messages.filter_views_min'), 'min' => 0],
            ['name' => 'views_max', 'type' => 'number', 'label' => __('messages.filter_views_max'), 'min' => 0],
            ['name' => 'clicks_min', 'type' => 'number', 'label' => __('messages.filter_clicks_min'), 'min' => 0],
            ['name' => 'clicks_max', 'type' => 'number', 'label' => __('messages.filter_clicks_max'), 'min' => 0],
            ['name' => 'size', 'type' => 'select', 'label' => __('messages.size'), 'options' => [
                '' => __('messages.all'),
                '728x90' => '728x90',
                '468x60' => '468x60',
                '300x250' => '300x250',
                '160x600' => '160x600',
            ]],
        ];
    }

    private function smartAdFilterFields(): array
    {
        return [
            ['name' => 'keyword', 'type' => 'text', 'label' => __('messages.filter_keyword')],
            ['name' => 'username', 'type' => 'text', 'label' => __('messages.username')],
            ['name' => 'user_id', 'type' => 'number', 'label' => __('messages.user_id_label'), 'min' => 1],
            ['name' => 'status', 'type' => 'select', 'label' => __('messages.status'), 'options' => [
                '' => __('messages.all'),
                '1' => __('messages.active'),
                '0' => __('messages.smart_status_paused'),
                '2' => __('messages.smart_status_blocked'),
            ]],
            ['name' => 'created_from', 'type' => 'date', 'label' => __('messages.created') . ' ' . __('messages.from')],
            ['name' => 'created_to', 'type' => 'date', 'label' => __('messages.created') . ' ' . __('messages.to')],
            ['name' => 'impressions_min', 'type' => 'number', 'label' => __('messages.filter_impressions_min'), 'min' => 0],
            ['name' => 'impressions_max', 'type' => 'number', 'label' => __('messages.filter_impressions_max'), 'min' => 0],
            ['name' => 'clicks_min', 'type' => 'number', 'label' => __('messages.filter_clicks_min'), 'min' => 0],
            ['name' => 'clicks_max', 'type' => 'number', 'label' => __('messages.filter_clicks_max'), 'min' => 0],
        ];
    }

    private function visitFilterFields(): array
    {
        return [
            ['name' => 'keyword', 'type' => 'text', 'label' => __('messages.filter_keyword')],
            ['name' => 'username', 'type' => 'text', 'label' => __('messages.username')],
            ['name' => 'user_id', 'type' => 'number', 'label' => __('messages.user_id_label'), 'min' => 1],
            ['name' => 'status', 'type' => 'select', 'label' => __('messages.status'), 'options' => [
                '' => __('messages.all'),
                '1' => __('messages.active'),
                '2' => __('messages.inactive'),
            ]],
            ['name' => 'date_from', 'type' => 'date', 'label' => __('messages.date') . ' ' . __('messages.from')],
            ['name' => 'date_to', 'type' => 'date', 'label' => __('messages.date') . ' ' . __('messages.to')],
            ['name' => 'id_min', 'type' => 'number', 'label' => __('messages.filter_id_min'), 'min' => 1],
            ['name' => 'id_max', 'type' => 'number', 'label' => __('messages.filter_id_max'), 'min' => 1],
            ['name' => 'views_min', 'type' => 'number', 'label' => __('messages.filter_views_min'), 'min' => 0],
            ['name' => 'views_max', 'type' => 'number', 'label' => __('messages.filter_views_max'), 'min' => 0],
        ];
    }

    // Forum Categories
    public function forumCategories()
    {
        $categories = ForumCategory::orderBy('ordercat', 'asc')->paginate(20);
        $allCategories = ForumCategory::orderBy('ordercat', 'asc')->get();
        return view('theme::admin.forum_categories', compact('categories', 'allCategories'));
    }

    public function storeForumCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'icons' => 'required|string',
            'ordercat' => 'required|integer',
            'visibility' => 'required|integer|in:0,1,2',
            'txt' => 'nullable|string',
        ]);

        ForumCategory::create([
            'name' => $request->name,
            'icons' => $request->icons,
            'ordercat' => $request->ordercat,
            'visibility' => $request->visibility,
            'txt' => $request->input('txt') ?? '',
        ]);

        return redirect()->back()->with('success', __('messages.category_created'));
    }

    public function updateForumCategory(Request $request, $id)
    {
        $category = ForumCategory::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string',
            'icons' => 'required|string',
            'ordercat' => 'required|integer',
            'visibility' => 'required|integer|in:0,1,2',
            'txt' => 'nullable|string',
        ]);

        $category->update([
            'name' => $request->name,
            'icons' => $request->icons,
            'ordercat' => $request->ordercat,
            'visibility' => $request->visibility,
            'txt' => $request->input('txt') ?? '',
        ]);

        return redirect()->back()->with('success', __('messages.category_updated'));
    }

    public function deleteForumCategory(Request $request, $id)
    {
        $category = ForumCategory::findOrFail($id);
        
        $topicCount = ForumTopic::where('cat', $id)->count();
        
        if ($topicCount > 0) {
            $request->validate([
                'move_to_id' => 'required|integer|exists:f_cat,id|different:id',
            ], [
                'move_to_id.required' => __('messages.category_not_empty'),
                'move_to_id.different' => __('messages.select_target_category'),
            ]);

            $moveToId = $request->input('move_to_id');
            ForumTopic::where('cat', $id)->update(['cat' => $moveToId]);
        }

        $category->delete();
        
        return redirect()->back()->with('success', __('messages.category_deleted'));
    }

    public function forumSettings()
    {
        $forumSettings = ForumSettings::all();
        return view('theme::admin.forum_settings', compact('forumSettings'));
    }

    public function updateForumSettings(Request $request)
    {
        $request->validate([
            'topics_per_page' => 'required|integer|min:1|max:100',
            'max_attachments_per_topic' => 'required|integer|min:1|max:20',
            'max_attachment_size_kb' => 'required|integer|min:512|max:51200',
            'allowed_attachment_extensions' => 'required|string|max:500',
            'attachments_enabled' => 'nullable|in:1',
            'show_role_badges' => 'nullable|in:1',
        ]);

        $values = ForumSettings::normalizeIncoming($request->all());
        ForumSettings::save($values);

        return redirect()->back()->with('success', __('messages.forum_settings_updated'));
    }

    public function forumModerators()
    {
        $moderators = ForumModerator::with(['user', 'categories'])->orderBy('id', 'desc')->paginate(20);
        $users = User::orderBy('username', 'asc')->get(['id', 'username', 'email']);
        $categories = ForumCategory::orderBy('ordercat', 'asc')->get(['id', 'name']);
        $permissionKeys = $this->forumPermissionKeys();

        return view('theme::admin.forum_moderators', compact('moderators', 'users', 'categories', 'permissionKeys'));
    }

    public function storeForumModerator(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id|unique:forum_moderators,user_id',
            'is_global' => 'nullable|in:1',
            'is_active' => 'nullable|in:1',
            'permissions' => 'nullable|array',
            'permissions.*' => 'in:' . implode(',', $this->forumPermissionKeys()),
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:f_cat,id',
        ]);

        $isGlobal = $request->has('is_global');
        $categoryIds = collect($request->input('category_ids', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if (!$isGlobal && $categoryIds->isEmpty()) {
            return redirect()->back()->withErrors([
                'category_ids' => __('messages.forum_moderator_categories_required'),
            ])->withInput();
        }

        $permissions = collect($request->input('permissions', []))
            ->filter(fn ($permission) => in_array($permission, $this->forumPermissionKeys(), true))
            ->values()
            ->all();

        $moderator = ForumModerator::create([
            'user_id' => (int) $request->input('user_id'),
            'is_global' => $isGlobal ? 1 : 0,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'permissions' => $permissions,
            'created_by' => Auth::id(),
        ]);

        $moderator->categories()->sync($isGlobal ? [] : $categoryIds->all());

        return redirect()->back()->with('success', __('messages.forum_moderator_created'));
    }

    public function updateForumModerator(Request $request, $id)
    {
        $moderator = ForumModerator::with('categories')->findOrFail($id);

        $request->validate([
            'user_id' => 'required|integer|exists:users,id|unique:forum_moderators,user_id,' . $moderator->id,
            'is_global' => 'nullable|in:1',
            'is_active' => 'nullable|in:1',
            'permissions' => 'nullable|array',
            'permissions.*' => 'in:' . implode(',', $this->forumPermissionKeys()),
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:f_cat,id',
        ]);

        $isGlobal = $request->has('is_global');
        $categoryIds = collect($request->input('category_ids', []))
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if (!$isGlobal && $categoryIds->isEmpty()) {
            return redirect()->back()->withErrors([
                'category_ids' => __('messages.forum_moderator_categories_required'),
            ])->withInput();
        }

        $permissions = collect($request->input('permissions', []))
            ->filter(fn ($permission) => in_array($permission, $this->forumPermissionKeys(), true))
            ->values()
            ->all();

        $moderator->update([
            'user_id' => (int) $request->input('user_id'),
            'is_global' => $isGlobal ? 1 : 0,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'permissions' => $permissions,
        ]);

        $moderator->categories()->sync($isGlobal ? [] : $categoryIds->all());

        return redirect()->back()->with('success', __('messages.forum_moderator_updated'));
    }

    public function deleteForumModerator($id)
    {
        $moderator = ForumModerator::findOrFail($id);
        $moderator->categories()->detach();
        $moderator->delete();

        return redirect()->back()->with('success', __('messages.forum_moderator_deleted'));
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

        DirectoryCategory::create([
            'name' => $request->name,
            'sub' => (int) $request->sub,
            'ordercat' => (int) $request->ordercat,
            'statu' => (int) ($request->statu ?? 1),
            'txt' => $request->input('txt') ?? '',
            'metakeywords' => $request->input('metakeywords') ?? '',
        ]);

        return redirect()->back()->with('success', __('messages.category_created'));
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

        $category->update([
            'name' => $request->name,
            'sub' => (int) $request->sub,
            'ordercat' => (int) $request->ordercat,
            'statu' => (int) ($request->statu ?? 1),
            'txt' => $request->input('txt') ?? '',
            'metakeywords' => $request->input('metakeywords') ?? '',
        ]);

        return redirect()->back()->with('success', __('messages.category_updated'));
    }

    public function deleteDirectoryCategory($id)
    {
        $category = DirectoryCategory::findOrFail($id);
        $category->delete();
        
        return redirect()->back()->with('success', __('messages.category_deleted'));
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
        $request->validate([
            'name' => 'required|string',
            'text' => 'required|string',
        ]);

        $text = $request->input('text');
        $img = null;
        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload'), $filename);
            $img = 'upload/' . $filename;
        }
        $news = new News();
        $news->name = $request->name;
        $news->text = $text;
        $news->date = time();
        $news->img = $img;
        $news->statu = 1;
        $news->save();

        Status::create([
            'uid' => auth()->id(),
            'tp_id' => $news->id,
            's_type' => 5, // News
            'date' => time(),
            'statu' => 1,
        ]);

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
        $reportItems = $this->buildAdminReportItems($reports->getCollection());
        $reportStats = [
            'total' => Report::query()->count(),
            'pending' => Report::query()->where('statu', 1)->count(),
            'reviewed' => Report::query()->where('statu', '!=', 1)->count(),
        ];

        return view('theme::admin.reports', compact('reports', 'reportItems', 'reportStats'));
    }

    /**
     * @param \Illuminate\Support\Collection<int, \App\Models\Report> $reports
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    private function buildAdminReportItems($reports)
    {
        $directoryIds = $reports->where('s_type', 1)->pluck('tp_id')->filter()->unique()->values();
        $forumTopicIds = $reports->filter(fn (Report $report) => in_array((int) $report->s_type, [2, 4, 100], true))
            ->pluck('tp_id')->filter()->unique()->values();
        $newsIds = $reports->filter(fn (Report $report) => in_array((int) $report->s_type, [3, 5], true))
            ->pluck('tp_id')->filter()->unique()->values();
        $productIds = $reports->where('s_type', 7867)->pluck('tp_id')->filter()->unique()->values();
        $userIds = $reports->filter(fn (Report $report) => in_array((int) $report->s_type, [99, 702], true))
            ->pluck('tp_id')->filter()->unique()->values();
        $linkIds = $reports->where('s_type', 201)->pluck('tp_id')->filter()->unique()->values();
        $bannerIds = $reports->where('s_type', 202)->pluck('tp_id')->filter()->unique()->values();
        $visitIds = $reports->where('s_type', 203)->pluck('tp_id')->filter()->unique()->values();
        $smartAdIds = $reports->where('s_type', 204)->pluck('tp_id')->filter()->unique()->values();
        $knowledgebaseIds = $reports->where('s_type', 205)->pluck('tp_id')->filter()->unique()->values();
        $orderIds = $reports->filter(fn (Report $report) => in_array((int) $report->s_type, [6, 701], true))
            ->pluck('tp_id')->filter()->unique()->values();

        $directories = Directory::with('user')->whereIn('id', $directoryIds)->get()->keyBy('id');
        $forumTopics = ForumTopic::with('user')->whereIn('id', $forumTopicIds)->get()->keyBy('id');
        $newsItems = News::whereIn('id', $newsIds)->get()->keyBy('id');
        $products = Product::withoutGlobalScope('store')->with('user')->whereIn('id', $productIds)->get()->keyBy('id');
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');
        $links = Link::with('user')->whereIn('id', $linkIds)->get()->keyBy('id');
        $banners = Banner::with('user')->whereIn('id', $bannerIds)->get()->keyBy('id');
        $visits = Visit::with('user')->whereIn('id', $visitIds)->get()->keyBy('id');
        $smartAds = SmartAd::with('user')->whereIn('id', $smartAdIds)->get()->keyBy('id');
        $knowledgebaseItems = Option::query()
            ->where('o_type', 'knowledgebase')
            ->whereIn('id', $knowledgebaseIds)
            ->get()
            ->keyBy('id');
        $knowledgebaseOwnerIds = $knowledgebaseItems->pluck('o_parent')
            ->filter(fn ($value) => (int) $value > 0)
            ->unique()
            ->values();
        $knowledgebaseOwners = User::whereIn('id', $knowledgebaseOwnerIds)->get()->keyBy('id');
        $orders = OrderRequest::with('user')->whereIn('id', $orderIds)->get()->keyBy('id');

        return $reports->map(function (Report $report) use (
            $directories,
            $forumTopics,
            $newsItems,
            $products,
            $users,
            $links,
            $banners,
            $visits,
            $smartAds,
            $knowledgebaseItems,
            $knowledgebaseOwners,
            $orders
        ) {
            $target = null;
            $targetUser = null;
            $targetTitle = null;
            $targetLabel = null;
            $targetIcon = 'feather-flag';
            $previewUrl = null;
            $previewLabel = __('messages.preview');

            switch ((int) $report->s_type) {
                case 1:
                    $target = $directories->get($report->tp_id);
                    $targetUser = $target?->user;
                    $targetTitle = $target?->name;
                    $targetLabel = __('messages.directory');
                    $targetIcon = 'feather-globe';
                    $previewUrl = $target ? route('directory.show.short', $target->id) : null;
                    break;

                case 2:
                case 4:
                case 100:
                    $target = $forumTopics->get($report->tp_id);
                    $targetUser = $target?->user;
                    $targetTitle = $target?->name;
                    $targetLabel = __('messages.forum');
                    $targetIcon = 'feather-message-square';
                    $previewUrl = $target ? route('forum.topic', $target->id) : null;
                    break;

                case 3:
                case 5:
                    $target = $newsItems->get($report->tp_id);
                    $targetTitle = $target?->name;
                    $targetLabel = __('messages.news');
                    $targetIcon = 'feather-file-text';
                    $previewUrl = $target ? route('news.show', $target->id) : null;
                    break;

                case 6:
                case 701:
                    $target = $orders->get($report->tp_id);
                    $targetUser = $target?->user;
                    $targetTitle = $target?->title;
                    $targetLabel = __('messages.order_request');
                    $targetIcon = 'feather-briefcase';
                    $previewUrl = $target ? route('orders.show', $target->id) : null;
                    break;

                case 99:
                case 702:
                    $target = $users->get($report->tp_id);
                    $targetUser = $target;
                    $targetTitle = $target?->username;
                    $targetLabel = __('messages.user');
                    $targetIcon = 'feather-user';
                    $previewUrl = $target ? route('profile.show', $target->username) : null;
                    $previewLabel = __('messages.view_profile');
                    break;

                case 201:
                    $target = $links->get($report->tp_id);
                    $targetUser = $target?->user;
                    $targetTitle = $target?->name;
                    $targetLabel = __('messages.links');
                    $targetIcon = 'feather-link';
                    $previewUrl = $target?->url;
                    break;

                case 202:
                    $target = $banners->get($report->tp_id);
                    $targetUser = $target?->user;
                    $targetTitle = $target?->name;
                    $targetLabel = __('messages.bannads');
                    $targetIcon = 'feather-image';
                    $previewUrl = $target ? route('admin.banners.edit', $target->id) : null;
                    $previewLabel = __('messages.view_reported');
                    break;

                case 203:
                    $target = $visits->get($report->tp_id);
                    $targetUser = $target?->user;
                    $targetTitle = $target?->name;
                    $targetLabel = __('messages.visits');
                    $targetIcon = 'feather-navigation';
                    $previewUrl = $target?->url;
                    break;

                case 204:
                    $target = $smartAds->get($report->tp_id);
                    $targetUser = $target?->user;
                    $targetTitle = $target?->displayTitle();
                    $targetLabel = __('messages.smart_ads');
                    $targetIcon = 'feather-layout';
                    $previewUrl = $target ? route('admin.smart_ads.edit', $target->id) : null;
                    $previewLabel = __('messages.view_reported');
                    break;

                case 205:
                    $target = $knowledgebaseItems->get($report->tp_id);
                    $targetUser = ($target && (int) $target->o_parent > 0) ? $knowledgebaseOwners->get((int) $target->o_parent) : null;
                    $targetTitle = $target ? $target->name : null;
                    $targetLabel = __('messages.knowledgebase');
                    $targetIcon = 'feather-book-open';
                    $previewUrl = $target ? route('kb.show', ['name' => $target->o_mode, 'article' => $target->name]) : null;
                    break;

                case 7867:
                    $target = $products->get($report->tp_id);
                    $targetUser = $target?->user;
                    $targetTitle = $target?->name;
                    $targetLabel = __('messages.store');
                    $targetIcon = 'feather-shopping-bag';
                    $previewUrl = $target ? route('store.show', $target->name) : null;
                    break;
            }

            return [
                'id' => $report->id,
                'reason' => $report->txt,
                'is_pending' => (int) $report->statu === 1,
                'status_label' => (int) $report->statu === 1 ? __('messages.pending') : __('messages.reviewed'),
                'status_modifier' => (int) $report->statu === 1 ? 'pending' : 'reviewed',
                'reporter' => $report->reporter,
                'reporter_profile_url' => $report->reporter ? route('profile.show', $report->reporter->username) : null,
                'reporter_message_url' => $report->reporter ? route('messages.create', ['recipient' => $report->reporter->username]) : null,
                'target_missing' => !$target,
                'target_title' => $targetTitle,
                'target_label' => $targetLabel,
                'target_icon' => $targetIcon,
                'preview_url' => $previewUrl,
                'preview_label' => $previewLabel,
                'target_user' => $targetUser,
                'target_user_profile_url' => $targetUser ? route('profile.show', $targetUser->username) : null,
                'target_user_message_url' => $targetUser ? route('messages.create', ['recipient' => $targetUser->username]) : null,
                'target_user_admin_url' => $targetUser ? route('admin.users.edit', $targetUser->id) : null,
            ];
        })->values();
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

    /**
     * Get all widget places including dynamic pages.
     */
    private function getWidgetPlaces()
    {
        $places = [
            '1' => 'portal_left',
            '2' => 'portal_right',
            '3' => 'forum_left',
            '4' => 'forum_right',
            '5' => 'directory_left',
            '6' => 'directory_right',
            '7' => 'profile_left',
            '8' => 'profile_right',
        ];

        // Add dynamic page places (safely handle missing table)
        if (\Illuminate\Support\Facades\Schema::hasTable('pages')) {
            $pages = Page::orderBy('id', 'asc')->get();
            foreach ($pages as $page) {
                $places[(string) $page->getLeftPlaceId()] = __('messages.page_left') . ': ' . $page->title;
                $places[(string) $page->getRightPlaceId()] = __('messages.page_right') . ': ' . $page->title;
            }
        }

        return $places;
    }

    /**
     * Get all allowed place IDs as a comma-separated string.
     */
    private function getAllowedPlaceIds($type = 'widget_html')
    {
        $places = $this->getWidgetPlaces();
        $ids = array_keys($places);

        // For non-HTML widgets, exclude profile places (7, 8)
        if ($type !== 'widget_html') {
            $ids = array_filter($ids, fn($id) => !in_array((int)$id, [7, 8], true));
        }

        return $ids;
    }

    private function forumPermissionKeys(): array
    {
        return self::FORUM_PERMISSION_KEYS;
    }

    public function widgets()
    {
        $widgets = Option::where('o_type', 'box_widget')
            ->orderBy('o_parent', 'asc')
            ->orderBy('o_order', 'asc')
            ->get();
        $places = $this->getWidgetPlaces();
        return view('theme::admin.widgets', compact('widgets', 'places'));
    }

    public function widgetForm(Request $request)
    {
        $type = $request->get('type');
        $allowedTypes = ['widget_html', 'widget_members', 'widget_stats_box', 'widget_forum_latest', 'widget_news_latest', 'widget_points_leaderboard', 'widget_store_latest', 'widget_directory_latest', 'widget_orders_latest', 'widget_badges_showcase', 'widget_quests_daily'];
        if (!in_array($type, $allowedTypes, true)) {
            abort(404);
        }
        $places = $this->getWidgetPlaces();
        $allowedPlaceIds = array_map('strval', $this->getAllowedPlaceIds($type));
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
        $places = $this->getWidgetPlaces();
        $allowedPlaceIds = array_map('strval', $this->getAllowedPlaceIds($widget->o_mode));
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
        $allowedPlaceIds = implode(',', $this->getAllowedPlaceIds($type));
        $request->validate([
            'name' => 'required|string',
            'o_parent' => 'required|integer|in:' . $allowedPlaceIds,
            'o_order' => 'required|integer',
            'o_valuer' => 'nullable|string', // Content
            'o_mode' => 'required|in:widget_html,widget_members,widget_stats_box,widget_forum_latest,widget_news_latest,widget_points_leaderboard,widget_store_latest,widget_directory_latest,widget_orders_latest,widget_badges_showcase,widget_quests_daily',
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
        $allowedPlaceIds = implode(',', $this->getAllowedPlaceIds($widget->o_mode));
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

    public function editProduct($id)
    {
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->findOrFail($id);
        $typeOption = Option::where('o_type', 'store_type')->where('o_parent', $product->id)->first();
        $files = ProductFile::where('o_parent', $product->id)->orderBy('id', 'desc')->get();
        $latestFile = $files->first();
        $topic = null;
        if ($typeOption && $typeOption->o_order) {
            $topic = ForumTopic::find($typeOption->o_order);
        }
        $storeCategories = Option::where('o_type', 'storecat')->orderBy('id')->get();
        $isSuspended = Option::where('o_type', 'store_status')
            ->where('o_parent', $product->id)
            ->where('name', 'suspended')
            ->exists();

        return view('theme::admin.product_edit', compact(
            'product', 'typeOption', 'files', 'latestFile', 'topic', 'storeCategories', 'isSuspended'
        ));
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->findOrFail($id);

        $request->validate([
            'pname'   => ['required', 'string', 'min:3', 'max:35'],
            'desc'    => ['required', 'string', 'min:10', 'max:2400'],
            'img'     => ['nullable', 'string'],
            'pts'     => ['required', 'integer', 'min:0', 'max:999999'],
            'cat_s'   => ['nullable', 'string'],
            'txt'     => ['nullable', 'string'],
            'vnbr'    => ['nullable', 'string', 'min:2', 'max:12', 'regex:/^[-a-zA-Z0-9.]+$/'],
            'linkzip' => ['nullable', 'string'],
        ]);

        // Update product core fields
        $updateData = [
            'name'     => $request->pname,
            'o_valuer' => $request->desc,
            'o_order'  => (int) $request->pts,
        ];
        if ($request->filled('img')) {
            $updateData['o_mode'] = $request->img;
        }
        $product->update($updateData);

        // Update category option
        if ($request->filled('cat_s')) {
            $typeOption = Option::where('o_type', 'store_type')->where('o_parent', $product->id)->first();
            if ($typeOption) {
                $typeOption->update(['name' => $request->cat_s]);
            }
        }

        // Update forum topic body text
        $typeOption = Option::where('o_type', 'store_type')->where('o_parent', $product->id)->first();
        if ($typeOption && $typeOption->o_order && $request->filled('txt')) {
            $topic = ForumTopic::find($typeOption->o_order);
            if ($topic) {
                $topic->update(['txt' => $request->txt]);
            }
        }

        // Optional: add new file version
        if ($request->filled('vnbr') && $request->filled('linkzip')) {
            $fileOption = ProductFile::create([
                'name'     => $request->vnbr,
                'o_valuer' => $request->desc,
                'o_type'   => 'store_file',
                'o_parent' => $product->id,
                'o_order'  => 0,
                'o_mode'   => $request->linkzip,
            ]);
            $hash = hash('crc32', $request->linkzip . $fileOption->id);
            \App\Models\Short::create([
                'uid'     => Auth::id(),
                'url'     => $request->linkzip,
                'sho'     => $hash,
                'clik'    => 0,
                'sh_type' => 7867,
                'tp_id'   => $fileOption->id,
            ]);
        }

        return redirect()->route('admin.products')->with('success', __('messages.product_updated') ?? 'Product updated successfully.');
    }

    public function suspendProduct(Request $request, $id)
    {
        $product = Product::withoutGlobalScope('store')->where('o_type', 'store')->findOrFail($id);

        $existing = Option::where('o_type', 'store_status')
            ->where('o_parent', $product->id)
            ->where('name', 'suspended')
            ->first();

        if ($existing) {
            // Unsuspend
            $existing->delete();
            $notifMsg = __('messages.product_reactivated') ?? 'Your product has been reactivated.';
            $successMsg = __('messages.product_unsuspended') ?? 'Product unsuspended.';
        } else {
            // Suspend
            Option::create([
                'name'     => 'suspended',
                'o_valuer' => '1',
                'o_type'   => 'store_status',
                'o_parent' => $product->id,
                'o_order'  => 0,
                'o_mode'   => time(),
            ]);
            $notifMsg = __('messages.product_suspended') ?? 'Your product has been suspended by the admin.';
            $successMsg = __('messages.product_suspended_ok') ?? 'Product suspended.';
        }

        // Notify product owner
        Notification::create([
            'uid'   => $product->o_parent,
            'name'  => $notifMsg,
            'nurl'  => 'store/' . $product->name,
            'logo'  => 'store',
            'time'  => time(),
            'state' => 1,
        ]);

        return redirect()->route('admin.products')->with('success', $successMsg);
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
        
        $this->maintenanceMode->enable(Auth::user(), 'plugin_activation');
        try {
            if ($pluginManager->activate($request->slug)) {
                $this->maintenanceMode->disable(Auth::user(), 'plugin_activation_success');
                return redirect()->back()->with('success', __('messages.plugin_activated_successfully'));
            }
            $this->maintenanceMode->disable(Auth::user(), 'plugin_activation_failed');
            return redirect()->back()->with('error', __('messages.plugin_activation_failed'));
        } catch (\Throwable $e) {
            $this->maintenanceMode->disable(Auth::user(), 'plugin_activation_error');
            return redirect()->back()->with('error', __('messages.error_prefix') . $e->getMessage());
        }
    }

    public function deactivatePlugin(Request $request, PluginManager $pluginManager)
    {
        $request->validate(['slug' => 'required|string']);
        
        $this->maintenanceMode->enable(Auth::user(), 'plugin_deactivation');
        try {
            if ($pluginManager->deactivate($request->slug)) {
                $this->maintenanceMode->disable(Auth::user(), 'plugin_deactivation_success');
                return redirect()->back()->with('success', __('messages.plugin_deactivated_successfully'));
            }
            $this->maintenanceMode->disable(Auth::user(), 'plugin_deactivation_failed');
            return redirect()->back()->with('error', __('messages.plugin_deactivation_failed'));
        } catch (\Throwable $e) {
            $this->maintenanceMode->disable(Auth::user(), 'plugin_deactivation_error');
            return redirect()->back()->with('error', __('messages.error_prefix') . $e->getMessage());
        }
    }

    public function deletePlugin(Request $request, PluginManager $pluginManager)
    {
        $request->validate(['slug' => 'required|string']);

        $result = $pluginManager->delete($request->slug);

        if ($result === true) {
            return redirect()->back()->with('success', __('messages.plugin_deleted_successfully'));
        }

        return redirect()->back()->with('error', is_string($result) ? $result : __('messages.plugin_delete_failed'));
    }

    public function uploadPlugin(Request $request, PluginManager $pluginManager)
    {
        $request->validate([
            'plugin_zip' => 'required|file|mimes:zip',
        ]);

        $this->maintenanceMode->enable(Auth::user(), 'plugin_upload');
        try {
            $result = $pluginManager->install($request->file('plugin_zip'));

            if ($result === true) {
                $this->maintenanceMode->disable(Auth::user(), 'plugin_upload_success');
                return redirect()->back()->with('success', __('messages.plugin_installed_successfully'));
            }

            $this->maintenanceMode->disable(Auth::user(), 'plugin_upload_failed');
            return redirect()->back()->with('error', $result);
        } catch (\Throwable $e) {
            $this->maintenanceMode->disable(Auth::user(), 'plugin_upload_error');
            return redirect()->back()->with('error', __('messages.error_prefix') . $e->getMessage());
        }
    }

    public function upgradePlugin(Request $request, PluginManager $pluginManager)
    {
        $request->validate(['slug' => 'required|string']);

        $this->maintenanceMode->enable(Auth::user(), 'plugin_upgrade');
        try {
            $result = $pluginManager->upgrade($request->slug);

            if ($result === true) {
                $this->maintenanceMode->disable(Auth::user(), 'plugin_upgrade_success');
                return redirect()->back()->with('success', __('messages.plugin_upgraded_successfully'));
            }

            $this->maintenanceMode->disable(Auth::user(), 'plugin_upgrade_failed');
            return redirect()->back()->with('error', $result);
        } catch (\Throwable $e) {
            $this->maintenanceMode->disable(Auth::user(), 'plugin_upgrade_error');
            return redirect()->back()->with('error', __('messages.error_prefix') . $e->getMessage());
        }
    }

    public function pluginThumbnail($slug, PluginManager $pluginManager)
    {
        $plugins = $pluginManager->getAllPlugins();
        $plugin = collect($plugins)->where('slug', $slug)->first();

        if ($plugin && isset($plugin['thumbnail'])) {
            $path = $plugin['path'] . '/' . $plugin['thumbnail'];
            if (File::exists($path)) {
                return response()->file($path);
            }
        }

        abort(404);
    }

    // Themes Management
    public function themes(ThemeManager $themeManager)
    {
        $themes = $themeManager->getAllThemes();
        $updates = $themeManager->checkForUpdates();
        return view('theme::admin.themes.index', compact('themes', 'updates'));
    }

    public function themeThumbnail($slug, ThemeManager $themeManager)
    {
        $themes = $themeManager->getAllThemes();
        $theme = collect($themes)->where('slug', $slug)->first();

        if ($theme && isset($theme['thumbnail'])) {
            $path = $theme['path'] . '/' . $theme['thumbnail'];
            if (File::exists($path)) {
                return response()->file($path);
            }
        }

        abort(404);
    }

    public function activateTheme(Request $request, ThemeManager $themeManager)
    {
        $request->validate([
            'slug' => 'required|string'
        ]);

        $this->maintenanceMode->enable(Auth::user(), 'theme_activation');
        try {
            if ($themeManager->activate($request->slug)) {
                $this->maintenanceMode->disable(Auth::user(), 'theme_activation_success');
                return redirect()->back()->with('success', __('messages.theme_activated_successfully'));
            }

            $this->maintenanceMode->disable(Auth::user(), 'theme_activation_failed');
            return redirect()->back()->with('error', __('messages.theme_activation_failed'));
        } catch (\Throwable $e) {
            $this->maintenanceMode->disable(Auth::user(), 'theme_activation_error');
            return redirect()->back()->with('error', __('messages.error_prefix') . $e->getMessage());
        }
    }

    public function upgradeTheme(Request $request, ThemeManager $themeManager)
    {
        $request->validate(['slug' => 'required|string']);

        $this->maintenanceMode->enable(Auth::user(), 'theme_upgrade');
        try {
            $result = $themeManager->upgrade($request->slug);

            if ($result === true) {
                $this->maintenanceMode->disable(Auth::user(), 'theme_upgrade_success');
                return redirect()->back()->with('success', __('messages.theme_upgraded_successfully'));
            }

            $this->maintenanceMode->disable(Auth::user(), 'theme_upgrade_failed');
            return redirect()->back()->with('error', $result);
        } catch (\Throwable $e) {
            $this->maintenanceMode->disable(Auth::user(), 'theme_upgrade_error');
            return redirect()->back()->with('error', __('messages.error_prefix') . $e->getMessage());
        }
    }

    private function validatedBannerSize(null|string|int $value): string
    {
        $bannerSize = BannerSizeCatalog::normalize($value);

        if ($bannerSize === null) {
            throw ValidationException::withMessages([
                'px' => 'Invalid banner size selected.',
            ]);
        }
        return $bannerSize;
    }

    public function maintenance()
    {
        $maintenanceSettings = $this->maintenanceMode->settings();
        $maintenanceUsers = User::query()->whereIn('id', array_filter([
            $maintenanceSettings['enabled_by'] ?? 0,
            $maintenanceSettings['last_changed_by'] ?? 0,
        ]))->get()->keyBy('id');

        return view('theme::admin.maintenance', compact('maintenanceSettings', 'maintenanceUsers'));
    }

    public function updateMaintenanceSettings(Request $request)
    {
        $validated = $request->validate([
            'maintenance_enabled' => ['nullable', 'boolean'],
            'maintenance_message' => ['nullable', 'string', 'max:5000'],
            'maintenance_logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif,svg', 'max:2048'],
            'remove_maintenance_logo' => ['nullable', 'boolean'],
        ]);

        $this->maintenanceMode->saveAdminSettings([
            'enabled' => $request->boolean('maintenance_enabled'),
            'message' => $validated['maintenance_message'] ?? '',
            'remove_logo' => $request->boolean('remove_maintenance_logo'),
        ], $request->file('maintenance_logo'), $request->user());

        return redirect()->route('admin.maintenance')->with('success', __('messages.maintenance_settings_saved'));
    }

    public function clearCache()
    {
        $this->maintenanceMode->enable(Auth::user(), 'clear_cache');
        try {
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('cache:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            
            $this->maintenanceMode->disable(Auth::user(), 'clear_cache_success');
            return redirect()->back()->with('success', __('Caches cleared successfully.'));
        } catch (\Throwable $e) {
            $this->maintenanceMode->disable(Auth::user(), 'clear_cache_error');
            return redirect()->back()->with('error', __('Failed to clear caches: ') . $e->getMessage());
        }
    }

    public function runMigrations()
    {
        $this->maintenanceMode->enable(Auth::user(), 'run_migrations');
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $output = \Illuminate\Support\Facades\Artisan::output();
            
            $this->gamification->repairQuestData();
            
            $this->maintenanceMode->disable(Auth::user(), 'run_migrations_success');
            return redirect()->back()->with('success', __('Migrations ran successfully: ') . $output);
        } catch (\Throwable $e) {
            $this->maintenanceMode->disable(Auth::user(), 'run_migrations_error');
            return redirect()->back()->with('error', __('Failed to run migrations: ') . $e->getMessage());
        }
    }

    public function dbRepair()
    {
        $this->maintenanceMode->enable(Auth::user(), 'db_repair');
        try {
            $tables = DB::getSchemaBuilder()->getTables();
            $results = [];
            
            foreach ($tables as $table) {
                // $table is an array or object depending on Laravel version, usually has 'name'
                $tableName = is_array($table) ? ($table['name'] ?? null) : ($table->name ?? null);
                
                if (!$tableName) {
                    // Fallback for older Laravel or raw SQL results
                    $tableArray = (array) $table;
                    $tableName = reset($tableArray);
                }

                if ($tableName) {
                    // Repair
                    DB::statement("REPAIR TABLE `{$tableName}`");
                    // Optimize
                    DB::statement("OPTIMIZE TABLE `{$tableName}`");
                    $results[] = $tableName;
                }
            }
            
            $this->gamification->repairQuestData();
            
            $this->maintenanceMode->disable(Auth::user(), 'db_repair_success');
            return redirect()->back()->with('success', __('Database maintenance completed for :count tables.', ['count' => count($results)]));
        } catch (\Throwable $e) {
            $this->maintenanceMode->disable(Auth::user(), 'db_repair_error');
            return redirect()->back()->with('error', __('Failed to perform database maintenance: ') . $e->getMessage());
        }
    }
}
