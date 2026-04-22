@extends('theme::layouts.oauth')

@section('title', __('messages.authorize_app'))

@push('head')
    @include('theme::developer.partials.styles')
    @include('theme::oauth.styles')
@endpush

@section('content')
@php
    $siteName = $site_settings->titer ?? config('app.name', 'MYADS');
    $appHost = parse_url($app->domain, PHP_URL_HOST) ?: $app->domain;
    $appInitial = strtoupper(mb_substr($app->name, 0, 1));
    $scopeIcons = [
        'user.identity.read' => 'fa-id-card',
        'user.profile.read' => 'fa-user',
        'user.social_links.read' => 'fa-share-nodes',
        'user.follows.read' => 'fa-user-group',
        'owner.profile.read' => 'fa-address-card',
        'owner.content.read' => 'fa-newspaper',
        'owner.follow.write' => 'fa-user-plus',
        'owner.messages.read' => 'fa-envelope-open-text',
        'owner.messages.write' => 'fa-paper-plane',
    ];
@endphp

<div class="oauth-consent-shell">
    <div class="widget-box dev-panel oauth-consent-card no-padding">
        <div class="oauth-consent-banner">
            <span class="oauth-consent-banner__glow oauth-consent-banner__glow--one"></span>
            <span class="oauth-consent-banner__glow oauth-consent-banner__glow--two"></span>

            <div class="oauth-consent-banner__inner">
                <span class="oauth-consent-banner__icon">
                    <i class="fa-solid fa-user-shield"></i>
                </span>

                <div>
                    <p class="oauth-consent-kicker">{{ __('messages.oauth_secured') }}</p>
                    <h1 class="oauth-consent-title">{{ __('messages.authorize_app') }}</h1>
                    <p class="oauth-consent-summary">{{ __('messages.app_wants_access', ['app' => $app->name, 'site' => $siteName]) }}</p>
                </div>
            </div>
        </div>

        <div class="widget-box-content oauth-consent-content">
            <div class="oauth-consent-app">
                @if($app->logo)
                    <img src="{{ asset($app->logo) }}" alt="{{ $app->name }}" class="oauth-consent-app__logo">
                @else
                    <span class="oauth-consent-app__fallback">{{ $appInitial }}</span>
                @endif

                <div class="oauth-consent-app__meta">
                    <div class="oauth-consent-chip-row">
                        <span class="oauth-consent-chip">
                            <i class="fa-solid fa-globe"></i>
                            {{ $appHost }}
                        </span>
                        <span class="oauth-consent-chip oauth-consent-chip--accent">
                            <i class="fa-solid fa-shield-halved"></i>
                            {{ __('messages.oauth_secured') }}
                        </span>
                    </div>

                    <h2 class="oauth-consent-app__name">{{ $app->name }}</h2>
                    <p class="oauth-consent-app__domain">{{ $app->domain }}</p>
                </div>
            </div>

            <div class="oauth-consent-section">
                <div class="oauth-consent-section__head">
                    <p class="dev-kicker">{{ __('messages.requested_permissions') }}</p>
                    <span class="oauth-consent-count">{{ count($scopeDetails) }}</span>
                </div>

                @if(count($scopeDetails) > 0)
                    <div class="oauth-consent-scopes">
                        @foreach($scopeDetails as $scope)
                            @php
                                $scopeIcon = $scopeIcons[$scope['id']] ?? 'fa-shield-halved';
                                $isSensitive = (bool) ($scope['is_sensitive'] ?? false);
                            @endphp
                            <div class="oauth-consent-scope {{ $isSensitive ? 'is-sensitive' : '' }}">
                                <div class="oauth-consent-scope__icon">
                                    <i class="fa-solid {{ $scopeIcon }}"></i>
                                </div>

                                <div class="oauth-consent-scope__copy">
                                    <div class="oauth-consent-scope__head">
                                        <h3>{{ __($scope['name']) }}</h3>

                                        <div class="oauth-consent-scope__meta">
                                            @if($isSensitive)
                                                <span class="oauth-consent-mini-chip oauth-consent-mini-chip--warning">{{ __('messages.sensitive') }}</span>
                                            @endif
                                            <code>{{ $scope['id'] }}</code>
                                        </div>
                                    </div>

                                    <p class="oauth-consent-scope__description">{{ __($scope['description']) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="oauth-consent-empty">{{ __('messages.no_data') }}</div>
                @endif
            </div>

            <div class="oauth-consent-note">
                <div class="oauth-consent-note__icon">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <div class="oauth-consent-note__copy">
                    <p class="oauth-consent-note__title">{{ __('messages.security') }}</p>
                    <p class="oauth-consent-note__text">{{ __('messages.authorize_disclaimer') }}</p>
                    <div class="oauth-consent-note__links">
                        <a href="{{ route('profile.apps') }}" class="oauth-consent-note__link">{{ __('messages.authorized_apps') }}</a>
                    </div>
                </div>
            </div>

            <form action="{{ route('oauth.authorize.post') }}" method="POST" class="oauth-consent-form">
                @csrf
                <input type="hidden" name="client_id" value="{{ request('client_id') }}">
                <input type="hidden" name="redirect_uri" value="{{ request('redirect_uri') }}">
                <input type="hidden" name="response_type" value="{{ request('response_type') }}">
                <input type="hidden" name="state" value="{{ request('state') }}">
                <input type="hidden" name="scope" value="{{ request('scope') }}">

                <div class="oauth-consent-actions">
                    <button type="submit" name="action" value="accept" class="button primary">
                        <i class="fa-solid fa-check"></i>
                        <span>{{ __('messages.authorize') }}</span>
                    </button>
                    <button type="submit" name="action" value="reject" class="button white">
                        <i class="fa-solid fa-xmark"></i>
                        <span>{{ __('messages.cancel') }}</span>
                    </button>
                </div>

                <p class="oauth-consent-disclaimer">{{ __('messages.authorize_disclaimer') }}</p>
            </form>
        </div>
    </div>
</div>
@endsection
