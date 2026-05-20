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
        $search = $request->query('search', '');
        $user = Auth::user();
        $activityService = app(StatusActivityService::class);
        $hiddenDirectoryStatusIds = $activityService->hiddenDirectoryStatusIds();

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
        if (!empty($search)) {
            try {
                // User search still uses LIKE as it's typically short and indexed by B-tree
                $searchedUsers = User::where('username', 'LIKE', "%{$search}%")->get();

                $topicIds = ForumTopic::visible($user)
                    ->whereRaw("MATCH(name, txt) AGAINST(? IN BOOLEAN MODE)", [$search])
                    ->pluck('id');

                $dirIds = Directory::whereRaw("MATCH(name, txt) AGAINST(? IN BOOLEAN MODE)", [$search])
                    ->pluck('id');

                $newsIds = News::whereRaw("MATCH(name, text) AGAINST(? IN BOOLEAN MODE)", [$search])
                    ->pluck('id');

                $kbIds = Option::where('o_type', 'knowledgebase')
                    ->where('o_order', 0)
                    ->whereRaw("MATCH(name, o_valuer) AGAINST(? IN BOOLEAN MODE)", [$search])
                    ->pluck('id');

                $searchedStatuses = Status::visible()
                ->when(!empty($hiddenDirectoryStatusIds), fn ($query) => $query->whereNotIn('id', $hiddenDirectoryStatusIds))
                ->where(function ($q) use ($topicIds, $dirIds, $newsIds, $kbIds) {
                    $q->whereIn('tp_id', $topicIds)->whereIn('s_type', [2, 4, 100, 10, 11, 12, 13, 14])
                      ->orWhere(function ($q2) use ($dirIds) {
                          $q2->whereIn('tp_id', $dirIds)->where('s_type', 1);
                      })
                      ->orWhere(function ($q3) use ($newsIds) {
                          $q3->whereIn('tp_id', $newsIds)->where('s_type', 5);
                      })
                      ->orWhere(function ($q4) use ($kbIds) {
                          $q4->whereIn('tp_id', $kbIds)->where('s_type', KnowledgebaseCommunityService::STATUS_TYPE);
                      });
                })
                ->orderBy('date', 'desc')
                ->get();

                $activityService->decorateMany($searchedStatuses);

                $searchedGroups = collect();
                if (\App\Support\GroupSettings::isEnabled()) {
                    $searchedGroups = \App\Models\Group::where('status', \App\Models\Group::STATUS_ACTIVE)
                        ->whereRaw("MATCH(name, description) AGAINST(? IN BOOLEAN MODE)", [$search])
                        ->get();
                }

                $searchedCommentsForum = \App\Models\ForumComment::visible()
                    ->whereHas('topic', fn ($query) => $query->visible($user))
                    ->whereRaw("MATCH(txt) AGAINST(? IN BOOLEAN MODE)", [$search])
                    ->orderBy('date', 'desc')
                    ->get();

                $searchedCommentsDir = \App\Models\Option::where('o_type', '=', 'd_coment')
                    ->visible(null, 'o_order')
                    ->whereRaw("MATCH(o_valuer) AGAINST(? IN BOOLEAN MODE)", [$search])
                    ->get();

                $searchedProducts = Product::visible()
                    ->whereRaw("MATCH(name, o_valuer) AGAINST(? IN BOOLEAN MODE)", [$search])
                    ->get();

            } catch (\Throwable $e) {
                \Log::error('Search failed: ' . $e->getMessage());
                $searchedUsers         = collect();
                $searchedStatuses      = collect();
                $searchedGroups        = collect();
                $searchedCommentsForum = collect();
                $searchedCommentsDir   = collect();
                $searchedProducts      = collect();
            }

            return view('theme::portal.index', compact(
                'filter', 'search',
                'searchedUsers', 'searchedStatuses', 'searchedGroups',
                'searchedCommentsForum', 'searchedCommentsDir', 'searchedProducts'
            ));
        }

        // ── Feed (no search) ─────────────────────────────────────
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
                    ->paginate(20);

                $activityService->decorateMany($activities);
            } elseif ($user && $filter === 'groups') {
                if (!\App\Support\GroupSettings::isEnabled()) {
                    return redirect()->route('portal.index');
                }

                $activities = Status::visible($user)
                    ->where('date', '<=', time())
                    ->whereNotNull('group_id')
                    ->when(!empty($hiddenDirectoryStatusIds), fn ($query) => $query->whereNotIn('id', $hiddenDirectoryStatusIds))
                    ->tap(fn ($query) => app(\App\Services\GroupAccessService::class)->applyMyGroupsScope($query, $user))
                    ->orderBy('date', 'desc')
                    ->paginate(20);

                $activityService->decorateMany($activities);
            } else {
                // Smart ranked feed
                $page       = (int) $request->query('page', 1);
                $activities = \App\Services\FeedService::getRankedFeed(
                    $user ? $user->id : null,
                    $page
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
            $activities = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        if ($request->ajax()) {
            $html = view('theme::partials.ajax.activities', compact('activities'))->render();
            return response()->json([
                'html'          => $html,
                'next_page_url' => $activities->nextPageUrl()
            ]);
        }

        return view('theme::portal.index', compact('activities', 'filter', 'search'));
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
