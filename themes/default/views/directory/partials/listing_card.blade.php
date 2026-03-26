@php
    $listing = $card['listing'];
    $activity = $card['activity'];
@endphp

<article class="widget-box no-padding directory-listing-card directory-listing post{{ $activity?->id ?? $listing->id }}">
    <div class="widget-box-settings">
        @include('theme::directory.partials.site_action_menu', ['card' => $card])
    </div>

    <div class="widget-box-status">
        <div class="widget-box-status-content">
            <div class="directory-listing-head">
                <div class="user-status no-padding-top">
                    @if($card['owner'])
                        <a class="user-status-avatar" href="{{ $card['owner_url'] }}">
                            <div class="user-avatar small no-outline {{ $card['owner']->isOnline() ? 'online' : 'offline' }}">
                                <div class="user-avatar-content">
                                    <div class="hexagon-image-30-32" data-src="{{ $card['owner_avatar'] }}"></div>
                                </div>
                                <div class="user-avatar-progress-border">
                                    <div class="hexagon-border-40-44"></div>
                                </div>
                            </div>
                        </a>
                    @else
                        <div class="user-status-avatar">
                            <div class="user-avatar small no-outline offline">
                                <div class="user-avatar-content">
                                    <div class="hexagon-image-30-32" data-src="{{ $card['owner_avatar'] }}"></div>
                                </div>
                                <div class="user-avatar-progress-border">
                                    <div class="hexagon-border-40-44"></div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <p class="user-status-title medium">
                        @if($card['owner_url'])
                            <a class="bold" href="{{ $card['owner_url'] }}">{{ $card['owner_name'] }}</a>
                        @else
                            <span class="bold">{{ $card['owner_name'] }}</span>
                        @endif
                    </p>

                    <p class="user-status-text small-space">
                        <i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;{{ $card['published_diff'] }}
                    </p>
                </div>
            </div>

            <div class="directory-listing-layout">
                @once
                    @include('theme::partials.directory.lazy_image_script')
                @endonce
                <a class="directory-listing-media" href="{{ $card['visit_url'] }}" target="_blank" rel="noopener">
                    <img src="{{ $card['listing']->prominent_image ?: theme_asset('img/dir_image.png') }}" data-lazy-fetch-url="{{ route('directory.image.fetch', $card['listing']->id) }}" alt="{{ $card['title'] }}">
                </a>

                <div class="directory-listing-content">
                    <p class="directory-listing-domain">{{ $card['display_domain'] }}</p>

                    <h3 class="directory-listing-title">
                        <a href="{{ $card['detail_url'] }}">{{ $card['title'] }}</a>
                    </h3>

                    <div class="directory-listing-badges">
                        @if($card['category_url'])
                            <a class="directory-meta-chip" href="{{ $card['category_url'] }}">
                                <i class="fa fa-folder-open" aria-hidden="true"></i>
                                {{ $card['category_name'] }}
                            </a>
                        @endif

                        <span class="directory-meta-chip directory-meta-chip-muted">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                            {{ $card['views'] }} {{ __('messages.visits') }}
                        </span>
                    </div>

                    @if($card['excerpt'])
                        <p class="directory-listing-excerpt" dir="auto">{{ $card['excerpt'] }}</p>
                    @endif

                    <div class="directory-listing-cta">
                        <a class="button small secondary directory-visit-button" href="{{ $card['visit_url'] }}" target="_blank" rel="noopener">
                            <i class="fa fa-external-link" aria-hidden="true"></i>&nbsp;{{ __('messages.visit_site') }}
                        </a>
                    </div>

                    @if(!empty($card['tags']))
                        <div class="directory-listing-tags">
                            @foreach(array_slice($card['tags'], 0, 4) as $tag)
                                <span class="directory-tag-pill">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="directory-listing-stats">
                <span><i class="fa fa-eye" aria-hidden="true"></i>{{ $card['views'] }}</span>
                <span><i class="fa fa-bolt" aria-hidden="true"></i>{{ $card['reactions_count'] }} {{ __('messages.reactions') }}</span>
                <span><i class="fa fa-comments" aria-hidden="true"></i>{{ $card['comments_count'] }} {{ __('messages.comments') }}</span>
            </div>

            <div id="report{{ $listing->id }}"></div>
            <div id="notif{{ $listing->id }}"></div>
        </div>
    </div>

    <div class="post-options directory-post-options">
        @include('theme::directory.partials.reaction_button', ['card' => $card])

        @auth
            <div class="post-option directory-comment-action" data-directory-comment-toggle="{{ $listing->id }}">
                <svg class="post-option-icon icon-comment">
                    <use xlink:href="#svg-comment"></use>
                </svg>
                <p class="post-option-text">{{ __('messages.comment') }}</p>
            </div>
        @endauth

        @include('theme::directory.partials.share_menu', ['shareUrl' => $card['detail_url'], 'shareTitle' => $card['title']])

        <a class="post-option directory-post-option-link" href="{{ $card['detail_url'] }}">
            <svg class="post-option-icon icon-info">
                <use xlink:href="#svg-info"></use>
            </svg>
            <p class="post-option-text">{{ __('messages.details') }}</p>
        </a>
    </div>

    <div class="post-comment-list post-comment-list-{{ $listing->id }}"></div>
</article>
