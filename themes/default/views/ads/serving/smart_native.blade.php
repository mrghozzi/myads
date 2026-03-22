<style>
    .myads-smart-native,
    .myads-smart-native * {
        box-sizing: border-box;
    }
    .myads-smart-native {
        display: block;
        width: 100%;
        max-width: 420px;
        margin: 0 auto;
        border: 1px solid #e6ecff;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 16px 32px rgba(37, 99, 235, 0.08);
        overflow: hidden;
        font-family: Arial, 'Segoe UI', sans-serif;
    }
    .myads-smart-native__media {
        aspect-ratio: 16 / 9;
        background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 60%, #38bdf8 100%);
        overflow: hidden;
    }
    .myads-smart-native__media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .myads-smart-native__body {
        padding: 18px 18px 16px;
    }
    .myads-smart-native__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 12px;
        padding: 5px 10px;
        border-radius: 999px;
        background: rgba(29,78,216,.08);
        color: #1d4ed8;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }
    .myads-smart-native__title {
        margin: 0 0 10px;
        color: #0f172a;
        font-size: 20px;
        line-height: 1.3;
        font-weight: 800;
    }
    .myads-smart-native__description {
        margin: 0 0 14px;
        color: #475569;
        font-size: 14px;
        line-height: 1.75;
    }
    .myads-smart-native__actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .myads-smart-native__cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 0 18px;
        border-radius: 999px;
        background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 55%, #38bdf8 100%);
        color: #fff;
        text-decoration: none;
        font-weight: 700;
    }
    .myads-smart-native__links {
        display: inline-flex;
        align-items: center;
        gap: 12px;
    }
    .myads-smart-native__link {
        color: #64748b;
        font-size: 12px;
        text-decoration: none;
    }
</style>
<div class="myads-smart-native" data-placement="smart-native">
    @if($smartAd->displayImage())
        <div class="myads-smart-native__media">
            <img src="{{ $smartAd->displayImage() }}" alt="{{ $smartAd->displayTitle() }}">
        </div>
    @endif
    <div class="myads-smart-native__body">
        <span class="myads-smart-native__eyebrow">{{ __('messages.smart_ad') }}</span>
        <h3 class="myads-smart-native__title">{{ $smartAd->displayTitle() }}</h3>
        <p class="myads-smart-native__description">{{ \Illuminate\Support\Str::limit($smartAd->displayDescription(), 190) }}</p>
        <div class="myads-smart-native__actions">
            <a class="myads-smart-native__cta" href="{{ $clickUrl }}" target="_blank" rel="noopener noreferrer">{{ __('messages.smart_native_visit_sponsor') }}</a>
            <span class="myads-smart-native__links">
                <a class="myads-smart-native__link" href="{{ $refUrl }}" target="_blank" rel="noopener noreferrer">{{ __('messages.ads_by_site', ['site' => config('app.name')]) }}</a>
                <a class="myads-smart-native__link" href="{{ $reportUrl }}" target="_blank" rel="noopener noreferrer">{{ __('messages.report') }}</a>
            </span>
        </div>
    </div>
</div>
