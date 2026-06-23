@extends('admin::layouts.admin')

@section('title', __('messages.billing_gateways_title'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<!-- Superdesign Header -->
<div class="row g-0 align-items-center mb-4">
    <div class="col-12 px-4">
        <div class="card border-0 shadow-lg overflow-hidden position-relative" style="border-radius: 24px; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
            <div class="position-absolute top-0 end-0 p-5 opacity-10">
                <i class="fa-solid fa-money-check-dollar" style="font-size: 160px; transform: rotate(-15deg);"></i>
            </div>
            
            <div class="card-body p-5 position-relative z-index-1">
                <div class="row align-items-center">
                    <div class="col-lg-8 text-white">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-bold fs-12 text-uppercase tracking-wider shadow-sm">
                                {{ __('messages.billing_admin_eyebrow') }}
                            </span>
                        </div>
                        <h1 class="display-5 fw-black mb-3 animate__animated animate__fadeIn">
                            {{ __('messages.billing_gateways_title') }}
                        </h1>
                        <p class="lead opacity-80 mb-0 animate__animated animate__fadeIn animate__delay-1s">
                            {{ __('messages.billing_gateways_help') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-content container-lg px-4 pb-5">
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; backdrop-filter: blur(10px); background: rgba(var(--nxl-white-rgb), 0.8);">
        <div class="card-body p-2">
            @include('admin::admin.billing.partials.nav', ['currentTab' => 'gateways'])
        </div>
    </div>

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
                @endphp
                <div class="col-12">
                    <form action="{{ route('admin.billing.gateways.update', $gateway['key']) }}" method="POST">
                        @csrf
                        <div class="card border-0 shadow-sm overflow-hidden h-100 transition-all hover-shadow" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                            <!-- Gateway Header -->
                            <div class="card-header bg-transparent border-0 p-4 pb-3 border-bottom border-soft-light d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="gateway-icon-box shadow-sm bg-white border d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; border-radius: 16px;">
                                        @if($gateway['key'] === 'stripe')
                                            <i class="fa-brands fa-stripe fs-2" style="color: #635bff;"></i>
                                        @elseif($gateway['key'] === 'paypal')
                                            <i class="fa-brands fa-paypal fs-3" style="color: #003087;"></i>
                                        @elseif($gateway['key'] === 'apple_pay')
                                            <i class="fa-brands fa-apple fs-3 text-dark"></i>
                                        @elseif($gateway['key'] === 'bank_transfer')
                                            <i class="fa-solid fa-building-columns fs-4 text-secondary"></i>
                                        @else
                                            <i class="fa-solid fa-money-check-dollar fs-4 text-muted"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-uppercase tracking-wider fw-bold text-muted mb-1" style="font-size: 0.75rem;">{{ __('messages.billing_gateways_tab') }}</div>
                                        <h4 class="fw-bold mb-0 d-flex align-items-center flex-wrap gap-2 text-dark">
                                            <span>{{ $gateway['label'] }}</span>
                                            @if(in_array($gateway['key'], ['tabby', 'flouci', 'apple_pay'], true))
                                                <span class="badge bg-warning text-dark px-2 py-1 shadow-sm" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; border-radius: 6px;">{{ __('messages.billing_gateway_beta') }}</span>
                                            @endif
                                            @if($gateway['key'] === 'tabby')
                                                <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.7rem; font-weight: 500; border-radius: 6px;">{{ __('messages.billing_gateway_tabby_restriction') }}</span>
                                            @endif
                                            @if($gateway['key'] === 'flouci')
                                                <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.7rem; font-weight: 500; border-radius: 6px;">{{ __('messages.billing_gateway_flouci_restriction') }}</span>
                                            @endif
                                        </h4>
                                    </div>
                                </div>
                                <div class="form-check form-switch form-switch-lg mb-0 d-flex align-items-center pe-2">
                                    <input class="form-check-input ms-0 mt-0 shadow-sm" type="checkbox" role="switch" name="enabled" id="enabled_{{ $gateway['key'] }}" value="1" @checked(!empty($config['enabled'])) style="width: 3.5em; height: 1.75em; cursor: pointer;">
                                    <label class="form-check-label ms-3 fw-bold text-dark cursor-pointer fs-15" for="enabled_{{ $gateway['key'] }}">{{ __('messages.active') }}</label>
                                </div>
                            </div>

                            <div class="card-body p-4 pt-4">
                                @if($setupGuide !== $setupGuideKey)
                                    <div class="alert bg-soft-info border-0 text-info py-3 px-4 mb-4 shadow-sm" style="border-radius: 16px;">
                                        <div class="d-flex gap-3">
                                            <i class="feather-info fs-4 mt-1"></i>
                                            <div>
                                                <div class="fw-bold mb-1 fs-14">{{ __('messages.billing_gateway_setup_title') }}</div>
                                                <div class="lh-base fs-13 opacity-90">{{ $setupGuide }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row g-4">
                                    @if($gateway['supports_mode'])
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_gateway_mode_label') }}</label>
                                                <select name="mode" class="form-select form-select-lg border-soft-light bg-light" style="border-radius: 12px;">
                                                    <option value="sandbox" @selected(($config['mode'] ?? 'sandbox') === 'sandbox')>{{ __('messages.billing_gateway_mode_sandbox') }}</option>
                                                    <option value="live" @selected(($config['mode'] ?? 'sandbox') === 'live')>{{ __('messages.billing_gateway_mode_live') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    @endif

                                    @if($gateway['key'] === 'stripe')
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_publishable_key_label') }}</label>
                                                <input type="text" name="publishable_key" class="form-control form-control-lg border-soft-light bg-light" value="{{ old('publishable_key', $config['publishable_key'] ?? '') }}" style="border-radius: 12px;">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_secret_key_label') }}</label>
                                                <input type="password" name="secret_key" class="form-control form-control-lg border-soft-light bg-light" value="" style="border-radius: 12px;">
                                                <div class="form-text fs-12 mt-2 opacity-75">
                                                    {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                                    @if(!empty($config['secret_key']))
                                                        <span class="text-success ms-1"><i class="feather-check-circle me-1"></i>{{ __('messages.billing_secret_current_masked', ['value' => $config['secret_key']]) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_webhook_secret_label') }}</label>
                                                <input type="password" name="webhook_secret" class="form-control form-control-lg border-soft-light bg-light" value="" style="border-radius: 12px;">
                                                <div class="form-text fs-12 mt-2 opacity-75">
                                                    {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                                    @if(!empty($config['webhook_secret']))
                                                        <span class="text-success ms-1"><i class="feather-check-circle me-1"></i>{{ __('messages.billing_secret_current_masked', ['value' => $config['webhook_secret']]) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-3 p-3 bg-light rounded border-soft-light" style="border-radius: 12px !important;">
                                                <div class="small fw-bold mb-2 text-muted text-uppercase tracking-wider fs-11">{{ __('messages.billing_webhook_url_label') }} ({{ __('messages.billing_stripe_webhook_hint') }}):</div>
                                                <code class="user-select-all d-block text-break fs-13 bg-white p-2 rounded shadow-sm border">{{ route('billing.webhook', ['gateway' => 'stripe']) }}</code>
                                            </div>
                                        </div>
                                    @elseif($gateway['key'] === 'paypal')
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_client_id_label') }}</label>
                                                <input type="text" name="client_id" class="form-control form-control-lg border-soft-light bg-light" value="{{ old('client_id', $config['client_id'] ?? '') }}" style="border-radius: 12px;">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_secret_key_label') }}</label>
                                                <input type="password" name="secret_key" class="form-control form-control-lg border-soft-light bg-light" value="" style="border-radius: 12px;">
                                                <div class="form-text fs-12 mt-2 opacity-75">
                                                    {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                                    @if(!empty($config['secret_key']))
                                                        <span class="text-success ms-1"><i class="feather-check-circle me-1"></i>{{ __('messages.billing_secret_current_masked', ['value' => $config['secret_key']]) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_webhook_id_label') }}</label>
                                                <input type="text" name="webhook_id" class="form-control form-control-lg border-soft-light bg-light" value="{{ old('webhook_id', $config['webhook_id'] ?? '') }}" style="border-radius: 12px;">
                                            </div>
                                            <div class="mt-3 p-3 bg-light rounded border-soft-light" style="border-radius: 12px !important;">
                                                <div class="small fw-bold mb-2 text-muted text-uppercase tracking-wider fs-11">{{ __('messages.billing_webhook_url_label') }} ({{ __('messages.billing_paypal_webhook_hint') }}):</div>
                                                <code class="user-select-all d-block text-break fs-13 bg-white p-2 rounded shadow-sm border">{{ route('billing.webhook', ['gateway' => 'paypal']) }}</code>
                                            </div>
                                        </div>
                                    @elseif($gateway['key'] === 'bank_transfer')
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_bank_instructions_label') }}</label>
                                                <textarea name="instructions" class="form-control border-soft-light bg-light" rows="5" style="border-radius: 12px;">{{ old('instructions', $config['instructions'] ?? '') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_bank_note_label') }}</label>
                                                <textarea name="note" class="form-control border-soft-light bg-light" rows="4" style="border-radius: 12px;">{{ old('note', $config['note'] ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    @elseif($gateway['key'] === 'lemon_squeezy')
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_store_id_label') }}</label>
                                                <input type="text" name="store_id" class="form-control form-control-lg border-soft-light bg-light" value="{{ old('store_id', $config['store_id'] ?? '') }}" style="border-radius: 12px;">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_variant_id_label') }}</label>
                                                <input type="text" name="variant_id" class="form-control form-control-lg border-soft-light bg-light" value="{{ old('variant_id', $config['variant_id'] ?? '') }}" style="border-radius: 12px;">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_api_key_label') }}</label>
                                                <input type="password" name="api_key" class="form-control form-control-lg border-soft-light bg-light" value="" style="border-radius: 12px;">
                                                <div class="form-text fs-12 mt-2 opacity-75">
                                                    {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                                    @if(!empty($config['api_key']))
                                                        <span class="text-success ms-1"><i class="feather-check-circle me-1"></i>{{ __('messages.billing_secret_current_masked', ['value' => $config['api_key']]) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_webhook_secret_label') }}</label>
                                                <input type="password" name="webhook_secret" class="form-control form-control-lg border-soft-light bg-light" value="" style="border-radius: 12px;">
                                                <div class="form-text fs-12 mt-2 opacity-75">
                                                    {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                                    @if(!empty($config['webhook_secret']))
                                                        <span class="text-success ms-1"><i class="feather-check-circle me-1"></i>{{ __('messages.billing_secret_current_masked', ['value' => $config['webhook_secret']]) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-3 p-3 bg-light rounded border-soft-light" style="border-radius: 12px !important;">
                                                <div class="small fw-bold mb-2 text-muted text-uppercase tracking-wider fs-11">{{ __('messages.billing_webhook_url_label') }} ({{ __('messages.billing_lemon_squeezy_webhook_hint') }}):</div>
                                                <code class="user-select-all d-block text-break fs-13 bg-white p-2 rounded shadow-sm border">{{ route('billing.webhook', ['gateway' => 'lemon_squeezy']) }}</code>
                                            </div>
                                        </div>
                                    @elseif($gateway['key'] === 'paddle')
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_api_key_label') }}</label>
                                                <input type="password" name="api_key" class="form-control form-control-lg border-soft-light bg-light" value="" style="border-radius: 12px;">
                                                <div class="form-text fs-12 mt-2 opacity-75">
                                                    {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                                    @if(!empty($config['api_key']))
                                                        <span class="text-success ms-1"><i class="feather-check-circle me-1"></i>{{ __('messages.billing_secret_current_masked', ['value' => $config['api_key']]) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_paddle_price_id_label') }}</label>
                                                <input type="text" name="price_id" class="form-control form-control-lg border-soft-light bg-light" value="{{ old('price_id', $config['price_id'] ?? '') }}" style="border-radius: 12px;">
                                                <div class="form-text fs-12 mt-2 opacity-75">{{ __('messages.billing_paddle_price_id_help') }}</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_webhook_secret_label') }}</label>
                                                <input type="password" name="webhook_secret" class="form-control form-control-lg border-soft-light bg-light" value="" style="border-radius: 12px;">
                                                <div class="form-text fs-12 mt-2 opacity-75">
                                                    {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                                    @if(!empty($config['webhook_secret']))
                                                        <span class="text-success ms-1"><i class="feather-check-circle me-1"></i>{{ __('messages.billing_secret_current_masked', ['value' => $config['webhook_secret']]) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-3 p-3 bg-light rounded border-soft-light" style="border-radius: 12px !important;">
                                                <div class="small fw-bold mb-2 text-muted text-uppercase tracking-wider fs-11">Webhook URL ({{ __('messages.billing_paddle_webhook_hint') }}):</div>
                                                <code class="user-select-all d-block text-break fs-13 bg-white p-2 rounded shadow-sm border">{{ route('billing.webhook', ['gateway' => 'paddle']) }}</code>
                                            </div>
                                        </div>
                                    @elseif($gateway['key'] === 'tabby')
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_tabby_region_label') }}</label>
                                                <select name="region" class="form-select form-select-lg border-soft-light bg-light" style="border-radius: 12px;">
                                                    <option value="UAE" @selected(($config['region'] ?? 'UAE') === 'UAE')>{{ __('messages.billing_tabby_region_uae') }}</option>
                                                    <option value="KSA" @selected(($config['region'] ?? 'UAE') === 'KSA')>{{ __('messages.billing_tabby_region_ksa') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_tabby_public_key_label') }}</label>
                                                <input type="text" name="public_key" class="form-control form-control-lg border-soft-light bg-light" value="{{ old('public_key', $config['public_key'] ?? '') }}" style="border-radius: 12px;">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_tabby_merchant_code_label') }}</label>
                                                <input type="text" name="merchant_code" class="form-control form-control-lg border-soft-light bg-light" value="{{ old('merchant_code', $config['merchant_code'] ?? '') }}" style="border-radius: 12px;">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_secret_key_label') }}</label>
                                                <input type="password" name="secret_key" class="form-control form-control-lg border-soft-light bg-light" value="" style="border-radius: 12px;">
                                                <div class="form-text fs-12 mt-2 opacity-75">
                                                    {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                                    @if(!empty($config['secret_key']))
                                                        <span class="text-success ms-1"><i class="feather-check-circle me-1"></i>{{ __('messages.billing_secret_current_masked', ['value' => $config['secret_key']]) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_webhook_secret_label') }}</label>
                                                <input type="password" name="webhook_secret" class="form-control form-control-lg border-soft-light bg-light" value="" style="border-radius: 12px;">
                                                <div class="form-text fs-12 mt-2 opacity-75">
                                                    {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                                    @if(!empty($config['webhook_secret']))
                                                        <span class="text-success ms-1"><i class="feather-check-circle me-1"></i>{{ __('messages.billing_secret_current_masked', ['value' => $config['webhook_secret']]) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-3 p-3 bg-light rounded border-soft-light" style="border-radius: 12px !important;">
                                                <div class="small fw-bold mb-2 text-muted text-uppercase tracking-wider fs-11">{{ __('messages.billing_webhook_url_label') }} ({{ __('messages.billing_tabby_webhook_hint') }}):</div>
                                                <code class="user-select-all d-block text-break fs-13 bg-white p-2 rounded shadow-sm border">{{ route('billing.webhook', ['gateway' => 'tabby']) }}</code>
                                            </div>
                                        </div>
                                    @elseif($gateway['key'] === 'flouci')
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_publishable_key_label') }}</label>
                                                <input type="text" name="public_key" class="form-control form-control-lg border-soft-light bg-light" value="{{ old('public_key', $config['public_key'] ?? '') }}" style="border-radius: 12px;">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_secret_key_label') }}</label>
                                                <input type="password" name="secret_key" class="form-control form-control-lg border-soft-light bg-light" value="" style="border-radius: 12px;">
                                                <div class="form-text fs-12 mt-2 opacity-75">
                                                    {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                                    @if(!empty($config['secret_key']))
                                                        <span class="text-success ms-1"><i class="feather-check-circle me-1"></i>{{ __('messages.billing_secret_current_masked', ['value' => $config['secret_key']]) }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <div class="p-3 bg-light rounded border-soft-light" style="border-radius: 12px !important;">
                                                <div class="small fw-bold mb-2 text-muted text-uppercase tracking-wider fs-11">{{ __('messages.billing_webhook_url_label') }} ({{ __('messages.billing_flouci_webhook_hint') }}):</div>
                                                <code class="user-select-all d-block text-break fs-13 bg-white p-2 rounded shadow-sm border">{{ route('billing.webhook', ['gateway' => 'flouci']) }}</code>
                                            </div>
                                        </div>
                                    @elseif($gateway['key'] === 'apple_pay')
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.billing_apple_merchant_id_label') }}</label>
                                                <input type="text" name="merchant_id" class="form-control form-control-lg border-soft-light bg-light" value="{{ old('merchant_id', $config['merchant_id'] ?? '') }}" style="border-radius: 12px;">
                                                <div class="form-text fs-12 mt-2 opacity-75">{{ __('messages.billing_apple_merchant_id_help') }}</div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-12 mt-4 pt-3 border-top border-soft-light">
                                        <label class="form-label fw-bold text-dark mb-3">{{ __('messages.billing_gateway_supported_currencies_label') }}</label>
                                        <div class="row g-3">
                                            @foreach($currencies as $currency)
                                                <div class="col-md-3 col-sm-4 col-6">
                                                    <div class="form-check custom-checkbox p-3 border rounded shadow-sm transition-all hover-border-primary h-100 d-flex align-items-center @if(in_array($currency->code, (array) ($config['supported_currencies'] ?? []), true)) border-primary bg-soft-primary @else border-soft-light bg-light @endif" style="border-radius: 12px !important;">
                                                        <input class="form-check-input mt-0 shadow-none me-3" style="width: 1.25em; height: 1.25em;" type="checkbox" name="supported_currencies[]" id="{{ $gateway['key'] }}_currency_{{ $currency->code }}" value="{{ $currency->code }}" @checked(in_array($currency->code, (array) ($config['supported_currencies'] ?? []), true))>
                                                        <label class="form-check-label fw-bold text-dark w-100 cursor-pointer" for="{{ $gateway['key'] }}_currency_{{ $currency->code }}">{{ $currency->code }}</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-light border-top border-soft-light p-4 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold px-5 shadow-sm hover-scale" style="border-radius: 12px;">
                                    <i class="feather-save me-2"></i> {{ __('messages.save_changes') }}
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
<style>
    .tracking-wider { letter-spacing: 0.05em; }
    .fw-black { font-weight: 900; }
    .opacity-10 { opacity: 0.1; }
    .opacity-80 { opacity: 0.8; }
    .opacity-90 { opacity: 0.9; }
    .z-index-1 { z-index: 1; }
    .fs-11 { font-size: 11px; }
    .fs-12 { font-size: 12px; }
    .fs-13 { font-size: 13px; }
    .fs-14 { font-size: 14px; }
    .fs-15 { font-size: 15px; }
    
    .transition-all { transition: all 0.3s ease; }
    .hover-shadow:hover { box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important; transform: translateY(-2px); }
    .hover-scale:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important; }
    .hover-border-primary:hover { border-color: var(--bs-primary) !important; }
    
    .cursor-pointer { cursor: pointer; }
    
    .custom-checkbox:has(input:checked) {
        border-color: var(--bs-primary) !important;
        background-color: rgba(var(--bs-primary-rgb), 0.05) !important;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle checkbox styling
        const currencyCheckboxes = document.querySelectorAll('input[name="supported_currencies[]"]');
        currencyCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const parentDiv = this.closest('.custom-checkbox');
                if (this.checked) {
                    parentDiv.classList.add('border-primary', 'bg-soft-primary');
                    parentDiv.classList.remove('border-soft-light', 'bg-light');
                } else {
                    parentDiv.classList.remove('border-primary', 'bg-soft-primary');
                    parentDiv.classList.add('border-soft-light', 'bg-light');
                }
            });
        });
    });
</script>
@endpush
