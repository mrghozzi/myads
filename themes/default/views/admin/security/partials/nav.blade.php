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

<div class="card stretch stretch-full mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            @foreach($securityNavItems as $item)
                <a href="{{ route($item['route']) }}" class="btn {{ $item['active'] ? 'btn-primary' : 'btn-light' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</div>
