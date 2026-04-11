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
                    @endif
                </div>

                <div class="tab-pane fade" id="plugins-marketplace-tab" role="tabpanel">
                    @include('admin::admin.partials.extension_marketplace_panel', ['marketplaceCatalog' => $marketplaceCatalog])
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
            
            // Hide tabs by default
            document.getElementById('plugin-btn-changelog').closest('li').classList.add('d-none');
            document.getElementById('plugin-btn-screenshots').closest('li').classList.add('d-none');
            
            // Activate first tab
            const firstTab = document.querySelector('.plugin-modal-tabs button[data-bs-target="#plugin-tab-description"]');
            if (firstTab) {
                const tab = new bootstrap.Tab(firstTab);
                tab.show();
            }
            
            // Fetch data
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
});
</script>
</style>
@include('admin::admin.partials.extension_hub_styles')
@endpush
