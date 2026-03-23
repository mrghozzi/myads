<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

                $searchedStatuses = Status::where(function ($q) use ($topicIds, $dirIds, $newsIds) {
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

                foreach ($searchedStatuses as $activity) {
                    self::attachRelatedContent($activity);
                }

                $searchedCommentsForum = \App\Models\ForumComment::where('txt', 'LIKE', "%{$search}%")
                    ->orderBy('date', 'desc')
                    ->get();

                $searchedCommentsDir = \App\Models\Option::where('o_type', '=', 'd_coment')
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

                $activities = Status::where('date', '<', time())
                    ->whereIn('uid', $followingIds)
                    ->orderBy('date', 'desc')
                    ->paginate(20);

                foreach ($activities as $activity) {
                    self::attachRelatedContent($activity);
                }
            } else {
                // Smart ranked feed
                $page       = (int) $request->query('page', 1);
                $activities = \App\Services\FeedService::getRankedFeed(
                    $user ? $user->id : null,
                    $page
                );

                foreach ($activities as $activity) {
                    self::attachRelatedContent($activity);
                }
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

    /**
     * Attach related_content and type_label to a Status instance.
     */
    private static function attachRelatedContent(\App\Models\Status $activity): void
    {
        $activity->related_content = null;

        switch ($activity->s_type) {
            case 1:
                $activity->related_content = Directory::find($activity->tp_id);
                $activity->type_label = 'Directory';
                break;
            case 2:
            case 4:
            case 100:
                $activity->related_content = ForumTopic::find($activity->tp_id);
                $activity->type_label = 'Forum';
                break;
            case 7867:
                $activity->related_content = Product::withoutGlobalScope('store')->find($activity->tp_id);
                $activity->type_label = 'Store';
                break;
            case 5:
                $activity->related_content = News::find($activity->tp_id);
                $activity->type_label = 'News';
                break;
        }
    }
}
