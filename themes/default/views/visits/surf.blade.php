<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $site->name }}</title>
  <style>
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow: hidden;
      font-family: Arial, sans-serif;
    }
    .traffic-container {
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    .header {
      background-color: #0099ff;
      color: white;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      height: 50px;
      box-sizing: border-box;
    }
    .site-info {
      display: flex;
      align-items: center;
      gap: 15px;
    }
    .report-btn {
      color: white;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 5px;
      background: rgba(255,255,255,0.2);
      padding: 5px 10px;
      border-radius: 3px;
      font-size: 0.9em;
    }
    .countdown {
      font-weight: bold;
      font-size: 1.2em;
    }
    .content {
      flex: 1;
      background-color: #f0f0f0;
    }
    .site-frame {
      width: 100%;
      height: 100%;
      border: none;
    }
  </style>
</head>
<body>
  <div class="traffic-container">
    <div class="header">
      <div class="site-info">
        <span style="font-weight: bold;">{{ $site->name }}</span>
        <a href="{{ route('report.index', ['visits' => $site->id]) }}" target="_blank" class="report-btn">{{ __('messages.report') }}</a>
      </div>
      
      <!-- Ad Placeholder -->
      <div class="ad-slot">
        {!! ads_site(3) !!}
      </div>

      <div>{{ __('messages.next_site_in') }} <span class="countdown" id="countdown">{{ $duration }}</span> {{ __('messages.seconds') }}</div>
    </div>
    <div class="content">
      <iframe class="site-frame" src="{{ $site->url }}"></iframe>
    </div>
  </div>

  <script>
    function startCountdown(duration, display) {
        var timer = duration, seconds;
        var interval = setInterval(function () {
            seconds = parseInt(timer, 10);
            display.textContent = seconds;

            if (--timer < 0) {
                clearInterval(interval);
                window.location.reload();
            }
        }, 1000);
    }

    window.onload = function () {
        var duration = {{ $duration }};
        var display = document.querySelector('#countdown');
        startCountdown(duration, display);
    };
  </script>
</body>
</html>
