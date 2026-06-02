@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%; background-size: cover;">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.yt_exchange') }}</p>
    <p class="section-banner-text">{{ __('messages.yt_watch_earn') }}</p>
</div>

<div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 28px; margin-bottom: 12px;">
    <a href="{{ route('ads.index') }}" class="button tertiary"><i class="fa fa-arrow-left"></i> {{ __('messages.back') }}</a>
    <a href="{{ route('youtube.advertiser.index') }}" class="button primary"><i class="fa-brands fa-youtube"></i> {{ __('messages.yt_my_campaigns') }}</a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<div class="widget-box" style="padding: 28px;">
    <h4 style="margin-top: 0; margin-bottom: 24px; font-weight: 700; color: #3e3f5e;"><i class="fa-brands fa-youtube" style="color: #ef4444;"></i> {{ __('messages.yt_available_videos') }}</h4>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px;">
        @forelse($videos as $video)
            <div style="border-radius: 16px; border: 1px solid #f1f1f5; overflow: hidden; background: #fff; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 24px rgba(0,0,0,0.08)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                <div style="position: relative;">
                    <img src="{{ $video->thumbnail_url }}" alt="Thumbnail" style="width: 100%; height: 160px; object-fit: cover;">
                    <div style="position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.8); color: #fff; padding: 2px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;">
                        {{ $video->duration_required }}s
                    </div>
                </div>
                <div style="padding: 16px;">
                    <h6 style="margin: 0 0 16px; font-weight: 600; color: #3e3f5e; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $video->title ?? 'YouTube Video' }}</h6>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="display: inline-block; padding: 4px 10px; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 20px; font-size: 0.8rem; font-weight: 700;">
                            +{{ $video->reward_points }} PTS
                        </span>
                        <a href="{{ route('youtube.exchange.watch', $video->id) }}" class="button primary" style="padding: 6px 14px; font-size: 0.85rem; height: auto; line-height: normal;">
                            <i class="fa fa-play"></i> {{ __('messages.yt_watch') }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                <i class="fa-brands fa-youtube" style="font-size: 3rem; color: #dedeea; margin-bottom: 12px;"></i>
                <p style="color: #8f91ac; font-weight: 600;">{{ __('messages.yt_no_videos') }}</p>
            </div>
        @endforelse
    </div>

    @if($videos->hasPages())
        <div style="margin-top: 24px;">
            {{ $videos->links() }}
        </div>
    @endif
</div>
@endsection
