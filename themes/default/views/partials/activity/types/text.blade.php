@php
    $activityUser = $activity->user;
    $activityUserProfileUrl = $activityUser ? route('profile.show', $activityUser->username) : '#';
    $activityUserName = $activityUser?->username ?? __('messages.unknown_user');
    $activityUserAvatar = $activityUser?->img ? asset($activityUser->img) : theme_asset('img/avatar/default.png');
    $activityUserPresence = $activityUser?->isOnline() ? 'online' : 'offline';
    $activityUserIsAdmin = $activityUser?->isAdmin() ?? false;
    $formattedText = \App\Support\ContentFormatter::format($activity->related_content->txt ?? '');
    $repostExcerpt = \Illuminate\Support\Str::limit(
        strip_tags(
            $activity->related_content->txt
            ?? $activity->related_content->o_valuer
            ?? $activity->related_content->text
            ?? $activity->related_content->name
            ?? ''
                ),
        80
    );
    $repostAuthorName = addslashes($activityUserName);
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
                        <!-- SIMPLE DROPDOWN LINK -->
                        <p class="simple-dropdown-link post_edit{{ $activity->id }}" onclick="postEdit({{ $activity->tp_id }}, 100)"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{{ __('messages.edit') }}</p>
                        <!-- /SIMPLE DROPDOWN LINK -->
                        <!-- SIMPLE DROPDOWN LINK -->
                        <p class="simple-dropdown-link post_delete{{ $activity->id }}" onclick="deletePost({{ $activity->tp_id }}, 100, '.post{{ $activity->id }}')"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}</p>
                        <!-- /SIMPLE DROPDOWN LINK -->
                    @endif
                    @include('theme::partials.activity.promotion_link', ['activity' => $activity])
                    <!-- SIMPLE DROPDOWN LINK -->
                    <p class="simple-dropdown-link post_report{{ $activity->id }}" onclick="reportPost({{ $activity->tp_id }}, 100, {{ $activity->related_content->id }})"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}</p>
                    <!-- /SIMPLE DROPDOWN LINK -->
                    <!-- SIMPLE DROPDOWN LINK -->
                    <p class="simple-dropdown-link author_report{{ $activity->id }}" onclick="reportUser({{ $activity->uid }}, {{ $activity->related_content->id }})"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_author') }}</p>
                    <!-- /SIMPLE DROPDOWN LINK -->
                @endauth
                <!-- SIMPLE DROPDOWN LINK -->
                <p class="simple-dropdown-link copy_link" onclick="navigator.clipboard.writeText('{{ route('forum.topic', $activity->tp_id) }}'); var notif = document.getElementById('notif{{ $activity->related_content->id }}'); notif.innerHTML = '<div class=\'alert alert-success\' role=\'alert\'>{{ __('messages.link_copied') }}</div>'; notif.style.display = 'block'; setTimeout(function() { notif.style.display = 'none'; }, 5000);"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}</p>
                <!-- /SIMPLE DROPDOWN LINK -->
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
                            <div class="hexagon-image-30-32" data-src="{{ $activityUserAvatar }}" style="width: 30px; height: 32px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                            </div>
                            <!-- /HEXAGON -->
                        </div>
                        <!-- /USER AVATAR CONTENT -->

                        <!-- USER AVATAR PROGRESS BORDER -->
                        <div class="user-avatar-progress-border">
                            <!-- HEXAGON -->
                            <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;"></div>
                            <!-- /HEXAGON -->
                        </div>
                        <!-- /USER AVATAR PROGRESS BORDER -->

                        @if($activityUserIsAdmin)
                            <!-- USER AVATAR BADGE -->
                            <div class="user-avatar-badge">
                                <!-- USER AVATAR BADGE BORDER -->
                                <div class="user-avatar-badge-border">
                                    <!-- HEXAGON -->
                                    <div class="hexagon-22-24" style="width: 22px; height: 24px; position: relative;"></div>
                                    <!-- /HEXAGON -->
                                </div>
                                <!-- /USER AVATAR BADGE BORDER -->
                                <!-- USER AVATAR BADGE CONTENT -->
                                <div class="user-avatar-badge-content">
                                    <!-- HEXAGON -->
                                    <div class="hexagon-dark-16-18" style="width: 16px; height: 18px; position: relative;"></div>
                                    <!-- /HEXAGON -->
                                </div>
                                <!-- /USER AVATAR BADGE CONTENT -->
                                <!-- USER AVATAR BADGE TEXT -->
                                <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                                <!-- /USER AVATAR BADGE TEXT -->
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
                </p>
                <!-- /USER STATUS TITLE -->

                <!-- USER STATUS TEXT -->
                <p class="user-status-text small">
                    <i class="fa fa-clock-o"></i>&nbsp;{{ __('messages.ago') }}&nbsp; {{ $activity->date_formatted }}
                    {{-- Assuming Suggestion logic needs to be migrated later --}}
                </p>
                <!-- /USER STATUS TEXT -->
            </div>
            <!-- /USER STATUS -->

            @include('theme::partials.activity.promotion_badge', ['activity' => $activity])

            <div class="tag-sticker">
                <!-- TAG STICKER ICON -->
                <svg class="tag-sticker-icon icon-blog-posts">
                    <use xlink:href="#svg-blog-posts"></use>
                </svg>
                <!-- /TAG STICKER ICON -->
            </div>

            <!-- WIDGET BOX STATUS TEXT -->
            <div class="widget-box-status-text post_text{{ $activity->related_content->id }}">
                <div class="textpost" id="post_form{{ $activity->related_content->id }}">
                    @if(trim(strip_tags($formattedText)) !== '')
                        {!! $formattedText !!}
                    @endif
                    <div id="report{{ $activity->related_content->id }}"></div>
                </div>
            </div>
            <!-- /WIDGET BOX STATUS TEXT -->

            @if($activity->linkPreviewRecord)
                @include('theme::partials.activity.link_preview', ['activity' => $activity])
            @endif

            @if($activity->repostRecord)
                @include('theme::partials.activity.repost_embed', ['activity' => $activity])
            @endif

            <div id="notif{{ $activity->related_content->id }}"></div>

            <!-- CONTENT ACTIONS -->
            <div class="content-actions">
                <!-- CONTENT ACTION -->
                <div class="content-action">
                    @include('theme::partials.activity.reaction-list', ['activity' => $activity])
                    <!-- META LINE -->
                    <div class="meta-line">
                        <!-- META LINE TEXT -->
                        <p class="meta-line-text">{{ $activity->reactions_count }} {{ __('messages.reactions') }}</p>
                        <!-- /META LINE TEXT -->
                    </div>
                    <!-- /META LINE -->
                </div>
                <!-- /CONTENT ACTION -->

                <!-- CONTENT ACTION -->
                <div class="content-action">
                    <!-- META LINE -->
                    <div class="meta-line">
                        <!-- META LINE LINK -->
                        <p class="meta-line-link">
                            <a href="{{ route('forum.topic', $activity->tp_id) }}">{{ $activity->comments_count }} {{ __('messages.comments') }}</a>
                        </p>
                        <!-- /META LINE LINK -->
                    </div>
                    <!-- /META LINE -->
                </div>
                <!-- /CONTENT ACTION -->

                <!-- CONTENT ACTION -->
                <div class="content-action">
                    <div class="meta-line">
                        <p class="meta-line-link">
                            <a href="{{ route('forum.topic', $activity->tp_id) }}">{{ $activity->reposts_count }} {{ __('messages.reposts') }}</a>
                        </p>
                    </div>
                </div>
                <!-- /CONTENT ACTION -->
            </div>
            <!-- /CONTENT ACTIONS -->
        </div>
        <!-- /WIDGET BOX STATUS CONTENT -->
    </div>
    <!-- /WIDGET BOX STATUS -->

    <!-- POST OPTIONS -->
    <div class="post-options">
        @auth
            <!-- REACTION OPTION -->
            <div class="post-option-wrap" style="position: relative;" data-activity-menu-wrap>
                <div class="post-option"
                     data-activity-menu-trigger
                     data-activity-menu-type="reaction">
                    <div id="reaction-btn-{{ $activity->related_content->id }}">
                    @php
                        $myReaction = \App\Models\Like::where('uid', auth()->id())
                            ->where('sid', $activity->related_content->id)
                            ->where('type', 2) // Forum type
                            ->first();
                        $myReactionOption = null;
                        if($myReaction){
                             $myReactionOption = \App\Models\Option::where('o_parent', $myReaction->id)->where('o_type', 'data_reaction')->first();
                        }
                    @endphp
                    @if($myReactionOption)
                        <img class="reaction-option-image" src="{{ theme_asset('img/reaction/'.$myReactionOption->o_valuer.'.png') }}" width="30" alt="reaction-{{ $myReactionOption->o_valuer }}">
                        <!-- <p class="post-option-text">{{ ucfirst($myReactionOption->o_valuer) }}</p> -->
                    @else
                        <svg class="post-option-icon icon-thumbs-up">
                            <use xlink:href="#svg-thumbs-up"></use>
                        </svg>
                        <p class="post-option-text">{{ __('messages.react') }}</p>
                    @endif
                    </div>
                </div>

                <!-- REACTION OPTIONS DROPDOWN -->
                <div class="reaction-options reaction-options-dropdown"
                     data-activity-menu-panel
                     style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                    @foreach(['like', 'love', 'dislike', 'happy', 'funny', 'wow', 'angry', 'sad'] as $reaction)
                        <div class="reaction-option text-tooltip-tft" data-title="{{ $reaction }}" onclick="toggleReaction({{ $activity->related_content->id }}, 'forum', '{{ $reaction }}')">
                            <img class="reaction-option-image" src="{{ theme_asset('img/reaction/'.$reaction.'.png') }}" alt="reaction-{{ $reaction }}">
                        </div>
                    @endforeach
                </div>
                <!-- /REACTION OPTIONS DROPDOWN -->
            </div>
            <!-- /REACTION OPTION -->
        @endauth
        @auth
            <!-- POST OPTION -->
            <div class="post-option"
                 data-activity-comment
                 data-comment-id="{{ $activity->related_content->id }}"
                 data-comment-type="forum">
                <!-- POST OPTION ICON -->
                <svg class="post-option-icon icon-comment">
                    <use xlink:href="#svg-comment"></use>
                </svg>
                <!-- /POST OPTION ICON -->
                <!-- POST OPTION TEXT -->
                <p class="post-option-text">{{ __('messages.comment') }}</p>
                <!-- /POST OPTION TEXT -->
            </div>
            <!-- /POST OPTION -->
        @endauth

        <!-- POST OPTION -->
        <div class="post-option-wrap" style="position: relative;" data-activity-menu-wrap>
            <!-- POST OPTION -->
            <div class="post-option"
                 data-activity-menu-trigger
                 data-activity-menu-type="share">
                <!-- POST OPTION ICON -->
                <svg class="post-option-icon icon-share">
                    <use xlink:href="#svg-share"></use>
                </svg>
                <!-- /POST OPTION ICON -->
                <!-- POST OPTION TEXT -->
                <p class="post-option-text">{{ __('messages.share') }}</p>
                <!-- /POST OPTION TEXT -->
            </div>
            <!-- /POST OPTION -->

            <!-- REACTION OPTIONS -->
            <div class="reaction-options reaction-options-dropdown"
                 data-activity-menu-panel
                 style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                 <!-- SHARE OPTIONS -->
                 @foreach(['facebook', 'twitter', 'linkedin', 'telegram'] as $social)
                    <div class="reaction-option text-tooltip-tft" data-title="{{ $social }}" style="position: relative;">
                        <a href="javascript:void(0);" onclick="sharePost('{{ $social }}', '{{ route('forum.topic', $activity->tp_id) }}', '{{ $activity->related_content->name ?? '' }}')">
                            <img class="reaction-option-image" src="{{ theme_asset('img/icons/'.$social.'-icon.png') }}">
                        </a>
                    </div>
                 @endforeach
                 @auth
                    <div class="reaction-option text-tooltip-tft" data-title="{{ __('messages.quote_repost') }}" style="position: relative;">
                        <a href="javascript:void(0);" onclick="openRepostComposer({{ $activity->id }}, '{{ $repostAuthorName }}', '{{ addslashes($repostExcerpt) }}')">
                            <span style="width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #615dfa; color: #fff;">
                                <i class="fa fa-retweet" aria-hidden="true"></i>
                            </span>
                        </a>
                    </div>
                 @endauth
            </div>
            <!-- /REACTION OPTIONS -->
        </div>
        <!-- /POST OPTION -->
    </div>
    <!-- /POST OPTIONS -->
    <div class="post-comment-list post-comment-list-{{ $activity->related_content->id }}"></div>
</div>
