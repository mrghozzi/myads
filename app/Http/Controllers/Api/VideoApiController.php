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
        if ($filter === 'shorts') {
            $filter = 'clips';
        }
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

        // Category filter
        $categoryParam = $request->input('cat') ?: $request->input('category');
        if ($categoryParam) {
            $cat = is_numeric($categoryParam)
                ? \App\Models\ForumCategory::find((int) $categoryParam)
                : \App\Models\ForumCategory::where('slug', $categoryParam)->first();
            if ($cat) {
                $videosQuery->whereHas('forumTopic', function ($ft) use ($cat) {
                    $ft->where('cat', $cat->id);
                });
            }
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

        $sort = (string) $request->input('sort', '');
        if ($filter === 'trending' || $sort === 'views') {
            $videosQuery->orderBy(
                ForumTopic::select('vu')
                    ->whereColumn('forum.id', 'status.tp_id')
                    ->limit(1),
                'desc'
            )->orderBy('status.date', 'desc');
        } elseif ($sort === 'reactions') {
            $videosQuery->withCount('likes')->orderBy('likes_count', 'desc')->orderBy('status.date', 'desc');
        } elseif ($sort === 'oldest') {
            $videosQuery->orderBy('status.date', 'asc');
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

        $categories = \App\Models\ForumCategory::orderBy('name')->get()->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'slug' => $c->slug ?? (string) $c->id,
            ];
        });

        return response()->json([
            'filter' => $filter,
            'search_query' => $searchQuery,
            'spotlight_video' => $spotlightVideo ? new StatusResource($spotlightVideo) : null,
            'clips' => StatusResource::collection($clips),
            'videos' => StatusResource::collection($videos),
            'categories' => $categories,
            'meta' => [
                'current_page' => $videos->currentPage(),
                'last_page' => $videos->lastPage(),
                'per_page' => $videos->perPage(),
                'total' => $videos->total(),
            ],
        ]);
    }

    public function show(Request $request, $id)
    {
        $topic = ForumTopic::where('id', $id)->first();
        $status = null;
        if ($topic) {
            $status = Status::where('tp_id', $topic->id)->first();
        }
        if (!$status) {
            $status = Status::where('id', $id)->first();
            if ($status && $status->tp_id && !$topic) {
                $topic = ForumTopic::where('id', $status->tp_id)->first();
            }
        }

        if (!$status && !$topic) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        if (!$status) {
            $status = new Status([
                'id' => $topic->id,
                'uid' => $topic->uid,
                'tp_id' => $topic->id,
                's_type' => 2,
                'date' => $topic->date,
                'txt' => $topic->name,
            ]);
            $status->setRelation('forumTopic', $topic);
            $status->setRelation('user', $topic->user);
        }

        app(StatusActivityService::class)->decorate($status);
        VideoHubController::attachThumbnailAndTitle([$status]);

        // Suggested videos
        $suggestedQuery = Status::visible()
            ->where('id', '!=', $status->id)
            ->whereIn('s_type', [10, 2, 4, 100])
            ->where(function ($q) {
                $q->where('s_type', 10)
                  ->orWhereHas('forumTopic', function ($ft) {
                      $ft->whereHas('attachments', function ($att) {
                          $att->where('mime_type', 'like', 'video/%')
                              ->orWhere('original_name', 'like', '%.mp4')
                              ->orWhere('original_name', 'like', '%.webm');
                      });
                  })
                  ->orWhereHas('linkPreviewRecord', function ($lp) {
                      $lp->where('url', 'like', '%youtube.com%')
                         ->orWhere('url', 'like', '%youtu.be%');
                  });
            })
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        app(StatusActivityService::class)->decorateMany($suggestedQuery);
        VideoHubController::attachThumbnailAndTitle($suggestedQuery);

        return response()->json([
            'status' => new StatusResource($status),
            'suggested_videos' => StatusResource::collection($suggestedQuery),
        ]);
    }
}
