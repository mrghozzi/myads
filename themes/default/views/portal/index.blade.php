@extends('theme::layouts.master')

@push('head')
<style>
    /* Superdesign Scoped Variables */
    .portal-page {
        --portal-card-bg: #ffffff;
        --portal-text: #3e3f5e;
        --portal-muted: #8f91ac;
        --portal-border: #eaeaf5;
        --portal-accent: #615dfa;
        --portal-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        --portal-hover: #fcfcfd;
    }

    body.dark-mode .portal-page,
    [data-theme="css_d"] .portal-page {
        --portal-card-bg: #1d2333;
        --portal-text: #ffffff;
        --portal-muted: #9aa4bf;
        --portal-border: #2f3749;
        --portal-accent: #7750f8;
        --portal-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        --portal-hover: #22293d;
    }

    /* Modern Portal Banner */
    .modern-portal-banner {
        background: linear-gradient(135deg, var(--portal-accent) 0%, #23d2e2 100%);
        border-radius: 16px;
        padding: 32px 40px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 24px;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(97, 93, 250, 0.2);
    }
    
    .modern-portal-banner::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background: url('{{ theme_asset("img/banner/Newsfeed.png") }}') no-repeat right center;
        opacity: 0.15;
        pointer-events: none;
    }

    .modern-portal-banner-icon {
        width: 64px;
        height: 64px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .modern-portal-banner-icon img {
        width: 32px;
        height: auto;
    }

    .modern-portal-banner-content {
        position: relative;
        z-index: 2;
    }

    .modern-portal-banner-title {
        font-size: 1.8rem;
        font-weight: 800;
        margin: 0 0 4px 0;
        line-height: 1.2;
    }

    .modern-portal-banner-text {
        font-size: 1rem;
        font-weight: 500;
        margin: 0;
        opacity: 0.9;
    }

    /* Tabs Enhancement */
    .portal-page .simple-tab-items {
        display: flex;
        gap: 8px;
        border-bottom: 1px solid var(--portal-border);
        padding-bottom: 0;
        margin: 0 0 24px 0;
        overflow-x: auto;
        scrollbar-width: none;
        align-items: center;
    }

    .portal-page .simple-tab-items::-webkit-scrollbar {
        display: none;
    }

    .portal-page .simple-tab-item {
        height: auto;
        padding: 12px 24px;
        color: var(--portal-muted);
        font-size: 0.94rem;
        font-weight: 800;
        opacity: 0.8;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        border-bottom: 4px solid transparent;
        margin-right: 0 !important;
        white-space: nowrap;
        border-radius: 12px 12px 0 0;
    }

    .portal-page .simple-tab-item:hover {
        color: var(--portal-accent);
        opacity: 1;
        background: rgba(97, 93, 250, 0.06);
    }

    .portal-page .simple-tab-item.active {
        color: var(--portal-text);
        opacity: 1;
        background: linear-gradient(180deg, rgba(97, 93, 250, 0.04) 0%, rgba(97, 93, 250, 0.01) 100%);
        border-bottom-color: var(--portal-accent);
    }

    .portal-page .simple-tab-item.active::after {
        content: "";
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--portal-accent), #23d2e2);
        border-radius: 4px 4px 0 0;
        box-shadow: 0 -2px 10px rgba(97, 93, 250, 0.25);
    }

    html[dir="rtl"] .portal-page .simple-tab-item {
        margin-left: 0 !important;
    }

    /* Search Section Titles */
    .search-section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--portal-text);
        margin: 0 0 16px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .search-section-title::before {
        content: '';
        display: block;
        width: 4px;
        height: 20px;
        background: var(--portal-accent);
        border-radius: 4px;
    }

    /* Modern Search Grids */
    .modern-search-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }

    .modern-search-grid.large {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }

    /* User/Group Cards */
    .modern-user-card {
        background: var(--portal-card-bg);
        border: 1px solid var(--portal-border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--portal-shadow);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        flex-direction: column;
    }

    .modern-user-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        border-color: rgba(97, 93, 250, 0.3);
    }

    .modern-user-cover {
        height: 80px;
        background-size: cover !important;
        background-position: center !important;
        position: relative;
    }

    .modern-user-info {
        padding: 0 16px 16px;
        text-align: center;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .modern-user-avatar {
        margin-top: -30px;
        margin-bottom: 12px;
        position: relative;
        z-index: 2;
        width: 74px;
        height: 80px;
    }

    .modern-user-name {
        font-size: 1rem;
        font-weight: 700;
        color: var(--portal-text);
        margin: 0 0 4px;
        text-decoration: none !important;
    }

    .modern-user-name:hover {
        color: var(--portal-accent);
    }

    .modern-user-meta {
        font-size: 0.85rem;
        color: var(--portal-muted);
        margin: 0;
    }

    /* Product Grid (Reuse from Store) */
    .modern-product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }

    .modern-product-card {
        background: var(--portal-card-bg);
        border: 1px solid var(--portal-border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--portal-shadow);
        transition: all 0.25s ease;
    }
    
    .modern-product-card:hover {
        transform: translateY(-4px);
        border-color: rgba(97, 93, 250, 0.3);
        box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }

    .modern-product-img {
        width: 100%;
        aspect-ratio: 16/9;
        background-size: cover;
        background-position: center;
        border-bottom: 1px solid var(--portal-border);
    }

    .modern-product-body {
        padding: 16px;
    }

    .modern-product-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--portal-text);
        margin: 0 0 8px;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .modern-product-title:hover {
        color: var(--portal-accent);
    }

    .modern-product-desc {
        font-size: 0.85rem;
        color: var(--portal-muted);
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Comments Section */
    .modern-comments-container {
        background: var(--portal-card-bg);
        border: 1px solid var(--portal-border);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 32px;
        box-shadow: var(--portal-shadow);
    }

    .modern-comment-item {
        padding: 16px 0;
        border-bottom: 1px dashed var(--portal-border);
        display: flex;
        gap: 16px;
    }

    .modern-comment-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .modern-comment-item:first-child {
        padding-top: 0;
    }

    .modern-comment-content {
        flex-grow: 1;
    }

    .modern-comment-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 6px;
    }

    .modern-comment-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--portal-text);
        margin: 0;
    }

    .modern-comment-meta {
        font-size: 0.8rem;
        color: var(--portal-muted);
        font-weight: 500;
    }

    .modern-comment-text {
        font-size: 0.9rem;
        color: var(--portal-text);
        line-height: 1.5;
        margin: 0;
    }

    .modern-empty-state {
        text-align: center;
        padding: 48px 24px;
        background: var(--portal-card-bg);
        border: 1px dashed var(--portal-border);
        border-radius: 16px;
        color: var(--portal-muted);
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="portal-page">
    <!-- MODERN BANNER -->
    <div class="modern-portal-banner">
        <div class="modern-portal-banner-icon">
            <img src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="Newsfeed">
        </div>
        <div class="modern-portal-banner-content">
            <h1 class="modern-portal-banner-title">{{ __('messages.community') }}</h1>
            <p class="modern-portal-banner-text">{{ __('messages.latest_updates') }}</p>
        </div>
    </div>

    <div class="grid grid-3-6-3 mobile-prefer-content">
        <!-- LEFT SIDEBAR -->
        <div class="grid-column">
            <x-widget-column side="portal_left" />
        </div>

        <!-- MAIN FEED -->
        <div class="grid-column">
            @if(!empty($search))
                <div style="margin-bottom: 24px; padding: 16px 24px; background: var(--portal-card-bg); border-radius: 12px; border: 1px solid var(--portal-border);">
                    <h2 style="margin:0; font-size: 1.1rem; color: var(--portal-text);">
                        <i class="fas fa-search" style="color: var(--portal-accent); margin-right: 8px;"></i>
                        {{ __('messages.search') }}: <span style="font-weight: 400;">"{{ $search }}"</span>
                    </h2>
                </div>

                <!-- Activities Search Results -->
                @if(isset($searchedStatuses) && $searchedStatuses->count() > 0)
                    <h3 class="search-section-title">{{ __('messages.activities') }}</h3>
                    <div style="display: grid; grid-gap: 16px; margin-bottom: 32px;">
                        @foreach($searchedStatuses as $activity)
                            @include('theme::partials.activity.render', ['activity' => $activity])
                        @endforeach
                    </div>
                @endif

                <!-- Users Search Results -->
                @if(isset($searchedUsers) && $searchedUsers->count() > 0)
                    <h3 class="search-section-title">{{ __('messages.members') }}</h3>
                    <div class="modern-search-grid">
                        @foreach($searchedUsers as $sUser)
                            <div class="modern-user-card">
                                <div class="modern-user-cover" style="background: url({{ asset('themes/default/assets/img/cover/01.jpg') }});"></div>
                                <div class="modern-user-info">
                                    <div class="modern-user-avatar">
                                        <a href="{{ route('profile.show', $sUser->username) }}" class="user-avatar {{ $sUser->isOnline() ? 'online' : 'offline' }}">
                                            <div class="user-avatar-border"><div class="hexagon-100-110"></div></div>
                                            <div class="user-avatar-content"><div class="hexagon-image-68-74" data-src="{{ $sUser->avatarUrl() }}"></div></div>
                                            <div class="user-avatar-progress-border"><div class="hexagon-border-84-92"></div></div>
                                        </a>
                                    </div>
                                    <a href="{{ route('profile.show', $sUser->username) }}" class="modern-user-name">{{ $sUser->username }}</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Groups Search Results -->
                @if(isset($searchedGroups) && $searchedGroups->count() > 0)
                    <h3 class="search-section-title">{{ __('messages.groups_title') }}</h3>
                    <div class="modern-search-grid">
                        @foreach($searchedGroups as $sGroup)
                            <div class="modern-user-card">
                                <div class="modern-user-cover" style="background: url({{ $sGroup->coverUrl() }});"></div>
                                <div class="modern-user-info">
                                    <div class="modern-user-avatar">
                                        <a href="{{ route('groups.show', $sGroup) }}" class="user-avatar">
                                            <div class="user-avatar-border"><div class="hexagon-100-110"></div></div>
                                            <div class="user-avatar-content"><div class="hexagon-image-68-74" data-src="{{ $sGroup->avatarUrl() }}"></div></div>
                                            <div class="user-avatar-progress-border"><div class="hexagon-border-84-92"></div></div>
                                        </a>
                                    </div>
                                    <a href="{{ route('groups.show', $sGroup) }}" class="modern-user-name">{{ $sGroup->name }}</a>
                                    <p class="modern-user-meta">{{ $sGroup->members_count }} {{ __('messages.members') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Products Search Results -->
                @if(isset($searchedProducts) && $searchedProducts->count() > 0)
                    <h3 class="search-section-title">{{ __('messages.products') }}</h3>
                    <div class="modern-product-grid">
                        @foreach($searchedProducts as $product)
                            <div class="modern-product-card">
                                <a href="{{ route('store.show', $product->name) }}">
                                    <div class="modern-product-img" style="background-image: url({{ $product->product_image ?? theme_asset('img/error_plug.png') }});"></div>
                                </a>
                                <div class="modern-product-body">
                                    <a href="{{ route('store.show', $product->name) }}" class="modern-product-title">{{ $product->name }}</a>
                                    <p class="modern-product-desc">{{ \Illuminate\Support\Str::limit($product->o_valuer, 60) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Comments Search Results -->
                @if((isset($searchedCommentsForum) && $searchedCommentsForum->count() > 0) || (isset($searchedCommentsDir) && $searchedCommentsDir->count() > 0))
                    <h3 class="search-section-title">{{ __('messages.comments') }}</h3>
                    <div class="modern-comments-container">
                        <!-- Forum Comments -->
                        @if(isset($searchedCommentsForum))
                            @foreach($searchedCommentsForum as $fComment)
                                <div class="modern-comment-item">
                                    <div class="user-avatar small no-outline">
                                        <div class="user-avatar-content">
                                            <div class="hexagon-image-30-32" data-src="{{ $fComment->user ? $fComment->user->avatarUrl() : asset('upload/_avatar.png') }}"></div>
                                        </div>
                                    </div>
                                    <div class="modern-comment-content">
                                        <div class="modern-comment-header">
                                            <h4 class="modern-comment-title">{{ $fComment->user->username ?? 'Unknown' }} <span style="font-weight: 400; color: var(--portal-muted);">{{ __('messages.on_forum_topic') ?? 'on Forum Topic' }} #{{ $fComment->tid }}</span></h4>
                                            <span class="modern-comment-meta">{{ \Carbon\Carbon::createFromTimestamp($fComment->date)->diffForHumans() }}</span>
                                        </div>
                                        <p class="modern-comment-text">{{ \Illuminate\Support\Str::limit(strip_tags($fComment->txt), 100) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <!-- Directory Comments -->
                        @if(isset($searchedCommentsDir))
                            @foreach($searchedCommentsDir as $dComment)
                                <div class="modern-comment-item">
                                    <div class="user-avatar small no-outline">
                                        <div class="user-avatar-content">
                                            <div class="hexagon-image-30-32" data-src="{{ asset('upload/_avatar.png') }}"></div>
                                        </div>
                                    </div>
                                    <div class="modern-comment-content">
                                        <div class="modern-comment-header">
                                            <h4 class="modern-comment-title">{{ __('messages.directory_comment') ?? 'Directory Comment' }} <span style="font-weight: 400; color: var(--portal-muted);">{{ __('messages.on_directory') ?? 'on Directory' }} #{{ $dComment->o_parent }}</span></h4>
                                        </div>
                                        <p class="modern-comment-text">{{ \Illuminate\Support\Str::limit(strip_tags($dComment->o_valuer), 100) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endif

                @if(
                    (!isset($searchedUsers) || $searchedUsers->count() == 0) &&
                    (!isset($searchedStatuses) || $searchedStatuses->count() == 0) &&
                    (!isset($searchedGroups) || $searchedGroups->count() == 0) &&
                    (!isset($searchedCommentsForum) || $searchedCommentsForum->count() == 0) &&
                    (!isset($searchedCommentsDir) || $searchedCommentsDir->count() == 0) &&
                    (!isset($searchedProducts) || $searchedProducts->count() == 0)
                )
                    <div class="modern-empty-state">
                        <i class="fas fa-search" style="font-size: 3rem; color: var(--portal-border); margin-bottom: 16px; display: block;"></i>
                        {{ __('messages.no_results_found') ?? 'No results found.' }}
                    </div>
                @endif

            @else
                @include('theme::partials.status.add_post')
                
                <!-- TABS -->
                @auth
                <div class="simple-tab-items">
                    <a href="{{ route('portal.index', ['filter' => 'all']) }}" class="simple-tab-item {{ $filter == 'all' ? 'active' : '' }}">{{ __('messages.all_updates') }}</a>
                    <a href="{{ route('portal.index', ['filter' => 'me']) }}" class="simple-tab-item {{ $filter == 'me' ? 'active' : '' }}">{{ __('messages.following') }}</a>
                    @if(\App\Support\GroupSettings::isEnabled())
                    <a href="{{ route('portal.index', ['filter' => 'groups']) }}" class="simple-tab-item {{ $filter == 'groups' ? 'active' : '' }}">{{ __('messages.groups_title') }}</a>
                    @endif
                </div>
                @endauth

                <!-- ACTIVITY LIST -->
                <div id="infinite-scroll-container" style="display: grid; grid-gap: 16px;">
                    @foreach($activities as $activity)
                        @include('theme::partials.activity.render', ['activity' => $activity])
                    @endforeach
                    
                    @include('theme::partials.ajax.infinite_scroll', ['paginator' => $activities->appends(['filter' => $filter])])
                </div>
            @endif
        </div>

        <!-- RIGHT SIDEBAR -->
        <div class="grid-column">
            <x-widget-column side="portal_right" />
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const renderer = new marked.Renderer();
        renderer.image = function(href, title, text) {
            return `<img src="${href}" alt="${text}" title="${title || ''}" loading="lazy" style="max-width:100%;height:auto;border-radius:12px;">`;
        };
        marked.setOptions({ renderer: renderer });

        function renderNewsMarkdown(container) {
            const scope = container || document;
            scope.querySelectorAll('.markdown-news-preview').forEach(el => {
                if (!el.getAttribute('data-rendered')) {
                    try {
                        const rawContent = el.innerHTML;
                        const markdownText = el.innerText || rawContent;
                        let html = marked.parse(markdownText.trim());
                        
                        const tagUrl = "{{ url('tag') }}";
                        html = html.replace(/(^|\s)#(\w+)/g, `$1<a href="${tagUrl}/$2">#$2</a>`);
                        
                        el.innerHTML = DOMPurify.sanitize(html);
                        el.setAttribute('data-rendered', 'true');
                    } catch (e) {
                        console.error('Error rendering news markdown:', e);
                    }
                }
            });
        }

        renderNewsMarkdown();

        window.afterInfiniteScrollRender = function(container) {
            renderNewsMarkdown(container);
        };
    });
</script>
@endpush
@endsection
