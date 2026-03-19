@php
    $pageLocale = str_replace('_', '-', app()->getLocale());
    $pageDirection = locale_direction();
@endphp
<!DOCTYPE html>
<html lang="{{ $pageLocale }}" dir="{{ $pageDirection }}" data-dir="{{ $pageDirection }}" class="{{ $pageDirection }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.no_sites') }}</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding-top: 50px; background: #f0f0f0; }
        .message { background: white; padding: 30px; border-radius: 5px; display: inline-block; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        p { color: #666; }
        a { color: #0099ff; text-decoration: none; }
        [dir="rtl"] body,
        [dir="rtl"] .message {
            direction: rtl;
            text-align: right;
        }
    </style>
</head>
<body data-dir="{{ $pageDirection }}" class="{{ $pageDirection }}">
    <div class="message">
        <h1>{{ __('messages.no_sites_available') }}</h1>
        <p>{{ __('messages.check_back_later') }}</p>
        <p>{{ __('messages.next_site_in') }} <span id="countdown">10</span> {{ __('messages.seconds') }}</p>
        <p><a href="javascript:window.close();">{{ __('messages.close_window') }}</a></p>
    </div>
    <script>
        var timer = 10;
        var interval = setInterval(function() {
            timer--;
            document.getElementById('countdown').textContent = timer;
            if (timer <= 0) {
                clearInterval(interval);
                window.location.reload();
            }
        }, 1000);
    </script>
</body>
</html>
