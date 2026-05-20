@extends('admin::layouts.admin')

@section('title', __('messages.billing_gateways_title'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.billing.overview') }}">{{ __('messages.billing_feature_title') }}</a></li>
                <li>{{ __('messages.billing_gateways_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.billing_admin_eyebrow') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.billing_gateways_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.billing_gateways_help') }}</p>
        </div>
    </section>

    <div class="mt-4">
        @include('admin::admin.billing.partials.nav', ['currentTab' => 'gateways'])
    </div>

    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mt-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        <div class="d-grid gap-3 mt-4">
            @foreach($gatewayDefinitions as $gateway)
                @php $config = $gateway['config']; @endphp
                <form action="{{ route('admin.billing.gateways.update', $gateway['key']) }}" method="POST">
                    @csrf
                    <section class="admin-panel">
                        <div class="admin-panel__header">
                            <div>
                                <span class="admin-panel__eyebrow">{{ __('messages.billing_gateways_tab') }}</span>
                                <h2 class="admin-panel__title d-inline-flex align-items-center flex-wrap gap-2">
                                    <span>{{ $gateway['label'] }}</span>
                                    @if(in_array($gateway['key'], ['tabby', 'flouci', 'apple_pay'], true))
                                        <span class="badge bg-warning text-dark" style="font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('messages.billing_gateway_beta') }}</span>
                                    @endif
                                    @if($gateway['key'] === 'tabby')
                                        <span class="badge bg-light text-secondary border" style="font-size: 0.75rem; font-weight: normal;">{{ __('messages.billing_gateway_tabby_restriction') }}</span>
                                    @endif
                                    @if($gateway['key'] === 'flouci')
                                        <span class="badge bg-light text-secondary border" style="font-size: 0.75rem; font-weight: normal;">{{ __('messages.billing_gateway_flouci_restriction') }}</span>
                                    @endif
                                </h2>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="enabled" id="enabled_{{ $gateway['key'] }}" value="1" @checked(!empty($config['enabled']))>
                                <label class="form-check-label" for="enabled_{{ $gateway['key'] }}">{{ __('messages.active') }}</label>
                            </div>
                        </div>
                        <div class="admin-panel__body">
                            <div class="row g-3">
                                @if($gateway['supports_mode'])
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('messages.billing_gateway_mode_label') }}</label>
                                        <select name="mode" class="form-select">
                                            <option value="sandbox" @selected(($config['mode'] ?? 'sandbox') === 'sandbox')>{{ __('messages.billing_gateway_mode_sandbox') }}</option>
                                            <option value="live" @selected(($config['mode'] ?? 'sandbox') === 'live')>{{ __('messages.billing_gateway_mode_live') }}</option>
                                        </select>
                                    </div>
                                @endif

                                @if($gateway['key'] === 'stripe')
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_publishable_key_label') }}</label>
                                        <input type="text" name="publishable_key" class="form-control" value="{{ old('publishable_key', $config['publishable_key'] ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_secret_key_label') }}</label>
                                        <input type="password" name="secret_key" class="form-control" value="">
                                        <div class="form-text">
                                            {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                            @if(!empty($config['secret_key']))
                                                {{ __('messages.billing_secret_current_masked', ['value' => $config['secret_key']]) }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_webhook_secret_label') }}</label>
                                        <input type="password" name="webhook_secret" class="form-control" value="">
                                        <div class="form-text">
                                            {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                            @if(!empty($config['webhook_secret']))
                                                {{ __('messages.billing_secret_current_masked', ['value' => $config['webhook_secret']]) }}
                                            @endif
                                        </div>
                                        <div class="mt-2 p-2 bg-light rounded border">
                                            <div class="small fw-semibold mb-1 text-muted">{{ __('messages.billing_webhook_url_label') }} ({{ __('messages.billing_stripe_webhook_hint') }}):</div>
                                            <code class="user-select-all d-block text-break">{{ route('billing.webhook', ['gateway' => 'stripe']) }}</code>
                                        </div>
                                    </div>
                                @elseif($gateway['key'] === 'paypal')
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_client_id_label') }}</label>
                                        <input type="text" name="client_id" class="form-control" value="{{ old('client_id', $config['client_id'] ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_secret_key_label') }}</label>
                                        <input type="password" name="secret_key" class="form-control" value="">
                                        <div class="form-text">
                                            {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                            @if(!empty($config['secret_key']))
                                                {{ __('messages.billing_secret_current_masked', ['value' => $config['secret_key']]) }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_webhook_id_label') }}</label>
                                        <input type="text" name="webhook_id" class="form-control" value="{{ old('webhook_id', $config['webhook_id'] ?? '') }}">
                                        <div class="mt-2 p-2 bg-light rounded border">
                                            <div class="small fw-semibold mb-1 text-muted">{{ __('messages.billing_webhook_url_label') }} ({{ __('messages.billing_paypal_webhook_hint') }}):</div>
                                            <code class="user-select-all d-block text-break">{{ route('billing.webhook', ['gateway' => 'paypal']) }}</code>
                                        </div>
                                    </div>
                                @elseif($gateway['key'] === 'bank_transfer')
                                    <div class="col-12">
                                        <label class="form-label">{{ __('messages.billing_bank_instructions_label') }}</label>
                                        <textarea name="instructions" class="form-control" rows="5">{{ old('instructions', $config['instructions'] ?? '') }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">{{ __('messages.billing_bank_note_label') }}</label>
                                        <textarea name="note" class="form-control" rows="4">{{ old('note', $config['note'] ?? '') }}</textarea>
                                    </div>
                                @elseif($gateway['key'] === 'lemon_squeezy')
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_store_id_label') }}</label>
                                        <input type="text" name="store_id" class="form-control" value="{{ old('store_id', $config['store_id'] ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_variant_id_label') }}</label>
                                        <input type="text" name="variant_id" class="form-control" value="{{ old('variant_id', $config['variant_id'] ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_api_key_label') }}</label>
                                        <input type="password" name="api_key" class="form-control" value="">
                                        <div class="form-text">
                                            {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                            @if(!empty($config['api_key']))
                                                {{ __('messages.billing_secret_current_masked', ['value' => $config['api_key']]) }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_webhook_secret_label') }}</label>
                                        <input type="password" name="webhook_secret" class="form-control" value="">
                                        <div class="form-text">
                                            {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                            @if(!empty($config['webhook_secret']))
                                                {{ __('messages.billing_secret_current_masked', ['value' => $config['webhook_secret']]) }}
                                            @endif
                                        </div>
                                        <div class="mt-2 p-2 bg-light rounded border">
                                            <div class="small fw-semibold mb-1 text-muted">{{ __('messages.billing_webhook_url_label') }} ({{ __('messages.billing_lemon_squeezy_webhook_hint') }}):</div>
                                            <code class="user-select-all d-block text-break">{{ route('billing.webhook', ['gateway' => 'lemon_squeezy']) }}</code>
                                        </div>
                                    </div>
                                @elseif($gateway['key'] === 'paddle')
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_api_key_label') }}</label>
                                        <input type="password" name="api_key" class="form-control" value="">
                                        <div class="form-text">
                                            {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                            @if(!empty($config['api_key']))
                                                {{ __('messages.billing_secret_current_masked', ['value' => $config['api_key']]) }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_paddle_price_id_label') }}</label>
                                        <input type="text" name="price_id" class="form-control" value="{{ old('price_id', $config['price_id'] ?? '') }}">
                                        <div class="form-text">
                                            {{ __('messages.billing_paddle_price_id_help') }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_webhook_secret_label') }}</label>
                                        <input type="password" name="webhook_secret" class="form-control" value="">
                                        <div class="form-text">
                                            {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                            @if(!empty($config['webhook_secret']))
                                                {{ __('messages.billing_secret_current_masked', ['value' => $config['webhook_secret']]) }}
                                            @endif
                                        </div>
                                        <div class="mt-2 p-2 bg-light rounded border">
                                            <div class="small fw-semibold mb-1 text-muted">Webhook URL ({{ __('messages.billing_paddle_webhook_hint') }}):</div>
                                            <code class="user-select-all d-block text-break">{{ route('billing.webhook', ['gateway' => 'paddle']) }}</code>
                                        </div>
                                    </div>
                                @elseif($gateway['key'] === 'tabby')
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('messages.billing_tabby_region_label') }}</label>
                                        <select name="region" class="form-select">
                                            <option value="UAE" @selected(($config['region'] ?? 'UAE') === 'UAE')>{{ __('messages.billing_tabby_region_uae') }}</option>
                                            <option value="KSA" @selected(($config['region'] ?? 'UAE') === 'KSA')>{{ __('messages.billing_tabby_region_ksa') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('messages.billing_tabby_public_key_label') }}</label>
                                        <input type="text" name="public_key" class="form-control" value="{{ old('public_key', $config['public_key'] ?? '') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('messages.billing_tabby_merchant_code_label') }}</label>
                                        <input type="text" name="merchant_code" class="form-control" value="{{ old('merchant_code', $config['merchant_code'] ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_secret_key_label') }}</label>
                                        <input type="password" name="secret_key" class="form-control" value="">
                                        <div class="form-text">
                                            {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                            @if(!empty($config['secret_key']))
                                                {{ __('messages.billing_secret_current_masked', ['value' => $config['secret_key']]) }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_webhook_secret_label') }}</label>
                                        <input type="password" name="webhook_secret" class="form-control" value="">
                                        <div class="form-text">
                                            {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                            @if(!empty($config['webhook_secret']))
                                                {{ __('messages.billing_secret_current_masked', ['value' => $config['webhook_secret']]) }}
                                            @endif
                                        </div>
                                        <div class="mt-2 p-2 bg-light rounded border">
                                            <div class="small fw-semibold mb-1 text-muted">{{ __('messages.billing_webhook_url_label') }} ({{ __('messages.billing_tabby_webhook_hint') }}):</div>
                                            <code class="user-select-all d-block text-break">{{ route('billing.webhook', ['gateway' => 'tabby']) }}</code>
                                        </div>
                                    </div>
                                @elseif($gateway['key'] === 'flouci')
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_publishable_key_label') }}</label>
                                        <input type="text" name="public_key" class="form-control" value="{{ old('public_key', $config['public_key'] ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_secret_key_label') }}</label>
                                        <input type="password" name="secret_key" class="form-control" value="">
                                        <div class="form-text">
                                            {{ __('messages.billing_leave_blank_to_keep_secret') }}
                                            @if(!empty($config['secret_key']))
                                                {{ __('messages.billing_secret_current_masked', ['value' => $config['secret_key']]) }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <div class="p-2 bg-light rounded border">
                                            <div class="small fw-semibold mb-1 text-muted">{{ __('messages.billing_webhook_url_label') }} ({{ __('messages.billing_flouci_webhook_hint') }}):</div>
                                            <code class="user-select-all d-block text-break">{{ route('billing.webhook', ['gateway' => 'flouci']) }}</code>
                                        </div>
                                    </div>
                                @elseif($gateway['key'] === 'apple_pay')
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('messages.billing_apple_merchant_id_label') }}</label>
                                        <input type="text" name="merchant_id" class="form-control" value="{{ old('merchant_id', $config['merchant_id'] ?? '') }}">
                                        <div class="form-text">{{ __('messages.billing_apple_merchant_id_help') }}</div>
                                    </div>
                                @endif

                                <div class="col-12">
                                    <label class="form-label">{{ __('messages.billing_gateway_supported_currencies_label') }}</label>
                                    <div class="row g-2">
                                        @foreach($currencies as $currency)
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="supported_currencies[]" id="{{ $gateway['key'] }}_currency_{{ $currency->code }}" value="{{ $currency->code }}" @checked(in_array($currency->code, (array) ($config['supported_currencies'] ?? []), true))>
                                                    <label class="form-check-label" for="{{ $gateway['key'] }}_currency_{{ $currency->code }}">{{ $currency->code }}</label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="admin-panel__body border-top d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                        </div>
                    </section>
                </form>
            @endforeach
        </div>
    @endif
</div>
@endsection
