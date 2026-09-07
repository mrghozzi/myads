@extends('admin::layouts.admin')

@section('title', __('messages.plugins'))
@section('admin_shell_header_mode', 'hidden')

@php
    $pluginCount = count($plugins);
    $activePluginCount = collect($plugins)->where('is_active', true)->count();
    $pluginUpdateCount = count($updates);
@endphp

@section('content')
<div class="main-content container-lg px-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="feather-check-circle me-3 fs-18"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="feather-alert-octagon me-3 fs-18"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
                            <a href="{{ route('admin.plugins.inspector') }}" class="btn btn-outline-light btn-lg fw-bold shadow-sm px-4 py-3 me-2" style="border-radius: 16px;">
                                <i class="feather-layers me-2"></i> {{ __('messages.plugins_inspector') }}
                            </a>
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
                    <div class="extension-hub__stat-value" id="stat-total-plugins">{{ $pluginCount }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="extension-hub__stat">
                    <div class="extension-hub__stat-label">
                        <span class="extension-hub__stat-icon"><i class="feather-check-circle"></i></span>
                        {{ __('messages.active_plugins') }}
                    </div>
                    <div class="extension-hub__stat-value" id="stat-active-plugins">{{ $activePluginCount }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="extension-hub__stat">
                    <div class="extension-hub__stat-label">
                        <span class="extension-hub__stat-icon"><i class="feather-arrow-up-circle"></i></span>
                        {{ __('messages.available_updates') }}
                    </div>
                    <div class="extension-hub__stat-value" id="stat-update-plugins">{{ $pluginUpdateCount }}</div>
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
                    <span id="pill-total-plugins">{{ $pluginCount }}</span> {{ __('messages.total_plugins') }}
                </span>
            </div>

            <ul class="nav extension-hub__tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link extension-hub__tab active" data-bs-toggle="tab" data-bs-target="#plugins-installed-tab" type="button" role="tab" aria-selected="true">
                        <i class="feather-box"></i>
                        <span>{{ __('messages.plugins') }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link extension-hub__tab" data-bs-toggle="tab" data-bs-target="#plugins-marketplace-tab" type="button" role="tab" aria-selected="false">
                        <i class="feather-shopping-bag"></i>
                        <span>{{ __('messages.marketplace') }}</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="plugins-installed-tab" role="tabpanel">
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
                        <!-- Filter & Search Toolbar -->
                        <div class="extension-hub__filter-toolbar d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 p-3 rounded-4" style="background: rgba(248, 250, 252, 0.95); border: 1px solid rgba(226, 232, 240, 0.9);">
                            <div class="d-flex align-items-center gap-2 flex-wrap" id="plugin-filter-group">
                                <span class="text-muted small fw-bold me-1 d-inline-flex align-items-center gap-1">
                                    <i class="feather-filter"></i>
                                    {{ __('messages.filter') ?? 'تصفية' }}:
                                </span>
                                <button type="button" class="btn btn-sm plugin-filter-btn active" data-filter="all">
                                    <span>{{ __('messages.all') ?? 'الكل' }}</span>
                                    <span class="badge rounded-pill bg-white text-dark ms-1 shadow-xs" id="count-all">{{ $pluginCount }}</span>
                                </button>
                                <button type="button" class="btn btn-sm plugin-filter-btn" data-filter="active">
                                    <i class="feather-check-circle text-success me-1"></i>
                                    <span>{{ __('messages.active') ?? 'المفعلة' }}</span>
                                    <span class="badge rounded-pill bg-success-subtle text-success ms-1" id="count-active">{{ $activePluginCount }}</span>
                                </button>
                                <button type="button" class="btn btn-sm plugin-filter-btn" data-filter="inactive">
                                    <i class="feather-minus-circle text-secondary me-1"></i>
                                    <span>{{ __('messages.inactive') ?? 'غير المفعلة' }}</span>
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary ms-1" id="count-inactive">{{ $pluginCount - $activePluginCount }}</span>
                                </button>
                                <button type="button" class="btn btn-sm plugin-filter-btn" data-filter="updates">
                                    <i class="feather-arrow-up-circle text-warning me-1"></i>
                                    <span>{{ __('messages.available_updates') ?? 'التحديثات المتاحة' }}</span>
                                    <span class="badge rounded-pill bg-warning-subtle text-warning ms-1" id="count-updates">{{ $pluginUpdateCount }}</span>
                                </button>
                            </div>

                            <div class="position-relative" style="min-width: 240px;">
                                <i class="feather-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <input type="text" id="plugin-search-input" class="form-control form-control-sm ps-5 rounded-pill" placeholder="{{ __('messages.search') ?? 'بحث...' }}" style="border-color: rgba(203, 213, 225, 0.8);">
                            </div>
                        </div>

                        <div class="row g-4" id="plugins-grid">
                            @foreach($plugins as $plugin)
                                @php
                                    $pluginUpdate = $updates[$plugin['slug']] ?? null;
                                    $pluginThumbnail = !empty($plugin['thumbnail']) ? route('admin.plugins.thumbnail', $plugin['slug']) : null;
                                    $pluginAuthor = $plugin['author'] ?? __('messages.unknown');
                                    $pluginDescription = trim((string) ($plugin['description'] ?? ''));
                                @endphp
                                <div class="col-12 col-xxl-6 plugin-card-col"
                                     data-slug="{{ $plugin['slug'] }}"
                                     data-name="{{ strtolower($plugin['name']) }}"
                                     data-search-text="{{ strtolower($plugin['name'] . ' ' . $plugin['slug'] . ' ' . $pluginDescription) }}"
                                     data-status="{{ $plugin['is_active'] ? 'active' : 'inactive' }}"
                                     data-has-update="{{ $pluginUpdate ? '1' : '0' }}">
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
                                                <div class="extension-hub__badge-stack mb-2 plugin-status-badge-wrap">
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
                                            <div class="plugin-action-toggle-slot">
                                                @if($plugin['is_active'])
                                                    <form action="{{ route('admin.plugins.deactivate') }}" method="POST" class="plugin-ajax-toggle-form">
                                                        @csrf
                                                        <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                                                        <button type="submit" class="btn-extension-glass btn-extension-glass--warning btn-plugin-toggle" title="{{ __('messages.deactivate') }}">
                                                            <i class="feather-pause"></i>
                                                            <span>{{ __('messages.deactivate') }}</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.plugins.activate') }}" method="POST" class="plugin-ajax-toggle-form">
                                                        @csrf
                                                        <input type="hidden" name="slug" value="{{ $plugin['slug'] }}">
                                                        <button type="submit" class="btn-extension-glass btn-extension-glass--success btn-plugin-toggle" title="{{ __('messages.activate') }}">
                                                            <i class="feather-play"></i>
                                                            <span>{{ __('messages.activate') }}</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>

                                            <button
                                                type="button"
                                                class="btn-extension-glass btn-extension-glass--danger btn-plugin-delete"
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

                                            <button
                                                type="button"
                                                class="btn-extension-glass btn-extension-glass--primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#pluginDetailsModal"
                                                data-slug="{{ $plugin['slug'] }}"
                                                title="{{ __('messages.details') ?? 'Details' }}"
                                            >
                                                <i class="feather-info"></i>
                                                <span>{{ __('messages.details') ?? 'Details' }}</span>
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

                        <div id="plugin-filter-empty" class="extension-hub__empty d-none text-center py-5">
                            <div class="extension-hub__empty-icon mb-3">
                                <i class="feather-filter fs-2 text-muted"></i>
                            </div>
                            <h3 class="extension-hub__section-title mb-2">{{ __('messages.no_results_found') ?? 'لا توجد نتائج' }}</h3>
                            <p class="extension-hub__section-subtitle mb-0" id="plugin-filter-empty-msg">{{ __('messages.no_plugins_matching_filter') ?? 'لا توجد إضافات تطابق هذا التصنيف.' }}</p>
                        </div>
                    @endif
                </div>

                <div class="tab-pane fade" id="plugins-marketplace-tab" role="tabpanel">
                    @include('admin::admin.partials.extension_marketplace_panel', [
                        'marketplaceCatalog' => $marketplaceCatalog, 
                        'installedSlugs' => $installedSlugs,
                        'detailsModalId' => 'pluginDetailsModal'
                    ])
                </div>
            </div>
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
@include('admin::admin.partials.extension_hub_modal_scripts')

<div class="modal fade" id="pluginDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 pb-0 pt-3 px-4 position-absolute top-0 end-0" style="z-index: 1051;">
                <button type="button" class="btn-close shadow-none bg-white rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="plugin-details-thumbnail-wrapper" style="height: 300px; background: #eee; position: relative;">
                <img id="plugin-modal-thumbnail" src="" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.85)); padding: 25px 30px; color: white;">
                    <h2 class="fw-bold mb-1" id="plugin-modal-title"></h2>
                    <p class="mb-0 opacity-75 fs-14" id="plugin-modal-slug-label"></p>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0 flex-nowrap h-100">
                    <!-- Main Content (Tabs) -->
                    <div class="col-md-8 p-4 order-1 scrollable-content">
                        <ul class="nav nav-pills plugin-modal-tabs mb-4 gap-2" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active fw-bold px-4 rounded-pill" data-bs-toggle="tab" data-bs-target="#plugin-tab-description">{{ __('messages.description') ?? 'Description' }}</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link fw-bold px-4 rounded-pill" data-bs-toggle="tab" data-bs-target="#plugin-tab-changelog" id="plugin-btn-changelog">{{ __('messages.changelog') ?? 'Changelog' }}</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link fw-bold px-4 rounded-pill" data-bs-toggle="tab" data-bs-target="#plugin-tab-screenshots" id="plugin-btn-screenshots">{{ __('messages.screenshots') ?? 'Screenshots' }}</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="plugin-modal-tab-content">
                            <div class="tab-pane fade show active" id="plugin-tab-description">
                                <div class="extension-markdown-content p-2" id="plugin-content-description"></div>
                            </div>
                            <div class="tab-pane fade" id="plugin-tab-changelog">
                                <div class="extension-markdown-content p-2" id="plugin-content-changelog"></div>
                            </div>
                            <div class="tab-pane fade" id="plugin-tab-screenshots">
                                <div class="extension-markdown-content p-2" id="plugin-content-screenshots"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar (Metadata) -->
                    <div class="col-md-4 p-4 order-2" id="plugin-modal-sidebar" style="background: #f8f9fa; border-inline-start: 1px solid #dee2e6;">
                        <div class="d-grid gap-4">
                            <div>
                                <label class="text-muted small text-uppercase fw-extrabold d-block mb-1" style="letter-spacing: 0.5px; font-size: 11px;">{{ __('messages.version') ?? 'Version' }}</label>
                                <span class="fw-bold fs-15 text-dark" id="plugin-modal-version"></span>
                            </div>
                            <div>
                                <label class="text-muted small text-uppercase fw-extrabold d-block mb-1" style="letter-spacing: 0.5px; font-size: 11px;">{{ __('messages.author') ?? 'Author' }}</label>
                                <span class="fw-bold fs-15 text-dark" id="plugin-modal-author-name"></span>
                            </div>
                            <div>
                                <label class="text-muted small text-uppercase fw-extrabold d-block mb-1" style="letter-spacing: 0.5px; font-size: 11px;">{{ __('messages.requires_myads') ?? 'Required MyAds' }}</label>
                                <span class="fw-bold fs-15 text-primary" id="plugin-modal-min-myads"></span>
                            </div>
                            <hr class="my-0 opacity-10">
                            <div id="plugin-modal-adstn-wrap">
                                <a href="" target="_blank" class="btn btn-soft-primary btn-sm w-100 text-start d-flex align-items-center justify-content-between py-2 fw-bold" id="plugin-modal-adstn-link">
                                    <span>{{ __('messages.adstn_page') ?? 'ADStn Page' }} »</span>
                                    <i class="feather-external-link"></i>
                                </a>
                            </div>
                            <div id="plugin-modal-website-wrap">
                                <a href="" target="_blank" class="btn btn-soft-secondary btn-sm w-100 text-start d-flex align-items-center justify-content-between py-2 fw-bold" id="plugin-modal-website-link">
                                    <span>{{ __('messages.plugin_website') ?? 'Plugin Website' }} »</span>
                                    <i class="feather-globe"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var pluginDetailsModal = document.getElementById('pluginDetailsModal');
    if (pluginDetailsModal) {
        pluginDetailsModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var slug = button.getAttribute('data-slug');
            
            // Reset content
            document.getElementById('plugin-modal-thumbnail').src = '';
            document.getElementById('plugin-modal-title').textContent = '';
            document.getElementById('plugin-modal-slug-label').textContent = '';
            document.getElementById('plugin-content-description').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
            document.getElementById('plugin-content-changelog').innerHTML = '';
            document.getElementById('plugin-content-screenshots').innerHTML = '';

            document.getElementById('plugin-modal-version').textContent = '';
            document.getElementById('plugin-modal-author-name').textContent = '';
            document.getElementById('plugin-modal-min-myads').textContent = '';
            document.getElementById('plugin-modal-adstn-link').closest('div').classList.add('d-none');
            document.getElementById('plugin-modal-website-link').closest('div').classList.add('d-none');
            
            // Hide tabs by default
            document.getElementById('plugin-btn-changelog').closest('li').classList.add('d-none');
            document.getElementById('plugin-btn-screenshots').closest('li').classList.add('d-none');
            
            // Activate first tab
            const firstTab = document.querySelector('.plugin-modal-tabs button[data-bs-target="#plugin-tab-description"]');
            if (firstTab) {
                const tab = new bootstrap.Tab(firstTab);
                tab.show();
            }
            
            var isMarket = button.getAttribute('data-is-market');
            
            if (isMarket) {
                // Populate from data attributes
                var name = button.getAttribute('data-name');
                var description = button.getAttribute('data-description');
                var version = button.getAttribute('data-version');
                var author = button.getAttribute('data-author');
                var thumbnail = button.getAttribute('data-thumbnail');
                var minMyAds = button.getAttribute('data-min-myads');
                var productUrl = button.getAttribute('data-product-url');

                document.getElementById('plugin-modal-thumbnail').src = thumbnail || '{{ admin_asset("admin-duralux/images/logo-abbr.png") }}';
                document.getElementById('plugin-modal-title').textContent = name;
                document.getElementById('plugin-modal-slug-label').textContent = slug;
                document.getElementById('plugin-modal-version').textContent = version;
                document.getElementById('plugin-modal-author-name').textContent = author;
                document.getElementById('plugin-modal-min-myads').textContent = minMyAds || '-';
                
                // Sidebar & Links Reset
                var adstnLink = document.getElementById('plugin-modal-adstn-link');
                if (productUrl) {
                    adstnLink.closest('div').classList.remove('d-none');
                    adstnLink.href = productUrl;
                } else {
                    adstnLink.closest('div').classList.add('d-none');
                }
                document.getElementById('plugin-modal-website-link').closest('div').classList.add('d-none');
                
                document.getElementById('plugin-content-description').innerHTML = description ? DOMPurify.sanitize(marked.parse(description)) : '-';
                return;
            }

            // Fetch data (for local plugins)
            fetch('{{ url("admin/plugins/details") }}/' + slug)

                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    
                    document.getElementById('plugin-modal-thumbnail').src = data.thumbnail || '{{ admin_asset("admin-duralux/images/logo-abbr.png") }}';
                    document.getElementById('plugin-modal-title').textContent = data.name;
                    document.getElementById('plugin-modal-slug-label').textContent = data.slug;
                    document.getElementById('plugin-modal-version').textContent = data.version;
                    
                    // Author
                    var authorHtml = data.author;
                    if (data.author_url) {
                        authorHtml = '<a href="' + data.author_url + '" target="_blank" class="text-primary text-decoration-none">' + data.author + '</a>';
                    }
                    document.getElementById('plugin-modal-author-name').innerHTML = authorHtml;
                    
                    document.getElementById('plugin-modal-min-myads').textContent = data.min_myads || '-';
                    
                    // Links
                    var adstnLink = document.getElementById('plugin-modal-adstn-link');
                    if (data.ADStn_url) {
                        adstnLink.closest('div').classList.remove('d-none');
                        adstnLink.href = 'https://www.adstn.ovh/store/' + data.ADStn_url;
                    } else {
                        adstnLink.closest('div').classList.add('d-none');
                    }
                    
                    var websiteLink = document.getElementById('plugin-modal-website-link');
                    if (data.siteweb) {
                        websiteLink.closest('div').classList.remove('d-none');
                        websiteLink.href = data.siteweb;
                    } else {
                        websiteLink.closest('div').classList.add('d-none');
                    }
                    
                    // Markdown contents - using marked and DOMPurify for security
                    var readme = data.readme || data.description || '';
                    document.getElementById('plugin-content-description').innerHTML = readme ? DOMPurify.sanitize(marked.parse(readme)) : '-';
                    
                    if (data.changelogs) {
                        document.getElementById('plugin-btn-changelog').closest('li').classList.remove('d-none');
                        document.getElementById('plugin-content-changelog').innerHTML = DOMPurify.sanitize(marked.parse(data.changelogs));
                    }
                    
                    if (data.screenshots) {
                        document.getElementById('plugin-btn-screenshots').closest('li').classList.remove('d-none');
                        document.getElementById('plugin-content-screenshots').innerHTML = DOMPurify.sanitize(marked.parse(data.screenshots));
                    }
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('plugin-content-description').innerHTML = '<div class="alert alert-danger">Error loading plugin details.</div>';
                });
        });
    }

    // --- Dynamic Filtering & AJAX Toggle for Plugins ---
    var currentFilter = 'all';
    var currentSearch = '';
    var pluginCards = Array.from(document.querySelectorAll('.plugin-card-col'));
    var searchInput = document.getElementById('plugin-search-input');
    var filterButtons = document.querySelectorAll('.plugin-filter-btn');
    var emptyState = document.getElementById('plugin-filter-empty');
    var emptyStateMsg = document.getElementById('plugin-filter-empty-msg');

    var pluginLabels = {
        active: @json(__('messages.active') ?? 'مفعل'),
        inactive: @json(__('messages.inactive') ?? 'غير مفعل'),
        activate: @json(__('messages.activate') ?? 'تفعيل'),
        deactivate: @json(__('messages.deactivate') ?? 'تعطيل'),
        updating: @json(__('messages.updating') ?? 'جاري التحديث...'),
        activateRoute: @json(route('admin.plugins.activate')),
        deactivateRoute: @json(route('admin.plugins.deactivate')),
        errorOccurred: @json(__('messages.error_occurred') ?? 'حدث خطأ ما.'),
        noActive: 'لا توجد إضافات مفعلة حالياً.',
        noInactive: 'لا توجد إضافات غير مفعلة حالياً.',
        noUpdates: 'لا توجد تحديثات متاحة حالياً.',
        noMatching: 'لا توجد إضافات تطابق هذا البحث أو التصنيف.'
    };

    function showExtensionToast(message, type) {
        type = type || 'success';
        var container = document.getElementById('plugin-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'plugin-toast-container';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = '99999';
            container.style.maxWidth = '400px';
            container.style.pointerEvents = 'none';
            document.body.appendChild(container);
        }

        var toastEl = document.createElement('div');
        var isSuccess = type === 'success';
        toastEl.className = 'toast show border-0 shadow-lg mb-2 text-white';
        toastEl.style.borderRadius = '14px';
        toastEl.style.background = isSuccess ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
        toastEl.style.boxShadow = '0 10px 25px rgba(0,0,0,0.18)';
        toastEl.style.pointerEvents = 'auto';
        toastEl.style.transition = 'all 0.3s ease';

        toastEl.innerHTML = `
            <div class="d-flex align-items-center p-3">
                <i class="${isSuccess ? 'feather-check-circle' : 'feather-alert-triangle'} fs-18 me-2"></i>
                <div class="flex-grow-1 fw-bold fs-14">${message}</div>
                <button type="button" class="btn-close btn-close-white ms-2 shadow-none" aria-label="Close"></button>
            </div>
        `;

        toastEl.querySelector('.btn-close').addEventListener('click', function () {
            toastEl.style.opacity = '0';
            toastEl.style.transform = 'translateY(-10px)';
            setTimeout(function () { toastEl.remove(); }, 300);
        });

        container.appendChild(toastEl);

        setTimeout(function () {
            if (toastEl.parentNode) {
                toastEl.style.opacity = '0';
                toastEl.style.transform = 'translateY(-10px)';
                setTimeout(function () { toastEl.remove(); }, 300);
            }
        }, 4000);
    }

    function updateCounts(activeCountOverride) {
        var total = pluginCards.length;
        var active = typeof activeCountOverride === 'number'
            ? activeCountOverride
            : pluginCards.filter(function (col) { return col.getAttribute('data-status') === 'active'; }).length;
        var inactive = total - active;
        var updates = pluginCards.filter(function (col) { return col.getAttribute('data-has-update') === '1'; }).length;

        var countAll = document.getElementById('count-all');
        if (countAll) countAll.textContent = total;

        var countActive = document.getElementById('count-active');
        if (countActive) countActive.textContent = active;

        var countInactive = document.getElementById('count-inactive');
        if (countInactive) countInactive.textContent = inactive;

        var countUpdates = document.getElementById('count-updates');
        if (countUpdates) countUpdates.textContent = updates;

        var statActive = document.getElementById('stat-active-plugins');
        if (statActive) statActive.textContent = active;

        var statTotal = document.getElementById('stat-total-plugins');
        if (statTotal) statTotal.textContent = total;

        var statUpdates = document.getElementById('stat-update-plugins');
        if (statUpdates) statUpdates.textContent = updates;

        var pillTotal = document.getElementById('pill-total-plugins');
        if (pillTotal) pillTotal.textContent = total;
    }

    function applyFilter() {
        var visibleCount = 0;
        var query = (currentSearch || '').trim().toLowerCase();

        pluginCards.forEach(function (card) {
            var status = card.getAttribute('data-status');
            var hasUpdate = card.getAttribute('data-has-update') === '1';
            var searchText = (card.getAttribute('data-search-text') || '').toLowerCase();

            var matchesFilter = true;
            if (currentFilter === 'active') {
                matchesFilter = (status === 'active');
            } else if (currentFilter === 'inactive') {
                matchesFilter = (status === 'inactive');
            } else if (currentFilter === 'updates') {
                matchesFilter = hasUpdate;
            }

            var matchesSearch = true;
            if (query !== '') {
                matchesSearch = searchText.indexOf(query) !== -1;
            }

            if (matchesFilter && matchesSearch) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (emptyState) {
            if (visibleCount === 0 && pluginCards.length > 0) {
                emptyState.classList.remove('d-none');
                if (emptyStateMsg) {
                    if (query !== '') {
                        emptyStateMsg.textContent = pluginLabels.noMatching;
                    } else if (currentFilter === 'active') {
                        emptyStateMsg.textContent = pluginLabels.noActive;
                    } else if (currentFilter === 'inactive') {
                        emptyStateMsg.textContent = pluginLabels.noInactive;
                    } else if (currentFilter === 'updates') {
                        emptyStateMsg.textContent = pluginLabels.noUpdates;
                    } else {
                        emptyStateMsg.textContent = pluginLabels.noMatching;
                    }
                }
            } else {
                emptyState.classList.add('d-none');
            }
        }
    }

    // Filter Buttons Click
    filterButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterButtons.forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter') || 'all';
            applyFilter();
        });
    });

    // Search Input
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            currentSearch = this.value;
            applyFilter();
        });
    }

    // AJAX Toggle (Activate / Deactivate)
    document.addEventListener('submit', function (e) {
        var form = e.target.closest('.plugin-ajax-toggle-form');
        if (!form) return;

        e.preventDefault();

        var btn = form.querySelector('.btn-plugin-toggle');
        if (!btn || btn.disabled) return;

        var slugInput = form.querySelector('input[name="slug"]');
        var slug = slugInput ? slugInput.value : '';
        if (!slug) return;

        var card = form.closest('.plugin-card-col');
        var originalHtml = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' + pluginLabels.updating;

        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || (form.querySelector('input[name="_token"]') ? form.querySelector('input[name="_token"]').value : '');

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ slug: slug })
        })
        .then(function (response) {
            return response.json().then(function (data) {
                return { ok: response.ok, status: response.status, data: data };
            }).catch(function () {
                return { ok: response.ok, status: response.status, data: {} };
            });
        })
        .then(function (res) {
            if (res.ok && res.data.success) {
                showExtensionToast(res.data.message || (res.data.is_active ? 'تم تفعيل الإضافة بنجاح.' : 'تم تعطيل الإضافة بنجاح.'), 'success');

                if (card) {
                    var newStatus = res.data.is_active ? 'active' : 'inactive';
                    card.setAttribute('data-status', newStatus);

                    // Update Status Badge
                    var badgeWrap = card.querySelector('.plugin-status-badge-wrap');
                    if (badgeWrap) {
                        var oldBadge = badgeWrap.querySelector('.extension-hub__status-badge');
                        if (oldBadge) {
                            if (res.data.is_active) {
                                oldBadge.className = 'extension-hub__status-badge extension-hub__status-badge--active';
                                oldBadge.innerHTML = '<i class="feather-check-circle"></i> ' + pluginLabels.active;
                            } else {
                                oldBadge.className = 'extension-hub__status-badge extension-hub__status-badge--inactive';
                                oldBadge.innerHTML = '<i class="feather-minus-circle"></i> ' + pluginLabels.inactive;
                            }
                        }
                    }

                    // Update Toggle Action Form
                    var actionSlot = card.querySelector('.plugin-action-toggle-slot');
                    if (actionSlot) {
                        if (res.data.is_active) {
                            actionSlot.innerHTML = `
                                <form action="${pluginLabels.deactivateRoute}" method="POST" class="plugin-ajax-toggle-form">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="slug" value="${slug}">
                                    <button type="submit" class="btn-extension-glass btn-extension-glass--warning btn-plugin-toggle" title="${pluginLabels.deactivate}">
                                        <i class="feather-pause"></i>
                                        <span>${pluginLabels.deactivate}</span>
                                    </button>
                                </form>
                            `;
                        } else {
                            actionSlot.innerHTML = `
                                <form action="${pluginLabels.activateRoute}" method="POST" class="plugin-ajax-toggle-form">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="slug" value="${slug}">
                                    <button type="submit" class="btn-extension-glass btn-extension-glass--success btn-plugin-toggle" title="${pluginLabels.activate}">
                                        <i class="feather-play"></i>
                                        <span>${pluginLabels.activate}</span>
                                    </button>
                                </form>
                            `;
                        }
                    }

                    // Update Delete Button data-is-active
                    var deleteBtn = card.querySelector('.btn-plugin-delete');
                    if (deleteBtn) {
                        deleteBtn.setAttribute('data-is-active', res.data.is_active ? '1' : '0');
                    }
                }

                updateCounts(res.data.active_count);
                applyFilter();
            } else {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                showExtensionToast(res.data.message || pluginLabels.errorOccurred, 'danger');
            }
        })
        .catch(function (err) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            showExtensionToast(err.message || pluginLabels.errorOccurred, 'danger');
        });
    });
});
</script>

<style>
    .plugin-filter-btn {
        border-radius: 999px;
        padding: 0.45rem 1rem;
        font-size: 0.82rem;
        font-weight: 700;
        border: 1px solid rgba(203, 213, 225, 0.85);
        background: #fff;
        color: #475569;
        transition: all 0.22s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .plugin-filter-btn:hover {
        background: rgba(99, 102, 241, 0.08);
        color: var(--extension-hub-accent);
        border-color: rgba(99, 102, 241, 0.35);
    }
    .plugin-filter-btn.active {
        background: linear-gradient(135deg, var(--extension-hub-accent) 0%, var(--extension-hub-accent-strong) 100%);
        color: #fff !important;
        border-color: transparent;
        box-shadow: 0 8px 18px rgba(var(--extension-hub-accent-rgb), 0.28);
    }
    .plugin-filter-btn.active i {
        color: #fff !important;
    }
    .plugin-filter-btn.active .badge {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #fff !important;
    }
    .plugin-card-col {
        transition: opacity 0.25s ease, transform 0.25s ease;
    }
</style>

@include('admin::admin.partials.extension_hub_styles')
@endpush
