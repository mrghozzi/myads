@extends('theme::layouts.master')

@section('title', $app->name . ' - ' . __('messages.dev_platform'))

@push('head')
    @include('theme::developer.partials.styles')
@endpush

@section('content')
@php
    $redirectUriCount = count($app->redirect_uris ?? []);
    $requestedScopeCount = count($app->requested_scopes ?? []);
@endphp

<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-cube" style="font-size: 26px; color: #fff;"></i>
    </div>
    <p class="section-banner-title">{{ $app->name }}</p>
    <p class="section-banner-text">{{ parse_url($app->domain, PHP_URL_HOST) ?: $app->domain }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <div class="dev-side-stack">
            @include('theme::developer.partials.nav', ['active' => 'apps'])
            @include('theme::developer.partials.platform_rules')
        </div>
    </div>

    <div class="grid-column">
        @if(session('success'))
            <div class="alert alert-success" role="alert" style="margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        <div class="widget-box dev-panel" style="margin-bottom: 20px;">
            <div class="widget-box-content" style="padding: 28px;">
                <div class="dev-summary-head">
                    <div>
                        <p class="dev-kicker">{{ __('messages.app_specifications') }}</p>
                        <h2 class="dev-section-title">{{ $app->name }}</h2>
                        <p class="dev-summary-copy" style="margin-top: 8px;">{{ $app->description }}</p>
                    </div>
                    @include('theme::developer.partials.status_badge', ['status' => $app->status])
                </div>

                <div class="dev-stat-grid" style="margin-top: 20px;">
                    <div class="dev-stat-card">
                        <span>{{ __('messages.current_status') }}</span>
                        <strong>{{ __('messages.app_status_' . $app->status) }}</strong>
                    </div>
                    <div class="dev-stat-card">
                        <span>{{ __('messages.redirect_uris') }}</span>
                        <strong>{{ $redirectUriCount }}</strong>
                    </div>
                    <div class="dev-stat-card">
                        <span>{{ __('messages.requested_scopes') }}</span>
                        <strong>{{ $requestedScopeCount }}</strong>
                    </div>
                </div>
            </div>
        </div>

        @if($app->status === 'draft')
            <div class="dev-note dev-note--info">
                <strong>{{ __('messages.app_draft_notice') }}</strong>
                <div class="dev-inline-actions">
                    <form action="{{ route('developer.apps.submit', $app->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="button primary">{{ __('messages.submit_for_review') }}</button>
                    </form>
                </div>
            </div>
        @elseif($app->status === 'pending_review')
            <div class="dev-note dev-note--warning">
                <strong>{{ __('messages.app_status_pending_review') }}</strong>
                <p>{{ __('messages.dev_pending_notice') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="dev-note dev-note--danger">
                <strong>{{ __('messages.save_changes') }}</strong>
                <div class="dev-card-copy">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="widget-box dev-panel" style="margin-bottom: 20px;">
            <p class="widget-box-title">{{ __('messages.api_credentials') }}</p>
            <div class="widget-box-content" style="padding: 28px;">
                <p class="dev-card-copy">{{ __('messages.dev_credentials_help') }}</p>

                <div class="dev-credential-field" style="margin-top: 18px;">
                    <label for="developer-client-id">{{ __('messages.client_id') }}</label>
                    <div class="dev-credential-input">
                        <input id="developer-client-id" type="text" class="form-control dev-control" value="{{ $app->client_id }}" readonly>
                        <button type="button" class="js-dev-copy dev-inline-icon-btn" data-copy-target="#developer-client-id">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="dev-credential-field" style="margin-top: 18px;">
                    <label for="developer-client-secret">Client Secret</label>
                    <div class="dev-credential-input">
                        <input id="developer-client-secret" type="password" class="form-control dev-control" value="{{ $app->client_secret }}" readonly>
                        <button type="button" class="js-dev-toggle-secret dev-inline-icon-btn" data-target="#developer-client-secret">
                            <i class="fa fa-eye"></i>
                        </button>
                        <button type="button" class="js-dev-copy dev-inline-icon-btn" data-copy-target="#developer-client-secret">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                </div>

                <form action="{{ route('developer.apps.rotate_secret', $app->id) }}" method="POST" style="margin-top: 18px;" onsubmit="return confirm('@lang('messages.rotate_secret_confirm')')">
                    @csrf
                    <button type="submit" class="button secondary">{{ __('messages.rotate_secret') }}</button>
                </form>
            </div>
        </div>

        <div class="widget-box dev-panel">
            <p class="widget-box-title">{{ __('messages.app_settings') }}</p>
            <div class="widget-box-content" style="padding: 32px;">
                <form action="{{ route('developer.apps.update', $app->id) }}" method="POST" class="dev-form-layout">
                    @csrf
                    @method('PUT')

                    @include('theme::developer.partials.form_fields', [
                        'app' => $app,
                        'scopes' => $scopes,
                        'scopeInputPrefix' => 'developer_show_scope',
                    ])

                    <div class="dev-form-actions">
                        <a href="{{ route('developer.apps.index') }}" class="button secondary">{{ __('messages.my_apps') }}</a>
                        <button type="submit" class="button primary">{{ __('messages.save_changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <div class="dev-side-stack">

            <div class="widget-box dev-panel">
                <p class="widget-box-title">{{ __('messages.take_action') }}</p>
                <div class="widget-box-content" style="padding: 28px;">
                    <div class="dev-rule-list">
                        <div class="dev-rule-item">
                            <strong>{{ __('messages.current_status') }}</strong>
                            <span class="dev-rule-value">{{ __('messages.app_status_' . $app->status) }}</span>
                        </div>
                        <div class="dev-rule-item">
                            <strong>{{ __('messages.domain') }}</strong>
                            <span class="dev-rule-value">{{ $app->domain }}</span>
                        </div>
                        @if($app->status === 'draft')
                            <div class="dev-rule-item">
                                <strong>{{ __('messages.submit_for_review') }}</strong>
                                <span class="dev-rule-value">{{ __('messages.dev_create_help') }}</span>
                            </div>
                        @endif
                    </div>

                    @if($app->status === 'draft')
                        <form action="{{ route('developer.apps.submit', $app->id) }}" method="POST" style="margin-top: 18px;">
                            @csrf
                            <button type="submit" class="button primary">{{ __('messages.submit_for_review') }}</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="widget-box dev-panel">
                <p class="widget-box-title">{{ __('messages.app_about') }}</p>
                <div class="widget-box-content" style="padding: 28px;">
                    <p class="dev-card-copy">{{ $app->description }}</p>
                    <div class="dev-chip-row" style="margin-top: 18px;">
                        <span class="dev-chip">
                            <i class="fa fa-link"></i>
                            {{ $redirectUriCount }} {{ __('messages.redirect_uris') }}
                        </span>
                        <span class="dev-chip">
                            <i class="fa fa-shield-halved"></i>
                            {{ $requestedScopeCount }} {{ __('messages.requested_scopes') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('theme::developer.partials.scripts')
@endpush
