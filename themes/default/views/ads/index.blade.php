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
    <a href="{{ route('ads.smart.index') }}" class="ads-nav-item" style="display: flex; align-items: center; gap: 8px; padding: 12px 22px; background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 55%, #38bdf8 100%); color: #fff; border-radius: 12px; font-weight: 700; font-size: 0.9rem; text-decoration: none; box-shadow: 0 4px 15px rgba(29,78,216,0.28); transition: all 0.3s ease;">
        <i class="fa fa-crosshairs"></i> {{ __('messages.smart_ads') }}
    </a>
    <a href="{{ route('ads.promote') }}" class="ads-nav-item" style="display: flex; align-items: center; gap: 8px; padding: 12px 22px; background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); color: #fff; border-radius: 12px; font-weight: 700; font-size: 0.9rem; text-decoration: none; box-shadow: 0 4px 15px rgba(245,158,11,0.25); transition: all 0.3s ease;">
        <i class="fa fa-bullhorn"></i> {{ __('messages.promote_your_site') }}
    </a>
    <a href="{{ route('ads.posts.index') }}" class="ads-nav-item" style="display: flex; align-items: center; gap: 8px; padding: 12px 22px; background: linear-gradient(135deg, #1d4ed8 0%, #38bdf8 100%); color: #fff; border-radius: 12px; font-weight: 700; font-size: 0.9rem; text-decoration: none; box-shadow: 0 4px 15px rgba(29,78,216,0.25); transition: all 0.3s ease;">
        <i class="fa fa-rocket"></i> {{ __('messages.status_promotions_title') }}
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
    <div class="widget-box" style="padding: 24px; text-align: center; border-left: 4px solid #1d4ed8;">
        <div style="font-size: 2rem; font-weight: 800; color: #1d4ed8;">{{ $smartAds->count() }}</div>
        <div style="font-size: 0.85rem; color: #8f91ac; font-weight: 600; margin-top: 4px;">{{ __('messages.smart_ads') }}</div>
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

    <div class="widget-box" style="padding: 0; overflow: hidden;">
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 28px; border-bottom: 1px solid #f1f1f5; background: linear-gradient(135deg, rgba(15,23,42,.02) 0%, rgba(29,78,216,.04) 100%);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 60%, #38bdf8 100%); display: flex; align-items: center; justify-content: center;">
                    <i class="fa fa-crosshairs" style="color: #fff; font-size: 1rem;"></i>
                </div>
                <div>
                    <h4 style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #3e3f5e;">{{ __('messages.smart_ads') }}</h4>
                    <p style="margin: 4px 0 0; font-size: .85rem; color: #8f91ac;">{{ __('messages.smart_targeting_summary') }}</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('ads.smart.code') }}" style="font-size: 0.85rem; font-weight: 600; color: #1d4ed8; text-decoration: none; padding: 6px 16px; border-radius: 8px; background: rgba(29,78,216,0.08);">
                    {{ __('messages.code') }}
                </a>
                <a href="{{ route('ads.smart.index') }}" style="font-size: 0.85rem; font-weight: 600; color: #fff; text-decoration: none; padding: 8px 16px; border-radius: 10px; background: linear-gradient(135deg, #0f172a 0%, #1d4ed8 60%, #38bdf8 100%);">
                    {{ __('messages.see_all') }}
                </a>
            </div>
        </div>
        <div style="padding: 24px 28px;">
            @if($smartAds->count() > 0)
                <div style="display: grid; gap: 16px;">
                    @foreach($smartAds as $smartAd)
                        <div style="display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 18px; align-items: start; padding: 18px; border-radius: 16px; border: 1px solid #eef2ff; background: #fbfdff;">
                            <div>
                                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 8px;">
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 999px; background: rgba(29,78,216,0.08); color: #1d4ed8; font-size: .72rem; font-weight: 700; text-transform: uppercase;">{{ __('messages.smart_ad') }}</span>
                                    <span style="font-size: .8rem; color: #8f91ac;">#{{ $smartAd->id }}</span>
                                </div>
                                <h4 style="margin: 0 0 8px; color: #1f2937;">{{ $smartAd->displayTitle() }}</h4>
                                <p style="margin: 0 0 12px; color: #6b7280; line-height: 1.65;">{{ \Illuminate\Support\Str::limit($smartAd->displayDescription(), 170) }}</p>
                                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                                    <span style="padding: 4px 10px; border-radius: 999px; background: #eff6ff; color: #1d4ed8; font-size: .75rem;">{{ \App\Support\SmartAdTargeting::formatTargets($smartAd->targetCountries()) }}</span>
                                    <span style="padding: 4px 10px; border-radius: 999px; background: #ecfeff; color: #0f766e; font-size: .75rem;">{{ \App\Support\SmartAdTargeting::formatTargets($smartAd->targetDevices()) }}</span>
                                </div>
                            </div>
                            <div style="text-align: right; min-width: 180px;">
                                <div style="font-size: 1.4rem; font-weight: 800; color: #1d4ed8;">{{ $smartAd->impressions }}</div>
                                <div style="font-size: .8rem; color: #8f91ac;">{{ __('messages.smart_impressions_label') }}</div>
                                <div style="margin-top: 12px; font-size: 1rem; font-weight: 700; color: #0f766e;">{{ $smartAd->clicks }} {{ __('messages.smart_clicks_label') }}</div>
                                <div style="margin-top: 14px; display: flex; justify-content: flex-end; gap: 8px;">
                                    <a href="{{ route('ads.smart.edit', $smartAd->id) }}" class="button tertiary">{{ __('messages.edit') }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="padding: 28px; border: 1px dashed #c7d2fe; border-radius: 18px; background: linear-gradient(135deg, #f8fbff 0%, #eef6ff 100%);">
                    <h4 style="margin: 0 0 8px; color: #1f2937;">{{ __('messages.smart_create_first_title') }}</h4>
                    <p style="margin: 0 0 14px; color: #6b7280;">{{ __('messages.smart_create_first_desc') }}</p>
                    <a href="{{ route('ads.smart.create') }}" class="button secondary">{{ __('messages.smart_create_ad') }}</a>
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
