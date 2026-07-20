@extends('theme::layouts.master')

@section('content')
<style>
    :root {
        --myads-primary: #615dfa;
        --myads-primary-hover: #4e4ac8;
        --myads-accent: #23d2e2;
        --myads-green: #4ff461;
        --myads-amber: #fbbf24;
        --myads-dark: #0f172a;
        --myads-darker: #0b1120;
        --myads-surface-light: #ffffff;
        --myads-surface-dark: #1e293b;
        --myads-text-light: #334155;
        --myads-text-dark: #f8fafc;
        --myads-text-muted: #64748b;
    }

    body.dark-mode {
        --surface-bg: var(--myads-surface-dark);
        --text-color: var(--myads-text-dark);
        --border-color: rgba(255,255,255,0.1);
        --card-bg: rgba(30, 41, 59, 0.8);
    }
    
    body:not(.dark-mode) {
        --surface-bg: var(--myads-surface-light);
        --text-color: var(--myads-text-light);
        --border-color: rgba(0,0,0,0.05);
        --card-bg: #ffffff;
    }

    .modern-store {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Modern Banner */
    .modern-banner {
        background: linear-gradient(135deg, var(--myads-primary) 0%, #8b5cf6 100%);
        border-radius: 16px;
        padding: 32px 24px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 10px 30px rgba(97, 93, 250, 0.2);
        position: relative;
        overflow: hidden;
    }

    .modern-banner::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }

    .modern-banner-icon {
        font-size: 48px;
        background: rgba(255,255,255,0.2);
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        backdrop-filter: blur(10px);
        flex-shrink: 0;
    }
    
    .modern-banner-icon img {
        width: 48px;
        height: 48px;
        object-fit: contain;
    }

    .modern-banner-content {
        flex-grow: 1;
        position: relative;
        z-index: 1;
    }

    .modern-banner-content h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .modern-banner-content p {
        margin: 4px 0 0;
        opacity: 0.9;
        font-size: 15px;
    }

    .modern-banner-action {
        position: relative;
        z-index: 1;
        background: rgba(0,0,0,0.2);
        padding: 12px 20px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        backdrop-filter: blur(5px);
        font-weight: 600;
    }
    
    /* Section Headers */
    .modern-section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 12px;
        margin-top: 8px;
    }
    
    .modern-section-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-color);
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .modern-section-pretitle {
        font-size: 13px;
        color: var(--myads-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin: 0;
    }
    
    .modern-section-actions {
        display: flex;
        gap: 8px;
    }
    
    /* Categories Grid */
    .modern-category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }
    
    .modern-category-card {
        border-radius: 16px;
        padding: 24px;
        color: #fff;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
        min-height: 120px;
    }
    
    .modern-category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.15);
        color: #fff;
    }
    
    .modern-category-card.active {
        box-shadow: 0 0 0 4px var(--surface-bg), 0 0 0 6px var(--myads-primary);
    }
    
    .modern-category-title {
        font-size: 20px;
        font-weight: 800;
        margin: 0 0 4px;
        position: relative;
        z-index: 1;
    }
    
    .modern-category-text {
        font-size: 14px;
        opacity: 0.9;
        margin: 0;
        position: relative;
        z-index: 1;
    }
    
    .modern-category-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(4px);
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 700;
        z-index: 1;
    }
    
    .cat-script { background: linear-gradient(135deg, #615dfa 0%, #8d7aff 100%); }
    .cat-themes { background: linear-gradient(135deg, #417ae1 0%, #5aafff 100%); }
    .cat-plugins { background: linear-gradient(135deg, #2ebfef 0%, #4ce4ff 100%); }
    
    .cat-bg-img {
        position: absolute;
        bottom: -10px;
        right: -10px;
        width: 100px;
        height: 100px;
        opacity: 0.3;
        z-index: 0;
        transform: rotate(-10deg);
        background-size: contain;
    }

    /* Product Grid */
    .modern-product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
    }
    
    .modern-product-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    
    .modern-product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    }
    
    .product-image-container {
        position: relative;
        width: 100%;
        padding-top: 60%; /* 16:9 aspect ratio roughly */
        background: rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .product-image-container img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .modern-product-card:hover .product-image-container img {
        transform: scale(1.05);
    }
    
    .product-badges {
        position: absolute;
        top: 12px;
        left: 12px;
        display: flex;
        gap: 6px;
        z-index: 2;
    }
    
    .prod-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .badge-price { background: #fff; color: var(--myads-dark); }
    .badge-sale { background: #ef4444; color: #fff; display: flex; align-items: center; gap: 4px;}
    .badge-free { background: var(--myads-green); color: #fff; }
    .badge-suspended { background: var(--myads-dark); color: #fff; }
    
    .product-details {
        padding: 16px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    
    .product-title {
        font-size: 16px;
        font-weight: 700;
        margin: 0 0 8px;
        color: var(--text-color);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .product-title a {
        color: inherit;
        text-decoration: none;
    }
    
    .product-category {
        font-size: 12px;
        color: var(--myads-primary);
        font-weight: 600;
        margin-bottom: 8px;
        text-transform: uppercase;
    }
    
    .product-category a {
        color: inherit;
    }
    
    .product-desc {
        font-size: 13px;
        color: var(--myads-text-muted);
        margin: 0 0 16px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex-grow: 1;
    }
    
    .product-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid var(--border-color);
        padding-top: 12px;
        margin-top: auto;
    }
    
    .product-author {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .product-author-info {
        display: flex;
        flex-direction: column;
    }
    
    .product-author-label {
        font-size: 10px;
        color: var(--myads-text-muted);
        text-transform: uppercase;
        margin: 0;
    }
    
    .product-author-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-color);
        margin: 0;
    }
    
    .product-author-name a {
        color: inherit;
    }
    
    .product-version {
        font-size: 12px;
        font-weight: 700;
        color: var(--myads-text-muted);
        background: rgba(0,0,0,0.05);
        padding: 2px 8px;
        border-radius: 4px;
    }

    /* Buttons */
    .modern-btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .modern-btn-primary {
        background: var(--myads-primary);
        color: #fff;
    }
    
    .modern-btn-primary:hover {
        background: var(--myads-primary-hover);
        color: #fff;
        transform: translateY(-1px);
    }
    
    .modern-btn-secondary {
        background: rgba(128,128,128,0.1);
        color: var(--text-color);
    }
    
    .modern-btn-secondary:hover {
        background: rgba(128,128,128,0.2);
        color: var(--text-color);
    }
    
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        background: var(--card-bg);
        border: 1px dashed var(--border-color);
        border-radius: 16px;
        color: var(--myads-text-muted);
    }
    
    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
</style>

<div class="modern-store">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert" style="border-radius: 12px;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <strong><i class="fa fa-check-circle" aria-hidden="true"></i></strong>&nbsp; {{ session('success') }}
        </div>
    @endif

    <!-- WELCOME BANNER -->
    <div class="modern-banner">
        <div class="modern-banner-icon">
            <img src="{{ theme_asset('img/banner/marketplace-icon.png') }}" alt="Store">
        </div>
        <div class="modern-banner-content">
            <h1>{{ __('messages.store') }}</h1>
            <p>{{ __('messages.store_banner_desc') ?? 'Discover themes, scripts, and plugins.' }}</p>
        </div>
        @auth
        <div class="modern-banner-action">
            <i class="fa fa-coins" style="color: var(--myads-amber);"></i>
            <span>{{ number_format((float) auth()->user()->pts, 2) }} PTS</span>
        </div>
        @endauth
    </div>

    <!-- CATEGORIES HEADER -->
    <div class="modern-section-header">
        <div class="modern-section-title-wrap">
            <p class="modern-section-pretitle">
                <a href="{{ route('store.index') }}" style="color: inherit; text-decoration: none;">{{ __('messages.search_what_you_want') ?? 'Search what you want!' }}</a>
            </p>
            <h2 class="modern-section-title">
                <a href="{{ route('store.index') }}" style="color: inherit; text-decoration: none;">{{ __('messages.market_categories') ?? 'Market Categories' }}</a>
            </h2>
        </div>
    </div>

    <!-- CATEGORIES GRID -->
    <div class="modern-category-grid">
        @php
            $isScriptSpecific = isset($scriptName) && $scriptName !== 'all';
        @endphp
        
        <a class="modern-category-card cat-script {{ ($category ?? '') === 'script' ? 'active' : '' }}" 
           href="{{ $isScriptSpecific ? route('store.script_category', [$scriptName, 'script']) : route('store.index', ['category' => 'script']) }}">
            <span class="modern-category-badge">{{ $categoryCounts['script'] ?? 0 }}</span>
            <h3 class="modern-category-title">{{ __('messages.script') }}</h3>
            <p class="modern-category-text">{{ __('messages.products') ?? 'Products' }}</p>
            <div class="cat-bg-img" style="background-image: url({{ theme_asset('img/banner/script.png') }});"></div>
        </a>
        
        <a class="modern-category-card cat-themes {{ ($category ?? '') === 'themes' ? 'active' : '' }}" 
           href="{{ $isScriptSpecific ? route('store.script_category', [$scriptName, 'themes']) : route('store.index', ['category' => 'themes']) }}">
            <span class="modern-category-badge">{{ $categoryCounts['themes'] ?? 0 }}</span>
            <h3 class="modern-category-title">{{ __('messages.themes') }}</h3>
            <p class="modern-category-text">{{ __('messages.products') ?? 'Products' }}</p>
            <div class="cat-bg-img" style="background-image: url({{ theme_asset('img/banner/templates.png') }});"></div>
        </a>
        
        <a class="modern-category-card cat-plugins {{ ($category ?? '') === 'plugins' ? 'active' : '' }}" 
           href="{{ $isScriptSpecific ? route('store.script_category', [$scriptName, 'plugins']) : route('store.index', ['category' => 'plugins']) }}">
            <span class="modern-category-badge">{{ $categoryCounts['plugins'] ?? 0 }}</span>
            <h3 class="modern-category-title">{{ __('messages.plugins') }}</h3>
            <p class="modern-category-text">{{ __('messages.products') ?? 'Products' }}</p>
            <div class="cat-bg-img" style="background-image: url({{ theme_asset('img/banner/plugins.png') }});"></div>
        </a>
    </div>

    <!-- PRODUCTS HEADER -->
    <div class="modern-section-header" style="margin-top: 16px;">
        <div class="modern-section-title-wrap">
            @php
                $pretitleUrl = $isScriptSpecific ? route('store.script_category', [$scriptName, 'all']) : route('store.index');
                $titleUrl = $isScriptSpecific 
                    ? ($category ? route('store.script_category', [$scriptName, $category]) : route('store.script_category', [$scriptName, 'all']))
                    : ($category ? route('store.index', ['category' => $category]) : route('store.index'));
            @endphp
            <p class="modern-section-pretitle">
                <a href="{{ $pretitleUrl }}" style="color: inherit; text-decoration: none;">{{ __('messages.see_whats_new') ?? "See what's new!" }}</a>
            </p>
            <h2 class="modern-section-title">
                <a href="{{ $titleUrl }}" style="color: inherit; text-decoration: none;">
                    @if($category ?? false)
                        {{ __('messages.' . $category) }}
                    @else
                        {{ __('messages.latest_items') ?? 'Latest Items' }}
                    @endif
                </a>
            </h2>
        </div>
        <div class="modern-section-actions">
            @if($category ?? false)
                @php
                    $allUrl = (isset($scriptName) && $scriptName !== 'all') 
                        ? route('store.script_category', ['script' => $scriptName, 'category' => 'all'])
                        : route('store.index');
                @endphp
                <a class="modern-btn modern-btn-secondary" href="{{ $allUrl }}"><i class="fa fa-th"></i> <span class="d-none d-sm-inline">{{ __('messages.all') ?? 'All' }}</span></a>
            @endif
            @auth
                <a class="modern-btn modern-btn-primary" href="{{ route('store.discounts.index') }}" style="background: var(--myads-primary);"><i class="fa fa-tags"></i> <span class="d-none d-md-inline">{{ __('messages.discount_codes') ?? 'Discount Codes' }}</span></a>
                <a class="modern-btn modern-btn-primary" href="{{ route('store.create') }}" style="background: var(--myads-green);"><i class="fa fa-plus"></i> <span class="d-none d-md-inline">{{ __('messages.add_product') }}</span></a>
            @endauth
        </div>
    </div>

    <!-- PRODUCTS GRID -->
    <div class="modern-product-grid">
        @php
            $hasProducts = $products->count() > 0;
        @endphp
        
        @foreach($products as $product)
            @php
                $latestFile = \App\Models\ProductFile::where('o_parent', $product->id)->orderBy('id', 'desc')->first();
                $owner = $product->user;
                $ownerAvatar = $owner ? $owner->avatarUrl() : asset('upload/_avatar.png');
                $productImage = $product->product_image ?? theme_asset('img/error_plug.png');
                $prodScript = $product->associated_script_name;
                $catName = $product->type ? $product->type->name : null;
                $categoryLink = '#';
                if ($catName) {
                    $targetScript = $isScriptSpecific ? $scriptName : $prodScript;
                    $categoryLink = $targetScript 
                        ? route('store.script_category', [$targetScript, $catName])
                        : route('store.index', ['category' => $catName]);
                }
            @endphp
            <div class="modern-product-card">
                <a href="{{ route('store.show', $product->name) }}" class="product-image-container">
                    <div class="product-badges">
                        @if($product->o_order > 0)
                            @if($product->sale && $product->sale->is_active)
                                <span class="prod-badge badge-sale">
                                    <i class="fa-solid fa-tags"></i>
                                    <span style="text-decoration: line-through; opacity: 0.8; font-size: 9px;">{{ $product->o_order }}</span>
                                    <span>{{ $product->sale->sale_price }} {{ __('messages.points') }}</span>
                                </span>
                            @else
                                <span class="prod-badge badge-price">{{ $product->o_order }} {{ __('messages.points') }}</span>
                            @endif
                        @else
                            <span class="prod-badge badge-free">{{ __('messages.free') }}</span>
                        @endif
                        
                        @if($product->is_suspended)
                            <span class="prod-badge badge-suspended">{{ __('messages.suspended') }}</span>
                        @endif
                    </div>
                    <img src="{{ $productImage }}" alt="{{ $product->name }}" class="product-img-render" onerror="this.src='{{ theme_asset('img/error_plug.png') }}'">
                </a>
                <div class="product-details">
                    @if($catName)
                        <div class="product-category">
                            <a href="{{ $categoryLink }}">{{ __('messages.' . $catName) ?? $catName }}</a>
                        </div>
                    @endif
                    <h3 class="product-title">
                        <a href="{{ route('store.show', $product->name) }}">{{ $product->name }}</a>
                    </h3>
                    <p class="product-desc">{{ $product->o_valuer }}</p>
                    
                    <div class="product-footer">
                        <div class="product-author">
                            <a href="{{ $owner ? route('profile.show', $owner->username) : '#' }}" class="user-avatar micro no-border">
                                <div class="hexagon-image-18-20" data-src="{{ $ownerAvatar }}" style="width: 18px; height: 20px; position: relative;"><canvas style="position: absolute; top: 0px; left: 0px;" width="18" height="20"></canvas></div>
                            </a>
                            <div class="product-author-info">
                                <p class="product-author-label">{{ __('messages.posted_by') ?? 'Posted By' }}</p>
                                <p class="product-author-name">
                                    @if($owner)
                                        <a href="{{ route('profile.show', $owner->username) }}">{{ $owner->username }}</a>
                                    @else
                                        {{ __('messages.unknown') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($latestFile)
                            <span class="product-version" title="Latest version">{{ $latestFile->name }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
        
        @if(!$hasProducts)
            <div class="empty-state">
                <i class="fa-solid fa-box-open"></i>
                <h3>{{ __('messages.no_products_found') ?? 'No products found' }}</h3>
            </div>
        @endif
    </div>
</div>
@endsection
