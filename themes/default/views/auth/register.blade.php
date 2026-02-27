@extends('theme::layouts.master')

@section('title', __('messages.sign_up'))

@section('content')
<div class="content">
    <!-- FORM BOX -->
    <div class="widget-box" style="max-width: 480px; margin: 0 auto;">
        @if(session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FORM BOX TITLE -->
        <center><h2>{{ __('messages.creayoacc') }}</h2></center><br />
        <!-- /FORM BOX TITLE -->

        <!-- FORM -->
        <form class="form" method="post" action="{{ route('register.post') }}">
            @csrf
            <div class="form-row">
                <!-- FORM ITEM -->
                <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input">
                        <label for="register-email">{{ __('messages.email') }}</label>
                        <input type="email" id="register-email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <!-- /FORM INPUT -->
                </div>
                <!-- /FORM ITEM -->
            </div>
            <!-- FORM ROW -->
            <div class="form-row">
                <!-- FORM ITEM -->
                <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input">
                        <label for="register-username">{{ __('messages.username') }}</label>
                        <input type="text" id="register-username" name="username" value="{{ old('username') }}" required>
                    </div>
                    <!-- /FORM INPUT -->
                </div>
                <!-- /FORM ITEM -->
            </div>
            <!-- /FORM ROW -->

            <!-- FORM ROW -->
            <div class="form-row">
                <!-- FORM ITEM -->
                <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input">
                        <label for="register-password">{{ __('messages.password') }}</label>
                        <input type="password" id="register-password" name="password" required>
                    </div>
                    <!-- /FORM INPUT -->
                </div>
                <!-- /FORM ITEM -->
            </div>
            <!-- /FORM ROW -->

            <!-- FORM ROW -->
            <div class="form-row">
                <!-- FORM ITEM -->
                <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input">
                        <label for="register-password-repeat">{{ __('messages.rep_password') }}</label>
                        <input type="password" id="register-password-repeat" name="password_confirmation" required>
                    </div>
                    <!-- /FORM INPUT -->
                </div>
                <!-- /FORM ITEM -->
            </div>
            <!-- /FORM ROW -->
            
            <!-- Captcha -->
            <div class="form-row">
                <div class="form-item">
                    <div class="form-input">
                        <label for="capt">{{ __('messages.verification_code') }}</label>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <img src="{{ route('captcha.generate') }}" id="captcha-img" style="cursor: pointer; border-radius: 4px;" title="Click to refresh" onclick="document.getElementById('captcha-img').src='{{ route('captcha.generate') }}?'+Math.random()">
                            <input type="text" id="capt" name="capt" required style="width: 100px;">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Captcha -->

            <!-- Terms Agreement -->
            <div class="form-row">
                <div class="form-item">
                    <div class="checkbox-wrap">
                        <input type="checkbox" id="agree-terms" name="agree_terms" value="1" {{ old('agree_terms') ? 'checked' : '' }} required>
                        <div class="checkbox-box">
                            <svg class="icon-cross">
                                <use xlink:href="#svg-cross"></use>
                            </svg>
                        </div>
                        <label for="agree-terms">
                            {{ __('messages.agree_terms') }}
                            <a href="{{ route('privacy') }}" target="_blank" style="color: #615dfa; text-decoration: underline;">{{ __('messages.privacy_policy') }}</a>
                            {{ __('messages.and') }}
                            <a href="{{ route('terms') }}" target="_blank" style="color: #615dfa; text-decoration: underline;">{{ __('messages.terms_conditions') }}</a>
                        </label>
                    </div>
                    @error('agree_terms')
                        <span style="color: #e74c3c; font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <!-- /Terms Agreement -->

            <!-- FORM ROW -->
            <div class="form-row">
                <!-- FORM ITEM -->
                <div class="form-item">
                    <!-- BUTTON -->
                    <button class="button medium secondary" name="submit" type="submit">{{ __('messages.sign_up') }}</button>
                    <!-- /BUTTON -->
                </div>
                <!-- /FORM ITEM -->
            </div>
            <!-- /FORM ROW -->
        </form>
        <!-- /FORM -->
        <hr />
        <!-- LINED TEXT -->
        <p class="lined-text">{{ __('messages.alrehaacc') }}</p>
        <br />
        <!-- /LINED TEXT -->
        <a class="button medium tertiary" href="{{ route('login') }}" style="color: #fff; width: 100%; text-align: center; display: block;">{{ __('messages.login') }}</a>
        <hr />
        
        {{-- Social Login Placeholder --}}
        {{-- 
        <p class="lined-text">Login with your Social Account</p>
        <div class="social-links">
             <!-- Social links here -->
        </div>
        --}}
    </div>
    <!-- /FORM BOX -->
</div>
@endsection
