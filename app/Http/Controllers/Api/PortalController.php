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
                    ->paginate(20);

                $activityService->decorateMany($activities);
            } else {
                $page = (int) $request->query('page', 1);
                $activities = FeedService::getRankedFeed($user ? $user->id : null, $page);
                $activityService->decorateMany($activities);
            }
            
            return StatusResource::collection($activities);

        } catch (\Throwable $e) {
            \Log::error('API Portal feed failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load feed'], 500);
        }
    }
}
