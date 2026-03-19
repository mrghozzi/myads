@extends('theme::layouts.master')

@section('content')
@php
    $scriptUrl = route('ads.link.script');
    $fixedCode = \App\Support\LinkEmbedCode::buildDirect($scriptUrl, $user->id, '468x60', $extensions_code ?? '');
    $responsiveCode = \App\Support\LinkEmbedCode::buildDirect($scriptUrl, $user->id, '510x320', $extensions_code ?? '');
    $responsive2QuickCode = \App\Support\LinkEmbedCode::buildDirect($scriptUrl, $user->id, 'responsive2', $extensions_code ?? '');
    $responsive2SmartCode = \App\Support\LinkEmbedCode::buildResponsive2Smart($scriptUrl, $user->id, $extensions_code ?? '');
    $fixedPreview = \App\Support\LinkEmbedCode::buildDirect($scriptUrl, $user->id, '468x60');
    $responsivePreview = \App\Support\LinkEmbedCode::buildDirect($scriptUrl, $user->id, '510x320');
    $responsive2Preview = \App\Support\LinkEmbedCode::buildResponsive2Smart($scriptUrl, $user->id);
    $linkCodeTabs = [
        [
            'key' => '468x60',
            'label' => '468x60',
            'title' => 'Your promotion tags 468x60 (1 point)',
            'code' => $fixedCode,
            'preview' => $fixedPreview,
        ],
        [
            'key' => 'responsive',
            'label' => __('messages.responsive'),
            'title' => 'Your promotion tags Responsive (1 point)',
            'code' => $responsiveCode,
            'preview' => $responsivePreview,
        ],
        [
            'key' => 'responsive2',
            'label' => 'Responsive 2',
            'title' => 'Your promotion tags Responsive 2 (1 point)',
            'quick_code' => $responsive2QuickCode,
            'smart_code' => $responsive2SmartCode,
            'preview' => $responsive2Preview,
        ],
    ];
@endphp

<div class="grid grid change-on-desktop">
    <div class="achievement-box secondary" style="background: url({{ theme_asset('img/banner/03.jpg') }}) no-repeat 50%; background-size: cover">
        <div class="achievement-box-info-wrap">
            <img class="achievement-box-image" src="{{ theme_asset('img/banner/link_ads.png') }}" alt="badge-caffeinated-b">

            <div class="achievement-box-info">
                <p class="achievement-box-title">{{ __('messages.codes') }}&nbsp;{{ __('messages.textads') }}</p>
                <p class="achievement-box-text"><b>{{ __('messages.yhtierbpyaci') }}</b></p>
            </div>
        </div>

        <a class="button white-solid" href="{{ route('legacy.l_list') }}">
            {{ __('messages.list') }}&nbsp;{{ __('messages.textads') }}
        </a>
    </div>
</div>

<div class="grid grid">
    <div class="grid-column">
        <div class="widget-box">
            <p class="widget-box-title">Your referral link</p>
            <br />
            <blockquote class="widget-box">
                <center><kbd>{{ route('register', ['ref' => $user->id]) }}</kbd></center>
            </blockquote>
            <br />
            <p class="widget-box-title"><i class="fa fa-share"></i>&nbsp;Share your referral link</p>
            <div class="widget-box-content">
                <div class="social-links multiline align-left">
                    <a class="social-link small facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('register', ['ref' => $user->id])) }}" target="_blank">
                        <i class="fa-brands fa-facebook-f" style="color: #ffffff;"></i>
                    </a>

                    <a class="social-link small" href="https://twitter.com/intent/tweet?text={{ urlencode(config('app.name')) }}&url={{ urlencode(route('register', ['ref' => $user->id])) }}" style="background-color: #011a24;" target="_blank">
                        <i class="fa-brands fa-x-twitter" style="color: #ffffff;"></i>
                    </a>

                    <a class="social-link small youtube" href="https://telegram.me/share/url?url={{ urlencode(route('register', ['ref' => $user->id])) }}&text={{ urlencode(config('app.name')) }}" style="background-color: #0088cc;" target="_blank">
                        <i class="fa-brands fa-telegram" style="color: #ffffff;"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="tab-box" style="margin-top: 24px;">
            <div class="tab-box-options">
                @foreach($linkCodeTabs as $index => $tab)
                    <div class="tab-box-option {{ $index === 0 ? 'active' : '' }}">
                        <p class="tab-box-option-title">
                            {{ $tab['label'] }}
                            @if($tab['key'] === 'responsive2')
                                &nbsp;<span class="badge badge-info"><font face="Comic Sans MS">beta<br></font></span>
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>

            <div class="tab-box-items">
                @foreach($linkCodeTabs as $index => $tab)
                    <div class="tab-box-item" style="display: {{ $index === 0 ? 'block' : 'none' }};">
                        <div class="tab-box-item-content">
                            <p class="tab-box-item-title">{{ $tab['title'] }}</p>
                            <hr />

                            @if($tab['key'] !== 'responsive2')
                                <div class="well" style="color: black;">
                                    <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');">{{ $tab['code'] }}</textarea>
                                </div>

                                <div class="tab-box-item-paragraph">
                                    <center>{!! $tab['preview'] !!}</center>
                                </div>
                            @else
                                <p class="widget-box-text" style="margin-bottom: 18px;">
                                    Responsive 2 adds a smarter card layout for narrow containers. The quick code stays direct, while the smart code measures the parent width and chooses the best layout automatically.
                                </p>

                                <div style="display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
                                    <div class="well" style="color: black; margin: 0;">
                                        <p class="widget-box-title" style="margin-bottom: 12px;">Quick Code</p>
                                        <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');">{{ $tab['quick_code'] }}</textarea>
                                    </div>

                                    <div class="well" style="color: black; margin: 0;">
                                        <p class="widget-box-title" style="margin-bottom: 12px;">Recommended Smart Code</p>
                                        <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');">{{ $tab['smart_code'] }}</textarea>
                                    </div>
                                </div>

                                <div class="tab-box-item-paragraph" style="margin-top: 24px;">
                                    <div style="max-width: 760px; margin: 0 auto;">
                                        {!! $tab['preview'] !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function initTabBox(tabBox) {
        const optionsWrap = tabBox.querySelector('.tab-box-options');
        const itemsWrap = tabBox.querySelector('.tab-box-items');
        const tabOptions = optionsWrap ? optionsWrap.querySelectorAll('.tab-box-option') : [];
        const tabItems = itemsWrap ? itemsWrap.querySelectorAll('.tab-box-item') : [];

        tabOptions.forEach((option, index) => {
            option.addEventListener('click', function() {
                tabOptions.forEach((opt) => opt.classList.remove('active'));
                this.classList.add('active');

                tabItems.forEach((item) => {
                    item.style.display = 'none';
                });

                if (tabItems[index]) {
                    tabItems[index].style.display = 'block';
                }
            });
        });
    }

    document.querySelectorAll('.tab-box').forEach(initTabBox);
});
</script>
@endsection
