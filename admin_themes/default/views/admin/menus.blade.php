@extends('admin::layouts.admin')

@section('title', __('messages.menu'))

@section('content')
<div class="admin-page">
    {{-- Hero Section --}}
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.menu') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.style') ?? 'Appearance' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.menu') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.navigation_menu_list') }} — {{ __('messages.new_menu') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-summary-grid w-100">
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.menu') }}</span>
                    <span class="admin-summary-value" id="heroTotalCount">{{ number_format($menus->total()) }}</span>
                </div>
                <div class="admin-summary-card d-none d-sm-flex flex-column justify-content-center">
                    <span class="admin-summary-label">{{ __('messages.status') ?? 'Status' }}</span>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <span class="badge bg-soft-success text-success px-2 py-1 fs-12 fw-bold">
                            <i class="feather-check-circle me-1"></i>{{ __('messages.active') ?? 'Active' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Fallback Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0 shadow-sm" role="alert">
            <i class="feather-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="feather-alert-circle me-2 fs-5"></i>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Workspace Grid --}}
    <div class="admin-workspace-grid">
        {{-- Navigation Menu List Panel (Wide Column) --}}
        <section class="admin-panel">
            <div class="admin-panel__header flex-wrap gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-soft-primary text-primary rounded p-2 d-inline-flex">
                        <i class="feather-list fs-5"></i>
                    </div>
                    <div>
                        <span class="admin-panel__eyebrow mb-0">{{ __('messages.navigation_menu_list') }}</span>
                        <div class="d-flex align-items-center gap-2">
                            <h2 class="admin-panel__title mb-0">{{ __('messages.menu') }}</h2>
                            <span class="badge bg-soft-primary text-primary rounded-pill px-2 py-1 fs-12" id="tableCountBadge">
                                {{ number_format($menus->total()) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Live Search Toolbar --}}
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <div class="input-group input-group-sm" style="width: 220px;">
                        <span class="input-group-text bg-transparent border-end-0 text-muted">
                            <i class="feather-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 ps-0" id="menuSearchInput" placeholder="{{ __('messages.search') ?? 'Search menus...' }}" autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="admin-panel__body p-0">
                <div class="admin-table-wrap table-responsive">
                    <table class="table table-hover align-middle admin-table mb-0" id="menusTable">
                        <thead>
                            <tr>
                                <th style="width: 75px;">#ID</th>
                                <th style="min-width: 170px;">{{ __('messages.name') }}</th>
                                <th style="min-width: 200px;">{{ __('messages.url') }}</th>
                                <th class="text-end" style="width: 140px;">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody id="menusTableBody">
                            @forelse($menus as $menu)
                                <tr id="menu-row-{{ $menu->id_m }}" data-id="{{ $menu->id_m }}" data-name="{{ $menu->name }}" data-dir="{{ $menu->dir }}" class="menu-table-row">
                                    <td data-label="#ID">
                                        <span class="badge bg-soft-secondary text-secondary fw-bold px-2 py-1 fs-12">#{{ $menu->id_m }}</span>
                                    </td>
                                    <td data-label="{{ __('messages.name') }}">
                                        <input type="text" class="form-control form-control-sm menu-inline-input js-inline-name" value="{{ $menu->name }}" aria-label="{{ __('messages.name') }}">
                                    </td>
                                    <td data-label="{{ __('messages.url') }}">
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="text" class="form-control form-control-sm menu-inline-input font-monospace js-inline-dir" value="{{ $menu->dir }}" aria-label="{{ __('messages.url') }}">
                                            <a href="{{ $menu->dir }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-icon btn-light text-muted flex-shrink-0 js-preview-link" title="{{ __('messages.view') ?? 'Open link' }}" data-bs-toggle="tooltip">
                                                <i class="feather-external-link"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td data-label="{{ __('messages.actions') }}" class="text-end">
                                        <div class="hstack gap-1 justify-content-end">
                                            <button type="button" class="btn btn-sm btn-icon btn-soft-success js-inline-save" title="{{ __('messages.save') ?? 'Save' }}" data-bs-toggle="tooltip">
                                                <i class="feather-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-icon btn-soft-primary js-modal-edit" title="{{ __('messages.edit') ?? 'Edit' }}" data-bs-toggle="tooltip">
                                                <i class="feather-edit-2"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-icon btn-soft-danger js-menu-delete" title="{{ __('messages.delete') ?? 'Delete' }}" data-bs-toggle="tooltip">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyMenusRow">
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-center py-4">
                                            <div class="avatar-text avatar-xl bg-soft-primary text-primary mx-auto mb-3" style="width: 56px; height: 56px; border-radius: 16px;">
                                                <i class="feather-compass fs-24"></i>
                                            </div>
                                            <h5 class="fw-bold mb-1">{{ __('messages.no_menus_found') }}</h5>
                                            <p class="text-muted small mb-0">{{ __('messages.new_menu') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            <tr id="emptySearchRow" style="display: none;">
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-center py-4">
                                        <div class="avatar-text avatar-xl bg-soft-warning text-warning mx-auto mb-3" style="width: 56px; height: 56px; border-radius: 16px;">
                                            <i class="feather-search fs-24"></i>
                                        </div>
                                        <h5 class="fw-bold mb-1">{{ __('messages.no_results_found') ?? 'No matching menus found' }}</h5>
                                        <p class="text-muted small mb-0">{{ __('messages.try_another_search') ?? 'Try typing a different term.' }}</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if($menus->hasPages())
                    <div class="p-3 border-top d-flex justify-content-center" id="paginationWrapper">
                        {{ $menus->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </section>

        {{-- Add New Menu Panel (Sidebar Column) --}}
        <section class="admin-panel">
            <div class="admin-panel__header">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-soft-success text-success rounded p-2 d-inline-flex">
                        <i class="feather-plus fs-5"></i>
                    </div>
                    <div>
                        <span class="admin-panel__eyebrow mb-0">{{ __('messages.new_menu') }}</span>
                        <h2 class="admin-panel__title mb-0">{{ __('messages.add') }}</h2>
                    </div>
                </div>
            </div>
            <div class="admin-panel__body">
                <form id="addMenuForm" action="{{ route('admin.menus.store') }}" method="POST" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="addMenuName" class="form-label fw-bold small mb-1">
                            <i class="feather-type me-1 text-primary"></i>{{ __('messages.name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="feather-tag text-muted"></i></span>
                            <input type="text" class="form-control border-start-0" id="addMenuName" name="name" placeholder="{{ __('messages.name') }}" required autocomplete="off">
                        </div>
                        <div class="invalid-feedback d-none small mt-1" id="addMenuNameError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="addMenuDir" class="form-label fw-bold small mb-1">
                            <i class="feather-link me-1 text-primary"></i>{{ __('messages.url') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="feather-globe text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 font-monospace" id="addMenuDir" name="dir" placeholder="/forum, https://..." required autocomplete="off">
                        </div>
                        <div class="invalid-feedback d-none small mt-1" id="addMenuDirError"></div>

                        {{-- Quick Shortcuts --}}
                        <div class="mt-3 pt-2 border-top">
                            <span class="text-muted fs-11 d-block mb-2 fw-bold text-uppercase" style="letter-spacing: 0.5px;">
                                <i class="feather-zap text-warning me-1"></i>{{ __('messages.shortcut') ?? 'Quick Shortcuts' }}:
                            </span>
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="menu-quick-chip js-quick-chip" data-name="{{ __('messages.home') ?? 'Home' }}" data-dir="/"><i class="feather-home fs-11"></i> /</button>
                                <button type="button" class="menu-quick-chip js-quick-chip" data-name="{{ __('messages.forum') ?? 'Forum' }}" data-dir="/forum"><i class="feather-message-square fs-11"></i> /forum</button>
                                <button type="button" class="menu-quick-chip js-quick-chip" data-name="{{ __('messages.store') ?? 'Store' }}" data-dir="/store"><i class="feather-shopping-bag fs-11"></i> /store</button>
                                <button type="button" class="menu-quick-chip js-quick-chip" data-name="{{ __('messages.news') ?? 'News' }}" data-dir="/news"><i class="feather-rss fs-11"></i> /news</button>
                                <button type="button" class="menu-quick-chip js-quick-chip" data-name="{{ __('messages.directory') ?? 'Directory' }}" data-dir="/directory"><i class="feather-folder fs-11"></i> /directory</button>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary fw-bold" id="addMenuSubmitBtn">
                            <i class="feather-plus-circle me-1"></i>
                            <span class="btn-text">{{ __('messages.add') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
@endsection

@section('modals')
{{-- Edit Menu Modal --}}
<div class="modal fade" id="menuEditModal" tabindex="-1" aria-labelledby="menuEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--admin-premium-radius, 24px); overflow: hidden;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-soft-primary text-primary rounded p-2 d-inline-flex">
                        <i class="feather-edit-2 fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="menuEditModalLabel">{{ __('messages.edit_menu') ?? 'Edit Menu' }}</h5>
                        <span class="text-muted small" id="editModalMenuId"></span>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="modalEditForm" novalidate>
                @csrf
                <input type="hidden" id="editMenuId" name="id">
                <div class="modal-body px-4 py-3">
                    <div class="alert alert-danger d-none py-2 px-3 small" id="modalEditAlert"></div>
                    <div class="mb-3">
                        <label for="editMenuName" class="form-label fw-bold small mb-1">
                            <i class="feather-type me-1 text-primary"></i>{{ __('messages.name') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="feather-tag text-muted"></i></span>
                            <input type="text" class="form-control border-start-0" id="editMenuName" name="name" required autocomplete="off">
                        </div>
                        <div class="invalid-feedback d-none small mt-1" id="editMenuNameError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="editMenuDir" class="form-label fw-bold small mb-1">
                            <i class="feather-link me-1 text-primary"></i>{{ __('messages.url') }}
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="feather-globe text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 font-monospace" id="editMenuDir" name="dir" required autocomplete="off">
                        </div>
                        <div class="invalid-feedback d-none small mt-1" id="editMenuDirError"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                    <button type="submit" class="btn btn-primary fw-bold" id="modalEditSubmitBtn">
                        <i class="feather-save me-1"></i>
                        <span class="btn-text">{{ __('messages.save') ?? 'Save Changes' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Menu Modal --}}
<div class="modal fade" id="menuDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--admin-premium-radius, 24px);">
            <div class="modal-body text-center p-4">
                <div class="avatar-text avatar-lg bg-soft-danger text-danger mx-auto mb-3" style="width: 54px; height: 54px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
                    <i class="feather-trash-2 fs-4"></i>
                </div>
                <h3 class="h5 fw-bold mb-2">{{ __('messages.delete') }}</h3>
                <p class="text-muted small mb-0">{{ __('messages.confirm_delete_menu') }}</p>
                <div class="badge bg-soft-secondary text-dark mt-2 px-2 py-1 fs-13 text-truncate max-w-100" id="deleteMenuBadge"></div>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0 pb-4 gap-2">
                <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <button type="button" class="btn btn-danger px-3 fw-bold" id="confirmDeleteBtn">
                    <i class="feather-trash-2 me-1"></i>
                    <span class="btn-text">{{ __('messages.delete') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Premium Menu Custom Styling */
    .menu-inline-input {
        background: var(--admin-premium-surface, #ffffff);
        border: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08));
        border-radius: 8px;
        color: var(--admin-premium-text, #1f2937);
        font-size: 0.88rem;
        padding: 0.4rem 0.65rem;
        transition: all 0.2s ease;
    }
    .menu-inline-input:focus {
        background: var(--admin-premium-surface, #ffffff);
        border-color: var(--admin-premium-accent, #615dfa);
        box-shadow: 0 0 0 3px var(--admin-premium-accent-soft, rgba(97, 93, 250, 0.15));
        color: var(--admin-premium-text, #1f2937);
        outline: none;
    }
    html.app-skin-dark .menu-inline-input {
        background: var(--admin-premium-surface-alt, #1b1e2f);
        border-color: var(--admin-premium-border, rgba(148, 163, 184, 0.14));
        color: var(--admin-premium-text, #eef2ff);
    }
    html.app-skin-dark .menu-inline-input:focus {
        background: var(--admin-premium-surface, #23263b);
        border-color: var(--admin-premium-accent, #615dfa);
    }

    .menu-quick-chip {
        cursor: pointer;
        font-size: 0.76rem;
        font-weight: 600;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        background: var(--admin-premium-surface-alt, #f6f7fb);
        border: 1px solid var(--admin-premium-border, rgba(15, 23, 42, 0.08));
        color: var(--admin-premium-text, #1f2937);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .menu-quick-chip:hover {
        background: var(--admin-premium-accent-soft, rgba(97, 93, 250, 0.12));
        border-color: var(--admin-premium-border-strong, rgba(97, 93, 250, 0.25));
        color: var(--admin-premium-accent, #615dfa);
        transform: translateY(-1px);
    }
    html.app-skin-dark .menu-quick-chip {
        background: var(--admin-premium-surface-alt, #1b1e2f);
        border-color: var(--admin-premium-border, rgba(148, 163, 184, 0.14));
        color: var(--admin-premium-text, #eef2ff);
    }

    /* Row Animations */
    @keyframes menuRowHighlight {
        0% { background-color: rgba(97, 93, 250, 0.25); }
        100% { background-color: transparent; }
    }
    .menu-row-highlight {
        animation: menuRowHighlight 2s ease-out;
    }

    .menu-row-fade-out {
        transition: all 0.35s ease-out;
        opacity: 0 !important;
        transform: translateX(25px);
    }

    /* Soft Icon Buttons */
    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 0;
        transition: all 0.2s ease;
    }
    .btn-soft-primary {
        background: var(--admin-premium-accent-soft, rgba(97, 93, 250, 0.12));
        color: var(--admin-premium-accent, #615dfa);
    }
    .btn-soft-primary:hover {
        background: var(--admin-premium-accent, #615dfa);
        color: #fff;
    }
    .btn-soft-success {
        background: var(--admin-premium-success-soft, rgba(34, 197, 94, 0.12));
        color: #16a34a;
    }
    .btn-soft-success:hover {
        background: #16a34a;
        color: #fff;
    }
    .btn-soft-danger {
        background: var(--admin-premium-danger-soft, rgba(239, 68, 68, 0.12));
        color: #dc2626;
    }
    .btn-soft-danger:hover {
        background: #dc2626;
        color: #fff;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const tableBody = document.getElementById('menusTableBody');
    const heroCount = document.getElementById('heroTotalCount');
    const tableCountBadge = document.getElementById('tableCountBadge');
    const emptyRow = document.getElementById('emptyMenusRow');
    const emptySearchRow = document.getElementById('emptySearchRow');
    const searchInput = document.getElementById('menuSearchInput');

    // Modals
    const editModalEl = document.getElementById('menuEditModal');
    const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;
    const deleteModalEl = document.getElementById('menuDeleteModal');
    const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;

    let pendingDeleteId = null;
    let pendingDeleteName = '';

    // ==========================================
    // Glassmorphic Toast Notification Engine
    // ==========================================
    function showMenuToast(message, type) {
        type = type || 'success';
        let container = document.getElementById('menu-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'menu-toast-container';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = '99999';
            container.style.maxWidth = '380px';
            container.style.pointerEvents = 'none';
            document.body.appendChild(container);
        }

        const toastEl = document.createElement('div');
        const isSuccess = type === 'success';
        toastEl.className = 'toast show border-0 shadow-lg mb-2 text-white';
        toastEl.style.borderRadius = '14px';
        toastEl.style.background = isSuccess
            ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)'
            : 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
        toastEl.style.boxShadow = '0 10px 25px rgba(0,0,0,0.18)';
        toastEl.style.pointerEvents = 'auto';
        toastEl.style.transition = 'all 0.3s ease';

        toastEl.innerHTML = `
            <div class="d-flex align-items-center p-3">
                <i class="${isSuccess ? 'feather-check-circle' : 'feather-alert-triangle'} fs-5 me-2"></i>
                <div class="flex-grow-1 fw-bold fs-13">${message}</div>
                <button type="button" class="btn-close btn-close-white ms-2 shadow-none" aria-label="Close"></button>
            </div>
        `;

        const closeBtn = toastEl.querySelector('.btn-close');
        function dismissToast() {
            toastEl.style.opacity = '0';
            toastEl.style.transform = 'translateY(-10px)';
            setTimeout(function () { toastEl.remove(); }, 300);
        }
        closeBtn.addEventListener('click', dismissToast);
        container.appendChild(toastEl);
        setTimeout(dismissToast, 4000);
    }

    // ==========================================
    // Counter Update Helper
    // ==========================================
    function setTotalCount(count) {
        const formatted = new Intl.NumberFormat().format(count);
        if (heroCount) heroCount.textContent = formatted;
        if (tableCountBadge) tableCountBadge.textContent = formatted;
    }

    function getCurrentRowCount() {
        return tableBody.querySelectorAll('tr.menu-table-row').length;
    }

    // ==========================================
    // Quick Route Chips
    // ==========================================
    document.querySelectorAll('.js-quick-chip').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetName = this.dataset.name || '';
            const targetDir = this.dataset.dir || '';
            const nameInput = document.getElementById('addMenuName');
            const dirInput = document.getElementById('addMenuDir');

            if (nameInput && !nameInput.value.trim()) {
                nameInput.value = targetName;
            }
            if (dirInput) {
                dirInput.value = targetDir;
                dirInput.focus();
            }
        });
    });

    // ==========================================
    // Row HTML Generator
    // ==========================================
    function createRowHtml(menu) {
        return `
            <tr id="menu-row-${menu.id_m}" data-id="${menu.id_m}" data-name="${escapeHtml(menu.name)}" data-dir="${escapeHtml(menu.dir)}" class="menu-table-row menu-row-highlight">
                <td data-label="#ID">
                    <span class="badge bg-soft-secondary text-secondary fw-bold px-2 py-1 fs-12">#${menu.id_m}</span>
                </td>
                <td data-label="{{ __('messages.name') }}">
                    <input type="text" class="form-control form-control-sm menu-inline-input js-inline-name" value="${escapeHtml(menu.name)}" aria-label="{{ __('messages.name') }}">
                </td>
                <td data-label="{{ __('messages.url') }}">
                    <div class="d-flex align-items-center gap-2">
                        <input type="text" class="form-control form-control-sm menu-inline-input font-monospace js-inline-dir" value="${escapeHtml(menu.dir)}" aria-label="{{ __('messages.url') }}">
                        <a href="${escapeHtml(menu.dir)}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-icon btn-light text-muted flex-shrink-0 js-preview-link" title="{{ __('messages.view') ?? 'Open link' }}">
                            <i class="feather-external-link"></i>
                        </a>
                    </div>
                </td>
                <td data-label="{{ __('messages.actions') }}" class="text-end">
                    <div class="hstack gap-1 justify-content-end">
                        <button type="button" class="btn btn-sm btn-icon btn-soft-success js-inline-save" title="{{ __('messages.save') ?? 'Save' }}">
                            <i class="feather-check"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-soft-primary js-modal-edit" title="{{ __('messages.edit') ?? 'Edit' }}">
                            <i class="feather-edit-2"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-soft-danger js-menu-delete" title="{{ __('messages.delete') ?? 'Delete' }}">
                            <i class="feather-trash-2"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // ==========================================
    // 1. AJAX Store Menu (Add Form)
    // ==========================================
    const addForm = document.getElementById('addMenuForm');
    const addBtn = document.getElementById('addMenuSubmitBtn');
    const nameInput = document.getElementById('addMenuName');
    const dirInput = document.getElementById('addMenuDir');
    const nameError = document.getElementById('addMenuNameError');
    const dirError = document.getElementById('addMenuDirError');

    if (addForm) {
        addForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Clear previous errors
            nameError.classList.add('d-none');
            dirError.classList.add('d-none');
            nameInput.classList.remove('is-invalid');
            dirInput.classList.remove('is-invalid');

            const nameVal = nameInput.value.trim();
            const dirVal = dirInput.value.trim();

            let hasClientError = false;
            if (!nameVal) {
                nameInput.classList.add('is-invalid');
                nameError.textContent = "{{ __('validation.required', ['attribute' => __('messages.name')]) }}";
                nameError.classList.remove('d-none');
                hasClientError = true;
            }
            if (!dirVal) {
                dirInput.classList.add('is-invalid');
                dirError.textContent = "{{ __('validation.required', ['attribute' => __('messages.url')]) }}";
                dirError.classList.remove('d-none');
                hasClientError = true;
            }
            if (hasClientError) return;

            // Loading state
            const originalBtnHtml = addBtn.innerHTML;
            addBtn.disabled = true;
            addBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> ${nameVal}...`;

            fetch(addForm.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    name: nameVal,
                    dir: dirVal
                })
            })
            .then(function (res) {
                return res.json().then(function (data) {
                    return { status: res.status, ok: res.ok, data: data };
                });
            })
            .then(function (response) {
                addBtn.disabled = false;
                addBtn.innerHTML = originalBtnHtml;

                if (!response.ok) {
                    const errors = response.data.errors || {};
                    if (errors.name) {
                        nameInput.classList.add('is-invalid');
                        nameError.textContent = errors.name[0];
                        nameError.classList.remove('d-none');
                    }
                    if (errors.dir) {
                        dirInput.classList.add('is-invalid');
                        dirError.textContent = errors.dir[0];
                        dirError.classList.remove('d-none');
                    }
                    showMenuToast(response.data.message || "{{ __('messages.error_occurred') ?? 'Error occurred' }}", 'danger');
                    return;
                }

                // Success
                const menu = response.data.menu;
                showMenuToast(response.data.message || "{{ __('messages.menu_created') }}", 'success');

                // Prepend row
                if (emptyRow) emptyRow.style.display = 'none';
                tableBody.insertAdjacentHTML('afterbegin', createRowHtml(menu));

                // Update total count
                if (typeof response.data.total === 'number') {
                    setTotalCount(response.data.total);
                } else {
                    setTotalCount(getCurrentRowCount());
                }

                // Reset form
                addForm.reset();
                nameInput.focus();
            })
            .catch(function (err) {
                addBtn.disabled = false;
                addBtn.innerHTML = originalBtnHtml;
                showMenuToast(err.message || "{{ __('messages.error_occurred') ?? 'Error occurred' }}", 'danger');
            });
        });
    }

    // ==========================================
    // 2. AJAX Inline Edit (Quick Save)
    // ==========================================
    tableBody.addEventListener('click', function (e) {
        const saveBtn = e.target.closest('.js-inline-save');
        if (!saveBtn) return;

        const row = saveBtn.closest('tr');
        const id = row.dataset.id;
        const nameInp = row.querySelector('.js-inline-name');
        const dirInp = row.querySelector('.js-inline-dir');
        const previewLink = row.querySelector('.js-preview-link');

        const newName = nameInp.value.trim();
        const newDir = dirInp.value.trim();

        if (!newName || !newDir) {
            showMenuToast("{{ __('messages.fill_all_required_fields') ?? 'Please fill all required fields.' }}", 'danger');
            return;
        }

        const originalBtnHtml = saveBtn.innerHTML;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

        fetch(`/admin/menus/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                name: newName,
                dir: newDir
            })
        })
        .then(function (res) {
            return res.json().then(function (data) {
                return { ok: res.ok, data: data };
            });
        })
        .then(function (response) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalBtnHtml;

            if (!response.ok) {
                showMenuToast(response.data.message || "{{ __('messages.error_occurred') ?? 'Error occurred' }}", 'danger');
                return;
            }

            // Update row dataset and link
            row.dataset.name = newName;
            row.dataset.dir = newDir;
            if (previewLink) previewLink.href = newDir;

            // Highlight row
            row.classList.remove('menu-row-highlight');
            void row.offsetWidth; // trigger reflow
            row.classList.add('menu-row-highlight');

            showMenuToast(response.data.message || "{{ __('messages.menu_updated') }}", 'success');
        })
        .catch(function (err) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalBtnHtml;
            showMenuToast(err.message || "{{ __('messages.error_occurred') ?? 'Error occurred' }}", 'danger');
        });
    });

    // ==========================================
    // 3. Modal Edit Trigger & Submission
    // ==========================================
    const modalEditForm = document.getElementById('modalEditForm');
    const modalEditSubmitBtn = document.getElementById('modalEditSubmitBtn');
    const editMenuIdInp = document.getElementById('editMenuId');
    const editMenuNameInp = document.getElementById('editMenuName');
    const editMenuDirInp = document.getElementById('editMenuDir');
    const editModalMenuId = document.getElementById('editModalMenuId');
    const modalEditAlert = document.getElementById('modalEditAlert');

    tableBody.addEventListener('click', function (e) {
        const editBtn = e.target.closest('.js-modal-edit');
        if (!editBtn) return;

        const row = editBtn.closest('tr');
        const id = row.dataset.id;
        const name = row.dataset.name || row.querySelector('.js-inline-name').value;
        const dir = row.dataset.dir || row.querySelector('.js-inline-dir').value;

        if (editMenuIdInp) editMenuIdInp.value = id;
        if (editMenuNameInp) editMenuNameInp.value = name;
        if (editMenuDirInp) editMenuDirInp.value = dir;
        if (editModalMenuId) editModalMenuId.textContent = '#' + id;
        if (modalEditAlert) modalEditAlert.classList.add('d-none');

        if (editModal) editModal.show();
    });

    if (modalEditForm) {
        modalEditForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const id = editMenuIdInp.value;
            const newName = editMenuNameInp.value.trim();
            const newDir = editMenuDirInp.value.trim();

            if (!newName || !newDir) {
                if (modalEditAlert) {
                    modalEditAlert.textContent = "{{ __('messages.fill_all_required_fields') ?? 'Please fill all required fields.' }}";
                    modalEditAlert.classList.remove('d-none');
                }
                return;
            }

            const originalBtnHtml = modalEditSubmitBtn.innerHTML;
            modalEditSubmitBtn.disabled = true;
            modalEditSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving...';

            fetch(`/admin/menus/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    name: newName,
                    dir: newDir
                })
            })
            .then(function (res) {
                return res.json().then(function (data) {
                    return { ok: res.ok, data: data };
                });
            })
            .then(function (response) {
                modalEditSubmitBtn.disabled = false;
                modalEditSubmitBtn.innerHTML = originalBtnHtml;

                if (!response.ok) {
                    if (modalEditAlert) {
                        modalEditAlert.textContent = response.data.message || "{{ __('messages.error_occurred') ?? 'Error occurred' }}";
                        modalEditAlert.classList.remove('d-none');
                    }
                    return;
                }

                // Update Table Row
                const row = document.getElementById(`menu-row-${id}`);
                if (row) {
                    row.dataset.name = newName;
                    row.dataset.dir = newDir;
                    const nameInp = row.querySelector('.js-inline-name');
                    const dirInp = row.querySelector('.js-inline-dir');
                    const previewLink = row.querySelector('.js-preview-link');
                    if (nameInp) nameInp.value = newName;
                    if (dirInp) dirInp.value = newDir;
                    if (previewLink) previewLink.href = newDir;

                    row.classList.remove('menu-row-highlight');
                    void row.offsetWidth;
                    row.classList.add('menu-row-highlight');
                }

                if (editModal) editModal.hide();
                showMenuToast(response.data.message || "{{ __('messages.menu_updated') }}", 'success');
            })
            .catch(function (err) {
                modalEditSubmitBtn.disabled = false;
                modalEditSubmitBtn.innerHTML = originalBtnHtml;
                if (modalEditAlert) {
                    modalEditAlert.textContent = err.message;
                    modalEditAlert.classList.remove('d-none');
                }
            });
        });
    }

    // ==========================================
    // 4. AJAX Delete Flow
    // ==========================================
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const deleteMenuBadge = document.getElementById('deleteMenuBadge');

    tableBody.addEventListener('click', function (e) {
        const deleteBtn = e.target.closest('.js-menu-delete');
        if (!deleteBtn) return;

        const row = deleteBtn.closest('tr');
        pendingDeleteId = row.dataset.id;
        pendingDeleteName = row.dataset.name || row.querySelector('.js-inline-name')?.value || '';

        if (deleteMenuBadge) {
            deleteMenuBadge.textContent = `#${pendingDeleteId} — ${pendingDeleteName}`;
        }

        if (deleteModal) deleteModal.show();
    });

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function () {
            if (!pendingDeleteId) return;

            const originalBtnHtml = confirmDeleteBtn.innerHTML;
            confirmDeleteBtn.disabled = true;
            confirmDeleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Deleting...';

            fetch(`/admin/menus/${pendingDeleteId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function (res) {
                return res.json().then(function (data) {
                    return { ok: res.ok, data: data };
                });
            })
            .then(function (response) {
                confirmDeleteBtn.disabled = false;
                confirmDeleteBtn.innerHTML = originalBtnHtml;

                if (deleteModal) deleteModal.hide();

                if (!response.ok) {
                    showMenuToast(response.data.message || "{{ __('messages.error_occurred') ?? 'Error occurred' }}", 'danger');
                    return;
                }

                // Smoothly remove row
                const row = document.getElementById(`menu-row-${pendingDeleteId}`);
                if (row) {
                    row.classList.add('menu-row-fade-out');
                    setTimeout(function () {
                        row.remove();
                        const remaining = getCurrentRowCount();
                        if (typeof response.data.total === 'number') {
                            setTotalCount(response.data.total);
                        } else {
                            setTotalCount(remaining);
                        }

                        if (remaining === 0 && emptyRow) {
                            emptyRow.style.display = '';
                        }
                    }, 350);
                }

                showMenuToast(response.data.message || "{{ __('messages.menu_deleted') }}", 'success');
                pendingDeleteId = null;
                pendingDeleteName = '';
            })
            .catch(function (err) {
                confirmDeleteBtn.disabled = false;
                confirmDeleteBtn.innerHTML = originalBtnHtml;
                if (deleteModal) deleteModal.hide();
                showMenuToast(err.message || "{{ __('messages.error_occurred') ?? 'Error occurred' }}", 'danger');
            });
        });
    }

    // ==========================================
    // 5. Real-Time Client-Side Search Filter
    // ==========================================
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            const rows = tableBody.querySelectorAll('tr.menu-table-row');
            let matchCount = 0;

            rows.forEach(function (row) {
                const nameText = (row.dataset.name || row.querySelector('.js-inline-name')?.value || '').toLowerCase();
                const dirText = (row.dataset.dir || row.querySelector('.js-inline-dir')?.value || '').toLowerCase();
                const idText = (row.dataset.id || '').toLowerCase();

                if (!query || nameText.includes(query) || dirText.includes(query) || idText.includes(query)) {
                    row.style.display = '';
                    matchCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (emptySearchRow) {
                if (matchCount === 0 && rows.length > 0 && query) {
                    emptySearchRow.style.display = '';
                } else {
                    emptySearchRow.style.display = 'none';
                }
            }
        });
    }
});
</script>
@endpush
