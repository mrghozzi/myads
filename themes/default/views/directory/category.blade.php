@extends('theme::layouts.master')

@section('content')
<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ $category->name }}</p>
    <p class="section-banner-text">{{ $category->txt }}</p>
</div>

<div class="section-filters-bar v7">
    <div class="section-filters-bar-actions">
        <div class="section-filters-bar-info">
            <p class="section-filters-bar-title">
                <a href="{{ url('/directory') }}">{{ __('messages.directory') }}</a>
                <span class="separator"></span>
                <a href="{{ url('/cat/' . $category->id) }}">{{ $category->name }}</a>
            </p>
        </div>
    </div>
</div>

<div class="grid grid-3-6-3">
    <!-- LEFT SIDEBAR -->
    <div class="grid-column">
        <div class="widget-box">
            <p class="widget-box-title"><h4>{{ __('messages.board') }}</h4></p>
            <div class="widget-box-content">
                <div class="post-peek-list">
                    <a href="{{ url('/directory') }}" class="btn btn-primary" >&nbsp;<i class="fa fa-home" aria-hidden="true"></i>&nbsp;</a>
                    <a href="{{ url('/add-site.html') }}" class="btn btn-success" >{{ __('messages.addWebsite') }}&nbsp;<i class="fa fa-plus" aria-hidden="true"></i> </a>
                </div>
            </div>
        </div>

        @if($subCategories->count() > 0)
        <div class="widget-box">
            <p class="widget-box-title"><h4>{{ __('messages.subcategories') }}</h4></p>
            <div class="widget-box-content">
                <div class="post-peek-list">
                    @foreach($subCategories as $sub)
                        @php
                            $subCount = \App\Models\Directory::where('cat', $sub->id)->where('statu', 1)->count();
                        @endphp
                        <div class="post-peek">
                            <p class="post-peek-title">
                                <a href="{{ url('/cat/' . $sub->id) }}">{{ $sub->name }}
                                <span class="badge badge-info">{{ $subCount }}</span></a>
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @include('theme::partials.widgets', ['place' => 5])
    </div>

    <!-- MAIN CONTENT -->
    <div class="grid-column">
        <div id="infinite-scroll-container" style="display: grid; grid-gap: 16px;">
            <div id="timeline-content" style="display: contents;">
                @forelse($activities as $activity)
                    @include('theme::partials.activity.render', ['activity' => $activity])
                @empty
                    <div class="widget-box" style="margin-bottom: 0;">
                        <div class="widget-box-content">
                            <p class="no-results">{{ __('messages.no_listings_found') }}</p>
                        </div>
                    </div>
                @endforelse
                
                @include('theme::partials.ajax.infinite_scroll', ['paginator' => $activities])
            </div>
        </div>
    </div>

    <!-- RIGHT SIDEBAR -->
    <div class="grid-column">
        @include('theme::partials.widgets', ['place' => 6])
    </div>
</div>
@endsection
