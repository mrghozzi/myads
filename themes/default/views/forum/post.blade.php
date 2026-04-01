@extends('theme::layouts.master')
@include('theme::forum._assets')

@section('content')
<div class="forum-rdx forum-rdx-topic">
<!-- ADS -->
@include('theme::partials.ads', ['id' => 5])

@php
    $showForumRoleBadges = (int) ($forumSettings['show_role_badges'] ?? 1) === 1;
    $topicCategoryId = (int) $topic->cat;
    $canEditTopic = auth()->check() && (
        auth()->id() === (int) $topic->uid
        || auth()->user()->canModerateForum('edit_topics', $topicCategoryId)
    );
    $canDeleteTopic = auth()->check() && (
        auth()->id() === (int) $topic->uid
        || auth()->user()->canModerateForum('delete_topics', $topicCategoryId)
    );
    $canPinTopic = auth()->check() && auth()->user()->canModerateForum('pin_topics', $topicCategoryId);
    $canLockTopic = auth()->check() && auth()->user()->canModerateForum('lock_topics', $topicCategoryId);
    $canCommentWhenLocked = auth()->check() && (
        auth()->id() === (int) $topic->uid
        || auth()->user()->canModerateForum('lock_topics', $topicCategoryId)
    );
@endphp

<div class="grid grid post{{ $status->id }}">
    <div class="widget-box no-padding activity-post-card post{{ $status->id }}">
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
                    @if($canEditTopic)
                        @if((int) $topic->cat === 0)
                            <p class="simple-dropdown-link" onclick="postEdit({{ $topic->id }}, {{ $status->s_type }})">
                                <i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{{ __('messages.edit') }}
                            </p>
                        @else
                            <a class="simple-dropdown-link" href="{{ route('forum.edit', $topic->id) }}">
                                <i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{{ __('messages.edit') }}
                            </a>
                        @endif
                    @endif
                    @if($canDeleteTopic)
                        <p class="simple-dropdown-link post_delete{{ $status->id }}" onclick="deletePost({{ $topic->id }}, 100)">
                            <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}
                        </p>
                    @endif
                    @include('theme::partials.activity.promotion_link', ['activity' => $status])
                    @if($canPinTopic && $topic->cat > 0)
                        <form method="POST" action="{{ route('forum.pin', $topic->id) }}">
                            @csrf
                            <button type="submit" class="simple-dropdown-link" style="width:100%;text-align:left;border:0;background:transparent;">
                                <i class="fa fa-thumb-tack" aria-hidden="true"></i>&nbsp;{{ $topic->is_pinned ? __('messages.unpin_topic') : __('messages.pin_topic') }}
                            </button>
                        </form>
                    @endif
                    @if($canLockTopic && $topic->cat > 0)
                        <form method="POST" action="{{ route('forum.lock', $topic->id) }}">
                            @csrf
                            <button type="submit" class="simple-dropdown-link" style="width:100%;text-align:left;border:0;background:transparent;">
                                <i class="fa {{ $topic->is_locked ? 'fa-unlock' : 'fa-lock' }}" aria-hidden="true"></i>&nbsp;{{ $topic->is_locked ? __('messages.unlock_topic') : __('messages.lock_topic') }}
                            </button>
                        </form>
                    @endif
                    <!-- SIMPLE DROPDOWN LINK -->
                    <p class="simple-dropdown-link post_report{{ $topic->id }}" onclick="reportPost({{ $topic->id }}, 2)">
                        <i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}
                    </p>
                    <!-- /SIMPLE DROPDOWN LINK -->

                    <!-- SIMPLE DROPDOWN LINK -->
                    <p class="simple-dropdown-link author_report{{ $topic->id }}" onclick="reportUser({{ $topic->uid }})">
                        <i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }} {{ __('messages.author') }}
                    </p>
                    <!-- /SIMPLE DROPDOWN LINK -->
                    
                    <!-- SIMPLE DROPDOWN LINK -->
                    <p class="simple-dropdown-link copy_link" onclick="navigator.clipboard.writeText('{{ route('forum.topic', $topic->id) }}'); var notif = document.getElementById('notif{{ $topic->id }}'); notif.innerHTML = '<div class=\'alert alert-success\' role=\'alert\'>{{ __('messages.link_copied') }}</div>'; notif.style.display = 'block'; setTimeout(function() { notif.style.display = 'none'; }, 5000);">
                        <i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}
                    </p>
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
                    <a class="user-status-avatar" href="{{ route('profile.show', $topic->user->username) }}">
                        <!-- USER AVATAR -->
                        <div class="user-avatar small no-outline {{ $topic->user->isOnline() ? 'online' : 'offline' }}">
                            <!-- USER AVATAR CONTENT -->
                            <div class="user-avatar-content">
                                <!-- HEXAGON -->
                                <div class="hexagon-image-30-32" data-src="{{ $topic->user ? $topic->user->avatarUrl() : asset('upload/_avatar.png') }}" style="width: 30px; height: 32px; position: relative;">
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

                            @if($topic->user->ucheck == 1)
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
                        <a class="bold" href="{{ route('profile.show', $topic->user->username) }}">{{ $topic->user->username }}</a>
                    </p>
                    <!-- /USER STATUS TITLE -->
                    @if($showForumRoleBadges)
                        <p class="user-status-text small" style="margin-top: -8px;">
                            {{ $topic->user->forumRoleLabel($topicCategoryId) }}
                        </p>
                    @endif

                    <!-- USER STATUS TEXT -->
                    <p class="user-status-text small">
                        <i class="fa fa-clock-o"></i>&nbsp;{{ __('messages.ago') }}&nbsp; {{ \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans() }}
                    </p>
                    <!-- /USER STATUS TEXT -->
                    @if($topic->is_pinned || $topic->is_locked)
                        <p class="user-status-text small" style="margin-top: -6px;">
                            @if($topic->is_pinned)
                                <span class="badge bg-warning text-dark">{{ __('messages.pinned') }}</span>
                            @endif
                            @if($topic->is_locked)
                                <span class="badge bg-secondary">{{ __('messages.locked') }}</span>
                            @endif
                        </p>
                    @endif
                </div>
                <!-- /USER STATUS -->

                <div class="tag-sticker">
                    <!-- TAG STICKER ICON -->
                    <svg class="tag-sticker-icon icon-blog-posts">
                        <use xlink:href="#svg-blog-posts"></use>
                    </svg>
                    <!-- /TAG STICKER ICON -->
                </div>

                <!-- WIDGET BOX STATUS TEXT -->
                <div class="widget-box-status-text post_text{{ $topic->id }}">
                    <br/>
                    <div class="textpost" id="post_form{{ $topic->id }}">
                        @php
                            $content = $topic->txt;
                            $content = preg_replace('/#(\w+)/', '<a href="'.url('/tag/$1').'">#$1</a>', $content);
                            // Basic sanitization if needed, but old code just did strip_tags with exceptions
                            $content = strip_tags($content, '<p><a><b><br><li><ul><font><span><pre><u><s><img><iframe>');
                        @endphp
                        {!! nl2br($content) !!}
                        <div id="report{{ $topic->id }}"></div>
                    </div>
                </div>
                <!-- /WIDGET BOX STATUS TEXT -->

                @if($status->linkPreviewRecord)
                    @include('theme::partials.activity.link_preview', ['activity' => $status])
                @endif

                @if($status->repostRecord)
                    @include('theme::partials.activity.repost_embed', ['activity' => $status])
                @endif

                @if($topic->attachments->isNotEmpty())
                    <div class="widget-box" style="margin-bottom: 14px;">
                        <div class="widget-box-content">
                            <p class="bold" style="margin-bottom: 8px;">{{ __('messages.topic_attachments') }}</p>
                            @foreach($topic->attachments as $attachment)
                                <p style="margin-bottom: 6px;">
                                    <a href="{{ route('forum.attachment.download', $attachment->id) }}">
                                        <i class="fa fa-paperclip" aria-hidden="true"></i>
                                        {{ $attachment->original_name }}
                                    </a>
                                    <span style="color:#7f85a3;font-size:12px;">({{ $attachment->human_size }})</span>
                                </p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div id="notif{{ $topic->id }}"></div>

                <!-- CONTENT ACTIONS -->
                <div class="content-actions">
                    <!-- CONTENT ACTION -->
                    <div class="content-action">
                        <!-- META LINE -->
                        <div class="meta-line">
                            <!-- META LINE TEXT -->
                            <p class="meta-line-text">{{ $topic->likes()->count() }} {{ __('messages.reactions') }}</p>
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
                                <a href="{{ route('forum.topic', $topic->id) }}">{{ $topic->comments()->count() }} {{ __('messages.comments') }}</a>
                            </p>
                            <!-- /META LINE LINK -->
                        </div>
                        <!-- /META LINE -->
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
            <!-- POST OPTION WRAP -->
            <div class="post-option-wrap" style="position: relative;">
                <!-- POST OPTION -->
                <div class="post-option reaction-options-dropdown-trigger" onclick="toggleReactionDropdown(this)">
                    <div id="reaction_image{{ $status->id }}">
                        @php
                            $myReaction = \App\Models\Like::where('uid', Auth::id())->where('sid', $topic->id)->where('type', 2)->first();
                            $reactionType = 'like';
                            if($myReaction) {
                                $reactionOption = \App\Models\Option::where('o_parent', $myReaction->id)->where('o_type', 'data_reaction')->first();
                                if($reactionOption) $reactionType = $reactionOption->o_valuer;
                            }
                        @endphp
                        
                        @if($myReaction)
                            <img class="reaction-option-image" src="{{ theme_asset('img/reaction/'.$reactionType.'.png') }}" width="30" alt="reaction-{{ $reactionType }}">
                        @else
                            <svg class="post-option-icon icon-thumbs-up"><use xlink:href="#svg-thumbs-up"></use></svg>
                        @endif
                    </div>
                    <!-- POST OPTION TEXT -->
                    <p class="post-option-text reaction_txt{{ $status->id }}" style="{{ $myReaction ? 'color: #1bc8db;' : '' }}">
                        &nbsp;{{ $myReaction ? ucfirst($reactionType) : __('messages.react') }}
                    </p>
                    <!-- /POST OPTION TEXT -->
                </div>
                <!-- /POST OPTION -->

                <!-- REACTION OPTIONS -->
                <div class="reaction-options reaction-options-dropdown" style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; display: none;">
                    @foreach(['like', 'love', 'dislike', 'happy', 'funny', 'wow', 'angry', 'sad'] as $reaction)
                    <div class="reaction-option text-tooltip-tft reaction_100_{{ $topic->id }}" data-title="{{ $reaction }}" onclick="postReaction({{ $topic->id }}, '{{ $reaction }}')">
                        <img class="reaction-option-image" src="{{ theme_asset('img/reaction/'.$reaction.'.png') }}" alt="reaction-{{ $reaction }}">
                    </div>
                    @endforeach
                </div>
                <!-- /REACTION OPTIONS -->
            </div>
            <!-- /POST OPTION WRAP -->

            <!-- POST OPTION -->
            @if(!$topic->is_locked || $canCommentWhenLocked)
                <div class="post-option sh_comment_p{{ $status->id }}" onclick="focusComment({{ $topic->id }})">
                    <svg class="post-option-icon icon-comment">
                        <use xlink:href="#svg-comment"></use>
                    </svg>
                    <p class="post-option-text">{{ __('messages.comment') }}</p>
                </div>
            @endif
            <!-- /POST OPTION -->
            @endauth

            <!-- POST OPTION -->
            <div class="post-option-wrap" style="position: relative;">
                <!-- POST OPTION -->
                <div class="post-option reaction-options-dropdown-trigger" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'flex' : 'none'">
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
                <div class="reaction-options reaction-options-dropdown" style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; display: none;">
                    @foreach(['facebook', 'twitter', 'linkedin', 'telegram'] as $social)
                    <div class="reaction-option text-tooltip-tft" data-title="{{ $social }}" style="position: relative;">
                        <a href="javascript:void(0);" onclick="sharePost('{{ $social }}', '{{ route('forum.topic', $topic->id) }}', '{{ $topic->name }}')">
                            <img class="reaction-option-image" src="{{ theme_asset('img/icons/'.$social.'-icon.png') }}">
                        </a>
                    </div>
                    @endforeach
                </div>
                <!-- /REACTION OPTIONS -->
            </div>
            <!-- /POST OPTION -->
        </div>
        <!-- /POST OPTIONS -->
    </div>

    <!-- COMMENTS -->
    <div class="post-comment-list post-comment-list-{{ $topic->id }} comment_100_{{ $topic->id }}">
        @include('theme::partials.activity.comments', [
            'comments' => $topic->comments()->orderBy('id', 'desc')->get(),
            'id' => $topic->id,
            'type' => 'forum',
            'limit' => 100,
            'hide_form' => $topic->is_locked && !$canCommentWhenLocked,
            'locked_topic' => (bool) $topic->is_locked,
            'forum_category_id' => $topicCategoryId
        ])
    </div>
</div>

@include('theme::forum.scripts')
</div>
@endsection
