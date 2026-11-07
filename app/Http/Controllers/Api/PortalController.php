<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Status;
use App\Models\Like;
use App\Services\StatusActivityService;
use App\Services\FeedService;
use App\Http\Resources\StatusResource;

class PortalController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $user = Auth::user();
        $activityService = app(StatusActivityService::class);
        $hiddenDirectoryStatusIds = $activityService->hiddenDirectoryStatusIds();
        $pageSize = (int) $request->query('per_page', 20);

        try {
            if ($user && $filter === 'me') {
                $followingIds = Like::where('uid', $user->id)->where('type', 1)->pluck('sid')->toArray();
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
                $activities = Status::visible($user)
                    ->where('date', '<=', time())
                    ->whereNotNull('group_id')
                    ->when(!empty($hiddenDirectoryStatusIds), fn ($query) => $query->whereNotIn('id', $hiddenDirectoryStatusIds))
                    ->tap(fn ($query) => app(\App\Services\GroupAccessService::class)->applyMyGroupsScope($query, $user))
                    ->orderBy('date', 'desc')
                    ->paginate($pageSize);

                $activityService->decorateMany($activities);
            } elseif ($filter === 'media') {
                $mediaTypes = [4, 10, 11, 12, 13, 14, 7867];
                $activities = Status::visible($user)
                    ->where('date', '<=', time())
                    ->whereIn('s_type', $mediaTypes)
                    ->when(!empty($hiddenDirectoryStatusIds), fn ($query) => $query->whereNotIn('id', $hiddenDirectoryStatusIds))
                    ->orderBy('date', 'desc')
                    ->paginate($pageSize);

                $activityService->decorateMany($activities);
            } elseif ($filter === 'trending') {
                $page = (int) $request->query('page', 1);
                $activities = FeedService::getRankedFeed(null, $page, $pageSize);
                $activityService->decorateMany($activities);
            } elseif ($user && $filter === 'saved') {
                $savedStatusIds = \Illuminate\Support\Facades\DB::table('saved_statuses')
                    ->where('user_id', $user->id)
                    ->orderBy('id', 'desc')
                    ->pluck('status_id')
                    ->toArray();

                if (empty($savedStatusIds)) {
                    $activities = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $pageSize);
                } else {
                    $activities = Status::visible($user)
                        ->whereIn('id', $savedStatusIds)
                        ->when(!empty($hiddenDirectoryStatusIds), fn ($query) => $query->whereNotIn('id', $hiddenDirectoryStatusIds))
                        ->orderByRaw('FIELD(id, ' . implode(',', array_map('intval', $savedStatusIds)) . ')')
                        ->paginate($pageSize);
                }
                $activityService->decorateMany($activities);
            } elseif ($user && $filter === 'my_posts') {
                $activities = Status::visible($user)
                    ->where('uid', $user->id)
                    ->when(!empty($hiddenDirectoryStatusIds), fn ($query) => $query->whereNotIn('id', $hiddenDirectoryStatusIds))
                    ->orderBy('date', 'desc')
                    ->paginate($pageSize);

                $activityService->decorateMany($activities);
            } else {
                $page = (int) $request->query('page', 1);
                $activities = FeedService::getRankedFeed($user ? $user->id : null, $page, $pageSize);
                $activityService->decorateMany($activities);
            }
            
            return StatusResource::collection($activities);

        } catch (\Throwable $e) {
            \Log::error('API Portal feed failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load feed'], 500);
        }
    }
}
