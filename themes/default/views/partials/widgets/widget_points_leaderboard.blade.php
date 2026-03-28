<div class="widget-box">
    <p class="widget-box-title">{{ $widget->name }}</p>
    <div class="widget-box-content">
        <div class="user-status-list">
            @php
                $topUsers = \App\Models\User::orderBy('pts', 'desc')->limit(5)->get();
            @endphp

            @foreach($topUsers as $index => $user)
                <div class="user-status request-small">
                <a class="user-status-avatar" href="{{ route('profile.short', $user->publicRouteIdentifier()) }}">
                        <div class="user-avatar small no-outline {{ $user->isOnline() ? 'online' : '' }}">
                            <div class="user-avatar-content">
                                <div class="hexagon-image-30-32" data-src="{{ $user->avatarUrl() }}"></div>
                            </div>
                            <div class="user-avatar-progress-border">
                                <div class="hexagon-border-40-44"></div>
                            </div>
                            <div class="user-avatar-badge" style="width: 20px; height: 20px; top: -5px; right: -5px;">
                                <div class="user-avatar-badge-content">
                                    <div class="hexagon-dark-16-18"></div>
                                </div>
                                <p class="user-avatar-badge-text" style="font-size: 10px;">{{ $index + 1 }}</p>
                            </div>
                        </div>
                    </a>
                    <p class="user-status-title">
                        <a class="bold" href="{{ route('profile.short', $user->publicRouteIdentifier()) }}">{{ Str::limit($user->username, 15) }}</a>
                    </p>
                    <p class="user-status-text small">
                        {{ number_format($user->pts) }} {{ __('messages.pts') }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</div>
