@extends('theme::layouts.master')

@section('content')
<div class="forum-rdx">
    <!-- SECTION BANNER -->
    <div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;">
        <img class="section-banner-icon" src="{{ theme_asset('img/banner/discussion-icon.png') }}">
        <p class="section-banner-title">#{{ $tag }}</p>
    </div>
    <!-- /SECTION BANNER -->

    <div class="section-header">
        <div class="section-header-info">
            <h2 class="section-title">{{ __('messages.search_results') }} #{{ $tag }}</h2>
        </div>
    </div>

    <!-- TABS -->
    <div class="section-filters-bar v7">
        <div class="section-filters-bar-actions">
            <div class="section-filters-bar-info">
                <p class="section-filters-bar-title">{{ __('messages.search_results') }} #{{ $tag }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-3-9-mobile">
        <div class="grid-column">
            <!-- RESULTS LIST -->
            <div class="widget-box">
                <p class="widget-box-title">{{ __('messages.topics') }} ({{ $topics->total() }})</p>
                <div class="widget-box-content">
                    @forelse($topics as $topic)
                        <div class="user-status">
                            <a class="user-status-avatar" href="{{ route('forum.topic', $topic->id) }}">
                                <div class="user-avatar small no-outline">
                                    <div class="user-avatar-content">
                                        <div class="hexagon-image-30-32" data-src="{{ $topic->user ? $topic->user->avatarUrl() : asset('upload/_avatar.png') }}"></div>
                                    </div>
                                </div>
                            </a>
                            <p class="user-status-title"><a class="bold" href="{{ route('forum.topic', $topic->id) }}">{{ $topic->name }}</a></p>
                            <p class="user-status-text small">{{ $topic->date_formatted }} {{ __('messages.by') }} {{ $topic->user->username ?? __('messages.unknown') }}</p>
                        </div>
                    @empty
                        <p>{{ __('messages.no_topics_found') }}</p>
                    @endforelse
                    
                    <div style="margin-top: 20px;">
                        {{ $topics->appends(['statuses_page' => $statuses->currentPage()])->links() }}
                    </div>
                </div>
            </div>
            
            <div class="widget-box" style="margin-top: 20px;">
                <p class="widget-box-title">{{ __('messages.latest_updates') }} ({{ $statuses->total() }})</p>
                <div class="widget-box-content">
                    @forelse($statuses as $status)
                        <div class="user-status">
                                            <a class="user-status-avatar" href="{{ route('profile.short', $status->user?->publicRouteIdentifier() ?? $status->uid) }}">
                                <div class="user-avatar small no-outline">
                                    <div class="user-avatar-content">
                                        <div class="hexagon-image-30-32" data-src="{{ $status->user ? $status->user->avatarUrl() : asset('upload/_avatar.png') }}"></div>
                                    </div>
                                </div>
                            </a>
                            <p class="user-status-title">
                                            <a class="bold" href="{{ route('profile.short', $status->user?->publicRouteIdentifier() ?? $status->uid) }}">{{ $status->user->username ?? __('messages.unknown') }}</a>
                            </p>
                            <p class="user-status-text small">{!! \App\Support\ContentFormatter::linkifyHashtags(Str::limit($status->txt ?: $status->statu, 150)) !!}</p>
                            <p class="user-status-timestamp small">{{ $status->date_formatted }}</p>
                        </div>
                    @empty
                        <p>{{ __('messages.no_activities') }}</p>
                    @endforelse

                    <div style="margin-top: 20px;">
                        {{ $statuses->appends(['topics_page' => $topics->currentPage()])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof app !== 'undefined' && app.plugins && app.plugins.createHexagon) {
            app.plugins.createHexagon({
                container: '.hexagon-image-30-32',
                width: 30,
                height: 32,
                roundedCorners: true,
                clip: true
            });
        }
    });
</script>
@endsection
