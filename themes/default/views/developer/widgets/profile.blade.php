<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $owner->username }} - Profile Card</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #ffffff;
            color: #22263f;
            padding: 16px;
            overflow: hidden;
        }
        .profile-card {
            border: 1px solid rgba(97, 93, 250, 0.16);
            border-radius: 14px;
            padding: 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 8px 24px rgba(94, 92, 154, 0.08);
            background: #ffffff;
        }
        .avatar-wrap {
            position: relative;
            width: 54px;
            height: 54px;
            flex-shrink: 0;
        }
        .avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #615dfa;
        }
        .avatar-fallback {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: linear-gradient(135deg, #615dfa, #23d2e2);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 20px;
        }
        .info {
            flex: 1;
            min-width: 0;
        }
        .username {
            font-size: 15px;
            font-weight: 800;
            color: #22263f;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .bio {
            font-size: 12px;
            color: #8e93b4;
            margin-top: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .stats {
            display: flex;
            gap: 12px;
            margin-top: 8px;
            font-size: 11px;
            color: #5f6480;
        }
        .stats strong {
            color: #615dfa;
        }
        .powered-by {
            font-size: 10px;
            color: #8e93b4;
            text-align: right;
            margin-top: 8px;
        }
        .powered-by a {
            color: #615dfa;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="profile-card">
        <div class="avatar-wrap">
            @if($owner->img)
                <img src="{{ asset($owner->img) }}" alt="{{ $owner->username }}" class="avatar-img">
            @else
                <div class="avatar-fallback">{{ strtoupper(mb_substr($owner->username, 0, 1)) }}</div>
            @endif
        </div>
        <div class="info">
            <a href="{{ url('/' . $owner->username) }}" target="_blank" class="username">
                {{ $owner->username }}
                @if(!empty($owner->ucheck))
                    <i class="fa fa-circle-check" style="color: #23d2e2; font-size: 13px;"></i>
                @endif
            </a>
            <p class="bio">{{ $owner->about_me ?: __('messages.community_member') }}</p>
            <div class="stats">
                <span><strong>{{ $followersCount }}</strong> {{ __('messages.followers') }}</span>
                <span><strong>{{ number_format($owner->pts ?? 0) }}</strong> PTS</span>
            </div>
        </div>
    </div>
    <div class="powered-by">
        {{ __('messages.powered_by') }} <a href="{{ url('/') }}" target="_blank">MYADS</a>
    </div>

    <script>
        window.addEventListener('load', function() {
            if (window.parent && window.parent !== window) {
                window.parent.postMessage({ type: 'myads:widget:resize', widget: 'profile', height: document.body.scrollHeight }, '*');
            }
        });
    </script>
</body>
</html>
