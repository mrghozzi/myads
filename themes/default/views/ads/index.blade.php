@extends('theme::layouts.master')

@section('content')
{{-- Hero Banner --}}
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%; background-size: cover;">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.advertising') }}</p>
    <p class="section-banner-text">{{ __('messages.manage_ads') }}</p>
</div>

{{-- Quick Navigation Cards --}}
<div class="ads-nav-bar" style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 28px; margin-bottom: 12px;">
    <a href="{{ route('dashboard') }}" class="ads-nav-item" style="display: flex; align-items: center; gap: 8px; padding: 12px 22px; background: linear-gradient(135deg, #615dfa 0%, #8b5cf6 100%); color: #fff; border-radius: 12px; font-weight: 700; font-size: 0.9rem; text-decoration: none; box-shadow: 0 4px 15px rgba(97,93,250,0.25); transition: all 0.3s ease;">
        <i class="fa fa-home"></i>
    </a>
    <a href="{{ route('ads.banners.index') }}" class="ads-nav-item" style="display: flex; align-items: center; gap: 8px; padding: 12px 22px; background: linear-gradient(135deg, #23d2e2 0%, #00b4d8 100%); color: #fff; border-radius: 12px; font-weight: 700; font-size: 0.9rem; text-decoration: none; box-shadow: 0 4px 15px rgba(35,210,226,0.25); transition: all 0.3s ease;">
        <i class="fa fa-image"></i> {{ __('messages.my_banners') }}
    </a>
    <a href="{{ route('ads.links.index') }}" class="ads-nav-item" style="display: flex; align-items: center; gap: 8px; padding: 12px 22px; background: linear-gradient(135deg, #23d2e2 0%, #00b4d8 100%); color: #fff; border-radius: 12px; font-weight: 700; font-size: 0.9rem; text-decoration: none; box-shadow: 0 4px 15px rgba(35,210,226,0.25); transition: all 0.3s ease;">
        <i class="fa fa-link"></i> {{ __('messages.my_links') }}
    </a>
    <a href="{{ route('ads.promote') }}" class="ads-nav-item" style="display: flex; align-items: center; gap: 8px; padding: 12px 22px; background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); color: #fff; border-radius: 12px; font-weight: 700; font-size: 0.9rem; text-decoration: none; box-shadow: 0 4px 15px rgba(245,158,11,0.25); transition: all 0.3s ease;">
        <i class="fa fa-bullhorn"></i> {{ __('messages.promote_your_site') }}
    </a>
</div>

{{-- Stats Overview Cards --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="widget-box" style="padding: 24px; text-align: center; border-left: 4px solid #615dfa;">
        <div style="font-size: 2rem; font-weight: 800; color: #615dfa;">{{ $banners->count() }}</div>
        <div style="font-size: 0.85rem; color: #8f91ac; font-weight: 600; margin-top: 4px;">{{ __('messages.latest_banners') }}</div>
    </div>
    <div class="widget-box" style="padding: 24px; text-align: center; border-left: 4px solid #23d2e2;">
        <div style="font-size: 2rem; font-weight: 800; color: #23d2e2;">{{ $links->count() }}</div>
        <div style="font-size: 0.85rem; color: #8f91ac; font-weight: 600; margin-top: 4px;">{{ __('messages.latest_links') }}</div>
    </div>
    <div class="widget-box" style="padding: 24px; text-align: center; border-left: 4px solid #10b981;">
        <div style="font-size: 2rem; font-weight: 800; color: #10b981;">{{ $banners->where('statu', 1)->count() + $links->where('statu', 1)->count() }}</div>
        <div style="font-size: 0.85rem; color: #8f91ac; font-weight: 600; margin-top: 4px;">{{ __('messages.active') }}</div>
    </div>
    <div class="widget-box" style="padding: 24px; text-align: center; border-left: 4px solid #f59e0b;">
        <div style="font-size: 2rem; font-weight: 800; color: #f59e0b;">{{ $banners->where('statu', '!=', 1)->count() + $links->where('statu', '!=', 1)->count() }}</div>
        <div style="font-size: 0.85rem; color: #8f91ac; font-weight: 600; margin-top: 4px;">{{ __('messages.pending') }}</div>
    </div>
</div>

{{-- Main Content: Banners & Links Tables --}}
<div style="display: grid; grid-template-columns: 1fr; gap: 24px;">

    {{-- Latest Banners --}}
    <div class="widget-box" style="padding: 0; overflow: hidden;">
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 28px; border-bottom: 1px solid #f1f1f5;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #615dfa 0%, #8b5cf6 100%); display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-image" style="color: #fff; font-size: 1rem;"></i>
                </div>
                <h4 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #3e3f5e;">{{ __('messages.latest_banners') }}</h4>
            </div>
            <a href="{{ route('ads.banners.index') }}" style="font-size: 0.85rem; font-weight: 600; color: #615dfa; text-decoration: none; padding: 6px 16px; border-radius: 8px; background: rgba(97,93,250,0.08); transition: all 0.3s ease;">
                {{ __('messages.see_all') }} <i class="fa fa-arrow-right" style="margin-left: 4px;"></i>
            </a>
        </div>
        <div style="padding: 0 28px 24px;">
            @if($banners->count() > 0)
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 16px;">
                <thead>
                    <tr>
                        <th style="padding: 12px 16px; text-align: left; font-size: 0.8rem; font-weight: 700; color: #8f91ac; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f1f1f5;">{{ __('messages.id') }}</th>
                        <th style="padding: 12px 16px; text-align: left; font-size: 0.8rem; font-weight: 700; color: #8f91ac; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f1f1f5;">{{ __('messages.img') }}</th>
                        <th style="padding: 12px 16px; text-align: left; font-size: 0.8rem; font-weight: 700; color: #8f91ac; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f1f1f5;">{{ __('messages.stats') }}</th>
                        <th style="padding: 12px 16px; text-align: left; font-size: 0.8rem; font-weight: 700; color: #8f91ac; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f1f1f5;">{{ __('messages.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($banners as $banner)
                    <tr style="transition: background-color 0.2s ease;">
                        <td style="padding: 14px 16px; border-bottom: 1px solid #f7f7fa; font-weight: 600; color: #3e3f5e;">#{{ $banner->id }}</td>
                        <td style="padding: 14px 16px; border-bottom: 1px solid #f7f7fa;">
                            <img src="{{ asset($banner->img) }}" style="max-height: 45px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);" alt="banner">
                        </td>
                        <td style="padding: 14px 16px; border-bottom: 1px solid #f7f7fa;">
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: rgba(97,93,250,0.08); border-radius: 6px; font-size: 0.8rem; font-weight: 600; color: #615dfa; margin-right: 6px;">
                                <i class="fa fa-eye"></i> {{ $banner->vu }}
                            </span>
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: rgba(35,210,226,0.08); border-radius: 6px; font-size: 0.8rem; font-weight: 600; color: #23d2e2;">
                                <i class="fa fa-mouse-pointer"></i> {{ $banner->clik }}
                            </span>
                        </td>
                        <td style="padding: 14px 16px; border-bottom: 1px solid #f7f7fa;">
                            @if($banner->statu == 1)
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 5px 14px; background: linear-gradient(135deg, #10b981, #059669); color: #fff; border-radius: 20px; font-size: 0.78rem; font-weight: 700;">
                                    <i class="fa fa-check-circle"></i> {{ __('messages.active') }}
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 5px 14px; background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; border-radius: 20px; font-size: 0.78rem; font-weight: 700;">
                                    <i class="fa fa-clock-o"></i> {{ __('messages.pending') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @else
            <div style="text-align: center; padding: 40px 20px;">
                <i class="fa fa-image" style="font-size: 3rem; color: #dedeea; margin-bottom: 12px;"></i>
                <p style="color: #8f91ac; font-weight: 600;">{{ __('messages.no_banners') }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Latest Links --}}
    <div class="widget-box" style="padding: 0; overflow: hidden;">
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 28px; border-bottom: 1px solid #f1f1f5;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #23d2e2 0%, #00b4d8 100%); display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-link" style="color: #fff; font-size: 1rem;"></i>
                </div>
                <h4 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #3e3f5e;">{{ __('messages.latest_links') }}</h4>
            </div>
            <a href="{{ route('ads.links.index') }}" style="font-size: 0.85rem; font-weight: 600; color: #23d2e2; text-decoration: none; padding: 6px 16px; border-radius: 8px; background: rgba(35,210,226,0.08); transition: all 0.3s ease;">
                {{ __('messages.see_all') }} <i class="fa fa-arrow-right" style="margin-left: 4px;"></i>
            </a>
        </div>
        <div style="padding: 0 28px 24px;">
            @if($links->count() > 0)
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 16px;">
                <thead>
                    <tr>
                        <th style="padding: 12px 16px; text-align: left; font-size: 0.8rem; font-weight: 700; color: #8f91ac; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f1f1f5;">{{ __('messages.id') }}</th>
                        <th style="padding: 12px 16px; text-align: left; font-size: 0.8rem; font-weight: 700; color: #8f91ac; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f1f1f5;">{{ __('messages.name') }}</th>
                        <th style="padding: 12px 16px; text-align: left; font-size: 0.8rem; font-weight: 700; color: #8f91ac; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f1f1f5;">{{ __('messages.stats') }}</th>
                        <th style="padding: 12px 16px; text-align: left; font-size: 0.8rem; font-weight: 700; color: #8f91ac; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f1f1f5;">{{ __('messages.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($links as $link)
                    <tr style="transition: background-color 0.2s ease;">
                        <td style="padding: 14px 16px; border-bottom: 1px solid #f7f7fa; font-weight: 600; color: #3e3f5e;">#{{ $link->id }}</td>
                        <td style="padding: 14px 16px; border-bottom: 1px solid #f7f7fa;">
                            <a href="{{ $link->url }}" target="_blank" style="color: #615dfa; font-weight: 600; text-decoration: none; transition: color 0.2s ease;">
                                {{ $link->name }}
                                <i class="fa fa-external-link" style="font-size: 0.7rem; margin-left: 4px; opacity: 0.6;"></i>
                            </a>
                        </td>
                        <td style="padding: 14px 16px; border-bottom: 1px solid #f7f7fa;">
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: rgba(35,210,226,0.08); border-radius: 6px; font-size: 0.8rem; font-weight: 600; color: #23d2e2;">
                                <i class="fa fa-mouse-pointer"></i> {{ $link->clik }}
                            </span>
                        </td>
                        <td style="padding: 14px 16px; border-bottom: 1px solid #f7f7fa;">
                            @if($link->statu == 1)
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 5px 14px; background: linear-gradient(135deg, #10b981, #059669); color: #fff; border-radius: 20px; font-size: 0.78rem; font-weight: 700;">
                                    <i class="fa fa-check-circle"></i> {{ __('messages.active') }}
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 4px; padding: 5px 14px; background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; border-radius: 20px; font-size: 0.78rem; font-weight: 700;">
                                    <i class="fa fa-clock-o"></i> {{ __('messages.pending') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @else
            <div style="text-align: center; padding: 40px 20px;">
                <i class="fa fa-link" style="font-size: 3rem; color: #dedeea; margin-bottom: 12px;"></i>
                <p style="color: #8f91ac; font-weight: 600;">{{ __('messages.no_links') }}</p>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- Hover effects --}}
<style>
    .ads-nav-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
        filter: brightness(1.1);
    }
    .ads-dashboard-table tbody tr:hover {
        background-color: #f8f9ff;
    }
</style>
@endsection
