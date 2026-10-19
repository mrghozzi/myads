@extends('theme::layouts.master')

@section('content')
<style>
    /* Full Clips Container */
    .clips-wrapper {
        height: 100%;
        background-color: #000 !important;
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        width: 100%;
        max-width: 450px;
        flex-shrink: 0;
        margin: 0 auto;
        box-shadow: 0 16px 40px rgba(0,0,0,0.6);
    }
    
    .clips-container {
        height: 100%;
        overflow-y: scroll;
        scroll-snap-type: y mandatory;
        scroll-behavior: smooth;
        -ms-overflow-style: none;
        scrollbar-width: none;
        background-color: #000 !important;
    }
    
    .clips-container::-webkit-scrollbar {
        display: none;
    }

    .reel-item {
        height: 100%;
        width: 100%;
        scroll-snap-align: start;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #000 !important;
    }

    .reel-video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
        background-color: #000 !important;
    }

    .reel-overlay {
        position: absolute;
        inset: 0;
        pointer-events: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* Double Tap Pop Heart Animation */
    .reel-heart-pop-container {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
        z-index: 25;
        opacity: 0;
    }
    .reel-heart-pop-container.animate-pop {
        animation: heartPop 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }
    .pop-heart-icon {
        width: 100px;
        height: 100px;
        fill: #ff3366;
        filter: drop-shadow(0 0 15px rgba(255,51,102,0.8));
    }
    @keyframes heartPop {
        0% { opacity: 0; transform: scale(0.3) rotate(-15deg); }
        40% { opacity: 1; transform: scale(1.2) rotate(0deg); }
        70% { opacity: 0.9; transform: scale(1) rotate(5deg); }
        100% { opacity: 0; transform: scale(1.4) translateY(-40px); }
    }

    /* Play/Pause indicator fades out */
    .reel-play-indicator {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .reel-item.is-paused .reel-play-indicator {
        opacity: 1;
    }

    .reel-info {
        position: absolute;
        left: 15px;
        bottom: 25px;
        color: #fff;
        max-width: calc(100% - 85px);
        pointer-events: auto;
        text-shadow: 0 1px 4px rgba(0,0,0,0.9);
        z-index: 10;
    }

    .reel-user {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        color: #fff;
        text-decoration: none;
    }

    .reel-user:hover {
        color: #fff;
    }

    .reel-hex-avatar {
        width: 40px;
        height: 44px;
        flex-shrink: 0;
    }

    .reel-username {
        font-weight: 700;
        font-size: 15px;
    }

    .reel-caption {
        font-size: 14px;
        line-height: 1.4;
        word-break: break-word;
    }

    .reel-caption.reel-caption-truncated {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .reel-caption-more {
        background: none;
        border: none;
        color: rgba(255,255,255,0.7);
        font-size: 13px;
        font-weight: 600;
        padding: 4px 0 0 0;
        cursor: pointer;
        text-shadow: 0 1px 3px rgba(0,0,0,0.8);
        pointer-events: auto;
    }

    .reel-caption-more:hover {
        color: #fff;
    }

    /* Sound Tag & Spinning Disc Styles */
    .spinning-audio-disc {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: radial-gradient(circle, #333 40%, #111 70%, #000 100%);
        border: 2px solid rgba(255, 255, 255, 0.4);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 10px;
        animation: spinDisc 4s linear infinite;
    }
    @keyframes spinDisc {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .reel-item.is-paused .spinning-audio-disc {
        animation-play-state: paused;
    }
    .reel-sound-tag {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.9);
        background: rgba(0, 0, 0, 0.45);
        padding: 5px 12px;
        border-radius: 20px;
        backdrop-filter: blur(4px);
        max-width: 240px;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .reel-actions {
        position: absolute;
        right: 12px;
        bottom: 25px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
        pointer-events: auto;
        z-index: 10;
    }

    .reel-action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #fff;
        cursor: pointer;
        background: transparent;
        border: none;
        padding: 0;
        transition: transform 0.2s;
    }

    .reel-action-btn:hover {
        transform: scale(1.1);
    }

    .reel-action-btn .icon {
        width: 32px;
        height: 32px;
        fill: #fff;
        margin-bottom: 4px;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.6));
        transition: fill 0.3s, transform 0.3s;
    }

    .reel-action-btn span {
        font-size: 12px;
        font-weight: 600;
        text-shadow: 0 1px 3px rgba(0,0,0,0.8);
    }

    .reel-action-btn.active.toggle-reaction .icon {
        fill: #ff3366;
        animation: heartBeat 0.5s ease-in-out;
    }

    .reel-action-btn.active.toggle-save .icon {
        fill: #23d2e2;
    }

    .clip-views-badge {
        cursor: default;
        opacity: 0.9;
    }
    .clip-views-badge:hover {
        transform: none;
    }
    .clip-views-badge .icon {
        width: 26px;
        height: 26px;
    }

    @keyframes heartBeat {
        0% { transform: scale(1); }
        50% { transform: scale(1.3); }
        100% { transform: scale(1); }
    }

    /* Floating Navigation Tabs (Latest | Trending | Following) */
    .clips-nav-tabs {
        position: absolute;
        top: 15px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 20;
        display: flex;
        align-items: center;
        gap: 6px;
        background: rgba(0, 0, 0, 0.45);
        backdrop-filter: blur(10px);
        padding: 4px 6px;
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .clip-tab-link {
        color: rgba(255, 255, 255, 0.7);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        padding: 5px 12px;
        border-radius: 20px;
        transition: all 0.2s ease;
    }

    .clip-tab-link:hover {
        color: #fff;
    }

    .clip-tab-link.active {
        background: rgba(255, 255, 255, 0.25);
        color: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }

    /* Floating Header Actions (Create + Saved + Report) */
    .clips-header-actions {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 20;
        display: flex;
        gap: 8px;
        align-items: center;
    }
    
    .btn-create-clip,
    .btn-saved-clips,
    .btn-report-clip {
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(8px);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 20px;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-create-clip {
        background: linear-gradient(135deg, #ff3366 0%, #ff5e3a 100%);
        border: none;
        box-shadow: 0 4px 12px rgba(255, 51, 102, 0.4);
    }
    .btn-create-clip:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(255, 51, 102, 0.6);
        color: #fff;
    }
    .btn-saved-clips:hover,
    .btn-report-clip:hover {
        background: rgba(0,0,0,0.8);
        color: #fff;
    }
    .btn-create-clip svg,
    .btn-saved-clips svg,
    .btn-report-clip svg {
        width: 15px;
        height: 15px;
        fill: currentColor;
    }

    /* Mute Toggle Button */
    .reel-mute-toggle {
        position: absolute;
        top: 18px;
        left: 18px;
        z-index: 20;
        width: 38px;
        height: 38px;
        background: rgba(0,0,0,0.5);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        backdrop-filter: blur(6px);
        pointer-events: auto;
        border: 1px solid rgba(255,255,255,0.15);
        transition: transform 0.2s;
    }
    .reel-mute-toggle:hover {
        transform: scale(1.08);
    }
    .reel-mute-toggle svg {
        pointer-events: none;
    }

    /* Seekable Progress Bar */
    .reel-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: rgba(255, 255, 255, 0.2);
        z-index: 30;
        cursor: pointer;
        transition: height 0.15s ease;
    }
    .reel-progress:hover {
        height: 8px;
    }
    .reel-progress-filled {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #ff3366, #23d2e2);
        transition: width 0.1s linear;
        border-radius: 0 4px 4px 0;
    }

    /* Layout & Comments Sidebar */
    .clips-layout-container {
        display: flex;
        justify-content: center;
        align-items: stretch;
        gap: 20px;
        max-width: 950px;
        margin: 0 auto;
        height: calc(100vh - 80px);
        position: relative;
        width: 100%;
    }

    .clips-comments-sidebar {
        width: 420px;
        background: var(--bg-color, #fff);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        display: none;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.08);
        z-index: 30;
    }

    .clips-comments-sidebar.active {
        display: flex;
    }

    .sidebar-header {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(0,0,0,0.08);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .sidebar-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: var(--text-color, #3e3f5e);
    }

    .close-sidebar-btn {
        background: transparent;
        border: none;
        font-size: 28px;
        cursor: pointer;
        line-height: 1;
        color: var(--text-color-light, #9aa0ac);
        padding: 0;
        transition: color 0.2s;
    }

    .close-sidebar-btn:hover {
        color: var(--text-color, #3e3f5e);
    }

    .sidebar-content {
        flex: 1;
        overflow-y: auto;
        padding: 10px 15px 20px 15px;
    }

    /* Quick keyboard shortcut reminder hint */
    .clips-keyboard-hint {
        position: absolute;
        bottom: -28px;
        left: 50%;
        transform: translateX(-50%);
        color: var(--text-color-light, #9aa0ac);
        font-size: 12px;
        white-space: nowrap;
        opacity: 0.8;
    }

    @media (max-width: 991px) {
        .clips-layout-container {
            flex-direction: column;
            align-items: center;
            height: calc(100dvh - 70px);
        }

        .clips-nav-tabs {
            top: 10px;
            padding: 3px 4px;
        }
        .clip-tab-link {
            font-size: 12px;
            padding: 4px 8px;
        }

        .clips-comments-sidebar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            height: 65%;
            border-radius: 20px 20px 0 0;
            box-shadow: 0 -10px 30px rgba(0,0,0,0.4);
            z-index: 100;
        }
        .clips-keyboard-hint {
            display: none;
        }
    }

    /* Upload Clip Modal */
    .clip-upload-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(8px);
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .clip-upload-modal-backdrop.is-open {
        display: flex;
    }
    .clip-upload-modal {
        background: var(--composer-surface, #1f2436);
        color: #fff;
        border-radius: 20px;
        max-width: 480px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        border: 1px solid rgba(255,255,255,0.1);
        padding: 24px;
        position: relative;
    }
    .clip-upload-modal h4 {
        margin: 0 0 8px 0;
        font-weight: 800;
        font-size: 18px;
        color: #fff;
    }
</style>

<div class="grid" style="padding-top: 0; position: relative;">
    <div class="clips-layout-container">
        <div class="clips-wrapper">
            <!-- Navigation Tabs (Latest | Trending | Following) -->
            <div class="clips-nav-tabs">
                <a href="{{ route('clips.index', ['tab' => 'latest']) }}" class="clip-tab-link {{ $tab === 'latest' ? 'active' : '' }}">
                    {{ __('messages.latest_clips') ?? 'Latest' }}
                </a>
                <a href="{{ route('clips.index', ['tab' => 'trending']) }}" class="clip-tab-link {{ $tab === 'trending' ? 'active' : '' }}">
                    <i class="fa fa-fire text-warning me-1"></i> {{ __('messages.trending_clips') ?? 'Trending' }}
                </a>
                <a href="{{ route('clips.index', ['tab' => 'following']) }}" class="clip-tab-link {{ $tab === 'following' ? 'active' : '' }}">
                    {{ __('messages.following_clips') ?? 'Following' }}
                </a>
            </div>

            <!-- Header Actions -->
            <div class="clips-header-actions">
                @auth
                    <button type="button" class="btn-create-clip" id="btn-open-clip-upload">
                        <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                        <span>{{ __('messages.create_clip') ?? 'Create' }}</span>
                    </button>
                    <button class="btn-report-clip" id="btn-report-clip" title="{{ __('messages.report') ?? 'Report' }}">
                        <svg viewBox="0 0 24 24"><path d="M14.4 6L14 4H5v17h2v-7h5.6l.4 2h7V6z"/></svg>
                    </button>
                @else
                    <a href="{{ route('login') }}" class="btn-create-clip">
                        <svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                        <span>{{ __('messages.create_clip') ?? 'Create' }}</span>
                    </a>
                @endauth
                <a href="{{ route('clips.saved') }}" class="btn-saved-clips" title="{{ __('messages.saved') ?? 'Saved' }}">
                    <svg viewBox="0 0 24 24"><path d="M17 3H7c-1.1 0-1.99.9-1.99 2L5 21l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
                </a>
            </div>
            
            <!-- Video Feed Container -->
            <div class="clips-container" id="clips-container">
                @if($activities->isEmpty())
                    <div style="height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #fff; padding: 30px; text-align: center;">
                        <i class="fa fa-film" style="font-size: 48px; color: #ff3366; margin-bottom: 16px;"></i>
                        <h4 style="color: #fff; font-weight: 700;">{{ __('messages.no_videos_found') ?? 'No clips found' }}</h4>
                        <p style="color: rgba(255,255,255,0.7); font-size: 14px; max-width: 300px; margin-bottom: 20px;">
                            @if($tab === 'following')
                                {{ __('messages.no_following_clips_desc') ?? 'None of the creators you follow have published clips yet. Explore Latest or Trending clips!' }}
                            @else
                                {{ __('messages.no_clips_desc') ?? 'Be the first to share a vertical clip with the community!' }}
                            @endif
                        </p>
                        @auth
                            <button type="button" class="btn-create-clip" onclick="document.getElementById('btn-open-clip-upload')?.click();">
                                <i class="fa fa-plus me-1"></i> {{ __('messages.create_clip') ?? 'Create Clip' }}
                            </button>
                        @else
                            <a href="{{ route('clips.index', ['tab' => 'latest']) }}" class="button primary small">
                                {{ __('messages.view_all_clips') ?? 'View All Clips' }}
                            </a>
                        @endauth
                    </div>
                @else
                    @include('theme::clips.partials.clips_list', ['activities' => $activities])
                @endif
            </div>
            
            <!-- Loading Indicator for Infinite Scroll -->
            <div id="clips-loading" style="display: none; position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); text-align: center; color: #fff; text-shadow: 0 1px 3px rgba(0,0,0,0.8); z-index: 40;">
                <i class="fa-solid fa-spinner fa-spin"></i> Loading...
            </div>
        </div>

        <!-- Comments Sidebar -->
        <div class="clips-comments-sidebar" id="clips-comments-sidebar">
            <div class="sidebar-header">
                <h3>{{ __('messages.comments') ?? 'Comments' }}</h3>
                <button class="close-sidebar-btn" id="close-comments-sidebar">&times;</button>
            </div>
            <div class="sidebar-content">
                <div id="clips-comments-placeholder" class="post-comment-list-placeholder">
                    <div class="no-comments-selected" style="text-align: center; padding: 40px 20px; color: var(--text-color-light, #9aa0ac);">
                        <i class="fa-regular fa-comments" style="font-size: 48px; margin-bottom: 15px; display: block; opacity: 0.5;"></i>
                        {{ __('messages.click_to_view_comments') ?? 'Click the comment icon on a clip to view comments.' }}
                    </div>
                </div>
            </div>
        </div>
        
        <div id="reportclips-modal-container" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1050; width: 90%; max-width: 400px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);"></div>

        <div class="clips-keyboard-hint">
            <i class="fa fa-keyboard me-1"></i> {{ __('messages.press_keys_hint') ?? 'Keys: ↑/↓ Scroll • Space Play/Pause • M Mute • L Like' }}
        </div>
    </div>
</div>

<!-- Upload Clip Modal Dialog -->
@auth
<div class="clip-upload-modal-backdrop" id="clip-upload-modal-backdrop">
    <div class="clip-upload-modal">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4><i class="fa fa-bolt text-danger me-2"></i> {{ __('messages.upload_clip_modal_title') ?? 'Post New Clip' }}</h4>
            <button type="button" class="close-sidebar-btn text-white" id="close-clip-upload">&times;</button>
        </div>
        <p class="text-white-50 small mb-4">{{ __('messages.upload_clip_desc') ?? 'Share a short vertical video (up to 3 minutes) with the community.' }}</p>

        <form action="{{ route('status.create') }}" method="POST" enctype="multipart/form-data" id="clip-upload-form">
            @csrf
            <input type="hidden" name="post_kind" value="clips">
            
            <!-- Video File Input -->
            <div class="mb-3">
                <label class="form-label text-white small fw-bold">{{ __('messages.select_clip_file') ?? 'Choose Video File' }} *</label>
                <input type="file" name="videos[]" id="modal-clip-video-input" accept="video/mp4,video/webm,video/quicktime,video/mov" class="form-control bg-dark text-white border-secondary" required>
                <div id="modal-video-preview-wrap" class="mt-2 text-center" style="display: none;">
                    <video id="modal-video-preview" style="max-height: 180px; max-width: 100%; border-radius: 10px; border: 1px solid rgba(255,255,255,0.2);" controls playsinline muted></video>
                    <p class="text-white-50 small mt-1" id="modal-video-duration"></p>
                </div>
            </div>

            <!-- Caption Textarea -->
            <div class="mb-3">
                <label class="form-label text-white small fw-bold">{{ __('messages.clip_caption') ?? 'Clip Caption' }} *</label>
                <textarea name="text" id="modal-clip-caption" class="form-control bg-dark text-white border-secondary" rows="3" placeholder="{{ __('messages.video_title_placeholder') ?? 'Write a short engaging caption with #hashtags...' }}" required></textarea>
            </div>

            <!-- Sound Title (Optional) -->
            <div class="mb-3">
                <label class="form-label text-white small fw-bold">{{ __('messages.clip_sound_title') ?? 'Sound / Music Title (Optional)' }}</label>
                <input type="text" name="sound_title" class="form-control bg-dark text-white border-secondary" placeholder="e.g. Original Audio • Artist / Track">
            </div>

            <!-- Custom Cover Thumbnail (Optional) -->
            <div class="mb-4">
                <label class="form-label text-white small fw-bold">{{ __('messages.clip_thumbnail_optional') ?? 'Custom Cover Thumbnail (Optional)' }}</label>
                <input type="file" name="video_thumbnail" accept="image/*" class="form-control bg-dark text-white border-secondary">
            </div>

            <!-- Submit Button & Spinner -->
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3" id="cancel-clip-upload">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4 fw-bold" id="submit-clip-btn">
                    <span class="normal-text"><i class="fa fa-cloud-upload-alt me-1"></i> {{ __('messages.publish') ?? 'Publish Clip' }}</span>
                    <span class="loading-text" style="display: none;"><i class="fa fa-spinner fa-spin me-1"></i> Uploading...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endauth
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('clips-container');
    if (!container) return;

    const loadingEl = document.getElementById('clips-loading');
    let nextPageUrl = '{{ $activities->nextPageUrl() }}';
    let isLoading = false;
    const CSRF_TOKEN = '{{ csrf_token() }}';

    // Track viewed clips in this session to prevent spamming view counters
    const viewedClipsInSession = new Set();
    let currentActiveClipTimer = null;

    // LocalStorage Mute Preference
    let isGlobalMuted = localStorage.getItem('clips_muted') !== 'false';

    // Apply stored mute state
    function applyMuteState(isMuted) {
        isGlobalMuted = isMuted;
        localStorage.setItem('clips_muted', isMuted ? 'true' : 'false');
        
        document.querySelectorAll('.reel-video').forEach(v => {
            v.muted = isMuted;
        });

        document.querySelectorAll('.reel-item').forEach(el => {
            const iconMute = el.querySelector('.icon-mute');
            const iconUnmute = el.querySelector('.icon-unmute');
            if (iconMute && iconUnmute) {
                if (isMuted) {
                    iconMute.style.display = 'block';
                    iconUnmute.style.display = 'none';
                } else {
                    iconMute.style.display = 'none';
                    iconUnmute.style.display = 'block';
                }
            }
        });
    }

    // Initialize hexagonal avatars
    if (typeof initHexagons === 'function') {
        initHexagons(container);
    }
    if (typeof recolorBadgeHexagons === 'function') {
        recolorBadgeHexagons(container);
    }

    // View Tracking API caller
    function recordClipView(statusId, itemElement) {
        if (!statusId || viewedClipsInSession.has(statusId)) return;
        viewedClipsInSession.add(statusId);

        fetch('{{ route('clips.view') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status_id: statusId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.views_count !== undefined && itemElement) {
                const countEl = itemElement.querySelector('.view-count-text');
                if (countEl) {
                    countEl.textContent = Number(data.views_count).toLocaleString();
                }
            }
        })
        .catch(err => console.debug('View record err:', err));
    }

    // Smart Preload Helper: Only preload current and immediate next sibling
    function updateVideoPreloads(activeItem) {
        if (!activeItem) return;
        
        const activeVideo = activeItem.querySelector('.reel-video');
        if (activeVideo && activeVideo.getAttribute('preload') !== 'auto') {
            activeVideo.setAttribute('preload', 'auto');
        }

        const nextItem = activeItem.nextElementSibling;
        if (nextItem) {
            const nextVideo = nextItem.querySelector('.reel-video');
            if (nextVideo && nextVideo.getAttribute('preload') !== 'auto') {
                nextVideo.setAttribute('preload', 'auto');
            }
        }
    }

    // Intersection Observer for playing/pausing videos and triggering view counter
    const videoObserverOptions = {
        root: container,
        rootMargin: '0px',
        threshold: 0.6
    };

    const videoObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const video = entry.target.querySelector('.reel-video');
            const item = entry.target;
            
            if (!video) return;

            if (entry.isIntersecting) {
                video.muted = isGlobalMuted;
                video.play().then(() => {
                    item.classList.remove('is-paused');
                }).catch(e => {
                    // If unmuted autoplay fails due to browser policy, auto fall back to muted
                    video.muted = true;
                    video.play().then(() => {
                        item.classList.remove('is-paused');
                        applyMuteState(true);
                    }).catch(err => {
                        item.classList.add('is-paused');
                    });
                });

                // Track active reel details
                const activeTpId = item.dataset.tpId;
                const statusId = item.dataset.id;
                container.dataset.activeTpId = activeTpId;
                container.dataset.activeStatusId = statusId;
                
                // Update URL hash without jumping
                if (statusId && window.location.hash !== '#' + statusId) {
                    history.replaceState(null, '', '/clips#' + statusId);
                }

                // Preload current & next video
                updateVideoPreloads(item);

                // View Counter Timer: Trigger view after 2.5s of continuous watching
                if (currentActiveClipTimer) {
                    clearTimeout(currentActiveClipTimer);
                }
                currentActiveClipTimer = setTimeout(() => {
                    if (container.dataset.activeStatusId === statusId) {
                        recordClipView(statusId, item);
                    }
                }, 2500);

                // Sync comments sidebar if open
                const sidebar = document.getElementById('clips-comments-sidebar');
                if (sidebar && sidebar.classList.contains('active')) {
                    loadClipsComments(activeTpId);
                }
            } else {
                video.pause();
                item.classList.add('is-paused');
            }
        });
    }, videoObserverOptions);

    function triggerHeartPop(item) {
        const popContainer = item.querySelector('.reel-heart-pop-container');
        if (popContainer) {
            popContainer.classList.remove('animate-pop');
            void popContainer.offsetWidth; // Trigger reflow
            popContainer.classList.add('animate-pop');
        }
    }

    function toggleClipLike(statusId, type, item) {
        const btn = item.querySelector('.toggle-reaction');
        if (!btn) return;

        const countSpan = btn.querySelector('.reaction-count');
        let currentCount = parseInt(countSpan.textContent) || 0;
        const isActive = btn.classList.contains('active');

        if (isActive) {
            btn.classList.remove('active');
            countSpan.textContent = Math.max(0, currentCount - 1);
        } else {
            btn.classList.add('active');
            countSpan.textContent = currentCount + 1;
        }

        fetch('{{ route('reaction.toggle') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN
            },
            body: JSON.stringify({
                id: btn.dataset.id,
                type: type || 'clips',
                reaction: 'like'
            })
        }).catch(err => console.error(err));
    }

    function observeVideos() {
        document.querySelectorAll('.reel-item').forEach(item => {
            videoObserver.unobserve(item);
            videoObserver.observe(item);
            
            const video = item.querySelector('.reel-video');
            if (video && !item.dataset.boundEvents) {
                item.dataset.boundEvents = 'true';
                
                // Click to play/pause
                video.addEventListener('click', (e) => {
                    if (video.paused) {
                        video.play();
                        item.classList.remove('is-paused');
                    } else {
                        video.pause();
                        item.classList.add('is-paused');
                    }
                });

                // Double click / Double tap to Like
                let lastTap = 0;
                item.addEventListener('click', (e) => {
                    const now = Date.now();
                    const tapGap = now - lastTap;
                    if (tapGap < 350 && tapGap > 0) {
                        e.preventDefault();
                        triggerHeartPop(item);
                        const likeBtn = item.querySelector('.toggle-reaction');
                        if (likeBtn && !likeBtn.classList.contains('active')) {
                            toggleClipLike(item.dataset.id, 'clips', item);
                        }
                    }
                    lastTap = now;
                });

                // Progress Bar Update
                const progressBar = item.querySelector('.reel-progress-filled');
                video.addEventListener('timeupdate', () => {
                    if (video.duration && progressBar) {
                        const percent = (video.currentTime / video.duration) * 100;
                        progressBar.style.width = percent + '%';
                    }
                });

                // Seekable progress bar click
                const progressContainer = item.querySelector('.reel-progress');
                if (progressContainer) {
                    progressContainer.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const rect = progressContainer.getBoundingClientRect();
                        const clickX = e.clientX - rect.left;
                        const percent = Math.max(0, Math.min(1, clickX / rect.width));
                        if (video.duration) {
                            video.currentTime = percent * video.duration;
                        }
                    });
                }

                // Mute Toggle Logic
                const muteBtn = item.querySelector('.reel-mute-toggle');
                if (muteBtn) {
                    muteBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        applyMuteState(!isGlobalMuted);
                    });
                }
            }
        });

        // Initialize mute icons according to global state
        applyMuteState(isGlobalMuted);
    }

    observeVideos();

    // Scroll to specific clip if URL has a hash (e.g. /clips#55)
    function scrollToHashClip() {
        const hash = window.location.hash;
        if (hash && hash.length > 1) {
            const clipId = hash.substring(1);
            const targetItem = document.querySelector('.reel-item[data-id="' + clipId + '"]');
            if (targetItem) {
                container.style.scrollBehavior = 'auto';
                targetItem.scrollIntoView({ block: 'start' });
                setTimeout(() => {
                    container.style.scrollBehavior = 'smooth';
                }, 100);
                return true;
            }
        }
        return false;
    }

    scrollToHashClip();

    // Infinite Scroll Observer
    const sentinelObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && nextPageUrl && !isLoading) {
                loadMoreClips();
            }
        });
    }, {
        root: container,
        threshold: 0.1
    });

    function observeSentinel() {
        const items = document.querySelectorAll('.reel-item');
        if (items.length > 0) {
            sentinelObserver.disconnect();
            sentinelObserver.observe(items[items.length - 1]);
        }
    }
    
    observeSentinel();

    function loadMoreClips() {
        if (!nextPageUrl) return;
        
        isLoading = true;
        if (loadingEl) loadingEl.style.display = 'block';

        fetch(nextPageUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                container.insertAdjacentHTML('beforeend', data.html);
                nextPageUrl = data.next_page_url;
                observeVideos();
                observeSentinel();

                if (typeof initHexagons === 'function') initHexagons(container);
                if (typeof recolorBadgeHexagons === 'function') recolorBadgeHexagons(container);

                scrollToHashClip();
            } else {
                nextPageUrl = null;
            }
        })
        .catch(err => console.error(err))
        .finally(() => {
            isLoading = false;
            if (loadingEl) loadingEl.style.display = 'none';
        });
    }

    // Reaction Button Click Delegation
    container.addEventListener('click', function(e) {
        const btn = e.target.closest('.toggle-reaction');
        if (!btn) return;
        
        const item = btn.closest('.reel-item');
        if (!item) return;

        toggleClipLike(item.dataset.id, 'clips', item);
    });

    // Save Button Click Delegation
    container.addEventListener('click', function(e) {
        const btn = e.target.closest('.toggle-save');
        if (!btn) return;
        
        const sid = btn.dataset.id;
        const countSpan = btn.querySelector('.save-count');
        let currentCount = parseInt(countSpan.textContent) || 0;
        const isActive = btn.classList.contains('active');
        
        if (isActive) {
            btn.classList.remove('active');
            countSpan.textContent = Math.max(0, currentCount - 1);
        } else {
            btn.classList.add('active');
            countSpan.textContent = currentCount + 1;
        }

        fetch('{{ route('clips.save.toggle') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status_id: sid })
        }).catch(err => console.error(err));
    });

    // Share Button Click Delegation
    container.addEventListener('click', function(e) {
        const btn = e.target.closest('.share-reel');
        if (!btn) return;
        
        const url = btn.dataset.url;
        if (navigator.share) {
            navigator.share({
                title: 'MYADS Clips',
                url: url
            }).catch(console.error);
        } else {
            navigator.clipboard.writeText(url).then(() => {
                alert('{{ __('messages.link_copied') ?? 'Link copied to clipboard!' }}');
            });
        }
    });

    // Report Clip Button
    const reportBtn = document.getElementById('btn-report-clip');
    if (reportBtn) {
        reportBtn.addEventListener('click', function() {
            const activeItem = document.querySelector('.reel-item[data-id="' + container.dataset.activeStatusId + '"]');
            if (activeItem) {
                const tpId = activeItem.dataset.tpId;
                const sType = activeItem.dataset.sType;
                if (typeof reportPost === 'function') {
                    reportPost(tpId, sType, 'reportclips-modal-container');
                }
            }
        });
    }

    // Comments Sidebar Logic
    const sidebar = document.getElementById('clips-comments-sidebar');
    const closeBtn = document.getElementById('close-comments-sidebar');
    
    function loadClipsComments(tpId) {
        const placeholder = document.getElementById('clips-comments-placeholder');
        if (!placeholder) return;
        
        placeholder.className = 'post-comment-list-placeholder post-comment-list-' + tpId;
        placeholder.innerHTML = '<div style="text-align: center; padding: 40px 20px; color: var(--text-color-light);"><i class="fa-solid fa-spinner fa-spin" style="font-size: 24px; margin-bottom: 10px; display: block;"></i> Loading comments...</div>';
        
        if (typeof window.loadComments === 'function') {
            window.loadComments(tpId, 'forum');
        }
    }

    if (closeBtn && sidebar) {
        closeBtn.addEventListener('click', () => {
            sidebar.classList.remove('active');
        });
    }

    container.addEventListener('click', function(e) {
        const btn = e.target.closest('.open-comments');
        if (!btn || !sidebar) return;
        
        e.preventDefault();
        const tpId = btn.dataset.tpId;
        const isActive = sidebar.classList.contains('active');
        
        if (isActive && container.dataset.activeTpId == tpId) {
            sidebar.classList.remove('active');
        } else {
            sidebar.classList.add('active');
            container.dataset.activeTpId = tpId;
            loadClipsComments(tpId);
        }
    });

    // Keyboard Navigation: Up/Down arrows, Space, M, L
    window.addEventListener('keydown', function(e) {
        // Don't trigger shortcuts if user is typing in an input/textarea
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName)) return;

        const currentActiveId = container.dataset.activeStatusId;
        const currentItem = document.querySelector('.reel-item[data-id="' + currentActiveId + '"]');

        if (e.key === 'ArrowDown' || e.key === 'j') {
            e.preventDefault();
            if (currentItem && currentItem.nextElementSibling) {
                currentItem.nextElementSibling.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } else if (e.key === 'ArrowUp' || e.key === 'k') {
            e.preventDefault();
            if (currentItem && currentItem.previousElementSibling) {
                currentItem.previousElementSibling.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } else if (e.key === ' ' || e.code === 'Space') {
            e.preventDefault();
            if (currentItem) {
                const video = currentItem.querySelector('.reel-video');
                if (video) {
                    if (video.paused) {
                        video.play();
                        currentItem.classList.remove('is-paused');
                    } else {
                        video.pause();
                        currentItem.classList.add('is-paused');
                    }
                }
            }
        } else if (e.key === 'm' || e.key === 'M') {
            e.preventDefault();
            applyMuteState(!isGlobalMuted);
        } else if (e.key === 'l' || e.key === 'L') {
            e.preventDefault();
            if (currentItem) {
                triggerHeartPop(currentItem);
                toggleClipLike(currentItem.dataset.id, 'clips', currentItem);
            }
        }
    });

    // Upload Clip Modal Handlers
    const uploadBtn = document.getElementById('btn-open-clip-upload');
    const uploadModalBackdrop = document.getElementById('clip-upload-modal-backdrop');
    const closeUploadBtn = document.getElementById('close-clip-upload');
    const cancelUploadBtn = document.getElementById('cancel-clip-upload');
    const videoFileInput = document.getElementById('modal-clip-video-input');
    const videoPreviewWrap = document.getElementById('modal-video-preview-wrap');
    const videoPreviewEl = document.getElementById('modal-video-preview');
    const videoDurationEl = document.getElementById('modal-video-duration');
    const uploadForm = document.getElementById('clip-upload-form');
    const submitBtn = document.getElementById('submit-clip-btn');

    if (uploadBtn && uploadModalBackdrop) {
        uploadBtn.addEventListener('click', () => {
            uploadModalBackdrop.classList.add('is-open');
        });
        const closeModal = () => uploadModalBackdrop.classList.remove('is-open');
        if (closeUploadBtn) closeUploadBtn.addEventListener('click', closeModal);
        if (cancelUploadBtn) cancelUploadBtn.addEventListener('click', closeModal);
        uploadModalBackdrop.addEventListener('click', (e) => {
            if (e.target === uploadModalBackdrop) closeModal();
        });
    }

    if (videoFileInput && videoPreviewWrap && videoPreviewEl) {
        videoFileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const fileUrl = URL.createObjectURL(file);
                videoPreviewEl.src = fileUrl;
                videoPreviewWrap.style.display = 'block';

                videoPreviewEl.onloadedmetadata = function() {
                    const duration = Math.round(videoPreviewEl.duration);
                    const mins = Math.floor(duration / 60);
                    const secs = duration % 60;
                    if (videoDurationEl) {
                        videoDurationEl.textContent = `Duration: ${mins}:${secs < 10 ? '0' : ''}${secs}`;
                    }
                };
            } else {
                videoPreviewWrap.style.display = 'none';
            }
        });
    }

    if (uploadForm && submitBtn) {
        uploadForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            const normal = submitBtn.querySelector('.normal-text');
            const loading = submitBtn.querySelector('.loading-text');
            if (normal) normal.style.display = 'none';
            if (loading) loading.style.display = 'inline-block';
        });
    }
});
</script>
@endpush
