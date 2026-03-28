@extends('theme::layouts.admin')

@section('title', __('messages.exvisit'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.exvisit') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.exvisit') }}</li>
        </ul>
    </div>
    <div class="page-header-right ms-auto">
        <div class="page-header-right-items">
            <div class="d-flex d-md-none">
                <a href="javascript:void(0)" class="page-header-right-close-toggle">
                    <i class="feather-arrow-left me-2"></i>
                    <span>{{ __('messages.back') ?? 'Back' }}</span>
                </a>
            </div>
            <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                <a href="javascript:void(0);" class="btn btn-icon btn-light-brand disabled" data-bs-toggle="tooltip" title="{{ __('messages.code_unavailable') }}" aria-disabled="true">
                    <i class="feather-code"></i>
                </a>
                <a href="{{ route('admin.visits', ['logic' => 'and', 'views_min' => 1]) }}" class="btn btn-icon btn-light-brand" data-bs-toggle="tooltip" title="{{ __('messages.Stats') }}">
                    <i class="feather-bar-chart-2"></i>
                </a>
                @include('theme::admin.partials.inventory_filter_dropdown', [
                    'action' => route('admin.visits'),
                    'resetUrl' => route('admin.visits', ['reset_filters' => 1]),
                    'preferenceKey' => 'visits',
                    'filterState' => $filterState,
                    'filterFields' => $filterFields,
                    'resultsCount' => $resultsCount,
                ])
                <a href="{{ route('visits.create') }}" class="btn btn-primary">
                    <i class="feather-plus me-2"></i>
                    <span>{{ __('messages.add') }}</span>
                </a>
            </div>
        </div>
        <div class="d-md-none d-flex align-items-center">
            <a href="javascript:void(0)" class="page-header-right-open-toggle">
                <i class="feather-align-right fs-20"></i>
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
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
@endsection

@section('modals')
@foreach($visits as $visit)
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
@endsection
