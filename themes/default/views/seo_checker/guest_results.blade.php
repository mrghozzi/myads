@php
    $seo = (object) [
        'title' => __('messages.seo_checker'),
        'robots' => 'index, follow'
    ];
@endphp
@extends('theme::layouts.oauth')

@section('title', __('messages.seo_checker'))

@section('content')
<div style="background: var(--oauth-shell-surface); border: 1px solid var(--oauth-shell-surface-border); border-radius: 24px; padding: 40px; box-shadow: var(--oauth-shell-shadow); text-align: center; max-width: 900px; width: 100%;">
    @include('theme::seo_checker._results_content', ['results' => $results, 'settings' => $settings, 'userRole' => $userRole])
</div>
@endsection
