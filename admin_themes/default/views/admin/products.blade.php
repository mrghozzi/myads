@extends('admin::layouts.admin')

@section('title', __('messages.manage_products') ?? 'Manage Products')
@section('admin_shell_header_mode', 'hidden')

@section('content')
    <div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.products') ?? 'Products' }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.products') ?? 'Products' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.manage_products') ?? 'Manage Products' }}</h1>
            <p class="admin-hero__copy">{{ __('messages.total_products') ?? 'Total Products' }}: {{ $products->total() }}</p>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($products as $product)
            @php
                $isSuspended = \App\Models\Option::where('o_type', 'store_status')
                    ->where('o_parent', $product->id)
                    ->where('name', 'suspended')
                    ->exists();
            @endphp
            <div class="col-xxl-3 col-lg-4 col-sm-6">
                <div class="card stretch stretch-full border-0 shadow-sm h-100 {{ $isSuspended ? 'border-start border-danger border-3' : '' }}">
                    <div class="card-body p-0" style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa; border-radius: var(--bs-border-radius) var(--bs-border-radius) 0 0;">
                        <a href="{{ route('store.show', $product->name) }}" class="w-100 h-100 d-flex align-items-center justify-content-center p-3" target="_blank">
                            @if($product->productImage)
                                <img src="{{ $product->productImage }}" class="img-fluid rounded" alt="{{ $product->name }}" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                            @else
                                <i class="feather-box fs-1 text-muted"></i>
                            @endif
                        </a>
                        @if($isSuspended)
                            <span class="badge bg-danger position-absolute top-0 start-0 m-2">{{ __('messages.suspended') ?? 'Suspended' }}</span>
                        @endif
                    </div>
                    <div class="card-footer p-4 d-flex align-items-center justify-content-between bg-white border-top position-relative">
                        <div class="overflow-hidden me-3" style="flex: 1;">
                            <h2 class="fs-14 fw-bold mb-1 text-truncate-1-line" title="{{ $product->name }}">
                                <a href="{{ route('store.show', $product->name) }}" class="text-dark" target="_blank">{{ $product->name }}</a>
                            </h2>
                            <small class="fs-11 text-uppercase d-flex flex-wrap gap-1 align-items-center">
                                <span class="text-muted text-truncate-1-line" style="max-width: 100px;">
                                    {{ $product->productCategory ? $product->productCategory : (__('messages.uncategorized') ?? 'Uncategorized') }}
                                </span>
                                <span class="text-muted">•</span>
                                <span class="text-success fw-bold">{{ number_format((float)$product->productPrice) }} {{ __('messages.point') ?? 'Points' }}</span>
                            </small>
                            <div class="fs-11 text-muted mt-1 text-truncate-1-line">
                                <i class="feather-user me-1"></i>
                                @if($product->user)
                                    <a href="{{ route('profile.show', $product->user->username) }}" class="text-muted">{{ $product->user->username }}</a>
                                @else
                                    {{ __('messages.unknown') ?? 'Unknown' }}
                                @endif
                            </div>
                        </div>
                        <div class="dropdown">
                            <a href="javascript:void(0)" class="avatar-text avatar-sm bg-soft-primary text-primary" data-bs-toggle="dropdown">
                                <i class="feather-more-vertical"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
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
                <div class="card text-center py-5">
                    <div class="card-body">
                        <i class="feather-shopping-bag fs-1 text-muted mb-3 d-block"></i>
                        <h5 class="text-muted">{{ __('messages.no_products_found') ?? 'No products found.' }}</h5>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if($products->hasPages())
        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    @endif

    <!-- Delete Confirmation -->
    <script>
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
