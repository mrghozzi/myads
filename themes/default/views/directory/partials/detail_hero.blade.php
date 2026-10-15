<section class="widget-box directory-detail-shell post{{ $card['listing']->id }}">
    <div class="widget-box-settings">
        @include('theme::directory.partials.site_action_menu', ['card' => $card, 'activity' => $activity ?? null])
    </div>

    <div class="widget-box-content">
        <div class="directory-detail-hero">
            @once
                @include('theme::partials.directory.lazy_image_script')
            @endonce
            <div class="directory-detail-brand">
                <div class="directory-detail-media">
                    <img src="{{ $card['listing']->prominent_image ?: theme_asset('img/dir_image.png') }}" data-lazy-fetch-url="{{ route('directory.image.fetch', $card['listing']->id) }}" alt="{{ $card['title'] }}">
                </div>

                <div class="directory-detail-copy">
                    <div class="directory-detail-top-badges">
                        <p class="directory-listing-domain">
                            {{ $card['display_domain'] }}
                            @if($card['is_verified'])
                                <span class="directory-verified-badge" title="{{ __('messages.verified_site') ?? 'موقع موثق من المطور' }}"><i class="fa fa-check-circle"></i> {{ __('messages.verified') ?? 'موثق' }}</span>
                            @endif
                            @if($card['is_featured'])
                                <span class="directory-featured-badge" title="{{ __('messages.featured_site') ?? 'موقع مميز' }}"><i class="fa fa-gem"></i> {{ __('messages.featured') ?? 'مميز' }}</span>
                            @endif
                        </p>

                        <span class="{{ $card['pricing_class'] }}">{{ $card['pricing_label'] }}</span>
                    </div>

                    <h2 class="directory-detail-title">{{ $card['title'] }}</h2>

                    <div class="directory-detail-meta">
                        @if($card['category_url'])
                            <a class="directory-meta-chip" href="{{ $card['category_url'] }}">
                                <i class="fa fa-folder-open" aria-hidden="true"></i>
                                {{ $card['category_name'] }}
                            </a>
                        @endif

                        <span class="directory-meta-chip directory-meta-chip-muted">
                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                            {{ $card['published_diff'] }}
                        </span>

                        <span class="directory-meta-chip directory-meta-chip-muted">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                            {{ $card['views'] }} {{ __('messages.visits') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="directory-detail-cta">
                {{-- Upvote Button --}}
                <button type="button" 
                        class="directory-upvote-btn directory-upvote-btn-hero {{ $card['has_upvoted'] ? 'is-upvoted' : '' }}" 
                        data-directory-upvote="{{ $card['listing']->id }}"
                        @guest onclick="window.location='{{ route('login') }}'" @endguest
                        aria-label="{{ __('messages.upvote') ?? 'تصويت' }}">
                    <span class="directory-upvote-arrow">▲</span>
                    <span class="directory-upvote-text">{{ __('messages.upvote') ?? 'تصويت' }}</span>
                    <span class="directory-upvote-count" data-count="{{ $card['upvotes_count'] }}">{{ $card['upvotes_count'] }}</span>
                </button>

                <a class="button secondary" href="{{ $card['visit_url'] }}" target="_blank" rel="noopener">
                    <i class="fa fa-external-link"></i>&nbsp;{{ __('messages.visit_site') }}
                </a>

                <button type="button" class="button white-outline" data-directory-modal-open="embed-badge-modal">
                    <i class="fa fa-code"></i>&nbsp;{{ __('messages.embed_badge') ?? 'شارة التضمين' }}
                </button>

                @if(!$card['is_verified'])
                    <button type="button" class="button white-outline" data-directory-modal-open="claim-modal">
                        <i class="fa fa-shield"></i>&nbsp;{{ __('messages.claim_listing') ?? 'توثيق الملكية' }}
                    </button>
                @endif

                @if($card['can_manage'])
                    <a class="button primary" href="{{ route('directory.edit', $card['listing']->id) }}">
                        <i class="fa fa-pencil"></i>&nbsp;{{ __('messages.edit') }}
                    </a>
                @endif
            </div>
        </div>

        <div id="notif{{ $card['listing']->id }}"></div>
        <div id="report{{ $card['listing']->id }}"></div>
    </div>
</section>
