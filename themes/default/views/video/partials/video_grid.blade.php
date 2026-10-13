@if($videos->count() > 0)
    <div class="row g-4" id="videoGridItems">
        @foreach($videos as $status)
            @php
                $topic = $status->forumTopic;
                $user = $status->user;
                $watchUrl = $topic ? route('forum.topic', $topic->id) : url('/portal');
                
                // Multi-source resolved Thumbnail & Video URL & Title
                $thumbUrl = $status->resolved_thumbnail ?: \App\Http\Controllers\VideoHubController::resolveVideoThumbnailUrl($status);
                $videoUrl = $status->resolved_video_url ?: \App\Http\Controllers\VideoHubController::resolveVideoUrl($status);
                $videoTitle = $status->resolved_title ?: \App\Http\Controllers\VideoHubController::resolveVideoTitle($status);

                // Views / Reactions / Date
                $viewsCount = $topic ? (int) ($topic->vu ?? 0) : (int) ($status->views_count ?? 0);
                $reactionsCount = (int) ($status->reactions_count ?? 0);
                $timeAgo = $status->date_formatted ?: ($status->created_at ? $status->created_at->diffForHumans() : '');
                $isClip = (int) $status->s_type === 14;
                $hasCustomThumb = $thumbUrl && !str_contains($thumbUrl, 'video-placeholder');
                $ytId = \App\Http\Controllers\VideoHubController::extractYoutubeId($videoUrl);

                $categoryName = $topic?->category?->name;
                $categoryColor = $topic?->category?->color;
            @endphp

            <div class="col-12 col-sm-6 col-md-4 col-xl-3 video-grid-col" data-status-id="{{ $status->id }}">
                <div class="yt-video-card h-100 shadow-sm d-flex flex-column" data-id="{{ $status->id }}">
                    <!-- Thumbnail Stage 16:9 -->
                    <a href="{{ $watchUrl }}" class="yt-thumb-stage d-block text-decoration-none position-relative" aria-label="{{ $videoTitle }}">
                        <div class="yt-thumb-wrapper ratio ratio-16x9 position-relative overflow-hidden rounded-3 bg-dark" @if($videoUrl) data-video-url="{{ $videoUrl }}" @endif @if($ytId) data-yt-id="{{ $ytId }}" @endif>
                            @if($hasCustomThumb)
                                <img src="{{ $thumbUrl }}" alt="{{ $videoTitle }}" class="yt-thumb-img img-fluid w-100 h-100 object-fit-cover lazyload" style="transition: opacity 0.25s ease;" onError="this.onerror=null;this.src='{{ theme_asset('img/video-placeholder.svg') }}';">
                            @else
                                <img src="{{ theme_asset('img/video-placeholder.svg') }}" alt="{{ $videoTitle }}" class="yt-thumb-img img-fluid w-100 h-100 object-fit-cover" style="transition: opacity 0.25s ease;">
                            @endif

                            @if($videoUrl && !$ytId)
                                <video src="{{ $videoUrl }}#t=0.5" preload="metadata" muted playsinline loop class="yt-preview-video position-absolute top-0 start-0 w-100 h-100 object-fit-cover pointer-events-none" style="opacity: 0; z-index: 2; transition: opacity 0.25s ease;"></video>
                            @endif

                            <!-- Play Overlay with Glassmorphic Ripple -->
                            <div class="yt-thumb-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="z-index: 3;">
                                <span class="yt-play-btn-circle d-flex align-items-center justify-content-center rounded-circle shadow-lg">
                                    <i class="fa-solid fa-play text-white fs-5 ms-1"></i>
                                </span>
                            </div>

                            <!-- Category / Type Badges -->
                            <div class="position-absolute top-0 start-0 m-2 d-flex gap-1" style="z-index: 4;">
                                @if($categoryName)
                                    <span class="badge rounded-pill px-2 py-1 fs-8 fw-semibold text-white shadow-xs" style="background-color: {{ $categoryColor ?: 'rgba(0,0,0,0.65)' }}; backdrop-filter: blur(4px);">
                                        {{ $categoryName }}
                                    </span>
                                @endif
                            </div>

                            <!-- Duration / Clip Sticker -->
                            <div class="position-absolute bottom-0 end-0 m-2" style="z-index: 4;">
                                @if($isClip)
                                    <span class="yt-duration-tag badge px-2 py-1 bg-danger text-white rounded-2 fs-7 fw-bold shadow-xs d-inline-flex align-items-center gap-1">
                                        <i class="fa-solid fa-bolt"></i> CLIP
                                    </span>
                                @else
                                    <span class="yt-duration-tag badge px-2 py-1 bg-dark bg-opacity-80 text-white rounded-2 fs-7 fw-bold shadow-xs">
                                        HD
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>

                    <!-- Video Details & Publisher -->
                    <div class="yt-card-body pt-3 d-flex align-items-start gap-2 flex-grow-1">
                        <!-- Publisher Avatar (MYADS Hexagon Standard) -->
                        @if($user)
                            <a href="{{ route('profile.short', $user->publicRouteIdentifier()) }}" class="user-avatar small no-outline flex-shrink-0" style="width: 36px; height: 40px; text-decoration: none;" title="{{ $user->username }}">
                                <div class="user-avatar-content">
                                    <div class="hexagon-image-30-32" data-src="{{ $user->img ? url($user->img) : theme_asset('img/avatar.jpg') }}"></div>
                                </div>
                                <div class="user-avatar-progress-border">
                                    <div class="hexagon-border-40-44" data-line-color="{{ $user->profileBadgeColor() }}"></div>
                                </div>
                            </a>
                        @endif

                        <div class="yt-card-meta flex-grow-1 min-w-0">
                            <!-- Video Title -->
                            <h3 class="yt-video-title h6 mb-1 text-truncate-2" style="line-height: 1.35;">
                                <a href="{{ $watchUrl }}" class="text-reset text-decoration-none fw-bold" title="{{ $videoTitle }}">
                                    {{ $videoTitle }}
                                </a>
                            </h3>

                            <!-- Creator Channel Name -->
                            @if($user)
                                <div class="yt-channel-name small text-muted d-flex align-items-center gap-1 mb-1">
                                    <a href="{{ route('profile.short', $user->publicRouteIdentifier()) }}" class="text-muted text-decoration-none fw-semibold text-truncate" style="max-width: 140px;">
                                        {{ $user->username }}
                                    </a>
                                    @if($user->verified)
                                        <i class="fa-solid fa-circle-check text-primary fs-7" title="{{ __('messages.verified') }}"></i>
                                    @endif
                                </div>
                            @endif

                            <!-- Meta Stats (Views, Relative Date, Reactions) -->
                            <div class="yt-meta-stats small text-muted d-flex align-items-center gap-2 flex-wrap fs-8">
                                <span>{{ __('messages.video_views_count', ['count' => number_format($viewsCount)]) }}</span>
                                <span class="dot-sep">•</span>
                                <span>{{ $timeAgo }}</span>
                                @if($reactionsCount > 0)
                                    <span class="dot-sep">•</span>
                                    <span class="text-primary fw-medium d-inline-flex align-items-center gap-1">
                                        <i class="fa-solid fa-heart text-danger"></i> {{ $reactionsCount }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Quick Actions Menu -->
                        <div class="dropdown flex-shrink-0">
                            <button class="btn btn-sm btn-icon text-muted p-1 border-0 rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('messages.options') }}">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border rounded-3 fs-7 py-1">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ $watchUrl }}">
                                        <i class="fa-solid fa-play text-primary"></i> {{ __('messages.watch_now') }}
                                    </a>
                                </li>
                                <li>
                                    <button class="dropdown-item d-flex align-items-center gap-2 py-2" type="button" onclick="navigator.clipboard.writeText('{{ $watchUrl }}'); if(typeof showToast === 'function') { showToast('{{ __('messages.link_copied') }}'); }">
                                        <i class="fa-solid fa-link text-info"></i> {{ __('messages.copy_video_link') }}
                                    </button>
                                </li>
                                @auth
                                    <li>
                                        <button class="dropdown-item d-flex align-items-center gap-2 py-2" type="button" onclick="if(typeof toggleSaveVideo === 'function'){ toggleSaveVideo({{ $status->id }}, this); }">
                                            <i class="fa-regular fa-bookmark text-warning"></i> {{ __('messages.save_video') }}
                                        </button>
                                    </li>
                                @endauth
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination / Load More -->
    @if($videos->hasPages())
        <div class="yt-pagination-wrap mt-5 d-flex flex-column align-items-center gap-3">
            <button type="button" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold shadow-xs d-inline-flex align-items-center gap-2" id="btnLoadMoreVideos" data-next-url="{{ $videos->nextPageUrl() }}">
                <i class="fa-solid fa-spinner fa-spin d-none" id="loadMoreSpinner"></i>
                <i class="fa-solid fa-arrow-down" id="loadMoreIcon"></i>
                <span>{{ __('messages.load_more_videos') }}</span>
            </button>
            <div class="d-none" id="standardPagination">
                {{ $videos->links() }}
            </div>
        </div>
    @endif
@else
    <div class="yt-empty-state text-center py-5 px-3 rounded-4 shadow-sm bg-surface border">
        <div class="yt-empty-icon mb-3 text-primary display-4">
            <i class="fa-solid fa-video-slash"></i>
        </div>
        <h4 class="fw-bold mb-2">{{ __('messages.no_videos_found') }}</h4>
        <p class="text-muted mb-4 max-w-md mx-auto">{{ __('messages.no_videos_desc') }}</p>
        @auth
            <a href="{{ url('/share') }}" class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-xs d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-cloud-arrow-up"></i> {{ __('messages.create_video') }}
            </a>
        @endauth
    </div>
@endif
