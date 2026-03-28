@php
    $status = $activity;
    $statusUser = $status->user;
    $statusUserProfileUrl = $statusUser ? route('profile.show', $statusUser->username) : '#';
    $statusUserName = $statusUser?->username ?? __('messages.unknown_user');
    $statusUserAvatar = $statusUser ? $statusUser->avatarUrl() : asset('upload/_avatar.png');
    $statusUserPresence = $statusUser?->isOnline() ? 'online' : 'offline';
    $statusUserIsAdmin = $statusUser?->isAdmin() ?? false;
    $product = $activity->related_content;
    $description = \App\Support\ContentFormatter::format(\Illuminate\Support\Str::limit($product->o_valuer ?? '', 480));
    $productImage = $product->product_image ?? theme_asset('img/error_plug.png');
    $repostExcerpt = \Illuminate\Support\Str::limit(strip_tags($product->name ?? ($product->o_valuer ?? '')), 80);
    $repostAuthorName = addslashes($statusUserName);
@endphp
<div class="widget-box no-padding activity-post-card post{{ $status->id }}">
    <div class="widget-box-settings">
        <div class="post-settings-wrap" style="position: relative;">
            <div class="post-settings widget-box-post-settings-dropdown-trigger">
                <svg class="post-settings-icon icon-more-dots">
                    <use xlink:href="#svg-more-dots"></use>
                </svg>
            </div>
            <div class="simple-dropdown widget-box-post-settings-dropdown" style="position: absolute; z-index: 9999; top: 30px; right: 9px; opacity: 0; visibility: hidden; transform: translate(0px, -20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                @auth
                    @if(auth()->id() == $status->uid || auth()->user()->isAdmin())
                        <p class="simple-dropdown-link post_edit{{ $status->id }}" onclick="postEdit({{ $status->tp_id }}, 7867, '{{ $product->name }}')"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{{ __('messages.edit') }}</p>
                        <p class="simple-dropdown-link post_delete{{ $status->id }}" onclick="deletePost({{ $status->tp_id }}, 7867, '.post{{ $status->id }}')"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}</p>
                    @endif
                    @include('theme::partials.activity.promotion_link', ['activity' => $activity])
                    <p class="simple-dropdown-link post_report{{ $status->id }}" onclick="reportPost({{ $status->tp_id }}, 7867, {{ $product->id }})"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}</p>
                    <p class="simple-dropdown-link author_report{{ $status->id }}" onclick="reportUser({{ $status->uid }}, {{ $product->id }})"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_author') }}</p>
                @endauth
                <a class="simple-dropdown-link" href="{{ route('store.show', $product->name) }}"><i class="fa fa-shopping-basket" aria-hidden="true"></i>&nbsp;{{ __('messages.preview') }}</a>
                <p class="simple-dropdown-link copy_link" onclick="navigator.clipboard.writeText('{{ route('store.show', $product->name) }}'); var notif = document.getElementById('notif{{ $product->id }}'); notif.innerHTML = '<div class=\'alert alert-success\' role=\'alert\'>{{ __('messages.link_copied') }}</div>'; notif.style.display = 'block'; setTimeout(function() { notif.style.display = 'none'; }, 5000);"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}</p>
            </div>
        </div>
    </div>
    <div class="widget-box-status">
        <div class="widget-box-status-content">
            <div class="user-status">
                <a class="user-status-avatar" href="{{ $statusUserProfileUrl }}">
                    <div class="user-avatar small no-outline {{ $statusUserPresence }}">
                        <div class="user-avatar-content">
                            <div class="hexagon-image-30-32" data-src="{{ $statusUserAvatar }}" style="width: 30px; height: 32px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                            </div>
                        </div>
                        <div class="user-avatar-progress-border">
                            <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                            </div>
                        </div>
                        @if($statusUserIsAdmin)
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
                    <a class="bold" href="{{ $statusUserProfileUrl }}">{{ $statusUserName }}</a>
                    &nbsp;@if(isset($status->txt) && $status->txt == 'update')
                        {{ __('messages.updated_product') }}
                    @else
                        {{ __('messages.added_new_product') }}
                    @endif
                </p>
                <p class="user-status-text small">
                    <i class="fa fa-clock-o"></i>&nbsp;{{ __('messages.ago') }}&nbsp; {{ \Carbon\Carbon::createFromTimestamp($status->date)->diffForHumans() }}
                </p>
            </div>
            @include('theme::partials.activity.promotion_badge', ['activity' => $activity])
            <div class="tag-sticker">
                <svg class="tag-sticker-icon icon-shopping-bag">
                    <use xlink:href="#svg-shopping-bag"></use>
                </svg>
            </div>
            <p class="widget-box-status-text">
                <div class="product-preview">
                    <div id="post_form{{ $product->id }}"><div id="report{{ $product->id }}"></div></div>
                    <a href="{{ route('store.show', $product->name) }}">
                        <figure class="product-preview-image liquid">
                            <img src="{{ $productImage }}" alt="{{ $product->name }}">
                        </figure>
                    </a>
                    <div class="product-preview-info">
                        @if($product->o_order > 0)
                            <p class="text-sticker"><span class="highlighted">{{ $product->o_order }}</span> {{ __('messages.points') }}</p>
                        @else
                            <p class="text-sticker">{{ __('messages.free') }}</p>
                        @endif
                        <p class="product-preview-title"><a href="{{ route('store.show', $product->name) }}">{{ $product->name }}</a></p>
                        <p class="product-preview-category digital">
                            @if($product->type)
                                {{ $product->type->name }}
                            @endif
                        </p>
                        @if($product->is_suspended)
                            <div class="mb-2">
                                <span class="badge bg-danger">{{ __('messages.suspended') }}</span>
                            </div>
                        @endif
                        <p class="product-preview-text">{!! $description !!}</p>
                    </div>
                </div>
            </p>
            <div id="notif{{ $product->id }}"></div>
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
                            <a href="{{ route('store.show', $product->name) }}">{{ $activity->comments_count }} {{ __('messages.comments') }}</a>
                        </p>
                    </div>
                </div>
                <div class="content-action">
                    <div class="meta-line">
                        <p class="meta-line-link">
                            <a href="{{ route('store.show', $product->name) }}">{{ $activity->reposts_count }} {{ __('messages.reposts') }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="post-options">
        @auth
            <div class="post-option-wrap" style="position: relative;" data-activity-menu-wrap>
                <div class="post-option"
                     data-activity-menu-trigger
                     data-activity-menu-type="reaction">
                    <div id="reaction-btn-{{ $product->id }}">
                        @php
                            $myReaction = \App\Models\Like::where('uid', auth()->id())
                                ->where('sid', $product->id)
                                ->where('type', 3)
                                ->first();
                            $myReactionOption = null;
                            if($myReaction){
                                $myReactionOption = \App\Models\Option::where('o_parent', $myReaction->id)->where('o_type', 'data_reaction')->first();
                            }
                            $reactionColor = '';
                            if($myReactionOption){
                                if($myReactionOption->o_valuer == 'like') $reactionColor = 'style="color: #1bc8db;"';
                                elseif($myReactionOption->o_valuer == 'love') $reactionColor = 'style="color: #fc1f3b;"';
                                elseif($myReactionOption->o_valuer == 'dislike') $reactionColor = 'style="color: #3f3cf8;"';
                                elseif($myReactionOption->o_valuer == 'sad') $reactionColor = 'style="color: #139dff;"';
                                elseif($myReactionOption->o_valuer == 'angry') $reactionColor = 'style="color: #fa690e;"';
                                elseif($myReactionOption->o_valuer == 'happy') $reactionColor = 'style="color: #ffda21;"';
                                elseif($myReactionOption->o_valuer == 'funny') $reactionColor = 'style="color: #ffda21;"';
                                elseif($myReactionOption->o_valuer == 'wow') $reactionColor = 'style="color: #ffda21;"';
                            }
                        @endphp
                        @if($myReactionOption)
                            <img class="reaction-option-image" src="{{ theme_asset('img/reaction/'.$myReactionOption->o_valuer.'.png') }}" width="30" alt="reaction-{{ $myReactionOption->o_valuer }}">
                            <p class="post-option-text" {!! $reactionColor !!}>&nbsp;{{ ucfirst($myReactionOption->o_valuer) }}</p>
                        @else
                            <svg class="post-option-icon icon-thumbs-up">
                                <use xlink:href="#svg-thumbs-up"></use>
                            </svg>
                            <p class="post-option-text">{{ __('messages.react') }}</p>
                        @endif
                    </div>
                </div>
                <div class="reaction-options reaction-options-dropdown"
                     data-activity-menu-panel
                     style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                    @foreach(['like', 'love', 'dislike', 'happy', 'funny', 'wow', 'angry', 'sad'] as $reaction)
                        <div class="reaction-option text-tooltip-tft" data-title="{{ $reaction }}" onclick="toggleReaction({{ $product->id }}, 'store', '{{ $reaction }}')">
                            <img class="reaction-option-image" src="{{ theme_asset('img/reaction/'.$reaction.'.png') }}" alt="reaction-{{ $reaction }}">
                        </div>
                    @endforeach
                </div>
            </div>
        @endauth
        @auth
            <div class="post-option"
                 data-activity-comment
                 data-comment-id="{{ $product->id }}"
                 data-comment-type="store">
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
                        <a href="javascript:void(0);" onclick="sharePost('{{ $social }}', '{{ route('store.show', $product->name) }}', '{{ $product->name }}')">
                            <img class="reaction-option-image" src="{{ theme_asset('img/icons/'.$social.'-icon.png') }}">
                        </a>
                    </div>
                @endforeach
                @auth
                    <div class="reaction-option text-tooltip-tft" data-title="{{ __('messages.quote_repost') }}" style="position: relative;">
                        <a href="javascript:void(0);" onclick="openRepostComposer({{ $activity->id }}, '{{ $repostAuthorName }}', '{{ addslashes($repostExcerpt) }}')">
                            <span style="width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #615dfa; color: #fff;">
                                <i class="fa fa-retweet" aria-hidden="true"></i>
                            </span>
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
    <div class="post-comment-list post-comment-list-{{ $product->id }}"></div>
</div>
