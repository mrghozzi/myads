@extends('theme::layouts.master')
@include('theme::forum._assets')

@section('content')
<div class="forum-rdx forum-rdx-topic">
<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/discussion-icon.png') }}">
    <p class="section-banner-title">{{ __('messages.forum') }}</p>
</div>
<!-- /SECTION BANNER -->

<!-- ADS -->
@include('theme::partials.ads', ['id' => 5])

<div class="section-header">
    <div class="section-header-info">
        <h2 class="section-title">{{ $topic->name }}</h2>
    </div>
</div>

<div class="section-filters-bar v7">
    <div class="section-filters-bar-actions">
        <div class="section-filters-bar-info">
            <p class="section-filters-bar-title">
                @if(!empty($group))
                    <a href="{{ route('groups.index') }}">{{ __('messages.groups_title') }}</a>
                    <span class="separator"></span>
                    <a href="{{ route('groups.show', $group) }}"><i class="fa fa-users" aria-hidden="true"></i>&nbsp;{{ $group->name }}</a>
                    <span class="separator"></span>
                @else
                    <a href="{{ route('forum.index') }}">{{ __('messages.forum') }}</a>
                    <span class="separator"></span>
                    <a href="{{ route('forum.category', $topic->cat) }}"><i class="fa {{ $topic->category->icons ?? '' }}" aria-hidden="true"></i>&nbsp;{{ $topic->category->name ?? __('messages.category_fallback') }}</a>
                    <span class="separator"></span>
                @endif
                <a href="{{ route('forum.topic', $topic->id) }}">{{ $topic->name }}</a>
            </p>
            <div class="section-filters-bar-text small-space">
                {{ \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans() }}
            </div>
        </div>
    </div>
</div>

@php
    $group = $group ?? null;
    $showForumRoleBadges = (int) ($forumSettings['show_role_badges'] ?? 1) === 1;
    $topicCategoryId = (int) $topic->cat;
    $groupAccess = app(\App\Services\GroupAccessService::class);
    $canManageGroupTopic = $group && auth()->check() ? $groupAccess->canManageGroup($group, auth()->user()) : false;
    $canEditTopic = auth()->check() && (
        auth()->id() === (int) $topic->uid
        || $canManageGroupTopic
        || auth()->user()->canModerateForum('edit_topics', $topicCategoryId)
    );
    $canDeleteTopic = auth()->check() && (
        auth()->id() === (int) $topic->uid
        || $canManageGroupTopic
        || auth()->user()->canModerateForum('delete_topics', $topicCategoryId)
    );
    $canPinTopic = auth()->check() && ($canManageGroupTopic || auth()->user()->canModerateForum('pin_topics', $topicCategoryId));
    $canLockTopic = auth()->check() && ($canManageGroupTopic || auth()->user()->canModerateForum('lock_topics', $topicCategoryId));
    $canCommentWhenLocked = auth()->check() && (
        auth()->id() === (int) $topic->uid
        || $canManageGroupTopic
        || auth()->user()->canModerateForum('lock_topics', $topicCategoryId)
    );
@endphp

<div class="section-header" style="margin-top: 12px;">
    @if($group)
        @include('theme::partials.groups.badge', ['groupBadge' => $group])
    @endif
    @if($topic->is_pinned)
        <span class="badge bg-warning text-dark">{{ __('messages.pinned') }}</span>
    @endif
    @if($topic->is_locked)
        <span class="badge bg-secondary">{{ __('messages.locked') }}</span>
    @endif
</div>

<div class="grid grid post{{ $status->id }}">
    <div class="forum-content">
        <div class="forum-post-header">
            <p class="forum-post-header-title">{{ __('messages.author') }}</p>
            <p class="forum-post-header-title">{{ __('messages.Posts') }}</p>
        </div>
        <div class="forum-post-list">
            <div class="forum-post">
                <!-- FORUM POST META -->
                <div class="forum-post-meta">
                    <p class="forum-post-timestamp">{{ date("Y-m-d H:i:s", $status->date) }}</p>
                    <div class="forum-post-actions">
                        <p class="forum-post-action">
                            <!-- WIDGET BOX SETTINGS -->
                            <div class="widget-box-settings">
                                <div class="post-settings-wrap" style="position: relative;">
                                    <div class="post-settings widget-box-post-settings-dropdown-trigger">
                                        <svg class="post-settings-icon icon-more-dots"><use xlink:href="#svg-more-dots"></use></svg>
                                    </div>
                                    <div class="simple-dropdown widget-box-post-settings-dropdown" style="position: absolute; z-index: 9999; top: 30px; right: 9px; opacity: 0; visibility: hidden; transform: translate(0px, -20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                                        @if($canEditTopic)
                                            <a class="simple-dropdown-link" href="{{ route('forum.edit', $topic->id) }}"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{{ __('messages.edit') }}</a>
                                        @endif
                                        @if($canDeleteTopic)
                                            <p class="simple-dropdown-link" onclick="deletePost({{ $topic->id }}, 2, '#post_form{{ $topic->id }}')">
                <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}
            </p>
                                        @endif
                                        @include('theme::partials.activity.promotion_link', ['activity' => $status])
                                        @if($canPinTopic)
                                            <form method="POST" action="{{ route('forum.pin', $topic->id) }}">
                                                @csrf
                                                <button type="submit" class="simple-dropdown-link" style="width:100%;text-align:left;border:0;background:transparent;">
                                                    <i class="fa fa-thumb-tack" aria-hidden="true"></i>&nbsp;{{ $topic->is_pinned ? __('messages.unpin_topic') : __('messages.pin_topic') }}
                                                </button>
                                            </form>
                                        @endif
                                        @if($canLockTopic)
                                            <form method="POST" action="{{ route('forum.lock', $topic->id) }}">
                                                @csrf
                                                <button type="submit" class="simple-dropdown-link" style="width:100%;text-align:left;border:0;background:transparent;">
                                                    <i class="fa {{ $topic->is_locked ? 'fa-unlock' : 'fa-lock' }}" aria-hidden="true"></i>&nbsp;{{ $topic->is_locked ? __('messages.unlock_topic') : __('messages.lock_topic') }}
                                                </button>
                                            </form>
                                        @endif
                                        <p class="simple-dropdown-link post_report{{ $topic->id }}" onclick="reportPost({{ $topic->id }}, 2)"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}</p>
                                        <p class="simple-dropdown-link author_report{{ $topic->id }}" onclick="reportUser({{ $topic->uid }})"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }} {{ __('messages.author') }}</p>
                                    </div>
                                </div>
                            </div>
                            <!-- /WIDGET BOX SETTINGS -->
                        </p>
                    </div>
                </div>
                <!-- /FORUM POST META -->

                <!-- FORUM POST CONTENT -->
                <div class="forum-post-content">
                    @if($topic->user)
                    <div class="forum-post-user">
                        <a class="user-avatar no-outline {{ $topic->user->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.short', $topic->user->publicRouteIdentifier()) }}">
                            <div class="user-avatar-content">
                                <div class="hexagon-image-68-74" data-src="{{ $topic->user->avatarUrl() }}" style="width: 68px; height: 74px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="68" height="74"></canvas></div>
                            </div>
                            <div class="user-avatar-progress-border">
                                <div class="hexagon-border-84-92" data-line-color="{{ $topic->user->profileBadgeColor() }}" style="width: 84px; height: 92px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="84" height="92"></canvas></div>
                            </div>
                            @if($topic->user->hasVerifiedBadge())
                            <div class="user-avatar-badge">
                                <div class="user-avatar-badge-border">
                                    <div class="hexagon-28-32" style="width: 22px; height: 24px; position: relative;"></div>
                                </div>
                                <div class="user-avatar-badge-content">
                                    <div class="hexagon-dark-22-24" style="width: 16px; height: 18px; position: relative;"></div>
                                </div>
                                <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check" ></i></p>
                            </div>
                            @endif
                        </a>
                        
                                <p class="forum-post-user-title"><a href="{{ route('profile.short', $topic->user->publicRouteIdentifier()) }}">{{ $topic->user->username }}</a></p>
                        @if($showForumRoleBadges)
                            <p class="forum-post-user-text">{{ $topic->user->forumRoleLabel($topicCategoryId) }}</p>
                        @endif
                                <p class="forum-post-user-text"><a href="{{ route('profile.short', $topic->user->publicRouteIdentifier()) }}">@ {{ $topic->user->username }}</a></p>
                    </div>
                    @else
                    <div class="forum-post-user">
                        <div class="user-avatar no-outline offline">
                            <div class="user-avatar-content">
                                <div class="hexagon-image-68-74" data-src="{{ asset('upload/_avatar.png') }}" style="width: 68px; height: 74px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="68" height="74"></canvas></div>
                            </div>
                            <div class="user-avatar-progress-border">
                                <div class="hexagon-border-84-92" style="width: 84px; height: 92px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="84" height="92"></canvas></div>
                            </div>
                        </div>
                        <p class="forum-post-user-title"><span class="bold">{{ __('messages.deleted_user') ?? __('Deleted User') }}</span></p>
                    </div>
                    @endif

                    <div class="forum-post-info">
                        <p class="forum-post-paragraph">
                            @php
                                $content = $topic->txt;
                                // Basic parsing for hashtags
                                $content = preg_replace('/#(\w+)/', '<a href="'.url('/tag/$1').'">#$1</a>', $content);
                                // Allow safe HTML tags (strip unsafe ones)
                                $content = strip_tags($content, '<p><a><b><br><li><ul><font><span><pre><u><s><img><iframe>');
                            @endphp
                            {!! nl2br($content) !!}
                            
                            @if($topic->imageOption)
                                <br><img src="{{ asset($topic->imageOption->o_valuer) }}" style="margin-top: 24px; width: 75%; height: auto; border-radius: 12px;">
                            @endif
                        </p>

                        @if($topic->attachments->isNotEmpty())
                            <div class="widget-box" style="margin-top: 12px; margin-bottom: 0;">
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
                    </div>
                </div>
                <!-- /FORUM POST CONTENT -->
            </div>
        </div>
    </div>
</div>

<div class="post-options post{{ $status->id }}">
    @auth
    <div class="post-option-wrap" style="position: relative;">
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
            <p class="post-option-text reaction_txt{{ $status->id }}" style="{{ $myReaction ? 'color: #1bc8db;' : '' }}">
                &nbsp;{{ $myReaction ? ucfirst($reactionType) : __('messages.react') }}
            </p>
        </div>
        
        <div class="reaction-options reaction-options-dropdown" style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; display: none;">
            @foreach(['like', 'love', 'dislike', 'happy', 'funny', 'wow', 'angry', 'sad'] as $reaction)
            <div class="reaction-option text-tooltip-tft reaction_2_{{ $topic->id }}" data-title="{{ $reaction }}" onclick="postReaction({{ $topic->id }}, '{{ $reaction }}')">
                <img class="reaction-option-image" src="{{ theme_asset('img/reaction/'.$reaction.'.png') }}" alt="reaction-{{ $reaction }}">
            </div>
            @endforeach
        </div>
    </div>
    
    @if(!$topic->is_locked || $canCommentWhenLocked)
        <div class="post-option sh_comment_t{{ $status->id }}" onclick="focusComment({{ $topic->id }})">
            <svg class="post-option-icon icon-comment"><use xlink:href="#svg-comment"></use></svg>
            <p class="post-option-text">{{ __('messages.comment') }}</p>
        </div>
    @endif
    @endauth
    
    <div class="post-option-wrap" style="position: relative;">
        <div class="post-option reaction-options-dropdown-trigger" onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display === 'none' ? 'flex' : 'none'">
            <svg class="post-option-icon icon-share"><use xlink:href="#svg-share"></use></svg>
            <p class="post-option-text">{{ __('messages.share') }}</p>
        </div>
        <div class="reaction-options reaction-options-dropdown" style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; display: none;">
             @foreach(['facebook', 'twitter', 'linkedin', 'telegram'] as $social)
                <div class="reaction-option text-tooltip-tft" data-title="{{ $social }}" style="position: relative;">
                    <a href="javascript:void(0);" onclick="sharePost('{{ $social }}', '{{ route('forum.topic', $topic->id) }}', '{{ $topic->name }}')">
                        <img class="reaction-option-image" src="{{ theme_asset('img/icons/'.$social.'-icon.png') }}">
                    </a>
                </div>
             @endforeach
        </div>
    </div>
</div>

<div class="post-comment-list post-comment-list-{{ $topic->id }} comment_2_{{ $topic->id }} post{{ $status->id }}">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initHexagons();
    });

    function initHexagons() {
        if (typeof app !== 'undefined' && app.plugins && app.plugins.createHexagon) {
            app.plugins.createHexagon({
                container: '.hexagon-image-30-32',
                width: 30,
                height: 32,
                roundedCorners: true,
                clip: true
            });
            app.plugins.createHexagon({
                container: '.hexagon-border-40-44',
                width: 40,
                height: 44,
                lineWidth: 3,
                roundedCorners: true,
                lineColor: '#e7e8ee'
            });
             app.plugins.createHexagon({
                container: '.hexagon-22-24',
                width: 22,
                height: 24,
                roundedCorners: true,
                fill: true
            });
            app.plugins.createHexagon({
                container: '.hexagon-dark-16-18',
                width: 16,
                height: 18,
                roundedCorners: true,
                fill: true,
                lineColor: '#4e4ac8' // Approximation
            });
        }
    }

    function postReaction(id, reaction) {
        fetch('{{ route('reaction.toggle') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id: id, type: 'forum', reaction: reaction })
        })
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                document.getElementById('reaction_image' + {{ $status->id }}).innerHTML = data.html;
                let textEl = document.querySelector('.reaction_txt' + {{ $status->id }});
                if (textEl) {
                    if (data.action === 'added' || data.action === 'updated') {
                        textEl.style.color = '#1bc8db';
                        textEl.innerHTML = '&nbsp;' + reaction.charAt(0).toUpperCase() + reaction.slice(1);
                    } else {
                        textEl.style.color = '';
                        textEl.innerHTML = '&nbsp;{{ __('messages.react') }}';
                    }
                }
            }
        });
    }

     /* deletePost is handled globally in master.blade.php */

    function reportPost(id, type) {
        let reason = prompt('{{ __('messages.report_reason') }}');
        if(reason) {
            fetch('{{ route('forum.report') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ tp_id: id, s_type: type, txt: reason })
            })
            .then(response => response.json())
            .then(data => {
                alert('{{ __('messages.report_sent') }}');
            });
        }
    }
    
    function toggleReactionDropdown(element) {
        let dropdown = element.nextElementSibling;
        dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'flex' : 'none';
        dropdown.style.opacity = dropdown.style.display === 'flex' ? '1' : '0';
        dropdown.style.visibility = dropdown.style.display === 'flex' ? 'visible' : 'hidden';
    }
</script>
</div>
@endsection
