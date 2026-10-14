@extends('theme::layouts.master')

@section('content')
<div class="video-hub-watch-wrapper py-3">
    <!-- ADS -->
    @include('theme::partials.ads', ['id' => 5])

    @php
        $topicCategoryId = (int) ($topic?->cat ?? 0);
        $videoFile = $videoFile ?? null;
        $ytEmbedUrl = $ytEmbedUrl ?? null;
        $viewsCount = $topic ? (int) ($topic->vu ?? 0) : (int) ($status->views_count ?? 0);
        $timeAgo = \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans();
        $isArabic = app()->getLocale() === 'ar';
        $categoryName = $category->display_name ?? $category->name ?? 'Technology';
        $categoryIcon = $category->icon ?? 'fa-solid fa-film';
        $categoryColor = $category->color ?? '#615dfa';
        $commentsCount = $topic ? $topic->comments()->count() : $comments->count();
    @endphp

    <div class="video-watch-page" id="videoWatchPage">
        <div class="row g-4" id="videoWatchColumns">
            <!-- 1. MAIN PLAYER & INFO COLUMN -->
            <div class="col-lg-8 col-xl-8 transition-all" id="videoMainCol">
                <!-- CINEMA VIDEO PLAYER STAGE -->
                <div class="video-stage-card shadow-lg mb-3 position-relative overflow-hidden rounded-4" id="videoStageCard">
                    <!-- Dynamic Ambient Glow Backdrop -->
                    <div class="v-ambient-backdrop" id="ambientBackdrop_{{ $status->id }}"></div>

                    @if($videoFile)
                        <div class="myads-video-player-container position-relative rounded-4 overflow-hidden" id="playerContainer_{{ $status->id }}" style="z-index: 1;">
                            <video id="videoElement_{{ $status->id }}" class="myads-video-element w-100 h-100 object-fit-cover" poster="{{ $thumbUrl }}" preload="metadata" playsinline>
                                <source src="{{ asset($videoFile->file_path) }}" type="{{ $videoFile->mime_type ?? 'video/mp4' }}">
                                {{ __('messages.browser_no_video_support') ?? 'Your browser does not support HTML5 video.' }}
                            </video>

                            <!-- Big Overlay Play Button -->
                            <div class="video-overlay-play" id="overlayPlay_{{ $status->id }}" onclick="togglePlayPause('{{ $status->id }}')">
                                <div class="play-icon-circle shadow-lg d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-play fs-2 ms-1 text-white"></i>
                                </div>
                            </div>

                            <!-- Custom Mini Floating PiP Controls -->
                            <div class="v-pip-controls-overlay">
                                <button type="button" class="v-pip-btn" onclick="togglePlayPause('{{ $status->id }}')" title="{{ __('messages.play_pause') }}">
                                    <i class="fa fa-play" id="pipPlayIcon_{{ $status->id }}"></i>
                                </button>
                                <button type="button" class="v-pip-btn" onclick="togglePictureInPicture('{{ $status->id }}')" title="Restore">
                                    <i class="fa fa-expand"></i>
                                </button>
                                <button type="button" class="v-pip-btn" onclick="closePip('{{ $status->id }}')" title="Close">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>

                            <!-- Controls Bar -->
                            <div class="video-controls-bar" id="controlsBar_{{ $status->id }}">
                                <!-- Scrubber -->
                                <div class="video-scrubber-container position-relative" id="scrubberContainer_{{ $status->id }}">
                                    <div class="video-scrubber-buffer" id="scrubberBuffer_{{ $status->id }}"></div>
                                    <input type="range" id="scrubberRange_{{ $status->id }}" class="video-scrubber-range" min="0" max="100" value="0" step="0.1" aria-label="Progress">
                                    <div class="v-scrub-tooltip d-none" id="scrubTooltip_{{ $status->id }}">0:00</div>
                                </div>

                                <div class="video-controls-row d-flex align-items-center justify-content-between p-2">
                                    <!-- Left Controls -->
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="v-ctrl-btn" id="btnPlay_{{ $status->id }}" onclick="togglePlayPause('{{ $status->id }}')" title="{{ __('messages.play_pause') }}">
                                            <i class="fa-solid fa-play"></i>
                                        </button>
                                        <button type="button" class="v-ctrl-btn subtle" onclick="skipTime('{{ $status->id }}', -10)" title="-10s">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                        <button type="button" class="v-ctrl-btn subtle" onclick="skipTime('{{ $status->id }}', 10)" title="+10s">
                                            <i class="fa-solid fa-rotate-right"></i>
                                        </button>

                                        <!-- Volume -->
                                        <div class="v-volume-wrap d-flex align-items-center gap-1 ms-1">
                                            <button type="button" class="v-ctrl-btn" id="btnMute_{{ $status->id }}" onclick="toggleMute('{{ $status->id }}')" title="{{ __('messages.mute') }}">
                                                <i class="fa-solid fa-volume-high"></i>
                                            </button>
                                            <input type="range" id="volumeRange_{{ $status->id }}" class="v-volume-slider" min="0" max="1" step="0.05" value="1" oninput="changeVolume('{{ $status->id }}', this.value)">
                                        </div>

                                        <!-- Timestamp -->
                                        <span class="v-time-counter ms-2 text-white font-monospace fs-8" id="timeDisplay_{{ $status->id }}">0:00 / 0:00</span>
                                    </div>

                                    <!-- Right Controls -->
                                    <div class="d-flex align-items-center gap-2">
                                        <!-- Speed -->
                                        <div class="position-relative">
                                            <button type="button" class="v-ctrl-btn subtle fs-8 fw-bold px-2" id="btnSpeed_{{ $status->id }}" onclick="toggleFlyout('speedFlyout_{{ $status->id }}')">
                                                1x
                                            </button>
                                            <div class="v-speed-flyout shadow-lg rounded-3" id="speedFlyout_{{ $status->id }}" style="display:none;">
                                                <div class="v-speed-option" onclick="setSpeed(0.5, '{{ $status->id }}')">0.5x</div>
                                                <div class="v-speed-option" onclick="setSpeed(0.75, '{{ $status->id }}')">0.75x</div>
                                                <div class="v-speed-option active" onclick="setSpeed(1.0, '{{ $status->id }}')">1.0x ({{ __('messages.speed_normal') ?? 'Normal' }})</div>
                                                <div class="v-speed-option" onclick="setSpeed(1.25, '{{ $status->id }}')">1.25x</div>
                                                <div class="v-speed-option" onclick="setSpeed(1.5, '{{ $status->id }}')">1.5x</div>
                                                <div class="v-speed-option" onclick="setSpeed(2.0, '{{ $status->id }}')">2.0x</div>
                                            </div>
                                        </div>

                                        <!-- Theater Mode -->
                                        <button type="button" class="v-ctrl-btn subtle" id="btnTheater_{{ $status->id }}" onclick="toggleTheaterMode()" title="{{ __('messages.theater_mode') ?? 'Theater Mode' }}">
                                            <i class="fa-solid fa-desktop"></i>
                                        </button>

                                        <!-- PiP -->
                                        <button type="button" class="v-ctrl-btn subtle" id="btnPip_{{ $status->id }}" onclick="togglePictureInPicture('{{ $status->id }}')" title="{{ __('messages.pip_mode') ?? 'Picture in Picture' }}">
                                            <i class="fa-regular fa-clone"></i>
                                        </button>

                                        <!-- Fullscreen -->
                                        <button type="button" class="v-ctrl-btn" id="btnFullscreen_{{ $status->id }}" onclick="toggleFullscreen('{{ $status->id }}')" title="{{ __('messages.fullscreen') ?? 'Fullscreen' }}">
                                            <i class="fa-solid fa-expand"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($ytEmbedUrl)
                        <div class="ratio ratio-16x9 video-iframe-box rounded-4 overflow-hidden position-relative" style="z-index: 1;">
                            <iframe src="{{ $ytEmbedUrl }}" title="{{ $videoTitle }}" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                        </div>
                    @else
                        <div class="ratio ratio-16x9 video-placeholder-box rounded-4 overflow-hidden position-relative d-flex flex-column align-items-center justify-content-center text-center p-5 bg-dark" style="z-index: 1;">
                            <img src="{{ $thumbUrl }}" alt="{{ $videoTitle }}" class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover opacity-25">
                            <i class="fa-solid fa-film fa-3x text-primary mb-3 position-relative" style="z-index: 2;"></i>
                            <p class="text-white fw-bold mb-0 position-relative" style="z-index: 2;">{{ __('messages.no_video_stream') }}</p>
                        </div>
                    @endif
                </div>

                <!-- 2. VIDEO TITLE (H1) -->
                <div class="video-watch-headline my-3">
                    <h1 class="h4 fw-extrabold text-light-emphasis line-clamp-2 mb-0" style="line-height: 1.4;">
                        {{ $videoTitle }}
                    </h1>
                </div>

                <!-- 3. CREATOR CHANNEL BAR & ACTION TOOLBAR (YOUTUBE STANDARD) -->
                <div class="v-author-toolbar shadow-sm mb-4">
                    <!-- Creator Channel Profile (Start) -->
                    <div class="v-author-identity">
                        @if($user)
                            <a href="{{ route('profile.short', $user->publicRouteIdentifier()) }}" class="user-avatar small no-outline flex-shrink-0" style="width: 44px; height: 48px; text-decoration: none;">
                                <div class="user-avatar-content">
                                    <div class="hexagon-image-30-32" data-src="{{ $user->img ? url($user->img) : theme_asset('img/avatar.jpg') }}"></div>
                                </div>
                                <div class="user-avatar-progress-border">
                                    <div class="hexagon-border-40-44" data-line-color="{{ $user->profileBadgeColor() }}"></div>
                                </div>
                            </a>

                            <div class="min-w-0">
                                <h2 class="h6 mb-0 fw-bold d-flex align-items-center gap-1 text-truncate">
                                    <a href="{{ route('profile.short', $user->publicRouteIdentifier()) }}" class="text-reset text-decoration-none">
                                        {{ $user->username }}
                                    </a>
                                    @if($user->verified)
                                        <i class="fa-solid fa-circle-check text-primary fs-7" title="{{ __('messages.verified') ?? 'Verified' }}"></i>
                                    @endif
                                </h2>
                                <span class="fs-8 text-muted d-block">
                                    {{ $user->videos_count ?? 1 }} {{ __('messages.videos') ?? 'Videos' }}
                                </span>
                            </div>

                            <!-- Follow / Subscribe Button -->
                            @auth
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('profile.follow', $user->username) }}" method="POST" class="d-inline ms-2">
                                        @csrf
                                        <button type="submit" class="v-subscribe-btn {{ $isFollowing ? 'following' : '' }}">
                                            <i class="fa-solid {{ $isFollowing ? 'fa-check' : 'fa-user-plus' }}"></i>
                                            <span>{{ $isFollowing ? __('messages.following') : __('messages.follow') }}</span>
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="v-subscribe-btn ms-2">
                                    <i class="fa-solid fa-user-plus"></i>
                                    <span>{{ __('messages.follow') }}</span>
                                </a>
                            @endauth
                        @endif
                    </div>

                    <!-- Action Toolbar (Like, Share, Save, Theater, Options) (End) -->
                    <div class="v-action-buttons-group">
                        <!-- Reactions / Like Button -->
                        @auth
                            <div class="position-relative">
                                <button type="button" class="v-btn-pill" onclick="toggleReactionMenu(this)">
                                    <i class="fa-solid fa-heart {{ $isLiked ? 'text-danger' : 'text-muted' }}" id="likeIcon_{{ $status->id }}"></i>
                                    <span id="reactionsCount_{{ $status->id }}">{{ (int) ($status->reactions_count ?? 0) }}</span>
                                </button>
                                <!-- Reaction Dropdown Palette -->
                                <div class="v-reactions-palette shadow-lg rounded-pill p-1 position-absolute" id="reactionsPalette_{{ $status->id }}" style="display:none; bottom: 44px; inset-inline-start: 0; z-index: 9999;">
                                    @foreach(['like' => '👍', 'love' => '❤️', 'happy' => '😄', 'wow' => '😮', 'sad' => '😢', 'angry' => '😡'] as $reactionKey => $reactionEmoji)
                                        <button type="button" class="v-reaction-bubble" onclick="submitVideoReaction({{ $status->tp_id ?: $status->id }}, '{{ $reactionKey }}', '{{ $status->id }}')">
                                            <span>{{ $reactionEmoji }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="v-btn-pill">
                                <i class="fa-solid fa-heart text-danger"></i>
                                <span>{{ (int) ($status->reactions_count ?? 0) }}</span>
                            </a>
                        @endauth

                        <!-- Share Button -->
                        <button type="button" class="v-btn-pill" onclick="openShareModal()">
                            <i class="fa-solid fa-share-nodes text-primary"></i>
                            <span>{{ __('messages.share_video') }}</span>
                        </button>

                        <!-- Save to Watch Later / Favorites -->
                        @auth
                            <button type="button" class="v-btn-pill" onclick="toggleSaveVideo({{ $status->id }}, this)">
                                <i class="{{ $isSaved ? 'fa-solid fa-bookmark text-warning' : 'fa-regular fa-bookmark' }}"></i>
                                <span class="btn-save-text">{{ $isSaved ? __('messages.saved_video') : __('messages.save_video') }}</span>
                            </button>
                        @endauth

                        <!-- Theater Mode Button -->
                        <button type="button" class="v-btn-icon-circle d-none d-md-inline-flex" onclick="toggleTheaterMode()" title="{{ __('messages.theater_mode') }}">
                            <i class="fa-solid fa-desktop"></i>
                        </button>

                        <!-- 3-Dots Quick Menu -->
                        <div class="dropdown">
                            <button type="button" class="v-btn-icon-circle" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('messages.options') ?? 'Options' }}">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border rounded-3 fs-7 py-1">
                                <li>
                                    <button class="dropdown-item py-2 d-flex align-items-center gap-2" type="button" onclick="navigator.clipboard.writeText(window.location.href); showToast('{{ __('messages.link_copied') }}');">
                                        <i class="fa-solid fa-link text-info"></i> {{ __('messages.copy_video_link') }}
                                    </button>
                                </li>
                                @if($videoFile)
                                    <li>
                                        <a class="dropdown-item py-2 d-flex align-items-center gap-2" href="{{ asset($videoFile->file_path) }}" download>
                                            <i class="fa-solid fa-download text-success"></i> {{ __('messages.download') }}
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <button class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" type="button" onclick="reportPost({{ $topic?->id ?? $status->id }}, 2)">
                                        <i class="fa-solid fa-flag"></i> {{ __('messages.report') }}
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 4. EXPANDABLE VIDEO DESCRIPTION CARD -->
                <div class="video-description-card p-4 rounded-4 mb-4 shadow-sm border bg-surface">
                    <!-- Meta stats row -->
                    <div class="d-flex align-items-center gap-3 flex-wrap mb-3 pb-3 border-bottom fs-7">
                        <span class="fw-bold text-light-emphasis d-inline-flex align-items-center gap-1">
                            <i class="fa-solid fa-eye text-primary"></i>
                            {{ __('messages.video_views_count', ['count' => number_format($viewsCount)]) }}
                        </span>
                        <span class="text-muted">•</span>
                        <span class="text-muted d-inline-flex align-items-center gap-1">
                            <i class="fa-regular fa-clock"></i>
                            {{ $timeAgo }}
                        </span>
                        <span class="text-muted">•</span>
                        <!-- Dedicated Video Genre Badge -->
                        <span class="badge rounded-pill px-3 py-1 shadow-xs fw-semibold" style="background-color: {{ $categoryColor }}; color: #fff;">
                            <i class="{{ $categoryIcon }} me-1"></i> {{ $categoryName }}
                        </span>
                    </div>

                    <!-- Description Text with auto formatting -->
                    <div class="video-description-body position-relative" id="videoDescWrapper">
                        <div class="video-desc-content" id="videoDescContent">
                            @if($topic && !empty($topic->txt))
                                {!! \App\Support\ContentFormatter::formatForum($topic->txt) !!}
                            @elseif(!empty($status->txt))
                                {!! nl2br(e($status->txt)) !!}
                            @else
                                <p class="text-muted fst-italic mb-0">{{ __('messages.no_description_provided') }}</p>
                            @endif
                        </div>
                        <button type="button" class="btn btn-link btn-sm p-0 mt-2 text-primary fw-bold text-decoration-none d-none" id="btnToggleDesc" onclick="toggleDescExpand()">
                            {{ __('messages.show_more') }}
                        </button>
                    </div>
                </div>

                <!-- 5. DEDICATED VIDEO COMMENTS SECTION -->
                <div class="video-comments-card p-4 rounded-4 shadow-sm border bg-surface mb-4" id="videoCommentsSection">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h3 class="h5 fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-comments text-primary"></i>
                            <span>{{ __('messages.comments') ?? 'Comments' }}</span>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1 fs-7" id="videoCommentCountBadge">
                                {{ $commentsCount }}
                            </span>
                        </h3>
                    </div>

                    <!-- Add Comment Form -->
                    @auth
                        <form action="{{ route('comment.store') }}" method="POST" class="mb-4" id="videoCommentForm" onsubmit="submitVideoComment(event)">
                            @csrf
                            <input type="hidden" name="id" value="{{ $topic?->id ?? $status->id }}">
                            <input type="hidden" name="type" value="forum">
                            <div class="v-comment-input-card">
                                <div class="user-avatar small no-outline flex-shrink-0" style="width: 40px; height: 44px;">
                                    <div class="user-avatar-content">
                                        <div class="hexagon-image-30-32" data-src="{{ auth()->user()->img ? url(auth()->user()->img) : theme_asset('img/avatar.jpg') }}"></div>
                                    </div>
                                    <div class="user-avatar-progress-border">
                                        <div class="hexagon-border-40-44" data-line-color="{{ auth()->user()->profileBadgeColor() }}"></div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <textarea name="text" id="videoCommentTextarea" class="v-comment-textarea" rows="2" placeholder="{{ __('messages.add_comment_placeholder') }}" required></textarea>
                                    <div class="d-flex justify-content-end mt-2">
                                        <button type="submit" class="v-comment-submit-btn">
                                            <i class="fa-solid fa-paper-plane"></i>
                                            <span>{{ __('messages.comment') ?? 'Comment' }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-light border rounded-4 text-center p-4 mb-4">
                            <p class="mb-2 fw-semibold">{{ __('messages.login_to_comment') }}</p>
                            <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-xs">
                                <i class="fa-solid fa-right-to-bracket me-1"></i> {{ __('messages.login') ?? 'Sign In' }}
                            </a>
                        </div>
                    @endauth

                    <!-- Comments Stream List -->
                    <div class="video-comments-list" id="videoCommentsList">
                        @forelse($comments as $comment)
                            @php
                                $cUser = $comment->user;
                                $cTimeAgo = \Carbon\Carbon::createFromTimestamp($comment->date)->diffForHumans();
                            @endphp
                            <div class="video-comment-item d-flex gap-3 py-3 border-bottom" id="comment_{{ $comment->id }}">
                                @if($cUser)
                                    <a href="{{ route('profile.short', $cUser->publicRouteIdentifier()) }}" class="user-avatar small no-outline flex-shrink-0" style="width: 36px; height: 40px;">
                                        <div class="user-avatar-content">
                                            <div class="hexagon-image-30-32" data-src="{{ $cUser->img ? url($cUser->img) : theme_asset('img/avatar.jpg') }}"></div>
                                        </div>
                                        <div class="user-avatar-progress-border">
                                            <div class="hexagon-border-40-44" data-line-color="{{ $cUser->profileBadgeColor() }}"></div>
                                        </div>
                                    </a>
                                @endif
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        @if($cUser)
                                            <a href="{{ route('profile.short', $cUser->publicRouteIdentifier()) }}" class="text-reset text-decoration-none fw-bold fs-7">
                                                {{ $cUser->username }}
                                            </a>
                                            @if($cUser->verified)
                                                <i class="fa-solid fa-circle-check text-primary fs-8"></i>
                                            @endif
                                        @endif
                                        <span class="text-muted fs-8">{{ $cTimeAgo }}</span>
                                    </div>
                                    <div class="comment-text fs-7 text-light-emphasis">
                                        {!! \App\Support\ContentFormatter::formatForum($comment->txt) !!}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted" id="noCommentsPlaceholder">
                                <i class="fa-regular fa-comment-dots fs-3 mb-2 opacity-50"></i>
                                <p class="mb-0 fs-7">{{ __('messages.no_comments_yet') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- 2. "UP NEXT" / SUGGESTED VIDEOS SIDEBAR -->
            <div class="col-lg-4 col-xl-4 transition-all" id="videoSidebarCol">
                <div class="suggested-videos-card rounded-4 p-3 shadow-sm border bg-surface">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h4 class="h6 fw-extrabold mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-play text-primary"></i>
                            <span>{{ __('messages.up_next') }}</span>
                        </h4>

                        <!-- Custom Robust Autoplay Switch (Immune to theme CSS bugs) -->
                        <div class="v-autoplay-wrap">
                            <span class="v-autoplay-text">{{ __('messages.autoplay') }}</span>
                            <button type="button" class="v-custom-switch active" id="autoplayCustomSwitch" onclick="toggleCustomAutoplay(this)" title="{{ __('messages.autoplay') }}" aria-label="{{ __('messages.autoplay') }}">
                                <span class="v-custom-switch-knob"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Suggested Videos List -->
                    <div class="suggested-videos-list d-flex flex-column gap-2" id="suggestedVideosList">
                        @foreach($suggestedVideos as $suggested)
                            @php
                                $sTopic = $suggested->forumTopic;
                                $sUser = $suggested->user;
                                $sWatchUrl = $suggested->resolved_watch_url ?: route('video.watch', $suggested->id);
                                $sThumb = $suggested->resolved_thumbnail ?: \App\Http\Controllers\VideoHubController::resolveVideoThumbnailUrl($suggested);
                                $sTitle = $suggested->resolved_title ?: \App\Http\Controllers\VideoHubController::resolveVideoTitle($suggested);
                                $sViews = $sTopic ? (int) ($sTopic->vu ?? 0) : (int) ($suggested->views_count ?? 0);
                                $sTimeAgo = \Carbon\Carbon::createFromTimestamp($suggested->date)->diffForHumans();
                                $sIsClip = (int) $suggested->s_type === 14;
                            @endphp
                            <a href="{{ $sWatchUrl }}" class="v-suggested-card suggested-video-row">
                                <!-- 16:9 Thumbnail -->
                                <div class="v-suggested-thumb flex-shrink-0 bg-dark">
                                    <img src="{{ $sThumb }}" alt="{{ $sTitle }}" onError="this.onerror=null;this.src='{{ theme_asset('img/video-placeholder.svg') }}';">
                                    <div class="position-absolute bottom-0 end-0 m-1">
                                        <span class="badge bg-dark bg-opacity-80 text-white px-1 py-0 rounded" style="font-size: 10px; font-weight: 700;">
                                            {{ $sIsClip ? 'CLIP' : 'HD' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Metadata -->
                                <div class="v-suggested-meta min-w-0">
                                    <h5 class="v-suggested-title suggested-title">
                                        {{ $sTitle }}
                                    </h5>
                                    @if($sUser)
                                        <div class="v-suggested-author">
                                            {{ $sUser->username }}
                                            @if($sUser->verified)
                                                <i class="fa-solid fa-circle-check text-primary" style="font-size: 10px;"></i>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="v-suggested-stats">
                                        <span>{{ __('messages.video_views_count', ['count' => number_format($sViews)]) }}</span>
                                        <span>•</span>
                                        <span>{{ $sTimeAgo }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODERN SHARE MODAL -->
<div class="modal fade" id="videoShareModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border bg-surface shadow-lg">
            <div class="modal-header border-0 pb-0 d-flex align-items-center justify-content-between">
                <h5 class="modal-title fw-bold mb-0">
                    <i class="fa-solid fa-share-nodes text-primary me-2"></i> {{ __('messages.share_video') }}
                </h5>
                <button type="button" class="btn-close shadow-none" onclick="closeShareModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Social Sharing Buttons Grid -->
                <div class="d-flex justify-content-center gap-3 flex-wrap mb-4">
                    <button type="button" class="btn btn-outline-success rounded-circle p-3 shadow-xs" onclick="shareSocial('whatsapp')" title="WhatsApp" style="width: 52px; height: 52px;">
                        <i class="fa-brands fa-whatsapp fs-4"></i>
                    </button>
                    <button type="button" class="btn btn-outline-dark rounded-circle p-3 shadow-xs" onclick="shareSocial('twitter')" title="X / Twitter" style="width: 52px; height: 52px;">
                        <i class="fa-brands fa-x-twitter fs-4"></i>
                    </button>
                    <button type="button" class="btn btn-outline-primary rounded-circle p-3 shadow-xs" onclick="shareSocial('facebook')" title="Facebook" style="width: 52px; height: 52px;">
                        <i class="fa-brands fa-facebook fs-4"></i>
                    </button>
                    <button type="button" class="btn btn-outline-info rounded-circle p-3 shadow-xs" onclick="shareSocial('telegram')" title="Telegram" style="width: 52px; height: 52px;">
                        <i class="fa-brands fa-telegram fs-4"></i>
                    </button>
                </div>

                <!-- Copy Link Input Group -->
                <label class="form-label fs-7 fw-bold text-muted">{{ __('messages.copy_video_link') }}</label>
                <div class="input-group mb-3 rounded-pill overflow-hidden border">
                    <input type="text" id="shareUrlInput" class="form-control bg-surface border-0 fs-7 shadow-none" readonly value="{{ url()->current() }}">
                    <button class="btn btn-primary px-3 fw-bold" type="button" onclick="copyShareUrl()">
                        <i class="fa-solid fa-copy me-1"></i> {{ __('messages.copy') ?? 'Copy' }}
                    </button>
                </div>

                <!-- Embed Code Box -->
                <label class="form-label fs-7 fw-bold text-muted">{{ __('messages.embed_video') }}</label>
                <textarea class="form-control bg-surface border rounded-3 fs-8 font-monospace shadow-none" rows="2" readonly>&lt;iframe src="{{ route('video.watch', $status->id) }}" width="560" height="315" frameborder="0" allowfullscreen&gt;&lt;/iframe&gt;</textarea>
            </div>
        </div>
    </div>
</div>

<style>
/* SCOPED VIDEO DESIGN SYSTEM WITH BULLETPROOF OVERRIDES */
.video-hub-watch-wrapper {
    max-width: 1480px;
    margin: 0 auto;
}

/* OVERRIDE VIKINGER FULL-WIDTH BUTTON DEFAULTS */
.video-watch-page button,
.video-watch-page .btn,
.video-watch-page .v-btn-pill,
.video-watch-page .v-subscribe-btn,
.video-watch-page .v-ctrl-btn,
.video-watch-page .v-btn-icon-circle {
    width: auto !important;
    display: inline-flex !important;
    flex: 0 0 auto !important;
    margin: 0 !important;
}

/* THEATER MODE */
.video-watch-page.theater-mode #videoMainCol {
    width: 100% !important;
    max-width: 100% !important;
    flex: 0 0 100% !important;
}
.video-watch-page.theater-mode #videoStageCard {
    max-height: 85vh !important;
}
.video-watch-page.theater-mode .myads-video-player-container {
    max-height: 85vh !important;
}
.video-watch-page.theater-mode #videoSidebarCol {
    width: 100% !important;
    max-width: 100% !important;
    flex: 0 0 100% !important;
    margin-top: 24px;
}

/* AMBIENT GLOW BACKDROP */
.v-ambient-backdrop {
    position: absolute;
    top: -20px; left: -20px; right: -20px; bottom: -20px;
    background: radial-gradient(circle at center, rgba(99, 102, 241, 0.35) 0%, rgba(6, 182, 212, 0.15) 50%, transparent 80%);
    filter: blur(45px);
    opacity: 0.75;
    z-index: 0;
    pointer-events: none;
    transition: opacity 0.4s ease;
}

/* STAGE CARD & PLAYER */
.video-stage-card {
    background: #000;
    border: 1px solid rgba(255, 255, 255, 0.08);
}
.myads-video-player-container {
    background: #000;
    aspect-ratio: 16 / 9;
}
.myads-video-element {
    width: 100%;
    height: 100%;
    cursor: pointer;
}

/* OVERLAY BIG PLAY BUTTON */
.video-overlay-play {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.2);
    cursor: pointer;
    transition: opacity 0.25s ease;
    z-index: 2;
}
.play-icon-circle {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: rgba(99, 102, 241, 0.9);
    backdrop-filter: blur(8px);
    transition: transform 0.25s ease, background 0.25s ease;
}
.video-overlay-play:hover .play-icon-circle {
    transform: scale(1.15);
    background: #4f46e5;
}
.myads-video-player-container.is-playing .video-overlay-play {
    opacity: 0;
    pointer-events: none;
}

/* CUSTOM CONTROLS BAR */
.video-controls-bar {
    position: absolute;
    bottom: 0; left: 0; width: 100%;
    background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.85) 100%);
    opacity: 0;
    transition: opacity 0.25s ease;
    z-index: 5;
    padding-bottom: 4px;
}
.myads-video-player-container:hover .video-controls-bar {
    opacity: 1;
}

/* SCRUBBER */
.video-scrubber-container {
    width: 100%;
    height: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    padding: 0 12px;
}
.video-scrubber-range {
    -webkit-appearance: none;
    width: 100%;
    height: 4px;
    background: rgba(255, 255, 255, 0.25);
    border-radius: 2px;
    outline: none;
    cursor: pointer;
    transition: height 0.15s ease;
}
.video-scrubber-container:hover .video-scrubber-range {
    height: 7px;
}
.video-scrubber-range::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #6366f1;
    cursor: pointer;
    box-shadow: 0 0 10px rgba(99, 102, 241, 0.8);
    transition: transform 0.15s ease;
}
.video-scrubber-container:hover .video-scrubber-range::-webkit-slider-thumb {
    transform: scale(1.2);
}

/* BUTTONS IN CONTROLS */
.v-ctrl-btn {
    background: transparent !important;
    border: none !important;
    color: #fff !important;
    width: 34px !important;
    height: 34px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 50% !important;
    cursor: pointer !important;
    transition: transform 0.15s ease, background 0.15s ease !important;
}
.v-ctrl-btn:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    transform: scale(1.1) !important;
}
.v-ctrl-btn.subtle {
    font-size: 13px !important;
    color: rgba(255, 255, 255, 0.8) !important;
}
.v-volume-slider {
    -webkit-appearance: none;
    width: 60px;
    height: 4px;
    border-radius: 2px;
    background: rgba(255, 255, 255, 0.4);
    outline: none;
    cursor: pointer;
}
.v-volume-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #fff;
    cursor: pointer;
}

/* SPEED FLYOUT */
.v-speed-flyout {
    position: absolute;
    bottom: 42px;
    inset-inline-end: 0;
    background: #111420;
    border: 1px solid rgba(255, 255, 255, 0.15);
    padding: 6px;
    min-width: 130px;
    z-index: 999;
}
.v-speed-option {
    padding: 6px 12px;
    color: #fff;
    font-size: 12px;
    cursor: pointer;
    border-radius: 6px;
    transition: background 0.15s ease;
}
.v-speed-option:hover, .v-speed-option.active {
    background: #6366f1;
}

/* MINI PIP FLOATING */
.myads-video-player-container.v-mini-floating-player {
    position: fixed !important;
    bottom: 24px !important;
    inset-inline-end: 24px !important;
    width: 360px !important;
    height: 202px !important;
    z-index: 99990 !important;
    border-radius: 16px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6) !important;
    border: 2px solid rgba(99, 102, 241, 0.5) !important;
}
.v-pip-controls-overlay {
    display: none;
    position: absolute;
    top: 8px; inset-inline-end: 8px;
    z-index: 99995;
    gap: 6px;
}
.myads-video-player-container.v-mini-floating-player .v-pip-controls-overlay {
    display: flex !important;
}
.v-pip-btn {
    width: 32px !important; height: 32px !important;
    border-radius: 50% !important;
    background: rgba(0, 0, 0, 0.7) !important;
    color: #fff !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    display: flex !important; align-items: center !important; justify-content: center !important;
    cursor: pointer !important;
    font-size: 12px !important;
}

/* AUTHOR TOOLBAR & BUTTONS (PERFECT ROW ALIGNMENT) */
.v-author-toolbar {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: wrap !important;
    gap: 16px !important;
    padding: 14px 18px !important;
    border-radius: 18px !important;
    background: var(--notification-ui-card-bg, #fff) !important;
    border: 1px solid var(--notification-ui-card-border, #e5e7eb) !important;
}
.v-author-toolbar button,
.v-author-toolbar form {
    width: auto !important;
    margin: 0 !important;
}
.v-author-identity {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    flex: 0 0 auto !important;
}
.v-subscribe-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    height: 36px !important;
    padding: 0 18px !important;
    border-radius: 9999px !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    background: #615dfa !important;
    color: #fff !important;
    border: none !important;
    box-shadow: 0 3px 12px rgba(97, 93, 250, 0.35) !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    width: auto !important;
    margin: 0 !important;
}
.v-subscribe-btn.following {
    background: rgba(0, 0, 0, 0.08) !important;
    color: inherit !important;
    box-shadow: none !important;
}
[data-theme="css_d"] .v-subscribe-btn.following {
    background: rgba(255, 255, 255, 0.12) !important;
    color: #fff !important;
}
.v-action-buttons-group {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
}
.v-action-buttons-group > *,
.v-action-buttons-group button,
.v-action-buttons-group .dropdown,
.v-action-buttons-group form {
    width: auto !important;
    margin: 0 !important;
}
.v-btn-pill {
    width: auto !important;
    margin: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    padding: 8px 16px !important;
    height: 38px !important;
    border-radius: 9999px !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    background: rgba(0, 0, 0, 0.04) !important;
    color: inherit !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    white-space: nowrap !important;
    text-decoration: none !important;
}
[data-theme="css_d"] .v-btn-pill {
    background: rgba(255, 255, 255, 0.08) !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
    color: #f1f5f9 !important;
}
.v-btn-pill:hover {
    background: rgba(97, 93, 250, 0.15) !important;
    border-color: rgba(97, 93, 250, 0.4) !important;
    color: #615dfa !important;
    transform: translateY(-1px) !important;
}
.v-btn-icon-circle {
    width: 38px !important;
    height: 38px !important;
    padding: 0 !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(0, 0, 0, 0.04) !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    color: inherit !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
}
[data-theme="css_d"] .v-btn-icon-circle {
    background: rgba(255, 255, 255, 0.08) !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
    color: #f1f5f9 !important;
}
.v-btn-icon-circle:hover {
    background: rgba(97, 93, 250, 0.15) !important;
    border-color: rgba(97, 93, 250, 0.4) !important;
    color: #615dfa !important;
}

/* UNIVERSAL STANDALONE DROPDOWN STYLING */
.dropdown {
    position: relative !important;
}
.dropdown:has(.dropdown-menu.show),
.dropdown.show {
    z-index: 1060 !important;
}
.dropdown-menu {
    position: absolute !important;
    top: 100% !important;
    inset-inline-end: 0 !important;
    inset-inline-start: auto !important;
    z-index: 1050 !important;
    display: none;
    min-width: 180px !important;
    margin-top: 6px !important;
    background: var(--notification-ui-card-bg, #ffffff) !important;
    border: 1px solid var(--notification-ui-card-border, #e2e8f0) !important;
    border-radius: 14px !important;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.18) !important;
    padding: 6px 0 !important;
}
.dropdown-menu.show {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
}
.dropdown-item {
    display: flex !important;
    align-items: center !important;
    width: 100% !important;
    padding: 9px 16px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    color: inherit !important;
    background: transparent !important;
    border: none !important;
    text-decoration: none !important;
    cursor: pointer !important;
    transition: background 0.15s ease, color 0.15s ease !important;
}
.dropdown-item:hover, .dropdown-item:focus {
    background: rgba(97, 93, 250, 0.1) !important;
    color: #615dfa !important;
}
[data-theme="css_d"] .dropdown-menu {
    background: #1c2233 !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
    color: #f1f5f9 !important;
}
[data-theme="css_d"] .dropdown-item {
    color: #cbd5e1 !important;
}
[data-theme="css_d"] .dropdown-item:hover {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #fff !important;
}

/* STANDALONE MODAL ENGINE */
.modal {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    overflow-x: hidden !important;
    overflow-y: auto !important;
    outline: 0 !important;
    z-index: 99999 !important;
    display: none;
}
.modal.fade {
    opacity: 0;
    transition: opacity 0.2s ease-in-out;
}
.modal.show {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    opacity: 1 !important;
}
.modal-backdrop {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background-color: rgba(0, 0, 0, 0.65) !important;
    backdrop-filter: blur(6px) !important;
    z-index: 99990 !important;
    opacity: 0;
    transition: opacity 0.2s ease-in-out;
}
.modal-backdrop.show {
    opacity: 1 !important;
}
.modal-dialog {
    max-width: 520px !important;
    width: 90% !important;
    margin: 1.75rem auto !important;
    z-index: 99999 !important;
    position: relative !important;
}
.modal-content {
    background: var(--notification-ui-card-bg, #ffffff) !important;
    border: 1px solid var(--notification-ui-card-border, #e2e8f0) !important;
}
[data-theme="css_d"] .modal-content {
    background: #1a202c !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
    color: #f1f5f9 !important;
}
.btn-close {
    background: transparent !important;
    border: 0 !important;
    cursor: pointer !important;
    font-size: 1.25rem !important;
    line-height: 1 !important;
    padding: 0.5rem !important;
    color: inherit !important;
    opacity: 0.7 !important;
}
.btn-close:hover {
    opacity: 1 !important;
}
.btn-close::before {
    content: "✕";
    font-family: inherit;
    font-weight: bold;
}

/* CUSTOM AUTOPLAY SWITCH */
.v-autoplay-wrap {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
}
.v-autoplay-text {
    font-size: 12px !important;
    font-weight: 600 !important;
    color: #888 !important;
    white-space: nowrap !important;
}
.v-custom-switch {
    width: 44px !important;
    height: 24px !important;
    background: #cbd5e1 !important;
    border-radius: 9999px !important;
    padding: 2px !important;
    cursor: pointer !important;
    border: none !important;
    display: inline-flex !important;
    align-items: center !important;
    transition: background 0.25s ease !important;
    position: relative !important;
}
.v-custom-switch.active {
    background: #615dfa !important;
}
.v-custom-switch-knob {
    width: 20px !important;
    height: 20px !important;
    background: #fff !important;
    border-radius: 50% !important;
    display: block !important;
    transition: transform 0.25s ease !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3) !important;
}
.v-custom-switch.active .v-custom-switch-knob {
    transform: translateX(20px) !important;
}
html[dir="rtl"] .v-custom-switch.active .v-custom-switch-knob {
    transform: translateX(-20px) !important;
}

/* COMMENT SECTION */
.v-comment-input-card {
    display: flex !important;
    gap: 14px !important;
    align-items: flex-start !important;
}
.v-comment-textarea {
    width: 100% !important;
    border-radius: 14px !important;
    padding: 14px 16px !important;
    font-size: 14px !important;
    background: var(--notification-ui-card-bg, #fff) !important;
    border: 1px solid var(--notification-ui-card-border, #e5e7eb) !important;
    color: inherit !important;
    min-height: 80px !important;
    resize: vertical !important;
    outline: none !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
}
.v-comment-textarea:focus {
    border-color: #615dfa !important;
    box-shadow: 0 0 0 3px rgba(97, 93, 250, 0.15) !important;
}
.v-comment-submit-btn {
    width: auto !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 8px !important;
    height: 38px !important;
    padding: 0 22px !important;
    border-radius: 9999px !important;
    background: linear-gradient(135deg, #615dfa 0%, #4b45e4 100%) !important;
    color: #fff !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    box-shadow: 0 4px 14px rgba(97, 93, 250, 0.3) !important;
    cursor: pointer !important;
    border: none !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease !important;
}
.v-comment-submit-btn:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 18px rgba(97, 93, 250, 0.45) !important;
}

/* SUGGESTED VIDEOS SIDEBAR */
.v-suggested-card {
    display: flex !important;
    gap: 12px !important;
    align-items: flex-start !important;
    padding: 8px !important;
    border-radius: 12px !important;
    text-decoration: none !important;
    color: inherit !important;
    transition: background 0.2s ease !important;
}
.v-suggested-card:hover {
    background: rgba(97, 93, 250, 0.08) !important;
}
.v-suggested-thumb {
    width: 140px !important;
    height: 79px !important;
    border-radius: 10px !important;
    overflow: hidden !important;
    position: relative !important;
    flex-shrink: 0 !important;
    background: #000 !important;
}
.v-suggested-thumb img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    transition: transform 0.25s ease !important;
}
.v-suggested-card:hover .v-suggested-thumb img {
    transform: scale(1.06) !important;
}
.v-suggested-meta {
    flex: 1 1 auto !important;
    min-width: 0 !important;
}
.v-suggested-title {
    font-size: 13px !important;
    font-weight: 700 !important;
    line-height: 1.35 !important;
    margin-bottom: 4px !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
    overflow: hidden !important;
    color: inherit !important;
}
.v-suggested-card:hover .v-suggested-title {
    color: #615dfa !important;
}
.v-suggested-author {
    font-size: 12px !important;
    color: #888 !important;
    margin-bottom: 2px !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}
.v-suggested-stats {
    font-size: 11px !important;
    color: #999 !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
}

/* REACTIONS PALETTE */
.v-reactions-palette {
    background: #1e2235;
    border: 1px solid rgba(255, 255, 255, 0.15);
    display: flex;
    gap: 4px;
}
.v-reaction-bubble {
    width: 34px !important; height: 34px !important;
    border-radius: 50% !important;
    background: transparent !important;
    border: none !important;
    display: flex !important; align-items: center !important; justify-content: center !important;
    cursor: pointer !important;
    font-size: 18px !important;
    transition: transform 0.15s ease !important;
}
.v-reaction-bubble:hover {
    transform: scale(1.3) !important;
}

.fs-8 { font-size: 12px; }
.fs-7 { font-size: 13px; }
</style>

<script>
// PLAY / PAUSE
function togglePlayPause(statusId) {
    const video = document.getElementById('videoElement_' + statusId);
    const container = document.getElementById('playerContainer_' + statusId);
    const btnPlay = document.getElementById('btnPlay_' + statusId);
    if (!video) return;

    if (video.paused) {
        video.play();
        if (container) container.classList.add('is-playing');
        if (btnPlay) btnPlay.innerHTML = '<i class="fa-solid fa-pause"></i>';
    } else {
        video.pause();
        if (container) container.classList.remove('is-playing');
        if (btnPlay) btnPlay.innerHTML = '<i class="fa-solid fa-play"></i>';
    }
}

// SKIP TIME
function skipTime(statusId, seconds) {
    const video = document.getElementById('videoElement_' + statusId);
    if (video) video.currentTime += seconds;
}

// VOLUME & MUTE
function toggleMute(statusId) {
    const video = document.getElementById('videoElement_' + statusId);
    const btnMute = document.getElementById('btnMute_' + statusId);
    if (!video) return;
    video.muted = !video.muted;
    if (btnMute) {
        btnMute.innerHTML = video.muted ? '<i class="fa-solid fa-volume-xmark"></i>' : '<i class="fa-solid fa-volume-high"></i>';
    }
}
function changeVolume(statusId, val) {
    const video = document.getElementById('videoElement_' + statusId);
    if (video) {
        video.volume = val;
        video.muted = (val == 0);
    }
}

// SPEED
function toggleFlyout(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
function setSpeed(speed, statusId) {
    const video = document.getElementById('videoElement_' + statusId);
    const btnSpeed = document.getElementById('btnSpeed_' + statusId);
    if (video) video.playbackRate = speed;
    if (btnSpeed) btnSpeed.textContent = speed + 'x';
    toggleFlyout('speedFlyout_' + statusId);
}

// THEATER MODE
function toggleTheaterMode() {
    const page = document.getElementById('videoWatchPage');
    if (page) {
        page.classList.toggle('theater-mode');
        window.dispatchEvent(new Event('resize'));
    }
}

// PICTURE IN PICTURE
function togglePictureInPicture(statusId) {
    const container = document.getElementById('playerContainer_' + statusId);
    const video = document.getElementById('videoElement_' + statusId);
    if (!container) return;

    if (document.pictureInPictureElement) {
        document.exitPictureInPicture().catch(() => {});
    } else if (video && video.requestPictureInPicture) {
        video.requestPictureInPicture().catch(() => {
            container.classList.toggle('v-mini-floating-player');
        });
    } else {
        container.classList.toggle('v-mini-floating-player');
    }
}
function closePip(statusId) {
    const container = document.getElementById('playerContainer_' + statusId);
    if (container) container.classList.remove('v-mini-floating-player');
}

// FULLSCREEN
function toggleFullscreen(statusId) {
    const container = document.getElementById('playerContainer_' + statusId);
    if (!container) return;
    if (!document.fullscreenElement) {
        container.requestFullscreen().catch(() => {});
    } else {
        document.exitFullscreen().catch(() => {});
    }
}

// TIME & SCRUBBER UPDATER
document.addEventListener('DOMContentLoaded', function() {
    const statusId = '{{ $status->id }}';
    const video = document.getElementById('videoElement_' + statusId);
    const scrubber = document.getElementById('scrubberRange_' + statusId);
    const timeDisplay = document.getElementById('timeDisplay_' + statusId);

    if (video && scrubber) {
        video.addEventListener('timeupdate', function() {
            if (video.duration) {
                const percent = (video.currentTime / video.duration) * 100;
                scrubber.value = percent;
                if (timeDisplay) {
                    timeDisplay.textContent = formatTime(video.currentTime) + ' / ' + formatTime(video.duration);
                }
            }
        });

        scrubber.addEventListener('input', function() {
            if (video.duration) {
                video.currentTime = (scrubber.value / 100) * video.duration;
            }
        });

        // Autoplay next when ended
        video.addEventListener('ended', function() {
            const sw = document.getElementById('autoplayCustomSwitch');
            if (sw && sw.classList.contains('active')) {
                const nextRow = document.querySelector('.suggested-video-row');
                if (nextRow && nextRow.href) {
                    window.location.href = nextRow.href;
                }
            }
        });
    }

    // Expand description button check
    const descContent = document.getElementById('videoDescContent');
    const btnToggle = document.getElementById('btnToggleDesc');
    if (descContent && btnToggle && descContent.scrollHeight > 120) {
        descContent.style.maxHeight = '100px';
        descContent.style.overflow = 'hidden';
        btnToggle.classList.remove('d-none');
    }
});

function formatTime(seconds) {
    const m = Math.floor(seconds / 60);
    const s = Math.floor(seconds % 60);
    return m + ':' + (s < 10 ? '0' : '') + s;
}

function toggleDescExpand() {
    const descContent = document.getElementById('videoDescContent');
    const btnToggle = document.getElementById('btnToggleDesc');
    if (!descContent || !btnToggle) return;
    if (descContent.style.maxHeight === 'none') {
        descContent.style.maxHeight = '100px';
        btnToggle.textContent = '{{ __('messages.show_more') }}';
    } else {
        descContent.style.maxHeight = 'none';
        btnToggle.textContent = '{{ __('messages.show_less') }}';
    }
}

// CUSTOM AUTOPLAY TOGGLE
function toggleCustomAutoplay(btn) {
    btn.classList.toggle('active');
}

// REACTIONS
function toggleReactionMenu(btn) {
    const palette = document.getElementById('reactionsPalette_{{ $status->id }}');
    if (palette) palette.style.display = palette.style.display === 'none' ? 'flex' : 'none';
}
function submitVideoReaction(topicId, reaction, statusId) {
    const palette = document.getElementById('reactionsPalette_' + statusId);
    if (palette) palette.style.display = 'none';

    fetch('{{ route("reaction.toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ id: topicId, type: 'forum', reaction: reaction })
    }).then(r => r.json()).then(data => {
        const countEl = document.getElementById('reactionsCount_' + statusId);
        const iconEl = document.getElementById('likeIcon_' + statusId);
        if (iconEl) iconEl.classList.add('text-danger');
        if (countEl && data.count !== undefined) countEl.textContent = data.count;
    }).catch(() => {});
}

// SHARE MODAL (ZERO-DEPENDENCY ENGINE)
function openShareModal() {
    const modal = document.getElementById('videoShareModal');
    if (!modal) return;
    
    let backdrop = document.getElementById('videoModalBackdrop');
    if (!backdrop) {
        backdrop = document.createElement('div');
        backdrop.id = 'videoModalBackdrop';
        backdrop.className = 'modal-backdrop fade';
        document.body.appendChild(backdrop);
        backdrop.addEventListener('click', closeShareModal);
    }
    
    document.body.classList.add('modal-open');
    modal.style.display = 'flex';
    void modal.offsetWidth; // Force reflow
    modal.classList.add('show');
    backdrop.classList.add('show');
}

function closeShareModal() {
    const modal = document.getElementById('videoShareModal');
    const backdrop = document.getElementById('videoModalBackdrop');
    if (modal) {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 200);
    }
    if (backdrop) {
        backdrop.classList.remove('show');
        setTimeout(() => {
            backdrop.remove();
        }, 200);
    }
    document.body.classList.remove('modal-open');
}

function copyShareUrl() {
    const input = document.getElementById('shareUrlInput');
    if (input) {
        input.select();
        navigator.clipboard.writeText(input.value);
        showToast('{{ __('messages.link_copied') }}');
    }
}

function shareSocial(network) {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    let shareLink = '';
    if (network === 'whatsapp') shareLink = 'https://api.whatsapp.com/send?text=' + title + '%20' + url;
    if (network === 'twitter') shareLink = 'https://twitter.com/intent/tweet?text=' + title + '&url=' + url;
    if (network === 'facebook') shareLink = 'https://www.facebook.com/sharer/sharer.php?u=' + url;
    if (network === 'telegram') shareLink = 'https://t.me/share/url?url=' + url + '&text=' + title;
    if (shareLink) window.open(shareLink, '_blank', 'width=600,height=400');
}

// SAVE TO WATCH LATER / FAVORITES
function toggleSaveVideo(statusId, btn) {
    fetch("{{ route('clips.save.toggle') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ status_id: statusId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const icon = btn.querySelector('i');
            const label = btn.querySelector('.btn-save-text');
            if (data.action === 'added') {
                if (icon) icon.className = 'fa-solid fa-bookmark text-warning';
                if (label) label.textContent = "{{ __('messages.saved_video') }}";
                showToast("{{ __('messages.video_saved_toast') ?? __('messages.saved_video') }}");
            } else {
                if (icon) icon.className = 'fa-regular fa-bookmark';
                if (label) label.textContent = "{{ __('messages.save_video') }}";
                showToast("{{ __('messages.video_unsaved_toast') ?? __('messages.save_video') }}");
            }
        }
    })
    .catch(() => {});
}

// ZERO-DEPENDENCY DROPDOWNS ENGINE
function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-menu.show').forEach(m => {
        m.classList.remove('show');
        const trigger = m.closest('.dropdown')?.querySelector('[data-bs-toggle="dropdown"], [data-toggle="dropdown"], .dropdown-toggle');
        if (trigger) trigger.setAttribute('aria-expanded', 'false');
    });
}

function initDropdownToggles() {
    document.addEventListener('click', function(e) {
        const toggleBtn = e.target.closest('[data-bs-toggle="dropdown"], [data-toggle="dropdown"], .dropdown-toggle');
        if (toggleBtn) {
            e.preventDefault();
            e.stopPropagation();
            const dropdown = toggleBtn.closest('.dropdown');
            if (!dropdown) return;
            const menu = dropdown.querySelector('.dropdown-menu');
            if (!menu) return;
            
            const isCurrentlyShown = menu.classList.contains('show');
            closeAllDropdowns();
            
            if (!isCurrentlyShown) {
                menu.classList.add('show');
                toggleBtn.setAttribute('aria-expanded', 'true');
            }
            return;
        }

        const item = e.target.closest('.dropdown-item');
        if (item) {
            closeAllDropdowns();
            return;
        }

        if (!e.target.closest('.dropdown-menu')) {
            closeAllDropdowns();
        }
    });

    // Close on Escape or click on modal backdrop
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeShareModal();
            closeAllDropdowns();
        }
    });

    document.addEventListener('click', function(e) {
        const modal = document.getElementById('videoShareModal');
        if (modal && modal.classList.contains('show') && e.target === modal) {
            closeShareModal();
        }
    });
}

// COMMENTS AJAX SUBMIT
function submitVideoComment(e) {
    e.preventDefault();
    const form = e.target;
    const textarea = document.getElementById('videoCommentTextarea');
    const text = textarea ? textarea.value.trim() : '';
    if (!text) return;

    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    }).then(r => r.json()).then(res => {
        if (res.error) {
            alert(res.error);
            return;
        }
        if (textarea) textarea.value = '';
        
        // Remove no comments placeholder if present
        const placeholder = document.getElementById('noCommentsPlaceholder');
        if (placeholder) placeholder.remove();

        // Increment count badge
        const badge = document.getElementById('videoCommentCountBadge');
        if (badge) {
            badge.textContent = parseInt(badge.textContent || '0') + 1;
        }

        // Prepend comment HTML
        const list = document.getElementById('videoCommentsList');
        if (list) {
            const tempDiv = document.createElement('div');
            tempDiv.className = 'video-comment-item d-flex gap-3 py-3 border-bottom animate__animated animate__fadeIn';
            tempDiv.innerHTML = `
                <div class="user-avatar small no-outline flex-shrink-0" style="width: 36px; height: 40px;">
                    <div class="user-avatar-content">
                        <div class="hexagon-image-30-32" data-src="{{ auth()->check() && auth()->user()->img ? url(auth()->user()->img) : theme_asset('img/avatar.jpg') }}"></div>
                    </div>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="fw-bold fs-7">{{ auth()->check() ? auth()->user()->username : 'Me' }}</span>
                        <span class="text-muted fs-8">{{ __('messages.just_now') }}</span>
                    </div>
                    <div class="comment-text fs-7 text-light-emphasis">${text.replace(/</g, "&lt;").replace(/>/g, "&gt;")}</div>
                </div>
            `;
            list.insertBefore(tempDiv, list.firstChild);
        }
    }).catch(err => {
        form.submit();
    });
}

// MODERN STANDALONE TOAST NOTIFICATION
function showToast(msg) {
    let toast = document.getElementById('vToastNotification');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'vToastNotification';
        toast.style.cssText = `
            position: fixed;
            bottom: 24px;
            inset-inline-start: 50%;
            transform: translateX(-50%) translateY(20px);
            background: rgba(15, 23, 42, 0.94);
            color: #ffffff;
            padding: 10px 22px;
            border-radius: 9999px;
            font-size: 14px;
            font-weight: 600;
            z-index: 100000;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
            backdrop-filter: blur(8px);
            opacity: 0;
            pointer-events: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        `;
        document.body.appendChild(toast);
    }
    toast.innerHTML = `<i class="fa-solid fa-circle-check" style="color: #10b981;"></i> <span>${msg}</span>`;
    toast.style.opacity = '1';
    toast.style.transform = 'translateX(-50%) translateY(0)';
    
    if (window._vToastTimer) clearTimeout(window._vToastTimer);
    window._vToastTimer = setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(20px)';
    }, 2800);
}

// INITIALIZE ON LOAD
document.addEventListener('DOMContentLoaded', function() {
    initDropdownToggles();
});
</script>
@endsection
