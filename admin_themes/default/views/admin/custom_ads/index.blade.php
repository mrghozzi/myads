@extends('admin::layouts.admin')

@section('title', __('messages.custom_ads'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.ads') }}">{{ __('messages.ads') }}</a></li>
                <li>{{ __('messages.custom_ads') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.ads') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.custom_ads') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.custom_ads_admin_subtitle') }}</p>
            <div class="admin-stat-strip">
                <div class="admin-stat-card"><span class="admin-stat-label">{{ __('messages.custom_ads_placements') }}</span><span class="admin-stat-value">{{ number_format($summary['placements']) }}</span></div>
                <div class="admin-stat-card"><span class="admin-stat-label">{{ __('messages.custom_ads_deals') }}</span><span class="admin-stat-value">{{ number_format($summary['deals']) }}</span></div>
                <div class="admin-stat-card"><span class="admin-stat-label">{{ __('messages.active') }}</span><span class="admin-stat-value">{{ number_format($summary['active_deals']) }}</span></div>
                <div class="admin-stat-card"><span class="admin-stat-label">{{ __('messages.custom_ads_pending_creatives') }}</span><span class="admin-stat-value">{{ number_format($summary['pending_creatives']) }}</span></div>
            </div>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-toolbar-card w-100">
                <form action="{{ route('admin.custom_ads.index') }}" method="GET" class="admin-toolbar-row w-100">
                    <div class="input-group flex-grow-1">
                        <span class="input-group-text bg-white border-end-0"><i class="feather-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ $search }}" class="form-control border-start-0 ps-0" placeholder="{{ __('messages.search') }}">
                    </div>
                    <button class="btn btn-primary" type="submit"><i class="feather-search me-1"></i>{{ __('messages.btn_search') }}</button>
                </form>
            </div>
            <a href="{{ route('admin.custom_ads.settings') }}" class="btn btn-light w-100">{{ __('messages.custom_ads_settings') }}</a>
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.custom_ads_placements') }}</span>
                <h2 class="admin-panel__title">{{ number_format($placements->total()) }}</h2>
            </div>
        </div>
        <div class="admin-panel__body p-0">
            <div class="admin-table-wrap">
                <table class="table table-hover mb-0 admin-table admin-table-cardify admin-density-table">
                    <thead><tr><th>ID</th><th>{{ __('messages.name') }}</th><th>{{ __('messages.smart_admin_owner') }}</th><th>{{ __('messages.type') }}</th><th>{{ __('messages.stats') }}</th><th>{{ __('messages.status') }}</th><th class="text-end">{{ __('messages.actions') }}</th></tr></thead>
                    <tbody>
                    @forelse($placements as $placement)
                        <tr>
                            <td>#{{ $placement->id }}</td>
                            <td>{{ $placement->name }}<div class="text-muted small">{{ $placement->placement_key }}</div></td>
                            <td>{{ $placement->user?->username }}</td>
                            <td>{{ $placement->format }} / {{ $placement->size }}</td>
                            <td>{{ number_format($placement->impressions) }} / {{ number_format($placement->clicks) }}</td>
                            <td><span class="badge bg-soft-primary text-primary">{{ $placement->status }}</span></td>
                            <td class="text-end">
                                <form action="{{ route('admin.custom_ads.placements.status', $placement) }}" method="POST" class="d-inline-flex gap-2">
                                    @csrf
                                    <select name="status" class="form-select form-select-sm">
                                        @foreach([\App\Models\CustomAdPlacement::STATUS_ACTIVE, \App\Models\CustomAdPlacement::STATUS_PAUSED, \App\Models\CustomAdPlacement::STATUS_DISABLED] as $status)
                                            <option value="{{ $status }}" @selected($placement->status === $status)>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-light" type="submit">{{ __('messages.save') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted">{{ __('messages.custom_ads_no_placements') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $placements->links() }}</div>
        </div>
    </section>

    <section class="admin-panel mt-4">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.custom_ads_deals') }}</span>
                <h2 class="admin-panel__title">{{ number_format($deals->total()) }}</h2>
            </div>
        </div>
        <div class="admin-panel__body p-0">
            <div class="admin-table-wrap">
                <table class="table table-hover mb-0 admin-table admin-table-cardify admin-density-table">
                    <thead><tr><th>ID</th><th>{{ __('messages.custom_ads_placement') }}</th><th>{{ __('messages.custom_ads_parties') }}</th><th>{{ __('messages.custom_ads_payment') }}</th><th>{{ __('messages.stats') }}</th><th>{{ __('messages.status') }}</th><th class="text-end">{{ __('messages.actions') }}</th></tr></thead>
                    <tbody>
                    @forelse($deals as $deal)
                        <tr>
                            <td>#{{ $deal->id }}</td>
                            <td>{{ $deal->placement?->name }}<div class="text-muted small">{{ $deal->source }}</div></td>
                            <td>{{ $deal->publisher?->username }} → {{ $deal->advertiser?->username }}</td>
                            <td>
                                @if($deal->payment_type === \App\Models\CustomAdDeal::PAYMENT_PTS_DAILY)
                                    {{ number_format((float) $deal->daily_pts, 2) }} PTS/{{ __('messages.day') }}
                                @else
                                    {{ __('messages.custom_ads_external') }} {{ $deal->external_amount ? number_format((float) $deal->external_amount, 2) . ' ' . $deal->external_currency : '' }}
                                @endif
                            </td>
                            <td>{{ number_format($deal->impressions) }} / {{ number_format($deal->clicks) }}</td>
                            <td>
                                <span class="badge bg-soft-primary text-primary">{{ $deal->status }}</span>
                                @if($deal->creative)
                                    <div class="text-muted small">{{ __('messages.custom_ads_creative') }}: {{ $deal->creative->status }}</div>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2 flex-wrap justify-content-end">
                                    @foreach(['pause', 'resume', 'cancel', 'complete', 'approve_creative', 'reject_creative'] as $action)
                                        <form action="{{ route('admin.custom_ads.deals.status', $deal) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="{{ $action }}">
                                            <button class="btn btn-sm btn-light" type="submit">{{ __('messages.custom_ads_action_' . $action) }}</button>
                                        </form>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted">{{ __('messages.custom_ads_no_deals') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">{{ $deals->links() }}</div>
        </div>
    </section>
</div>
@endsection
