@php
    $status = $activity;
    $order = $status->related_content;
    $activityUser = $status->user;
    $activityUserProfileUrl = $activityUser ? route('profile.show', $activityUser->username) : '#';
    $activityUserName = $activityUser?->username ?? __('messages.unknown_user');
    $activityUserAvatar = $activityUser ? $activityUser->avatarUrl() : asset('upload/_avatar.png');
    $activityUserPresence = $activityUser?->isOnline() ? 'online' : 'offline';
    $activityUserIsAdmin = $activityUser?->isAdmin() ?? false;
    $orderUrl = $order ? route('orders.show', $order) : '#';
    $offersCount = $order?->offers_count ?? $status->comments_count ?? 0;
    $reportKey = 'orderfeed' . $status->id;
    $notifyKey = 'ordernotif' . $status->id;
    $canDeleteOrder = $order
        && auth()->check()
        && ((int) $order->uid === (int) auth()->id() || auth()->user()->isAdmin());
    $canEditOrder = $order
        && auth()->check()
        && (int) auth()->id() === (int) $order->uid
        && !$order->isManagedWorkflow()
        && (string) $order->workflow_status !== \App\Models\OrderRequest::WORKFLOW_COMPLETED;
    $canReportOrder = auth()->check() && !$canDeleteOrder;
    $canReportAuthor = $order && auth()->check() && (int) auth()->id() !== (int) $order->uid;
@endphp

@if($order)
    <div class="widget-box no-padding activity-post-card post{{ $status->id }}">
        <div class="widget-box-settings">
            <div class="post-settings-wrap" style="position: relative;">
                <div class="post-settings widget-box-post-settings-dropdown-trigger">
                    <svg class="post-settings-icon icon-more-dots">
                        <use xlink:href="#svg-more-dots"></use>
                    </svg>
                </div>

                <div class="simple-dropdown widget-box-post-settings-dropdown" style="position: absolute; z-index: 9999; top: 30px; right: 9px; opacity: 0; visibility: hidden; transform: translate(0px, -20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                    @if($canEditOrder)
                        <a class="simple-dropdown-link" href="{{ route('orders.edit', $order) }}">
                            <i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{{ __('messages.edit') }}
                        </a>
                    @endif

                    @if($canDeleteOrder)
                        <p class="simple-dropdown-link post_delete{{ $status->id }}" onclick="deletePost({{ $order->id }}, 6, '.post{{ $status->id }}')">
                            <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}
                        </p>
                    @endif

                    @auth
                        @include('theme::partials.activity.promotion_link', ['activity' => $activity])

                        @if($canReportOrder)
                            <p class="simple-dropdown-link" onclick="reportPost({{ $order->id }}, 6, '{{ $reportKey }}')">
                                <i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}
                            </p>
                        @endif

                        @if($canReportAuthor)
                            <p class="simple-dropdown-link" onclick="reportUser({{ $order->uid }}, '{{ $reportKey }}')">
                                <i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_author') }}
                            </p>
                        @endif
                    @endauth

                    <a class="simple-dropdown-link" href="{{ $orderUrl }}">
                        <i class="fa fa-briefcase" aria-hidden="true"></i>&nbsp;{{ __('messages.view_details') }}
                    </a>

                    <p class="simple-dropdown-link copy_link" onclick="navigator.clipboard.writeText('{{ $orderUrl }}'); var notif = document.getElementById('{{ $notifyKey }}'); if (notif) { notif.innerHTML = '<div class=\'alert alert-success\' role=\'alert\'>{{ __('messages.link_copied') }}</div>'; notif.style.display = 'block'; setTimeout(function() { notif.style.display = 'none'; }, 5000); }">
                        <i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}
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
                                <div class="hexagon-border-40-44" data-line-color="{{ $activityUser ? $activityUser->profileBadgeColor() : '' }}" style="width: 40px; height: 44px; position: relative;">
                                    <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                                </div>
                            </div>

                            @if($activityUserIsAdmin)
                                <div class="user-avatar-badge">
                                    <div class="user-avatar-badge-border">
                                        <div class="hexagon-22-24" style="width: 22px; height: 24px; position: relative;">
                                            <canvas style="position: absolute; top: 0px; left: 0px;" width="22" height="24"></canvas>
                                        </div>
                                    </div>
                                    <div class="user-avatar-badge-content">
                                        <div class="hexagon-dark-16-18" style="width: 16px; height: 18px; position: relative;">
                                            <canvas style="position: absolute; top: 0px; left: 0px;" width="16" height="18"></canvas>
                                        </div>
                                    </div>
                                    <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                                </div>
                            @endif
                        </div>
                    </a>

                    <p class="user-status-title medium">
                        <a class="bold" href="{{ $activityUserProfileUrl }}">{{ $activityUserName }}</a>
                        <span class="status-type-label" style="background: #615dfa; color: #fff; padding: 2px 8px; border-radius: 999px; font-size: 10px; margin-inline-start: 8px;">{{ __('messages.order_request') }}</span>
                    </p>

                    <p class="user-status-text small">
                        <i class="fa fa-clock-o"></i>&nbsp;{{ __('messages.ago') }}&nbsp; {{ $status->date_formatted }}
                    </p>
                </div>

                @include('theme::partials.activity.promotion_badge', ['activity' => $activity])

                <div class="tag-sticker">
                    <svg class="tag-sticker-icon icon-orders">
                        <use xlink:href="#svg-orders-fallback"></use>
                    </svg>
                </div>

                <div class="widget-box-status-text">
                    <div style="margin-top: 16px; display: flex; flex-wrap: wrap; gap: 8px;">
                        <span style="display:inline-flex; align-items:center; padding:8px 12px; border-radius:999px; background:#eef2ff; color:#3f4a7a; font-size:12px; font-weight:800;">{{ $order->displayCategory() }}</span>
                        <span style="display:inline-flex; align-items:center; padding:8px 12px; border-radius:999px; background:#f0f7ff; color:#147fb4; font-size:12px; font-weight:800;">{{ $order->displayBudget() }}</span>
                        <span style="display:inline-flex; align-items:center; padding:8px 12px; border-radius:999px; background:#f7f8ff; color:#5b6380; font-size:12px; font-weight:800;">{{ __('messages.offers') }}: {{ $offersCount }}</span>
                        <span style="display:inline-flex; align-items:center; padding:8px 12px; border-radius:999px; background:#f7f8ff; color:#5b6380; font-size:12px; font-weight:800;">{{ $order->displayWorkflowStatus() }}</span>
                    </div>

                    <h2 style="margin-top: 16px; font-size: 20px; font-weight: 800;">
                        <a href="{{ $orderUrl }}">{{ $order->title }}</a>
                    </h2>

                    <div style="margin-top: 12px; color: #5d637c; line-height: 1.8;">
                        {{ \Illuminate\Support\Str::limit(trim(strip_tags($order->description)), 240) }}
                    </div>
                </div>

                <div id="report{{ $reportKey }}"></div>
                <div id="{{ $notifyKey }}"></div>

                <div class="content-actions" style="margin-top: 20px; border-top: 1px solid #eaeaf5; padding-top: 16px;">
                    <div class="content-action">
                        @include('theme::partials.activity.reaction-list', ['activity' => $activity])
                        <div class="meta-line">
                            <p class="meta-line-text">{{ $status->reactions_count }} {{ __('messages.reactions') }}</p>
                        </div>
                    </div>

                    <div class="content-action">
                        <div class="meta-line">
                            <p class="meta-line-link">
                                <a href="{{ $orderUrl }}">{{ $offersCount }} {{ __('messages.offers') }}</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="post-options">
            @auth
                <div class="post-option-wrap" style="position: relative;" data-activity-menu-wrap>
                    <div class="post-option" data-activity-menu-trigger data-activity-menu-type="reaction">
                        <div id="reaction-btn-{{ $order->id }}">
                            <svg class="post-option-icon icon-thumbs-up">
                                <use xlink:href="#svg-thumbs-up"></use>
                            </svg>
                            <p class="post-option-text">{{ __('messages.react') }}</p>
                        </div>
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

            <a class="post-option" href="{{ $orderUrl }}">
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
                            <a href="javascript:void(0);" onclick='sharePost("{{ $social }}", @json($orderUrl), @json($order->title))'>
                                <img class="reaction-option-image" src="{{ theme_asset('img/icons/'.$social.'-icon.png') }}" alt="{{ $social }}">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
