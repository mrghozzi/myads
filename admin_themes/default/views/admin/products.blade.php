@extends('admin::layouts.admin')

@section('title', __('messages.manage_products') ?? 'Manage Products')
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.products') }}">{{ __('messages.store') ?? 'Store' }}</a></li>
                <li>{{ __('messages.products') ?? 'Products' }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.store') ?? 'Store' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.manage_products') ?? 'Manage Products' }}</h1>
            <p class="admin-hero__copy">{{ __('messages.total_products') ?? 'Total Products' }}: {{ $totalCount }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.store.sales') }}" class="btn btn-outline-secondary">
                    <i class="feather-dollar-sign me-1"></i>
                    <span>{{ __('messages.store_sales_licenses') ?? 'Sales & Licenses' }}</span>
                </a>
                <a href="{{ route('admin.store.reviews') }}" class="btn btn-outline-secondary">
                    <i class="feather-star me-1"></i>
                    <span>{{ __('messages.ratings_and_reviews') ?? 'Ratings & Reviews' }}</span>
                </a>
                <a href="{{ route('store.create') }}" target="_blank" class="btn btn-primary">
                    <i class="feather-plus me-1"></i>
                    <span>{{ __('messages.add_product') ?? 'Add Product' }}</span>
                </a>
            </div>
        </div>
    </section>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-12 fw-semibold text-uppercase d-block">{{ __('messages.total_products') ?? 'Total Products' }}</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ number_format($totalCount) }}</h3>
                    </div>
                    <div class="avatar-text avatar-md bg-soft-primary text-primary rounded-circle">
                        <i class="feather-package fs-18"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-12 fw-semibold text-uppercase d-block">{{ __('messages.active_products') ?? 'Active' }}</span>
                        <h3 class="fw-bold mb-0 mt-1 text-success">{{ number_format($activeCount) }}</h3>
                    </div>
                    <div class="avatar-text avatar-md bg-soft-success text-success rounded-circle">
                        <i class="feather-check-circle fs-18"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-12 fw-semibold text-uppercase d-block">{{ __('messages.pending_approval') ?? 'Pending Review' }}</span>
                        <h3 class="fw-bold mb-0 mt-1 text-warning">{{ number_format($pendingCount) }}</h3>
                    </div>
                    <div class="avatar-text avatar-md bg-soft-warning text-warning rounded-circle">
                        <i class="feather-clock fs-18"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-12 fw-semibold text-uppercase d-block">{{ __('messages.suspended_products') ?? 'Suspended' }}</span>
                        <h3 class="fw-bold mb-0 mt-1 text-danger">{{ number_format($suspendedCount) }}</h3>
                    </div>
                    <div class="avatar-text avatar-md bg-soft-danger text-danger rounded-circle">
                        <i class="feather-slash fs-18"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="feather-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="feather-alert-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Search & Filters Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.products') }}" class="row g-2 align-items-center">
                {{-- Search Box --}}
                <div class="col-lg-4 col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="feather-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="{{ __('messages.search_store_placeholder') ?? 'Search by title, ID, description, or seller...' }}" value="{{ $search }}">
                    </div>
                </div>

                {{-- Status Filter --}}
                <div class="col-lg-3 col-sm-6">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ ($status ?? 'all') === 'all' ? 'selected' : '' }}>{{ __('messages.all_statuses') ?? 'All Statuses' }}</option>
                        <option value="active" {{ ($status ?? '') === 'active' ? 'selected' : '' }}>{{ __('messages.active_products') ?? 'Active Products' }}</option>
                        <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>{{ __('messages.pending_approval') ?? 'Pending Review' }} ({{ $pendingCount }})</option>
                        <option value="suspended" {{ ($status ?? '') === 'suspended' ? 'selected' : '' }}>{{ __('messages.suspended_products') ?? 'Suspended' }}</option>
                        <option value="free" {{ ($status ?? '') === 'free' ? 'selected' : '' }}>{{ __('messages.free_products') ?? 'Free Products' }}</option>
                        <option value="paid" {{ ($status ?? '') === 'paid' ? 'selected' : '' }}>{{ __('messages.paid_products') ?? 'Paid Products' }}</option>
                    </select>
                </div>

                {{-- Category Filter --}}
                <div class="col-lg-2 col-sm-6">
                    <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="all" {{ ($category ?? 'all') === 'all' ? 'selected' : '' }}>{{ __('messages.all_categories') ?? 'All Categories' }}</option>
                        @foreach($categories as $catKey)
                            <option value="{{ $catKey }}" {{ ($category ?? '') === $catKey ? 'selected' : '' }}>
                                {{ __('messages.' . $catKey) != 'messages.' . $catKey ? __('messages.' . $catKey) : ucfirst($catKey) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- View Mode Toggle & Reset --}}
                <div class="col-lg-3 col-12 d-flex justify-content-lg-end gap-2">
                    <div class="btn-group btn-group-sm">
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" class="btn {{ ($viewMode ?? 'grid') === 'grid' ? 'btn-primary' : 'btn-outline-secondary' }}" title="{{ __('messages.grid_view') ?? 'Grid View' }}">
                            <i class="feather-grid"></i>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'table']) }}" class="btn {{ ($viewMode ?? 'grid') === 'table' ? 'btn-primary' : 'btn-outline-secondary' }}" title="{{ __('messages.table_view') ?? 'Table View' }}">
                            <i class="feather-list"></i>
                        </a>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">
                        {{ __('messages.filter') ?? 'Filter' }}
                    </button>
                    @if($search || ($status && $status !== 'all') || ($category && $category !== 'all'))
                        <a href="{{ route('admin.products', ['view' => $viewMode]) }}" class="btn btn-sm btn-light text-muted" title="{{ __('messages.clear_filters') ?? 'Clear' }}">
                            <i class="feather-x"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if(($viewMode ?? 'grid') === 'table')
        {{-- TABLE VIEW --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3" style="width: 70px;">#ID</th>
                                <th style="width: 80px;">{{ __('messages.img') ?? 'Cover' }}</th>
                                <th>{{ __('messages.name') ?? 'Product Name' }}</th>
                                <th>{{ __('messages.category') ?? 'Category' }}</th>
                                <th>{{ __('messages.price') ?? 'Price' }}</th>
                                <th style="width: 110px;">{{ __('messages.rating') ?? 'Rating' }}</th>
                                <th>{{ __('messages.seller') ?? 'Seller' }}</th>
                                <th>{{ __('messages.status') ?? 'Status' }}</th>
                                <th class="text-end pe-3">{{ __('messages.actions') ?? 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                @php
                                    $isSuspended = $product->is_suspended;
                                    $isPending = $product->is_pending;
                                @endphp
                                <tr>
                                    <td class="ps-3 text-muted fw-bold">#{{ $product->id }}</td>
                                    <td>
                                        <div style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(0,0,0,0.05);">
                                            @if($product->productImage)
                                                <img src="{{ $product->productImage }}" alt="{{ $product->name }}" style="max-width: 100%; max-height: 100%; object-fit: cover;">
                                            @else
                                                <i class="feather-package text-muted"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                            <a href="{{ route('store.show', $product->name) }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                                                {{ $product->name }}
                                            </a>
                                            @if($product->live_demo_url)
                                                <a href="{{ $product->live_demo_url }}" target="_blank" class="badge bg-soft-info text-info text-decoration-none" title="{{ __('messages.live_preview_demo') ?? 'Live Demo' }}">
                                                    <i class="feather-external-link me-1"></i>Demo
                                                </a>
                                            @endif
                                            @if($product->screenshots->count() > 0)
                                                <span class="badge bg-soft-secondary text-secondary" title="{{ $product->screenshots->count() }} {{ __('messages.screenshots') ?? 'Screenshots' }}">
                                                    <i class="feather-image me-1"></i>{{ $product->screenshots->count() }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="fs-12 text-muted text-truncate" style="max-width: 250px;">
                                            {{ \Illuminate\Support\Str::limit($product->o_valuer, 50) }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-primary text-primary">
                                            {{ $product->productCategory ? (__('messages.' . $product->productCategory) != 'messages.' . $product->productCategory ? __('messages.' . $product->productCategory) : ucfirst($product->productCategory)) : __('messages.uncategorized') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($product->o_order > 0)
                                            <span class="text-success fw-bold">{{ number_format((float)$product->productPrice) }} PTS</span>
                                        @else
                                            <span class="badge bg-soft-success text-success">{{ __('messages.free') ?? 'Free' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->reviews_count > 0)
                                            <a href="{{ route('admin.store.reviews', ['product_id' => $product->id]) }}" class="text-decoration-none d-flex align-items-center gap-1">
                                                <span class="text-warning fw-bold"><i class="feather-star fs-12 text-warning"></i> {{ number_format($product->average_rating, 1) }}</span>
                                                <span class="text-muted fs-11">({{ $product->reviews_count }})</span>
                                            </a>
                                        @else
                                            <span class="text-muted fs-12">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->user)
                                            <a href="{{ route('profile.show', $product->user->username) }}" target="_blank" class="text-muted fw-semibold text-decoration-none d-flex align-items-center gap-1">
                                                <i class="feather-user fs-12"></i>
                                                {{ $product->user->username }}
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('messages.unknown') ?? 'Unknown' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($isSuspended)
                                            <span class="badge bg-danger">{{ __('messages.suspended') ?? 'Suspended' }}</span>
                                        @elseif($isPending)
                                            <span class="badge bg-warning text-dark">{{ __('messages.pending_approval') ?? 'Pending Review' }}</span>
                                        @else
                                            <span class="badge bg-success">{{ __('messages.active') ?? 'Active' }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-inline-flex gap-1 align-items-center">
                                            @if($isPending)
                                                <form method="POST" action="{{ route('admin.products.approve', $product->id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="{{ __('messages.approve') ?? 'Approve' }}">
                                                        <i class="feather-check"></i>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="openRejectModal('{{ $product->id }}', '{{ addslashes($product->name) }}')" title="{{ __('messages.reject') ?? 'Reject' }}">
                                                    <i class="feather-x"></i>
                                                </button>
                                            @endif
                                            <a href="{{ route('store.show', $product->name) }}" target="_blank" class="btn btn-sm btn-light" title="{{ __('messages.view') ?? 'View' }}">
                                                <i class="feather-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-light" title="{{ __('messages.edit') ?? 'Edit' }}">
                                                <i class="feather-edit-2"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.products.suspend', $product->id) }}" class="d-inline" onsubmit="return confirm('{{ $isSuspended ? (__('messages.confirm_unsuspend') ?? 'Unsuspend?') : (__('messages.confirm_suspend') ?? 'Suspend and notify owner?') }}')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $isSuspended ? 'btn-soft-success text-success' : 'btn-soft-warning text-warning' }}" title="{{ $isSuspended ? (__('messages.unsuspend') ?? 'Unsuspend') : (__('messages.suspend') ?? 'Suspend') }}">
                                                    <i class="feather-{{ $isSuspended ? 'check-circle' : 'slash' }}"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-soft-danger text-danger" onclick="confirmDelete('{{ $product->id }}', '{{ addslashes($product->name) }}')" title="{{ __('messages.delete') ?? 'Delete' }}">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="feather-package fs-1 d-block mb-2"></i>
                                        {{ __('messages.no_products_found') ?? 'No products found.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        {{-- GRID VIEW --}}
        <div class="row g-4">
            @forelse($products as $product)
                @php
                    $isSuspended = $product->is_suspended;
                    $isPending = $product->is_pending;
                @endphp
                <div class="col-xxl-3 col-lg-4 col-sm-6">
                    <div class="card stretch stretch-full border-0 shadow-sm h-100 {{ $isSuspended ? 'border-start border-danger border-3' : ($isPending ? 'border-start border-warning border-3' : '') }}">
                        <div class="card-body p-0 position-relative" style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa; border-radius: var(--bs-border-radius) var(--bs-border-radius) 0 0; overflow: hidden;">
                            <a href="{{ route('store.show', $product->name) }}" class="w-100 h-100 d-flex align-items-center justify-content-center p-3" target="_blank">
                                @if($product->productImage)
                                    <img src="{{ $product->productImage }}" class="img-fluid rounded" alt="{{ $product->name }}" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                @else
                                    <i class="feather-box fs-1 text-muted"></i>
                                @endif
                            </a>

                            {{-- Status Badges --}}
                            <div class="position-absolute top-0 start-0 m-2 d-flex flex-column gap-1">
                                @if($isSuspended)
                                    <span class="badge bg-danger shadow-sm">{{ __('messages.suspended') ?? 'Suspended' }}</span>
                                @elseif($isPending)
                                    <span class="badge bg-warning text-dark shadow-sm">{{ __('messages.pending_approval') ?? 'Pending Review' }}</span>
                                @endif
                                @if($product->live_demo_url)
                                    <a href="{{ $product->live_demo_url }}" target="_blank" class="badge bg-info text-white shadow-sm text-decoration-none">
                                        <i class="feather-external-link me-1"></i>Demo
                                    </a>
                                @endif
                            </div>

                            @if($product->o_order == 0)
                                <span class="badge bg-success position-absolute top-0 end-0 m-2 shadow-sm">{{ __('messages.free') ?? 'Free' }}</span>
                            @endif
                        </div>
                        <div class="card-footer p-3 d-flex align-items-center justify-content-between bg-white border-top position-relative">
                            <div class="overflow-hidden me-2" style="flex: 1;">
                                <h2 class="fs-14 fw-bold mb-1 text-truncate-1-line" title="{{ $product->name }}">
                                    <a href="{{ route('store.show', $product->name) }}" class="text-dark text-decoration-none" target="_blank">{{ $product->name }}</a>
                                </h2>
                                <small class="fs-11 text-uppercase d-flex flex-wrap gap-1 align-items-center">
                                    <span class="text-muted text-truncate-1-line" style="max-width: 100px;">
                                        {{ $product->productCategory ? (__('messages.' . $product->productCategory) != 'messages.' . $product->productCategory ? __('messages.' . $product->productCategory) : ucfirst($product->productCategory)) : __('messages.uncategorized') }}
                                    </span>
                                    <span class="text-muted">•</span>
                                    @if($product->o_order > 0)
                                        <span class="text-success fw-bold">{{ number_format((float)$product->productPrice) }} PTS</span>
                                    @else
                                        <span class="text-success fw-bold">{{ __('messages.free') ?? 'Free' }}</span>
                                    @endif
                                </small>
                                @if($product->reviews_count > 0)
                                    <div class="fs-11 text-warning mt-1 d-flex align-items-center gap-1">
                                        <i class="feather-star fs-11"></i>
                                        <span class="fw-bold">{{ number_format($product->average_rating, 1) }}</span>
                                        <span class="text-muted">({{ $product->reviews_count }})</span>
                                    </div>
                                @endif
                                <div class="fs-11 text-muted mt-1 text-truncate-1-line">
                                    <i class="feather-user me-1"></i>
                                    @if($product->user)
                                        <a href="{{ route('profile.show', $product->user->username) }}" class="text-muted text-decoration-none" target="_blank">{{ $product->user->username }}</a>
                                    @else
                                        {{ __('messages.unknown') ?? 'Unknown' }}
                                    @endif
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="javascript:void(0)" class="avatar-text avatar-sm bg-soft-primary text-primary" data-bs-toggle="dropdown">
                                    <i class="feather-more-vertical"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    @if($isPending)
                                        <li>
                                            <form method="POST" action="{{ route('admin.products.approve', $product->id) }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-success">
                                                    <i class="feather-check-circle me-3"></i>
                                                    <span>{{ __('messages.approve') ?? 'Approve' }}</span>
                                                </button>
                                            </form>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item text-danger" onclick="openRejectModal('{{ $product->id }}', '{{ addslashes($product->name) }}')">
                                                <i class="feather-x-circle me-3"></i>
                                                <span>{{ __('messages.reject') ?? 'Reject' }}</span>
                                            </a>
                                        </li>
                                        <li class="dropdown-divider"></li>
                                    @endif
                                    <li>
                                        <a href="{{ route('store.show', $product->name) }}" class="dropdown-item" target="_blank">
                                            <i class="feather-eye me-3"></i>
                                            <span>{{ __('messages.view') ?? 'View' }}</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="dropdown-item">
                                            <i class="feather-edit-2 me-3"></i>
                                            <span>{{ __('messages.edit') ?? 'Edit' }}</span>
                                        </a>
                                    </li>
                                    @if($product->user)
                                    <li>
                                        <a href="{{ route('profile.show', $product->user->username) }}" class="dropdown-item" target="_blank">
                                            <i class="feather-user me-3"></i>
                                            <span>{{ __('messages.seller_profile') ?? 'Seller Profile' }}</span>
                                        </a>
                                    </li>
                                    @endif
                                    <li class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('admin.products.suspend', $product->id) }}" onsubmit="return confirm('{{ $isSuspended ? (__('messages.confirm_unsuspend') ?? 'Unsuspend?') : (__('messages.confirm_suspend') ?? 'Suspend and notify owner?') }}')">
                                            @csrf
                                            <button type="submit" class="dropdown-item {{ $isSuspended ? 'text-success' : 'text-warning' }}">
                                                <i class="feather-{{ $isSuspended ? 'check-circle' : 'slash' }} me-3"></i>
                                                <span>{{ $isSuspended ? (__('messages.unsuspend') ?? 'Unsuspend') : (__('messages.suspend') ?? 'Suspend') }}</span>
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item text-danger" onclick="confirmDelete('{{ $product->id }}', '{{ addslashes($product->name) }}')">
                                            <i class="feather-trash-2 me-3"></i>
                                            <span>{{ __('messages.delete') ?? 'Delete' }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card text-center py-5 border-0 shadow-sm">
                        <div class="card-body">
                            <i class="feather-shopping-bag fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="text-muted">{{ __('messages.no_products_found') ?? 'No products found.' }}</h5>
                            @if($search || ($status && $status !== 'all') || ($category && $category !== 'all'))
                                <a href="{{ route('admin.products') }}" class="btn btn-sm btn-outline-primary mt-2">
                                    {{ __('messages.clear_filters') ?? 'Clear Filters' }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    @endif

    @if($products->hasPages())
        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    @endif

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <form id="rejectProductForm" method="POST" action="">
                    @csrf
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold text-danger">
                            <i class="feather-x-circle me-2"></i>{{ __('messages.reject_product') ?? 'Reject Product' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="mb-3">{{ __('messages.confirm_reject_product') ?? 'Are you sure you want to reject this product?' }} <strong id="rejectProductName"></strong></p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.rejection_reason') ?? 'Reason for Rejection' }} <small class="text-muted">({{ __('messages.optional') ?? 'Optional' }})</small></label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="{{ __('messages.rejection_reason_optional') ?? 'Explain why this product was rejected so the seller can fix it...' }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') ?? 'Cancel' }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('messages.reject') ?? 'Reject' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        function openRejectModal(id, name) {
            var form = document.getElementById('rejectProductForm');
            form.action = '{{ url("admin/products") }}/' + id + '/reject';
            document.getElementById('rejectProductName').textContent = name;
            var modal = new bootstrap.Modal(document.getElementById('rejectProductModal'));
            modal.show();
        }

        function confirmDelete(id, name) {
            var msg = "{{ __('messages.delete_product_confirm') ?? 'Are you sure you want to delete this product:' }} " + name + "?";
            if (confirm(msg)) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.products.delete") }}';
                form.style.display = 'none';

                var csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);

                var method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';
                form.appendChild(method);

                var inputId = document.createElement('input');
                inputId.type = 'hidden';
                inputId.name = 'id';
                inputId.value = id;
                form.appendChild(inputId);

                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</div>
@endsection
