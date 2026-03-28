@php
    $authId = $user ? $user->id : auth()->id();
    $itemsOnly = $itemsOnly ?? false;
    $oldestId = $messages->first()->id_msg ?? 0;
    $latestId = $messages->last()->id_msg ?? 0;
    $hasOlder = isset($hasOlderMessages) ? (bool) $hasOlderMessages : false;
    $hasPreviousState = (bool) ($hasPreviousConversationMessage ?? false);
    $previousEncryptedState = $hasPreviousState ? (bool) ($precedingMessageEncrypted ?? false) : null;
@endphp

@if(!$itemsOnly)
<div class="messages-thread" data-oldest-id="{{ $oldestId }}" data-latest-id="{{ $latestId }}" data-has-older="{{ $hasOlder ? 1 : 0 }}">
@endif
    @forelse($messages as $message)
        @php
            $isMine = (int) $message->us_env === (int) $authId;
            $isEncryptedPayload = method_exists($message, 'isEncryptedPayload') ? $message->isEncryptedPayload() : false;
            $showEncryptionNotice = $isEncryptedPayload && $hasPreviousState && $previousEncryptedState === false;
            $text = trim((string) ($message->text ?? ''));
            $hasText = $text !== '';
            $attachmentPath = $message->attachment_path ?? null;
            $attachmentName = trim((string) ($message->attachment_name ?? ''));
            $attachmentSize = (int) ($message->attachment_size ?? 0);
            $attachmentLabel = $attachmentName !== '' ? $attachmentName : basename((string) $attachmentPath);
            $attachmentExtension = strtolower(pathinfo($attachmentLabel, PATHINFO_EXTENSION));
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'avif'];
            $isImageAttachment = in_array($attachmentExtension, $imageExtensions, true);
            $attachmentInlineUrl = route('messages.attachment', ['id' => $message->id_msg, 'inline' => 1]);
            $attachmentDownloadUrl = route('messages.attachment', ['id' => $message->id_msg, 'download' => 1]);
            $messageDateTime = \Carbon\Carbon::createFromTimestamp($message->time);
            $showFullDateTime = $messageDateTime->lt(\Carbon\Carbon::now()->subDay());
            $messageTimeLabel = $showFullDateTime
                ? $messageDateTime->format('Y-m-d g:i A')
                : $messageDateTime->format('g:i A');
        @endphp
        @if($showEncryptionNotice)
            <div class="messages-encryption-notice" role="note">
                <span>{{ __('messages.private_messages_encryption_notice') }}</span>
            </div>
        @endif
        <article class="messages-bubble-row {{ $isMine ? 'is-me' : '' }}" data-message-id="{{ $message->id_msg }}">
            @unless($isMine)
                <div class="messages-bubble-avatar">
                    <img src="{{ $partner ? $partner->avatarUrl() : asset('upload/_avatar.png') }}" alt="{{ $partner->username ?? __('messages.unknown_user') }}">
                </div>
            @endunless

            <div class="messages-bubble-group">
                @if($hasText)
                    <p class="messages-bubble">{!! nl2br(e($text)) !!}</p>
                @endif

                @if(!empty($attachmentPath))
                    @if($isImageAttachment)
                        <a
                            class="messages-image-attachment {{ $isMine ? 'is-me' : '' }}"
                            href="{{ $attachmentInlineUrl }}"
                            target="_blank"
                            rel="noopener"
                        >
                            <img src="{{ $attachmentInlineUrl }}" alt="{{ $attachmentLabel }}" loading="lazy">
                        </a>

                        <a
                            class="messages-image-meta {{ $isMine ? 'is-me' : '' }}"
                            href="{{ $attachmentDownloadUrl }}"
                            target="_blank"
                            rel="noopener"
                        >
                            <i class="fa fa-image" aria-hidden="true"></i>
                            <span class="messages-image-meta-name">{{ $attachmentLabel }}</span>
                            @if($attachmentSize > 0)
                                <span class="messages-image-meta-size">{{ number_format($attachmentSize / 1024, 1) }} KB</span>
                            @endif
                        </a>
                    @else
                        <a
                            class="messages-file-attachment {{ $isMine ? 'is-me' : '' }}"
                            href="{{ $attachmentDownloadUrl }}"
                            target="_blank"
                            rel="noopener"
                        >
                            <span class="messages-file-attachment-icon">
                                <i aria-hidden="true" class="fa fa-file"></i>
                            </span>

                            <span class="messages-file-attachment-meta">
                                <span class="messages-file-attachment-name">{{ $attachmentLabel }}</span>
                                <span class="messages-file-attachment-sub">
                                    <span class="messages-file-attachment-ext">{{ $attachmentExtension ? strtoupper($attachmentExtension) : __('messages.file') }}</span>
                                    @if($attachmentSize > 0)
                                        <span class="messages-file-attachment-size">{{ number_format($attachmentSize / 1024, 1) }} KB</span>
                                    @endif
                                </span>
                            </span>

                            <span class="messages-file-attachment-action">
                                <i class="fa fa-download" aria-hidden="true"></i>
                            </span>
                        </a>
                    @endif
                @endif

                <span class="messages-bubble-time">{{ $messageTimeLabel }}</span>
            </div>
        </article>
        @php
            $previousEncryptedState = $isEncryptedPayload;
            $hasPreviousState = true;
        @endphp
    @empty
        @if(!$itemsOnly)
            <p class="messages-empty-thread">{{ __('messages.no_messages') }}</p>
        @endif
    @endforelse
@if(!$itemsOnly)
</div>
@endif
