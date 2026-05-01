<div class="messages-chat-empty">
    <div class="messages-empty-badge">
        <i class="fa fa-message" aria-hidden="true"></i>
    </div>
    <h3>{{ __('messages.msgs') }}</h3>
    <p>{{ __('messages.no_msg') }}</p>
    <a href="{{ route('messages.create') }}" class="messages-empty-action">
        <i class="fa fa-pen" aria-hidden="true"></i>
        <span>{{ __('messages.send_message') }}</span>
    </a>
</div>
