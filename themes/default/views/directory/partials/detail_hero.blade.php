<section class="widget-box directory-detail-shell">
    <div class="widget-box-settings">
        @include('theme::directory.partials.site_action_menu', ['card' => $card])
    </div>

    <div class="widget-box-content">
        <div class="directory-detail-hero">
            <div class="directory-detail-brand">
                <div class="directory-detail-media">
                    <img src="{{ theme_asset('img/dir_image.png') }}" alt="{{ $card['title'] }}">
                </div>

                <div class="directory-detail-copy">
                    <p class="directory-listing-domain">{{ $card['display_domain'] }}</p>
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
                <a class="button secondary" href="{{ $card['visit_url'] }}" target="_blank" rel="noopener">
                    {{ __('messages.visit_site') }}
                </a>

                @if($card['can_manage'])
                    <a class="button primary" href="{{ route('directory.edit', $card['listing']->id) }}">
                        {{ __('messages.edit') }}
                    </a>
                @endif
            </div>
        </div>

        <div id="notif{{ $card['listing']->id }}"></div>
        <div id="report{{ $card['listing']->id }}"></div>
    </div>
</section>
