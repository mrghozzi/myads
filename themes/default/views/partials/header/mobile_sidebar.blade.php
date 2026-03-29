<nav id="navigation-widget-mobile" class="navigation-widget navigation-widget-mobile sidebar left hidden" data-simplebar>
    <div class="navigation-widget-close-button">
        <svg class="navigation-widget-close-button-icon icon-back-arrow">
            <use xlink:href="#svg-back-arrow"></use>
        </svg>
    </div>
    @auth
        <div class="navigation-widget-info-wrap">
            <div class="navigation-widget-info">
                <a class="user-avatar small no-outline" href="{{ route('profile.short', auth()->user()->publicRouteIdentifier()) }}">
                    <div class="user-avatar-content">
                        <div class="hexagon-image-30-32" data-src="{{ auth()->user()->img ? url(auth()->user()->img) : theme_asset('img/avatar.jpg') }}"></div>
                    </div>
                    <div class="user-avatar-progress-border">
                        <div class="hexagon-border-40-44"></div>
                    </div>
                </a>
                <p class="navigation-widget-info-title"><a href="{{ route('profile.short', auth()->user()->publicRouteIdentifier()) }}">{{ auth()->user()->username }}</a></p>
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
        <li class="menu-item {{ Request::is('home') ? 'active' : '' }}"><a class="menu-item-link text-tooltip-tfr" href="{{ url('/home') }}"><svg class="menu-item-link-icon icon-overview"><use xlink:href="#svg-overview"></use></svg>{{ __('messages.board') }}</a></li>
        <li class="menu-item {{ Request::is('quests') ? 'active' : '' }}"><a class="menu-item-link text-tooltip-tfr" href="{{ url('/quests') }}"><svg class="menu-item-link-icon icon-quests"><use xlink:href="#svg-quests"></use></svg>{{ __('messages.quests') }}</a></li>
        <li class="menu-item {{ Request::is('badges') ? 'active' : '' }}"><a class="menu-item-link text-tooltip-tfr" href="{{ route('badges.all') }}"><svg class="menu-item-link-icon icon-badges"><use xlink:href="#svg-badges"></use></svg>{{ __('messages.badges') }}</a></li>
        @endauth
        <li class="menu-item"><a class="menu-item-link text-tooltip-tfr" href="{{ url('/forum') }}"><svg class="menu-item-link-icon icon-forums"><use xlink:href="#svg-forums"></use></svg>{{ __('messages.forum') }}</a></li>
        <li class="menu-item"><a class="menu-item-link text-tooltip-tfr" href="{{ url('/directory') }}"><svg class="menu-item-link-icon icon-list-grid-view"><use xlink:href="#svg-list-grid-view"></use></svg>{{ __('messages.directory') }}</a></li>
        <li class="menu-item"><a class="menu-item-link text-tooltip-tfr" href="{{ url('/orders') }}"><svg class="menu-item-link-icon icon-status"><use xlink:href="#svg-status"></use></svg>{{ __('messages.order_requests') }}</a></li>
        <li class="menu-item"><a class="menu-item-link text-tooltip-tfr" href="{{ url('/news') }}"><svg class="menu-item-link-icon icon-newsfeed"><use xlink:href="#svg-newsfeed"></use></svg>{{ __('messages.news') }}</a></li>
        @auth
        <li class="menu-item"><a class="menu-item-link text-tooltip-tfr" href="{{ route('ads.index') }}"><svg class="menu-item-link-icon icon-revenue"><use xlink:href="#svg-revenue"></use></svg>{{ __('messages.advertising') }}</a></li>
        <li class="menu-item"><a class="menu-item-link text-tooltip-tfr" href="{{ route('visits.index') }}"><svg class="menu-item-link-icon icon-timeline"><use xlink:href="#svg-timeline"></use></svg>{{ __('messages.exvisit') }}</a></li>
        @endauth
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

    <p class="navigation-widget-section-title">{{ __('messages.preferences') ?? 'Preferences' }}</p>
    <div class="navigation-widget-section-link" style="display: flex; align-items: center; justify-content: space-between; padding: 10px 20px;">
        @php
            $mode = request()->cookie('modedark', 'css');
        @endphp
        <span>{{ $mode == 'css_d' ? __('messages.light_mode') ?? 'Light Mode' : __('messages.dark_mode') ?? 'Dark Mode' }}</span>
        <button type="button" class="action-item theme-toggle {{ $mode == 'css_d' ? 'is-dark' : '' }}" aria-pressed="{{ $mode == 'css_d' ? 'true' : 'false' }}" title="{{ $mode == 'css_d' ? 'Light Mode' : 'Dark Mode' }}" style="transform: scale(0.8); margin: 0;">
            <span class="theme-toggle-track">
                <span class="theme-toggle-thumb"></span>
                <i class="fa-solid fa-sun theme-toggle-icon theme-toggle-icon-light"></i>
                <i class="fa-solid fa-moon theme-toggle-icon theme-toggle-icon-dark"></i>
            </span>
        </button>
    </div>
    
    <div class="navigation-widget-section-link" style="display: flex; align-items: center; justify-content: space-between; padding: 10px 20px; cursor: pointer;" onclick="document.getElementById('mobile-lang-list').classList.toggle('hidden')">
        <span>{{ __('messages.languages') }}</span>
        <span class="highlighted" style="font-weight: bold; font-size: 0.8rem;">{{ strtoupper(app()->getLocale()) }}</span>
    </div>
    <div id="mobile-lang-list" class="hidden" style="padding-left: 20px; background: rgba(0,0,0,0.05);">
        @foreach($available_languages as $lang)
            <a class="navigation-widget-section-link {{ app()->getLocale() == $lang->code ? 'active' : '' }}" href="?lang={{ $lang->code }}" style="padding-left: 20px; font-size: 0.85rem;">
                {{ $lang->name }}
                @if(app()->getLocale() == $lang->code)
                    <i class="fa fa-check-circle" style="margin-left: 5px; font-size: 0.7rem;"></i>
                @endif
            </a>
        @endforeach
    </div>

    <p class="navigation-widget-section-title">{{ __('messages.main_links') }}</p>
    @foreach($site_menus as $menu)
        <a class="navigation-widget-section-link" href="{{ $menu->dir }}">{{ $menu->name }}</a>
    @endforeach
</nav>
