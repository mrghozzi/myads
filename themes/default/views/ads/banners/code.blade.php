@extends('theme::layouts.master')

@section('content')
<div class="grid grid change-on-desktop" >
       <div class="achievement-box secondary" style="background: url({{ theme_asset('img/banner/03.jpg') }}) no-repeat 50%; background-size: cover " >
          <!-- ACHIEVEMENT BOX INFO WRAP -->
          <div class="achievement-box-info-wrap">
            <!-- ACHIEVEMENT BOX IMAGE -->
            <img class="achievement-box-image" src="{{ theme_asset('img/banner/banner_ads.png') }}" alt="badge-caffeinated-b">
            <!-- /ACHIEVEMENT BOX IMAGE -->

            <!-- ACHIEVEMENT BOX INFO -->
            <div class="achievement-box-info">
              <!-- ACHIEVEMENT BOX TITLE -->
              <p class="achievement-box-title">{{ __('messages.codes') }}&nbsp;{{ __('messages.bannads') }}</p>
              <!-- /ACHIEVEMENT BOX TITLE -->

              <!-- ACHIEVEMENT BOX TEXT -->
              <p class="achievement-box-text"><b>{{ __('messages.yhtierbpyaci') }}</b></p>
              <!-- /ACHIEVEMENT BOX TEXT -->
            </div>
            <!-- /ACHIEVEMENT BOX INFO -->
          </div>
          <!-- /ACHIEVEMENT BOX INFO WRAP -->

          <!-- BUTTON -->
          <a class="button white-solid" href="{{ route('legacy.b_list') }}">
          {{ __('messages.list') }}&nbsp;{{ __('messages.bannads') }}
          </a>
          <!-- /BUTTON -->
       </div>
</div>

<div class="grid grid">
  <div class="grid-column">
    <div class="widget-box">
         <p class="widget-box-title">{{ __('messages.your_referral_link') }}</p>
         <br />
         <blockquote class="widget-box">
         <center><kbd>{{ route('register', ['ref' => $user->id]) }}</kbd></center>
         </blockquote>
         <br />
         <p class="widget-box-title"><i class="fa fa-share"></i>&nbsp;{{ __('messages.share_your_referral_link') }}</p>
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

    @php
        $quickBannerCodeTabs = \App\Support\BannerSizeCatalog::ordered();
        $advancedBannerCodeTabs = array_merge(
            $quickBannerCodeTabs,
            [
                [
                    'value' => 'responsive2',
                    'label' => 'Responsive 2',
                ],
                [
                    'value' => 'responsive',
                    'label' => __('messages.responsive'),
                ],
            ]
        );
        $bannerEmbedUrl = route('ads.embed.banner');
        $bannerServingUrl = route('ads.script');
    @endphp

    <style>
      .myads-banner-code-preview,
      .myads-banner-code-preview * {
        box-sizing: border-box;
      }

      .myads-banner-code-preview {
        margin-top: 20px;
        border: 1px solid #e8eaed;
        border-radius: 18px;
        padding: 16px;
        background: #ffffff;
        box-shadow: 0 10px 24px rgba(94, 92, 154, 0.06);
      }

      .myads-banner-code-preview__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
        flex-wrap: wrap;
      }

      .myads-banner-code-preview__pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border: 1px solid #dadce0;
        border-radius: 999px;
        background: #ffffff;
        color: #5f6368;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
      }

      .myads-banner-code-preview__pill::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #5f8def;
      }

      .myads-banner-code-preview__hint {
        margin: 0;
        color: #6b7280;
        font-size: 12px;
        line-height: 1.55;
        max-width: 540px;
      }

      .myads-banner-code-preview__frame {
        min-height: 172px;
        padding: 16px;
        border: 1px solid #e8eaed;
        border-radius: 14px;
        background: linear-gradient(180deg, #fbfcff 0%, #ffffff 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
      }

      .myads-banner-code-preview__frame > * {
        max-width: 100%;
      }

      .myads-banner-code-preview__stage {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .myads-banner-code-preview--responsive2 .myads-banner-code-preview__frame {
        min-height: 210px;
        padding: 18px;
      }
    </style>

    <div class="widget-box" style="margin-top: 24px;">
      <div class="widget-box-content">
        <div class="code-mode-switch" style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 18px;">
          <button type="button" class="button secondary" data-code-mode-button="quick" data-active="true">{{ __('messages.quick_code') }}</button>
          <button type="button" class="button tertiary" data-code-mode-button="advanced">{{ __('messages.advanced_code') }}</button>
        </div>

        <div data-code-mode-panel="quick" style="display: block;">
          <p class="widget-box-title">{{ __('messages.quick_code') }}</p>
          <p style="margin: 8px 0 18px;">{{ __('messages.quick_code_desc') }}</p>

          <div class="tab-box">
            <div class="tab-box-options">
              @foreach($quickBannerCodeTabs as $index => $tab)
              <div class="tab-box-option {{ $index === 0 ? 'active' : '' }}">
                <p class="tab-box-option-title">{{ $tab['label'] }}</p>
              </div>
              @endforeach
            </div>

            <div class="tab-box-items">
              @foreach($quickBannerCodeTabs as $index => $tab)
              @php($embedCode = \App\Support\BannerEmbedCode::buildLegacy(route('ads.script'), $user->id, $tab['value'], $extensions_code ?? ''))
              <div class="tab-box-item" style="display: {{ $index === 0 ? 'block' : 'none' }};">
                <div class="tab-box-item-content">
                  <p class="tab-box-item-title">{{ __('messages.your_quick_banner_code', ['label' => $tab['label']]) }}</p>
                  <hr />
                  <div class="well" style="color: black;">
                    <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');">{{ $embedCode }}</textarea>
                  </div>
                  <div class="tab-box-item-paragraph">
                    <center>{!! $embedCode !!}</center>
                  </div>
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>

        <div data-code-mode-panel="advanced" style="display: none;">
          <p class="widget-box-title">{{ __('messages.advanced_code') }}</p>
          <p style="margin: 8px 0 18px;">{{ __('messages.advanced_code_desc') }}</p>

          <div class="tab-box">
            <div class="tab-box-options">
              @foreach($advancedBannerCodeTabs as $index => $tab)
              <div class="tab-box-option {{ $index === 0 ? 'active' : '' }}">
                <p class="tab-box-option-title">
                  {{ $tab['label'] }}
                  @if(in_array($tab['value'], ['responsive', 'responsive2'], true))
                    &nbsp;<span class="badge badge-info"><font face="Comic Sans MS">beta<br></font></span>
                  @endif
                </p>
              </div>
              @endforeach
            </div>

            <div class="tab-box-items">
              @foreach($advancedBannerCodeTabs as $index => $tab)
              @php($embedCode = \App\Support\BannerEmbedCode::build($bannerEmbedUrl, $user->id, $tab['value'], $extensions_code ?? ''))
              @php($compatibleCode = \App\Support\BannerEmbedCode::buildInlineLoader($bannerServingUrl, $user->id, $tab['value'], $extensions_code ?? ''))
              <div class="tab-box-item" style="display: {{ $index === 0 ? 'block' : 'none' }};">
                <div class="tab-box-item-content">
                  @if($tab['value'] === 'responsive2')
                    <p class="tab-box-item-title">{{ __('messages.recommended_smart_code') }} {{ $tab['label'] }} (1 {{ __('messages.point') }})</p>
                    <p style="margin: 8px 0 18px; color: #5d6488; line-height: 1.7;">
                      {{ __('messages.responsive_2_desc', ['app' => config('app.name')]) }}
                    </p>
                    <hr />
                    <div class="well" style="color: black;">
                      <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');">{{ $embedCode }}</textarea>
                    </div>
                    <div class="well" style="color: black; margin-top: 16px;">
                      <p class="widget-box-title" style="margin-bottom: 12px;">{{ __('messages.advanced_code') }}</p>
                      <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');">{{ $compatibleCode }}</textarea>
                    </div>
                    <div class="myads-banner-code-preview myads-banner-code-preview--responsive2">
                      <div class="myads-banner-code-preview__header">
                        <span class="myads-banner-code-preview__pill">Responsive 2</span>
                        <p class="myads-banner-code-preview__hint">{{ __('messages.responsive_2_preview_desc', ['app' => config('app.name')]) }}</p>
                      </div>
                      <div class="myads-banner-code-preview__frame">
                        <div class="myads-banner-code-preview__stage">
                          {!! $embedCode !!}
                        </div>
                      </div>
                    </div>
                  @else
                    <p class="tab-box-item-title">{{ __('messages.your_advanced_promotion_tags', ['label' => $tab['label']]) }} (1 {{ __('messages.point') }})</p>
                    <hr />
                    <div class="well" style="color: black;">
                      <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');">{{ $embedCode }}</textarea>
                    </div>
                    <div class="well" style="color: black; margin-top: 16px;">
                      <p class="widget-box-title" style="margin-bottom: 12px;">{{ __('messages.advanced_code') }}</p>
                      <textarea class="form-control" type="text" readonly onclick="this.select(); document.execCommand('copy');">{{ $compatibleCode }}</textarea>
                    </div>
                    <div class="tab-box-item-paragraph">
                      <center>{!! $embedCode !!}</center>
                    </div>
                  @endif
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function initTabBox(tabBox) {
        const children = Array.from(tabBox.children);
        const optionsWrap = children.find((child) => child.classList && child.classList.contains('tab-box-options'));
        const itemsWrap = children.find((child) => child.classList && child.classList.contains('tab-box-items'));
        const tabOptions = optionsWrap ? optionsWrap.querySelectorAll('.tab-box-option') : [];
        const tabItems = itemsWrap ? itemsWrap.querySelectorAll('.tab-box-item') : [];

        tabOptions.forEach((option, index) => {
            option.addEventListener('click', function() {
                tabOptions.forEach((opt) => opt.classList.remove('active'));
                this.classList.add('active');

                tabItems.forEach((item) => item.style.display = 'none');
                if (tabItems[index]) {
                    tabItems[index].style.display = 'block';
                }
            });
        });
    }

    document.querySelectorAll('.tab-box').forEach(initTabBox);

    const modeButtons = document.querySelectorAll('[data-code-mode-button]');
    const modePanels = document.querySelectorAll('[data-code-mode-panel]');

    function setMode(mode) {
        modeButtons.forEach((button) => {
            const isActive = button.getAttribute('data-code-mode-button') === mode;
            button.setAttribute('data-active', isActive ? 'true' : 'false');
            button.classList.toggle('secondary', isActive);
            button.classList.toggle('tertiary', !isActive);
        });

        modePanels.forEach((panel) => {
            panel.style.display = panel.getAttribute('data-code-mode-panel') === mode ? 'block' : 'none';
        });
    }

    modeButtons.forEach((button) => {
        button.addEventListener('click', function() {
            setMode(this.getAttribute('data-code-mode-button'));
        });
    });

    setMode('quick');
});
</script>
@endsection
