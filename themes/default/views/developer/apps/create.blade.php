@extends('theme::layouts.master')

@section('title', __('messages.create_app'))

@push('head')
    @include('theme::developer.partials.styles')
@endpush

@section('content')
<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-plus-circle" style="font-size: 26px; color: #fff;"></i>
    </div>
    <p class="section-banner-title">{{ __('messages.create_app') }}</p>
    <p class="section-banner-text">{{ __('messages.dev_create_help') }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <div class="dev-side-stack">
            @include('theme::developer.partials.nav', ['active' => 'create'])
            @include('theme::developer.partials.platform_rules')
        </div>
    </div>

    <div class="grid-column">
        @if($errors->any())
            <div class="dev-note dev-note--danger">
                <strong>{{ __('messages.save') }}</strong>
                <div class="dev-card-copy">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="widget-box dev-panel">
            <p class="widget-box-title">{{ __('messages.create_app') }}</p>
            <div class="widget-box-content" style="padding: 32px;">
                <form action="{{ route('developer.apps.store') }}" method="POST" class="dev-form-layout">
                    @csrf

                    @include('theme::developer.partials.form_fields', [
                        'scopes' => $scopes,
                        'scopeInputPrefix' => 'developer_create_scope',
                    ])

                    <div class="dev-form-actions">
                        <a href="{{ route('developer.apps.index') }}" class="button secondary">{{ __('messages.cancel') }}</a>
                        <button type="submit" class="button primary">{{ __('messages.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <div class="dev-side-stack">
            <div class="widget-box dev-panel">
                <p class="widget-box-title">{{ __('messages.information') }}</p>
                <div class="widget-box-content" style="padding: 28px;">
                    <p class="dev-card-copy">{{ __('messages.dev_create_help') }}</p>
                    <ul class="dev-list-reset" style="margin-top: 18px;">
                        <li>
                            <i class="fa fa-check-circle"></i>
                            <span>{{ __('messages.dev_https_hint') }}</span>
                        </li>
                        <li>
                            <i class="fa fa-check-circle"></i>
                            <span>{{ __('messages.dev_scopes_help') }}</span>
                        </li>
                        <li>
                            <i class="fa fa-check-circle"></i>
                            <span>{{ __('messages.submit_for_review') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="widget-box dev-panel">
                <p class="widget-box-title">{{ __('messages.dev_docs') }}</p>
                <div class="widget-box-content" style="padding: 28px;">
                    <p class="dev-card-copy">{{ __('messages.dev_widgets_desc') }}</p>
                    <div class="dev-chip-row" style="margin-top: 18px;">
                        <span class="dev-chip">
                            <i class="fa fa-user-shield"></i>
                            OAuth 2.0
                        </span>
                        <span class="dev-chip">
                            <i class="fa fa-share-nodes"></i>
                            Share API
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
