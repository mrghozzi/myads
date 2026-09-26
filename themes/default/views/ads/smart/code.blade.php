@extends('theme::layouts.master')
@section('content')
@php
    $recommendedCode = \App\Support\SmartAdEmbedCode::build(route('ads.embed.smart'), $user->id, $extensions_code ?? '');
    $compatibleCode = \App\Support\SmartAdEmbedCode::buildInlineLoader(route('ads.smart.script'), $user->id, $extensions_code ?? '');
@endphp
@include('theme::ads.partials.workspace_styles')
<main class="ads-workspace" data-ads-workspace data-copy-label="{{ __('messages.copy') }}" data-copied="{{ __('messages.copied') }}">
    <header class="ads-workspace__hero"><div><span class="ads-workspace__eyebrow">{{ __('messages.smart_ads') }}</span><h1 class="ads-workspace__title">{{ __('messages.smart_code_title') }}</h1><p class="ads-workspace__copy">{{ __('messages.smart_code_desc') }}</p></div><div class="ads-workspace__actions"><a class="ads-workspace__button ads-workspace__button--soft" href="{{ route('ads.smart.index') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i>{{ __('messages.smart_list_ads') }}</a><a class="ads-workspace__button" href="{{ route('ads.smart.create') }}"><i class="fa fa-plus" aria-hidden="true"></i>{{ __('messages.smart_create_ad') }}</a></div></header>
    <section class="ads-workspace__code-layout">
        <article class="ads-workspace__panel"><div class="ads-workspace__toolbar"><h2 class="ads-workspace__name">{{ __('messages.smart_code_recommended') }}</h2></div><div class="ads-workspace__list"><p class="ads-workspace__copy">{{ __('messages.smart_code_recommended_desc') }}</p><textarea class="ads-workspace__snippet" readonly aria-label="{{ __('messages.smart_code_recommended') }}">{{ $recommendedCode }}</textarea><p class="ads-workspace__copy">{{ __('messages.smart_code_live_behavior_note') }}</p></div></article>
        <article class="ads-workspace__panel"><div class="ads-workspace__toolbar"><h2 class="ads-workspace__name">{{ __('messages.advanced_code') }}</h2></div><div class="ads-workspace__list"><p class="ads-workspace__copy">{{ __('messages.smart_code_live_behavior_note') }}</p><textarea class="ads-workspace__snippet" readonly aria-label="{{ __('messages.advanced_code') }}">{{ $compatibleCode }}</textarea></div></article>
    </section>
    <section class="ads-workspace__panel"><div class="ads-workspace__toolbar"><h2 class="ads-workspace__name">{{ __('messages.preview') }}</h2></div><div class="ads-workspace__list">
        @if($previewMarkup)
            <p class="ads-workspace__copy">{{ $previewSmartAd->displayTitle() }}</p><div class="ads-workspace__preview">{!! $previewMarkup !!}</div>
        @else
            <div class="ads-workspace__empty"><h3>{{ __('messages.smart_code_preview_empty_title') }}</h3><p>{{ __('messages.smart_code_preview_empty_desc') }}</p><a href="{{ route('ads.smart.create') }}" class="ads-workspace__button">{{ __('messages.smart_create_ad') }}</a></div>
        @endif
    </div></section>
    <div class="ads-workspace__toast" data-ads-toast role="status" aria-live="polite"></div>
</main>
@include('theme::ads.partials.workspace_scripts')
@endsection
