@extends('theme::layouts.master')

@section('content')
<div class="content">
    <div class="widget-box" style="max-width: 480px; margin: 0 auto;">
        <center><h2>{{ __('messages.reset_password') }}</h2></center><br />

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="form">
            @csrf

            <div class="form-row">
                <div class="form-item">
                    <div class="form-input">
                        <label for="email">{{ __('messages.email') }}</label>
                        <input id="email" type="text" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
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
                    <button type="submit" class="button medium secondary">
                        {{ __('messages.send_password_reset_link') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
