@php
    $tabs = [
        'overview' => ['route' => 'admin.billing.overview', 'label' => __('messages.billing_overview_tab')],
        'plans' => ['route' => 'admin.billing.plans', 'label' => __('messages.billing_plans_tab')],
        'orders' => ['route' => 'admin.billing.orders', 'label' => __('messages.billing_orders_tab')],
        'transactions' => ['route' => 'admin.billing.transactions', 'label' => __('messages.billing_transactions_tab')],
        'currencies' => ['route' => 'admin.billing.currencies', 'label' => __('messages.billing_currencies_tab')],
        'gateways' => ['route' => 'admin.billing.gateways', 'label' => __('messages.billing_gateways_tab')],
        'settings' => ['route' => 'admin.billing.settings', 'label' => __('messages.billing_settings_tab')],
    ];
@endphp

<div class="admin-toolbar-card d-flex flex-wrap gap-2">
    @foreach($tabs as $key => $tab)
        <a href="{{ route($tab['route']) }}" class="btn {{ ($currentTab ?? '') === $key ? 'btn-primary' : 'btn-light' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
