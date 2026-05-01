@forelse($conversations as $conversation)
    @php
        $partnerItem = $conversation['user'];
        $lastMessage = $conversation['message'];
        $conversationKey = $conversation['route_key'] ?? \App\Models\Message::encodeConversationRouteKey(auth()->id(), $partnerItem);
        $isActive = $partner && (int) $partner->id === (int) $partnerItem->id;
        $previewSource = trim(strip_tags((string) ($lastMessage->text ?? '')));
        if ($previewSource === '' && !empty($lastMessage->attachment_path)) {
            $previewSource = __('messages.file');
        }
        $previewText = \Illuminate\Support\Str::limit($previewSource, 96);
        $timeLabel = \Carbon\Carbon::createFromTimestamp($lastMessage->time)->diffForHumans();
    @endphp

    <a
        class="messages-conversation {{ $isActive ? 'is-active' : '' }} {{ $conversation['unread'] ? 'is-unread' : '' }}"
        href="{{ route('messages.show', $conversationKey) }}"
        data-conversation-row
        data-conversation-key="{{ $conversationKey }}"
        data-name="{{ \Illuminate\Support\Str::lower($partnerItem->username) }}"
        data-message="{{ \Illuminate\Support\Str::lower($previewSource) }}"
    >
        <span class="messages-conversation-avatar">
            <img src="{{ $partnerItem->avatarUrl() }}" alt="{{ $partnerItem->username }}">
            <span class="messages-presence {{ $partnerItem->isOnline() ? 'is-online' : '' }}"></span>
        </span>

        <span class="messages-conversation-main">
            <span class="messages-conversation-line">
                <strong>{{ $partnerItem->username }}</strong>
                <time>{{ $timeLabel }}</time>
            </span>
            <span class="messages-conversation-preview">{{ $previewText }}</span>
        </span>

        @if($conversation['unread'])
            <span class="messages-conversation-dot" aria-hidden="true"></span>
        @endif
    </a>
@empty
    <div class="messages-rail-empty" data-conversation-empty>
        <i class="fa fa-comments" aria-hidden="true"></i>
        <p>{{ __('messages.no_msg') }}</p>
    </div>
@endforelse
