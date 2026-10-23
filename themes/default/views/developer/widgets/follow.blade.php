<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Follow {{ $owner->username }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: transparent;
            display: flex;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        .widget-follow-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #615dfa;
            color: #ffffff;
            border: none;
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(97, 93, 250, 0.3);
        }
        .widget-follow-btn:hover {
            background: #4e4ac8;
            transform: translateY(-1px);
        }
        .widget-follow-btn.is-following {
            background: rgba(97, 93, 250, 0.15);
            color: #615dfa;
            box-shadow: none;
            border: 1px solid #615dfa;
        }
        .widget-counter {
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 7px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 800;
        }
        .widget-follow-btn.is-following .widget-counter {
            background: rgba(97, 93, 250, 0.2);
        }
        .widget-avatar {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <button type="button" id="follow-btn" class="widget-follow-btn {{ $isFollowing ? 'is-following' : '' }}">
        @if($owner->img)
            <img src="{{ asset($owner->img) }}" alt="{{ $owner->username }}" class="widget-avatar">
        @else
            <i class="fa fa-user-plus"></i>
        @endif
        <span id="follow-text">{{ $isFollowing ? __('messages.following') : __('messages.follow') }}</span>
        <span id="counter" class="widget-counter">{{ $followersCount }}</span>
    </button>

    <script>
        window.addEventListener('load', function() {
            if (window.parent && window.parent !== window) {
                window.parent.postMessage({ type: 'myads:widget:resize', widget: 'follow', height: document.body.scrollHeight }, '*');
            }
        });
        document.getElementById('follow-btn').addEventListener('click', function() {
            var btn = this;
            fetch('{{ route("developer.widget.follow.toggle", $app->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(function(res) {
                if (res.status === 401) {
                    window.open('{{ route("login") }}', '_blank');
                    return null;
                }
                return res.json();
            })
            .then(function(data) {
                if (!data) return;
                if (data.success) {
                    if (data.following) {
                        btn.classList.add('is-following');
                        document.getElementById('follow-text').textContent = '{{ __("messages.following") }}';
                    } else {
                        btn.classList.remove('is-following');
                        document.getElementById('follow-text').textContent = '{{ __("messages.follow") }}';
                    }
                    document.getElementById('counter').textContent = data.count;
                }
            });
        });
    </script>
</body>
</html>
