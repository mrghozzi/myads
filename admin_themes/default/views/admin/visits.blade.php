@extends('admin::layouts.admin')

@section('title', __('messages.exvisit'))
@section('admin_shell_header_mode', 'hidden')

@php
    $visitDurationOptions = [
        1 => '10 ' . __('messages.seconds') . ' (1 ' . __('messages.point') . ')',
        2 => '20 ' . __('messages.seconds') . ' (2 ' . __('messages.points') . ')',
        3 => '30 ' . __('messages.seconds') . ' (5 ' . __('messages.points') . ')',
        4 => '60 ' . __('messages.seconds') . ' (10 ' . __('messages.points') . ')',
    ];
@endphp

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.exvisit') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.ads') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.exvisit') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.code_unavailable') }} / {{ __('messages.Stats') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-toolbar-card">
                <div class="admin-toolbar-row w-100">
                    <a href="javascript:void(0);" class="btn btn-icon btn-light-brand disabled" data-bs-toggle="tooltip" title="{{ __('messages.code_unavailable') }}" aria-disabled="true">
                        <i class="feather-code"></i>
                    </a>
                    <a href="{{ route('admin.visits', ['logic' => 'and', 'views_min' => 1]) }}" class="btn btn-icon btn-light-brand" data-bs-toggle="tooltip" title="{{ __('messages.Stats') }}">
                        <i class="feather-bar-chart-2"></i>
                    </a>
                    @include('admin::admin.partials.inventory_filter_dropdown', [
                        'action' => route('admin.visits'),
                        'resetUrl' => route('admin.visits', ['reset_filters' => 1]),
                        'preferenceKey' => 'visits',
                        'filterState' => $filterState,
                        'filterFields' => $filterFields,
                        'resultsCount' => $resultsCount,
                    ])
                    <button class="btn btn-danger ms-2 d-none" id="bulkDeleteBtn" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
                        <i class="feather-trash-2 me-2"></i>
                        <span>{{ __('messages.delete_selected') }}</span>
                    </button>
                    <a href="{{ route('visits.create') }}" class="btn btn-primary ms-auto">
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

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="main-content">
    <div class="row">
        <div class="col-lg-12">
            <div class="card stretch stretch-full">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0">{{ __('messages.exvisit') }}</h5>
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
                                    <th>{{ __('messages.name') }}</th>
                                    <th>{{ __('messages.views') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.status') }}</th>
                                    <th class="text-end">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($visits as $visit)
                                <tr>
                                    <td data-label="#">
                                        <div class="form-check form-check-md">
                                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $visit->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold">#{{ $visit->id }}</span>
                                    </td>
                                    <td>
                                        @if($visit->user)
                                            <a href="{{ route('profile.show', $visit->user->username) }}" target="_blank" class="hstack gap-3">
                                                <div class="avatar-image avatar-md">
                                                    <img src="{{ $visit->user->img ? asset($visit->user->img) : asset('themes/default/assets/images/avatar/1.png') }}" alt="" class="img-fluid">
                                                </div>
                                                <div>
                                                    <span class="text-truncate-1-line fw-bold text-dark">{{ $visit->user->username }}</span>
                                                </div>
                                            </a>
                                        @else
                                            <span class="text-muted">{{ __('messages.unknown') ?? 'Unknown' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $visit->name }}</div>
                                        <div class="small">
                                            <a href="{{ $visit->url }}" target="_blank" class="text-primary">{{ Str::limit($visit->url, 30) }}</a>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-primary text-primary">{{ $visit->vu }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ date('Y-m-d H:i', $visit->tims) }}</span>
                                    </td>
                                    <td>
                                        @if($visit->statu == 1)
                                            <span class="badge bg-soft-success text-success">{{ __('messages.active') }}</span>
                                        @else
                                            <span class="badge bg-soft-danger text-danger">{{ __('messages.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-success text-success" data-bs-toggle="modal" data-bs-target="#editModal{{ $visit->id }}">
                                                <i class="feather-edit-3"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="avatar-text avatar-md bg-soft-danger text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $visit->id }}">
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
                    {{ $visits->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('modals')
@foreach($visits as $visit)
<!-- Edit Modal -->
<div class="modal fade" id="editModal{{ $visit->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.edit') }} {{ __('messages.exvisit') }} #{{ $visit->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.visits.update', $visit->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ $visit->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.url') }}</label>
                        <input type="url" name="url" class="form-control" value="{{ $visit->url }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.duration') }}</label>
                        <select name="tims" class="form-select" required>
                            @foreach($visitDurationOptions as $durationValue => $durationLabel)
                                <option value="{{ $durationValue }}" {{ (int) $visit->tims === (int) $durationValue ? 'selected' : '' }}>{{ $durationLabel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">{{ __('messages.status') }}</label>
                        <select name="statu" class="form-select" required>
                            <option value="1" {{ (int) $visit->statu === 1 ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                            <option value="2" {{ (int) $visit->statu === 2 ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal{{ $visit->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('messages.delete_visit') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto">
                    <i class="feather-trash-2"></i>
                </div>
                <h4>{{ __('messages.confirm_delete_visit') }}</h4>
                <p class="text-muted">#{{ $visit->id }}</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <form action="{{ route('admin.visits.delete', $visit->id) }}" method="POST" class="d-inline">
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
                <form action="{{ route('admin.visits.bulk_delete') }}" method="POST" id="bulkDeleteForm" class="d-inline">
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
