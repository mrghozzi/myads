@extends('theme::layouts.master')

@section('content')
<div class="video-hub-container py-3">
    <!-- ADS placement -->
    @include('theme::partials.ads', ['id' => 5])

    <!-- 1. HERO & SEARCH BANNER (.superdesign) -->
    <div class="yt-hero-card rounded-4 p-4 p-md-5 mb-4 position-relative overflow-hidden shadow-sm">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="badge bg-primary text-white rounded-pill px-3 py-2 mb-3 fw-bold fs-7 shadow-xs d-inline-flex align-items-center gap-2">
                    <i class="fa-solid fa-play"></i> {{ __('messages.video_hub') }}
                </div>
                <h1 class="display-6 fw-extrabold mb-2 text-gradient-primary">
                    {{ __('messages.video_hub_title') }}
                </h1>
                <p class="text-muted fs-6 mb-4 max-w-lg">
                    {{ __('messages.video_hub_subtitle') }}
                </p>

                <!-- Search Bar -->
                <form action="{{ route('video.index') }}" method="GET" class="yt-search-form d-flex gap-2 max-w-lg">
                    <div class="input-group input-group-lg shadow-xs rounded-pill overflow-hidden border" style="border-color: rgba(97, 93, 250, 0.3) !important;">
                        <span class="input-group-text bg-surface border-0 ps-3 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="q" value="{{ $searchQuery }}" class="form-control bg-surface border-0 fs-6 shadow-none" placeholder="{{ __('messages.search_videos_placeholder') }}" autocomplete="off">
                        @if($searchQuery !== '')
                            <a href="{{ route('video.index') }}" class="input-group-text bg-surface border-0 text-muted" title="{{ __('messages.clear_filter') }}">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="fa-solid fa-arrow-left dir-rtl-icon"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Upload Quick Actions -->
            <div class="col-lg-5 text-lg-end">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                    @auth
                        <!-- Post Video button directing to /share as explicitly requested -->
                        <a href="{{ url('/share') }}" class="btn btn-post-video btn-lg rounded-pill px-4 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                            <i class="fa-solid fa-cloud-arrow-up"></i> {{ __('messages.create_video') }}
                        </a>
                        <a href="{{ route('clips.index') }}" class="btn btn-outline-danger btn-lg rounded-pill px-4 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                            <i class="fa-solid fa-bolt"></i> {{ __('messages.create_clip') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold">
                            <i class="fa-solid fa-right-to-bracket me-2"></i> {{ __('messages.login') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- 2. YOUTUBE CATEGORY FILTER CHIPS -->
    <div class="yt-filter-chips-wrap mb-4 overflow-x-auto pb-2 scrollbar-none">
        <div class="d-flex gap-2 align-items-center flex-nowrap">
            <a href="{{ route('video.index') }}" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $filter === 'all' ? 'btn-primary shadow-xs' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-border-all me-1"></i> {{ __('messages.all_videos') }}
            </a>
            <a href="{{ route('video.filter', 'clips') }}" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $filter === 'clips' ? 'btn-danger text-white shadow-xs' : 'btn-outline-danger' }}">
                <i class="fa-solid fa-bolt me-1"></i> {{ __('messages.shorts_clips') }}
            </a>
            <a href="{{ route('video.filter', 'videos') }}" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $filter === 'videos' ? 'btn-primary shadow-xs' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-video me-1"></i> {{ __('messages.only_videos') }}
            </a>
            <a href="{{ route('video.filter', 'trending') }}" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $filter === 'trending' ? 'btn-warning text-dark fw-bold shadow-xs' : 'btn-outline-warning' }}">
                <i class="fa-solid fa-fire me-1"></i> {{ __('messages.trending_videos') }}
            </a>
            <a href="{{ route('video.filter', 'latest') }}" class="btn btn-sm rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $filter === 'latest' ? 'btn-primary shadow-xs' : 'btn-outline-secondary' }}">
                <i class="fa-solid fa-clock me-1"></i> {{ __('messages.latest_videos') }}
            </a>
        </div>
    </div>

    <!-- 3. YOUTUBE SHORTS / CLIPS SHELF (مقاطع الـ Clips) -->
    @if($clips->isNotEmpty() && in_array($filter, ['all', 'clips'], true))
        <div class="yt-shorts-shelf-card rounded-4 p-4 mb-5 shadow-sm position-relative">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center gap-2">
                    <span class="yt-shorts-badge d-flex align-items-center justify-content-center rounded-3 px-3 py-2 text-white fw-bold shadow-xs">
                        <i class="fa-solid fa-bolt fs-5 me-1"></i> {{ __('messages.shorts_clips') }}
                    </span>
                    <span class="text-muted small ms-2 d-none d-sm-inline-block">9:16 Vertical Video Feed</span>
                </div>
                <a href="{{ route('clips.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
                    {{ __('messages.view_all_clips') }} <i class="fa-solid fa-arrow-left dir-rtl-icon ms-1"></i>
                </a>
            </div>

            <!-- Vertical Clips Carousel Grid -->
            <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6">
                @foreach($clips as $clip)
                    @php
                        $clipTopic = $clip->forumTopic;
                        $clipUser = $clip->user;
                        $clipUrl = route('clips.index') . '#' . $clip->id;
                        
                        // Resolved Thumbnail & Title & Video URL
                        $clipThumb = $clip->resolved_thumbnail ?: \App\Http\Controllers\VideoHubController::resolveVideoThumbnailUrl($clip);
                        $clipVideoUrl = $clip->resolved_video_url ?: \App\Http\Controllers\VideoHubController::resolveVideoUrl($clip);
                        $clipTitle = \Illuminate\Support\Str::limit($clip->resolved_title ?: \App\Http\Controllers\VideoHubController::resolveVideoTitle($clip), 40);
                        $clipViews = $clipTopic ? (int) ($clipTopic->vu ?? 0) : (int) ($clip->views_count ?? 0);
                        $hasClipCustomThumb = $clipThumb && !str_contains($clipThumb, 'video-placeholder');
                    @endphp

                    <div class="col">
                        <div class="yt-shorts-card rounded-4 position-relative overflow-hidden shadow-xs h-100 bg-dark" style="aspect-ratio: 9/16;">
                            <a href="{{ $clipUrl }}" class="d-block w-100 h-100 text-decoration-none">
                                @if($hasClipCustomThumb)
                                    <img src="{{ $clipThumb }}" alt="{{ $clipTitle }}" class="w-100 h-100 object-fit-cover opacity-90 lazyload" onError="this.onerror=null;this.src='{{ theme_asset('img/video-placeholder.svg') }}';">
                                @elseif($clipVideoUrl)
                                    <video src="{{ $clipVideoUrl }}#t=0.5" preload="metadata" muted playsinline class="w-100 h-100 object-fit-cover opacity-90 pointer-events-none"></video>
                                @else
                                    <img src="{{ theme_asset('img/video-placeholder.svg') }}" alt="{{ $clipTitle }}" class="w-100 h-100 object-fit-cover opacity-90">
                                @endif
                                
                                <!-- Dark Gradient Scrim -->
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-between p-3" style="background: linear-gradient(180deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.1) 40%, rgba(0,0,0,0.85) 100%);">
                                    <!-- Top Shorts Icon -->
                                    <div class="d-flex justify-content-end">
                                        <span class="badge bg-danger rounded-circle p-2 shadow-xs">
                                            <i class="fa-solid fa-bolt text-white"></i>
                                        </span>
                                    </div>

                                    <!-- Bottom Info -->
                                    <div class="yt-shorts-info text-white">
                                        @if($clipUser)
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <div class="user-avatar small no-outline flex-shrink-0" style="width: 24px; height: 26px;">
                                                    <div class="user-avatar-content">
                                                        <div class="hexagon-image-30-32" data-src="{{ $clipUser->img ? url($clipUser->img) : theme_asset('img/avatar.jpg') }}"></div>
                                                    </div>
                                                </div>
                                                <span class="small fw-semibold text-truncate text-white-50" style="max-width: 100px;">
                                                    {{ $clipUser->username }}
                                                </span>
                                            </div>
                                        @endif
                                        <p class="clip-title small fw-bold mb-1 line-clamp-2 text-white" style="line-height: 1.3;">
                                            {{ $clipTitle }}
                                        </p>
                                        <div class="small text-white-50 fs-8">
                                            <i class="fa-solid fa-eye me-1"></i> {{ number_format($clipViews) }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- 4. SPOTLIGHT HERO VIDEO BANNER (IF AVAILABLE) -->
    @if($spotlightVideo && $searchQuery === '')
        @php
            $spTopic = $spotlightVideo->forumTopic;
            $spUser = $spotlightVideo->user;
            $spWatchUrl = $spTopic ? route('forum.topic', $spTopic->id) : url('/portal');
            
            // Resolved Thumbnail & Title & Video URL
            $spThumb = $spotlightVideo->resolved_thumbnail ?: \App\Http\Controllers\VideoHubController::resolveVideoThumbnailUrl($spotlightVideo);
            $spVideoUrl = $spotlightVideo->resolved_video_url ?: \App\Http\Controllers\VideoHubController::resolveVideoUrl($spotlightVideo);
            $spTitle = $spotlightVideo->resolved_title ?: \App\Http\Controllers\VideoHubController::resolveVideoTitle($spotlightVideo);
            $spViews = $spTopic ? (int) ($spTopic->vu ?? 0) : (int) ($spotlightVideo->views_count ?? 0);
            $hasSpCustomThumb = $spThumb && !str_contains($spThumb, 'video-placeholder');
        @endphp

        <div class="yt-spotlight-card rounded-4 p-4 mb-5 shadow-sm">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <a href="{{ $spWatchUrl }}" class="d-block ratio ratio-16x9 rounded-4 overflow-hidden shadow-lg position-relative group">
                        @if($hasSpCustomThumb)
                            <img src="{{ $spThumb }}" alt="{{ $spTitle }}" class="w-100 h-100 object-fit-cover lazyload" onError="this.onerror=null;this.src='{{ theme_asset('img/video-placeholder.svg') }}';">
                        @elseif($spVideoUrl)
                            <video src="{{ $spVideoUrl }}#t=0.5" preload="metadata" muted playsinline class="w-100 h-100 object-fit-cover pointer-events-none"></video>
                        @else
                            <img src="{{ theme_asset('img/video-placeholder.svg') }}" alt="{{ $spTitle }}" class="w-100 h-100 object-fit-cover">
                        @endif
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-40 d-flex align-items-center justify-content-center">
                            <span class="btn btn-light rounded-circle p-4 shadow-lg text-primary scale-on-hover">
                                <i class="fa-solid fa-play fs-2 ms-1"></i>
                            </span>
                        </div>
                    </a>
                </div>

                <div class="col-lg-5">
                    <span class="badge bg-primary px-3 py-2 rounded-pill fw-bold mb-3 shadow-xs">
                        <i class="fa-solid fa-star me-1"></i> {{ __('messages.trending_videos') }}
                    </span>
                    <h2 class="h3 fw-bold mb-3">
                        <a href="{{ $spWatchUrl }}" class="text-reset text-decoration-none">
                            {{ $spTitle }}
                        </a>
                    </h2>

                    @if($spUser)
                        <div class="d-flex align-items-center gap-3 mb-4 p-2 rounded-3 bg-surface border">
                            <a href="{{ route('profile.short', $spUser->publicRouteIdentifier()) }}" class="user-avatar small no-outline flex-shrink-0" style="width: 38px; height: 42px;">
                                <div class="user-avatar-content">
                                    <div class="hexagon-image-30-32" data-src="{{ $spUser->img ? url($spUser->img) : theme_asset('img/avatar.jpg') }}"></div>
                                </div>
                                <div class="user-avatar-progress-border">
                                    <div class="hexagon-border-40-44" data-line-color="{{ $spUser->profileBadgeColor() }}"></div>
                                </div>
                            </a>
                            <div>
                                <h4 class="h6 mb-0 fw-bold">
                                    <a href="{{ route('profile.short', $spUser->publicRouteIdentifier()) }}" class="text-reset text-decoration-none">
                                        {{ $spUser->username }}
                                    </a>
                                </h4>
                                <span class="small text-muted">{{ number_format($spViews) }} {{ __('messages.views') }}</span>
                            </div>
                        </div>
                    @endif

                    <a href="{{ $spWatchUrl }}" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold">
                        <i class="fa-solid fa-play me-2"></i> {{ __('messages.watch_now') }}
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- 5. MAIN VIDEOS GRID -->
    <div class="yt-main-videos-section">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="h4 fw-extrabold mb-0 d-flex align-items-center gap-2">
                <i class="fa-solid fa-film text-primary"></i> {{ __('messages.main_videos') }}
            </h2>
            <span class="badge bg-primary text-white rounded-pill px-3 py-2 fw-bold shadow-xs">
                {{ number_format($videos->total()) }} {{ __('messages.videos') ?? 'Videos' }}
            </span>
        </div>

        <!-- Injected Partial Grid -->
        @include('theme::video.partials.video_grid', ['videos' => $videos])
    </div>
</div>

<style>
/* Modern YouTube Hub Superdesign Styling */
.text-gradient-primary {
    background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.yt-hero-card {
    background: linear-gradient(135deg, rgba(97, 93, 250, 0.16) 0%, rgba(35, 210, 226, 0.12) 100%), var(--notification-ui-card-bg);
    border: 1px solid var(--notification-ui-card-border);
    backdrop-filter: blur(12px);
}
.btn-post-video {
    background: linear-gradient(135deg, #615dfa 0%, #4b45e4 100%);
    color: #fff !important;
    border: none;
    box-shadow: 0 6px 20px rgba(97, 93, 250, 0.35);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.btn-post-video:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 26px rgba(97, 93, 250, 0.55);
}
.yt-shorts-shelf-card {
    background: linear-gradient(135deg, rgba(255, 0, 51, 0.05) 0%, rgba(255, 0, 51, 0.02) 100%), var(--notification-ui-card-bg);
    border: 1px solid rgba(255, 0, 51, 0.2) !important;
}
.yt-shorts-badge {
    background: linear-gradient(135deg, #ff0033 0%, #f03355 100%);
}
.yt-spotlight-card {
    background: linear-gradient(135deg, rgba(97, 93, 250, 0.08) 0%, rgba(15, 17, 26, 0.95) 100%), var(--notification-ui-card-bg);
    border: 1px solid var(--notification-ui-card-border);
}
.yt-thumb-stage {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
}
.yt-thumb-stage .yt-play-btn-circle {
    width: 48px;
    height: 48px;
    background: rgba(97, 93, 250, 0.9);
    backdrop-filter: blur(4px);
    transition: transform 0.25s ease, background 0.25s ease;
}
.yt-thumb-stage:hover .yt-play-btn-circle {
    transform: scale(1.15);
    background: #615dfa;
}
.yt-thumb-img {
    transition: transform 0.35s ease;
}
.yt-thumb-stage:hover .yt-thumb-img {
    transform: scale(1.06);
}
.yt-thumb-overlay {
    background: rgba(0,0,0,0.25);
    opacity: 0;
    transition: opacity 0.25s ease;
}
.yt-thumb-stage:hover .yt-thumb-overlay {
    opacity: 1;
}
.yt-video-card {
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
    border-radius: 18px;
    padding: 12px;
    background: var(--notification-ui-card-bg);
    border: 1px solid var(--notification-ui-card-border);
}
.yt-video-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 32px rgba(0,0,0,0.18), 0 0 20px rgba(97, 93, 250, 0.15) !important;
    border-color: rgba(97, 93, 250, 0.35);
}
.yt-shorts-card {
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
}
.yt-shorts-card:hover {
    transform: scale(1.04);
    box-shadow: 0 14px 30px rgba(255, 0, 51, 0.3);
    border-color: rgba(255, 0, 51, 0.5);
}
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.scrollbar-none::-webkit-scrollbar {
    display: none;
}
.scrollbar-none {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.dir-rtl-icon {
    transform: scaleX(1);
}
html[dir="rtl"] .dir-rtl-icon {
    transform: scaleX(-1);
}
</style>
@endsection
