@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/marketplace-icon.png') }}">
    <p class="section-banner-title">{{ __('messages.store') }}</p>
    @auth
        <p class="section-banner-text"><b><i class="fa fa-gift" aria-hidden="true"></i>&nbsp;Tout Points&nbsp;:&nbsp;<font color="#339966">{{ auth()->user()->pts }}</font>&nbsp;<font face="Comic Sans MS">PTS</font></b></p>
    @endauth
</div>

<div class="section-header">
    <div class="section-header-info">
        <p class="section-pretitle">{{ __('messages.search_what_you_want') ?? 'Search what you want!' }}</p>
        <h2 class="section-title">{{ __('messages.market_categories') ?? 'Market Categories' }}</h2>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" role="alert">
        <strong><i class="fa fa-check-circle" aria-hidden="true"></i></strong>&nbsp; {{ session('success') }}
    </div>
@endif

<div class="grid grid-3-3-3 centered">
    <a class="product-category-box category-all{{ ($category ?? '') === 'script' ? ' active' : '' }}" href="{{ route('store.index', ['category' => 'script']) }}" style="--bg: url({{ theme_asset('img/banner/script.png') }}) no-repeat 100% 0, linear-gradient(90deg, #615dfa, #8d7aff); background: var(--bg);">
        <p class="product-category-box-title">{{ __('messages.script') }}</p>
        <p class="product-category-box-text">{{ $categoryCounts['script'] ?? 0 }} {{ __('messages.products') ?? 'Products' }}</p>
        <p class="product-category-box-tag">{{ $categoryCounts['script'] ?? 0 }}</p>
    </a>
    <a class="product-category-box category-featured{{ ($category ?? '') === 'themes' ? ' active' : '' }}" href="{{ route('store.index', ['category' => 'themes']) }}" style="--bg: url({{ theme_asset('img/banner/templates.png') }}) no-repeat 100% 0, linear-gradient(90deg, #417ae1, #5aafff); background: var(--bg);">
        <p class="product-category-box-title">{{ __('messages.themes') }}</p>
        <p class="product-category-box-text">{{ $categoryCounts['themes'] ?? 0 }} {{ __('messages.products') ?? 'Products' }}</p>
        <p class="product-category-box-tag">{{ $categoryCounts['themes'] ?? 0 }}</p>
    </a>
    <a class="product-category-box category-digital{{ ($category ?? '') === 'plugins' ? ' active' : '' }}" href="{{ route('store.index', ['category' => 'plugins']) }}" style="--bg: url({{ theme_asset('img/banner/plugins.png') }}) no-repeat 100% 0, linear-gradient(90deg, #2ebfef, #4ce4ff); background: var(--bg);">
        <p class="product-category-box-title">{{ __('messages.plugins') }}</p>
        <p class="product-category-box-text">{{ $categoryCounts['plugins'] ?? 0 }} {{ __('messages.products') ?? 'Products' }}</p>
        <p class="product-category-box-tag">{{ $categoryCounts['plugins'] ?? 0 }}</p>
    </a>
</div>

<div class="section-header">
    <div class="section-header-info">
        <p class="section-pretitle">{{ __('messages.see_whats_new') ?? "See what's new!" }}</p>
        <h2 class="section-title">
            @if($category ?? false)
                {{ __('messages.' . $category) }}
            @else
                {{ __('messages.latest_items') ?? 'Latest Items' }}
            @endif
        </h2>
    </div>
    <div class="section-header-actions">
        @if($category ?? false)
            <a class="button white small" role="button" href="{{ route('store.index') }}">&nbsp;<i class="fa fa-th" aria-hidden="true"></i>&nbsp;{{ __('messages.all') ?? 'All' }}&nbsp;</a>&nbsp;
        @endif
        @auth
            <a class="button secondary" role="button" href="{{ route('store.create') }}">&nbsp;&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>&nbsp;{{ __('messages.add_product') }}&nbsp;&nbsp;</a>
        @endauth
    </div>
</div>

<div class="grid grid-3-3-3-3 centered">
    @php
        $hasProducts = $products->count() > 0;
    @endphp
    @foreach($products as $product)
        @php
            $latestFile = \App\Models\ProductFile::where('o_parent', $product->id)->orderBy('id', 'desc')->first();
            $owner = $product->user;
            $ownerAvatar = $owner ? $owner->avatarUrl() : asset('upload/_avatar.png');
            $productImage = $product->product_image ?? theme_asset('img/error_plug.png');
        @endphp
        <div class="product-preview">
            <a href="{{ route('store.show', $product->name) }}">
                <figure class="product-preview-image liquid" style="background: rgba(0, 0, 0, 0) url({{ theme_asset('img/error_plug.png') }}) no-repeat scroll center center / cover;">
                    <img src="{{ $productImage }}" alt="{{ $product->name }}" style="display: none;">
                </figure>
            </a>
            <div class="product-preview-info">
                @if($product->o_order > 0)
                    <p class="text-sticker"><span class="highlighted">{{ $product->o_order }}</span> {{ __('messages.points') }}</p>
                @else
                    <p class="text-sticker">{{ __('messages.free') }}</p>
                @endif
                @if($product->is_suspended)
                    <p class="text-sticker" style="background-color: #f34141; color: #fff; margin-left: 5px;">{{ __('messages.suspended') }}</p>
                @endif
                <p class="product-preview-title"><a href="{{ route('store.show', $product->name) }}">{{ $product->name }}</a></p>
                <p class="product-preview-category digital">
                    @if($product->type)
                        <a href="#">{{ $product->type->name }}</a>
                    @endif
                </p>
                <p class="product-preview-text">{{ $product->o_valuer }}</p>
            </div>
            <div class="product-preview-meta">
                <div class="product-preview-author">
                    <a class="product-preview-author-image user-avatar micro no-border" href="{{ $owner ? route('profile.show', $owner->username) : '#' }}">
                        <div class="user-avatar-content">
                            <div class="hexagon-image-18-20" data-src="{{ $ownerAvatar }}" style="width: 18px; height: 20px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="18" height="20"></canvas></div>
                        </div>
                    </a>
                    <p class="product-preview-author-title">{{ __('messages.posted_by') ?? 'Posted By' }}</p>
                    <p class="product-preview-author-text">
                        @if($owner)
                            <a href="{{ route('profile.show', $owner->username) }}">{{ $owner->username }}</a>
                        @else
                            {{ __('messages.unknown') }}
                        @endif
                    </p>
                </div>
                <div class="rating-list">
                    <b>{{ $latestFile ? $latestFile->name : '' }}</b>
                </div>
            </div>
        </div>
    @endforeach
    @if(!$hasProducts)
        <center><pre>{{ __('messages.sieanpr') }}</pre></center>
    @endif
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.product-preview-image').forEach(function (figure) {
            var img = figure.querySelector('img');
            if (!img) return;
            var src = img.getAttribute('src');
            if (!src) {
                img.style.display = 'none';
                return;
            }
            var showImage = function () {
                img.style.display = '';
                figure.style.backgroundImage = 'none';
            };
            var hideImage = function () {
                img.style.display = 'none';
            };
            img.addEventListener('load', showImage);
            img.addEventListener('error', hideImage);
            if (img.complete) {
                if (img.naturalWidth > 0) {
                    showImage();
                } else {
                    hideImage();
                }
            }
        });
    });
</script>
@endsection
