@extends('theme::layouts.master')

@push('head')
<style>
    .tag-page {
        --tag-accent: #615dfa;
        --tag-accent-rgb: 97, 93, 250;
        --tag-bg: #fff;
        --tag-border: #edf0f7;
        --tag-text: #3e3f5e;
        --tag-muted: #8f91ac;
        --tag-shadow: 0 14px 30px rgba(var(--tag-accent-rgb), 0.08);
    }

    [data-theme="css_d"] .tag-page {
        --tag-bg: #1d2333;
        --tag-border: #2f3749;
        --tag-text: #fff;
        --tag-muted: #9aa4bf;
        --tag-shadow: 0 14px 30px rgba(0, 0, 0, 0.3);
    }

    /* Modern Banner */
    .tag-page .tag-banner {
        padding: 40px;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--tag-accent), #23d2e2);
        position: relative;
        overflow: hidden;
        margin-bottom: 30px;
        box-shadow: 0 12px 24px rgba(var(--tag-accent-rgb), 0.2);
    }

    .tag-page .tag-banner::after {
        content: "";
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        pointer-events: none;
    }

    .tag-page .tag-banner-content {
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 25px;
    }

    .tag-page .tag-banner-icon-wrapper {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        color: #fff;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .tag-page .tag-banner-title {
        color: #fff;
        font-size: 2.2rem;
        font-weight: 900;
        margin: 0;
        letter-spacing: -0.02em;
    }

    .tag-page .tag-banner-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.95rem;
        font-weight: 600;
        margin-top: 5px;
    }

    /* Section Tabs */
    .tag-page .section-nav {
        display: flex;
        gap: 10px;
        margin-bottom: 25px;
        background: var(--tag-bg);
        padding: 10px;
        border-radius: 14px;
        border: 1px solid var(--tag-border);
        box-shadow: var(--tag-shadow);
    }

    .tag-page .nav-item {
        padding: 10px 24px;
        border-radius: 10px;
        color: var(--tag-muted);
        font-weight: 700;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
        background: transparent;
    }

    .tag-page .nav-item:hover {
        color: var(--tag-accent);
        background: rgba(var(--tag-accent-rgb), 0.05);
    }

    .tag-page .nav-item.active {
        background: var(--tag-accent);
        color: #fff;
        box-shadow: 0 4px 12px rgba(var(--tag-accent-rgb), 0.2);
    }

    /* Topic Card Styles */
    .tag-page .topic-card {
        background: var(--tag-bg);
        border: 1px solid var(--tag-border);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 16px;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: var(--tag-shadow);
    }

    .tag-page .topic-card:hover {
        transform: translateY(-3px);
        border-color: rgba(var(--tag-accent-rgb), 0.3);
        box-shadow: 0 10px 25px rgba(var(--tag-accent-rgb), 0.12);
    }

    .tag-page .topic-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(var(--tag-accent-rgb), 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--tag-accent);
        font-size: 20px;
        flex-shrink: 0;
    }

    .tag-page .topic-info {
        flex: 1;
        min-width: 0;
    }

    .tag-page .topic-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--tag-text);
        margin: 0 0 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .tag-page .topic-meta {
        font-size: 0.8rem;
        color: var(--tag-muted);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .tag-page .topic-meta span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .tag-page .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: var(--tag-bg);
        border-radius: 16px;
        border: 1px dashed var(--tag-border);
    }

    .tag-page .empty-icon {
        font-size: 50px;
        color: var(--tag-muted);
        margin-bottom: 20px;
        opacity: 0.5;
    }

    /* Grid layout fixes */
    .tag-page .grid {
        grid-gap: 24px;
    }

    /* Floating Side Info */
    .tag-page .info-card {
        background: var(--tag-bg);
        border-radius: 16px;
        border: 1px solid var(--tag-border);
        padding: 25px;
        box-shadow: var(--tag-shadow);
        position: sticky;
        top: 100px;
    }

    .tag-page .info-card-title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--tag-text);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tag-page .stat-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px dashed var(--tag-border);
    }

    .tag-page .stat-row:last-child {
        border-bottom: none;
    }

    .tag-page .stat-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--tag-muted);
    }

    .tag-page .stat-value {
        font-size: 0.85rem;
        font-weight: 800;
        color: var(--tag-accent);
    }
</style>
@endpush

@section('content')
<div class="tag-page">
    <!-- TAG BANNER -->
    <div class="tag-banner">
        <div class="tag-banner-content">
            <div class="tag-banner-icon-wrapper">
                <i class="fas fa-hashtag"></i>
            </div>
            <div>
                <h1 class="tag-banner-title">{{ $tag }}</h1>
                <p class="tag-banner-subtitle">{{ __('messages.search_results_for_tag') ?? 'Discover everything tagged with' }} #{{ $tag }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-8-4 mobile-prefer-content">
        <!-- MAIN CONTENT -->
        <div class="grid-column">
            
            <!-- SECTION NAVIGATION -->
            <div class="section-nav">
                <button class="nav-item active" onclick="switchTagTab('statuses', this)">
                    <i class="fas fa-stream me-2"></i> {{ __('messages.latest_updates') }}
                </button>
                <button class="nav-item" onclick="switchTagTab('topics', this)">
                    <i class="fas fa-comments me-2"></i> {{ __('messages.topics') }}
                </button>
            </div>

            <!-- STATUSES LIST -->
            <div id="tag-tab-statuses" class="tag-tab-content">
                @forelse($statuses as $status)
                    @include('theme::partials.activity.render', ['activity' => $status])
                @empty
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-ghost"></i></div>
                        <h3>{{ __('messages.no_activities') }}</h3>
                        <p>{{ __('messages.no_results_found_tag') ?? 'Be the first to post something with this tag!' }}</p>
                    </div>
                @endforelse

                @if($statuses->hasPages())
                <div style="margin-top: 30px;">
                    {{ $statuses->appends(['topics_page' => $topics->currentPage()])->links() }}
                </div>
                @endif
            </div>

            <!-- TOPICS LIST -->
            <div id="tag-tab-topics" class="tag-tab-content" style="display: none;">
                @forelse($topics as $topic)
                    <a href="{{ route('forum.topic', $topic->id) }}" class="topic-card">
                        <div class="topic-icon">
                            <i class="fas fa-comment-dots"></i>
                        </div>
                        <div class="topic-info">
                            <h3 class="topic-title">{{ $topic->name }}</h3>
                            <div class="topic-meta">
                                <span><i class="fas fa-user"></i> {{ $topic->user->username ?? __('messages.unknown') }}</span>
                                <span><i class="fas fa-clock"></i> {{ $topic->date_formatted }}</span>
                            </div>
                        </div>
                        <div class="topic-action">
                            <i class="fas fa-chevron-right" style="color: var(--tag-muted); opacity: 0.5;"></i>
                        </div>
                    </a>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-search"></i></div>
                        <h3>{{ __('messages.no_topics_found') }}</h3>
                        <p>{{ __('messages.no_topics_tag_desc') ?? 'No forum discussions found for this hashtag.' }}</p>
                    </div>
                @endforelse

                @if($topics->hasPages())
                <div style="margin-top: 30px;">
                    {{ $topics->appends(['statuses_page' => $statuses->currentPage()])->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- SIDEBAR -->
        <div class="grid-column">
            <div class="info-card">
                <h3 class="info-card-title">
                    <i class="fas fa-info-circle" style="color: var(--tag-accent);"></i>
                    {{ __('messages.tag_insights') ?? 'Tag Insights' }}
                </h3>
                <div class="stat-row">
                    <span class="stat-label">{{ __('messages.total_posts') }}</span>
                    <span class="stat-value">{{ $statuses->total() }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">{{ __('messages.total_topics') }}</span>
                    <span class="stat-value">{{ $topics->total() }}</span>
                </div>
                
                <hr style="border-top: 1px solid var(--tag-border); margin: 20px 0;">
                
                <p style="font-size: 0.8rem; color: var(--tag-muted); line-height: 1.5; font-weight: 600;">
                    {{ __('messages.tag_footer_note') ?? 'Explore trending content and discussions associated with this hashtag across the community.' }}
                </p>
            </div>

            <div style="margin-top: 24px;">
                <x-widget-column side="portal_right" />
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function switchTagTab(tab, btn) {
        // Hide all tabs
        document.querySelectorAll('.tag-tab-content').forEach(el => el.style.display = 'none');
        // Show selected tab
        document.getElementById('tag-tab-' + tab).style.display = 'block';
        
        // Update buttons
        document.querySelectorAll('.tag-page .nav-item').forEach(el => el.classList.remove('active'));
        btn.classList.add('active');
        
        // If switching to statuses, re-init hexagons if needed (though they should be init by global)
        if (tab === 'statuses' && typeof initHexagons === 'function') {
            initHexagons(document.getElementById('tag-tab-statuses'));
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Handle URL fragments if any (optional)
        const hash = window.location.hash.replace('#', '');
        if (hash === 'topics') {
            const btn = document.querySelector('button[onclick*="topics"]');
            if (btn) btn.click();
        }
    });
</script>
@endpush
@endsection
