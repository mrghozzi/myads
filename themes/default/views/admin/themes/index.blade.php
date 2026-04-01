@extends('theme::layouts.admin')

@section('title', __('messages.themes'))
@section('admin_shell_header_mode', 'hidden')

@php
    $themeCount = count($themes);
    $themeUpdateCount = count($updates);
    $activeTheme = collect($themes)->firstWhere('is_active', true);
@endphp

@section('content')
<div class="main-content container-lg px-4">
    <section class="extension-hub extension-hub--themes">
        <div class="row g-0 align-items-center mb-4">
            <div class="col-12">
                <div class="extension-hub__hero">
                    <span class="extension-hub__hero-icon">
                        <i class="fa-solid fa-layer-group"></i>
                    </span>

                    <div class="row align-items-center g-4 position-relative">
                        <div class="col-xl-7">
                            <span class="extension-hub__hero-kicker">
                                <i class="feather-layout"></i>
                                {{ __('messages.themes') }}
                            </span>
                            <h1 class="extension-hub__hero-title mt-4">{{ __('messages.themes') }}</h1>
                            <p class="extension-hub__hero-desc">{{ __('messages.themes_desc') }}</p>
                        </div>
                        <div class="col-xl-5 text-xl-end">
                            <div class="extension-hub__hero-panel ms-xl-auto">
                                <span class="extension-hub__hero-panel-icon">
                                    <i class="feather-star"></i>
                                </span>
                                <span>
                                    <span class="extension-hub__hero-panel-label">{{ __('messages.current_theme') }}</span>
                                    <span class="extension-hub__hero-panel-value">{{ $activeTheme['name'] ?? __('messages.unknown') }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 extension-hub__stats mb-4">
            <div class="col-md-4">
                <div class="extension-hub__stat">
                    <div class="extension-hub__stat-label">
                        <span class="extension-hub__stat-icon"><i class="feather-layers"></i></span>
                        {{ __('messages.total_themes') }}
                    </div>
                    <div class="extension-hub__stat-value">{{ $themeCount }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="extension-hub__stat">
                    <div class="extension-hub__stat-label">
                        <span class="extension-hub__stat-icon"><i class="feather-star"></i></span>
                        {{ __('messages.current_theme') }}
                    </div>
                    <div class="extension-hub__stat-value">{{ $activeTheme['name'] ?? '-' }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="extension-hub__stat">
                    <div class="extension-hub__stat-label">
                        <span class="extension-hub__stat-icon"><i class="feather-arrow-up-circle"></i></span>
                        {{ __('messages.available_updates') }}
                    </div>
                    <div class="extension-hub__stat-value">{{ $themeUpdateCount }}</div>
                </div>
            </div>
        </div>

        <div class="extension-hub__surface p-4 p-xl-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <h2 class="extension-hub__section-title">{{ __('messages.themes') }}</h2>
                    <p class="extension-hub__section-subtitle">{{ __('messages.themes_desc') }}</p>
                </div>
                <span class="extension-hub__count-pill">
                    <i class="feather-layers"></i>
                    {{ $themeCount }} {{ __('messages.total_themes') }}
                </span>
            </div>

            @if(empty($themes))
                <div class="extension-hub__empty">
                    <div class="extension-hub__empty-icon">
                        <i class="feather-layout"></i>
                    </div>
                    <h3 class="extension-hub__section-title mb-2">{{ __('messages.no_themes_found') }}</h3>
                    <p class="extension-hub__section-subtitle mb-0">{{ __('messages.no_themes_desc') }}</p>
                </div>
            @else
                <div class="row g-4">
                    @foreach($themes as $theme)
                        @php
                            $themeUpdate = $updates[$theme['slug']] ?? null;
                            $themeAuthor = $theme['author'] ?? __('messages.unknown');
                            $themeDescription = trim((string) ($theme['description'] ?? ''));
                        @endphp
                        <div class="col-md-6 col-xxl-4">
                            <article class="extension-hub__theme-card d-flex flex-column">
                                <div class="extension-hub__theme-preview">
                                    @if($theme['screenshot'])
                                        <img src="{{ $theme['screenshot'] }}" alt="{{ $theme['name'] }}" loading="lazy">
                                    @else
                                        <div class="extension-hub__theme-fallback">
                                            <i class="feather-image"></i>
                                        </div>
                                    @endif

                                    <div class="extension-hub__theme-overlay">
                                        @if($theme['is_active'])
                                            <span class="extension-hub__status-badge extension-hub__status-badge--active">
                                                <i class="feather-check-circle"></i>
                                                {{ __('messages.active') }}
                                            </span>
                                        @endif

                                        @if($themeUpdate)
                                            <span class="extension-hub__update-badge">
                                                <i class="feather-arrow-up-circle"></i>
                                                {{ __('messages.update_available') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                    <div>
                                        <h3 class="extension-hub__card-title">{{ $theme['name'] }}</h3>
                                        <div class="extension-hub__slug">{{ $theme['slug'] }}</div>
                                    </div>
                                    <span class="extension-hub__token">
                                        <i class="feather-tag"></i>
                                        {{ $theme['version'] ?? '1.0' }}
                                    </span>
                                </div>

                                <p class="extension-hub__card-description">{{ $themeDescription !== '' ? $themeDescription : '-' }}</p>

                                <div class="extension-hub__token-row">
                                    <span class="extension-hub__token">
                                        <i class="feather-user"></i>
                                        {{ __('messages.by') }} {{ $themeAuthor }}
                                    </span>
                                    @if(!empty($theme['min_myads']))
                                        <span class="extension-hub__token">
                                            <i class="feather-shield"></i>
                                            {{ __('messages.requires_myads') }}: {{ $theme['min_myads'] }}
                                        </span>
                                    @endif
                                </div>

                                <div class="extension-hub__actions">
                                    @if($themeUpdate && !empty($themeUpdate['changelog']))
                                        <button
                                            type="button"
                                            class="btn-extension-glass btn-extension-glass--muted"
                                            data-bs-toggle="modal"
                                            data-bs-target="#extensionChangelogModal"
                                            data-name="{{ $theme['name'] }}"
                                            data-slug="{{ $theme['slug'] }}"
                                            data-changelog="{{ base64_encode($themeUpdate['changelog']) }}"
                                            data-github-url="{{ $themeUpdate['github_url'] ?? '' }}"
                                            data-upgrade-action="{{ !empty($themeUpdate['download_url']) ? route('admin.themes.upgrade') : '' }}"
                                            title="{{ __('messages.view_changelog') }}"
                                        >
                                            <i class="feather-info"></i>
                                            <span>{{ __('messages.view_changelog') }}</span>
                                        </button>
                                    @endif

                                    @if($themeUpdate && !empty($themeUpdate['download_url']))
                                        <form action="{{ route('admin.themes.upgrade') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="slug" value="{{ $theme['slug'] }}">
                                            <button type="submit" class="btn-extension-glass btn-extension-glass--primary" title="{{ __('messages.update_now') }}">
                                                <i class="feather-download-cloud"></i>
                                                <span>{{ __('messages.update_now') }}</span>
                                            </button>
                                        </form>
                                    @endif

                                    @if(!$theme['is_active'])
                                        <form action="{{ route('admin.themes.activate') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="slug" value="{{ $theme['slug'] }}">
                                            <button type="submit" class="btn-extension-glass btn-extension-glass--success">
                                                <i class="feather-check"></i>
                                                <span>{{ __('messages.activate') }}</span>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn-extension-glass btn-extension-glass--muted" disabled>
                                            <i class="feather-check-circle"></i>
                                            <span>{{ __('messages.Activated') }}</span>
                                        </button>
                                    @endif
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@section('modals')
@php($showExtensionDeleteModal = false)
@include('theme::admin.partials.extension_hub_modal_scripts')
@endsection

@push('scripts')
@include('theme::admin.partials.extension_hub_styles')
@endpush
