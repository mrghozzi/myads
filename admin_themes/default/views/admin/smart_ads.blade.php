@extends('admin::layouts.admin')

@section('title', __('messages.smart_ads'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.smart_ads') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.ads') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.smart_ads') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.codes') }} / {{ __('messages.Stats') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-toolbar-card">
                <div class="admin-toolbar-row w-100">
                    <a href="{{ route('ads.smart.code') }}" class="btn btn-icon btn-light-brand" data-bs-toggle="tooltip" title="{{ __('messages.codes') }}">
                        <i class="feather-code"></i>
                    </a>
                    <a href="{{ route('admin.stats', ['ty' => 'smart']) }}" class="btn btn-icon btn-light-brand" data-bs-toggle="tooltip" title="{{ __('messages.Stats') }}">
                        <i class="feather-bar-chart-2"></i>
                    </a>
                    @include('admin::admin.partials.inventory_filter_dropdown', [
                        'action' => route('admin.smart_ads'),
                        'resetUrl' => route('admin.smart_ads', ['reset_filters' => 1]),
                        'preferenceKey' => 'smart-ads',
                        'filterState' => $filterState,
                        'filterFields' => $filterFields,
                        'resultsCount' => $resultsCount,
                    ])
                    <button class="btn btn-danger ms-2 d-none" id="bulkDeleteBtn" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
                        <i class="feather-trash-2 me-2"></i>
                        <span>{{ __('messages.delete_selected') }}</span>
                    </button>
                    <a href="{{ route('ads.smart.create') }}" class="btn btn-primary ms-auto">
                        <i class="feather-plus me-2"></i>
                        <span>{{ __('messages.add') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0">{{ __('messages.smart_admin_all_smart_ads') }}</h5>
                    <span class="badge bg-soft-primary text-primary">{{ __('messages.results_count', ['count' => $resultsCount]) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="wd-30">
                                        <div class="form-check form-check-md">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </div>
                                    </th>
                                    <th>#ID</th>
                                    <th>{{ __('messages.user') }}</th>
                                    <th>{{ __('messages.smart_form_headline_override') }}</th>
                                    <th>{{ __('messages.smart_targets') }}</th>
                                    <th>{{ __('messages.smart_impressions_label') }}</th>
                                    <th>{{ __('messages.smart_clicks_label') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th class="text-end">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($smartAds as $smartAd)
                                    <tr>
                                        <td data-label="#">
                                            <div class="form-check form-check-md">
                                                <input class="form-check-input row-checkbox" type="checkbox" value="{{ $smartAd->id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold">#{{ $smartAd->id }}</span>
                                        </td>
                                        <td>
                                            <div class="small fw-bold text-dark">{{ $smartAd->user?->username ?? __('messages.unknown') }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $smartAd->displayTitle() }}</div>
                                            <div class="small text-muted">{{ \Illuminate\Support\Str::limit($smartAd->landing_url, 48) }}</div>
                                        </td>
                                        <td>
                                            <div class="small">{{ __('messages.smart_target_countries_label') }}: {{ \App\Support\SmartAdTargeting::formatTargets($smartAd->targetCountries()) }}</div>
                                            <div class="small">{{ __('messages.smart_target_devices_label') }}: {{ \App\Support\SmartAdTargeting::formatTargets($smartAd->targetDevices()) }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-warning text-warning">{{ $smartAd->impressions }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-soft-primary text-primary">{{ $smartAd->clicks }}</span>
                                        </td>
                                        <td>
                                            @if((int) $smartAd->statu === 1)
                                                <span class="badge bg-soft-success text-success">{{ __('messages.active') }}</span>
                                            @elseif((int) $smartAd->statu === 2)
                                                <span class="badge bg-soft-danger text-danger">{{ __('messages.smart_status_blocked') }}</span>
                                            @else
                                                <span class="badge bg-soft-warning text-warning">{{ __('messages.smart_status_paused') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="hstack gap-2 justify-content-end">
                                                <a href="{{ route('admin.smart_ads.edit', $smartAd->id) }}" class="avatar-text avatar-md bg-soft-success text-success">
                                                    <i class="feather-edit-3"></i>
                                                </a>
                                                <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-danger text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $smartAd->id }}">
                                                    <i class="feather-trash-2"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $smartAds->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('modals')
@foreach($smartAds as $smartAd)
<div class="modal fade" id="deleteModal{{ $smartAd->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto">
                    <i class="feather-trash-2"></i>
                </div>
                <h4>{{ __('messages.smart_delete_confirm') }}</h4>
                <p class="text-muted">#{{ $smartAd->id }}</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <form action="{{ route('admin.smart_ads.delete', $smartAd->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('messages.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.delete_selected') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto">
                    <i class="feather-trash-2"></i>
                </div>
                <h4>{!! __('messages.selected_items_count', ['count' => '<span id="bulkDeleteCount" class="fw-bold text-dark">0</span>']) !!}</h4>
                <p class="text-muted">{{ __('messages.bulk_delete_items_warning') }}</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <form action="{{ route('admin.smart_ads.bulk_delete') }}" method="POST" id="bulkDeleteForm" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <div id="bulkDeleteInputs"></div>
                    <button type="submit" class="btn btn-danger">{{ __('messages.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const bulkDeleteCount = document.getElementById('bulkDeleteCount');
    const bulkDeleteInputs = document.getElementById('bulkDeleteInputs');

    function updateBulkDeleteButton() {
        const selected = Array.from(checkboxes).filter(cb => cb.checked);
        if (selected.length > 0) {
            bulkDeleteBtn.classList.remove('d-none');
            bulkDeleteCount.innerHTML = selected.length;
            
            bulkDeleteInputs.innerHTML = '';
            selected.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                bulkDeleteInputs.appendChild(input);
            });
        } else {
            bulkDeleteBtn.classList.add('d-none');
        }
        
        if (checkAll) {
            checkAll.checked = selected.length === checkboxes.length && checkboxes.length > 0;
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkDeleteButton();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkDeleteButton);
    });
});
</script>
@endpush
