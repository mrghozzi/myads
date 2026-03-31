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

    .news-page .news-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 12px;
        padding: 10px 14px;
        font-weight: 700;
    }

    html[dir="rtl"] .news-page .news-back-btn i {
        transform: rotate(180deg);
    }

    .news-page .news-article-card {
        position: relative;
        overflow: hidden;
        border: 1px solid var(--news-border);
        border-radius: 16px;
        background: linear-gradient(180deg, var(--news-card-bg) 0%, var(--news-soft) 100%);
        box-shadow: var(--news-shadow);
    }

    .news-page .news-article-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--news-accent), #23d2e2);
        opacity: 0.85;
    }

    .news-page .news-article-inner {
        padding: 24px 26px 22px;
    }

    .news-page .news-article-meta {
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

    .news-page .news-article-title {
        margin: 0 0 12px;
        line-height: 1.35;
        font-size: 1.6rem;
        color: var(--news-text);
    }

    .news-page .news-article-content {
        color: var(--news-muted);
        font-size: 0.98rem;
        line-height: 1.9;
        word-wrap: break-word;
    }

    .news-page .news-article-content h1,
    .news-page .news-article-content h2,
    .news-page .news-article-content h3,
    .news-page .news-article-content h4,
    .news-page .news-article-content h5,
    .news-page .news-article-content h6 {
        color: var(--news-text);
        line-height: 1.35;
        margin: 1.35em 0 0.55em;
    }

    .news-page .news-article-content h1 { font-size: 1.7rem; }
    .news-page .news-article-content h2 { font-size: 1.5rem; }
    .news-page .news-article-content h3 { font-size: 1.3rem; }

    .news-page .news-article-content p {
        margin: 0 0 1em;
    }

    .news-page .news-article-content a {
        color: var(--news-accent);
        text-decoration: underline;
    }

    .news-page .news-article-content ul,
    .news-page .news-article-content ol {
        margin: 0 0 1.1em;
        padding-inline-start: 1.4em;
    }

    .news-page .news-article-content li {
        margin-bottom: 0.4em;
    }

    .news-page .news-article-content blockquote {
        margin: 1.2em 0;
        padding: 12px 14px;
        border-inline-start: 4px solid var(--news-accent);
        background: rgba(97, 93, 250, 0.08);
        border-radius: 10px;
        color: var(--news-text);
    }

    .news-page .news-article-content pre,
    .news-page .news-article-content code {
        font-family: Consolas, Monaco, "Courier New", monospace;
    }

    .news-page .news-article-content pre {
        margin: 1em 0;
        padding: 12px;
        overflow-x: auto;
        border-radius: 10px;
        border: 1px solid var(--news-border);
        background: rgba(20, 24, 36, 0.86);
        color: #f2f4ff;
    }

    .news-page .news-article-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.1em 0;
    }

    .news-page .news-article-content table th,
    .news-page .news-article-content table td {
        border: 1px solid var(--news-border);
        padding: 8px 10px;
    }

    .news-page .news-article-content table th {
        color: var(--news-text);
        background: rgba(97, 93, 250, 0.08);
    }

    .news-page .news-article-content img,
    .news-page .news-article-content video,
    .news-page .news-article-content iframe {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
    }

    @media (max-width: 768px) {
        .news-page .news-article-inner {
            padding: 18px 16px 16px;
        }

        .news-page .news-article-title {
            font-size: 1.35rem;
        }
    }
    
    .markdown-content { display: none; }
</style>
@endpush

@section('content')
<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ $article->name }}</p>
    <p class="section-banner-text">{{ $article->date ? date('Y-m-d', $article->date) : '' }}</p>
</div>

<div class="grid grid-3-9 news-page">
    <!-- LEFT SIDEBAR -->
    <div class="grid-column">
        <div class="widget-box news-menu-card">
            <p class="widget-box-title">{{ __('messages.menu') }}</p>
            <div class="widget-box-content">
                <div class="post-peek-list">
                    <a href="{{ route('news.index') }}" class="btn btn-primary news-back-btn">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i>
                        <span>{{ __('messages.back_to_news') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="grid-column">
        <article class="widget-box news-article-card">
            <div class="news-article-inner">
                <div class="news-article-meta">
                    <span class="news-card-chip">
                        <i class="fa fa-newspaper-o" aria-hidden="true"></i>
                        {{ __('messages.news') }}
                    </span>
                    <time class="news-card-date">
                        <i class="fa fa-calendar-o" aria-hidden="true"></i>
                        {{ $article->date ? date('Y-m-d', $article->date) : '' }}
                    </time>
                </div>
                <h1 class="news-article-title">{{ $article->name }}</h1>
                <div class="news-article-content markdown-content">
                    {!! $article->text !!}
                </div>
            </div>
        </article>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.markdown-content').forEach(el => {
            if (!el.getAttribute('data-rendered')) {
                try {
                    const rawContent = el.innerHTML;
                    el.innerHTML = DOMPurify.sanitize(marked.parse(el.innerText || rawContent));
                    el.setAttribute('data-rendered', 'true');
                    el.style.display = 'block';
                } catch (e) {
                    console.error('Error rendering markdown:', e);
                }
            }
        });
    });
</script>
@endpush
