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
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card stretch stretch-full">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <h5 class="card-title mb-0">{{ __('messages.smart_admin_all_smart_ads') }}</h5>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.ads') }}" class="btn btn-light">{{ __('messages.ads') }}</a>
            <a href="{{ route('admin.stats', ['ty' => 'smart']) }}" class="btn btn-light">{{ __('messages.smart_impressions_label') }}</a>
            <a href="{{ route('admin.stats', ['ty' => 'smart_click']) }}" class="btn btn-light">{{ __('messages.smart_clicks_label') }}</a>
        </div>
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
                            <td>#{{ $smartAd->id }}</td>
                            <td>{{ $smartAd->user?->username ?? __('messages.unknown') }}</td>
                            <td>
                                <div class="fw-bold">{{ $smartAd->displayTitle() }}</div>
                                <div class="small text-muted">{{ \Illuminate\Support\Str::limit($smartAd->landing_url, 48) }}</div>
                            </td>
                            <td>
                                <div class="small">{{ __('messages.smart_target_countries_label') }}: {{ \App\Support\SmartAdTargeting::formatTargets($smartAd->targetCountries()) }}</div>
                                <div class="small">{{ __('messages.smart_target_devices_label') }}: {{ \App\Support\SmartAdTargeting::formatTargets($smartAd->targetDevices()) }}</div>
                            </td>
                            <td>{{ $smartAd->impressions }}</td>
                            <td>{{ $smartAd->clicks }}</td>
                            <td>
                                @if((int) $smartAd->statu === 1)
                                    <span class="badge bg-soft-success text-success">{{ __('messages.active') }}</span>
                                @elseif((int) $smartAd->statu === 2)
                                    <span class="badge bg-soft-danger text-danger">{{ __('messages.smart_status_blocked') }}</span>
                                @else
                                    <span class="badge bg-soft-warning text-warning">{{ __('messages.smart_status_paused') }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a href="{{ route('admin.smart_ads.edit', $smartAd->id) }}" class="btn btn-sm btn-light">{{ __('messages.edit') }}</a>
                                    <form action="{{ route('admin.smart_ads.delete', $smartAd->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.smart_delete_confirm') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">{{ __('messages.delete') }}</button>
                                    </form>
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
@endsection
