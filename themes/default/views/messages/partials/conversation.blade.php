@php
    $authId = $user ? $user->id : auth()->id();
@endphp

@foreach($messages as $message)
    @if($message->us_env == $authId)
        <div class="chat-widget-speaker right">
            <p class="chat-widget-speaker-message">{!! nl2br(e($message->text)) !!}</p>
            <p class="chat-widget-speaker-timestamp">{{ \Carbon\Carbon::createFromTimestamp($message->time)->diffForHumans() }}</p>
        </div>
    @else
        <div class="chat-widget-speaker left">
            <div class="chat-widget-speaker-avatar">
                <div class="user-avatar tiny no-border">
                    <div class="user-avatar-content">
                        <img src="{{ $partner->img ? asset($partner->img) : theme_asset('img/avatar/01.jpg') }}" width="24" height="26" alt="">
                    </div>
                </div>
            </div>
            <p class="chat-widget-speaker-message">{!! nl2br(e($message->text)) !!}</p>
            <p class="chat-widget-speaker-timestamp">{{ \Carbon\Carbon::createFromTimestamp($message->time)->diffForHumans() }}</p>
        </div>
    @endif
@endforeach
