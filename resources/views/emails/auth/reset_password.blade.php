<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ is_locale_rtl() ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Reset Password Notification') }}</title>
    <style>
        /* Base Reset */
        body, table, td, div, p, a {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        body {
            margin: 0;
            padding: 0;
            background-color: #0b0f19;
            color: #e2e8f0;
            width: 100% !important;
        }
        table {
            border-collapse: collapse !important;
        }
        
        /* Container */
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #0b0f19;
            padding: 40px 20px;
        }
        .main-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #151e32;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid #2a3449;
        }
        
        /* Header */
        .header {
            padding: 40px 40px 20px;
            text-align: center;
        }
        .logo-text {
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            text-decoration: none;
            letter-spacing: 1px;
            background: linear-gradient(135deg, #60a5fa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Content */
        .content {
            padding: 20px 40px 40px;
        }
        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #f8fafc;
            margin-top: 0;
            margin-bottom: 20px;
            text-align: center;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            color: #94a3b8;
            margin-top: 0;
            margin-bottom: 24px;
        }
        
        /* Button */
        .btn-container {
            text-align: center;
            margin: 35px 0;
        }
        .btn {
            display: inline-block;
            padding: 16px 36px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            color: #ffffff !important;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 50px;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
            transition: all 0.3s ease;
        }
        
        /* Subtext */
        .subtext {
            font-size: 14px;
            color: #64748b;
            text-align: center;
        }
        
        /* Footer */
        .footer {
            background-color: #0d1322;
            padding: 30px 40px;
            border-top: 1px solid #1e293b;
            text-align: center;
        }
        .footer p {
            font-size: 13px;
            color: #475569;
            margin-bottom: 10px;
        }
        .raw-url {
            word-break: break-all;
            color: #3b82f6;
            text-decoration: none;
            font-size: 12px;
        }
        
        /* RTL Support */
        html[dir="rtl"] .content, html[dir="rtl"] .header, html[dir="rtl"] .footer {
            text-align: right;
        }
        html[dir="rtl"] h1 {
            text-align: center;
        }
    </style>
</head>
<body>
    @php
        $siteName = \App\Models\Setting::first()->titer ?? config('app.name', 'MYADS');
    @endphp
    <div class="wrapper">
        <table class="main-container" width="100%" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
                <td class="header">
                    <a href="{{ url('/') }}" class="logo-text">
                        {{ $siteName }}
                    </a>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <h1>{{ __('Reset Password') }}</h1>
                    
                    <p>{{ __('Hello!') }} {{ $user->username ?? '' }}</p>
                    
                    <p>{{ __('You are receiving this email because we received a password reset request for your account.') }}</p>
                    
                    <div class="btn-container">
                        <a href="{{ $url }}" class="btn">{{ __('Reset Password') }}</a>
                    </div>
                    
                    <p class="subtext">{{ __('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]) }}</p>
                    
                    <p>{{ __('If you did not request a password reset, no further action is required.') }}</p>
                    
                    <p style="margin-bottom: 0;">{{ __('Regards,') }}<br>{{ $siteName }}</p>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    <p>{{ __('If you\'re having trouble clicking the ":actionText" button, copy and paste the URL below into your web browser:', ['actionText' => __('Reset Password')]) }}</p>
                    <a href="{{ $url }}" class="raw-url">{{ $url }}</a>
                    
                    <p style="margin-top: 20px; font-size: 12px;">&copy; {{ date('Y') }} {{ $siteName }}. {{ __('All rights reserved.') }}</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
