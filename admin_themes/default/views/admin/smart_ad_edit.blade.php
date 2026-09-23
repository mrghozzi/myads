@extends('admin::layouts.admin')

@section('title', __('messages.smart_edit_title', ['id' => $smartAd->id]))

@section('content')
@php
    $ctr = $smartAd->impressions > 0 ? round(($smartAd->clicks / $smartAd->impressions) * 100, 2) : 0;
@endphp

<div class="admin-page">
    <!-- Hero Header -->
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}"><i class="feather-home me-1"></i>{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.ads') }}">{{ __('messages.ads') }}</a></li>
                <li><a href="{{ route('admin.smart_ads') }}">{{ __('messages.smart_ads') }}</a></li>
                <li>{{ __('messages.edit') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">
                <span class="badge bg-soft-primary text-primary px-2 py-1"><i class="feather-cpu me-1"></i>{{ __('messages.smart_ads') }}</span>
            </div>
            <h1 class="admin-hero__title d-flex align-items-center gap-2">
                <i class="feather-edit-3 text-primary"></i>
                {{ __('messages.smart_edit_title', ['id' => $smartAd->id]) }}
            </h1>
            <p class="admin-hero__copy">{{ $smartAd->displayTitle() }} &bull; {{ $smartAd->user?->username ?? __('messages.unknown') }}</p>

            <div class="admin-stat-strip mt-3">
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-eye me-1 text-warning"></i>{{ __('messages.impressions') }}</span>
                    <span class="admin-stat-value text-warning">{{ number_format($smartAd->impressions) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-mouse-pointer me-1 text-primary"></i>{{ __('messages.clicks') }}</span>
                    <span class="admin-stat-value text-primary">{{ number_format($smartAd->clicks) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-percent me-1 text-success"></i>{{ __('messages.ctr') }}</span>
                    <span class="admin-stat-value text-success">{{ $ctr }}%</span>
                </div>
            </div>
        </div>

        <div class="admin-hero__actions">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.smart_ads') }}" class="btn btn-light d-inline-flex align-items-center gap-2">
                    <i class="feather-arrow-left"></i>
                    <span>{{ __('messages.back') ?? 'Back' }}</span>
                </a>
                <a href="{{ $smartAd->landing_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-primary d-inline-flex align-items-center gap-2">
                    <i class="feather-external-link"></i>
                    <span>{{ __('messages.open_link') }}</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Split Grid Layout -->
    <div class="row g-4">
        <!-- Left: Form Controls -->
        <div class="col-lg-7">
            <div class="card stretch stretch-full shadow-sm">
                <div class="card-header border-bottom bg-transparent py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                        <i class="feather-sliders text-primary"></i>
                        {{ __('messages.smart_admin_details') }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="editSmartAdForm" action="{{ route('admin.smart_ads.update', $smartAd->id) }}" method="POST" class="row g-3">
                        @csrf
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.smart_form_landing_url') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="feather-link"></i></span>
                                <input type="url" name="landing_url" id="inputLandingUrl" class="form-control" value="{{ old('landing_url', $smartAd->landing_url) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.smart_form_headline_override') }}</label>
                            <input type="text" name="headline_override" id="inputHeadline" class="form-control" value="{{ old('headline_override', $smartAd->headline_override) }}" placeholder="{{ $smartAd->source_title ?: 'Custom headline...' }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.smart_form_image_override') }}</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="feather-image"></i></span>
                                <input type="text" name="image" id="inputImage" class="form-control" value="{{ old('image', $smartAd->image) }}" placeholder="https://...">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.smart_form_description_override') }}</label>
                            <textarea name="description_override" id="inputDescription" rows="3" class="form-control" placeholder="{{ $smartAd->source_description ?: 'Custom description...' }}">{{ old('description_override', $smartAd->description_override) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.status') }} <span class="text-danger">*</span></label>
                            <select name="statu" class="form-select">
                                <option value="1" {{ (int) old('statu', $smartAd->statu) === 1 ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                                <option value="0" {{ (int) old('statu', $smartAd->statu) === 0 ? 'selected' : '' }}>{{ __('messages.paused') }}</option>
                                <option value="2" {{ (int) old('statu', $smartAd->statu) === 2 ? 'selected' : '' }}>{{ __('messages.blocked') }}</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.target_countries') }}</label>
                            <input type="text" name="countries" class="form-control" value="{{ old('countries', $targetCountries) }}" placeholder="US, CA, SA, EG...">
                        </div>

                        <div class="col-12">
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

                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">{{ __('messages.smart_form_manual_keywords') }}</label>
                            <textarea name="manual_keywords" rows="3" class="form-control" placeholder="seo, traffic, marketing...">{{ old('manual_keywords', $smartAd->manual_keywords) }}</textarea>
                            <small class="text-muted">{{ __('messages.smart_form_manual_keywords_help') ?? 'Separate keywords with commas for AI context matching.' }}</small>
                        </div>

                        <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.smart_ads') }}" class="btn btn-light">{{ __('messages.cancel') ?? 'Cancel' }}</a>
                            <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4" id="saveSmartAdBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="saveSmartAdSpinner"></span>
                                <i class="feather-save" id="saveSmartAdIcon"></i>
                                <span>{{ __('messages.save_changes') ?? 'Save Changes' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: Live Smart Ad Preview -->
        <div class="col-lg-5">
            <div class="card stretch stretch-full shadow-sm sticky-top" style="top: 100px;">
                <div class="card-header border-bottom bg-transparent py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                        <i class="feather-monitor text-primary"></i>
                        {{ __('messages.live_preview') ?? 'Live Ad Preview' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Simulated Smart Ad Card Component -->
                    <div class="border rounded-4 p-3 bg-white shadow-sm mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-soft-primary text-primary px-2 py-1" style="font-size: 10px;">
                                <i class="feather-zap me-1"></i>Sponsored
                            </span>
                            <span class="small text-muted" id="previewDomain">
                                {{ parse_url($smartAd->landing_url, PHP_URL_HOST) ?? 'example.com' }}
                            </span>
                        </div>
                        <div class="row g-2 align-items-center">
                            <div class="col-8">
                                <h6 class="fw-bold text-dark mb-1" id="previewHeadline" style="font-size: 14px; line-height: 1.3;">
                                    {{ $smartAd->displayTitle() }}
                                </h6>
                                <p class="small text-muted mb-0" id="previewDesc" style="font-size: 12px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $smartAd->displayDescription() }}
                                </p>
                            </div>
                            <div class="col-4 text-end">
                                <img id="previewImage" src="{{ $smartAd->image ?: asset('themes/default/assets/images/placeholder.jpg') }}" alt="" class="img-fluid rounded-3 shadow-sm" style="width: 72px; height: 72px; object-fit: cover;" onerror="this.src='{{ asset('themes/default/assets/images/placeholder.jpg') }}';">
                            </div>
                        </div>
                    </div>

                    <!-- Performance & AI Metadata -->
                    <div class="border-top pt-3">
                        <h6 class="fw-bold mb-3 d-flex align-items-center gap-2 text-dark">
                            <i class="feather-bar-chart-2 text-primary"></i>
                            {{ __('messages.smart_performance') }}
                        </h6>
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">{{ __('messages.smart_admin_owner') }}</span>
                                <span class="fw-semibold text-dark">{{ $smartAd->user?->username ?? __('messages.unknown') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">{{ __('messages.impressions') }}</span>
                                <span class="fw-semibold text-warning">{{ number_format($smartAd->impressions) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">{{ __('messages.clicks') }}</span>
                                <span class="fw-semibold text-primary">{{ number_format($smartAd->clicks) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">{{ __('messages.ctr') }}</span>
                                <span class="fw-semibold text-success">{{ $ctr }}%</span>
                            </li>
                            @if($smartAd->extracted_keywords)
                                <li class="list-group-item px-0">
                                    <span class="text-muted d-block mb-1">{{ __('messages.smart_admin_extracted_topic') }}</span>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach(array_filter(array_map('trim', explode(',', $smartAd->extracted_keywords))) as $kw)
                                            <span class="badge bg-light text-dark border">{{ $kw }}</span>
                                        @endforeach
                                    </div>
                                </li>
                            @endif
                        </ul>
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
    const inputLandingUrl = document.getElementById('inputLandingUrl');
    const inputHeadline = document.getElementById('inputHeadline');
    const inputImage = document.getElementById('inputImage');
    const inputDescription = document.getElementById('inputDescription');
    const previewHeadline = document.getElementById('previewHeadline');
    const previewDesc = document.getElementById('previewDesc');
    const previewImage = document.getElementById('previewImage');
    const previewDomain = document.getElementById('previewDomain');
    const editForm = document.getElementById('editSmartAdForm');
    const saveBtn = document.getElementById('saveSmartAdBtn');
    const saveSpinner = document.getElementById('saveSmartAdSpinner');
    const saveIcon = document.getElementById('saveSmartAdIcon');

    // Live preview update
    inputHeadline.addEventListener('input', function() {
        previewHeadline.textContent = this.value.trim() || 'Headline Preview';
    });

    inputDescription.addEventListener('input', function() {
        previewDesc.textContent = this.value.trim() || 'Description preview...';
    });

    inputImage.addEventListener('input', function() {
        if (this.value.trim()) {
            previewImage.src = this.value.trim();
        }
    });

    inputLandingUrl.addEventListener('input', function() {
        try {
            const urlObj = new URL(this.value.trim());
            previewDomain.textContent = urlObj.hostname;
        } catch(e) {}
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
                showToast(data.message || @json(__('messages.smart_ad_admin_updated')), 'success');
            } else {
                showToast(data.message || @json(__('messages.operation_failed') ?? 'Operation failed.'), 'error');
            }
        })
        .catch(err => {
            console.error('Save smart ad error:', err);
            saveBtn.disabled = false;
            saveSpinner.classList.add('d-none');
            saveIcon.classList.remove('d-none');
            showToast(@json(__('messages.network_error') ?? 'Network error.'), 'error');
        });
    });
});
</script>
@endpush
