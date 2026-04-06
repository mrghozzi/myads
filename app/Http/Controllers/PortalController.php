<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\StatusActivityService;
use App\Models\Status;
use App\Models\User;
use App\Models\Like;
use App\Models\ForumTopic;
use App\Models\Directory;
use App\Models\Product;
use App\Models\News;

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
                $searchedUsers = User::where('username', 'LIKE', "%{$search}%")->get();

                $topicIds = ForumTopic::where('txt', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->pluck('id');

                $dirIds = Directory::where('txt', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->pluck('id');

                $newsIds = News::where('text', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->pluck('id');

                $searchedStatuses = Status::visible()
                ->when(!empty($hiddenDirectoryStatusIds), fn ($query) => $query->whereNotIn('id', $hiddenDirectoryStatusIds))
                ->where(function ($q) use ($topicIds, $dirIds, $newsIds) {
                    $q->whereIn('tp_id', $topicIds)->whereIn('s_type', [2, 4, 100])
                      ->orWhere(function ($q2) use ($dirIds) {
                          $q2->whereIn('tp_id', $dirIds)->where('s_type', 1);
                      })
                      ->orWhere(function ($q3) use ($newsIds) {
                          $q3->whereIn('tp_id', $newsIds)->where('s_type', 5);
                      });
                })
                ->orderBy('date', 'desc')
                ->get();

                $activityService->decorateMany($searchedStatuses);

                $searchedCommentsForum = \App\Models\ForumComment::visible()
                    ->where('txt', 'LIKE', "%{$search}%")
                    ->orderBy('date', 'desc')
                    ->get();

                $searchedCommentsDir = \App\Models\Option::where('o_type', '=', 'd_coment')
                    ->visible(null, 'o_order')
                    ->where('o_valuer', 'LIKE', "%{$search}%")
                    ->get();
            } catch (\Throwable $e) {
                $searchedUsers        = collect();
                $searchedStatuses     = collect();
                $searchedCommentsForum = collect();
                $searchedCommentsDir  = collect();
            }

            return view('theme::portal.index', compact(
                'filter', 'search',
                'searchedUsers', 'searchedStatuses',
                'searchedCommentsForum', 'searchedCommentsDir'
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
                    ->where('date', '<', time())
                    ->when(!empty($hiddenDirectoryStatusIds), fn ($query) => $query->whereNotIn('id', $hiddenDirectoryStatusIds))
                    ->whereIn('uid', $followingIds)
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
            'description' => __('messages.share_page_description') ?? 'Share content with the MYADS community.',
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
