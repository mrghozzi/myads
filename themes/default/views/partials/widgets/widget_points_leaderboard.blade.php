@php
    $config = [];
    if (!empty($widget?->o_valuer)) {
        if (is_array($widget->o_valuer)) {
            $config = $widget->o_valuer;
        } else {
            $decoded = json_decode((string) $widget->o_valuer, true);
            $config = is_array($decoded) ? $decoded : [];
        }
    }

    $limit = (int) ($config['limit'] ?? 5);
    $showRanks = isset($config['show_ranks']) ? (bool) $config['show_ranks'] : true;
    $showPoints = isset($config['show_points']) ? (bool) $config['show_points'] : true;
    $showButton = !empty($config['show_button']);
    $buttonText = !empty($config['button_text']) ? $config['button_text'] : (__('messages.leaderboard') ?? 'Leaderboard');

    $topUsers = \App\Models\User::orderBy('pts', 'desc')->limit($limit)->get();
@endphp

<div class="widget-box">
    <p class="widget-box-title">{{ $widget->name }}</p>
    <div class="widget-box-content">
        <div class="user-status-list">
            @foreach($topUsers as $index => $user)
                <div class="user-status request-small">
                    <a class="user-status-avatar" href="{{ route('profile.short', $user->publicRouteIdentifier()) }}">
                        <div class="user-avatar small no-outline {{ $user->isOnline() ? 'online' : '' }}">
                            <div class="user-avatar-content">
                                <div class="hexagon-image-30-32" data-src="{{ $user->avatarUrl() }}"></div>
                            </div>
                            <div class="user-avatar-progress-border">
                                <div class="hexagon-border-40-44" data-line-color="{{ $user->profileBadgeColor() }}"></div>
                            </div>
                            @if($showRanks)
                                <div class="user-avatar-badge" style="width: 20px; height: 20px; top: -5px; right: -5px;">
                                    <div class="user-avatar-badge-content">
                                        <div class="hexagon-dark-16-18"></div>
                                    </div>
                                    <p class="user-avatar-badge-text" style="font-size: 10px;">{{ $index + 1 }}</p>
                                </div>
                            @endif
                        </div>
                    </a>
                    <p class="user-status-title">
                        <a class="bold" href="{{ route('profile.short', $user->publicRouteIdentifier()) }}">{{ Str::limit($user->username, 15) }}</a>
                    </p>
                    @if($showPoints)
                        <p class="user-status-text small">
                            {{ number_format($user->pts) }} {{ __('messages.pts') }}
                        </p>
                    @endif
                </div>
            @endforeach

            @if($topUsers->isEmpty())
                <p class="text-center small text-muted my-2">{{ __('messages.no_results_found') ?? 'No users found.' }}</p>
            @endif
        </div>
        @if($showButton)
            <a href="/members?tab=leaderboard" class="button secondary full" style="margin-top: 20px;">{{ $buttonText }}</a>
        @endif
    </div>
</div>
