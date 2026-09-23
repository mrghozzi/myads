@extends('admin::layouts.admin')

@section('title', __('messages.edit_banner'))

@section('content')
@php
    $bannerSizes = \App\Support\BannerSizeCatalog::ordered();
@endphp

<div class="admin-page">
    <!-- Hero Header -->
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}"><i class="feather-home me-1"></i>{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.ads') }}">{{ __('messages.ads') }}</a></li>
                <li><a href="{{ route('admin.banners') }}">{{ __('messages.bannads') }}</a></li>
                <li>{{ __('messages.edit') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">
                <span class="badge bg-soft-primary text-primary px-2 py-1"><i class="feather-image me-1"></i>{{ __('messages.bannads') }}</span>
            </div>
            <h1 class="admin-hero__title d-flex align-items-center gap-2">
                <i class="feather-edit-3 text-primary"></i>
                {{ __('messages.edit_banner') }}
            </h1>
            <p class="admin-hero__copy">#{{ $banner->id }} &bull; {{ $banner->name }}</p>
        </div>

        <div class="admin-hero__actions">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.banners') }}" class="btn btn-light d-inline-flex align-items-center gap-2">
                    <i class="feather-arrow-left"></i>
                    <span>{{ __('messages.back') ?? 'Back' }}</span>
                </a>
                <a href="{{ $banner->url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                    <i class="feather-external-link"></i>
                    <span>{{ __('messages.open_link') }}</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Split Grid Layout -->
    <div class="row g-4">
        <!-- Left: Edit Form -->
        <div class="col-lg-7">
            <div class="card stretch stretch-full shadow-sm">
                <div class="card-header border-bottom bg-transparent py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                        <i class="feather-sliders text-primary"></i>
                        {{ __('messages.banner_details') ?? 'Banner Details & Targeting' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="editBannerForm" action="{{ route('admin.banners.update', $banner->id) }}" method="POST" class="row g-3">
                        @csrf
                        
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ $banner->name }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.target_url') ?? 'Target URL' }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="feather-link"></i></span>
                                <input type="url" class="form-control" name="url" value="{{ $banner->url }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.size') }} <span class="text-danger">*</span></label>
                            <select class="form-select" name="px" required id="bannerSizeSelect">
                                @foreach($bannerSizes as $size)
                                    <option value="{{ $size['value'] }}" {{ $banner->px == $size['value'] ? 'selected' : '' }}>
                                        {{ $size['label'] }} ({{ $size['value'] }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.status') }} <span class="text-danger">*</span></label>
                            <select class="form-select" name="statu" required>
                                <option value="1" {{ $banner->statu == 1 ? 'selected' : '' }}>{{ __('messages.active') }} (ON)</option>
                                <option value="2" {{ $banner->statu == 2 ? 'selected' : '' }}>{{ __('messages.paused') }} (OFF)</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.image_url') ?? 'Image URL' }} (Version A) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="feather-image"></i></span>
                                <input type="text" class="form-control" name="img" id="imgInputA" value="{{ $banner->img }}" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.image_url') ?? 'Image URL' }} (Version B - A/B Test)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="feather-layers"></i></span>
                                <input type="text" class="form-control" name="img_b" id="imgInputB" value="{{ $banner->img_b }}" placeholder="https://...">
                            </div>
                            <small class="text-muted">{{ __('messages.ab_test_hint') ?? 'Optional: Provide a second image for traffic split optimization.' }}</small>
                        </div>

                        <!-- Targeting Section -->
                        <div class="col-12 mt-4 pt-3 border-top">
                            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2 text-dark">
                                <i class="feather-crosshair text-primary"></i>
                                {{ __('messages.targeting') ?? 'Audience Targeting' }}
                            </h6>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.target_countries') }}</label>
                            <input type="text" name="countries" class="form-control" value="{{ old('countries', $targetCountries) }}" placeholder="{{ __('messages.smart_form_countries_placeholder') }}">
                            <small class="text-muted">{{ __('messages.smart_form_target_countries_help') }}</small>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark mb-2">{{ __('messages.target_devices') }}</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($deviceOptions as $value => $label)
                                    <label class="btn btn-sm btn-outline-light text-dark border d-inline-flex align-items-center gap-2">
                                        <input type="checkbox" name="devices[]" value="{{ $value }}" class="form-check-input mt-0" {{ in_array($value, old('devices', $selectedDevices), true) ? 'checked' : '' }}>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.banners') }}" class="btn btn-light">{{ __('messages.cancel') ?? 'Cancel' }}</a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4" id="saveBannerBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="saveSpinner"></span>
                                <i class="feather-save" id="saveIcon"></i>
                                <span>{{ __('messages.save_changes') ?? 'Save Changes' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: Live Preview Panel -->
        <div class="col-lg-5">
            <div class="card stretch stretch-full shadow-sm sticky-top" style="top: 100px;">
                <div class="card-header border-bottom bg-transparent py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                        <i class="feather-monitor text-primary"></i>
                        {{ __('messages.live_preview') ?? 'Live Banner Preview' }}
                    </h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-soft-primary text-primary fw-bold">Version A</span>
                            <span class="small text-muted">
                                <i class="feather-eye me-1"></i>{{ number_format($banner->vu_a ?? $banner->vu) }}
                                &bull; <i class="feather-mouse-pointer me-1"></i>{{ number_format($banner->clik_a ?? $banner->clik) }}
                            </span>
                        </div>
                        <div class="p-3 bg-light rounded-3 d-inline-block mw-100 border">
                            <img id="livePreviewA" src="{{ $banner->img }}" alt="" class="img-fluid rounded shadow-sm" style="max-height: 240px;" onerror="this.src='{{ asset('themes/default/assets/images/placeholder.jpg') }}';">
                        </div>
                    </div>

                    <div id="previewContainerB" class="{{ $banner->img_b ? '' : 'd-none' }}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-soft-info text-info fw-bold">Version B (A/B Test)</span>
                            <span class="small text-muted">
                                <i class="feather-eye me-1"></i>{{ number_format($banner->vu_b) }}
                                &bull; <i class="feather-mouse-pointer me-1"></i>{{ number_format($banner->clik_b) }}
                            </span>
                        </div>
                        <div class="p-3 bg-light rounded-3 d-inline-block mw-100 border">
                            <img id="livePreviewB" src="{{ $banner->img_b }}" alt="" class="img-fluid rounded shadow-sm" style="max-height: 240px;" onerror="this.src='{{ asset('themes/default/assets/images/placeholder.jpg') }}';">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imgInputA = document.getElementById('imgInputA');
    const imgInputB = document.getElementById('imgInputB');
    const livePreviewA = document.getElementById('livePreviewA');
    const livePreviewB = document.getElementById('livePreviewB');
    const previewContainerB = document.getElementById('previewContainerB');
    const editForm = document.getElementById('editBannerForm');
    const saveBtn = document.getElementById('saveBannerBtn');
    const saveSpinner = document.getElementById('saveSpinner');
    const saveIcon = document.getElementById('saveIcon');

    // Live preview updates
    imgInputA.addEventListener('input', function() {
        if (this.value.trim()) {
            livePreviewA.src = this.value.trim();
        }
    });

    imgInputB.addEventListener('input', function() {
        if (this.value.trim()) {
            livePreviewB.src = this.value.trim();
            previewContainerB.classList.remove('d-none');
        } else {
            previewContainerB.classList.add('d-none');
        }
    });

    // Toast helper
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed shadow-lg`;
        toast.style.top = '24px';
        toast.style.right = '24px';
        toast.style.zIndex = '99999';
        toast.style.minWidth = '300px';
        toast.role = 'alert';
        toast.innerHTML = `
            <div class="d-flex align-items-center gap-2">
                <i class="feather-${type === 'success' ? 'check-circle' : 'alert-circle'} fs-5"></i>
                <div class="fw-semibold">${message}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // Ajax Form Submit
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();

        saveBtn.disabled = true;
        saveSpinner.classList.remove('d-none');
        saveIcon.classList.add('d-none');

        const formData = new FormData(editForm);

        fetch(editForm.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            saveBtn.disabled = false;
            saveSpinner.classList.add('d-none');
            saveIcon.classList.remove('d-none');

            if (data.success) {
                showToast(data.message || @json(__('messages.banner_updated_successfully')), 'success');
            } else {
                showToast(data.message || @json(__('messages.operation_failed') ?? 'Operation failed.'), 'error');
            }
        })
        .catch(err => {
            console.error('Save error:', err);
            saveBtn.disabled = false;
            saveSpinner.classList.add('d-none');
            saveIcon.classList.remove('d-none');
            showToast(@json(__('messages.network_error') ?? 'Network error.'), 'error');
        });
    });
});
</script>
@endpush
