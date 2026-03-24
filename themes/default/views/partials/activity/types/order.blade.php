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

<div class="widget-box no-padding post{{ $activity->id }}">
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
                        <form action="{{ route('orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="simple-dropdown-link" style="background: none; border: none; width: 100%; text-align: right; color: #ff5b5b;">
                                <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}
                            </button>
                        </form>
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
                            <div class="hexagon-image-30-32" data-src="{{ $activityUserAvatar }}"></div>
                        </div>
                    </div>
                </a>
                <p class="user-status-title medium">
                    <a class="bold" href="{{ $activityUserProfileUrl }}">{{ $activityUserName }}</a>
                    <span class="status-type-label" style="background: #615dfa; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 10px; margin-inline-start: 8px;">{{ __('messages.order_request') }}</span>
                </p>
                <p class="user-status-text small">
                    <i class="fa fa-clock-o"></i>&nbsp;{{ $activity->date_formatted }}
                </p>
            </div>

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
</div>
