@extends('theme::layouts.master')

@section('content')
<div class="profile-header">
    <figure class="profile-header-cover liquid" style="background: rgba(0, 0, 0, 0) url({{ asset($cover) }}) no-repeat scroll center center / cover;">
        <img src="{{ asset($cover) }}" alt="cover-{{ $user->username }}" style="display: none;">
    </figure>

    <div class="profile-header-info">
        <div class="user-short-description big">
            <a class="user-short-description-avatar user-avatar big {{ $showOnlineStatus && $user->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.show', $user->username) }}">
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

            <a class="user-short-description-avatar user-short-description-avatar-mobile user-avatar medium {{ $showOnlineStatus && $user->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.show', $user->username) }}">
                <div class="user-avatar-border">
                    <div class="hexagon-120-132" style="width: 120px; height: 132px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="120" height="132"></canvas></div>
                </div>
                <div class="user-avatar-content">
                    <div class="hexagon-image-82-90" data-src="{{ $user->avatarUrl() }}" style="width: 82px; height: 90px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="82" height="90"></canvas></div>
                </div>
                <div class="user-avatar-progress-border">
                    <div class="hexagon-border-100-110" style="width: 100px; height: 110px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="100" height="110"></canvas></div>
                </div>
            </a>

            <p class="user-short-description-title"><a href="{{ route('profile.show', $user->username) }}">{{ $user->username }}</a></p>
            @if(!empty($subscriptionProfileBadge))
                <div style="margin-top: 10px;">
                    <span class="badge" style="display: inline-flex; align-items: center; gap: 6px; background: {{ $subscriptionProfileBadge['color'] ?? '#615dfa' }}; color: #fff; border-radius: 999px; padding: 7px 14px; font-size: 12px; font-weight: 700;">
                        <i class="fa fa-crown" aria-hidden="true"></i>
                        {{ $subscriptionProfileBadge['label'] }}
                    </span>
                </div>
            @endif
            <p class="user-short-description-text">
                @if($showOnlineStatus)
                    {{ __('messages.lastcontact') }} {{ \Carbon\Carbon::createFromTimestamp($user->online)->diffForHumans() }}
                @else
                    {{ __('messages.online_status_hidden') }}
                @endif
            </p>
        </div>

        <div class="user-stats">
            <div class="user-stat big">
                <p class="user-stat-title">
                    @if($canViewFollowers)
                        <a href="{{ route('profile.followers', $user->username) }}">{{ $followersCount ?? 0 }}</a>
                    @else
                        <span>{{ $followersCount ?? 0 }}</span>
                    @endif
                </p>
                <p class="user-stat-text">{{ __('messages.Followers') }}</p>
            </div>
            <div class="user-stat big">
                <p class="user-stat-title">
                    @if($canViewFollowing)
                        <a href="{{ route('profile.following', $user->username) }}">{{ $followingCount ?? 0 }}</a>
                    @else
                        <span>{{ $followingCount ?? 0 }}</span>
                    @endif
                </p>
                <p class="user-stat-text">{{ __('messages.following') }}</p>
            </div>
            <div class="user-stat big">
                <p class="user-stat-title">{{ $postsCount ?? 0 }}</p>
                <p class="user-stat-text">{{ __('messages.Posts') }}</p>
            </div>
            @if(Auth::check() && Auth::user()->canAccessAdminModule('users'))
                <div class="user-stat big">
                    <a class="social-link patreon" href="{{ route('admin.users.edit', $user->id) }}" style="color: #fff;">
                        <i class="fa fa-edit" aria-hidden="true"></i>
                    </a>
                </div>
            @endif
        </div>

        <div class="profile-header-info-actions">
            @if(Auth::check() && Auth::id() == $user->id)
                <a class="profile-header-info-action button secondary" href="{{ route('profile.edit') }}" style="color: #fff;">
                    <span class="hide-text-mobile">{{ __('messages.edit') }}</span>&nbsp;<i class="fa fa-edit" aria-hidden="true"></i>
                </a>
                <a class="profile-header-info-action button primary" href="{{ route('profile.badges') }}">
                    <span class="hide-text-mobile">{{ __('messages.badges') }}</span>&nbsp;<i class="fa fa-trophy" aria-hidden="true"></i>
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

                @if($canSendMessage)
                    <a class="profile-header-info-action button primary" href="{{ route('messages.show', \App\Models\Message::encodeConversationRouteKey(auth()->id(), $user)) }}">
                        <span class="hide-text-mobile">{{ __('messages.send_message') }}</span>&nbsp;<i class="fa fa-envelope" aria-hidden="true"></i>
                    </a>
                @endif
            @endif
        </div>
    </div>
</div>

@include('theme::profile.navigation')

<style>
    .profile-hub-badge-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }
    .profile-hub-badge-card {
        padding: 14px 10px;
        border: 1px solid #eaeaf5;
        border-radius: 12px;
        text-align: center;
        background: linear-gradient(180deg, #fff 0%, #f9faff 100%);
    }
    .profile-hub-badge-icon {
        width: 42px;
        height: 42px;
        margin: 0 auto 10px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        background: rgba(97, 93, 250, 0.12);
        color: #615dfa;
        font-size: 18px;
    }
    .profile-photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 16px;
    }
    .profile-photo-card {
        display: block;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 18px 38px rgba(94, 92, 154, 0.12);
        background: #fff;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .profile-photo-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 26px 40px rgba(94, 92, 154, 0.18);
    }
    .profile-photo-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }
    .profile-photo-card-body {
        padding: 12px 14px 14px;
    }
    .profile-empty-state {
        text-align: center;
        padding: 40px 24px;
    }
</style>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        @if($canViewAbout)
            <div class="widget-box">
                <p class="widget-box-title">{{ __('messages.about_me') }}</p>
                <div class="widget-box-content">
                    <p class="paragraph">{{ trim((string) $user->sig) !== '' ? $user->sig : __('messages.about_me_empty') }}</p>
                </div>
            </div>
        @endif

        @if($badgeShowcase->isNotEmpty())
            <div class="widget-box" style="margin-top: 16px;">
                <p class="widget-box-title">{{ __('messages.badges') }}</p>
                <div class="widget-box-content">
                    <div class="profile-hub-badge-grid">
                        @foreach($badgeShowcase as $badgeItem)
                            @php $badge = $badgeItem->badge; @endphp
                            @if($badge)
                                <div class="profile-hub-badge-card">
                                    <div class="profile-hub-badge-icon">
                                        @if($badge->icon && str_starts_with($badge->icon, 'fa-'))
                                            <i class="fa {{ $badge->icon }}" aria-hidden="true"></i>
                                        @else
                                            <i class="fa fa-trophy" aria-hidden="true"></i>
                                        @endif
                                    </div>
                                    <p class="user-status-title" style="font-size: 14px;">{{ __('messages.' . $badge->name_key) }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @if(!empty($socialLinks))
            <div class="widget-box" style="margin-top: 16px;">
                <p class="widget-box-title">{{ __('messages.social_links') }}</p>
                <div class="widget-box-content">
                    <div style="display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; padding: 10px 0;">
                        @foreach($socialLinks as $platform => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="social-link-icon-box" title="{{ __('messages.' . $platform) ?? ucfirst($platform) }}" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-size: 20px; transition: all 0.3s ease; background: var(--section-banner-bg, #f0f2f5); color: var(--text-color, #333); border: 1px solid var(--border-color, #eee);">
                                @php
                                    $iconClass = match($platform) {
                                        'facebook' => 'fab fa-facebook-f',
                                        'twitter' => 'fab fa-x-twitter',
                                        'vkontakte' => 'fab fa-vk',
                                        'linkedin' => 'fab fa-linkedin-in',
                                        'instagram' => 'fab fa-instagram',
                                        'youtube' => 'fab fa-youtube',
                                        'threads' => 'fab fa-threads',
                                        'reddit' => 'fab fa-reddit-alien',
                                        'github' => 'fab fa-github',
                                        'adstn' => 'fa-brands fa-buysellads',
                                        'tiktok' => 'fab fa-tiktok',
                                        'discord' => 'fab fa-discord',
                                        default => 'fa fa-link',
                                    };
                                    $iconColor = match($platform) {
                                        'facebook' => '#1877f2',
                                        'twitter' => '#000000',
                                        'vkontakte' => '#0077ff',
                                        'linkedin' => '#0077b5',
                                        'instagram' => '#e4405f',
                                        'youtube' => '#ff0000',
                                        'threads' => '#000000',
                                        'reddit' => '#ff4500',
                                        'github' => '#333333',
                                        'adstn' => 'rgb(84, 56, 163)',
                                        'tiktok' => '#000000',
                                        'discord' => '#5865F2',
                                        default => 'var(--primary-color)',
                                    };
                                @endphp
                                <i class="{{ $iconClass }}" style="color: {{ $iconColor }};"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <style>
                .social-link-icon-box:hover {
                    transform: translateY(-4px);
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                    border-color: var(--primary-color) !important;
                }
            </style>
        @endif

        <x-widget-column side="profile_left" />
    </div>

    <div class="grid-column">
        @if(Auth::check() && Auth::id() == $user->id && !in_array($selectedTab, ['photos', 'about'], true))
            @include('theme::partials.status.add_post')
        @endif

        @if($selectedTab === 'about')
            <div class="widget-box">
                <p class="widget-box-title">{{ __('messages.about_me') }}</p>
                <div class="widget-box-content">
                    @if($canViewAbout)
                        <p class="paragraph" style="white-space: pre-line;">{{ trim((string) $user->sig) !== '' ? $user->sig : __('messages.about_me_empty') }}</p>
                    @else
                        <p class="text-center">{{ __('messages.section_private') }}</p>
                    @endif
                </div>
            </div>
        @elseif($selectedTab === 'photos')
            <div class="widget-box">
                <p class="widget-box-title">{{ __('messages.Photos') }}</p>
                <div class="widget-box-content">
                    @if($profileContentNotice)
                        <div class="profile-empty-state">
                            <p>{{ $profileContentNotice }}</p>
                        </div>
                    @elseif(!$canViewPhotos)
                        <div class="profile-empty-state">
                            <p>{{ __('messages.section_private') }}</p>
                        </div>
                    @elseif($photoItems->count() === 0)
                        <div class="profile-empty-state">
                            <p>{{ __('messages.no_photos_found') }}</p>
                        </div>
                    @else
                        <div class="profile-photo-grid">
                            @foreach($photoItems as $photo)
                                <a class="profile-photo-card" href="{{ $photo->post_url }}">
                                    <img src="{{ $photo->image_url }}" alt="{{ $photo->caption }}">
                                    <div class="profile-photo-card-body">
                                        <p class="user-status-title" style="font-size: 14px;">{{ $photo->caption ?: __('messages.photo_post') }}</p>
                                        <p class="user-status-text small">{{ \Carbon\Carbon::createFromTimestamp($photo->timestamp)->diffForHumans() }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        @if($photoItems->hasPages())
                            <div style="margin-top: 20px;">
                                {{ $photoItems->links('pagination::bootstrap-5') }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @else
            <div id="infinite-scroll-container" style="display: grid; grid-gap: 16px;">
                <div id="timeline-content" style="display: contents;">
                    @if($profileContentNotice)
                        <div class="widget-box" style="margin-bottom: 0;">
                            <div class="widget-box-content">
                                <p class="text-center">{{ $profileContentNotice }}</p>
                            </div>
                        </div>
                    @else
                        @forelse($activities as $activity)
                            @include('theme::partials.activity.render', ['activity' => $activity])
                        @empty
                            <div class="widget-box" style="margin-bottom: 0;">
                                <div class="widget-box-content">
                                    <p class="text-center">{{ __('messages.no_activities') }}</p>
                                </div>
                            </div>
                        @endforelse

                        @if($activities->hasPages())
                            @include('theme::partials.ajax.infinite_scroll', ['paginator' => $activities])
                        @endif
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="grid-column">
        @if($canViewAbout && trim((string) $user->sig) !== '')
            <div class="widget-box">
                <p class="widget-box-title">{{ __('messages.profile_highlights') }}</p>
                <div class="widget-box-content">
                    <div class="user-status">
                        <p class="user-status-title">{{ __('messages.about_me') }}</p>
                        <p class="user-status-text">{{ \Illuminate\Support\Str::limit(trim((string) $user->sig), 130) }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(Auth::check() && Auth::id() == $user->id)
            <div class="widget-box" style="margin-top: 16px;">
                <p class="widget-box-title">{{ __('messages.account_settings') }}</p>
                <div class="widget-box-content">
                    <a class="button secondary full" style="margin-bottom: 10px;" href="{{ route('profile.privacy') }}">{{ __('messages.privacy_settings') }}</a>
                    <a class="button primary full" href="{{ route('profile.badges') }}">{{ __('messages.manage_badges') }}</a>
                </div>
            </div>
        @endif

        <x-widget-column side="profile_right" />
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof initHexagons === 'function') {
            initHexagons();
        }
    });
</script>
@endpush
