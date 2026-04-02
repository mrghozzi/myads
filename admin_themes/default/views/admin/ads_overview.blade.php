@extends('admin::layouts.admin')

@section('title', __('messages.smart_admin_overview'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.smart_admin_overview') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.ads') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.smart_admin_overview') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.smart_admin_recent_inventory') }}</p>
            <div class="admin-stat-strip">
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.bannads') }}</span>
                    <span class="admin-stat-value">{{ number_format($summary['banners']) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.textads') }}</span>
                    <span class="admin-stat-value">{{ number_format($summary['links']) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.smart_ads') }}</span>
                    <span class="admin-stat-value">{{ number_format($summary['smart_ads']) }}</span>
                </div>
            </div>
        </div>

        <div class="admin-hero__actions">
            <div class="admin-toolbar-card w-100">
                <form action="{{ route('admin.ads') }}" method="GET" class="admin-toolbar-row w-100">
                    <div class="input-group flex-grow-1">
                        <span class="input-group-text bg-white border-end-0"><i class="feather-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ $search }}" class="form-control border-start-0 ps-0" placeholder="{{ __('messages.smart_admin_search_ads') }}">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-search me-1"></i>{{ __('messages.btn_search') }}
                    </button>
                </form>
            </div>
            <div class="admin-action-tiles w-100">
                <a href="{{ route('admin.banners') }}" class="admin-action-tile text-decoration-none">
                    <span class="admin-action-tile__icon"><i class="feather-image"></i></span>
                    <strong>{{ __('messages.bannads') }}</strong>
                    <span class="text-muted">{{ __('messages.codes') }} / {{ __('messages.Stats') }}</span>
                </a>
                <a href="{{ route('admin.smart_ads') }}" class="admin-action-tile text-decoration-none">
                    <span class="admin-action-tile__icon"><i class="feather-monitor"></i></span>
                    <strong>{{ __('messages.smart_ads') }}</strong>
                    <span class="text-muted">{{ __('messages.smart_admin_recent_inventory') }}</span>
                </a>
            </div>
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.smart_admin_recent_inventory') }}</span>
                <h2 class="admin-panel__title">{{ number_format(count($items)) }}</h2>
            </div>
        </div>

        <div class="admin-panel__body p-0">
            <div class="admin-table-wrap">
                <table class="table table-hover mb-0 admin-table admin-table-cardify admin-density-table">
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
                                <td data-label="{{ __('messages.type') }}"><span class="badge bg-soft-primary text-primary text-uppercase">{{ $item->type }}</span></td>
                                <td data-label="ID">#{{ $item->id }}</td>
                                <td data-label="{{ __('messages.name') }}">{{ $item->name }}</td>
                                <td data-label="{{ __('messages.smart_admin_owner') }}">{{ $item->owner ?? __('messages.unknown') }}</td>
                                <td data-label="{{ __('messages.smart_admin_primary') }}">{{ $item->metric_primary ?? 0 }}</td>
                                <td data-label="{{ __('messages.smart_admin_secondary') }}">{{ $item->metric_secondary ?? '-' }}</td>
                                <td data-label="{{ __('messages.smart_admin_badge') }}">{{ $item->badge }}</td>
                                <td data-label="{{ __('messages.status') }}">
                                    @if((int) $item->status === 1)
                                        <span class="badge bg-soft-success text-success">{{ __('messages.active') }}</span>
                                    @else
                                        <span class="badge bg-soft-warning text-warning">{{ __('messages.smart_status_paused') }}</span>
                                    @endif
                                </td>
                                <td data-label="{{ __('messages.actions') }}" class="text-end">
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
    </section>
</div>
@endsection
