@extends('admin::layouts.admin')

@section('title', __('messages.billing_currencies_title'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.billing.overview') }}">{{ __('messages.billing_feature_title') }}</a></li>
                <li>{{ __('messages.billing_currencies_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.billing_admin_eyebrow') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.billing_currencies_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.billing_currencies_help') }}</p>
        </div>
    </section>

    <div class="mt-4">
        @include('admin::admin.billing.partials.nav', ['currentTab' => 'currencies'])
    </div>

    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mt-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        <div class="row g-3 mt-1">
            <div class="col-xl-7">
                <section class="admin-panel">
                    <div class="admin-panel__header">
                        <div>
                            <span class="admin-panel__eyebrow">{{ __('messages.billing_currencies_tab') }}</span>
                            <h2 class="admin-panel__title">{{ __('messages.billing_currency_library_title') }}</h2>
                        </div>
                    </div>
                    <div class="admin-panel__body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('messages.code') }}</th>
                                        <th>{{ __('messages.symbol') }}</th>
                                        <th>{{ __('messages.billing_exchange_rate_label') }}</th>
                                        <th>{{ __('messages.billing_decimal_places_label') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                        <th>{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($currencies as $currency)
                                        <tr>
                                            <td class="fw-semibold">
                                                {{ $currency->code }}
                                                @if($currency->is_base)
                                                    <span class="badge bg-primary-subtle text-primary">{{ __('messages.billing_base_currency_badge') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $currency->symbol ?: '-' }}</td>
                                            <td>{{ number_format((float) $currency->exchange_rate, 6) }}</td>
                                            <td>{{ $currency->decimal_places }}</td>
                                            <td>
                                                <span class="badge {{ $currency->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">
                                                    {{ $currency->is_active ? __('messages.active') : __('messages.inactive') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    @if(!$currency->is_base)
                                                        <form action="{{ route('admin.billing.currencies.base', $currency->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-light">{{ __('messages.billing_set_base_currency') }}</button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('admin.billing.currencies', ['edit' => $currency->id]) }}" class="btn btn-sm btn-light">{{ __('messages.edit') }}</a>
                                                    @if(!$currency->is_base)
                                                        <form action="{{ route('admin.billing.currencies.delete', $currency->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('messages.delete') }}</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">{{ __('messages.no_data') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-xl-5">
                <form action="{{ $editingCurrency ? route('admin.billing.currencies.update', $editingCurrency->id) : route('admin.billing.currencies.store') }}" method="POST" class="d-grid gap-3">
                    @csrf
                    <section class="admin-panel">
                        <div class="admin-panel__header">
                            <div>
                                <span class="admin-panel__eyebrow">{{ $editingCurrency ? __('messages.edit') : __('messages.add') }}</span>
                                <h2 class="admin-panel__title">{{ $editingCurrency ? __('messages.billing_edit_currency_title') : __('messages.billing_create_currency_title') }}</h2>
                            </div>
                        </div>
                        <div class="admin-panel__body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.code') }}</label>
                                    <input type="text" name="code" class="form-control" value="{{ old('code', $editingCurrency->code ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.name') }}</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $editingCurrency->name ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.symbol') }}</label>
                                    <input type="text" name="symbol" class="form-control" value="{{ old('symbol', $editingCurrency->symbol ?? '') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.billing_exchange_rate_label') }}</label>
                                    <input type="number" step="0.000001" name="exchange_rate" class="form-control" value="{{ old('exchange_rate', $editingCurrency->exchange_rate ?? 1) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.billing_decimal_places_label') }}</label>
                                    <input type="number" name="decimal_places" class="form-control" value="{{ old('decimal_places', $editingCurrency->decimal_places ?? 2) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('messages.order') }}</label>
                                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $editingCurrency->sort_order ?? 0) }}">
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="billing_currency_is_active" value="1" @checked(old('is_active', $editingCurrency->is_active ?? true))>
                                        <label class="form-check-label" for="billing_currency_is_active">{{ __('messages.active') }}</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_base" id="billing_currency_is_base" value="1" @checked(old('is_base', $editingCurrency->is_base ?? false))>
                                        <label class="form-check-label" for="billing_currency_is_base">{{ __('messages.billing_base_currency_badge') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="admin-panel">
                        <div class="admin-panel__body d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
                        </div>
                    </section>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
