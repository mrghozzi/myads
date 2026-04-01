@extends('theme::layouts.admin')

@section('title', __('messages.plugins'))
@section('admin_shell_header_mode', 'hidden')

@php
    $pluginCount = count($plugins);
    $activePluginCount = collect($plugins)->where('is_active', true)->count();
    $pluginUpdateCount = count($updates);
@endphp

@section('content')
<div class="main-content container-lg px-4">
    <section class="extension-hub extension-hub--plugins">
        <div class="row g-0 align-items-center mb-4">
            <div class="col-12">
                <div class="extension-hub__hero">
                    <span class="extension-hub__hero-icon">
                        <i class="fa-solid fa-puzzle-piece"></i>
                    </span>

                    <div class="row align-items-center g-4 position-relative">
                        <div class="col-xl-7">
                            <span class="extension-hub__hero-kicker">
                                <i class="feather-box"></i>
                                {{ __('messages.plugins') }}
                            </span>
                            <h1 class="extension-hub__hero-title mt-4">{{ __('messages.plugins') }}</h1>
                            <p class="extension-hub__hero-desc">{{ __('messages.plugins_desc') }}</p>
                        </div>
                        <div class="col-xl-5 text-xl-end">
                            <button type="button" class="btn btn-light btn-lg fw-bold shadow-sm px-4 py-3" data-bs-toggle="modal" data-bs-target="#uploadPluginModal" style="border-radius: 16px; color: var(--extension-hub-accent);">
                                <i class="feather-upload-cloud me-2"></i> {{ __('messages.upload_plugin') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 extension-hub__stats mb-4">
            <div class="col-md-4">
                <div class="extension-hub__stat">
                    <div class="extension-hub__stat-label">
                        <span class="extension-hub__stat-icon"><i class="feather-box"></i></span>
                        {{ __('messages.total_plugins') }}
                    </div>
                    <div class="extension-hub__stat-value">{{ $pluginCount }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="extension-hub__stat">
                    <div class="extension-hub__stat-label">
                        <span class="extension-hub__stat-icon"><i class="feather-check-circle"></i></span>
                        {{ __('messages.active_plugins') }}
                    </div>
                    <div class="extension-hub__stat-value">{{ $activePluginCount }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="extension-hub__stat">
                    <div class="extension-hub__stat-label">
                        <span class="extension-hub__stat-icon"><i class="feather-arrow-up-circle"></i></span>
                        {{ __('messages.available_updates') }}
                    </div>
                    <div class="extension-hub__stat-value">{{ $pluginUpdateCount }}</div>
                </div>
            </div>
        </div>

        <div class="extension-hub__surface p-4 p-xl-5">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <h2 class="extension-hub__section-title">{{ __('messages.plugins') }}</h2>
                    <p class="extension-hub__section-subtitle">{{ __('messages.upload_plugin_info') }}</p>
                </div>
                <span class="extension-hub__count-pill">
                    <i class="feather-layers"></i>
                    {{ $pluginCount }} {{ __('messages.total_plugins') }}
                </span>
            </div>

            @if(empty($plugins))
                <div class="extension-hub__empty">
                    <div class="extension-hub__empty-icon">
                        <i class="feather-box"></i>
                    </div>
                    <h3 class="extension-hub__section-title mb-2">{{ __('messages.no_plugins_found') }}</h3>
                    <p class="extension-hub__section-subtitle mb-4">{{ __('messages.no_plugins_desc') }}</p>
                    <button type="button" class="btn btn-primary px-4 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#uploadPluginModal">
                        <i class="feather-upload me-2"></i>{{ __('messages.upload_first_plugin') }}
                    </button>
                </div>
            @else
                <div class="row g-4">
                    @foreach($plugins as $plugin)
                        @php
                            $pluginUpdate = $updates[$plugin['slug']] ?? null;
                            $pluginThumbnail = !empty($plugin['thumbnail']) ? route('admin.plugins.thumbnail', $plugin['slug']) : null;
                            $pluginAuthor = $plugin['author'] ?? __('messages.unknown');
                            $pluginDescription = trim((string) ($plugin['description'] ?? ''));
                        @endphp
                        <div class="col-12 col-xxl-6">
                            <article class="extension-hub__list-card d-flex flex-column">
                                <div class="extension-hub__card-head">
                                    <div class="extension-hub__thumbnail">
                                        @if($pluginThumbnail)
                                            <img src="{{ $pluginThumbnail }}" alt="{{ $plugin['name'] }}" loading="lazy">
                                        @else
                                            {{ strtoupper(substr($plugin['name'], 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="extension-hub__badge-stack mb-2">
                                            @if($plugin['is_active'])
                                                <span class="extension-hub__status-badge extension-hub__status-badge--active">
                                                    <i class="feather-check-circle"></i>
                                                    {{ __('messages.active') }}
                                                </span>
                                            @else
                                                <span class="extension-hub__status-badge extension-hub__status-badge--inactive">
                                                    <i class="feather-minus-circle"></i>
                                                    {{ __('messages.inactive') }}
                                                </span>
                                            @endif

                                            @if($pluginUpdate)
                                                <span class="extension-hub__update-badge">
                                                    <i class="feather-arrow-up-circle"></i>
                                                    {{ __('messages.update_available') }}: {{ $pluginUpdate['new_version'] }}
                                                </span>
                                            @endif
                                        </div>

                                        <h3 class="extension-hub__card-title">{{ $plugin['name'] }}</h3>
                                        <div class="extension-hub__slug">{{ $plugin['slug'] }}</div>
                                        <p class="extension-hub__card-description">{{ $pluginDescription !== '' ? $pluginDescription : '-' }}</p>
                                    </div>
                                </div>

                                <div class="extension-hub__token-row">
                                    <span class="extension-hub__token">
                                        <i class="feather-tag"></i>
                                        {{ __('messages.version') }}: {{ $plugin['version'] ?? '1.0' }}
                                    </span>
                                    <span class="extension-hub__token">
                                        <i class="feather-user"></i>
                                        {{ __('messages.author') }}: {{ $pluginAuthor }}
                                    </span>
                                    @if(!empty($plugin['min_myads']))
                                        <span class="extension-hub__token">
                                            <i class="feather-shield"></i>
                                            {{ __('messages.requires_myads') }}: {{ $plugin['min_myads'] }}
                                        </span>
                                    @endif
                                </div>

                                <div class="extension-hub__actions">
                                    @if($plugin['is_active'])
                                        <form action="{{ route('admin.plugins.deactivate') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                                            <button type="submit" class="btn-extension-glass btn-extension-glass--warning" title="{{ __('messages.deactivate') }}">
                                                <i class="feather-pause"></i>
                                                <span>{{ __('messages.deactivate') }}</span>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.plugins.activate') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                                            <button type="submit" class="btn-extension-glass btn-extension-glass--success" title="{{ __('messages.activate') }}">
                                                <i class="feather-play"></i>
                                                <span>{{ __('messages.activate') }}</span>
                                            </button>
                                        </form>
                                    @endif

                                    <button
                                        type="button"
                                        class="btn-extension-glass btn-extension-glass--danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#extensionDeleteModal"
                                        data-action="{{ route('admin.plugins.delete') }}"
                                        data-slug="{{ $plugin['slug'] }}"
                                        data-name="{{ $plugin['name'] }}"
                                        data-identifier="{{ $plugin['slug'] }}"
                                        data-warning="{{ __('messages.delete_plugin_warning') }}"
                                        data-is-active="{{ $plugin['is_active'] ? '1' : '0' }}"
                                        data-active-error="{{ __('messages.plugin_delete_active_forbidden') }}"
                                        title="{{ __('messages.delete') }}"
                                    >
                                        <i class="feather-trash-2"></i>
                                        <span>{{ __('messages.delete') }}</span>
                                    </button>

                                    @if($pluginUpdate && !empty($pluginUpdate['changelog']))
                                        <button
                                            type="button"
                                            class="btn-extension-glass btn-extension-glass--muted"
                                            data-bs-toggle="modal"
                                            data-bs-target="#extensionChangelogModal"
                                            data-name="{{ $plugin['name'] }}"
                                            data-slug="{{ $plugin['slug'] }}"
                                            data-changelog="{{ base64_encode($pluginUpdate['changelog']) }}"
                                            data-github-url="{{ $pluginUpdate['github_url'] ?? '' }}"
                                            data-upgrade-action="{{ !empty($pluginUpdate['download_url']) ? route('admin.plugins.upgrade') : '' }}"
                                            title="{{ __('messages.view_changelog') }}"
                                        >
                                            <i class="feather-info"></i>
                                            <span>{{ __('messages.view_changelog') }}</span>
                                        </button>
                                    @endif

                                    @if($pluginUpdate && !empty($pluginUpdate['download_url']))
                                        <form action="{{ route('admin.plugins.upgrade') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                                            <button type="submit" class="btn-extension-glass btn-extension-glass--primary" title="{{ __('messages.update_now') }}">
                                                <i class="feather-download-cloud"></i>
                                                <span>{{ __('messages.update_now') }}</span>
                                            </button>
                                        </form>
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
<div class="modal fade" id="uploadPluginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.upload_plugin') }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.plugins.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info mb-4">
                        <i class="feather-info me-2"></i> {{ __('messages.upload_plugin_info') }}
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.plugin_zip_file') }}</label>
                        <input type="file" name="plugin_zip" class="form-control" accept=".zip" required>
                        <div class="form-text">{{ __('messages.allowed_file_types') }}: .zip</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm" style="border-radius: 10px;">{{ __('messages.install_now') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@php($showExtensionDeleteModal = true)
@include('theme::admin.partials.extension_hub_modal_scripts')
@endsection

@push('scripts')
@include('theme::admin.partials.extension_hub_styles')
@endpush
