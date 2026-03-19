@extends('theme::layouts.admin')

@section('title', __('messages.settings_site'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.settings_site') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.settings_site') }}</li>
        </ul>
    </div>
</div>

<div class="card stretch stretch-full">
    <div class="card-header">
        <h5 class="card-title">{{ __('messages.settings_site') }}</h5>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            
            <div class="row mb-4">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <label class="form-label">{{ __('messages.site_name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="titer" class="form-control" value="{{ old('titer', $settings->titer) }}" placeholder="{{ __('messages.site_name') }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.url_link') }} <span class="text-danger">*</span></label>
                    <input type="url" name="url" class="form-control" value="{{ old('url', $settings->url) }}" placeholder="{{ __('messages.url_link') }}">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">{{ __('messages.desc') }}</label>
                <textarea rows="6" name="description" class="form-control" placeholder="{{ __('messages.desc') }}">{{ old('description', $settings->description) }}</textarea>
            </div>

            <div class="row mb-4">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <label class="form-label">{{ __('messages.template') }}</label>
                    <input type="text" name="styles" class="form-control" value="{{ old('styles', $settings->styles) }}" placeholder="{{ __('messages.template') }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.language_default') }}</label>
                    <input type="text" name="lang" class="form-control" value="{{ old('lang', $settings->lang) }}" placeholder="{{ __('messages.language_default') }}">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-lg-6 mb-4 mb-lg-0">
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
            </div>

            <div class="row mb-4">
                <div class="col-lg-6">
                    <label class="form-label">Banner repeat window (minutes)</label>
                    <input
                        type="number"
                        min="0"
                        max="525600"
                        name="banner_repeat_window_minutes"
                        class="form-control"
                        value="{{ old('banner_repeat_window_minutes', $bannerRepeatWindowMinutes) }}"
                        placeholder="1440"
                    >
                    <small class="text-muted">Prevents showing the same banner to the same visitor on the same publisher within this time window. Use 0 to disable.</small>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">{{ __('messages.admin_email') }}</label>
                <input type="email" name="a_mail" class="form-control" value="{{ old('a_mail', $settings->a_mail) }}" placeholder="{{ __('messages.admin_email') }}">
            </div>

            <button type="submit" class="btn btn-primary w-100">{{ __('messages.edit') }}</button>
        </form>
    </div>
</div>
@endsection
