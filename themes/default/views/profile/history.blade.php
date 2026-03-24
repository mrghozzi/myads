@extends('theme::layouts.master')

@section('content')
@php
    $positiveTotal = collect($history->items())->sum(fn ($item) => max(0, (float) $item->amount));
    $negativeTotal = collect($history->items())->sum(fn ($item) => min(0, (float) $item->amount));
@endphp

<div class="section-banner">
    <p class="section-banner-title">{{ __('messages.pts_history') }}</p>
    <p class="section-banner-text">{{ __('messages.badge_showcase_help') }}</p>
</div>

<style>
    .points-history-shell {
        display: grid;
        gap: 18px;
    }
    .points-history-summary {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }
    .points-history-stat {
        border: 1px solid #eaeaf5;
        border-radius: 16px;
        padding: 18px;
        background: linear-gradient(180deg, #fff 0%, #f9faff 100%);
    }
    .points-history-table {
        width: 100%;
        border-collapse: collapse;
    }
    .points-history-table th,
    .points-history-table td {
        padding: 14px 12px;
        vertical-align: middle;
        border-bottom: 1px solid #eef0f6;
    }
    .points-history-entry {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
    }
    .points-history-entry span {
        width: 38px;
        height: 38px;
        border-radius: 14px;
        display: inline-grid;
        place-items: center;
        background: rgba(97, 93, 250, 0.12);
        color: #615dfa;
    }
    .points-history-empty {
        text-align: center;
        padding: 32px 20px;
    }
    @media (max-width: 768px) {
        .points-history-summary {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">
        @include('theme::profile.settings_nav')
    </div>

    <div class="grid-column">
        <div class="points-history-shell">
            @if(session('error'))
                <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif

            @if(!empty($upgradeNotice))
                @include('theme::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
            @endif

            <div class="points-history-summary">
                <div class="points-history-stat">
                    <p class="widget-box-title">{{ __('messages.pts') }}</p>
                    <p class="user-status-title" style="font-size: 26px; color: #23d2e2; margin-top: 10px;">
                        +{{ rtrim(rtrim(number_format($positiveTotal, 2), '0'), '.') }}
                    </p>
                </div>
                <div class="points-history-stat">
                    <p class="widget-box-title">{{ __('messages.no_history') }}</p>
                    <p class="user-status-title" style="font-size: 26px; color: #e94b5f; margin-top: 10px;">
                        {{ rtrim(rtrim(number_format($negativeTotal, 2), '0'), '.') }}
                    </p>
                </div>
            </div>

            <div class="widget-box">
                <p class="widget-box-title">{{ __('messages.pts_history') }}</p>
                <div class="widget-box-content">
                    @if($history->count() === 0)
                        <div class="points-history-empty">
                            <p>{{ ($featureAvailable ?? true) ? __('messages.no_history') : __('messages.upgrade_legacy_mode_notice') }}</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="points-history-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('messages.name') }}</th>
                                        <th>{{ __('messages.date') }}</th>
                                        <th style="text-align: {{ is_locale_rtl() ? 'left' : 'right' }};">{{ __('messages.pts') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($history as $item)
                                        @php
                                            $description = __('messages.' . $item->description_key);
                                            $description = $description !== 'messages.' . $item->description_key ? $description : $item->description_key;
                                            $amount = (float) $item->amount;
                                        @endphp
                                        <tr>
                                            <td>#{{ $item->id }}</td>
                                            <td>
                                                <div class="points-history-entry">
                                                    <span><i class="fa fa-star" aria-hidden="true"></i></span>
                                                    <div>
                                                        <p class="user-status-title" style="font-size: 14px;">{{ $description }}</p>
                                                        @if($item->is_legacy)
                                                            <p class="user-status-text small">{{ __('messages.legacy_points') }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ \Carbon\Carbon::createFromTimestamp($item->created_at_ts)->format('Y-m-d H:i') }}</td>
                                            <td style="font-weight: 700; text-align: {{ is_locale_rtl() ? 'left' : 'right' }}; color: {{ $amount >= 0 ? '#23d2e2' : '#e94b5f' }};">
                                                {{ $amount > 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($amount, 2), '0'), '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($history->hasPages())
                            <div style="margin-top: 20px;">
                                {{ $history->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
