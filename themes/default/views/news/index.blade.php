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

    .news-page .news-menu-card {
        border: 1px solid var(--news-border);
        box-shadow: var(--news-shadow);
    }

    .news-page .news-menu-card .widget-box-title {
        color: var(--news-text);
        margin: 0 0 16px;
        font-size: 1.05rem;
    }

    .news-page .news-home-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 12px;
        padding: 10px 14px;
        font-weight: 700;
    }

    .news-page .news-feed-stack {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

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

    .news-page .news-pagination-wrap {
        margin-top: 10px;
        padding: 8px 0 0;
    }

    .news-page .news-pagination-wrap .pagination {
        margin: 0;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .news-page .news-pagination-wrap .page-link {
        border-radius: 10px;
        border: 1px solid var(--news-border);
        min-width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--news-text);
        background: var(--news-card-bg);
        font-weight: 700;
    }

    .news-page .news-pagination-wrap .page-item.active .page-link,
    .news-page .news-pagination-wrap .page-link:hover {
        background: var(--news-accent);
        border-color: var(--news-accent);
        color: #fff;
    }

    .news-page .news-pagination-wrap .page-link:focus {
        box-shadow: none;
    }

    .news-page .news-load-more-wrap {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 8px;
    }

    .news-page .news-load-more-btn {
        min-width: 200px;
        border-radius: 12px;
        padding: 10px 18px;
        font-weight: 700;
    }

    .news-page .news-load-more-btn.is-loading,
    .news-page .news-load-more-btn:disabled {
        opacity: 0.8;
        cursor: wait;
    }

    .news-page .news-skeletons {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .news-page .news-skeletons.is-hidden {
        display: none;
    }

    .news-page .news-card.is-skeleton {
        pointer-events: none;
    }

    .news-page .news-card.is-skeleton::before {
        opacity: 0.3;
    }

    .news-page .news-card.is-skeleton:hover {
        transform: none;
        border-color: var(--news-border);
        box-shadow: var(--news-shadow);
    }

    .news-page .news-card-skeleton-line,
    .news-page .news-card-skeleton-pill,
    .news-page .news-skeleton-chip,
    .news-page .news-skeleton-date {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
        background: rgba(97, 93, 250, 0.12);
    }

    .news-page .news-card-skeleton-line::after,
    .news-page .news-card-skeleton-pill::after,
    .news-page .news-skeleton-chip::after,
    .news-page .news-skeleton-date::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(
            90deg,
            rgba(255, 255, 255, 0) 0%,
            rgba(255, 255, 255, 0.45) 50%,
            rgba(255, 255, 255, 0) 100%
        );
        transform: translateX(-100%);
        animation: newsSkeletonShimmer 1.05s linear infinite;
    }

    .news-page .news-skeleton-chip {
        width: 92px;
        height: 26px;
        border-radius: 999px;
    }

    .news-page .news-skeleton-date {
        width: 124px;
        height: 20px;
    }

    .news-page .news-card-skeleton-line {
        height: 14px;
        margin-bottom: 10px;
    }

    .news-page .news-card-skeleton-line.w-95 { width: 95%; }
    .news-page .news-card-skeleton-line.w-86 { width: 86%; }
    .news-page .news-card-skeleton-line.w-100 { width: 100%; }
    .news-page .news-card-skeleton-line.w-74 { width: 74%; }

    .news-page .news-card-skeleton-pill {
        width: 120px;
        height: 28px;
        border-radius: 999px;
    }

    @keyframes newsSkeletonShimmer {
        100% {
            transform: translateX(100%);
        }
    }

    @media (max-width: 768px) {
        .news-page .news-card-inner {
            padding: 18px 16px 14px;
        }

        .news-page .news-card-title {
            font-size: 1.2rem;
        }
    }
</style>
@endpush

@section('content')
<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.news') }}</p>
    <p class="section-banner-text">{{ __('messages.latest_news') }}</p>
</div>

<div class="grid grid-3-9 news-page">
    <!-- LEFT SIDEBAR -->
    <div class="grid-column">
        <div class="widget-box news-menu-card">
            <p class="widget-box-title">{{ __('messages.menu') }}</p>
            <div class="widget-box-content">
                <div class="post-peek-list">
                    <a href="{{ url('/home') }}" class="btn btn-primary news-home-btn">
                        <i class="fa fa-home" aria-hidden="true"></i>
                        <span>{{ __('messages.board') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="grid-column">
        <div class="news-feed-stack" id="news_feed_stack">
            <div
                id="news_cards"
                data-show-base="{{ url('/news') }}"
                data-news-label="{{ __('messages.news') }}"
                data-details-label="{{ __('messages.details') }}"
            >
                @foreach($news as $item)
                    @php
                        $newsExcerpt = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($item->text))), 230);
                    @endphp
                    <article class="widget-box news-card">
                        <div class="news-card-inner">
                            <div class="news-card-meta">
                                <span class="news-card-chip">
                                    <i class="fa fa-newspaper-o" aria-hidden="true"></i>
                                    {{ __('messages.news') }}
                                </span>
                                <time class="news-card-date">
                                    <i class="fa fa-calendar-o" aria-hidden="true"></i>
                                    {{ $item->date ? date('Y-m-d', $item->date) : '' }}
                                </time>
                            </div>

                            <h3 class="news-card-title">
                                <a href="{{ route('news.show', $item->id) }}">{{ $item->name }}</a>
                            </h3>

                            <p class="news-card-excerpt">{{ $newsExcerpt }}</p>

                            <div class="news-card-footer">
                                <a class="news-card-link" href="{{ route('news.show', $item->id) }}">
                                    <span>{{ __('messages.details') }}</span>
                                    <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="news-skeletons is-hidden" id="news_skeletons" aria-hidden="true"></div>

            <div class="news-pagination-wrap news-load-more-wrap">
                @if($news->hasMorePages())
                    <button
                        type="button"
                        class="btn btn-primary news-load-more-btn"
                        id="news_load_more_btn"
                        data-next-page="{{ $news->currentPage() + 1 }}"
                        data-endpoint="{{ route('news.index') }}"
                    >
                        {{ __('messages.more_topics') }}
                    </button>
                @endif
                <noscript>
                    {{ $news->links() }}
                </noscript>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const cardsContainer = document.getElementById('news_cards');
        const loadMoreButton = document.getElementById('news_load_more_btn');
        const skeletonContainer = document.getElementById('news_skeletons');

        if (!cardsContainer || !loadMoreButton || !skeletonContainer) {
            return;
        }

        let loading = false;
        const skeletonCount = 3;
        const showBase = cardsContainer.getAttribute('data-show-base') || '';
        const newsLabel = cardsContainer.getAttribute('data-news-label') || 'News';
        const detailsLabel = cardsContainer.getAttribute('data-details-label') || 'Details';

        function buildSkeletonCard() {
            return [
                '<article class="widget-box news-card is-skeleton" aria-hidden="true">',
                '<div class="news-card-inner">',
                '<div class="news-card-meta">',
                '<span class="news-skeleton-chip"></span>',
                '<span class="news-skeleton-date"></span>',
                '</div>',
                '<div class="news-card-skeleton-line w-95"></div>',
                '<div class="news-card-skeleton-line w-86"></div>',
                '<div class="news-card-skeleton-line w-100"></div>',
                '<div class="news-card-skeleton-line w-74"></div>',
                '<div class="news-card-footer"><span class="news-card-skeleton-pill"></span></div>',
                '</div>',
                '</article>'
            ].join('');
        }

        function showSkeletons() {
            let html = '';
            for (let i = 0; i < skeletonCount; i += 1) {
                html += buildSkeletonCard();
            }
            skeletonContainer.innerHTML = html;
            skeletonContainer.classList.remove('is-hidden');
        }

        function hideSkeletons() {
            skeletonContainer.classList.add('is-hidden');
            skeletonContainer.innerHTML = '';
        }

        function escapeHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function buildArticleUrl(id) {
            const base = showBase.replace(/\/$/, '');
            return base + '/' + encodeURIComponent(String(id));
        }

        function renderNewsCards(items) {
            if (!Array.isArray(items) || items.length === 0) {
                return '';
            }

            return items.map(function (item) {
                const articleUrl = buildArticleUrl(item.id);
                const title = escapeHtml(item.name);
                const excerpt = escapeHtml(item.excerpt);
                const dateText = escapeHtml(item.date);

                return [
                    '<article class="widget-box news-card">',
                    '<div class="news-card-inner">',
                    '<div class="news-card-meta">',
                    '<span class="news-card-chip"><i class="fa fa-newspaper-o" aria-hidden="true"></i>' + escapeHtml(newsLabel) + '</span>',
                    '<time class="news-card-date"><i class="fa fa-calendar-o" aria-hidden="true"></i>' + dateText + '</time>',
                    '</div>',
                    '<h3 class="news-card-title"><a href="' + articleUrl + '">' + title + '</a></h3>',
                    '<p class="news-card-excerpt">' + excerpt + '</p>',
                    '<div class="news-card-footer">',
                    '<a class="news-card-link" href="' + articleUrl + '">',
                    '<span>' + escapeHtml(detailsLabel) + '</span>',
                    '<i class="fa fa-arrow-right" aria-hidden="true"></i>',
                    '</a>',
                    '</div>',
                    '</div>',
                    '</article>'
                ].join('');
            }).join('');
        }

        async function loadMoreNews() {
            if (loading) {
                return;
            }

            const nextPage = parseInt(loadMoreButton.getAttribute('data-next-page') || '0', 10);
            if (!Number.isFinite(nextPage) || nextPage <= 0) {
                return;
            }

            loading = true;
            loadMoreButton.disabled = true;
            loadMoreButton.classList.add('is-loading');
            showSkeletons();

            try {
                const endpoint = loadMoreButton.getAttribute('data-endpoint') || window.location.pathname;
                const requestUrl = new URL(endpoint, window.location.origin);
                const currentUrl = new URL(window.location.href);

                currentUrl.searchParams.forEach(function (value, key) {
                    if (key !== 'page' && key !== 'ajax') {
                        requestUrl.searchParams.set(key, value);
                    }
                });

                requestUrl.searchParams.set('page', String(nextPage));
                requestUrl.searchParams.set('ajax', '1');

                const response = await fetch(requestUrl.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to load page');
                }

                const payload = await response.json();
                if (payload && Array.isArray(payload.items) && payload.items.length > 0) {
                    cardsContainer.insertAdjacentHTML('beforeend', renderNewsCards(payload.items));
                }

                const parsedNext = parseInt(payload.next_page, 10);
                if (Number.isFinite(parsedNext) && parsedNext > 0) {
                    loadMoreButton.setAttribute('data-next-page', String(parsedNext));
                    loadMoreButton.disabled = false;
                    loadMoreButton.classList.remove('is-loading');
                } else {
                    loadMoreButton.remove();
                }
            } catch (error) {
                loadMoreButton.disabled = false;
                loadMoreButton.classList.remove('is-loading');
            } finally {
                hideSkeletons();
                loading = false;
            }
        }

        loadMoreButton.addEventListener('click', loadMoreNews);
    })();
</script>
@endpush
