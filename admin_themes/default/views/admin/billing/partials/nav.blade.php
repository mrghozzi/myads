@php
    $tabs = [
        'overview' => [
            'route' => 'admin.billing.overview',
            'label' => __('messages.billing_overview_tab'),
            'icon' => 'feather-activity',
        ],
        'plans' => [
            'route' => 'admin.billing.plans',
            'label' => __('messages.billing_plans_tab'),
            'icon' => 'feather-layers',
        ],
        'orders' => [
            'route' => 'admin.billing.orders',
            'label' => __('messages.billing_orders_tab'),
            'icon' => 'feather-shopping-bag',
        ],
        'transactions' => [
            'route' => 'admin.billing.transactions',
            'label' => __('messages.billing_transactions_tab'),
            'icon' => 'feather-file-text',
        ],
        'currencies' => [
            'route' => 'admin.billing.currencies',
            'label' => __('messages.billing_currencies_tab'),
            'icon' => 'feather-dollar-sign',
        ],
        'gateways' => [
            'route' => 'admin.billing.gateways',
            'label' => __('messages.billing_gateways_tab'),
            'icon' => 'feather-credit-card',
        ],
        'settings' => [
            'route' => 'admin.billing.settings',
            'label' => __('messages.billing_settings_tab'),
            'icon' => 'feather-sliders',
        ],
    ];
    $currentTab = $currentTab ?? 'overview';
@endphp

<div class="admin-toolbar-card d-flex flex-wrap align-items-center justify-content-start gap-2 mb-3">
    @foreach($tabs as $key => $tab)
        @php($isActive = $currentTab === $key)
        <a href="{{ route($tab['route']) }}"
           class="btn {{ $isActive ? 'btn-primary shadow-sm fw-bold' : 'btn-light fw-medium text-dark' }} d-inline-flex align-items-center gap-2 transition-all"
           style="border-radius: 12px; padding: 0.55rem 1.05rem; font-size: 0.88rem; {{ !$isActive ? 'background: var(--admin-premium-surface); border: 1px solid var(--admin-premium-border);' : '' }}">
            <i class="{{ $tab['icon'] }} fs-6 {{ $isActive ? '' : 'text-primary' }}"></i>
            <span>{{ $tab['label'] }}</span>
        </a>
    @endforeach
</div>
