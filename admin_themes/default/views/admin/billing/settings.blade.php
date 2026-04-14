@extends('admin::layouts.admin')

@section('title', __('messages.billing_settings_tab'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.billing.overview') }}">{{ __('messages.billing_feature_title') }}</a></li>
                <li>{{ __('messages.billing_settings_tab') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.billing_admin_eyebrow') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.billing_settings_tab') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.billing_settings_help') }}</p>
        </div>
    </section>

    <div class="mt-4">
        @include('admin::admin.billing.partials.nav', ['currentTab' => 'settings'])
    </div>

    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mt-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        <form action="{{ route('admin.billing.settings.update') }}" method="POST" class="mt-4 d-grid gap-3">
            @csrf
            <section class="admin-panel">
                <div class="admin-panel__body">
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" id="enabled" name="enabled" value="1" @checked(!empty($settings['enabled']))>
                        <label class="form-check-label fw-semibold" for="enabled">{{ __('messages.billing_enable_system_label') }}</label>
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-6">
                            <label class="form-label">{{ __('messages.billing_base_currency_label') }}</label>
                            <select name="base_currency_code" class="form-select">
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->code }}" @selected(($settings['base_currency_code'] ?? 'USD') === $currency->code)>
                                        {{ $currency->code }}{{ $currency->name ? ' - ' . $currency->name : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </section>

            <section class="admin-panel">
                <div class="admin-panel__body d-flex justify-content-between align-items-center gap-3 flex-wrap">
                    <p class="text-muted mb-0">{{ __('messages.billing_settings_runtime_note') }}</p>
                    <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                </div>
            </section>
        </form>
    @endif
</div>
@endsection
