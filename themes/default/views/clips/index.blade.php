@extends('theme::layouts.master')

@section('content')
<style>
    /* Full Clips TikTok-style Container */
    .clips-wrapper {
        height: 100%;
        background-color: #000 !important;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        width: 100%;
        max-width: 450px;
        flex-shrink: 0;
        margin: 0 auto;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    
    .clips-container {
        height: 100%;
        overflow-y: scroll;
        scroll-snap-type: y mandatory;
        scroll-behavior: smooth;
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
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
        pointer-events: none; /* Let clicks pass to video */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
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
        bottom: 20px;
        color: #fff;
        max-width: calc(100% - 80px);
        pointer-events: auto;
        text-shadow: 0 1px 3px rgba(0,0,0,0.8);
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

    /* Hexagonal Avatar in Clips - Override theme defaults for dark background */
    .reel-hex-avatar {
        width: 40px;
        height: 44px;
        flex-shrink: 0;
    }

    .reel-hex-avatar .user-avatar-progress-border .hexagon-border-40-44 canvas {
        opacity: 0.9;
    }

    .reel-username {
        font-weight: 700;
        font-size: 16px;
    }

    /* Reel Caption with truncation and "more" button */
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

    .reel-actions {
        position: absolute;
        right: 15px;
        bottom: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
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
        width: 35px;
        height: 35px;
        fill: #fff;
        margin-bottom: 5px;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));
        transition: fill 0.3s, transform 0.3s;
    }

    .reel-action-btn span {
        font-size: 13px;
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

    @keyframes heartBeat {
        0% { transform: scale(1); }
        50% { transform: scale(1.3); }
        100% { transform: scale(1); }
    }

    /* Floating Header Actions (Saved + Report) */
    .clips-header-actions {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 20;
        display: flex;
        gap: 8px;
        align-items: center;
    }
    
    .btn-saved-clips,
    .btn-report-clip {
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(5px);
        color: #fff;
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 20px;
        padding: 6px 15px;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: background 0.2s;
        cursor: pointer;
    }
    .btn-saved-clips:hover,
    .btn-report-clip:hover {
        background: rgba(0,0,0,0.8);
        color: #fff;
    }
    .btn-saved-clips svg,
    .btn-report-clip svg {
        width: 16px;
        height: 16px;
        fill: currentColor;
    }

    /* Mute and Progress */
    .reel-mute-toggle {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 100;
        width: 36px;
        height: 36px;
        background: rgba(0,0,0,0.5);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        backdrop-filter: blur(4px);
        pointer-events: auto; /* Enable clicks on mute button overlay child */
    }

    .reel-mute-toggle svg {
        pointer-events: none; /* Let clicks pass through to the parent div handler */
    }

    /* Comments Sidebar Layout */
    .clips-layout-container {
        display: flex;
        justify-content: center;
        align-items: stretch;
        gap: 20px;
        max-width: 900px;
        margin: 0 auto;
        height: calc(100vh - 80px);
        position: relative;
        width: 100%;
    }

    .clips-comments-sidebar {
        width: 420px;
        background: var(--bg-color, #fff);
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
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
        padding: 15px 20px;
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

    @media (max-width: 991px) {
        .clips-layout-container {
            flex-direction: column;
            align-items: center;
            height: calc(100vh - 70px);
        }

        .clips-comments-sidebar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            height: 60%;
            border-radius: 16px 16px 0 0;
            box-shadow: 0 -10px 30px rgba(0,0,0,0.3);
            z-index: 100;
        }
    }

    .reel-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: rgba(255, 255, 255, 0.3);
        z-index: 100;
    }

    .reel-progress-filled {
        height: 100%;
        width: 0%;
        background: #23d2e2;
        transition: width 0.1s linear;
    }
</style>

<div class="grid" style="padding-top: 0;">
    <div class="clips-layout-container">
        <div class="clips-wrapper">
            <div class="clips-header-actions">
                @auth
                <button class="btn-report-clip" id="btn-report-clip" title="{{ __('messages.report') ?? 'Report' }}">
                    <svg viewBox="0 0 24 24"><path d="M14.4 6L14 4H5v17h2v-7h5.6l.4 2h7V6z"/></svg>
                    {{ __('messages.report') ?? 'Report' }}
                </button>
                @endauth
                <a href="{{ route('clips.saved') }}" class="btn-saved-clips">
                    <svg viewBox="0 0 24 24"><path d="M17 3H7c-1.1 0-1.99.9-1.99 2L5 21l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
                    {{ __('messages.saved') ?? 'Saved' }}
                </a>
            </div>
            
            <div class="clips-container" id="clips-container">
                @include('theme::clips.partials.clips_list', ['activities' => $activities])
            </div>
            
            <!-- Loading Indicator for Infinite Scroll -->
            <div id="clips-loading" style="display: none; position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); text-align: center; color: #fff; text-shadow: 0 1px 3px rgba(0,0,0,0.8);">
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
        
        <!-- Centralized container for the report modal -->
        <div id="reportclips-modal-container" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1050; width: 90%; max-width: 400px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('clips-container');
    const loadingEl = document.getElementById('clips-loading');
    let nextPageUrl = '{{ $activities->nextPageUrl() }}';
    let isLoading = false;

    // Initialize hexagonal avatars for clips
    if (typeof initHexagons === 'function') {
        initHexagons(container);
    }
    if (typeof recolorBadgeHexagons === 'function') {
        recolorBadgeHexagons(container);
    }

    // Intersection Observer for playing/pausing videos
    const videoObserverOptions = {
        root: container,
        rootMargin: '0px',
        threshold: 0.6 // 60% of the video must be visible
    };

    const videoObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const video = entry.target.querySelector('.reel-video');
            const item = entry.target;
            
            if (!video) return;

            if (entry.isIntersecting) {
                video.play().then(() => {
                    item.classList.remove('is-paused');
                }).catch(e => {
                    // Autoplay prevented, show paused state
                    item.classList.add('is-paused');
                    console.log('Autoplay prevented:', e);
                });

                // Track active reel details!
                const activeTpId = item.dataset.tpId;
                container.dataset.activeTpId = activeTpId;
                container.dataset.activeStatusId = item.dataset.id;
                
                // Update URL hash to reflect the active clip
                const clipId = item.dataset.id;
                if (clipId && window.location.hash !== '#' + clipId) {
                    history.replaceState(null, '', '/clips#' + clipId);
                }

                // If comments sidebar is active, dynamically load comments for the newly active clip!
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

    function observeVideos() {
        document.querySelectorAll('.reel-item').forEach(item => {
            // Unobserve first to prevent duplicates
            videoObserver.unobserve(item);
            videoObserver.observe(item);
            
            const video = item.querySelector('.reel-video');
            if(video && !item.dataset.boundEvents) {
                item.dataset.boundEvents = 'true';
                
                // Add click to play/pause
                video.addEventListener('click', () => {
                    if (video.paused) {
                        video.play();
                        item.classList.remove('is-paused');
                    } else {
                        video.pause();
                        item.classList.add('is-paused');
                    }
                });

                // Progress Bar Update
                const progressBar = item.querySelector('.reel-progress-filled');
                video.addEventListener('timeupdate', () => {
                    if (video.duration) {
                        const percent = (video.currentTime / video.duration) * 100;
                        progressBar.style.width = percent + '%';
                    }
                });

                // Mute Toggle Logic
                const muteBtn = item.querySelector('.reel-mute-toggle');
                if (muteBtn) {
                    muteBtn.addEventListener('click', (e) => {
                        e.stopPropagation(); // Prevent play/pause toggle
                        
                        // Toggle global mute state for all clips
                        const isMuted = !video.muted;
                        
                        document.querySelectorAll('.reel-video').forEach(v => {
                            v.muted = isMuted;
                        });

                        document.querySelectorAll('.reel-item').forEach(el => {
                            const iconMute = el.querySelector('.icon-mute');
                            const iconUnmute = el.querySelector('.icon-unmute');
                            if (isMuted) {
                                iconMute.style.display = 'block';
                                iconUnmute.style.display = 'none';
                            } else {
                                iconMute.style.display = 'none';
                                iconUnmute.style.display = 'block';
                            }
                        });
                    });
                }
            }
        });
    }

    observeVideos();

    // Scroll to specific clip if URL has a hash (e.g. /clips#55)
    function scrollToHashClip() {
        const hash = window.location.hash;
        if (hash && hash.length > 1) {
            const clipId = hash.substring(1);
            const targetItem = document.querySelector('.reel-item[data-id="' + clipId + '"]');
            if (targetItem) {
                // Temporarily disable smooth scroll for instant jump
                container.style.scrollBehavior = 'auto';
                targetItem.scrollIntoView({ block: 'start' });
                // Restore smooth scroll after a brief delay
                setTimeout(() => {
                    container.style.scrollBehavior = 'smooth';
                }, 100);
                return true;
            }
        }
        return false;
    }

    // Try to scroll to hash clip on initial load
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
            // Observe the last item
            sentinelObserver.disconnect();
            sentinelObserver.observe(items[items.length - 1]);
        }
    }
    
    observeSentinel();

    function loadMoreClips() {
        if (!nextPageUrl) return;
        
        isLoading = true;
        loadingEl.style.display = 'block';

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

                // Initialize hexagonal avatars for newly loaded clips
                if (typeof initHexagons === 'function') {
                    initHexagons(container);
                }
                if (typeof recolorBadgeHexagons === 'function') {
                    recolorBadgeHexagons(container);
                }

                // After loading more clips, check if we need to scroll to hash target
                scrollToHashClip();
            } else {
                nextPageUrl = null;
            }
        })
        .catch(err => console.error(err))
        .finally(() => {
            isLoading = false;
            loadingEl.style.display = 'none';
        });
    }

    // Toggle Reaction Action
    container.addEventListener('click', function(e) {
        const btn = e.target.closest('.toggle-reaction');
        if (!btn) return;
        
        const sid = btn.dataset.id;
        const type = btn.dataset.type;
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
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                id: sid,
                type: type,
                reaction: 'like'
            })
        }).catch(err => console.error(err));
    });

    // Toggle Save Action
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
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status_id: sid
            })
        }).catch(err => console.error(err));
    });

    // Share Action
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

    // Report Button (top header)
    const reportBtn = document.getElementById('btn-report-clip');
    if (reportBtn) {
        reportBtn.addEventListener('click', function() {
            const activeItem = document.querySelector('.reel-item[data-id="' + container.dataset.activeStatusId + '"]');
            if (activeItem) {
                const tpId = activeItem.dataset.tpId;
                const sType = activeItem.dataset.sType;
                if (typeof reportPost === 'function') {
                    reportPost(tpId, sType, 'clips-modal-container');
                }
            }
        });
    }

    // Load comments helper
    function loadClipsComments(tpId) {
        const placeholder = document.getElementById('clips-comments-placeholder');
        if (!placeholder) return;
        
        placeholder.className = 'post-comment-list-placeholder post-comment-list-' + tpId;
        placeholder.innerHTML = '<div style="text-align: center; padding: 40px 20px; color: var(--text-color-light);"><i class="fa-solid fa-spinner fa-spin" style="font-size: 24px; margin-bottom: 10px; display: block;"></i> Loading comments...</div>';
        
        if (typeof window.loadComments === 'function') {
            window.loadComments(tpId, 'forum');
        }
    }

    // Toggle comments sidebar
    const sidebar = document.getElementById('clips-comments-sidebar');
    const closeBtn = document.getElementById('close-comments-sidebar');
    
    if (closeBtn && sidebar) {
        closeBtn.addEventListener('click', () => {
            sidebar.classList.remove('active');
        });
    }

    // Comments click delegation
    container.addEventListener('click', function(e) {
        const btn = e.target.closest('.open-comments');
        if (!btn) return;
        
        e.preventDefault();
        if (!sidebar) return;

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

    // Observe comment list changes to update the count badge
    // Uses MutationObserver since postComment/deleteComment don't return promises
    const commentCountObserver = new MutationObserver(() => {
        const activeTpId = container.dataset.activeTpId;
        if (!activeTpId) return;
        const item = document.querySelector(`.reel-item[data-tp-id="${activeTpId}"]`);
        if (!item) return;
        const placeholder = document.getElementById('clips-comments-placeholder');
        if (!placeholder) return;
        const commentElements = placeholder.querySelectorAll('[class*="coment"]');
        const countSpan = item.querySelector('.open-comments span');
        if (countSpan) {
            countSpan.textContent = commentElements.length;
        }
    });

    const commentsPlaceholder = document.getElementById('clips-comments-placeholder');
    if (commentsPlaceholder) {
        commentCountObserver.observe(commentsPlaceholder, { childList: true, subtree: true });
    }
});
</script>
@endpush
