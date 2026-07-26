@extends('theme::layouts.master')
@include('theme::forum._assets')

@section('content')
<div class="forum-rdx video-watch-page">
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
                $ytEmbedUrl = 'https://www.youtube.com/embed/' . $matches[1] . '?autoplay=0&rel=0';
            }
        }
    @endphp

    @if($group)
        <div class="section-header" style="margin-bottom:14px;">
            @include('theme::partials.groups.badge', ['groupBadge' => $group])
        </div>
    @endif

    <div class="row g-4">
        <!-- MAIN WATCH COLUMN (Video + Header + Details + Comments) -->
        <div class="col-lg-8 col-xl-8">
            <!-- 1. PROFESSIONAL VIDEO PLAYER STAGE -->
            <div class="video-stage-card shadow-sm mb-3" id="videoStageCard">
                @if($videoFile)
                    <div class="myads-video-player-container" id="playerContainer_{{ $status->id }}">
                        <video id="videoElement_{{ $status->id }}" class="myads-video-element" poster="{{ $topic->image_url ?? '' }}" preload="metadata" playsinline>
                            <source src="{{ asset($videoFile->file_path) }}" type="{{ $videoFile->mime_type ?? 'video/mp4' }}">
                            {{ __('messages.browser_no_video_support') ?? 'متصفحك لا يدعم تشغيل الفيديو.' }}
                        </video>

                        <!-- BIG OVERLAY PLAY BUTTON -->
                        <div class="video-overlay-play" id="overlayPlay_{{ $status->id }}">
                            <div class="play-icon-circle">
                                <i class="fa fa-play"></i>
                            </div>
                        </div>

                        <!-- CUSTOM HTML5 CONTROLS BAR -->
                        <div class="video-controls-bar" id="controlsBar_{{ $status->id }}">
                            <!-- TIMELINE PROGRESS SCRUBBER -->
                            <div class="video-scrubber-container">
                                <div class="video-scrubber-buffer" id="scrubberBuffer_{{ $status->id }}"></div>
                                <input type="range" id="scrubberRange_{{ $status->id }}" class="video-scrubber-range" min="0" max="100" value="0" step="0.1">
                            </div>

                            <div class="video-controls-row d-flex align-items-center justify-content-between">
                                <!-- LEFT CONTROLS (Play, Skip, Time) -->
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="v-btn" id="btnPlay_{{ $status->id }}" title="{{ __('messages.play_pause') ?? 'تشغيل / إيقاف' }}">
                                        <i class="fa fa-play"></i>
                                    </button>

                                    <button type="button" class="v-btn v-btn-subtle" id="btnRewind_{{ $status->id }}" title="-10s">
                                        <i class="fa fa-undo"></i>
                                    </button>
                                    <button type="button" class="v-btn v-btn-subtle" id="btnForward_{{ $status->id }}" title="+10s">
                                        <i class="fa fa-redo"></i>
                                    </button>

                                    <!-- VOLUME CONTROL -->
                                    <div class="v-volume-group d-flex align-items-center gap-1">
                                        <button type="button" class="v-btn" id="btnMute_{{ $status->id }}" title="{{ __('messages.mute') ?? 'كتم الصوت' }}">
                                            <i class="fa fa-volume-up"></i>
                                        </button>
                                        <input type="range" id="volumeRange_{{ $status->id }}" class="v-volume-range" min="0" max="1" step="0.05" value="1">
                                    </div>

                                    <!-- TIME DISPLAY -->
                                    <span class="v-time-text ms-2" id="timeDisplay_{{ $status->id }}">0:00 / 0:00</span>
                                </div>

                                <!-- RIGHT CONTROLS (Speed, Fullscreen) -->
                                <div class="d-flex align-items-center gap-2">
                                    <!-- PLAYBACK SPEED -->
                                    <div class="v-flyout-wrap" style="position:relative;">
                                        <button type="button" class="v-btn v-btn-badge" id="btnSpeed_{{ $status->id }}" onclick="toggleCustomFlyout(this, 'speedFlyout_{{ $status->id }}')">
                                            1x
                                        </button>
                                        <div class="custom-v-flyout" id="speedFlyout_{{ $status->id }}" style="display:none; position:absolute; bottom:38px; right:0; background: #1d2333; border: 1px solid rgba(255,255,255,0.15); border-radius: 8px; padding: 4px; min-width: 100px; z-index:99;">
                                            <div class="v-speed-item" data-speed="0.5" onclick="setVideoSpeed(0.5, '{{ $status->id }}')">0.5x</div>
                                            <div class="v-speed-item active" data-speed="1.0" onclick="setVideoSpeed(1.0, '{{ $status->id }}')">1.0x (عادي)</div>
                                            <div class="v-speed-item" data-speed="1.25" onclick="setVideoSpeed(1.25, '{{ $status->id }}')">1.25x</div>
                                            <div class="v-speed-item" data-speed="1.5" onclick="setVideoSpeed(1.5, '{{ $status->id }}')">1.5x</div>
                                            <div class="v-speed-item" data-speed="2.0" onclick="setVideoSpeed(2.0, '{{ $status->id }}')">2.0x</div>
                                        </div>
                                    </div>

                                    <!-- FULLSCREEN -->
                                    <button type="button" class="v-btn" id="btnFullscreen_{{ $status->id }}" title="{{ __('messages.fullscreen') ?? 'ملء الشاشة' }}">
                                        <i class="fa fa-expand"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($ytEmbedUrl)
                    <div class="ratio ratio-16x9 video-iframe-box">
                        <iframe src="{{ $ytEmbedUrl }}" title="{{ $topic->name }}" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                    </div>
                @else
                    <div class="video-placeholder-box d-flex flex-column align-items-center justify-content-center text-center p-5">
                        <i class="fa fa-film fa-3x text-primary mb-3"></i>
                        <p class="text-muted fw-bold mb-0">{{ __('messages.no_video_stream') ?? 'لا يوجد مقطع فيديو متاح للعرض.' }}</p>
                    </div>
                @endif
            </div>

            <!-- 2. PUBLISHER HEADER & ACTION BUTTONS BAR -->
            <div class="video-author-actions-card shadow-sm mb-3 p-3 rounded-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <!-- PUBLISHER IDENTITY -->
                    <div class="d-flex align-items-center gap-3">
                        <!-- HEXAGON AVATAR (EXACT MYADS SIDEBAR / FEED STRUCTURE) -->
                        <a class="user-status-avatar flex-shrink-0" href="{{ route('profile.show', $topic->user->username) }}">
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
                                <a class="bold text-decoration-none author-name-link" href="{{ route('profile.show', $topic->user->username) }}">
                                    {{ $topic->user->username }}
                                </a>
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
                                <form action="{{ route('profile.follow', $topic->user->id) }}" method="POST" class="ms-2 d-inline">
                                    @csrf
                                    <button type="submit" class="button {{ $isFollowing ? 'primary' : 'secondary' }} small" style="height: 32px; padding: 0 14px; font-size: 12px; font-weight: 700; border-radius: 16px; width: auto !important; flex: none !important;">
                                        <i class="fa {{ $isFollowing ? 'fa-check' : 'fa-user-plus' }} me-1"></i>
                                        <span>{{ $isFollowing ? __('messages.following') : '+ ' . __('messages.follow') }}</span>
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>

                    <!-- COMPACT UNIFORM ACTION BUTTONS (REACTIONS, SAVE, SHARE, OPTIONS) -->
                    <div class="v-action-buttons-wrap">
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
                                            <span class="reaction_txt{{ $status->id }}">تفاعل</span>
                                        @endif
                                    </div>
                                </button>

                                <div class="reaction-options reaction-options-dropdown" style="position: absolute; z-index: 99999; bottom: 44px; right: 0; display: none;">
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
                                    <i class="fa {{ $isSaved ? 'fa-bookmark text-primary' : 'fa-bookmark-o' }} me-1"></i>
                                    <span class="btn-save-text">{{ $isSaved ? 'تم الحفظ' : 'حفظ' }}</span>
                                </button>
                            </div>
                        @endauth

                        <!-- SHARE BUTTON (CUSTOM POPUP FLYOUT) -->
                        <div class="v-flyout-wrap">
                            <button type="button" class="v-action-btn" onclick="toggleCustomFlyout(this, 'flyout_share_{{ $status->id }}')">
                                <i class="fa fa-share-alt me-1"></i>
                                <span>مشاركة</span>
                            </button>
                            <div class="custom-v-flyout" id="flyout_share_{{ $status->id }}" style="display:none;">
                                <div class="v-flyout-item" onclick="navigator.clipboard.writeText('{{ route('forum.topic', $topic->id) }}'); showToast('تم نسخ رابط الفيديو بنجاح'); toggleCustomFlyout(null, 'flyout_share_{{ $status->id }}');">
                                    <i class="fa fa-link text-primary me-2"></i> نسخ الرابط
                                </div>
                                <div style="height:1px; background:var(--border-color, #eee); margin:6px 0;"></div>
                                @foreach(['facebook' => 'فيس بوك', 'twitter' => 'تويتر (X)', 'linkedin' => 'لينكد إن', 'telegram' => 'تيليجرام'] as $socialKey => $socialName)
                                    <div class="v-flyout-item" onclick="sharePost('{{ $socialKey }}', '{{ route('forum.topic', $topic->id) }}', '{{ e($topic->name) }}'); toggleCustomFlyout(null, 'flyout_share_{{ $status->id }}');">
                                        <img src="{{ theme_asset('img/icons/'.$socialKey.'-icon.png') }}" width="16" class="me-2"> {{ $socialName }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- OPTIONS / REPORT MENU (CUSTOM POPUP FLYOUT) -->
                        <div class="v-flyout-wrap">
                            <button type="button" class="v-action-btn icon-only" onclick="toggleCustomFlyout(this, 'flyout_more_{{ $status->id }}')" title="خيارات">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <div class="custom-v-flyout" id="flyout_more_{{ $status->id }}" style="display:none;">
                                @if($canEditTopic)
                                    <a href="{{ route('forum.edit', $topic->id) }}" class="v-flyout-item text-decoration-none">
                                        <i class="fa fa-edit text-warning me-2"></i> تعديل
                                    </a>
                                @endif
                                @if($canDeleteTopic)
                                    <div class="v-flyout-item text-danger" onclick="deletePost({{ $topic->id }}, 100)">
                                        <i class="fa fa-trash me-2"></i> حذف
                                    </div>
                                @endif
                                <div class="v-flyout-item" onclick="reportPost({{ $topic->id }}, 2); toggleCustomFlyout(null, 'flyout_more_{{ $status->id }}');">
                                    <i class="fa fa-flag text-danger me-2"></i> إبلاغ
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. TITLE & DESCRIPTION CARD -->
            <div class="video-details-card shadow-sm mb-4">
                <h1 class="video-watch-title">{{ $topic->name }}</h1>

                <!-- METADATA BAR -->
                <div class="d-flex align-items-center flex-wrap gap-3 text-muted small my-2 pb-3 border-bottom">
                    <span>
                        <i class="fa fa-eye me-1 text-primary"></i>
                        {{ number_format($topic->vu ?? 0) }} مشاهدة
                    </span>
                    <span>
                        <i class="fa fa-clock-o me-1"></i>
                        {{ \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans() }}
                    </span>
                    @if($topic->category)
                        <span class="badge bg-light text-dark border">
                            <i class="fa fa-folder-open me-1 text-primary"></i> {{ $topic->category->name }}
                        </span>
                    @endif
                </div>

                <!-- FORMATTED DESCRIPTION CONTENT -->
                <div class="video-description-wrapper mt-3" id="videoDescWrapper">
                    <div class="video-description-text textpost" id="videoDescText">
                        {!! \App\Support\ContentFormatter::formatForum($topic->txt) !!}
                    </div>
                    <button type="button" class="btn btn-link btn-sm p-0 mt-2 text-primary fw-bold d-none" id="btnToggleDesc" onclick="toggleDescExpand()">
                        عرض المزيد
                    </button>
                </div>
            </div>

            <!-- 4. COMMENTS SECTION BELOW DESCRIPTION -->
            <div class="video-comments-card shadow-sm p-4 rounded-3 bg-card mb-4">
                <h3 class="h5 fw-bold mb-3 d-flex align-items-center gap-2">
                    <i class="fa fa-comments text-primary"></i>
                    <span>التعليقات ({{ $topic->comments()->count() }})</span>
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
        <div class="col-lg-4 col-xl-4">
            <div class="suggested-videos-card shadow-sm sticky-top" style="top: 80px; z-index: 10;">
                <h3 class="suggested-card-heading">
                    <i class="fa fa-play-circle text-primary me-2"></i>
                    <span>فيديوهات مقترحة</span>
                </h3>

                <div class="suggested-videos-list">
                    @forelse($suggestedVideos as $sVideo)
                        @php $sTopic = $sVideo->forumTopic; @endphp
                        @if($sTopic && (int) $sVideo->s_type !== 4 && (int) $sVideo->s_type !== 14)
                            <a href="{{ route('forum.topic', $sTopic->id) }}" class="suggested-video-item text-decoration-none">
                                <!-- THUMBNAIL BOX -->
                                <div class="suggested-thumb-box flex-shrink-0">
                                    @if($sTopic->image_url)
                                        <img src="{{ $sTopic->image_url }}" alt="{{ $sTopic->name }}" class="suggested-thumb-img" loading="lazy">
                                    @else
                                        <div class="suggested-thumb-placeholder">
                                            <i class="fa fa-play-circle fa-2x text-white-50"></i>
                                        </div>
                                    @endif
                                    <span class="video-play-badge">
                                        <i class="fa fa-play"></i>
                                    </span>
                                </div>

                                <!-- DETAILS -->
                                <div class="suggested-video-info flex-grow-1 min-w-0">
                                    <h4 class="suggested-video-title text-truncate-2 mb-1">
                                        {{ $sTopic->name }}
                                    </h4>
                                    <div class="suggested-video-author text-muted small text-truncate">
                                        <i class="fa fa-user me-1"></i> {{ $sTopic->user?->username }}
                                    </div>
                                    <div class="suggested-video-meta text-muted extra-small mt-1">
                                        <span>{{ number_format($sTopic->vu ?? 0) }} مشاهدة</span>
                                        <span class="mx-1">&bull;</span>
                                        <span>{{ \Carbon\Carbon::createFromTimestamp($sVideo->date)->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @empty
                        <div class="p-3 text-center text-muted small">
                            لا توجد فيديوهات مقترحة حالياً.
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
.bg-card,
.video-stage-card,
.video-details-card,
.suggested-videos-card {
    background: var(--section-background-color, #ffffff);
    border: 1px solid var(--border-color, #e0e6ed);
    border-radius: 16px;
    overflow: hidden;
}

/* CRITICAL FIX: OVERFLOW VISIBLE ON AUTHOR ACTIONS CARD TO PREVENT FLYOUT CLIPPING */
.video-author-actions-card {
    background: var(--section-background-color, #ffffff);
    border: 1px solid var(--border-color, #e0e6ed);
    border-radius: 16px;
    overflow: visible !important;
    position: relative;
    z-index: 25;
}

/* DARK MODE OVERRIDES */
[data-bs-theme="dark"] .video-stage-card,
[data-bs-theme="dark"] .video-author-actions-card,
[data-bs-theme="dark"] .video-details-card,
[data-bs-theme="dark"] .suggested-videos-card,
.dark .video-stage-card,
.dark .video-author-actions-card,
.dark .video-details-card,
.dark .suggested-videos-card {
    background: var(--section-background-color, #1d2333);
    border-color: rgba(255, 255, 255, 0.08);
}

/* HEXAGON AVATAR CLIP-PATH FALLBACK */
.user-avatar .hexagon-image-30-32,
.user-avatar .hexagon-image-40-44 {
    clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
    -webkit-clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
    background-size: cover;
    background-position: center;
}

/* 1. VIDEO PLAYER STAGE */
.video-stage-card {
    background: #000;
    border-color: rgba(97, 93, 250, 0.3);
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
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: rgba(97, 93, 250, 0.9);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    box-shadow: 0 0 25px rgba(97, 93, 250, 0.6);
    transition: transform 0.2s ease, background 0.2s ease;
    padding-left: 4px;
}
.video-overlay-play:hover .play-icon-circle {
    transform: scale(1.12);
    background: #615dfa;
}

/* CUSTOM HTML5 CONTROLS BAR */
.video-controls-bar {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.92), rgba(0,0,0,0.3) 70%, transparent);
    padding: 8px 14px;
    z-index: 3;
    transition: opacity 0.3s ease;
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
    background: rgba(97, 93, 250, 0.85);
    border-radius: 3px;
    width: 0%;
    pointer-events: none;
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
    transition: background 0.2s ease, color 0.2s ease;
}
.v-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: #615dfa;
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
    font-family: monospace;
}
.v-volume-range {
    width: 55px;
    accent-color: #615dfa;
    cursor: pointer;
}

/* 2. PUBLISHER HEADER & UNIFORM ACTION BUTTONS */
.video-author-actions-card {
    padding: 14px 18px;
}
.author-name-link {
    color: var(--text-color, #2b2d42);
    font-size: 15px;
    font-weight: 700;
}
.author-name-link:hover {
    color: #615dfa;
}

/* ACTION BUTTONS CONTAINER & STYLING */
.v-action-buttons-wrap {
    display: flex !important;
    align-items: center !important;
    flex-direction: row !important;
    gap: 8px !important;
    flex-wrap: wrap !important;
    margin: 0 !important;
}

.v-flyout-wrap {
    position: relative !important;
    display: inline-flex !important;
    align-items: center !important;
    width: auto !important;
    flex: 0 0 auto !important;
    margin: 0 !important;
}

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
    width: auto !important;
    min-width: auto !important;
    max-width: max-content !important;
    flex: 0 0 auto !important;
    margin: 0 !important;
    box-sizing: border-box !important;
}
.v-action-btn:hover {
    background: #615dfa !important;
    color: #fff !important;
    border-color: #615dfa !important;
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

/* CUSTOM POPUP FLYOUT BOX */
.custom-v-flyout {
    position: absolute !important;
    top: 42px !important;
    left: 0 !important;
    min-width: 180px !important;
    background: var(--section-background-color, #ffffff) !important;
    border: 1px solid var(--border-color, #e0e6ed) !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.18) !important;
    padding: 8px !important;
    z-index: 99999 !important;
}

[dir="rtl"] .custom-v-flyout {
    left: 0 !important;
    right: auto !important;
}

[data-bs-theme="dark"] .custom-v-flyout,
.dark .custom-v-flyout {
    background: #1d2333 !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
    color: #ffffff !important;
}

.reaction-options-dropdown {
    position: absolute !important;
    z-index: 99999 !important;
    bottom: 44px !important;
    right: 0 !important;
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

/* 3. DETAILS CARD */
.video-details-card {
    padding: 20px;
}
.video-watch-title {
    font-size: 19px;
    font-weight: 700;
    color: var(--text-color, #1e2022);
    line-height: 1.4;
    margin-bottom: 6px;
}
.video-description-text {
    font-size: 14px;
    line-height: 1.6;
    color: var(--text-color, #333);
}
.video-description-text.collapsed {
    max-height: 130px;
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

/* 4. SUGGESTED VIDEOS SIDEBAR */
.suggested-videos-card {
    padding: 18px;
}
.suggested-card-heading {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    color: var(--text-color, #222);
}
.suggested-video-item {
    display: flex;
    gap: 12px;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-color, #f0f2f5);
    transition: transform 0.2s ease;
}
.suggested-video-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
.suggested-video-item:hover {
    transform: translateX(-3px);
}
[dir="rtl"] .suggested-video-item:hover {
    transform: translateX(3px);
}
.suggested-thumb-box {
    width: 115px;
    height: 68px;
    border-radius: 8px;
    overflow: hidden;
    position: relative;
    background: #111;
}
.suggested-thumb-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.suggested-thumb-placeholder {
    width: 100%; height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1d2333, #615dfa);
}
.video-play-badge {
    position: absolute;
    bottom: 4px; right: 4px;
    background: rgba(0, 0, 0, 0.75);
    color: #fff;
    font-size: 10px;
    padding: 2px 5px;
    border-radius: 3px;
}
.suggested-video-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-color, #222);
    line-height: 1.35;
    margin: 0;
}
.suggested-video-item:hover .suggested-video-title {
    color: #615dfa;
}
.extra-small {
    font-size: 11px;
}
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<!-- SCOPED JAVASCRIPT FOR PLAYER & INTERACTIVE ACTIONS -->
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

        video.addEventListener('play', function () {
            if (overlayPlay) overlayPlay.classList.add('playing');
            if (btnPlay) btnPlay.innerHTML = '<i class="fa fa-pause"></i>';
        });

        video.addEventListener('pause', function () {
            if (overlayPlay) overlayPlay.classList.remove('playing');
            if (btnPlay) btnPlay.innerHTML = '<i class="fa fa-play"></i>';
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

        // KEYBOARD SHORTCUTS (Space/K for Play/Pause, F for Fullscreen, M for Mute)
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
            }
        });
    }

    // EXPANDABLE DESCRIPTION TOGGLE
    const descText = document.getElementById('videoDescText');
    const btnToggleDesc = document.getElementById('btnToggleDesc');
    if (descText && descText.scrollHeight > 150) {
        descText.classList.add('collapsed');
        if (btnToggleDesc) btnToggleDesc.classList.remove('d-none');
    }
});

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
        if (btnToggleDesc) btnToggleDesc.textContent = "عرض أقل";
    } else {
        descText.classList.add('collapsed');
        if (btnToggleDesc) btnToggleDesc.textContent = "عرض المزيد";
    }
}

// CUSTOM FLYOUT DROPDOWN TOGGLE (WITHOUT BOOTSTRAP MODAL DEPENDENCY)
function toggleCustomFlyout(triggerBtn, targetId) {
    document.querySelectorAll('.custom-v-flyout').forEach(el => {
        if (el.id !== targetId) el.style.display = 'none';
    });
    const flyout = document.getElementById(targetId);
    if (!flyout) return;
    if (flyout.style.display === 'none' || !flyout.style.display) {
        flyout.style.display = 'block';
    } else {
        flyout.style.display = 'none';
    }
}

// CLOSE FLYOUTS WHEN CLICKING OUTSIDE
document.addEventListener('click', function(e) {
    if (!e.target.closest('.v-flyout-wrap')) {
        document.querySelectorAll('.custom-v-flyout').forEach(el => {
            el.style.display = 'none';
        });
    }
});

// SAVE VIDEO AJAX TOGGLE
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
                if (icon) icon.className = 'fa fa-bookmark text-primary me-1';
                if (label) label.textContent = 'تم الحفظ';
                showToast('تم حفظ الفيديو بنجاح');
            } else {
                if (icon) icon.className = 'fa fa-bookmark-o me-1';
                if (label) label.textContent = 'حفظ';
                showToast('تم إزالة الفيديو من المحفوظات');
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
        notifBox.style.cssText = 'position:fixed; bottom:24px; left:24px; z-index:99999;';
        document.body.appendChild(notifBox);
    }
    const toastEl = document.createElement('div');
    toastEl.className = 'alert alert-success shadow-lg border-0 rounded-3 px-4 py-3 mb-2 text-white';
    toastEl.style.background = '#615dfa';
    toastEl.innerHTML = '<i class="fa fa-check-circle me-2"></i>' + msg;
    notifBox.appendChild(toastEl);
    setTimeout(() => toastEl.remove(), 3500);
}
</script>

@include('theme::forum.scripts')
@endsection
