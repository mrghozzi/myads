@extends('admin::layouts.admin')

@section('title', __('messages.billing_settings_tab'))

@section('content')
<div class="admin-page">
    {{-- Superdesign Hero --}}
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
        <div class="admin-hero__actions">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span id="hero-status-badge" class="badge {{ !empty($settings['enabled']) ? 'bg-soft-success text-success border border-success border-opacity-25' : 'bg-soft-secondary text-secondary border border-secondary border-opacity-25' }} rounded-pill px-3 py-2 fw-bold fs-12">
                    <i class="feather-power me-1"></i>
                    <span id="hero-status-text">{{ !empty($settings['enabled']) ? __('messages.billing_system_enabled') : __('messages.billing_system_disabled') }}</span>
                </span>
                <span id="hero-currency-badge" class="badge bg-soft-primary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-bold fs-12">
                    <i class="feather-dollar-sign me-1"></i>
                    <span id="hero-currency-text">{{ $settings['base_currency_code'] ?? 'USD' }}</span>
                </span>
            </div>
        </div>
    </section>

    {{-- Billing Navigation Tabs --}}
    @include('admin::admin.billing.partials.nav', ['currentTab' => 'settings'])

    {{-- Alerts & Toasts --}}
    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mb-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10">
                <form id="billing-settings-form" action="{{ route('admin.billing.settings.update') }}" method="POST">
                    @csrf
                    <div class="admin-panel mb-4">
                        <div class="admin-panel__header">
                            <div>
                                <div class="admin-panel__eyebrow">{{ __('messages.options') }}</div>
                                <h3 class="admin-panel__title">{{ __('messages.billing_settings_tab') }}</h3>
                            </div>
                        </div>

                        <div class="admin-panel__body p-4">
                            {{-- Master Switch Card --}}
                            <div class="p-4 rounded-4 mb-4 transition-all" style="background: var(--admin-premium-surface-alt); border: 1px solid var(--admin-premium-border);">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px;">
                                            <i class="feather-power fs-3"></i>
                                        </div>
                                        <div>
                                            <label for="enabled" class="fw-bold text-dark fs-15 mb-1 cursor-pointer">
                                                {{ __('messages.billing_enable_system_label') }}
                                            </label>
                                            <div class="text-muted fs-12">
                                                {{ __('messages.billing_settings_runtime_note') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-check form-switch form-switch-lg mb-0">
                                        <input class="form-check-input shadow-sm" type="checkbox" id="enabled" name="enabled" value="1" @checked(!empty($settings['enabled'])) style="width: 3.2em; height: 1.6em; cursor: pointer;">
                                    </div>
                                </div>
                            </div>

                            {{-- Base Currency --}}
                            <div class="row g-4">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="base_currency_code" class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-2">
                                            {{ __('messages.billing_base_currency_label') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white text-muted border-end-0" style="border-radius: 12px 0 0 12px;">
                                                <i class="feather-dollar-sign"></i>
                                            </span>
                                            <select name="base_currency_code" id="base_currency_code" class="form-select form-select-lg border-start-0" style="border-radius: 0 12px 12px 0;">
                                                @foreach($currencies as $currency)
                                                    <option value="{{ $currency->code }}" @selected(($settings['base_currency_code'] ?? 'USD') === $currency->code)>
                                                        {{ $currency->code }} - {{ $currency->name ?: $currency->code }} ({{ $currency->symbol ?: $currency->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="text-muted fs-12 mt-2">
                                            {{ __('messages.billing_currencies_help') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-panel__footer d-flex align-items-center justify-content-between flex-wrap gap-3 bg-light">
                            <div class="d-flex align-items-center gap-2 text-muted fs-12">
                                <i class="feather-info text-primary fs-5"></i>
                                <span>{{ __('messages.billing_settings_runtime_note') }}</span>
                            </div>
                            <button type="submit" id="btn-save-settings" class="btn btn-primary btn-lg fw-bold px-4 shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: 12px;">
                                <i class="feather-save fs-5"></i>
                                <span>{{ __('messages.save_changes') }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('billing-settings-form');
    var btn = document.getElementById('btn-save-settings');
    if (!form || !btn) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        var originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> {{ __("messages.saving") }}';

        var formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function(res) {
            return res.json().then(function(data) {
                return { status: res.status, data: data };
            });
        })
        .then(function(result) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;

            if (result.status >= 200 && result.status < 300 && result.data.success) {
                window.showBillingToast(result.data.message || '{{ __("messages.billing_settings_saved") }}', 'success');

                // Update hero badges live
                var isEnabled = form.querySelector('#enabled').checked;
                var currSelect = form.querySelector('#base_currency_code');
                var currCode = currSelect ? currSelect.value : 'USD';

                var heroStatusBadge = document.getElementById('hero-status-badge');
                var heroStatusText = document.getElementById('hero-status-text');
                if (heroStatusBadge && heroStatusText) {
                    if (isEnabled) {
                        heroStatusBadge.className = 'badge bg-soft-success text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-bold fs-12';
                        heroStatusText.innerText = '{{ __("messages.billing_system_enabled") }}';
                    } else {
                        heroStatusBadge.className = 'badge bg-soft-secondary text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2 fw-bold fs-12';
                        heroStatusText.innerText = '{{ __("messages.billing_system_disabled") }}';
                    }
                }

                var heroCurrText = document.getElementById('hero-currency-text');
                if (heroCurrText) {
                    heroCurrText.innerText = currCode;
                }
            } else {
                var errorMsg = result.data.message || '{{ __("messages.error_occurred") }}';
                if (result.data.errors) {
                    var firstKey = Object.keys(result.data.errors)[0];
                    if (firstKey && result.data.errors[firstKey][0]) {
                        errorMsg = result.data.errors[firstKey][0];
                    }
                }
                window.showBillingToast(errorMsg, 'danger');
            }
        })
        .catch(function(err) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            window.showBillingToast(err.message || '{{ __("messages.error_occurred") }}', 'danger');
        });
    });
});
</script>
@endpush
