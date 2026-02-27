<div class="widget-box no-padding post{{ $status->id }}">
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
                @if((auth()->check() && auth()->id() == $topic->uid) || (auth()->check() && auth()->user()->isAdmin()))
                    <!-- SIMPLE DROPDOWN LINK -->
                    <a class="simple-dropdown-link" href="{{ route('forum.edit', $topic->id) }}"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{{ __('messages.edit') }}</a>
                    <!-- /SIMPLE DROPDOWN LINK -->
                    
                    <!-- SIMPLE DROPDOWN LINK -->
                    <p class="simple-dropdown-link post_delete{{ $topic->id }}" onclick="deletePost({{ $topic->id }}, 2)"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}</p>
                    <!-- /SIMPLE DROPDOWN LINK -->
                @endif
                
                <!-- SIMPLE DROPDOWN LINK -->
                <p class="simple-dropdown-link post_report{{ $topic->id }}" onclick="reportPost({{ $topic->id }}, 2)"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}</p>
                <!-- /SIMPLE DROPDOWN LINK -->

                <!-- SIMPLE DROPDOWN LINK -->
                <p class="simple-dropdown-link author_report{{ $topic->id }}" onclick="reportUser({{ $topic->uid }})"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }} {{ __('messages.author') }}</p>
                <!-- /SIMPLE DROPDOWN LINK -->

                <!-- SIMPLE DROPDOWN LINK -->
                <p class="simple-dropdown-link copy_link" onclick="navigator.clipboard.writeText('{{ route('forum.topic', $topic->id) }}'); var notif = document.getElementById('notif{{ $topic->id }}'); if(notif){ notif.innerHTML = '<div class=&quot;alert alert-success&quot; role=&quot;alert&quot;>{{ __('messages.link_copied') }}</div>'; notif.style.display = 'block'; setTimeout(function(){ notif.style.display = 'none'; }, 5000);}"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}</p>
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
                @if($topic->user)
                <!-- USER STATUS AVATAR -->
                <a class="user-status-avatar" href="{{ route('profile.short', $topic->user->id) }}">
                    <!-- USER AVATAR -->
                    <div class="user-avatar small no-outline {{ $topic->user->isOnline() ? 'online' : '' }}">
                        <!-- USER AVATAR CONTENT -->
                        <div class="user-avatar-content">
                            <!-- HEXAGON -->
                            <div class="hexagon-image-30-32" data-src="{{ $topic->user->img ? url($topic->user->img) : theme_asset('img/avatar/01.jpg') }}"></div>
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

                        @if($topic->user->ucheck == 1)
                            <!-- USER AVATAR BADGE -->
                            <div class="user-avatar-badge">
                                <!-- USER AVATAR BADGE BORDER -->
                                <div class="user-avatar-badge-border">
                                    <!-- HEXAGON -->
                                    <div class="hexagon-22-24"></div>
                                    <!-- /HEXAGON -->
                                </div>
                                <!-- /USER AVATAR BADGE BORDER -->

                                <!-- USER AVATAR BADGE CONTENT -->
                                <div class="user-avatar-badge-content">
                                    <!-- HEXAGON -->
                                    <div class="hexagon-dark-16-18"></div>
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
                    <a class="bold" href="{{ route('profile.short', $topic->user->id) }}">{{ $topic->user->username }}</a>
                </p>
                <!-- /USER STATUS TITLE -->
                @else
                <!-- DELETED USER -->
                <div class="user-status-avatar">
                     <div class="user-avatar small no-outline">
                        <div class="user-avatar-content">
                            <div class="hexagon-image-30-32" data-src="{{ theme_asset('img/avatar/01.jpg') }}"></div>
                        </div>
                     </div>
                </div>
                <p class="user-status-title medium">
                    <span class="bold">{{ __('Deleted User') }}</span>
                </p>
                @endif

                <!-- USER STATUS TEXT -->
                <p class="user-status-text small"><i class="fa fa-clock-o"></i>&nbsp;{{ $status->date ? \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans() : '' }}</p>
                <!-- /USER STATUS TEXT -->
            </div>
            <!-- /USER STATUS -->
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
                        </div>
                    </a>
                    <div id="report{{ $topic->id }}"></div>
                </div>
            </p>
            <div id="notif{{ $topic->id }}"></div>

            <!-- CONTENT ACTIONS -->
            <div class="content-actions">
                <!-- CONTENT ACTION -->
                <div class="content-action">
                    <!-- META LINE -->
                    <div class="meta-line">
                        <!-- META LINE LIST -->
                        <div class="meta-line-list reaction-item-list">
                            <!-- REACTIONS -->
                            @php
                                $likes = \App\Models\Like::where('sid', $topic->id)->where('type', 2)->with('user')->get();
                                $grouped_reactions = [];
                                foreach($likes as $like) {
                                    $reaction = \App\Models\Option::where('o_parent', $like->id)->where('o_type', 'data_reaction')->value('o_valuer') ?? 'like';
                                    if($like->user) {
                                        $grouped_reactions[$reaction][] = $like->user;
                                    }
                                }
                            @endphp

                            @if(count($grouped_reactions) > 0)
                                @foreach($grouped_reactions as $type => $users)
                                    <div class="reaction-item">
                                        <!-- REACTION IMAGE -->
                                        <img class="reaction-image reaction-item-dropdown-trigger" src="{{ theme_asset('img/reaction/'.$type.'.png') }}" alt="reaction-{{ $type }}">
                                        <!-- /REACTION IMAGE -->
                        
                                        <!-- SIMPLE DROPDOWN -->
                                        <div class="simple-dropdown padded reaction-item-dropdown">
                                            <!-- SIMPLE DROPDOWN TEXT -->
                                            <p class="simple-dropdown-text">
                                                <img class="reaction" src="{{ theme_asset('img/reaction/'.$type.'.png') }}" alt="reaction-{{ $type }}">
                                                <span class="bold">{{ ucfirst($type) }}</span>
                                            </p>
                                            <!-- /SIMPLE DROPDOWN TEXT -->
                                            
                                            @foreach($users as $user)
                                                <p class="simple-dropdown-text">{{ $user->username }}</p>
                                            @endforeach
                                        </div>
                                        <!-- /SIMPLE DROPDOWN -->
                                    </div>
                                @endforeach
                            @endif
                            <!-- /REACTIONS -->
                        </div>
                        <!-- /META LINE LIST -->

                        <!-- META LINE TEXT -->
                        <p class="meta-line-text">{{ \App\Models\Like::where('sid', $topic->id)->where('type', 2)->count() }}</p>
                        <!-- /META LINE TEXT -->
                    </div>
                    <!-- /META LINE -->

                    <!-- META LINE -->
                    <div class="meta-line">
                        <!-- META LINE LINK -->
                        <a class="meta-line-link" href="{{ route('forum.topic', $topic->id) }}">{{ \App\Models\ForumComment::where('tid', $topic->id)->count() }} {{ __('messages.comments') }}</a>
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
    <!-- /POST OPTIONS -->
</div>
