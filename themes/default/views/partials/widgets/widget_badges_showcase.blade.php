@php
    $showcaseBadges = [];
    if (auth()->check()) {
        $showcaseBadges = \App\Models\BadgeShowcase::where('user_id', auth()->id())
            ->with('badge')
            ->orderBy('sort_order', 'asc')
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
                <div class="badge-item" title="{{ __('messages.' . $item->badge->name_key) }}: {{ __('messages.' . $item->badge->description_key) }}" style="width: 40px; height: 40px; border-radius: 8px; background: {{ $item->badge->color ?? '#615dfa' }}; display: grid; place-items: center; color: #fff; font-size: 18px;">
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
        <a class="widget-box-button button small white" href="{{ route('profile.show', ['username' => auth()->user()->username, 'tab' => 'badges']) }}" style="margin-top: 16px;">{{ __('messages.manage_badges') ?? 'Manage Badges' }}</a>
    </div>
</div>
@endif
