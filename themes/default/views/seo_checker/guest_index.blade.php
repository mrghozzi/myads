@php
    $seo = (object) [
        'title' => __('messages.seo_checker'),
        'robots' => 'index, follow'
    ];
@endphp
@extends('theme::layouts.oauth')

@section('title', __('messages.seo_checker'))

@section('content')
<div style="background: var(--oauth-shell-surface); border: 1px solid var(--oauth-shell-surface-border); border-radius: 24px; padding: 40px; box-shadow: var(--oauth-shell-shadow); text-align: center;">
    <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 10px; color: var(--oauth-shell-text);">{{ __('messages.seo_checker') }}</h1>
    <p style="color: var(--oauth-shell-muted); margin-bottom: 30px;">{{ __('messages.seo_checker_desc') }}</p>

    <form action="{{ route('seo_checker.analyze') }}" method="POST" style="max-width: 500px; margin: 0 auto;">
        @csrf
        <div style="margin-bottom: 20px; text-align: left;">
            <input type="url" name="url" placeholder="https://example.com" required style="width: 100%; padding: 15px 20px; border-radius: 50px; border: 1px solid rgba(97, 93, 250, 0.2); background: rgba(0,0,0,0.02); color: var(--oauth-shell-text); font-size: 1rem; outline: none;">
        </div>
        <button type="submit" style="width: 100%; padding: 15px 30px; border-radius: 50px; font-weight: 700; color: #fff; background: linear-gradient(135deg, #615dfa, #23d2e2); border: none; box-shadow: 0 10px 30px rgba(97, 93, 250, 0.3); cursor: pointer; font-size: 1.1rem; transition: transform 0.2s;">
            <i class="fa-solid fa-magnifying-glass"></i> {{ __('messages.seo_analyze_now') }}
        </button>
    </form>
</div>
@endsection
