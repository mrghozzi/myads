@extends('admin::layouts.admin')

@section('title', __('messages.billing_currencies_title'))

@section('content')
<div class="admin-page">
    {{-- Superdesign Hero --}}
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.billing.overview') }}">{{ __('messages.billing_feature_title') }}</a></li>
                <li>{{ __('messages.billing_currencies_tab') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.billing_admin_eyebrow') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.billing_currencies_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.billing_currencies_help') }}</p>
        </div>
        <div class="admin-hero__actions">
            @if($editingCurrency)
                <a href="{{ route('admin.billing.currencies') }}" class="btn btn-light fw-bold text-dark d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 12px; padding: 0.6rem 1.25rem; background: var(--admin-premium-surface); border: 1px solid var(--admin-premium-border);">
                    <i class="feather-plus-circle text-primary fs-5"></i>
                    <span>{{ __('messages.billing_create_currency_title') }}</span>
                </a>
            @else
                <button type="button" onclick="document.getElementById('currency-code-input')?.focus();" class="btn btn-primary fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: 12px; padding: 0.6rem 1.25rem;">
                    <i class="feather-plus-circle fs-5"></i>
                    <span>{{ __('messages.billing_create_currency_title') }}</span>
                </button>
            @endif
        </div>
    </section>

    {{-- Billing Navigation Tabs --}}
    @include('admin::admin.billing.partials.nav', ['currentTab' => 'currencies'])

    {{-- Alerts & Toasts --}}
    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mb-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        <div class="row g-4">
            {{-- Currencies List (Left Column) --}}
            <div class="col-xl-7">
                <div class="admin-panel h-100">
                    <div class="admin-panel__header d-flex align-items-center justify-content-between">
                        <div>
                            <div class="admin-panel__eyebrow">{{ __('messages.billing_currencies_tab') }}</div>
                            <h3 class="admin-panel__title">{{ __('messages.billing_currency_library_title') }}</h3>
                        </div>
                        <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 fs-12 fw-bold">
                            {{ count($currencies) }} {{ __('messages.billing_currencies_tab') }}
                        </span>
                    </div>

                    <div class="admin-panel__body p-0">
                        <div class="admin-table-wrap">
                            <table class="table admin-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">{{ __('messages.code') }}</th>
                                        <th>{{ __('messages.symbol') }}</th>
                                        <th>{{ __('messages.billing_exchange_rate_label') }}</th>
                                        <th>{{ __('messages.billing_decimal_places_label') }}</th>
                                        <th>{{ __('messages.status') }}</th>
                                        <th class="pe-4 text-end">{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($currencies as $currency)
                                        <tr id="currency-row-{{ $currency->id }}" class="transition-all {{ $editingCurrency && $editingCurrency->id === $currency->id ? 'bg-soft-primary' : '' }}">
                                            <td class="ps-4 fw-bold">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="fs-14 text-dark font-monospace">{{ $currency->code }}</span>
                                                    @if($currency->is_base)
                                                        <span id="base-badge-{{ $currency->id }}" class="badge bg-soft-primary text-primary border border-primary border-opacity-25 rounded-pill px-2 py-0 fs-11">
                                                            <i class="feather-star me-1 text-warning"></i>{{ __('messages.billing_base_currency_badge') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($currency->name)
                                                    <div class="text-muted small fs-12">{{ $currency->name }}</div>
                                                @endif
                                            </td>
                                            <td class="fs-14 fw-bold text-dark">{{ $currency->symbol ?: '-' }}</td>
                                            <td class="fw-semibold text-dark fs-13 font-monospace">
                                                {{ number_format((float) $currency->exchange_rate, 4) }}
                                            </td>
                                            <td class="text-muted fs-13">{{ $currency->decimal_places }}</td>
                                            <td>
                                                <span class="badge {{ $currency->is_active ? 'bg-soft-success text-success border border-success border-opacity-25' : 'bg-soft-secondary text-secondary border border-secondary border-opacity-25' }} rounded-pill px-2 py-1 fs-11">
                                                    {{ $currency->is_active ? __('messages.active') : __('messages.inactive') }}
                                                </span>
                                            </td>
                                            <td class="pe-4 text-end">
                                                <div class="d-flex justify-content-end gap-1 flex-wrap">
                                                    @if(!$currency->is_base)
                                                        <button type="button" onclick="setBaseCurrencyAjax({{ $currency->id }}, this)" class="btn btn-sm btn-light fw-bold text-primary shadow-sm d-inline-flex align-items-center gap-1" style="border-radius: 8px; border: 1px solid var(--admin-premium-border);" title="{{ __('messages.billing_set_base_currency') }}">
                                                            <i class="feather-check-circle"></i>
                                                            <span class="d-none d-md-inline">{{ __('messages.billing_set_base_currency') }}</span>
                                                        </button>
                                                    @endif

                                                    <a href="{{ route('admin.billing.currencies', ['edit' => $currency->id]) }}" class="btn btn-sm btn-primary fw-bold shadow-sm d-inline-flex align-items-center justify-content-center" style="border-radius: 8px; width: 32px; height: 32px;" title="{{ __('messages.edit') }}">
                                                        <i class="feather-edit-2"></i>
                                                    </a>

                                                    @if(!$currency->is_base)
                                                        <button type="button" onclick="deleteCurrencyAjax({{ $currency->id }}, this)" class="btn btn-sm btn-light text-danger fw-bold shadow-sm d-inline-flex align-items-center justify-content-center" style="border-radius: 8px; width: 32px; height: 32px; border: 1px solid var(--admin-premium-border);" title="{{ __('messages.delete') }}">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-5">
                                                <div class="d-flex flex-column align-items-center">
                                                    <div class="bg-soft-secondary text-secondary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                                                        <i class="feather-inbox fs-3"></i>
                                                    </div>
                                                    <span class="fw-semibold">{{ __('messages.no_data') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Currency Form (Right Column) --}}
            <div class="col-xl-5">
                <form id="billing-currency-form" action="{{ $editingCurrency ? route('admin.billing.currencies.update', $editingCurrency->id) : route('admin.billing.currencies.store') }}" method="POST">
                    @csrf
                    <div class="admin-panel">
                        <div class="admin-panel__header d-flex align-items-center justify-content-between">
                            <div>
                                <div class="admin-panel__eyebrow">{{ $editingCurrency ? __('messages.edit') : __('messages.add') }}</div>
                                <h3 class="admin-panel__title">{{ $editingCurrency ? __('messages.billing_edit_currency_title') : __('messages.billing_create_currency_title') }}</h3>
                            </div>
                            @if($editingCurrency)
                                <a href="{{ route('admin.billing.currencies') }}" class="btn btn-sm btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="{{ __('messages.cancel') }}">
                                    <i class="feather-x"></i>
                                </a>
                            @endif
                        </div>

                        <div class="admin-panel__body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.code') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="currency-code-input" name="code" class="form-control font-monospace" value="{{ old('code', $editingCurrency->code ?? '') }}" required placeholder="{{ __('messages.billing_currency_code_placeholder') }}" style="border-radius: 10px; text-transform: uppercase;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.name') }}
                                    </label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $editingCurrency->name ?? '') }}" placeholder="{{ __('messages.billing_currency_name_placeholder') }}" style="border-radius: 10px;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.symbol') }}
                                    </label>
                                    <input type="text" name="symbol" class="form-control" value="{{ old('symbol', $editingCurrency->symbol ?? '') }}" placeholder="{{ __('messages.billing_currency_symbol_placeholder') }}" style="border-radius: 10px;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.billing_exchange_rate_label') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.000001" min="0.000001" name="exchange_rate" class="form-control font-monospace" value="{{ old('exchange_rate', $editingCurrency->exchange_rate ?? 1) }}" required style="border-radius: 10px;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.billing_decimal_places_label') }}
                                    </label>
                                    <input type="number" min="0" max="4" name="decimal_places" class="form-control" value="{{ old('decimal_places', $editingCurrency->decimal_places ?? 2) }}" style="border-radius: 10px;">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark small text-uppercase tracking-wider mb-1">
                                        {{ __('messages.order') }}
                                    </label>
                                    <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $editingCurrency->sort_order ?? 0) }}" style="border-radius: 10px;">
                                </div>

                                {{-- Switches --}}
                                <div class="col-12 mt-3">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="p-3 rounded-3 transition-all d-flex align-items-center justify-content-between" style="background: var(--admin-premium-surface-alt); border: 1px solid var(--admin-premium-border);">
                                                <label class="fw-bold text-dark fs-12 mb-0 cursor-pointer" for="billing_currency_is_active">{{ __('messages.active') }}</label>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input shadow-sm" type="checkbox" name="is_active" id="billing_currency_is_active" value="1" @checked(old('is_active', $editingCurrency->is_active ?? true))>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="p-3 rounded-3 transition-all d-flex align-items-center justify-content-between" style="background: var(--admin-premium-surface-alt); border: 1px solid var(--admin-premium-border);">
                                                <label class="fw-bold text-dark fs-12 mb-0 cursor-pointer" for="billing_currency_is_base">{{ __('messages.billing_base_currency_badge') }}</label>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input shadow-sm" type="checkbox" name="is_base" id="billing_currency_is_base" value="1" @checked(old('is_base', $editingCurrency->is_base ?? false))>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="admin-panel__footer d-flex align-items-center justify-content-between p-3 bg-light">
                            <span class="text-muted fs-12">{{ __('messages.currency_note') }}</span>
                            <button type="submit" id="btn-save-currency" class="btn btn-primary fw-bold shadow-sm px-4 d-inline-flex align-items-center gap-2" style="border-radius: 12px;">
                                <i class="feather-save"></i>
                                <span>{{ $editingCurrency ? __('messages.save_changes') : __('messages.save') }}</span>
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
// AJAX Currency Save
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('billing-currency-form');
    var btn = document.getElementById('btn-save-currency');
    if (form && btn) {
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
                    window.showBillingToast(result.data.message || '{{ __("messages.billing_currency_saved") }}', 'success');
                    setTimeout(function() {
                        window.location.href = '{{ route("admin.billing.currencies") }}';
                    }, 700);
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
    }
});

// Set Base Currency AJAX
function setBaseCurrencyAjax(currencyId, btnEl) {
    if (!confirm('{{ __("messages.confirm_set_base_currency") }}')) return;

    if (btnEl) btnEl.disabled = true;

    var url = '{{ route("admin.billing.currencies.base", ":id") }}'.replace(':id', currencyId);
    var formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');

    fetch(url, {
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
        if (result.status >= 200 && result.status < 300 && result.data.success) {
            window.showBillingToast(result.data.message || '{{ __("messages.billing_currency_base_saved") }}', 'success');
            setTimeout(function() { window.location.reload(); }, 600);
        } else {
            if (btnEl) btnEl.disabled = false;
            window.showBillingToast(result.data.message || '{{ __("messages.error_occurred") }}', 'danger');
        }
    })
    .catch(function(err) {
        if (btnEl) btnEl.disabled = false;
        window.showBillingToast(err.message, 'danger');
    });
}

// Delete Currency AJAX
function deleteCurrencyAjax(currencyId, btnEl) {
    if (!confirm('{{ __("messages.confirm_delete") }}')) return;

    if (btnEl) btnEl.disabled = true;

    var url = '{{ route("admin.billing.currencies.delete", ":id") }}'.replace(':id', currencyId);
    var formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'DELETE');

    fetch(url, {
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
        if (result.status >= 200 && result.status < 300 && result.data.success) {
            window.showBillingToast(result.data.message || '{{ __("messages.billing_currency_deleted") }}', 'success');
            var row = document.getElementById('currency-row-' + currencyId);
            if (row) {
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                setTimeout(function() { row.remove(); }, 300);
            }
        } else {
            if (btnEl) btnEl.disabled = false;
            window.showBillingToast(result.data.message || '{{ __("messages.error_occurred") }}', 'danger');
        }
    })
    .catch(function(err) {
        if (btnEl) btnEl.disabled = false;
        window.showBillingToast(err.message, 'danger');
    });
}
</script>
@endpush
