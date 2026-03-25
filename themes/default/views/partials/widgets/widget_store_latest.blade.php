<div class="widget-box">
    <p class="widget-box-title">{{ $widget->name }}</p>
    <div class="widget-box-content">
        <div class="user-status-list">
            @php
                $products = \App\Models\Product::withoutGlobalScope('store')->where('o_type', 'store')->latest('id')->limit(5)->get();
            @endphp

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
                        {{ number_format($product->pts) }} {{ __('messages.pts') }} | {{ __('messages.seller') }}: {{ $product->user?->username ?? 'Admin' }}
                    </p>
                </div>
            @endforeach

            @if($products->isEmpty())
                <p class="text-center small">{{ __('messages.no_products_found') }}</p>
            @endif
        </div>
        <a href="{{ route('store.index') }}" class="button secondary full" style="margin-top: 20px;">{{ __('messages.see_whats_new') }}</a>
    </div>
</div>
