@extends('theme::layouts.master')

@section('title', $pageTitle ?? ($category->name . ' - ' . __('support.support_forum') . ' - ' . $siteName))

@section('content')
<div class="content-grid" style="padding-bottom: 80px;">
    <style>
        .cat-hero {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-xl, 22px);
            padding: 36px 30px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
            position: relative;
            overflow: hidden;
            box-shadow: var(--myads-shadow-sm, 0 4px 16px rgba(0,0,0,0.03));
        }

        .cat-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: {{ $category->color ?: '#615dfa' }};
        }

        .cat-hero-info {
            display: flex;
            align-items: center;
            gap: 20px;
            flex: 1;
            min-width: 280px;
        }

        .cat-hero-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            background: rgba(97, 93, 250, 0.1);
            color: {{ $category->color ?: '#615dfa' }};
            flex-shrink: 0;
        }

        .cat-hero-title {
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            margin: 0 0 6px 0;
        }

        .cat-hero-desc {
            font-size: 0.95rem;
            color: var(--myads-text-muted, #64748b);
            margin: 0;
        }

        .cat-nav-crumbs {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.88rem;
            color: var(--myads-text-muted, #64748b);
            margin-bottom: 20px;
        }

        .cat-nav-crumbs a {
            color: var(--myads-primary, #615dfa);
            text-decoration: none;
            font-weight: 600;
        }

        /* Filter Tabs */
        .support-filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
            background: var(--myads-surface, #ffffff);
            padding: 12px 20px;
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

        .support-topic-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--myads-text-main, #1e293b);
            margin: 0 0 6px 0;
            line-height: 1.4;
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

        .btn-support-ask {
            background: linear-gradient(135deg, #615dfa 0%, #4e4ac8 100%);
            color: #ffffff !important;
            padding: 12px 26px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(97, 93, 250, 0.3);
            white-space: nowrap;
        }

        .support-empty-state {
            text-align: center;
            padding: 50px 20px;
            background: var(--myads-surface, #ffffff);
            border-radius: 20px;
            border: 1px dashed var(--myads-surface-border, #cbd5e1);
        }
    </style>

    <!-- Breadcrumb -->
    <div class="cat-nav-crumbs">
        <a href="{{ route('support.index') }}"><i class="fa-solid fa-house"></i> {{ __('support.support_forum') }}</a>
        <i class="fa-solid fa-chevron-left" style="font-size: 0.75rem;"></i>
        <span>{{ $category->name }}</span>
    </div>

    <!-- Category Header Card -->
    <div class="cat-hero">
        <div class="cat-hero-info">
            <div class="cat-hero-icon">
                <i class="{{ $category->icon }}"></i>
            </div>
            <div>
                <h1 class="cat-hero-title">{{ $category->name }}</h1>
                <p class="cat-hero-desc">{{ $category->description }}</p>
            </div>
        </div>
        <a href="{{ route('support.create', ['category_id' => $category->id]) }}" class="btn-support-ask">
            <i class="fa-solid fa-plus-circle"></i>
            <span>{{ __('support.ask_question') }}</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="support-filter-bar">
        <div class="support-filter-tabs">
            <a href="{{ route('support.category', ['slug' => $category->slug, 'filter' => 'all']) }}" class="support-filter-tab {{ $filter === 'all' ? 'active' : '' }}">
                {{ __('support.all_topics') }}
            </a>
            <a href="{{ route('support.category', ['slug' => $category->slug, 'filter' => 'solved']) }}" class="support-filter-tab {{ $filter === 'solved' ? 'active' : '' }}">
                <i class="fa-solid fa-check-circle"></i> {{ __('support.solved_topics') }}
            </a>
            <a href="{{ route('support.category', ['slug' => $category->slug, 'filter' => 'unsolved']) }}" class="support-filter-tab {{ $filter === 'unsolved' ? 'active' : '' }}">
                <i class="fa-solid fa-circle-question"></i> {{ __('support.unsolved_topics') }}
            </a>
        </div>
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
                            <span class="support-counter-label">{{ __('support.replies_count', ['count' => '']) }}</span>
                        </div>
                        <div class="support-counter-box">
                            <span class="support-counter-num">{{ $topic->views_count }}</span>
                            <span class="support-counter-label">{{ __('support.views_count', ['count' => '']) }}</span>
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
            <a href="{{ route('support.create', ['category_id' => $category->id]) }}" class="btn-support-ask">
                <i class="fa-solid fa-plus-circle"></i> {{ __('support.ask_question') }}
            </a>
        </div>
    @endif
</div>
@endsection
