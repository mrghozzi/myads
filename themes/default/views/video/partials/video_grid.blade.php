@if($videos->count() > 0)
    <div class="row g-4" id="videoGridItems">
        @foreach($videos as $status)
            @php
                $topic = $status->forumTopic;
                $user = $status->user;
                $watchUrl = $topic ? route('forum.topic', $topic->id) : url('/portal');
                
                // Resolved Thumbnail & Title & Video URL
                $thumbUrl = $status->resolved_thumbnail ?: \App\Http\Controllers\VideoHubController::resolveVideoThumbnailUrl($status);
                $videoUrl = $status->resolved_video_url ?: \App\Http\Controllers\VideoHubController::resolveVideoUrl($status);
                $videoTitle = \Illuminate\Support\Str::limit($status->resolved_title ?: \App\Http\Controllers\VideoHubController::resolveVideoTitle($status), 60);

                // Views / Reactions / Date
                $viewsCount = $topic ? (int) ($topic->vu ?? 0) : (int) ($status->views_count ?? 0);
                $reactionsCount = (int) ($status->reactions_count ?? 0);
                $timeAgo = $status->date_formatted ?: ($status->created_at ? $status->created_at->diffForHumans() : '');
                $isClip = (int) $status->s_type === 14;
                $hasCustomThumb = $thumbUrl && !str_contains($thumbUrl, 'video-placeholder');
            @endphp

            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="yt-video-card h-100 shadow-sm" data-id="{{ $status->id }}">
                    <!-- Thumbnail Container 16:9 -->
                    <a href="{{ $watchUrl }}" class="yt-thumb-stage d-block text-decoration-none">
                        <div class="yt-thumb-wrapper ratio ratio-16x9 position-relative overflow-hidden rounded-3 bg-dark">
                            @if($hasCustomThumb)
                                <img src="{{ $thumbUrl }}" alt="{{ $videoTitle }}" class="yt-thumb-img img-fluid w-100 h-100 object-fit-cover lazyload" onError="this.onerror=null;this.src='{{ theme_asset('img/video-placeholder.svg') }}';">
                            @elseif($videoUrl)
                                <video src="{{ $videoUrl }}#t=0.5" preload="metadata" muted playsinline class="yt-thumb-img img-fluid w-100 h-100 object-fit-cover pointer-events-none"></video>
                            @else
                                <img src="{{ theme_asset('img/video-placeholder.svg') }}" alt="{{ $videoTitle }}" class="yt-thumb-img img-fluid w-100 h-100 object-fit-cover">
                            @endif
                            <div class="yt-thumb-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                <span class="yt-play-btn-circle d-flex align-items-center justify-content-center rounded-circle">
                                    <i class="fa-solid fa-play text-white fs-5 ms-1"></i>
                                </span>
                            </div>
                            @if($isClip)
                                <span class="yt-duration-tag badge position-absolute bottom-0 end-0 m-2 px-2 py-1 bg-danger text-white rounded-2 fs-7 fw-bold shadow-xs">
                                    <i class="fa-solid fa-bolt me-1"></i> CLIP
                                </span>
                            @else
                                <span class="yt-duration-tag badge position-absolute bottom-0 end-0 m-2 px-2 py-1 bg-dark bg-opacity-80 text-white rounded-2 fs-7 fw-bold shadow-xs">
                                    HD
                                </span>
                            @endif
                        </div>
                    </a>

                    <!-- Video Details & Publisher -->
                    <div class="yt-card-body pt-3 d-flex align-items-start gap-2">
                        <!-- Publisher Avatar (Vikinger Hexagon Styling) -->
                        @if($user)
                            <a href="{{ route('profile.short', $user->publicRouteIdentifier()) }}" class="user-avatar small no-outline flex-shrink-0" style="width: 36px; height: 40px; text-decoration: none;">
                                <div class="user-avatar-content">
                                    <div class="hexagon-image-30-32" data-src="{{ $user->img ? url($user->img) : theme_asset('img/avatar.jpg') }}"></div>
                                </div>
                                <div class="user-avatar-progress-border">
                                    <div class="hexagon-border-40-44" data-line-color="{{ $user->profileBadgeColor() }}"></div>
                                </div>
                            </a>
                        @endif

                        <div class="yt-card-meta flex-grow-1 min-w-0">
                            <h3 class="yt-video-title h6 mb-1 text-truncate-2">
                                <a href="{{ $watchUrl }}" class="text-reset text-decoration-none fw-bold">
                                    {{ $videoTitle }}
                                </a>
                            </h3>

                            @if($user)
                                <div class="yt-channel-name small text-muted d-flex align-items-center gap-1 mb-1">
                                    <a href="{{ route('profile.short', $user->publicRouteIdentifier()) }}" class="text-muted text-decoration-none fw-semibold">
                                        {{ $user->username }}
                                    </a>
                                    @if($user->verified)
                                        <i class="fa-solid fa-circle-check text-primary fs-7" title="{{ __('messages.verified') }}"></i>
                                    @endif
                                </div>
                            @endif

                            <div class="yt-meta-stats small text-muted d-flex align-items-center gap-2 flex-wrap fs-8">
                                <span>{{ __('messages.video_views_count', ['count' => number_format($viewsCount)]) }}</span>
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
            </div>
        @endforeach
    </div>

    <!-- Pagination Links -->
    <div class="yt-pagination-wrap mt-5 d-flex justify-content-center">
        {{ $videos->links() }}
    </div>
@else
    <div class="yt-empty-state text-center py-5 px-3 rounded-4 shadow-sm bg-surface">
        <div class="yt-empty-icon mb-3 text-primary display-4">
            <i class="fa-solid fa-video-slash"></i>
        </div>
        <h4 class="fw-bold mb-2">{{ __('messages.no_videos_found') }}</h4>
        <p class="text-muted mb-4 max-w-md mx-auto">{{ __('messages.no_videos_desc') }}</p>
        @auth
            <a href="{{ url('/share') }}" class="btn btn-primary px-4 py-2 rounded-pill fw-bold">
                <i class="fa-solid fa-cloud-arrow-up me-2"></i> {{ __('messages.create_video') }}
            </a>
        @endauth
    </div>
@endif
