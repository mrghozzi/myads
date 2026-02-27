@extends('theme::layouts.master')

@section('content')
<div class="content">
    <div class="widget-box" style="max-width: 480px; margin: 0 auto;">
        <center><h2>{{ __('messages.reset_password') }}</h2></center><br />

        <form method="POST" action="{{ route('password.update') }}" class="form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-row">
                <div class="form-item">
                    <div class="form-input">
                        <label for="email">{{ __('messages.email') }}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                    </div>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-item">
                    <div class="form-input">
                        <label for="password">{{ __('messages.password') }}</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-item">
                    <div class="form-input">
                        <label for="password-confirm">{{ __('messages.confirm_password') }}</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-item">
                    <button type="submit" class="button medium secondary">
                        {{ __('messages.reset_password') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
