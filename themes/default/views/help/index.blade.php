@extends('theme::layouts.master')

@section('title', $pageTitle ?? (__('help.help_center') . ' - ' . $siteName))

@section('content')
<div class="content-grid" style="padding-bottom: 80px;">
    <!-- Help Center Styles -->
    <style>
        .help-hero {
            position: relative;
            background: linear-gradient(135deg, rgba(97, 93, 250, 0.12) 0%, rgba(35, 210, 226, 0.08) 100%);
            border: 1px solid var(--myads-surface-border, rgba(97, 93, 250, 0.15));
            border-radius: var(--myads-radius-xl, 24px);
            padding: 60px 32px 50px;
            text-align: center;
            overflow: hidden;
            margin-bottom: 40px;
            box-shadow: var(--myads-shadow-sm, 0 4px 20px rgba(0,0,0,0.03));
        }

        .help-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: 9999px;
            background: rgba(97, 93, 250, 0.15);
            color: var(--myads-primary, #615dfa);
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 16px;
            border: 1px solid rgba(97, 93, 250, 0.25);
        }

        .help-hero-title {
            font-size: 2.4rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            margin-bottom: 12px;
            letter-spacing: -0.02em;
            line-height: 1.25;
        }

        .help-hero-subtitle {
            font-size: 1.1rem;
            color: var(--myads-text-muted, #64748b);
            max-width: 680px;
            margin: 0 auto 32px;
            line-height: 1.6;
        }

        .help-search-box {
            position: relative;
            max-width: 650px;
            margin: 0 auto;
        }

        .help-search-input-wrap {
            display: flex;
            align-items: center;
            background: var(--myads-surface, #ffffff);
            border: 2px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-pill, 9999px);
            padding: 6px 10px 6px 20px;
            box-shadow: 0 10px 30px rgba(97, 93, 250, 0.12);
            transition: all 0.3s ease;
        }

        [dir="rtl"] .help-search-input-wrap {
            padding: 6px 20px 6px 10px;
        }

        .help-search-input-wrap:focus-within {
            border-color: var(--myads-primary, #615dfa);
            box-shadow: 0 12px 36px rgba(97, 93, 250, 0.22);
            transform: translateY(-2px);
        }

        .help-hero .help-search-input,
        .help-search-input {
            flex: 1 1 auto;
            min-width: 0;
            width: auto;
            border: none;
            outline: none;
            background: transparent;
            font-size: 1.05rem;
            color: var(--myads-text-main, #1e293b);
            padding: 12px 14px;
        }

        .help-hero .help-search-btn,
        .help-search-btn {
            width: auto !important;
            flex: 0 0 auto !important;
            white-space: nowrap;
            background: linear-gradient(135deg, #615dfa 0%, #4e4ac8 100%);
            color: #ffffff;
            border: none;
            padding: 10px 22px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            line-height: 1.2;
        }

        [dir="rtl"] .help-search-btn i.fa-arrow-right {
            transform: scaleX(-1);
        }

        .help-search-btn:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 20px rgba(97, 93, 250, 0.4);
            color: #ffffff;
        }

        .help-quick-stats {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 24px;
            margin-top: 30px;
        }

        .help-stat-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: var(--myads-text-muted, #64748b);
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(8px);
            padding: 6px 14px;
            border-radius: 12px;
            border: 1px solid var(--myads-surface-border, rgba(0,0,0,0.05));
        }

        [data-theme="css_d"] .help-stat-chip {
            background: rgba(29, 35, 51, 0.6);
        }

        /* Search Dropdown */
        .help-live-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 12px;
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            max-height: 380px;
            overflow-y: auto;
            display: none;
            text-align: initial;
        }

        .help-live-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            border-bottom: 1px solid var(--myads-surface-border, #f1f5f9);
            color: var(--myads-text-main, #1e293b);
            text-decoration: none;
            transition: background 0.2s ease;
        }

        .help-live-item:last-child {
            border-bottom: none;
        }

        .help-live-item:hover {
            background: rgba(97, 93, 250, 0.06);
            color: var(--myads-primary, #615dfa);
        }

        .help-live-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(97, 93, 250, 0.1);
            color: var(--myads-primary, #615dfa);
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        /* Categories Section */
        .help-section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 24px;
        }

        .help-section-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .help-section-subtitle {
            font-size: 0.95rem;
            color: var(--myads-text-muted, #64748b);
            margin-top: 4px;
        }

        .help-categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 20px;
            margin-bottom: 48px;
        }

        .help-category-card {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-lg, 18px);
            padding: 26px 22px;
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: var(--myads-text-main, #1e293b);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: var(--myads-shadow-sm, 0 2px 8px rgba(0,0,0,0.03));
        }

        .help-category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--card-color, #615dfa);
            opacity: 0.8;
            transition: height 0.3s ease;
        }

        .help-category-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 32px rgba(97, 93, 250, 0.12);
            border-color: rgba(97, 93, 250, 0.3);
            color: var(--myads-text-main, #1e293b);
        }

        .help-category-card:hover::before {
            height: 6px;
        }

        .help-category-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: var(--card-gradient, rgba(97, 93, 250, 0.1));
            color: var(--card-color, #615dfa);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 18px;
            transition: transform 0.3s ease;
        }

        .help-category-card:hover .help-category-icon-wrap {
            transform: scale(1.1) rotate(4deg);
        }

        .help-category-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--myads-text-main, #1e293b);
        }

        .help-category-desc {
            font-size: 0.88rem;
            color: var(--myads-text-muted, #64748b);
            line-height: 1.55;
            margin-bottom: 18px;
            flex-grow: 1;
        }

        .help-category-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--myads-text-muted, #64748b);
            border-top: 1px solid var(--myads-surface-border, #f1f5f9);
            padding-top: 14px;
        }

        .help-category-badge {
            background: rgba(97, 93, 250, 0.08);
            color: var(--myads-primary, #615dfa);
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.78rem;
        }

        /* Featured Articles Grid */
        .help-articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 20px;
            margin-bottom: 50px;
        }

        .help-article-card {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-lg, 18px);
            padding: 22px;
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: var(--myads-text-main, #1e293b);
            transition: all 0.3s ease;
            box-shadow: var(--myads-shadow-sm, 0 2px 8px rgba(0,0,0,0.03));
        }

        .help-article-card:hover {
            transform: translateY(-4px);
            border-color: var(--myads-primary, #615dfa);
            box-shadow: 0 12px 28px rgba(97, 93, 250, 0.1);
            color: var(--myads-text-main, #1e293b);
        }

        .help-article-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .help-article-tag {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 6px;
            background: rgba(35, 210, 226, 0.12);
            color: #0891b2;
            text-transform: uppercase;
        }

        .help-article-readtime {
            font-size: 0.8rem;
            color: var(--myads-text-muted, #64748b);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .help-article-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--myads-text-main, #1e293b);
            margin-bottom: 8px;
            line-height: 1.45;
        }

        .help-article-summary {
            font-size: 0.86rem;
            color: var(--myads-text-muted, #64748b);
            line-height: 1.55;
            margin-bottom: 16px;
            flex-grow: 1;
        }

        .help-article-arrow {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--myads-primary, #615dfa);
        }

        /* Callout / Still Need Help */
        .help-support-banner {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border-radius: var(--myads-radius-xl, 24px);
            padding: 36px 32px;
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.2);
        }

        .help-support-info h3 {
            font-size: 1.4rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .help-support-info p {
            color: #94a3b8;
            font-size: 0.95rem;
            max-width: 520px;
            margin: 0;
        }

        .help-support-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-help-outline {
            padding: 10px 22px;
            border-radius: 9999px;
            font-size: 0.9rem;
            font-weight: 700;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-help-outline:hover {
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
            transform: translateY(-2px);
        }

        .btn-help-primary {
            padding: 10px 24px;
            border-radius: 9999px;
            font-size: 0.9rem;
            font-weight: 700;
            color: #ffffff;
            background: linear-gradient(135deg, #615dfa 0%, #4e4ac8 100%);
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(97, 93, 250, 0.4);
        }

        .btn-help-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(97, 93, 250, 0.6);
            color: #ffffff;
        }

        @media (max-width: 768px) {
            .help-hero { padding: 40px 18px 30px; }
            .help-hero-title { font-size: 1.8rem; }
            .help-search-input-wrap { padding: 4px 8px; }
            .help-search-btn { padding: 10px 18px; font-size: 0.85rem; }
            .help-support-banner { flex-direction: column; text-align: center; }
            .help-support-info p { margin-bottom: 16px; }
            .help-support-actions { justify-content: center; }
        }
    </style>

    <!-- Hero Section -->
    <div class="help-hero">
        <div class="help-hero-badge">
            <i class="fa-solid fa-book-bookmark"></i>
            {{ __('help.help_center') }}
        </div>
        <h1 class="help-hero-title">
            {{ __('help.help_subtitle', ['site' => $siteName]) }}
        </h1>
        <p class="help-hero-subtitle">
            {{ __('help.cat_getting_started_desc', ['site' => $siteName]) }}
        </p>

        <!-- Live Search Bar -->
        <div class="help-search-box">
            <form action="{{ route('help.search') }}" method="GET" id="help-search-form">
                <div class="help-search-input-wrap">
                    <i class="fa-solid fa-magnifying-glass" style="color: var(--myads-primary, #615dfa); font-size: 1.2rem; margin: 0 8px;"></i>
                    <input type="text"
                           name="q"
                           id="help-search-input"
                           class="help-search-input"
                           placeholder="{{ __('help.search_placeholder', ['site' => $siteName]) }}"
                           autocomplete="off">
                    <button type="submit" class="help-search-btn">
                        <i class="fa-solid fa-arrow-right"></i>
                        <span>{{ __('messages.search') }}</span>
                    </button>
                </div>
            </form>

            <!-- Real-time live suggestions dropdown -->
            <div id="help-live-dropdown" class="help-live-results"></div>
        </div>

        <!-- Quick Stats -->
        <div class="help-quick-stats">
            <div class="help-stat-chip">
                <i class="fa-solid fa-file-lines" style="color: #615dfa;"></i>
                <span>{{ __('help.articles_count', ['count' => $totalArticlesCount]) }}</span>
            </div>
            <div class="help-stat-chip">
                <i class="fa-solid fa-layer-group" style="color: #23d2e2;"></i>
                <span>{{ count($categories) }} {{ __('help.explore_categories') }}</span>
            </div>
            <div class="help-stat-chip">
                <i class="fa-solid fa-shield-check" style="color: #10b981;"></i>
                <span>100% {{ __('help.pro_tip') }}</span>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="help-section-header">
        <div>
            <h2 class="help-section-title">
                <i class="fa-solid fa-folder-tree" style="color: var(--myads-primary, #615dfa);"></i>
                {{ __('help.explore_categories') }}
            </h2>
            <p class="help-section-subtitle">{{ __('help.all_guides') }}</p>
        </div>
    </div>

    <div class="help-categories-grid">
        @foreach($categories as $cat)
            <a href="{{ route('help.category', $cat['slug']) }}"
               class="help-category-card"
               style="--card-color: {{ $cat['color'] }}; --card-gradient: {{ $cat['bg_gradient'] }};">
                <div class="help-category-icon-wrap">
                    <i class="{{ $cat['icon'] }}"></i>
                </div>
                <h3 class="help-category-title">{{ $cat['title'] }}</h3>
                <p class="help-category-desc">{{ $cat['description'] }}</p>
                <div class="help-category-footer">
                    <span class="help-category-badge">
                        <i class="fa-solid fa-book-open"></i>
                        {{ __('help.articles_count', ['count' => $cat['article_count']]) }}
                    </span>
                    <span>
                        <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem;"></i>
                    </span>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Popular Tutorials Section -->
    <div class="help-section-header">
        <div>
            <h2 class="help-section-title">
                <i class="fa-solid fa-fire" style="color: #ef4444;"></i>
                {{ __('help.popular_guides') }}
            </h2>
            <p class="help-section-subtitle">{{ __('help.quick_start') }}</p>
        </div>
    </div>

    <div class="help-articles-grid">
        @foreach($featuredArticles as $art)
            <a href="{{ route('help.article', ['category' => $art['category'], 'article' => $art['slug']]) }}" class="help-article-card">
                <div class="help-article-top">
                    <span class="help-article-tag">{{ $art['badge'] }}</span>
                    <span class="help-article-readtime">
                        <i class="fa-regular fa-clock"></i>
                        {{ __('help.read_time', ['minutes' => $art['read_time']]) }}
                    </span>
                </div>
                <h3 class="help-article-title">{{ $art['title'] }}</h3>
                <p class="help-article-summary">{{ $art['summary'] }}</p>
                <div class="help-article-arrow">
                    <span>{{ __('help.table_of_contents') }}</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Need More Help Banner -->
    <div class="help-support-banner">
        <div class="help-support-info">
            <h3>{{ __('help.need_more_help') }}</h3>
            <p>{{ __('help.need_more_help_desc', ['site' => $siteName]) }}</p>
        </div>
        <div class="help-support-actions">
            <a href="{{ route('support.index') }}" class="btn-help-outline">
                <i class="fa-solid fa-comments"></i>
                {{ __('help.ask_community') }}
            </a>
            @auth
                <a href="{{ route('tickets.index') }}" class="btn-help-primary">
                    <i class="fa-solid fa-headset"></i>
                    {{ __('help.contact_support') }}
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-help-primary">
                    <i class="fa-solid fa-headset"></i>
                    {{ __('help.contact_support') }}
                </a>
            @endauth
        </div>
    </div>
</div>

<!-- Live Search AutoComplete Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('help-search-input');
    const dropdown = document.getElementById('help-live-dropdown');
    let timeout = null;

    if (!searchInput || !dropdown) return;

    searchInput.addEventListener('input', function () {
        clearTimeout(timeout);
        const query = this.value.trim();

        if (query.length < 2) {
            dropdown.style.display = 'none';
            dropdown.innerHTML = '';
            return;
        }

        timeout = setTimeout(function () {
            fetch('{{ route('help.search') }}?q=' + encodeURIComponent(query), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.results && data.results.length > 0) {
                    let html = '';
                    data.results.slice(0, 6).forEach(item => {
                        html += `
                            <a href="${item.url}" class="help-live-item">
                                <div class="help-live-icon">
                                    <i class="${item.icon}"></i>
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 2px;">${item.title}</div>
                                    <div style="font-size: 0.8rem; color: var(--myads-text-muted, #888);">${item.category_title} &bull; ${item.read_time} min read</div>
                                </div>
                                <i class="fa-solid fa-chevron-right" style="color: var(--myads-text-muted, #ccc); font-size: 0.75rem;"></i>
                            </a>
                        `;
                    });
                    dropdown.innerHTML = html;
                    dropdown.style.display = 'block';
                } else {
                    dropdown.innerHTML = '<div style="padding: 16px; color: var(--myads-text-muted, #888); text-align: center;">{{ __("help.no_results", ["query" => ":q"]) }}'.replace(':q', query) + '</div>';
                    dropdown.style.display = 'block';
                }
            })
            .catch(() => {
                dropdown.style.display = 'none';
            });
        }, 220);
    });

    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
});
</script>
@endsection
