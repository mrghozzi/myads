@foreach($activities as $clip)
    @php
        $clipTopic = $clip->forumTopic;
        $clipUrl = route('clips.index') . '#' . $clip->id;
        
        $clipThumb = $clip->resolved_thumbnail ?: \App\Http\Controllers\VideoHubController::resolveVideoThumbnailUrl($clip);
        $clipVideoUrl = $clip->resolved_video_url ?: \App\Http\Controllers\VideoHubController::resolveVideoUrl($clip);
        $clipTitle = \Illuminate\Support\Str::limit($clip->resolved_title ?: \App\Http\Controllers\VideoHubController::resolveVideoTitle($clip), 40);
        $clipViews = $clipTopic ? (int) ($clipTopic->vu ?? 0) : (int) ($clip->views_count ?? 0);
        $hasClipCustomThumb = $clipThumb && !str_contains($clipThumb, 'video-placeholder');
    @endphp

    <div class="col">
        <div class="yt-shorts-card profile-yt-shorts-card rounded-4 position-relative overflow-hidden shadow-xs h-100 bg-dark" style="aspect-ratio: 9/16;" @if($clipVideoUrl) data-video-url="{{ $clipVideoUrl }}" @endif>
            <a href="{{ $clipUrl }}" class="d-block w-100 h-100 text-decoration-none">
                @if($hasClipCustomThumb)
                    <img src="{{ $clipThumb }}" alt="{{ $clipTitle }}" class="w-100 h-100 object-fit-cover opacity-90 lazyload" onError="this.onerror=null;this.src='{{ theme_asset('img/video-placeholder.svg') }}';">
                @elseif($clipVideoUrl)
                    <video src="{{ $clipVideoUrl }}#t=0.5" preload="metadata" muted playsinline class="w-100 h-100 object-fit-cover opacity-90 pointer-events-none"></video>
                @else
                    <img src="{{ theme_asset('img/video-placeholder.svg') }}" alt="{{ $clipTitle }}" class="w-100 h-100 object-fit-cover opacity-90">
                @endif
                
                <!-- Dark Gradient Scrim -->
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-between p-3" style="background: linear-gradient(180deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.1) 40%, rgba(0,0,0,0.85) 100%);">
                    <!-- Top Shorts Icon -->
                    <div class="d-flex justify-content-end">
                        <span class="badge bg-danger rounded-circle p-2 shadow-xs">
                            <i class="fa-solid fa-bolt text-white"></i>
                        </span>
                    </div>

                    <!-- Bottom Info -->
                    <div class="yt-shorts-info text-white">
                        <p class="clip-title small fw-bold mb-1 line-clamp-2 text-white" style="line-height: 1.3;">
                            {{ $clipTitle }}
                        </p>
                        <div class="small text-white-50 fs-8">
                            <i class="fa-solid fa-eye me-1"></i> {{ number_format($clipViews) }}
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endforeach
