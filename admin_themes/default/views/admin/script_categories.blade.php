@extends('admin::layouts.admin')

@section('title', __('messages.script_categories') ?? 'Script Subcategories')
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="row g-0 align-items-center mb-5">
    <div class="col-12 px-4">
        <div class="card border-0 shadow-lg overflow-hidden position-relative" style="border-radius: 24px; background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);">
            <div class="position-absolute top-0 end-0 p-5 opacity-10">
                <i class="fa-solid fa-code" style="font-size: 160px; transform: rotate(-15deg);"></i>
            </div>

            <div class="card-body p-5 position-relative z-index-1">
                <div class="row align-items-center">
                    <div class="col-lg-7 text-white">
                        <h1 class="display-5 fw-black mb-2 animate__animated animate__fadeIn">
                            {{ __('messages.script_categories') ?? 'Script Subcategories' }}
                        </h1>
                        <p class="lead opacity-80 mb-0 animate__animated animate__fadeIn animate__delay-1s">
                            {{ __('messages.script_categories_desc') ?? 'Manage the subcategories available for the Script main category in the store. These power the dynamic sub-category selector on the product creation page.' }}
                        </p>
                    </div>
                    <div class="col-lg-5 text-lg-end mt-4 mt-lg-0 animate__animated animate__fadeInRight">
                        <a href="{{ route('admin.products') }}" class="btn btn-light btn-lg fw-bold shadow-sm px-4 py-3 hover-scale me-2" style="border-radius: 16px; color: #0369a1;">
                            <i class="feather-package me-2"></i> {{ __('messages.products') ?? 'Products' }}
                        </a>
                        <button type="button" class="btn btn-warning btn-lg fw-bold shadow-sm px-4 py-3 hover-scale" data-bs-toggle="modal" data-bs-target="#addCategoryModal" style="border-radius: 16px; color: #1e293b;">
                            <i class="feather-plus-circle me-2"></i> {{ __('messages.add') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-content container-lg px-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 16px;">
            {{ session('success') }}
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert" style="border-radius: 16px;">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($categories->isEmpty())
        <div class="card border-0 shadow-sm" style="border-radius: 20px; backdrop-filter: blur(10px); background: rgba(var(--nxl-white-rgb), 0.8);">
            <div class="card-body text-center py-5">
                <div class="wd-60 ht-60 d-flex align-items-center justify-content-center mb-3 mx-auto bg-soft-primary rounded-circle shadow-sm">
                    <i class="fa-solid fa-code fs-24 text-primary"></i>
                </div>
                <h5 class="fw-bold mb-2">{{ __('messages.no_script_categories') ?? 'No script subcategories yet' }}</h5>
                <p class="text-muted mb-3">{{ __('messages.no_script_categories_desc') ?? 'Add your first script subcategory so members can pick a script type when publishing a product.' }}</p>
                <button type="button" class="btn btn-primary fw-bold px-4 py-2 shadow-sm hover-scale" data-bs-toggle="modal" data-bs-target="#addCategoryModal" style="border-radius: 10px;">
                    <i class="feather-plus me-1"></i> {{ __('messages.add') }}
                </button>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm mb-5" style="border-radius: 20px; backdrop-filter: blur(10px); background: rgba(var(--nxl-white-rgb), 0.8);">
            <div class="card-header border-0 bg-transparent py-4 ps-4 pe-4 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">{{ __('messages.category_list') ?? 'Category List' }}</h5>
                <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-2 fw-bold">
                    {{ $categories->count() }} {{ __('messages.total') }}
                </span>
            </div>
            <div class="card-body px-0">
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead class="text-uppercase fs-11 fw-bold text-muted bg-soft-light">
                            <tr>
                                <th class="ps-4 py-3">#ID</th>
                                <th class="py-3">{{ __('messages.name') }}</th>
                                <th class="text-center py-3">{{ __('messages.sort') }}</th>
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
                                    <div class="d-flex align-items-center">
                                        <div class="category-icon-box me-3 shadow-sm bg-gradient-brand">
                                            <i class="fa-solid fa-code"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-14 mb-1">{{ $category->name }}</div>
                                            <div class="text-muted small opacity-80">
                                                {{ $category->o_mode }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark fw-bold rounded-pill px-3">{{ $category->o_order }}</span>
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
        </div>
    @endif
</div>
@endsection

@section('modals')
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.add_script_category') ?? 'New Script Subcategory' }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.script_categories.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control form-control-lg border-soft-light bg-light" placeholder="CMS, Blog, Forum..." style="border-radius: 12px;" required pattern="[A-Za-z0-9_]+" maxlength="80">
                        <small class="text-muted d-block mt-2">{{ __('messages.script_category_name_hint') ?? 'Use letters, numbers, and underscores only (e.g. Ecommerce, Social_Network).' }}</small>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.sort') }}</label>
                        <input type="number" name="sort_order" class="form-control form-control-lg border-soft-light bg-light" value="0" min="0" max="9999" style="border-radius: 12px;">
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
<div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.edit_script_category') ?? 'Edit Script Subcategory' }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.script_categories.update', $category->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control form-control-lg border-soft-light bg-light" value="{{ $category->name }}" style="border-radius: 12px;" required pattern="[A-Za-z0-9_]+" maxlength="80">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.sort') }}</label>
                        <input type="number" name="sort_order" class="form-control form-control-lg border-soft-light bg-light" value="{{ $category->o_order }}" min="0" max="9999" style="border-radius: 12px;">
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

<div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.delete_script_category') ?? 'Delete Script Subcategory' }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto shadow-sm" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 32px;">
                    <i class="feather-trash-2"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">{{ __('messages.confirm_delete') ?? 'Are you sure?' }}</h4>
                <p class="text-muted mb-4">{{ $category->name }}</p>
                <form action="{{ route('admin.script_categories.delete', $category->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-footer border-0 justify-content-center p-0">
                        <button type="button" class="btn btn-light fw-bold px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('messages.cancel') }}</button>
                        <button type="submit" class="btn btn-danger fw-bold px-4 py-2 shadow-sm ms-2" style="border-radius: 10px;">{{ __('messages.delete') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('styles')
<style>
    .category-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #fff;
    }
    .bg-gradient-brand {
        background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
    }
    .hover-scale:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
    }
    .hover-scale-11:hover {
        transform: scale(1.1);
    }
    .transition-all {
        transition: all 0.3s ease;
    }
    .fw-black {
        font-weight: 900;
    }
    .opacity-10 { opacity: 0.1; }
    .opacity-80 { opacity: 0.8; }
    .z-index-1 { z-index: 1; }
    .btn-glass {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .bg-soft-light {
        background-color: rgba(241, 245, 249, 0.6);
    }
    .border-soft-light {
        border-color: #eef1f6 !important;
    }
    .hover-bg-light:hover {
        background-color: rgba(241, 245, 249, 0.4);
    }
</style>
@endpush
