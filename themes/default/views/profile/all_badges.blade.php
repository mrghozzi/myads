@extends('theme::layouts.master')

@section('content')
<style>
    .badges-hub-header {
        position: relative;
        padding: 60px 40px;
        background: linear-gradient(135deg, #615dfa 0%, #3e3f5e 100%);
        border-radius: 20px;
        color: #fff;
        margin-bottom: 30px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(97, 93, 250, 0.2);
    }
    .badges-hub-header::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    .badges-hub-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 10px;
        letter-spacing: -0.02em;
        color: #fff !important;
    }
    .badges-hub-text {
        font-size: 1.125rem;
        opacity: 0.9;
        max-width: 600px;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.9) !important;
    }
    .badges-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }
    .badge-item-card {
        position: relative;
        background: var(--notification-ui-card-bg);
        border: 1px solid var(--notification-ui-card-border);
        border-radius: 20px;
        padding: 30px 20px;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: var(--notification-ui-card-shadow);
        overflow: hidden;
    }
    .badge-item-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--notification-ui-card-shadow-hover);
    }
    .badge-item-card.locked {
        filter: grayscale(0.8);
        opacity: 0.8;
    }
    .badge-item-card.locked:hover {
        filter: grayscale(0.4);
        opacity: 1;
    }
    .badge-icon-wrap {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }
    .badge-icon-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #f0f1f7;
        clip-path: polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%);
        transition: transform 0.3s ease;
    }
    .badge-item-card:hover .badge-icon-bg {
        transform: rotate(10deg) scale(1.1);
    }
    .badge-item-card.unlocked .badge-icon-bg {
        background: linear-gradient(135deg, #615dfa 0%, #3e3f5e 100%);
    }
    .badge-icon-main {
        font-size: 42px;
        color: #615dfa;
        z-index: 2;
        transition: transform 0.3s ease;
    }
    .badge-item-card.unlocked .badge-icon-main {
        color: #fff;
    }
    .badge-item-card:hover .badge-icon-main {
        transform: scale(1.1);
    }
    .badge-name {
        font-size: 1.125rem;
        font-weight: 800;
        margin-bottom: 8px;
        color: var(--notification-ui-summary-heading);
    }
    .badge-desc {
        font-size: 0.875rem;
        color: var(--notification-ui-muted);
        line-height: 1.5;
        margin-bottom: 20px;
    }
    .badge-progress-container {
        margin-top: auto;
    }
    .badge-progress-label {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--notification-ui-muted);
        margin-bottom: 8px;
        text-transform: uppercase;
    }
    .badge-progress-bar-bg {
        width: 100%;
        height: 6px;
        background: rgba(0, 0, 0, 0.05);
        border-radius: 10px;
        overflow: hidden;
    }
    .badge-progress-bar-fill {
        height: 100%;
        background: #615dfa;
        border-radius: 10px;
        transition: width 1s ease;
    }
    .badge-status-tag {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 0.625rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .badge-status-tag.locked {
        background: #e7e8ee;
        color: #7b819d;
    }
    .badge-status-tag.unlocked {
        background: #4ff461;
        color: #fff;
        box-shadow: 0 4px 12px rgba(79, 244, 97, 0.3);
    }
    .badge-unlocked-date {
        margin-top: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #4ff461;
    }
</style>

<div class="badges-hub-header">
    <h1 class="badges-hub-title">{{ __('messages.badges_hub') }}</h1>
    <p class="badges-hub-text">{{ __('messages.badges_hub_desc') }}</p>
</div>

@if(!empty($upgradeNotice))
    @include('theme::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
@endif

<div class="badges-grid">
    @forelse($badges as $badge)
        <div class="badge-item-card {{ $badge->is_unlocked ? 'unlocked' : 'locked' }}">
            <span class="badge-status-tag {{ $badge->is_unlocked ? 'unlocked' : 'locked' }}">
                {{ $badge->is_unlocked ? __('messages.badge_unlocked') : __('messages.badge_locked') }}
            </span>
            
            <div class="badge-icon-wrap">
                <div class="badge-icon-bg"></div>
                <div class="badge-icon-main">
                    @if($badge->icon && str_contains($badge->icon, ' '))
                        <i class="{{ $badge->icon }}"></i>
                    @elseif($badge->icon && str_starts_with($badge->icon, 'fa-'))
                        <i class="fa {{ $badge->icon }}"></i>
                    @elseif($badge->icon && str_starts_with($badge->icon, 'svg-'))
                        <svg class="icon {{ $badge->icon }}"><use xlink:href="#{{ $badge->icon }}"></use></svg>
                    @else
                        <i class="fa fa-trophy"></i>
                    @endif
                </div>
            </div>

            <h3 class="badge-name">{{ __('messages.' . $badge->name_key) }}</h3>
            <p class="badge-desc">{{ __('messages.' . $badge->description_key) }}</p>

            <div class="badge-progress-container">
                <div class="badge-progress-label">
                    <span>{{ __('messages.progress') }}</span>
                    <span>{{ $badge->progress }} / {{ $badge->criteria_target }}</span>
                </div>
                <div class="badge-progress-bar-bg">
                    <div class="badge-progress-bar-fill" style="width: {{ ($badge->progress / $badge->criteria_target) * 100 }}%"></div>
                </div>
                @if($badge->is_unlocked && $badge->unlocked_at)
                    <p class="badge-unlocked-date">
                        <i class="fa fa-check-circle"></i> {{ __('messages.unlocked_on', ['date' => $badge->unlocked_at->format('M d, Y')]) }}
                    </p>
                @endif
            </div>
        </div>
    @empty
        <div class="widget-box" style="grid-column: 1 / -1;">
            <div class="widget-box-content">
                <p class="text-center">{{ __('messages.no_data') }}</p>
            </div>
        </div>
    @endforelse
</div>
@endsection
