@php
    $marketplaceItems = $marketplaceCatalog['items'] ?? [];
    $marketplaceError = $marketplaceCatalog['error'] ?? null;
    $marketplaceBrowseUrl = $marketplaceCatalog['browse_url'] ?? 'https://www.adstn.ovh/store';
@endphp

<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
    <div>
        <h2 class="extension-hub__section-title">{{ __('messages.marketplace') }}</h2>
        <p class="extension-hub__section-subtitle">{{ __('messages.marketplace_extensions_desc') }}</p>
    </div>
    <a href="{{ $marketplaceBrowseUrl }}" target="_blank" rel="noopener noreferrer" class="btn-extension-glass btn-extension-glass--primary">
        <i class="feather-external-link"></i>
        <span>{{ __('messages.browse_store') }}</span>
    </a>
</div>

@if($marketplaceError)
    <div class="alert alert-warning extension-hub__market-alert mb-4" role="alert">
        <div class="d-flex gap-3 align-items-start">
            <i class="feather-alert-triangle mt-1"></i>
            <div>
                <div class="fw-bold">{{ __('messages.marketplace_unavailable') }}</div>
                <div class="small">{{ __('messages.marketplace_unavailable_help') }}</div>
            </div>
        </div>
    </div>
@endif

@if(!empty($marketplaceItems))
    <div class="row g-4">
        @foreach($marketplaceItems as $item)
            @php
                $marketImage = trim((string) ($item['image_url'] ?? ''));
                $marketName = trim((string) ($item['name'] ?? ''));
                $marketSlug = trim((string) ($item['slug'] ?? ''));
                $marketDescription = trim((string) ($item['description'] ?? ''));
                $marketInitial = strtoupper(substr($marketName !== '' ? $marketName : $marketSlug, 0, 1));
            @endphp
            <div class="col-12 col-xl-6">
                <article class="extension-hub__list-card extension-hub__market-card d-flex flex-column">
                    <div class="extension-hub__market-visual">
                        @if($marketImage !== '')
                            <img src="{{ $marketImage }}" alt="{{ $marketName !== '' ? $marketName : $marketSlug }}" loading="lazy">
                        @else
                            <div class="extension-hub__market-fallback">{{ $marketInitial !== '' ? $marketInitial : 'M' }}</div>
                        @endif
                    </div>

                    <div class="extension-hub__badge-stack mb-2">
                        @if(!empty($item['category']))
                            <span class="extension-hub__status-badge extension-hub__status-badge--inactive">
                                <i class="feather-tag"></i>
                                {{ __('messages.' . ($item['category'] === 'templates' ? 'themes' : $item['category'])) }}
                            </span>
                        @endif
                    </div>

                    <h3 class="extension-hub__card-title">{{ $marketName !== '' ? $marketName : $marketSlug }}</h3>
                    <div class="extension-hub__slug">{{ $marketSlug }}</div>
                    <p class="extension-hub__card-description">{{ $marketDescription !== '' ? $marketDescription : '-' }}</p>

                    <div class="extension-hub__token-row">
                        @if(!empty($item['version']))
                            <span class="extension-hub__token">
                                <i class="feather-tag"></i>
                                {{ __('messages.version') }}: {{ $item['version'] }}
                            </span>
                        @endif

                        @if(!empty($item['author']))
                            <span class="extension-hub__token">
                                <i class="feather-user"></i>
                                {{ __('messages.author') }}: {{ $item['author'] }}
                            </span>
                        @endif

                        @if(!empty($item['min_myads']))
                            <span class="extension-hub__token">
                                <i class="feather-shield"></i>
                                {{ __('messages.requires_myads') }}: {{ $item['min_myads'] }}
                            </span>
                        @endif
                    </div>

                    <div class="extension-hub__actions">
                        <a
                            href="{{ $item['product_url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn-extension-glass btn-extension-glass--primary"
                        >
                            <i class="feather-external-link"></i>
                            <span>{{ __('messages.open_in_store') }}</span>
                        </a>
                    </div>
                </article>
            </div>
        @endforeach
    </div>
@else
    <div class="extension-hub__empty">
        <div class="extension-hub__empty-icon">
            <i class="feather-shopping-bag"></i>
        </div>
        <h3 class="extension-hub__section-title mb-2">{{ __('messages.no_marketplace_items') }}</h3>
        <p class="extension-hub__section-subtitle mb-4">{{ __('messages.marketplace_extensions_desc') }}</p>
        <a href="{{ $marketplaceBrowseUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary px-4 py-2 fw-bold">
            <i class="feather-external-link me-2"></i>{{ __('messages.browse_store') }}
        </a>
    </div>
@endif
