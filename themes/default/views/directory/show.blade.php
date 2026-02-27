@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ $listing->name }}</p>
    <p class="section-banner-text">{{ __('messages.directory') }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column"></div>
    <div class="grid-column">
        @include('theme::partials.widgets', ['place' => 5])
        @include('theme::partials.activity.render', ['activity' => $activity, 'detailView' => true])
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                if (typeof loadComments === 'function') {
                    loadComments({{ $listing->id }}, 'directory');
                }
                var btn = document.querySelector('.sh_comment_s{{ $activity->id }}');
                if (btn) {
                    btn.classList.add('active');
                }
            });
        </script>
    </div>
    <div class="grid-column"></div>
</div>
@endsection
