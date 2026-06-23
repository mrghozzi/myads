@extends('admin::layouts.admin')

@section('title', __('messages.billing_currencies_title'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<!-- Superdesign Header -->
<div class="row g-0 align-items-center mb-4">
    <div class="col-12 px-4">
        <div class="card border-0 shadow-lg overflow-hidden position-relative" style="border-radius: 24px; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
            <div class="position-absolute top-0 end-0 p-5 opacity-10">
                <i class="fa-solid fa-coins" style="font-size: 160px; transform: rotate(-15deg);"></i>
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
                            {{ __('messages.billing_currencies_title') }}
                        </h1>
                        <p class="lead opacity-80 mb-0 animate__animated animate__fadeIn animate__delay-1s">
                            {{ __('messages.billing_currencies_help') }}
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
            @include('admin::admin.billing.partials.nav', ['currentTab' => 'currencies'])
        </div>
    </div>

    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mb-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        <div class="row g-4 mt-1">
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                    <div class="card-header bg-transparent border-0 p-4 pb-3 border-bottom border-soft-light d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase tracking-wider fw-bold text-muted mb-1 fs-11">{{ __('messages.billing_currencies_tab') }}</div>
                            <h4 class="fw-bold mb-0 text-dark">{{ __('messages.billing_currency_library_title') }}</h4>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="text-uppercase fs-11 fw-bold text-muted bg-soft-light">
                                    <tr>
                                        <th class="ps-4 py-3">{{ __('messages.code') }}</th>
                                        <th class="py-3">{{ __('messages.symbol') }}</th>
                                        <th class="py-3">{{ __('messages.billing_exchange_rate_label') }}</th>
                                        <th class="py-3">{{ __('messages.billing_decimal_places_label') }}</th>
                                        <th class="py-3">{{ __('messages.status') }}</th>
                                        <th class="pe-4 py-3 text-end">{{ __('messages.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="fs-13">
                                    @forelse($currencies as $currency)
                                        <tr class="hover-bg-light transition-all border-bottom border-soft-light">
                                            <td class="ps-4 fw-bold">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="fs-14 text-dark">{{ $currency->code }}</span>
                                                    @if($currency->is_base)
                                                        <span class="badge bg-soft-primary text-primary rounded-pill border">{{ __('messages.billing_base_currency_badge') }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="fs-14 fw-semibold text-muted">{{ $currency->symbol ?: '-' }}</td>
                                            <td class="fw-semibold">{{ number_format((float) $currency->exchange_rate, 6) }}</td>
                                            <td class="text-muted">{{ $currency->decimal_places }}</td>
                                            <td>
                                                <span class="badge {{ $currency->is_active ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }} rounded-pill px-3 border">
                                                    {{ $currency->is_active ? __('messages.active') : __('messages.inactive') }}
                                                </span>
                                            </td>
                                            <td class="pe-4 text-end">
                                                <div class="d-flex justify-content-end gap-2 flex-wrap">
                                                    @if(!$currency->is_base)
                                                        <form action="{{ route('admin.billing.currencies.base', $currency->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-light fw-bold shadow-sm" style="border-radius: 8px;">{{ __('messages.billing_set_base_currency') }}</button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('admin.billing.currencies', ['edit' => $currency->id]) }}" class="btn btn-sm btn-primary fw-bold shadow-sm" style="border-radius: 8px;">
                                                        <i class="feather-edit-2"></i>
                                                    </a>
                                                    @if(!$currency->is_base)
                                                        <form action="{{ route('admin.billing.currencies.delete', $currency->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm_delete') }}');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger fw-bold shadow-sm" style="border-radius: 8px;">
                                                                <i class="feather-trash-2"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-5">
                                                <div class="d-flex flex-column align-items-center">
                                                    <div class="bg-soft-secondary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                                        <i class="feather-inbox fs-3 text-secondary"></i>
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

            <div class="col-xl-5">
                <form action="{{ $editingCurrency ? route('admin.billing.currencies.update', $editingCurrency->id) : route('admin.billing.currencies.store') }}" method="POST" class="d-grid gap-4">
                    @csrf
                    <div class="card border-0 shadow-sm" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                        <div class="card-header bg-transparent border-0 p-4 pb-3 border-bottom border-soft-light d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-uppercase tracking-wider fw-bold text-primary mb-1 fs-11">{{ $editingCurrency ? __('messages.edit') : __('messages.add') }}</div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $editingCurrency ? __('messages.billing_edit_currency_title') : __('messages.billing_create_currency_title') }}</h4>
                            </div>
                            @if($editingCurrency)
                                <a href="{{ route('admin.billing.currencies') }}" class="btn btn-sm btn-light rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                    <i class="feather-x"></i>
                                </a>
                            @endif
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.code') }}</label>
                                    <input type="text" name="code" class="form-control border-soft-light bg-light" value="{{ old('code', $editingCurrency->code ?? '') }}" required style="border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.name') }}</label>
                                    <input type="text" name="name" class="form-control border-soft-light bg-light" value="{{ old('name', $editingCurrency->name ?? '') }}" style="border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.symbol') }}</label>
                                    <input type="text" name="symbol" class="form-control border-soft-light bg-light" value="{{ old('symbol', $editingCurrency->symbol ?? '') }}" style="border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.billing_exchange_rate_label') }}</label>
                                    <input type="number" step="0.000001" name="exchange_rate" class="form-control border-soft-light bg-light" value="{{ old('exchange_rate', $editingCurrency->exchange_rate ?? 1) }}" required style="border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.billing_decimal_places_label') }}</label>
                                    <input type="number" name="decimal_places" class="form-control border-soft-light bg-light" value="{{ old('decimal_places', $editingCurrency->decimal_places ?? 2) }}" style="border-radius: 10px;">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.order') }}</label>
                                    <input type="number" name="sort_order" class="form-control border-soft-light bg-light" value="{{ old('sort_order', $editingCurrency->sort_order ?? 0) }}" style="border-radius: 10px;">
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center p-3 bg-light rounded-3 border border-soft-light h-100">
                                                <div class="form-check form-switch mb-0 w-100 d-flex align-items-center">
                                                    <input class="form-check-input ms-0 mt-0 shadow-sm" type="checkbox" name="is_active" id="billing_currency_is_active" value="1" @checked(old('is_active', $editingCurrency->is_active ?? true)) style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                                    <label class="form-check-label ms-3 fw-bold text-dark cursor-pointer fs-13 flex-grow-1" for="billing_currency_is_active">
                                                        {{ __('messages.active') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center p-3 bg-light rounded-3 border border-soft-light h-100">
                                                <div class="form-check form-switch mb-0 w-100 d-flex align-items-center">
                                                    <input class="form-check-input ms-0 mt-0 shadow-sm" type="checkbox" name="is_base" id="billing_currency_is_base" value="1" @checked(old('is_base', $editingCurrency->is_base ?? false)) style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                                    <label class="form-check-label ms-3 fw-bold text-dark cursor-pointer fs-13 flex-grow-1" for="billing_currency_is_base">
                                                        {{ __('messages.billing_base_currency_badge') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top border-soft-light p-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary fw-bold shadow-sm hover-scale px-4" style="border-radius: 10px;">
                                <i class="feather-save me-2"></i> {{ __('messages.save_changes') }}
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
<style>
    .tracking-wider { letter-spacing: 0.05em; }
    .fw-black { font-weight: 900; }
    .opacity-10 { opacity: 0.1; }
    .opacity-80 { opacity: 0.8; }
    .z-index-1 { z-index: 1; }
    .fs-11 { font-size: 11px; }
    .fs-12 { font-size: 12px; }
    .fs-13 { font-size: 13px; }
    .fs-14 { font-size: 14px; }
    
    .transition-all { transition: all 0.3s ease; }
    .hover-scale:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important; }
    .cursor-pointer { cursor: pointer; }
</style>
@endpush
