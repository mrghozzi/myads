@extends('theme::layouts.master')

@section('title', __('messages.app_directory') ?? 'App Directory & Integrations')

@push('head')
    @include('theme::developer.partials.styles')
@endpush

@section('content')
<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-store" style="font-size: 26px; color: #fff;"></i>
    </div>
    <p class="section-banner-title">{{ __('messages.app_directory') ?? 'App Directory & Ecosystem' }}</p>
    <p class="section-banner-text">Discover verified apps, tools, and integrations built by developers for the MYADS platform.</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <div class="dev-side-stack">
            @include('theme::developer.partials.nav', ['active' => 'directory'])

            <div class="widget-box dev-panel">
                <p class="widget-box-title">Categories</p>
                <div class="widget-box-content padding-none">
                    <a href="{{ route('developer.directory.index') }}" class="dev-nav-link {{ !request('category') ? 'is-active' : '' }}">
                        <span>All Categories</span>
                    </a>
                    @foreach($categories as $catKey => $catLabel)
                        <a href="{{ route('developer.directory.index', ['category' => $catKey]) }}" class="dev-nav-link {{ request('category') === $catKey ? 'is-active' : '' }}">
                            <span>{{ $catLabel }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <!-- Search bar -->
        <div class="widget-box dev-panel" style="margin-bottom: 24px;">
            <div class="widget-box-content" style="padding: 16px 20px;">
                <form action="{{ route('developer.directory.index') }}" method="GET" style="display: flex; gap: 10px;">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control dev-control" placeholder="Search apps, integrations, bots..." style="flex: 1;">
                    <button type="submit" class="button primary" style="padding: 0 18px;">
                        <i class="fa fa-search me-1"></i> Search
                    </button>
                </form>
            </div>
        </div>

        @if($apps->isEmpty())
            <div class="widget-box dev-panel">
                <div class="widget-box-content" style="text-align: center; padding: 50px 20px; color: var(--dev-muted);">
                    <i class="fa fa-cubes" style="font-size: 36px; margin-bottom: 14px; opacity: 0.5;"></i>
                    <p style="font-size: 15px; font-weight: 600;">No applications found matching your criteria.</p>
                    <p style="font-size: 13px;">Are you a developer? Publish your approved app to the App Directory!</p>
                    <a href="{{ route('developer.apps.create') }}" class="button primary" style="margin-top: 14px;">
                        <i class="fa fa-plus me-1"></i> Create Developer App
                    </a>
                </div>
            </div>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 18px;">
                @foreach($apps as $app)
                    <div class="widget-box dev-panel" style="display: flex; flex-direction: column; justify-content: space-between; padding: 22px; border-radius: 12px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
                                @if($app->logo)
                                    <img src="{{ asset($app->logo) }}" alt="{{ $app->name }}" style="width: 48px; height: 48px; border-radius: 10px; object-fit: cover;">
                                @else
                                    <div style="width: 48px; height: 48px; border-radius: 10px; background: linear-gradient(135deg, var(--dev-accent), #23d2e2); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 18px;">
                                        {{ strtoupper(mb_substr($app->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <h3 style="margin: 0; font-size: 16px; font-weight: 800; color: var(--dev-title);">{{ $app->name }}</h3>
                                    <span style="font-size: 11px; color: var(--dev-muted);">By {{ $app->user->username ?? 'Developer' }}</span>
                                </div>
                            </div>

                            <p style="font-size: 13px; color: var(--dev-text); line-height: 1.5; margin-bottom: 16px; min-height: 40px;">
                                {{ \Illuminate\Support\Str::limit($app->description, 100) }}
                            </p>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 14px; border-top: 1px solid var(--dev-border);">
                            <span class="dev-chip" style="font-size: 11px; text-transform: capitalize;">
                                {{ $app->category ?: 'Integration' }}
                            </span>
                            <a href="{{ route('developer.directory.show', $app->id) }}" class="button primary" style="font-size: 12px; height: 32px; line-height: 32px; padding: 0 14px;">
                                View Details <i class="fa fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 24px;">
                {{ $apps->links() }}
            </div>
        @endif
    </div>

    <div class="grid-column"></div>
</div>
@endsection
