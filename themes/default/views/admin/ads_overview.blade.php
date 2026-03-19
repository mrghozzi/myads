@extends('theme::layouts.admin')

@section('title', __('messages.smart_admin_overview'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.smart_admin_overview') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.smart_admin_overview') }}</li>
        </ul>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-4 col-md-6">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <p class="text-muted text-uppercase fs-11 fw-bold mb-2">{{ __('messages.bannads') }}</p>
                <h3 class="mb-0">{{ $summary['banners'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <p class="text-muted text-uppercase fs-11 fw-bold mb-2">{{ __('messages.textads') }}</p>
                <h3 class="mb-0">{{ $summary['links'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="card stretch stretch-full">
            <div class="card-body">
                <p class="text-muted text-uppercase fs-11 fw-bold mb-2">{{ __('messages.smart_ads') }}</p>
                <h3 class="mb-0">{{ $summary['smart_ads'] }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card stretch stretch-full">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="card-title mb-0">{{ __('messages.smart_admin_recent_inventory') }}</h5>
        <form action="{{ route('admin.ads') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('messages.smart_admin_search_ads') }}">
            <button type="submit" class="btn btn-primary">{{ __('messages.btn_search') }}</button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('messages.type') }}</th>
                        <th>ID</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.smart_admin_owner') }}</th>
                        <th>{{ __('messages.smart_admin_primary') }}</th>
                        <th>{{ __('messages.smart_admin_secondary') }}</th>
                        <th>{{ __('messages.smart_admin_badge') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th class="text-end">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td><span class="badge bg-soft-primary text-primary text-uppercase">{{ $item->type }}</span></td>
                            <td>#{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->owner ?? __('messages.unknown') }}</td>
                            <td>{{ $item->metric_primary ?? 0 }}</td>
                            <td>{{ $item->metric_secondary ?? '-' }}</td>
                            <td>{{ $item->badge }}</td>
                            <td>
                                @if((int) $item->status === 1)
                                    <span class="badge bg-soft-success text-success">{{ __('messages.active') }}</span>
                                @else
                                    <span class="badge bg-soft-warning text-warning">{{ __('messages.smart_status_paused') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ $item->edit_url }}" class="btn btn-sm btn-light">{{ __('messages.smart_admin_open') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">{{ __('messages.smart_admin_no_ads') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
