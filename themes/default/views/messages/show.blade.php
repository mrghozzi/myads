@extends('theme::layouts.master')

@section('content')
<div class="account-hub-content">
    <div class="section-header">
        <div class="section-header-info">
            <p class="section-pretitle">{{ __('messages.my_profile') }}</p>
            <h2 class="section-title">{{ __('messages.msgs') }}</h2>
        </div>
    </div>

    <div class="grid grid-3-9">
        <div class="grid-column">
            <div class="widget-box">
                <div class="widget-box-content">
                    <div class="form-input small">
                        <label for="message-search">{{ __('messages.for_search') }}</label>
                        <input type="text" id="message-search" placeholder="{{ __('messages.for_search') }}">
                    </div>
                </div>
            </div>

            <div class="widget-box">
                <div class="widget-box-content">
                    <div class="user-status-list">
                        @forelse($conversations as $conversation)
                            @php
                                $partnerItem = $conversation['user'];
                                $lastMessage = $conversation['message'];
                                $isActive = $partner && $partner->id === $partnerItem->id;
                            @endphp
                            <a class="user-status {{ $isActive ? 'active' : '' }}" href="{{ route('messages.show', $partnerItem->id) }}">
                                <div class="user-status-avatar">
                                    <div class="user-avatar small no-outline {{ $partnerItem->isOnline() ? 'online' : 'offline' }}">
                                        <div class="user-avatar-content">
                                            <div class="hexagon-image-30-32" data-src="{{ $partnerItem->img ? asset($partnerItem->img) : theme_asset('img/avatar/01.jpg') }}"></div>
                                        </div>
                                        <div class="user-avatar-progress-border">
                                            <div class="hexagon-border-40-44"></div>
                                        </div>
                                    </div>
                                </div>
                                <p class="user-status-title">{{ $partnerItem->username }}</p>
                                <p class="user-status-text">{{ \Illuminate\Support\Str::limit(strip_tags($lastMessage->text), 60) }}</p>
                                <p class="user-status-timestamp">{{ \Carbon\Carbon::createFromTimestamp($lastMessage->time)->diffForHumans() }}</p>
                                <div class="mark-{{ $conversation['unread'] ? 'unread' : 'read' }}-button"></div>
                            </a>
                        @empty
                            <p class="text-center">{{ __('messages.no_msg') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
            {{ $conversations->links() }}
        </div>

        <div class="grid-column">
            <div class="chat-widget-wrap">
                <div class="chat-widget" style="width: 100%;">
                    <div class="chat-widget-header">
                        <div class="user-status">
                            <div class="user-status-avatar">
                                <a href="{{ route('profile.show', $partner->username) }}">
                                    <div class="user-avatar small no-outline {{ $partner->isOnline() ? 'online' : 'offline' }}">
                                        <div class="user-avatar-content">
                                            <div class="hexagon-image-30-32" data-src="{{ $partner->img ? asset($partner->img) : theme_asset('img/avatar/01.jpg') }}"></div>
                                        </div>
                                        <div class="user-avatar-progress-border">
                                            <div class="hexagon-border-40-44"></div>
                                        </div>
                                        @if($partner->ucheck == 1)
                                            <div class="user-avatar-badge">
                                                <div class="user-avatar-badge-border">
                                                    <div class="hexagon-22-24"></div>
                                                </div>
                                                <div class="user-avatar-badge-content">
                                                    <div class="hexagon-dark-16-18"></div>
                                                </div>
                                                <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            </div>
                            <p class="user-status-title"><span class="bold">{{ $partner->username }}</span></p>
                            <p class="user-status-tag {{ $partner->isOnline() ? 'online' : 'offline' }}">{{ $partner->isOnline() ? __('messages.online') : __('messages.offline') }}</p>
                        </div>
                    </div>

                    <div class="chat-widget-conversation" id="message_list" data-simplebar>
                        @include('theme::messages.partials.conversation', ['messages' => $messages, 'partner' => $partner, 'user' => auth()->user()])
                    </div>

                    <div class="chat-widget-form">
                        <div class="form-row split">
                            <div class="form-item">
                                <div class="interactive-input">
                                    <textarea id="message_text" placeholder="{{ __('messages.write_reply_placeholder') }}"></textarea>
                                    <div class="interactive-input-action">
                                        <svg class="interactive-input-action-icon icon-cross-thin">
                                            <use xlink:href="#svg-cross-thin"></use>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="form-item auto-width">
                                <button type="button" class="button primary padded" id="btnSend">
                                    <svg class="button-icon no-space icon-send-message">
                                        <use xlink:href="#svg-send-message"></use>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sendButton = document.getElementById('btnSend');
        const messageInput = document.getElementById('message_text');
        if (!sendButton || !messageInput) {
            return;
        }

        sendButton.addEventListener('click', postMessage);

        messageInput.addEventListener('keydown', function (event) {
            if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
                event.preventDefault();
                postMessage();
            }
        });
    });

    function postMessage() {
        const messageInput = document.getElementById('message_text');
        const messageList = document.getElementById('message_list');
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const text = messageInput ? messageInput.value.trim() : '';
        if (!text || !messageList) {
            return;
        }

        fetch('{{ route('messages.send', $partner->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ message: text })
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('Request failed');
            }
            return response.text();
        })
        .then(function (html) {
            messageList.innerHTML = html;
            messageInput.value = '';
            if (typeof initHexagons === 'function') {
                initHexagons();
            }
        })
        .catch(function () {});
    }

    setInterval(function () {
        const messageList = document.getElementById('message_list');
        if (!messageList) {
            return;
        }
        fetch('{{ route('messages.load', $partner->id) }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('Request failed');
            }
            return response.text();
        })
        .then(function (html) {
            messageList.innerHTML = html;
            if (typeof initHexagons === 'function') {
                initHexagons();
            }
        })
        .catch(function () {});
    }, 5000);
</script>
@endsection
