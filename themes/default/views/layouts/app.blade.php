@php
    $pageLocale = str_replace('_', '-', app()->getLocale());
    $pageDirection = locale_direction();
@endphp
<!DOCTYPE html>
<html lang="{{ $pageLocale }}" dir="{{ $pageDirection }}" data-dir="{{ $pageDirection }}" class="{{ $pageDirection }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="{{ theme_asset('css/style.css') }}">
    @if(is_locale_rtl())
        <link rel="stylesheet" href="{{ theme_asset('css/rtl.css') }}">
    @endif
</head>
<body data-dir="{{ $pageDirection }}" class="{{ $pageDirection }}">
    <header>
        <nav>
            <a href="{{ url('/') }}">{{ __('messages.home') }}</a>
            <a href="{{ url('/login') }}">{{ __('messages.login') }}</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('messages.all_rights_reserved') }}</p>
    </footer>
    
    <script src="{{ theme_asset('js/script.js') }}"></script>
</body>
</html>
