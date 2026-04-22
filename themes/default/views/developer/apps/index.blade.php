@extends('theme::layouts.master')

@section('title', __('messages.my_apps'))

@push('head')
    @include('theme::developer.partials.styles')
@endpush

@section('content')
@php
    $developerApps = collect($apps ?? []);
@endphp

<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-cubes" style="font-size: 26px; color: #fff;"></i>
    </div>
    <p class="section-banner-title">{{ __('messages.my_apps') }}</p>
    <p class="section-banner-text">{{ __('messages.dev_platform_desc', ['site' => $site_settings->titer ?? 'MYADS']) }}</p>
</div>

<div class="grid grid-3-9 mobile-prefer-content">
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

        <div class="dev-shell">
            <div class="widget-box dev-panel">
                <div class="widget-box-content" style="padding: 28px;">
                    <div class="dev-summary-head">
                        <div>
                            <p class="dev-kicker">{{ __('messages.applications') }}</p>
                            <h2 class="dev-section-title">{{ __('messages.my_apps') }}</h2>
                            <p class="dev-summary-copy" style="margin-top: 8px;">{{ __('messages.dev_platform_settings_desc') }}</p>
                        </div>
                        <a href="{{ route('developer.apps.create') }}" class="button primary">{{ __('messages.create_app') }}</a>
                    </div>

                    <div class="dev-stat-grid" style="margin-top: 20px;">
                        <div class="dev-stat-card">
                            <span>{{ __('messages.total_apps') }}</span>
                            <strong>{{ $developerApps->count() }}</strong>
                        </div>
                        <div class="dev-stat-card">
                            <span>{{ __('messages.active_apps') }}</span>
                            <strong>{{ $developerApps->where('status', 'active')->count() }}</strong>
                        </div>
                        <div class="dev-stat-card">
                            <span>{{ __('messages.pending_review') }}</span>
                            <strong>{{ $developerApps->where('status', 'pending_review')->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            @if($developerApps->isEmpty())
                <div class="widget-box dev-panel">
                    <div class="widget-box-content" style="padding: 28px;">
                        <div class="dev-empty">
                            <i class="fa fa-cubes"></i>
                            <p class="dev-card-copy">{{ __('messages.no_apps_yet') }}</p>
                            <div class="dev-inline-actions" style="justify-content: center; margin-top: 16px;">
                                <a href="{{ route('developer.apps.create') }}" class="button primary">{{ __('messages.create_app') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="dev-app-list">
                    @foreach($developerApps as $developerApp)
                        <article class="widget-box dev-panel dev-app-card">
                            <div class="widget-box-content">
                                <div class="dev-app-card-head">
                                    <div>
                                        <a href="{{ route('developer.apps.show', $developerApp->id) }}" class="dev-app-name">
                                            <i class="fa fa-cube"></i>
                                            {{ $developerApp->name }}
                                        </a>
                                        <div class="dev-app-domain" style="margin-top: 8px;">
                                            <i class="fa fa-globe"></i>
                                            {{ parse_url($developerApp->domain, PHP_URL_HOST) ?: $developerApp->domain }}
                                        </div>
                                    </div>

                                    @include('theme::developer.partials.status_badge', ['status' => $developerApp->status])
                                </div>

                                <p class="dev-app-description" style="margin-top: 16px;">
                                    {{ \Illuminate\Support\Str::limit($developerApp->description, 180) }}
                                </p>

                                <div class="dev-app-meta" style="margin-top: 18px;">
                                    <span class="dev-chip">
                                        <i class="fa fa-link"></i>
                                        {{ count($developerApp->redirect_uris ?? []) }} {{ __('messages.redirect_uris') }}
                                    </span>
                                    <span class="dev-chip">
                                        <i class="fa fa-shield-halved"></i>
                                        {{ count($developerApp->requested_scopes ?? []) }} {{ __('messages.requested_scopes') }}
                                    </span>
                                </div>

                                <div class="dev-inline-actions" style="margin-top: 20px;">
                                    <a href="{{ route('developer.apps.show', $developerApp->id) }}" class="button secondary">{{ __('messages.manage') }}</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
