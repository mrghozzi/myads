@foreach($activities as $status)
    @php
        $topic = $status->forumTopic;
        $watchUrl = $topic ? route('forum.topic', $topic->id) : url('/portal');
        
        $thumbUrl = $status->resolved_thumbnail ?: \App\Http\Controllers\VideoHubController::resolveVideoThumbnailUrl($status);
        $videoUrl = $status->resolved_video_url ?: \App\Http\Controllers\VideoHubController::resolveVideoUrl($status);
        $videoTitle = \Illuminate\Support\Str::limit($status->resolved_title ?: \App\Http\Controllers\VideoHubController::resolveVideoTitle($status), 55);

        $viewsCount = $topic ? (int) ($topic->vu ?? 0) : (int) ($status->views_count ?? 0);
        $reactionsCount = (int) ($status->reactions_count ?? 0);
        $timeAgo = $status->date_formatted ?: ($status->created_at ? $status->created_at->diffForHumans() : '');
        $hasCustomThumb = $thumbUrl && !str_contains($thumbUrl, 'video-placeholder');
    @endphp

    <div class="col">
        <div class="profile-yt-video-card rounded-4 overflow-hidden shadow-sm bg-surface h-100 border border-translucent position-relative" data-id="{{ $status->id }}">
            <a href="{{ $watchUrl }}" class="yt-thumb-stage d-block text-decoration-none">
                <div class="yt-thumb-wrapper ratio ratio-16x9 position-relative overflow-hidden bg-dark rounded-top-4" @if($videoUrl) data-video-url="{{ $videoUrl }}" @endif>
                    @if($hasCustomThumb)
                        <img src="{{ $thumbUrl }}" alt="{{ $videoTitle }}" class="yt-thumb-img img-fluid w-100 h-100 object-fit-cover lazyload" onError="this.onerror=null;this.src='{{ theme_asset('img/video-placeholder.svg') }}';">
                    @elseif($videoUrl)
                        <video src="{{ $videoUrl }}#t=0.5" preload="metadata" muted playsinline class="yt-thumb-img img-fluid w-100 h-100 object-fit-cover pointer-events-none"></video>
                    @else
                        <img src="{{ theme_asset('img/video-placeholder.svg') }}" alt="{{ $videoTitle }}" class="yt-thumb-img img-fluid w-100 h-100 object-fit-cover">
                    @endif
                    <div class="yt-thumb-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                        <span class="yt-play-btn-circle d-flex align-items-center justify-content-center rounded-circle">
                            <i class="fa-solid fa-play text-white fs-6 ms-1"></i>
                        </span>
                    </div>
                    <span class="yt-duration-tag badge position-absolute bottom-0 end-0 m-2 px-2 py-1 bg-dark bg-opacity-80 text-white rounded-2 fs-8 fw-bold">
                        HD
                    </span>
                </div>
            </a>

            <div class="p-3">
                <h3 class="h6 mb-2 line-clamp-2" style="font-size: 0.925rem; line-height: 1.35;">
                    <a href="{{ $watchUrl }}" class="text-reset text-decoration-none fw-bold">
                        {{ $videoTitle }}
                    </a>
                </h3>

                <div class="small text-muted d-flex align-items-center gap-2 flex-wrap fs-8">
                    <span><i class="fa-solid fa-eye me-1"></i>{{ number_format($viewsCount) }}</span>
                    <span class="dot-sep">•</span>
                    <span>{{ $timeAgo }}</span>
                    @if($reactionsCount > 0)
                        <span class="dot-sep">•</span>
                        <span class="text-primary fw-medium"><i class="fa-solid fa-heart me-1"></i>{{ $reactionsCount }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
