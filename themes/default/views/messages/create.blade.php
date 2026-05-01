@extends('theme::layouts.master')

@push('head')
    <link href="{{ theme_asset('css/messages.css') }}" rel="stylesheet" type="text/css">
@endpush

@section('content')
<div class="messages-workspace account-hub-content">
    <div class="messages-topbar">
        <div class="messages-title-block">
            <p class="messages-kicker">{{ __('messages.msgs') }}</p>
            <h2>{{ __('messages.send_message') }}</h2>
        </div>

        <a href="{{ route('messages.index') }}" class="messages-action-link">
            <i class="fa fa-arrow-left" aria-hidden="true"></i>
            <span>{{ __('messages.back_to_inbox') }}</span>
        </a>
    </div>

    <div class="messages-create-panel messages-panel">
        <form action="{{ route('messages.store') }}" method="POST" class="messages-create-form">
            @csrf

            <div class="messages-create-intro">
                <div class="messages-empty-badge">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                </div>
                <div>
                    <p class="messages-panel-label">{{ __('messages.new_message') }}</p>
                    <h3>{{ __('messages.send_message') }}</h3>
                </div>
            </div>

            @if($errors->any())
                <div class="messages-form-alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <label class="messages-form-field" for="recipient">
                <span>{{ __('messages.recipient') }}</span>
                <input type="text" id="recipient" name="recipient" value="{{ old('recipient', $recipient ?? '') }}" required autocomplete="off">
            </label>

            <label class="messages-form-field" for="message">
                <span>{{ __('messages.message') }}</span>
                <textarea id="message" name="message" rows="7" required>{{ old('message') }}</textarea>
            </label>

            <div class="messages-create-actions">
                <a href="{{ route('messages.index') }}" class="messages-action-link">{{ __('messages.cancel') }}</a>
                <button type="submit" class="messages-create-submit">
                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                    <span>{{ __('messages.send') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
