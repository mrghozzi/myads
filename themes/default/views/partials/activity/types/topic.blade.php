@php
    $activityUser = $activity->user;
    $activityUserProfileUrl = $activityUser ? route('profile.show', $activityUser->username) : '#';
    $activityUserName = $activityUser?->username ?? __('messages.unknown_user');
    $activityUserAvatar = $activityUser ? $activityUser->avatarUrl() : asset('upload/_avatar.png');
    $activityUserPresence = $activityUser?->isOnline() ? 'online' : 'offline';
    $activityUserIsAdmin = $activityUser?->isAdmin() ?? false;
    $repostExcerpt = \Illuminate\Support\Str::limit(strip_tags($activity->related_content->name ?? ''), 80);
    $repostAuthorName = addslashes($activityUserName);
@endphp

<div class="widget-box no-padding activity-post-card post{{ $activity->id }}">
    <div class="widget-box-settings">
        <div class="post-settings-wrap" style="position: relative;">
            <div class="post-settings widget-box-post-settings-dropdown-trigger">
                <svg class="post-settings-icon icon-more-dots">
                    <use xlink:href="#svg-more-dots"></use>
                </svg>
            </div>
            <div class="simple-dropdown widget-box-post-settings-dropdown" style="position: absolute; z-index: 9999; top: 30px; right: 9px; opacity: 0; visibility: hidden; transform: translate(0px, -20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                @auth
                    @if(auth()->id() == $activity->uid || auth()->user()->isAdmin())
                        <p class="simple-dropdown-link post_edit{{ $activity->id }}" onclick="postEdit({{ $activity->tp_id }}, 2)"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{{ __('messages.edit') }}</p>
                        <p class="simple-dropdown-link post_delete{{ $activity->id }}" onclick="deletePost({{ $activity->tp_id }}, 2, '.post{{ $activity->id }}')"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}</p>
                    @endif
                    @include('theme::partials.activity.promotion_link', ['activity' => $activity])
                    <p class="simple-dropdown-link post_report{{ $activity->id }}" onclick="reportPost({{ $activity->tp_id }}, 2, {{ $activity->related_content->id }})"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}</p>
                    <p class="simple-dropdown-link author_report{{ $activity->id }}" onclick="reportUser({{ $activity->uid }}, {{ $activity->related_content->id }})"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_author') }}</p>
                @endauth
                <p class="simple-dropdown-link copy_link" onclick="navigator.clipboard.writeText('{{ route('forum.topic', $activity->tp_id) }}'); var notif = document.getElementById('notif{{ $activity->related_content->id }}'); notif.innerHTML = '<div class=\'alert alert-success\' role=\'alert\'>{{ __('messages.link_copied') }}</div>'; notif.style.display = 'block'; setTimeout(function() { notif.style.display = 'none'; }, 5000);"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}</p>
            </div>
        </div>
    </div>

    <div class="widget-box-status">
        <div class="widget-box-status-content">
            <div class="user-status">
                <a class="user-status-avatar" href="{{ $activityUserProfileUrl }}">
                    <div class="user-avatar small no-outline {{ $activityUserPresence }}">
                        <div class="user-avatar-content">
                            <div class="hexagon-image-30-32" data-src="{{ $activityUserAvatar }}" style="width: 30px; height: 32px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                            </div>
                        </div>
                        <div class="user-avatar-progress-border">
                            <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;"></div>
                        </div>
                        @if($activityUserIsAdmin)
                            <div class="user-avatar-badge">
                                <div class="user-avatar-badge-border">
                                    <div class="hexagon-22-24" style="width: 22px; height: 24px; position: relative;"></div>
                                </div>
                                <div class="user-avatar-badge-content">
                                    <div class="hexagon-dark-16-18" style="width: 16px; height: 18px; position: relative;"></div>
                                </div>
                                <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                            </div>
                        @endif
                    </div>
                </a>
                <p class="user-status-title medium">
                    <a class="bold" href="{{ $activityUserProfileUrl }}">{{ $activityUserName }}</a>
                </p>
                <p class="user-status-text small">
                    <i class="fa fa-clock-o"></i>&nbsp;{{ __('messages.ago') }}&nbsp; {{ $activity->date_formatted }}
                </p>
            </div>

            @include('theme::partials.activity.promotion_badge', ['activity' => $activity])

            <div class="tag-sticker">
                <svg class="tag-sticker-icon icon-forums">
                    <use xlink:href="#svg-forums"></use>
                </svg>
            </div>

            <div class="widget-box-status-text post_text{{ $activity->related_content->id }}">
                <div class="textpost" id="post_form{{ $activity->related_content->id }}">
                    @php
                        $topicExcerpt = strip_tags($activity->related_content->txt);
                        $topicExcerpt = \Illuminate\Support\Str::limit($topicExcerpt, 180);
                        $topicBanner = $activity->related_content->image_url ? asset($activity->related_content->image_url) : theme_asset('img/background_topic.jpg');
                    @endphp
                    <a class="activity-super" href="{{ route('forum.topic', $activity->tp_id) }}">
                        <div class="activity-super-banner" style="background-image: url({{ $topicBanner }});">
                            <span class="activity-super-category">
                                <i class="fa {{ $activity->related_content->category->icon ?? 'fa-folder' }}" aria-hidden="true"></i>
                                {{ $activity->related_content->category->name ?? '' }}
                            </span>
                        </div>
                        <div class="activity-super-content">
                            <h3 class="activity-super-title">{{ $activity->related_content->name }}</h3>
                            <p class="activity-super-excerpt">{{ $topicExcerpt }}</p>
                            <div class="activity-super-footer">
                                <div class="activity-super-stats">
                                    <div class="activity-super-stat">
                                        <i class="fa fa-eye"></i>
                                        {{ $activity->related_content->vu }}
                                    </div>
                                    <div class="activity-super-stat">
                                        <i class="fa fa-comment"></i>
                                        {{ $activity->comments_count }}
                                    </div>
                                </div>
                                <span class="activity-super-more">
                                    {{ __('messages.read_more') }}
                                    <i class="fa fa-arrow-right"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                    <div id="report{{ $activity->related_content->id }}"></div>
                </div>
            </div>
            <div id="notif{{ $activity->related_content->id }}"></div>

            <div class="content-actions">
                <div class="content-action">
                    @include('theme::partials.activity.reaction-list', ['activity' => $activity])
                    <div class="meta-line">
                        <p class="meta-line-text">{{ $activity->reactions_count }} {{ __('messages.reactions') }}</p>
                    </div>
                </div>
                <div class="content-action">
                    <div class="meta-line">
                        <p class="meta-line-link">
                            <a href="{{ route('forum.topic', $activity->tp_id) }}">{{ $activity->comments_count }} {{ __('messages.comments') }}</a>
                        </p>
                    </div>
                </div>
                <div class="content-action">
                    <div class="meta-line">
                        <p class="meta-line-link">
                            <a href="{{ route('forum.topic', $activity->tp_id) }}">{{ $activity->reposts_count }} {{ __('messages.reposts') }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
            <div class="post-option"
                 data-activity-comment
                 data-comment-id="{{ $activity->related_content->id }}"
                 data-comment-type="forum"
                 data-comment-focus="1">
                <svg class="post-option-icon icon-comment">
                    <use xlink:href="#svg-comment"></use>
                </svg>
                <p class="post-option-text">{{ __('messages.comment') }}</p>
            </div>
        @endauth
        <div class="post-option-wrap" style="position: relative;" data-activity-menu-wrap>
            <div class="post-option"
                 data-activity-menu-trigger
                 data-activity-menu-type="share">
                <svg class="post-option-icon icon-share">
                    <use xlink:href="#svg-share"></use>
                </svg>
                <p class="post-option-text">{{ __('messages.share') }}</p>
            </div>
            <div class="reaction-options reaction-options-dropdown"
                 data-activity-menu-panel
                 style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
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
        </div>
    </div>
    <div class="post-comment-list post-comment-list-{{ $activity->related_content->id }}"></div>
</div>
