<nav id="navigation-widget" class="navigation-widget navigation-widget-desktop sidebar left hidden" data-simplebar>
    <ul class="menu">
        <li class="menu-item {{ Request::is('portal*', 'tag*') ? 'active' : '' }}">
            <a class="menu-item-link text-tooltip-tfr" href="{{ url('/portal') }}">
                <svg class="menu-item-link-icon icon-newsfeed">
                    <use xlink:href="#svg-newsfeed"></use>
                </svg>
                {{ __('messages.community') }}
            </a>
        </li>
        @auth
            <li class="menu-item {{ Request::is('home*') ? 'active' : '' }}">
                <a class="menu-item-link text-tooltip-tfr" href="{{ url('/home') }}">
                    <svg class="menu-item-link-icon icon-overview">
                        <use xlink:href="#svg-overview"></use>
                    </svg>
                    {{ __('messages.board') }}
                </a>
            </li>
            <li class="menu-item {{ Request::is('quests*') ? 'active' : '' }}">
                <a class="menu-item-link text-tooltip-tfr" href="{{ url('/quests') }}">
                    <svg class="menu-item-link-icon icon-quests">
                        <use xlink:href="#svg-quests"></use>
                    </svg>
                    {{ __('messages.quests') }}
                </a>
            </li>
            <li class="menu-item {{ Request::is('badges*') ? 'active' : '' }}">
                <a class="menu-item-link text-tooltip-tfr" href="{{ route('badges.all') }}">
                    <svg class="menu-item-link-icon icon-badges">
                        <use xlink:href="#svg-badges"></use>
                    </svg>
                    {{ __('messages.badges') }}
                </a>
            </li>
        @endauth
        <li class="menu-item {{ Request::is('forum*', 'f*', 't*', 'post*', 'editor*') ? 'active' : '' }}">
            <a class="menu-item-link text-tooltip-tfr" href="{{ url('/forum') }}">
                <svg class="menu-item-link-icon icon-forums">
                    <use xlink:href="#svg-forums"></use>
                </svg>
                {{ __('messages.forum') }}
            </a>
        </li>
        <li class="menu-item {{ Request::is('directory*', 'dr*', 'cat*', 'add-site*') ? 'active' : '' }}">
            <a class="menu-item-link text-tooltip-tfr" href="{{ url('/directory') }}">
                <svg class="menu-item-link-icon icon-list-grid-view">
                    <use xlink:href="#svg-list-grid-view"></use>
                </svg>
                {{ __('messages.directory') }}
            </a>
        </li>
        <li class="menu-item {{ Request::is('orders*') ? 'active' : '' }}">
            <a class="menu-item-link text-tooltip-tfr" href="{{ url('/orders') }}">
                <svg class="menu-item-link-icon icon-status">
                    <use xlink:href="#svg-status"></use>
                </svg>
                {{ __('messages.order_requests') }}
            </a>
        </li>
        <li class="menu-item {{ Request::is('store*', 'kb*', 'download*') ? 'active' : '' }}">
            <a class="menu-item-link text-tooltip-tfr" href="{{ url('/store') }}">
                <svg class="menu-item-link-icon icon-marketplace">
                    <use xlink:href="#svg-marketplace"></use>
                </svg>
                {{ __('messages.store') }}
            </a>
        </li>
        <li class="menu-item {{ Request::is('news*') ? 'active' : '' }}">
            <a class="menu-item-link text-tooltip-tfr" href="{{ url('/news') }}">
                <svg class="menu-item-link-icon icon-blog-posts">
                    <use xlink:href="#svg-blog-posts"></use>
                </svg>
                {{ __('messages.news') }}
            </a>
        </li>
        @auth
        <li class="menu-item {{ Request::is('ads*', 'promote*') ? 'active' : '' }}">
            <a class="menu-item-link text-tooltip-tfr" href="{{ route('ads.index') }}">
                <svg class="menu-item-link-icon icon-megaphone">
                    <use xlink:href="#svg-megaphone"></use>
                </svg>
                {{ __('messages.advertising') }}
            </a>
        </li>
        @endauth

    </ul>
    <ul class="menu">
        @foreach($site_menus as $menu)
            <li class="menu-item"><a class="menu-item-link text-tooltip-tfr" href="{{ $menu->dir }}">{{ $menu->name }}</a></li>
        @endforeach
    </ul>
</nav>
