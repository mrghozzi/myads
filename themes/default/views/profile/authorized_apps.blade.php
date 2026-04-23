@extends('theme::layouts.master')

@section('content')
<div class="section-banner">
    <p class="section-banner-title">{{ __('messages.authorized_apps') }}</p>
    <p class="section-banner-text">{{ __('messages.authorized_apps_desc') }}</p>
</div>

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">
        @include('theme::profile.settings_nav')
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <p class="widget-box-title">{{ __('messages.authorized_apps') }}</p>
            <div class="widget-box-content">
                @if (session('success'))
                    <div class="alert-box success" style="margin-bottom: 24px; padding: 15px; border-radius: 12px; background: rgba(31, 179, 77, 0.1); border: 1px solid rgba(31, 179, 77, 0.2); color: #1fb34d;">
                        <p class="alert-box-text" style="font-size: 14px; font-weight: 600; margin: 0;">{{ session('success') }}</p>
                    </div>
                @endif

                @if(count($authorizations) > 0)
                    <div class="apps-list">
                        @foreach($authorizations as $auth)
                            <div class="app-item">
                                <div class="app-info">
                                    <div class="app-icon-wrapper">
                                        @if($auth->app->logo)
                                            <img src="{{ asset($auth->app->logo) }}" alt="{{ $auth->app->name }}" class="app-icon">
                                        @else
                                            <div class="app-icon-placeholder">
                                                <i class="fa-solid fa-cube" style="font-size: 24px; color: var(--text-color-alt); opacity: 0.5;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="app-details">
                                        <h6 class="app-name">{{ $auth->app->name }}</h6>
                                        @if($auth->app->domain)
                                            <p class="app-domain">{{ $auth->app->domain }}</p>
                                        @endif
                                        <p class="app-date">@lang('messages.authorized_on') {{ $auth->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="app-actions">
                                    <form action="{{ route('profile.apps.revoke', $auth->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.revoke_app_confirm') }}')">
                                        @csrf
                                        <button type="submit" class="button white small" style="border-color: #f68b8b; color: #f68b8b;">
                                            {{ __('messages.revoke_access') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fa-solid fa-shield-halved" style="font-size: 48px; color: var(--text-color-alt); opacity: 0.2;"></i>
                        </div>
                        <p class="empty-state-text">{{ __('messages.no_authorized_apps') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .apps-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .app-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        background: var(--widget-box-bg);
        transition: all 0.3s ease;
    }
    .app-item:hover {
        border-color: var(--primary-color);
        box-shadow: 0 8px 20px rgba(0,0,0,0.04);
        transform: translateY(-2px);
    }
    .app-info {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .app-icon-wrapper {
        width: 60px;
        height: 60px;
        flex-shrink: 0;
    }
    .app-icon {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }
    .app-icon-placeholder {
        width: 100%;
        height: 100%;
        background: var(--dark-light-color, rgba(0,0,0,0.03));
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border-color);
    }
    .app-details {
        display: flex;
        flex-direction: column;
    }
    .app-name {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: var(--text-color);
    }
    .app-domain {
        margin: 2px 0 0;
        font-size: 13px;
        color: var(--primary-color);
        font-weight: 600;
    }
    .app-date {
        margin: 6px 0 0;
        font-size: 12px;
        color: var(--text-color-alt);
    }
    .empty-state {
        padding: 80px 24px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
    }
    .empty-state-text {
        color: var(--text-color-alt);
        font-size: 15px;
        margin: 0;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .app-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }
        .app-actions {
            width: 100%;
        }
        .app-actions button {
            width: 100%;
            justify-content: center;
        }
    }
    
    [dir="rtl"] .app-info {
        text-align: right;
    }
</style>
@endsection
