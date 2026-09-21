@extends('theme::layouts.master')

@push('head')
<style>
    .saved-page {
        --saved-card-bg: #ffffff;
        --saved-text: #3e3f5e;
        --saved-muted: #8f91ac;
        --saved-border: #eaeaf5;
        --saved-accent: #615dfa;
        --saved-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    body.dark-mode .saved-page,
    [data-theme="css_d"] .saved-page {
        --saved-card-bg: #1d2333;
        --saved-text: #ffffff;
        --saved-muted: #9aa4bf;
        --saved-border: #2f3749;
        --saved-accent: #7750f8;
        --saved-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .saved-banner {
        background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
        border-radius: 16px;
        padding: 32px 40px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 24px;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(97, 93, 250, 0.2);
    }

    .saved-banner-icon {
        width: 64px;
        height: 64px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: #fff;
        flex-shrink: 0;
    }

    .saved-banner-title {
        font-size: 1.8rem;
        font-weight: 800;
        margin: 0;
        color: #fff;
        letter-spacing: -0.02em;
    }

    .saved-banner-desc {
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.85);
        margin: 4px 0 0;
    }

    .saved-empty-card {
        background: var(--saved-card-bg);
        border: 1px dashed var(--saved-border);
        border-radius: 16px;
        padding: 60px 24px;
        text-align: center;
        box-shadow: var(--saved-shadow);
    }

    .saved-empty-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(97, 93, 250, 0.08);
        color: var(--saved-accent);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin-bottom: 18px;
    }

    .saved-empty-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--saved-text);
        margin-bottom: 8px;
    }

    .saved-empty-copy {
        color: var(--saved-muted);
        max-width: 440px;
        margin: 0 auto 24px;
        font-size: 0.92rem;
    }
</style>
@endpush

@section('content')
<div class="content-grid saved-page">
    <!-- HERO BANNER -->
    <div class="saved-banner">
        <div class="saved-banner-icon">
            <i class="fa-solid fa-bookmark"></i>
        </div>
        <div>
            <h1 class="saved-banner-title">{{ __('messages.saved_posts') }}</h1>
            <p class="saved-banner-desc">{{ __('messages.saved_posts_desc') }}</p>
        </div>
    </div>

    <!-- MAIN GRID LAYOUT -->
    <div class="grid grid-3-6-3 mobile-1-1">
        <!-- LEFT SIDEBAR -->
        <div class="grid-column">
            <x-widget-column side="portal_left" />
        </div>

        <!-- CENTER CONTENT -->
        <div class="grid-column">
            @if($statuses->isNotEmpty())
                <div id="infinite-scroll-container" style="display: grid; grid-gap: 16px;">
                    @foreach($statuses as $activity)
                        @include('theme::partials.activity.render', ['activity' => $activity])
                    @endforeach

                    @include('theme::partials.ajax.infinite_scroll', ['paginator' => $statuses])
                </div>
            @else
                <div class="saved-empty-card">
                    <div class="saved-empty-icon">
                        <i class="fa-regular fa-bookmark"></i>
                    </div>
                    <h3 class="saved-empty-title">{{ __('messages.no_saved_posts_title') }}</h3>
                    <p class="saved-empty-copy">{{ __('messages.no_saved_posts_desc') }}</p>
                    <a href="{{ route('portal.index') }}" class="button primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 0 24px; height: 44px;">
                        <i class="fa-solid fa-compass"></i> {{ __('messages.explore_community') }}
                    </a>
                </div>
            @endif
        </div>

        <!-- RIGHT SIDEBAR -->
        <div class="grid-column">
            <x-widget-column side="portal_right" />
        </div>
    </div>
</div>
@endsection
