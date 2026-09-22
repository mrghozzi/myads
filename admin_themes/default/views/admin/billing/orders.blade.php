@extends('admin::layouts.admin')

@section('title', __('messages.billing_orders_title') ?? 'طلبات الفوترة')

@section('content')
<div class="admin-page">
    {{-- Superdesign Hero --}}
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'لوحة التحكم' }}</a></li>
                <li><a href="{{ route('admin.billing.overview') }}">{{ __('messages.billing_feature_title') ?? 'الفوترة' }}</a></li>
                <li>{{ __('messages.billing_orders_tab') ?? 'الطلبات' }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.billing_admin_eyebrow') ?? 'مساحة عمل الإيرادات' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.billing_orders_title') ?? 'طلبات الفوترة والاشتراكات' }}</h1>
            <p class="admin-hero__copy">{{ __('messages.billing_orders_help') ?? 'تتبع كل عملية دفع وتحويل وكافة قرارات المراجعة اليدوية.' }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-soft-primary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-bold fs-12">
                    <i class="feather-shopping-bag me-1"></i>
                    {{ $orders->total() }} {{ __('messages.billing_orders_tab') ?? 'طلب' }}
                </span>
            </div>
        </div>
    </section>

    {{-- Billing Navigation Tabs --}}
    @include('admin::admin.billing.partials.nav', ['currentTab' => 'orders'])

    {{-- Alerts & Toasts --}}
    @include('admin::admin.billing.partials.alerts')

    @if(!empty($upgradeNotice))
        <div class="mb-4">
            @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
        </div>
    @endif

    @if($featureAvailable)
        {{-- Filter Toolbar --}}
        <div class="admin-toolbar-card mb-4">
            <form id="orders-filter-form" method="GET" action="{{ route('admin.billing.orders') }}" class="d-flex flex-wrap align-items-center gap-2 w-100">
                <div class="input-group" style="min-width: 220px; flex: 1 1 240px;">
                    <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 12px 0 0 12px;">
                        <i class="feather-search"></i>
                    </span>
                    <input type="text" name="search" id="orders-search-input" class="form-control border-start-0" value="{{ $search }}" placeholder="{{ __('messages.search_placeholder') ?? 'ابحث برقم الطلب أو المستخدم...' }}" style="border-radius: 0 12px 12px 0;">
                </div>

                <div style="min-width: 170px;">
                    <select name="status" id="orders-status-select" class="form-select" style="border-radius: 12px;">
                        <option value="">{{ __('messages.billing_all_statuses') ?? 'جميع الحالات' }}</option>
                        @foreach(['paid', 'pending_checkout', 'pending_receipt', 'pending_review', 'rejected', 'failed', 'cancelled'] as $statusOption)
                            <option value="{{ $statusOption }}" @selected($status === $statusOption)>
                                {{ __('messages.billing_status_' . $statusOption) ?? $statusOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="min-width: 170px;">
                    <select name="gateway" id="orders-gateway-select" class="form-select" style="border-radius: 12px;">
                        <option value="">{{ __('messages.billing_all_gateways') ?? 'جميع البوابات' }}</option>
                        @foreach($gateways as $gatewayDefinition)
                            <option value="{{ $gatewayDefinition['key'] }}" @selected($gateway === $gatewayDefinition['key'])>
                                {{ $gatewayDefinition['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" id="btn-filter-orders" class="btn btn-primary fw-bold shadow-sm d-inline-flex align-items-center gap-2" style="border-radius: 12px; padding: 0.6rem 1.25rem;">
                    <i class="feather-filter"></i>
                    <span>{{ __('messages.filter') ?? 'تصفية' }}</span>
                </button>

                @if($search !== '' || $status !== '' || $gateway !== '')
                    <a href="{{ route('admin.billing.orders') }}" class="btn btn-light fw-bold text-muted d-inline-flex align-items-center gap-1 shadow-sm" style="border-radius: 12px; border: 1px solid var(--admin-premium-border);">
                        <i class="feather-x"></i>
                        <span>{{ __('messages.reset') ?? 'إعادة تعيين' }}</span>
                    </a>
                @endif
            </form>
        </div>

        {{-- Orders Table Container --}}
        <div id="orders-table-wrapper" class="admin-panel transition-all">
            <div class="admin-panel__body p-0">
                <div class="admin-table-wrap">
                    <table class="table admin-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">{{ __('messages.billing_order_number_label') ?? 'رقم الطلب' }}</th>
                                <th>{{ __('messages.user') ?? 'المستخدم' }}</th>
                                <th>{{ __('messages.plan') ?? 'الخطة' }}</th>
                                <th>{{ __('messages.gateway') ?? 'البوابة' }}</th>
                                <th>{{ __('messages.amount') ?? 'المبلغ' }}</th>
                                <th>{{ __('messages.status') ?? 'الحالة' }}</th>
                                <th>{{ __('messages.date') ?? 'التاريخ' }}</th>
                                <th class="pe-4 text-end">{{ __('messages.actions') ?? 'الإجراءات' }}</th>
                            </tr>
                        </thead>
                        <tbody id="orders-table-body">
                            @forelse($orders as $order)
                                <tr class="transition-all">
                                    <td class="ps-4 fw-bold">
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ route('admin.billing.orders.show', $order->id) }}" class="text-primary text-decoration-none">
                                                {{ $order->order_number }}
                                            </a>
                                            <button type="button" class="btn btn-sm btn-link text-muted p-0 shadow-none" onclick="window.copyBillingText('{{ $order->order_number }}', this);" title="نسخ رقم الطلب">
                                                <i class="feather-copy" style="font-size: 13px;"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 11px;">
                                                {{ strtoupper(substr($order->user->username ?? 'U', 0, 1)) }}
                                            </div>
                                            <span>{{ $order->user->username ?? ('#' . $order->user_id) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ data_get($order->plan_snapshot, 'name', __('messages.billing_subscription_plan') ?? 'خطة اشتراك') }}</td>
                                    <td>
                                        <span class="d-inline-flex align-items-center gap-1 text-dark fs-13">
                                            <i class="feather-credit-card text-muted"></i>
                                            <span>{{ data_get($order->meta, 'gateway_label', $order->gatewayLabel()) }}</span>
                                        </span>
                                    </td>
                                    <td class="fw-bold text-dark fs-14">
                                        {{ $order->display_amount }}
                                        <span class="text-muted fw-normal fs-11 ms-1">{{ $order->currency_code }}</span>
                                    </td>
                                    <td>
                                        @include('admin::admin.billing.partials.status_badge', ['status' => $order->status])
                                    </td>
                                    <td class="text-muted fs-12">
                                        {{ optional($order->created_at)->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('admin.billing.orders.show', $order->id) }}" class="btn btn-sm btn-light fw-bold text-dark shadow-sm d-inline-flex align-items-center gap-1" style="border-radius: 8px; border: 1px solid var(--admin-premium-border);">
                                            <i class="feather-eye"></i>
                                            <span>{{ __('messages.details') ?? 'التفاصيل' }}</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-5">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="bg-soft-secondary text-secondary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px;">
                                                <i class="feather-inbox fs-3"></i>
                                            </div>
                                            <span class="fw-semibold">{{ __('messages.no_data') ?? 'لا توجد طلبات تطابق هذا البحث' }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($orders->hasPages())
                <div class="admin-panel__footer d-flex justify-content-center p-3" id="orders-pagination-container">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('orders-filter-form');
    var wrapper = document.getElementById('orders-table-wrapper');
    if (!form || !wrapper) return;

    function fetchOrders(url) {
        wrapper.style.opacity = '0.5';
        wrapper.style.pointerEvents = 'none';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function(res) {
            return res.text();
        })
        .then(function(html) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var newWrapper = doc.getElementById('orders-table-wrapper');
            if (newWrapper) {
                wrapper.innerHTML = newWrapper.innerHTML;
            }
            wrapper.style.opacity = '1';
            wrapper.style.pointerEvents = 'auto';

            // Bind pagination clicks in new content
            bindPaginationLinks();

            window.history.pushState(null, '', url);
        })
        .catch(function(err) {
            wrapper.style.opacity = '1';
            wrapper.style.pointerEvents = 'auto';
            window.showBillingToast('{{ __("messages.error_occurred") ?? "حدث خطأ أثناء تحميل البيانات" }}', 'danger');
        });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var params = new URLSearchParams(new FormData(form)).toString();
        var url = form.action + (params ? '?' + params : '');
        fetchOrders(url);
    });

    var statusSelect = document.getElementById('orders-status-select');
    var gatewaySelect = document.getElementById('orders-gateway-select');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            form.dispatchEvent(new Event('submit'));
        });
    }
    if (gatewaySelect) {
        gatewaySelect.addEventListener('change', function() {
            form.dispatchEvent(new Event('submit'));
        });
    }

    function bindPaginationLinks() {
        var links = wrapper.querySelectorAll('.pagination a');
        links.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                var href = this.getAttribute('href');
                if (href) {
                    fetchOrders(href);
                }
            });
        });
    }

    bindPaginationLinks();

    window.addEventListener('popstate', function() {
        fetchOrders(window.location.href);
    });
});
</script>
@endpush
