@once
    @php
        $mode = \Illuminate\Support\Facades\Cookie::get('modedark', 'css');
        $cssPath = $mode === 'css_d' ? 'css_d' : 'css';
    @endphp

    @push('head')
        <link
            id="theme-profile-relationships"
            data-theme-link="true"
            href="{{ theme_asset($cssPath . '/profile-relationships.css') }}"
            rel="stylesheet"
            type="text/css"
        />
    @endpush
@endonce
