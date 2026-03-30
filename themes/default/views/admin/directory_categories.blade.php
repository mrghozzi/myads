@extends('theme::layouts.admin')

@section('title', __('messages.directory_categories'))

@section('content')
<!-- Superdesign Header -->
<div class="row g-0 align-items-center mb-5">
    <div class="col-12 px-4">
        <div class="card border-0 shadow-lg overflow-hidden position-relative" style="border-radius: 24px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <!-- Decorative Elements -->
            <div class="position-absolute top-0 end-0 p-5 opacity-10">
                <i class="fa-solid fa-folder-tree" style="font-size: 160px; transform: rotate(-15deg);"></i>
            </div>
            
            <div class="card-body p-5 position-relative z-index-1">
                <div class="row align-items-center">
                    <div class="col-lg-7 text-white">
                        <h1 class="display-5 fw-black mb-2 animate__animated animate__fadeIn">
                            {{ __('messages.directory_categories') }}
                        </h1>
                        <p class="lead opacity-80 mb-0 animate__animated animate__fadeIn animate__delay-1s">
                            {{ __('messages.directory_categories_desc') }}
                        </p>
                    </div>
                    <div class="col-lg-5 text-lg-end mt-4 mt-lg-0 animate__animated animate__fadeInRight">
                        <button type="button" class="btn btn-primary btn-lg fw-bold shadow-sm px-4 py-3 hover-scale" data-bs-toggle="modal" data-bs-target="#addCategoryModal" style="border-radius: 16px;">
                            <i class="feather-plus-circle me-2"></i> {{ __('messages.add_category') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-content container-lg px-4">
    <!-- Modern Category List -->
    <div class="card border-0 shadow-sm mb-5" style="border-radius: 20px; backdrop-filter: blur(10px); background: rgba(var(--nxl-white-rgb), 0.8);">
        <div class="card-header border-0 bg-transparent py-4 ps-4 pe-4 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0">{{ __('messages.category_list') }}</h5>
            <span class="badge bg-soft-warning text-warning rounded-pill px-3 py-2 fw-bold">
                {{ $categories->total() }} {{ __('messages.total') }}
            </span>
        </div>
        <div class="card-body px-0">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead class="text-uppercase fs-11 fw-bold text-muted bg-soft-light">
                        <tr>
                            <th class="ps-4 py-3">#ID</th>
                            <th class="py-3">{{ __('messages.category') }}</th>
                            <th class="py-3">{{ __('messages.parent_category') }}</th>
                            <th class="py-3 text-center">{{ __('messages.order') }}</th>
                            <th class="text-end pe-4 py-3">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="fs-13">
                        @foreach($categories as $category)
                        <tr class="hover-bg-light transition-all border-bottom border-soft-light">
                            <td class="ps-4">
                                <span class="fw-bold text-muted">#{{ $category->id }}</span>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-bold text-dark fs-14 mb-1">{{ $category->name }}</div>
                                    @if($category->txt)
                                    <div class="text-muted small opacity-80" title="{{ $category->txt }}">
                                        {{ Str::limit($category->txt, 45) }}
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($category->sub == 0)
                                    <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 fw-bold">
                                        <i class="feather-layers me-1"></i> {{ __('messages.main_category') }}
                                    </span>
                                @else
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="feather-corner-down-right me-2 opacity-50"></i>
                                        <span class="fw-medium">{{ $category->parent->name ?? __('messages.unknown') }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark fw-bold rounded-pill px-3">{{ $category->ordercat }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-icon btn-glass btn-light-primary hover-scale-11" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}" title="{{ __('messages.edit') }}">
                                        <i class="feather-edit-2"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon btn-glass btn-light-danger ms-2 hover-scale-11" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $category->id }}" title="{{ __('messages.delete') }}">
                                        <i class="feather-trash-2"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if($categories->hasPages())
        <div class="card-footer bg-transparent border-0 pb-4">
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('modals')
<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.new_category') }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.directory_categories.store') }}" method="POST">
                @csrf
                <input type="hidden" name="statu" value="1">
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control form-control-lg border-soft-light bg-light" placeholder="{{ __('messages.enter_category_name') }}" style="border-radius: 12px;" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.parent_category') }}</label>
                        <select name="sub" class="form-select form-select-lg border-soft-light bg-light" style="border-radius: 12px;">
                            <option value="0">{{ __('messages.main_category') }}</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.desc') }}</label>
                        <textarea name="txt" class="form-control border-soft-light bg-light" rows="2" placeholder="{{ __('messages.brief_description') }}" style="border-radius: 12px;"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.meta_keywords') }}</label>
                        <textarea name="metakeywords" class="form-control border-soft-light bg-light" rows="2" placeholder="{{ __('messages.meta_keywords_placeholder') }}" style="border-radius: 12px;"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.order') }}</label>
                        <input type="number" name="ordercat" class="form-control form-control-lg border-soft-light bg-light" value="0" style="border-radius: 12px;" required>
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
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.edit_category') }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.directory_categories.update', $category->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control form-control-lg border-soft-light bg-light" value="{{ $category->name }}" style="border-radius: 12px;" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.parent_category') }}</label>
                        <select name="sub" class="form-select form-select-lg border-soft-light bg-light" style="border-radius: 12px;">
                            <option value="0" {{ $category->sub == 0 ? 'selected' : '' }}>{{ __('messages.main_category') }}</option>
                            @foreach($parents as $parent)
                                @if($parent->id != $category->id) {{-- Prevent selecting itself as parent --}}
                                <option value="{{ $parent->id }}" {{ $category->sub == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.desc') }}</label>
                        <textarea name="txt" class="form-control border-soft-light bg-light" rows="2" style="border-radius: 12px;">{{ $category->txt }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.meta_keywords') }}</label>
                        <textarea name="metakeywords" class="form-control border-soft-light bg-light" rows="2" style="border-radius: 12px;">{{ $category->metakeywords }}</textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.order') }}</label>
                        <input type="number" name="ordercat" class="form-control form-control-lg border-soft-light bg-light" value="{{ $category->ordercat }}" style="border-radius: 12px;" required>
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
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.delete_category') }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto shadow-sm" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 32px;">
                    <i class="feather-trash-2"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">{{ __('messages.confirm_delete_category') }}</h4>
                <p class="text-muted mb-4">{{ $category->name }}</p>
                @if($category->children()->count() > 0)
                <div class="alert alert-soft-warning border-0 rounded-4 text-start mb-0">
                    <div class="d-flex border-start border-warning border-4 ps-3">
                        <i class="feather-alert-triangle text-warning me-3 fs-20 mt-1"></i>
                        <div>
                            <div class="fw-bold text-warning">{{ __('messages.warning') ?? 'Warning' }}</div>
                            <div class="small opacity-80">{{ __('messages.warning_has_subcategories') }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light fw-bold px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('messages.cancel') }}</button>
                <form action="{{ route('admin.directory_categories.delete', $category->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger fw-bold px-4 py-2 shadow-sm" style="border-radius: 10px;">{{ __('messages.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@push('scripts')
<style>
    .hover-scale-11:hover {
        transform: scale(1.1);
    }
    .hover-scale:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }
    .btn-glass {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .transition-all {
        transition: all 0.3s ease;
    }
    .hover-bg-light:hover {
        background-color: rgba(var(--nxl-primary-rgb), 0.02);
    }
    .fw-black {
        font-weight: 900;
    }
    .opacity-10 { opacity: 0.1; }
    .opacity-50 { opacity: 0.5; }
    .opacity-80 { opacity: 0.8; }
    .z-index-1 { z-index: 1; }
    .fs-18 { font-size: 18px; }
    .fs-20 { font-size: 20px; }
    
    .alert-soft-warning {
        background-color: #fffbeb;
        color: #92400e;
    }
</style>
@endpush
@endsection
