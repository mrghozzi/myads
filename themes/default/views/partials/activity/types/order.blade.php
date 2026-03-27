@php
    $activityUser = $activity->user;
    $activityUserProfileUrl = $activityUser ? route('profile.show', $activityUser->username) : '#';
    $activityUserName = $activityUser?->username ?? __('messages.unknown_user');
    $activityUserAvatar = $activityUser?->img ? asset($activityUser->img) : theme_asset('img/avatar/default.png');
    $activityUserPresence = $activityUser?->isOnline() ? 'online' : 'offline';
    $activityUserIsAdmin = $activityUser?->isAdmin() ?? false;
    
    $order = $activity->related_content;
    $formattedDescription = \App\Support\ContentFormatter::format(\Illuminate\Support\Str::limit($order->description ?? '', 500));
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
                <p class="simple-dropdown-link copy_link" onclick="navigator.clipboard.writeText('{{ route('orders.show', $order->id) }}'); alert('{{ __('messages.link_copied') }}');">
                    <i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}
                </p>
                @auth
                    @if(auth()->id() == $activity->uid || auth()->user()->isAdmin())
                        @include('theme::partials.activity.promotion_link', ['activity' => $activity])
                        <p class="simple-dropdown-link" onclick="deletePost({{ $order->id }}, 6, '.post{{ $activity->id }}')" style="color: #ff5b5b; cursor: pointer;">
                            <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}
                        </p>
                    @endif
                @endauth
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
                    @if($order->statu == 0)
                        <span class="status-type-label" style="background: #ff5b5b; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 10px; margin-inline-start: 8px;">{{ __('messages.closed') }}</span>
                    @else
                        <span class="status-type-label" style="background: #615dfa; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 10px; margin-inline-start: 8px;">{{ __('messages.order_request') }}</span>
                    @endif
                </p>
                <p class="user-status-text small">
                    <i class="fa fa-clock-o"></i>&nbsp;{{ $activity->date_formatted }}
                </p>
            </div>

            @include('theme::partials.activity.promotion_badge', ['activity' => $activity])

            <div class="order-card-content" style="margin-top: 16px;">
                <h2 class="order-card-title" style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">
                    <a href="{{ route('orders.show', $order->id) }}">{{ $order->title }}</a>
                </h2>
                
                <div class="order-meta-info" style="display: flex; gap: 16px; margin-bottom: 12px; font-size: 13px;">
                    @if($order->budget)
                        <div class="order-meta-item">
                            <span style="color: #8f919d;">{{ __('messages.budget') }}:</span>
                            <span style="color: #23d2e2; font-weight: 700;">{{ $order->budget }}</span>
                        </div>
                    @endif
                    @if($order->category)
                        <div class="order-meta-item">
                            <span style="color: #8f919d;">{{ __('messages.category') }}:</span>
                            <span style="font-weight: 600;">{{ $order->category }}</span>
                        </div>
                    @endif
                    @if($order->avg_rating > 0)
                        <div class="order-meta-item">
                            <span style="color: #ffc107;"><i class="fa fa-star"></i> {{ number_format($order->avg_rating, 1) }}</span>
                        </div>
                    @endif
                </div>

                <div class="order-description" style="color: #3e3f5e; line-height: 1.6; margin-bottom: 16px;">
                    {!! $formattedDescription !!}
                </div>

                <div class="order-actions" style="display: flex; gap: 12px;">
                    <a href="{{ route('orders.show', $order->id) }}" class="button secondary small">{{ __('messages.view_details') }}</a>
                    @auth
                        @if(auth()->id() != $order->uid)
                            <a href="{{ url('/messages/' . $order->uid) }}" class="button primary small">
                                <i class="fa fa-envelope"></i>&nbsp;{{ __('messages.contact_client') }}
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="content-actions" style="margin-top: 20px; border-top: 1px solid #eaeaf5; padding-top: 16px;">
                <div class="content-action">
                    @include('theme::partials.activity.reaction-list', ['activity' => $activity])
                    <div class="meta-line">
                        <p class="meta-line-text">{{ $activity->reactions_count }} {{ __('messages.reactions') }}</p>
                    </div>
                </div>
                <div class="content-action">
                    <div class="meta-line">
                        <p class="meta-line-link">
                            <a href="{{ route('orders.show', $order->id) }}">{{ $activity->comments_count }} {{ __('messages.comments') }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            ->where('type', 6) // Order type
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
                        <div class="reaction-option text-tooltip-tft" data-title="{{ $reaction }}" onclick="toggleReaction({{ $activity->related_content->id }}, 'order', '{{ $reaction }}')">
                            <img class="reaction-option-image" src="{{ theme_asset('img/reaction/'.$reaction.'.png') }}" alt="reaction-{{ $reaction }}">
                        </div>
                    @endforeach
                </div>
            </div>
        @endauth

        @auth
            <div class="post-option"
                 data-activity-comment
                 data-comment-id="{{ $activity->related_content->id }}"
                 data-comment-type="order">
                <svg class="post-option-icon icon-comment">
                    <use xlink:href="#svg-comment"></use>
                </svg>
                <p class="post-option-text">{{ __('messages.comment') }}</p>
            </div>
        @endauth

        <!-- POST OPTION -->
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
                        <a href="javascript:void(0);" onclick="sharePost('{{ $social }}', '{{ route('orders.show', $order->id) }}', '{{ $order->title ?? '' }}')">
                            <img class="reaction-option-image" src="{{ theme_asset('img/icons/'.$social.'-icon.png') }}">
                        </a>
                    </div>
                 @endforeach
            </div>
        </div>
    </div>
    <div class="post-comment-list post-comment-list-{{ $activity->related_content->id }}"></div>
</div>
