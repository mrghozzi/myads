@extends('theme::layouts.master')

@section('content')
<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/discussion-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">
        <h3 style="color: #fff; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" >
            {{ $category->name }}
        </h3>
    </p>
</div>
<!-- /SECTION BANNER -->

<div class="grid grid-3-6-3 mobile-prefer-content" >
    <div class="grid-column" >
        <div class="widget-box">
            <!-- WIDGET BOX TITLE -->
            <p class="widget-box-title"><h4>{{ __('messages.board') }}</h4></p>
            <!-- /WIDGET BOX TITLE -->

            <!-- WIDGET BOX CONTENT -->
            <div class="widget-box-content">
                <!-- POST PEEK LIST -->
                <div class="post-peek-list">
                    <a href="{{ route('forum.index') }}" class="btn btn-primary" >&nbsp;<i class="fa fa-home" aria-hidden="true"></i>&nbsp;</a>
                    @auth
                    <a href="{{ route('forum.create') }}" class="btn btn-success" >{{ __('messages.w_new_tpc') }}&nbsp;<i class="fa fa-plus" aria-hidden="true"></i> </a>
                    @endauth
                </div>
                <!-- /POST PEEK LIST -->
            </div>
            <!-- /WIDGET BOX CONTENT -->
        </div>
        <div class="widget-box">
            <!-- WIDGET BOX TITLE -->
            <p class="widget-box-title"><h4>{{ __('messages.cat_s') }}</h4></p>
            <!-- /WIDGET BOX TITLE -->

            <!-- WIDGET BOX CONTENT -->
            <div class="widget-box-content">
                <!-- POST PEEK LIST -->
                <div class="post-peek-list">
                    @php
                        $allCategories = \App\Models\ForumCategory::orderBy('id', 'desc')->get();
                    @endphp
                    @foreach($allCategories as $cat)
                        <!-- CATEGORY ITEM -->
                        <div class="post-peek card">
                            <!-- CATEGORY LINK -->
                            <h4>
                                <a href="{{ route('forum.category', $cat->id) }}">
                                    <i class="fa {{ $cat->icons }}" aria-hidden="true"></i>
                                    &nbsp;{{ $cat->name }}
                                </a>
                            </h4>
                            <!-- /CATEGORY LINK -->
                        </div>
                        <!-- /CATEGORY ITEM -->
                    @endforeach
                </div>
                <!-- /POST PEEK LIST -->
            </div>
            <!-- /WIDGET BOX CONTENT -->
        </div>
        <x-widget-column side="forum_left" />
    </div>
    
    <div class="grid-column" >
        <div id="infinite-scroll-container" style="display: grid; grid-gap: 16px;">
            <div id="timeline-content" style="display: contents;">
                @forelse($statuses as $status)
                    @php
                        $topic = $topics->get($status->tp_id);
                        if(!$topic) continue;
                    @endphp
                    @include('theme::partials.forum.topic_card', ['topic' => $topic, 'status' => $status])
                @empty
                    <div class="widget-box" style="margin-bottom: 0;">
                        <div class="widget-box-content">
                            <p class="text-center">{{ __('messages.no_topics_found') }}</p>
                        </div>
                    </div>
                @endforelse
                
                @include('theme::partials.ajax.infinite_scroll', ['paginator' => $statuses])
            </div>
        </div>
    </div>
    
    <div class="grid-column" >
        <x-widget-column side="forum_right" />
    </div>
</div>
@endsection
