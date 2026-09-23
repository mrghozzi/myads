@extends('admin::layouts.admin')

@section('title', __('messages.smart_admin_overview'))

@section('content')
<div class="admin-page">
    <!-- Hero Header -->
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}"><i class="feather-home me-1"></i>{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.ads') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">
                <span class="badge bg-soft-primary text-primary px-2 py-1"><i class="feather-grid me-1"></i>{{ __('messages.admin_panel') ?? 'Admin Panel' }}</span>
            </div>
            <h1 class="admin-hero__title d-flex align-items-center gap-2">
                <i class="feather-layers text-primary"></i>
                {{ __('messages.smart_admin_overview') }}
            </h1>
            <p class="admin-hero__copy">{{ __('messages.smart_admin_recent_inventory') }}</p>

            <!-- 4 Global Inventory Stat Cards -->
            <div class="admin-stat-strip mt-3" id="hubStatsContainer">
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-image me-1 text-primary"></i>{{ __('messages.bannads') }}</span>
                    <span class="admin-stat-value text-primary">{{ number_format($summary['banners'] ?? 0) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-file-text me-1 text-info"></i>{{ __('messages.textads') }}</span>
                    <span class="admin-stat-value text-info">{{ number_format($summary['links'] ?? 0) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-cpu me-1 text-warning"></i>{{ __('messages.smart_ads') }}</span>
                    <span class="admin-stat-value text-warning">{{ number_format($summary['smart_ads'] ?? 0) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-layout me-1 text-success"></i>{{ __('messages.custom_ads') }}</span>
                    <span class="admin-stat-value text-success">{{ number_format($summary['custom_ads'] ?? 0) }}</span>
                </div>
            </div>
        </div>

        <!-- Action Tiles to Sections -->
        <div class="admin-hero__actions">
            <div class="d-flex flex-wrap gap-2 w-100">
                <a href="{{ route('admin.banners') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2 flex-grow-1">
                    <i class="feather-image"></i>
                    <span>{{ __('messages.bannads') }}</span>
                </a>
                <a href="{{ route('admin.links') }}" class="btn btn-outline-info d-inline-flex align-items-center gap-2 flex-grow-1">
                    <i class="feather-file-text"></i>
                    <span>{{ __('messages.textads') }}</span>
                </a>
                <a href="{{ route('admin.visits') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 flex-grow-1">
                    <i class="feather-activity"></i>
                    <span>{{ __('messages.exvisit') }}</span>
                </a>
                <a href="{{ route('admin.smart_ads') }}" class="btn btn-outline-warning d-inline-flex align-items-center gap-2 flex-grow-1">
                    <i class="feather-cpu"></i>
                    <span>{{ __('messages.smart_ads') }}</span>
                </a>
                <a href="{{ route('admin.ads.settings') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                    <i class="feather-settings"></i>
                    <span>{{ __('messages.settings') ?? 'Settings' }}</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Main Inventory Panel -->
    <section class="admin-panel shadow-sm">
        <div class="admin-panel__header p-3 border-bottom bg-transparent">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 w-100">
                <!-- Live Search Box -->
                <div class="position-relative flex-grow-1" style="max-width: 380px; min-width: 240px;">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted">
                            <i class="feather-search"></i>
                        </span>
                        <input 
                            type="text" 
                            id="hubSearchInput" 
                            class="form-control border-start-0 ps-0" 
                            placeholder="{{ __('messages.smart_admin_search_ads') }}" 
                            value="{{ $search }}"
                            autocomplete="off"
                        >
                        <button type="button" class="btn btn-transparent text-muted d-none" id="clearHubSearchBtn" title="Clear">
                            <i class="feather-x"></i>
                        </button>
                    </div>
                </div>

                <!-- Type Selector Pills -->
                <div class="d-flex align-items-center gap-1 overflow-auto ms-auto" id="typePillsContainer">
                    <button type="button" class="btn btn-sm type-pill {{ ($type ?? 'all') === 'all' ? 'btn-primary' : 'btn-light' }}" data-type="all">
                        {{ __('messages.all') ?? 'All' }}
                    </button>
                    <button type="button" class="btn btn-sm type-pill {{ ($type ?? '') === 'banner' ? 'btn-primary' : 'btn-light' }}" data-type="banner">
                        {{ __('messages.bannads') }}
                    </button>
                    <button type="button" class="btn btn-sm type-pill {{ ($type ?? '') === 'link' ? 'btn-primary' : 'btn-light' }}" data-type="link">
                        {{ __('messages.textads') }}
                    </button>
                    <button type="button" class="btn btn-sm type-pill {{ ($type ?? '') === 'smart' ? 'btn-primary' : 'btn-light' }}" data-type="smart">
                        {{ __('messages.smart_ads') }}
                    </button>
                    <button type="button" class="btn btn-sm type-pill {{ ($type ?? '') === 'custom' ? 'btn-primary' : 'btn-light' }}" data-type="custom">
                        {{ __('messages.custom_ads') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="admin-panel__body p-0" id="hubTableContainer">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 admin-table admin-table-cardify">
                    <thead>
                        <tr>
                            <th>{{ __('messages.type') }}</th>
                            <th>ID</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.smart_admin_owner') }}</th>
                            <th>{{ __('messages.views') }}</th>
                            <th>{{ __('messages.clicks') }}</th>
                            <th>{{ __('messages.smart_admin_badge') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody id="hubTableBody">
                        @forelse($items as $item)
                            <tr>
                                <td data-label="{{ __('messages.type') }}">
                                    @if($item->type === 'banner')
                                        <span class="badge bg-soft-primary text-primary text-uppercase">{{ __('messages.bannads') }}</span>
                                    @elseif($item->type === 'link')
                                        <span class="badge bg-soft-info text-info text-uppercase">{{ __('messages.textads') }}</span>
                                    @elseif($item->type === 'smart')
                                        <span class="badge bg-soft-warning text-warning text-uppercase">{{ __('messages.smart_ad') }}</span>
                                    @else
                                        <span class="badge bg-soft-success text-success text-uppercase">{{ __('messages.custom_ads') }}</span>
                                    @endif
                                </td>
                                <td data-label="ID">
                                    <span class="badge bg-soft-secondary text-dark fw-bold">#{{ $item->id }}</span>
                                </td>
                                <td data-label="{{ __('messages.name') }}" style="max-width: 260px;">
                                    <div class="fw-bold text-dark text-truncate" title="{{ $item->name }}">{{ $item->name }}</div>
                                </td>
                                <td data-label="{{ __('messages.smart_admin_owner') }}">
                                    <span class="fw-semibold text-dark small">{{ $item->owner ?? __('messages.unknown') }}</span>
                                </td>
                                <td data-label="{{ __('messages.views') }}">
                                    <span class="badge bg-soft-warning text-warning">
                                        <i class="feather-eye me-1"></i>{{ number_format($item->metric_primary ?? 0) }}
                                    </span>
                                </td>
                                <td data-label="{{ __('messages.clicks') }}">
                                    @if($item->metric_secondary !== null)
                                        <span class="badge bg-soft-primary text-primary">
                                            <i class="feather-mouse-pointer me-1"></i>{{ number_format($item->metric_secondary) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('messages.smart_admin_badge') }}">
                                    <span class="badge bg-light text-dark border">{{ $item->badge }}</span>
                                </td>
                                <td data-label="{{ __('messages.status') }}">
                                    @if((int) $item->status === 1)
                                        <span class="badge bg-soft-success text-success">{{ __('messages.active') }}</span>
                                    @else
                                        <span class="badge bg-soft-warning text-warning">{{ __('messages.paused') }}</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('messages.actions') }}" class="text-end">
                                    <a href="{{ $item->edit_url }}" class="btn btn-sm btn-light d-inline-flex align-items-center gap-1">
                                        <i class="feather-external-link"></i>
                                        <span>{{ __('messages.smart_admin_open') }}</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="avatar-text avatar-xl bg-soft-primary text-primary rounded-circle mx-auto mb-3">
                                        <i class="feather-layers"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">{{ __('messages.smart_admin_no_ads') }}</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('hubSearchInput');
    const clearBtn = document.getElementById('clearHubSearchBtn');
    const typePills = document.querySelectorAll('.type-pill');
    const tableContainer = document.getElementById('hubTableContainer');
    const tableBody = document.getElementById('hubTableBody');
    const currentBaseUrl = window.location.pathname.replace(/\/+$/, '');

    let activeType = '{{ $type ?? "all" }}';
    let searchTimeout = null;

    function fetchHubData() {
        tableContainer.style.opacity = '0.5';
        tableContainer.style.pointerEvents = 'none';

        const url = new URL(currentBaseUrl, window.location.origin);
        if (searchInput.value.trim()) url.searchParams.set('search', searchInput.value.trim());
        if (activeType !== 'all') url.searchParams.set('type', activeType);

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            tableContainer.style.opacity = '1';
            tableContainer.style.pointerEvents = 'auto';

            if (data.items) {
                renderItems(data.items);
            }
            history.pushState(null, '', url.toString());
        })
        .catch(err => {
            console.error('Hub fetch error:', err);
            tableContainer.style.opacity = '1';
            tableContainer.style.pointerEvents = 'auto';
        });
    }

    function renderItems(items) {
        if (!items || items.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <div class="avatar-text avatar-xl bg-soft-primary text-primary rounded-circle mx-auto mb-3">
                            <i class="feather-layers"></i>
                        </div>
                        <h6 class="fw-bold mb-1">${@json(__('messages.smart_admin_no_ads'))}</h6>
                    </td>
                </tr>
            `;
            return;
        }

        tableBody.innerHTML = items.map(item => {
            let typeBadgeClass = 'bg-soft-primary text-primary';
            if (item.type === 'link') typeBadgeClass = 'bg-soft-info text-info';
            else if (item.type === 'smart') typeBadgeClass = 'bg-soft-warning text-warning';
            else if (item.type === 'custom') typeBadgeClass = 'bg-soft-success text-success';

            const statusBadge = Number(item.status) === 1
                ? `<span class="badge bg-soft-success text-success">${@json(__('messages.active'))}</span>`
                : `<span class="badge bg-soft-warning text-warning">${@json(__('messages.paused'))}</span>`;

            const secondaryMetric = item.metric_secondary !== null
                ? `<span class="badge bg-soft-primary text-primary"><i class="feather-mouse-pointer me-1"></i>${Number(item.metric_secondary).toLocaleString()}</span>`
                : `<span class="text-muted">-</span>`;

            return `
                <tr>
                    <td data-label="${@json(__('messages.type'))}">
                        <span class="badge ${typeBadgeClass} text-uppercase">${item.type}</span>
                    </td>
                    <td data-label="ID">
                        <span class="badge bg-soft-secondary text-dark fw-bold">#${item.id}</span>
                    </td>
                    <td data-label="${@json(__('messages.name'))}" style="max-width: 260px;">
                        <div class="fw-bold text-dark text-truncate" title="${item.name}">${item.name}</div>
                    </td>
                    <td data-label="${@json(__('messages.smart_admin_owner'))}">
                        <span class="fw-semibold text-dark small">${item.owner || @json(__('messages.unknown'))}</span>
                    </td>
                    <td data-label="${@json(__('messages.views'))}">
                        <span class="badge bg-soft-warning text-warning">
                            <i class="feather-eye me-1"></i>${Number(item.metric_primary || 0).toLocaleString()}
                        </span>
                    </td>
                    <td data-label="${@json(__('messages.clicks'))}">
                        ${secondaryMetric}
                    </td>
                    <td data-label="${@json(__('messages.smart_admin_badge'))}">
                        <span class="badge bg-light text-dark border">${item.badge || ''}</span>
                    </td>
                    <td data-label="${@json(__('messages.status'))}">
                        ${statusBadge}
                    </td>
                    <td data-label="${@json(__('messages.actions'))}" class="text-end">
                        <a href="${item.edit_url}" class="btn btn-sm btn-light d-inline-flex align-items-center gap-1">
                            <i class="feather-external-link"></i>
                            <span>${@json(__('messages.smart_admin_open'))}</span>
                        </a>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // Debounced search
    searchInput.addEventListener('input', function() {
        if (this.value.trim().length > 0) {
            clearBtn.classList.remove('d-none');
        } else {
            clearBtn.classList.add('d-none');
        }
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchHubData(), 350);
    });

    clearBtn.addEventListener('click', function() {
        searchInput.value = '';
        this.classList.add('d-none');
        fetchHubData();
    });

    // Type pills
    typePills.forEach(pill => {
        pill.addEventListener('click', function() {
            typePills.forEach(p => p.className = 'btn btn-sm type-pill btn-light');
            this.className = 'btn btn-sm type-pill btn-primary';
            activeType = this.dataset.type;
            fetchHubData();
        });
    });
});
</script>
@endpush
