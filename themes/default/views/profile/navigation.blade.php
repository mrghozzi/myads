<nav class="section-navigation">
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
