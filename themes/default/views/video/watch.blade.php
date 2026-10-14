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
        $categoryColor = $category->color ?? '#6366f1';
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
                                                <div class="v-speed-option active" onclick="setSpeed(1.0, '{{ $status->id }}')">1.0x (Normal)</div>
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
                            <p class="text-white fw-bold mb-0 position-relative" style="z-index: 2;">{{ __('messages.no_video_stream') ?? 'No playable stream detected' }}</p>
                        </div>
                    @endif
                </div>

                <!-- 2. VIDEO TITLE (H1) -->
                <div class="video-watch-headline mb-3">
                    <h1 class="h3 fw-extrabold text-light-emphasis line-clamp-2 mb-2" style="line-height: 1.35;">
                        {{ $videoTitle }}
                    </h1>
                </div>

                <!-- 3. CREATOR CHANNEL BAR & ACTION TOOLBAR (YOUTUBE STANDARD) -->
                <div class="video-channel-action-card p-3 rounded-4 mb-4 shadow-sm border bg-surface">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <!-- Creator Channel Profile -->
                        <div class="d-flex align-items-center gap-3">
                            @if($user)
                                <a href="{{ route('profile.short', $user->publicRouteIdentifier()) }}" class="user-avatar small no-outline flex-shrink-0" style="width: 44px; height: 48px; text-decoration: none;">
                                    <div class="user-avatar-content">
                                        <div class="hexagon-image-30-32" data-src="{{ $user->img ? url($user->img) : theme_asset('img/avatar.jpg') }}"></div>
                                    </div>
                                    <div class="user-avatar-progress-border">
                                        <div class="hexagon-border-40-44" data-line-color="{{ $user->profileBadgeColor() }}"></div>
                                    </div>
                                </a>

                                <div>
                                    <h2 class="h6 mb-0 fw-bold d-flex align-items-center gap-1">
                                        <a href="{{ route('profile.short', $user->publicRouteIdentifier()) }}" class="text-reset text-decoration-none">
                                            {{ $user->username }}
                                        </a>
                                        @if($user->verified)
                                            <i class="fa-solid fa-circle-check text-primary fs-7" title="{{ __('messages.verified') }}"></i>
                                        @endif
                                    </h2>
                                    <span class="fs-8 text-muted">
                                        {{ $user->videos_count ?? 1 }} {{ __('messages.videos') ?? 'Videos' }}
                                    </span>
                                </div>

                                <!-- Follow / Subscribe Button -->
                                @auth
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('profile.follow', $user->username) }}" method="POST" class="d-inline ms-2">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $isFollowing ? 'btn-secondary' : 'btn-primary' }} rounded-pill px-3 fw-bold shadow-xs d-inline-flex align-items-center gap-1">
                                                <i class="fa-solid {{ $isFollowing ? 'fa-check' : 'fa-user-plus' }}"></i>
                                                <span>{{ $isFollowing ? __('messages.following') : __('messages.follow') }}</span>
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-xs ms-2">
                                        + {{ __('messages.follow') }}
                                    </a>
                                @endauth
                            @endif
                        </div>

                        <!-- Action Toolbar (Like, Share, Save, Options) -->
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <!-- Reactions / Like Button -->
                            @auth
                                <div class="position-relative">
                                    <button type="button" class="btn btn-sm btn-light bg-surface border rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-xs" onclick="toggleReactionMenu(this)">
                                        <i class="fa-solid fa-heart {{ $isLiked ? 'text-danger' : 'text-muted' }}" id="likeIcon_{{ $status->id }}"></i>
                                        <span id="reactionsCount_{{ $status->id }}" class="fw-bold">{{ (int) ($status->reactions_count ?? 0) }}</span>
                                    </button>
                                    <!-- Reaction Dropdown Palette -->
                                    <div class="v-reactions-palette shadow-lg rounded-pill p-1 position-absolute" id="reactionsPalette_{{ $status->id }}" style="display:none; bottom: 44px; inset-inline-start: 0; z-index: 9999;">
                                        @foreach(['like' => '👍', 'love' => '❤️', 'happy' => '😄', 'wow' => '😮', 'sad' => '😢', 'angry' => '😡'] as $reactionKey => $reactionEmoji)
                                            <button type="button" class="btn btn-icon rounded-circle p-1 v-reaction-bubble" onclick="submitVideoReaction({{ $status->tp_id ?: $status->id }}, '{{ $reactionKey }}', '{{ $status->id }}')">
                                                <span class="fs-5">{{ $reactionEmoji }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-sm btn-light bg-surface border rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-xs">
                                    <i class="fa-solid fa-heart text-danger"></i>
                                    <span>{{ (int) ($status->reactions_count ?? 0) }}</span>
                                </a>
                            @endauth

                            <!-- Share Button -->
                            <button type="button" class="btn btn-sm btn-light bg-surface border rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-xs" onclick="openShareModal()">
                                <i class="fa-solid fa-share-nodes text-primary"></i>
                                <span>{{ __('messages.share_video') ?? 'Share' }}</span>
                            </button>

                            <!-- Save to Watch Later / Favorites -->
                            @auth
                                <button type="button" class="btn btn-sm btn-light bg-surface border rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-xs" onclick="toggleSaveVideo({{ $status->id }}, this)">
                                    <i class="{{ $isSaved ? 'fa-solid fa-bookmark text-warning' : 'fa-regular fa-bookmark' }}"></i>
                                    <span class="btn-save-text">{{ $isSaved ? __('messages.saved_video') : __('messages.save_video') }}</span>
                                </button>
                            @endauth

                            <!-- Theater Mode Button -->
                            <button type="button" class="btn btn-sm btn-light bg-surface border rounded-circle p-2 shadow-xs d-none d-md-inline-flex" onclick="toggleTheaterMode()" title="{{ __('messages.theater_mode') ?? 'Theater Mode' }}">
                                <i class="fa-solid fa-desktop text-muted"></i>
                            </button>

                            <!-- 3-Dots Quick Menu -->
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-light bg-surface border rounded-circle p-2 shadow-xs" data-bs-toggle="dropdown" aria-expanded="false" title="{{ __('messages.options') }}">
                                    <i class="fa-solid fa-ellipsis-vertical text-muted"></i>
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
                                                <i class="fa-solid fa-download text-success"></i> {{ __('messages.download') ?? 'Download' }}
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <button class="dropdown-item py-2 d-flex align-items-center gap-2 text-danger" type="button" onclick="reportPost({{ $topic?->id ?? $status->id }}, 2)">
                                            <i class="fa-solid fa-flag"></i> {{ __('messages.report') ?? 'Report' }}
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. EXPANDABLE VIDEO DESCRIPTION CARD -->
                <div class="video-description-card p-4 rounded-4 mb-4 shadow-sm border bg-surface">
                    <!-- Meta stats row -->
                    <div class="d-flex align-items-center gap-3 flex-wrap mb-3 pb-3 border-bottom fs-7">
                        <span class="fw-bold text-light-emphasis d-inline-flex align-items-center gap-1">
                            <i class="fa-solid fa-eye text-primary"></i>
                            {{ number_format($viewsCount) }} {{ __('messages.views') ?? 'views' }}
                        </span>
                        <span class="text-muted">•</span>
                        <span class="text-muted d-inline-flex align-items-center gap-1">
                            <i class="fa-regular fa-clock"></i>
                            {{ $timeAgo }}
                        </span>
                        <span class="text-muted">•</span>
                        <!-- Dedicated Video Genre Badge (NOT Forum Category) -->
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
                                <p class="text-muted fst-italic mb-0">{{ __('messages.no_description_provided') ?? 'No description provided for this video.' }}</p>
                            @endif
                        </div>
                        <button type="button" class="btn btn-link btn-sm p-0 mt-2 text-primary fw-bold text-decoration-none d-none" id="btnToggleDesc" onclick="toggleDescExpand()">
                            {{ __('messages.show_more') ?? 'Show more' }}
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
                                {{ $topic ? $topic->comments()->count() : $comments->count() }}
                            </span>
                        </h3>
                    </div>

                    <!-- Add Comment Form -->
                    @auth
                        <form action="{{ route('comment.store') }}" method="POST" class="video-comment-form mb-4" id="videoCommentForm" onsubmit="submitVideoComment(event)">
                            @csrf
                            <input type="hidden" name="id" value="{{ $topic?->id ?? $status->id }}">
                            <input type="hidden" name="type" value="forum">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="user-avatar small no-outline flex-shrink-0" style="width: 36px; height: 40px;">
                                    <div class="user-avatar-content">
                                        <div class="hexagon-image-30-32" data-src="{{ auth()->user()->img ? url(auth()->user()->img) : theme_asset('img/avatar.jpg') }}"></div>
                                    </div>
                                    <div class="user-avatar-progress-border">
                                        <div class="hexagon-border-40-44" data-line-color="{{ auth()->user()->profileBadgeColor() }}"></div>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <textarea name="text" id="videoCommentTextarea" class="form-control bg-surface border rounded-3 p-3 fs-6 shadow-none" rows="2" placeholder="{{ __('messages.add_comment_placeholder') ?? 'Add a comment...' }}" required></textarea>
                                    <div class="d-flex justify-content-end gap-2 mt-2">
                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-xs">
                                            <i class="fa-solid fa-paper-plane me-1"></i> {{ __('messages.comment') ?? 'Comment' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-light border rounded-4 text-center p-4 mb-4">
                            <p class="mb-2 fw-semibold">{{ __('messages.login_to_comment') ?? 'Please sign in to join the discussion.' }}</p>
                            <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-xs">
                                <i class="fa-solid fa-right-to-bracket me-1"></i> {{ __('messages.login') }}
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
                                    <a href="{{ route('profile.short', $cUser->publicRouteIdentifier()) }}" class="user-avatar small no-outline flex-shrink-0" style="width: 34px; height: 38px;">
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
                                <p class="mb-0 fs-7">{{ __('messages.no_comments_yet') ?? 'No comments yet. Be the first to comment!' }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- 2. "UP NEXT" / SUGGESTED VIDEOS SIDEBAR (YOUTUBE STANDARD) -->
            <div class="col-lg-4 col-xl-4 transition-all" id="videoSidebarCol">
                <div class="suggested-videos-card rounded-4 p-3 shadow-sm border bg-surface">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <h4 class="h6 fw-extrabold mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-play text-primary"></i>
                            <span>{{ __('messages.up_next') ?? 'Up Next' }}</span>
                        </h4>

                        <!-- Autoplay Toggle Switch -->
                        <div class="form-check form-switch mb-0 d-flex align-items-center gap-2">
                            <label class="form-check-label fs-8 text-muted" for="autoplayToggle">{{ __('messages.autoplay') ?? 'Autoplay' }}</label>
                            <input class="form-check-input shadow-none cursor-pointer" type="checkbox" role="switch" id="autoplayToggle" checked>
                        </div>
                    </div>

                    <!-- Suggested Videos List -->
                    <div class="suggested-videos-list d-flex flex-column gap-3" id="suggestedVideosList">
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
                                $sCategory = $suggested->resolved_video_category ?: \App\Http\Controllers\VideoHubController::resolveVideoCategory($suggested);
                            @endphp
                            <a href="{{ $sWatchUrl }}" class="suggested-video-row d-flex gap-2 text-decoration-none group rounded-3 p-1 transition-all">
                                <!-- 16:9 Thumbnail -->
                                <div class="suggested-thumb-wrap ratio ratio-16x9 rounded-3 overflow-hidden flex-shrink-0 bg-dark" style="width: 140px;">
                                    <img src="{{ $sThumb }}" alt="{{ $sTitle }}" class="w-100 h-100 object-fit-cover transition-transform" onError="this.onerror=null;this.src='{{ theme_asset('img/video-placeholder.svg') }}';">
                                    <div class="position-absolute bottom-0 end-0 m-1">
                                        <span class="badge bg-dark bg-opacity-75 text-white fs-9 px-1 py-0 rounded">
                                            {{ $sIsClip ? 'CLIP' : 'HD' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Metadata -->
                                <div class="suggested-meta min-w-0 flex-grow-1">
                                    <h5 class="h6 mb-1 fs-7 fw-bold text-light-emphasis line-clamp-2 suggested-title" style="line-height: 1.3;">
                                        {{ $sTitle }}
                                    </h5>
                                    @if($sUser)
                                        <div class="fs-8 text-muted text-truncate mb-1">
                                            {{ $sUser->username }}
                                            @if($sUser->verified)
                                                <i class="fa-solid fa-circle-check text-primary fs-9"></i>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="fs-8 text-muted d-flex align-items-center gap-1">
                                        <span>{{ number_format($sViews) }} {{ __('messages.views') ?? 'views' }}</span>
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
<div class="modal fade" id="videoShareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border bg-surface shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-share-nodes text-primary me-2"></i> {{ __('messages.share_video') ?? 'Share Video' }}
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <label class="form-label fs-7 fw-bold text-muted">{{ __('messages.copy_video_link') ?? 'Copy Link' }}</label>
                <div class="input-group mb-3 rounded-pill overflow-hidden border">
                    <input type="text" id="shareUrlInput" class="form-control bg-surface border-0 fs-7 shadow-none" readonly value="{{ url()->current() }}">
                    <button class="btn btn-primary px-3 fw-bold" type="button" onclick="copyShareUrl()">
                        <i class="fa-solid fa-copy me-1"></i> {{ __('messages.copy') ?? 'Copy' }}
                    </button>
                </div>

                <!-- Embed Code Box -->
                <label class="form-label fs-7 fw-bold text-muted">{{ __('messages.embed_video') ?? 'Embed Code' }}</label>
                <textarea class="form-control bg-surface border rounded-3 fs-8 font-monospace shadow-none" rows="2" readonly>&lt;iframe src="{{ route('video.watch', $status->id) }}" width="560" height="315" frameborder="0" allowfullscreen&gt;&lt;/iframe&gt;</textarea>
            </div>
        </div>
    </div>
</div>

<style>
/* ULTRA-MODERN VIDEO WATCH PAGE DESIGN SYSTEM */
.video-hub-watch-wrapper {
    max-width: 1480px;
    margin: 0 auto;
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
    background: transparent;
    border: none;
    color: #fff;
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    cursor: pointer;
    transition: transform 0.15s ease, background 0.15s ease;
}
.v-ctrl-btn:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: scale(1.1);
}
.v-ctrl-btn.subtle {
    font-size: 13px;
    color: rgba(255, 255, 255, 0.8);
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
    width: 32px; height: 32px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.7);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    font-size: 12px;
}

/* SUGGESTED ROW HOVER */
.suggested-video-row:hover {
    background: rgba(99, 102, 241, 0.08);
}
.suggested-video-row:hover .suggested-title {
    color: #6366f1 !important;
}
.suggested-video-row:hover img {
    transform: scale(1.05);
}

/* REACTIONS PALETTE */
.v-reactions-palette {
    background: #1e2235;
    border: 1px solid rgba(255, 255, 255, 0.15);
    display: flex;
    gap: 4px;
}
.v-reaction-bubble {
    width: 34px; height: 34px;
    transition: transform 0.15s ease;
}
.v-reaction-bubble:hover {
    transform: scale(1.3);
}

.fs-9 { font-size: 10px; }
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
            const toggle = document.getElementById('autoplayToggle');
            if (toggle && toggle.checked) {
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
        btnToggle.textContent = '{{ __('messages.show_more') ?? 'Show more' }}';
    } else {
        descContent.style.maxHeight = 'none';
        btnToggle.textContent = '{{ __('messages.show_less') ?? 'Show less' }}';
    }
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
        if (countEl && data.count) countEl.textContent = data.count;
    }).catch(() => {});
}

// SHARE MODAL
function openShareModal() {
    const modalEl = document.getElementById('videoShareModal');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
}
function copyShareUrl() {
    const input = document.getElementById('shareUrlInput');
    if (input) {
        input.select();
        navigator.clipboard.writeText(input.value);
        showToast('{{ __('messages.link_copied') ?? 'Link copied!' }}');
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
                <div class="user-avatar small no-outline flex-shrink-0" style="width: 34px; height: 38px;">
                    <div class="user-avatar-content">
                        <div class="hexagon-image-30-32" data-src="{{ auth()->check() && auth()->user()->img ? url(auth()->user()->img) : theme_asset('img/avatar.jpg') }}"></div>
                    </div>
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="fw-bold fs-7">{{ auth()->check() ? auth()->user()->username : 'Me' }}</span>
                        <span class="text-muted fs-8">{{ __('messages.just_now') ?? 'Just now' }}</span>
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

function showToast(msg) {
    if (typeof window.showToast === 'function') {
        window.showToast(msg);
    } else {
        alert(msg);
    }
}
</script>
@endsection
