@extends('theme::layouts.master')

@push('head')
<style>
    .news-page {
        --news-card-bg: #ffffff;
        --news-soft: #f6f7ff;
        --news-text: #3e3f5e;
        --news-muted: #8f91ac;
        --news-border: #eaeaf5;
        --news-accent: #615dfa;
        --news-shadow: 0 14px 30px rgba(97, 93, 250, 0.08);
    }

    [data-theme="css_d"] .news-page {
        --news-card-bg: #1d2333;
        --news-soft: #22293d;
        --news-text: #ffffff;
        --news-muted: #9aa4bf;
        --news-border: #2f3749;
        --news-accent: #7750f8;
        --news-shadow: 0 14px 30px rgba(0, 0, 0, 0.3);
    }

    /* News Card Design */
    .news-page .news-card {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--news-border);
        border-radius: 16px;
        background: linear-gradient(180deg, var(--news-card-bg) 0%, var(--news-soft) 100%);
        box-shadow: var(--news-shadow);
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .news-page .news-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--news-accent), #23d2e2);
        opacity: 0.65;
        transition: opacity 0.25s ease;
    }

    .news-page .news-card:hover {
        transform: translateY(-4px);
        border-color: rgba(97, 93, 250, 0.35);
        box-shadow: 0 18px 36px rgba(97, 93, 250, 0.14);
    }

    .news-page .news-card:hover::before {
        opacity: 1;
    }

    .news-page .news-card-inner {
        padding: 22px 24px 18px;
    }

    .news-page .news-card-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .news-page .news-card-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        border-radius: 999px;
        padding: 5px 10px;
        background: rgba(97, 93, 250, 0.12);
        color: var(--news-accent);
    }

    .news-page .news-card-date {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--news-muted);
    }

    .news-page .news-card-title {
        margin: 0 0 12px;
        line-height: 1.35;
        font-size: 1.45rem;
    }

    .news-page .news-card-title a {
        color: var(--news-text);
        transition: color 0.2s ease;
    }

    .news-page .news-card-title a:hover {
        color: var(--news-accent);
        text-decoration: none;
    }

    .news-page .news-card-excerpt {
        margin: 0;
        color: var(--news-muted);
        line-height: 1.8;
        font-size: 0.96rem;
    }

    .news-page .news-card-footer {
        margin-top: 18px;
        padding-top: 14px;
        border-top: 1px dashed var(--news-border);
        display: flex;
        justify-content: flex-end;
    }

    .news-page .news-card-link {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: var(--news-text);
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .news-page .news-card-link:hover {
        color: var(--news-accent);
        text-decoration: none;
    }

    .news-page .news-card-link i {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: rgba(97, 93, 250, 0.12);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease, background 0.2s ease, color 0.2s ease;
    }

    .news-page .news-card-link:hover i {
        transform: translateX(2px);
        background: var(--news-accent);
        color: #fff;
    }

    html[dir="rtl"] .news-page .news-card-link:hover i {
        transform: translateX(-2px);
    }

    /* News Markdown Preview Layout Inside Card */
    .news-preview-content.markdown-news-preview {
        max-height: 160px;
        overflow: hidden;
        position: relative;
    }

    /* Superdesign Tabs Enhancement */
    .news-page .simple-tab-items {
        display: flex;
        gap: 8px;
        border-bottom: 1px solid var(--news-border);
        padding-bottom: 0;
        margin: 24px 0;
        overflow-x: auto;
        scrollbar-width: none;
        align-items: center;
    }

    .news-page .simple-tab-items::-webkit-scrollbar {
        display: none;
    }

    .news-page .simple-tab-item {
        height: auto;
        padding: 12px 24px;
        color: var(--news-muted);
        font-size: 0.94rem;
        font-weight: 800;
        opacity: 0.8;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        border-bottom: 4px solid transparent;
        margin-right: 0 !important; /* Override legacy margin-right */
        white-space: nowrap;
        border-radius: 12px 12px 0 0;
    }

    .news-page .simple-tab-item:hover {
        color: var(--news-accent);
        opacity: 1;
        background: rgba(97, 93, 250, 0.06);
    }

    .news-page .simple-tab-item.active {
        color: var(--news-text);
        opacity: 1;
        background: linear-gradient(180deg, rgba(97, 93, 250, 0.04) 0%, rgba(97, 93, 250, 0.01) 100%);
        border-bottom-color: var(--news-accent);
    }

    .news-page .simple-tab-item.active::after {
        content: "";
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--news-accent), #23d2e2);
        border-radius: 4px 4px 0 0;
        box-shadow: 0 -2px 10px rgba(97, 93, 250, 0.25);
    }

    html[dir="rtl"] .news-page .simple-tab-item {
        margin-left: 0 !important;
    }
</style>
@endpush

@section('content')
<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.community') }}</p>
    <p class="section-banner-text">{{ __('messages.latest_updates') }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content news-page">
    <!-- LEFT SIDEBAR -->
    <div class="grid-column">
        <x-widget-column side="portal_left" />
    </div>

    <!-- MAIN FEED -->
    <div class="grid-column">
        @if(!empty($search))
            <h2 class="section-title" style="margin-bottom: 24px;">{{ __('messages.search') }}: "{{ $search }}"</h2>

            <!-- Users Search Results -->
            @if(isset($searchedUsers) && $searchedUsers->count() > 0)
                <h3 style="margin-bottom: 16px;">{{ __('messages.members') }}</h3>
                <div class="grid grid-3-3-3 centered" style="margin-bottom: 32px;">
                    @foreach($searchedUsers as $sUser)
                        <div class="user-preview small">
                            <figure class="user-preview-cover liquid" style="background: url({{ asset('themes/default/assets/img/cover/01.jpg') }}) center center / cover no-repeat;">
                                <img src="{{ asset('themes/default/assets/img/cover/01.jpg') }}" alt="cover-01" style="display: none;">
                            </figure>
                            <div class="user-preview-info">
                                <div class="user-short-description small">
                                    <a class="user-short-description-avatar user-avatar {{ $sUser->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.show', $sUser->username) }}">
                                        <div class="user-avatar-border">
                                            <div class="hexagon-100-110" style="width: 100px; height: 110px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="100" height="110"></canvas></div>
                                        </div>
                                        <div class="user-avatar-content">
                                            <div class="hexagon-image-68-74" data-src="{{ $sUser->avatarUrl() }}" style="width: 68px; height: 74px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="68" height="74"></canvas></div>
                                        </div>
                                        <div class="user-avatar-progress-border">
                                            <div class="hexagon-border-84-92" style="width: 84px; height: 92px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="84" height="92"></canvas></div>
                                        </div>
                                    </a>
                                    <p class="user-short-description-title"><a href="{{ route('profile.show', $sUser->username) }}">{{ $sUser->username }}</a></p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Posts Search Results -->
            @if(isset($searchedStatuses) && $searchedStatuses->count() > 0)
                <h3 style="margin-bottom: 16px;">{{ __('messages.posts') }}</h3>
                <div style="display: grid; grid-gap: 16px; margin-bottom: 32px;">
                    @foreach($searchedStatuses as $activity)
                        @include('theme::partials.activity.render', ['activity' => $activity])
                    @endforeach
                </div>
            @endif

            <!-- Comments Search Results -->
            @if((isset($searchedCommentsForum) && $searchedCommentsForum->count() > 0) || (isset($searchedCommentsDir) && $searchedCommentsDir->count() > 0))
                <h3 style="margin-bottom: 16px;">{{ __('messages.comments') }}</h3>
                <div class="widget-box" style="margin-bottom: 32px;">
                    <div class="widget-box-content padding-none">
                        <div class="user-status-list">
                            <!-- Forum Comments -->
                            @if(isset($searchedCommentsForum))
                                @foreach($searchedCommentsForum as $fComment)
                                    <div class="user-status">
                                        <div class="user-status-avatar">
                                            <div class="user-avatar small no-outline">
                                                <div class="user-avatar-content">
                                                    <div class="hexagon-image-30-32" data-src="{{ $fComment->user ? $fComment->user->avatarUrl() : asset('upload/_avatar.png') }}"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="user-status-title">{{ $fComment->user->username ?? 'Unknown' }} <span style="font-weight: 400; font-size: 12px; color: #8f919d;">{{ __('messages.on_forum_topic') ?? 'on Forum Topic' }} #{{ $fComment->tid }}</span></p>
                                        <p class="user-status-text">{{ \Illuminate\Support\Str::limit(strip_tags($fComment->txt), 100) }}</p>
                                        <p class="user-status-timestamp">{{ \Carbon\Carbon::createFromTimestamp($fComment->date)->diffForHumans() }}</p>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Directory Comments -->
                            @if(isset($searchedCommentsDir))
                                @foreach($searchedCommentsDir as $dComment)
                                    <div class="user-status">
                                        <p class="user-status-title">{{ __('messages.directory_comment') ?? 'Directory Comment' }} <span style="font-weight: 400; font-size: 12px; color: #8f919d;">{{ __('messages.on_directory') ?? 'on Directory' }} #{{ $dComment->o_parent }}</span></p>
                                        <p class="user-status-text">{{ \Illuminate\Support\Str::limit(strip_tags($dComment->o_valuer), 100) }}</p>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if(
                (!isset($searchedUsers) || $searchedUsers->count() == 0) &&
                (!isset($searchedStatuses) || $searchedStatuses->count() == 0) &&
                (!isset($searchedCommentsForum) || $searchedCommentsForum->count() == 0) &&
                (!isset($searchedCommentsDir) || $searchedCommentsDir->count() == 0)
            )
                <div class="widget-box">
                    <div class="widget-box-content">
                        <p class="text-center">{{ __('messages.no_results_found') ?? 'No results found.' }}</p>
                    </div>
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
                        
                        // Safely handle hashtags within the Markdown content
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

        // Initial render
        renderNewsMarkdown();

        // Handle Infinite Scroll
        window.afterInfiniteScrollRender = function(container) {
            renderNewsMarkdown(container);
        };
    });
</script>
@endpush
@endsection
