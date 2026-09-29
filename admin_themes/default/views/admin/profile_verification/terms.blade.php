@extends('admin::layouts.admin')

@section('title', __('messages.verification_terms'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb"><li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li><li>{{ __('messages.verification_terms') }}</li></ul>
            <div class="admin-hero__eyebrow">{{ __('messages.users') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.verification_terms') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.verification_admin_terms_intro') }}</p>
        </div>
    </section>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ __('messages.please_check_errors') }}</div>@endif
    <div class="row g-4">
        <div class="col-lg-7"><div class="card border-0 shadow-sm"><div class="card-body">
            <form method="GET" action="{{ route('admin.profile_verification.terms') }}" class="mb-4">
                <label for="locale" class="form-label">{{ __('messages.language') }}</label>
                <div class="d-flex gap-2"><select id="locale" name="locale" class="form-select">
                    @foreach($locales as $availableLocale)<option value="{{ $availableLocale }}" {{ $locale === $availableLocale ? 'selected' : '' }}>{{ $availableLocale }}</option>@endforeach
                </select><button class="btn btn-outline-primary">{{ __('messages.verification_preview_language') }}</button></div>
            </form>
            <form method="POST" action="{{ route('admin.profile_verification.terms.update') }}">
                @csrf
                <input type="hidden" name="locale" value="{{ $locale }}">
                <label for="terms" class="form-label fw-semibold">{{ __('messages.verification_terms_content') }}</label>
                <textarea id="terms" name="terms" rows="16" maxlength="20000" required class="form-control" placeholder="{{ __('messages.verification_terms_placeholder') }}">{{ old('terms', $settings['terms']) }}</textarea>
                @error('terms')<small class="text-danger">{{ $message }}</small>@enderror
                <button class="btn btn-primary mt-3"><i class="feather-save me-2"></i>{{ __('messages.save_changes') }}</button>
            </form>
        </div></div></div>
        <div class="col-lg-5"><div class="card border-0 shadow-sm h-100"><div class="card-body">
            <h5 class="card-title"><i class="feather-eye text-primary me-2"></i>{{ __('messages.verification_terms_preview') }}</h5>
            <div class="text-muted" style="white-space:pre-line">{{ $settings['terms'] ?: __('messages.verification_terms_preview_empty') }}</div>
        </div></div></div>
    </div>
</div>
@endsection
