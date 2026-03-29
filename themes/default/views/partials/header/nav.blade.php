<nav id="navigation-widget-small" class="navigation-widget navigation-widget-desktop closed sidebar left delayed">
    @auth
        <a class="user-avatar small no-outline {{ auth()->user()->isOnline() ? 'online' : 'offline' }}" href="{{ route('profile.short', auth()->user()->publicRouteIdentifier()) }}">
    @else
        <a class="user-avatar small" href="{{ route('login') }}">
    @endauth

    @auth
        <div class="user-avatar-content">
        <div class="hexagon-image-30-32" data-src="{{ auth()->user()->img ? url(auth()->user()->img) : theme_asset('img/avatar.jpg') }}"></div>
        </div>
        <div class="user-avatar-progress"></div>
        <div class="user-avatar-progress-border">
            <div class="hexagon-border-40-44"></div>
        </div>

        @if(auth()->user()->ucheck == 1)
            <div class="user-avatar-badge">
                <div class="user-avatar-badge-border">
                    <div class="hexagon-22-24"></div>
                </div>
                <div class="user-avatar-badge-content">
                    <div class="hexagon-dark-16-18"></div>
                </div>
                <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
            </div>
        @endif
    @endauth
    </a>

    <ul class="menu small">
        <li class="menu-item {{ Request::is('portal*', 'tag*') ? 'active' : '' }}">
            <a class="menu-item-link text-tooltip-tfr" href="{{ url('/portal') }}" data-title="{{ __('messages.community') }}">
                <svg class="menu-item-link-icon icon-newsfeed">
                    <use xlink:href="#svg-newsfeed"></use>
                </svg>
            </a>
        </li>
        @auth
            <li class="menu-item {{ Request::is('home*') ? 'active' : '' }}">
                <a class="menu-item-link text-tooltip-tfr" href="{{ url('/home') }}" data-title="{{ __('messages.board') }}">
                    <svg class="menu-item-link-icon icon-overview">
                        <use xlink:href="#svg-overview"></use>
                    </svg>
                </a>
            </li>
            <li class="menu-item {{ Request::is('quests*') ? 'active' : '' }}">
                <a class="menu-item-link text-tooltip-tfr" href="{{ url('/quests') }}" data-title="{{ __('messages.quests') }}">
                    <svg class="menu-item-link-icon icon-quests">
                        <use xlink:href="#svg-quests"></use>
                    </svg>
                </a>
            </li>
            <li class="menu-item {{ Request::is('badges*') ? 'active' : '' }}">
                <a class="menu-item-link text-tooltip-tfr" href="{{ route('badges.all') }}" data-title="{{ __('messages.badges') }}">
                    <svg class="menu-item-link-icon icon-badges">
                        <use xlink:href="#svg-badges"></use>
                    </svg>
                </a>
            </li>
        @endauth
        <li class="menu-item {{ Request::is('forum*', 'f*', 't*', 'post*', 'editor*') ? 'active' : '' }}">
            <a class="menu-item-link text-tooltip-tfr" href="{{ url('/forum') }}" data-title="{{ __('messages.forum') }}">
                <svg class="menu-item-link-icon icon-forums">
                    <use xlink:href="#svg-forums"></use>
                </svg>
            </a>
        </li>
        <li class="menu-item {{ Request::is('directory*', 'dr*', 'cat*', 'add-site*') ? 'active' : '' }}">
            <a class="menu-item-link text-tooltip-tfr" href="{{ url('/directory') }}" data-title="{{ __('messages.directory') }}">
                <svg class="menu-item-link-icon icon-list-grid-view">
                    <use xlink:href="#svg-list-grid-view"></use>
                </svg>
            </a>
        </li>
        <li class="menu-item {{ Request::is('orders*') ? 'active' : '' }}">
            <a class="menu-item-link text-tooltip-tfr" href="{{ url('/orders') }}" data-title="{{ __('messages.order_requests') }}">
                <svg class="menu-item-link-icon icon-status">
                    <use xlink:href="#svg-status"></use>
                </svg>
            </a>
        </li>
        <li class="menu-item {{ Request::is('store*', 'kb*', 'download*') ? 'active' : '' }}">
            <a class="menu-item-link text-tooltip-tfr" href="{{ url('/store') }}" data-title="{{ __('messages.store') }}">
                <svg class="menu-item-link-icon icon-marketplace">
                    <use xlink:href="#svg-marketplace"></use>
                </svg>
            </a>
        </li>
        <li class="menu-item {{ Request::is('news*') ? 'active' : '' }}">
            <a class="menu-item-link text-tooltip-tfr" href="{{ url('/news') }}" data-title="{{ __('messages.news') }}">
                <svg class="menu-item-link-icon icon-blog-posts">
                    <use xlink:href="#svg-blog-posts"></use>
                </svg>
            </a>
        </li>
        @auth
            <li class="menu-item {{ Request::is('ads*', 'promote*') ? 'active' : '' }}">
                <a class="menu-item-link text-tooltip-tfr" href="{{ route('ads.index') }}" data-title="{{ __('messages.advertising') }}">
                    <svg class="menu-item-link-icon icon-revenue">
                        <use xlink:href="#svg-revenue"></use>
                    </svg>
                </a>
            </li>
            <li class="menu-item {{ Request::is('visits*') ? 'active' : '' }}">
                <a class="menu-item-link text-tooltip-tfr" href="{{ route('visits.index') }}" data-title="{{ __('messages.exvisit') }}">
                    <svg class="menu-item-link-icon icon-timeline">
                        <use xlink:href="#svg-timeline"></use>
                    </svg>
                </a>
            </li>
        @endauth

    </ul>
</nav>
