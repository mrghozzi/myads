@extends('theme::layouts.master')

@section('content')
<div class="section-banner">
    <p class="section-banner-title">{{ __('messages.active_sessions') }}</p>
</div>

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">
        @include('theme::profile.settings_nav')
    </div>

    <div class="grid-column">
        <div class="widget-box" style="background: transparent; box-shadow: none; border: none;">
            <!-- Header with Superdesign Background -->
            <div style="background: linear-gradient(135deg, var(--primary-color, #23d2e2) 0%, #1c9bb0 100%); padding: 40px; border-radius: 20px; color: #fff; margin-bottom: 30px; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(35, 210, 226, 0.2);">
                <div style="position: relative; z-index: 2;">
                    <h3 style="font-weight: 800; font-size: 24px; margin-bottom: 8px;">{{ __('messages.manage_sessions') }}</h3>
                    <p style="opacity: 0.9; font-size: 15px; max-width: 600px;">{{ __('messages.security_member_sessions_desc') ?? 'Monitor and manage your active login sessions across all your devices.' }}</p>
                </div>
                <!-- Abstract Design Elements -->
                <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -30px; left: 10%; width: 100px; height: 100px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                <i class="fa-solid fa-shield-halved" style="position: absolute; right: 40px; bottom: -20px; font-size: 120px; color: rgba(255,255,255,0.1); transform: rotate(-15deg);"></i>
            </div>

            @if($upgradeNotice)
                <div class="alert alert-warning" style="border-radius: 12px; margin-bottom: 20px;">
                    {!! $upgradeNotice !!}
                </div>
            @endif

            <div class="sessions-container" style="display: grid; gap: 20px;">
                @forelse($sessions as $session)
                    @php
                        $icon = 'fa-laptop';
                        if($session->device_type === 'mobile') $icon = 'fa-mobile-screen-button';
                        if($session->device_type === 'tablet') $icon = 'fa-tablet-screen-button';
                        
                        $isRevoked = $session->revoked_at !== null;
                        $isEnded = $session->ended_at !== null && !$isRevoked;
                        $isActive = !$isRevoked && !$isEnded;
                    @endphp
                    
                    <div class="session-card {{ $session->is_current ? 'current' : '' }}" 
                         style="background: var(--widget-box-bg, #fff); border: 1px solid var(--border-color, #ebebeb); border-radius: 20px; padding: 24px; display: flex; align-items: center; gap: 24px; transition: all 0.3s ease; position: relative; overflow: hidden; {{ $session->is_current ? 'border-color: var(--primary-color); box-shadow: 0 8px 24px rgba(35, 210, 226, 0.1);' : '' }}">
                        
                        <!-- Side Decoration for Current Session -->
                        @if($session->is_current)
                            <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 5px; background: var(--primary-color);"></div>
                        @endif

                        <!-- Device Icon with Gradient Back -->
                        <div class="device-icon-box" style="width: 64px; height: 64px; background: {{ $session->is_current ? 'linear-gradient(135deg, rgba(35, 210, 226, 0.1) 0%, rgba(35, 210, 226, 0.2) 100%)' : 'var(--dark-light-color, #f8f8fb)' }}; border-radius: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fa-solid {{ $icon }}" style="font-size: 28px; color: {{ $session->is_current ? 'var(--primary-color)' : 'var(--text-color-alt)' }};"></i>
                        </div>

                        <!-- Session Info -->
                        <div style="flex-grow: 1;">
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 4px; flex-wrap: wrap;">
                                <h4 style="font-weight: 700; font-size: 17px; margin: 0; color: var(--text-color);">
                                    {{ $session->browser }} {{ __('messages.on') }} {{ __('messages.' . $session->device_type) }}
                                </h4>
                                
                                @if($session->is_current)
                                    <span class="badge" style="background: #eefdf3; color: #1fb34d; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(31, 179, 77, 0.2);">
                                        {{ __('messages.current_session') }}
                                    </span>
                                @elseif($isActive)
                                    <span class="badge" style="background: #eff6ff; color: #3b82f6; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(59, 130, 246, 0.2);">
                                        {{ __('messages.active') }}
                                    </span>
                                @elseif($isRevoked)
                                    <span class="badge" style="background: #fef2f2; color: #ef4444; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(239, 68, 68, 0.2);">
                                        {{ __('messages.revoked') ?? 'Revoked' }}
                                    </span>
                                @else
                                    <span class="badge" style="background: #f3f4f6; color: #6b7280; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(107, 114, 128, 0.2);">
                                        {{ __('messages.ended') ?? 'Ended' }}
                                    </span>
                                @endif
                            </div>

                            <div style="display: flex; flex-wrap: wrap; gap: 16px; color: var(--text-color-alt); font-size: 14px;">
                                <span title="{{ __('messages.ip_address') }}"><i class="fa-solid fa-network-wired" style="margin-right: 6px; width: 14px;"></i> {{ $session->ip_address }}</span>
                                <span title="{{ __('messages.last_activity') }}"><i class="fa-solid fa-clock-rotate-left" style="margin-right: 6px; width: 14px;"></i> {{ $session->last_seen_at->diffForHumans() }}</span>
                                <span title="{{ __('messages.started_at') }}"><i class="fa-solid fa-calendar-day" style="margin-right: 6px; width: 14px;"></i> {{ $session->started_at->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        @if($isActive || $session->is_current)
                            <div class="session-actions">
                                <form action="{{ route('profile.sessions.revoke', $session->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_revoke_session') }}');">
                                    @csrf
                                    <button type="submit" class="button {{ $session->is_current ? 'secondary' : 'primary' }}" 
                                            style="border-radius: 12px; padding: 10px 18px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 8px; transition: all 0.2s ease;">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                        {{ $session->is_current ? __('messages.logout') : __('messages.revoke_session') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="widget-box" style="padding: 60px; text-align: center; border-radius: 20px;">
                        <i class="fa-solid fa-user-lock" style="font-size: 64px; color: var(--border-color); margin-bottom: 20px;"></i>
                        <p style="color: var(--text-color-alt); font-size: 16px;">{{ __('messages.no_active_sessions') }}</p>
                    </div>
                @endforelse
            </div>
            
            <style>
                .session-card:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 12px 30px rgba(0,0,0,0.08);
                }
                [dir="rtl"] .session-card:hover {
                    transform: translateY(-4px);
                }
                [dir="rtl"] .device-icon-box i {
                    margin-left: 0;
                }
                [dir="rtl"] i {
                    margin-right: 0 !important;
                    margin-left: 6px !important;
                }
                @media (max-width: 768px) {
                    .session-card {
                        flex-direction: column;
                        align-items: flex-start !important;
                        gap: 16px !important;
                    }
                    .device-icon-box {
                        width: 48px !important;
                        height: 48px !important;
                    }
                    .session-actions {
                        width: 100%;
                    }
                    .session-actions button {
                        width: 100%;
                        justify-content: center;
                    }
                }
            </style>
        </div>
    </div>
</div>
@endsection
