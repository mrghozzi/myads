@extends('theme::layouts.master')

@section('content')
<div class="content-grid full" style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div class="widget-box" style="width: 100%; max-width: 480px; padding: 40px;">
        <div class="widget-box-title" style="margin-bottom: 32px; text-align: center;">
            <img src="{{ theme_asset('img/banner/discussion-icon.png') }}" style="width: 64px; margin-bottom: 16px;">
            <h2 class="section-title">{{ __('messages.two_factor_auth') ?? 'Two-Factor Authentication' }}</h2>
            <p class="section-text" style="margin-top: 8px; color: #8f919d;">
                {{ __('messages.two_factor_description') ?? 'Please enter the verification code sent to your email to continue.' }}
            </p>
        </div>

        <form action="{{ url('/two-factor-challenge') }}" method="POST">
            @csrf
            
            <div class="form-row">
                <div class="form-item">
                    <div class="form-input">
                        <label for="code">{{ __('messages.verification_code') ?? 'Verification Code' }}</label>
                        <input type="text" id="code" name="code" required autocomplete="one-time-code" autofocus 
                               style="text-align: center; font-size: 24px; letter-spacing: 8px; font-weight: 700;">
                    </div>
                    @error('code')
                        <p class="form-error" style="color: #f34141; font-size: 12px; margin-top: 4px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-row" style="margin-top: 32px;">
                <div class="form-item">
                    <button type="submit" class="button primary" style="width: 100%;">
                        {{ __('messages.verify') ?? 'Verify' }}
                    </button>
                </div>
            </div>
        </form>

        <div style="margin-top: 24px; text-align: center;">
            <form action="{{ route('two-factor.resend') }}" method="POST">
                @csrf
                <p style="font-size: 14px; color: #8f919d;">
                    {{ __('messages.didnt_receive_code') ?? "Didn't receive the code?" }}
                    <button type="submit" class="button-link" style="background: none; border: none; color: #615dfa; font-weight: 700; cursor: pointer; padding: 0;">
                        {{ __('messages.resend_code') ?? 'Resend Code' }}
                    </button>
                </p>
            </form>
            
            @if(session('success'))
                <p style="color: #339966; font-size: 12px; margin-top: 8px;">{{ session('success') }}</p>
            @endif
        </div>

        <div style="margin-top: 16px; text-align: center;">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="button-link" style="background: none; border: none; color: #8f919d; font-size: 12px; cursor: pointer; padding: 0;">
                    {{ __('messages.cancel_and_logout') ?? 'Cancel and Logout' }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
