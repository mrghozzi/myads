@extends('theme::layouts.master')

@section('content')
<!-- PROFILE HEADER -->
<div class="profile-header">
    <!-- PROFILE HEADER COVER -->
    <figure class="profile-header-cover liquid" style="background: rgba(0, 0, 0, 0) url({{ asset($cover) }}) no-repeat scroll center center / cover;">
        <img src="{{ asset($cover) }}" alt="cover-{{ $user->username }}" style="display: none;">
    </figure>
    <!-- /PROFILE HEADER COVER -->

    <!-- PROFILE HEADER INFO -->
    <div class="profile-header-info">
        <!-- USER SHORT DESCRIPTION -->
        <div class="user-short-description big">
            <!-- USER SHORT DESCRIPTION AVATAR -->
            <a class="user-short-description-avatar user-avatar big {{ $user->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.show', $user->username) }}">
                <!-- USER AVATAR BORDER -->
                <div class="user-avatar-border">
                    <!-- HEXAGON -->
                    <div class="hexagon-148-164" style="width: 148px; height: 164px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="148" height="164"></canvas></div>
                    <!-- /HEXAGON -->
                </div>
                <!-- /USER AVATAR BORDER -->
            
                <!-- USER AVATAR CONTENT -->
                <div class="user-avatar-content">
                    <!-- HEXAGON -->
                    <div class="hexagon-image-100-110" data-src="{{ $user->img ? asset($user->img) : theme_asset('img/avatar/default.png') }}" style="width: 100px; height: 110px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="100" height="110"></canvas></div>
                    <!-- /HEXAGON -->
                </div>
                <!-- /USER AVATAR CONTENT -->
            
                <!-- USER AVATAR PROGRESS BORDER -->
                <div class="user-avatar-progress-border">
                    <!-- HEXAGON -->
                    <div class="hexagon-border-124-136" style="width: 124px; height: 136px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="124" height="136"></canvas></div>
                    <!-- /HEXAGON -->
                </div>
                <!-- /USER AVATAR PROGRESS BORDER -->
            
                @if($user->ucheck == 1)
                <!-- USER AVATAR BADGE -->
                <div class="user-avatar-badge">
                    <div class="user-avatar-badge-border">
                        <div class="hexagon-40-44" style="width: 22px; height: 24px; position: relative;"></div>
                    </div>
                    <div class="user-avatar-badge-content">
                        <div class="hexagon-dark-32-34" style="width: 16px; height: 18px; position: relative;"></div>
                    </div>
                    <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                </div>
                <!-- /USER AVATAR BADGE -->
                @endif
            </a>
            <!-- /USER SHORT DESCRIPTION AVATAR -->
  
            <!-- USER SHORT DESCRIPTION AVATAR (MOBILE) -->
            <a class="user-short-description-avatar user-short-description-avatar-mobile user-avatar medium {{ $user->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.show', $user->username) }}">
                <div class="user-avatar-border">
                    <div class="hexagon-120-132" style="width: 120px; height: 132px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="120" height="132"></canvas></div>
                </div>
                <div class="user-avatar-content">
                    <div class="hexagon-image-82-90" data-src="{{ $user->img ? asset($user->img) : theme_asset('img/avatar/default.png') }}" style="width: 82px; height: 90px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="82" height="90"></canvas></div>
                </div>
                <div class="user-avatar-progress-border">
                    <div class="hexagon-border-100-110" style="width: 100px; height: 110px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="100" height="110"></canvas></div>
                </div>
                @if($user->ucheck == 1)
                <div class="user-avatar-badge">
                    <div class="user-avatar-badge-border">
                        <div class="hexagon-32-34" style="width: 22px; height: 24px; position: relative;"></div>
                    </div>
                    <div class="user-avatar-badge-content">
                        <div class="hexagon-dark-26-28" style="width: 16px; height: 18px; position: relative;"></div>
                    </div>
                    <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                </div>
                @endif
            </a>
            <!-- /USER SHORT DESCRIPTION AVATAR (MOBILE) -->
    
            <!-- USER SHORT DESCRIPTION TITLE -->
            <p class="user-short-description-title"><a href="{{ route('profile.show', $user->username) }}">{{ $user->username }}</a></p>
            <!-- /USER SHORT DESCRIPTION TITLE -->
    
            <!-- USER SHORT DESCRIPTION TEXT -->
            <p class="user-short-description-text">{{ __('messages.lastcontact') }}&nbsp;{{ \Carbon\Carbon::createFromTimestamp($user->online)->diffForHumans() }}</p>
            <!-- /USER SHORT DESCRIPTION TEXT -->
        </div>
        <!-- /USER SHORT DESCRIPTION -->
  
        <!-- USER STATS -->
        <div class="user-stats">
            <div class="user-stat big">
                <p class="user-stat-title"><a href="{{ route('profile.followers', $user->username) }}">{{ $followersCount ?? 0 }}</a></p>
                <p class="user-stat-text">{{ __('messages.Followers') }}</p>
            </div>
            <div class="user-stat big">
                <p class="user-stat-title"><a href="{{ route('profile.following', $user->username) }}">{{ $followingCount ?? 0 }}</a></p>
                <p class="user-stat-text">{{ __('messages.following') }}</p>
            </div>
            <div class="user-stat big">
                <p class="user-stat-title">{{ $postsCount ?? 0 }}</p>
                <p class="user-stat-text">{{ __('messages.Posts') }}</p>
            </div>
            @if(Auth::check() && Auth::user()->isAdmin())
            <div class="user-stat big">
                <a class="social-link patreon" href="{{ url('admin/users/' . $user->id . '/edit') }}" style="color: #fff;">
                    <i class="fa fa-edit" aria-hidden="true"></i>
                </a>
            </div>
            @endif
        </div>
        <!-- /USER STATS -->
  
        <!-- PROFILE HEADER INFO ACTIONS -->
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
                    
                    @if(Auth::check())
                        <a class="profile-header-info-action button primary" href="{{ route('messages.show', $user->id) }}">
                            <span class="hide-text-mobile">{{ __('messages.send_message') }}</span>&nbsp;<i class="fa fa-envelope" aria-hidden="true"></i>
                        </a>
                    @endif
                @endif
        </div>
        <!-- /PROFILE HEADER INFO ACTIONS -->
    </div>
    <!-- /PROFILE HEADER INFO -->
</div>
<!-- /PROFILE HEADER -->

<!-- SECTION NAVIGATION -->
@include('theme::profile.navigation')
<!-- /SECTION NAVIGATION -->

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <x-widget-column side="profile_left" />
    </div>
    <div class="grid-column">
        @if(Auth::check() && Auth::id() == $user->id)
            @include('theme::partials.status.add_post')
        @endif
        
        <!-- TIMELINE ACTIVITIES -->
        <div id="infinite-scroll-container" style="display: grid; grid-gap: 16px;">
            <div id="timeline-content" style="display: contents;">
                @forelse($activities as $activity)
                    @include('theme::partials.activity.render', ['activity' => $activity])
                @empty
                    <div class="widget-box" style="margin-bottom: 0;">
                        <p class="widget-box-title">{{ __('messages.no_activities') }}</p>
                    </div>
                @endforelse
                
                @include('theme::partials.ajax.infinite_scroll', ['paginator' => $activities])
            </div>
        </div>
    </div>
    <div class="grid-column">
        <x-widget-column side="profile_right" />
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
