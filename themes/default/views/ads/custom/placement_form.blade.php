@extends('theme::layouts.master')

@section('content')
@include('theme::ads.custom.partials.styles')

@php
    $isEdit = $placement->exists;
@endphp

<div class="section-banner" style="background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%);">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/banner_ads.png') }}" alt="custom-placement">
    <p class="section-banner-title">{{ $isEdit ? __('messages.custom_ads_edit_placement') : __('messages.custom_ads_new_placement') }}</p>
    <p class="section-banner-text">{{ __('messages.custom_ads_placement_form_intro') }}</p>
</div>

<div class="custom-ads-toolbar">
    <a class="custom-ads-pill" href="{{ route('ads.custom.index') }}"><i class="fa fa-arrow-left"></i>{{ __('messages.custom_ads') }}</a>
</div>

<div class="widget-box">
    <form method="POST" action="{{ $isEdit ? route('ads.custom.placements.update', $placement) : route('ads.custom.placements.store') }}">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="custom-ads-form-grid">
            <div class="form-item">
                <label class="rl-label">{{ __('messages.name') }}</label>
                <input type="text" name="name" value="{{ old('name', $placement->name) }}" required>
                @error('name')<p class="error">{{ $message }}</p>@enderror
            </div>

            <div class="form-item">
                <label class="rl-label">{{ __('messages.type') }}</label>
                <select name="format" required>
                    @foreach($formats as $value => $label)
                        <option value="{{ $value }}" @selected(old('format', $placement->format) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('format')<p class="error">{{ $message }}</p>@enderror
            </div>

            <div class="form-item">
                <label class="rl-label">{{ __('messages.size') }}</label>
                <select name="size" required>
                    @foreach($sizes as $value => $label)
                        <option value="{{ $value }}" @selected(old('size', $placement->size) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('size')<p class="error">{{ $message }}</p>@enderror
            </div>

            <div class="form-item">
                <label class="rl-label">{{ __('messages.url') }}</label>
                <input type="url" name="site_url" value="{{ old('site_url', $placement->site_url) }}" placeholder="https://example.com">
                @error('site_url')<p class="error">{{ $message }}</p>@enderror
            </div>

            @if($isEdit)
                <div class="form-item">
                    <label class="rl-label">{{ __('messages.status') }}</label>
                    <select name="status">
                        @foreach([\App\Models\CustomAdPlacement::STATUS_ACTIVE, \App\Models\CustomAdPlacement::STATUS_PAUSED, \App\Models\CustomAdPlacement::STATUS_DISABLED] as $status)
                            <option value="{{ $status }}" @selected(old('status', $placement->status) === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="form-item" style="margin-top: 18px;">
            <label class="rl-label">{{ __('messages.description') }}</label>
            <textarea name="description" rows="4">{{ old('description', $placement->description) }}</textarea>
            @error('description')<p class="error">{{ $message }}</p>@enderror
        </div>

        <div class="custom-ads-form-grid" style="margin-top: 18px;">
            <div class="form-item">
                <label class="rl-label">{{ __('messages.custom_ads_background_color') }}</label>
                <input type="color" name="background_color" value="{{ old('background_color', $placement->background_color ?: '#ffffff') }}">
            </div>
            <div class="form-item">
                <label class="rl-label">{{ __('messages.custom_ads_text_color') }}</label>
                <input type="color" name="text_color" value="{{ old('text_color', $placement->text_color ?: '#1f2937') }}">
            </div>
            <div class="form-item">
                <label class="rl-label">{{ __('messages.custom_ads_accent_color') }}</label>
                <input type="color" name="accent_color" value="{{ old('accent_color', $placement->accent_color ?: '#615dfa') }}">
            </div>
        </div>

        <div style="margin: 18px 0;">
            <label style="display: inline-flex; align-items: center; gap: 8px; font-weight: 700; color: #3e3f5e;">
                <input type="checkbox" name="is_public" value="1" @checked(old('is_public', $placement->is_public ?? true))>
                {{ __('messages.custom_ads_public_space') }}
            </label>
            <p class="custom-ads-muted">{{ __('messages.custom_ads_public_space_help') }}</p>
        </div>

        <div class="custom-ads-actions">
            <button type="submit" class="button secondary">{{ $isEdit ? __('messages.save') : __('messages.create') }}</button>
            <a href="{{ route('ads.custom.index') }}" class="button tertiary">{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
