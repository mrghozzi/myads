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
            $searchedUsers = User::whereRaw("CONVERT(username USING utf8mb4) LIKE ?", ["%{$search}%"])->get();
            
            // Search in ForumTopic (posts, topics)
            $topicIds = ForumTopic::whereRaw("CONVERT(txt USING utf8mb4) LIKE ?", ["%{$search}%"])
                ->orWhereRaw("CONVERT(name USING utf8mb4) LIKE ?", ["%{$search}%"])
                ->pluck('id');

            // Search in Directory (listings)
            $dirIds = Directory::whereRaw("CONVERT(txt USING utf8mb4) LIKE ?", ["%{$search}%"])
                ->orWhereRaw("CONVERT(name USING utf8mb4) LIKE ?", ["%{$search}%"])
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

            // Forum comments + Directory comments (Option table, type 'd_coment')
            $searchedCommentsForum = \App\Models\ForumComment::whereRaw("CONVERT(txt USING utf8mb4) LIKE ?", ["%{$search}%"])
                ->orderBy('date', 'desc')
                ->get();
                                                             
            $searchedCommentsDir = \App\Models\Option::where('o_type', '=', 'd_coment')
                ->whereRaw("CONVERT(o_valuer USING utf8mb4) LIKE ?", ["%{$search}%"])
                ->get();

            return view('theme::portal.index', compact('filter', 'search', 'searchedUsers', 'searchedStatuses', 'searchedCommentsForum', 'searchedCommentsDir'));
        }

        // Default behavior (No search)
        $query = Status::where('date', '<', time())->orderBy('date', 'desc');

        if ($user && $filter == 'me') {
            // Get IDs of users I follow
            $followingIds = Like::where('uid', $user->id)->where('type', 1)->pluck('sid')->toArray();
            $followingIds[] = $user->id; // Include self
            $followingIds[] = 1; // Include Admin/System (ID 1)
            
            $query->whereIn('uid', $followingIds);
        }

        $activities = $query->paginate(20);

        // Eager load related content manually
        foreach ($activities as $activity) {
            $activity->related_content = null;
            
            switch ($activity->s_type) {
                case 1: // Directory
                    $activity->related_content = Directory::find($activity->tp_id);
                    $activity->type_label = 'Directory';
                    break;
                case 2: // Forum Topic
                case 4: // Forum Image
                    $activity->related_content = ForumTopic::find($activity->tp_id);
                    $activity->type_label = 'Forum';
                    break;
                case 7867: // Store Product
                    $activity->related_content = Product::withoutGlobalScope('store')->find($activity->tp_id);
                    $activity->type_label = 'Store';
                    break;
                // Add other types as needed
            }
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
