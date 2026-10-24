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
    $showPrice = isset($config['show_price']) ? (bool) $config['show_price'] : true;
    $showSeller = isset($config['show_seller']) ? (bool) $config['show_seller'] : true;
    $showButton = isset($config['show_button']) ? (bool) $config['show_button'] : true;
    $buttonText = !empty($config['button_text']) ? $config['button_text'] : __('messages.see_whats_new');

    $products = \App\Models\Product::withoutGlobalScope('store')->where('o_type', 'store')->latest('id')->limit($limit)->get();
@endphp

<div class="widget-box">
    <p class="widget-box-title">{{ $widget->name }}</p>
    <div class="widget-box-content">
        <div class="user-status-list">
            @foreach($products as $product)
                <div class="user-status request-small">
                    <!-- PRODUCT IMAGE / ICON -->
                    <div class="user-status-avatar">
                        <div class="user-avatar small no-outline">
                            <div class="user-avatar-content">
                                <div class="hexagon-image-30-32" data-src="{{ theme_asset('img/marketplace/category/all-01.png') }}"></div>
                            </div>
                        </div>
                    </div>
                    <p class="user-status-title">
                        <a class="bold" href="{{ route('store.show', $product->name) }}">{{ Str::limit($product->name, 20) }}</a>
                    </p>
                    <p class="user-status-text small">
                        @if($showPrice)
                            {{ number_format($product->pts) }} {{ __('messages.pts') }}
                        @endif
                        @if($showPrice && $showSeller)
                            |
                        @endif
                        @if($showSeller)
                            {{ __('messages.seller') }}: {{ $product->user?->username ?? 'Admin' }}
                        @endif
                    </p>
                </div>
            @endforeach

            @if($products->isEmpty())
                <p class="text-center small text-muted my-2">{{ __('messages.no_products_found') }}</p>
            @endif
        </div>
        @if($showButton)
            <a href="{{ route('store.index') }}" class="button secondary full" style="margin-top: 20px;">{{ $buttonText }}</a>
        @endif
    </div>
</div>
