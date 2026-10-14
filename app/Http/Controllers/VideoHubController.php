<?php

namespace App\Http\Controllers;

use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\Like;
use App\Models\Status;
use App\Services\SeoManager;
use App\Services\StatusActivityService;
use App\Support\VideoCategoryCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VideoHubController extends Controller
{
    /**
     * Direct standalone video watch endpoint (/video/watch/{id} or /video/{id}).
     */
    public function watch(Request $request, $id)
    {
        // 1. Resolve Status & Topic
        $status = Status::visible()->with(['user', 'forumTopic.attachments', 'linkPreviewRecord'])->find($id);
        $topic = null;

        if ($status && $status->tp_id) {
            $topic = $status->forumTopic;
        }

        if (!$status) {
            $topic = ForumTopic::with(['attachments', 'user'])->find($id);
            if ($topic) {
                $status = Status::where('tp_id', $topic->id)->whereIn('s_type', [10, 2, 4, 100, 14])->first();
            }
        }

        if (!$status && !$topic) {
            abort(404, __('messages.video_not_found') ?? 'Video not found');
        }

        if (!$status) {
            $status = new Status([
                'id' => $topic->id,
                'uid' => $topic->uid,
                'tp_id' => $topic->id,
                's_type' => 10,
                'date' => $topic->date,
                'txt' => $topic->name,
            ]);
            $status->setRelation('forumTopic', $topic);
            $status->setRelation('user', $topic->user);
        }

        if (!$topic && $status->tp_id) {
            $topic = $status->forumTopic;
        }

        // Increment view count
        if ($topic) {
            $topic->increment('vu');
        }

        // Decorate Status
        app(StatusActivityService::class)->decorate($status);
        static::attachThumbnailAndTitle([$status]);

        // Video Assets & Embeds
        $videoFile = null;
        if ($topic && $topic->relationLoaded('attachments') && $topic->attachments) {
            $videoFile = $topic->attachments->first(function ($att) {
                $mime = (string) ($att->mime_type ?? '');
                $ext = strtolower(pathinfo((string) ($att->file_path ?? ''), PATHINFO_EXTENSION));
                return str_starts_with($mime, 'video/') || in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'm4v', 'mkv'], true);
            });
        }

        $videoUrl = $status->resolved_video_url ?: static::resolveVideoUrl($status);
        $thumbUrl = $status->resolved_thumbnail ?: static::resolveVideoThumbnailUrl($status);
        $videoTitle = $status->resolved_title ?: static::resolveVideoTitle($status);
        $category = $status->resolved_video_category ?: static::resolveVideoCategory($status);

        $ytId = static::extractYoutubeId($videoUrl) ?: static::extractYoutubeId($topic?->txt) ?: static::extractYoutubeId($status->linkPreviewRecord?->url);
        $ytEmbedUrl = $ytId ? 'https://www.youtube.com/embed/' . $ytId . '?autoplay=1&rel=0&enablejsapi=1' : null;

        // Suggested / Up Next Videos
        $suggestedQuery = Status::visible()
            ->with(['user', 'forumTopic'])
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

        if ($suggestedQuery->isEmpty()) {
            $suggestedQuery = Status::visible()
                ->where('id', '!=', $status->id)
                ->where('s_type', 10)
                ->orderBy('id', 'desc')
                ->limit(8)
                ->get();
        }

        app(StatusActivityService::class)->decorateMany($suggestedQuery);
        static::attachThumbnailAndTitle($suggestedQuery);
        $suggestedVideos = $suggestedQuery;

        // User states
        $viewer = Auth::user();
        $user = $status->user ?? $topic?->user;
        $isFollowing = ($viewer && $user)
            ? Like::where('uid', $viewer->id)->where('sid', $user->id)->where('type', 1)->exists()
            : false;
        $isSaved = $viewer
            ? DB::table('saved_statuses')->where('user_id', $viewer->id)->where('status_id', $status->id)->exists()
            : false;
        $isLiked = ($viewer && $topic)
            ? Like::where('uid', $viewer->id)->where('sid', $topic->id)->where('type', 2)->exists()
            : false;

        // Comments
        $comments = $topic
            ? ForumComment::with(['user', 'replies.user'])
                ->where('tid', $topic->id)
                ->whereNull('parent_id')
                ->orderBy('id', 'desc')
                ->limit(50)
                ->get()
            : collect();

        // SEO Configuration
        app(SeoManager::class)->setContext([
            'scope_key' => 'video_watch',
            'resource_title' => $videoTitle,
            'description' => Str::limit(strip_tags((string) ($topic?->txt ?? $status->txt)), 160),
            'image' => $thumbUrl,
            'breadcrumbs' => [
                ['name' => __('messages.home'), 'url' => url('/')],
                ['name' => __('messages.video_hub'), 'url' => route('video.index')],
                ['name' => $videoTitle, 'url' => route('video.watch', $status->id)],
            ],
        ]);

        return view('theme::video.watch', compact(
            'status',
            'topic',
            'user',
            'videoFile',
            'ytEmbedUrl',
            'videoUrl',
            'thumbUrl',
            'videoTitle',
            'suggestedVideos',
            'isLiked',
            'isSaved',
            'isFollowing',
            'category',
            'comments',
            'ytId'
        ));
    }

    public function index(Request $request, string $filter = 'all')
    {
        $searchQuery = trim((string) $request->input('q', ''));
        $categoryFilter = $request->input('category') ?: $request->input('cat');
        $sort = (string) $request->input('sort', '');

        $validFilters = ['all', 'trending', 'latest', 'videos', 'clips', 'shorts'];
        if (!in_array($filter, $validFilters, true)) {
            $filter = 'all';
        }
        $isClipsFilter = in_array($filter, ['clips', 'shorts'], true);

        // 1. Fetch Clips (YouTube Shorts shelf) - Top 8 latest/popular clips
        $clipsQuery = Status::visible()->with(['user', 'forumTopic'])->where('s_type', 14);

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
        $this->attachThumbnailAndTitle($clips);

        // 2. Fetch Main Videos
        $videosQuery = Status::visible()->with(['user', 'forumTopic']);

        // Filter type handling
        if ($isClipsFilter) {
            $videosQuery->where('s_type', 14);
        } else {
            // Exclude Clips (s_type = 14) from main video grid
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

        // Search filtering
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

        // Dedicated Video Category filtering (Separated completely from forum categories)
        if ($categoryFilter) {
            VideoCategoryCatalog::applyCategoryFilter($videosQuery, $categoryFilter);
        }

        // Sorting
        if ($sort === 'views' || $filter === 'trending') {
            $videosQuery->orderBy(
                ForumTopic::select('vu')
                    ->whereColumn('forum.id', 'status.tp_id')
                    ->limit(1),
                'desc'
            )->orderBy('status.date', 'desc');
        } elseif ($sort === 'reactions') {
            $videosQuery->orderBy('status.reactions_count', 'desc')->orderBy('status.date', 'desc');
        } elseif ($sort === 'oldest') {
            $videosQuery->orderBy('status.date', 'asc');
        } else {
            $videosQuery->orderBy('status.date', 'desc');
        }

        $videos = $videosQuery->paginate(12)->withQueryString();
        app(StatusActivityService::class)->decorateMany($videos->items());
        $this->attachThumbnailAndTitle($videos->items());

        // 3. Featured / Spotlight Video (Top Video on 'all' or 'trending')
        $spotlightVideo = null;
        if (in_array($filter, ['all', 'trending'], true) && $videos->isNotEmpty() && $searchQuery === '' && !$categoryFilter) {
            $spotlightVideo = collect($videos->items())->first(fn ($item) => (int) $item->s_type === 10) ?: $videos->first();
        }

        // 4. Dedicated Modern Video Categories (NO Forum Categories)
        $categories = collect(VideoCategoryCatalog::all());

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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('theme::video.partials.video_grid', compact('videos'))->render(),
                'next_page_url' => $videos->nextPageUrl(),
            ]);
        }

        return view('theme::video.index', compact('videos', 'clips', 'spotlightVideo', 'filter', 'searchQuery', 'categories', 'categoryFilter', 'sort'));
    }

    /**
     * Attach resolved thumbnail URL, video URL, display title, category, and watch URL to status models.
     */
    public static function attachThumbnailAndTitle(iterable $statuses): void
    {
        foreach ($statuses as $status) {
            $status->resolved_thumbnail = static::resolveVideoThumbnailUrl($status);
            $status->resolved_video_url = static::resolveVideoUrl($status);
            $status->resolved_title = static::resolveVideoTitle($status);
            $status->resolved_video_category = static::resolveVideoCategory($status);
            $status->resolved_watch_url = static::resolveWatchUrl($status);
        }
    }

    /**
     * Resolve dedicated watch route URL for a video status.
     */
    public static function resolveWatchUrl($status): string
    {
        if (!empty($status->id)) {
            return route('video.watch', $status->id);
        }
        if (!empty($status->tp_id)) {
            return route('video.watch', $status->tp_id);
        }
        return route('video.index');
    }

    /**
     * Resolve dedicated video category metadata.
     */
    public static function resolveVideoCategory($status): object
    {
        $title = static::resolveVideoTitle($status);
        $text = (string) ($status->txt ?? '');
        $topic = $status->forumTopic ?? ($status->related_content instanceof ForumTopic ? $status->related_content : null);
        if ($topic && !empty($topic->txt)) {
            $text .= ' ' . $topic->txt;
        }
        $sType = (int) ($status->s_type ?? 10);
        return VideoCategoryCatalog::inferCategory($text, $title, $sType);
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

        $topic = $status->forumTopic ?? ($status->related_content instanceof ForumTopic ? $status->related_content : null);

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
        $topic = $status->forumTopic ?? ($status->related_content instanceof ForumTopic ? $status->related_content : null);

        if ($topic) {
            $attachments = $topic->relationLoaded('attachments')
                ? $topic->attachments
                : (method_exists($topic, 'attachments') ? $topic->attachments()->get() : collect());

            if ($attachments && $attachments->isNotEmpty()) {
                $videoAtt = $attachments->first(function ($att) {
                    $mime = strtolower((string) ($att->mime_type ?? ''));
                    $ext = strtolower(pathinfo((string) ($att->file_path ?? ''), PATHINFO_EXTENSION));
                    return str_starts_with($mime, 'video/') || in_array($ext, ['mp4', 'webm', 'mov', 'mkv', 'avi', 'ogg'], true);
                });
                if ($videoAtt && !empty($videoAtt->file_path)) {
                    return asset($videoAtt->file_path);
                }
            }

            $topicText = (string) ($topic->txt ?? '');
            if ($topicText !== '') {
                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $topicText, $matches)) {
                    return 'https://www.youtube.com/watch?v=' . $matches[1];
                }
                if (preg_match('/(https?:\/\/[^\s<"]+\.(?:mp4|webm|mov|mkv|avi|ogg))/i', $topicText, $matches)) {
                    return $matches[1];
                }
            }
        }

        if (!empty($status->video_url)) {
            $url = (string) $status->video_url;
            return (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) ? $url : asset($url);
        }

        $linkRecord = $status->linkPreviewRecord;
        if ($linkRecord) {
            $linkUrl = (string) ($linkRecord->url ?? '');
            if ($linkUrl !== '') {
                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $linkUrl, $matches)) {
                    return 'https://www.youtube.com/watch?v=' . $matches[1];
                }
                $ext = strtolower(pathinfo(parse_url($linkUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
                if (in_array($ext, ['mp4', 'webm', 'mov', 'mkv', 'avi', 'ogg'], true)) {
                    return $linkUrl;
                }
            }
        }

        return null;
    }

    /**
     * Extract 11-char YouTube Video ID from text or URL if present.
     */
    public static function extractYoutubeId(?string $text): ?string
    {
        if (empty($text)) return null;
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $text, $matches)) {
            return $matches[1];
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

        $topic = $status->forumTopic ?? ($status->related_content instanceof ForumTopic ? $status->related_content : null);
        if ($topic && !empty($topic->name) && !in_array(strtolower(trim($topic->name)), ['video', 'text', 'gallery', 'clips', 'audio', 'file', 'music'], true)) {
            return (string) $topic->name;
        }

        if (!empty($status->txt)) {
            $txt = trim((string) $status->txt);
            $firstLine = trim(explode("\n", $txt)[0]);
            if ($firstLine !== '' && !in_array(strtolower($firstLine), ['video', 'clips', 'text', 'gallery'], true)) {
                return Str::limit(strip_tags($firstLine), 70);
            }
        }

        if ($topic && !empty($topic->txt)) {
            $tTxt = trim((string) $topic->txt);
            $firstLine = trim(explode("\n", $tTxt)[0]);
            if ($firstLine !== '' && !in_array(strtolower($firstLine), ['video', 'clips', 'text', 'gallery'], true)) {
                return Str::limit(strip_tags($firstLine), 70);
            }
        }

        $user = $status->user ?? $topic?->user;
        if ($user && !empty($user->username)) {
            return __('messages.video_by_user', ['user' => $user->username]);
        }

        return (int) ($status->s_type ?? 10) === 14 
            ? (__('messages.shorts_clips') ?? 'Clip')
            : (__('messages.main_videos') ?? 'Video');
    }
}
