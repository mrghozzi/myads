@php
    $viewer = auth()->user();
    $topicCategoryId = (int) $topic->cat;
    $canEditTopic = auth()->check() && (
        (int) auth()->id() === (int) $topic->uid
        || $viewer->canModerateForum('edit_topics', $topicCategoryId)
    );
    $canDeleteTopic = auth()->check() && (
        (int) auth()->id() === (int) $topic->uid
        || $viewer->canModerateForum('delete_topics', $topicCategoryId)
    );
    $canPinTopic = auth()->check() && $viewer->canModerateForum('pin_topics', $topicCategoryId);
    $canLockTopic = auth()->check() && $viewer->canModerateForum('lock_topics', $topicCategoryId);
    $showForumRoleBadges = (int) \App\Support\ForumSettings::get('show_role_badges', 1) === 1;
@endphp

<div class="widget-box no-padding post{{ $status->id }}">
    <div class="widget-box-settings">
        <div class="post-settings-wrap" style="position: relative;">
            <div class="post-settings widget-box-post-settings-dropdown-trigger">
                <svg class="post-settings-icon icon-more-dots">
                    <use xlink:href="#svg-more-dots"></use>
                </svg>
            </div>

            <div class="simple-dropdown widget-box-post-settings-dropdown" style="position: absolute; z-index: 9999; top: 30px; right: 9px; opacity: 0; visibility: hidden; transform: translate(0px, -20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                @if($canEditTopic)
                    <a class="simple-dropdown-link" href="{{ route('forum.edit', $topic->id) }}">
                        <i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{{ __('messages.edit') }}
                    </a>
                @endif

                @if($canDeleteTopic)
                    <p class="simple-dropdown-link post_delete{{ $topic->id }}" onclick="deletePost({{ $topic->id }}, 2)">
                        <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}
                    </p>
                @endif

                @if($canPinTopic)
                    <form method="POST" action="{{ route('forum.pin', $topic->id) }}">
                        @csrf
                        <button type="submit" class="simple-dropdown-link" style="width:100%; text-align:left; border:0; background:transparent;">
                            <i class="fa fa-thumb-tack" aria-hidden="true"></i>&nbsp;
                            {{ $topic->is_pinned ? __('messages.unpin_topic') : __('messages.pin_topic') }}
                        </button>
                    </form>
                @endif

                @if($canLockTopic)
                    <form method="POST" action="{{ route('forum.lock', $topic->id) }}">
                        @csrf
                        <button type="submit" class="simple-dropdown-link" style="width:100%; text-align:left; border:0; background:transparent;">
                            <i class="fa {{ $topic->is_locked ? 'fa-unlock' : 'fa-lock' }}" aria-hidden="true"></i>&nbsp;
                            {{ $topic->is_locked ? __('messages.unlock_topic') : __('messages.lock_topic') }}
                        </button>
                    </form>
                @endif

                <p class="simple-dropdown-link post_report{{ $topic->id }}" onclick="reportPost({{ $topic->id }}, 2)">
                    <i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}
                </p>

                <p class="simple-dropdown-link author_report{{ $topic->id }}" onclick="reportUser({{ $topic->uid }})">
                    <i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }} {{ __('messages.author') }}
                </p>

                <p class="simple-dropdown-link copy_link" onclick="navigator.clipboard.writeText('{{ route('forum.topic', $topic->id) }}'); var notif = document.getElementById('notif{{ $topic->id }}'); if(notif){ notif.innerHTML = '<div class=&quot;alert alert-success&quot; role=&quot;alert&quot;>{{ __('messages.link_copied') }}</div>'; notif.style.display = 'block'; setTimeout(function(){ notif.style.display = 'none'; }, 5000);}">
                    <i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}
                </p>
            </div>
        </div>
    </div>

    <div class="widget-box-status">
        <div class="widget-box-status-content">
            <div class="user-status">
                @if($topic->user)
                    <a class="user-status-avatar" href="{{ route('profile.short', $topic->user->id) }}">
                        <div class="user-avatar small no-outline {{ $topic->user->isOnline() ? 'online' : '' }}">
                            <div class="user-avatar-content">
                                <div class="hexagon-image-30-32" data-src="{{ $topic->user->img ? url($topic->user->img) : theme_asset('img/avatar/01.jpg') }}"></div>
                            </div>
                            <div class="user-avatar-progress-border">
                                <div class="hexagon-border-40-44"></div>
                            </div>

                            @if($topic->user->ucheck == 1)
                                <div class="user-avatar-badge">
                                    <div class="user-avatar-badge-border">
                                        <div class="hexagon-22-24"></div>
                                    </div>
                                    <div class="user-avatar-badge-content">
                                        <div class="hexagon-dark-16-18"></div>
                                    </div>
                                    <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                                </div>
                            @endif
                        </div>
                    </a>

                    <p class="user-status-title medium">
                        <a class="bold" href="{{ route('profile.short', $topic->user->id) }}">{{ $topic->user->username }}</a>
                    </p>
                    @if($showForumRoleBadges)
                        <p class="user-status-text small" style="margin-top: -8px;">
                            {{ $topic->user->forumRoleLabel($topicCategoryId) }}
                        </p>
                    @endif
                @else
                    <div class="user-status-avatar">
                        <div class="user-avatar small no-outline">
                            <div class="user-avatar-content">
                                <div class="hexagon-image-30-32" data-src="{{ theme_asset('img/avatar/01.jpg') }}"></div>
                            </div>
                        </div>
                    </div>
                    <p class="user-status-title medium">
                        <span class="bold">{{ __('messages.deleted_user') }}</span>
                    </p>
                @endif

                <p class="user-status-text small">
                    <i class="fa fa-clock-o"></i>&nbsp;{{ $status->date ? \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans() : '' }}
                </p>
            </div>

            <div class="tag-sticker">
                <svg class="tag-sticker-icon icon-forums">
                    <use xlink:href="#svg-forums"></use>
                </svg>
            </div>

            <p class="widget-box-status-text post_text{{ $topic->id }}">
                <div class="textpost" id="post_form{{ $topic->id }}">
                    <a class="video-status" href="{{ route('forum.topic', $topic->id) }}">
                        <div class="video-status-info" style="background-image: url({{ theme_asset('img/background_topic.jpg') }});">
                            <p class="video-status-title">
                                <span class="bold">{{ $topic->name }}</span>
                            </p>
                            <p class="video-status-title">
                                <span class="highlighted">
                                    <i class="fa {{ optional($topic->category)->icons }}" aria-hidden="true"></i>
                                    {{ optional($topic->category)->name }}
                                </span>
                            </p>
                            <p class="video-status-title" style="margin-top: 8px;">
                                @if($topic->is_pinned)
                                    <span class="badge bg-warning text-dark">{{ __('messages.pinned') }}</span>
                                @endif
                                @if($topic->is_locked)
                                    <span class="badge bg-secondary">{{ __('messages.locked') }}</span>
                                @endif
                            </p>
                        </div>
                    </a>
                    <div id="report{{ $topic->id }}"></div>
                </div>
            </p>

            <div id="notif{{ $topic->id }}"></div>

            <div class="content-actions">
                <div class="content-action">
                    <div class="meta-line">
                        <div class="meta-line-list reaction-item-list">
                            @php
                                $likes = \App\Models\Like::where('sid', $topic->id)->where('type', 2)->with('user')->get();
                                $grouped_reactions = [];
                                foreach ($likes as $like) {
                                    $reaction = \App\Models\Option::where('o_parent', $like->id)->where('o_type', 'data_reaction')->value('o_valuer') ?? 'like';
                                    if ($like->user) {
                                        $grouped_reactions[$reaction][] = $like->user;
                                    }
                                }
                            @endphp

                            @if(count($grouped_reactions) > 0)
                                @foreach($grouped_reactions as $type => $users)
                                    <div class="reaction-item">
                                        <img class="reaction-image reaction-item-dropdown-trigger" src="{{ theme_asset('img/reaction/'.$type.'.png') }}" alt="reaction-{{ $type }}">
                                        <div class="simple-dropdown padded reaction-item-dropdown">
                                            <p class="simple-dropdown-text">
                                                <img class="reaction" src="{{ theme_asset('img/reaction/'.$type.'.png') }}" alt="reaction-{{ $type }}">
                                                <span class="bold">{{ ucfirst($type) }}</span>
                                            </p>
                                            @foreach($users as $user)
                                                <p class="simple-dropdown-text">{{ $user->username }}</p>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <p class="meta-line-text">{{ \App\Models\Like::where('sid', $topic->id)->where('type', 2)->count() }}</p>
                    </div>

                    <div class="meta-line">
                        <a class="meta-line-link" href="{{ route('forum.topic', $topic->id) }}">{{ \App\Models\ForumComment::where('tid', $topic->id)->count() }} {{ __('messages.comments') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="post-options">
        @auth
            <div class="post-option-wrap" style="position: relative;">
                <div class="post-option reaction-options-dropdown-trigger">
                    <div id="reaction-btn-{{ $topic->id }}">
                        @php
                            $myReaction = \App\Models\Like::where('uid', auth()->id())
                                ->where('sid', $topic->id)
                                ->where('type', 2)
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
                <div class="reaction-options reaction-options-dropdown" style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                    @foreach(['like', 'love', 'dislike', 'happy', 'funny', 'wow', 'angry', 'sad'] as $reaction)
                        <div class="reaction-option text-tooltip-tft" data-title="{{ $reaction }}" onclick="toggleReaction({{ $topic->id }}, 'forum', '{{ $reaction }}')">
                            <img class="reaction-option-image" src="{{ theme_asset('img/reaction/'.$reaction.'.png') }}" alt="reaction-{{ $reaction }}">
                        </div>
                    @endforeach
                </div>
            </div>
        @endauth

        @auth
            <div class="post-option sh_comment_t{{ $status->id }}" onclick="loadComments({{ $topic->id }}, 'forum').then(function(){ focusComment({{ $topic->id }}); });">
                <svg class="post-option-icon icon-comment">
                    <use xlink:href="#svg-comment"></use>
                </svg>
                <p class="post-option-text">{{ __('messages.comment') }}</p>
            </div>
        @endauth

        <div class="post-option-wrap" style="position: relative;">
            <div class="post-option reaction-options-dropdown-trigger">
                <svg class="post-option-icon icon-share">
                    <use xlink:href="#svg-share"></use>
                </svg>
                <p class="post-option-text">{{ __('messages.share') }}</p>
            </div>
            <div class="reaction-options reaction-options-dropdown" style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
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
    <div class="post-comment-list post-comment-list-{{ $topic->id }}"></div>
</div>
