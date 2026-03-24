@extends('theme::layouts.master')
@include('theme::directory._assets')

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
                                        <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;">
                                            <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                                        </div>
                                    </div>
                                    @if($listing->user && $listing->user->isAdmin())
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
                                    <div class="hexagon-border-40-44" style="width: 40px; height: 44px; position: relative;">
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
                        {{ __('messages.visit_site') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="grid-column">
            @include('theme::directory.partials.detail_hero', ['card' => $detail])

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
@endsection
