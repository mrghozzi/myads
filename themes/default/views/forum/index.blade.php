@extends('theme::layouts.master')
@include('theme::forum._assets')

@section('content')
<div class="forum-rdx forum-rdx-index">
<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;">
    <!-- SECTION BANNER ICON -->
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/discussion-icon.png') }}">
    <!-- /SECTION BANNER ICON -->

    <!-- SECTION BANNER TITLE -->
    <p class="section-banner-title">{{ __('messages.forum') }}</p>
    <!-- /SECTION BANNER TITLE -->

    <!-- SECTION BANNER TEXT -->
    <p class="section-banner-text"></p>
    <!-- /SECTION BANNER TEXT -->
</div>
<!-- /SECTION BANNER -->

<!-- ADS -->
@include('theme::partials.ads', ['id' => 4])

<div class="section-filters-bar v6">
    <!-- SECTION FILTERS BAR ACTIONS -->
    <div class="section-filters-bar-actions">
    </div>
    @auth
    <div class="section-filters-bar-actions">
        <!-- BUTTON -->
        <a href="{{ route('forum.create') }}" class="button secondary" style="color: #fff;">
            <i class="fa fa-plus nav_icon"></i>&nbsp;{{ __('messages.add') }}
        </a>
        <!-- /BUTTON -->
    </div>
    @endauth
    <!-- /SECTION FILTERS BAR ACTIONS -->
</div>

<div class="table table-forum table-forum-category">
    <!-- TABLE HEADER -->
    <div class="table-header">
        <div class="table-header-column">
            <p class="table-header-title">{{ __('messages.cat_s') }}</p>
        </div>
        <div class="table-header-column centered padded-medium">
            <p class="table-header-title">{{ __('messages.topics') }}</p>
        </div>
        <div class="table-header-column centered padded-medium">
            <p class="table-header-title">{{ __('messages.replies') ?? 'المساهمات' }}</p>
        </div>
        <div class="table-header-column padded-big-left">
            <p class="table-header-title">{{ __('messages.latest_post') }}</p>
        </div>
    </div>
    <!-- /TABLE HEADER -->

    <!-- TABLE BODY -->
    <div class="table-body">
        @foreach($categories as $category)
        @php
            $topicCount = \App\Models\ForumTopic::where('cat', $category->id)->where('statu', 1)->count();
            $commentsCount = \App\Models\ForumComment::whereHas('topic', function($q) use ($category) { $q->where('cat', $category->id); })->count();
            $latestTopic = \App\Models\ForumTopic::where('cat', $category->id)->where('statu', 1)->orderBy('id', 'desc')->first();
            
            // Get latest status date for the latest topic
            $latestDate = "";
            if ($latestTopic) {
                $status = \App\Models\Status::where('tp_id', $latestTopic->id)->where('s_type', 2)->first();
                if ($status) {
                    $latestDate = \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans();
                }
            }
        @endphp
        <!-- TABLE ROW -->
        <div class="table-row big">
            <div class="table-column">
                <div class="forum-category">
                    <a href="{{ route('forum.category', $category->id) }}">
                        <i class="fa {{ $category->icons }}" aria-hidden="true"></i>
                    </a>
                    <div class="forum-category-info">
                        <p class="forum-category-title">
                            <a href="{{ route('forum.category', $category->id) }}">{{ $category->name }}</a>
                        </p>
                        <p class="forum-category-text">{!! nl2br(strip_tags($category->txt, '<br>')) !!}</p>
                    </div>
                </div>
            </div>
            <div class="table-column centered padded-medium">
                <p class="table-title">{{ $topicCount }}</p>
            </div>
            <div class="table-column centered padded-medium">
                <p class="table-title">{{ $commentsCount }}</p>
            </div>
            <div class="table-column padded-big-left">
                @if($latestTopic)
                <a class="table-link" href="{{ route('forum.topic', $latestTopic->id) }}">{{ $latestTopic->name }}</a>
                <a class="table-link" href="{{ route('forum.topic', $latestTopic->id) }}">
                    <i class="fa fa-clock-o" aria-hidden="true"></i> {{ __('messages.since') }} {{ $latestDate }}
                </a>
                @else
                <p class="table-text">-</p>
                @endif
            </div>
        </div>
        <!-- /TABLE ROW -->
        @endforeach
    </div>
    <!-- /TABLE BODY -->
</div>

<!-- FORUM STATS SUPERDESIGN -->
@php
    $totalTopics = \App\Models\ForumTopic::count();
    $totalComments = \App\Models\ForumComment::count();
    $totalMembers = \App\Models\User::count();
    $latestMember = \App\Models\User::orderBy('id', 'desc')->first();
@endphp
<div class="section-header" style="margin-top: 32px;">
    <div class="section-header-info">
        <h2 class="section-title"><i class="fa fa-line-chart"></i> إحصائيات المنتدى</h2>
    </div>
</div>
<div class="grid grid-4-4-4-4" style="margin-top: 16px;">
    <div class="widget-box superdesign-wrap" style="text-align: center; padding: 24px; border-radius: 12px; background: linear-gradient(135deg, #615dfa, #23d2e2); color: white;">
        <i class="fa fa-folder-open fa-3x" style="margin-bottom: 12px; color: rgba(255,255,255,0.8);"></i>
        <p style="font-size: 28px; font-weight: bold; margin: 0;">{{ $totalTopics }}</p>
        <p style="margin: 0; font-size: 14px; font-weight: 500;">عدد المواضيع</p>
    </div>
    <div class="widget-box superdesign-wrap" style="text-align: center; padding: 24px; border-radius: 12px; background: linear-gradient(135deg, #fd4350, #ff8c42); color: white;">
        <i class="fa fa-comments fa-3x" style="margin-bottom: 12px; color: rgba(255,255,255,0.8);"></i>
        <p style="font-size: 28px; font-weight: bold; margin: 0;">{{ $totalComments }}</p>
        <p style="margin: 0; font-size: 14px; font-weight: 500;">عدد المساهمات</p>
    </div>
    <div class="widget-box superdesign-wrap" style="text-align: center; padding: 24px; border-radius: 12px; background: linear-gradient(135deg, #1bc8db, #00d2ff); color: white;">
        <i class="fa fa-users fa-3x" style="margin-bottom: 12px; color: rgba(255,255,255,0.8);"></i>
        <p style="font-size: 28px; font-weight: bold; margin: 0;">{{ $totalMembers }}</p>
        <p style="margin: 0; font-size: 14px; font-weight: 500;">الأعضاء المسجلين</p>
    </div>
    <div class="widget-box superdesign-wrap" style="text-align: center; padding: 24px; border-radius: 12px; background: linear-gradient(135deg, #44cc56, #28a745); color: white;">
        <i class="fa fa-user-plus fa-3x" style="margin-bottom: 12px; color: rgba(255,255,255,0.8);"></i>
        <p style="font-size: 28px; font-weight: bold; margin: 0;">
            @if($latestMember)
                <a href="{{ route('profile.show', $latestMember->username) }}" style="color: white; text-decoration: underline;">{{ $latestMember->username }}</a>
            @else
                -
            @endif
        </p>
        <p style="margin: 0; font-size: 14px; font-weight: 500;">أحدث عضو مسجل</p>
    </div>
</div>
<!-- /FORUM STATS SUPERDESIGN -->
</div>
@endsection
