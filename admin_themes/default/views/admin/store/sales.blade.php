@extends('admin::layouts.admin')

@section('title', __('messages.store_sales_licenses') ?? 'Store Sales & Licenses')
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.products') }}">{{ __('messages.store') ?? 'Store' }}</a></li>
                <li>{{ __('messages.store_sales_licenses') ?? 'Sales & Licenses' }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.store') ?? 'Store' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.store_sales_licenses') ?? 'Sales & Licenses' }}</h1>
            <p class="admin-hero__copy">{{ __('messages.sales_and_licenses_desc') ?? 'Monitor store purchase transactions, issued digital product licenses, and points flow.' }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.products') }}" class="btn btn-outline-secondary">
                    <i class="feather-package me-1"></i>
                    <span>{{ __('messages.manage_products') ?? 'Products' }}</span>
                </a>
                <a href="{{ route('admin.store.discounts.index') }}" class="btn btn-primary">
                    <i class="feather-tag me-1"></i>
                    <span>{{ __('messages.discount_codes') ?? 'Discount Codes' }}</span>
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
                        <span class="text-muted fs-12 fw-semibold text-uppercase d-block">{{ __('messages.total_sales') ?? 'Total Sales' }}</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ number_format($totalSales) }}</h3>
                    </div>
                    <div class="avatar-text avatar-md bg-soft-primary text-primary rounded-circle">
                        <i class="feather-shopping-cart fs-18"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-12 fw-semibold text-uppercase d-block">{{ __('messages.total_pts_volume') ?? 'Total Points Volume' }}</span>
                        <h3 class="fw-bold mb-0 mt-1 text-success">{{ number_format($totalPtsVolume) }} PTS</h3>
                    </div>
                    <div class="avatar-text avatar-md bg-soft-success text-success rounded-circle">
                        <i class="feather-dollar-sign fs-18"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-12 fw-semibold text-uppercase d-block">{{ __('messages.unique_buyers') ?? 'Unique Buyers' }}</span>
                        <h3 class="fw-bold mb-0 mt-1 text-info">{{ number_format($uniqueBuyers) }}</h3>
                    </div>
                    <div class="avatar-text avatar-md bg-soft-info text-info rounded-circle">
                        <i class="feather-users fs-18"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-12 fw-semibold text-uppercase d-block">{{ __('messages.coupon_uses') ?? 'Discounts Redeemed' }}</span>
                        <h3 class="fw-bold mb-0 mt-1 text-warning">{{ number_format($totalDiscountsRedeemed) }}</h3>
                        <small class="text-muted fs-11">({{ number_format($totalPointsSaved) }} PTS saved)</small>
                    </div>
                    <div class="avatar-text avatar-md bg-soft-warning text-warning rounded-circle">
                        <i class="feather-tag fs-18"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search Filter Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.store.sales') }}" class="row g-2 align-items-center">
                <div class="col-md-8">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="feather-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="{{ __('messages.search_sales_placeholder') ?? 'Search by license key, product, buyer or seller username...' }}" value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-4 d-flex gap-2 justify-content-md-end">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="feather-filter me-1"></i>{{ __('messages.filter') ?? 'Filter' }}
                    </button>
                    @if($search || $productId)
                        <a href="{{ route('admin.store.sales') }}" class="btn btn-sm btn-light text-muted">
                            <i class="feather-x me-1"></i>{{ __('messages.clear_filters') ?? 'Clear' }}
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Sales Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 70px;">#ID</th>
                            <th>{{ __('messages.product') ?? 'Product' }}</th>
                            <th>{{ __('messages.buyer') ?? 'Buyer' }}</th>
                            <th>{{ __('messages.seller') ?? 'Seller' }}</th>
                            <th>{{ __('messages.license_key') ?? 'License Key' }}</th>
                            <th>{{ __('messages.price') ?? 'Price' }}</th>
                            <th>{{ __('messages.date') ?? 'Date' }}</th>
                            <th class="text-end pe-3">{{ __('messages.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td class="ps-3 text-muted fw-bold">#{{ $sale->license_id }}</td>
                                <td>
                                    <a href="{{ route('store.show', $sale->product_name) }}" target="_blank" class="fw-bold text-dark text-decoration-none">
                                        <i class="feather-box me-1 text-primary"></i>
                                        {{ $sale->product_name }}
                                    </a>
                                </td>
                                <td>
                                    @if($sale->buyer_username)
                                        <a href="{{ route('profile.show', $sale->buyer_username) }}" target="_blank" class="d-flex align-items-center gap-2 text-decoration-none text-dark">
                                            @if($sale->buyer_avatar)
                                                <img src="{{ asset('upload/' . $sale->buyer_avatar) }}" class="rounded-circle" width="26" height="26" alt="">
                                            @else
                                                <div class="avatar-text avatar-xs bg-soft-primary text-primary rounded-circle">
                                                    {{ strtoupper(substr($sale->buyer_username, 0, 1)) }}
                                                </div>
                                            @endif
                                            <span class="fw-semibold fs-13">{{ $sale->buyer_username }}</span>
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('messages.unknown') ?? 'Unknown' }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($sale->seller_username)
                                        <a href="{{ route('profile.show', $sale->seller_username) }}" target="_blank" class="text-muted fw-semibold text-decoration-none fs-13">
                                            <i class="feather-user fs-11 me-1"></i>{{ $sale->seller_username }}
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('messages.unknown') ?? 'Unknown' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-soft-primary text-primary font-monospace px-2 py-1 fs-12 border border-primary-subtle" style="letter-spacing: 0.5px;">
                                        {{ $sale->license_key }}
                                    </span>
                                </td>
                                <td>
                                    @if($sale->product_price > 0)
                                        <span class="text-success fw-bold">{{ number_format((float)$sale->product_price) }} PTS</span>
                                    @else
                                        <span class="badge bg-soft-success text-success">{{ __('messages.free') ?? 'Free' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted fs-12">
                                        {{ \Carbon\Carbon::parse($sale->purchased_at)->format('Y-m-d H:i') }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('store.show', $sale->product_name) }}" target="_blank" class="btn btn-sm btn-light" title="{{ __('messages.view_product') ?? 'View Product' }}">
                                            <i class="feather-eye"></i>
                                        </a>
                                        @if($sale->buyer_username)
                                            <a href="{{ route('profile.show', $sale->buyer_username) }}" target="_blank" class="btn btn-sm btn-light" title="{{ __('messages.view_buyer') ?? 'View Buyer' }}">
                                                <i class="feather-user"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="feather-shopping-bag fs-1 d-block mb-2 text-muted"></i>
                                    {{ __('messages.no_sales_found') ?? 'No sales or licenses found.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($sales->hasPages())
        <div class="mt-4">
            {{ $sales->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
