@php
    $activityUser = $activity->user;
    $order = $activity->related_content;
    $activityUserProfileUrl = $activityUser ? route('profile.show', $activityUser->username) : '#';
    $activityUserName = $activityUser?->username ?? __('messages.unknown_user');
    $activityUserAvatar = $activityUser ? $activityUser->avatarUrl() : asset('upload/_avatar.png');
    $activityUserPresence = $activityUser?->isOnline() ? 'online' : 'offline';
@endphp

@if($order)
<div class="widget-box no-padding activity-post-card post{{ $activity->id }}">
    <div class="widget-box-status">
        <div class="widget-box-status-content">
            <div class="user-status">
                <a class="user-status-avatar" href="{{ $activityUserProfileUrl }}">
                    <div class="user-avatar small no-outline {{ $activityUserPresence }}">
                        <div class="user-avatar-content">
                            <div class="hexagon-image-30-32" data-src="{{ $activityUserAvatar }}"></div>
                        </div>
                    </div>
                </a>
                <p class="user-status-title medium">
                    <a class="bold" href="{{ $activityUserProfileUrl }}">{{ $activityUserName }}</a>
                    <span class="status-type-label" style="background: #615dfa; color: #fff; padding: 2px 8px; border-radius: 999px; font-size: 10px; margin-inline-start: 8px;">{{ __('messages.order_request') }}</span>
                </p>
                <p class="user-status-text small">{{ $activity->date_formatted }}</p>
            </div>

            <div style="margin-top: 16px; display: flex; flex-wrap: wrap; gap: 8px;">
                <span style="display:inline-flex; align-items:center; padding:8px 12px; border-radius:999px; background:#eef2ff; color:#3f4a7a; font-size:12px; font-weight:800;">{{ $order->displayCategory() }}</span>
                <span style="display:inline-flex; align-items:center; padding:8px 12px; border-radius:999px; background:#f0f7ff; color:#147fb4; font-size:12px; font-weight:800;">{{ $order->displayBudget() }}</span>
                <span style="display:inline-flex; align-items:center; padding:8px 12px; border-radius:999px; background:#f7f8ff; color:#5b6380; font-size:12px; font-weight:800;">{{ __('messages.offers') }}: {{ $order->offers_count ?? $activity->comments_count }}</span>
                <span style="display:inline-flex; align-items:center; padding:8px 12px; border-radius:999px; background:#f7f8ff; color:#5b6380; font-size:12px; font-weight:800;">{{ $order->displayWorkflowStatus() }}</span>
            </div>

            <h2 style="margin-top: 16px; font-size: 20px; font-weight: 800;">
                <a href="{{ route('orders.show', $order) }}">{{ $order->title }}</a>
            </h2>

            <div style="margin-top: 12px; color: #5d637c; line-height: 1.8;">
                {{ \Illuminate\Support\Str::limit(trim(strip_tags($order->description)), 240) }}
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
                        <p class="meta-line-link"><a href="{{ route('orders.show', $order) }}">{{ $activity->comments_count }} {{ __('messages.offers') }}</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="post-options">
        @auth
            <div class="post-option-wrap" style="position: relative;" data-activity-menu-wrap>
                <div class="post-option" data-activity-menu-trigger data-activity-menu-type="reaction">
                    <svg class="post-option-icon icon-thumbs-up">
                        <use xlink:href="#svg-thumbs-up"></use>
                    </svg>
                    <p class="post-option-text">{{ __('messages.react') }}</p>
                </div>
                <div class="reaction-options reaction-options-dropdown" data-activity-menu-panel style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                    @foreach(['like', 'love', 'dislike', 'happy', 'funny', 'wow', 'angry', 'sad'] as $reaction)
                        <div class="reaction-option text-tooltip-tft" data-title="{{ $reaction }}" onclick="toggleReaction({{ $order->id }}, 'order', '{{ $reaction }}')">
                            <img class="reaction-option-image" src="{{ theme_asset('img/reaction/'.$reaction.'.png') }}" alt="reaction-{{ $reaction }}">
                        </div>
                    @endforeach
                </div>
            </div>
        @endauth

        <a class="post-option" href="{{ route('orders.show', $order) }}">
            <svg class="post-option-icon icon-comment">
                <use xlink:href="#svg-comment"></use>
            </svg>
            <p class="post-option-text">{{ __('messages.view_details') }}</p>
        </a>

        <div class="post-option-wrap" style="position: relative;" data-activity-menu-wrap>
            <div class="post-option" data-activity-menu-trigger data-activity-menu-type="share">
                <svg class="post-option-icon icon-share">
                    <use xlink:href="#svg-share"></use>
                </svg>
                <p class="post-option-text">{{ __('messages.share') }}</p>
            </div>
            <div class="reaction-options reaction-options-dropdown" data-activity-menu-panel style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                @foreach(['facebook', 'twitter', 'linkedin', 'telegram'] as $social)
                    <div class="reaction-option text-tooltip-tft" data-title="{{ $social }}">
                        <a href="javascript:void(0);" onclick="sharePost('{{ $social }}', '{{ route('orders.show', $order) }}', '{{ $order->title }}')">
                            <img class="reaction-option-image" src="{{ theme_asset('img/icons/'.$social.'-icon.png') }}" alt="{{ $social }}">
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
