<aside class="floaty-bar">
    @auth
        <div class="bar-actions">
            <div class="progress-stat">
                <div class="bar-progress-wrap">
                    <a class="bar-progress-info" href="{{ url('/history') }}">{{ auth()->user()->pts }}&nbsp;PTS</a>
                </div>
            </div>
        </div>
        <div class="bar-actions">
            <div class="action-list dark">
                <a class="action-list-item" href="{{ url('/portal') }}">
                    <svg class="action-list-item-icon icon-newsfeed">
                        <use xlink:href="#svg-newsfeed"></use>
                    </svg>
                </a>
                <a class="action-list-item" href="{{ url('/messages') }}">
                    <svg class="action-list-item-icon icon-messages">
                        <use xlink:href="#svg-messages"></use>
                    </svg>
                    {{-- Badge count here --}}
                </a>
                <a class="action-list-item listnotif" href="{{ url('/notification') }}">
                    <svg class="action-list-item-icon icon-notification">
                        <use xlink:href="#svg-notification"></use>
                    </svg>
                    <div id="count">
                        {{-- Notif count here --}}
                    </div>
                </a>
            </div>
            <a class="action-item-wrap" href="{{ url('/e' . auth()->id()) }}">
                <div class="action-item dark">
                    <svg class="action-item-icon icon-settings">
                        <use xlink:href="#svg-settings"></use>
                    </svg>
                </div>
            </a>
            @if(auth()->user()->isAdmin())
                <a class="action-item-wrap" href="{{ route('admin.index') }}">
                    <div class="action-item dark">
                        <svg class="action-item-icon icon-private">
                            <use xlink:href="#svg-private"></use>
                        </svg>
                    </div>
                </a>
            @endif
        </div>
    @else
        <div class="bar-actions">
            <a class="login-button button small primary" href="{{ route('login') }}">{{ __('messages.login') }}</a>&nbsp;
            <a class="login-button button small primary" href="{{ url('/register') }}">{{ __('messages.p_sign_up') }}</a>
        </div>
    @endauth
</aside>
