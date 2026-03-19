@extends('theme::layouts.master')

@section('content')
<div class="grid grid change-on-desktop">
    <div class="achievement-box secondary" style="background: linear-gradient(135deg, rgba(15,23,42,.96) 0%, rgba(29,78,216,.94) 56%, rgba(14,165,233,.88) 100%);">
        <div class="achievement-box-info-wrap">
            <img class="achievement-box-image" src="{{ theme_asset('img/banner/banner_ads.png') }}" alt="smart-ads-code">
            <div class="achievement-box-info">
                <p class="achievement-box-title">{{ __('messages.smart_code_title') }}</p>
                <p class="achievement-box-text"><b>{{ __('messages.smart_code_desc') }}</b></p>
            </div>
        </div>

        <a class="button white-solid" href="{{ route('ads.smart.index') }}">{{ __('messages.smart_list_ads') }}</a>
    </div>
</div>

<div class="grid grid">
    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-content">
                <p class="widget-box-title">{{ __('messages.smart_code_recommended') }}</p>
                <p style="margin: 10px 0 18px; color: #5d6488; line-height: 1.7;">{{ __('messages.smart_code_recommended_desc') }}</p>
                <div class="well" style="color: black;">
                    <textarea class="form-control" readonly onclick="this.select(); document.execCommand('copy');">{{ $embedCode }}</textarea>
                </div>
            </div>
        </div>

        <div class="widget-box" style="margin-top: 24px;">
            <div class="widget-box-content">
                <p class="widget-box-title">{{ __('messages.preview') }}</p>
                <div style="margin-top: 16px; padding: 18px; border: 1px solid #e5e7eb; border-radius: 20px; background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);">
                    <div style="max-width: 760px; margin: 0 auto;">
                        {!! $previewCode !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
