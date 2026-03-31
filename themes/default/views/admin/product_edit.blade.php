@extends('theme::layouts.admin')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bolder mb-1">
            <a href="{{ route('admin.products') }}" class="text-muted me-2"><i class="feather-arrow-left"></i></a>
            {{ __('messages.edit_product') ?? 'Edit Product' }}: <span class="text-primary">{{ $product->name }}</span>
        </h4>
        <div class="fs-12 text-muted">
            ID: {{ $product->id }}
            @if($isSuspended)
                &nbsp;<span class="badge bg-danger">{{ __('messages.suspended') ?? 'Suspended' }}</span>
            @else
                &nbsp;<span class="badge bg-success">{{ __('messages.active') ?? 'Active' }}</span>
            @endif
        </div>
    </div>
    <div class="d-flex gap-2">
        {{-- Suspend / Unsuspend --}}
        <form method="POST" action="{{ route('admin.products.suspend', $product->id) }}" onsubmit="return confirm('{{ $isSuspended ? (__('messages.confirm_unsuspend') ?? 'Unsuspend this product?') : (__('messages.confirm_suspend') ?? 'Suspend this product and notify the owner?') }}')">
            @csrf
            <button type="submit" class="btn {{ $isSuspended ? 'btn-success' : 'btn-warning' }} btn-sm">
                <i class="feather-{{ $isSuspended ? 'check-circle' : 'slash' }} me-1"></i>
                {{ $isSuspended ? (__('messages.unsuspend') ?? 'Unsuspend') : (__('messages.suspend') ?? 'Suspend') }}
            </button>
        </form>
        {{-- View Product --}}
        <a href="{{ route('store.show', $product->name) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="feather-eye me-1"></i>{{ __('messages.view') ?? 'View' }}
        </a>
    </div>
</div>

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

<div class="row g-4">
    {{-- Edit Form --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">{{ __('messages.product_details') ?? 'Product Details' }}</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.products.update', $product->id) }}">
                    @csrf
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
                    {{-- Category --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.categories') ?? 'Category' }}</label>
                        <select name="cat_s" class="form-select">
                            <option value="">-- {{ __('messages.select') ?? 'Select' }} --</option>
                            @foreach($storeCategories as $cat)
                                <option value="{{ $cat->name }}" {{ ($typeOption && $typeOption->name === $cat->name) ? 'selected' : '' }}>
                                    {{ __($cat->name) }}
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
                                <img src="{{ $product->o_mode }}" style="max-height:80px;max-width:200px;border-radius:6px;border:1px solid #dee2e6;" alt="cover">
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
                </form>
            </div>
        </div>
    </div>

    {{-- Sidebar: File Versions --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">{{ __('messages.file_versions') ?? 'File Versions' }}</div>
            <div class="card-body p-0">
                @forelse($files as $file)
                    @php
                        $fileHash = hash('crc32', $file->o_mode . $file->id);
                        $isUrl = filter_var($file->o_mode, FILTER_VALIDATE_URL);
                    @endphp
                    <div class="d-flex align-items-center gap-3 p-3 border-bottom">
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-semibold fs-13">{{ $file->name }}</div>
                            <div class="fs-11 text-muted text-truncate">
                                @if($isUrl)
                                    <i class="feather-link me-1"></i><a href="{{ $file->o_mode }}" target="_blank" class="text-muted">{{ $file->o_mode }}</a>
                                @else
                                    <i class="feather-file me-1"></i><a href="{{ url($file->o_mode) }}" target="_blank" class="text-muted">{{ $file->o_mode }}</a>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('store.download.hash', $fileHash) }}" class="btn btn-sm btn-outline-primary" title="{{ __('messages.download') ?? 'Download' }}">
                            <i class="feather-download"></i>
                        </a>
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
                <img src="{{ $product->user->img ?? theme_asset('img/profile.png') }}" class="rounded-circle" width="40" height="40" alt="">
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
@endsection
