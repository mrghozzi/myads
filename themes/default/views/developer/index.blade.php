@extends('theme::layouts.master')

@section('title', __('messages.dev_platform'))

@push('head')
    @include('theme::developer.partials.styles')
@endpush

@section('content')
@php
    $developerApps = collect($apps ?? []);
    $activeAppsCount = $developerApps->where('status', 'active')->count();
    $pendingAppsCount = $developerApps->where('status', 'pending_review')->count();
@endphp

<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-code-branch" style="font-size: 26px; color: #fff;"></i>
    </div>
    <p class="section-banner-title">{{ __('messages.dev_platform') }}</p>
    <p class="section-banner-text">{{ __('messages.dev_platform_desc', ['site' => $site_settings->titer ?? 'MYADS']) }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <div class="dev-side-stack">
            @include('theme::developer.partials.nav', ['active' => 'overview'])

            <div class="widget-box dev-panel">
                <p class="widget-box-title">{{ auth()->check() && $eligible ? __('messages.applications') : __('messages.platform_info') }}</p>
                <div class="widget-box-content" style="padding: 28px;">
                    @if(auth()->check() && $eligible)
                        <div class="dev-stat-grid dev-stat-grid--compact">
                            <div class="dev-stat-card">
                                <span>{{ __('messages.total_apps') }}</span>
                                <strong>{{ $developerApps->count() }}</strong>
                            </div>
                            <div class="dev-stat-card">
                                <span>{{ __('messages.active_apps') }}</span>
                                <strong>{{ $activeAppsCount }}</strong>
                            </div>
                            <div class="dev-stat-card">
                                <span>{{ __('messages.pending_review') }}</span>
                                <strong>{{ $pendingAppsCount }}</strong>
                            </div>
                        </div>
                    @else
                        <p class="dev-card-copy">{{ __('messages.v1_api_desc') }}</p>
                        <div class="dev-chip-row" style="margin-top: 18px;">
                            <span class="dev-chip">
                                <i class="fa fa-plug"></i>
                                {{ __('messages.v1_api') }}
                            </span>
                            <span class="dev-chip">
                                <i class="fa fa-shield-halved"></i>
                                {{ __('messages.oauth_secured') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            @include('theme::developer.partials.platform_rules')
        </div>
    </div>

    <div class="grid-column">
        @if(session('error'))
            <div class="alert alert-danger" role="alert" style="margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <div class="dev-shell">
            <div class="widget-box dev-panel">
                <div class="widget-box-content" style="padding: 28px;">
                    <div class="dev-surface-header">
                        <div>
                            <p class="dev-kicker">{{ __('messages.dev_docs') }}</p>
                            <h2 class="dev-section-title">{{ __('messages.dev_docs') }}</h2>
                            <p class="dev-summary-copy" style="margin-top: 8px;">{{ __('messages.dev_docs_intro') }}</p>
                        </div>

                        @auth
                            @if($eligible)
                                <a href="{{ $developerApps->isEmpty() ? route('developer.apps.create') : route('developer.apps.index') }}" class="button primary">
                                    {{ $developerApps->isEmpty() ? __('messages.create_app') : __('messages.manage_apps') }}
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="button primary">{{ __('messages.login') }}</a>
                        @endauth
                    </div>

                    <div class="dev-chip-row" style="margin-top: 18px;">
                        <span class="dev-chip">
                            <i class="fa fa-user-shield"></i>
                            OAuth 2.0
                        </span>
                        <span class="dev-chip">
                            <i class="fa fa-bolt"></i>
                            Widgets
                        </span>
                        <span class="dev-chip">
                            <i class="fa fa-share-nodes"></i>
                            Share API
                        </span>
                    </div>
                </div>
            </div>

            <div class="dev-doc-grid">
                <article class="widget-box dev-panel dev-doc-card">
                    <div class="widget-box-content">
                        <span class="dev-doc-icon">
                            <i class="fa fa-user-shield"></i>
                        </span>
                        <div class="dev-card-head">
                            <div>
                                <p class="dev-kicker">OAuth 2.0</p>
                                <h3 class="dev-card-title">Authorization Code Flow</h3>
                            </div>
                            <span class="dev-mini-chip">1</span>
                        </div>
                        <p class="dev-card-copy" style="margin-top: 12px;">{{ __('messages.dev_oauth_desc', ['site' => $site_settings->titer ?? 'MYADS']) }}</p>

                        <div class="dev-code-block">
                            <div class="dev-code-toolbar">
                                <span>Code Sample</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy="GET /oauth/authorize?client_id=YOUR_CLIENT_ID&amp;redirect_uri=YOUR_URL&amp;response_type=code&amp;scope=user.profile.read">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
                            <pre><code>GET /oauth/authorize?client_id=YOUR_CLIENT_ID
&amp;redirect_uri=YOUR_URL
&amp;response_type=code
&amp;scope=user.profile.read</code></pre>
                        </div>
                    </div>
                </article>

                <article class="widget-box dev-panel dev-doc-card">
                    <div class="widget-box-content">
                        <span class="dev-doc-icon">
                            <i class="fa fa-wand-magic-sparkles"></i>
                        </span>
                        <div class="dev-card-head">
                            <div>
                                <p class="dev-kicker">Widgets</p>
                                <h3 class="dev-card-title">Embed MYADS Surfaces</h3>
                            </div>
                            <span class="dev-mini-chip">2</span>
                        </div>
                        <p class="dev-card-copy" style="margin-top: 12px;">{{ __('messages.dev_widgets_desc', ['site' => $site_settings->titer ?? 'MYADS']) }}</p>

                        <ul class="dev-list-reset" style="margin-top: 18px;">
                            <li>
                                <i class="fa fa-check-circle"></i>
                                <span><strong>Follow Widget:</strong> <code>&lt;div id="myads-widget-follow-APPID"&gt;&lt;/div&gt;</code></span>
                            </li>
                            <li>
                                <i class="fa fa-check-circle"></i>
                                <span><strong>Profile Widget:</strong> <code>&lt;div id="myads-widget-profile-APPID"&gt;&lt;/div&gt;</code></span>
                            </li>
                            <li>
                                <i class="fa fa-check-circle"></i>
                                <span><strong>Content Widget:</strong> <code>&lt;div id="myads-widget-content-APPID"&gt;&lt;/div&gt;</code></span>
                            </li>
                        </ul>
                    </div>
                </article>

                <article class="widget-box dev-panel dev-doc-card">
                    <div class="widget-box-content">
                        <span class="dev-doc-icon">
                            <i class="fa fa-share-nodes"></i>
                        </span>
                        <div class="dev-card-head">
                            <div>
                                <p class="dev-kicker">Share API</p>
                                <h3 class="dev-card-title">Pre-fill the Composer</h3>
                            </div>
                            <span class="dev-mini-chip">3</span>
                        </div>
                        <p class="dev-card-copy" style="margin-top: 12px;">{{ __('messages.dev_share_desc') }}</p>

                        <div class="dev-code-block">
                            <div class="dev-code-toolbar">
                                <span>Code Sample</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy="GET /share?text=Hello+World&amp;url=https://example.com">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
                            <pre><code>GET /share?text=Hello+World
&amp;url=https://example.com</code></pre>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>

    <div class="grid-column">
        @include('theme::developer.partials.account_state', ['apps' => $developerApps])
    </div>
</div>
@endsection

@push('scripts')
    @include('theme::developer.partials.scripts')
@endpush
