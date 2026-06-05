{{--
    SEO Head Partial — for standalone pages that don't use theme::layouts.master.
    Resolves SEO payload from SeoManager and outputs all meta tags, OG, Twitter,
    canonical URL, Schema.org/JSON-LD, and admin head snippets (e.g. GA4).

    Usage: @include('theme::partials._seo_head')
    The partial self-resolves $seo if the variable is not already provided by a View Composer.
--}}
@php
    $seo = $seo ?? app(\App\Services\SeoManager::class)->resolve(request());
@endphp
<meta name="robots" content="{{ $seo->robots ?? 'index,follow' }}">
@if(!empty($seo->description))
    <meta name="description" content="{{ $seo->description }}">
@endif
@if(!empty($seo->keywords))
    <meta name="keywords" content="{{ $seo->keywords }}">
@endif
@if(!empty($seo->canonical_url))
    <link rel="canonical" href="{{ $seo->canonical_url }}">
@endif
@if(!empty($seo->og))
    @foreach($seo->og as $property => $content)
        <meta property="og:{{ $property }}" content="{{ $content }}">
    @endforeach
@endif
@if(!empty($seo->twitter))
    @foreach($seo->twitter as $name => $content)
        <meta name="twitter:{{ $name }}" content="{{ $content }}">
    @endforeach
@endif
@if(!empty($seo->schema_blocks))
    @foreach($seo->schema_blocks as $schemaBlock)
        <script type="application/ld+json">{!! json_encode($schemaBlock, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endforeach
@endif
@if(!empty($seo->head_snippets))
    @foreach($seo->head_snippets as $snippet)
        {!! $snippet !!}
    @endforeach
@endif
