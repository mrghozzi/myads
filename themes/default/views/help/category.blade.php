@extends('theme::layouts.master')

@section('title', $pageTitle ?? ($categoryData['title'] . ' - ' . __('help.help_center') . ' - ' . $siteName))

@section('content')
<div class="content-grid" style="padding-bottom: 80px;">
    <style>
        .help-breadcrumbs {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.88rem;
            color: var(--myads-text-muted, #64748b);
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .help-breadcrumbs a {
            color: var(--myads-text-muted, #64748b);
            text-decoration: none;
            transition: color 0.2s;
        }

        .help-breadcrumbs a:hover {
            color: var(--myads-primary, #615dfa);
        }

        .help-cat-hero {
            background: linear-gradient(135deg, rgba(97, 93, 250, 0.1) 0%, rgba(35, 210, 226, 0.08) 100%);
            border: 1px solid var(--myads-surface-border, rgba(97, 93, 250, 0.15));
            border-radius: var(--myads-radius-xl, 24px);
            padding: 40px 32px;
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 36px;
            position: relative;
            overflow: hidden;
        }

        .help-cat-icon {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            background: var(--myads-surface, #ffffff);
            color: var(--myads-primary, #615dfa);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 8px 24px rgba(97, 93, 250, 0.15);
            flex-shrink: 0;
        }

        .help-cat-info h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            margin-bottom: 8px;
        }

        .help-cat-info p {
            color: var(--myads-text-muted, #64748b);
            font-size: 1rem;
            max-width: 700px;
            margin: 0;
            line-height: 1.55;
        }

        .help-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 30px;
        }

        .help-sidebar {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-lg, 18px);
            padding: 20px 16px;
            height: fit-content;
        }

        .help-sidebar-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            padding: 8px 12px 14px;
            border-bottom: 1px solid var(--myads-surface-border, #f1f5f9);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .help-sidebar-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 12px;
            color: var(--myads-text-main, #1e293b);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
            margin-bottom: 4px;
        }

        .help-sidebar-item:hover {
            background: rgba(97, 93, 250, 0.08);
            color: var(--myads-primary, #615dfa);
        }

        .help-sidebar-item.active {
            background: linear-gradient(135deg, #615dfa 0%, #4e4ac8 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(97, 93, 250, 0.35);
        }

        .help-sidebar-item.active .help-sidebar-count {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .help-sidebar-count {
            background: rgba(97, 93, 250, 0.1);
            color: var(--myads-primary, #615dfa);
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
        }

        .help-article-list-item {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-lg, 18px);
            padding: 24px;
            margin-bottom: 18px;
            text-decoration: none;
            color: var(--myads-text-main, #1e293b);
            display: block;
            transition: all 0.25s ease;
            box-shadow: var(--myads-shadow-sm, 0 2px 6px rgba(0,0,0,0.02));
        }

        .help-article-list-item:hover {
            border-color: var(--myads-primary, #615dfa);
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(97, 93, 250, 0.09);
            color: var(--myads-text-main, #1e293b);
        }

        .help-article-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            gap: 12px;
        }

        .help-article-title-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--myads-text-main, #1e293b);
            line-height: 1.4;
        }

        .help-article-meta {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 0.82rem;
            color: var(--myads-text-muted, #64748b);
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid var(--myads-surface-border, #f1f5f9);
            flex-wrap: wrap;
        }

        .help-article-rating-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #10b981;
            font-weight: 600;
        }

        .help-article-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .help-tag-pill {
            background: rgba(97, 93, 250, 0.06);
            color: var(--myads-primary, #615dfa);
            font-size: 0.75rem;
            padding: 3px 10px;
            border-radius: 6px;
            font-weight: 600;
        }

        @media (max-width: 860px) {
            .help-layout { grid-template-columns: 1fr; }
            .help-cat-hero { flex-direction: column; text-align: center; padding: 30px 18px; }
            .help-cat-info p { margin-bottom: 12px; }
        }
    </style>

    <!-- Breadcrumbs -->
    <div class="help-breadcrumbs">
        <a href="{{ url('/') }}"><i class="fa-solid fa-house"></i> {{ __('help.home') }}</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
        <a href="{{ route('help.index') }}">{{ __('help.help_center') }}</a>
        <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
        <span style="color: var(--myads-primary, #615dfa); font-weight: 700;">{{ $categoryData['title'] }}</span>
    </div>

    <!-- Category Hero -->
    <div class="help-cat-hero">
        <div class="help-cat-icon">
            <i class="{{ $categoryData['icon'] }}"></i>
        </div>
        <div class="help-cat-info">
            <h1>{{ $categoryData['title'] }}</h1>
            <p>{{ $categoryData['description'] }}</p>
        </div>
    </div>

    <!-- Layout: Sidebar + Articles -->
    <div class="help-layout">
        <!-- Sidebar Navigation -->
        <div class="help-sidebar">
            <div class="help-sidebar-title">
                <i class="fa-solid fa-layer-group" style="color: var(--myads-primary, #615dfa);"></i>
                {{ __('help.all_categories') }}
            </div>
            @foreach($categories as $cat)
                <a href="{{ route('help.category', $cat['slug']) }}"
                   class="help-sidebar-item {{ $cat['slug'] === $categoryData['slug'] ? 'active' : '' }}">
                    <span>
                        <i class="{{ $cat['icon'] }}" style="margin-right: 6px; width: 18px;"></i>
                        {{ $cat['title'] }}
                    </span>
                    <span class="help-sidebar-count">{{ $cat['article_count'] }}</span>
                </a>
            @endforeach
        </div>

        <!-- Articles List -->
        <div>
            @forelse($articles as $art)
                <a href="{{ route('help.article', ['category' => $art['category'], 'article' => $art['slug']]) }}" class="help-article-list-item">
                    <div class="help-article-header">
                        <div class="help-article-title-text">{{ $art['title'] }}</div>
                        <span class="help-tag-pill" style="background: rgba(35, 210, 226, 0.12); color: #0891b2;">
                            {{ $art['badge'] }}
                        </span>
                    </div>

                    <p style="color: var(--myads-text-muted, #64748b); font-size: 0.92rem; line-height: 1.6; margin: 0;">
                        {{ $art['summary'] }}
                    </p>

                    <div class="help-article-tags">
                        @foreach($art['tags'] as $tag)
                            <span class="help-tag-pill">#{{ $tag }}</span>
                        @endforeach
                    </div>

                    <div class="help-article-meta">
                        <span>
                            <i class="fa-regular fa-clock"></i>
                            {{ __('help.read_time', ['minutes' => $art['read_time']]) }}
                        </span>
                        @if(($art['rating']['total'] ?? 0) > 0)
                            <span class="help-article-rating-pill">
                                <i class="fa-solid fa-thumbs-up"></i>
                                {{ __('help.helpful_ratio', ['percent' => $art['rating']['percentage'], 'count' => $art['rating']['total']]) }}
                            </span>
                        @else
                            <span style="color: var(--myads-text-muted, #94a3b8);">
                                <i class="fa-regular fa-star"></i>
                                {{ __('help.no_ratings_yet') }}
                            </span>
                        @endif
                        <span style="margin-left: auto; color: var(--myads-primary, #615dfa); font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                            {{ __('help.open_feature') }}
                            <i class="fa-solid fa-arrow-right"></i>
                        </span>
                    </div>
                </a>
            @empty
                <div style="text-align: center; padding: 60px 20px; background: var(--myads-surface, #fff); border-radius: 18px; border: 1px dashed var(--myads-surface-border, #cbd5e1);">
                    <i class="fa-solid fa-inbox" style="font-size: 3rem; color: var(--myads-text-muted, #94a3b8); margin-bottom: 16px;"></i>
                    <p style="font-size: 1.1rem; color: var(--myads-text-main, #333); font-weight: 700;">{{ __('help.no_articles_in_category') }}</p>
                    <a href="{{ route('help.index') }}" class="btn-help-primary" style="margin-top: 14px; display: inline-block;">
                        {{ __('help.back_to_help') }}
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
