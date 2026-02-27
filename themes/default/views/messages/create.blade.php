@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/profile.png') }}) no-repeat 50%;">
    <p class="section-banner-title">{{ __('messages.send_message') }}</p>
</div>

<div class="grid grid-3-9">
    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-settings">
                <div class="post-peek-header">
                    <p class="widget-box-title">{{ __('messages.actions') }}</p>
                </div>
                <div class="post-peek-body">
                    <a href="{{ route('messages.index') }}" class="button secondary full">{{ __('messages.back_to_inbox') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                <form action="{{ route('messages.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-item">
                        <div class="form-input small">
                            <label for="recipient">{{ __('messages.recipient') }}</label>
                            <input type="text" id="recipient" name="recipient" value="{{ $recipient ?? '' }}" required>
                        </div>
                    </div>

                    <div class="form-item">
                        <div class="form-input small">
                            <label for="message">{{ __('messages.message') }}</label>
                            <textarea id="message" name="message" required style="height: 150px;"></textarea>
                        </div>
                    </div>

                    <div class="form-item">
                        <button type="submit" class="button primary">{{ __('messages.send') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
