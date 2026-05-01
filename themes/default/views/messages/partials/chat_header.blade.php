<header class="messages-chat-header">
    <div class="messages-chat-identity">
        <a class="messages-chat-avatar" href="{{ route('profile.short', $partner->publicRouteIdentifier()) }}">
            <img src="{{ $partner->avatarUrl() }}" alt="{{ $partner->username }}">
            <span class="messages-presence {{ $partner->isOnline() ? 'is-online' : '' }}"></span>
        </a>

        <div class="messages-chat-user">
            <div class="messages-chat-name-row">
                <h3>{{ $partner->username }}</h3>
                @if($partner->hasVerifiedBadge())
                    <i class="fa fa-circle-check" aria-hidden="true"></i>
                @endif
            </div>
            <p class="messages-chat-status {{ $partner->isOnline() ? 'is-online' : '' }}">
                {{ $partner->isOnline() ? __('messages.online') : __('messages.offline') }}
            </p>
        </div>
    </div>

    <div class="messages-chat-actions">
        <a href="{{ route('messages.index', ['id' => $partnerConversationRouteKey, 'mark_all_read' => 1]) }}" class="messages-chat-action">
            <i class="fa fa-check-double" aria-hidden="true"></i>
            <span>{{ __('messages.mark_all_read') }}</span>
        </a>
        <a href="{{ route('settings') }}" class="messages-chat-action">
            <i class="fa fa-gear" aria-hidden="true"></i>
            <span>{{ __('messages.account_settings') }}</span>
        </a>
    </div>
</header>
