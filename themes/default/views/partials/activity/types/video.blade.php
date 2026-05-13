@php
    $activityUser = $activity->user;
    $activityUserProfileUrl = $activityUser ? route('profile.show', $activityUser->username) : '#';
    $activityUserName = $activityUser?->username ?? __('messages.unknown_user');
    $activityUserAvatar = $activityUser ? $activityUser->avatarUrl() : asset('upload/_avatar.png');
    $activityUserPresence = $activityUser?->isOnline() ? 'online' : 'offline';
    $activityUserIsAdmin = $activityUser?->isAdmin() ?? false;
    $formattedText = \App\Support\ContentFormatter::format($activity->related_content->txt ?? '');
    $repostExcerpt = \Illuminate\Support\Str::limit(strip_tags($activity->related_content->txt ?? ''), 80);
    $repostAuthorName = addslashes($activityUserName);
    $video = $activity->related_content->attachments->first();
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
                    @include('theme::partials.activity.promotion_link', ['activity' => $activity])
                    <p class="simple-dropdown-link post_report{{ $activity->id }}" onclick="reportPost({{ $activity->tp_id }}, {{ $activity->s_type }}, {{ $activity->related_content->id }})"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}</p>
                @endauth
                <p class="simple-dropdown-link copy_link" onclick="navigator.clipboard.writeText('{{ route('forum.topic', $activity->tp_id) }}');"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}</p>
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
                <p class="user-status-title medium">
                    <a class="bold" href="{{ $activityUserProfileUrl }}">{{ $activityUserName }}</a>
                    &nbsp;{{ $activity->s_type == 14 ? __('messages.added_reels') : __('messages.added_video') }}
                </p>
                <p class="user-status-text small">
                    <i class="fa fa-clock-o"></i>&nbsp;{{ __('messages.ago') }}&nbsp;{{ $activity->date_formatted }}
                </p>
            </div>

            @include('theme::partials.activity.promotion_badge', ['activity' => $activity])
            
            <div class="tag-sticker">
                <svg class="tag-sticker-icon icon-{{ (int) $activity->s_type === 14 ? 'streams' : 'videos' }}">
                    <use xlink:href="#svg-{{ (int) $activity->s_type === 14 ? 'streams' : 'videos' }}"></use>
                </svg>
            </div>

            <div class="widget-box-status-text post_text{{ $activity->related_content->id }}">
                <div class="textpost" id="post_form{{ $activity->related_content->id }}">
                    {!! $formattedText !!}
                    <div id="report{{ $activity->related_content->id }}"></div>
                </div>
            </div>

            @if($video)
                <div class="post-video-wrapper">
                    <div class="video-watermark">
                        <img src="{{ theme_asset('img/logo_w.webp') }}" alt="Watermark">
                    </div>
                    <video class="js-plyr" controls preload="metadata">
                        <source src="{{ asset($video->file_path) }}" type="{{ $video->mime_type }}">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <script>
                    if (typeof Plyr !== 'undefined') {
                        new Plyr('.post{{ $activity->id }} .js-plyr', {
                            controls: ['play-large', 'play', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'],
                        });
                    }
                </script>
            @endif

            @if($activity->linkPreviewRecord)
                @include('theme::partials.activity.link_preview', ['activity' => $activity])
            @endif
        </div>
    </div>

    @include('theme::partials.activity.post_footer_shared', ['activity' => $activity, 'repostAuthorName' => $repostAuthorName, 'repostExcerpt' => $repostExcerpt])
</div>
