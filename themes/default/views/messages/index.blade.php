@extends('theme::layouts.master')

@push('head')
<style>
  .messages-shell {
    --msg-bg: #f8f8fc;
    --msg-card: #ffffff;
    --msg-muted: #8f91ac;
    --msg-text: #3e3f5e;
    --msg-border: #eaeaf5;
    --msg-primary: #615dfa;
    --msg-accent: #23d2e2;
    --msg-soft: #f5f5fa;
    --msg-success: #1df377;
    --msg-shadow: 0 14px 30px rgba(94, 92, 154, 0.08);
  }

  [data-theme="css_d"] .messages-shell {
    --msg-bg: #171d2d;
    --msg-card: #1d2333;
    --msg-muted: #9aa4bf;
    --msg-text: #ffffff;
    --msg-border: #2f3749;
    --msg-primary: #7750f8;
    --msg-accent: #40d04f;
    --msg-soft: #21283b;
    --msg-success: #40d04f;
    --msg-shadow: 0 14px 30px rgba(0, 0, 0, 0.28);
  }

  .messages-shell {
    padding-bottom: 12px;
  }

  .messages-board {
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 20px;
  }

  .messages-card {
    border-radius: 16px;
    background: var(--msg-card);
    border: 1px solid var(--msg-border);
    box-shadow: var(--msg-shadow);
  }

  .messages-rail {
    overflow: hidden;
  }

  .messages-rail-search {
    padding: 18px;
    border-bottom: 1px solid var(--msg-border);
  }

  .messages-search-wrap {
    position: relative;
  }

  .messages-search-wrap input {
    width: 100%;
    height: 48px;
    border: 1px solid var(--msg-border);
    border-radius: 12px;
    background: var(--msg-bg);
    color: var(--msg-text);
    font-size: 0.9rem;
    font-weight: 600;
    padding: 0 44px 0 14px;
  }

  .messages-search-wrap input:focus {
    border-color: var(--msg-accent);
  }

  .messages-search-wrap i {
    position: absolute;
    top: 16px;
    right: 14px;
    color: var(--msg-muted);
  }

  .messages-rail-list {
    max-height: 610px;
    overflow: auto;
  }

  .messages-conversation {
    position: relative;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px 18px;
    border-bottom: 1px solid var(--msg-border);
    color: inherit;
    text-decoration: none;
    transition: background-color .2s ease;
  }

  .messages-conversation:hover {
    text-decoration: none;
    background: var(--msg-soft);
  }

  .messages-conversation.is-active {
    background: var(--msg-soft);
    padding-left: 14px;
  }

  .messages-conversation.is-active::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--msg-accent);
  }

  .messages-conversation-main {
    flex: 1;
    min-width: 0;
  }

  .messages-conversation-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
  }

  .messages-conversation-name {
    margin: 0;
    color: var(--msg-text);
    font-size: 1rem;
    font-weight: 700;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .messages-conversation-time {
    flex-shrink: 0;
    color: var(--msg-muted);
    font-size: 0.74rem;
    font-weight: 600;
  }

  .messages-conversation-snippet {
    margin-top: 6px;
    color: var(--msg-muted);
    font-size: 0.86rem;
    font-weight: 600;
    line-height: 1.25rem;
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .messages-conversation.is-unread .messages-conversation-name,
  .messages-conversation.is-unread .messages-conversation-snippet {
    color: var(--msg-text);
  }

  .messages-conversation-badge {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--msg-primary);
    margin-top: 8px;
    flex-shrink: 0;
  }

  .messages-rail-empty {
    color: var(--msg-muted);
    font-weight: 600;
    padding: 20px 18px;
    text-align: center;
  }

  .messages-rail-footer {
    border-top: 1px solid var(--msg-border);
    padding: 12px 18px;
  }

  .messages-chat {
    display: flex;
    flex-direction: column;
    min-height: 720px;
    overflow: hidden;
  }

  .messages-chat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 22px;
    border-bottom: 1px solid var(--msg-border);
  }

  .messages-chat-identity {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
  }

  .messages-chat-user {
    min-width: 0;
  }

  .messages-chat-name {
    margin: 0;
    color: var(--msg-text);
    font-size: 1.05rem;
    font-weight: 700;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .messages-chat-status {
    margin-top: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 18px;
    border-radius: 99px;
    padding: 0 8px;
    font-size: 0.67rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #fff;
    background: var(--msg-muted);
  }

  .messages-chat-status.is-online {
    background: var(--msg-success);
  }

  .messages-chat-actions {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-left: 12px;
  }

  .messages-chat-actions a {
    color: var(--msg-muted);
    font-size: 0.82rem;
    font-weight: 700;
    text-decoration: none;
  }

  .messages-chat-actions a:hover {
    color: var(--msg-primary);
    text-decoration: none;
  }

  .messages-chat-body {
    flex: 1;
    min-height: 0;
    height: 615px;
    padding: 22px;
    overflow-y: auto;
    overflow-x: hidden;
    background: var(--msg-bg);
  }

  .messages-thread {
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .messages-bubble-row {
    display: flex;
    align-items: flex-end;
    gap: 10px;
    max-width: 86%;
  }

  .messages-bubble-row.is-me {
    margin-left: auto;
    justify-content: flex-end;
  }

  .messages-bubble-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
  }

  .messages-bubble-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .messages-bubble-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 0;
  }

  .messages-bubble {
    margin: 0;
    border-radius: 12px;
    background: var(--msg-soft);
    color: var(--msg-text);
    font-size: 0.92rem;
    font-weight: 600;
    line-height: 1.35rem;
    padding: 11px 14px;
    white-space: normal;
    word-break: break-word;
  }

  .messages-bubble-row.is-me .messages-bubble {
    background: var(--msg-primary);
    color: #fff;
  }

  .messages-image-attachment {
    display: block;
    width: min(100%, 340px);
    max-width: 100%;
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid var(--msg-border);
    background: #dfe3f4;
    box-shadow: 0 8px 18px rgba(62, 63, 94, 0.14);
  }

  .messages-image-attachment img {
    display: block;
    width: 100%;
    height: clamp(150px, 22vw, 250px);
    object-fit: cover;
  }

  .messages-image-attachment.is-me {
    border-color: rgba(255, 255, 255, 0.36);
    box-shadow: 0 8px 18px rgba(14, 18, 33, 0.32);
  }

  .messages-image-meta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    max-width: min(100%, 340px);
    border-radius: 999px;
    border: 1px solid var(--msg-border);
    background: #fff;
    color: var(--msg-text);
    padding: 7px 12px;
    font-size: 0.74rem;
    font-weight: 700;
    text-decoration: none;
  }

  .messages-image-meta:hover {
    color: var(--msg-primary);
    text-decoration: none;
  }

  .messages-image-meta-name {
    min-width: 0;
    max-width: 170px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .messages-image-meta-size {
    color: var(--msg-muted);
    font-weight: 600;
  }

  .messages-image-meta.is-me {
    background: rgb(97, 93, 250);
    border-color: rgba(255, 255, 255, 0.36);
    color: #fff;
  }

  .messages-image-meta.is-me .messages-image-meta-size {
    color: rgba(255, 255, 255, 0.84);
  }

  .messages-image-meta.is-me:hover {
    color: #fff;
  }

  .messages-file-attachment {
    display: flex;
    align-items: center;
    gap: 10px;
    width: min(100%, 340px);
    max-width: 100%;
    border: 1px solid var(--msg-border);
    border-radius: 12px;
    background: #fff;
    color: var(--msg-text);
    padding: 9px 10px;
    text-decoration: none;
  }

  .messages-file-attachment:hover {
    color: var(--msg-primary);
    text-decoration: none;
  }

  .messages-file-attachment.is-me {
    background: rgb(97, 93, 250);
    border-color: rgba(255, 255, 255, 0.36);
    color: #fff;
  }

  .messages-file-attachment.is-me:hover {
    color: #fff;
  }

  .messages-file-attachment-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    border: 1px solid rgba(97, 93, 250, 0.18);
    background: rgba(97, 93, 250, 0.1);
    color: var(--msg-primary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .messages-file-attachment.is-me .messages-file-attachment-icon {
    border-color: rgba(255, 255, 255, 0.36);
    background: rgba(255, 255, 255, 0.16);
    color: #fff;
  }

  .messages-file-attachment-meta {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
  }

  .messages-file-attachment-name {
    font-size: 0.82rem;
    font-weight: 700;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .messages-file-attachment-sub {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.71rem;
    font-weight: 700;
  }

  .messages-file-attachment-ext {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 19px;
    border-radius: 999px;
    padding: 0 7px;
    background: rgba(97, 93, 250, 0.1);
    color: var(--msg-primary);
    letter-spacing: .04em;
  }

  .messages-file-attachment.is-me .messages-file-attachment-ext {
    background: rgba(255, 255, 255, 0.18);
    color: #fff;
  }

  .messages-file-attachment-size {
    color: var(--msg-muted);
    font-weight: 600;
  }

  .messages-file-attachment.is-me .messages-file-attachment-size {
    color: rgba(255, 255, 255, 0.84);
  }

  .messages-file-attachment-action {
    color: var(--msg-muted);
    font-size: 0.8rem;
    flex-shrink: 0;
  }

  .messages-file-attachment.is-me .messages-file-attachment-action {
    color: rgba(255, 255, 255, 0.88);
  }

  .messages-bubble-time {
    color: var(--msg-muted);
    font-size: 0.71rem;
    font-weight: 600;
  }

  .messages-bubble-row.is-me .messages-bubble-time {
    text-align: right;
  }

  .messages-empty-thread {
    color: var(--msg-muted);
    font-size: .9rem;
    font-weight: 600;
    text-align: center;
    padding: 8px 0;
  }

  .messages-chat-compose {
    border-top: 1px solid var(--msg-border);
    padding: 16px 22px;
    background: var(--msg-card);
  }

  .messages-compose-row {
    display: flex;
    align-items: flex-end;
    gap: 10px;
  }

  .messages-compose-attach {
    width: 46px;
    height: 46px;
    border: 1px solid var(--msg-border);
    border-radius: 12px;
    background: var(--msg-soft);
    color: var(--msg-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
  }

  .messages-compose-input {
    flex: 1;
    min-width: 0;
    position: relative;
  }

  .messages-compose-input textarea {
    width: 100%;
    min-height: 46px;
    max-height: 124px;
    resize: none;
    border: 1px solid var(--msg-border);
    border-radius: 12px;
    background: var(--msg-bg);
    color: var(--msg-text);
    padding: 12px 46px 12px 14px;
    font-size: .9rem;
    font-weight: 600;
    line-height: 1.3rem;
  }

  .messages-compose-input textarea:focus {
    border-color: var(--msg-accent);
  }

  .messages-compose-emoji-btn {
    position: absolute;
    right: 10px;
    top: 10px;
    width: 26px;
    height: 26px;
    border: none;
    border-radius: 8px;
    background: transparent;
    color: #b2b7cc;
    font-size: 1.18rem;
    line-height: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 5;
  }

  .messages-compose-emoji-btn:hover,
  .messages-compose-emoji-btn.is-active {
    color: var(--msg-primary);
  }

  .messages-emoji-panel {
    position: absolute;
    right: 0;
    bottom: calc(100% + 10px);
    width: min(470px, calc(100vw - 56px));
    border: 1px solid var(--msg-border);
    border-radius: 12px;
    background: var(--msg-card);
    box-shadow: 0 18px 34px rgba(62, 63, 94, 0.22);
    overflow: hidden;
    display: none;
    z-index: 30;
  }

  .messages-emoji-panel.is-open {
    display: block;
  }

  .messages-emoji-tabs {
    display: flex;
    align-items: center;
    gap: 2px;
    padding: 8px;
    background: #eef1f7;
    border-bottom: 1px solid var(--msg-border);
    overflow-x: auto;
  }

  .messages-emoji-tab {
    width: 42px;
    min-width: 42px;
    height: 38px;
    border: none;
    border-radius: 10px;
    background: transparent;
    color: #616b84;
    font-size: 1.16rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .messages-emoji-tab.is-active {
    background: #fff;
    box-shadow: 0 1px 1px rgba(38, 41, 51, 0.08);
  }

  .messages-emoji-toolbar {
    padding: 10px 12px;
    border-bottom: 1px solid var(--msg-border);
  }

  .messages-emoji-search {
    width: 100%;
    height: 42px;
    border: 1px solid var(--msg-border);
    border-radius: 9px;
    background: var(--msg-bg);
    color: var(--msg-text);
    padding: 0 12px;
    font-size: .9rem;
    font-weight: 600;
  }

  .messages-emoji-body {
    max-height: 260px;
    overflow: auto;
    padding: 10px 12px 12px;
  }

  .messages-emoji-grid {
    display: grid;
    grid-template-columns: repeat(8, minmax(0, 1fr));
    gap: 6px;
  }

  .messages-emoji-item {
    width: 100%;
    aspect-ratio: 1 / 1;
    border: none;
    border-radius: 10px;
    background: transparent;
    font-size: 1.35rem;
    line-height: 1;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .messages-emoji-item:hover {
    background: #edf1f9;
  }

  .messages-emoji-empty {
    color: var(--msg-muted);
    font-size: .86rem;
    font-weight: 700;
    padding: 8px 2px;
  }

  .messages-compose-file {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-top: 8px;
    border: 1px dashed var(--msg-border);
    border-radius: 10px;
    padding: 8px 10px;
    background: var(--msg-soft);
    color: var(--msg-text);
    font-size: 0.78rem;
    font-weight: 700;
  }

  .messages-compose-file.is-hidden {
    display: none;
  }

  .messages-compose-file-name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .messages-compose-file-clear {
    border: none;
    background: transparent;
    color: var(--msg-muted);
    font-size: 0.8rem;
    font-weight: 700;
    cursor: pointer;
    padding: 0;
  }

  .messages-compose-file-clear:hover {
    color: #ff4e7a;
  }

  .messages-compose-error {
    margin-top: 7px;
    color: #ff4e7a;
    font-size: 0.78rem;
    font-weight: 700;
    min-height: 1.1em;
  }

  .messages-compose-send {
    width: 56px;
    height: 46px;
    border-radius: 12px;
    border: none;
    background: var(--msg-accent);
    color: #fff;
    font-size: 1.05rem;
    cursor: pointer;
    flex-shrink: 0;
  }

  .messages-compose-send:hover {
    filter: brightness(.96);
  }

  .messages-chat-empty {
    min-height: 260px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--msg-muted);
    font-size: 0.95rem;
    font-weight: 700;
    text-align: center;
    padding: 28px;
  }

  .messages-rails-pagination .pagination {
    margin: 0;
  }

  .messages-rails-pagination .pagination li a,
  .messages-rails-pagination .pagination li span {
    border-radius: 8px;
  }

  @media (max-width: 1200px) {
    .messages-board {
      grid-template-columns: 320px 1fr;
    }
  }

  @media (max-width: 960px) {
    .messages-board {
      grid-template-columns: 1fr;
    }

    .messages-chat {
      min-height: 620px;
    }

    .messages-chat-body {
      height: 460px;
    }

    .messages-rail-list {
      max-height: 360px;
    }

    .messages-chat-actions a {
      font-size: .75rem;
    }

    .messages-emoji-grid {
      grid-template-columns: repeat(7, minmax(0, 1fr));
    }
  }
</style>
@endpush

@section('content')
<div class="messages-shell account-hub-content">
    <div class="section-header">
        <div class="section-header-info">
            <p class="section-pretitle">{{ __('messages.my_profile') }}</p>
            <h2 class="section-title">{{ __('messages.msgs') }}</h2>
        </div>
    </div>

    <div class="messages-board">
        <aside class="messages-card messages-rail">
            <div class="messages-rail-search">
                <div class="messages-search-wrap">
                    <input type="text" id="message-search" placeholder="{{ __('messages.for_search') }}">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </div>
            </div>

            <div class="messages-rail-list" id="messages_conversation_list">
                @forelse($conversations as $conversation)
                    @php
                        $partnerItem = $conversation['user'];
                        $lastMessage = $conversation['message'];
                        $isActive = $partner && $partner->id === $partnerItem->id;
                        $previewSource = trim(strip_tags((string) ($lastMessage->text ?? '')));
                        if ($previewSource === '' && !empty($lastMessage->attachment_path)) {
                            $previewSource = __('messages.file');
                        }
                        $previewText = \Illuminate\Support\Str::limit($previewSource, 78);
                    @endphp
                    <a
                        class="messages-conversation {{ $isActive ? 'is-active' : '' }} {{ $conversation['unread'] ? 'is-unread' : '' }}"
                        href="{{ route('messages.show', $partnerItem->id) }}"
                        data-name="{{ \Illuminate\Support\Str::lower($partnerItem->username) }}"
                        data-message="{{ \Illuminate\Support\Str::lower(strip_tags($lastMessage->text)) }}"
                    >
                        <div class="user-avatar small no-outline {{ $partnerItem->isOnline() ? 'online' : 'offline' }}">
                            <div class="user-avatar-content">
                                <div class="hexagon-image-30-32" data-src="{{ $partnerItem->img ? asset($partnerItem->img) : theme_asset('img/avatar/01.jpg') }}"></div>
                            </div>
                            <div class="user-avatar-progress-border">
                                <div class="hexagon-border-40-44"></div>
                            </div>
                        </div>

                        <div class="messages-conversation-main">
                            <div class="messages-conversation-title">
                                <p class="messages-conversation-name">{{ $partnerItem->username }}</p>
                                <span class="messages-conversation-time">{{ \Carbon\Carbon::createFromTimestamp($lastMessage->time)->diffForHumans() }}</span>
                            </div>
                            <p class="messages-conversation-snippet">{{ $previewText }}</p>
                        </div>

                        @if($conversation['unread'])
                            <span class="messages-conversation-badge" aria-hidden="true"></span>
                        @endif
                    </a>
                @empty
                    <p class="messages-rail-empty">{{ __('messages.no_msg') }}</p>
                @endforelse
            </div>

            @if($conversations->hasPages())
                <div class="messages-rail-footer messages-rails-pagination">
                    {{ $conversations->links() }}
                </div>
            @endif
        </aside>

        <section class="messages-card messages-chat">
            @if($partner)
                <header class="messages-chat-header">
                    <div class="messages-chat-identity">
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

                        <div class="messages-chat-user">
                            <p class="messages-chat-name">{{ $partner->username }}</p>
                            <span class="messages-chat-status {{ $partner->isOnline() ? 'is-online' : '' }}">
                                {{ $partner->isOnline() ? __('messages.online') : __('messages.offline') }}
                            </span>
                        </div>
                    </div>

                    <div class="messages-chat-actions">
                        <a href="{{ route('messages.index', ['id' => $partner->id, 'mark_all_read' => 1]) }}">{{ __('messages.mark_all_read') }}</a>
                        <a href="{{ route('settings') }}">{{ __('messages.account_settings') }}</a>
                    </div>
                </header>

                <div class="messages-chat-body" id="message_list" data-simplebar data-simplebar-auto-hide="false">
                    @include('theme::messages.partials.conversation', [
                        'messages' => $messages,
                        'partner' => $partner,
                        'user' => auth()->user(),
                        'hasOlderMessages' => $hasOlderMessages ?? false
                    ])
                </div>

                <div class="messages-chat-compose">
                    <div class="messages-compose-row">
                        <button type="button" class="messages-compose-attach" id="btnAttach" aria-label="{{ __('messages.file') }}">
                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                        </button>

                        <div class="messages-compose-input">
                            <textarea id="message_text" placeholder="{{ __('messages.write_reply_placeholder') }}"></textarea>
                            <button type="button" class="messages-compose-emoji-btn" id="btnEmoji" aria-label="Emoji">
                                <i aria-hidden="true" class="fa fa-smile"></i>
                            </button>
                            <div class="messages-emoji-panel" id="emoji_panel" aria-hidden="true">
                                <div class="messages-emoji-tabs" id="emoji_tabs">
                                    <button type="button" class="messages-emoji-tab is-active" data-emoji-category="smileys">😀</button>
                                    <button type="button" class="messages-emoji-tab" data-emoji-category="animals">🐶</button>
                                    <button type="button" class="messages-emoji-tab" data-emoji-category="food">🍕</button>
                                    <button type="button" class="messages-emoji-tab" data-emoji-category="activity">⚽</button>
                                    <button type="button" class="messages-emoji-tab" data-emoji-category="travel">🚀</button>
                                    <button type="button" class="messages-emoji-tab" data-emoji-category="objects">💡</button>
                                    <button type="button" class="messages-emoji-tab" data-emoji-category="symbols">💜</button>
                                    <button type="button" class="messages-emoji-tab" data-emoji-category="flags">🇬🇧</button>
                                </div>
                                <div class="messages-emoji-toolbar">
                                    <input type="text" id="emoji_search" class="messages-emoji-search" placeholder="{{ __('messages.for_search') }}">
                                </div>
                                <div class="messages-emoji-body">
                                    <div class="messages-emoji-grid" id="emoji_grid"></div>
                                </div>
                            </div>
                            <input type="file" id="message_attachment" class="d-none" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.zip,.rar,.7z,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv" aria-hidden="true">
                            <div class="messages-compose-file is-hidden" id="message_attachment_meta">
                                <span class="messages-compose-file-name" id="message_attachment_name"></span>
                                <button type="button" class="messages-compose-file-clear" id="message_attachment_clear">&times;</button>
                            </div>
                            <p class="messages-compose-error" id="message_compose_error" aria-live="polite"></p>
                        </div>

                        <button type="button" class="messages-compose-send" id="btnSend" aria-label="{{ __('messages.send') }}">
                            <i class="fa fa-paper-plane" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            @else
                <div class="messages-chat-empty">
                    {{ __('messages.no_msg') }}
                </div>
            @endif
        </section>
    </div>
</div>
@endsection

@push('scripts')
@php
    $emojiFlagCountries = [];
    $regionsPath = base_path('vendor/nesbot/carbon/src/Carbon/List/regions.php');
    if (is_file($regionsPath)) {
        $regions = require $regionsPath;
        if (is_array($regions)) {
            foreach ($regions as $code => $name) {
                $normalizedCode = strtoupper((string) $code);
                if (!preg_match('/^[A-Z]{2}$/', $normalizedCode) || $normalizedCode === 'IL') {
                    continue;
                }

                $emojiFlagCountries[] = [
                    'code' => $normalizedCode,
                    'label' => (string) $name,
                ];
            }
        }
    }
@endphp
<script>
  (function () {
    const searchInput = document.getElementById('message-search');
    const conversationList = document.getElementById('messages_conversation_list');
    const messageList = document.getElementById('message_list');
    const messageInput = document.getElementById('message_text');
    const sendButton = document.getElementById('btnSend');
    const attachButton = document.getElementById('btnAttach');
    const attachmentInput = document.getElementById('message_attachment');
    const attachmentMeta = document.getElementById('message_attachment_meta');
    const attachmentName = document.getElementById('message_attachment_name');
    const attachmentClear = document.getElementById('message_attachment_clear');
    const composeError = document.getElementById('message_compose_error');
    const emojiButton = document.getElementById('btnEmoji');
    const emojiPanel = document.getElementById('emoji_panel');
    const emojiSearchInput = document.getElementById('emoji_search');
    const emojiGrid = document.getElementById('emoji_grid');
    const sendUrl = '{{ $partner ? route('messages.send', $partner->id) : '' }}';
    const loadUrl = '{{ $partner ? route('messages.load', $partner->id) : '' }}';
    const historyUrl = '{{ $partner ? route('messages.history', $partner->id) : '' }}';
    const historyTopThreshold = 32;
    const maxAttachmentBytes = 5 * 1024 * 1024;
    let refreshTimer = null;
    let oldestMessageId = 0;
    let latestMessageId = 0;
    let hasOlderMessages = false;
    let historyLoading = false;
    let historyDone = false;
    let refreshLoading = false;
    let sendLoading = false;
    let activeEmojiCategory = 'smileys';
    const emojiFlagCountries = @json($emojiFlagCountries, JSON_UNESCAPED_UNICODE);

    function countryCodeToFlagEmoji(countryCode) {
      const normalized = String(countryCode || '').trim().toUpperCase();
      if (!/^[A-Z]{2}$/.test(normalized)) {
        return '';
      }
      const regionalIndicatorOffset = 127397;
      return String.fromCodePoint(
        regionalIndicatorOffset + normalized.charCodeAt(0),
        regionalIndicatorOffset + normalized.charCodeAt(1)
      );
    }

    function buildEmojiFlags() {
      const byCode = new Map();

      const pushCountry = function (countryCode, countryLabel) {
        const code = String(countryCode || '').trim().toUpperCase();
        if (!/^[A-Z]{2}$/.test(code) || code === 'IL' || byCode.has(code)) {
          return;
        }

        const flag = countryCodeToFlagEmoji(code);
        if (!flag) {
          return;
        }

        const label = String(countryLabel || code).trim().toLowerCase();
        byCode.set(code, { char: flag, label: label || code.toLowerCase() });
      };

      if (Array.isArray(emojiFlagCountries) && emojiFlagCountries.length) {
        emojiFlagCountries.forEach(function (country) {
          if (!country || typeof country !== 'object') {
            return;
          }
          pushCountry(country.code, country.label);
        });
      } else if (typeof Intl !== 'undefined' && typeof Intl.supportedValuesOf === 'function') {
        const regionNames = typeof Intl.DisplayNames === 'function'
          ? new Intl.DisplayNames(['en'], { type: 'region' })
          : null;

        Intl.supportedValuesOf('region').forEach(function (regionCode) {
          if (!/^[A-Z]{2}$/.test(regionCode)) {
            return;
          }
          const regionLabel = regionNames ? regionNames.of(regionCode) : regionCode;
          pushCountry(regionCode, regionLabel);
        });
      } else {
        ['US', 'GB', 'FR', 'DE', 'ES', 'IT', 'PT', 'TR', 'SA', 'AE', 'EG', 'MA', 'DZ', 'QA', 'KW', 'JO', 'IQ', 'PS']
          .forEach(function (regionCode) {
            pushCountry(regionCode, regionCode);
          });
      }

      return Array.from(byCode.values()).sort(function (a, b) {
        return a.label.localeCompare(b.label);
      });
    }

    const emojiCatalog = {
      smileys: [
        { char: '😀', label: 'grinning' }, { char: '😃', label: 'smile' }, { char: '😄', label: 'smile eyes' },
        { char: '😁', label: 'beaming' }, { char: '😆', label: 'laugh' }, { char: '😅', label: 'sweat smile' },
        { char: '🤣', label: 'rofl' }, { char: '😂', label: 'joy tears' }, { char: '🙂', label: 'slight smile' },
        { char: '😉', label: 'wink' }, { char: '😊', label: 'blush' }, { char: '😍', label: 'heart eyes' },
        { char: '😘', label: 'kiss' }, { char: '😋', label: 'yum' }, { char: '😎', label: 'cool sunglasses' },
        { char: '🤩', label: 'star struck' }, { char: '🥳', label: 'party' }, { char: '😴', label: 'sleeping' },
        { char: '🤔', label: 'thinking' }, { char: '🙄', label: 'rolling eyes' }, { char: '😬', label: 'grimace' },
        { char: '😢', label: 'cry' }, { char: '😭', label: 'sob' }, { char: '😤', label: 'steam nose' },
        { char: '😡', label: 'angry' }, { char: '🤯', label: 'mind blown' }, { char: '🥺', label: 'pleading' },
        { char: '😱', label: 'scream' }, { char: '🤗', label: 'hug' }, { char: '🤭', label: 'giggle' }
      ],
      animals: [
        { char: '🐶', label: 'dog' }, { char: '🐱', label: 'cat' }, { char: '🐭', label: 'mouse' },
        { char: '🐹', label: 'hamster' }, { char: '🐰', label: 'rabbit' }, { char: '🦊', label: 'fox' },
        { char: '🐻', label: 'bear' }, { char: '🐼', label: 'panda' }, { char: '🐨', label: 'koala' },
        { char: '🐯', label: 'tiger' }, { char: '🦁', label: 'lion' }, { char: '🐮', label: 'cow' },
        { char: '🐷', label: 'pig' }, { char: '🐸', label: 'frog' }, { char: '🐵', label: 'monkey' },
        { char: '🐔', label: 'chicken' }, { char: '🐧', label: 'penguin' }, { char: '🦄', label: 'unicorn' },
        { char: '🐝', label: 'bee' }, { char: '🦋', label: 'butterfly' }, { char: '🐢', label: 'turtle' },
        { char: '🐙', label: 'octopus' }, { char: '🐬', label: 'dolphin' }, { char: '🦈', label: 'shark' }
      ],
      food: [
        { char: '🍎', label: 'apple' }, { char: '🍌', label: 'banana' }, { char: '🍓', label: 'strawberry' },
        { char: '🍒', label: 'cherry' }, { char: '🍍', label: 'pineapple' }, { char: '🥑', label: 'avocado' },
        { char: '🍔', label: 'burger' }, { char: '🍟', label: 'fries' }, { char: '🍕', label: 'pizza' },
        { char: '🌭', label: 'hotdog' }, { char: '🥪', label: 'sandwich' }, { char: '🍝', label: 'pasta' },
        { char: '🍣', label: 'sushi' }, { char: '🥗', label: 'salad' }, { char: '🍜', label: 'ramen' },
        { char: '🍰', label: 'cake' }, { char: '🧁', label: 'cupcake' }, { char: '🍪', label: 'cookie' },
        { char: '🍩', label: 'donut' }, { char: '🍫', label: 'chocolate' }, { char: '☕', label: 'coffee' },
        { char: '🍵', label: 'tea' }, { char: '🥤', label: 'drink' }, { char: '🍹', label: 'cocktail' }
      ],
      activity: [
        { char: '⚽', label: 'soccer' }, { char: '🏀', label: 'basketball' }, { char: '🏈', label: 'football' },
        { char: '⚾', label: 'baseball' }, { char: '🎾', label: 'tennis' }, { char: '🏐', label: 'volleyball' },
        { char: '🎱', label: 'billiards' }, { char: '🏓', label: 'ping pong' }, { char: '🥊', label: 'boxing' },
        { char: '🥇', label: 'gold medal' }, { char: '🏆', label: 'trophy' }, { char: '🎮', label: 'video game' },
        { char: '🕹️', label: 'joystick' }, { char: '🎲', label: 'dice' }, { char: '🎯', label: 'target' },
        { char: '🎸', label: 'guitar' }, { char: '🎹', label: 'piano' }, { char: '🎤', label: 'microphone' },
        { char: '🎧', label: 'headphones' }, { char: '🎬', label: 'movie' }, { char: '📚', label: 'books' }
      ],
      travel: [
        { char: '🚗', label: 'car' }, { char: '🚕', label: 'taxi' }, { char: '🚌', label: 'bus' },
        { char: '🚎', label: 'tram' }, { char: '🏎️', label: 'race car' }, { char: '🚓', label: 'police car' },
        { char: '🚑', label: 'ambulance' }, { char: '🚒', label: 'fire truck' }, { char: '🚜', label: 'tractor' },
        { char: '✈️', label: 'airplane' }, { char: '🚀', label: 'rocket' }, { char: '🛸', label: 'ufo' },
        { char: '⛵', label: 'boat' }, { char: '🚢', label: 'ship' }, { char: '🏖️', label: 'beach' },
        { char: '🏝️', label: 'island' }, { char: '⛰️', label: 'mountain' }, { char: '🗽', label: 'liberty' },
        { char: '🗼', label: 'tower' }, { char: '🌋', label: 'volcano' }, { char: '🌍', label: 'earth' }
      ],
      objects: [
        { char: '💡', label: 'idea bulb' }, { char: '📱', label: 'phone' }, { char: '💻', label: 'laptop' },
        { char: '⌚', label: 'watch' }, { char: '📷', label: 'camera' }, { char: '🎥', label: 'video camera' },
        { char: '🔦', label: 'flashlight' }, { char: '🧭', label: 'compass' }, { char: '📌', label: 'pin' },
        { char: '📎', label: 'paperclip' }, { char: '✏️', label: 'pencil' }, { char: '🖊️', label: 'pen' },
        { char: '📖', label: 'book' }, { char: '🧾', label: 'receipt' }, { char: '💎', label: 'diamond' },
        { char: '💰', label: 'money bag' }, { char: '💳', label: 'card' }, { char: '🔑', label: 'key' },
        { char: '🧸', label: 'teddy bear' }, { char: '🎁', label: 'gift' }, { char: '🪄', label: 'magic wand' }
      ],
      symbols: [
        { char: '❤️', label: 'red heart' }, { char: '🧡', label: 'orange heart' }, { char: '💛', label: 'yellow heart' },
        { char: '💚', label: 'green heart' }, { char: '💙', label: 'blue heart' }, { char: '💜', label: 'purple heart' },
        { char: '🖤', label: 'black heart' }, { char: '🤍', label: 'white heart' }, { char: '🤎', label: 'brown heart' },
        { char: '💔', label: 'broken heart' }, { char: '❣️', label: 'heart exclamation' }, { char: '💕', label: 'two hearts' },
        { char: '✨', label: 'sparkles' }, { char: '🔥', label: 'fire' }, { char: '💯', label: 'hundred' },
        { char: '✅', label: 'check' }, { char: '❌', label: 'cross mark' }, { char: '⚠️', label: 'warning' },
        { char: '🔔', label: 'bell' }, { char: '⭐', label: 'star' }, { char: '🌟', label: 'glowing star' }
      ],
      flags: buildEmojiFlags()
    };

    function getScrollWrapper() {
      if (!messageList) {
        return null;
      }
      return messageList.querySelector('.simplebar-content-wrapper') || messageList;
    }

    function getThreadNode() {
      if (!messageList) {
        return null;
      }
      return messageList.querySelector('.messages-thread');
    }

    function getThreadRows() {
      const thread = getThreadNode();
      if (!thread) {
        return [];
      }
      return Array.from(thread.querySelectorAll('.messages-bubble-row[data-message-id]'));
    }

    function parseMessageId(value) {
      const parsed = parseInt(value, 10);
      return Number.isFinite(parsed) && parsed > 0 ? parsed : 0;
    }

    function syncMessageBounds() {
      const thread = getThreadNode();
      if (!thread) {
        oldestMessageId = 0;
        latestMessageId = 0;
        hasOlderMessages = false;
        historyDone = true;
        return;
      }

      oldestMessageId = parseMessageId(thread.getAttribute('data-oldest-id') || 0);
      latestMessageId = parseMessageId(thread.getAttribute('data-latest-id') || 0);
      hasOlderMessages = thread.getAttribute('data-has-older') === '1';

      const rows = getThreadRows();
      if (rows.length > 0) {
        if (!oldestMessageId) {
          oldestMessageId = parseMessageId(rows[0].getAttribute('data-message-id'));
        }
        if (!latestMessageId) {
          latestMessageId = parseMessageId(rows[rows.length - 1].getAttribute('data-message-id'));
        }
      }

      historyDone = !hasOlderMessages || !oldestMessageId;
    }

    function updateThreadBoundsAttributes() {
      const thread = getThreadNode();
      if (!thread) {
        return;
      }
      thread.setAttribute('data-oldest-id', String(oldestMessageId || 0));
      thread.setAttribute('data-latest-id', String(latestMessageId || 0));
      thread.setAttribute('data-has-older', hasOlderMessages ? '1' : '0');
    }

    function removeEmptyPlaceholder() {
      if (!messageList) {
        return;
      }
      const emptyNode = messageList.querySelector('.messages-empty-thread');
      if (emptyNode) {
        emptyNode.remove();
      }
    }

    function htmlToRows(html) {
      if (!html) {
        return [];
      }
      const container = document.createElement('div');
      container.innerHTML = html;
      return Array.from(container.children).filter(function (node) {
        return node.classList && node.classList.contains('messages-bubble-row');
      });
    }

    function collectExistingRowIds() {
      const ids = new Set();
      getThreadRows().forEach(function (row) {
        const id = row.getAttribute('data-message-id');
        if (id) {
          ids.add(id);
        }
      });
      return ids;
    }

    function sortRowsAscending(rows) {
      return rows.slice().sort(function (a, b) {
        const aId = parseMessageId(a.getAttribute('data-message-id'));
        const bId = parseMessageId(b.getAttribute('data-message-id'));
        return aId - bId;
      });
    }

    function syncBoundsFromDom() {
      const rows = getThreadRows();
      if (rows.length === 0) {
        oldestMessageId = 0;
        latestMessageId = 0;
        updateThreadBoundsAttributes();
        return;
      }

      oldestMessageId = parseMessageId(rows[0].getAttribute('data-message-id'));
      latestMessageId = parseMessageId(rows[rows.length - 1].getAttribute('data-message-id'));
      updateThreadBoundsAttributes();
    }

    function normalizeThreadOrder() {
      const thread = getThreadNode();
      if (!thread) {
        return;
      }

      const rows = sortRowsAscending(getThreadRows());
      if (rows.length === 0) {
        return;
      }

      const fragment = document.createDocumentFragment();
      rows.forEach(function (row) {
        fragment.appendChild(row);
      });
      thread.appendChild(fragment);
      syncBoundsFromDom();
    }

    function updateBoundsFromRows(rows) {
      rows.forEach(function (row) {
        const id = parseMessageId(row.getAttribute('data-message-id'));
        if (!id) {
          return;
        }
        if (!oldestMessageId || id < oldestMessageId) {
          oldestMessageId = id;
        }
        if (!latestMessageId || id > latestMessageId) {
          latestMessageId = id;
        }
      });
      updateThreadBoundsAttributes();
    }

    function appendRows(rows, forceBottom) {
      const thread = getThreadNode();
      if (!thread || rows.length === 0) {
        return 0;
      }

      const wrapper = getScrollWrapper();
      const shouldStickBottom = forceBottom || (wrapper
        ? (wrapper.scrollHeight - wrapper.scrollTop - wrapper.clientHeight) < 80
        : true);
      const existingIds = collectExistingRowIds();
      const incomingRows = sortRowsAscending(rows);

      const fragment = document.createDocumentFragment();
      let addedCount = 0;
      incomingRows.forEach(function (row) {
        const id = row.getAttribute('data-message-id');
        if (id && existingIds.has(id)) {
          return;
        }
        fragment.appendChild(row);
        if (id) {
          existingIds.add(id);
        }
        addedCount += 1;
      });

      if (!addedCount) {
        return 0;
      }

      removeEmptyPlaceholder();
      thread.appendChild(fragment);
      updateBoundsFromRows(incomingRows);
      normalizeThreadOrder();
      refreshHexagons();

      if (wrapper && shouldStickBottom) {
        wrapper.scrollTop = wrapper.scrollHeight;
      }

      return addedCount;
    }

    function prependRows(rows) {
      const thread = getThreadNode();
      if (!thread || rows.length === 0) {
        return 0;
      }

      const wrapper = getScrollWrapper();
      const beforeHeight = wrapper ? wrapper.scrollHeight : 0;
      const beforeTop = wrapper ? wrapper.scrollTop : 0;
      const existingIds = collectExistingRowIds();
      const incomingRows = sortRowsAscending(rows);

      const fragment = document.createDocumentFragment();
      let addedCount = 0;
      incomingRows.forEach(function (row) {
        const id = row.getAttribute('data-message-id');
        if (id && existingIds.has(id)) {
          return;
        }
        fragment.appendChild(row);
        if (id) {
          existingIds.add(id);
        }
        addedCount += 1;
      });

      if (!addedCount) {
        return 0;
      }

      removeEmptyPlaceholder();
      thread.insertBefore(fragment, thread.firstChild);
      updateBoundsFromRows(incomingRows);
      normalizeThreadOrder();
      refreshHexagons();

      if (wrapper) {
        const addedHeight = wrapper.scrollHeight - beforeHeight;
        if (beforeTop <= historyTopThreshold + 1) {
          wrapper.scrollTop = 8;
        } else {
          wrapper.scrollTop = beforeTop + addedHeight;
        }
      }

      return addedCount;
    }

    function scrollThreadToBottom() {
      const wrapper = getScrollWrapper();
      if (!wrapper) {
        return;
      }
      wrapper.scrollTop = wrapper.scrollHeight;
    }

    function refreshHexagons() {
      if (typeof initHexagons === 'function') {
        initHexagons();
      }
    }

    function setComposeError(message) {
      if (!composeError) {
        return;
      }
      composeError.textContent = message || '';
    }

    function clearComposeError() {
      setComposeError('');
    }

    function formatBytes(bytes) {
      if (!bytes || bytes <= 0) {
        return '0 B';
      }
      if (bytes >= 1024 * 1024) {
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
      }
      return (bytes / 1024).toFixed(1) + ' KB';
    }

    function resetAttachmentInput() {
      if (attachmentInput) {
        attachmentInput.value = '';
      }
      if (attachmentName) {
        attachmentName.textContent = '';
      }
      if (attachmentMeta) {
        attachmentMeta.classList.add('is-hidden');
      }
    }

    function applyAttachmentMeta(file) {
      if (!attachmentMeta || !attachmentName) {
        return;
      }

      if (!file) {
        resetAttachmentInput();
        return;
      }

      attachmentName.textContent = file.name + ' (' + formatBytes(file.size) + ')';
      attachmentMeta.classList.remove('is-hidden');
    }

    function setEmojiPanelOpen(open) {
      if (!emojiPanel || !emojiButton) {
        return;
      }

      if (open) {
        emojiPanel.classList.add('is-open');
        emojiPanel.setAttribute('aria-hidden', 'false');
        emojiButton.classList.add('is-active');
      } else {
        emojiPanel.classList.remove('is-open');
        emojiPanel.setAttribute('aria-hidden', 'true');
        emojiButton.classList.remove('is-active');
      }
    }

    function insertEmojiAtCaret(char) {
      if (!messageInput) {
        return;
      }

      const start = messageInput.selectionStart ?? messageInput.value.length;
      const end = messageInput.selectionEnd ?? messageInput.value.length;
      const text = messageInput.value;

      messageInput.value = text.slice(0, start) + char + text.slice(end);
      const newCaret = start + char.length;
      messageInput.focus();
      messageInput.setSelectionRange(newCaret, newCaret);
    }

    function getEmojiItemsForRender() {
      const allItems = (emojiCatalog[activeEmojiCategory] || []).slice();
      const query = emojiSearchInput ? emojiSearchInput.value.trim().toLowerCase() : '';
      if (!query) {
        return allItems;
      }

      return allItems.filter(function (item) {
        return item.label.includes(query) || item.char.includes(query);
      });
    }

    function renderEmojiGrid() {
      if (!emojiGrid) {
        return;
      }

      const items = getEmojiItemsForRender();
      emojiGrid.innerHTML = '';

      if (items.length === 0) {
        const empty = document.createElement('p');
        empty.className = 'messages-emoji-empty';
        empty.textContent = 'No emoji found.';
        emojiGrid.appendChild(empty);
        return;
      }

      const fragment = document.createDocumentFragment();
      items.forEach(function (item) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'messages-emoji-item';
        button.textContent = item.char;
        button.setAttribute('title', item.label);
        button.addEventListener('click', function () {
          insertEmojiAtCaret(item.char);
        });
        fragment.appendChild(button);
      });

      emojiGrid.appendChild(fragment);
    }

    function activateEmojiCategory(category) {
      activeEmojiCategory = category;
      const tabs = document.querySelectorAll('.messages-emoji-tab');
      tabs.forEach(function (tab) {
        const isActive = tab.getAttribute('data-emoji-category') === category;
        tab.classList.toggle('is-active', isActive);
      });
      renderEmojiGrid();
    }

    function bindHistoryScroll() {
      const wrapper = getScrollWrapper();
      if (!wrapper || wrapper.getAttribute('data-history-bound') === '1') {
        return;
      }

      wrapper.setAttribute('data-history-bound', '1');
      wrapper.addEventListener('scroll', function () {
        if (wrapper.scrollTop <= historyTopThreshold) {
          loadOlderMessages();
        }
      }, { passive: true });
    }

    function initializeThreadViewport() {
      bindHistoryScroll();

      if (getScrollWrapper()) {
        scrollThreadToBottom();
        return;
      }

      setTimeout(function () {
        bindHistoryScroll();
        scrollThreadToBottom();
      }, 120);
    }

    function applyConversationSearch() {
      if (!searchInput || !conversationList) {
        return;
      }

      const query = searchInput.value.trim().toLowerCase();
      const rows = conversationList.querySelectorAll('.messages-conversation');
      rows.forEach(function (row) {
        const name = row.getAttribute('data-name') || '';
        const text = row.getAttribute('data-message') || '';
        const match = !query || name.includes(query) || text.includes(query);
        row.style.display = match ? '' : 'none';
      });
    }

    function postMessage() {
      if (!messageInput || !messageList || !sendUrl || sendLoading) {
        return;
      }

      const tokenElement = document.querySelector('meta[name="csrf-token"]');
      const token = tokenElement ? tokenElement.getAttribute('content') : '';
      const text = messageInput.value.trim();
      const selectedFile = attachmentInput && attachmentInput.files && attachmentInput.files.length
        ? attachmentInput.files[0]
        : null;

      if (!text && !selectedFile) {
        return;
      }
      if (selectedFile && selectedFile.size > maxAttachmentBytes) {
        setComposeError('Maximum attachment size is 5 MB.');
        return;
      }

      clearComposeError();

      sendLoading = true;
      const payloadBody = new FormData();
      payloadBody.append('message', text);
      if (selectedFile) {
        payloadBody.append('attachment', selectedFile);
      }

      fetch(sendUrl, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': token
        },
        body: payloadBody
      })
      .then(function (response) {
        return response.json().catch(function () {
          return { success: false, message: 'Request failed.' };
        }).then(function (payload) {
          if (!response.ok) {
            throw new Error((payload && payload.message) ? payload.message : 'Request failed');
          }
          return payload;
        });
      })
      .then(function (payload) {
        if (!payload || payload.success !== true) {
          throw new Error((payload && payload.message) ? payload.message : 'Request failed.');
        }

        const rows = htmlToRows(payload.html || '');
        appendRows(rows, true);

        const serverLatestId = parseMessageId(payload.latest_id || 0);
        if (serverLatestId && serverLatestId > latestMessageId) {
          latestMessageId = serverLatestId;
        }
        if (!oldestMessageId) {
          oldestMessageId = latestMessageId;
        }
        updateThreadBoundsAttributes();
        messageInput.value = '';
        resetAttachmentInput();
      })
      .catch(function (error) {
        setComposeError(error && error.message ? error.message : 'Unable to send message.');
      })
      .finally(function () {
        sendLoading = false;
      });
    }

    function loadOlderMessages() {
      if (!messageList || !historyUrl || historyLoading || historyDone || !oldestMessageId) {
        return;
      }

      historyLoading = true;
      const url = new URL(historyUrl, window.location.origin);
      url.searchParams.set('before_id', String(oldestMessageId));

      fetch(url.toString(), {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(function (response) {
        if (!response.ok) {
          throw new Error('Request failed');
        }
        return response.json();
      })
      .then(function (payload) {
        if (!payload || payload.success !== true) {
          return;
        }

        const rows = htmlToRows(payload.html || '');
        prependRows(rows);

        const serverOldestId = parseMessageId(payload.oldest_id || 0);
        if (serverOldestId && (!oldestMessageId || serverOldestId < oldestMessageId)) {
          oldestMessageId = serverOldestId;
        }

        hasOlderMessages = payload.has_more === true;
        historyDone = !hasOlderMessages || (payload.count || 0) === 0;
        updateThreadBoundsAttributes();
      })
      .catch(function () {})
      .finally(function () {
        historyLoading = false;
      });
    }

    function refreshConversation() {
      if (!messageList || !loadUrl || refreshLoading) {
        return;
      }

      refreshLoading = true;
      const url = new URL(loadUrl, window.location.origin);
      url.searchParams.set('after_id', String(latestMessageId || 0));

      fetch(url.toString(), {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(function (response) {
        if (!response.ok) {
          throw new Error('Request failed');
        }
        return response.json();
      })
      .then(function (payload) {
        if (!payload || payload.success !== true || !payload.count) {
          return;
        }

        const rows = htmlToRows(payload.html || '');
        appendRows(rows, true);

        const serverLatestId = parseMessageId(payload.latest_id || 0);
        if (serverLatestId && serverLatestId > latestMessageId) {
          latestMessageId = serverLatestId;
        }

        if (!oldestMessageId) {
          const rowsNow = getThreadRows();
          if (rowsNow.length) {
            oldestMessageId = parseMessageId(rowsNow[0].getAttribute('data-message-id'));
          }
        }
        updateThreadBoundsAttributes();
      })
      .catch(function () {})
      .finally(function () {
        refreshLoading = false;
      });
    }

    document.addEventListener('DOMContentLoaded', function () {
      if (searchInput) {
        searchInput.addEventListener('input', applyConversationSearch);
      }

      @if($partner)
      syncMessageBounds();

      if (sendButton) {
        sendButton.addEventListener('click', postMessage);
      }
      if (attachButton && attachmentInput) {
        attachButton.addEventListener('click', function () {
          attachmentInput.click();
        });
      }
      if (attachmentInput) {
        attachmentInput.addEventListener('change', function () {
          const file = attachmentInput.files && attachmentInput.files.length
            ? attachmentInput.files[0]
            : null;

          if (file && file.size > maxAttachmentBytes) {
            setComposeError('Maximum attachment size is 5 MB.');
            resetAttachmentInput();
            return;
          }

          clearComposeError();
          applyAttachmentMeta(file);
        });
      }
      if (attachmentClear) {
        attachmentClear.addEventListener('click', function () {
          resetAttachmentInput();
          clearComposeError();
        });
      }
      if (emojiButton && emojiPanel) {
        emojiButton.addEventListener('click', function (event) {
          event.preventDefault();
          const isOpen = emojiPanel.classList.contains('is-open');
          setEmojiPanelOpen(!isOpen);
          if (!isOpen) {
            renderEmojiGrid();
            if (emojiSearchInput) {
              emojiSearchInput.focus();
            }
          }
        });
      }

      const emojiTabs = document.querySelectorAll('.messages-emoji-tab');
      emojiTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
          const category = tab.getAttribute('data-emoji-category');
          if (!category) {
            return;
          }
          activateEmojiCategory(category);
        });
      });

      if (emojiSearchInput) {
        emojiSearchInput.addEventListener('input', renderEmojiGrid);
      }

      document.addEventListener('click', function (event) {
        if (!emojiPanel || !emojiButton || !emojiPanel.classList.contains('is-open')) {
          return;
        }

        if (emojiPanel.contains(event.target) || emojiButton.contains(event.target)) {
          return;
        }

        setEmojiPanelOpen(false);
      });

      if (messageInput) {
        messageInput.addEventListener('keydown', function (event) {
          if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
            event.preventDefault();
            postMessage();
            return;
          }

          if (event.key === 'Escape') {
            setEmojiPanelOpen(false);
          }
        });
      }

      clearComposeError();
      resetAttachmentInput();
      activateEmojiCategory(activeEmojiCategory);
      setEmojiPanelOpen(false);

      initializeThreadViewport();
      refreshTimer = setInterval(refreshConversation, 5000);
      @endif
    });

    window.addEventListener('beforeunload', function () {
      if (refreshTimer) {
        clearInterval(refreshTimer);
      }
    });
  })();
</script>
@endpush
