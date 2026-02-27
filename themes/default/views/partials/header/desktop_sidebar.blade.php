<header class="header">
    <div class="header-actions">
        <div class="header-brand">
            <div class="logo">
                <a href="{{ url('/') }}"><img src="{{ theme_asset('img/logo_w.png') }}" width="40" alt="logo"></a>
            </div>
            <h1 class="header-brand-text">{{ $site_settings->titer ?? 'MyAds' }}</h1>
        </div>
    </div>

    <div class="header-actions">
        <div class="sidemenu-trigger navigation-widget-trigger">
            <svg class="icon-grid">
                <use xlink:href="#svg-grid"></use>
            </svg>
        </div>
        <div class="mobilemenu-trigger navigation-widget-mobile-trigger">
            <div class="burger-icon inverted">
                <div class="burger-icon-bar"></div>
                <div class="burger-icon-bar"></div>
                <div class="burger-icon-bar"></div>
            </div>
        </div>
        <nav class="navigation">
            <ul class="menu-main">
                @foreach($site_menus as $menu)
                    <li class="menu-main-item"><a class="menu-main-item-link" href="{{ $menu->dir }}">{{ $menu->name }}</a></li>
                @endforeach
            </ul>
        </nav>
    </div>

    <form class="header-actions search-bar" action="{{ url('/portal') }}" method="GET">
        <div class="interactive-input dark">
            <input type="text" id="search-main" name="search" placeholder="{{ __('messages.search_placeholder') }}">
            <div class="interactive-input-icon-wrap">
                <svg class="interactive-input-icon icon-magnifying-glass">
                    <use xlink:href="#svg-magnifying-glass"></use>
                </svg>
            </div>
            <div class="interactive-input-action">
                <svg class="interactive-input-action-icon icon-cross-thin">
                    <use xlink:href="#svg-cross-thin"></use>
                </svg>
            </div>
        </div>
    </form>

    <div class="header-actions">
        @auth
            <div class="progress-stat">
                <div class="bar-progress-wrap">
                    <a class="bar-progress-info" href="{{ url('/history') }}">{{ auth()->user()->pts }}&nbsp;PTS</a>
                </div>
            </div>
        @endauth

        <!-- Lang Switcher -->
        <div class="action-item-wrap">
             <div class="action-item dark header-dropdown-trigger">
                 <span style="font-size: 10px; font-weight: bold; color: #fff;">{{ strtoupper(app()->getLocale()) }}</span>
             </div>
             <div class="header-dropdown">
                 <div class="dropdown-box">
                    <div class="dropdown-box-header">
                        <p class="dropdown-box-header-title">{{ __('messages.languages') }}</p>
                    </div>
                    <div class="dropdown-box-list" style="padding: 10px;">
                        @foreach($available_languages as $lang)
                            <a class="dropdown-box-list-item" href="?lang={{ $lang->code }}">{{ $lang->name }}</a>
                        @endforeach
                    </div>
                 </div>
             </div>
        </div>

        @php
            $mode = request()->cookie('modedark', 'css');
        @endphp
        <div class="action-item-wrap">
            <button type="button" class="action-item theme-toggle {{ $mode == 'css_d' ? 'is-dark' : '' }}" aria-pressed="{{ $mode == 'css_d' ? 'true' : 'false' }}" title="{{ $mode == 'css_d' ? 'Light Mode' : 'Dark Mode' }}">
                <span class="theme-toggle-track">
                    <span class="theme-toggle-thumb"></span>
                    <i class="fa-solid fa-sun theme-toggle-icon theme-toggle-icon-light"></i>
                    <i class="fa-solid fa-moon theme-toggle-icon theme-toggle-icon-dark"></i>
                </span>
            </button>
        </div>

        <!-- Admin -->
        @if(auth()->check() && auth()->user()->isAdmin())
            <a class="action-item-wrap" href="{{ route('admin.index') }}">
                <div class="action-item dark" title="Admin CP">
                    <svg class="action-item-icon icon-private"><use xlink:href="#svg-private"></use></svg>
                </div>
            </a>
        @endif
    </div>

    @auth
        @php
            $headerUser = auth()->user();
            $headerMessages = collect();
            $headerMessageUnreadCount = 0;
            $headerNotifications = collect();
            $headerNotificationUnreadCount = 0;

            if ($headerUser) {
                $headerAllMessages = \App\Models\Message::where('us_rec', $headerUser->id)
                    ->orWhere('us_env', $headerUser->id)
                    ->orderBy('time', 'desc')
                    ->get();

                $headerPartnerIds = [];
                foreach ($headerAllMessages as $headerMessage) {
                    $headerPartnerId = $headerMessage->us_env == $headerUser->id ? $headerMessage->us_rec : $headerMessage->us_env;
                    if (!in_array($headerPartnerId, $headerPartnerIds, true)) {
                        $headerPartnerIds[] = $headerPartnerId;
                    }
                }

                $headerPartners = \App\Models\User::whereIn('id', $headerPartnerIds)->get()->keyBy('id');
                $headerUnreadPartnerIds = \App\Models\Message::where('us_rec', $headerUser->id)
                    ->where('state', '!=', 0)
                    ->groupBy('us_env')
                    ->pluck('us_env')
                    ->all();
                $headerMessageUnreadCount = count($headerUnreadPartnerIds);
                $headerUnreadMap = array_flip($headerUnreadPartnerIds);

                $headerConversations = [];
                $headerAdded = [];
                foreach ($headerAllMessages as $headerMessage) {
                    $headerPartnerId = $headerMessage->us_env == $headerUser->id ? $headerMessage->us_rec : $headerMessage->us_env;
                    if (isset($headerAdded[$headerPartnerId])) {
                        continue;
                    }
                    $headerPartner = $headerPartners->get($headerPartnerId);
                    if (!$headerPartner) {
                        continue;
                    }
                    $headerAdded[$headerPartnerId] = true;
                    $headerConversations[] = [
                        'user' => $headerPartner,
                        'message' => $headerMessage,
                        'unread' => isset($headerUnreadMap[$headerPartnerId]),
                    ];
                }

                $headerMessages = collect($headerConversations)->take(5);
                $headerNotifications = \App\Models\Notification::where('uid', $headerUser->id)
                    ->orderBy('time', 'desc')
                    ->limit(5)
                    ->get();
                $headerNotificationUnreadCount = \App\Models\Notification::where('uid', $headerUser->id)
                    ->whereIn('state', [0, 3])
                    ->count();
            }
        @endphp
        <div class="header-actions">
            <div class="action-list dark">
                <div class="action-list-item-wrap">
                    <a class="action-list-item header-dropdown-trigger {{ $headerMessageUnreadCount > 0 ? 'unread' : '' }}" href="javascript:void(0)">
                        <svg class="action-list-item-icon icon-messages">
                            <use xlink:href="#svg-messages"></use>
                        </svg>
                        @if($headerMessageUnreadCount > 0)
                            <span class="header-action-count">{{ $headerMessageUnreadCount }}</span>
                        @endif
                    </a>
                    <div class="header-dropdown">
                        <div class="dropdown-box">
                            <div class="dropdown-box-header">
                                <p class="dropdown-box-header-title">
                                    {{ __('messages.msgs') }}
                                    @if($headerMessageUnreadCount > 0)
                                        <span class="highlighted">{{ $headerMessageUnreadCount }}</span>
                                    @endif
                                </p>
                                <div class="dropdown-box-header-actions">
                                    <a class="dropdown-box-header-action" href="{{ url('/messages') }}">{{ __('messages.msgs') }}</a>
                                </div>
                            </div>
                            <div class="dropdown-box-list">
                                @forelse($headerMessages as $headerConversation)
                                    @php
                                        $headerPartner = $headerConversation['user'];
                                        $headerLastMessage = $headerConversation['message'];
                                    @endphp
                                    <a class="dropdown-box-list-item {{ $headerConversation['unread'] ? 'unread' : '' }}" href="{{ route('messages.show', $headerPartner->id) }}">
                                        <div class="user-status">
                                            <div class="user-status-avatar">
                                                <div class="user-avatar small no-outline {{ $headerPartner->isOnline() ? 'online' : 'offline' }}">
                                                    <div class="user-avatar-content">
                                                        <div class="hexagon-image-30-32" data-src="{{ $headerPartner->img ? asset($headerPartner->img) : theme_asset('img/avatar/01.jpg') }}"></div>
                                                    </div>
                                                    <div class="user-avatar-progress-border">
                                                        <div class="hexagon-border-40-44"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="user-status-title">{{ $headerPartner->username }}</p>
                                            <p class="user-status-text">{{ \Illuminate\Support\Str::limit(strip_tags($headerLastMessage->text), 60) }}</p>
                                            <p class="user-status-timestamp">{{ \Carbon\Carbon::createFromTimestamp($headerLastMessage->time)->diffForHumans() }}</p>
                                        </div>
                                    </a>
                                @empty
                                    <p class="text-center" style="padding: 18px 28px;">{{ __('messages.no_msg') }}</p>
                                @endforelse
                            </div>
                            <a class="dropdown-box-button secondary" href="{{ url('/messages') }}">{{ __('messages.msgs') }}</a>
                        </div>
                    </div>
                </div>
                <div class="action-list-item-wrap">
                    <a class="action-list-item header-dropdown-trigger {{ $headerNotificationUnreadCount > 0 ? 'unread' : '' }}" href="javascript:void(0)">
                        <svg class="action-list-item-icon icon-notification">
                            <use xlink:href="#svg-notification"></use>
                        </svg>
                        @if($headerNotificationUnreadCount > 0)
                            <span class="header-action-count">{{ $headerNotificationUnreadCount }}</span>
                        @endif
                    </a>
                    <div class="header-dropdown">
                        <div class="dropdown-box">
                            <div class="dropdown-box-header">
                                <p class="dropdown-box-header-title">
                                    {{ __('messages.notifications') }}
                                    @if($headerNotificationUnreadCount > 0)
                                        <span class="highlighted">{{ $headerNotificationUnreadCount }}</span>
                                    @endif
                                </p>
                                <div class="dropdown-box-header-actions">
                                    <a class="dropdown-box-header-action" href="{{ url('/notification') }}">{{ __('messages.notifications') }}</a>
                                </div>
                            </div>
                            <div class="dropdown-box-list">
                                @forelse($headerNotifications as $headerNotif)
                                    <a class="dropdown-box-list-item {{ $headerNotif->state == 0 || $headerNotif->state == 3 ? 'unread' : '' }}" href="{{ route('notifications.show', $headerNotif->id) }}">
                                        <div class="user-status">
                                            <div class="user-status-avatar">
                                                <div class="user-avatar small no-outline">
                                                    <div class="user-avatar-content">
                                                        <div class="hexagon-image-30-32" data-src="{{ theme_asset('img/avatar/01.jpg') }}"></div>
                                                    </div>
                                                    <div class="user-avatar-progress-border">
                                                        <div class="hexagon-border-40-44"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="user-status-title">{{ $headerNotif->name }}</p>
                                            <p class="user-status-timestamp">{{ \Carbon\Carbon::createFromTimestamp($headerNotif->time)->diffForHumans() }}</p>
                                            <div class="user-status-icon">
                                                <svg class="icon-{{ $headerNotif->logo ?: 'notification' }}">
                                                    <use xlink:href="#svg-{{ $headerNotif->logo ?: 'notification' }}"></use>
                                                </svg>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <p class="text-center" style="padding: 18px 28px;">{{ __('messages.no_notifications') }}</p>
                                @endforelse
                            </div>
                            <a class="dropdown-box-button secondary" href="{{ url('/notification') }}">{{ __('messages.notifications') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="action-item-wrap">
                <div class="action-item dark header-settings-dropdown-trigger">
                    <svg class="action-item-icon icon-settings">
                        <use xlink:href="#svg-settings"></use>
                    </svg>
                </div>
                <div class="dropdown-navigation header-settings-dropdown">
                    <p class="dropdown-navigation-category">{{ __('messages.account') }}</p>
                    <a class="dropdown-navigation-link" href="{{ route('profile.show', auth()->user()->username) }}">{{ __('messages.member_profile') }}</a>
                    <a class="dropdown-navigation-link" href="{{ route('profile.edit') }}">{{ __('messages.edit_profile') }}</a>
                    <a class="dropdown-navigation-link" href="{{ route('settings') }}">{{ __('messages.account_settings') }}</a>
                    
                    <p class="dropdown-navigation-category">{{ __('messages.ads') }}</p>
                    <a class="dropdown-navigation-link" href="{{ route('ads.banners.index') }}">{{ __('messages.list') }} {{ __('messages.bannads') }}</a>
                    <a class="dropdown-navigation-link" href="{{ route('ads.links.index') }}">{{ __('messages.list') }} {{ __('messages.textads') }}</a>
                    <a class="dropdown-navigation-link" href="{{ route('visits.index') }}">{{ __('messages.list') }} {{ __('messages.exvisit') }}</a>
                    <a class="dropdown-navigation-link" href="{{ route('ads.referrals') }}">{{ __('messages.list') }} {{ __('messages.referal') }}</a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-navigation-button button small secondary" style="width: 100%; border: none; cursor: pointer;">{{ __('messages.logout') }}</button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="header-actions">
            <a class="register-button button no-shadow" href="{{ route('login') }}">{{ __('messages.login') }}</a>
            <a class="register-button button no-shadow" href="{{ url('/register') }}">{{ __('messages.p_sign_up') }}</a>
        </div>
    @endauth
</header>
