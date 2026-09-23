@extends('admin::layouts.admin')

@section('title', __('messages.bannads'))

@section('content')
<div class="admin-page">
    <!-- Hero Section -->
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}"><i class="feather-home me-1"></i>{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.ads') }}">{{ __('messages.ads') }}</a></li>
                <li>{{ __('messages.bannads') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">
                <span class="badge bg-soft-primary text-primary px-2 py-1"><i class="feather-image me-1"></i>{{ __('messages.bannads') }}</span>
            </div>
            <h1 class="admin-hero__title d-flex align-items-center gap-2">
                <i class="feather-image text-primary"></i>
                {{ __('messages.bannads') }}
            </h1>
            <p class="admin-hero__copy">{{ __('messages.smart_admin_recent_inventory') }} &bull; {{ __('messages.codes') }} / {{ __('messages.Stats') }}</p>

            <!-- 5 KPI Stat Strip -->
            <div class="admin-stat-strip mt-3" id="heroStatsContainer">
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-image me-1 text-primary"></i>{{ __('messages.total_banners') }}</span>
                    <span class="admin-stat-value text-primary" id="kpiTotal">{{ number_format($stats['total'] ?? 0) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-check-circle me-1 text-success"></i>{{ __('messages.active_banners') }}</span>
                    <span class="admin-stat-value text-success" id="kpiActive">{{ number_format($stats['active'] ?? 0) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-pause-circle me-1 text-warning"></i>{{ __('messages.paused_banners') }}</span>
                    <span class="admin-stat-value text-warning" id="kpiPaused">{{ number_format($stats['paused'] ?? 0) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-eye me-1 text-info"></i>{{ __('messages.total_impressions') }}</span>
                    <span class="admin-stat-value text-info" id="kpiViews">{{ number_format($stats['total_views'] ?? 0) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-mouse-pointer me-1 text-danger"></i>{{ __('messages.total_clicks') }}</span>
                    <span class="admin-stat-value text-danger" id="kpiClicks">{{ number_format($stats['total_clicks'] ?? 0) }}</span>
                </div>
            </div>
        </div>

        <!-- Hero Actions -->
        <div class="admin-hero__actions">
            <div class="d-flex align-items-center gap-2 w-100 flex-wrap">
                <a href="{{ route('ads.banners.code') }}" class="btn btn-light admin-icon-btn" data-bs-toggle="tooltip" title="{{ __('messages.codes') }}">
                    <i class="feather-code"></i>
                </a>
                <a href="{{ route('admin.stats', ['ty' => 'banner', 'st' => 'vu']) }}" class="btn btn-light admin-icon-btn" data-bs-toggle="tooltip" title="{{ __('messages.Stats') }}">
                    <i class="feather-bar-chart-2"></i>
                </a>
                <button type="button" class="btn btn-light admin-icon-btn" id="refreshTableBtn" title="{{ __('messages.refresh') ?? 'Refresh' }}">
                    <i class="feather-refresh-cw"></i>
                </button>
                <a href="{{ route('ads.promote', ['p' => 'banners']) }}" class="btn btn-primary d-inline-flex align-items-center gap-2 flex-grow-1">
                    <i class="feather-plus"></i>
                    <span>{{ __('messages.add') }}</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Main Panel Container -->
    <section class="admin-panel shadow-sm">
        <!-- Panel Filter Toolbar -->
        <div class="admin-panel__header p-3 border-bottom bg-transparent">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 w-100">
                <!-- Search Box -->
                <div class="position-relative flex-grow-1" style="max-width: 380px; min-width: 240px;">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted">
                            <i class="feather-search"></i>
                        </span>
                        <input 
                            type="text" 
                            id="bannerSearchInput" 
                            class="form-control border-start-0 ps-0" 
                            placeholder="{{ __('messages.search_banners_placeholder') }}" 
                            value="{{ request('search', $filterState['keyword'] ?? '') }}"
                            autocomplete="off"
                        >
                        <button type="button" class="btn btn-transparent text-muted d-none" id="clearSearchBtn" title="Clear">
                            <i class="feather-x"></i>
                        </button>
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="d-flex flex-wrap align-items-center gap-2 ms-auto">
                    <!-- Status Filter -->
                    <select id="statusFilter" class="form-select form-select-sm" style="width: auto; min-width: 130px;">
                        <option value="">{{ __('messages.status') }}: {{ __('messages.all') ?? 'All' }}</option>
                        <option value="1" {{ request('status', $filterState['status'] ?? '') === '1' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                        <option value="2" {{ request('status', $filterState['status'] ?? '') === '2' ? 'selected' : '' }}>{{ __('messages.paused') }}</option>
                    </select>

                    <!-- Size Filter -->
                    <select id="sizeFilter" class="form-select form-select-sm" style="width: auto; min-width: 140px;">
                        <option value="">{{ __('messages.size') }}: {{ __('messages.all_sizes') }}</option>
                        @foreach(\App\Support\BannerSizeCatalog::ordered() as $size)
                            <option value="{{ $size['value'] }}" {{ request('size', $filterState['size'] ?? '') === $size['value'] ? 'selected' : '' }}>
                                {{ $size['label'] }} ({{ $size['value'] }})
                            </option>
                        @endforeach
                    </select>

                    <!-- Sort Filter -->
                    <select id="sortFilter" class="form-select form-select-sm" style="width: auto; min-width: 140px;">
                        <option value="newest">{{ __('messages.sort_newest') ?? 'Newest' }}</option>
                        <option value="views">{{ __('messages.sort_views') ?? 'Most Views' }}</option>
                        <option value="clicks">{{ __('messages.sort_clicks') ?? 'Most Clicks' }}</option>
                        <option value="oldest">{{ __('messages.sort_oldest') ?? 'Oldest' }}</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="admin-panel__body p-0 position-relative" id="bannersTableContainer">
            @include('admin::admin.partials.banners_table', ['banners' => $banners])
        </div>
    </section>
</div>

<!-- Floating Bulk Actions Bar -->
<div class="admin-floating-bulk-bar shadow-lg d-none" id="floatingBulkBar">
    <div class="d-flex align-items-center gap-3">
        <div class="bulk-count-badge">
            <span class="badge bg-primary rounded-pill px-3 py-2 fs-6 fw-bold" id="bulkSelectedCount">0</span>
            <span class="small text-muted ms-1">{{ __('messages.selected_items') }}</span>
        </div>
        <div class="vr"></div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1" id="bulkActivateBtn">
                <i class="feather-check"></i>
                <span>{{ __('messages.active') }}</span>
            </button>
            <button type="button" class="btn btn-sm btn-warning d-inline-flex align-items-center gap-1" id="bulkPauseBtn">
                <i class="feather-pause"></i>
                <span>{{ __('messages.paused') }}</span>
            </button>
            <button type="button" class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1" id="bulkDeleteTriggerBtn">
                <i class="feather-trash-2"></i>
                <span>{{ __('messages.delete') }}</span>
            </button>
        </div>
    </div>
</div>

<!-- Single Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                    <i class="feather-alert-triangle"></i>
                    {{ __('messages.single_delete_confirm_title') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mx-auto mb-3">
                    <i class="feather-trash-2 fs-2"></i>
                </div>
                <h5 class="fw-bold mb-2" id="deleteItemTitle">...</h5>
                <p class="text-muted mb-0">{{ __('messages.confirm_delete_text') }}</p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteSubmitBtn">
                    <span class="spinner-border spinner-border-sm me-1 d-none" id="deleteSpinner"></span>
                    {{ __('messages.delete') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Delete Confirmation Modal -->
<div class="modal fade" id="bulkDeleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2">
                    <i class="feather-alert-triangle"></i>
                    {{ __('messages.bulk_delete_confirm_title') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mx-auto mb-3">
                    <i class="feather-trash-2 fs-2"></i>
                </div>
                <h5 class="fw-bold mb-2"><span id="bulkDeleteModalCount">0</span> {{ __('messages.selected_items') }}</h5>
                <p class="text-muted mb-0">{{ __('messages.confirm_delete_text') }}</p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <button type="button" class="btn btn-danger px-4" id="confirmBulkDeleteSubmitBtn">
                    <span class="spinner-border spinner-border-sm me-1 d-none" id="bulkDeleteSpinner"></span>
                    {{ __('messages.delete') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Banner Preview Lightbox Modal -->
<div class="modal fade" id="bannerPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="previewModalTitle">{{ __('messages.preview') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="p-3 bg-light rounded-3 d-inline-block mw-100">
                    <img id="previewModalImageA" src="" alt="Banner A" class="img-fluid rounded mb-2 shadow-sm" style="max-height: 420px;">
                    <div class="small fw-semibold text-muted" id="previewLabelA">Version A</div>
                </div>
                <div class="p-3 bg-light rounded-3 d-none mw-100 mt-3" id="previewContainerB">
                    <img id="previewModalImageB" src="" alt="Banner B" class="img-fluid rounded mb-2 shadow-sm" style="max-height: 420px;">
                    <div class="small fw-semibold text-muted">Version B (A/B Test)</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    // 1. Dynamic base URL resolution for XAMPP subdirectories
    const currentBaseUrl = window.location.pathname.replace(/\/+$/, '');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // 2. Centralized bilingual i18n dictionary
    const i18n = {
        actionSuccess: @json(__('messages.action_completed_successfully')),
        statusToggled: @json(__('messages.status_toggled_successfully')),
        bannerDeleted: @json(__('messages.banner_deleted_successfully')),
        bulkSuccess: @json(__('messages.bulk_operation_success')),
        networkError: @json(__('messages.network_error') ?? 'Network error. Please try again.'),
        active: @json(__('messages.active')),
        paused: @json(__('messages.paused')),
        operationFailed: @json(__('messages.operation_failed') ?? 'Operation failed.')
    };

    // DOM Elements
    const searchInput = document.getElementById('bannerSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const statusFilter = document.getElementById('statusFilter');
    const sizeFilter = document.getElementById('sizeFilter');
    const sortFilter = document.getElementById('sortFilter');
    const refreshTableBtn = document.getElementById('refreshTableBtn');
    const tableContainer = document.getElementById('bannersTableContainer');
    const floatingBulkBar = document.getElementById('floatingBulkBar');
    const bulkSelectedCount = document.getElementById('bulkSelectedCount');
    const bulkActivateBtn = document.getElementById('bulkActivateBtn');
    const bulkPauseBtn = document.getElementById('bulkPauseBtn');
    const bulkDeleteTriggerBtn = document.getElementById('bulkDeleteTriggerBtn');
    const confirmDeleteSubmitBtn = document.getElementById('confirmDeleteSubmitBtn');
    const confirmBulkDeleteSubmitBtn = document.getElementById('confirmBulkDeleteSubmitBtn');

    let deleteTargetId = null;
    let searchTimeout = null;

    // Toast helper
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed shadow-lg`;
        toast.style.top = '24px';
        toast.style.right = '24px';
        toast.style.zIndex = '99999';
        toast.style.minWidth = '300px';
        toast.role = 'alert';
        toast.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <i class="feather-${type === 'success' ? 'check-circle' : 'alert-circle'} fs-5"></i>
                <div class="fw-semibold">${message}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // Lightbox helper (global for inline onclick)
    window.previewBannerImage = function(srcA, name, srcB) {
        document.getElementById('previewModalTitle').textContent = name || 'Banner Preview';
        const imgA = document.getElementById('previewModalImageA');
        imgA.src = srcA;
        const containerB = document.getElementById('previewContainerB');
        const imgB = document.getElementById('previewModalImageB');
        if (srcB && srcB.trim().length > 0) {
            imgB.src = srcB;
            containerB.classList.remove('d-none');
        } else {
            containerB.classList.add('d-none');
        }
        const modal = new bootstrap.Modal(document.getElementById('bannerPreviewModal'));
        modal.show();
    };

    // Load table data via Ajax
    function fetchTableData(pageUrl = null) {
        tableContainer.style.opacity = '0.5';
        tableContainer.style.pointerEvents = 'none';

        const url = new URL(pageUrl || currentBaseUrl, window.location.origin);
        if (!pageUrl) {
            if (searchInput.value.trim()) url.searchParams.set('search', searchInput.value.trim());
            if (statusFilter.value) url.searchParams.set('status', statusFilter.value);
            if (sizeFilter.value) url.searchParams.set('size', sizeFilter.value);
            if (sortFilter.value) url.searchParams.set('sort', sortFilter.value);
        }

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(data => {
            tableContainer.style.opacity = '1';
            tableContainer.style.pointerEvents = 'auto';

            if (data.html) {
                tableContainer.innerHTML = data.html;
            }

            if (data.stats) {
                document.getElementById('kpiTotal').textContent = Number(data.stats.total || 0).toLocaleString();
                document.getElementById('kpiActive').textContent = Number(data.stats.active || 0).toLocaleString();
                document.getElementById('kpiPaused').textContent = Number(data.stats.paused || 0).toLocaleString();
                document.getElementById('kpiViews').textContent = Number(data.stats.total_views || 0).toLocaleString();
                document.getElementById('kpiClicks').textContent = Number(data.stats.total_clicks || 0).toLocaleString();
            }

            updateBulkBar();
            history.pushState(null, '', url.toString());
        })
        .catch(err => {
            console.error('Fetch table error:', err);
            tableContainer.style.opacity = '1';
            tableContainer.style.pointerEvents = 'auto';
            showToast(i18n.networkError, 'error');
        });
    }

    // Debounced search
    searchInput.addEventListener('input', function() {
        if (this.value.trim().length > 0) {
            clearSearchBtn.classList.remove('d-none');
        } else {
            clearSearchBtn.classList.add('d-none');
        }
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchTableData(), 350);
    });

    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        this.classList.add('d-none');
        fetchTableData();
    });

    statusFilter.addEventListener('change', () => fetchTableData());
    sizeFilter.addEventListener('change', () => fetchTableData());
    sortFilter.addEventListener('change', () => fetchTableData());
    refreshTableBtn.addEventListener('click', () => fetchTableData());

    // Pagination link clicks (delegate)
    tableContainer.addEventListener('click', function(e) {
        const link = e.target.closest('.pagination a');
        if (link) {
            e.preventDefault();
            fetchTableData(link.getAttribute('href'));
        }
    });

    // Checkbox and Bulk Bar Management
    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
    }

    function updateBulkBar() {
        const selected = getSelectedIds();
        if (selected.length > 0) {
            floatingBulkBar.classList.remove('d-none');
            bulkSelectedCount.textContent = selected.length;
        } else {
            floatingBulkBar.classList.add('d-none');
        }

        const checkAll = document.getElementById('checkAll');
        const allCheckboxes = document.querySelectorAll('.row-checkbox');
        if (checkAll) {
            checkAll.checked = allCheckboxes.length > 0 && selected.length === allCheckboxes.length;
        }
    }

    tableContainer.addEventListener('change', function(e) {
        if (e.target.id === 'checkAll') {
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = e.target.checked);
            updateBulkBar();
        } else if (e.target.classList.contains('row-checkbox')) {
            updateBulkBar();
        }
    });

    // Inline Status Toggle
    tableContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('status-toggle-btn')) {
            const toggle = e.target;
            const bannerId = toggle.dataset.id;
            const newStatus = toggle.checked ? 1 : 2;
            const label = document.querySelector(`.status-label-${bannerId}`);

            toggle.disabled = true;

            fetch(`${currentBaseUrl}/${bannerId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    toggle_status: 1,
                    statu: newStatus
                })
            })
            .then(res => res.json())
            .then(data => {
                toggle.disabled = false;
                if (data.success) {
                    showToast(data.message || i18n.statusToggled, 'success');
                    if (label) {
                        label.textContent = newStatus === 1 ? i18n.active : i18n.paused;
                        label.className = `form-check-label ms-1 small fw-semibold status-label-${bannerId} ${newStatus === 1 ? 'text-success' : 'text-muted'}`;
                    }
                } else {
                    toggle.checked = !toggle.checked;
                    showToast(data.message || i18n.operationFailed, 'error');
                }
            })
            .catch(err => {
                console.error('Status toggle error:', err);
                toggle.disabled = false;
                toggle.checked = !toggle.checked;
                showToast(i18n.networkError, 'error');
            });
        }
    });

    // Single Delete Trigger
    tableContainer.addEventListener('click', function(e) {
        const delBtn = e.target.closest('.delete-banner-btn');
        if (delBtn) {
            deleteTargetId = delBtn.dataset.id;
            document.getElementById('deleteItemTitle').textContent = delBtn.dataset.name || `#${deleteTargetId}`;
            const modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            modal.show();
        }
    });

    // Single Delete Submit
    confirmDeleteSubmitBtn.addEventListener('click', function() {
        if (!deleteTargetId) return;

        const spinner = document.getElementById('deleteSpinner');
        spinner.classList.remove('d-none');
        this.disabled = true;

        fetch(`${currentBaseUrl}/${deleteTargetId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            spinner.classList.add('d-none');
            confirmDeleteSubmitBtn.disabled = false;
            bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal')).hide();

            if (data.success) {
                showToast(data.message || i18n.bannerDeleted, 'success');
                const row = document.getElementById(`bannerRow${deleteTargetId}`);
                if (row) row.remove();
                fetchTableData();
            } else {
                showToast(data.message || i18n.operationFailed, 'error');
            }
        })
        .catch(err => {
            console.error('Delete error:', err);
            spinner.classList.add('d-none');
            confirmDeleteSubmitBtn.disabled = false;
            showToast(i18n.networkError, 'error');
        });
    });

    // Bulk Actions Trigger
    function handleBulkAction(action) {
        const ids = getSelectedIds();
        if (ids.length === 0) return;

        floatingBulkBar.style.opacity = '0.5';
        floatingBulkBar.style.pointerEvents = 'none';

        fetch(`${currentBaseUrl}/bulk/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ids: ids,
                action: action
            })
        })
        .then(res => res.json())
        .then(data => {
            floatingBulkBar.style.opacity = '1';
            floatingBulkBar.style.pointerEvents = 'auto';

            if (data.success) {
                showToast(data.message || i18n.bulkSuccess, 'success');
                fetchTableData();
            } else {
                showToast(data.message || i18n.operationFailed, 'error');
            }
        })
        .catch(err => {
            console.error('Bulk action error:', err);
            floatingBulkBar.style.opacity = '1';
            floatingBulkBar.style.pointerEvents = 'auto';
            showToast(i18n.networkError, 'error');
        });
    }

    bulkActivateBtn.addEventListener('click', () => handleBulkAction('activate'));
    bulkPauseBtn.addEventListener('click', () => handleBulkAction('pause'));

    bulkDeleteTriggerBtn.addEventListener('click', function() {
        const ids = getSelectedIds();
        if (ids.length === 0) return;
        document.getElementById('bulkDeleteModalCount').textContent = ids.length;
        const modal = new bootstrap.Modal(document.getElementById('bulkDeleteConfirmModal'));
        modal.show();
    });

    confirmBulkDeleteSubmitBtn.addEventListener('click', function() {
        const spinner = document.getElementById('bulkDeleteSpinner');
        spinner.classList.remove('d-none');
        this.disabled = true;

        const ids = getSelectedIds();
        fetch(`${currentBaseUrl}/bulk/delete`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ids: ids,
                action: 'delete'
            })
        })
        .then(res => res.json())
        .then(data => {
            spinner.classList.add('d-none');
            confirmBulkDeleteSubmitBtn.disabled = false;
            bootstrap.Modal.getInstance(document.getElementById('bulkDeleteConfirmModal')).hide();

            if (data.success) {
                showToast(data.message || i18n.bannerDeleted, 'success');
                fetchTableData();
            } else {
                showToast(data.message || i18n.operationFailed, 'error');
            }
        })
        .catch(err => {
            console.error('Bulk delete error:', err);
            spinner.classList.add('d-none');
            confirmBulkDeleteSubmitBtn.disabled = false;
            showToast(i18n.networkError, 'error');
        });
    });
})();
</script>
@endpush
