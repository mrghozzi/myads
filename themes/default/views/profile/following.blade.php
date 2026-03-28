@extends('theme::layouts.master')

@section('content')
<!-- Reusing Profile Header -->
<div class="profile-header">
    <figure class="profile-header-cover liquid" style="background: rgba(0, 0, 0, 0) url({{ asset($cover) }}) no-repeat scroll center center / cover;">
        <img src="{{ asset($cover) }}" alt="cover-{{ $user->username }}" style="display: none;">
    </figure>
    <div class="profile-header-info">
        <div class="user-short-description big">
            <a class="user-short-description-avatar user-avatar big {{ $user->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.show', $user->username) }}">
                <div class="user-avatar-border">
                    <div class="hexagon-148-164" style="width: 148px; height: 164px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="148" height="164"></canvas></div>
                </div>
                <div class="user-avatar-content">
                    <div class="hexagon-image-100-110" data-src="{{ $user->avatarUrl() }}" style="width: 100px; height: 110px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="100" height="110"></canvas></div>
                </div>
                <div class="user-avatar-progress-border">
                    <div class="hexagon-border-124-136" style="width: 124px; height: 136px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="124" height="136"></canvas></div>
                </div>
                @if($user->ucheck == 1)
                <div class="user-avatar-badge">
                    <div class="user-avatar-badge-border">
                        <div class="hexagon-40-44" style="width: 22px; height: 24px; position: relative;"></div>
                    </div>
                    <div class="user-avatar-badge-content">
                        <div class="hexagon-dark-32-34" style="width: 16px; height: 18px; position: relative;"></div>
                    </div>
                    <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                </div>
                @endif
            </a>
            <p class="user-short-description-title"><a href="{{ route('profile.show', $user->username) }}">{{ $user->username }}</a></p>
            <p class="user-short-description-text">{{ __('messages.lastcontact') }}&nbsp;{{ \Carbon\Carbon::createFromTimestamp($user->online)->diffForHumans() }}</p>
        </div>
        
        <div class="user-stats">
            <div class="user-stat big">
                <p class="user-stat-title"><a href="{{ route('profile.followers', $user->username) }}">{{ $followersCount ?? 0 }}</a></p>
                <p class="user-stat-text">{{ __('messages.Followers') }}</p>
            </div>
            <div class="user-stat big">
                <p class="user-stat-title">{{ $followingCount ?? 0 }}</p>
                <p class="user-stat-text">{{ __('messages.following') }}</p>
            </div>
            <div class="user-stat big">
                <p class="user-stat-title">{{ $postsCount ?? 0 }}</p>
                <p class="user-stat-text">{{ __('messages.Posts') }}</p>
            </div>
        </div>

        <div class="profile-header-info-actions">
            @if(Auth::check() && Auth::id() == $user->id)
                <a class="profile-header-info-action button secondary" href="{{ route('profile.edit') }}" style="color: #fff;">
                    <span class="hide-text-mobile">{{ __('messages.edit') }}</span>&nbsp;<i class="fa fa-edit" aria-hidden="true"></i>
                </a>
            @else
                @if($isFollowing)
                    <form action="{{ route('profile.follow', $user->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="profile-header-info-action button tertiary">
                            <span class="hide-text-mobile">{{ __('messages.unfollow') }}</span>&nbsp;<i class="fa fa-user-times" aria-hidden="true"></i>
                        </button>
                    </form>
                @else
                    <form action="{{ route('profile.follow', $user->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="profile-header-info-action button secondary" style="color: #fff;">
                            <span class="hide-text-mobile">{{ __('messages.follow') }}</span>&nbsp;<i class="fa fa-user-plus" aria-hidden="true"></i>
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- SECTION NAVIGATION -->
@include('theme::profile.navigation')
<!-- /SECTION NAVIGATION -->

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        @include('theme::partials.widgets', ['position' => 'left'])
    </div>
    <div class="grid-column">
        <div class="section-header">
            <div class="section-header-info">
                <h2 class="section-title">{{ __('messages.following') }}</h2>
            </div>
        </div>
        
        <div class="grid">
            @forelse($following as $follow)
                @include('theme::profile.partials.user_preview_landscape', [
                    'targetUser' => $follow->targetUser,
                    'actionTime' => $follow->date ?? $follow->time_t
                ])
            @empty
                <div class="widget-box">
                    <p class="widget-box-title">{{ __('messages.no_following') }}</p>
                </div>
            @endforelse
        </div>
        {{ $following->links() }}
    </div>
    <div class="grid-column">
        @include('theme::partials.widgets', ['place' => 8])
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof initHexagons === 'function') {
            initHexagons();
        }
    });
</script>
@endpush
