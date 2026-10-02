@extends('theme::layouts.master')

@section('content')
@php
    $articles = $articles ?? collect();
    $productImage = $product->product_image ?? theme_asset('img/error_plug.png');
    $owner = $product->user;
    $ownerAvatar = $owner ? $owner->avatarUrl() : asset('upload/_avatar.png');
    $pendingCounts = $pendingCounts ?? collect();
    $articleAuthors = $articleAuthors ?? collect();
    $kbCategories = $kbCategories ?? collect();
    $selectedCategory = $selectedCategory ?? null;
    $currentArticle = $article ?? null;
    $articleAuthor = $articleAuthor ?? null;
    $canManageCurrentArticle = $canManageCurrentArticle ?? false;
    $mode = $mode ?? 'list';
    $currentTopicPendingCount = $currentArticle
        ? \App\Models\Option::where('o_type', 'knowledgebase')->where('o_mode', $product->name)->where('name', $currentArticle->name)->where('o_order', 1)->count()
        : 0;
    $knowledgebaseExternalShareUrl = $knowledgebaseExternalShareUrl
        ?? ($currentArticle ? route('kb.show', ['name' => $product->name, 'article' => $currentArticle->name]) : null);
    $knowledgebaseExternalShareTitle = $knowledgebaseExternalShareTitle
        ?? ($currentArticle ? trim($currentArticle->name . ' - ' . $product->name) : $product->name);
    $knowledgebaseSocialPlatforms = ['facebook', 'twitter', 'linkedin', 'telegram'];
    $shellTitle = $currentArticle ? $currentArticle->name : $product->name;
    $shellSummary = $currentArticle
        ? \Illuminate\Support\Str::limit(strip_tags((string) $currentArticle->o_valuer), 240)
        : \Illuminate\Support\Str::limit((string) $product->o_valuer, 240);
    $newTopicUrl = route('kb.index', $product->name) . '#kb-new-topic';
@endphp

@include('theme::store.partials.page-shell-styles')
@include('theme::store.partials.kb-superdesign-formatter')

<style>
    /* KB Search & Modern Header */
    .kb-search-container {
        position: relative;
        margin-bottom: 20px;
    }
    .kb-search-bar {
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--store-shell-surface, #fff);
        border: 1px solid var(--store-shell-border, rgba(143,145,172,0.2));
        border-radius: 16px;
        padding: 8px 18px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.03);
        transition: all 0.2s ease;
    }
    .kb-search-bar:focus-within {
        border-color: var(--store-shell-accent, #615dfa);
        box-shadow: 0 8px 24px rgba(97,93,250,0.15);
    }
    .kb-search-bar input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        font-size: 0.95rem;
        color: var(--store-shell-title, #283c50);
    }
    .kb-search-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        background: var(--store-shell-surface, #fff);
        border: 1px solid var(--store-shell-border, rgba(143,145,172,0.2));
        border-radius: 16px;
        box-shadow: 0 16px 36px rgba(0,0,0,0.12);
        max-height: 380px;
        overflow-y: auto;
        z-index: 99;
        display: none;
    }
    .kb-search-dropdown-item {
        display: block;
        padding: 12px 18px;
        border-bottom: 1px solid var(--store-shell-border, rgba(143,145,172,0.08));
        text-decoration: none;
        transition: background 0.15s ease;
    }
    .kb-search-dropdown-item:last-child { border-bottom: none; }
    .kb-search-dropdown-item:hover { background: var(--store-shell-soft, #f7f8fd); text-decoration: none; }
    .kb-search-dropdown-title { font-weight: 700; color: var(--store-shell-title); font-size: 0.92rem; margin-bottom: 2px; }
    .kb-search-dropdown-desc { font-size: 0.8rem; color: var(--store-shell-muted); line-height: 1.3; }

    /* Sticky Table of Contents (TOC) */
    .kb-toc-box {
        position: sticky;
        top: 90px;
        background: var(--store-shell-surface, #fff);
        border: 1px solid var(--store-shell-border, rgba(143,145,172,0.18));
        border-radius: 16px;
        padding: 18px;
        margin-bottom: 20px;
    }
    .kb-toc-title {
        font-size: 0.85rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--store-shell-muted);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .kb-toc-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .kb-toc-link {
        font-size: 0.86rem;
        color: var(--store-shell-text);
        text-decoration: none;
        display: block;
        padding: 4px 8px;
        border-radius: 6px;
        transition: all 0.15s ease;
        line-height: 1.4;
    }
    .kb-toc-link:hover, .kb-toc-link.is-active {
        color: var(--store-shell-accent, #615dfa);
        background: rgba(97,93,250,0.08);
        font-weight: 600;
        text-decoration: none;
    }
    .kb-toc-link.level-3 {
        padding-inline-start: 18px;
        font-size: 0.82rem;
    }

    /* Feedback Box */
    .kb-feedback-widget {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        padding: 18px 24px;
        border-radius: 16px;
        background: var(--store-shell-soft, #f7f8fd);
        border: 1px solid var(--store-shell-border, rgba(143,145,172,0.18));
        margin-top: 28px;
    }
    .kb-feedback-label {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--store-shell-title);
    }
    .kb-feedback-btns {
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    .kb-feedback-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 16px;
        border-radius: 10px;
        border: 1px solid var(--store-shell-border);
        background: var(--store-shell-surface, #fff);
        color: var(--store-shell-title);
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .kb-feedback-btn:hover {
        background: rgba(97,93,250,0.08);
        border-color: var(--store-shell-accent);
        color: var(--store-shell-accent);
    }
    .kb-feedback-btn.active-up {
        background: #10b981;
        border-color: #10b981;
        color: #fff;
    }
    .kb-feedback-btn.active-down {
        background: #ef4444;
        border-color: #ef4444;
        color: #fff;
    }

    /* Sibling Nav Cards */
    .kb-sibling-nav {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
        margin-top: 24px;
    }
    .kb-sibling-card {
        padding: 16px 20px;
        border-radius: 14px;
        background: var(--store-shell-surface, #fff);
        border: 1px solid var(--store-shell-border, rgba(143,145,172,0.18));
        text-decoration: none;
        display: flex;
        flex-direction: column;
        gap: 4px;
        transition: all 0.2s ease;
    }
    .kb-sibling-card:hover {
        border-color: var(--store-shell-accent, #615dfa);
        box-shadow: 0 6px 18px rgba(0,0,0,0.04);
        text-decoration: none;
    }
    .kb-sibling-label { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: var(--store-shell-muted); }
    .kb-sibling-title { font-size: 0.95rem; font-weight: 700; color: var(--store-shell-title); }

    /* Visual Diff Styles */
    .kb-diff-panel {
        background: #161b28;
        border-radius: 14px;
        border: 1px solid rgba(255,255,255,0.08);
        overflow: hidden;
        margin-top: 16px;
        font-family: 'JetBrains Mono', Consolas, monospace;
        font-size: 0.85rem;
        direction: ltr !important;
        text-align: left !important;
    }
    .kb-diff-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 16px;
        background: rgba(255,255,255,0.04);
        border-bottom: 1px solid rgba(255,255,255,0.08);
        color: #94a3b8;
        font-size: 0.8rem;
    }
    .kb-diff-line {
        padding: 2px 12px;
        line-height: 1.5;
        white-space: pre-wrap;
        word-break: break-all;
    }
    .kb-diff-line--add { background: rgba(16, 185, 129, 0.16); color: #34d399; }
    .kb-diff-line--del { background: rgba(239, 68, 68, 0.16); color: #f87171; }
    .kb-diff-line--ctx { color: #94a3b8; }

    /* Live Editor Tabs */
    .kb-editor-tabs {
        display: flex;
        gap: 6px;
        margin-bottom: 12px;
    }
    .kb-editor-tab-btn {
        padding: 6px 14px;
        border-radius: 8px;
        border: 1px solid var(--store-shell-border);
        background: var(--store-shell-surface, #fff);
        color: var(--store-shell-muted);
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .kb-editor-tab-btn.active {
        background: var(--store-shell-accent, #615dfa);
        color: #fff;
        border-color: var(--store-shell-accent);
    }
</style>

<div class="section-header">
    <div class="section-header-info">
        <p class="section-pretitle">{{ __('messages.store') }}</p>
        <h2 class="section-title">{{ $product->name }}</h2>
    </div>
    <div class="section-header-actions">
        <a class="button small secondary me-2" href="{{ route('kb.portal') }}"><i class="fa fa-home"></i>&nbsp;{{ __('messages.kb_portal_title') }}</a>
        <a class="section-header-subsection" href="{{ route('store.show', $product->name) }}">{{ $product->name }}</a>
        <p class="section-header-subsection">{{ __('messages.knowledgebase') }}</p>
    </div>
</div>

<div class="knowledgebase-page">
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 24px;">{{ session('success') }}</div>
    @endif

    <div class="widget-box kb-shell-card no-padding">
        <div class="kb-hero">
            <div class="kb-hero__main">
                <div class="kb-hero__media">
                    <img src="{{ $productImage }}" alt="{{ $product->name }}" onerror="this.onerror=null;this.src='{{ theme_asset('img/error_plug.png') }}';">
                </div>
                <div class="kb-hero__content">
                    <div class="kb-badge-row">
                        @if($product->o_order > 0)
                            <span class="kb-pill"><strong>{{ $product->o_order }}</strong> {{ __('messages.points') }}</span>
                        @else
                            <span class="kb-pill">{{ __('messages.free') }}</span>
                        @endif
                        <span class="kb-pill"><strong>{{ $articleTotal ?? 0 }}</strong> {{ __('messages.topics') }}</span>
                        <span class="kb-pill"><strong>{{ $pendingTotal ?? 0 }}</strong> {{ __('messages.pending') }}</span>
                    </div>
                    <h3 class="kb-title">{{ $shellTitle }}</h3>
                    <p class="kb-subtitle">{{ $shellSummary }}</p>
                    <div class="kb-stat-grid">
                        <div class="kb-stat-card"><span>{{ __('messages.topics') }}</span><strong>{{ $articleTotal ?? 0 }}</strong></div>
                        <div class="kb-stat-card"><span>{{ __('messages.pending') }}</span><strong>{{ $pendingTotal ?? 0 }}</strong></div>
                        <div class="kb-stat-card"><span>{{ __('messages.current') }}</span><strong>{{ $currentArticle ? ('#' . $currentArticle->id) : __('messages.preview') }}</strong></div>
                        <div class="kb-stat-card"><span>{{ __('messages.seller') }}</span><strong>{{ $owner ? $owner->username : __('messages.unknown') }}</strong></div>
                    </div>
                    <div class="kb-shell-nav">
                        <a class="kb-shell-nav__link {{ $mode === 'list' ? 'active' : '' }}" href="{{ route('kb.index', $product->name) }}"><i class="fa fa-list-ul" aria-hidden="true"></i>{{ __('messages.topics') }}</a>
                        <a class="kb-shell-nav__link {{ $mode === 'create' ? 'active' : '' }}" href="{{ $newTopicUrl }}"><i class="fa fa-plus" aria-hidden="true"></i>{{ __('messages.add') }}</a>
                        @if($currentArticle)
                            <a class="kb-shell-nav__link {{ $mode === 'show' ? 'active' : '' }}" href="{{ route('kb.show', ['name' => $product->name, 'article' => $currentArticle->name]) }}"><i class="fa fa-book" aria-hidden="true"></i>{{ __('messages.topic') }}</a>
                            <a class="kb-shell-nav__link {{ $mode === 'edit' ? 'active' : '' }}" href="{{ route('kb.edit', ['name' => $product->name, 'article' => $currentArticle->name]) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>{{ $canManageCurrentArticle ? __('messages.edit_topic') : __('messages.suggest_edit') }}</a>
                            <a class="kb-shell-nav__link {{ $mode === 'pending' ? 'active' : '' }}" href="{{ route('kb.pending', ['name' => $product->name, 'article' => $currentArticle->name]) }}"><i class="fa fa-hourglass-half" aria-hidden="true"></i>{{ __('messages.pending') }} <strong>{{ $currentTopicPendingCount }}</strong></a>
                            <a class="kb-shell-nav__link {{ $mode === 'history' ? 'active' : '' }}" href="{{ route('kb.history', ['name' => $product->name, 'article' => $currentArticle->name]) }}"><i class="fa fa-history" aria-hidden="true"></i>{{ __('messages.history') }}</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="kb-aside">
                <div class="kb-aside-card">
                    <div class="kb-aside-card__header">
                        <div>
                            <p class="kb-aside-card__label">{{ __('messages.details') }}</p>
                            <p class="kb-aside-card__title">{{ __('messages.knowledgebase') }}</p>
                        </div>
                        <a class="button small tertiary" href="{{ route('store.show', $product->name) }}"><i class="fa fa-shopping-basket" aria-hidden="true"></i>&nbsp;{{ __('messages.preview') }}</a>
                    </div>
                    <div class="user-status" style="margin-top: 18px;">
                        @if($owner)
                            <a class="user-status-avatar" href="{{ route('profile.show', $owner->username) }}">
                                <div class="user-avatar small no-outline {{ $owner->isOnline() ? 'online' : 'offline' }}">
                                    <div class="user-avatar-content"><div class="hexagon-image-30-32" data-src="{{ $ownerAvatar }}"></div></div>
                                    <div class="user-avatar-progress-border"><div class="hexagon-border-40-44"></div></div>
                                </div>
                            </a>
                        @endif
                        <p class="user-status-title medium">@if($owner)<a class="bold" href="{{ route('profile.show', $owner->username) }}">{{ $owner->username }}</a>@else{{ __('messages.unknown') }}@endif</p>
                        <p class="user-status-text small">{{ __('messages.seller') }}</p>
                    </div>
                    <div class="kb-aside-card__meta">
                        <div class="kb-meta-row"><span>{{ __('messages.seller') }}</span><strong>{{ $owner ? $owner->username : __('messages.unknown') }}</strong></div>
                        @if($currentArticle)
                            <div class="kb-meta-row"><span>{{ __('messages.publisher') }}</span><strong>{{ $articleAuthor ? $articleAuthor->username : __('messages.guest') }}</strong></div>
                            @if(isset($readingTime))
                                <div class="kb-meta-row"><span>{{ __('messages.reading_time') ?? 'Reading Time' }}</span><strong>{{ $readingTime }} min</strong></div>
                            @endif
                        @endif
                        <div class="kb-meta-row"><span>{{ __('messages.topics') }}</span><strong>{{ $articleTotal ?? 0 }}</strong></div>
                        <div class="kb-meta-row"><span>{{ __('messages.pending') }}</span><strong>{{ $pendingTotal ?? 0 }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($mode === 'list')
        <!-- Live AJAX Search & Create Topic Bar -->
        <div class="kb-search-container">
            <div class="kb-search-bar">
                <i class="fa fa-search text-muted" aria-hidden="true"></i>
                <input
                    type="text"
                    id="kb-topics-search"
                    placeholder="{{ __('messages.kb_search_product_placeholder') }}"
                    autocomplete="off"
                    value="{{ $searchQuery ?? '' }}"
                >
                <a href="{{ $newTopicUrl }}" class="button small secondary">
                    <i class="fa fa-plus"></i>&nbsp;{{ __('messages.add') }} {{ __('messages.topic') }}
                </a>
            </div>
            <div class="kb-search-dropdown" id="kb-topics-search-results"></div>
        </div>

        @if($articles->isEmpty() && !$selectedCategory && empty($searchQuery))
            <div class="kb-empty-state">{{ __('messages.no_post') }}</div>
        @else
            @if($kbCategories->isNotEmpty())
                <div class="widget-box kb-helper-card" style="padding: 16px 20px;">
                    <div class="kb-category-filter">
                        <span class="kb-category-filter__label"><i class="fa fa-filter" aria-hidden="true"></i>&nbsp;{{ __('messages.kb_filter_by_category') }}:</span>
                        <div class="kb-category-filter__pills">
                            <a class="kb-category-pill {{ !$selectedCategory ? 'active' : '' }}" href="{{ route('kb.index', $product->name) }}">{{ __('messages.kb_all_categories') }}</a>
                            @foreach($kbCategories as $cat)
                                <a class="kb-category-pill {{ $selectedCategory == $cat->id ? 'active' : '' }}" href="{{ route('kb.index', $product->name) }}?category={{ $cat->id }}">{{ $cat->name }}</a>
                            @endforeach
                            <a class="kb-category-pill {{ $selectedCategory === 'uncategorized' ? 'active' : '' }}" href="{{ route('kb.index', $product->name) }}?category=uncategorized">{{ __('messages.kb_no_category') }}</a>
                        </div>
                    </div>
                </div>
            @endif

            @if($articles->isEmpty())
                <div class="kb-empty-state">{{ __('messages.no_post') }}</div>
            @else
            <div class="kb-topic-grid" id="kb-topics-grid">
                @foreach($articles as $item)
                    @php
                        $pending = $pendingCounts[$item->name] ?? 0;
                        $cardAuthor = ((int) $item->o_parent > 0) ? ($articleAuthors[$item->o_parent] ?? null) : null;
                        $canManageTopic = auth()->check() && (auth()->id() == $product->o_parent || auth()->user()->isAdmin() || auth()->id() == $item->o_parent);
                        $reportKey = 'kbtopic' . $item->id;
                    @endphp
                    <article class="widget-box kb-topic-card" data-topic-title="{{ strtolower($item->name) }}">
                        <div class="kb-topic-card__header">
                            <div>
                                <p class="kb-topic-card__label">#{{ $item->id }}</p>
                                <h3 class="kb-topic-card__title"><a href="{{ route('kb.show', ['name' => $product->name, 'article' => $item->name]) }}">{{ $item->name }}</a></h3>
                            </div>
                            <div class="kb-action-menu" data-activity-menu-wrap>
                                <button type="button" class="kb-action-menu__trigger" data-activity-menu-trigger data-activity-menu-type="actions" aria-expanded="false"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                                <div class="simple-dropdown kb-action-menu__panel" data-activity-menu-panel>
                                    <a class="simple-dropdown-link" href="{{ route('kb.show', ['name' => $product->name, 'article' => $item->name]) }}"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;{{ __('messages.preview') }}</a>
                                    <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="navigator.clipboard.writeText('{{ route('kb.show', ['name' => $product->name, 'article' => $item->name]) }}'); alert('{{ __('messages.link_copied') }}');"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}</button>
                                    @if(auth()->check() && !$canManageTopic)
                                        <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="reportPost({{ $item->id }}, 205, '{{ $reportKey }}')"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_topic') }}</button>
                                        @if($cardAuthor && auth()->id() != $cardAuthor->id)
                                            <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="reportUser({{ $cardAuthor->id }}, '{{ $reportKey }}')"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_publisher') }}</button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                        <p class="kb-topic-card__summary">{{ \Illuminate\Support\Str::limit(strip_tags((string) $item->o_valuer), 180) }}</p>
                        <div class="kb-topic-card__meta">
                            <span class="kb-pill">{{ __('messages.author') }}: {{ $cardAuthor ? $cardAuthor->username : __('messages.guest') }}</span>
                            <span class="kb-pill">{{ __('messages.pending') }}: <strong>{{ $pending }}</strong></span>
                            @php
                                $itemCategoryName = null;
                                try {
                                    if ($item->relationLoaded('kbCategory') && $item->kbCategory) {
                                        $itemCategoryName = $item->kbCategory->name;
                                    } elseif (!empty($item->kb_category_id)) {
                                        $itemCategoryName = $item->kbCategory ? $item->kbCategory->name : null;
                                    }
                                } catch (\Throwable $e) {
                                    $itemCategoryName = null;
                                }
                            @endphp
                            @if($itemCategoryName)
                                <span class="kb-pill kb-pill--category"><i class="fa fa-folder-o" aria-hidden="true"></i>&nbsp;{{ $itemCategoryName }}</span>
                            @endif
                            @if(!empty($item->updated_at) && $item->updated_at !== '0000-00-00 00:00:00')
                                @php
                                    try {
                                        $itemUpdatedAt = \Carbon\Carbon::parse($item->updated_at)->diffForHumans();
                                    } catch (\Throwable $e) {
                                        $itemUpdatedAt = null;
                                    }
                                @endphp
                                @if($itemUpdatedAt)
                                    <span class="kb-pill"><i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;{{ __('messages.kb_last_modified') }}: {{ $itemUpdatedAt }}</span>
                                @endif
                            @endif
                        </div>
                        <div id="report{{ $reportKey }}" class="store-inline-report"></div>
                        <div class="kb-topic-card__footer">
                            <a class="button secondary" href="{{ route('kb.show', ['name' => $product->name, 'article' => $item->name]) }}"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;{{ __('messages.preview') }}</a>
                        </div>
                    </article>
                @endforeach
            </div>
            @if(method_exists($articles, 'hasPages') && $articles->hasPages())
                <div class="kb-pagination">
                    {{ $articles->links('pagination::bootstrap-5') }}
                </div>
            @endif
            @endif
        @endif
    @endif

    @if($mode === 'create' || $mode === 'edit')
        <div class="kb-editor-layout">
            <div class="widget-box kb-form-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <p class="widget-box-title mb-0">{{ $mode === 'edit' ? ($canManageCurrentArticle ? __('messages.edit_topic') : __('messages.suggest_edit')) : __('messages.add') . ' ' . __('messages.topic') }}</p>
                    <div class="kb-editor-tabs">
                        <button type="button" class="kb-editor-tab-btn active" id="tab-write"><i class="fa fa-pencil"></i> {{ __('messages.kb_write_tab') }}</button>
                        <button type="button" class="kb-editor-tab-btn" id="tab-split"><i class="fa fa-columns"></i> {{ __('messages.kb_split_tab') }}</button>
                        <button type="button" class="kb-editor-tab-btn" id="tab-preview"><i class="fa fa-eye"></i> {{ __('messages.kb_preview_tab') }}</button>
                    </div>
                </div>
                <div class="widget-box-content">
                    @if(session('kb_error'))
                        <div class="alert alert-danger">{{ session('kb_error') }}</div>
                    @endif
                    <form method="POST" action="{{ route('kb.store') }}" id="kb-article-form">
                        @csrf
                        <input type="hidden" name="store" value="{{ $product->name }}">
                        @if($mode === 'create')
                            <div class="form-row">
                                <div class="form-item">
                                    <div class="form-input">
                                        <input type="text" name="name" value="{{ $articleName ?? '' }}" placeholder="{{ __('messages.name') }}" required>
                                    </div>
                                </div>
                            </div>
                        @else
                            <input type="hidden" name="name" value="{{ $articleName }}">
                            <div class="form-row">
                                <div class="form-item">
                                    <div class="form-input">
                                        <input type="text" value="{{ $articleName }}" readonly>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if(isset($kbCategories) && $kbCategories->isNotEmpty())
                            <div class="form-row">
                                <div class="form-item">
                                    <label style="font-size: 13px; font-weight: 600; margin-bottom: 6px; display: block; color: var(--store-shell-muted);">{{ __('messages.kb_category') }} <small style="font-weight: 400;">({{ __('messages.optional') }})</small></label>
                                    <div class="form-select" style="position:relative;">
                                        <select name="kb_category_id" class="form-control" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--store-shell-border, rgba(143,145,172,0.2)); background: var(--store-shell-input-bg, #fff); color: var(--store-shell-body); font-size: 14px;">
                                            <option value="">{{ __('messages.kb_no_category') }}</option>
                                            @foreach($kbCategories as $cat)
                                                <option value="{{ $cat->id }}" {{ old('kb_category_id', ($article->kb_category_id ?? null)) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="form-row" id="kb-editor-row">
                            <div class="form-item" id="kb-editor-col" style="flex: 1;">
                                <div class="form-input">
                                    <textarea id="kb-editor" name="txt" rows="16" required>{{ old('txt', $editorText ?? '') }}</textarea>
                                </div>
                            </div>
                            <div class="form-item" id="kb-live-preview-col" style="flex: 1; display: none;">
                                <div class="kb-article-body markdown-content-preview px-3 py-2" id="kb-live-preview-box" style="border: 1px solid var(--store-shell-border); border-radius: 12px; min-height: 380px; max-height: 520px; overflow-y: auto; background: var(--store-shell-surface);">
                                </div>
                            </div>
                        </div>
                        <div class="form-row split">
                            <div class="form-item"><img src="{{ route('kb.captcha') }}" id="kb-captcha" alt="captcha" style="height: 30px; cursor: pointer;"></div>
                            <div class="form-item"><div class="form-input"><input type="text" name="capt" required></div></div>
                        </div>
                        @if($mode === 'create' && auth()->check())
                            <div class="form-row">
                                <div class="form-item">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="kb-share-to-community" name="share_to_community" value="1" {{ old('share_to_community') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="kb-share-to-community">{{ __('messages.share_to_community') }}</label>
                                    </div>
                                    <p style="margin: 10px 0 0; font-size: 12px; color: var(--store-shell-muted);">{{ __('messages.knowledgebase_share_to_community_hint') }}</p>
                                </div>
                            </div>
                        @endif
                        <div class="form-row">
                            <div class="form-item"><button class="button primary" type="submit">{{ __('messages.save') }}</button></div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="widget-box kb-helper-card">
                <p class="widget-box-title">{{ __('messages.details') }}</p>
                <div class="widget-box-content">
                    <div class="kb-aside-card__meta">
                        <div class="kb-meta-row"><span>{{ __('messages.seller') }}</span><strong>{{ $owner ? $owner->username : __('messages.unknown') }}</strong></div>
                        <div class="kb-meta-row"><span>{{ __('messages.topics') }}</span><strong>{{ $articleTotal ?? 0 }}</strong></div>
                        <div class="kb-meta-row"><span>{{ __('messages.pending') }}</span><strong>{{ $pendingTotal ?? 0 }}</strong></div>
                    </div>
                    <div class="kb-side-card__actions">
                        <a class="button secondary" href="{{ route('store.show', $product->name) }}"><i class="fa fa-shopping-basket" aria-hidden="true"></i>&nbsp;{{ __('messages.preview') }}</a>
                        <a class="button tertiary" href="{{ route('kb.index', $product->name) }}"><i class="fa fa-list-ul" aria-hidden="true"></i>&nbsp;{{ __('messages.topics') }}</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($mode === 'show')
        @php
            $reportKey = 'kbcurrent' . $article->id;
            $canReportTopic = auth()->check() && !$canManageCurrentArticle;
            $canReportPublisher = $canReportTopic && $articleAuthor && auth()->id() != $articleAuthor->id;
        @endphp
        <div class="kb-topic-layout">
            <div class="widget-box kb-main-card">
                <div class="widget-box-content">
                    <div class="kb-main-card__header">
                        <div>
                            <p class="kb-topic-card__label">{{ __('messages.topic') }}</p>
                            <h3 class="widget-box-title">{{ $article->name }}</h3>
                            <p class="kb-main-card__subtitle">
                                {{ __('messages.current') }} #{{ $article->id }}
                                @if($articleAuthor)
                                    &nbsp;|&nbsp;{{ __('messages.publisher') }}: {{ $articleAuthor->username }}
                                @else
                                    &nbsp;|&nbsp;{{ __('messages.publisher') }}: {{ __('messages.guest') }}
                                @endif
                                @if(isset($readingTime))
                                    &nbsp;|&nbsp;<i class="fa fa-clock-o"></i> {{ $readingTime }} min
                                @endif
                            </p>
                        </div>
                        <div class="kb-action-menu" data-activity-menu-wrap>
                            <button type="button" class="kb-action-menu__trigger" data-activity-menu-trigger data-activity-menu-type="actions" aria-expanded="false"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></button>
                            <div class="simple-dropdown kb-action-menu__panel" data-activity-menu-panel>
                                @auth
                                    @if($knowledgebaseCommunityPublishAction && $knowledgebaseCommunityPublishPayload)
                                        <form method="POST" action="{{ $knowledgebaseCommunityPublishAction }}">
                                            @csrf
                                            <input type="hidden" name="store" value="{{ $knowledgebaseCommunityPublishPayload['store'] }}">
                                            <input type="hidden" name="article" value="{{ $knowledgebaseCommunityPublishPayload['article'] }}">
                                            <button type="submit" class="simple-dropdown-link store-dropdown-button"><i class="fa fa-bullhorn" aria-hidden="true"></i>&nbsp;{{ __('messages.share_to_community') }}</button>
                                        </form>
                                    @endif
                                @endauth
                                <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="navigator.clipboard.writeText('{{ route('kb.show', ['name' => $product->name, 'article' => $article->name]) }}'); alert('{{ __('messages.link_copied') }}');"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}</button>
                                <a class="simple-dropdown-link" href="{{ route('store.show', $product->name) }}"><i class="fa fa-shopping-basket" aria-hidden="true"></i>&nbsp;{{ __('messages.preview') }}</a>
                                @if($canReportTopic)
                                    <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="reportPost({{ $article->id }}, 205, '{{ $reportKey }}')"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_topic') }}</button>
                                @endif
                                @if($canReportPublisher)
                                    <button type="button" class="simple-dropdown-link store-dropdown-button" onclick="reportUser({{ $articleAuthor->id }}, '{{ $reportKey }}')"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_publisher') }}</button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Article Body with Safe Template Hydration to prevent XSS & blank screen -->
                    <div class="kb-article-body markdown-content" id="kb-content-{{ $article->id }}" data-rendered="false">
                        <template class="kb-source-markdown">{!! htmlspecialchars($processedContent ?? $article->o_valuer, ENT_NOQUOTES, 'UTF-8') !!}</template>
                        <div class="kb-skeleton-loader" style="padding: 10px 0;">
                            <div style="height: 20px; background: rgba(143,145,172,0.1); border-radius: 6px; width: 45%; margin-bottom: 14px;"></div>
                            <div style="height: 14px; background: rgba(143,145,172,0.08); border-radius: 6px; width: 90%; margin-bottom: 8px;"></div>
                            <div style="height: 14px; background: rgba(143,145,172,0.08); border-radius: 6px; width: 80%; margin-bottom: 8px;"></div>
                        </div>
                    </div>

                    <!-- Was This Helpful? Feedback Widget -->
                    <div class="kb-feedback-widget" id="kb-feedback-widget">
                        <span class="kb-feedback-label">{{ __('messages.kb_was_helpful') }}</span>
                        <div class="kb-feedback-btns">
                            <button type="button" class="kb-feedback-btn {{ ($userVote ?? 0) == 1 ? 'active-up' : '' }}" id="kb-vote-up" data-vote="up">
                                <i class="fa fa-thumbs-up"></i> <span>{{ __('messages.kb_helpful_yes') }}</span> (<strong id="kb-up-val">{{ $upVotes ?? 0 }}</strong>)
                            </button>
                            <button type="button" class="kb-feedback-btn {{ ($userVote ?? 0) == -1 ? 'active-down' : '' }}" id="kb-vote-down" data-vote="down">
                                <i class="fa fa-thumbs-down"></i> <span>{{ __('messages.kb_helpful_no') }}</span> (<strong id="kb-down-val">{{ $downVotes ?? 0 }}</strong>)
                            </button>
                        </div>
                    </div>

                    <!-- Previous & Next Sibling Articles Navigation -->
                    @if(isset($prevArticle) || isset($nextArticle))
                        <div class="kb-sibling-nav">
                            @if($prevArticle)
                                <a href="{{ route('kb.show', ['name' => $product->name, 'article' => $prevArticle->name]) }}" class="kb-sibling-card">
                                    <span class="kb-sibling-label"><i class="fa fa-arrow-left"></i> {{ __('messages.previous') ?? 'Previous' }}</span>
                                    <span class="kb-sibling-title">{{ $prevArticle->name }}</span>
                                </a>
                            @endif
                            @if($nextArticle)
                                <a href="{{ route('kb.show', ['name' => $product->name, 'article' => $nextArticle->name]) }}" class="kb-sibling-card" style="text-align: right;">
                                    <span class="kb-sibling-label">{{ __('messages.next') ?? 'Next' }} <i class="fa fa-arrow-right"></i></span>
                                    <span class="kb-sibling-title">{{ $nextArticle->name }}</span>
                                </a>
                            @endif
                        </div>
                    @endif

                    <div id="report{{ $reportKey }}" class="store-inline-report"></div>
                </div>
            </div>

            <!-- Sidebar Actions & Table of Contents -->
            <div class="kb-aside">
                <!-- Sticky Table of Contents Card -->
                <div class="kb-toc-box" id="kb-toc-container" style="display: none;">
                    <div class="kb-toc-title"><i class="fa fa-list-ol"></i> {{ __('messages.kb_toc_title') }}</div>
                    <ul class="kb-toc-list" id="kb-toc-nav"></ul>
                </div>

                <div class="widget-box kb-side-card">
                    <p class="widget-box-title">{{ __('messages.actions') }}</p>
                    <div class="widget-box-content">
                        <div class="kb-aside-card__meta">
                            <div class="kb-meta-row"><span>{{ __('messages.pending') }}</span><strong>{{ $currentTopicPendingCount }}</strong></div>
                            <div class="kb-meta-row"><span>{{ __('messages.publisher') }}</span><strong>{{ $articleAuthor ? $articleAuthor->username : __('messages.guest') }}</strong></div>
                        </div>
                        <div class="kb-side-card__actions">
                            @auth
                                @if($knowledgebaseCommunityPublishAction && $knowledgebaseCommunityPublishPayload)
                                    <form method="POST" action="{{ $knowledgebaseCommunityPublishAction }}" style="width: 100%;">
                                        @csrf
                                        <input type="hidden" name="store" value="{{ $knowledgebaseCommunityPublishPayload['store'] }}">
                                        <input type="hidden" name="article" value="{{ $knowledgebaseCommunityPublishPayload['article'] }}">
                                        <button class="button primary" type="submit" style="width: 100%;"><i class="fa fa-bullhorn" aria-hidden="true"></i>&nbsp;{{ __('messages.share_to_community') }}</button>
                                    </form>
                                @endif
                            @endauth
                            <a class="button secondary" href="{{ route('kb.edit', ['name' => $product->name, 'article' => $article->name]) }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;{{ $canManageCurrentArticle ? __('messages.edit_topic') : __('messages.suggest_edit') }}</a>
                            <a class="button tertiary" href="{{ route('kb.pending', ['name' => $product->name, 'article' => $article->name]) }}"><i class="fa fa-hourglass-half" aria-hidden="true"></i>&nbsp;{{ __('messages.pending') }}</a>
                            <a class="button tertiary" href="{{ route('kb.history', ['name' => $product->name, 'article' => $article->name]) }}"><i class="fa fa-history" aria-hidden="true"></i>&nbsp;{{ __('messages.history') }}</a>
                            <a class="button white" href="{{ route('store.show', $product->name) }}"><i class="fa fa-shopping-basket" aria-hidden="true"></i>&nbsp;{{ $product->name }}</a>
                        </div>
                        @if($knowledgebaseExternalShareUrl)
                            <div style="margin-top: 18px; border-top: 1px solid var(--store-shell-border, rgba(143, 145, 172, 0.18)); padding-top: 18px;">
                                <p style="margin: 0 0 12px; font-size: 12px; font-weight: 700; color: var(--store-shell-muted); text-transform: uppercase; letter-spacing: 0.08em;">{{ __('messages.share_externally') }}</p>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($knowledgebaseSocialPlatforms as $social)
                                        <a
                                            class="button white small"
                                            href="javascript:void(0);"
                                            style="display: inline-flex; align-items: center; gap: 8px;"
                                            onclick="sharePost('{{ $social }}', {{ Illuminate\Support\Js::from($knowledgebaseExternalShareUrl) }}, {{ Illuminate\Support\Js::from($knowledgebaseExternalShareTitle) }}); return false;"
                                        >
                                            <img
                                                src="{{ theme_asset('img/icons/' . $social . '-icon.png') }}"
                                                alt="{{ __('messages.' . $social) }}"
                                                style="width: 16px; height: 16px; object-fit: contain;"
                                            >
                                            <span>{{ __('messages.' . $social) }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($mode === 'pending' || $mode === 'history')
        @php
            $reportKey = 'kbcurrent' . $article->id;
            $canReportTopic = auth()->check() && !$canManageCurrentArticle;
            $canReportPublisher = $canReportTopic && $articleAuthor && auth()->id() != $articleAuthor->id;
        @endphp
        <div class="kb-review-layout">
            <!-- Sidebar Area (Narrow - Column 1) -->
            <div class="kb-review-sidebar-area">
                <div class="widget-box kb-main-card" style="margin-bottom: 24px;">
                    <div class="widget-box-content">
                        <div class="kb-main-card__header">
                            <div>
                                <p class="kb-topic-card__label">{{ __('messages.current') }}</p>
                                <h3 class="widget-box-title" style="font-size: 1.1rem;">{{ $article->name }}</h3>
                                <p class="kb-main-card__subtitle">
                                    #{{ $article->id }}
                                    @if($articleAuthor)
                                        <br>{{ __('messages.publisher') }}: {{ $articleAuthor->username }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="widget-box kb-side-card kb-review-card__table">
                    <p class="widget-box-title">{{ $mode === 'pending' ? __('messages.pending') : __('messages.history') }}</p>
                    <div class="widget-box-content">
                        @if($entries->isEmpty())
                            <div class="kb-empty-state">{{ __('messages.no_post') }}</div>
                        @else
                            <form method="POST" action="{{ route('kb.approve') }}" id="kb-review-form">
                                @csrf
                                <input type="hidden" name="store" value="{{ $product->name }}">
                                <input type="hidden" name="article" value="{{ $article->name }}">
                                <div class="table-responsive">
                                    <table id="tablepagination" class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#ID</th>
                                                <th>{{ __('messages.topic') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($entries as $entry)
                                                <tr>
                                                    <td><input type="radio" name="entry" value="{{ $entry->id }}" required> #{{ $entry->id }}</td>
                                                    <td>
                                                        <div class="d-flex flex-column gap-2">
                                                            <div class="d-flex gap-1 align-items-center">
                                                                <button type="button" class="button secondary small preview-entry-btn w-100" 
                                                                        data-entry-id="{{ $entry->id }}" 
                                                                        data-entry-title="{{ $entry->name }} (#{{ $entry->id }})">
                                                                    <i class="fa fa-eye"></i>&nbsp;{{ __('messages.preview') }}
                                                                </button>
                                                                <button type="button" class="button tertiary small diff-entry-btn" 
                                                                        data-entry-id="{{ $entry->id }}" 
                                                                        title="{{ __('messages.kb_view_diff') }}">
                                                                    <i class="fa fa-exchange"></i>
                                                                </button>
                                                                <script type="text/template" id="entry-raw-{{ $entry->id }}">{!! $entry->o_valuer !!}</script>
                                                            </div>
                                                            <span style="font-size: 10px; color: var(--store-shell-muted);">{{ \Illuminate\Support\Str::limit(strip_tags((string)$entry->o_valuer), 40) }}</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($isAuthorized)
                                    <div class="mt-3 d-flex flex-column gap-2">
                                        <button type="submit" class="button primary w-100" onclick="return confirm('{{ __('messages.kb_approve_confirm') }}')">
                                            <i class="fa fa-check"></i> {{ $mode === 'pending' ? __('messages.kb_approve_suggestion') : __('messages.recovery') }}
                                        </button>
                                        @if($mode === 'pending')
                                            <button type="button" class="button tertiary w-100" id="kb-reject-btn" style="color: #ef4444;">
                                                <i class="fa fa-times"></i> {{ __('messages.kb_reject_suggestion') }}
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </form>
                        @endif
                        <div class="kb-side-card__actions mt-3">
                            <a class="button tertiary" href="{{ route('kb.edit', ['name' => $product->name, 'article' => $article->name]) }}"><i class="fa fa-pencil-square-o"></i>&nbsp;{{ $canManageCurrentArticle ? __('messages.edit_topic') : __('messages.suggest_edit') }}</a>
                            <a class="button white" href="{{ route('kb.show', ['name' => $product->name, 'article' => $article->name]) }}"><i class="fa fa-book"></i>&nbsp;{{ __('messages.topic') }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area (Wide - Column 2 - AJAX Preview & Visual Diff) -->
            <div class="kb-review-content-area">
                <div id="ajax-preview-card" class="widget-box kb-main-card">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h3 class="widget-box-title mb-0" id="ajax-preview-title">{{ __('messages.preview') }}</h3>
                        <div class="btn-group" role="group">
                            <button type="button" class="button small secondary active" id="view-mode-preview"><i class="fa fa-eye"></i> {{ __('messages.preview') }}</button>
                            <button type="button" class="button small tertiary" id="view-mode-diff"><i class="fa fa-exchange"></i> {{ __('messages.kb_visual_diff') }}</button>
                        </div>
                    </div>
                    <div class="widget-box-content">
                        <!-- Rendered Markdown View -->
                        <div id="kb-ajax-preview-content" class="kb-article-body markdown-content-preview px-2">
                            <div class="kb-empty-state" style="border: 0;">{{ __('messages.preview') }}</div>
                        </div>

                        <!-- Visual Diff View (Hidden by default) -->
                        <div id="kb-ajax-diff-container" class="kb-diff-panel" style="display: none;">
                            <div class="kb-diff-header">
                                <span><i class="fa fa-exchange"></i> {{ __('messages.kb_visual_diff') }}</span>
                                <span id="kb-diff-stat-badge"></span>
                            </div>
                            <div id="kb-diff-output" style="padding: 8px 0; max-height: 500px; overflow-y: auto;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Raw template of the current article for client diff comparison -->
@if(isset($article))
    <script type="text/template" id="current-article-raw">{!! $article->o_valuer !!}</script>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify/dist/purify.min.js"></script>
<script src="https://unpkg.com/stackedit-js@1.0.7/docs/lib/stackedit.min.js"></script>
<script>
(function() {
    function initKB() {
        // Safe Markdown Rendering
        const renderMarkdown = () => {
            document.querySelectorAll('.markdown-content').forEach(el => {
                if (el.getAttribute('data-rendered') !== 'true') {
                    try {
                        const template = el.querySelector('.kb-source-markdown');
                        const rawText = template ? template.innerHTML : (el.innerText || el.innerHTML);
                        const cleanHtml = DOMPurify.sanitize(marked.parse(rawText));
                        el.innerHTML = cleanHtml;
                        el.setAttribute('data-rendered', 'true');
                        el.style.display = 'block';

                        if (window.enhanceSuperdesignKbContent) {
                            window.enhanceSuperdesignKbContent(el);
                        }

                        // Generate Table of Contents (TOC)
                        generateTOC(el);
                    } catch (e) {
                        console.error('Error rendering markdown:', e);
                    }
                }
            });
        };
        renderMarkdown();

        // Table of Contents Generator
        function generateTOC(contentEl) {
            const headings = contentEl.querySelectorAll('h2, h3');
            const tocContainer = document.getElementById('kb-toc-container');
            const tocNav = document.getElementById('kb-toc-nav');

            if (!headings || headings.length < 2 || !tocContainer || !tocNav) return;

            tocNav.innerHTML = '';
            headings.forEach((h, idx) => {
                const id = 'section-' + idx;
                h.id = id;
                const li = document.createElement('li');
                const link = document.createElement('a');
                link.className = 'kb-toc-link ' + (h.tagName.toLowerCase() === 'h3' ? 'level-3' : 'level-2');
                link.href = '#' + id;
                link.innerText = h.innerText;
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    h.scrollIntoView({ behavior: 'smooth' });
                });
                li.appendChild(link);
                tocNav.appendChild(li);
            });
            tocContainer.style.display = 'block';

            // Active section scrollspy
            window.addEventListener('scroll', () => {
                let current = '';
                headings.forEach(h => {
                    const top = h.getBoundingClientRect().top;
                    if (top < 150) current = h.id;
                });
                document.querySelectorAll('.kb-toc-link').forEach(link => {
                    link.classList.toggle('is-active', link.getAttribute('href') === '#' + current);
                });
            }, { passive: true });
        }

        // Helpful Feedback AJAX Voting
        const upBtn = document.getElementById('kb-vote-up');
        const downBtn = document.getElementById('kb-vote-down');
        if (upBtn && downBtn) {
            const castVote = (vote) => {
                fetch('{{ route('kb.feedback', $product->name) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        article_id: {{ $currentArticle->id ?? 0 }},
                        vote: vote
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('kb-up-val').innerText = data.up;
                        document.getElementById('kb-down-val').innerText = data.down;
                        if (vote === 'up') {
                            upBtn.classList.add('active-up');
                            downBtn.classList.remove('active-down');
                        } else {
                            downBtn.classList.add('active-down');
                            upBtn.classList.remove('active-up');
                        }
                    }
                })
                .catch(err => console.error('Feedback error', err));
            };

            upBtn.addEventListener('click', () => castVote('up'));
            downBtn.addEventListener('click', () => castVote('down'));
        }

        // Live In-Page Markdown Editor Tabs (Write / Split / Preview)
        const tabWrite = document.getElementById('tab-write');
        const tabSplit = document.getElementById('tab-split');
        const tabPreview = document.getElementById('tab-preview');
        const editorCol = document.getElementById('kb-editor-col');
        const previewCol = document.getElementById('kb-live-preview-col');
        const previewBox = document.getElementById('kb-live-preview-box');
        const textarea = document.getElementById('kb-editor');

        const updateLivePreview = () => {
            if (textarea && previewBox && typeof marked !== 'undefined') {
                previewBox.innerHTML = DOMPurify.sanitize(marked.parse(textarea.value || ''));
                if (window.enhanceSuperdesignKbContent) {
                    window.enhanceSuperdesignKbContent(previewBox);
                }
            }
        };

        if (tabWrite && tabSplit && tabPreview && textarea) {
            textarea.addEventListener('input', () => {
                if (previewCol.style.display !== 'none') {
                    updateLivePreview();
                }
            });

            tabWrite.addEventListener('click', () => {
                tabWrite.classList.add('active');
                tabSplit.classList.remove('active');
                tabPreview.classList.remove('active');
                editorCol.style.display = 'block';
                previewCol.style.display = 'none';
            });

            tabSplit.addEventListener('click', () => {
                tabSplit.classList.add('active');
                tabWrite.classList.remove('active');
                tabPreview.classList.remove('active');
                editorCol.style.display = 'block';
                previewCol.style.display = 'block';
                updateLivePreview();
            });

            tabPreview.addEventListener('click', () => {
                tabPreview.classList.add('active');
                tabWrite.classList.remove('active');
                tabSplit.classList.remove('active');
                editorCol.style.display = 'none';
                previewCol.style.display = 'block';
                updateLivePreview();
            });

            if (window.initKbSnippetsToolbar) {
                window.initKbSnippetsToolbar('kb-editor');
            }

            if (typeof Stackedit !== 'undefined') {
                const stackedit = new Stackedit();
                const editorWrapper = document.createElement('div');
                editorWrapper.className = 'stackedit-tools mb-3';
                editorWrapper.innerHTML = `
                    <button type="button" class="button secondary small" id="open-stackedit">
                        <i class="fa fa-pencil-square" aria-hidden="true"></i>&nbsp;{{ __('messages.edit_with_stackedit') ?? 'Edit with StackEdit' }}
                    </button>
                `;
                textarea.parentNode.insertBefore(editorWrapper, textarea);

                document.getElementById('open-stackedit').addEventListener('click', () => {
                    stackedit.openFile({
                        name: '{{ $articleName ?? $shellTitle }}',
                        content: { text: textarea.value }
                    });
                });

                stackedit.on('fileChange', (file) => {
                    textarea.value = file.content.text;
                    updateLivePreview();
                });
            }
        }

        // Live Instant Search in Topics List
        const topicsSearchInput = document.getElementById('kb-topics-search');
        const topicsGrid = document.getElementById('kb-topics-grid');
        const topicsSearchDropdown = document.getElementById('kb-topics-search-results');
        let searchTimer = null;

        if (topicsSearchInput) {
            topicsSearchInput.addEventListener('input', function() {
                const q = this.value.trim().toLowerCase();
                clearTimeout(searchTimer);

                // In-place instant card filtering
                if (topicsGrid) {
                    const cards = topicsGrid.querySelectorAll('.kb-topic-card');
                    cards.forEach(card => {
                        const title = card.getAttribute('data-topic-title') || '';
                        card.style.display = (q === '' || title.includes(q)) ? '' : 'none';
                    });
                }

                // AJAX search for deeper full-text matches
                if (q.length >= 2 && topicsSearchDropdown) {
                    searchTimer = setTimeout(() => {
                        fetch(`{{ route('kb.search', $product->name) }}?q=${encodeURIComponent(q)}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (res.articles && res.articles.length > 0) {
                                let html = '';
                                res.articles.forEach(art => {
                                    html += `
                                        <a href="${art.url}" class="kb-search-dropdown-item">
                                            <div class="kb-search-dropdown-title">${art.name}</div>
                                            <div class="kb-search-dropdown-desc">${art.snippet}</div>
                                        </a>
                                    `;
                                });
                                topicsSearchDropdown.innerHTML = html;
                                topicsSearchDropdown.style.display = 'block';
                            } else {
                                topicsSearchDropdown.innerHTML = `<div style="padding: 14px; text-align: center; color: var(--store-shell-muted); font-size: 0.85rem;">{{ __('messages.kb_no_search_results') }}</div>`;
                                topicsSearchDropdown.style.display = 'block';
                            }
                        })
                        .catch(() => { topicsSearchDropdown.style.display = 'none'; });
                    }, 250);
                } else if (topicsSearchDropdown) {
                    topicsSearchDropdown.style.display = 'none';
                }
            });

            document.addEventListener('click', function(e) {
                if (topicsSearchDropdown && !topicsSearchInput.contains(e.target) && !topicsSearchDropdown.contains(e.target)) {
                    topicsSearchDropdown.style.display = 'none';
                }
            });
        }

        // Visual Diff Engine & Pending/History Review Actions
        const currentRawEl = document.getElementById('current-article-raw');
        const currentRawText = currentRawEl ? currentRawEl.innerHTML : '';
        const diffBtn = document.getElementById('view-mode-diff');
        const previewBtn = document.getElementById('view-mode-preview');
        const ajaxPreviewBox = document.getElementById('kb-ajax-preview-content');
        const ajaxDiffBox = document.getElementById('kb-ajax-diff-container');
        const diffOutput = document.getElementById('kb-diff-output');
        const diffStatBadge = document.getElementById('kb-diff-stat-badge');
        let activeEntryRaw = '';

        function computeDiffLines(oldText, newText) {
            const oldLines = oldText.split('\n');
            const newLines = newText.split('\n');
            let html = '';
            let adds = 0, dels = 0;

            const max = Math.max(oldLines.length, newLines.length);
            for (let i = 0; i < max; i++) {
                const ol = oldLines[i];
                const nl = newLines[i];

                if (ol === undefined) {
                    html += `<div class="kb-diff-line kb-diff-line--add">+ ${escapeHtml(nl)}</div>`;
                    adds++;
                } else if (nl === undefined) {
                    html += `<div class="kb-diff-line kb-diff-line--del">- ${escapeHtml(ol)}</div>`;
                    dels++;
                } else if (ol !== nl) {
                    html += `<div class="kb-diff-line kb-diff-line--del">- ${escapeHtml(ol)}</div>`;
                    html += `<div class="kb-diff-line kb-diff-line--add">+ ${escapeHtml(nl)}</div>`;
                    adds++; dels++;
                } else {
                    html += `<div class="kb-diff-line kb-diff-line--ctx">  ${escapeHtml(ol)}</div>`;
                }
            }
            return { html, adds, dels };
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.innerText = str;
            return div.innerHTML;
        }

        document.addEventListener('click', function(e) {
            const previewBtnEl = e.target.closest('.preview-entry-btn');
            const diffBtnEl = e.target.closest('.diff-entry-btn');

            if (previewBtnEl || diffBtnEl) {
                e.preventDefault();
                const btn = previewBtnEl || diffBtnEl;
                const id = btn.getAttribute('data-entry-id');
                const rawTemplate = document.getElementById('entry-raw-' + id);
                if (!rawTemplate) return;

                activeEntryRaw = rawTemplate.innerHTML;
                const title = btn.getAttribute('data-entry-title') || ('#' + id);

                document.getElementById('ajax-preview-title').innerText = title;
                ajaxPreviewBox.innerHTML = DOMPurify.sanitize(marked.parse(activeEntryRaw));
                if (window.enhanceSuperdesignKbContent) {
                    window.enhanceSuperdesignKbContent(ajaxPreviewBox);
                }

                const diff = computeDiffLines(currentRawText, activeEntryRaw);
                diffOutput.innerHTML = diff.html;
                if (diffStatBadge) {
                    diffStatBadge.innerHTML = `<span style="color:#34d399;">+${diff.adds}</span> <span style="color:#f87171;">-${diff.dels}</span>`;
                }

                if (diffBtnEl) {
                    diffBtn.click();
                } else {
                    previewBtn.click();
                }
            }
        });

        if (diffBtn && previewBtn && ajaxPreviewBox && ajaxDiffBox) {
            diffBtn.addEventListener('click', () => {
                diffBtn.classList.add('active');
                diffBtn.classList.remove('tertiary');
                diffBtn.classList.add('secondary');
                previewBtn.classList.remove('active');
                previewBtn.classList.remove('secondary');
                previewBtn.classList.add('tertiary');
                ajaxPreviewBox.style.display = 'none';
                ajaxDiffBox.style.display = 'block';
            });

            previewBtn.addEventListener('click', () => {
                previewBtn.classList.add('active');
                previewBtn.classList.remove('tertiary');
                previewBtn.classList.add('secondary');
                diffBtn.classList.remove('active');
                diffBtn.classList.remove('secondary');
                diffBtn.classList.add('tertiary');
                ajaxPreviewBox.style.display = 'block';
                ajaxDiffBox.style.display = 'none';
            });
        }

        // Reject pending revision button
        const rejectBtn = document.getElementById('kb-reject-btn');
        if (rejectBtn) {
            rejectBtn.addEventListener('click', () => {
                const checked = document.querySelector('input[name="entry"]:checked');
                if (!checked) {
                    alert('{{ __('messages.please_select_an_entry') ?? 'Please select an entry.' }}');
                    return;
                }
                if (!confirm('{{ __('messages.kb_reject_confirm') }}')) return;

                fetch('{{ route('kb.reject') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        store: '{{ $product->name }}',
                        article: '{{ $article->name ?? '' }}',
                        entry: checked.value
                    })
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        location.reload();
                    }
                })
                .catch(() => location.reload());
            });
        }

        // Captcha reload
        const captcha = document.getElementById('kb-captcha');
        if (captcha) {
            captcha.addEventListener('click', function () {
                this.src = '{{ route('kb.captcha') }}?t=' + Date.now();
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initKB);
    } else {
        initKB();
    }
})();
</script>
@endpush
@endsection
