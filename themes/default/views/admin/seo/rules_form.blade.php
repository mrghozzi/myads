@php
    $field = static fn ($name, $fallback = null) => old($name, $rule?->{$name} ?? $fallback);
    $indexableMode = old('indexable_mode', $rule?->indexable === true ? 'index' : ($rule?->indexable === false ? 'noindex' : 'inherit'));
    $activeState = old('is_active', $rule?->is_active ?? true);
@endphp

<div class="col-lg-4">
    <label class="form-label fw-semibold">{{ __('messages.seo_scope_field') }}</label>
    <select name="scope_key" class="form-select">
        @foreach($supportedScopes as $value => $label)
            <option value="{{ $value }}" @selected($field('scope_key') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="col-lg-4">
    <label class="form-label fw-semibold">{{ __('messages.seo_content_type') }}</label>
    <input type="text" name="content_type" class="form-control" value="{{ $field('content_type') }}" placeholder="{{ __('messages.seo_content_type_placeholder') }}">
</div>
<div class="col-lg-4">
    <label class="form-label fw-semibold">{{ __('messages.seo_content_id') }}</label>
    <input type="number" min="1" name="content_id" class="form-control" value="{{ $field('content_id') }}">
</div>
<div class="col-lg-6">
    <label class="form-label fw-semibold">{{ __('messages.seo_title_override') }}</label>
    <input type="text" name="title" class="form-control" value="{{ $field('title') }}" placeholder="{{ __('messages.seo_title_override_placeholder') }}">
</div>
<div class="col-lg-6">
    <label class="form-label fw-semibold">{{ __('messages.seo_keywords_override') }}</label>
    <input type="text" name="keywords" class="form-control" value="{{ $field('keywords') }}">
</div>
<div class="col-12">
    <label class="form-label fw-semibold">{{ __('messages.seo_description_override') }}</label>
    <textarea name="description" rows="3" class="form-control">{{ $field('description') }}</textarea>
</div>
<div class="col-lg-4">
    <label class="form-label fw-semibold">{{ __('messages.seo_robots_override') }}</label>
    <input type="text" name="robots" class="form-control" value="{{ $field('robots') }}" placeholder="{{ __('messages.seo_robots_override_placeholder') }}">
</div>
<div class="col-lg-4">
    <label class="form-label fw-semibold">{{ __('messages.seo_canonical_url_override') }}</label>
    <input type="text" name="canonical_url" class="form-control" value="{{ $field('canonical_url') }}">
</div>
<div class="col-lg-4">
    <label class="form-label fw-semibold">{{ __('messages.seo_indexable_mode') }}</label>
    <select name="indexable_mode" class="form-select">
        <option value="inherit" @selected($indexableMode === 'inherit')>{{ __('messages.seo_inherit') }}</option>
        <option value="index" @selected($indexableMode === 'index')>{{ __('messages.seo_indexable_label') }}</option>
        <option value="noindex" @selected($indexableMode === 'noindex')>{{ __('messages.seo_noindex') }}</option>
    </select>
</div>
<div class="col-lg-4">
    <label class="form-label fw-semibold">{{ __('messages.seo_og_title') }}</label>
    <input type="text" name="og_title" class="form-control" value="{{ $field('og_title') }}">
</div>
<div class="col-lg-4">
    <label class="form-label fw-semibold">{{ __('messages.seo_og_description') }}</label>
    <input type="text" name="og_description" class="form-control" value="{{ $field('og_description') }}">
</div>
<div class="col-lg-4">
    <label class="form-label fw-semibold">{{ __('messages.seo_og_image_url') }}</label>
    <input type="text" name="og_image_url" class="form-control" value="{{ $field('og_image_url') }}">
</div>
<div class="col-lg-4">
    <label class="form-label fw-semibold">{{ __('messages.seo_twitter_card') }}</label>
    <select name="twitter_card" class="form-select">
        <option value="">{{ __('messages.seo_inherit') }}</option>
        @foreach($twitterCards as $value => $label)
            <option value="{{ $value }}" @selected($field('twitter_card') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="col-lg-4">
    <label class="form-label fw-semibold">{{ __('messages.seo_schema_type') }}</label>
    <select name="schema_type" class="form-select">
        <option value="">{{ __('messages.seo_inherit') }}</option>
        @foreach($schemaTypes as $value => $label)
            <option value="{{ $value }}" @selected($field('schema_type') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="col-lg-4 d-flex align-items-end">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="is_active_{{ $prefix }}" name="is_active" value="1" @checked($activeState)>
        <label class="form-check-label fw-semibold" for="is_active_{{ $prefix }}">{{ __('messages.seo_rule_is_active') }}</label>
    </div>
</div>
