@extends('theme::layouts.admin')

@section('title', __('messages.settings_site'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.settings_site') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.options') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.settings_site') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.site_name') }} / {{ __('messages.url_link') }} / {{ __('messages.language_default') }}</p>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <section class="admin-panel">
        <div class="admin-panel__body">
            <form action="{{ route('admin.settings.update') }}" method="POST" class="row g-4">
                @csrf
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.site_name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="titer" class="form-control" value="{{ old('titer', $settings->titer) }}" placeholder="{{ __('messages.site_name') }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.url_link') }} <span class="text-danger">*</span></label>
                    <input type="url" name="url" class="form-control" value="{{ old('url', $settings->url) }}" placeholder="{{ __('messages.url_link') }}">
                </div>
                <div class="col-12">
                    <label class="form-label">{{ __('messages.desc') }}</label>
                    <textarea rows="6" name="description" class="form-control" placeholder="{{ __('messages.desc') }}">{{ old('description', $settings->description) }}</textarea>
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.template') }}</label>
                    <input type="text" name="styles" class="form-control" value="{{ old('styles', $settings->styles) }}" placeholder="{{ __('messages.template') }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.language_default') }}</label>
                    <input type="text" name="lang" class="form-control" value="{{ old('lang', $settings->lang) }}" placeholder="{{ __('messages.language_default') }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.educational_links') }}</label>
                    <select name="e_links" class="form-select">
                        <option value="1" {{ old('e_links', $settings->e_links) == 1 ? 'selected' : '' }}>{{ __('messages.activate') }}</option>
                        <option value="0" {{ old('e_links', $settings->e_links) == 0 ? 'selected' : '' }}>{{ __('messages.close') }}</option>
                    </select>
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.timezone') }}</label>
                    <select name="timezone" class="form-select">
                        <option value="{{ $settings->timezone }}">{{ $settings->timezone }}</option>
                        <option value="Etc/GMT+12">(GMT-12:00) International Date Line West</option>
                        <option value="Pacific/Midway">(GMT-11:00) Midway Island, Samoa</option>
                        <option value="Pacific/Honolulu">(GMT-10:00) Hawaii</option>
                        <option value="US/Alaska">(GMT-09:00) Alaska</option>
                        <option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US & Canada)</option>
                        <option value="America/New_York">(GMT-05:00) Eastern Time (US & Canada)</option>
                        <option value="Europe/London">(GMT+00:00) Dublin, Edinburgh, Lisbon, London</option>
                        <option value="Europe/Berlin">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
                        <option value="Africa/Cairo">(GMT+02:00) Cairo</option>
                        <option value="Asia/Riyadh">(GMT+03:00) Kuwait, Riyadh</option>
                        <option value="Asia/Dubai">(GMT+04:00) Abu Dhabi, Muscat</option>
                    </select>
                </div>
                <div class="col-lg-6">
                    <label class="form-label">Banner repeat window (minutes)</label>
                    <input type="number" min="0" max="525600" name="banner_repeat_window_minutes" class="form-control" value="{{ old('banner_repeat_window_minutes', $bannerRepeatWindowMinutes) }}" placeholder="1440">
                    <small class="text-muted">Prevents showing the same banner to the same visitor on the same publisher within this time window. Use 0 to disable.</small>
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.smart_admin_points_divisor') }}</label>
                    <input type="number" min="0.1" max="1000" step="0.1" name="smart_ads_points_divisor" class="form-control" value="{{ old('smart_ads_points_divisor', $smartAdsPointsDivisor) }}" placeholder="4">
                    <small class="text-muted">{{ __('messages.smart_admin_points_divisor_help') }}</small>
                </div>
                <div class="col-12">
                    <label class="form-label">{{ __('messages.admin_email') }}</label>
                    <input type="email" name="a_mail" class="form-control" value="{{ old('a_mail', $settings->a_mail) }}" placeholder="{{ __('messages.admin_email') }}">
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">{{ __('messages.edit') }}</button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
