@extends('admin::layouts.admin')

@section('title', __('messages.billing_settings_tab'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<!-- Superdesign Header -->
<div class="row g-0 align-items-center mb-4">
    <div class="col-12 px-4">
        <div class="card border-0 shadow-lg overflow-hidden position-relative" style="border-radius: 24px; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
            <div class="position-absolute top-0 end-0 p-5 opacity-10">
                <i class="fa-solid fa-gear" style="font-size: 160px; transform: rotate(-15deg);"></i>
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
                            {{ __('messages.billing_settings_tab') }}
                        </h1>
                        <p class="lead opacity-80 mb-0 animate__animated animate__fadeIn animate__delay-1s">
                            {{ __('messages.billing_settings_help') }}
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
            @include('admin::admin.billing.partials.nav', ['currentTab' => 'settings'])
        </div>
    </div>

    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mb-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        <form action="{{ route('admin.billing.settings.update') }}" method="POST" class="mt-4 d-grid gap-4">
            @csrf
            <div class="card border-0 shadow-sm" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                <div class="card-body p-4 pt-4">
                    <div class="d-flex align-items-center p-4 bg-soft-primary rounded-3 border-primary border opacity-100 mb-4 shadow-sm" style="border-radius: 16px !important;">
                        <div class="form-check form-switch form-switch-lg mb-0 w-100 d-flex align-items-center">
                            <input class="form-check-input ms-0 mt-0 shadow-sm" type="checkbox" id="enabled" name="enabled" value="1" @checked(!empty($settings['enabled'])) style="width: 3.5em; height: 1.75em; cursor: pointer;">
                            <label class="form-check-label ms-3 fw-bold text-dark cursor-pointer fs-15 flex-grow-1" for="enabled">
                                {{ __('messages.billing_enable_system_label') }}
                            </label>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label fw-bold text-muted small text-uppercase tracking-wider mb-2">{{ __('messages.billing_base_currency_label') }}</label>
                                <select name="base_currency_code" class="form-select form-select-lg border-soft-light bg-light" style="border-radius: 12px;">
                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency->code }}" @selected(($settings['base_currency_code'] ?? 'USD') === $currency->code)>
                                            {{ $currency->code }}{{ $currency->name ? ' - ' . $currency->name : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius: 20px; background: rgba(var(--nxl-white-rgb), 0.8);">
                <div class="card-body p-4 d-flex justify-content-between align-items-center gap-3 flex-wrap bg-light border-top border-soft-light" style="border-radius: 20px;">
                    <div class="d-flex align-items-center gap-2 text-muted">
                        <i class="feather-info fs-5 text-primary"></i>
                        <span class="fs-14">{{ __('messages.billing_settings_runtime_note') }}</span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg fw-bold px-5 shadow-sm hover-scale" style="border-radius: 12px;">
                        <i class="feather-save me-2"></i> {{ __('messages.save_changes') }}
                    </button>
                </div>
            </div>
        </form>
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
    .fs-12 { font-size: 12px; }
    .fs-14 { font-size: 14px; }
    .fs-15 { font-size: 15px; }
    
    .hover-scale:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important; }
    .cursor-pointer { cursor: pointer; }
</style>
@endpush
