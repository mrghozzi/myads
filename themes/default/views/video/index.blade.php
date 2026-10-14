@extends('theme::layouts.master')

@section('content')
<div class="video-hub-container py-3">
    <!-- ADS placement -->
    @include('theme::partials.ads', ['id' => 5])

    <!-- 1. HERO & SEARCH BANNER (World-Class Cinema Video Platform Design) -->
    <div class="yt-hero-card rounded-4 p-4 p-md-5 mb-4 position-relative overflow-hidden shadow-lg">
        <div class="yt-hero-ambient-glow position-absolute"></div>
        <div class="row align-items-center g-4 position-relative" style="z-index: 2;">
            <div class="col-lg-7">
                <div class="badge yt-hero-badge rounded-pill px-3 py-2 mb-3 fw-bold fs-7 shadow-xs d-inline-flex align-items-center gap-2">
                    <span class="pulse-dot"></span>
                    <i class="fa-solid fa-play"></i> {{ __('messages.video_hub') }}
                </div>
                <h1 class="display-6 fw-extrabold mb-2 yt-hero-heading">
                    {{ __('messages.video_hub_title') }}
                </h1>
                <p class="yt-hero-desc fs-6 mb-4 max-w-lg">
                    {{ __('messages.video_hub_subtitle') }}
                </p>

                <!-- Search Bar with Live Debounce and Clear Option -->
                <form action="{{ route('video.index') }}" method="GET" class="yt-search-form d-flex gap-2 max-w-lg" id="videoSearchForm">
                    @if(request('cat'))
                        <input type="hidden" name="cat" value="{{ request('cat') }}">
                    @endif
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden border yt-search-input-group">
                        <span class="input-group-text border-0 ps-3 yt-search-icon">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" name="q" id="videoSearchInput" value="{{ $searchQuery }}" class="form-control border-0 fs-6 shadow-none yt-search-input" placeholder="{{ __('messages.search_videos_placeholder') }}" autocomplete="off">
                        @if($searchQuery !== '')
                            <a href="{{ route('video.index') }}" class="input-group-text border-0 yt-search-clear" title="{{ __('messages.clear_filter') ?? 'Clear' }}">
                                <i class="fa-solid fa-xmark"></i>
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary px-4 fw-bold yt-search-btn" aria-label="Search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Upload Quick Actions -->
            <div class="col-lg-5 text-lg-end">
                <div class="d-flex flex-wrap gap-2 justify-content-lg-end yt-hero-actions">
                    @auth
                        <a href="{{ url('/share') }}" class="btn btn-post-video btn-lg rounded-pill px-4 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                            <i class="fa-solid fa-cloud-arrow-up"></i> {{ __('messages.create_video') }}
                        </a>
                        <a href="{{ route('clips.index') }}" class="btn btn-post-clip btn-lg rounded-pill px-4 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                            <i class="fa-solid fa-bolt"></i> {{ __('messages.create_clip') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow-sm">
                            <i class="fa-solid fa-right-to-bracket me-2"></i> {{ __('messages.login') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- 2. FILTER NAVIGATION STRIP (Pure Video Categories, NO Forum Categories) -->
    <div class="yt-filter-bar mb-4">
        <div class="d-flex align-items-center justify-content-between gap-3 flex-nowrap yt-filter-bar-inner">
            <!-- Scrollable Video Category Pills -->
            <div class="yt-filter-chips-wrap overflow-x-auto pb-1 scrollbar-none flex-grow-1">
                <div class="d-flex gap-2 align-items-center flex-nowrap" id="videoFilterPills">
                    <!-- All Videos -->
                    <a href="{{ route('video.index') }}" class="yt-chip btn btn-sm rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $filter === 'all' && !request('cat') ? 'active' : '' }}">
                        <i class="fa-solid fa-border-all me-1"></i> {{ __('messages.all_videos') }}
                    </a>

                    <!-- Shorts / Clips Filter -->
                    <a href="{{ route('video.filter', 'clips') }}" class="yt-chip chip-shorts btn btn-sm rounded-pill px-3 py-2 fw-semibold text-nowrap {{ in_array($filter, ['clips', 'shorts'], true) ? 'active' : '' }}">
                        <i class="fa-solid fa-bolt me-1"></i> {{ __('messages.shorts_clips') }}
                    </a>

                    <!-- Standard Videos Only -->
                    <a href="{{ route('video.filter', 'videos') }}" class="yt-chip btn btn-sm rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $filter === 'videos' ? 'active' : '' }}">
                        <i class="fa-solid fa-video me-1"></i> {{ __('messages.only_videos') }}
                    </a>

                    <!-- Trending -->
                    <a href="{{ route('video.filter', 'trending') }}" class="yt-chip chip-trending btn btn-sm rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $filter === 'trending' ? 'active' : '' }}">
                        <i class="fa-solid fa-fire me-1"></i> {{ __('messages.trending_videos') }}
                    </a>

                    <!-- Latest -->
                    <a href="{{ route('video.filter', 'latest') }}" class="yt-chip btn btn-sm rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $filter === 'latest' ? 'active' : '' }}">
                        <i class="fa-solid fa-clock me-1"></i> {{ __('messages.latest_videos') }}
                    </a>

                    <!-- Dedicated Video Genres (Technology, Gaming, Entertainment, Music, Tutorials, etc.) -->
                    @if(isset($categories) && $categories->isNotEmpty())
                        <div class="vr mx-1 opacity-25"></div>
                        @foreach($categories as $category)
                            @if(in_array($category->slug, ['all', 'trending', 'shorts'], true))
                                @continue
                            @endif
                            @php
                                $isCatActive = (string) request('cat') === (string) $category->slug 
                                    || (string) request('cat') === (string) $category->id 
                                    || (string) request('category') === (string) $category->slug;
                            @endphp
                            <a href="{{ route('video.index', ['cat' => $category->slug]) }}" class="yt-chip btn btn-sm rounded-pill px-3 py-2 fw-semibold text-nowrap {{ $isCatActive ? 'active' : '' }}">
                                @if(!empty($category->icon))
                                    <i class="{{ $category->icon }} me-1" @if($category->color) style="color: {{ $isCatActive ? '#fff' : $category->color }};" @endif></i>
                                @elseif(!empty($category->color))
                                    <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background-color: {{ $category->color }};"></span>
                                @endif
                                {{ $category->display_name ?? $category->name }}
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Sort By Dropdown -->
            <div class="dropdown flex-shrink-0 yt-sort-dropdown">
                <button class="btn btn-sm yt-sort-btn rounded-pill px-3 py-2 fw-semibold dropdown-toggle d-inline-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-arrow-down-short-wide"></i>
                    <span>{{ __('messages.sort_by') ?? 'Sort by' }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border rounded-3 fs-7 py-1">
                    <li>
                        <a class="dropdown-item py-2 {{ empty($sort) || $sort === 'latest' ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}">
                            <i class="fa-solid fa-clock me-2"></i> {{ __('messages.sort_newest') ?? 'Newest' }}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 {{ $sort === 'views' ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'views']) }}">
                            <i class="fa-solid fa-fire me-2"></i> {{ __('messages.sort_most_viewed') ?? 'Most Viewed' }}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2 {{ $sort === 'reactions' ? 'active fw-bold' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'reactions']) }}">
                            <i class="fa-solid fa-heart me-2 text-danger"></i> {{ __('messages.sort_top_rated') ?? 'Top Rated' }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- 3. YOUTUBE SHORTS / CLIPS SHELF (9:16 Vertical Video Carousel) -->
    @if($clips->isNotEmpty() && in_array($filter, ['all', 'clips', 'shorts'], true))
        <div class="yt-shorts-shelf-card rounded-4 p-4 mb-5 shadow-sm position-relative">
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="yt-shorts-badge d-flex align-items-center justify-content-center rounded-3 px-3 py-2 text-white fw-bold shadow-xs">
                        <i class="fa-solid fa-bolt fs-5 me-1"></i> {{ __('messages.shorts_clips') }}
                    </span>
                    <span class="text-muted small ms-2 d-none d-sm-inline-block">9:16 Vertical Video Feed</span>
                </div>
                <a href="{{ route('clips.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold shadow-xs">
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
                        
                        $clipThumb = $clip->resolved_thumbnail ?: \App\Http\Controllers\VideoHubController::resolveVideoThumbnailUrl($clip);
                        $clipVideoUrl = $clip->resolved_video_url ?: \App\Http\Controllers\VideoHubController::resolveVideoUrl($clip);
                        $clipTitle = \Illuminate\Support\Str::limit($clip->resolved_title ?: \App\Http\Controllers\VideoHubController::resolveVideoTitle($clip), 40);
                        $clipViews = $clipTopic ? (int) ($clipTopic->vu ?? 0) : (int) ($clip->views_count ?? 0);
                        $hasClipCustomThumb = $clipThumb && !str_contains($clipThumb, 'video-placeholder');
                    @endphp

                    <div class="col">
                        <div class="yt-shorts-card rounded-4 position-relative overflow-hidden shadow-xs h-100 bg-dark" style="aspect-ratio: 9/16;" @if($clipVideoUrl) data-video-url="{{ $clipVideoUrl }}" @endif>
                            <a href="{{ $clipUrl }}" class="d-block w-100 h-100 text-decoration-none">
                                @if($hasClipCustomThumb)
                                    <img src="{{ $clipThumb }}" alt="{{ $clipTitle }}" class="w-100 h-100 object-fit-cover opacity-90 lazyload" onError="this.onerror=null;this.src='{{ theme_asset('img/video-placeholder.svg') }}';">
                                @elseif($clipVideoUrl)
                                    <video src="{{ $clipVideoUrl }}#t=0.5" preload="metadata" muted playsinline class="w-100 h-100 object-fit-cover opacity-90 pointer-events-none"></video>
                                @else
                                    <img src="{{ theme_asset('img/video-placeholder.svg') }}" alt="{{ $clipTitle }}" class="w-100 h-100 object-fit-cover opacity-90">
                                @endif
                                
                                <!-- Dark Gradient Scrim -->
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-between p-3" style="background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.1) 40%, rgba(0,0,0,0.9) 100%);">
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
                                        <div class="small text-white-50 fs-8 d-flex align-items-center gap-1">
                                            <i class="fa-solid fa-eye"></i> {{ number_format($clipViews) }}
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

    <!-- 4. SPOTLIGHT HERO VIDEO BANNER (Featured Widescreen Marquee) -->
    @if($spotlightVideo && $searchQuery === '')
        @php
            $spTopic = $spotlightVideo->forumTopic;
            $spUser = $spotlightVideo->user;
            $spWatchUrl = route('video.watch', $spotlightVideo->id);
            
            $spThumb = $spotlightVideo->resolved_thumbnail ?: \App\Http\Controllers\VideoHubController::resolveVideoThumbnailUrl($spotlightVideo);
            $spVideoUrl = $spotlightVideo->resolved_video_url ?: \App\Http\Controllers\VideoHubController::resolveVideoUrl($spotlightVideo);
            $spTitle = $spotlightVideo->resolved_title ?: \App\Http\Controllers\VideoHubController::resolveVideoTitle($spotlightVideo);
            $spViews = $spTopic ? (int) ($spTopic->vu ?? 0) : (int) ($spotlightVideo->views_count ?? 0);
            $hasSpCustomThumb = $spThumb && !str_contains($spThumb, 'video-placeholder');
            $spYtId = \App\Http\Controllers\VideoHubController::extractYoutubeId($spVideoUrl);
        @endphp

        <div class="yt-spotlight-card rounded-4 p-4 p-md-5 mb-5 shadow-sm position-relative overflow-hidden">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <a href="{{ $spWatchUrl }}" class="d-block ratio ratio-16x9 rounded-4 overflow-hidden shadow-lg position-relative group yt-spotlight-thumb-wrap" @if($spVideoUrl) data-video-url="{{ $spVideoUrl }}" @endif @if($spYtId) data-yt-id="{{ $spYtId }}" @endif>
                        @if($hasSpCustomThumb)
                            <img src="{{ $spThumb }}" alt="{{ $spTitle }}" class="w-100 h-100 object-fit-cover lazyload" onError="this.onerror=null;this.src='{{ theme_asset('img/video-placeholder.svg') }}';">
                        @elseif($spVideoUrl)
                            <video src="{{ $spVideoUrl }}#t=0.5" preload="metadata" muted playsinline class="w-100 h-100 object-fit-cover pointer-events-none"></video>
                        @else
                            <img src="{{ theme_asset('img/video-placeholder.svg') }}" alt="{{ $spTitle }}" class="w-100 h-100 object-fit-cover">
                        @endif
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-40 d-flex align-items-center justify-content-center transition-all">
                            <span class="btn btn-primary rounded-circle p-4 shadow-lg scale-on-hover d-flex align-items-center justify-content-center" style="width: 72px; height: 72px;">
                                <i class="fa-solid fa-play fs-3 text-white ms-1"></i>
                            </span>
                        </div>
                    </a>
                </div>

                <div class="col-lg-5">
                    <span class="badge bg-primary px-3 py-2 rounded-pill fw-bold mb-3 shadow-xs d-inline-flex align-items-center gap-1">
                        <i class="fa-solid fa-star"></i> {{ __('messages.trending_videos') }}
                    </span>
                    <h2 class="h3 fw-extrabold mb-3 line-clamp-2">
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
                            <div class="flex-grow-1 min-w-0">
                                <h4 class="h6 mb-0 fw-bold text-truncate">
                                    <a href="{{ route('profile.short', $spUser->publicRouteIdentifier()) }}" class="text-reset text-decoration-none">
                                        {{ $spUser->username }}
                                    </a>
                                    @if($spUser->verified)
                                        <i class="fa-solid fa-circle-check text-primary fs-7 ms-1"></i>
                                    @endif
                                </h4>
                                <span class="small text-muted">{{ number_format($spViews) }} {{ __('messages.views') }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <a href="{{ $spWatchUrl }}" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow-sm d-inline-flex align-items-center gap-2">
                            <i class="fa-solid fa-play"></i> {{ __('messages.watch_now') }}
                        </a>
                        <button type="button" class="btn btn-outline-secondary btn-lg rounded-pill px-3 shadow-xs" onclick="navigator.clipboard.writeText('{{ $spWatchUrl }}'); if(typeof showToast === 'function') { showToast('{{ __('messages.link_copied') }}'); }">
                            <i class="fa-solid fa-share-nodes"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 5. MAIN VIDEOS GRID -->
    <div class="yt-main-videos-section">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <h2 class="h4 fw-extrabold mb-0 d-flex align-items-center gap-2">
                <i class="fa-solid fa-film text-primary"></i> {{ __('messages.main_videos') }}
            </h2>
            <span class="badge bg-primary text-white rounded-pill px-3 py-2 fw-bold shadow-xs">
                {{ number_format($videos->total()) }} {{ __('messages.videos') ?? 'Videos' }}
            </span>
        </div>

        <!-- Injected Partial Grid -->
        <div id="videoGridContainer">
            @include('theme::video.partials.video_grid', ['videos' => $videos])
        </div>
    </div>
</div>

<style>
/* Modern Cinema Video Platform Hub Superdesign Styling */
.text-gradient-primary {
    background: linear-gradient(135deg, #6366f1 0%, #06b6d4 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.yt-hero-card {
    background: linear-gradient(135deg, #0b0f19 0%, #111827 50%, #1e1b4b 100%) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 24px !important;
    color: #fff !important;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25) !important;
}
.yt-hero-heading {
    color: #ffffff !important;
    background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 40%, #60a5fa 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: -0.02em;
}
.yt-hero-desc {
    color: rgba(255, 255, 255, 0.75) !important;
}
.yt-hero-badge {
    background: rgba(99, 102, 241, 0.22) !important;
    border: 1px solid rgba(99, 102, 241, 0.45) !important;
    color: #a5b4fc !important;
}
.yt-hero-ambient-glow {
    top: -50px;
    inset-inline-end: -50px;
    width: 380px;
    height: 380px;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.4) 0%, rgba(6, 182, 212, 0.15) 70%, transparent 100%);
    filter: blur(55px);
    pointer-events: none;
    z-index: 1;
}
.pulse-dot {
    width: 8px;
    height: 8px;
    background: #00ff88;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 10px #00ff88;
    animation: pulseAnimation 2s infinite ease-in-out;
}
@keyframes pulseAnimation {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(0.85); }
}
.yt-search-input-group {
    background: rgba(255, 255, 255, 0.08) !important;
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
    backdrop-filter: blur(16px) !important;
    transition: all 0.25s ease !important;
}
.yt-search-input-group:focus-within {
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.35) !important;
}
.yt-search-icon {
    background: transparent !important;
    color: rgba(255, 255, 255, 0.6) !important;
}
.yt-search-input {
    background: transparent !important;
    color: #fff !important;
}
.yt-search-input::placeholder {
    color: rgba(255, 255, 255, 0.45) !important;
}
.yt-search-clear {
    background: transparent !important;
    color: rgba(255, 255, 255, 0.6) !important;
}
.yt-search-btn {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
    border: none !important;
    color: #fff !important;
    transition: all 0.2s ease !important;
}
.yt-search-btn:hover {
    background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%) !important;
    transform: scale(1.05);
}
.btn-post-video {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
    color: #fff !important;
    border: none !important;
    box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4) !important;
    transition: all 0.25s ease !important;
}
.btn-post-video:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 12px 30px rgba(99, 102, 241, 0.6) !important;
    color: #fff !important;
}
.btn-post-clip {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(244, 63, 94, 0.25) 100%) !important;
    border: 1px solid rgba(239, 68, 68, 0.5) !important;
    color: #fca5a5 !important;
    box-shadow: 0 8px 24px rgba(239, 68, 68, 0.25) !important;
    transition: all 0.25s ease !important;
}
.btn-post-clip:hover {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
    border-color: #ef4444 !important;
    color: #fff !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 12px 30px rgba(239, 68, 68, 0.5) !important;
}

/* Category Filter Bar & Chips */
.yt-filter-bar-inner {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 12px !important;
    flex-wrap: nowrap !important;
}
.yt-filter-chips-wrap {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    overflow-x: auto !important;
    white-space: nowrap !important;
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
    -webkit-overflow-scrolling: touch !important;
    padding-bottom: 2px !important;
}
.yt-filter-chips-wrap::-webkit-scrollbar {
    display: none !important;
}
.yt-sort-dropdown {
    flex: 0 0 auto !important;
}
.yt-chip {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    height: 38px !important;
    padding: 0 18px !important;
    border-radius: 9999px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    background: rgba(0, 0, 0, 0.05) !important;
    color: inherit !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    text-decoration: none !important;
    white-space: nowrap !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    cursor: pointer !important;
}
[data-theme="css_d"] .yt-chip {
    background: rgba(255, 255, 255, 0.06) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    color: #e2e8f0 !important;
}
.yt-chip:hover {
    background: rgba(99, 102, 241, 0.12) !important;
    border-color: rgba(99, 102, 241, 0.35) !important;
    color: #6366f1 !important;
    transform: translateY(-1px) !important;
}
.yt-chip.active {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
    border-color: #6366f1 !important;
    color: #fff !important;
    box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35) !important;
}
.yt-chip.chip-shorts.active {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
    border-color: #ef4444 !important;
    color: #fff !important;
    box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35) !important;
}
.yt-chip.chip-trending.active {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    border-color: #f59e0b !important;
    color: #fff !important;
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35) !important;
}
.yt-sort-btn {
    height: 38px !important;
    padding: 0 18px !important;
    border-radius: 9999px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    background: rgba(0, 0, 0, 0.05) !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    color: inherit !important;
    white-space: nowrap !important;
    transition: all 0.2s ease !important;
}
[data-theme="css_d"] .yt-sort-btn {
    background: rgba(255, 255, 255, 0.06) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    color: #e2e8f0 !important;
}
.yt-sort-btn:hover {
    background: rgba(99, 102, 241, 0.12) !important;
    border-color: rgba(99, 102, 241, 0.35) !important;
    color: #6366f1 !important;
}

.yt-shorts-shelf-card {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.05) 0%, rgba(239, 68, 68, 0.02) 100%), var(--notification-ui-card-bg);
    border: 1px solid rgba(239, 68, 68, 0.2) !important;
}
.yt-shorts-badge {
    background: linear-gradient(135deg, #ef4444 0%, #f43f5e 100%);
}
.yt-spotlight-card {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(15, 17, 26, 0.95) 100%), var(--notification-ui-card-bg);
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
    background: rgba(99, 102, 241, 0.9);
    backdrop-filter: blur(4px);
    transition: transform 0.25s ease, background 0.25s ease;
}
.yt-thumb-stage:hover .yt-play-btn-circle {
    transform: scale(1.15);
    background: #6366f1;
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
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease, border-color 0.3s ease;
    border-radius: 18px;
    padding: 12px;
    background: var(--notification-ui-card-bg);
    border: 1px solid var(--notification-ui-card-border);
}
.yt-video-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 32px rgba(0,0,0,0.18), 0 0 20px rgba(99, 102, 241, 0.15) !important;
    border-color: rgba(99, 102, 241, 0.45);
}
.yt-shorts-card {
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease, border-color 0.3s ease;
}
.yt-shorts-card:hover {
    transform: scale(1.04);
    box-shadow: 0 14px 30px rgba(239, 68, 68, 0.35);
    border: 1px solid rgba(239, 68, 68, 0.6);
}
.text-truncate-2, .line-clamp-2 {
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
.scale-on-hover {
    transition: transform 0.25s ease;
}
.scale-on-hover:hover {
    transform: scale(1.1);
}
</style>

<script>
/**
 * Modern High-Performance Video Hover Preview Engine
 */
function initVideoHoverPreviews() {
    const selector = '.yt-video-card, .yt-thumb-wrapper, .yt-shorts-card, .profile-yt-video-card, .profile-yt-shorts-card, .yt-spotlight-thumb-wrap';
    const cards = document.querySelectorAll(selector);
    
    cards.forEach(card => {
        if (card.dataset.hoverPreviewBound === 'true') return;
        card.dataset.hoverPreviewBound = 'true';

        const stage = card.querySelector('.yt-thumb-wrapper') || card.querySelector('.ratio') || card;
        const imgEl = stage.querySelector('img');
        const overlayEl = stage.querySelector('.yt-thumb-overlay');
        let videoEl = stage.querySelector('video');
        const ytId = stage.dataset.ytId || card.dataset.ytId;
        const rawUrl = stage.dataset.videoUrl || card.dataset.videoUrl || (videoEl ? videoEl.getAttribute('src') : null);

        if (!rawUrl && !videoEl && !ytId) return;

        let hoverTimer = null;
        let iframeEl = null;

        card.addEventListener('mouseenter', function() {
            hoverTimer = setTimeout(() => {
                if (ytId) {
                    if (!stage.querySelector('.yt-hover-iframe')) {
                        iframeEl = document.createElement('iframe');
                        iframeEl.src = `https://www.youtube.com/embed/${ytId}?autoplay=1&mute=1&controls=0&modestbranding=1&loop=1&playlist=${ytId}&enablejsapi=1`;
                        iframeEl.className = 'yt-hover-iframe position-absolute top-0 start-0 w-100 h-100 border-0 pointer-events-none';
                        iframeEl.style.zIndex = '4';
                        iframeEl.style.opacity = '0';
                        iframeEl.style.transition = 'opacity 0.25s ease';
                        iframeEl.onload = function() {
                            if (imgEl) imgEl.style.opacity = '0';
                            if (overlayEl) overlayEl.style.opacity = '0';
                            iframeEl.style.opacity = '1';
                        };
                        stage.appendChild(iframeEl);
                    }
                } else {
                    if (!videoEl && rawUrl) {
                        const cleanUrl = rawUrl.split('#')[0];
                        videoEl = document.createElement('video');
                        videoEl.src = cleanUrl + '#t=0.1';
                        videoEl.muted = true;
                        videoEl.loop = true;
                        videoEl.playsInline = true;
                        videoEl.setAttribute('muted', '');
                        videoEl.setAttribute('playsinline', '');
                        videoEl.className = 'yt-preview-video position-absolute top-0 start-0 w-100 h-100 object-fit-cover pointer-events-none';
                        videoEl.style.zIndex = '4';
                        videoEl.style.opacity = '0';
                        videoEl.style.transition = 'opacity 0.25s ease';
                        stage.appendChild(videoEl);
                    }

                    if (videoEl) {
                        videoEl.muted = true;
                        try { videoEl.currentTime = 0.1; } catch(e) {}
                        const playPromise = videoEl.play();
                        if (playPromise !== undefined) {
                            playPromise.then(() => {
                                if (imgEl) imgEl.style.opacity = '0';
                                if (overlayEl) overlayEl.style.opacity = '0';
                                videoEl.style.opacity = '1';
                            }).catch(() => {});
                        }
                    }
                }
            }, 180);
        });

        card.addEventListener('mouseleave', function() {
            if (hoverTimer) {
                clearTimeout(hoverTimer);
                hoverTimer = null;
            }

            if (imgEl) imgEl.style.opacity = '1';
            if (overlayEl) overlayEl.style.opacity = '';

            if (ytId) {
                const iframe = stage.querySelector('.yt-hover-iframe');
                if (iframe) iframe.remove();
            } else if (videoEl) {
                videoEl.pause();
                try { videoEl.currentTime = 0.5; } catch(e) {}
                videoEl.style.opacity = '0';
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initVideoHoverPreviews();
});
</script>
@endsection
