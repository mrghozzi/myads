@php
    $partnerConversationRouteKey = $partnerConversationRouteKey ?? ($partner ? \App\Models\Message::encodeConversationRouteKey(auth()->id(), $partner) : null);
    $threadLatestId = $messages->last()->id_msg ?? 0;
@endphp

@push('head')
    <link href="{{ theme_asset('css/messages.css') }}" rel="stylesheet" type="text/css">
@endpush

@section('content')
<div
    id="messages-app"
    class="messages-workspace account-hub-content {{ $partner ? 'has-active-conversation' : 'has-empty-conversation' }}"
    data-messages-app
    data-update-url="{{ route('messages.updates') }}"
    data-active-conversation="{{ $partnerConversationRouteKey ?? '' }}"
    data-send-url="{{ $partnerConversationRouteKey ? route('messages.send', $partnerConversationRouteKey) : '' }}"
    data-history-url="{{ $partnerConversationRouteKey ? route('messages.history', $partnerConversationRouteKey) : '' }}"
    data-latest-id="{{ $threadLatestId }}"
    data-latest-global-id="{{ (int) ($latestGlobalMessageId ?? 0) }}"
    data-unread-count="{{ (int) ($unreadConversationCount ?? 0) }}"
    data-max-attachment-bytes="5242880"
    data-sound-url="{{ theme_asset('sound/pop.wav') }}"
>
    <div class="messages-topbar">
        <div class="messages-title-block">
            <p class="messages-kicker">{{ __('messages.my_profile') }}</p>
            <h2>{{ __('messages.msgs') }}</h2>
        </div>

        <div class="messages-topbar-actions">
            <a href="{{ route('messages.create') }}" class="messages-icon-action" aria-label="{{ __('messages.send_message') }}">
                <i class="fa fa-plus" aria-hidden="true"></i>
            </a>
            <a href="{{ route('messages.index', array_filter(['id' => $partnerConversationRouteKey, 'mark_all_read' => 1])) }}" class="messages-action-link">
                <i class="fa fa-check-double" aria-hidden="true"></i>
                <span>{{ __('messages.mark_all_read') }}</span>
            </a>
        </div>
    </div>

    <div class="messages-layout">
        @if($partner)
            <button type="button" class="messages-mobile-rail-toggle" data-rail-toggle aria-expanded="false">
                <i class="fa fa-comments" aria-hidden="true"></i>
                <span>{{ __('messages.msgs') }}</span>
            </button>
        @endif

        <aside class="messages-rail messages-panel">
            <div class="messages-rail-head">
                <div>
                    <p class="messages-panel-label">{{ __('messages.msgs') }}</p>
                    <strong data-message-rail-count>{{ $conversations->total() }}</strong>
                </div>

                <a href="{{ route('messages.create') }}" class="messages-rail-compose" aria-label="{{ __('messages.send_message') }}">
                    <i class="fa fa-pen" aria-hidden="true"></i>
                </a>
            </div>

            <div class="messages-search">
                <i class="fa fa-magnifying-glass" aria-hidden="true"></i>
                <input type="search" id="message-search" placeholder="{{ __('messages.for_search') }}">
            </div>

            <div class="messages-rail-list" id="messages_conversation_list" data-conversation-list data-conversations-url="{{ route('messages.conversations') }}" data-has-more="{{ $conversations->hasPages() ? '1' : '0' }}">
                @include('theme::messages.partials.conversations', [
                    'conversations' => $conversations,
                    'partner' => $partner,
                ])
            </div>
        </aside>

        <section class="messages-chat messages-panel">
            @if($partner)
                @include('theme::messages.partials.chat_header', [
                    'partner' => $partner,
                    'partnerConversationRouteKey' => $partnerConversationRouteKey,
                ])

                <div class="messages-chat-body" id="message_list" data-message-list>
                    @include('theme::messages.partials.conversation', [
                        'messages' => $messages,
                        'partner' => $partner,
                        'user' => auth()->user(),
                        'hasOlderMessages' => $hasOlderMessages ?? false,
                        'hasPreviousConversationMessage' => $hasPreviousConversationMessage ?? false,
                        'precedingMessageEncrypted' => $precedingMessageEncrypted ?? false,
                    ])
                </div>

                @include('theme::messages.partials.composer')
            @else
                @include('theme::messages.partials.empty_state')
            @endif
        </section>
    </div>

    <div class="messages-toast-stack" id="messages_toast_stack" aria-live="polite" aria-atomic="true"></div>
</div>
@endsection

@push('scripts')
    <script src="{{ theme_asset('js/messages-app.js') }}"></script>
@endpush
