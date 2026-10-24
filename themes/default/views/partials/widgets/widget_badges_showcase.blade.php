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

    $maxBadges = (int) ($config['max_badges'] ?? 8);
    $badgeSize = $config['badge_size'] ?? 'medium';
    $showButton = isset($config['show_button']) ? (bool) $config['show_button'] : true;
    $guestVisibility = !empty($config['guest_visibility']);

    $sizePx = 40;
    $fontPx = 18;
    if ($badgeSize === 'small') {
        $sizePx = 32;
        $fontPx = 14;
    } elseif ($badgeSize === 'large') {
        $sizePx = 48;
        $fontPx = 22;
    }

    $showcaseBadges = [];
    if (auth()->check()) {
        $showcaseBadges = \App\Models\BadgeShowcase::where('user_id', auth()->id())
            ->with('badge')
            ->orderBy('sort_order', 'asc')
            ->limit($maxBadges)
            ->get();
    }
@endphp

@if(auth()->check() && count($showcaseBadges) > 0)
<div class="widget-box">
    <p class="widget-box-title">{{ $widget->name ?? __('messages.your_badges') }}</p>
    <div class="widget-box-content">
        <div class="badge-list" style="display: flex; flex-wrap: wrap; gap: 8px;">
            @foreach($showcaseBadges as $item)
                @if($item->badge)
                <div class="badge-item" title="{{ __('messages.' . $item->badge->name_key) }}: {{ __('messages.' . $item->badge->description_key) }}" style="width: {{ $sizePx }}px; height: {{ $sizePx }}px; border-radius: 8px; background: {{ $item->badge->color ?? '#615dfa' }}; display: grid; place-items: center; color: #fff; font-size: {{ $fontPx }}px;">
                    @if($item->badge->icon && str_contains($item->badge->icon, ' '))
                        <i class="{{ $item->badge->icon }}" aria-hidden="true"></i>
                    @elseif($item->badge->icon && str_starts_with($item->badge->icon, 'fa-'))
                        <i class="fa {{ $item->badge->icon }}" aria-hidden="true"></i>
                    @else
                        <i class="fa fa-trophy" aria-hidden="true"></i>
                    @endif
                </div>
                @endif
            @endforeach
        </div>
        @if($showButton)
            <a class="widget-box-button button small white" href="{{ route('profile.show', ['username' => auth()->user()->username, 'tab' => 'badges']) }}" style="margin-top: 16px;">{{ __('messages.manage_badges') ?? 'Manage Badges' }}</a>
        @endif
    </div>
</div>
@elseif(!auth()->check() && $guestVisibility)
<div class="widget-box">
    <p class="widget-box-title">{{ $widget->name ?? __('messages.badges') }}</p>
    <div class="widget-box-content text-center py-3">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(97, 93, 250, 0.12); color: #615dfa; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 8px;">
            <i class="fa fa-trophy"></i>
        </div>
        <p class="small text-muted mb-2">{{ __('messages.guest_visibility_prompt') ?? 'Login to unlock and showcase badges!' }}</p>
        <a class="button small primary full" href="{{ route('login') }}">{{ __('messages.login') }}</a>
    </div>
</div>
@endif
