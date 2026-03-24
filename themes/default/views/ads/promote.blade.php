@extends('theme::layouts.master')

@section('content')
<div class="promotion-shell">
    <div class="section-banner" style="background: linear-gradient(135deg, #615dfa 0%, #40d4f3 100%); min-height: 200px; border-radius: 16px; margin-bottom: 30px; position: relative; overflow: hidden; display: flex; align-items: center; padding: 0 50px;">
        <div class="section-banner-content" style="z-index: 2; position: relative; max-width: 600px;">
            <p class="section-banner-title" style="font-size: 2.5rem; font-weight: 800; color: #fff; margin-bottom: 10px; line-height: 1.2;">{{ __('messages.promote_your_site') }}</p>
            <p class="section-banner-text" style="color: rgba(255, 255, 255, 0.95); font-size: 1.1rem; line-height: 1.5;">{{ __('messages.get_traffic') }}</p>
        </div>
        <div class="section-banner-decoration" style="position: absolute; right: -50px; bottom: -50px; width: 300px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 50%; pointer-events: none;"></div>
        <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="promote-icon" style="position: absolute; right: 60px; top: 50%; transform: translateY(-50%); height: 140px; opacity: 0.2; pointer-events: none;">
    </div>

    @if(session('success'))
    <div class="alert alert-success shadow-sm mb-4" style="border-radius: 12px; border: none; padding: 15px 25px;">
        <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger shadow-sm mb-4" style="border-radius: 12px; border: none;">
        <ul style="list-style: none; padding: 0; margin: 0;">
            @foreach($errors->all() as $error)
                <li><i class="fa fa-exclamation-triangle me-2"></i> {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @php
        $activeTab = request('p', 'all');
        $bannerSizes = \App\Support\BannerSizeCatalog::ordered();
    @endphp

    <div class="grid grid-3-9">
        {{-- Sidebar Navigation --}}
        <div class="grid-column">
            <div class="widget-box shadow-sm" style="border-radius: 12px; border: none; padding: 10px;">
                <div class="sidebar-menu">
                    <a href="{{ route('ads.promote', ['p' => 'banners']) }}" class="sidebar-menu-item {{ $activeTab === 'banners' ? 'active' : '' }}">
                        <i class="fa fa-image me-3"></i> {{ __('messages.bannads') }}
                    </a>
                    <a href="{{ route('ads.promote', ['p' => 'link']) }}" class="sidebar-menu-item {{ $activeTab === 'link' ? 'active' : '' }}">
                        <i class="fa fa-link me-3"></i> {{ __('messages.textads') }}
                    </a>
                    <a href="{{ route('ads.promote', ['p' => 'exchange']) }}" class="sidebar-menu-item {{ $activeTab === 'exchange' ? 'active' : '' }}">
                        <i class="fa fa-exchange-alt me-3"></i> {{ __('messages.exvisit') }}
                    </a>
                    <hr style="margin: 10px 0; border-top: 1px solid #eee;">
                    <a href="{{ route('ads.promote') }}" class="sidebar-menu-item {{ $activeTab === 'all' ? 'active' : '' }}">
                        <i class="fa fa-th-large me-3"></i> {{ __('messages.all_methods') }}
                    </a>
                </div>
            </div>

            <div class="widget-box shadow-sm mt-4" style="border-radius: 12px; border: none; padding: 20px;">
                <h6 style="font-weight: 700; margin-bottom: 10px; color: #333;">{{ __('messages.quick_tip') }}</h6>
                <p style="font-size: 0.85rem; color: #777; line-height: 1.5;">
                    {{ __('messages.promote_tip_desc') }}
                </p>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="grid-column">
            {{-- BANNERS SECTION --}}
            @if($activeTab === 'banners' || $activeTab === 'all')
            <div class="widget-box shadow-lg mb-4" style="border-radius: 16px; border: none; padding: 30px;">
                <div class="widget-box-header d-flex justify-content-between align-items-center mb-4">
                    <h5 class="widget-box-title" style="font-size: 1.25rem; font-weight: 700; color: #615dfa;">
                        <i class="fa fa-image me-2"></i> {{ __('messages.bannads') }}
                    </h5>
                    <span class="badge" style="background: rgba(97, 93, 250, 0.1); color: #615dfa; padding: 5px 12px; border-radius: 20px; font-weight: 600;">-1 {{ __('messages.point') }}</span>
                </div>
                <form method="post" action="{{ route('ads.banners.store') }}" class="form-modern">
                    @csrf
                    <div class="form-row split mb-3">
                        <div class="form-item">
                            <div class="form-input small active shadow-sm">
                                <label>{{ __('messages.name_ads') }}</label>
                                <input type="text" name="name" value="{{ old('name') }}" required placeholder="{{ __('messages.name_ads_placeholder') }}">
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input small active shadow-sm">
                                <label>{{ __('messages.url_link') }}</label>
                                <input type="url" name="url" value="{{ old('url') }}" required placeholder="{{ __('messages.url_link_placeholder') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-row split mb-4">
                        <div class="form-item">
                            <div class="form-select shadow-sm" style="border: 1px solid #eee; border-radius: 8px;">
                                <label>{{ __('messages.banner_size') }}</label>
                                <select name="px" required>
                                    @foreach($bannerSizes as $size)
                                        <option value="{{ $size['value'] }}" {{ old('px') == $size['value'] ? 'selected' : '' }}>{{ $size['label'] }}</option>
                                    @endforeach
                                </select>
                                <svg class="form-select-icon icon-small-arrow"><use xlink:href="#svg-small-arrow"></use></svg>
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input small active shadow-sm">
                                <label>{{ __('messages.image_link') }}</label>
                                <input type="text" name="img" value="{{ old('img') }}" required placeholder="{{ __('messages.image_link_placeholder') }}">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="button primary big shadow-sm" style="padding: 0 40px; border-radius: 8px;">
                            <i class="fa fa-plus me-2"></i> {{ __('messages.add') }}
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- TEXT ADS SECTION --}}
            @if($activeTab === 'link' || $activeTab === 'all')
            <div class="widget-box shadow-lg mb-4" style="border-radius: 16px; border: none; padding: 30px;">
                <div class="widget-box-header d-flex justify-content-between align-items-center mb-4">
                    <h5 class="widget-box-title" style="font-size: 1.25rem; font-weight: 700; color: #40d4f3;">
                        <i class="fa fa-link me-2"></i> {{ __('messages.textads') }}
                    </h5>
                    <span class="badge" style="background: rgba(64, 212, 243, 0.1); color: #40d4f3; padding: 5px 12px; border-radius: 20px; font-weight: 600;">-1 {{ __('messages.point') }}</span>
                </div>
                <form method="post" action="{{ route('ads.links.store') }}" class="form-modern">
                    @csrf
                    <div class="form-row split mb-3">
                        <div class="form-item">
                            <div class="form-input small active shadow-sm">
                                <label>{{ __('messages.name_ads') }}</label>
                                <input type="text" name="name" value="{{ old('name') }}" required placeholder="{{ __('messages.text_ads_placeholder') }}">
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input small active shadow-sm">
                                <label>{{ __('messages.url_link') }}</label>
                                <input type="url" name="url" value="{{ old('url') }}" required placeholder="{{ __('messages.url_link_placeholder') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-4">
                        <div class="form-item">
                            <div class="form-input small full shadow-sm" style="border: 1px solid #eee; border-radius: 8px;">
                                <textarea name="txt" placeholder="{{ __('messages.was_desc') }}" required style="min-height: 100px;">{{ old('txt') }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="button secondary big shadow-sm" style="padding: 0 40px; border-radius: 8px; background: #40d4f3 !important;">
                            <i class="fa fa-plus me-2" style="color: #fff;"></i> <span style="color: #fff;">{{ __('messages.add') }}</span>
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- EXCHANGE SECTION --}}
            @if($activeTab === 'exchange' || $activeTab === 'all')
            <div class="widget-box shadow-lg mb-4" style="border-radius: 16px; border: none; padding: 30px;">
                <div class="widget-box-header d-flex justify-content-between align-items-center mb-4">
                    <h5 class="widget-box-title" style="font-size: 1.25rem; font-weight: 700; color: #fd4350;">
                        <i class="fa fa-exchange-alt me-2"></i> {{ __('messages.exvisit') }}
                    </h5>
                    <span class="badge" style="background: rgba(253, 67, 80, 0.1); color: #fd4350; padding: 5px 12px; border-radius: 20px; font-weight: 600;">{{ __('messages.dynamic_cost') }}</span>
                </div>
                <form method="post" action="{{ route('visits.store') }}" class="form-modern">
                    @csrf
                    <div class="form-row split mb-3">
                        <div class="form-item">
                            <div class="form-input small active shadow-sm">
                                <label>{{ __('messages.name_ads') }}</label>
                                <input type="text" name="name" value="{{ old('name') }}" required placeholder="{{ __('messages.exchange_name_placeholder') }}">
                            </div>
                        </div>
                        <div class="form-item">
                            <div class="form-input small active shadow-sm">
                                <label>{{ __('messages.url_link') }}</label>
                                <input type="url" name="url" value="{{ old('url') }}" required placeholder="{{ __('messages.url_link_placeholder') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-row split mb-4">
                        <div class="form-item">
                            <div class="form-select shadow-sm" style="border: 1px solid #eee; border-radius: 8px;">
                                <label>{{ __('messages.visits_time') }}</label>
                                <select name="tims" required>
                                    <option value="1" {{ old('tims') == '1' ? 'selected' : '' }}>10s / -1 {{ __('messages.pts_short') }}</option>
                                    <option value="2" {{ old('tims') == '2' ? 'selected' : '' }}>20s / -2 {{ __('messages.pts_short') }}</option>
                                    <option value="3" {{ old('tims') == '3' ? 'selected' : '' }}>30s / -5 {{ __('messages.pts_short') }}</option>
                                    <option value="4" {{ old('tims') == '4' ? 'selected' : '' }}>60s / -10 {{ __('messages.pts_short') }}</option>
                                </select>
                                <svg class="form-select-icon icon-small-arrow"><use xlink:href="#svg-small-arrow"></use></svg>
                            </div>
                        </div>
                        <div class="form-item d-flex align-items-center justify-content-end">
                            <button type="submit" class="button primary big shadow-sm" style="padding: 0 40px; border-radius: 8px; background: #fd4350 !important;">
                                <i class="fa fa-plus me-2"></i> {{ __('messages.add') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .sidebar-menu { display: flex; flex-direction: column; }
    .sidebar-menu-item {
        padding: 12px 15px;
        border-radius: 8px;
        color: #3e3f5e;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        text-decoration: none !important;
    }
    .sidebar-menu-item:hover { background: #f8f8fb; color: #615dfa; }
    .sidebar-menu-item.active { background: #615dfa; color: #fff !important; }
    
    .form-input.small.active { border: 1px solid #eee; border-radius: 8px; }
    .form-input.small.active label { background: #fff; padding: 0 5px; left: 10px; top: -10px; color: #615dfa; font-weight: 600; }
    
    .shadow-lg { box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important; }
    .shadow-sm { box-shadow: 0 2px 8px rgba(0,0,0,0.04) !important; }
    
    .d-flex { display: flex !important; }
    .justify-content-between { justify-content: space-between !important; }
    .justify-content-end { justify-content: flex-end !important; }
    .align-items-center { align-items: center !important; }
    .me-2 { margin-right: 0.5rem; }
    .me-3 { margin-right: 0.75rem; }
    .mb-4 { margin-bottom: 1.5rem; }
    .mb-3 { margin-bottom: 1rem; }
    .mt-4 { margin-top: 1.5rem; }
    .w-100 { width: 100%; }
</style>
@endsection
