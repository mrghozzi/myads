<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GroupAccessService;
use App\Services\StatusActivityService;
use App\Models\Status;
use App\Models\User;
use App\Models\Like;
use App\Models\ForumTopic;
use App\Models\Directory;
use App\Models\Product;
use App\Models\News;
use App\Models\Option;
use App\Services\KnowledgebaseCommunityService;

class PortalController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $search = trim((string) $request->query('search', ''));
        $user = Auth::user();
        $activityService = app(StatusActivityService::class);
        $hiddenDirectoryStatusIds = $activityService->hiddenDirectoryStatusIds();

        $configuredFeedMode = \App\Support\CommunityFeedSettings::get('feed_mode', 'smart');
        $requestedMode = $request->query('mode', $configuredFeedMode);

        $this->seo([
            'scope_key' => 'portal',
            'resource_title' => __('messages.seo_portal_title'),
            'description' => $search !== ''
                ? __('messages.seo_portal_search_description')
                : __('messages.seo_portal_description'),
            'indexable' => $search === '' && $filter === 'all',
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.seo_portal_title'), 'url' => route('portal.index')],
            ],
        ]);

        // ── Search ───────────────────────────────────────────────
        if ($search !== '') {
            $searchedUsers         = collect();
            $searchedStatuses      = collect();
            $searchedGroups        = collect();
            $searchedCommentsForum = collect();
            $searchedCommentsDir   = collect();
            $searchedProducts      = collect();

            try {
                // User search with limit for performance
                $searchedUsers = User::where('username', 'LIKE', "%{$search}%")->limit(30)->get();
            } catch (\Throwable $e) {
                \Log::warning('Search users failed: ' . $e->getMessage());
            }

            try {
                $topicIds = ForumTopic::visible($user)
                    ->where(function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")->orWhere('txt', 'LIKE', "%{$search}%");
                    })
                    ->limit(50)
                    ->pluck('id');

                $dirIds = Directory::where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")->orWhere('txt', 'LIKE', "%{$search}%");
                })
                ->limit(50)
                ->pluck('id');

                $newsIds = News::where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")->orWhere('text', 'LIKE', "%{$search}%");
                })
                ->limit(50)
                ->pluck('id');

                $kbIds = Option::where('o_type', 'knowledgebase')
                    ->where('o_order', 0)
                    ->where(function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")->orWhere('o_valuer', 'LIKE', "%{$search}%");
                    })
                    ->limit(50)
                    ->pluck('id');

                $searchedStatuses = Status::visible()
                    ->when(!empty($hiddenDirectoryStatusIds), fn ($query) => $query->whereNotIn('id', $hiddenDirectoryStatusIds))
                    ->where(function ($q) use ($topicIds, $dirIds, $newsIds, $kbIds, $search) {
                        $hasCondition = false;
                        if ($topicIds->isNotEmpty()) {
                            $q->where(function ($q1) use ($topicIds) {
                                $q1->whereIn('tp_id', $topicIds)->whereIn('s_type', [2, 4, 100, 10, 11, 12, 13, 14]);
                            });
                            $hasCondition = true;
                        }

                        if ($dirIds->isNotEmpty()) {
                            if ($hasCondition) {
                                $q->orWhere(function ($q2) use ($dirIds) {
                                    $q2->whereIn('tp_id', $dirIds)->where('s_type', 1);
                                });
                            } else {
                                $q->where(function ($q2) use ($dirIds) {
                                    $q2->whereIn('tp_id', $dirIds)->where('s_type', 1);
                                });
                                $hasCondition = true;
                            }
                        }

                        if ($newsIds->isNotEmpty()) {
                            if ($hasCondition) {
                                $q->orWhere(function ($q3) use ($newsIds) {
                                    $q3->whereIn('tp_id', $newsIds)->where('s_type', 5);
                                });
                            } else {
                                $q->where(function ($q3) use ($newsIds) {
                                    $q3->whereIn('tp_id', $newsIds)->where('s_type', 5);
                                });
                                $hasCondition = true;
                            }
                        }

                        if ($kbIds->isNotEmpty()) {
                            if ($hasCondition) {
                                $q->orWhere(function ($q4) use ($kbIds) {
                                    $q4->whereIn('tp_id', $kbIds)->where('s_type', KnowledgebaseCommunityService::STATUS_TYPE);
                                });
                            } else {
                                $q->where(function ($q4) use ($kbIds) {
                                    $q4->whereIn('tp_id', $kbIds)->where('s_type', KnowledgebaseCommunityService::STATUS_TYPE);
                                });
                                $hasCondition = true;
                            }
                        }

                        if ($hasCondition) {
                            $q->orWhere('txt', 'LIKE', "%{$search}%");
                        } else {
                            $q->where('txt', 'LIKE', "%{$search}%");
                        }
                    })
                    ->orderBy('date', 'desc')
                    ->limit(50)
                    ->get();

                $activityService->decorateMany($searchedStatuses);
            } catch (\Throwable $e) {
                \Log::warning('Search statuses failed: ' . $e->getMessage());
            }

            try {
                if (\App\Support\GroupSettings::isEnabled()) {
                    $searchedGroups = \App\Models\Group::where('status', \App\Models\Group::STATUS_ACTIVE)
                        ->where(function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%")
                              ->orWhere('short_description', 'LIKE', "%{$search}%")
                              ->orWhere('description', 'LIKE', "%{$search}%");
                        })
                        ->limit(30)
                        ->get();
                }
            } catch (\Throwable $e) {
                \Log::warning('Search groups failed: ' . $e->getMessage());
            }

            try {
                $searchedCommentsForum = \App\Models\ForumComment::visible()
                    ->whereHas('topic', fn ($query) => $query->visible($user))
                    ->where('txt', 'LIKE', "%{$search}%")
                    ->with('user')
                    ->orderBy('date', 'desc')
                    ->limit(30)
                    ->get();
            } catch (\Throwable $e) {
                \Log::warning('Search forum comments failed: ' . $e->getMessage());
            }

            try {
                $searchedCommentsDir = \App\Models\Option::where('o_type', '=', 'd_coment')
                    ->visible(null, 'o_order')
                    ->where('o_valuer', 'LIKE', "%{$search}%")
                    ->limit(30)
                    ->get();
            } catch (\Throwable $e) {
                \Log::warning('Search directory comments failed: ' . $e->getMessage());
            }

            try {
                $searchedProducts = Product::visible()
                    ->where(function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                          ->orWhere('o_valuer', 'LIKE', "%{$search}%");
                    })
                    ->limit(30)
                    ->get();
            } catch (\Throwable $e) {
                \Log::warning('Search products failed: ' . $e->getMessage());
            }

            if ($request->ajax() || $request->wantsJson()) {
                $html = view('theme::portal.partials.search_results', compact(
                    'filter', 'search',
                    'searchedUsers', 'searchedStatuses', 'searchedGroups',
                    'searchedCommentsForum', 'searchedCommentsDir', 'searchedProducts'
                ))->render();

                $totalCount = $searchedStatuses->count() + $searchedUsers->count() + $searchedGroups->count()
                    + $searchedProducts->count() + $searchedCommentsForum->count() + $searchedCommentsDir->count();

                return response()->json([
                    'type' => 'search',
                    'html' => $html,
                    'total_count' => $totalCount,
                    'search' => $search,
                ]);
            }

            $portalStats = \Illuminate\Support\Facades\Cache::remember('portal_stats_summary', 300, function () {
                try {
                    return [
                        'members_count' => User::count(),
                        'posts_today' => Status::where('date', '>=', strtotime('today'))->count(),
                        'feed_mode' => \App\Support\CommunityFeedSettings::get('feed_mode', 'smart'),
                    ];
                } catch (\Throwable) {
                    return [
                        'members_count' => 0,
                        'posts_today' => 0,
                        'feed_mode' => 'smart',
                    ];
                }
            });

            return view('theme::portal.index', compact(
                'filter', 'search', 'portalStats', 'requestedMode',
                'searchedUsers', 'searchedStatuses', 'searchedGroups',
                'searchedCommentsForum', 'searchedCommentsDir', 'searchedProducts'
            ));
        }

        // ── Feed (no search) ─────────────────────────────────────
        $pageSize = (int) \App\Support\CommunityFeedSettings::get('feed_page_size', 20);
        $configuredFeedMode = \App\Support\CommunityFeedSettings::get('feed_mode', 'smart');
        $requestedMode = $request->query('mode', $configuredFeedMode);

        try {
            if ($user && $filter === 'me') {
                // Chronological feed of followed users
                $followingIds   = Like::where('uid', $user->id)->where('type', 1)->pluck('sid')->toArray();
                $followingIds[] = $user->id;
                $followingIds[] = 1;

                $activities = Status::visible()
                    ->where('date', '<=', time())
                    ->when(!empty($hiddenDirectoryStatusIds), fn ($query) => $query->whereNotIn('id', $hiddenDirectoryStatusIds))
                    ->whereIn('uid', $followingIds)
                    ->orderBy('date', 'desc')
                    ->paginate($pageSize);

                $activityService->decorateMany($activities);
            } elseif ($user && $filter === 'groups') {
                if (!\App\Support\GroupSettings::isEnabled()) {
                    if ($request->ajax()) {
                        return response()->json([
                            'type' => 'feed',
                            'html' => '',
                            'next_page_url' => null,
                            'has_more' => false,
                            'total' => 0,
                            'filter' => $filter,
                        ]);
                    }
                    return redirect()->route('portal.index');
                }

                $activities = Status::visible($user)
                    ->where('date', '<=', time())
                    ->whereNotNull('group_id')
                    ->when(!empty($hiddenDirectoryStatusIds), fn ($query) => $query->whereNotIn('id', $hiddenDirectoryStatusIds))
                    ->tap(fn ($query) => app(\App\Services\GroupAccessService::class)->applyMyGroupsScope($query, $user))
                    ->orderBy('date', 'desc')
                    ->paginate($pageSize);

                $activityService->decorateMany($activities);
            } elseif ($filter === 'media') {
                // Feed filtered to rich media types
                $mediaTypes = [4, 10, 11, 12, 13, 14, 7867];
                $activities = Status::visible($user)
                    ->where('date', '<=', time())
                    ->whereIn('s_type', $mediaTypes)
                    ->when(!empty($hiddenDirectoryStatusIds), fn ($query) => $query->whereNotIn('id', $hiddenDirectoryStatusIds))
                    ->orderBy('date', 'desc')
                    ->paginate($pageSize);

                $activityService->decorateMany($activities);
            } elseif ($requestedMode === 'simple' || $requestedMode === 'latest') {
                // Chronological latest feed
                $activities = Status::visible($user)
                    ->where('date', '<=', time())
                    ->when(!empty($hiddenDirectoryStatusIds), fn ($query) => $query->whereNotIn('id', $hiddenDirectoryStatusIds))
                    ->orderBy('date', 'desc')
                    ->paginate($pageSize);

                $activityService->decorateMany($activities);
            } else {
                // Smart ranked feed
                $page       = (int) $request->query('page', 1);
                $activities = \App\Services\FeedService::getRankedFeed(
                    $user ? $user->id : null,
                    $page,
                    $pageSize
                );

                $activityService->decorateMany($activities);
            }
        } catch (\Throwable $e) {
            \Log::error('Portal feed render failed', [
                'filter' => $filter,
                'search' => $search,
                'user_id' => $user?->id,
                'message' => $e->getMessage(),
            ]);
            $activities = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $pageSize);
        }

        if ($request->ajax() || $request->wantsJson()) {
            $html = view('theme::partials.ajax.activities', compact('activities', 'filter'))->render();
            return response()->json([
                'type'          => 'feed',
                'html'          => $html,
                'next_page_url' => $activities->nextPageUrl(),
                'has_more'      => $activities->hasMorePages(),
                'total'         => $activities->total(),
                'filter'        => $filter,
                'feed_mode'     => $requestedMode,
            ]);
        }

        // Lightweight portal stats cached for 5 minutes (low server load)
        $portalStats = \Illuminate\Support\Facades\Cache::remember('portal_stats_summary', 300, function () {
            try {
                return [
                    'members_count' => User::count(),
                    'posts_today' => Status::where('date', '>=', strtotime('today'))->count(),
                    'feed_mode' => \App\Support\CommunityFeedSettings::get('feed_mode', 'smart'),
                ];
            } catch (\Throwable) {
                return [
                    'members_count' => 0,
                    'posts_today' => 0,
                    'feed_mode' => 'smart',
                ];
            }
        });

        return view('theme::portal.index', compact('activities', 'filter', 'search', 'portalStats', 'requestedMode'));
    }

    public function share(Request $request)
    {
        $this->seo([
            'scope_key' => 'share',
            'resource_title' => __('messages.share_to_community') ?? 'Share to Community',
            'description' => __('messages.share_page_description', ['site' => \App\Models\Setting::first()->titer ?? 'MYADS']) ?? 'Share content with the ' . (\App\Models\Setting::first()->titer ?? 'MYADS') . ' community.',
            'indexable' => false,
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.community'), 'url' => route('portal.index')],
                ['name' => __('messages.share') ?? 'Share', 'url' => route('portal.share')],
            ],
        ]);

        return view('theme::portal.share');
    }
}
