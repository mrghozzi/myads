@extends('theme::layouts.master')
@include('theme::forum._assets')

@section('content')
<style>
/* MINI FLOATING PICTURE-IN-PICTURE (PIP) STYLES */
.myads-video-player-container.v-mini-floating-player {
    position: fixed !important;
    bottom: 24px !important;
    inset-inline-end: 24px !important;
    width: 360px !important;
    height: 202px !important;
    z-index: 99990 !important;
    border-radius: 14px !important;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45) !important;
    animation: pipSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
    overflow: hidden !important;
    border: 2px solid rgba(97, 93, 250, 0.4) !important;
}

@keyframes pipSlideIn {
    from { transform: translateY(40px) scale(0.9); opacity: 0; }
    to { transform: translateY(0) scale(1); opacity: 1; }
}

.v-pip-controls-overlay {
    display: none;
    position: absolute;
    top: 8px;
    inset-inline-end: 8px;
    z-index: 99995;
    gap: 6px;
}

.myads-video-player-container.v-mini-floating-player .v-pip-controls-overlay {
    display: flex !important;
}

.v-pip-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.65);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    cursor: pointer;
    backdrop-filter: blur(4px);
    transition: all 0.2s ease;
}

.v-pip-btn:hover {
    background: #615dfa;
    color: #fff;
    transform: scale(1.1);
}

/* THEATER MODE STYLING */
.video-watch-page.theater-mode #videoMainCol {
    width: 100% !important;
    max-width: 100% !important;
    flex: 0 0 100% !important;
}
.video-watch-page.theater-mode #videoStageCard {
    max-height: 80vh !important;
}
.video-watch-page.theater-mode .myads-video-player-container {
    max-height: 80vh !important;
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
    background: radial-gradient(circle at center, rgba(97, 93, 250, 0.25) 0%, rgba(35, 210, 226, 0.12) 50%, transparent 80%);
    filter: blur(40px);
    opacity: 0.7;
    z-index: 0;
    pointer-events: none;
    transition: opacity 0.4s ease;
}
</style>

<div class="forum-rdx video-watch-page" id="videoWatchPage">
    <!-- ADS -->
    @include('theme::partials.ads', ['id' => 5])

    @php
        $group = $group ?? null;
        $showForumRoleBadges = (int) ($forumSettings['show_role_badges'] ?? 1) === 1;
        $topicCategoryId = (int) $topic->cat;
        $groupAccess = app(\App\Services\GroupAccessService::class);
        $canManageGroupTopic = $group && auth()->check() ? $groupAccess->canManageGroup($group, auth()->user()) : false;
        $canEditTopic = auth()->check() && (
            auth()->id() === (int) $topic->uid
            || $canManageGroupTopic
            || auth()->user()->canModerateForum('edit_topics', $topicCategoryId)
        );
        $canDeleteTopic = auth()->check() && (
            auth()->id() === (int) $topic->uid
            || $canManageGroupTopic
            || auth()->user()->canModerateForum('delete_topics', $topicCategoryId)
        );
        $canPinTopic = auth()->check() && ($canManageGroupTopic || auth()->user()->canModerateForum('pin_topics', $topicCategoryId));
        $canLockTopic = auth()->check() && ($canManageGroupTopic || auth()->user()->canModerateForum('lock_topics', $topicCategoryId));
        $canCommentWhenLocked = auth()->check() && (
            auth()->id() === (int) $topic->uid
            || $canManageGroupTopic
            || auth()->user()->canModerateForum('lock_topics', $topicCategoryId)
        );

        $videoFile = $topic->attachments ? $topic->attachments->first(function($att) {
            $mime = (string) $att->mime_type;
            $ext = strtolower(pathinfo((string) $att->file_path, PATHINFO_EXTENSION));
            return str_starts_with($mime, 'video/') || in_array($ext, ['mp4', 'webm', 'ogg', 'mov', 'm4v', 'mkv']);
        }) : null;

        $ytEmbedUrl = null;
        if (!$videoFile) {
            $linkUrl = $status->linkPreviewRecord?->url ?: $topic->txt;
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', (string) $linkUrl, $matches)) {
                $ytEmbedUrl = 'https://www.youtube.com/embed/' . $matches[1] . '?autoplay=0&rel=0&enablejsapi=1';
            }
        }
    @endphp

    @if($group)
        <div class="section-header" style="margin-bottom:14px;">
            @include('theme::partials.groups.badge', ['groupBadge' => $group])
        </div>
    @endif

    <div class="row g-4" id="videoWatchColumns">
        <!-- MAIN WATCH COLUMN (Video + Header + Details + Comments) -->
        <div class="col-lg-8 col-xl-8 transition-all" id="videoMainCol">
            <!-- 1. PROFESSIONAL VIDEO PLAYER STAGE (CINEMA THEATER) -->
            <div class="video-stage-card shadow-sm mb-3 position-relative overflow-hidden rounded-4" id="videoStageCard">
                <div class="v-ambient-backdrop" id="ambientBackdrop_{{ $status->id }}"></div>

                @if($videoFile)
                    <div class="myads-video-player-container position-relative rounded-4 overflow-hidden" id="playerContainer_{{ $status->id }}" style="z-index: 1;">
                        <video id="videoElement_{{ $status->id }}" class="myads-video-element" poster="{{ $topic->image_url ?? '' }}" preload="metadata" playsinline>
                            <source src="{{ asset($videoFile->file_path) }}" type="{{ $videoFile->mime_type ?? 'video/mp4' }}">
                            {{ __('messages.browser_no_video_support') }}
                        </video>

                        <!-- BIG OVERLAY PLAY BUTTON WITH PULSING GLOW -->
                        <div class="video-overlay-play" id="overlayPlay_{{ $status->id }}">
                            <div class="play-icon-circle shadow-lg">
                                <i class="fa fa-play ms-1"></i>
                            </div>
                        </div>

                        <!-- CUSTOM HTML5 CONTROLS BAR -->
                        <div class="video-controls-bar" id="controlsBar_{{ $status->id }}">
                            <!-- TIMELINE PROGRESS SCRUBBER -->
                            <div class="video-scrubber-container position-relative" id="scrubberContainer_{{ $status->id }}">
                                <div class="video-scrubber-buffer" id="scrubberBuffer_{{ $status->id }}"></div>
                                <input type="range" id="scrubberRange_{{ $status->id }}" class="video-scrubber-range" min="0" max="100" value="0" step="0.1" aria-label="Timeline progress">
                                <div class="v-scrub-tooltip d-none" id="scrubTooltip_{{ $status->id }}">0:00</div>
                            </div>

                            <div class="video-controls-row d-flex align-items-center justify-content-between">
                                <!-- LEFT CONTROLS (Play, Rewind, Forward, Volume, Time) -->
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="v-btn" id="btnPlay_{{ $status->id }}" title="{{ __('messages.play_pause') }}" aria-label="{{ __('messages.play_pause') }}">
                                        <i class="fa fa-play"></i>
                                    </button>

                                    <button type="button" class="v-btn v-btn-subtle" id="btnRewind_{{ $status->id }}" title="-10s" aria-label="Rewind 10 seconds">
                                        <i class="fa fa-undo"></i>
                                    </button>
                                    <button type="button" class="v-btn v-btn-subtle" id="btnForward_{{ $status->id }}" title="+10s" aria-label="Forward 10 seconds">
                                        <i class="fa fa-redo"></i>
                                    </button>

                                    <!-- VOLUME CONTROL -->
                                    <div class="v-volume-group d-flex align-items-center gap-1 ms-1">
                                        <button type="button" class="v-btn" id="btnMute_{{ $status->id }}" title="{{ __('messages.mute') }}" aria-label="{{ __('messages.mute') }}">
                                            <i class="fa fa-volume-up"></i>
                                        </button>
                                        <input type="range" id="volumeRange_{{ $status->id }}" class="v-volume-range" min="0" max="1" step="0.05" value="1" aria-label="Volume">
                                    </div>

                                    <!-- TIME DISPLAY -->
                                    <span class="v-time-text ms-2 font-monospace" id="timeDisplay_{{ $status->id }}">0:00 / 0:00</span>
                                </div>

                                <!-- RIGHT CONTROLS (Speed, Theater, PiP, Fullscreen) -->
                                <div class="d-flex align-items-center gap-2">
                                    <!-- PLAYBACK SPEED -->
                                    <div class="v-flyout-wrap" style="position:relative;">
                                        <button type="button" class="v-btn v-btn-badge" id="btnSpeed_{{ $status->id }}" onclick="toggleCustomFlyout(this, 'speedFlyout_{{ $status->id }}')" title="{{ __('messages.playback_speed') }}">
                                            1x
                                        </button>
                                        <div class="custom-v-flyout" id="speedFlyout_{{ $status->id }}" style="display:none; position:absolute; bottom:38px; inset-inline-end:0; background: #1d2333; border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; padding: 4px; min-width: 120px; z-index:99;">
                                            <div class="v-speed-item" data-speed="0.5" onclick="setVideoSpeed(0.5, '{{ $status->id }}')">0.5x</div>
                                            <div class="v-speed-item" data-speed="0.75" onclick="setVideoSpeed(0.75, '{{ $status->id }}')">0.75x</div>
                                            <div class="v-speed-item active" data-speed="1.0" onclick="setVideoSpeed(1.0, '{{ $status->id }}')">1.0x ({{ __('messages.speed_normal') }})</div>
                                            <div class="v-speed-item" data-speed="1.25" onclick="setVideoSpeed(1.25, '{{ $status->id }}')">1.25x</div>
                                            <div class="v-speed-item" data-speed="1.5" onclick="setVideoSpeed(1.5, '{{ $status->id }}')">1.5x</div>
                                            <div class="v-speed-item" data-speed="2.0" onclick="setVideoSpeed(2.0, '{{ $status->id }}')">2.0x</div>
                                        </div>
                                    </div>

                                    <!-- THEATER MODE TOGGLE -->
                                    <button type="button" class="v-btn v-btn-subtle" id="btnTheater_{{ $status->id }}" title="{{ __('messages.theater_mode') }}" aria-label="{{ __('messages.theater_mode') }}" onclick="toggleTheaterMode()">
                                        <i class="fa-solid fa-rectangle-wide fa-desktop"></i>
                                    </button>

                                    <!-- PICTURE-IN-PICTURE -->
                                    <button type="button" class="v-btn v-btn-subtle" id="btnPip_{{ $status->id }}" title="{{ __('messages.pip_mode') }}" aria-label="{{ __('messages.pip_mode') }}" onclick="togglePictureInPicture('{{ $status->id }}')">
                                        <i class="fa-solid fa-clone"></i>
                                    </button>

                                    <!-- FULLSCREEN -->
                                    <button type="button" class="v-btn" id="btnFullscreen_{{ $status->id }}" title="{{ __('messages.fullscreen') }}" aria-label="{{ __('messages.fullscreen') }}">
                                        <i class="fa fa-expand"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($ytEmbedUrl)
                    <div class="ratio ratio-16x9 video-iframe-box rounded-4 overflow-hidden position-relative" style="z-index: 1;">
                        <iframe src="{{ $ytEmbedUrl }}" title="{{ $topic->name }}" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                    </div>
                @else
                    <div class="video-placeholder-box d-flex flex-column align-items-center justify-content-center text-center p-5 rounded-4 position-relative" style="z-index: 1;">
                        <i class="fa fa-film fa-3x text-primary mb-3"></i>
                        <p class="text-muted fw-bold mb-0">{{ __('messages.no_video_stream') }}</p>
                    </div>
                @endif
            </div>

            <!-- 2. PUBLISHER HEADER & ACTION BUTTONS BAR -->
            <div class="video-author-actions-card shadow-sm mb-3 p-3 rounded-4 bg-surface border">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <!-- PUBLISHER IDENTITY -->
                    <div class="d-flex align-items-center gap-3">
                        <!-- HEXAGON AVATAR -->
                        <a class="user-status-avatar flex-shrink-0 text-decoration-none" href="{{ route('profile.show', $topic->user->username) }}" title="{{ $topic->user->username }}">
                            <div class="user-avatar small no-outline {{ $topic->user->isOnline() ? 'online' : 'offline' }}">
                                <div class="user-avatar-content">
                                    <div class="hexagon-image-30-32" data-src="{{ $topic->user ? $topic->user->avatarUrl() : asset('upload/_avatar.png') }}" style="width: 30px; height: 32px; position: relative;">
                                        <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                                    </div>
                                </div>
                                <div class="user-avatar-progress-border">
                                    <div class="hexagon-border-40-44" data-line-color="{{ $topic->user->profileBadgeColor() }}" style="width: 40px; height: 44px; position: relative;"></div>
                                </div>
                                @if($topic->user->hasVerifiedBadge())
                                    <div class="user-avatar-badge">
                                        <div class="user-avatar-badge-border">
                                            <div class="hexagon-22-24" style="width: 22px; height: 24px; position: relative;"></div>
                                        </div>
                                        <div class="user-avatar-badge-content">
                                            <div class="hexagon-dark-16-18" style="width: 16px; height: 18px; position: relative;"></div>
                                        </div>
                                        <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                                    </div>
                                @endif
                            </div>
                        </a>

                        <!-- USER NAME & ROLE -->
                        <div>
                            <p class="user-status-title medium mb-0">
                                <a class="bold text-decoration-none author-name-link fw-bold" href="{{ route('profile.show', $topic->user->username) }}">
                                    {{ $topic->user->username }}
                                </a>
                                @if($topic->user->hasVerifiedBadge())
                                    <i class="fa-solid fa-circle-check text-primary fs-7 ms-1" title="{{ __('messages.verified') }}"></i>
                                @endif
                            </p>
                            @if($showForumRoleBadges)
                                <p class="user-status-text small text-muted mb-0" style="font-size: 12px; margin-top: 1px;">
                                    {{ $topic->user->forumRoleLabel($topicCategoryId) }}
                                </p>
                            @endif
                        </div>

                        <!-- FOLLOW / UNFOLLOW BUTTON -->
                        @auth
                            @if(auth()->id() !== $topic->user->id)
                                <form action="{{ route('profile.follow', $topic->user->username) }}" method="POST" class="ms-2 d-inline">
                                    @csrf
                                    <button type="submit" class="button {{ $isFollowing ? 'primary' : 'secondary' }} small rounded-pill" style="height: 32px; padding: 0 14px; font-size: 12px; font-weight: 700;">
                                        <i class="fa {{ $isFollowing ? 'fa-check' : 'fa-user-plus' }} me-1"></i>
                                        <span>{{ $isFollowing ? __('messages.following') : '+ ' . __('messages.follow') }}</span>
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>

                    <!-- UNIFORM ACTION BUTTONS (REACTIONS, SAVE, SHARE, OPTIONS) -->
                    <div class="v-action-buttons-wrap d-flex align-items-center gap-2 flex-wrap">
                        <!-- REACTIONS BUTTON -->
                        @auth
                            <div class="v-flyout-wrap">
                                <button type="button" class="v-action-btn reaction-options-dropdown-trigger" onclick="toggleReactionDropdown(this)">
                                    <div id="reaction_image{{ $status->id }}" class="d-inline-flex align-items-center">
                                        @php
                                            $myReaction = \App\Models\Like::where('uid', Auth::id())->where('sid', $topic->id)->where('type', 2)->first();
                                            $reactionType = 'like';
                                            if($myReaction) {
                                                $reactionOption = \App\Models\Option::where('o_parent', $myReaction->id)->where('o_type', 'data_reaction')->first();
                                                if($reactionOption) $reactionType = $reactionOption->o_valuer;
                                            }
                                        @endphp
                                        @if($myReaction)
                                            <img class="reaction-option-image me-1" src="{{ theme_asset('img/reaction/'.$reactionType.'.png') }}" width="18" alt="reaction">
                                            <span class="reaction_txt{{ $status->id }} text-info fw-bold">{{ ucfirst($reactionType) }}</span>
                                        @else
                                            <i class="fa fa-thumbs-up me-1 text-primary"></i>
                                            <span class="reaction_txt{{ $status->id }}">{{ __('messages.like') ?? 'Like' }}</span>
                                        @endif
                                    </div>
                                </button>

                                <div class="reaction-options reaction-options-dropdown" style="position: absolute; z-index: 99999; bottom: 44px; inset-inline-end: 0; display: none;">
                                    @foreach(['like', 'love', 'dislike', 'happy', 'funny', 'wow', 'angry', 'sad'] as $reaction)
                                        <div class="reaction-option text-tooltip-tft reaction_100_{{ $topic->id }}" data-title="{{ $reaction }}" onclick="postReaction({{ $topic->id }}, '{{ $reaction }}')">
                                            <img class="reaction-option-image" src="{{ theme_asset('img/reaction/'.$reaction.'.png') }}" alt="reaction-{{ $reaction }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endauth

                        <!-- SAVE VIDEO BUTTON -->
                        @auth
                            <div class="v-flyout-wrap">
                                <button type="button" class="v-action-btn" onclick="toggleSaveVideo({{ $status->id }}, this)">
                                    <i class="{{ $isSaved ? 'fa-solid fa-bookmark text-primary' : 'fa-regular fa-bookmark fa-bookmark-o' }} me-1"></i>
                                    <span class="btn-save-text">{{ $isSaved ? __('messages.saved_video') : __('messages.save_video') }}</span>
                                </button>
                            </div>
                        @endauth

                        <!-- SHARE BUTTON (CUSTOM POPUP FLYOUT) -->
                        <div class="v-flyout-wrap">
                            <button type="button" class="v-action-btn" onclick="toggleCustomFlyout(this, 'flyout_share_{{ $status->id }}')">
                                <i class="fa fa-share-alt me-1"></i>
                                <span>{{ __('messages.share_video') }}</span>
                            </button>
                            <div class="custom-v-flyout" id="flyout_share_{{ $status->id }}" style="display:none;">
                                <div class="v-flyout-item" onclick="navigator.clipboard.writeText('{{ route('forum.topic', $topic->id) }}'); showToast('{{ __('messages.link_copied') }}'); toggleCustomFlyout(null, 'flyout_share_{{ $status->id }}');">
                                    <i class="fa fa-link text-primary me-2"></i> {{ __('messages.copy_video_link') }}
                                </div>
                                <div style="height:1px; background:var(--border-color, #eee); margin:6px 0;"></div>
                                @foreach(['facebook' => 'Facebook', 'twitter' => 'Twitter (X)', 'linkedin' => 'LinkedIn', 'telegram' => 'Telegram'] as $socialKey => $socialName)
                                    <div class="v-flyout-item" onclick="sharePost('{{ $socialKey }}', '{{ route('forum.topic', $topic->id) }}', '{{ e($topic->name) }}'); toggleCustomFlyout(null, 'flyout_share_{{ $status->id }}');">
                                        <img src="{{ theme_asset('img/icons/'.$socialKey.'-icon.png') }}" width="16" class="me-2" alt="{{ $socialName }}"> {{ $socialName }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- THEATER MODE SHORTCUT BUTTON -->
                        <button type="button" class="v-action-btn icon-only d-none d-md-inline-flex" onclick="toggleTheaterMode()" title="{{ __('messages.theater_mode') }}">
                            <i class="fa-solid fa-desktop text-primary"></i>
                        </button>

                        <!-- OPTIONS / REPORT MENU -->
                        <div class="v-flyout-wrap">
                            <button type="button" class="v-action-btn icon-only" onclick="toggleCustomFlyout(this, 'flyout_more_{{ $status->id }}')" title="{{ __('messages.options') }}">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <div class="custom-v-flyout" id="flyout_more_{{ $status->id }}" style="display:none;">
                                @if($canEditTopic)
                                    <a href="{{ route('forum.edit', $topic->id) }}" class="v-flyout-item text-decoration-none">
                                        <i class="fa fa-edit text-warning me-2"></i> {{ __('messages.edit_topic') }}
                                    </a>
                                @endif
                                @if($canDeleteTopic)
                                    <div class="v-flyout-item text-danger" onclick="deletePost({{ $topic->id }}, 100)">
                                        <i class="fa fa-trash me-2"></i> {{ __('messages.delete_topic') }}
                                    </div>
                                @endif
                                <div class="v-flyout-item" onclick="reportPost({{ $topic->id }}, 2); toggleCustomFlyout(null, 'flyout_more_{{ $status->id }}');">
                                    <i class="fa fa-flag text-danger me-2"></i> {{ __('messages.report') ?? 'Report' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. TITLE & DESCRIPTION CARD -->
            <div class="video-details-card shadow-sm mb-4 rounded-4 bg-surface border p-4">
                <h1 class="video-watch-title display-6 fs-4 fw-extrabold mb-2">{{ $topic->name }}</h1>

                <!-- METADATA BAR -->
                <div class="d-flex align-items-center flex-wrap gap-3 text-muted small my-2 pb-3 border-bottom">
                    <span class="d-inline-flex align-items-center gap-1">
                        <i class="fa fa-eye text-primary"></i>
                        {{ __('messages.video_views_count', ['count' => number_format($topic->vu ?? 0)]) }}
                    </span>
                    <span class="d-inline-flex align-items-center gap-1">
                        <i class="fa-regular fa-clock text-muted"></i>
                        {{ \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans() }}
                    </span>
                    @if($topic->category)
                        <span class="badge rounded-pill px-3 py-1 shadow-xs" style="background-color: {{ $topic->category->color ?: '#615dfa' }}; color: #fff;">
                            <i class="fa fa-folder-open me-1"></i> {{ $topic->category->name }}
                        </span>
                    @endif
                </div>

                <!-- FORMATTED DESCRIPTION CONTENT -->
                <div class="video-description-wrapper mt-3" id="videoDescWrapper">
                    <div class="video-description-text textpost" id="videoDescText">
                        {!! \App\Support\ContentFormatter::formatForum($topic->txt) !!}
                    </div>
                    <button type="button" class="btn btn-link btn-sm p-0 mt-2 text-primary fw-bold d-none text-decoration-none" id="btnToggleDesc" onclick="toggleDescExpand()">
                        {{ __('messages.show_more') }}
                    </button>
                </div>
            </div>

            <!-- 4. COMMENTS SECTION BELOW DESCRIPTION -->
            <div class="video-comments-card shadow-sm p-4 rounded-4 bg-surface border mb-4">
                <h3 class="h5 fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="fa fa-comments text-primary"></i>
                    <span>{{ __('messages.comments') ?? 'Comments' }} ({{ $topic->comments()->count() }})</span>
                </h3>

                <div class="post-comment-list post-comment-list-{{ $topic->id }} comment_100_{{ $topic->id }}">
                    @include('theme::partials.activity.comments', [
                        'comments' => $topic->comments()->orderBy('id', 'desc')->get(),
                        'id' => $topic->id,
                        'type' => 'forum',
                        'limit' => 100,
                        'hide_form' => $topic->is_locked && !$canCommentWhenLocked,
                        'locked_topic' => (bool) $topic->is_locked,
                        'forum_category_id' => $topicCategoryId
                    ])
                </div>
            </div>
        </div>

        <!-- SIDEBAR COLUMN (Suggested Videos) -->
        <div class="col-lg-4 col-xl-4" id="videoSidebarCol">
            <div class="suggested-videos-card shadow-sm sticky-top rounded-4 bg-surface border p-3" style="top: 85px; z-index: 10;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="suggested-card-heading h6 fw-bold mb-0 d-flex align-items-center">
                        <i class="fa fa-play-circle text-primary me-2"></i>
                        <span>{{ __('messages.suggested_videos') }}</span>
                    </h3>
                    <div class="form-check form-switch mb-0 fs-7">
                        <input class="form-check-input" type="checkbox" role="switch" id="autoplaySwitch" checked>
                        <label class="form-check-label text-muted small" for="autoplaySwitch">{{ __('messages.autoplay') }}</label>
                    </div>
                </div>

                <div class="suggested-videos-list">
                    @forelse($suggestedVideos as $sVideo)
                        @php
                            $sTopic = $sVideo->forumTopic;
                            $sThumb = $sVideo->resolved_thumbnail ?: \App\Http\Controllers\VideoHubController::resolveVideoThumbnailUrl($sVideo);
                            $sTitle = $sVideo->resolved_title ?: \App\Http\Controllers\VideoHubController::resolveVideoTitle($sVideo);
                            $sViews = $sTopic ? (int) ($sTopic->vu ?? 0) : (int) ($sVideo->views_count ?? 0);
                        @endphp
                        @if($sTopic && (int) $sVideo->s_type !== 4 && (int) $sVideo->s_type !== 14)
                            <a href="{{ route('forum.topic', $sTopic->id) }}" class="suggested-video-item text-decoration-none d-flex gap-2 mb-3 pb-2 border-bottom">
                                <!-- THUMBNAIL BOX (16:9) -->
                                <div class="suggested-thumb-box flex-shrink-0 ratio ratio-16x9 rounded-3 overflow-hidden bg-dark position-relative" style="width: 120px;">
                                    <img src="{{ $sThumb }}" alt="{{ $sTitle }}" class="suggested-thumb-img w-100 h-100 object-fit-cover lazyload" onError="this.onerror=null;this.src='{{ theme_asset('img/video-placeholder.svg') }}';">
                                    <span class="video-play-badge badge bg-dark bg-opacity-75 text-white position-absolute bottom-0 end-0 m-1 fs-8">
                                        <i class="fa fa-play"></i>
                                    </span>
                                </div>

                                <!-- DETAILS -->
                                <div class="suggested-video-info flex-grow-1 min-w-0">
                                    <h4 class="suggested-video-title text-truncate-2 mb-1 fs-7 fw-bold text-reset" style="line-height: 1.35;">
                                        {{ $sTitle }}
                                    </h4>
                                    <div class="suggested-video-author text-muted small text-truncate fs-8">
                                        {{ $sTopic->user?->username }}
                                    </div>
                                    <div class="suggested-video-meta text-muted extra-small mt-1 fs-8">
                                        <span>{{ __('messages.video_views_count', ['count' => number_format($sViews)]) }}</span>
                                        <span class="mx-1">&bull;</span>
                                        <span>{{ \Carbon\Carbon::createFromTimestamp($sVideo->date)->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @empty
                        <div class="p-3 text-center text-muted small">
                            {{ __('messages.no_suggested_videos') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCOPED CUSTOM STYLES FOR YOUTUBE WATCH PAGE & PLAYER -->
<style>
/* GENERAL WATCH PAGE LAYOUT */
.video-watch-page {
    margin-top: 10px;
}
.video-stage-card {
    background: #000;
    border: 1px solid rgba(97, 93, 250, 0.3);
    position: relative;
}
.myads-video-player-container {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    background: #000;
    overflow: hidden;
}
.myads-video-element {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
}

/* BIG OVERLAY PLAY BUTTON */
.video-overlay-play {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.35);
    cursor: pointer;
    z-index: 2;
    transition: opacity 0.3s ease;
}
.video-overlay-play.playing {
    opacity: 0;
    pointer-events: none;
}
.play-icon-circle {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: linear-gradient(135deg, #615dfa 0%, #4b45e4 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    box-shadow: 0 0 30px rgba(97, 93, 250, 0.75);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.video-overlay-play:hover .play-icon-circle {
    transform: scale(1.12);
    box-shadow: 0 0 40px rgba(97, 93, 250, 0.95);
}

/* CUSTOM HTML5 CONTROLS BAR */
.video-controls-bar {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.92), rgba(0,0,0,0.4) 70%, transparent);
    padding: 8px 14px;
    z-index: 3;
    opacity: 1;
    transition: opacity 0.3s ease;
}
.myads-video-player-container.is-idle .video-controls-bar {
    opacity: 0;
}
.myads-video-player-container:hover .video-controls-bar {
    opacity: 1;
}

/* TIMELINE SCRUBBER */
.video-scrubber-container {
    position: relative;
    width: 100%;
    height: 6px;
    margin-bottom: 8px;
    border-radius: 3px;
    background: rgba(255, 255, 255, 0.25);
    cursor: pointer;
}
.video-scrubber-range {
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0; left: 0;
    opacity: 0;
    z-index: 2;
    cursor: pointer;
}
.video-scrubber-buffer {
    position: absolute;
    top: 0; left: 0; height: 100%;
    background: linear-gradient(90deg, #615dfa, #23d2e2);
    border-radius: 3px;
    width: 0%;
    pointer-events: none;
    box-shadow: 0 0 8px rgba(35, 210, 226, 0.6);
}

/* CONTROL BUTTONS */
.v-btn {
    background: transparent;
    border: none;
    color: #fff;
    font-size: 15px;
    width: 32px; height: 32px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
}
.v-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: #615dfa;
    transform: scale(1.08);
}
.v-btn-subtle {
    font-size: 12px;
}
.v-btn-badge {
    width: auto;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.15);
}
.v-time-text {
    color: #e0e0e0;
    font-size: 12px;
    font-weight: 500;
}
.v-volume-range {
    width: 55px;
    accent-color: #615dfa;
    cursor: pointer;
}

/* ACTION BUTTONS */
.v-action-btn {
    background: var(--button-background-color, #f0f2f5) !important;
    border: 1px solid var(--border-color, #e0e6ed) !important;
    color: var(--text-color, #4a4d68) !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    height: 36px !important;
    padding: 0 16px !important;
    border-radius: 18px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    white-space: nowrap !important;
}
.v-action-btn:hover {
    background: #615dfa !important;
    color: #fff !important;
    border-color: #615dfa !important;
    transform: translateY(-1px);
}
.v-action-btn:hover .text-primary {
    color: #fff !important;
}
.v-action-btn.icon-only {
    width: 36px !important;
    height: 36px !important;
    padding: 0 !important;
    border-radius: 50% !important;
}

/* CUSTOM POPUP FLYOUT */
.custom-v-flyout {
    position: absolute !important;
    top: 42px !important;
    inset-inline-start: 0 !important;
    min-width: 180px !important;
    background: var(--section-background-color, #ffffff) !important;
    border: 1px solid var(--border-color, #e0e6ed) !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.18) !important;
    padding: 8px !important;
    z-index: 99999 !important;
}
[data-bs-theme="dark"] .custom-v-flyout,
.dark .custom-v-flyout,
html.app-skin-dark .custom-v-flyout {
    background: #1d2333 !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
    color: #ffffff !important;
}
.v-flyout-item {
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-color, #333);
    cursor: pointer;
    display: flex;
    align-items: center;
    transition: background 0.15s ease, color 0.15s ease;
}
.v-flyout-item:hover {
    background: rgba(97, 93, 250, 0.12);
    color: #615dfa;
}
.v-speed-item {
    padding: 6px 10px;
    font-size: 12px;
    color: #fff;
    border-radius: 6px;
    cursor: pointer;
}
.v-speed-item:hover, .v-speed-item.active {
    background: #615dfa;
}
.video-description-text.collapsed {
    max-height: 120px;
    overflow: hidden;
    position: relative;
}
.video-description-text.collapsed::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 40px;
    background: linear-gradient(to bottom, transparent, var(--section-background-color, #ffffff));
}
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.suggested-video-item {
    transition: transform 0.2s ease;
}
.suggested-video-item:hover {
    transform: translateX(-3px);
}
html[dir="rtl"] .suggested-video-item:hover {
    transform: translateX(3px);
}
</style>

<!-- SCOPED JAVASCRIPT FOR PLAYER & THEATER MODE -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusId = "{{ $status->id }}";
    const video = document.getElementById('videoElement_' + statusId);
    const overlayPlay = document.getElementById('overlayPlay_' + statusId);
    const btnPlay = document.getElementById('btnPlay_' + statusId);
    const btnRewind = document.getElementById('btnRewind_' + statusId);
    const btnForward = document.getElementById('btnForward_' + statusId);
    const btnMute = document.getElementById('btnMute_' + statusId);
    const volumeRange = document.getElementById('volumeRange_' + statusId);
    const timeDisplay = document.getElementById('timeDisplay_' + statusId);
    const scrubberRange = document.getElementById('scrubberRange_' + statusId);
    const scrubberBuffer = document.getElementById('scrubberBuffer_' + statusId);
    const btnFullscreen = document.getElementById('btnFullscreen_' + statusId);
    const playerContainer = document.getElementById('playerContainer_' + statusId);

    if (video) {
        function formatTime(seconds) {
            if (isNaN(seconds)) return '0:00';
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return mins + ':' + (secs < 10 ? '0' : '') + secs;
        }

        function togglePlay() {
            if (video.paused || video.ended) {
                video.play();
            } else {
                video.pause();
            }
        }

        if (overlayPlay) overlayPlay.addEventListener('click', togglePlay);
        if (btnPlay) btnPlay.addEventListener('click', togglePlay);
        video.addEventListener('click', togglePlay);

        // Auto-hide controls bar on idle
        let idleTimer = null;
        if (playerContainer) {
            playerContainer.addEventListener('mousemove', function() {
                playerContainer.classList.remove('is-idle');
                clearTimeout(idleTimer);
                if (!video.paused) {
                    idleTimer = setTimeout(() => {
                        playerContainer.classList.add('is-idle');
                    }, 2500);
                }
            });
        }

        video.addEventListener('play', function () {
            if (overlayPlay) overlayPlay.classList.add('playing');
            if (btnPlay) btnPlay.innerHTML = '<i class="fa fa-pause"></i>';
        });

        video.addEventListener('pause', function () {
            if (overlayPlay) overlayPlay.classList.remove('playing');
            if (btnPlay) btnPlay.innerHTML = '<i class="fa fa-play"></i>';
            if (playerContainer) playerContainer.classList.remove('is-idle');
        });

        video.addEventListener('timeupdate', function () {
            if (!video.duration) return;
            const pct = (video.currentTime / video.duration) * 100;
            if (scrubberRange) scrubberRange.value = pct;
            if (scrubberBuffer) scrubberBuffer.style.width = pct + '%';
            if (timeDisplay) timeDisplay.textContent = formatTime(video.currentTime) + ' / ' + formatTime(video.duration);
        });

        if (scrubberRange) {
            scrubberRange.addEventListener('input', function () {
                if (!video.duration) return;
                video.currentTime = (scrubberRange.value / 100) * video.duration;
            });
        }

        if (btnRewind) {
            btnRewind.addEventListener('click', function () {
                video.currentTime = Math.max(0, video.currentTime - 10);
            });
        }
        if (btnForward) {
            btnForward.addEventListener('click', function () {
                video.currentTime = Math.min(video.duration || 0, video.currentTime + 10);
            });
        }

        if (btnMute) {
            btnMute.addEventListener('click', function () {
                video.muted = !video.muted;
                btnMute.innerHTML = video.muted ? '<i class="fa fa-volume-mute"></i>' : '<i class="fa fa-volume-up"></i>';
                if (volumeRange) volumeRange.value = video.muted ? 0 : video.volume;
            });
        }

        if (volumeRange) {
            volumeRange.addEventListener('input', function () {
                video.volume = volumeRange.value;
                video.muted = (volumeRange.value == 0);
                if (btnMute) btnMute.innerHTML = video.muted ? '<i class="fa fa-volume-mute"></i>' : '<i class="fa fa-volume-up"></i>';
            });
        }

        if (btnFullscreen && playerContainer) {
            btnFullscreen.addEventListener('click', function () {
                if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                    if (playerContainer.requestFullscreen) {
                        playerContainer.requestFullscreen();
                    } else if (playerContainer.webkitRequestFullscreen) {
                        playerContainer.webkitRequestFullscreen();
                    }
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    }
                }
            });
        }

        // Global Keyboard Shortcuts
        document.addEventListener('keydown', function (e) {
            if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) return;
            if (e.code === 'Space' || e.code === 'KeyK') {
                e.preventDefault();
                togglePlay();
            } else if (e.code === 'KeyF') {
                e.preventDefault();
                if (btnFullscreen) btnFullscreen.click();
            } else if (e.code === 'KeyM') {
                e.preventDefault();
                if (btnMute) btnMute.click();
            } else if (e.code === 'KeyT') {
                e.preventDefault();
                toggleTheaterMode();
            } else if (e.code === 'ArrowLeft' || e.code === 'KeyJ') {
                e.preventDefault();
                video.currentTime = Math.max(0, video.currentTime - 5);
            } else if (e.code === 'ArrowRight' || e.code === 'KeyL') {
                e.preventDefault();
                video.currentTime = Math.min(video.duration || 0, video.currentTime + 5);
            } else if (e.code === 'ArrowUp') {
                e.preventDefault();
                video.volume = Math.min(1, video.volume + 0.05);
                if (volumeRange) volumeRange.value = video.volume;
            } else if (e.code === 'ArrowDown') {
                e.preventDefault();
                video.volume = Math.max(0, video.volume - 0.05);
                if (volumeRange) volumeRange.value = video.volume;
            }
        });
    }

    // Auto-collapse long description
    const descText = document.getElementById('videoDescText');
    const btnToggleDesc = document.getElementById('btnToggleDesc');
    if (descText && descText.scrollHeight > 150) {
        descText.classList.add('collapsed');
        if (btnToggleDesc) btnToggleDesc.classList.remove('d-none');
    }

    // Restore Theater Mode Preference
    if (localStorage.getItem('myads_video_theater_mode') === 'true') {
        const watchPage = document.getElementById('videoWatchPage');
        if (watchPage) watchPage.classList.add('theater-mode');
    }
});

function toggleTheaterMode() {
    const watchPage = document.getElementById('videoWatchPage');
    if (!watchPage) return;
    const isTheater = watchPage.classList.toggle('theater-mode');
    localStorage.setItem('myads_video_theater_mode', isTheater ? 'true' : 'false');
}

function togglePictureInPicture(statusId) {
    const video = document.getElementById('videoElement_' + statusId);
    if (!video) return;
    if (document.pictureInPictureElement) {
        document.exitPictureInPicture().catch(() => {});
    } else if (video.requestPictureInPicture) {
        video.requestPictureInPicture().catch(() => {});
    }
}

function setVideoSpeed(spd, statusId) {
    const video = document.getElementById('videoElement_' + statusId);
    if (video) video.playbackRate = spd;
    const btnSpeed = document.getElementById('btnSpeed_' + statusId);
    if (btnSpeed) btnSpeed.textContent = spd + 'x';
    toggleCustomFlyout(null, 'speedFlyout_' + statusId);
}

function toggleDescExpand() {
    const descText = document.getElementById('videoDescText');
    const btnToggleDesc = document.getElementById('btnToggleDesc');
    if (descText.classList.contains('collapsed')) {
        descText.classList.remove('collapsed');
        if (btnToggleDesc) btnToggleDesc.textContent = "{{ __('messages.show_less') }}";
    } else {
        descText.classList.add('collapsed');
        if (btnToggleDesc) btnToggleDesc.textContent = "{{ __('messages.show_more') }}";
    }
}

function toggleCustomFlyout(triggerBtn, targetId) {
    document.querySelectorAll('.custom-v-flyout').forEach(el => {
        if (el.id !== targetId) el.style.display = 'none';
    });
    const flyout = document.getElementById(targetId);
    if (!flyout) return;
    flyout.style.display = (flyout.style.display === 'none' || !flyout.style.display) ? 'block' : 'none';
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.v-flyout-wrap')) {
        document.querySelectorAll('.custom-v-flyout').forEach(el => {
            el.style.display = 'none';
        });
    }
});

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
                if (icon) icon.className = 'fa-solid fa-bookmark text-primary me-1';
                if (label) label.textContent = "{{ __('messages.saved_video') }}";
                showToast("{{ __('messages.video_saved_toast') }}");
            } else {
                if (icon) icon.className = 'fa-regular fa-bookmark fa-bookmark-o me-1';
                if (label) label.textContent = "{{ __('messages.save_video') }}";
                showToast("{{ __('messages.video_unsaved_toast') }}");
            }
        }
    })
    .catch(err => console.error(err));
}

function showToast(msg) {
    let notifBox = document.getElementById('globalToastBox');
    if (!notifBox) {
        notifBox = document.createElement('div');
        notifBox.id = 'globalToastBox';
        notifBox.style.cssText = 'position:fixed; bottom:24px; inset-inline-start:24px; z-index:99999;';
        document.body.appendChild(notifBox);
    }
    const toastEl = document.createElement('div');
    toastEl.className = 'alert alert-success shadow-lg border-0 rounded-4 px-4 py-3 mb-2 text-white d-flex align-items-center gap-2';
    toastEl.style.background = 'linear-gradient(135deg, #615dfa, #4b45e4)';
    toastEl.innerHTML = '<i class="fa fa-check-circle fs-5"></i><span>' + msg + '</span>';
    notifBox.appendChild(toastEl);
    setTimeout(() => toastEl.remove(), 3500);
}

/* MINI FLOATING PICTURE-IN-PICTURE (PIP) OBSERVER */
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const stageCard = document.getElementById('videoStageCard');
        const container = document.querySelector('.myads-video-player-container');
        const video = container ? container.querySelector('video') : null;
        
        if (!stageCard || !container || !video) return;

        let pipOverlay = container.querySelector('.v-pip-controls-overlay');
        if (!pipOverlay) {
            pipOverlay = document.createElement('div');
            pipOverlay.className = 'v-pip-controls-overlay';
            pipOverlay.innerHTML = `
                <button type="button" class="v-pip-btn v-pip-return-btn" title="{{ __('messages.fullscreen') }}">
                    <i class="fa fa-expand"></i>
                </button>
                <button type="button" class="v-pip-btn v-pip-close-btn" title="{{ __('messages.close') ?? 'Close' }}">
                    <i class="fa fa-times"></i>
                </button>
            `;
            container.appendChild(pipOverlay);

            pipOverlay.querySelector('.v-pip-return-btn').addEventListener('click', function(e) {
                e.stopPropagation();
                container.classList.remove('v-mini-floating-player');
                stageCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });

            pipOverlay.querySelector('.v-pip-close-btn').addEventListener('click', function(e) {
                e.stopPropagation();
                container.classList.remove('v-mini-floating-player');
                container.dataset.pipDismissed = 'true';
            });
        }

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                const isVideoPlaying = video && !video.paused && !video.ended && video.readyState >= 2;
                const isDismissed = container.dataset.pipDismissed === 'true';

                if (!entry.isIntersecting && isVideoPlaying && !isDismissed) {
                    container.classList.add('v-mini-floating-player');
                } else if (entry.isIntersecting) {
                    container.classList.remove('v-mini-floating-player');
                    container.dataset.pipDismissed = 'false';
                }
            });
        }, { threshold: 0.2 });

        observer.observe(stageCard);
    });
})();
</script>

@include('theme::forum.scripts')
@endsection
