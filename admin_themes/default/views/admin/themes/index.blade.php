@extends('admin::layouts.admin')

@section('title', __('messages.themes'))
@section('admin_shell_header_mode', 'hidden')

@php
    $themeCount = count($themes);
    $themeUpdateCount = count($updates);
    $activeTheme = collect($themes)->firstWhere('is_active', true);
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

            <ul class="nav extension-hub__tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link extension-hub__tab active" data-bs-toggle="tab" data-bs-target="#themes-installed-tab" type="button" role="tab" aria-selected="true">
                        <i class="feather-layout"></i>
                        <span>{{ __('messages.themes') }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link extension-hub__tab" data-bs-toggle="tab" data-bs-target="#themes-marketplace-tab" type="button" role="tab" aria-selected="false">
                        <i class="feather-shopping-bag"></i>
                        <span>{{ __('messages.marketplace') }}</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="themes-installed-tab" role="tabpanel">
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
                                            <button
                                                type="button"
                                                class="btn-extension-glass btn-extension-glass--primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#themeDetailsModal"
                                                data-slug="{{ $theme['slug'] }}"
                                                title="{{ __('messages.details') ?? 'Details' }}"
                                            >
                                                <i class="feather-info"></i>
                                                <span>{{ __('messages.details') ?? 'Details' }}</span>
                                            </button>

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

                <div class="tab-pane fade" id="themes-marketplace-tab" role="tabpanel">
                    @include('admin::admin.partials.extension_marketplace_panel', [
                        'marketplaceCatalog' => $marketplaceCatalog, 
                        'installedSlugs' => $installedSlugs,
                        'detailsModalId' => 'themeDetailsModal'
                    ])

                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('modals')
@php($showExtensionDeleteModal = false)
@include('admin::admin.partials.extension_hub_modal_scripts')

<div class="modal fade" id="themeDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 pb-0 pt-3 px-4 position-absolute top-0 end-0" style="z-index: 1051;">
                <button type="button" class="btn-close shadow-none bg-white rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="theme-details-thumbnail-wrapper" style="height: 300px; background: #eee; position: relative;">
                <img id="theme-modal-thumbnail" src="" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.85)); padding: 25px 30px; color: white;">
                    <h2 class="fw-bold mb-1" id="theme-modal-title"></h2>
                    <p class="mb-0 opacity-75 fs-14" id="theme-modal-slug-label"></p>
                </div>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0 flex-nowrap h-100">
                    <!-- Main Content (Tabs) -->
                    <div class="col-md-8 p-4 order-1 scrollable-content">
                        <ul class="nav nav-pills theme-modal-tabs mb-4 gap-2" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active fw-bold px-4 rounded-pill" data-bs-toggle="tab" data-bs-target="#theme-tab-description">{{ __('messages.description') ?? 'Description' }}</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link fw-bold px-4 rounded-pill" data-bs-toggle="tab" data-bs-target="#theme-tab-changelog" id="theme-btn-changelog">{{ __('messages.changelog') ?? 'Changelog' }}</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link fw-bold px-4 rounded-pill" data-bs-toggle="tab" data-bs-target="#theme-tab-screenshots" id="theme-btn-screenshots">{{ __('messages.screenshots') ?? 'Screenshots' }}</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="theme-modal-tab-content">
                            <div class="tab-pane fade show active" id="theme-tab-description">
                                <div class="extension-markdown-content p-2" id="theme-content-description"></div>
                            </div>
                            <div class="tab-pane fade" id="theme-tab-changelog">
                                <div class="extension-markdown-content p-2" id="theme-content-changelog"></div>
                            </div>
                            <div class="tab-pane fade" id="theme-tab-screenshots">
                                <div class="extension-markdown-content p-2" id="theme-content-screenshots"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar (Metadata) -->
                    <div class="col-md-4 p-4 order-2" id="theme-modal-sidebar" style="background: #f8f9fa; border-inline-start: 1px solid #dee2e6;">
                        <div class="d-grid gap-4">
                            <div>
                                <label class="text-muted small text-uppercase fw-extrabold d-block mb-1" style="letter-spacing: 0.5px; font-size: 11px;">{{ __('messages.version') ?? 'Version' }}</label>
                                <span class="fw-bold fs-15 text-dark" id="theme-modal-version"></span>
                            </div>
                            <div>
                                <label class="text-muted small text-uppercase fw-extrabold d-block mb-1" style="letter-spacing: 0.5px; font-size: 11px;">{{ __('messages.author') ?? 'Author' }}</label>
                                <span class="fw-bold fs-15 text-dark" id="theme-modal-author-name"></span>
                            </div>
                            <div>
                                <label class="text-muted small text-uppercase fw-extrabold d-block mb-1" style="letter-spacing: 0.5px; font-size: 11px;">{{ __('messages.requires_myads') ?? 'Required MyAds' }}</label>
                                <span class="fw-bold fs-15 text-primary" id="theme-modal-min-myads"></span>
                            </div>
                            <hr class="my-0 opacity-10">
                            <div id="theme-modal-adstn-wrap">
                                <a href="" target="_blank" class="btn btn-soft-primary btn-sm w-100 text-start d-flex align-items-center justify-content-between py-2 fw-bold" id="theme-modal-adstn-link">
                                    <span>{{ __('messages.adstn_page') ?? 'ADStn Page' }} »</span>
                                    <i class="feather-external-link"></i>
                                </a>
                            </div>
                            <div id="theme-modal-website-wrap">
                                <a href="" target="_blank" class="btn btn-soft-secondary btn-sm w-100 text-start d-flex align-items-center justify-content-between py-2 fw-bold" id="theme-modal-website-link">
                                    <span>{{ __('messages.theme_website') ?? 'Theme Website' }} »</span>
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
    var themeDetailsModal = document.getElementById('themeDetailsModal');
    if (themeDetailsModal) {
        themeDetailsModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var slug = button.getAttribute('data-slug');
            
            // Reset content
            document.getElementById('theme-modal-thumbnail').src = '';
            document.getElementById('theme-modal-title').textContent = '';
            document.getElementById('theme-modal-slug-label').textContent = '';
            document.getElementById('theme-content-description').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
            document.getElementById('theme-content-changelog').innerHTML = '';
            document.getElementById('theme-content-screenshots').innerHTML = '';

            document.getElementById('theme-modal-version').textContent = '';
            document.getElementById('theme-modal-author-name').textContent = '';
            document.getElementById('theme-modal-min-myads').textContent = '';
            document.getElementById('theme-modal-adstn-link').closest('div').classList.add('d-none');
            document.getElementById('theme-modal-website-link').closest('div').classList.add('d-none');
            
            // Hide tabs by default
            document.getElementById('theme-btn-changelog').closest('li').classList.add('d-none');
            document.getElementById('theme-btn-screenshots').closest('li').classList.add('d-none');
            
            // Activate first tab
            const firstTab = document.querySelector('.theme-modal-tabs button[data-bs-target="#theme-tab-description"]');
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

                document.getElementById('theme-modal-thumbnail').src = thumbnail || '{{ admin_asset("admin-duralux/images/logo-abbr.png") }}';
                document.getElementById('theme-modal-title').textContent = name;
                document.getElementById('theme-modal-slug-label').textContent = slug;
                document.getElementById('theme-modal-version').textContent = version;
                document.getElementById('theme-modal-author-name').textContent = author;
                document.getElementById('theme-modal-min-myads').textContent = minMyAds || '-';
                
                // Sidebar & Links Reset
                var adstnLink = document.getElementById('theme-modal-adstn-link');
                if (productUrl) {
                    adstnLink.closest('div').classList.remove('d-none');
                    adstnLink.href = productUrl;
                } else {
                    adstnLink.closest('div').classList.add('d-none');
                }
                document.getElementById('theme-modal-website-link').closest('div').classList.add('d-none');
                
                document.getElementById('theme-content-description').innerHTML = description ? DOMPurify.sanitize(marked.parse(description)) : '-';
                return;
            }

            // Fetch data (for local themes)
            fetch('{{ url("admin/themes/details") }}/' + slug)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    
                    document.getElementById('theme-modal-thumbnail').src = data.thumbnail || '{{ admin_asset("admin-duralux/images/logo-abbr.png") }}';
                    document.getElementById('theme-modal-title').textContent = data.name;
                    document.getElementById('theme-modal-slug-label').textContent = data.slug;
                    document.getElementById('theme-modal-version').textContent = data.version;
                    
                    // Author
                    var authorHtml = data.author;
                    if (data.author_url) {
                        authorHtml = '<a href="' + data.author_url + '" target="_blank" class="text-primary text-decoration-none">' + data.author + '</a>';
                    }
                    document.getElementById('theme-modal-author-name').innerHTML = authorHtml;
                    
                    document.getElementById('theme-modal-min-myads').textContent = data.min_myads || '-';
                    
                    // Links
                    var adstnLink = document.getElementById('theme-modal-adstn-link');
                    if (data.ADStn_url) {
                        adstnLink.closest('div').classList.remove('d-none');
                        adstnLink.href = 'https://www.adstn.ovh/store/' + data.ADStn_url;
                    } else {
                        adstnLink.closest('div').classList.add('d-none');
                    }
                    
                    var websiteLink = document.getElementById('theme-modal-website-link');
                    if (data.siteweb) {
                        websiteLink.closest('div').classList.remove('d-none');
                        websiteLink.href = data.siteweb;
                    } else {
                        websiteLink.closest('div').classList.add('d-none');
                    }
                    
                    // Markdown contents - using marked and DOMPurify for security
                    var readme = data.readme || data.description || '';
                    document.getElementById('theme-content-description').innerHTML = readme ? DOMPurify.sanitize(marked.parse(readme)) : '-';
                    
                    if (data.changelogs) {
                        document.getElementById('theme-btn-changelog').closest('li').classList.remove('d-none');
                        document.getElementById('theme-content-changelog').innerHTML = DOMPurify.sanitize(marked.parse(data.changelogs));
                    }
                    
                    if (data.screenshots) {
                        document.getElementById('theme-btn-screenshots').closest('li').classList.remove('d-none');
                        document.getElementById('theme-content-screenshots').innerHTML = DOMPurify.sanitize(marked.parse(data.screenshots));
                    }
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('theme-content-description').innerHTML = '<div class="alert alert-danger">Error loading theme details.</div>';
                });
        });
    }
});
</script>
@include('admin::admin.partials.extension_hub_styles')
@endpush
