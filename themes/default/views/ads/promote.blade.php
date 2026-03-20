@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%; background-size: cover;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="promote-icon">
    <p class="section-banner-title">{{ __('messages.promote_your_site') }}</p>
    <p class="section-banner-text">{{ __('messages.get_traffic') }}</p>
</div>

@if(session('success'))
<div class="grid grid">
    <div class="grid-column">
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    </div>
</div>
@endif

@if($errors->any())
<div class="grid grid">
    <div class="grid-column">
        <div class="alert alert-danger" role="alert">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Determine active tab from ?p= parameter --}}
@php
    $activeTab = request('p', 'banners');
    $bannerSizes = \App\Support\BannerSizeCatalog::ordered();
@endphp

{{-- DIRECT TAB VIEW (when ?p= is specified) --}}
@if($activeTab === 'banners')
{{-- ========== BANNERS FORM ========== --}}
<div class="grid grid">
  <div class="grid-column">
    <div class="widget-box">
         <p class="widget-box-title">{{ __('messages.bannads') }}</p>
         <br />
      <form method="post" class="form" action="{{ route('ads.banners.store') }}">
         @csrf
         <div class="form-row split">
                  <div class="form-item">
                    <div class="form-input small active">
                      <label>{{ __('messages.name_ads') }}</label>
                      <input type="text" name="name" value="{{ old('name') }}" required>
                    </div>
                  </div>
                  <div class="form-item">
                    <div class="form-input small active">
                      <label>{{ __('messages.url_link') }}</label>
                      <input type="url" name="url" value="{{ old('url') }}" required>
                    </div>
                  </div>
         </div>
         <div class="form-row split">
                  <div class="form-item">
                    <div class="form-select">
                      <label>{{ __('messages.banner_size') }}</label>
                      <select name="px" required>
                        @foreach($bannerSizes as $size)
                          <option value="{{ $size['value'] }}" {{ old('px') == $size['value'] ? 'selected' : '' }}>{{ $size['label'] }} (-1 pts)</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="form-item">
                    <div class="form-input small active">
                      <label>{{ __('messages.image_link') }}</label>
                      <input type="text" name="img" value="{{ old('img') }}" required>
                    </div>
                  </div>
         </div>
         <div class="form-row split">
                    <div class="form-item">
                      <div class="form-row split">
                           <button type="submit" class="button primary">{{ __('messages.add') }}</button>
                      </div>
                    </div>
         </div>
      </form>
    </div>
  </div>
</div>

@elseif($activeTab === 'link')
{{-- ========== TEXT ADS / LINKS FORM ========== --}}
<div class="grid grid">
  <div class="grid-column">
    <div class="widget-box">
         <p class="widget-box-title">{{ __('messages.textads') }}</p>
         <br />
      <form method="post" class="form" action="{{ route('ads.links.store') }}">
         @csrf
         <div class="form-row split">
                  <div class="form-item">
                    <div class="form-input small active">
                      <label>{{ __('messages.name_ads') }}</label>
                      <input type="text" name="name" value="{{ old('name') }}" required>
                    </div>
                  </div>
                  <div class="form-item">
                    <div class="form-input small active">
                      <label>{{ __('messages.url_link') }}</label>
                      <input type="url" name="url" value="{{ old('url') }}" required>
                    </div>
                  </div>
         </div>
         <div class="form-row split">
                  <div class="form-item">
                    <div class="form-input small full">
                      <textarea name="txt" placeholder="{{ __('messages.was_desc') }}" required>{{ old('txt') }}</textarea>
                    </div>
                  </div>
         </div>
         <div class="form-row split">
                    <div class="form-item">
                      <div class="form-row split">
                           <button type="submit" class="button primary">{{ __('messages.add') }}</button>
                      </div>
                    </div>
         </div>
      </form>
    </div>
  </div>
</div>

@elseif($activeTab === 'exchange')
{{-- ========== EXCHANGE VISITS FORM ========== --}}
<div class="grid grid">
  <div class="grid-column">
    <div class="widget-box">
         <p class="widget-box-title">{{ __('messages.exvisit') }}</p>
         <br />
      <form method="post" class="form" action="{{ route('visits.store') }}">
         @csrf
         <div class="form-row split">
                  <div class="form-item">
                    <div class="form-input small active">
                      <label>{{ __('messages.name_ads') }}</label>
                      <input type="text" name="name" value="{{ old('name') }}" required>
                    </div>
                  </div>
                  <div class="form-item">
                    <div class="form-input small active">
                      <label>{{ __('messages.url_link') }}</label>
                      <input type="url" name="url" value="{{ old('url') }}" required>
                    </div>
                  </div>
         </div>
         <div class="form-row split">
                    <div class="form-item">
                    <div class="form-select">
                      <label>{{ __('messages.visits_time') }}</label>
                      <select name="tims" required>
                        <option value="1" {{ old('tims') == '1' ? 'selected' : '' }}>10s / -1 pts</option>
                        <option value="2" {{ old('tims') == '2' ? 'selected' : '' }}>20s / -2 pts</option>
                        <option value="3" {{ old('tims') == '3' ? 'selected' : '' }}>30s / -5 pts</option>
                        <option value="4" {{ old('tims') == '4' ? 'selected' : '' }}>60s / -10 pts</option>
                      </select>
                    </div>
                    </div>
                    <div class="form-item">
                      <div class="form-row split">
                           <button type="submit" class="button primary">{{ __('messages.add') }}</button>
                      </div>
                    </div>
         </div>
      </form>
    </div>
  </div>
</div>

@else
{{-- ========== DEFAULT: TABBED VIEW (all 3 forms) ========== --}}
<div class="grid grid">
  <div class="grid-column">
    <div class="tab-box">
          <div class="tab-box-options">
            <div class="tab-box-option active">
              <p class="tab-box-option-title">{{ __('messages.bannads') }}</p>
            </div>
            <div class="tab-box-option">
              <p class="tab-box-option-title">{{ __('messages.textads') }}</p>
            </div>
            <div class="tab-box-option">
              <p class="tab-box-option-title">{{ __('messages.exvisit') }}</p>
            </div>
          </div>

          <div class="tab-box-items">
            {{-- TAB 1: Banners --}}
            <div class="tab-box-item" style="display: block;">
              <div class="tab-box-item-content">
                <br />
                <form method="post" class="form" action="{{ route('ads.banners.store') }}">
                    @csrf
                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-input small active">
                                <label>{{ __('messages.name_ads') }}</label>
                                <input type="text" name="name" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input small active">
                                <label>{{ __('messages.url_link') }}</label>
                                <input type="url" name="url" value="{{ old('url') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-select">
                                <label>{{ __('messages.banner_size') }}</label>
                                <select name="px" required>
                                    @foreach($bannerSizes as $size)
                                        <option value="{{ $size['value'] }}" {{ old('px') == $size['value'] ? 'selected' : '' }}>{{ $size['label'] }} (-1 pts)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input small active">
                                <label>{{ __('messages.image_link') }}</label>
                                <input type="text" name="img" value="{{ old('img') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-row split">
                                <button type="submit" class="button primary">{{ __('messages.add') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
              </div>
            </div>

            {{-- TAB 2: Text Ads --}}
            <div class="tab-box-item" style="display: none;">
              <div class="tab-box-item-content">
                <br />
                <form method="post" class="form" action="{{ route('ads.links.store') }}">
                    @csrf
                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-input small active">
                                <label>{{ __('messages.name_ads') }}</label>
                                <input type="text" name="name" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input small active">
                                <label>{{ __('messages.url_link') }}</label>
                                <input type="url" name="url" value="{{ old('url') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-input small full">
                                <textarea name="txt" placeholder="{{ __('messages.was_desc') }}" required>{{ old('txt') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-row split">
                                <button type="submit" class="button primary">{{ __('messages.add') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
              </div>
            </div>

            {{-- TAB 3: Exchange Visits --}}
            <div class="tab-box-item" style="display: none;">
              <div class="tab-box-item-content">
                <br />
                <form method="post" class="form" action="{{ route('visits.store') }}">
                    @csrf
                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-input small active">
                                <label>{{ __('messages.name_ads') }}</label>
                                <input type="text" name="name" value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input small active">
                                <label>{{ __('messages.url_link') }}</label>
                                <input type="url" name="url" value="{{ old('url') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-select">
                                <label>{{ __('messages.visits_time') }}</label>
                                <select name="tims" required>
                                    <option value="1">10s / -1 pts</option>
                                    <option value="2">20s / -2 pts</option>
                                    <option value="3">30s / -5 pts</option>
                                    <option value="4">60s / -10 pts</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-row split">
                                <button type="submit" class="button primary">{{ __('messages.add') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
              </div>
            </div>
          </div>
    </div>
  </div>
</div>
@endif
@endsection
