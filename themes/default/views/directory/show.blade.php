@extends('theme::layouts.master')
@include('theme::directory._assets')

@push('head')
{{-- Google Schema.org Structured Data --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'SoftwareApplication',
    'name' => $detail['title'],
    'url' => $listing->url,
    'description' => $detail['excerpt'],
    'applicationCategory' => $detail['category_name'],
    'operatingSystem' => 'All',
    'offers' => [
        '@type' => 'Offer',
        'price' => in_array($detail['pricing_type'], ['free', 'open_source']) ? '0' : '',
        'priceCurrency' => 'USD',
    ],
    'aggregateRating' => [
        '@type' => 'AggregateRating',
        'ratingValue' => (string) min(5.0, max(4.0, 4.0 + ($detail['upvotes_count'] > 0 ? 0.9 : 0.0))),
        'reviewCount' => (string) max(1, $detail['upvotes_count'] + $detail['reactions_count'] + $detail['comments_count']),
    ],
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<div class="directory-rdx directory-detail-page">
    <div class="section-banner directory-hub-banner directory-detail-banner">
        <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="directory-detail-icon">
        <div class="directory-hub-banner-copy">
            <p class="section-banner-title">{{ $detail['title'] }}</p>
            <p class="section-banner-text">{{ $detail['display_domain'] }}</p>
        </div>
    </div>

    <div class="section-filters-bar v7 directory-breadcrumb-bar">
        <div class="section-filters-bar-actions">
            <div class="section-filters-bar-info">
                <p class="section-filters-bar-title">
                    <a href="{{ route('directory.index') }}">{{ __('messages.directory') }}</a>

                    @if($detail['category_url'])
                        <span class="separator"></span>
                        <a href="{{ $detail['category_url'] }}">{{ $detail['category_name'] }}</a>
                    @endif

                    <span class="separator"></span>
                    <a href="{{ $detail['detail_url'] }}">{{ $detail['title'] }}</a>
                </p>
            </div>
        </div>
    </div>

    @include('theme::partials.ads', ['id' => 5])

    <div class="grid grid-3-6-3 mobile-prefer-content directory-detail-grid">
        <div class="grid-column">
            <div class="widget-box directory-side-card directory-detail-sidebar">
                <p class="widget-box-title">{{ __('messages.details') }}</p>

                <div class="widget-box-content">
                    <div class="directory-side-stat-list">
                        <div class="directory-side-stat-item">
                            <span>{{ __('messages.upvotes') ?? 'التصويتات' }}</span>
                            <strong class="upvote-stat-val">{{ $detail['upvotes_count'] }}</strong>
                        </div>

                        <div class="directory-side-stat-item">
                            <span>{{ __('messages.visits') }}</span>
                            <strong>{{ $detail['views'] }}</strong>
                        </div>

                        <div class="directory-side-stat-item">
                            <span>{{ __('messages.reactions') }}</span>
                            <strong>{{ $detail['reactions_count'] }}</strong>
                        </div>

                        <div class="directory-side-stat-item">
                            <span>{{ __('messages.comments') }}</span>
                            <strong>{{ $detail['comments_count'] }}</strong>
                        </div>
                    </div>

                    <div class="directory-detail-sidebar-meta">
                        @if($detail['owner_url'])
                            <a class="user-status-avatar" href="{{ $detail['owner_url'] }}" style="margin-inline-end: 12px;">
                                <div class="user-avatar small no-outline {{ $listing->user->isOnline() ? 'online' : 'offline' }}">
                                    <div class="user-avatar-content">
                                        <div class="hexagon-image-30-32" data-src="{{ $detail['owner_avatar'] }}" style="width: 30px; height: 32px; position: relative;">
                                            <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                                        </div>
                                    </div>
                                    <div class="user-avatar-progress-border">
                                        <div class="hexagon-border-40-44" data-line-color="{{ $listing->user ? $listing->user->profileBadgeColor() : '' }}" style="width: 40px; height: 44px; position: relative;">
                                            <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                                        </div>
                                    </div>
                                    @if($listing->user && $listing->user->hasVerifiedBadge())
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
                            <a class="directory-owner-link" href="{{ $detail['owner_url'] }}">
                                <span>{{ $detail['owner_name'] }}</span>
                            </a>
                        @else
                            <div class="user-avatar small no-outline offline" style="margin-inline-end: 12px;">
                                <div class="user-avatar-content">
                                    <div class="hexagon-image-30-32" data-src="{{ $detail['owner_avatar'] }}" style="width: 30px; height: 32px; position: relative;">
                                        <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                                    </div>
                                </div>
                                <div class="user-avatar-progress-border">
                                    <div class="hexagon-border-40-44" data-line-color="{{ $listing->user ? $listing->user->profileBadgeColor() : '' }}" style="width: 40px; height: 44px; position: relative;">
                                        <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="directory-owner-link">
                                <span>{{ $detail['owner_name'] }}</span>
                            </div>
                        @endif

                        @if($detail['category_url'])
                            <a class="directory-meta-line" href="{{ $detail['category_url'] }}">
                                <i class="fa fa-folder-open" aria-hidden="true"></i>
                                {{ $detail['category_name'] }}
                            </a>
                        @endif

                        <div class="directory-meta-line">
                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                            {{ $detail['published_diff'] }}
                        </div>
                    </div>

                    <a class="button secondary directory-sidebar-visit" href="{{ $detail['visit_url'] }}" target="_blank" rel="noopener">
                        <i class="fa fa-external-link"></i>&nbsp;{{ __('messages.visit_site') }}
                    </a>

                    <div style="margin-top: 12px; display: flex; flex-direction: column; gap: 8px;">
                        <button type="button" class="button white-outline" data-directory-modal-open="embed-badge-modal" style="width: 100%;">
                            <i class="fa fa-code"></i>&nbsp;{{ __('messages.embed_badge') ?? 'شارة التضمين' }}
                        </button>

                        @if(!$detail['is_verified'])
                            <button type="button" class="button white-outline" data-directory-modal-open="claim-modal" style="width: 100%;">
                                <i class="fa fa-shield"></i>&nbsp;{{ __('messages.claim_listing') ?? 'توثيق الملكية' }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-column">
            @include('theme::directory.partials.detail_hero', ['card' => $detail, 'activity' => $activity])

            @if(!empty($detail['features']))
                <div class="widget-box directory-features-section">
                    <p class="widget-box-title">{{ __('messages.key_features') ?? 'أبرز مميزات الموقع' }}</p>
                    <div class="widget-box-content">
                        <div class="directory-features-grid">
                            @foreach($detail['features'] as $feature)
                                <div class="directory-feature-item">
                                    <i class="fa fa-check-circle text-success"></i>
                                    <span>{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="widget-box directory-detail-copy-card">
                <p class="widget-box-title">{{ __('messages.desc') }}</p>

                <div class="widget-box-content">
                    @if($detail['description_html'])
                        <div class="directory-detail-description" dir="auto">{!! $detail['description_html'] !!}</div>
                    @else
                        <p class="directory-empty-copy">{{ __('messages.check_back_later') }}</p>
                    @endif
                </div>
            </div>

            @if(!empty($detail['tags']))
                <div class="widget-box directory-tags-section">
                    <p class="widget-box-title">{{ __('messages.meta_keywords') }}</p>

                    <div class="widget-box-content">
                        <div class="directory-tags-list">
                            @foreach($detail['tags'] as $tag)
                                <span class="directory-tag-pill">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="widget-box no-padding directory-detail-interactions">
                <div class="widget-box-content">
                    <div class="directory-listing-stats directory-listing-stats-detail">
                        <div class="directory-detail-stat-tile is-upvotes">
                            <span class="directory-detail-stat-tile-icon">
                                <i class="fa fa-arrow-up" aria-hidden="true"></i>
                            </span>

                            <div class="directory-detail-stat-tile-copy">
                                <strong class="directory-detail-stat-tile-value upvote-stat-val">{{ $detail['upvotes_count'] }}</strong>
                                <span class="directory-detail-stat-tile-label">{{ __('messages.upvotes') ?? 'تصويت' }}</span>
                            </div>
                        </div>

                        <div class="directory-detail-stat-tile is-views">
                            <span class="directory-detail-stat-tile-icon">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </span>

                            <div class="directory-detail-stat-tile-copy">
                                <strong class="directory-detail-stat-tile-value">{{ $detail['views'] }}</strong>
                                <span class="directory-detail-stat-tile-label">{{ __('messages.visits') }}</span>
                            </div>
                        </div>

                        <div class="directory-detail-stat-tile is-reactions">
                            <span class="directory-detail-stat-tile-icon">
                                <i class="fa fa-bolt" aria-hidden="true"></i>
                            </span>

                            <div class="directory-detail-stat-tile-copy">
                                <strong class="directory-detail-stat-tile-value">{{ $detail['reactions_count'] }}</strong>
                                <span class="directory-detail-stat-tile-label">{{ __('messages.reactions') }}</span>
                            </div>
                        </div>

                        <div class="directory-detail-stat-tile is-comments">
                            <span class="directory-detail-stat-tile-icon">
                                <i class="fa fa-comments" aria-hidden="true"></i>
                            </span>

                            <div class="directory-detail-stat-tile-copy">
                                <strong class="directory-detail-stat-tile-value">{{ $detail['comments_count'] }}</strong>
                                <span class="directory-detail-stat-tile-label">{{ __('messages.comments') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="post-options directory-post-options">
                    @include('theme::directory.partials.reaction_button', ['card' => $detail])

                    @auth
                        <div class="post-option directory-comment-action" data-directory-comment-toggle="{{ $listing->id }}">
                            <svg class="post-option-icon icon-comment">
                                <use xlink:href="#svg-comment"></use>
                            </svg>
                            <p class="post-option-text">{{ __('messages.comment') }}</p>
                        </div>
                    @endauth

                    @include('theme::directory.partials.share_menu', ['shareUrl' => $detail['detail_url'], 'shareTitle' => $detail['title']])

                    <a class="post-option directory-post-option-link" href="{{ $detail['visit_url'] }}" target="_blank" rel="noopener">
                        <svg class="post-option-icon icon-public">
                            <use xlink:href="#svg-public"></use>
                        </svg>
                        <p class="post-option-text">{{ __('messages.visit_site') }}</p>
                    </a>
                </div>

                <div class="post-comment-list post-comment-list-{{ $listing->id }}" data-directory-detail-comments="{{ $listing->id }}"></div>
            </div>
        </div>

        <div class="grid-column">
            <x-widget-column side="directory_right" />
        </div>
    </div>
</div>

{{-- Modal 1: Embed Badge Modal --}}
<div class="directory-modal" id="embed-badge-modal" style="display: none;">
    <div class="directory-modal-backdrop" data-directory-modal-close="embed-badge-modal"></div>
    <div class="directory-modal-content">
        <div class="directory-modal-header">
            <h3><i class="fa fa-code"></i> {{ __('messages.embed_badge_title') ?? 'شارة التضمين لموقعك' }}</h3>
            <button type="button" class="directory-modal-close-btn" data-directory-modal-close="embed-badge-modal">&times;</button>
        </div>
        <div class="directory-modal-body">
            <p>{{ __('messages.embed_badge_desc') ?? 'ضع هذه الشارة في موقعك ليتمكن زوارك من رؤية تقييم وتصويت موقعك على دليل MYADS:' }}</p>
            
            <div class="directory-badge-preview-box">
                <img src="{{ route('directory.badge', $listing->id) }}" alt="Featured on MYADS">
            </div>

            <label class="form-label font-bold" style="margin-top: 15px;">HTML Code:</label>
            <div class="directory-code-copy-box">
                <textarea readonly class="directory-code-textarea" id="html-badge-code">&lt;a href="{{ route('directory.show', $listing->id) }}" target="_blank" rel="noopener"&gt;&lt;img src="{{ route('directory.badge', $listing->id) }}" alt="Featured on MYADS Directory" /&gt;&lt;/a&gt;</textarea>
                <button type="button" class="button small primary directory-copy-btn" data-copy-target="html-badge-code">
                    <i class="fa fa-copy"></i> {{ __('messages.copy') ?? 'نسخ' }}
                </button>
            </div>

            <label class="form-label font-bold" style="margin-top: 15px;">Markdown Code:</label>
            <div class="directory-code-copy-box">
                <textarea readonly class="directory-code-textarea" id="md-badge-code">[![Featured on MYADS Directory]({{ route('directory.badge', $listing->id) }})]({{ route('directory.show', $listing->id) }})</textarea>
                <button type="button" class="button small secondary directory-copy-btn" data-copy-target="md-badge-code">
                    <i class="fa fa-copy"></i> {{ __('messages.copy') ?? 'نسخ' }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal 2: Claim Listing Modal --}}
<div class="directory-modal" id="claim-modal" style="display: none;">
    <div class="directory-modal-backdrop" data-directory-modal-close="claim-modal"></div>
    <div class="directory-modal-content">
        <div class="directory-modal-header">
            <h3><i class="fa fa-shield"></i> {{ __('messages.claim_listing_title') ?? 'توثيق ملكية الموقع' }}</h3>
            <button type="button" class="directory-modal-close-btn" data-directory-modal-close="claim-modal">&times;</button>
        </div>
        <div class="directory-modal-body">
            @if(auth()->check())
                <p>{{ __('messages.claim_listing_desc') ?? 'لإثبات أنك مالك أو مطور هذا الموقع، ضع وسم الميتا التالي في ترويسة (<head>) صفحة موقعك الرئيسية:' }}</p>
                
                @php
                    $verificationToken = 'myads-verify-' . hash('crc32', $listing->id . '-' . auth()->id());
                @endphp

                <div class="directory-code-copy-box">
                    <textarea readonly class="directory-code-textarea" id="meta-tag-code">&lt;meta name="myads-verification" content="{{ $verificationToken }}"&gt;</textarea>
                    <button type="button" class="button small primary directory-copy-btn" data-copy-target="meta-tag-code">
                        <i class="fa fa-copy"></i> {{ __('messages.copy') ?? 'نسخ' }}
                    </button>
                </div>

                <div id="claim-status-message" style="margin-top: 15px; display: none;"></div>

                <div style="margin-top: 20px; text-align: end;">
                    <button type="button" class="button secondary" id="btn-verify-claim" data-listing-id="{{ $listing->id }}">
                        <i class="fa fa-check-circle"></i> {{ __('messages.verify_now') ?? 'تحقق من الموقع الآن' }}
                    </button>
                </div>
            @else
                <p>{{ __('messages.login_to_claim') ?? 'يرجى تسجيل الدخول أولاً لتتمكن من توثيق ملكية هذا الموقع.' }}</p>
                <a href="{{ route('login') }}" class="button primary" style="margin-top: 12px;">{{ __('messages.login') }}</a>
            @endif
        </div>
    </div>
</div>
@endsection
