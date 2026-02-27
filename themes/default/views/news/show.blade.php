@extends('theme::layouts.master')

@section('content')
<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ $article->name }}</p>
    <p class="section-banner-text">{{ $article->date ? date('Y-m-d', $article->date) : '' }}</p>
</div>

<div class="grid grid-3-9">
    <!-- LEFT SIDEBAR -->
    <div class="grid-column">
        <div class="widget-box">
            <p class="widget-box-title"><h4>{{ __('messages.menu') }}</h4></p>
            <div class="widget-box-content">
                <div class="post-peek-list">
                    <a href="{{ route('news.index') }}" class="btn btn-primary" >&nbsp;<i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; {{ __('messages.back_to_news') }}</a>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-settings">
                <div class="post-peek-body">
                    {!! $article->text !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
