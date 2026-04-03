<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ locale_direction() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.error_503_title') }}</title>
    <style>
        :root {
            color-scheme: light dark;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at top, rgba(97, 93, 250, 0.18), transparent 40%),
                linear-gradient(180deg, #f4f6fb 0%, #e9edf7 100%);
            color: #1f2937;
        }

        .status-card {
            width: min(680px, 100%);
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(97, 93, 250, 0.14);
            box-shadow: 0 30px 70px rgba(15, 23, 42, 0.12);
            padding: 40px 32px;
            text-align: center;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(97, 93, 250, 0.12);
            color: #4c46c7;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .status-code {
            margin: 20px 0 10px;
            font-size: clamp(72px, 16vw, 120px);
            line-height: 1;
            font-weight: 900;
            color: #615dfa;
        }

        .status-title {
            margin: 0 0 14px;
            font-size: clamp(28px, 5vw, 38px);
            line-height: 1.15;
        }

        .status-text {
            margin: 0 auto 24px;
            max-width: 520px;
            font-size: 17px;
            line-height: 1.8;
            color: #4b5563;
        }

        .status-note {
            margin: 0;
            font-size: 14px;
            color: #6b7280;
        }

        .status-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 26px;
            padding: 14px 22px;
            border-radius: 14px;
            background: #615dfa;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background:
                    radial-gradient(circle at top, rgba(97, 93, 250, 0.2), transparent 40%),
                    linear-gradient(180deg, #111827 0%, #0f172a 100%);
                color: #f9fafb;
            }

            .status-card {
                background: rgba(17, 24, 39, 0.92);
                border-color: rgba(129, 140, 248, 0.2);
                box-shadow: 0 24px 60px rgba(0, 0, 0, 0.34);
            }

            .status-pill {
                background: rgba(129, 140, 248, 0.18);
                color: #c7d2fe;
            }

            .status-code {
                color: #a5b4fc;
            }

            .status-text,
            .status-note {
                color: #cbd5e1;
            }
        }
    </style>
</head>
<body>
    <main class="status-card">
        <div class="status-pill">{{ __('messages.error_503_title') }}</div>
        <div class="status-code">503</div>
        <h1 class="status-title">{{ __('messages.maintenance_page_title') }}</h1>
        <p class="status-text">{{ __('messages.maintenance_auto_message') }}</p>
        <p class="status-note">{{ __('messages.maintenance_retry_notice') }}</p>
        <a class="status-link" href="{{ url('/') }}">{{ __('messages.back_to_home') }}</a>
    </main>
</body>
</html>
