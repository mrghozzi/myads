@extends('admin::layouts.admin')

@section('title', __('messages.ratings_and_reviews') ?? 'Store Ratings & Reviews')
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.products') }}">{{ __('messages.store') ?? 'Store' }}</a></li>
                <li>{{ __('messages.ratings_and_reviews') ?? 'Ratings & Reviews' }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.store') ?? 'Store' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.ratings_and_reviews') ?? 'Ratings & Reviews' }}</h1>
            <p class="admin-hero__copy">{{ __('messages.store_reviews_desc') ?? 'Monitor customer reviews, product star ratings, and verified buyer feedback across the store.' }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.products') }}" class="btn btn-outline-secondary">
                    <i class="feather-package me-1"></i>
                    <span>{{ __('messages.manage_products') ?? 'Products' }}</span>
                </a>
                <a href="{{ route('admin.store.sales') }}" class="btn btn-outline-secondary">
                    <i class="feather-dollar-sign me-1"></i>
                    <span>{{ __('messages.store_sales_licenses') ?? 'Sales & Licenses' }}</span>
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
                        <span class="text-muted fs-12 fw-semibold text-uppercase d-block">{{ __('messages.total_reviews') ?? 'Total Reviews' }}</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ number_format($totalReviews) }}</h3>
                    </div>
                    <div class="avatar-text avatar-md bg-soft-primary text-primary rounded-circle">
                        <i class="feather-message-square fs-18"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-12 fw-semibold text-uppercase d-block">{{ __('messages.average_rating') ?? 'Average Rating' }}</span>
                        <h3 class="fw-bold mb-0 mt-1 text-warning">⭐ {{ number_format($avgRating, 1) }}</h3>
                    </div>
                    <div class="avatar-text avatar-md bg-soft-warning text-warning rounded-circle">
                        <i class="feather-star fs-18"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fs-12 fw-semibold text-uppercase d-block">{{ __('messages.verified_reviews') ?? 'Verified Buyers' }}</span>
                        <h3 class="fw-bold mb-0 mt-1 text-success">{{ number_format($verifiedCount) }}</h3>
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
                        <span class="text-muted fs-12 fw-semibold text-uppercase d-block">{{ __('messages.five_star_reviews') ?? '5-Star Reviews' }}</span>
                        <h3 class="fw-bold mb-0 mt-1 text-info">{{ number_format($fiveStarCount) }}</h3>
                    </div>
                    <div class="avatar-text avatar-md bg-soft-info text-info rounded-circle">
                        <i class="feather-award fs-18"></i>
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

    {{-- Search Filter Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.store.reviews') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="feather-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="{{ __('messages.search_reviews_placeholder') ?? 'Search by comment, product or reviewer...' }}" value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <select name="rating" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">{{ __('messages.all_ratings') ?? 'All Ratings' }}</option>
                        <option value="5" {{ ($rating ?? '') === '5' ? 'selected' : '' }}>5 ★★★★★</option>
                        <option value="4" {{ ($rating ?? '') === '4' ? 'selected' : '' }}>4 ★★★★</option>
                        <option value="3" {{ ($rating ?? '') === '3' ? 'selected' : '' }}>3 ★★★</option>
                        <option value="2" {{ ($rating ?? '') === '2' ? 'selected' : '' }}>2 ★★</option>
                        <option value="1" {{ ($rating ?? '') === '1' ? 'selected' : '' }}>1 ★</option>
                    </select>
                </div>
                <div class="col-md-2 col-sm-6">
                    <select name="verified" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">{{ __('messages.all') ?? 'All Reviewers' }}</option>
                        <option value="1" {{ ($verified ?? '') === '1' ? 'selected' : '' }}>{{ __('messages.verified_only') ?? 'Verified Only' }}</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2 justify-content-md-end">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="feather-filter me-1"></i>{{ __('messages.filter') ?? 'Filter' }}
                    </button>
                    @if($search || $rating || $verified || $productId)
                        <a href="{{ route('admin.store.reviews') }}" class="btn btn-sm btn-light text-muted">
                            <i class="feather-x me-1"></i>{{ __('messages.clear_filters') ?? 'Clear' }}
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Reviews Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width: 70px;">#ID</th>
                            <th>{{ __('messages.product') ?? 'Product' }}</th>
                            <th>{{ __('messages.buyer') ?? 'Reviewer' }}</th>
                            <th style="width: 120px;">{{ __('messages.rating') ?? 'Rating' }}</th>
                            <th>{{ __('messages.verified_buyer') ?? 'Verified' }}</th>
                            <th>{{ __('messages.review_content') ?? 'Comment' }}</th>
                            <th>{{ __('messages.date') ?? 'Date' }}</th>
                            <th class="text-end pe-3">{{ __('messages.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $rev)
                            <tr>
                                <td class="ps-3 text-muted fw-bold">#{{ $rev->id }}</td>
                                <td>
                                    @if($rev->product)
                                        <a href="{{ route('store.show', $rev->product->name) }}" target="_blank" class="fw-bold text-dark text-decoration-none d-flex align-items-center gap-2">
                                            @if($rev->product->productImage)
                                                <img src="{{ $rev->product->productImage }}" class="rounded" width="30" height="30" style="object-fit: cover;" alt="">
                                            @else
                                                <div class="avatar-text avatar-xs bg-soft-primary text-primary rounded">
                                                    <i class="feather-package"></i>
                                                </div>
                                            @endif
                                            <span>{{ $rev->product->name }}</span>
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('messages.unknown') ?? 'Unknown Product' }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rev->user)
                                        <a href="{{ route('profile.show', $rev->user->username) }}" target="_blank" class="d-flex align-items-center gap-2 text-decoration-none text-dark">
                                            @if($rev->user->img)
                                                <img src="{{ asset('upload/' . $rev->user->img) }}" class="rounded-circle" width="26" height="26" alt="">
                                            @else
                                                <div class="avatar-text avatar-xs bg-soft-primary text-primary rounded-circle">
                                                    {{ strtoupper(substr($rev->user->username, 0, 1)) }}
                                                </div>
                                            @endif
                                            <span class="fw-semibold fs-13">{{ $rev->user->username }}</span>
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('messages.unknown') ?? 'Unknown' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-soft-warning text-warning fw-bold fs-12">
                                        ★ {{ $rev->rating }}/5
                                    </span>
                                </td>
                                <td>
                                    @if($rev->is_verified_buyer)
                                        <span class="badge bg-soft-success text-success">
                                            <i class="feather-check-circle me-1"></i>{{ __('messages.verified_buyer') ?? 'Verified' }}
                                        </span>
                                    @else
                                        <span class="text-muted fs-12">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rev->title)
                                        <div class="fw-semibold fs-13">{{ $rev->title }}</div>
                                    @endif
                                    <div class="fs-12 text-muted text-truncate" style="max-width: 350px;">
                                        {{ $rev->comment }}
                                    </div>
                                </td>
                                <td class="fs-12 text-muted">
                                    {{ $rev->created_at ? $rev->created_at->format('Y-m-d H:i') : '-' }}
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-inline-flex gap-1 align-items-center">
                                        @if($rev->product)
                                            <a href="{{ route('store.show', $rev->product->name) }}" target="_blank" class="btn btn-sm btn-light" title="{{ __('messages.view_product') ?? 'View Product' }}">
                                                <i class="feather-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $rev->product->id) }}" class="btn btn-sm btn-light" title="{{ __('messages.edit_product') ?? 'Edit Product' }}">
                                                <i class="feather-edit-2"></i>
                                            </a>
                                        @endif
                                        <form method="POST" action="{{ route('admin.store.reviews.delete', $rev->id) }}" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete_review') ?? 'Are you sure you want to delete this review?' }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger text-danger" title="{{ __('messages.delete') ?? 'Delete Review' }}">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="feather-star fs-1 d-block mb-2 text-muted"></i>
                                    {{ __('messages.no_reviews_found') ?? 'No store reviews found.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($reviews->hasPages())
                <div class="p-3 border-top">
                    {{ $reviews->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
