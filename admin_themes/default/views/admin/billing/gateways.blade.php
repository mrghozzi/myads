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
                                <h2 class="admin-panel__title">{{ $gateway['label'] }}</h2>
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
