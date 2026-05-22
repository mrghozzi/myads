@extends('theme::layouts.master')

@section('content')
<div class="grid">
    <div class="section-banner">
        <p class="section-banner-title">{{ __('messages.reels') ?? 'Reels' }}</p>
        <p class="section-banner-text text-center">{{ __('messages.reels_desc') ?? 'Watch short videos from the community.' }}</p>
    </div>

    <div class="widget-box" style="margin-top: 30px; text-align: center; padding: 50px;">
        <svg class="widget-box-icon icon-videos" style="width: 64px; height: 64px; fill: #615dfa; margin-bottom: 20px;">
            <use xlink:href="#svg-videos"></use>
        </svg>
        <h2 style="font-size: 24px; font-weight: bold; margin-bottom: 10px;">{{ __('messages.coming_soon') ?? 'Coming Soon to Web' }}</h2>
        <p style="color: #777d74;">{{ __('messages.reels_mobile_desc') ?? 'Reels are currently available in our mobile app. A full web experience is coming soon.' }}</p>
    </div>
</div>
@endsection
