@extends('theme::layouts.master')

@section('title', __('messages.sign_in'))

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
        <center><h2>{{ __('messages.sign_in') }}</h2></center><br />
        <!-- /FORM BOX TITLE -->

        <!-- FORM -->
        <form class="form" method="post" action="{{ route('login.post') }}">
            @csrf
            <!-- FORM ROW -->
            <div class="form-row">
                <!-- FORM ITEM -->
                <div class="form-item">
                    <!-- FORM INPUT -->
                    <div class="form-input">
                        <label for="login-username">{{ __('messages.usermail') }}</label>
                        <input type="text" id="login-username" name="username" value="{{ old('username') }}" required>
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
                        <label for="login-password">{{ __('messages.password') }}</label>
                        <input type="password" id="login-password" name="password" required>
                    </div>
                    <!-- /FORM INPUT -->
                </div>
                <!-- /FORM ITEM -->
            </div>
            <!-- /FORM ROW -->

            <!-- FORM ROW -->
            <div class="form-row space-between">
                <!-- FORM ITEM -->
                <div class="form-item">
                    <!-- CHECKBOX WRAP -->
                    <div class="checkbox-wrap">
                        <input type="checkbox" id="login-remember" name="remember" checked="">
                        <!-- CHECKBOX BOX -->
                        <div class="checkbox-box">
                            <!-- ICON CROSS -->
                            <svg class="icon-cross">
                                <use xlink:href="#svg-cross"></use>
                            </svg>
                            <!-- /ICON CROSS -->
                        </div>
                        <!-- /CHECKBOX BOX -->
                        <label for="login-remember">{{ __('messages.remember_me') }}</label>
                    </div>
                    <!-- /CHECKBOX WRAP -->
                </div>
                <!-- /FORM ITEM -->

                <!-- FORM ITEM -->
                <div class="form-item">
                    <!-- FORM LINK -->
                    <a class="form-link" href="{{ route('password.request') }}">{{ __('messages.forgot_password') }}</a>
                    <!-- /FORM LINK -->
                </div>
                <!-- /FORM ITEM -->
            </div>
            <!-- /FORM ROW -->

            <!-- FORM ROW -->
            <div class="form-row">
                <!-- FORM ITEM -->
                <div class="form-item">
                    <!-- BUTTON -->
                    <button class="button medium secondary" name="login" type="submit">{{ __('messages.login') }}</button>
                    <!-- /BUTTON -->
                </div>
                <!-- /FORM ITEM -->
            </div>
            <!-- /FORM ROW -->
        </form>
        <!-- /FORM -->
        <hr />
        
        {{-- Social Login --}}
        @if(env('FACEBOOK_CLIENT_ID') || env('GOOGLE_CLIENT_ID'))
        <div style="text-align: center; margin: 20px 0;">
            <p class="lined-text" style="margin-bottom: 15px;">{{ __('messages.login_with_social') }}</p>
            <div class="social-links" style="display: flex; gap: 10px; justify-content: center;">
                @if(env('FACEBOOK_CLIENT_ID'))
                <a href="{{ route('social.redirect', 'facebook') }}" class="button small facebook" style="background-color: #3b5998; color: white; {{ env('FACEBOOK_CLIENT_ID') && env('GOOGLE_CLIENT_ID') ? 'width: 48%;' : 'width: 100%;' }} text-align: center; display: inline-block;">
                    <i class="fa-brands fa-facebook"></i> Facebook
                </a>
                @endif
                @if(env('GOOGLE_CLIENT_ID'))
                <a href="{{ route('social.redirect', 'google') }}" class="button small google" style="background-color: #dd4b39; color: white; {{ env('FACEBOOK_CLIENT_ID') && env('GOOGLE_CLIENT_ID') ? 'width: 48%;' : 'width: 100%;' }} text-align: center; display: inline-block;">
                    <i class="fa-brands fa-google"></i> Google
                </a>
                @endif
            </div>
        </div>
        <hr />
        @endif

        <!-- LINED TEXT -->
        <p class="lined-text">{{ __('messages.donthaacc') }}</p>
        <br />
        <!-- /LINED TEXT -->
        <a class="button medium tertiary" href="{{ route('register') }}" style="color: #fff; width: 100%; text-align: center; display: block;">{{ __('messages.sign_up') }}</a>
        <hr />
    </div>
    <!-- /FORM BOX -->
</div>
@endsection
