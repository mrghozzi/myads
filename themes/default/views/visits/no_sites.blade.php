@php
    $pageLocale = str_replace('_', '-', app()->getLocale());
    $pageDirection = locale_direction();
    $limitReached = $daily_limit_reached ?? false;
@endphp
<!DOCTYPE html>
<html lang="{{ $pageLocale }}" dir="{{ $pageDirection }}" data-dir="{{ $pageDirection }}" class="{{ $pageDirection }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.no_sites') }}</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding-top: 50px; background: #f0f0f0; }
        .message { background: white; padding: 30px; border-radius: 8px; display: inline-block; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 400px; }
        h1 { color: #333; font-size: 1.3em; margin-bottom: 10px; }
        p { color: #666; margin: 8px 0; }
        a { color: #0099ff; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .limit-badge {
            display: inline-block;
            background: #fff3cd;
            color: #856404;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85em;
            margin-bottom: 12px;
            border: 1px solid #ffc107;
        }
        [dir="rtl"] body,
        [dir="rtl"] .message {
            direction: rtl;
            text-align: right;
        }
    </style>
</head>
<body data-dir="{{ $pageDirection }}" class="{{ $pageDirection }}">
    <div class="message">
        @if($limitReached)
            <div class="limit-badge">⚠️ {{ __('messages.daily_limit') ?? 'Daily Limit' }}</div>
            <h1>{{ __('messages.daily_visit_limit_reached') ?? 'You have reached your daily visit limit' }}</h1>
            <p>{{ __('messages.daily_visit_limit_message') ?? 'Come back tomorrow to earn more points!' }}</p>
        @else
            <h1>{{ __('messages.no_sites_available') }}</h1>
            <p>{{ __('messages.check_back_later') }}</p>
            <p>{{ __('messages.next_site_in') }} <span id="countdown">10</span> {{ __('messages.seconds') }}</p>
        @endif
        <p><a href="javascript:window.close();">{{ __('messages.close_window') }}</a></p>
    </div>

    @unless($limitReached)
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
    @endunless
</body>
</html>
