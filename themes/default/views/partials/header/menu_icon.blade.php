@switch($icon ?? '')
    @case('directory')
        <svg class="menu-item-link-icon menu-fallback-icon icon-directory" viewBox="0 0 20 20" width="20" height="20" aria-hidden="true" focusable="false">
            <use xlink:href="#svg-directory-fallback"></use>
        </svg>
        @break

    @case('orders')
        <svg class="menu-item-link-icon menu-fallback-icon icon-orders" viewBox="0 0 20 20" width="20" height="20" aria-hidden="true" focusable="false">
            <use xlink:href="#svg-orders-fallback"></use>
        </svg>
        @break

    @case('news')
        <svg class="menu-item-link-icon menu-fallback-icon icon-news" viewBox="0 0 20 20" width="20" height="20" aria-hidden="true" focusable="false">
            <use xlink:href="#svg-news-fallback"></use>
        </svg>
        @break

    @case('megaphone')
        <svg class="menu-item-link-icon menu-fallback-icon icon-megaphone" viewBox="0 0 20 20" width="20" height="20" aria-hidden="true" focusable="false">
            <use xlink:href="#svg-megaphone-fallback"></use>
        </svg>
        @break
@endswitch
