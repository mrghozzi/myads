@extends('theme::layouts.master')

@section('content')
<div class="grid grid change-on-desktop">
    <div class="achievement-box secondary" style="background: linear-gradient(135deg, rgba(15,23,42,.96) 0%, rgba(29,78,216,.94) 56%, rgba(14,165,233,.88) 100%);">
        <div class="achievement-box-info-wrap">
            <img class="achievement-box-image" src="{{ theme_asset('img/banner/banner_ads.png') }}" alt="smart-ads">
            <div class="achievement-box-info">
                <p class="achievement-box-title">{{ __('messages.smart_ads') }}</p>
                <p class="achievement-box-text"><b>{{ __('messages.smart_index_byline') }}</b></p>
            </div>
        </div>

        <a class="button white-solid" href="{{ route('ads.smart.code') }}">
            <i class="fa fa-code" aria-hidden="true"></i>&nbsp;{{ __('messages.code') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
@endif

<div class="section-filters-bar v6">
    <div class="section-filters-bar-actions">
        <a class="button tertiary" href="{{ route('legacy.state', ['ty' => 'smart', 'st' => 'vu']) }}"><i class="fa fa-line-chart" aria-hidden="true"></i></a>
    </div>
    <p class="text-sticker">
        <svg class="text-sticker-icon icon-info">
            <use xlink:href="#svg-info"></use>
        </svg>
        {{ __('messages.smart_you_have_credits', ['credits' => number_format((float) $user->nsmart, 2)]) }}
    </p>
    <div class="section-filters-bar-actions">
        <a href="{{ route('ads.smart.create') }}" class="button secondary" style="color: #fff;">
            <i class="fa fa-plus nav_icon"></i>&nbsp;{{ __('messages.create') }}
        </a>
        <a href="{{ route('ads.smart.code') }}" class="button primary">
            <i class="fa fa-code nav_icon"></i>&nbsp;{{ __('messages.code') }}
        </a>
    </div>
</div>

<div class="grid grid">
    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                @if($smartAds->isEmpty())
                    <div style="padding: 24px; border: 1px dashed #c7d2fe; border-radius: 18px; background: linear-gradient(135deg, #f8fbff 0%, #eef6ff 100%);">
                        <h4 style="margin: 0 0 8px;">{{ __('messages.smart_empty_title') }}</h4>
                        <p style="margin: 0 0 14px; color: #6b7280;">{{ __('messages.smart_empty_desc') }}</p>
                        <a href="{{ route('ads.smart.create') }}" class="button secondary">{{ __('messages.smart_create_ad') }}</a>
                    </div>
                @else
                    <div style="display: grid; gap: 18px;">
                        @foreach($smartAds as $smartAd)
                            <div style="display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 18px; padding: 22px; border: 1px solid #eef2ff; border-radius: 20px; background: #fff;">
                                <div>
                                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 10px;">
                                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 999px; background: rgba(29,78,216,0.08); color: #1d4ed8; font-size: .72rem; font-weight: 700; text-transform: uppercase;">{{ __('messages.smart_ad') }}</span>
                                        <span style="font-size: .8rem; color: #8f91ac;">#{{ $smartAd->id }}</span>
                                        <span style="font-size: .8rem; color: {{ (int) $smartAd->statu === 1 ? '#0f766e' : '#b45309' }};">{{ (int) $smartAd->statu === 1 ? __('messages.active') : __('messages.smart_status_paused') }}</span>
                                    </div>

                                    <div style="display: grid; grid-template-columns: {{ $smartAd->displayImage() ? '112px minmax(0, 1fr)' : '1fr' }}; gap: 18px; align-items: start;">
                                        @if($smartAd->displayImage())
                                            <div style="width: 112px; height: 112px; border-radius: 16px; overflow: hidden; background: #f3f4f6;">
                                                <img src="{{ $smartAd->displayImage() }}" alt="{{ $smartAd->displayTitle() }}" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                        @endif
                                        <div>
                                            <h4 style="margin: 0 0 8px; color: #1f2937;">{{ $smartAd->displayTitle() }}</h4>
                                            <p style="margin: 0 0 10px; color: #6b7280; line-height: 1.7;">{{ \Illuminate\Support\Str::limit($smartAd->displayDescription(), 200) }}</p>
                                            <p style="margin: 0; color: #1d4ed8; font-size: .85rem; line-height: 1.6;">
                                                <a href="{{ $smartAd->landing_url }}" target="_blank" rel="noopener noreferrer">{{ $smartAd->landing_url }}</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div style="min-width: 220px; display: grid; gap: 14px;">
                                    <div style="padding: 14px 16px; border-radius: 16px; background: linear-gradient(135deg, #f8fbff 0%, #eef6ff 100%);">
                                        <p style="margin: 0 0 6px; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; color: #1d4ed8; font-weight: 700;">{{ __('messages.smart_targets') }}</p>
                                        <p style="margin: 0; color: #475569; line-height: 1.65;">{{ __('messages.smart_target_countries_label') }}: {{ \App\Support\SmartAdTargeting::formatTargets($smartAd->targetCountries()) }}</p>
                                        <p style="margin: 6px 0 0; color: #475569; line-height: 1.65;">{{ __('messages.smart_target_devices_label') }}: {{ \App\Support\SmartAdTargeting::formatTargets($smartAd->targetDevices()) }}</p>
                                    </div>
                                    <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px;">
                                        <a href="{{ route('legacy.state', ['ty' => 'smart', 'id' => $smartAd->id]) }}" style="padding: 14px 12px; border-radius: 16px; background: #f8fafc; text-align: center; text-decoration: none; display: block;">
                                            <div style="font-size: 1.25rem; font-weight: 800; color: #1d4ed8;">{{ $smartAd->impressions }}</div>
                                            <div style="font-size: .75rem; color: #64748b;">{{ __('messages.smart_impressions_label') }}</div>
                                        </a>
                                        <a href="{{ route('legacy.state', ['ty' => 'smart_click', 'id' => $smartAd->id]) }}" style="padding: 14px 12px; border-radius: 16px; background: #f8fafc; text-align: center; text-decoration: none; display: block;">
                                            <div style="font-size: 1.25rem; font-weight: 800; color: #0f766e;">{{ $smartAd->clicks }}</div>
                                            <div style="font-size: .75rem; color: #64748b;">{{ __('messages.smart_clicks_label') }}</div>
                                        </a>
                                    </div>
                                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                        <a href="{{ route('ads.smart.edit', $smartAd->id) }}" class="button tertiary">{{ __('messages.edit') }}</a>
                                        <form action="{{ route('ads.smart.destroy', $smartAd->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.smart_delete_confirm') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button primary">{{ __('messages.delete') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
