@extends('theme::layouts.master')

@section('content')
<style>
    /* Full Reels TikTok-style Container */
    .reels-wrapper {
        height: calc(100vh - 80px); /* Adjust based on your header height */
        background-color: #000 !important;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        max-width: 450px;
        margin: 0 auto;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    
    .reels-container {
        height: 100%;
        overflow-y: scroll;
        scroll-snap-type: y mandatory;
        scroll-behavior: smooth;
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
        background-color: #000 !important;
    }
    
    .reels-container::-webkit-scrollbar {
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
        margin-bottom: 10px;
        color: #fff;
        text-decoration: none;
    }

    .reel-user:hover {
        color: #fff;
    }

    .reel-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid #fff;
        object-fit: cover;
    }

    .reel-username {
        font-weight: 700;
        font-size: 16px;
    }

    .reel-caption {
        font-size: 14px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
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

    /* Floating Saved Reels Button */
    .reels-header-actions {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 20;
    }
    
    .btn-saved-reels {
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
    }
    .btn-saved-reels:hover {
        background: rgba(0,0,0,0.8);
        color: #fff;
    }
    .btn-saved-reels svg {
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

<div class="grid" style="padding-top: 20px;">
    <div class="reels-wrapper">
        <div class="reels-header-actions">
            <a href="{{ route('reels.saved') }}" class="btn-saved-reels">
                <svg viewBox="0 0 24 24"><path d="M17 3H7c-1.1 0-1.99.9-1.99 2L5 21l7-3 7 3V5c0-1.1-.9-2-2-2z"/></svg>
                {{ __('messages.saved') ?? 'Saved' }}
            </a>
        </div>
        
        <div class="reels-container" id="reels-container">
            @include('theme::reels.partials.reels_list', ['activities' => $activities])
        </div>
        
        <!-- Loading Indicator for Infinite Scroll -->
        <div id="reels-loading" style="display: none; position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); text-align: center; color: #fff; text-shadow: 0 1px 3px rgba(0,0,0,0.8);">
            <i class="fa-solid fa-spinner fa-spin"></i> Loading...
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('reels-container');
    const loadingEl = document.getElementById('reels-loading');
    let nextPageUrl = '{{ $activities->nextPageUrl() }}';
    let isLoading = false;

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
                        
                        // Toggle global mute state for all reels
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

    // Infinite Scroll Observer
    const sentinelObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && nextPageUrl && !isLoading) {
                loadMoreReels();
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

    function loadMoreReels() {
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

        fetch('{{ url('/api/reels/save') }}', {
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
                title: 'MYADS Reels',
                url: url
            }).catch(console.error);
        } else {
            navigator.clipboard.writeText(url).then(() => {
                alert('{{ __('messages.link_copied') ?? 'Link copied to clipboard!' }}');
            });
        }
    });
    
    // Comments Action (redirect to post page)
    container.addEventListener('click', function(e) {
        const btn = e.target.closest('.open-comments');
        if (!btn) return;
        
        const sid = btn.dataset.id;
        window.location.href = '{{ url('/status') }}/' + sid;
    });
});
</script>
@endsection
