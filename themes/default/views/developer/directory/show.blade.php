@extends('theme::layouts.master')

@section('title', $app->name . ' - ' . (__('messages.app_directory') ?? 'App Directory'))

@push('head')
    @include('theme::developer.partials.styles')
@endpush

@section('content')
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
            @include('theme::developer.partials.nav', ['active' => 'directory'])

            <div class="widget-box dev-panel">
                <p class="widget-box-title">Developer Info</p>
                <div class="widget-box-content" style="padding: 20px;">
                    <p style="font-size: 13px; color: var(--dev-muted); margin-bottom: 8px;">Published by:</p>
                    <strong style="color: var(--dev-title); font-size: 15px;">{{ $app->user->username ?? 'Developer' }}</strong>
                    <div style="margin-top: 14px;">
                        <a href="{{ $app->domain }}" target="_blank" rel="noopener noreferrer" class="button white" style="width: 100%; text-align: center; font-size: 12px;">
                            <i class="fa fa-external-link-alt me-1"></i> Visit Website
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <!-- App Details Box -->
        <div class="widget-box dev-panel" style="margin-bottom: 24px;">
            <div class="widget-box-content" style="padding: 28px;">
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                    @if($app->logo)
                        <img src="{{ asset($app->logo) }}" alt="{{ $app->name }}" style="width: 64px; height: 64px; border-radius: 14px; object-fit: cover;">
                    @else
                        <div style="width: 64px; height: 64px; border-radius: 14px; background: linear-gradient(135deg, var(--dev-accent), #23d2e2); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 24px;">
                            {{ strtoupper(mb_substr($app->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <span class="dev-chip" style="font-size: 11px; margin-bottom: 4px; display: inline-block;">
                            {{ $app->category ?: 'Integration' }}
                        </span>
                        <h2 style="margin: 0; font-size: 22px; font-weight: 800; color: var(--dev-title);">{{ $app->name }}</h2>
                        <a href="{{ $app->domain }}" target="_blank" rel="noopener noreferrer" style="font-size: 13px; color: var(--dev-muted);">{{ $app->domain }}</a>
                    </div>
                </div>

                <div style="border-top: 1px solid var(--dev-border); padding-top: 20px; margin-bottom: 20px;">
                    <h4 style="font-size: 15px; font-weight: 700; color: var(--dev-title); margin-bottom: 10px;">About this Application</h4>
                    <p style="font-size: 14px; line-height: 1.6; color: var(--dev-text);">
                        {{ $app->description }}
                    </p>
                </div>

                @php
                    $redirectUris = (array) ($app->redirect_uris ?? []);
                    $firstRedirect = $redirectUris[0] ?? $app->domain;
                    $oauthConnectUrl = route('oauth.authorize', [
                        'client_id' => $app->client_id,
                        'redirect_uri' => $firstRedirect,
                        'response_type' => 'code',
                        'scope' => implode(' ', (array) ($app->requested_scopes ?? ['user.identity.read'])),
                        'state' => 'dir_install_' . time(),
                    ]);
                @endphp

                <div style="display: flex; gap: 12px; align-items: center; margin-top: 24px;">
                    <a href="{{ $oauthConnectUrl }}" class="button primary" style="padding: 0 24px;">
                        <i class="fa fa-plug me-2"></i> Connect with MYADS
                    </a>
                    <a href="{{ route('developer.directory.index') }}" class="button white">
                        <i class="fa fa-arrow-left me-1"></i> Back to Directory
                    </a>
                </div>
            </div>
        </div>

        <!-- Permissions Required Box -->
        <div class="widget-box dev-panel">
            <p class="widget-box-title">Required Permissions ({{ count($scopes) }})</p>
            <div class="widget-box-content" style="padding: 24px;">
                @if(empty($scopes))
                    <p style="color: var(--dev-muted); font-size: 13px;">This application requires basic profile identity access.</p>
                @else
                    <div style="display: grid; gap: 10px;">
                        @foreach($scopes as $sc)
                            <div style="display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid var(--dev-border); border-radius: 8px; background: var(--dev-surface-soft);">
                                <i class="fa fa-shield-halved" style="color: var(--dev-accent); font-size: 16px;"></i>
                                <div>
                                    <strong style="font-size: 13px; color: var(--dev-title); font-family: monospace;">{{ $sc['id'] }}</strong>
                                    <p style="font-size: 12px; color: var(--dev-muted); margin: 2px 0 0;">{{ __($sc['description']) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid-column"></div>
</div>
@endsection
