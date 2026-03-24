<nav id="navigation-widget-mobile" class="navigation-widget navigation-widget-mobile sidebar left hidden" data-simplebar>
    <div class="navigation-widget-close-button">
        <svg class="navigation-widget-close-button-icon icon-back-arrow">
            <use xlink:href="#svg-back-arrow"></use>
        </svg>
    </div>
    @auth
        <div class="navigation-widget-info-wrap">
            <div class="navigation-widget-info">
                <a class="user-avatar small no-outline" href="{{ url('/u/' . auth()->id()) }}">
                    <div class="user-avatar-content">
                        <div class="hexagon-image-30-32" data-src="{{ auth()->user()->img ? url(auth()->user()->img) : theme_asset('img/avatar.jpg') }}"></div>
                    </div>
                    <div class="user-avatar-progress-border">
                        <div class="hexagon-border-40-44"></div>
                    </div>
                </a>
                <p class="navigation-widget-info-title"><a href="{{ url('/u/' . auth()->id()) }}">{{ auth()->user()->username }}</a></p>
                <p class="navigation-widget-info-text">{{ __('messages.welcome_back') }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="navigation-widget-info-button button small secondary">{{ __('messages.logout') }}</button>
            </form>
        </div>
    @else
        <div class="navigation-widget-info-wrap">
            <a href="{{ route('login') }}" class="navigation-widget-info-button button small secondary">{{ __('messages.login') }}</a>
            <a href="{{ url('/register') }}" class="navigation-widget-info-button button small secondary">{{ __('messages.sign_up') }}</a>
        </div>
    @endauth

    <p class="navigation-widget-section-title">{{ __('messages.sections') }}</p>
    <ul class="menu">
        <li class="menu-item"><a class="menu-item-link text-tooltip-tfr" href="{{ url('/portal') }}"><svg class="menu-item-link-icon icon-newsfeed"><use xlink:href="#svg-newsfeed"></use></svg>{{ __('messages.community') }}</a></li>
        @auth
        <li class="menu-item active"><a class="menu-item-link text-tooltip-tfr" href="{{ url('/home') }}"><svg class="menu-item-link-icon icon-overview"><use xlink:href="#svg-overview"></use></svg>{{ __('messages.board') }}</a></li>
        @endauth
        <li class="menu-item"><a class="menu-item-link text-tooltip-tfr" href="{{ url('/forum') }}"><svg class="menu-item-link-icon icon-forums"><use xlink:href="#svg-forums"></use></svg>{{ __('messages.forum') }}</a></li>
        <li class="menu-item"><a class="menu-item-link text-tooltip-tfr" href="{{ url('/directory') }}"><svg class="menu-item-link-icon icon-list-grid-view"><use xlink:href="#svg-list-grid-view"></use></svg>{{ __('messages.directory') }}</a></li>
        <li class="menu-item"><a class="menu-item-link text-tooltip-tfr" href="{{ url('/orders') }}"><svg class="menu-item-link-icon icon-list-grid-view"><use xlink:href="#svg-list-grid-view"></use></svg>{{ __('messages.order_requests') }}</a></li>
        <li class="menu-item"><a class="menu-item-link text-tooltip-tfr" href="{{ url('/store') }}"><svg class="menu-item-link-icon icon-marketplace"><use xlink:href="#svg-marketplace"></use></svg>{{ __('messages.store') }}</a></li>
    </ul>

    @auth
        <p class="navigation-widget-section-title">{{ __('messages.account') }}</p>
        <a class="navigation-widget-section-link" href="{{ url('/e' . auth()->id()) }}">{{ __('messages.e_profile') }}</a>
        <a class="navigation-widget-section-link" href="{{ url('/p' . auth()->id()) }}">{{ __('messages.change_avatar_cover') }}</a>
        <a class="navigation-widget-section-link" href="{{ url('/options') }}">{{ __('messages.options') }}</a>

        @if(auth()->user()->hasAdminAccess())
            <p class="navigation-widget-section-title">{{ __('messages.mode_admin') }}</p>
            <a class="navigation-widget-section-link" href="{{ route('admin.index') }}">{{ __('messages.activate') }}</a>
        @endif
    @endauth

    <p class="navigation-widget-section-title">{{ __('messages.main_links') }}</p>
    @foreach($site_menus as $menu)
        <a class="navigation-widget-section-link" href="{{ $menu->dir }}">{{ $menu->name }}</a>
    @endforeach
</nav>
