@php
    $pageLocale = str_replace('_', '-', app()->getLocale());
    $pageDirection = locale_direction();
@endphp
<!DOCTYPE html>
<html lang="{{ $pageLocale }}" dir="{{ $pageDirection }}" data-dir="{{ $pageDirection }}" class="{{ $pageDirection }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
      min-height: 50px;
      box-sizing: border-box;
      flex-wrap: wrap;
      gap: 8px;
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

    /* Status indicator */
    .status-bar {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 0.85em;
      padding: 3px 8px;
      border-radius: 4px;
      transition: all 0.3s ease;
    }
    .status-bar.viewing {
      background: rgba(255,255,255,0.15);
    }
    .status-bar.paused {
      background: rgba(255,200,0,0.3);
    }
    .status-bar.verifying {
      background: rgba(255,255,255,0.25);
    }
    .status-bar.success {
      background: rgba(0,200,80,0.3);
    }
    .status-bar.error {
      background: rgba(255,60,60,0.3);
    }
    .status-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      display: inline-block;
    }
    .viewing .status-dot { background: #4fc3f7; animation: pulse 1.5s infinite; }
    .paused .status-dot { background: #ffd54f; }
    .verifying .status-dot { background: #fff; animation: pulse 0.5s infinite; }
    .success .status-dot { background: #69f0ae; }
    .error .status-dot { background: #ff5252; }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.3; }
    }

    /* Progress bar */
    .progress-wrap {
      width: 100%;
      height: 3px;
      background: rgba(0,0,0,0.1);
    }
    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #4fc3f7, #00e676);
      transition: width 1s linear;
      width: 0%;
    }

    [dir="rtl"] body,
    [dir="rtl"] .traffic-container,
    [dir="rtl"] .content {
      direction: rtl;
    }
    [dir="rtl"] .header,
    [dir="rtl"] .site-info {
      flex-direction: row-reverse;
    }
    [dir="rtl"] .header {
      text-align: right;
    }
  </style>
</head>
<body data-dir="{{ $pageDirection }}" class="{{ $pageDirection }}">
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

      <div style="display:flex; align-items:center; gap:12px;">
        <div class="status-bar viewing" id="statusBar">
          <span class="status-dot"></span>
          <span id="statusText">{{ __('messages.viewing') ?? 'Viewing...' }}</span>
        </div>
        <div>{{ __('messages.next_site_in') }} <span class="countdown" id="countdown">{{ $duration }}</span> {{ __('messages.seconds') }}</div>
      </div>
    </div>
    <div class="progress-wrap">
      <div class="progress-fill" id="progressBar"></div>
    </div>
    <div class="content">
      <iframe class="site-frame" src="{{ $site->url }}"></iframe>
    </div>
  </div>

  <script>
    (function() {
      var DURATION = {{ $duration }};
      var TOKEN = @json($token);
      var VERIFY_URL = @json(route('visits.verify'));
      var CHALLENGE = {{ $challenge }};
      var CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      var timer = DURATION;
      var paused = false;
      var verified = false;
      var display = document.getElementById('countdown');
      var statusBar = document.getElementById('statusBar');
      var statusText = document.getElementById('statusText');
      var progressBar = document.getElementById('progressBar');

      // Update progress bar
      function updateProgress() {
        var pct = ((DURATION - timer) / DURATION) * 100;
        progressBar.style.width = Math.min(pct, 100) + '%';
      }

      function setStatus(cls, text) {
        statusBar.className = 'status-bar ' + cls;
        statusText.textContent = text;
      }

      // Focus detection — pause when window loses focus
      var hasFocus = document.hasFocus();
      window.addEventListener('focus', function() {
        if (paused && !verified) {
          paused = false;
          hasFocus = true;
          setStatus('viewing', '{{ __("messages.viewing") ?? "Viewing..." }}');
        }
      });
      window.addEventListener('blur', function() {
        if (!verified && timer > 0) {
          paused = true;
          hasFocus = false;
          setStatus('paused', '{{ __("messages.visit_paused") ?? "Paused — return to window" }}');
        }
      });

      // Solve JS challenge (simple math proof)
      function solveChallenge(c) {
        // Challenge is a number; solution = challenge * 7 + 3 (must match server)
        return (c * 7 + 3);
      }

      // Verify the visit via AJAX
      function verifyVisit() {
        if (verified) return;
        verified = true;

        setStatus('verifying', '{{ __("messages.visit_verifying") ?? "Verifying..." }}');

        var xhr = new XMLHttpRequest();
        xhr.open('POST', VERIFY_URL, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', CSRF);
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.onreadystatechange = function() {
          if (xhr.readyState !== 4) return;

          try {
            var resp = JSON.parse(xhr.responseText);
          } catch(e) {
            var resp = { success: false, message: 'Error' };
          }

          if (xhr.status === 200 && resp.success) {
            setStatus('success', resp.message || '{{ __("messages.visit_verified") ?? "Points awarded!" }}');
            progressBar.style.width = '100%';
            progressBar.style.background = 'linear-gradient(90deg, #00e676, #69f0ae)';
          } else {
            setStatus('error', resp.message || '{{ __("messages.visit_error") ?? "Error" }}');
          }

          // Reload for next site after 2 seconds
          setTimeout(function() {
            window.location.reload();
          }, 2000);
        };

        xhr.onerror = function() {
          setStatus('error', '{{ __("messages.visit_network_error") ?? "Network error" }}');
          setTimeout(function() { window.location.reload(); }, 3000);
        };

        xhr.send(JSON.stringify({
          token: TOKEN,
          challenge_answer: solveChallenge(CHALLENGE)
        }));
      }

      // Countdown
      var interval = setInterval(function() {
        if (paused) return;

        display.textContent = timer;
        updateProgress();

        if (timer <= 0) {
          clearInterval(interval);
          display.textContent = '0';
          verifyVisit();
          return;
        }

        timer--;
      }, 1000);

      // Initial progress
      updateProgress();
    })();
  </script>
</body>
</html>
