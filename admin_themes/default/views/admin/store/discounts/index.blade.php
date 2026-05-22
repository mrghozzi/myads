@extends('admin::layouts.admin')

@section('title', __('messages.discount_codes') ?? 'Store Discount Codes')
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content d-flex justify-content-between align-items-center flex-wrap w-100">
            <div>
                <ul class="admin-breadcrumb">
                    <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                    <li>{{ __('messages.store') ?? 'Store' }}</li>
                    <li>{{ __('messages.discount_codes') ?? 'Discount Codes' }}</li>
                </ul>
                <div class="admin-hero__eyebrow">{{ __('messages.store') ?? 'Store' }}</div>
                <h1 class="admin-hero__title">{{ __('messages.discount_codes') ?? 'Discount Codes' }}</h1>
                <p class="admin-hero__copy">{{ __('messages.total_coupons') ?? 'Total Coupons' }}: {{ $discounts->total() }}</p>
            </div>
            <div>
                <a href="{{ route('admin.store.discounts.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="feather-plus"></i>
                    <span>{{ __('messages.create_discount') ?? 'Create Coupon' }}</span>
                </a>
            </div>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong><i class="feather-check-circle me-2"></i></strong>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">{{ __('messages.all_discount_codes') ?? 'All Discount Codes' }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">{{ __('messages.coupon_name') ?? 'Name' }}</th>
                            <th>{{ __('messages.coupon_code') ?? 'Code' }}</th>
                            <th>{{ __('messages.coupon_value') ?? 'Value' }}</th>
                            <th>{{ __('messages.creator') ?? 'Creator' }}</th>
                            <th>{{ __('messages.applies_to') ?? 'Applies To' }}</th>
                            <th>{{ __('messages.coupon_uses') ?? 'Uses' }}</th>
                            <th>{{ __('messages.coupon_dates') ?? 'Validity' }}</th>
                            <th>{{ __('messages.status') ?? 'Status' }}</th>
                            <th class="text-end pe-4">{{ __('messages.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($discounts as $discount)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $discount->name }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-soft-primary text-primary border border-primary-subtle font-monospace px-2 py-1" style="font-size: 13px;">
                                        {{ $discount->code }}
                                    </span>
                                </td>
                                <td>
                                    @if($discount->discount_type === 'percent')
                                        <span class="badge bg-soft-success text-success px-2 py-1" style="font-size: 13px;">{{ $discount->discount_value }}%</span>
                                    @else
                                        <span class="badge bg-soft-info text-info px-2 py-1" style="font-size: 13px;">{{ $discount->discount_value }} PTS</span>
                                    @endif
                                </td>
                                <td>
                                    @if($discount->user)
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="feather-user text-muted"></i>
                                            <a href="{{ route('profile.show', $discount->user->username) }}" target="_blank" class="text-muted fw-semibold">{{ $discount->user->username }}</a>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center gap-2 text-danger">
                                            <i class="feather-shield"></i>
                                            <span class="fw-bold">{{ __('messages.admin') ?? 'Admin / Global' }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($discount->applies_to === 'all')
                                        <span class="text-muted"><i class="feather-globe me-1"></i>{{ __('messages.all_products') ?? 'All Store Products' }}</span>
                                    @elseif($discount->applies_to === 'product')
                                        @php
                                            $prod = \App\Models\Product::withoutGlobalScope('store')->find($discount->target_value);
                                        @endphp
                                        @if($prod)
                                            <a href="{{ route('store.show', $prod->name) }}" target="_blank" class="text-primary fw-semibold">
                                                <i class="feather-box me-1"></i>{{ $prod->name }}
                                            </a>
                                        @else
                                            <span class="text-danger"><i class="feather-alert-triangle me-1"></i>{{ __('messages.unknown_product') ?? 'Deleted Product' }}</span>
                                        @endif
                                    @elseif($discount->applies_to === 'category')
                                        <span class="badge bg-light text-dark border"><i class="feather-folder me-1"></i>{{ ucfirst($discount->target_value) }}</span>
                                    @elseif($discount->applies_to === 'seller')
                                        @php
                                            $seller = \App\Models\User::find($discount->target_value);
                                        @endphp
                                        @if($seller)
                                            <a href="{{ route('profile.show', $seller->username) }}" target="_blank" class="text-muted">
                                                <i class="feather-shopping-bag me-1"></i>{{ $seller->username }}
                                            </a>
                                        @else
                                            <span class="text-danger">{{ __('messages.unknown_seller') ?? 'Deleted Seller' }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">{{ $discount->applies_to }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold">{{ $discount->uses }}</span>
                                    @if($discount->max_uses)
                                        <span class="text-muted">/ {{ $discount->max_uses }}</span>
                                    @else
                                        <span class="text-muted">/ &infin;</span>
                                    @endif
                                </td>
                                <td>
                                    @if($discount->start_date || $discount->end_date)
                                        <div class="fs-12 text-muted">
                                            @if($discount->start_date)
                                                <div>{{ __('messages.from') ?? 'From' }}: {{ $discount->start_date->format('Y-m-d H:i') }}</div>
                                            @endif
                                            @if($discount->end_date)
                                                <div>{{ __('messages.to') ?? 'To' }}: {{ $discount->end_date->format('Y-m-d H:i') }}</div>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">&infin;&nbsp;{{ __('messages.always_valid') ?? 'Always valid' }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$discount->is_active)
                                        <span class="badge bg-danger">{{ __('messages.inactive') ?? 'Inactive' }}</span>
                                    @elseif($discount->end_date && $discount->end_date->isPast())
                                        <span class="badge bg-secondary">{{ __('messages.expired') ?? 'Expired' }}</span>
                                    @elseif($discount->max_uses && $discount->uses >= $discount->max_uses)
                                        <span class="badge bg-warning text-dark">{{ __('messages.limit_reached') ?? 'Limit Reached' }}</span>
                                    @else
                                        <span class="badge bg-success">{{ __('messages.active') ?? 'Active' }}</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.store.discounts.edit', $discount->id) }}" class="btn btn-sm btn-icon btn-soft-info" title="{{ __('messages.edit') }}">
                                            <i class="feather-edit-2"></i>
                                        </a>
                                        <a href="{{ route('admin.store.discounts.destroy', $discount->id) }}" onclick="return confirm('{{ __('messages.confirm_delete') ?? 'Are you sure you want to delete this?' }}')" class="btn btn-sm btn-icon btn-soft-danger" title="{{ __('messages.delete') }}">
                                            <i class="feather-trash-2"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="feather-tag fs-1 mb-3 d-block"></i>
                                    {{ __('messages.no_discounts_found') ?? 'No discount codes found.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($discounts->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $discounts->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
