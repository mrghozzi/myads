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

class PortalController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $search = $request->query('search', '');
        $user = Auth::user();

        // If a search query is provided
        if (!empty($search)) {
            try {
                $searchedUsers = User::where('username', 'LIKE', "%{$search}%")->get();
                
                // Search in ForumTopic (posts, topics)
                $topicIds = ForumTopic::where('txt', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->pluck('id');

                // Search in Directory (listings)
                $dirIds = Directory::where('txt', 'LIKE', "%{$search}%")
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->pluck('id');

                $searchedStatuses = Status::where(function($q) use ($topicIds, $dirIds) {
                    $q->whereIn('tp_id', $topicIds)->whereIn('s_type', [2, 4, 100])
                      ->orWhere(function($q2) use ($dirIds) {
                          $q2->whereIn('tp_id', $dirIds)->where('s_type', 1);
                      });
                })
                ->orderBy('date', 'desc')
                ->get();

                // Format searched statuses with related content
                foreach ($searchedStatuses as $activity) {
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
                    }
                }

                // Forum comments + Directory comments
                $searchedCommentsForum = \App\Models\ForumComment::where('txt', 'LIKE', "%{$search}%")
                    ->orderBy('date', 'desc')
                    ->get();
                                                                 
                $searchedCommentsDir = \App\Models\Option::where('o_type', '=', 'd_coment')
                    ->where('o_valuer', 'LIKE', "%{$search}%")
                    ->get();
            } catch (\Throwable $e) {
                $searchedUsers = collect();
                $searchedStatuses = collect();
                $searchedCommentsForum = collect();
                $searchedCommentsDir = collect();
            }

            return view('theme::portal.index', compact('filter', 'search', 'searchedUsers', 'searchedStatuses', 'searchedCommentsForum', 'searchedCommentsDir'));
        }

        // Default behavior (No search)
        try {
            $query = Status::where('date', '<', time())->orderBy('date', 'desc');

            if ($user && $filter == 'me') {
                $followingIds = Like::where('uid', $user->id)->where('type', 1)->pluck('sid')->toArray();
                $followingIds[] = $user->id;
                $followingIds[] = 1;
                
                $query->whereIn('uid', $followingIds);
            }

            $activities = $query->paginate(20);

            // Eager load related content manually
            foreach ($activities as $activity) {
                $activity->related_content = null;
                
                switch ($activity->s_type) {
                    case 1:
                        $activity->related_content = Directory::find($activity->tp_id);
                        $activity->type_label = 'Directory';
                        break;
                    case 2:
                    case 4:
                        $activity->related_content = ForumTopic::find($activity->tp_id);
                        $activity->type_label = 'Forum';
                        break;
                    case 7867:
                        $activity->related_content = Product::withoutGlobalScope('store')->find($activity->tp_id);
                        $activity->type_label = 'Store';
                        break;
                }
            }
        } catch (\Throwable $e) {
            $activities = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        if ($request->ajax()) {
            $html = view('theme::partials.ajax.activities', compact('activities'))->render();
            return response()->json([
                'html' => $html,
                'next_page_url' => $activities->nextPageUrl()
            ]);
        }

        return view('theme::portal.index', compact('activities', 'filter', 'search'));
    }
}
