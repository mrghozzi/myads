<div class="widget-box no-padding post{{ $activity->id }}">
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
                        <p class="simple-dropdown-link post_edit{{ $activity->id }}" onclick="postEdit({{ $activity->tp_id }}, 1)"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{{ __('messages.edit') }}</p>
                        <p class="simple-dropdown-link post_delete{{ $activity->id }}" onclick="deletePost({{ $activity->tp_id }}, 1)"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}</p>
                    @endif
                    <p class="simple-dropdown-link post_report{{ $activity->id }}" onclick="reportPost({{ $activity->tp_id }}, 1, {{ $activity->related_content->id }})"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}</p>
                    <p class="simple-dropdown-link author_report{{ $activity->id }}" onclick="reportUser({{ $activity->uid }}, {{ $activity->related_content->id }})"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_author') }}</p>
                @endauth
                <p class="simple-dropdown-link copy_link" onclick="navigator.clipboard.writeText('{{ route('directory.show', $activity->tp_id) }}'); var notif = document.getElementById('notif{{ $activity->related_content->id }}'); notif.innerHTML = '<div class=\'alert alert-success\' role=\'alert\'>{{ __('messages.link_copied') }}</div>'; notif.style.display = 'block'; setTimeout(function() { notif.style.display = 'none'; }, 5000);"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}</p>
            </div>
        </div>
    </div>

            @php
                $detailView = $detailView ?? false;
                $shortUrl = null;
                $listing = $activity->related_content;
                // Generate hash exactly as old system: crc32(url . id)
                $hash = hash('crc32', $listing->url . $listing->id);
                $shortUrl = route('directory.redirect.short', 'site-'.$hash);
            @endphp
            <div class="widget-box-status">
                <div class="widget-box-status-content">
                    <div class="user-status">
                <a class="user-status-avatar" href="{{ $activity->user ? route('profile.show', $activity->user->username) : '#' }}">
                    <div class="user-avatar small no-outline {{ $activity->user?->isOnline() ? 'online' : 'offline' }}">
                        <div class="user-avatar-content">
                            <div class="hexagon-image-30-32" data-src="{{ $activity->user?->img ? asset($activity->user->img) : theme_asset('img/avatar.jpg') }}" style="width: 30px; height: 32px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                            </div>
                        </div>
                        <div class="user-avatar-progress-border">
                            <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;"></div>
                        </div>
                        @if($activity->user?->isAdmin())
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
                    <a class="bold" href="{{ $activity->user ? route('profile.show', $activity->user->username) : '#' }}">{{ $activity->user?->username ?? __('messages.unknown_user') }}</a>
                </p>
                <p class="user-status-text small">
                    <i class="fa fa-clock-o"></i>&nbsp;{{ __('messages.ago') }}&nbsp; {{ $activity->date_formatted }}
                </p>
            </div>

            <div class="tag-sticker">
                <svg class="tag-sticker-icon icon-public">
                    <use xlink:href="#svg-public"></use>
                </svg>
            </div>

            <p class="widget-box-status-text post_text{{ $activity->related_content->id }}">
                <div class="textpost" id="post_form{{ $activity->related_content->id }}">
                    @php
                        $txt = nl2br(e($activity->related_content->txt));
                        $txt = Str::limit($txt, 1600);
                        if (preg_match('/\p{Arabic}/u', $txt)) {
                            $txt = '<div style="text-align: right;">' . $txt . '</div>';
                        }
                        $domain = parse_url($activity->related_content->url, PHP_URL_HOST);
                    @endphp
                    @if($detailView)
                        <br/>
                        <div class="event-info">
                            <h1 class="event-title" style="text-align: center">{{ $activity->related_content->name }}</h1>
                            <br/>
                            <div class="decorated-feature-list" style="display: block;">
                                <div class="decorated-feature">
                                    <svg class="decorated-feature-icon icon-public">
                                        <use xlink:href="#svg-public"></use>
                                    </svg>
                                    <div class="decorated-feature-info">
                                        <a class="decorated-feature-title" href="{{ $shortUrl }}" target="_blank">{{ $activity->related_content->name }}</a>
                                        <p class="decorated-feature-text">{{ $domain }}</p>
                                    </div>
                                </div>
                                @if($activity->related_content->category)
                                    <div class="decorated-feature">
                                        <svg class="decorated-feature-icon icon-forum">
                                            <use xlink:href="#svg-forum"></use>
                                        </svg>
                                        <div class="decorated-feature-info">
                                            <a class="decorated-feature-title" href="{{ route('directory.category.legacy', $activity->related_content->category->id) }}">{{ $activity->related_content->category->name }}</a>
                                        </div>
                                    </div>
                                @endif
                                <div class="decorated-feature">
                                    <svg class="decorated-feature-icon icon-info">
                                        <use xlink:href="#svg-info"></use>
                                    </svg>
                                    <div class="decorated-feature-info">
                                        <p class="decorated-feature-title">{{ __('messages.desc') }}</p>
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <p class="event-text" style="text-align: center">{!! $txt !!}</p>
                        </div>
                    @else
                        {!! $txt !!}
                    @endif
                    <div id="report{{ $activity->related_content->id }}"></div>
                </div>
            </p>
            <div id="notif{{ $activity->related_content->id }}"></div>

            <a class="video-status small" href="{{ $shortUrl }}" style="background-color: #efeff9;" target="_blank">
                <figure class="video-status-image liquid">
                    <img class="video-status-image" src="{{ theme_asset('img/dir_image.png') }}">
                </figure>
                <div class="video-status-info" style="background-color: #efeff9;">
                    <p class="video-status-title"><span class="bold">{{ $activity->related_content->name }}</span></p>
                    <p class="video-status-text">{{ $domain }}</p>
                </div>
            </a>

            @if(!$detailView)
                <div class="tag-list">
                    <a class="tag-item secondary" href="{{ route('directory.show', $activity->tp_id) }}">{{ $activity->related_content->name }}</a>
                    <a class="tag-item secondary" href="{{ route('directory.category', $activity->related_content->category->id ?? 0) }}">{{ $activity->related_content->category->name ?? '' }}</a>
                </div>
            @endif

            <div class="content-actions">
                <div class="content-action">
                    @include('theme::partials.activity.reaction-list', ['activity' => $activity])
                    <div class="meta-line">
                        <p class="meta-line-text">{{ $activity->reactions_count }} {{ __('messages.reactions') }}</p>
                    </div>
                </div>
                <div class="content-action">
                    <div class="meta-line">
                        <p class="meta-line-text"><i class="fa fa-eye" aria-hidden="true"></i>&nbsp;{{ $activity->related_content->vu }}</p>
                    </div>
                </div>
                <div class="content-action">
                    <div class="meta-line">
                        <p class="meta-line-link">
                            <a href="{{ route('directory.show', $activity->tp_id) }}">{{ $activity->comments_count }} {{ __('messages.comments') }}</a>
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
                            ->where('type', 22) // Directory type
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
                        <div class="reaction-option text-tooltip-tft" data-title="{{ $reaction }}" onclick="toggleReaction({{ $activity->related_content->id }}, 'directory', '{{ $reaction }}')">
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
                 data-comment-type="directory">
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
                        <a href="javascript:void(0);" onclick="sharePost('{{ $social }}', '{{ route('directory.show', $activity->tp_id) }}', '{{ $activity->related_content->name ?? '' }}')">
                            <img class="reaction-option-image" src="{{ theme_asset('img/icons/'.$social.'-icon.png') }}">
                        </a>
                    </div>
                 @endforeach
            </div>
        </div>
    </div>
    <div class="post-comment-list post-comment-list-{{ $activity->related_content->id }}"></div>
</div>
