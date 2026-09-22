@extends('admin::layouts.admin')

@section('title', __('messages.billing_gateways_title') ?? 'بوابات الدفع')

@section('content')
<div class="admin-page">
    {{-- Superdesign Hero --}}
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'لوحة التحكم' }}</a></li>
                <li><a href="{{ route('admin.billing.overview') }}">{{ __('messages.billing_feature_title') ?? 'الفوترة' }}</a></li>
                <li>{{ __('messages.billing_gateways_tab') ?? 'البوابات' }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.billing_admin_eyebrow') ?? 'مساحة عمل الإيرادات' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.billing_gateways_title') ?? 'بوابات الدفع الإلكتروني والتحويل' }}</h1>
            <p class="admin-hero__copy">{{ __('messages.billing_gateways_help') ?? 'تكوين مزودي الدفع الإلكتروني وتوفير تعليمات التحويل البنكي اليدوي الآمن.' }}</p>
        </div>
        <div class="admin-hero__actions">
            @php
                $activeCount = collect($gatewayDefinitions)->filter(fn($g) => !empty($g['config']['enabled']))->count();
                $totalCount = count($gatewayDefinitions);
            @endphp
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-soft-success text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-bold fs-12">
                    <i class="feather-check-circle me-1"></i>
                    {{ $activeCount }} {{ __('messages.active') ?? 'بوابات نشطة' }} / {{ $totalCount }}
                </span>
            </div>
        </div>
    </section>

    {{-- Billing Navigation Tabs --}}
    @include('admin::admin.billing.partials.nav', ['currentTab' => 'gateways'])

    {{-- Alerts & Toasts --}}
    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mb-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        <div class="row g-4">
            @foreach($gatewayDefinitions as $gateway)
                @php
                    $config = $gateway['config'];
                    $setupGuideKey = 'messages.billing_gateway_setup_' . $gateway['key'];
                    $setupGuide = __($setupGuideKey);
                    $webhookUrl = url('/billing/webhook/' . $gateway['key']);
                    $isEnabled = !empty($config['enabled']);
                @endphp
                <div class="col-12">
                    <form id="gateway-form-{{ $gateway['key'] }}" action="{{ route('admin.billing.gateways.update', $gateway['key']) }}" method="POST" class="gateway-ajax-form" data-gateway="{{ $gateway['key'] }}">
                        @csrf
                        <div class="admin-panel transition-all">
                            {{-- Gateway Header --}}
                            <div class="admin-panel__header d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white border rounded-3 shadow-sm d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px;">
                                        @if($gateway['key'] === 'stripe')
                                            <i class="fa-brands fa-stripe fs-2 text-primary"></i>
                                        @elseif($gateway['key'] === 'paypal')
                                            <i class="fa-brands fa-paypal fs-3 text-info"></i>
                                        @elseif($gateway['key'] === 'apple_pay')
                                            <i class="fa-brands fa-apple fs-3 text-dark"></i>
                                        @elseif($gateway['key'] === 'bank_transfer')
                                            <i class="fa-solid fa-building-columns fs-4 text-secondary"></i>
                                        @else
                                            <i class="fa-solid fa-money-check-dollar fs-4 text-muted"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="admin-panel__eyebrow mb-1">{{ __('messages.billing_gateways_tab') ?? 'بوابة دفع' }}</div>
                                        <h3 class="admin-panel__title d-flex align-items-center flex-wrap gap-2">
                                            <span>{{ $gateway['label'] }}</span>
                                            <span id="gateway-status-badge-{{ $gateway['key'] }}" class="badge {{ $isEnabled ? 'bg-soft-success text-success border border-success border-opacity-25' : 'bg-soft-secondary text-secondary border border-secondary border-opacity-25' }} rounded-pill px-2 py-1 fs-11">
                                                {{ $isEnabled ? (__('messages.active') ?? 'نشطة') : (__('messages.inactive') ?? 'معطلة') }}
                                            </span>
                                            @if(in_array($gateway['key'], ['tabby', 'flouci', 'apple_pay'], true))
                                                <span class="badge bg-soft-warning text-warning border border-warning border-opacity-25 rounded-pill px-2 py-1 fs-11">{{ __('messages.billing_gateway_beta') ?? 'تجريبي' }}</span>
                                            @endif
                                        </h3>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check form-switch form-switch-lg mb-0 d-flex align-items-center">
                                        <input class="form-check-input shadow-sm gateway-enable-switch" type="checkbox" role="switch" name="enabled" id="enabled_{{ $gateway['key'] }}" value="1" @checked($isEnabled) style="width: 3.2em; height: 1.6em; cursor: pointer;">
                                        <label class="form-check-label ms-2 fw-bold text-dark cursor-pointer fs-13" for="enabled_{{ $gateway['key'] }}">
                                            {{ __('messages.enable') ?? 'تفعيل' }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Gateway Body --}}
                            <div class="admin-panel__body p-4">
                                {{-- Setup Guide & Webhook info --}}
                                @if($setupGuide !== $setupGuideKey || in_array($gateway['key'], ['stripe', 'paypal', 'lemon_squeezy', 'paddle', 'tabby'], true))
                                    <div class="p-3 rounded-3 mb-4" style="background: var(--admin-premium-surface-alt); border: 1px solid var(--admin-premium-border);">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2 text-dark fw-bold fs-13">
                                                <i class="feather-info text-primary"></i>
                                                <span>{{ __('messages.instructions') ?? 'دليل الإعداد ورابط الويب هوك (Webhook)' }}</span>
                                            </div>
                                        </div>
                                        @if($setupGuide !== $setupGuideKey)
                                            <p class="text-muted fs-12 mb-3 lh-base">{{ $setupGuide }}</p>
                                        @endif
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-2 rounded-2 bg-white border">
                                            <span class="font-monospace text-muted fs-12 text-break">{{ $webhookUrl }}</span>
                                            <button type="button" class="btn btn-sm btn-light fw-bold text-dark shadow-sm d-inline-flex align-items-center gap-1" style="border-radius: 6px; font-size: 11px;" onclick="window.copyBillingText('{{ $webhookUrl }}', this);">
                                                <i class="feather-copy"></i>
                                                <span>{{ __('messages.copy') ?? 'نسخ الرابط' }}</span>
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                {{-- Specific Fields --}}
                                <div class="row g-3">
                                    {{-- Stripe --}}
                                    @if($gateway['key'] === 'stripe')
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.mode') ?? 'بيئة التشغيل' }}</label>
                                            <select name="mode" class="form-select" style="border-radius: 10px;">
                                                <option value="sandbox" @selected(($config['mode'] ?? 'sandbox') === 'sandbox')>{{ __('messages.sandbox_mode') ?? 'Sandbox (تجريبي)' }}</option>
                                                <option value="live" @selected(($config['mode'] ?? 'sandbox') === 'live')>{{ __('messages.live_mode') ?? 'Live (إنتاجي مباشر)' }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_stripe_publishable_key_label') ?? 'المفتاح القابل للنشر (Publishable Key)' }}</label>
                                            <input type="text" name="publishable_key" class="form-control font-monospace" value="{{ old('publishable_key', $config['publishable_key'] ?? '') }}" style="border-radius: 10px;">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_stripe_secret_key_label') ?? 'المفتاح السري (Secret Key)' }}</label>
                                            <input type="password" name="secret_key" class="form-control font-monospace" value="{{ old('secret_key', $config['secret_key'] ?? '') }}" style="border-radius: 10px;">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_stripe_webhook_secret_label') ?? 'مفتاح توقيع الويب هوك (Webhook Secret)' }}</label>
                                            <input type="password" name="webhook_secret" class="form-control font-monospace" value="{{ old('webhook_secret', $config['webhook_secret'] ?? '') }}" style="border-radius: 10px;">
                                        </div>

                                    {{-- PayPal --}}
                                    @elseif($gateway['key'] === 'paypal')
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.mode') ?? 'بيئة التشغيل' }}</label>
                                            <select name="mode" class="form-select" style="border-radius: 10px;">
                                                <option value="sandbox" @selected(($config['mode'] ?? 'sandbox') === 'sandbox')>{{ __('messages.sandbox_mode') ?? 'Sandbox (تجريبي)' }}</option>
                                                <option value="live" @selected(($config['mode'] ?? 'sandbox') === 'live')>{{ __('messages.live_mode') ?? 'Live (إنتاجي مباشر)' }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_paypal_client_id_label') ?? 'Client ID' }}</label>
                                            <input type="text" name="client_id" class="form-control font-monospace" value="{{ old('client_id', $config['client_id'] ?? '') }}" style="border-radius: 10px;">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_paypal_secret_key_label') ?? 'Secret Key' }}</label>
                                            <input type="password" name="secret_key" class="form-control font-monospace" value="{{ old('secret_key', $config['secret_key'] ?? '') }}" style="border-radius: 10px;">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_paypal_webhook_id_label') ?? 'Webhook ID' }}</label>
                                            <input type="text" name="webhook_id" class="form-control font-monospace" value="{{ old('webhook_id', $config['webhook_id'] ?? '') }}" style="border-radius: 10px;">
                                        </div>

                                    {{-- Bank Transfer --}}
                                    @elseif($gateway['key'] === 'bank_transfer')
                                        <div class="col-12">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_bank_transfer_instructions_label') ?? 'تعليمات التحويل البنكي (تظهر للعميل)' }}</label>
                                            <textarea name="instructions" class="form-control" rows="4" placeholder="{{ __('messages.billing_bank_transfer_instructions_placeholder') ?? 'اسم البنك، رقم الحساب، الآيبان IBAN، واسم المستفيد...' }}" style="border-radius: 10px;">{{ old('instructions', $config['instructions'] ?? '') }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_bank_transfer_note_label') ?? 'ملاحظة توجيهية إضافية' }}</label>
                                            <textarea name="note" class="form-control" rows="2" placeholder="{{ __('messages.billing_bank_transfer_note_placeholder') ?? 'مثال: يرجى كتابة رقم الطلب في سبب التحويل ورفع الإيصال بعد الإتمام.' }}" style="border-radius: 10px;">{{ old('note', $config['note'] ?? '') }}</textarea>
                                        </div>

                                    {{-- Lemon Squeezy --}}
                                    @elseif($gateway['key'] === 'lemon_squeezy')
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_store_id_label') ?? 'Store ID' }}</label>
                                            <input type="text" name="store_id" class="form-control font-monospace" value="{{ old('store_id', $config['store_id'] ?? '') }}" style="border-radius: 10px;">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_variant_id_label') ?? 'Default Variant ID' }}</label>
                                            <input type="text" name="variant_id" class="form-control font-monospace" value="{{ old('variant_id', $config['variant_id'] ?? '') }}" style="border-radius: 10px;">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_api_key_label') ?? 'API Key' }}</label>
                                            <input type="password" name="api_key" class="form-control font-monospace" value="{{ old('api_key', $config['api_key'] ?? '') }}" style="border-radius: 10px;">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_webhook_secret_label') ?? 'Webhook Secret' }}</label>
                                            <input type="password" name="webhook_secret" class="form-control font-monospace" value="{{ old('webhook_secret', $config['webhook_secret'] ?? '') }}" style="border-radius: 10px;">
                                        </div>

                                    {{-- Paddle --}}
                                    @elseif($gateway['key'] === 'paddle')
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.mode') ?? 'بيئة التشغيل' }}</label>
                                            <select name="mode" class="form-select" style="border-radius: 10px;">
                                                <option value="sandbox" @selected(($config['mode'] ?? 'sandbox') === 'sandbox')>{{ __('messages.sandbox_mode') ?? 'Sandbox' }}</option>
                                                <option value="live" @selected(($config['mode'] ?? 'sandbox') === 'live')>{{ __('messages.live_mode') ?? 'Live' }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_api_key_label') ?? 'API Key' }}</label>
                                            <input type="password" name="api_key" class="form-control font-monospace" value="{{ old('api_key', $config['api_key'] ?? '') }}" style="border-radius: 10px;">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_price_id_label') ?? 'Default Price ID' }}</label>
                                            <input type="text" name="price_id" class="form-control font-monospace" value="{{ old('price_id', $config['price_id'] ?? '') }}" style="border-radius: 10px;">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_webhook_secret_label') ?? 'Webhook Secret' }}</label>
                                            <input type="password" name="webhook_secret" class="form-control font-monospace" value="{{ old('webhook_secret', $config['webhook_secret'] ?? '') }}" style="border-radius: 10px;">
                                        </div>

                                    {{-- Tabby --}}
                                    @elseif($gateway['key'] === 'tabby')
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.region') ?? 'المنطقة' }}</label>
                                            <select name="region" class="form-select" style="border-radius: 10px;">
                                                <option value="UAE" @selected(($config['region'] ?? 'UAE') === 'UAE')>{{ __('messages.uae_region') ?? 'الإمارات (UAE)' }}</option>
                                                <option value="KSA" @selected(($config['region'] ?? 'UAE') === 'KSA')>{{ __('messages.ksa_region') ?? 'السعودية (KSA)' }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_public_key_label') ?? 'Public Key' }}</label>
                                            <input type="text" name="public_key" class="form-control font-monospace" value="{{ old('public_key', $config['public_key'] ?? '') }}" style="border-radius: 10px;">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_secret_key_label') ?? 'Secret Key' }}</label>
                                            <input type="password" name="secret_key" class="form-control font-monospace" value="{{ old('secret_key', $config['secret_key'] ?? '') }}" style="border-radius: 10px;">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_merchant_code_label') ?? 'Merchant Code' }}</label>
                                            <input type="text" name="merchant_code" class="form-control font-monospace" value="{{ old('merchant_code', $config['merchant_code'] ?? '') }}" style="border-radius: 10px;">
                                        </div>

                                    {{-- Flouci --}}
                                    @elseif($gateway['key'] === 'flouci')
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_flouci_app_token_label') ?? 'Public Key (App Token)' }}</label>
                                            <input type="text" name="public_key" class="form-control font-monospace" value="{{ old('public_key', $config['public_key'] ?? '') }}" style="border-radius: 10px;">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_flouci_app_secret_label') ?? 'Secret Key (App Secret)' }}</label>
                                            <input type="password" name="secret_key" class="form-control font-monospace" value="{{ old('secret_key', $config['secret_key'] ?? '') }}" style="border-radius: 10px;">
                                        </div>

                                    {{-- Apple Pay --}}
                                    @elseif($gateway['key'] === 'apple_pay')
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.mode') ?? 'بيئة التشغيل' }}</label>
                                            <select name="mode" class="form-select" style="border-radius: 10px;">
                                                <option value="sandbox" @selected(($config['mode'] ?? 'sandbox') === 'sandbox')>{{ __('messages.sandbox_simulation_mode') ?? 'Sandbox (محاكاة)' }}</option>
                                                <option value="live" @selected(($config['mode'] ?? 'sandbox') === 'live')>{{ __('messages.live_mode') ?? 'Live' }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">{{ __('messages.billing_merchant_id_label') ?? 'Merchant ID' }}</label>
                                            <input type="text" name="merchant_id" class="form-control font-monospace" value="{{ old('merchant_id', $config['merchant_id'] ?? '') }}" placeholder="merchant.com.example" style="border-radius: 10px;">
                                        </div>
                                    @endif

                                    {{-- Supported Currencies --}}
                                    <div class="col-12 mt-3">
                                        <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-2">
                                            {{ __('messages.billing_supported_currencies_label') ?? 'العملات المدعومة في هذه البوابة' }}
                                        </label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @php
                                                $selectedCurrencies = (array) ($config['supported_currencies'] ?? []);
                                            @endphp
                                            @foreach($currencies as $curr)
                                                <label class="admin-chip cursor-pointer" style="cursor: pointer;">
                                                    <input type="checkbox" name="supported_currencies[]" value="{{ $curr->code }}" @checked(empty($selectedCurrencies) || in_array($curr->code, $selectedCurrencies, true)) class="form-check-input me-1">
                                                    <span>{{ $curr->code }}</span>
                                                    <span class="text-muted small">({{ $curr->symbol ?: $curr->code }})</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <div class="text-muted small fs-11 mt-1">{{ __('messages.billing_all_active_currencies_note') ?? 'إذا لم تحدد عملات معينة، فسيتم قبول جميع العملات النشطة افتراضياً.' }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Gateway Footer --}}
                            <div class="admin-panel__footer d-flex align-items-center justify-content-between p-3 bg-light">
                                <span class="text-muted fs-12">{{ __('messages.billing_gateway_note') ?? 'يتم تفعيل البوابة فورياً للمستخدمين بعد الحفظ.' }}</span>
                                <button type="submit" id="btn-save-{{ $gateway['key'] }}" class="btn btn-primary fw-bold shadow-sm px-4 d-inline-flex align-items-center gap-2" style="border-radius: 12px;">
                                    <i class="feather-save"></i>
                                    <span>{{ __('messages.save_changes') ?? 'حفظ إعدادات البوابة' }}</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var forms = document.querySelectorAll('.gateway-ajax-form');

    forms.forEach(function(form) {
        var gatewayKey = form.getAttribute('data-gateway');
        var btn = document.getElementById('btn-save-' + gatewayKey);
        var statusBadge = document.getElementById('gateway-status-badge-' + gatewayKey);
        var enableSwitch = form.querySelector('.gateway-enable-switch');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            var originalHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> {{ __("messages.saving") ?? "جاري الحفظ..." }}';
            }

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
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }

                if (result.status >= 200 && result.status < 300 && result.data.success) {
                    window.showBillingToast(result.data.message || '{{ __("messages.billing_gateway_saved") ?? "تم حفظ إعدادات البوابة بنجاح" }}', 'success');

                    // Update status badge on card
                    if (statusBadge && enableSwitch) {
                        var isChecked = enableSwitch.checked;
                        if (isChecked) {
                            statusBadge.className = 'badge bg-soft-success text-success border border-success border-opacity-25 rounded-pill px-2 py-1 fs-11';
                            statusBadge.innerText = '{{ __("messages.active") ?? "نشطة" }}';
                        } else {
                            statusBadge.className = 'badge bg-soft-secondary text-secondary border border-secondary border-opacity-25 rounded-pill px-2 py-1 fs-11';
                            statusBadge.innerText = '{{ __("messages.inactive") ?? "معطلة" }}';
                        }
                    }
                } else {
                    var errorMsg = result.data.message || '{{ __("messages.error_occurred") ?? "حدث خطأ أثناء حفظ البوابة" }}';
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
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
                window.showBillingToast(err.message || '{{ __("messages.error_occurred") ?? "حدث خطأ غير متوقع" }}', 'danger');
            });
        });
    });
});
</script>
@endpush
