<nav class="section-navigation" dir="{{ is_locale_rtl() ? 'rtl' : 'ltr' }}">
    <style>
        .section-navigation {
            position: relative;
        }
        .section-menu {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            scroll-behavior: smooth !important;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }
        .section-menu::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .section-menu .section-menu-item {
            flex: 0 0 auto !important;
            float: none !important;
            width: auto !important;
            min-width: 105px !important;
            padding: 0 16px !important;
            display: inline-flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            white-space: nowrap !important;
        }
        .section-menu-item-icon {
            width: 20px !important;
            height: 20px !important;
        }
        .section-navigation .slider-controls {
            pointer-events: auto;
        }
        .section-navigation .slider-controls .slider-control {
            cursor: pointer;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
    <div id="section-navigation-slider" class="section-menu">
        <a class="section-menu-item {{ ($selectedTab ?? request('tab', 'timeline')) === 'timeline' ? 'active' : '' }}" href="{{ route('profile.show', $user->username) }}">
            <svg class="section-menu-item-icon icon-timeline"><use xlink:href="#svg-timeline"></use></svg>
            <p class="section-menu-item-text">{{ __('messages.Timeline') }}</p>
        </a>
        @if(($canViewAbout ?? true) || ($selectedTab ?? request('tab')) === 'about')
            <a class="section-menu-item {{ ($selectedTab ?? request('tab')) == 'about' ? 'active' : '' }}" href="{{ route('profile.show', $user->username) }}?tab=about">
                <svg class="section-menu-item-icon icon-info"><use xlink:href="#svg-info"></use></svg>
                <p class="section-menu-item-text">{{ __('messages.about_me') }}</p>
            </a>
        @endif
        @if((($canViewPhotos ?? true) && ($canViewProfileContent ?? true)) || ($selectedTab ?? request('tab')) === 'photos')
            <a class="section-menu-item {{ ($selectedTab ?? request('tab')) == 'photos' ? 'active' : '' }}" href="{{ route('profile.show', $user->username) }}?tab=photos">
                <svg class="section-menu-item-icon icon-photos"><use xlink:href="#svg-photos"></use></svg>
                <p class="section-menu-item-text">{{ __('messages.Photos') }}</p>
            </a>
        @endif
        <a class="section-menu-item {{ request('tab') == 'videos' ? 'active' : '' }}" href="{{ route('profile.show', $user->username) }}?tab=videos">
            <svg class="section-menu-item-icon icon-videos" width="20" height="20"><use xlink:href="#svg-videos"></use></svg>
            <p class="section-menu-item-text">{{ __('messages.Videos') }}</p>
        </a>
        <a class="section-menu-item {{ request('tab') == 'audios' ? 'active' : '' }}" href="{{ route('profile.show', $user->username) }}?tab=audios">
            <svg class="section-menu-item-icon icon-play" width="20" height="20"><use xlink:href="#svg-play"></use></svg>
            <p class="section-menu-item-text">{{ __('messages.Audio') }}</p>
        </a>
        <a class="section-menu-item {{ request('tab') == 'files' ? 'active' : '' }}" href="{{ route('profile.show', $user->username) }}?tab=files">
            <svg class="section-menu-item-icon icon-files" width="20" height="20"><use xlink:href="#svg-file-custom"></use></svg>
            <p class="section-menu-item-text">{{ __('messages.Files') }}</p>
        </a>
        <a class="section-menu-item {{ request('tab') == 'clips' ? 'active' : '' }}" href="{{ route('profile.show', $user->username) }}?tab=clips">
            <svg class="section-menu-item-icon icon-clips" width="20" height="20"><use xlink:href="#svg-streams"></use></svg>
            <p class="section-menu-item-text">{{ __('messages.Clips') }}</p>
        </a>
        @if(($canViewFollowers ?? true) || request()->routeIs('profile.followers'))
            <a class="section-menu-item {{ request()->routeIs('profile.followers') ? 'active' : '' }}" href="{{ route('profile.followers', $user->username) }}">
                <svg class="section-menu-item-icon icon-friend"><use xlink:href="#svg-friend"></use></svg>
                <p class="section-menu-item-text">{{ __('messages.Followers') }}</p>
            </a>
        @endif
        @if(($canViewFollowing ?? true) || request()->routeIs('profile.following'))
            <a class="section-menu-item {{ request()->routeIs('profile.following') ? 'active' : '' }}" href="{{ route('profile.following', $user->username) }}">
                <svg class="section-menu-item-icon icon-friend"><use xlink:href="#svg-friend"></use></svg>
                <p class="section-menu-item-text">{{ __('messages.following') }}</p>
            </a>
        @endif
        <a class="section-menu-item {{ request('tab') == 'blog' ? 'active' : '' }}" href="{{ route('profile.show', $user->username) }}?tab=blog">
            <svg class="section-menu-item-icon icon-blog-posts"><use xlink:href="#svg-blog-posts"></use></svg>
            <p class="section-menu-item-text">{{ __('messages.Blog') }}</p>
        </a>
        <a class="section-menu-item {{ request('tab') == 'links' ? 'active' : '' }}" href="{{ route('profile.show', $user->username) }}?tab=links">
            <svg class="section-menu-item-icon icon-list-grid-view"><use xlink:href="#svg-list-grid-view"></use></svg>
            <p class="section-menu-item-text">{{ __('messages.directory') }}</p>
        </a>
        <a class="section-menu-item {{ request('tab') == 'store' ? 'active' : '' }}" href="{{ route('profile.show', $user->username) }}?tab=store">
            <svg class="section-menu-item-icon icon-marketplace"><use xlink:href="#svg-marketplace"></use></svg>
            <p class="section-menu-item-text">{{ __('messages.store') }}</p>
        </a>
        <a class="section-menu-item {{ request('tab') == 'forum' ? 'active' : '' }}" href="{{ route('profile.show', $user->username) }}?tab=forum">
            <svg class="section-menu-item-icon icon-forum"><use xlink:href="#svg-forum"></use></svg>
            <p class="section-menu-item-text">{{ __('messages.forum') }}</p>
        </a>
    </div>
    
    <div id="section-navigation-slider-controls" class="slider-controls">
        <div class="slider-control left"><svg class="slider-control-icon icon-small-arrow"><use xlink:href="#svg-small-arrow"></use></svg></div>
        <div class="slider-control right"><svg class="slider-control-icon icon-small-arrow"><use xlink:href="#svg-small-arrow"></use></svg></div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const navMenu = document.getElementById('section-navigation-slider');
    const controls = document.getElementById('section-navigation-slider-controls');
    if (!navMenu) return;

    // Scroll active item into view
    const activeItem = navMenu.querySelector('.section-menu-item.active');
    if (activeItem) {
        setTimeout(function () {
            activeItem.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
        }, 150);
    }

    if (controls) {
        const leftBtn = controls.querySelector('.slider-control.left');
        const rightBtn = controls.querySelector('.slider-control.right');
        const scrollAmount = 240;

        if (leftBtn) {
            leftBtn.addEventListener('click', function (e) {
                e.preventDefault();
                const isRtl = document.documentElement.getAttribute('dir') === 'rtl' || document.body.getAttribute('dir') === 'rtl' || navMenu.getAttribute('dir') === 'rtl';
                const direction = isRtl ? scrollAmount : -scrollAmount;
                navMenu.scrollBy({ left: direction, behavior: 'smooth' });
            });
        }

        if (rightBtn) {
            rightBtn.addEventListener('click', function (e) {
                e.preventDefault();
                const isRtl = document.documentElement.getAttribute('dir') === 'rtl' || document.body.getAttribute('dir') === 'rtl' || navMenu.getAttribute('dir') === 'rtl';
                const direction = isRtl ? -scrollAmount : scrollAmount;
                navMenu.scrollBy({ left: direction, behavior: 'smooth' });
            });
        }
    }
});
</script>
