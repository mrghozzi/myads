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
                <p style="margin: 14px 0 0; color: #5d6488; line-height: 1.7;">{{ __('messages.smart_code_live_behavior_note') }}</p>
            </div>
        </div>

        <div class="widget-box" style="margin-top: 24px;">
            <div class="widget-box-content">
                <p class="widget-box-title">{{ __('messages.preview') }}</p>
                @if($previewMarkup)
                    <div style="margin-top: 10px; color: #64748b; line-height: 1.7;">
                        {{ $previewSmartAd->displayTitle() }}
                    </div>
                    <div style="margin-top: 16px; padding: 18px; border: 1px solid #e5e7eb; border-radius: 20px; background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);">
                        <div style="max-width: 760px; margin: 0 auto;">
                            {!! $previewMarkup !!}
                        </div>
                    </div>
                @else
                    <div style="margin-top: 16px; padding: 24px; border: 1px dashed #cbd5e1; border-radius: 20px; background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%); text-align: center;">
                        <p style="margin: 0 0 8px; font-size: 1rem; font-weight: 700; color: #1f2937;">{{ __('messages.smart_code_preview_empty_title') }}</p>
                        <p style="margin: 0; color: #64748b; line-height: 1.8;">{{ __('messages.smart_code_preview_empty_desc') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
