@extends('admin::layouts.admin')

@section('title', __('messages.seo_ads_files'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="seo-shell">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.seo.index') }}">{{ __('messages.seo_dashboard') }}</a></li>
                <li>{{ __('messages.seo_ads_files') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.seo_ads_files') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.seo_ads_files_heading') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.seo_ads_files_intro') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-toolbar-card">
                <div class="admin-toolbar-row w-100">
                    <a href="{{ url('ads.txt') }}" target="_blank" class="btn btn-light">
                        <i class="feather-file-text me-2"></i>{{ __('messages.seo_open') }} ads.txt
                    </a>
                    <a href="{{ url('app-ads.txt') }}" target="_blank" class="btn btn-outline-primary">
                        <i class="feather-file-text me-2"></i>{{ __('messages.seo_open') }} app-ads.txt
                    </a>
                </div>
            </div>
        </div>
    </section>

    @include('admin::admin.seo.partials.nav')
    @include('admin::admin.seo.partials.alerts')

    <div class="card seo-card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.seo.ads_files.update') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">ads.txt</label>
                        <textarea name="ads_txt" rows="15" class="form-control seo-code" placeholder="google.com, pub-xxxxxxxxxxxxxxxx, DIRECT, f08c47fec0942fa0">{{ old('ads_txt', $adsTxt) }}</textarea>
                        <div class="seo-form-note mt-2">{{ __('messages.seo_ads_txt_help') ?? 'Authorized Digital Sellers file for web inventory.' }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">app-ads.txt</label>
                        <textarea name="app_ads_txt" rows="15" class="form-control seo-code" placeholder="google.com, pub-xxxxxxxxxxxxxxxx, DIRECT, f08c47fec0942fa0">{{ old('app_ads_txt', $appAdsTxt) }}</textarea>
                        <div class="seo-form-note mt-2">{{ __('messages.seo_app_ads_txt_help') ?? 'Authorized Digital Sellers file for mobile app inventory.' }}</div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-2"></i>{{ __('messages.seo_save_ads_files') ?? __('messages.save_changes') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
