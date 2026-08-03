<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Services\SeoManager;
use App\Services\StatusActivityService;
use Illuminate\Http\Request;

class VideoHubController extends Controller
{
    public function index(Request $request, string $filter = 'all')
    {
        $searchQuery = trim((string) $request->input('q', ''));
        $validFilters = ['all', 'trending', 'latest', 'videos', 'clips'];
        if (!in_array($filter, $validFilters, true)) {
            $filter = 'all';
        }

        // 1. Fetch Clips (YouTube Shorts shelf) - Top 8 latest/popular clips
        $clipsQuery = Status::visible()->where('s_type', 14);

        if ($searchQuery !== '') {
            $clipsQuery->where(function ($q) use ($searchQuery) {
                $q->where('txt', 'like', '%' . $searchQuery . '%')
                  ->orWhereHas('forumTopic', function ($ft) use ($searchQuery) {
                      $ft->where('name', 'like', '%' . $searchQuery . '%');
                  });
            });
        }

        $clips = $clipsQuery->orderBy('date', 'desc')->limit(8)->get();
        app(StatusActivityService::class)->decorateMany($clips);
        $this->attachThumbnailAndTitle($clips);

        // 2. Fetch Main Videos
        $videosQuery = Status::visible();

        // Filter type handling
        if ($filter === 'clips') {
            $videosQuery->where('s_type', 14);
        } else {
            // 'all', 'videos', 'trending', 'latest' -> Exclude Clips (s_type = 14) from main video grid
            $videosQuery->where('s_type', '!=', 14)
                ->where(function ($q) {
                    $q->where('s_type', 10)
                      ->orWhereHas('forumTopic', function ($ft) {
                          $ft->whereHas('attachments', function ($att) {
                              $att->where('mime_type', 'like', 'video/%')
                                  ->orWhere('original_name', 'like', '%.mp4')
                                  ->orWhere('original_name', 'like', '%.webm')
                                  ->orWhere('original_name', 'like', '%.mov')
                                  ->orWhere('original_name', 'like', '%.mkv');
                          });
                      })
                      ->orWhereHas('linkPreviewRecord', function ($lp) {
                          $lp->where('url', 'like', '%youtube.com%')
                             ->orWhere('url', 'like', '%youtu.be%')
                             ->orWhere('url', 'like', '%vimeo.com%');
                      });
                });
        }

        // Search filtering
        if ($searchQuery !== '') {
            $videosQuery->where(function ($q) use ($searchQuery) {
                $q->where('txt', 'like', '%' . $searchQuery . '%')
                  ->orWhereHas('forumTopic', function ($ft) use ($searchQuery) {
                      $ft->where('name', 'like', '%' . $searchQuery . '%');
                  });
            });
        }

        // Sorting
        if ($filter === 'trending') {
            $videosQuery->orderBy(
                \App\Models\ForumTopic::select('vu')
                    ->whereColumn('forum.id', 'status.tp_id')
                    ->limit(1),
                'desc'
            )->orderBy('status.date', 'desc');
        } else {
            $videosQuery->orderBy('status.date', 'desc');
        }

        $videos = $videosQuery->paginate(12)->withQueryString();
        app(StatusActivityService::class)->decorateMany($videos->items());
        $this->attachThumbnailAndTitle($videos->items());

        // 3. Featured / Spotlight Video (Top Video on 'all' or 'trending')
        $spotlightVideo = null;
        if (in_array($filter, ['all', 'trending'], true) && $videos->isNotEmpty() && $searchQuery === '') {
            $spotlightVideo = collect($videos->items())->first(fn ($item) => (int) $item->s_type === 10) ?: $videos->first();
        }

        // Configure SEO metadata
        app(SeoManager::class)->setContext([
            'scope_key' => 'video_hub',
            'resource_title' => __('messages.video_hub_title'),
            'description' => __('messages.video_hub_subtitle'),
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.video_hub'), 'url' => route('video.index')],
            ],
        ]);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('theme::video.partials.video_grid', compact('videos'))->render(),
                'next_page_url' => $videos->nextPageUrl(),
            ]);
        }

        return view('theme::video.index', compact('videos', 'clips', 'spotlightVideo', 'filter', 'searchQuery'));
    }

    /**
     * Attach resolved thumbnail URL, video URL, and display title to status models.
     */
    protected function attachThumbnailAndTitle(iterable $statuses): void
    {
        foreach ($statuses as $status) {
            $status->resolved_thumbnail = static::resolveVideoThumbnailUrl($status);
            $status->resolved_video_url = static::resolveVideoUrl($status);
            $status->resolved_title = static::resolveVideoTitle($status);
        }
    }

    /**
     * Multi-source Video Thumbnail Engine.
     */
    public static function resolveVideoThumbnailUrl($status): string
    {
        // 1. Direct status video_thumbnail property/column
        if (!empty($status->video_thumbnail)) {
            $thumb = (string) $status->video_thumbnail;
            return (str_starts_with($thumb, 'http://') || str_starts_with($thumb, 'https://'))
                ? $thumb
                : asset($thumb);
        }

        $topic = $status->forumTopic ?? ($status->related_content instanceof \App\Models\ForumTopic ? $status->related_content : null);

        if ($topic) {
            // 2. Topic image_url attribute
            if (!empty($topic->image_url)) {
                $imgUrl = (string) $topic->image_url;
                return (str_starts_with($imgUrl, 'http://') || str_starts_with($imgUrl, 'https://'))
                    ? $imgUrl
                    : asset($imgUrl);
            }

            // 3. Topic imageOption relation
            if ($topic->relationLoaded('imageOption') && $topic->imageOption && !empty($topic->imageOption->o_valuer)) {
                $val = (string) $topic->imageOption->o_valuer;
                return (str_starts_with($val, 'http://') || str_starts_with($val, 'https://'))
                    ? $val
                    : asset($val);
            }

            // 4. Topic image attachments
            if ($topic->relationLoaded('attachments') && $topic->attachments && $topic->attachments->isNotEmpty()) {
                $imgAtt = $topic->attachments->first(function ($att) {
                    $mime = strtolower((string) ($att->mime_type ?? ''));
                    $ext = strtolower(pathinfo((string) ($att->file_path ?? ''), PATHINFO_EXTENSION));
                    return str_starts_with($mime, 'image/') || in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
                });
                if ($imgAtt && !empty($imgAtt->file_path)) {
                    return asset($imgAtt->file_path);
                }
            }

            // 5. YouTube video URL extraction from topic text
            $topicText = (string) ($topic->txt ?? '');
            if ($topicText !== '' && preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $topicText, $matches)) {
                return 'https://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg';
            }
        }

        // 6. Link preview URL
        $linkRecord = $status->linkPreviewRecord;
        if ($linkRecord) {
            $linkUrl = (string) ($linkRecord->url ?? '');
            if ($linkUrl !== '' && preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $linkUrl, $matches)) {
                return 'https://img.youtube.com/vi/' . $matches[1] . '/hqdefault.jpg';
            }

            if (!empty($linkRecord->image_url)) {
                return (string) $linkRecord->image_url;
            }
            if (!empty($linkRecord->image)) {
                return (string) $linkRecord->image;
            }
        }

        // 7. Fallback video placeholder asset
        return theme_asset('img/video-placeholder.svg');
    }

    /**
     * Resolve direct video file URL (e.g. MP4/WebM attachment or direct video link) if available.
     */
    public static function resolveVideoUrl($status): ?string
    {
        $topic = $status->forumTopic ?? ($status->related_content instanceof \App\Models\ForumTopic ? $status->related_content : null);

        if ($topic) {
            if ($topic->relationLoaded('attachments') && $topic->attachments && $topic->attachments->isNotEmpty()) {
                $videoAtt = $topic->attachments->first(function ($att) {
                    $mime = strtolower((string) ($att->mime_type ?? ''));
                    $ext = strtolower(pathinfo((string) ($att->file_path ?? ''), PATHINFO_EXTENSION));
                    return str_starts_with($mime, 'video/') || in_array($ext, ['mp4', 'webm', 'mov', 'mkv', 'avi', 'ogg'], true);
                });
                if ($videoAtt && !empty($videoAtt->file_path)) {
                    return asset($videoAtt->file_path);
                }
            }

            if (!$topic->relationLoaded('attachments') && method_exists($topic, 'attachments')) {
                $videoAtt = $topic->attachments()->get()->first(function ($att) {
                    $mime = strtolower((string) ($att->mime_type ?? ''));
                    $ext = strtolower(pathinfo((string) ($att->file_path ?? ''), PATHINFO_EXTENSION));
                    return str_starts_with($mime, 'video/') || in_array($ext, ['mp4', 'webm', 'mov', 'mkv', 'avi', 'ogg'], true);
                });
                if ($videoAtt && !empty($videoAtt->file_path)) {
                    return asset($videoAtt->file_path);
                }
            }
        }

        if (!empty($status->video_url)) {
            $url = (string) $status->video_url;
            return (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) ? $url : asset($url);
        }

        return null;
    }

    /**
     * Resolve clean title for video.
     */
    public static function resolveVideoTitle($status): string
    {
        if (!empty($status->video_title)) {
            return (string) $status->video_title;
        }

        $topic = $status->forumTopic ?? ($status->related_content instanceof \App\Models\ForumTopic ? $status->related_content : null);
        if ($topic && !empty($topic->name) && !in_array(strtolower(trim($topic->name)), ['video', 'text', 'gallery', 'clips', 'audio', 'file', 'music'], true)) {
            return (string) $topic->name;
        }

        if (!empty($status->txt)) {
            return (string) $status->txt;
        }

        return __('messages.video_hub');
    }
}
