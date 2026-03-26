@extends('theme::layouts.master')

@section('content')
<style>
    .quests-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 24px;
        margin-top: 30px;
        width: 100%;
    }

    .quest-card {
        background: var(--component-bg, #fff);
        border-radius: 20px;
        padding: 30px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .quest-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .quest-reward-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
        color: #fff !important;
        font-weight: 800;
        font-size: 14px;
        padding: 6px 14px;
        border-radius: 50px;
        box-shadow: 0 4px 10px rgba(255, 165, 0, 0.3);
        z-index: 2;
    }

    [dir="rtl"] .quest-reward-badge {
        right: auto;
        left: 20px;
    }

    .quest-icon-wrap {
        width: 100px;
        height: 100px;
        margin-bottom: 24px;
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .quest-hexagon {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        fill: rgba(97, 93, 250, 0.1);
        z-index: 1;
    }

    .quest-main-icon {
        width: 48px;
        height: 48px;
        fill: #615dfa;
        z-index: 2;
        transition: transform 0.3s ease;
    }

    .quest-card:hover .quest-main-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .quest-title {
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 8px;
        color: #3e3f5e;
    }

    .quest-desc {
        font-size: 14px;
        color: #777d74;
        margin-bottom: 24px;
        line-height: 1.6;
        min-height: 45px;
    }

    .quest-progress-info {
        width: 100%;
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 13px;
        font-weight: 700;
        color: #3e3f5e;
    }

    .quest-progress-bar-wrap {
        width: 100%;
        height: 8px;
        background: #eaeaf5;
        border-radius: 10px;
        margin-bottom: 10px;
        overflow: hidden;
    }

    .quest-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #615dfa 0%, #48c6ef 100%);
        border-radius: 10px;
        transition: width 1s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .quest-period-tag {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 800;
        letter-spacing: 1px;
        color: #615dfa;
        margin-bottom: 4px;
    }

    .user-status-text {
        color: #3e3f5e;
    }

    .completed-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(2px);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 5;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .quest-card.is-completed .completed-overlay {
        opacity: 1;
    }

    .quest-card.is-completed .quest-progress-bar {
        background: linear-gradient(90deg, #23d5ab 0%, #23a6d5 100%);
    }

    .completed-badge {
        background: #23d5ab;
        color: #fff;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 800;
        box-shadow: 0 4px 15px rgba(35, 213, 171, 0.4);
        transform: scale(0.8);
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .quest-card.is-completed:hover .completed-badge {
        transform: scale(1.1);
    }

    /* Dark Mode Overrides */
    [data-theme="css_d"] .quest-card {
        background: #1d2333;
        border-color: rgba(255, 255, 255, 0.05);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    [data-theme="css_d"] .quest-title {
        color: #fff !important;
    }

    [data-theme="css_d"] .quest-desc,
    [data-theme="css_d"] .quest-progress-info,
    [data-theme="css_d"] .user-status-text {
        color: #9aa4bf !important;
    }

    [data-theme="css_d"] .quest-hexagon {
        fill: rgba(255, 255, 255, 0.05);
    }

    [data-theme="css_d"] .quest-progress-bar-wrap {
        background: rgba(255, 255, 255, 0.1);
    }

    /* Centering & Layout Fixes */
    .section-banner {
        text-align: center;
        margin-bottom: 30px;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .grid {
        max-width: 1184px;
        margin: 0 auto !important;
        width: 100%;
    }

    /* RTL Fix for content-grid from master */
    [dir="rtl"] .content-grid {
        padding-left: 0 !important;
        padding-right: 100px !important;
    }

    @media screen and (max-width: 1365px) {
        [dir="rtl"] .content-grid {
            padding-right: 0 !important;
        }
        .content-grid {
            padding-left: 0 !important;
        }
    }

    /* Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .quest-card {
        animation: fadeInUp 0.5s ease backwards;
    }

    .quest-card:nth-child(1) { animation-delay: 0.1s; }
    .quest-card:nth-child(2) { animation-delay: 0.2s; }
    .quest-card:nth-child(3) { animation-delay: 0.3s; }
</style>

<div class="grid">
    <div class="section-banner">
        <p class="section-banner-title">{{ __('messages.quests') }}</p>
        <p class="section-banner-text text-center">{{ __('messages.quests_description') }}</p>
    </div>

    <div class="quests-grid">
        @forelse($quests as $quest)
            <div class="quest-card {{ $quest['is_completed'] ? 'is-completed' : '' }}">
                <div class="quest-reward-badge">+{{ $quest['reward'] }} PTS</div>
                
                <div class="quest-icon-wrap">
                    <svg class="quest-hexagon" viewBox="0 0 100 100">
                        <path d="M50 0 L93.3 25 L93.3 75 L50 100 L6.7 75 L6.7 25 Z"></path>
                    </svg>
                    <svg class="quest-main-icon">
                        <use xlink:href="#{{ $quest['model']->icon ?: 'svg-quests' }}"></use>
                    </svg>
                </div>

                <div class="quest-period-tag">{{ $quest['period'] }}</div>
                <h3 class="quest-title">{{ __('messages.' . $quest['model']->name_key) }}</h3>
                <p class="quest-desc">{{ __('messages.' . $quest['model']->description_key) }}</p>

                <div class="quest-progress-info">
                    <span>{{ __('messages.quest_progress') }}</span>
                    <span>{{ round($quest['percent']) }}%</span>
                </div>

                <div class="quest-progress-bar-wrap">
                    <div class="quest-progress-bar" style="width: {{ $quest['percent'] }}%;"></div>
                </div>

                <p class="user-status-text small">{{ $quest['is_completed'] ? __('messages.completed') : $quest['current'] . ' / ' . $quest['target'] }}</p>

                @if($quest['is_completed'])
                    <div class="completed-overlay">
                        <div class="completed-badge">
                            <i class="fa fa-check-circle" aria-hidden="true"></i> {{ __('messages.completed') }}
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="widget-box" style="grid-column: 1 / -1;">
                <div class="widget-box-content">
                    <p class="text-center">{{ __('messages.no_active_quests') }}</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
