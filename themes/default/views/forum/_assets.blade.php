@once
    @php
        $mode = \Illuminate\Support\Facades\Cookie::get('modedark', 'css');
        $cssPath = $mode === 'css_d' ? 'css_d' : 'css';
    @endphp

    @push('head')
        <link
            id="theme-forum-redesign"
            data-theme-link="true"
            href="{{ theme_asset($cssPath . '/forum-redesign.css') }}"
            rel="stylesheet"
            type="text/css"
        />
    @endpush

    @push('scripts')
        <script src="{{ theme_asset('js/forum-redesign.js') }}"></script>
    @endpush
@endonce
