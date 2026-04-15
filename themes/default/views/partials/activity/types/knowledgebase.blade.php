@php
    $status = $activity;
    $statusUser = $status->user;
    $statusUserProfileUrl = $statusUser ? route('profile.show', $statusUser->username) : '#';
    $statusUserName = $statusUser?->username ?? __('messages.unknown_user');
    $statusUserAvatar = $statusUser ? $statusUser->avatarUrl() : asset('upload/_avatar.png');
    $statusUserPresence = $statusUser?->isOnline() ? 'online' : 'offline';
    $statusUserIsAdmin = $statusUser?->isAdmin() ?? false;
    $article = $status->related_content;
    $product = $article?->productItem;
    $articleAuthor = $article?->authorUser;
    $productSlug = $product?->name ?? $article?->o_mode ?? '';
    $productName = $productSlug !== '' ? $productSlug : __('messages.knowledgebase');
    $knowledgebaseUrl = ($productSlug !== '' && $article?->name)
        ? route('kb.show', ['name' => $productSlug, 'article' => $article->name])
        : '#';
    $reportKey = 'kbfeed' . $status->id;
    $notifyKey = 'kbnotif' . $status->id;
    $rawSummary = html_entity_decode(strip_tags((string) ($article?->o_valuer ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $rawSummary = trim((string) preg_replace('/[#>*_`~\\[\\]\\(\\)\r\n]+/u', ' ', $rawSummary));
    $summary = \Illuminate\Support\Str::limit((string) preg_replace('/\s+/u', ' ', $rawSummary), 240);
    $repostExcerpt = \Illuminate\Support\Str::limit(trim(($article?->name ?? '') . ' ' . $summary), 80);
    $repostAuthorName = addslashes($statusUserName);
    $canDeleteStatus = auth()->check() && (auth()->id() == $status->uid || auth()->user()->isAdmin());
    $canReportTopic = auth()->check() && !$canDeleteStatus;
    $canReportAuthor = $canReportTopic && $articleAuthor && auth()->id() != $articleAuthor->id;
@endphp

@if($article)
    <div class="widget-box no-padding activity-post-card kb-community-card post{{ $status->id }}" id="community-post-{{ $status->id }}">
        <div class="widget-box-settings">
            <div class="post-settings-wrap" style="position: relative;">
                <div class="post-settings widget-box-post-settings-dropdown-trigger">
                    <svg class="post-settings-icon icon-more-dots">
                        <use xlink:href="#svg-more-dots"></use>
                    </svg>
                </div>
                <div class="simple-dropdown widget-box-post-settings-dropdown" style="position: absolute; z-index: 9999; top: 30px; right: 9px; opacity: 0; visibility: hidden; transform: translate(0px, -20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                    <a class="simple-dropdown-link" href="{{ $knowledgebaseUrl }}"><i class="fa fa-book" aria-hidden="true"></i>&nbsp;{{ __('messages.preview') }}</a>
                    <p class="simple-dropdown-link copy_link" onclick="navigator.clipboard.writeText('{{ $knowledgebaseUrl }}'); var notif = document.getElementById('{{ $notifyKey }}'); if (notif) { notif.innerHTML = '<div class=\'alert alert-success\' role=\'alert\'>{{ __('messages.link_copied') }}</div>'; notif.style.display = 'block'; setTimeout(function() { notif.style.display = 'none'; }, 5000);}"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}</p>
                    @auth
                        @if($canDeleteStatus)
                            <p class="simple-dropdown-link post_delete{{ $status->id }}" onclick="deletePost({{ $status->id }}, 205, '.post{{ $status->id }}')"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}</p>
                        @endif
                        @if($canReportTopic)
                            <p class="simple-dropdown-link" onclick="reportPost({{ $article->id }}, 205, '{{ $reportKey }}')"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_topic') }}</p>
                        @endif
                        @if($canReportAuthor)
                            <p class="simple-dropdown-link" onclick="reportUser({{ $articleAuthor->id }}, '{{ $reportKey }}')"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_publisher') }}</p>
                        @endif
                    @endauth
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
                                <div class="hexagon-border-40-44" data-line-color="{{ $statusUser ? $statusUser->profileBadgeColor() : '' }}" style="width: 40px; height: 44px; position: relative;"></div>
                            </div>
                            @if($statusUserIsAdmin)
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
                        <a class="bold" href="{{ $statusUserProfileUrl }}">{{ $statusUserName }}</a>
                    </p>
                    <p class="user-status-text small">
                        <i class="fa fa-clock-o"></i>&nbsp;{{ __('messages.ago') }}&nbsp; {{ $status->date_formatted }}
                    </p>
                </div>

                <div class="tag-sticker">
                    <i class="fa fa-book" aria-hidden="true"></i>
                </div>

                <div class="kb-community-card__surface">
                    <div class="kb-community-card__badges">
                        <span class="kb-community-card__badge kb-community-card__badge--primary">{{ __('messages.knowledgebase') }}</span>
                        <span class="kb-community-card__badge">{{ $productName }}</span>
                        <span class="kb-community-card__badge kb-community-card__badge--success">{{ __('messages.published') }}</span>
                    </div>

                    <a class="kb-community-card__title" href="{{ $knowledgebaseUrl }}">{{ $article->name }}</a>

                    @if($summary !== '')
                        <p class="kb-community-card__summary">{{ $summary }}</p>
                    @endif

                    <div class="kb-community-card__meta">
                        <span class="kb-community-card__meta-item">
                            <i class="fa fa-hashtag" aria-hidden="true"></i>
                            {{ __('messages.topic') }} #{{ $article->id }}
                        </span>
                        <span class="kb-community-card__meta-item">
                            <i class="fa fa-user" aria-hidden="true"></i>
                            {{ __('messages.publisher') }}: {{ $articleAuthor?->username ?? __('messages.guest') }}
                        </span>
                        <a class="kb-community-card__cta" href="{{ $knowledgebaseUrl }}">
                            {{ __('messages.preview') }}
                            <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div id="report{{ $reportKey }}"></div>
                <div id="{{ $notifyKey }}"></div>

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
                                <a href="{{ $knowledgebaseUrl }}">{{ $activity->comments_count }} {{ __('messages.comments') }}</a>
                            </p>
                        </div>
                    </div>
                    <div class="content-action">
                        <div class="meta-line">
                            <p class="meta-line-link">
                                <a href="{{ $knowledgebaseUrl }}">{{ $activity->reposts_count }} {{ __('messages.reposts') }}</a>
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
                        <div id="reaction-btn-{{ $status->id }}">
                            @php
                                $myReaction = \App\Models\Like::where('uid', auth()->id())
                                    ->where('sid', $status->id)
                                    ->where('type', \App\Services\KnowledgebaseCommunityService::REACTION_TYPE)
                                    ->first();
                                $myReactionOption = null;
                                if ($myReaction) {
                                    $myReactionOption = \App\Models\Option::where('o_parent', $myReaction->id)
                                        ->where('o_type', 'data_reaction')
                                        ->first();
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
                    <div class="reaction-options reaction-options-dropdown"
                         data-activity-menu-panel
                         style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                        @foreach(['like', 'love', 'dislike', 'happy', 'funny', 'wow', 'angry', 'sad'] as $reaction)
                            <div class="reaction-option text-tooltip-tft" data-title="{{ $reaction }}" onclick="toggleReaction({{ $status->id }}, 'knowledgebase', '{{ $reaction }}')">
                                <img class="reaction-option-image" src="{{ theme_asset('img/reaction/'.$reaction.'.png') }}" alt="reaction-{{ $reaction }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endauth

            @auth
                <div class="post-option"
                     data-activity-comment
                     data-comment-id="{{ $status->id }}"
                     data-comment-type="knowledgebase">
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
                            <a href="javascript:void(0);" onclick="sharePost('{{ $social }}', {{ Illuminate\Support\Js::from($knowledgebaseUrl) }}, {{ Illuminate\Support\Js::from($article->name) }})">
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

        <div class="post-comment-list post-comment-list-{{ $status->id }}"></div>
    </div>
@endif
