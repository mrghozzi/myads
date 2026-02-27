@extends('theme::layouts.master')

@section('content')
<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.directory') }}</p>
    <p class="section-banner-text">{{ __('messages.latest_sites') }}</p>
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

        @if($categories->count() > 0)
        <div class="widget-box">
            <p class="widget-box-title"><h4>{{ __('messages.cat_s') }}</h4></p>
            <div class="widget-box-content">
                <div class="post-peek-list">
                    @foreach($categories as $cat)
                        @php
                            $catCount = \App\Models\Directory::where('cat', $cat->id)->where('statu', 1)->count();
                            // Count subcategories listings
                            $subCats = \App\Models\DirectoryCategory::where('sub', $cat->id)->where('statu', 1)->get();
                            foreach($subCats as $sub) {
                                $catCount += \App\Models\Directory::where('cat', $sub->id)->where('statu', 1)->count();
                            }
                        @endphp
                        <div class="post-peek">
                            <a class="post-peek-image" href="{{ url('/cat/' . $cat->id) }}">
                                <svg xmlns="http://www.w3.org/2000/svg" height="16" width="18" viewBox="0 0 576 512"><path fill="#615dfa" d="M88.7 223.8L0 375.8V96C0 60.7 28.7 32 64 32H181.5c17 0 33.3 6.7 45.3 18.7l26.5 26.5c12 12 28.3 18.7 45.3 18.7H416c35.3 0 64 28.7 64 64v32H144c-22.8 0-43.8 12.1-55.3 31.8zm27.6 16.1C122.1 230 132.6 224 144 224H544c11.5 0 22 6.1 27.7 16.1s5.7 22.2-.1 32.1l-112 192C453.9 474 443.4 480 432 480H32c-11.5 0-22-6.1-27.7-16.1s-5.7-22.2 .1-32.1l112-192z"/></svg>
                            </a>
                            <p class="post-peek-title">
                                <a href="{{ url('/cat/' . $cat->id) }}">{{ $cat->name }}
                                <span class="badge badge-info">{{ $catCount }}</span></a>
                            </p>
                            @if($subCats->count() > 0)
                                <p class="post-peek-text">
                                    @foreach($subCats as $sub)
                                        @php
                                            $subCount = \App\Models\Directory::where('cat', $sub->id)->where('statu', 1)->count();
                                        @endphp
                                        <span class="d-block">
                                            <i class="fa-solid fa-circle-chevron-right" style="color: #615dfa;"></i>&nbsp;&nbsp;
                                            <a href="{{ url('/cat/' . $sub->id) }}">{{ $sub->name }}
                                            <span class="badge badge-info">{{ $subCount }}</span></a>
                                        </span>
                                    @endforeach
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <x-widget-column side="directory_left" />
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
        <x-widget-column side="directory_right" />
    </div>
</div>
@endsection
