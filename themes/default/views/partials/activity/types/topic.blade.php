@php
    $activityUser = $activity->user;
    $activityUserProfileUrl = $activityUser ? route('profile.show', $activityUser->username) : '#';
    $activityUserName = $activityUser?->username ?? __('messages.unknown_user');
    $activityUserAvatar = $activityUser ? $activityUser->avatarUrl() : asset('upload/_avatar.png');
    $activityUserPresence = $activityUser?->isOnline() ? 'online' : 'offline';
    $activityUserIsAdmin = $activityUser?->isAdmin() ?? false;
    $activityUserHasVerifiedBadge = $activityUser?->hasVerifiedBadge() ?? false;
    $statusUserHasVerifiedBadge = $statusUser?->hasVerifiedBadge() ?? false;
    
    $topic = $activity->related_content;
    $repostExcerpt = \Illuminate\Support\Str::limit(strip_tags($topic?->name ?? ''), 80);
    $repostAuthorName = addslashes($activityUserName);

    // Topic Rich Meta & Styling
    $topicCategory = $topic?->category;
    $catColor = $topicCategory?->color ?: '#6366f1';
    $catIcon = $topicCategory?->icons ?: 'fa-folder';
    $topicImageUrl = $topic?->imageOption ? asset($topic->imageOption->o_valuer) : ($topic?->image_url ? asset($topic->image_url) : null);
    $hasImage = !empty($topicImageUrl);
    $rawTopicText = $topic?->txt ?? '';
    $topicExcerpt = \Illuminate\Support\Str::limit(strip_tags(\App\Support\ContentFormatter::formatForum($rawTopicText)), 220);
    $topicCommentsCount = $activity->comments_count ?? $topic?->comments_count ?? 0;
    $topicViews = $topic?->vu ?? 0;
    $topicDate = $activity->date ?? $topic?->date ?? time();
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
                        <p class="simple-dropdown-link post_edit{{ $activity->id }}" onclick="postEdit({{ $activity->tp_id }}, 2)">
                            <i class="fa-solid fa-pen-to-square me-2"></i>{{ __('messages.edit') }}
                        </p>
                        <p class="simple-dropdown-link post_delete{{ $activity->id }}" onclick="deletePost({{ $activity->tp_id }}, 2, '.post{{ $activity->id }}')">
                            <i class="fa-solid fa-trash me-2"></i>{{ __('messages.delete') }}
                        </p>
                    @endif
                    @if(isset($isOwnProfile) && $isOwnProfile && auth()->id() == $activity->uid)
                        <p class="simple-dropdown-link" onclick="togglePinPost({{ $activity->id }}, {{ $activity->is_pinned ? 'true' : 'false' }}, {{ isset($hasPinnedPost) && $hasPinnedPost ? 'true' : 'false' }})" style="cursor: pointer;">
                            <i class="fa-solid fa-thumbtack me-2"></i>{{ $activity->is_pinned ? __('messages.unpin_post') ?? 'Unpin' : __('messages.pin_post') ?? 'Pin to profile' }}
                        </p>
                    @endif
                    @include('theme::partials.activity.promotion_link', ['activity' => $activity])
                    <p class="simple-dropdown-link post_report{{ $activity->id }}" onclick="reportPost({{ $activity->tp_id }}, 2, {{ $activity->related_content->id }})">
                        <i class="fa-solid fa-flag text-warning me-2"></i>{{ __('messages.report') }}
                    </p>
                    <p class="simple-dropdown-link author_report{{ $activity->id }}" onclick="reportUser({{ $activity->uid }}, {{ $activity->related_content->id }})">
                        <i class="fa-solid fa-flag text-warning me-2"></i>{{ __('messages.report_author') }}
                    </p>
                @endauth
                <p class="simple-dropdown-link copy_link" onclick="navigator.clipboard.writeText('{{ route('forum.topic', $activity->tp_id) }}'); var notif = document.getElementById('notif{{ $activity->related_content->id }}'); notif.innerHTML = '<div class=\'alert alert-success\' role=\'alert\'>{{ __('messages.link_copied') }}</div>'; notif.style.display = 'block'; setTimeout(function() { notif.style.display = 'none'; }, 5000);">
                    <i class="fa-solid fa-link me-2"></i>{{ __('messages.copy_link') }}
                </p>
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
                            <div class="hexagon-border-40-44" data-line-color="{{ $activityUser ? $activityUser->profileBadgeColor() : '' }}" style="width: 40px; height: 44px; position: relative;"></div>
                        </div>
                        @if($activityUserHasVerifiedBadge)
                            <div class="user-avatar-badge">
                                <div class="user-avatar-badge-border">
                                    <div class="hexagon-22-24" style="width: 22px; height: 24px; position: relative;"></div>
                                </div>
                                <div class="user-avatar-badge-content">
                                    <div class="hexagon-dark-16-18" style="width: 16px; height: 18px; position: relative;"></div>
                                </div>
                                <p class="user-avatar-badge-text"><i class="fa-solid fa-check"></i></p>
                            </div>
                        @endif
                    </div>
                </a>
                <p class="user-status-title medium">
                    <a class="bold" href="{{ $activityUserProfileUrl }}">{{ $activityUserName }}</a>
                </p>
                <p class="user-status-text small d-flex align-items-center flex-wrap gap-2">
                    <span>
                        <i class="fa-regular fa-clock me-1"></i>{{ __('messages.ago') }} {{ $activity->date_formatted }}
                    </span>
                    <span class="forum-origin-chip">
                        <i class="fa-solid fa-comments me-1"></i>{{ __('messages.forum_discussion') ?? 'Forum' }}
                    </span>
                </p>

                @if($activity->group)
                    <div style="margin-top:8px;">
                        @include('theme::partials.groups.badge', ['groupBadge' => $activity->group])
                    </div>
                @endif
            </div>

            @include('theme::partials.activity.promotion_badge', ['activity' => $activity])

            <div class="tag-sticker">
                <svg class="tag-sticker-icon icon-forums">
                    <use xlink:href="#svg-forums"></use>
                </svg>
            </div>

            <div class="widget-box-status-text post_text{{ $activity->related_content->id }}">
                <div class="textpost" id="post_form{{ $activity->related_content->id }}">
                    <!-- Modern Forum-Style Forum Discussion Feed Card -->
                    <div class="forum-feed-card">
                        <!-- Top Metadata / Category / Badges Bar -->
                        <div class="forum-feed-top-bar">
                            <div class="forum-feed-pills">
                                @if($topicCategory)
                                    <a href="{{ route('forum.category', $topic->cat) }}" class="forum-feed-cat-pill" style="--cat-color: {{ $catColor }};">
                                        <span class="forum-feed-cat-bullet" style="background-color: {{ $catColor }};"></span>
                                        <i class="fa {{ $catIcon }}"></i>
                                        <span>{{ $topicCategory->name }}</span>
                                    </a>
                                @else
                                    <span class="forum-feed-cat-pill default">
                                        <i class="fa-solid fa-comments"></i>
                                        <span>{{ __('messages.forum') ?? 'Forum' }}</span>
                                    </span>
                                @endif

                                @if($activity->group)
                                    <span class="forum-feed-group-pill">
                                        <i class="fa-solid fa-users me-1"></i>
                                        <span>{{ $activity->group->name }}</span>
                                    </span>
                                @endif
                            </div>

                            <div class="forum-feed-status-badges">
                                @if($topic->is_pinned)
                                    <span class="forum-status-pill pinned" title="{{ __('messages.pinned') }}">
                                        <i class="fa-solid fa-thumbtack me-1"></i>
                                        <span>{{ __('messages.pinned') }}</span>
                                    </span>
                                @endif
                                @if($topic->is_locked)
                                    <span class="forum-status-pill locked" title="{{ __('messages.locked') }}">
                                        <i class="fa-solid fa-lock me-1"></i>
                                        <span>{{ __('messages.locked') }}</span>
                                    </span>
                                @endif
                                @if($topic->best_comment_id)
                                    <span class="forum-status-pill solved" title="{{ __('messages.best_answer') }}">
                                        <i class="fa-solid fa-circle-check me-1"></i>
                                        <span>{{ __('messages.best_answer') }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Discussion Title & Content Excerpt -->
                        <div class="forum-feed-body">
                            <h3 class="forum-feed-title">
                                <a href="{{ route('forum.topic', $activity->tp_id) }}">{{ $topic->name }}</a>
                            </h3>

                            @if(!empty(trim($topicExcerpt)))
                                <p class="forum-feed-excerpt">{{ $topicExcerpt }}</p>
                            @endif

                            @if($hasImage)
                                <div class="forum-feed-media">
                                    <a href="{{ route('forum.topic', $activity->tp_id) }}" class="forum-feed-media-link">
                                        <img src="{{ $topicImageUrl }}" alt="{{ $topic->name }}" class="forum-feed-media-img" loading="lazy">
                                    </a>
                                </div>
                            @endif

                            @if($topic->attachments && $topic->attachments->isNotEmpty())
                                <div class="forum-feed-attachments">
                                    <span class="forum-feed-attachment-pill">
                                        <i class="fa-solid fa-paperclip me-1 text-primary"></i>
                                        <span>{{ $topic->attachments->count() }} {{ __('messages.topic_attachments') ?? 'Attachments' }}</span>
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Footer Stats & CTA Action -->
                        <div class="forum-feed-footer">
                            <div class="forum-feed-metrics">
                                <span class="forum-metric-item" title="{{ __('messages.views') }}">
                                    <i class="fa-regular fa-eye me-1 text-primary"></i>
                                    <span class="metric-val">{{ number_format($topicViews) }}</span>
                                    <span class="d-none d-md-inline">{{ __('messages.views') }}</span>
                                </span>
                                <a href="{{ route('forum.topic', $activity->tp_id) }}#reply" class="forum-metric-item" title="{{ __('messages.replies') }}">
                                    <i class="fa-regular fa-comment-dots me-1 text-info"></i>
                                    <span class="metric-val">{{ number_format($topicCommentsCount) }}</span>
                                    <span class="d-none d-sm-inline">{{ __('messages.replies') }}</span>
                                </a>
                                @if($topic->last_posted_at && $topic->last_posted_at != $topicDate)
                                    <span class="forum-metric-item forum-last-activity d-none d-lg-inline" title="{{ __('messages.latest_reply') }}">
                                        <i class="fa-solid fa-clock-rotate-left me-1 text-muted"></i>
                                        <span>{{ \Carbon\Carbon::createFromTimestamp($topic->last_posted_at)->diffForHumans() }}</span>
                                    </span>
                                @endif
                            </div>

                            <a href="{{ route('forum.topic', $activity->tp_id) }}" class="forum-feed-join-btn">
                                <span>{{ __('messages.view_discussion') ?? 'View Discussion' }}</span>
                                <i class="fa-solid fa-arrow-right forum-arrow-icon ms-1"></i>
                            </a>
                        </div>
                    </div>

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
                            <a href="{{ route('forum.topic', $activity->tp_id) }}#reply">{{ $activity->comments_count }} {{ __('messages.comments') }}</a>
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
        <!-- COMMENT / REPLY OPTION (ينتقل مباشرة إلى صندوق الرد في صفحة الموضوع) -->
        <a href="{{ route('forum.topic', $activity->tp_id) }}#reply" class="post-option forum-comment-link" title="{{ __('messages.comment') }}">
            <svg class="post-option-icon icon-comment">
                <use xlink:href="#svg-comment"></use>
            </svg>
            <p class="post-option-text">{{ __('messages.comment') }}</p>
        </a>
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
                    @if((int) ($activity->group_id ?? 0) === 0)
                    <div class="reaction-option text-tooltip-tft" data-title="{{ __('messages.quote_repost') }}" style="position: relative;">
                        <a href="javascript:void(0);" onclick="openRepostComposer({{ $activity->id }}, '{{ $repostAuthorName }}', '{{ addslashes($repostExcerpt) }}')">
                            <span style="width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #615dfa; color: #fff;">
                                <i class="fa-solid fa-retweet" aria-hidden="true"></i>
                            </span>
                        </a>
                    </div>
                    @endif
                 @endauth
            </div>
        </div>
    </div>
    <div class="post-comment-list post-comment-list-{{ $activity->related_content->id }}"></div>
</div>
