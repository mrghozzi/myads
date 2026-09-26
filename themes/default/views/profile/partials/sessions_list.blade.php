@if($sessions->count() > 0)
    <div class="sessions-cards-stack">
        @foreach($sessions as $session)
            @php
                $icon = 'fa-laptop';
                if($session->device_type === 'mobile') $icon = 'fa-mobile-screen-button';
                if($session->device_type === 'tablet') $icon = 'fa-tablet-screen-button';
                
                $isRevoked = $session->revoked_at !== null;
                $isEnded = $session->ended_at !== null && !$isRevoked;
                $isActive = !$isRevoked && !$isEnded;
            @endphp
            
            <div class="session-card {{ $session->is_current ? 'current' : '' }}" id="session-card-{{ $session->id }}">
                @if($session->is_current)
                    <div class="session-card-current-bar"></div>
                @endif

                <!-- Device Icon with Visual Glow -->
                <div class="device-icon-box {{ $session->is_current ? 'is-current' : '' }}">
                    <i class="fa-solid {{ $icon }}"></i>
                </div>

                <!-- Session Info -->
                <div class="session-info-main">
                    <div class="session-info-top">
                        <h4 class="session-device-name">
                            {{ $session->browser }} {{ __('messages.on') }} {{ __('messages.' . $session->device_type) }}
                        </h4>
                        
                        @if($session->is_current)
                            <span class="session-status-badge badge-current">
                                <span class="session-live-dot"></span>
                                {{ __('messages.current_session') }}
                            </span>
                        @elseif($isActive)
                            <span class="session-status-badge badge-active">
                                <i class="fa-solid fa-circle-check"></i>
                                {{ __('messages.active') }}
                            </span>
                        @elseif($isRevoked)
                            <span class="session-status-badge badge-revoked">
                                <i class="fa-solid fa-ban"></i>
                                {{ __('messages.revoked') ?? 'Revoked' }}
                            </span>
                        @else
                            <span class="session-status-badge badge-ended">
                                <i class="fa-solid fa-clock"></i>
                                {{ __('messages.ended') ?? 'Ended' }}
                            </span>
                        @endif
                    </div>

                    <div class="session-meta-tags">
                        <span class="session-meta-item" title="{{ __('messages.ip_address') }}">
                            <i class="fa-solid fa-network-wired"></i>
                            <code>{{ $session->ip_address }}</code>
                        </span>
                        <span class="session-meta-item" title="{{ __('messages.last_activity') }}">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            {{ $session->last_seen_at ? $session->last_seen_at->diffForHumans() : __('messages.not_available') }}
                        </span>
                        <span class="session-meta-item" title="{{ __('messages.started_at') }}">
                            <i class="fa-solid fa-calendar-day"></i>
                            {{ $session->started_at ? $session->started_at->format('Y-m-d H:i') : '' }}
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                @if($isActive || $session->is_current)
                    <div class="session-actions-wrap">
                        <form action="{{ route('profile.sessions.revoke', $session->id) }}" method="POST" class="js-revoke-form" data-session-id="{{ $session->id }}" data-is-current="{{ $session->is_current ? '1' : '0' }}">
                            @csrf
                            <button type="submit" class="button {{ $session->is_current ? 'secondary' : 'primary' }} btn-session-action">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <span>{{ $session->is_current ? __('messages.logout') : __('messages.revoke_session') }}</span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    @if(method_exists($sessions, 'hasPages') && $sessions->hasPages())
        <div class="sessions-pagination-holder" id="sessionsPaginationHolder">
            {{ $sessions->links('pagination::bootstrap-5') }}
        </div>
    @endif
@else
    <div class="sessions-empty-state">
        <div class="sessions-empty-icon">
            <i class="fa-solid fa-user-lock"></i>
        </div>
        <h4 class="sessions-empty-title">{{ __('messages.no_active_sessions') }}</h4>
        <p class="sessions-empty-desc">{{ __('messages.security_member_sessions_desc') ?? 'No session records found.' }}</p>
    </div>
@endif
