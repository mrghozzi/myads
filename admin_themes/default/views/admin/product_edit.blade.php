@extends('admin::layouts.admin')

@section('title', __('messages.edit_product') ?? 'Edit Product')
@section('admin_shell_header_mode', 'hidden')

@section('content')
@php
    $selectedStoreCategory = \App\Support\StoreCategoryCatalog::normalize(optional($typeOption)->name);
@endphp

<div class="admin-page">
<style>
    .cursor-pointer { cursor: pointer; }
    .bg-light-soft:hover { background-color: rgba(0,0,0,0.03); transition: background 0.2s; }
</style>

<section class="admin-hero">
    <div class="admin-hero__content">
        <ul class="admin-breadcrumb">
            <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li><a href="{{ route('admin.products') }}">{{ __('messages.products') ?? 'Products' }}</a></li>
            <li>{{ __('messages.edit') }}</li>
        </ul>
        <div class="admin-hero__eyebrow">{{ __('messages.products') ?? 'Products' }}</div>
        <h1 class="admin-hero__title">{{ __('messages.edit_product') ?? 'Edit Product' }}</h1>
        <p class="admin-hero__copy">{{ $product->name }} / ID: {{ $product->id }}</p>
    </div>
    <div class="admin-hero__actions">
        <div class="admin-toolbar-card">
            <div class="admin-toolbar-row w-100">
                <form method="POST" action="{{ route('admin.products.suspend', $product->id) }}" onsubmit="return confirm('{{ $isSuspended ? (__('messages.confirm_unsuspend') ?? 'Unsuspend this product?') : (__('messages.confirm_suspend') ?? 'Suspend this product and notify the owner?') }}')">
                    @csrf
                    <button type="submit" class="btn {{ $isSuspended ? 'btn-success' : 'btn-warning' }} btn-sm">
                        <i class="feather-{{ $isSuspended ? 'check-circle' : 'slash' }} me-1"></i>
                        {{ $isSuspended ? (__('messages.unsuspend') ?? 'Unsuspend') : (__('messages.suspend') ?? 'Suspend') }}
                    </button>
                </form>
                <a href="{{ route('store.show', $product->name) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                    <i class="feather-eye me-1"></i>{{ __('messages.view') ?? 'View' }}
                </a>
            </div>
        </div>
        <div class="admin-summary-grid w-100">
            <div class="admin-summary-card">
                <span class="admin-summary-label">{{ __('messages.status') }}</span>
                <span class="admin-summary-value">{{ $isSuspended ? (__('messages.suspended') ?? 'Suspended') : (__('messages.active') ?? 'Active') }}</span>
            </div>
        </div>
    </div>
</section>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('admin.products.update', $product->id) }}">
    @csrf
    <div class="row g-4">
        {{-- Edit Form --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white fw-semibold">{{ __('messages.product_details') ?? 'Product Details' }}</div>
                <div class="card-body">
                    {{-- Product Name --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.titer') ?? 'Name' }} <span class="text-danger">*</span></label>
                        <input type="text" name="pname" class="form-control" value="{{ old('pname', $product->name) }}" required minlength="3" maxlength="35">
                        <small class="text-muted">{{ __('messages.nameonly') ?? 'Letters, numbers, hyphens and underscores only' }}</small>
                    </div>
                    {{-- Description --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.desc') ?? 'Description' }} <span class="text-danger">*</span></label>
                        <textarea name="desc" class="form-control" rows="3" required minlength="10" maxlength="2400">{{ old('desc', $product->o_valuer) }}</textarea>
                    </div>
                    {{-- Price --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.price_pts') ?? 'Price (Points)' }} <span class="text-danger">*</span></label>
                        <input type="number" name="pts" class="form-control" value="{{ old('pts', $product->o_order) }}" required min="0" max="999999">
                    </div>
                    {{-- Seller --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.seller_id') ?? 'Seller ID' }} <span class="text-danger">*</span></label>
                        <input type="number" name="owner_id" class="form-control" value="{{ old('owner_id', $product->o_parent) }}" required>
                        <small class="text-muted">{{ __('messages.seller_id_help') ?? 'Enter the numeric ID of the user who should own this product.' }}</small>
                    </div>
                    {{-- Category --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.category') ?? 'Category' }}</label>
                        <select name="cat_s" class="form-select">
                            <option value="">-- {{ __('messages.select') ?? 'Select' }} --</option>
                            @foreach($storeCategories as $cat)
                                <option value="{{ $cat->name }}" {{ $selectedStoreCategory === $cat->name ? 'selected' : '' }}>
                                    {{ __('messages.' . $cat->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Cover Image --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.img') ?? 'Cover Image URL' }}</label>
                        <input type="text" name="img" class="form-control" value="{{ old('img', $product->o_mode) }}" placeholder="https://...">
                        @if($product->o_mode)
                            <div class="mt-2">
                                <img src="{{ $product->product_image }}" style="max-height:80px;max-width:200px;border-radius:6px;border:1px solid #dee2e6;" alt="cover">
                            </div>
                        @endif
                    </div>
                    {{-- Body Text (Forum Topic) --}}
                    @if($topic)
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.topic') ?? 'Product Body / Description Text' }}</label>
                        <div class="stackedit-tools mb-2">
                            <button type="button" class="btn btn-sm btn-outline-primary open-stackedit" data-target="#admin_product_txt">
                                <i class="feather-edit me-1"></i> {{ __('messages.edit_with_stackedit') ?? 'Edit with StackEdit' }}
                            </button>
                        </div>
                        <textarea name="txt" id="admin_product_txt" rows="10" class="form-control">{{ old('txt', $topic->txt) }}</textarea>
                    </div>
                    @endif

                    <hr>
                    <h6 class="fw-semibold mb-3">{{ __('messages.add_new_version') ?? 'Add New File Version' }} <small class="text-muted fw-normal">({{ __('messages.optional') ?? 'Optional' }})</small></h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ __('messages.Version_nbr') ?? 'Version Number' }}</label>
                            <input type="text" name="vnbr" class="form-control" placeholder="v2.0" minlength="2" maxlength="12" pattern="^[-a-zA-Z0-9.]+$">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">{{ __('messages.file') ?? 'File Link / URL' }}</label>
                            <input type="text" name="linkzip" class="form-control" placeholder="upload/file.zip or https://...">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-1"></i> {{ __('messages.save') ?? 'Save Changes' }}
                        </button>
                        <a href="{{ route('admin.products') }}" class="btn btn-outline-secondary ms-2">{{ __('messages.cancel') ?? 'Cancel' }}</a>
                    </div>
            </div>
        </div>
    </div>

    {{-- Sidebar: File Versions --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">{{ __('messages.file_versions') ?? 'File Versions' }}</div>
            <div class="card-body p-0">
                @forelse($files as $file)
                    <div class="border-bottom">
                        <div class="p-3 bg-light-soft d-flex justify-content-between align-items-center cursor-pointer" data-bs-toggle="collapse" data-bs-target="#file-version-{{ $file->id }}" aria-expanded="false">
                            <div class="d-flex align-items-center gap-2">
                                <i class="feather-chevron-down fs-12 text-muted"></i>
                                <span class="fw-semibold fs-13">{{ $file->name }}</span>
                            </div>
                            <span class="text-muted fs-11">
                                <i class="feather-download me-1"></i>{{ $file->shortLink->clik ?? 0 }}
                            </span>
                        </div>
                        
                        <div class="collapse" id="file-version-{{ $file->id }}">
                            <div class="p-3 pt-0 bg-light-soft">
                                <div class="mb-3 border-top pt-3">
                                    <div class="mb-3">
                                        <label class="fs-11 fw-semibold text-muted mb-1">{{ __('messages.version_number') ?? 'Version Number' }}</label>
                                        <input type="text" name="existing_files[{{ $file->id }}][vnbr]" class="form-control form-control-sm mb-2" value="{{ $file->name }}" placeholder="v2.0">
                                        
                                        <label class="fs-11 fw-semibold text-muted mb-1">{{ __('messages.file_link') ?? 'File Link' }}</label>
                                        <input type="text" name="existing_files[{{ $file->id }}][link]" class="form-control form-control-sm mb-2" value="{{ $file->o_mode }}" placeholder="URL">
                                        
                                        <label class="fs-11 fw-semibold text-muted mb-1">{{ __('messages.description') ?? 'Description' }}</label>
                                        <textarea name="existing_files[{{ $file->id }}][desc]" class="form-control form-control-sm" rows="2" placeholder="{{ __('messages.version_description') ?? 'Version Description' }}">{{ $file->o_valuer }}</textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        @php $isUrl = filter_var($file->o_mode, FILTER_VALIDATE_URL); @endphp
                                        <a href="{{ $isUrl ? $file->o_mode : url($file->o_mode) }}" target="_blank" class="btn btn-link btn-sm p-0 fs-11 text-decoration-none">
                                            <i class="feather-external-link me-1"></i>{{ __('messages.test_link') ?? 'Test Link' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted">
                        <i class="feather-package fs-3 d-block mb-2"></i>
                        {{ __('messages.no_files') ?? 'No file versions yet.' }}
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Owner Info --}}
        @if($product->user)
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white fw-semibold">{{ __('messages.seller') ?? 'Seller' }}</div>
            <div class="card-body d-flex align-items-center gap-3">
                <img src="{{ $product->user->avatarUrl() }}" class="rounded-circle" width="40" height="40" alt="">
                <div>
                    <div class="fw-semibold">{{ $product->user->username }}</div>
                    <a href="{{ route('admin.users.edit', $product->user->id) }}" class="fs-11 text-muted text-decoration-none">
                        <i class="feather-edit-2 me-1"></i>{{ __('messages.edit_user') ?? 'Edit User' }}
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
</form>

@if($topic)
<script src="https://unpkg.com/stackedit-js@1.0.7/docs/lib/stackedit.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stackedit = new Stackedit();
    const btn = document.querySelector('.open-stackedit[data-target="#admin_product_txt"]');
    if (btn) {
        btn.addEventListener('click', function() {
            const textarea = document.getElementById('admin_product_txt');
            const articleName = '{{ $product->name }}';
            
            stackedit.openFile({
                name: articleName,
                content: {
                    text: textarea.value
                }
            });

            const adjustIframe = () => {
                const iframe = document.querySelector('iframe[src*="stackedit.io"]');
                if (iframe) {
                    const header = document.querySelector('.header, .nxl-header');
                    if (header) {
                        const headerHeight = header.offsetHeight;
                        iframe.style.top = headerHeight + 'px';
                        iframe.style.height = `calc(100% - ${headerHeight}px)`;
                    } else {
                        iframe.style.top = '80px';
                        iframe.style.height = 'calc(100% - 80px)';
                    }
                } else {
                    setTimeout(adjustIframe, 50);
                }
            };
            adjustIframe();

            stackedit.off('fileChange');
            stackedit.on('fileChange', (file) => {
                textarea.value = file.content.text;
            });
        });
    }
});
</script>
@endif
</div>
@endsection
