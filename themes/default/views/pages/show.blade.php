@extends('theme::layouts.master')

@section('title', $page->title . ' - ' . ($site_settings->titer ?? 'MyAds'))

@push('head')
<style>
    .dynamic-page-grid .dynamic-page-column .widget-box {
        border-radius: 16px;
        border: 1px solid rgba(97, 93, 250, 0.08);
        box-shadow: 0 18px 40px rgba(94, 92, 154, 0.09);
        overflow: hidden;
    }

    .dynamic-page-grid.single-content .dynamic-page-column {
        width: 100%;
        max-width: 980px;
        margin-inline: auto;
    }

    .dynamic-page {
        --dp-primary: #615dfa;
        --dp-heading: #1e1f33;
        --dp-body: #4b5563;
        --dp-muted: #8f91ac;
        --dp-border: #e7e8ee;
        --dp-soft: #f8f8fb;
        --dp-quote: #f0f1ff;
        color: var(--dp-body);
        padding: 26px 30px 24px;
    }

    .dynamic-page h1,
    .dynamic-page .page-body h2,
    .dynamic-page .page-body h3,
    .dynamic-page .page-body h4 {
        font-family: "Comfortaa", sans-serif;
    }

    .dynamic-page .dynamic-page-head {
        padding-bottom: 22px;
        margin-bottom: 24px;
        border-bottom: 1px solid var(--dp-border);
    }

    .dynamic-page .dynamic-page-breadcrumb {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        margin: 0 0 10px;
        padding: 0;
        list-style: none;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: var(--dp-muted);
    }

    .dynamic-page .dynamic-page-breadcrumb li {
        display: inline-flex;
        align-items: center;
    }

    .dynamic-page .dynamic-page-breadcrumb li + li::before {
        content: "/";
        margin-inline: 8px;
        color: var(--dp-muted);
    }

    .dynamic-page .dynamic-page-breadcrumb a {
        color: var(--dp-primary);
    }

    .dynamic-page .dynamic-page-breadcrumb a:hover {
        text-decoration: underline;
    }

    .dynamic-page h1 {
        margin: 0;
        color: var(--dp-heading);
        font-size: 2rem;
        line-height: 1.22;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .dynamic-page .dynamic-page-meta {
        margin-top: 12px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px 12px;
        color: var(--dp-muted);
        font-size: 12px;
        font-weight: 600;
    }

    .dynamic-page .dynamic-page-meta span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        background: var(--dp-soft);
        border: 1px solid var(--dp-border);
        border-radius: 999px;
        max-width: 100%;
    }

    .dynamic-page .dynamic-page-meta i {
        color: var(--dp-primary);
    }

    .dynamic-page .page-body {
        font-size: 1.02rem;
        line-height: 1.9;
        color: var(--dp-body);
        overflow-wrap: anywhere;
    }

    .dynamic-page .page-body > *:first-child {
        margin-top: 0;
    }

    .dynamic-page .page-body > *:last-child {
        margin-bottom: 0;
    }

    .dynamic-page .page-body h2,
    .dynamic-page .page-body h3,
    .dynamic-page .page-body h4 {
        color: var(--dp-heading);
        line-height: 1.35;
        font-weight: 700;
        margin-top: 2.2rem;
        margin-bottom: .95rem;
    }

    .dynamic-page .page-body h2 {
        font-size: 1.42rem;
        position: relative;
        padding-inline-start: 14px;
    }

    .dynamic-page .page-body h2::before {
        content: "";
        position: absolute;
        inset-inline-start: 0;
        top: .22em;
        width: 4px;
        height: 1.08em;
        background: var(--dp-primary);
        border-radius: 999px;
    }

    .dynamic-page .page-body h3 {
        font-size: 1.2rem;
    }

    .dynamic-page .page-body h4 {
        font-size: 1.08rem;
    }

    .dynamic-page .page-body p {
        margin-top: 0;
        margin-bottom: 1.1rem;
    }

    .dynamic-page .page-body a {
        color: var(--dp-primary);
        text-decoration: underline;
        text-underline-offset: 2px;
        text-decoration-thickness: 1.5px;
    }

    .dynamic-page .page-body ul,
    .dynamic-page .page-body ol {
        margin: 1rem 0 1.35rem;
        padding-inline-start: 1.45rem;
    }

    .dynamic-page .page-body li + li {
        margin-top: .34rem;
    }

    .dynamic-page .page-body li::marker {
        color: var(--dp-primary);
    }

    .dynamic-page .page-body blockquote {
        margin: 1.9rem 0;
        padding: 1.3rem 1.4rem 1.3rem 1.2rem;
        border-inline-start: 4px solid var(--dp-primary);
        background: var(--dp-quote);
        border-radius: 0 10px 10px 0;
        color: var(--dp-heading);
        font-style: italic;
    }

    [dir="rtl"] .dynamic-page .page-body blockquote {
        border-inline-start: none;
        border-inline-end: 4px solid var(--dp-primary);
        border-radius: 10px 0 0 10px;
    }

    .dynamic-page .page-body code {
        background: var(--dp-soft);
        border: 1px solid var(--dp-border);
        color: var(--dp-heading);
        border-radius: 6px;
        padding: 2px 6px;
        font-size: .88em;
    }

    .dynamic-page .page-body pre {
        margin: 1.6rem 0;
        background: #1e1f33;
        color: #e2e8f0;
        border-radius: 10px;
        border-inline-start: 4px solid var(--dp-primary);
        padding: 1rem 1.1rem;
        overflow-x: auto;
    }

    [dir="rtl"] .dynamic-page .page-body pre {
        border-inline-start: none;
        border-inline-end: 4px solid var(--dp-primary);
    }

    .dynamic-page .page-body pre code {
        border: none;
        background: transparent;
        color: inherit;
        padding: 0;
    }

    .dynamic-page .page-body img,
    .dynamic-page .page-body iframe,
    .dynamic-page .page-body video {
        display: block;
        max-width: 100%;
        height: auto;
        margin: 1.5rem auto;
        border-radius: 10px;
        border: 1px solid var(--dp-border);
    }

    .dynamic-page .page-body table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.6rem 0;
        border: 1px solid var(--dp-border);
        border-radius: 10px;
        overflow: hidden;
    }

    .dynamic-page .page-body th,
    .dynamic-page .page-body td {
        border-bottom: 1px solid var(--dp-border);
        padding: 12px 14px;
        text-align: start;
    }

    .dynamic-page .page-body th {
        background: var(--dp-soft);
        color: var(--dp-heading);
        font-weight: 700;
    }

    .dynamic-page .page-body hr {
        border: 0;
        border-top: 1px solid var(--dp-border);
        margin: 2rem 0;
    }

    @media (max-width: 1366px) {
        .dynamic-page-grid.single-content .dynamic-page-column {
            max-width: 100%;
        }
    }

    @media (max-width: 767.98px) {
        .dynamic-page {
            padding: 18px 16px 16px;
        }

        .dynamic-page h1 {
            font-size: 1.52rem;
        }

        .dynamic-page .dynamic-page-head {
            margin-bottom: 18px;
            padding-bottom: 18px;
        }

        .dynamic-page .page-body {
            font-size: .98rem;
            line-height: 1.8;
        }

        .dynamic-page .page-body h2 {
            font-size: 1.25rem;
            margin-top: 1.9rem;
        }

        .dynamic-page .page-body table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
    }

    [data-theme="css_d"] .dynamic-page {
        --dp-heading: #f1f5f9;
        --dp-body: #cbd5e1;
        --dp-muted: #94a3b8;
        --dp-border: #2b3143;
        --dp-soft: #1b2231;
        --dp-quote: #182236;
        --dp-primary: #8f8cff;
    }

    [data-theme="css_d"] .dynamic-page-grid .dynamic-page-column .widget-box {
        background: #121826;
        border-color: #2b3143;
        box-shadow: 0 20px 44px rgba(0, 0, 0, .32);
    }
</style>
@endpush

@section('content')
@php
    $hasLeftWidget = (bool) $page->widget_left;
    $hasRightWidget = (bool) $page->widget_right;
    $layoutClass = $hasLeftWidget && $hasRightWidget
        ? 'grid-3-6-3 mobile-prefer-content'
        : ($hasLeftWidget ? 'grid-3-9' : ($hasRightWidget ? 'grid-9-3' : ''));
@endphp

<div class="grid dynamic-page-grid {{ $layoutClass }} {{ (!$hasLeftWidget && !$hasRightWidget) ? 'single-content' : '' }}">
    @if($hasLeftWidget)
        <div class="grid-column">
            <x-widget-column :side="$page->getLeftPlaceId()" />
        </div>
    @endif

    <div class="grid-column dynamic-page-column">
        <div class="widget-box">
            <div class="dynamic-page">
                <div class="dynamic-page-head">
                    <ul class="dynamic-page-breadcrumb">
                        <li><a href="{{ route('index') }}">{{ __('messages.home') ?? 'Home' }}</a></li>
                        <li>{{ $page->title }}</li>
                    </ul>
                    <h1>{{ $page->title }}</h1>
                    <div class="dynamic-page-meta">
                        @if($page->updated_at)
                            <span><i class="fa-regular fa-clock"></i>{{ __('messages.updated') ?? 'Updated' }}: {{ $page->updated_at->format('M d, Y') }}</span>
                        @endif
                        @if($page->meta_description)
                            <span>{{ \Illuminate\Support\Str::limit(strip_tags($page->meta_description), 120) }}</span>
                        @endif
                    </div>
                </div>
                <article class="page-body">
                    {!! $page->content !!}
                </article>
            </div>
        </div>
    </div>

    @if($hasRightWidget)
        <div class="grid-column">
            <x-widget-column :side="$page->getRightPlaceId()" />
        </div>
    @endif
</div>
@endsection
