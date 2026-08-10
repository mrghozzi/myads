<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\VideoHubController;
use App\Http\Resources\StatusResource;
use App\Models\ForumTopic;
use App\Models\Status;
use App\Services\StatusActivityService;
use Illuminate\Http\Request;

class VideoApiController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = trim((string) $request->input('q', ''));
        $filter = (string) $request->input('filter', 'all');
        $validFilters = ['all', 'trending', 'latest', 'videos', 'clips'];
        if (!in_array($filter, $validFilters, true)) {
            $filter = 'all';
        }

        $perPage = (int) $request->input('per_page', 12);

        // 1. Fetch Clips (YouTube Shorts shelf) - Top 8 latest/popular clips
        $clipsQuery = Status::visible()->where('s_type', 14);

        if ($searchQuery !== '') {
            $clipsQuery->where(function ($q) use ($searchQuery) {
                $q->where('txt', 'like', '%' . $searchQuery . '%')
                  ->orWhere(function ($sub) use ($searchQuery) {
                      $sub->whereIn('s_type', [2, 4, 100, 10, 11, 12, 13, 14])
                          ->whereHas('forumTopic', function ($ft) use ($searchQuery) {
                              $ft->where('name', 'like', '%' . $searchQuery . '%');
                          });
                  });
            });
        }

        $clips = $clipsQuery->orderBy('date', 'desc')->limit(8)->get();
        app(StatusActivityService::class)->decorateMany($clips);
        VideoHubController::attachThumbnailAndTitle($clips);

        // 2. Fetch Main Videos
        $videosQuery = Status::visible();

        if ($filter === 'clips') {
            $videosQuery->where('s_type', 14);
        } else {
            $videosQuery->where('s_type', '!=', 14)
                ->whereIn('s_type', [10, 2, 4, 100])
                ->where(function ($q) {
                    $q->where('s_type', 10)
                      ->orWhere(function ($sub) {
                          $sub->whereIn('s_type', [2, 4, 100])
                              ->whereHas('forumTopic', function ($ft) {
                                  $ft->whereHas('attachments', function ($att) {
                                      $att->where('mime_type', 'like', 'video/%')
                                          ->orWhere('original_name', 'like', '%.mp4')
                                          ->orWhere('original_name', 'like', '%.webm')
                                          ->orWhere('original_name', 'like', '%.mov')
                                          ->orWhere('original_name', 'like', '%.mkv');
                                  });
                              });
                      })
                      ->orWhere(function ($sub) {
                          $sub->whereIn('s_type', [2, 4, 100, 10])
                              ->whereHas('linkPreviewRecord', function ($lp) {
                                  $lp->where('url', 'like', '%youtube.com%')
                                     ->orWhere('url', 'like', '%youtu.be%')
                                     ->orWhere('url', 'like', '%vimeo.com%');
                              });
                      });
                });
        }

        if ($searchQuery !== '') {
            $videosQuery->where(function ($q) use ($searchQuery) {
                $q->where('txt', 'like', '%' . $searchQuery . '%')
                  ->orWhere(function ($sub) use ($searchQuery) {
                      $sub->whereIn('s_type', [2, 4, 100, 10, 14])
                          ->whereHas('forumTopic', function ($ft) use ($searchQuery) {
                              $ft->where('name', 'like', '%' . $searchQuery . '%');
                          });
                  });
            });
        }

        if ($filter === 'trending') {
            $videosQuery->orderBy(
                ForumTopic::select('vu')
                    ->whereColumn('forum.id', 'status.tp_id')
                    ->limit(1),
                'desc'
            )->orderBy('status.date', 'desc');
        } else {
            $videosQuery->orderBy('status.date', 'desc');
        }

        $videos = $videosQuery->paginate($perPage)->withQueryString();
        app(StatusActivityService::class)->decorateMany($videos->items());
        VideoHubController::attachThumbnailAndTitle($videos->items());

        $spotlightVideo = null;
        if (in_array($filter, ['all', 'trending'], true) && $videos->isNotEmpty() && $searchQuery === '') {
            $spotlightVideo = collect($videos->items())->first(fn ($item) => (int) $item->s_type === 10) ?: $videos->first();
        }

        return response()->json([
            'filter' => $filter,
            'search_query' => $searchQuery,
            'spotlight_video' => $spotlightVideo ? new StatusResource($spotlightVideo) : null,
            'clips' => StatusResource::collection($clips),
            'videos' => StatusResource::collection($videos),
            'meta' => [
                'current_page' => $videos->currentPage(),
                'last_page' => $videos->lastPage(),
                'per_page' => $videos->perPage(),
                'total' => $videos->total(),
            ],
        ]);
    }
}
