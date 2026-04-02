@php
    $securityNavItems = [
        [
            'route' => 'admin.security.index',
            'label' => __('messages.security_settings_title'),
            'active' => request()->routeIs('admin.security.index'),
        ],
        [
            'route' => 'admin.security.ip-bans',
            'label' => __('messages.security_ip_bans_title'),
            'active' => request()->routeIs('admin.security.ip-bans*'),
        ],
        [
            'route' => 'admin.security.sessions',
            'label' => __('messages.security_member_sessions_title'),
            'active' => request()->routeIs('admin.security.sessions*'),
        ],
    ];
@endphp

<div class="admin-suite-nav">
    @foreach($securityNavItems as $item)
        <a href="{{ route($item['route']) }}" class="admin-suite-nav__link {{ $item['active'] ? 'is-active' : '' }}">
            {{ $item['label'] }}
        </a>
    @endforeach
</div>
