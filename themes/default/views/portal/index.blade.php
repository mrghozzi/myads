@extends('theme::layouts.master')

@section('content')
<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.community') }}</p>
    <p class="section-banner-text">{{ __('messages.latest_updates') }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <!-- LEFT SIDEBAR -->
    <div class="grid-column">
        <x-widget-column side="portal_left" />
    </div>

    <!-- MAIN FEED -->
    <div class="grid-column">
        @if(!empty($search))
            <h2 class="section-title" style="margin-bottom: 24px;">{{ __('messages.search') }}: "{{ $search }}"</h2>

            <!-- Users Search Results -->
            @if(isset($searchedUsers) && $searchedUsers->count() > 0)
                <h3 style="margin-bottom: 16px;">{{ __('messages.members') }}</h3>
                <div class="grid grid-3-3-3 centered" style="margin-bottom: 32px;">
                    @foreach($searchedUsers as $sUser)
                        <div class="user-preview small">
                            <figure class="user-preview-cover liquid" style="background: url({{ asset('themes/default/assets/img/cover/01.jpg') }}) center center / cover no-repeat;">
                                <img src="{{ asset('themes/default/assets/img/cover/01.jpg') }}" alt="cover-01" style="display: none;">
                            </figure>
                            <div class="user-preview-info">
                                <div class="user-short-description small">
                                    <a class="user-short-description-avatar user-avatar {{ $sUser->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.show', $sUser->username) }}">
                                        <div class="user-avatar-border">
                                            <div class="hexagon-100-110" style="width: 100px; height: 110px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="100" height="110"></canvas></div>
                                        </div>
                                        <div class="user-avatar-content">
                                            <div class="hexagon-image-68-74" data-src="{{ $sUser->avatarUrl() }}" style="width: 68px; height: 74px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="68" height="74"></canvas></div>
                                        </div>
                                        <div class="user-avatar-progress-border">
                                            <div class="hexagon-border-84-92" style="width: 84px; height: 92px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="84" height="92"></canvas></div>
                                        </div>
                                    </a>
                                    <p class="user-short-description-title"><a href="{{ route('profile.show', $sUser->username) }}">{{ $sUser->username }}</a></p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Posts Search Results -->
            @if(isset($searchedStatuses) && $searchedStatuses->count() > 0)
                <h3 style="margin-bottom: 16px;">{{ __('messages.posts') }}</h3>
                <div style="display: grid; grid-gap: 16px; margin-bottom: 32px;">
                    @foreach($searchedStatuses as $activity)
                        @include('theme::partials.activity.render', ['activity' => $activity])
                    @endforeach
                </div>
            @endif

            <!-- Comments Search Results -->
            @if((isset($searchedCommentsForum) && $searchedCommentsForum->count() > 0) || (isset($searchedCommentsDir) && $searchedCommentsDir->count() > 0))
                <h3 style="margin-bottom: 16px;">{{ __('messages.comments') }}</h3>
                <div class="widget-box" style="margin-bottom: 32px;">
                    <div class="widget-box-content padding-none">
                        <div class="user-status-list">
                            <!-- Forum Comments -->
                            @if(isset($searchedCommentsForum))
                                @foreach($searchedCommentsForum as $fComment)
                                    <div class="user-status">
                                        <div class="user-status-avatar">
                                            <div class="user-avatar small no-outline">
                                                <div class="user-avatar-content">
                                                    <div class="hexagon-image-30-32" data-src="{{ $fComment->user ? $fComment->user->avatarUrl() : asset('upload/_avatar.png') }}"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="user-status-title">{{ $fComment->user->username ?? 'Unknown' }} <span style="font-weight: 400; font-size: 12px; color: #8f919d;">{{ __('messages.on_forum_topic') ?? 'on Forum Topic' }} #{{ $fComment->tid }}</span></p>
                                        <p class="user-status-text">{{ \Illuminate\Support\Str::limit(strip_tags($fComment->txt), 100) }}</p>
                                        <p class="user-status-timestamp">{{ \Carbon\Carbon::createFromTimestamp($fComment->date)->diffForHumans() }}</p>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Directory Comments -->
                            @if(isset($searchedCommentsDir))
                                @foreach($searchedCommentsDir as $dComment)
                                    <div class="user-status">
                                        <p class="user-status-title">{{ __('messages.directory_comment') ?? 'Directory Comment' }} <span style="font-weight: 400; font-size: 12px; color: #8f919d;">{{ __('messages.on_directory') ?? 'on Directory' }} #{{ $dComment->o_parent }}</span></p>
                                        <p class="user-status-text">{{ \Illuminate\Support\Str::limit(strip_tags($dComment->o_valuer), 100) }}</p>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            @if(
                (!isset($searchedUsers) || $searchedUsers->count() == 0) &&
                (!isset($searchedStatuses) || $searchedStatuses->count() == 0) &&
                (!isset($searchedCommentsForum) || $searchedCommentsForum->count() == 0) &&
                (!isset($searchedCommentsDir) || $searchedCommentsDir->count() == 0)
            )
                <div class="widget-box">
                    <div class="widget-box-content">
                        <p class="text-center">{{ __('messages.no_results_found') ?? 'No results found.' }}</p>
                    </div>
                </div>
            @endif

        @else
            @include('theme::partials.status.add_post')
            
            <!-- TABS -->
            @auth
            <div class="simple-tab-items">
                <a href="{{ route('portal.index', ['filter' => 'all']) }}" class="simple-tab-item {{ $filter == 'all' ? 'active' : '' }}">{{ __('messages.all_updates') }}</a>
                <a href="{{ route('portal.index', ['filter' => 'me']) }}" class="simple-tab-item {{ $filter == 'me' ? 'active' : '' }}">{{ __('messages.following') }}</a>
            </div>
            @endauth

            <!-- ACTIVITY LIST -->
            <div id="infinite-scroll-container" style="display: grid; grid-gap: 16px;">
                @foreach($activities as $activity)
                    @include('theme::partials.activity.render', ['activity' => $activity])
                @endforeach
                
                @include('theme::partials.ajax.infinite_scroll', ['paginator' => $activities->appends(['filter' => $filter])])
            </div>
        @endif
    </div>

    <!-- RIGHT SIDEBAR -->
    <div class="grid-column">
        <x-widget-column side="portal_right" />
    </div>
</div>
@endsection
