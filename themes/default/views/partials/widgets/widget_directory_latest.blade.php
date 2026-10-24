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
    $categoryId = (int) ($config['category_id'] ?? 0);
    $showCategory = isset($config['show_category']) ? (bool) $config['show_category'] : true;
    $showDate = isset($config['show_date']) ? (bool) $config['show_date'] : true;
    $showButton = isset($config['show_button']) ? (bool) $config['show_button'] : true;
    $buttonText = !empty($config['button_text']) ? $config['button_text'] : __('messages.see_all');

    $query = \App\Models\Directory::where('statu', 1)->latest('id');
    if ($categoryId > 0) {
        $query->where('cat', $categoryId);
    }
    $listings = $query->limit($limit)->get();
@endphp

<div class="widget-box">
    <p class="widget-box-title">{{ $widget->name }}</p>
    <div class="widget-box-content">
        <div class="user-status-list">
            @foreach($listings as $listing)
                <div class="user-status request-small">
                    <!-- SITE ICON -->
                    <div class="user-status-avatar">
                        <div class="user-avatar small no-outline">
                            <div class="user-avatar-content">
                                <div class="hexagon-image-30-32" data-src="{{ theme_asset('img/directory/category/all-01.png') }}"></div>
                            </div>
                        </div>
                    </div>
                    <p class="user-status-title">
                        <a class="bold" href="{{ route('directory.show', $listing->id) }}">{{ Str::limit($listing->name, 25) }}</a>
                    </p>
                    <p class="user-status-text small">
                        @if($showCategory)
                            {{ $listing->category?->name ?? __('messages.directory') }}
                        @endif
                        @if($showCategory && $showDate)
                            |
                        @endif
                        @if($showDate)
                            {{ \Carbon\Carbon::createFromTimestamp($listing->date)->diffForHumans() }}
                        @endif
                    </p>
                </div>
            @endforeach

            @if($listings->isEmpty())
                <p class="text-center small text-muted my-2">{{ __('messages.no_listings_found') }}</p>
            @endif
        </div>
        @if($showButton)
            <a href="{{ route('directory.index') }}" class="button primary full" style="margin-top: 20px;">{{ $buttonText }}</a>
        @endif
    </div>
</div>
