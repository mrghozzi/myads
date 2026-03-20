@extends('theme::layouts.master')
@include('theme::forum._assets')

@section('content')
<div class="forum-rdx forum-rdx-category">
<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/discussion-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ $category->name }}</p>
</div>
<!-- /SECTION BANNER -->

<div class="grid grid-3-6-3 mobile-prefer-content" >
    <div class="grid-column" >
        <div class="forum-sidebar-stack">
        <div class="widget-box forum-sidebar-card forum-category-command-card">
            <!-- WIDGET BOX TITLE -->
            <p class="widget-box-title">{{ __('messages.board') }}</p>
            <!-- /WIDGET BOX TITLE -->

            <!-- WIDGET BOX CONTENT -->
            <div class="widget-box-content">
                <div class="forum-category-command-list">
                    <a href="{{ route('forum.index') }}" class="button small secondary forum-category-command">
                        <i class="fa fa-home" aria-hidden="true"></i>
                        <span>{{ __('messages.forum') }}</span>
                    </a>
                    @auth
                    <a href="{{ route('forum.create') }}" class="button small primary forum-category-command">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        <span>{{ __('messages.w_new_tpc') }}</span>
                    </a>
                    @endauth
                </div>
            </div>
            <!-- /WIDGET BOX CONTENT -->
        </div>
        @include('theme::forum.partials.category_sidebar', ['sidebarCategories' => $sidebarCategories])
        <x-widget-column side="forum_left" />
        </div>
    </div>
    
    <div class="grid-column" >
        <div class="forum-rdx-discussion-shell">
            <div class="forum-rdx-discussion-head">
                <p>{{ __('messages.topic') }}</p>
                <p class="text-center">{{ __('messages.stats') }}</p>
                <p class="text-end">{{ __('messages.options') }}</p>
            </div>
            <div id="infinite-scroll-container" class="forum-rdx-discussion-list">
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
    </div>
    
    <div class="grid-column" >
        <x-widget-column side="forum_right" />
    </div>
</div>
</div>
@endsection
