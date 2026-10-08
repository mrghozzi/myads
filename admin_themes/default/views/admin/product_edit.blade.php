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
    .file-version-header:hover { background-color: rgba(128, 128, 128, 0.1); transition: background 0.2s; }
    .collapse-icon { transition: transform 0.2s ease; }
    .collapse-icon.rotated { transform: rotate(90deg); }
    [data-bs-theme="dark"] .bg-white { background-color: transparent !important; }
    .file-version-content { background-color: rgba(128, 128, 128, 0.03); }
</style>

<script>
function toggleFileVersion(id) {
    const el = document.getElementById('file-version-' + id);
    const icon = document.getElementById('icon-' + id);
    if (el.style.display === 'none') {
        el.style.display = 'block';
        icon.classList.add('rotated');
    } else {
        el.style.display = 'none';
        icon.classList.remove('rotated');
    }
}

function updateCharCount(id) {
    const el = document.getElementById('file-desc-' + id);
    const counter = document.getElementById('file-desc-count-' + id);
    if (el && counter) {
        counter.textContent = el.value.length.toLocaleString() + ' ' + (@json(__('messages.chars') ?? 'chars'));
    }
}

function saveFileVersion(fileId) {
    const btn = document.getElementById('btn-save-file-' + fileId);
    const alertBox = document.getElementById('file-alert-' + fileId);
    const vnbr = document.getElementById('file-vnbr-' + fileId).value.trim();
    const link = document.getElementById('file-link-' + fileId).value.trim();
    const desc = document.getElementById('file-desc-' + fileId).value;

    alertBox.innerHTML = '';
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>' + @json(__('messages.saving') ?? 'Saving...');

    fetch(@json(url('admin/products/' . $product->id . '/files')) + '/' + fileId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': @json(csrf_token()),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            vnbr: vnbr,
            link: link,
            desc: desc
        })
    })
    .then(async (response) => {
        const data = await response.json();
        if (!response.ok) {
            let errorMsg = data.message || @json(__('messages.error_occurred') ?? 'An error occurred.');
            if (data.errors && Array.isArray(data.errors)) {
                errorMsg = data.errors.join('<br>');
            } else if (data.errors && typeof data.errors === 'object') {
                errorMsg = Object.values(data.errors).flat().join('<br>');
            }
            throw new Error(errorMsg);
        }
        return data;
    })
    .then((data) => {
        alertBox.innerHTML = '<div class="alert alert-success alert-dismissible fade show py-2 px-3 fs-12 mb-2"><i class="feather-check-circle me-1"></i> ' + (data.message || @json(__('messages.file_updated') ?? 'File updated successfully.')) + '<button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button></div>';
        
        const headerName = document.getElementById('version-header-name-' + fileId);
        if (headerName) headerName.textContent = vnbr;
        
        const testLink = document.getElementById('file-test-link-' + fileId);
        if (testLink) {
            testLink.href = link.startsWith('http://') || link.startsWith('https://') ? link : @json(url('/')) + '/' + link;
        }

        btn.disabled = false;
        btn.innerHTML = '<i class="feather-check me-1"></i>' + @json(__('messages.saved') ?? 'Saved');
        setTimeout(() => {
            btn.innerHTML = originalHtml;
        }, 2000);
    })
    .catch((err) => {
        alertBox.innerHTML = '<div class="alert alert-danger alert-dismissible fade show py-2 px-3 fs-12 mb-2"><i class="feather-alert-circle me-1"></i> ' + err.message + '<button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button></div>';
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}

function deleteFileVersion(fileId) {
    if (!confirm(@json(__('messages.confirm_delete_version') ?? 'Are you sure you want to delete this file version?'))) {
        return;
    }

    const alertBox = document.getElementById('file-alert-' + fileId);
    alertBox.innerHTML = '';

    fetch(@json(url('admin/products/' . $product->id . '/files')) + '/' + fileId, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': @json(csrf_token()),
            'Accept': 'application/json'
        }
    })
    .then(async (response) => {
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || @json(__('messages.error_occurred') ?? 'An error occurred.'));
        }
        return data;
    })
    .then((data) => {
        const row = document.getElementById('file-version-row-' + fileId);
        if (row) {
            row.style.transition = 'opacity 0.3s ease';
            row.style.opacity = '0';
            setTimeout(() => {
                row.remove();
                const container = document.getElementById('file-versions-container');
                const badge = document.getElementById('file-versions-count');
                if (container && container.querySelectorAll('[id^="file-version-row-"]').length === 0) {
                    container.innerHTML = '<div class="p-4 text-center text-muted" id="no-files-placeholder"><i class="feather-package fs-3 d-block mb-2"></i>' + @json(__('messages.no_files') ?? 'No file versions yet.') + '</div>';
                }
                if (badge) {
                    const currentCount = parseInt(badge.textContent) || 1;
                    badge.textContent = Math.max(0, currentCount - 1);
                }
            }, 300);
        }
    })
    .catch((err) => {
        alertBox.innerHTML = '<div class="alert alert-danger alert-dismissible fade show py-2 px-3 fs-12 mb-2"><i class="feather-alert-circle me-1"></i> ' + err.message + '<button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button></div>';
    });
}
</script>

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

<div class="row g-4">
    {{-- Edit Form --}}
    <div class="col-lg-8">
        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" id="product-main-form">
            @csrf
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
                        <div id="store-category-container">
                            <select name="cat_s" id="cat_s" class="form-select mb-3" onchange="updateSubCategories(this.value)">
                                <option value="">-- {{ __('messages.select') ?? 'Select' }} --</option>
                                @foreach($storeCategories as $cat)
                                    <option value="{{ $cat->name }}" {{ $selectedStoreCategory === $cat->name ? 'selected' : '' }}>
                                        {{ __('messages.' . $cat->name) ?? $cat->name }}
                                    </option>
                                @endforeach
                            </select>

                            <div id="sub-category-wrapper">
                                @if($selectedStoreCategory === \App\Support\StoreCategoryCatalog::PLUGINS || $selectedStoreCategory === \App\Support\StoreCategoryCatalog::THEMES)
                                    <label class="form-label fs-12 text-muted">{{ __('messages.script') ?? 'Script' }}</label>
                                    <select name="sc_cat" id="sc_cat" class="form-select">
                                        <option value="">-- {{ __('messages.select') ?? 'Select' }} --</option>
                                        @foreach($scriptProductOptions as $id => $name)
                                            <option value="{{ $id }}" {{ (string)$selectedStoreSubcategory === (string)$id ? 'selected' : '' }}>
                                                {{ $name }}
                                            </option>
                                        @endforeach
                                        <option value="others" {{ $selectedStoreSubcategory === 'others' ? 'selected' : '' }}>{{ __('messages.others') ?? 'Others' }}</option>
                                    </select>
                                @elseif($selectedStoreCategory === \App\Support\StoreCategoryCatalog::SCRIPT)
                                    <label class="form-label fs-12 text-muted">{{ __('messages.subcategories') ?? 'Sub Category' }}</label>
                                    <select name="sc_cat" id="sc_cat" class="form-select">
                                        <option value="">-- {{ __('messages.select') ?? 'Select' }} --</option>
                                        @foreach($scriptCategoryOptions as $scriptCategory)
                                            <option value="{{ $scriptCategory->name }}" {{ (string)$selectedStoreSubcategory === (string)$scriptCategory->name ? 'selected' : '' }}>
                                                {{ __('messages.' . $scriptCategory->name) ?? $scriptCategory->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif(in_array($selectedStoreCategory, [\App\Support\StoreCategoryCatalog::GRAPHICS, \App\Support\StoreCategoryCatalog::AUDIO, \App\Support\StoreCategoryCatalog::VIDEO, \App\Support\StoreCategoryCatalog::EBOOKS, \App\Support\StoreCategoryCatalog::SOFTWARE, \App\Support\StoreCategoryCatalog::COURSES]))
                                    <label class="form-label fs-12 text-muted">{{ __('messages.subcategories') ?? 'Sub Category' }}</label>
                                    <select name="sc_cat" id="sc_cat" class="form-select">
                                        <option value="">-- {{ __('messages.select') ?? 'Select' }} --</option>
                                        @foreach($genericCategoryOptions as $genericCategory)
                                            <option value="{{ $genericCategory->name }}" {{ (string)$selectedStoreSubcategory === (string)$genericCategory->name ? 'selected' : '' }}>
                                                {{ __('messages.' . $genericCategory->name) ?? $genericCategory->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                    </div>

                    <script>
                    function updateSubCategories(category) {
                        const wrapper = document.getElementById('sub-category-wrapper');
                        wrapper.innerHTML = '<div class="text-center p-2"><div class="spinner-border spinner-border-sm text-primary"></div></div>';
                        
                        fetch("{{ route('store.categories') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: 'cat_s=' + encodeURIComponent(category) + '&sc_cat={{ $selectedStoreSubcategory }}'
                        })
                        .then(response => response.text())
                        .then(html => {
                            // Extract only the part we need or wrap it
                            const temp = document.createElement('div');
                            temp.innerHTML = html;
                            
                            // The partial returns multiple form-select divs
                            // We want to extract anything that isn't the first select (cat_s)
                            const selects = temp.querySelectorAll('select');
                            let subHtml = '';
                            selects.forEach(select => {
                                if (select.id !== 'cat_s') {
                                    const label = select.parentElement.querySelector('label');
                                    subHtml += '<div class="mt-2">';
                                    if (label) subHtml += '<label class="form-label fs-12 text-muted">' + label.innerHTML + '</label>';
                                    select.className = 'form-select'; // Use Bootstrap class
                                    subHtml += select.outerHTML;
                                    subHtml += '</div>';
                                }
                            });
                            wrapper.innerHTML = subHtml;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            wrapper.innerHTML = '';
                        });
                    }
                    </script>
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

                    {{-- Media & Previews --}}
                    <div class="card border border-light bg-light p-3 mb-3 rounded">
                        <h6 class="fw-semibold mb-3 d-flex align-items-center gap-2">
                            <i class="feather-video text-primary"></i>
                            <span>{{ __('messages.media_and_demo') ?? 'Media & Live Previews' }}</span>
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-semibold text-muted">{{ __('messages.live_preview_url') ?? 'Live Preview / Demo URL' }}</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="feather-external-link"></i></span>
                                    <input type="url" name="demo_url" class="form-control" value="{{ old('demo_url', $demoUrl) }}" placeholder="https://demo.example.com">
                                    @if($demoUrl)
                                        <a href="{{ $demoUrl }}" target="_blank" class="btn btn-outline-secondary">{{ __('messages.view') ?? 'Open' }}</a>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-semibold text-muted">{{ __('messages.video_preview_url') ?? 'Video Preview URL' }}</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text"><i class="feather-video"></i></span>
                                    <input type="url" name="video_url" class="form-control" value="{{ old('video_url', $videoUrl) }}" placeholder="https://youtube.com/watch?v=...">
                                    @if($videoUrl)
                                        <a href="{{ $videoUrl }}" target="_blank" class="btn btn-outline-secondary">{{ __('messages.view') ?? 'Open' }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Screenshots Gallery --}}
                        <div class="mt-3">
                            <label class="form-label fs-12 fw-semibold text-muted">{{ __('messages.screenshots') ?? 'Screenshots' }}</label>
                            @if(isset($screenshots) && $screenshots->isNotEmpty())
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @foreach($screenshots as $shot)
                                        <div class="position-relative border rounded p-1 bg-white" style="width: 100px;">
                                            <a href="{{ $shot->url }}" target="_blank">
                                                <img src="{{ $shot->url }}" class="rounded" style="width: 100%; height: 60px; object-fit: cover;" alt="screenshot">
                                            </a>
                                            <div class="form-check form-check-sm mt-1">
                                                <input class="form-check-input" type="checkbox" name="remove_screenshot_ids[]" value="{{ $shot->id }}" id="del_shot_{{ $shot->id }}">
                                                <label class="form-check-label fs-11 text-danger" for="del_shot_{{ $shot->id }}">
                                                    {{ __('messages.delete') ?? 'Delete' }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="feather-plus"></i></span>
                                <input type="text" name="new_screenshot_url" class="form-control" placeholder="{{ __('messages.screenshot_url') ?? 'Add new screenshot URL (https://...)' }}">
                            </div>
                        </div>
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
                        <div class="col-12">
                            <label class="form-label">{{ __('messages.version_changelog') ?? 'Version Changelog / Notes' }} <small class="text-muted">({{ __('messages.optional') ?? 'Optional' }})</small></label>
                            <textarea name="new_version_desc" class="form-control" rows="2" placeholder="{{ __('messages.version_changelog_placeholder') ?? 'Enter release notes / changelog for this new version...' }}"></textarea>
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
        </form>
    </div>

    {{-- Sidebar: File Versions --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span>{{ __('messages.file_versions') ?? 'File Versions' }}</span>
                <span class="badge bg-soft-primary text-primary" id="file-versions-count">{{ $files->count() }}</span>
            </div>
            <div class="card-body p-0" id="file-versions-container">
                @forelse($files as $file)
                    <div class="border-bottom border-light" id="file-version-row-{{ $file->id }}">
                        <div class="p-3 cursor-pointer d-flex justify-content-between align-items-center file-version-header" onclick="toggleFileVersion({{ $file->id }})">
                            <div class="fw-semibold fs-13 d-flex align-items-center">
                                <i class="feather-chevron-right me-2 text-muted collapse-icon" id="icon-{{ $file->id }}"></i>
                                <span id="version-header-name-{{ $file->id }}">{{ $file->name }}</span>
                            </div>
                            <span class="badge bg-soft-primary text-primary fs-11" title="{{ __('messages.downloads') ?? 'Downloads' }}">
                                <i class="feather-download me-1"></i>{{ $file->shortLink->clik ?? 0 }}
                            </span>
                        </div>
                        
                        <div id="file-version-{{ $file->id }}" style="display: none;" class="file-version-content">
                            <div class="p-3 border-top border-light">
                                <div id="file-alert-{{ $file->id }}"></div>

                                <div class="mb-3">
                                    <label class="form-label fs-11 fw-bold text-uppercase text-muted mb-1">{{ __('messages.version_number') ?? 'Version Number' }}</label>
                                    <input type="text" id="file-vnbr-{{ $file->id }}" class="form-control form-control-sm mb-2" value="{{ $file->name }}">
                                    
                                    <label class="form-label fs-11 fw-bold text-uppercase text-muted mb-1">{{ __('messages.file_link') ?? 'File Link' }}</label>
                                    <input type="text" id="file-link-{{ $file->id }}" class="form-control form-control-sm mb-2" value="{{ $file->o_mode }}">
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label fs-11 fw-bold text-uppercase text-muted mb-0">{{ __('messages.changelog_notes') ?? 'Changelog / Notes' }}</label>
                                        <span class="text-muted fs-11" id="file-desc-count-{{ $file->id }}">{{ number_format(mb_strlen($file->o_valuer ?? '')) }} {{ __('messages.chars') ?? 'chars' }}</span>
                                    </div>
                                    <textarea id="file-desc-{{ $file->id }}" class="form-control form-control-sm mb-3 font-monospace fs-12" rows="4" oninput="updateCharCount({{ $file->id }})">{{ $file->o_valuer }}</textarea>
                                </div>
                                
                                <div class="d-flex align-items-center justify-content-between pt-2 border-top border-light">
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-primary btn-sm fs-11" id="btn-save-file-{{ $file->id }}" onclick="saveFileVersion({{ $file->id }})">
                                            <i class="feather-save me-1"></i>{{ __('messages.save') ?? 'Save Version' }}
                                        </button>
                                        <button type="button" class="btn btn-soft-danger btn-sm fs-11" onclick="deleteFileVersion({{ $file->id }})" title="{{ __('messages.delete') ?? 'Delete Version' }}">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </div>
                                    @php $isUrl = filter_var($file->o_mode, FILTER_VALIDATE_URL); @endphp
                                    <a href="{{ $isUrl ? $file->o_mode : url($file->o_mode) }}" target="_blank" class="btn btn-soft-info btn-sm fs-11" id="file-test-link-{{ $file->id }}">
                                        <i class="feather-external-link me-1"></i>{{ __('messages.test_link') ?? 'Test Link' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center text-muted" id="no-files-placeholder">
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

{{-- Ratings & Reviews Moderation --}}
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <div class="fw-semibold d-flex align-items-center gap-2">
            <i class="feather-star text-warning"></i>
            <span>{{ __('messages.ratings_and_reviews') ?? 'Ratings & Reviews' }}</span>
            <span class="badge bg-soft-primary text-primary">{{ isset($reviews) ? $reviews->count() : 0 }}</span>
        </div>
        @if(isset($reviews) && $reviews->count() > 0)
            <div class="text-warning fw-bold fs-13 d-flex align-items-center gap-1">
                <i class="feather-star fill-warning"></i>
                <span>{{ number_format($product->average_rating, 1) }} / 5.0</span>
            </div>
        @endif
    </div>
    <div class="card-body p-0">
        @if(isset($reviews) && $reviews->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light fs-12">
                        <tr>
                            <th class="ps-3" style="width: 70px;">#ID</th>
                            <th>{{ __('messages.buyer') ?? 'Buyer' }}</th>
                            <th>{{ __('messages.rating') ?? 'Rating' }}</th>
                            <th>{{ __('messages.verified_buyer') ?? 'Verified' }}</th>
                            <th>{{ __('messages.review_content') ?? 'Comment' }}</th>
                            <th>{{ __('messages.date') ?? 'Date' }}</th>
                            <th class="text-end pe-3">{{ __('messages.actions') ?? 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $rev)
                            <tr>
                                <td class="ps-3 text-muted">#{{ $rev->id }}</td>
                                <td>
                                    @if($rev->user)
                                        <a href="{{ route('profile.show', $rev->user->username) }}" target="_blank" class="d-flex align-items-center gap-2 text-decoration-none text-dark">
                                            <img src="{{ $rev->user->avatarUrl() }}" class="rounded-circle" width="24" height="24" alt="">
                                            <span class="fw-semibold fs-13">{{ $rev->user->username }}</span>
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('messages.unknown') ?? 'Unknown' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-soft-warning text-warning fw-bold">
                                        ★ {{ $rev->rating }}/5
                                    </span>
                                </td>
                                <td>
                                    @if($rev->is_verified_buyer)
                                        <span class="badge bg-soft-success text-success">
                                            <i class="feather-check-circle me-1"></i>{{ __('messages.verified_buyer') ?? 'Verified' }}
                                        </span>
                                    @else
                                        <span class="text-muted fs-11">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($rev->title)
                                        <div class="fw-semibold fs-12">{{ $rev->title }}</div>
                                    @endif
                                    <div class="fs-12 text-muted text-truncate" style="max-width: 300px;">
                                        {{ $rev->comment }}
                                    </div>
                                </td>
                                <td class="fs-12 text-muted">
                                    {{ $rev->created_at ? $rev->created_at->format('Y-m-d') : '-' }}
                                </td>
                                <td class="text-end pe-3">
                                    <form method="POST" action="{{ route('admin.store.reviews.delete', $rev->id) }}" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete_review') ?? 'Delete this review?' }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger text-danger" title="{{ __('messages.delete') ?? 'Delete Review' }}">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-center text-muted">
                <i class="feather-star fs-3 d-block mb-2 text-muted"></i>
                {{ __('messages.no_reviews_yet') ?? 'No reviews yet for this product.' }}
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
</div>
@endsection
