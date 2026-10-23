<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $owner->username }} - Posts Feed</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #ffffff;
            color: #22263f;
            padding: 16px;
        }
        .feed-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(97, 93, 250, 0.14);
        }
        .feed-title {
            font-size: 14px;
            font-weight: 800;
            color: #22263f;
        }
        .post-card {
            border: 1px solid rgba(97, 93, 250, 0.12);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 10px;
            background: #f7f8ff;
        }
        .post-text {
            font-size: 13px;
            color: #5f6480;
            line-height: 1.5;
            white-space: pre-line;
        }
        .post-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            font-size: 11px;
            color: #8e93b4;
        }
        .powered-by {
            font-size: 11px;
            color: #8e93b4;
            text-align: right;
            margin-top: 12px;
        }
        .powered-by a {
            color: #615dfa;
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="feed-header">
        <span class="feed-title"><i class="fa fa-newspaper me-1" style="color: #615dfa;"></i> {{ $owner->username }}</span>
        <a href="{{ url('/' . $owner->username) }}" target="_blank" style="font-size: 12px; color: #615dfa; text-decoration: none; font-weight: 700;">{{ __('messages.view_profile') }}</a>
    </div>

    @forelse($posts as $post)
        <div class="post-card">
            <p class="post-text">{{ \Illuminate\Support\Str::limit($post->txt, 140) }}</p>
            <div class="post-meta">
                <span>{{ $post->date ? date('M d, Y', $post->date) : '' }}</span>
                <a href="{{ url('/status/' . $post->id) }}" target="_blank" style="color: #615dfa; text-decoration: none; font-weight: 600;">{{ __('messages.read_more') }} <i class="fa fa-arrow-right"></i></a>
            </div>
        </div>
    @empty
        <div style="text-align: center; padding: 30px; color: #8e93b4; font-size: 13px;">
            {{ __('messages.no_posts_to_display') }}
        </div>
    @endforelse

    <div class="powered-by">
        {{ __('messages.powered_by') }} <a href="{{ url('/') }}" target="_blank">MYADS</a>
    </div>

    <script>
        window.addEventListener('load', function() {
            if (window.parent && window.parent !== window) {
                window.parent.postMessage({ type: 'myads:widget:resize', widget: 'content', height: document.body.scrollHeight }, '*');
            }
        });
    </script>
</body>
</html>
