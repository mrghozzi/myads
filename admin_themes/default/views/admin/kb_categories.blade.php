@extends('admin::layouts.admin')

@section('title', __('messages.kb_manage_categories'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<!-- Toast Notification -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="kbCatToast" class="toast align-items-center text-white bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 14px;">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2">
                <i class="feather-check-circle fs-5" id="kbCatToastIcon"></i>
                <span id="kbCatToastMessage">Success</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Superdesign Header -->
<div class="row g-0 align-items-center mb-4">
    <div class="col-12 px-3">
        <div class="card border-0 shadow-sm overflow-hidden position-relative" style="border-radius: 20px; background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
            <div class="position-absolute top-0 end-0 p-4 opacity-10">
                <i class="feather-folder" style="font-size: 140px; transform: rotate(-10deg);"></i>
            </div>
            
            <div class="card-body p-4 p-md-5 position-relative z-index-1">
                <div class="row align-items-center">
                    <div class="col-lg-7 text-white">
                        <h1 class="display-6 fw-bold mb-2">
                            {{ __('messages.kb_manage_categories') }}
                        </h1>
                        <p class="lead opacity-80 mb-0 fs-6">
                            {{ __('messages.kb_manage_categories_desc') ?? 'Organize platform documentation into clear structural categories and taxonomy.' }}
                        </p>
                    </div>
                    <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
                        <a href="{{ route('admin.knowledgebase') }}" class="btn btn-light btn-lg fw-bold shadow-sm px-4 py-2 me-2" style="border-radius: 12px; color: #4338ca;">
                            <i class="feather-book-open me-2"></i> {{ __('messages.knowledgebase') }}
                        </a>
                        <button type="button" class="btn btn-warning btn-lg fw-bold shadow-sm px-4 py-2" data-bs-toggle="modal" data-bs-target="#addCategoryModal" style="border-radius: 12px; color: #1e293b;">
                            <i class="feather-plus-circle me-2"></i> {{ __('messages.add') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-content container-lg px-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 14px;">
            {{ session('success') }}
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; background: var(--admin-premium-surface, #fff); border: 1px solid var(--admin-premium-border, rgba(143,145,172,0.15)) !important;">
        <div class="card-header border-0 bg-transparent py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
                <h5 class="fw-bold mb-0 text-dark">{{ __('messages.category_list') ?? 'Taxonomy Categories' }}</h5>
                <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 fw-bold" id="catTotalBadge">
                    {{ $categories->count() }} {{ __('messages.total') }}
                </span>
            </div>
            <div class="cat-quick-search position-relative" style="min-width: 240px;">
                <input type="text" id="catQuickSearch" class="form-control form-control-sm bg-light border-0" placeholder="{{ __('messages.search_placeholder') ?? 'Filter categories…' }}" style="border-radius: 10px; padding-left: 32px;">
                <i class="feather-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted" style="font-size: 14px;"></i>
            </div>
        </div>
        <div class="card-body px-0 pt-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="kbCategoriesTable">
                    <thead class="text-uppercase fs-11 fw-bold text-muted bg-light">
                        <tr>
                            <th class="ps-4 py-3" style="width: 80px;">#ID</th>
                            <th class="py-3">{{ __('messages.name') }}</th>
                            <th class="text-center py-3">{{ __('messages.topics') }}</th>
                            <th class="text-center py-3">{{ __('messages.sort') }}</th>
                            <th class="text-end pe-4 py-3">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="fs-13" id="catTableBody">
                        @forelse($categories as $category)
                        <tr class="transition-all" id="kb-cat-row-{{ $category->id }}">
                            <td class="ps-4">
                                <span class="fw-bold text-muted">#{{ $category->id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="category-icon-box me-3 shadow-sm bg-gradient-brand">
                                        <i class="feather-folder"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-14 mb-1 cat-name-display">{{ $category->name }}</div>
                                        <div class="text-muted small opacity-80 cat-meta-display">
                                            <code>{{ $category->slug }}</code>
                                            @if($category->description)
                                                <span class="ms-1">- {{ Str::limit($category->description, 50) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1">{{ $category->articles_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark fw-bold rounded-pill px-3 cat-sort-display">{{ $category->sort_order }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-icon btn-light-primary hover-scale-11" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}" title="{{ __('messages.edit') }}">
                                        <i class="feather-edit-2"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon btn-light-danger ms-2 hover-scale-11" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $category->id }}" title="{{ __('messages.delete') }}">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="noCatRow">
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="wd-60 ht-60 d-flex align-items-center justify-content-center mb-3 mx-auto bg-soft-primary rounded-circle shadow-sm" style="width: 50px; height: 50px;">
                                    <i class="feather-folder fs-4 text-primary"></i>
                                </div>
                                <h6 class="fw-bold mb-1">{{ __('messages.kb_no_categories') }}</h6>
                                <p class="small text-muted mb-0">{{ __('messages.kb_no_categories_desc') ?? 'Create categories to organize your knowledge base topics.' }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modals')
<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.add') }} {{ __('messages.kb_category') }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.kb_categories.store') }}" method="POST" id="ajaxAddCatForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg border-soft-light bg-light" required maxlength="150" style="border-radius: 12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.description') }}</label>
                        <textarea name="description" class="form-control border-soft-light bg-light" rows="3" maxlength="500" style="border-radius: 12px;"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.sort') }}</label>
                        <input type="number" name="sort_order" class="form-control form-control-lg border-soft-light bg-light" value="0" min="0" style="border-radius: 12px;">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm" style="border-radius: 10px;">{{ __('messages.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($categories as $category)
<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.edit') }} — {{ $category->name }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.kb_categories.update', $category->id) }}" method="POST" class="ajax-edit-cat-form" data-id="{{ $category->id }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-lg border-soft-light bg-light" value="{{ $category->name }}" required maxlength="150" style="border-radius: 12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.description') }}</label>
                        <textarea name="description" class="form-control border-soft-light bg-light" rows="3" maxlength="500" style="border-radius: 12px;">{{ $category->description }}</textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.sort') }}</label>
                        <input type="number" name="sort_order" class="form-control form-control-lg border-soft-light bg-light" value="{{ $category->sort_order }}" min="0" style="border-radius: 12px;">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light fw-bold px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4 py-2 shadow-sm" style="border-radius: 10px;">{{ __('messages.update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.delete') }} — {{ $category->name }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto shadow-sm" style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center; font-size: 28px;">
                    <i class="feather-trash-2"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">{{ __('messages.kb_confirm_delete_category') }}</h4>
                <p class="text-muted mb-2">{{ $category->name }} ({{ $category->articles_count }} {{ __('messages.topics') }})</p>
                <p class="text-muted small mb-0">{{ __('messages.kb_delete_category_note') }}</p>
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light fw-bold px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn btn-danger fw-bold px-4 py-2 shadow-sm cat-delete-confirm-btn" data-id="{{ $category->id }}" data-url="{{ route('admin.kb_categories.delete', $category->id) }}" style="border-radius: 10px;">
                    {{ __('messages.delete') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
(function() {
    function showCatToast(msg, isSuccess = true) {
        const toastEl = document.getElementById('kbCatToast');
        if (!toastEl) return;
        const icon = document.getElementById('kbCatToastIcon');
        const text = document.getElementById('kbCatToastMessage');
        text.innerText = msg;
        toastEl.className = 'toast align-items-center text-white border-0 shadow-lg ' + (isSuccess ? 'bg-success' : 'bg-danger');
        if (icon) icon.className = isSuccess ? 'feather-check-circle fs-5' : 'feather-alert-triangle fs-5';
        const bsToast = new bootstrap.Toast(toastEl, { delay: 3500 });
        bsToast.show();
    }

    // Quick client-side filter
    const quickInput = document.getElementById('catQuickSearch');
    if (quickInput) {
        quickInput.addEventListener('input', function() {
            const val = this.value.toLowerCase().trim();
            document.querySelectorAll('#catTableBody tr').forEach(row => {
                if (row.id === 'noCatRow') return;
                const txt = row.innerText.toLowerCase();
                row.style.display = txt.includes(val) ? '' : 'none';
            });
        });
    }

    // AJAX Delete Category
    document.querySelectorAll('.cat-delete-confirm-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const url = this.getAttribute('data-url');
            const modalEl = document.getElementById('deleteCategoryModal' + id);
            const bsModal = bootstrap.Modal.getInstance(modalEl);

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (bsModal) bsModal.hide();
                if (res.success) {
                    showCatToast(res.message);
                    const row = document.getElementById('kb-cat-row-' + id);
                    if (row) row.remove();
                    const badge = document.getElementById('catTotalBadge');
                    if (badge) {
                        let cur = parseInt(badge.innerText, 10);
                        if (!isNaN(cur)) badge.innerText = Math.max(0, cur - 1) + ' {{ __('messages.total') }}';
                    }
                } else {
                    showCatToast('Failed to delete category', false);
                }
            })
            .catch(() => {
                if (bsModal) bsModal.hide();
                showCatToast('Error deleting category', false);
            });
        });
    });

})();
</script>
<style>
    .category-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #fff;
    }
    .bg-gradient-brand {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
    }
    .hover-scale-11:hover {
        transform: scale(1.08);
    }
    .btn-light-primary {
        background: rgba(79, 70, 229, 0.1);
        color: #4f46e5;
        border: 1px solid rgba(79, 70, 229, 0.2);
    }
    .btn-light-primary:hover {
        background: #4f46e5;
        color: #fff;
    }
    .btn-light-danger {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
    .btn-light-danger:hover {
        background: #ef4444;
        color: #fff;
    }
    .transition-all {
        transition: all 0.2s ease;
    }
    .opacity-10 { opacity: 0.1; }
    .opacity-80 { opacity: 0.8; }
    .z-index-1 { z-index: 1; }
</style>
@endpush
