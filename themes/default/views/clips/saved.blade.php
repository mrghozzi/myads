@extends('theme::layouts.master')

@section('content')
<style>
    .saved-clips-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        padding: 20px 0;
    }
    
    .saved-reel-card {
        background: var(--bg-color);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.2s;
        display: flex;
        flex-direction: column;
    }
    
    .saved-reel-card:hover {
        transform: translateY(-5px);
    }
    
    .saved-reel-link {
        position: relative;
        display: block;
        padding-top: 177%; /* 16:9 ratio approx */
        overflow: hidden;
        background: #000;
    }
    
    .saved-reel-video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .saved-reel-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 50%);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.2s;
    }
    
    .saved-reel-link:hover .saved-reel-overlay {
        opacity: 1;
    }
    
    .saved-reel-stats {
        position: absolute;
        bottom: 10px;
        left: 10px;
        display: flex;
        align-items: center;
        gap: 5px;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
    }
    
    .saved-reel-info {
        padding: 12px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .saved-reel-caption {
        font-size: 13px;
        color: var(--text-color);
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .saved-reel-author {
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: var(--text-color-light);
        font-size: 12px;
    }
    
    .saved-reel-author img {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        object-fit: cover;
    }

    .empty-state {
        text-align: center;
        padding: 50px 20px;
        background: var(--bg-color);
        border-radius: 12px;
        margin-top: 20px;
    }
</style>

<div class="grid">
    <div class="section-banner">
        <p class="section-banner-title">{{ __('messages.saved_clips') ?? 'Saved Clips' }}</p>
        <p class="section-banner-text text-center">{{ __('messages.saved_clips_desc') ?? 'Clips you have saved.' }}</p>
    </div>

    @if($activities->isEmpty())
        <div class="empty-state widget-box">
            <svg class="widget-box-icon" style="width: 64px; height: 64px; fill: #615dfa; margin-bottom: 20px;" viewBox="0 0 24 24">
                <path d="M17 3H7c-1.1 0-1.99.9-1.99 2L5 21l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
            </svg>
            <h2>{{ __('messages.no_saved_clips') ?? 'No Saved Clips' }}</h2>
            <p>{{ __('messages.no_saved_clips_desc') ?? 'You haven\'t saved any clips yet. Explore the community and save your favorites!' }}</p>
            <a href="{{ route('clips.index') }}" class="button primary" style="margin-top: 15px; display: inline-block;">{{ __('messages.explore_clips') ?? 'Explore Clips' }}</a>
        </div>
    @else
        <div class="saved-clips-container" id="saved-clips-container">
            @include('theme::clips.partials.clips_grid', ['activities' => $activities])
        </div>
        
        <div id="clips-loading" style="display: none; text-align: center; padding: 20px; color: var(--text-color);">
            <i class="fa-solid fa-spinner fa-spin"></i> Loading...
        </div>
    @endif
</div>
@endsection

@push('scripts')
@if($activities->isNotEmpty())
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('saved-clips-container');
    const loadingEl = document.getElementById('clips-loading');
    let nextPageUrl = '{{ $activities->nextPageUrl() }}';
    let isLoading = false;

    const sentinelObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && nextPageUrl && !isLoading) {
                loadMoreClips();
            }
        });
    }, {
        root: null,
        threshold: 0.1
    });

    function observeSentinel() {
        const items = document.querySelectorAll('.saved-reel-card');
        if (items.length > 0) {
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
});
</script>
@endif
@endpush
