@extends('theme::layouts.admin')

@section('title', __('messages.smart_ads'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.smart_ads') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.smart_ads') }}</li>
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
                <a href="{{ route('ads.smart.code') }}" class="btn btn-icon btn-light-brand" data-bs-toggle="tooltip" title="{{ __('messages.codes') }}">
                    <i class="feather-code"></i>
                </a>
                <a href="{{ route('admin.stats', ['ty' => 'smart']) }}" class="btn btn-icon btn-light-brand" data-bs-toggle="tooltip" title="{{ __('messages.Stats') }}">
                    <i class="feather-bar-chart-2"></i>
                </a>
                @include('theme::admin.partials.inventory_filter_dropdown', [
                    'action' => route('admin.smart_ads'),
                    'resetUrl' => route('admin.smart_ads', ['reset_filters' => 1]),
                    'preferenceKey' => 'smart-ads',
                    'filterState' => $filterState,
                    'filterFields' => $filterFields,
                    'resultsCount' => $resultsCount,
                ])
                <a href="{{ route('ads.smart.create') }}" class="btn btn-primary">
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
                    <h5 class="card-title mb-0">{{ __('messages.smart_admin_all_smart_ads') }}</h5>
                    <span class="badge bg-soft-primary text-primary">{{ __('messages.results_count', ['count' => $resultsCount]) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
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
@endsection
