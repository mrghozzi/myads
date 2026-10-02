@extends('theme::layouts.master')

@section('content')
@include('theme::store.partials.page-shell-styles')

<style>
    .kb-portal {
        display: flex;
        flex-direction: column;
        gap: 28px;
        width: 100%;
        max-width: 100%;
        min-width: 0;
    }

    /* Portal Hero Header */
    .kb-portal-hero {
        position: relative;
        padding: 42px 36px;
        border-radius: 24px;
        background: linear-gradient(135deg, rgba(97, 93, 250, 0.12), rgba(35, 210, 226, 0.16));
        border: 1px solid var(--store-shell-border, rgba(143, 145, 172, 0.18));
        box-shadow: var(--store-shell-shadow, 0 20px 48px rgba(0, 0, 0, 0.08));
        overflow: hidden;
    }

    body[data-theme="css_d"] .kb-portal-hero {
        background: linear-gradient(135deg, rgba(97, 93, 250, 0.22), rgba(35, 210, 226, 0.14));
    }

    .kb-portal-hero__inner {
        position: relative;
        z-index: 2;
        max-width: 760px;
        margin: 0 auto;
        text-align: center;
    }

    .kb-portal-hero__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 16px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        background: var(--store-shell-surface, #fff);
        color: var(--store-shell-accent, #615dfa);
        border: 1px solid var(--store-shell-border, rgba(97, 93, 250, 0.2));
        margin-bottom: 16px;
    }

    .kb-portal-hero__title {
        font-size: 2.3rem;
        font-weight: 900;
        line-height: 1.2;
        color: var(--store-shell-title, #283c50);
        margin-bottom: 12px;
        letter-spacing: -0.02em;
    }

    .kb-portal-hero__subtitle {
        font-size: 1.05rem;
        line-height: 1.6;
        color: var(--store-shell-text, #5d607a);
        margin-bottom: 28px;
    }

    /* Live AJAX Search Box */
    .kb-portal-search-box {
        position: relative;
        max-width: 640px;
        margin: 0 auto;
    }

    .kb-portal-search-input-wrap {
        display: flex;
        align-items: center;
        background: var(--store-shell-surface, #ffffff);
        border: 2px solid var(--store-shell-border, rgba(143, 145, 172, 0.2));
        border-radius: 18px;
        padding: 6px 10px 6px 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
        transition: all 0.25s ease;
    }

    .kb-portal-search-input-wrap:focus-within {
        border-color: var(--store-shell-accent, #615dfa);
        box-shadow: 0 14px 34px rgba(97, 93, 250, 0.18);
    }

    .kb-portal-search-input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        font-size: 1rem;
        color: var(--store-shell-title, #283c50);
        padding: 8px 12px;
    }

    .kb-portal-search-btn {
        background: var(--store-shell-accent, #615dfa);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 10px 22px;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .kb-portal-search-btn:hover {
        background: #4e4ac8;
    }

    /* Live Search Results Dropdown */
    .kb-search-results-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        left: 0;
        right: 0;
        background: var(--store-shell-surface, #fff);
        border: 1px solid var(--store-shell-border, rgba(143, 145, 172, 0.2));
        border-radius: 18px;
        box-shadow: 0 20px 48px rgba(0, 0, 0, 0.14);
        max-height: 420px;
        overflow-y: auto;
        z-index: 100;
        display: none;
        text-align: left;
    }

    [dir="rtl"] .kb-search-results-dropdown {
        text-align: right;
    }

    .kb-search-result-item {
        display: block;
        padding: 14px 20px;
        border-bottom: 1px solid var(--store-shell-border, rgba(143, 145, 172, 0.1));
        text-decoration: none;
        transition: background 0.15s ease;
    }

    .kb-search-result-item:last-child {
        border-bottom: none;
    }

    .kb-search-result-item:hover {
        background: var(--store-shell-soft, #f7f8fd);
    }

    .kb-search-result-title {
        font-size: 0.98rem;
        font-weight: 700;
        color: var(--store-shell-title, #283c50);
        margin-bottom: 4px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .kb-search-result-badge {
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 6px;
        background: rgba(97, 93, 250, 0.1);
        color: var(--store-shell-accent, #615dfa);
    }

    .kb-search-result-snippet {
        font-size: 0.85rem;
        color: var(--store-shell-muted, #8f94b5);
        line-height: 1.4;
    }

    /* KPI Stats Bar */
    .kb-portal-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }

    .kb-portal-stat-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 24px;
        border-radius: 18px;
        background: var(--store-shell-surface, #fff);
        border: 1px solid var(--store-shell-border, rgba(143, 145, 172, 0.16));
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
    }

    .kb-portal-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    .kb-portal-stat-icon--indigo { background: rgba(99, 102, 241, 0.12); color: #6366f1; }
    .kb-portal-stat-icon--emerald { background: rgba(16, 185, 129, 0.12); color: #10b981; }
    .kb-portal-stat-icon--amber { background: rgba(245, 158, 11, 0.12); color: #f59e0b; }
    .kb-portal-stat-icon--rose { background: rgba(244, 63, 94, 0.12); color: #f43f5e; }

    .kb-portal-stat-number {
        font-size: 1.5rem;
        font-weight: 800;
        line-height: 1.1;
        color: var(--store-shell-title, #283c50);
    }

    .kb-portal-stat-label {
        font-size: 0.8rem;
        color: var(--store-shell-muted, #8f94b5);
        margin-top: 3px;
    }

    /* Product Grid Cards */
    .kb-portal-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
    }

    .kb-product-doc-card {
        border-radius: 20px;
        background: var(--store-shell-surface, #fff);
        border: 1px solid var(--store-shell-border, rgba(143, 145, 172, 0.18));
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.04);
        padding: 24px;
        display: flex;
        flex-direction: column;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .kb-product-doc-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.08);
        border-color: rgba(97, 93, 250, 0.3);
    }

    .kb-product-doc-card__header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
    }

    .kb-product-doc-card__media {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        overflow: hidden;
        background: var(--store-shell-soft, #f7f8fd);
        flex-shrink: 0;
        border: 1px solid var(--store-shell-border, rgba(143, 145, 172, 0.12));
    }

    .kb-product-doc-card__media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .kb-product-doc-card__title {
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.3;
        margin: 0 0 4px;
    }

    .kb-product-doc-card__title a {
        color: var(--store-shell-title, #283c50);
        text-decoration: none;
    }

    .kb-product-doc-card__title a:hover {
        color: var(--store-shell-accent, #615dfa);
    }

    .kb-product-doc-card__meta {
        font-size: 0.8rem;
        color: var(--store-shell-muted, #8f94b5);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .kb-product-doc-card__desc {
        font-size: 0.88rem;
        line-height: 1.5;
        color: var(--store-shell-text, #5d607a);
        margin-bottom: 20px;
        flex: 1;
    }

    .kb-product-doc-card__footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 16px;
        border-top: 1px solid var(--store-shell-border, rgba(143, 145, 172, 0.12));
    }

    .kb-product-doc-card__pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 999px;
        background: rgba(97, 93, 250, 0.08);
        color: var(--store-shell-accent, #615dfa);
    }

    /* Section Title Strip */
    .kb-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .kb-section-title {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--store-shell-title, #283c50);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Recent Updates List */
    .kb-recent-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
        gap: 16px;
    }

    .kb-recent-card {
        padding: 16px 20px;
        border-radius: 16px;
        background: var(--store-shell-surface, #fff);
        border: 1px solid var(--store-shell-border, rgba(143, 145, 172, 0.16));
        text-decoration: none;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .kb-recent-card:hover {
        border-color: var(--store-shell-accent, #615dfa);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        text-decoration: none;
    }

    .kb-recent-card__top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.75rem;
    }

    .kb-recent-card__product {
        font-weight: 700;
        color: var(--store-shell-accent, #615dfa);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .kb-recent-card__date {
        color: var(--store-shell-muted, #8f94b5);
    }

    .kb-recent-card__title {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--store-shell-title, #283c50);
        margin: 0;
    }
</style>

<div class="kb-portal">
    <!-- Portal Hero Header -->
    <div class="kb-portal-hero">
        <div class="kb-portal-hero__inner">
            <span class="kb-portal-hero__eyebrow">
                <i class="fa fa-book" aria-hidden="true"></i> {{ __('messages.knowledgebase') }}
            </span>
            <h1 class="kb-portal-hero__title">{{ __('messages.kb_portal_title') }}</h1>
            <p class="kb-portal-hero__subtitle">{{ __('messages.kb_portal_desc') }}</p>

            <!-- Search Form -->
            <div class="kb-portal-search-box">
                <form action="{{ route('kb.portal') }}" method="GET" id="kb-portal-search-form">
                    <div class="kb-portal-search-input-wrap">
                        <i class="fa fa-search text-muted" aria-hidden="true"></i>
                        <input
                            type="text"
                            name="q"
                            id="kb-portal-search-input"
                            class="kb-portal-search-input"
                            placeholder="{{ __('messages.kb_search_portal_placeholder') }}"
                            value="{{ $searchQuery }}"
                            autocomplete="off"
                        >
                        <button type="submit" class="kb-portal-search-btn">
                            <span>{{ __('messages.search') ?? 'Search' }}</span>
                        </button>
                    </div>
                </form>

                <!-- Live Results Dropdown -->
                <div class="kb-search-results-dropdown" id="kb-portal-results-dropdown"></div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="kb-portal-stats">
        <div class="kb-portal-stat-card">
            <div class="kb-portal-stat-icon kb-portal-stat-icon--indigo"><i class="fa fa-book" aria-hidden="true"></i></div>
            <div>
                <div class="kb-portal-stat-number">{{ $totalArticles }}</div>
                <div class="kb-portal-stat-label">{{ __('messages.total_articles') }}</div>
            </div>
        </div>
        <div class="kb-portal-stat-card">
            <div class="kb-portal-stat-icon kb-portal-stat-icon--emerald"><i class="fa fa-cubes" aria-hidden="true"></i></div>
            <div>
                <div class="kb-portal-stat-number">{{ $products->count() }}</div>
                <div class="kb-portal-stat-label">{{ __('messages.kb_all_products') }}</div>
            </div>
        </div>
        <div class="kb-portal-stat-card">
            <div class="kb-portal-stat-icon kb-portal-stat-icon--amber"><i class="fa fa-folder-open" aria-hidden="true"></i></div>
            <div>
                <div class="kb-portal-stat-number">{{ $kbCategories->count() }}</div>
                <div class="kb-portal-stat-label">{{ __('messages.kb_categories') }}</div>
            </div>
        </div>
        <div class="kb-portal-stat-card">
            <div class="kb-portal-stat-icon kb-portal-stat-icon--rose"><i class="fa fa-users" aria-hidden="true"></i></div>
            <div>
                <div class="kb-portal-stat-number">{{ $totalContributors }}</div>
                <div class="kb-portal-stat-label">{{ __('messages.kb_contributors') }}</div>
            </div>
        </div>
    </div>

    @if(!empty($searchQuery) && isset($searchResults))
        <!-- Search Results View -->
        <div class="kb-section-head">
            <h3 class="kb-section-title">
                <i class="fa fa-search" aria-hidden="true"></i> {{ __('messages.search_results') }}: "{{ $searchQuery }}" ({{ $searchResults->total() }})
            </h3>
            <a href="{{ route('kb.portal') }}" class="button small secondary">{{ __('messages.clear') ?? 'Clear' }}</a>
        </div>

        @if($searchResults->isEmpty())
            <div class="kb-empty-state" style="padding: 48px; text-align: center; background: var(--store-shell-surface, #fff); border-radius: 18px; border: 1px solid var(--store-shell-border);">
                <i class="fa fa-search-minus" style="font-size: 2.5rem; color: var(--store-shell-muted); margin-bottom: 16px;"></i>
                <h4>{{ __('messages.kb_no_docs_found') }}</h4>
            </div>
        @else
            <div class="kb-portal-grid">
                @foreach($searchResults as $item)
                    <article class="kb-product-doc-card">
                        <div class="kb-recent-card__top" style="margin-bottom: 12px;">
                            <span class="kb-recent-card__product">{{ $item->o_mode }}</span>
                            @if($item->kbCategory)
                                <span class="badge bg-light text-muted">{{ $item->kbCategory->name }}</span>
                            @endif
                        </div>
                        <h4 class="kb-product-doc-card__title">
                            <a href="{{ route('kb.show', ['name' => $item->o_mode, 'article' => $item->name]) }}">{{ $item->name }}</a>
                        </h4>
                        <p class="kb-product-doc-card__desc">{{ Str::limit(strip_tags((string) $item->o_valuer), 160) }}</p>
                        <div class="kb-product-doc-card__footer">
                            <span class="text-muted small"><i class="fa fa-clock-o"></i> {{ $item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->diffForHumans() : '—' }}</span>
                            <a href="{{ route('kb.show', ['name' => $item->o_mode, 'article' => $item->name]) }}" class="button small secondary">{{ __('messages.read_more') ?? 'Read' }}</a>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="kb-pagination" style="display: flex; justify-content: center; margin-top: 24px;">
                {{ $searchResults->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @else
        <!-- Products Documentation Catalog -->
        <div>
            <div class="kb-section-head">
                <h3 class="kb-section-title"><i class="fa fa-cubes" aria-hidden="true"></i> {{ __('messages.kb_featured_products') }}</h3>
            </div>

            @if($products->isEmpty())
                <div class="kb-empty-state" style="padding: 48px; text-align: center; background: var(--store-shell-surface, #fff); border-radius: 18px; border: 1px solid var(--store-shell-border);">
                    <p>{{ __('messages.no_post') }}</p>
                </div>
            @else
                <div class="kb-portal-grid">
                    @foreach($products as $prod)
                        @php
                            $pCount = $articleCounts[$prod->name] ?? 0;
                            $pImg = $prod->product_image ?? theme_asset('img/error_plug.png');
                            $seller = $prod->user;
                        @endphp
                        <div class="kb-product-doc-card">
                            <div class="kb-product-doc-card__header">
                                <div class="kb-product-doc-card__media">
                                    <img src="{{ $pImg }}" alt="{{ $prod->name }}" onerror="this.onerror=null;this.src='{{ theme_asset('img/error_plug.png') }}';">
                                </div>
                                <div>
                                    <h4 class="kb-product-doc-card__title">
                                        <a href="{{ route('kb.index', $prod->name) }}">{{ $prod->name }}</a>
                                    </h4>
                                    <div class="kb-product-doc-card__meta">
                                        <span><i class="fa fa-user-circle"></i> {{ $seller ? $seller->username : __('messages.unknown') }}</span>
                                    </div>
                                </div>
                            </div>
                            <p class="kb-product-doc-card__desc">
                                {{ Str::limit(strip_tags((string) $prod->o_valuer), 120) ?: __('messages.seo_kb_description', ['product' => $prod->name]) }}
                            </p>
                            <div class="kb-product-doc-card__footer">
                                <span class="kb-product-doc-card__pill">
                                    <i class="fa fa-book"></i> <strong>{{ $pCount }}</strong> {{ __('messages.topics') }}
                                </span>
                                <a href="{{ route('kb.index', $prod->name) }}" class="button small secondary">
                                    {{ __('messages.kb_explore_docs') }} <i class="fa fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if($recentArticles->isNotEmpty())
            <!-- Recently Updated Guides -->
            <div>
                <div class="kb-section-head">
                    <h3 class="kb-section-title"><i class="fa fa-history" aria-hidden="true"></i> {{ __('messages.kb_recent_articles') }}</h3>
                </div>
                <div class="kb-recent-grid">
                    @foreach($recentArticles as $recent)
                        <a href="{{ route('kb.show', ['name' => $recent->o_mode, 'article' => $recent->name]) }}" class="kb-recent-card">
                            <div class="kb-recent-card__top">
                                <span class="kb-recent-card__product">{{ $recent->o_mode }}</span>
                                <span class="kb-recent-card__date">{{ $recent->updated_at ? \Carbon\Carbon::parse($recent->updated_at)->diffForHumans() : '—' }}</span>
                            </div>
                            <h5 class="kb-recent-card__title">{{ $recent->name }}</h5>
                            <span class="text-muted small" style="display: flex; align-items: center; gap: 8px;">
                                @if($recent->kbCategory)
                                    <span class="badge bg-light text-primary">{{ $recent->kbCategory->name }}</span>
                                @endif
                                <span><i class="fa fa-clock-o"></i> {{ max(1, (int) ceil(str_word_count(strip_tags((string)$recent->o_valuer)) / 200)) }} min</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
(function() {
    const input = document.getElementById('kb-portal-search-input');
    const dropdown = document.getElementById('kb-portal-results-dropdown');
    let debounceTimer = null;

    if (input && dropdown) {
        input.addEventListener('input', function() {
            const query = this.value.trim();
            clearTimeout(debounceTimer);

            if (query.length < 2) {
                dropdown.style.display = 'none';
                dropdown.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(`{{ route('kb.portal') }}?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.articles && data.articles.length > 0) {
                        let html = '';
                        data.articles.forEach(art => {
                            html += `
                                <a href="${art.url}" class="kb-search-result-item">
                                    <div class="kb-search-result-title">
                                        <span>${art.name}</span>
                                        <span class="kb-search-result-badge">${art.product}</span>
                                    </div>
                                    <div class="kb-search-result-snippet">${art.snippet}</div>
                                </a>
                            `;
                        });
                        dropdown.innerHTML = html;
                        dropdown.style.display = 'block';
                    } else {
                        dropdown.innerHTML = `<div style="padding: 18px; text-align: center; color: var(--store-shell-muted); font-size: 0.9rem;">{{ __('messages.kb_no_search_results') }}</div>`;
                        dropdown.style.display = 'block';
                    }
                })
                .catch(() => {
                    dropdown.style.display = 'none';
                });
            }, 250);
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }
})();
</script>
@endpush
@endsection
