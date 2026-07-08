@php
    $activityUser = $activity->user;
    $activityUserProfileUrl = $activityUser ? route('profile.show', $activityUser->username) : '#';
    $activityUserName = $activityUser?->username ?? __('messages.unknown_user');
    $activityUserAvatar = $activityUser ? $activityUser->avatarUrl() : asset('upload/_avatar.png');
    $activityUserPresence = $activityUser?->isOnline() ? 'online' : 'offline';
    $activityUserIsAdmin = $activityUser?->isAdmin() ?? false;
    $activityUserHasVerifiedBadge = $activityUser?->hasVerifiedBadge() ?? false;
    $statusUserHasVerifiedBadge = $statusUser?->hasVerifiedBadge() ?? false;
    $formattedText = \App\Support\ContentFormatter::format($activity->related_content->txt ?? '');
    $repostExcerpt = \Illuminate\Support\Str::limit(strip_tags($activity->related_content->txt ?? ''), 80);
    $repostAuthorName = addslashes($activityUserName);
    $attachments = $activity->related_content->attachments;
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
                        <p class="simple-dropdown-link post_edit{{ $activity->id }}" onclick="postEdit({{ $activity->tp_id }}, {{ $activity->s_type }})"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{{ __('messages.edit') }}</p>
                        <p class="simple-dropdown-link post_delete{{ $activity->id }}" onclick="deletePost({{ $activity->tp_id }}, {{ $activity->s_type }}, '.post{{ $activity->id }}')"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}</p>
                                        @endif
                    @if(isset($isOwnProfile) && $isOwnProfile && auth()->id() == $activity->uid)
                        <!-- SIMPLE DROPDOWN LINK -->
                        <p class="simple-dropdown-link" onclick="togglePinPost({{ $activity->id }}, {{ $activity->is_pinned ? 'true' : 'false' }}, {{ isset($hasPinnedPost) && $hasPinnedPost ? 'true' : 'false' }})" style="cursor: pointer;">
                            <i class="fa fa-thumb-tack" aria-hidden="true"></i>&nbsp;{{ $activity->is_pinned ? __('messages.unpin_post') ?? 'Unpin' : __('messages.pin_post') ?? 'Pin to profile' }}
                        </p>
                        <!-- /SIMPLE DROPDOWN LINK -->
                    @endif
                    <p class="simple-dropdown-link post_report{{ $activity->id }}" onclick="reportPost({{ $activity->tp_id }}, {{ $activity->s_type }}, {{ $activity->related_content->id }})"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}</p>
                @endauth
            </div>
        </div>
    </div>

    <div class="widget-box-status">
        <div class="widget-box-status-content">
            <div class="user-status">
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
                            <div class="hexagon-border-40-44" data-line-color="{{ $activityUser ? $activityUser->profileBadgeColor() : '' }}" style="width: 40px; height: 44px; position: relative;"></div>
                            <!-- /HEXAGON -->
                        </div>
                        <!-- /USER AVATAR PROGRESS BORDER -->

                        @if($activityUserHasVerifiedBadge)
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
                <p class="user-status-title medium">
                    <a class="bold" href="{{ $activityUserProfileUrl }}">{{ $activityUserName }}</a>
                    &nbsp;{{ __('messages.added_files') ?? 'shared files' }}
                </p>
                <p class="user-status-text small">
                    <i class="fa fa-clock-o"></i>&nbsp;{{ __('messages.ago') }}&nbsp;{{ $activity->date_formatted }}
                </p>
            </div>

            @include('theme::partials.activity.promotion_badge', ['activity' => $activity])
            
            <div class="tag-sticker">
                <i class="fa-solid fa-download" style="font-size: 14px;"></i>
            </div>

            <div class="widget-box-status-text post_text{{ $activity->related_content->id }}">
                <div class="textpost" id="post_form{{ $activity->related_content->id }}">
                    {!! $formattedText !!}
                    <div id="report{{ $activity->related_content->id }}"></div>
                </div>
            </div>

            @if($attachments->count() > 0)
                <div class="post-files-list" style="margin-top: 18px; display: grid; gap: 10px;">
                    @foreach($attachments as $file)
                        <div class="post-file-item">
                            <div class="file-item-icon">
                                <i class="fa fa-file-text" aria-hidden="true"></i>
                            </div>
                            <div class="file-item-info">
                                <p class="file-item-name">{{ $file->original_name }}</p>
                                <p class="file-item-size">{{ round($file->file_size / 1024, 1) }} KB</p>
                            </div>
                            <a href="{{ asset($file->file_path) }}" download class="button small primary" style="padding: 0 12px; height: 32px; min-width: auto; border-radius: 8px;">
                                <i class="fa fa-download" aria-hidden="true"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @include('theme::partials.activity.post_footer_shared', ['activity' => $activity, 'repostAuthorName' => $repostAuthorName, 'repostExcerpt' => $repostExcerpt])
</div>
