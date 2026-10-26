@extends('theme::layouts.master')

@section('title', $pageTitle ?? (__('support.support_forum') . ' - ' . $siteName))

@section('content')
<div class="content-grid" style="padding-bottom: 80px;">
    <!-- Support Forum Styles -->
    <style>
        .support-hero {
            position: relative;
            background: linear-gradient(135deg, rgba(97, 93, 250, 0.12) 0%, rgba(35, 210, 226, 0.08) 100%);
            border: 1px solid var(--myads-surface-border, rgba(97, 93, 250, 0.15));
            border-radius: var(--myads-radius-xl, 24px);
            padding: 50px 32px 42px;
            text-align: center;
            overflow: hidden;
            margin-bottom: 35px;
            box-shadow: var(--myads-shadow-sm, 0 4px 20px rgba(0,0,0,0.03));
        }

        .support-hero-badge {
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

        .support-hero-title {
            font-size: 2.3rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            margin-bottom: 12px;
            letter-spacing: -0.02em;
            line-height: 1.25;
        }

        .support-hero-subtitle {
            font-size: 1.05rem;
            color: var(--myads-text-muted, #64748b);
            max-width: 680px;
            margin: 0 auto 28px;
            line-height: 1.6;
        }

        .support-quick-nav {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .btn-support-ask {
            background: linear-gradient(135deg, #615dfa 0%, #4e4ac8 100%);
            color: #ffffff !important;
            padding: 12px 28px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 6px 20px rgba(97, 93, 250, 0.35);
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .btn-support-ask:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(97, 93, 250, 0.5);
            color: #ffffff;
        }

        .btn-support-tickets {
            background: var(--myads-surface, #ffffff);
            color: var(--myads-text-main, #1e293b) !important;
            border: 2px solid var(--myads-surface-border, #e2e8f0);
            padding: 12px 26px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .btn-support-tickets:hover {
            border-color: var(--myads-primary, #615dfa);
            color: var(--myads-primary, #615dfa) !important;
            transform: translateY(-2px);
        }

        .support-stats-row {
            display: flex;
            justify-content: center;
            gap: 24px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .support-stat-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--myads-surface, rgba(255,255,255,0.8));
            padding: 7px 18px;
            border-radius: 9999px;
            font-size: 0.88rem;
            font-weight: 700;
            color: var(--myads-text-main, #334155);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
        }

        /* Categories Section */
        .support-section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .support-section-title {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
        }

        .support-categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 45px;
        }

        .support-category-card {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-lg, 18px);
            padding: 24px;
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: var(--myads-text-main, #1e293b);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: var(--myads-shadow-sm, 0 2px 8px rgba(0,0,0,0.03));
        }

        .support-category-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 28px rgba(97, 93, 250, 0.12);
            border-color: var(--cat-color, #615dfa);
            color: var(--myads-text-main, #1e293b);
        }

        .support-category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--cat-color, #615dfa);
        }

        .support-cat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 16px;
            background: rgba(97, 93, 250, 0.1);
            color: var(--cat-color, #615dfa);
        }

        .support-cat-name {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--myads-text-main, #1e293b);
        }

        .support-cat-desc {
            font-size: 0.88rem;
            color: var(--myads-text-muted, #64748b);
            line-height: 1.5;
            margin-bottom: 16px;
            flex-grow: 1;
        }

        .support-cat-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.82rem;
            font-weight: 700;
            color: var(--myads-text-muted, #64748b);
            padding-top: 12px;
            border-top: 1px solid var(--myads-surface-border, #f1f5f9);
        }

        /* Filter Tabs & Search Bar */
        .support-filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
            background: var(--myads-surface, #ffffff);
            padding: 14px 20px;
            border-radius: 16px;
            border: 1px solid var(--myads-surface-border, #e2e8f0);
        }

        .support-filter-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .support-filter-tab {
            padding: 7px 16px;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--myads-text-muted, #64748b);
            background: transparent;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .support-filter-tab.active,
        .support-filter-tab:hover {
            background: var(--myads-primary, #615dfa);
            color: #ffffff !important;
        }

        .support-search-form {
            position: relative;
            width: 100%;
            max-width: 320px;
        }

        .support-search-input {
            width: 100%;
            padding: 8px 16px 8px 36px;
            border-radius: 9999px;
            border: 1px solid var(--myads-surface-border, #cbd5e1);
            background: var(--myads-surface, #ffffff);
            font-size: 0.88rem;
            color: var(--myads-text-main, #1e293b);
            outline: none;
        }

        [dir="rtl"] .support-search-input {
            padding: 8px 36px 8px 16px;
        }

        .support-search-icon {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: var(--myads-text-muted, #94a3b8);
            font-size: 0.85rem;
        }

        [dir="rtl"] .support-search-icon {
            left: auto;
            right: 12px;
        }

        /* Topics Stream */
        .support-topics-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .support-topic-row {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: 16px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            text-decoration: none;
            color: var(--myads-text-main, #1e293b);
            transition: all 0.25s ease;
        }

        .support-topic-row:hover {
            transform: translateY(-2px);
            border-color: var(--myads-primary, #615dfa);
            box-shadow: 0 8px 24px rgba(97, 93, 250, 0.08);
            color: var(--myads-text-main, #1e293b);
        }

        .support-topic-main {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            flex: 1;
            min-width: 0;
        }

        .support-topic-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--myads-surface-border, #e2e8f0);
            flex-shrink: 0;
        }

        .support-topic-meta-box {
            flex: 1;
            min-width: 0;
        }

        .support-topic-badges {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 6px;
        }

        .support-badge-pill {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-solved {
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .badge-unsolved {
            background: rgba(245, 158, 11, 0.12);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        .badge-pinned {
            background: rgba(97, 93, 250, 0.12);
            color: #615dfa;
            border: 1px solid rgba(97, 93, 250, 0.25);
        }

        .badge-category {
            background: rgba(35, 210, 226, 0.12);
            color: #0891b2;
            border: 1px solid rgba(35, 210, 226, 0.25);
        }

        .support-topic-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--myads-text-main, #1e293b);
            margin: 0 0 6px 0;
            line-height: 1.4;
            word-break: break-word;
        }

        .support-topic-author-meta {
            font-size: 0.82rem;
            color: var(--myads-text-muted, #64748b);
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .support-topic-stats {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-shrink: 0;
        }

        .support-counter-box {
            text-align: center;
            padding: 8px 14px;
            background: rgba(97, 93, 250, 0.05);
            border-radius: 12px;
            min-width: 65px;
            border: 1px solid var(--myads-surface-border, #f1f5f9);
        }

        .support-counter-num {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--myads-primary, #615dfa);
            display: block;
            line-height: 1.1;
        }

        .support-counter-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--myads-text-muted, #64748b);
        }

        .support-empty-state {
            text-align: center;
            padding: 50px 20px;
            background: var(--myads-surface, #ffffff);
            border-radius: 20px;
            border: 1px dashed var(--myads-surface-border, #cbd5e1);
        }

        @media (max-width: 768px) {
            .support-hero { padding: 36px 18px; }
            .support-hero-title { font-size: 1.7rem; }
            .support-topic-row { flex-direction: column; align-items: flex-start; }
            .support-topic-stats { width: 100%; justify-content: flex-end; }
            .support-search-form { max-width: 100%; }
        }
    </style>

    <!-- Support Forum Hero -->
    <div class="support-hero">
        <div class="support-hero-badge">
            <i class="fa-solid fa-headset"></i>
            {{ __('support.support_forum') }}
        </div>
        <h1 class="support-hero-title">
            {{ __('support.support_center') }}
        </h1>
        <p class="support-hero-subtitle">
            {{ __('support.support_forum_desc', ['site' => $siteName]) }}
        </p>

        <!-- Main Action Buttons -->
        <div class="support-quick-nav">
            <a href="{{ route('support.create') }}" class="btn-support-ask">
                <i class="fa-solid fa-plus-circle"></i>
                <span>{{ __('support.ask_question') }}</span>
            </a>
            <a href="{{ route('tickets.index') }}" class="btn-support-tickets">
                <i class="fa-solid fa-ticket"></i>
                <span>{{ __('support.support_tickets') }}</span>
            </a>
            <a href="{{ route('help.index') }}" class="btn-support-tickets">
                <i class="fa-solid fa-book-bookmark"></i>
                <span>{{ __('help.help_center') }}</span>
            </a>
        </div>

        <!-- Quick Stats Chips -->
        <div class="support-stats-row">
            <div class="support-stat-chip">
                <i class="fa-solid fa-comments" style="color: #615dfa;"></i>
                <span>{{ __('support.topics_count', ['count' => $totalTopics]) }}</span>
            </div>
            <div class="support-stat-chip">
                <i class="fa-solid fa-circle-check" style="color: #10b981;"></i>
                <span>{{ __('support.topics_count', ['count' => $totalSolved]) }} {{ __('support.solved') }}</span>
            </div>
            <div class="support-stat-chip">
                <i class="fa-solid fa-layer-group" style="color: #23d2e2;"></i>
                <span>{{ $totalCategories }} {{ __('support.support_categories') }}</span>
            </div>
        </div>
    </div>

    <!-- Support Categories Grid -->
    <div class="support-section-header">
        <h2 class="support-section-title">
            <i class="fa-solid fa-folder-tree" style="color: var(--myads-primary, #615dfa);"></i>
            {{ __('support.support_categories') }}
        </h2>
    </div>

    <div class="support-categories-grid">
        @foreach($categories as $cat)
            <a href="{{ route('support.category', $cat->slug) }}" class="support-category-card" style="--cat-color: {{ $cat->color }};">
                <div class="support-cat-icon">
                    <i class="{{ $cat->icon }}"></i>
                </div>
                <h3 class="support-cat-name">{{ $cat->name }}</h3>
                <p class="support-cat-desc">{{ $cat->description }}</p>
                <div class="support-cat-footer">
                    <span>{{ __('support.topics_count', ['count' => $cat->topics_count]) }}</span>
                    <span><i class="fa-solid {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i></span>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Topics Header & Filters -->
    <div class="support-section-header">
        <h2 class="support-section-title">
            <i class="fa-solid fa-list-check" style="color: var(--myads-primary, #615dfa);"></i>
            {{ __('support.all_topics') }}
        </h2>
    </div>

    <div class="support-filter-bar">
        <div class="support-filter-tabs">
            <a href="{{ route('support.index', ['filter' => 'all', 'q' => $search]) }}" class="support-filter-tab {{ $filter === 'all' ? 'active' : '' }}">
                {{ __('support.all_topics') }}
            </a>
            <a href="{{ route('support.index', ['filter' => 'solved', 'q' => $search]) }}" class="support-filter-tab {{ $filter === 'solved' ? 'active' : '' }}">
                <i class="fa-solid fa-check-circle"></i> {{ __('support.solved_topics') }}
            </a>
            <a href="{{ route('support.index', ['filter' => 'unsolved', 'q' => $search]) }}" class="support-filter-tab {{ $filter === 'unsolved' ? 'active' : '' }}">
                <i class="fa-solid fa-circle-question"></i> {{ __('support.unsolved_topics') }}
            </a>
        </div>

        <form action="{{ route('support.index') }}" method="GET" class="support-search-form">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <i class="fa-solid fa-magnifying-glass support-search-icon"></i>
            <input type="text" name="q" value="{{ $search }}" class="support-search-input" placeholder="{{ __('support.search_support_placeholder') }}">
        </form>
    </div>

    <!-- Topics List -->
    @if($topics->count() > 0)
        <div class="support-topics-list">
            @foreach($topics as $topic)
                <a href="{{ route('support.topic', ['id' => $topic->id, 'slug' => $topic->slug]) }}" class="support-topic-row">
                    <div class="support-topic-main">
                        <img src="{{ !empty($topic->user->img) ? theme_asset('img/' . $topic->user->img) : theme_asset('img/avatar.png') }}"
                             alt="{{ $topic->user->username ?? 'User' }}"
                             class="support-topic-avatar">
                        <div class="support-topic-meta-box">
                            <div class="support-topic-badges">
                                @if($topic->is_pinned)
                                    <span class="support-badge-pill badge-pinned">
                                        <i class="fa-solid fa-thumbtack"></i> {{ __('support.pinned') }}
                                    </span>
                                @endif
                                @if($topic->isResolved())
                                    <span class="support-badge-pill badge-solved">
                                        <i class="fa-solid fa-check-circle"></i> {{ __('support.solved') }}
                                    </span>
                                @else
                                    <span class="support-badge-pill badge-unsolved">
                                        <i class="fa-solid fa-circle-question"></i> {{ __('support.unsolved') }}
                                    </span>
                                @endif
                                @if($topic->category)
                                    <span class="support-badge-pill badge-category">
                                        <i class="{{ $topic->category->icon }}"></i> {{ $topic->category->name }}
                                    </span>
                                @endif
                            </div>
                            <h3 class="support-topic-title">{{ $topic->title }}</h3>
                            <div class="support-topic-author-meta">
                                <span><i class="fa-solid fa-user"></i> {{ $topic->user->username ?? 'Member' }}</span>
                                <span><i class="fa-regular fa-clock"></i> {{ $topic->created_at->diffForHumans() }}</span>
                                @if($topic->last_reply_at)
                                    <span><i class="fa-solid fa-reply"></i> {{ __('support.last_update') }}: {{ $topic->last_reply_at->diffForHumans() }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="support-topic-stats">
                        <div class="support-counter-box">
                            <span class="support-counter-num">{{ $topic->replies_count }}</span>
                            <span class="support-counter-label">{{ __('support.replies') }}</span>
                        </div>
                        <div class="support-counter-box">
                            <span class="support-counter-num">{{ $topic->views_count }}</span>
                            <span class="support-counter-label">{{ __('support.views') }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div style="margin-top: 30px;">
            {{ $topics->links() }}
        </div>
    @else
        <div class="support-empty-state">
            <i class="fa-solid fa-inbox" style="font-size: 3rem; color: #94a3b8; margin-bottom: 15px;"></i>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--myads-text-main, #1e293b); margin-bottom: 8px;">
                {{ __('support.no_topics_found') }}
            </h3>
            <p style="color: var(--myads-text-muted, #64748b); margin-bottom: 20px;">
                {{ __('support.be_first_to_ask') }}
            </p>
            <a href="{{ route('support.create') }}" class="btn-support-ask">
                <i class="fa-solid fa-plus-circle"></i> {{ __('support.ask_question') }}
            </a>
        </div>
    @endif
</div>
@endsection
