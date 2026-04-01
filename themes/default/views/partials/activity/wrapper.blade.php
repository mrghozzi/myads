@php
    $activityUser = $activity->user;
    $activityUserProfileUrl = $activityUser ? route('profile.show', $activityUser->username) : '#';
    $activityUserName = $activityUser?->username ?? __('messages.unknown_user');
    $activityUserAvatar = $activityUser ? $activityUser->avatarUrl() : asset('upload/_avatar.png');
    $activityUserPresence = $activityUser?->isOnline() ? 'online' : 'offline';
    $activityUserIsAdmin = $activityUser?->isAdmin() ?? false;
@endphp

<div class="widget-box no-padding activity-post-card post{{ $activity->id }}">
    <!-- WIDGET BOX SETTINGS -->
    <div class="widget-box-settings">
        <!-- POST SETTINGS WRAP -->
        <div class="post-settings-wrap" style="position: relative;">
            <!-- POST SETTINGS -->
            <div class="post-settings widget-box-post-settings-dropdown-trigger">
                <!-- POST SETTINGS ICON -->
                <svg class="post-settings-icon icon-more-dots">
                    <use xlink:href="#svg-more-dots"></use>
                </svg>
                <!-- /POST SETTINGS ICON -->
            </div>
            <!-- /POST SETTINGS -->

            <!-- SIMPLE DROPDOWN -->
            <div class="simple-dropdown widget-box-post-settings-dropdown" style="position: absolute; z-index: 9999; top: 30px; right: 9px; opacity: 0; visibility: hidden; transform: translate(0px, -20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                @auth
                    @if(auth()->id() == $activity->uid || auth()->user()->isAdmin())
                        <p class="simple-dropdown-link post_delete{{ $activity->id }}" onclick="deletePost({{ $activity->tp_id }}, {{ $activity->s_type }}, '.post{{ $activity->id }}')" style="cursor: pointer;">
                            <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}
                        </p>
                    @endif
                    <p class="simple-dropdown-link"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}</p>
                @endauth
            </div>
            <!-- /SIMPLE DROPDOWN -->
        </div>
        <!-- /POST SETTINGS WRAP -->
    </div>
    <!-- /WIDGET BOX SETTINGS -->

    <!-- WIDGET BOX STATUS -->
    <div class="widget-box-status">
        <!-- WIDGET BOX STATUS CONTENT -->
        <div class="widget-box-status-content">
            <!-- USER STATUS -->
            <div class="user-status">
                <!-- USER STATUS AVATAR -->
                <a class="user-status-avatar" href="{{ $activityUserProfileUrl }}">
                    <!-- USER AVATAR -->
                    <div class="user-avatar small no-outline {{ $activityUserPresence }}">
                        <!-- USER AVATAR CONTENT -->
                        <div class="user-avatar-content">
                            <!-- HEXAGON -->
                            <div class="hexagon-image-30-32" data-src="{{ $activityUserAvatar }}"></div>
                            <!-- /HEXAGON -->
                        </div>
                        <!-- /USER AVATAR CONTENT -->
                        
                        <!-- USER AVATAR PROGRESS BORDER -->
                        <div class="user-avatar-progress-border">
                            <!-- HEXAGON -->
                            <div class="hexagon-border-40-44"></div>
                            <!-- /HEXAGON -->
                        </div>
                        <!-- /USER AVATAR PROGRESS BORDER -->
                        
                        @if($activityUserIsAdmin)
                            <!-- USER AVATAR BADGE -->
                            <div class="user-avatar-badge">
                                <div class="user-avatar-badge-border">
                                    <div class="hexagon-22-24"></div>
                                </div>
                                <div class="user-avatar-badge-content">
                                    <div class="hexagon-dark-16-18"></div>
                                </div>
                                <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                            </div>
                            <!-- /USER AVATAR BADGE -->
                        @endif
                    </div>
                    <!-- /USER AVATAR -->
                </a>
                <!-- /USER STATUS AVATAR -->

                <!-- USER STATUS TITLE -->
                <p class="user-status-title medium">
                    <a class="bold" href="{{ $activityUserProfileUrl }}">{{ $activityUserName }}</a>
                    
                    @if($activity->s_type == 1 && $activity->related_content)
                        <span class="user-status-title-text">{{ __('messages.added_website') }}</span>
                    @elseif(($activity->s_type == 2 || $activity->s_type == 4) && $activity->related_content)
                        <span class="user-status-title-text">{{ __('messages.posted_topic_in') }}</span>
                        @if($activity->related_content->category)
                            <a class="bold" href="{{ route('forum.category', $activity->related_content->category->id) }}">{{ $activity->related_content->category->name }}</a>
                        @endif
                    @elseif($activity->s_type == 7867 && $activity->related_content)
                        <span class="user-status-title-text">{{ __('messages.added_product') }}</span>
                    @endif
                </p>
                <!-- /USER STATUS TITLE -->

                <!-- USER STATUS TIMESTAMP -->
                <p class="user-status-timestamp small-space">{{ \Carbon\Carbon::createFromTimestamp($activity->date)->diffForHumans() }}</p>
                <!-- /USER STATUS TIMESTAMP -->
            </div>
            <!-- /USER STATUS -->

            <!-- WIDGET BOX STATUS TEXT -->
            <p class="widget-box-status-text">
                @if($activity->s_type == 1 && $activity->related_content)
                    {!! nl2br(e(Str::limit($activity->related_content->txt, 500))) !!}
                    <br>
                    <a href="{{ $activity->related_content->url }}" target="_blank" class="button small primary mt-2">{{ __('messages.visit_website') }}</a>
                @elseif(($activity->s_type == 2 || $activity->s_type == 4) && $activity->related_content)
                    <a href="{{ route('forum.topic', $activity->tp_id) }}" class="h4 d-block mb-2">{{ $activity->related_content->name }}</a>
                    {!! nl2br(e(Str::limit(strip_tags($activity->related_content->txt), 500))) !!}
                @elseif($activity->s_type == 7867 && $activity->related_content)
                    <a href="{{ route('store.show', $activity->related_content->name) }}" class="h4 d-block mb-2">{{ $activity->related_content->name }}</a>
                    {!! nl2br(e(Str::limit(strip_tags($activity->related_content->o_valuer), 500))) !!}
                    <br>
                    <a href="{{ route('store.show', $activity->related_content->name) }}" class="button small secondary mt-2">{{ __('messages.view_product') }}</a>
                @endif
            </p>
            <!-- /WIDGET BOX STATUS TEXT -->
        </div>
        <!-- /WIDGET BOX STATUS CONTENT -->

        <!-- CONTENT ACTIONS -->
        <div class="content-actions">
            <div class="content-action">
                <div class="meta-line">
                    <a class="meta-line-link" href="#">
                        <!-- REACTION OPTION IMAGE -->
                        <img class="reaction-option-image" src="{{ theme_asset('img/reaction/like.png') }}" alt="reaction-like">
                        <!-- /REACTION OPTION IMAGE -->
                    </a>
                    <p class="meta-line-text">0</p>
                </div>
            </div>

            <div class="content-action">
                <div class="meta-line">
                    <a class="meta-line-link" href="#">
                        <!-- META LINE ICON -->
                        <svg class="meta-line-icon icon-comment">
                            <use xlink:href="#svg-comment"></use>
                        </svg>
                        <!-- /META LINE ICON -->
                    </a>
                    <p class="meta-line-text">0</p>
                </div>
            </div>

            <div class="content-action">
                <div class="meta-line">
                    <a class="meta-line-link" href="#">
                        <!-- META LINE ICON -->
                        <svg class="meta-line-icon icon-share">
                            <use xlink:href="#svg-share"></use>
                        </svg>
                        <!-- /META LINE ICON -->
                    </a>
                    <p class="meta-line-text">0</p>
                </div>
            </div>
        </div>
        <!-- /CONTENT ACTIONS -->
    </div>
    <!-- /WIDGET BOX STATUS -->
</div>
