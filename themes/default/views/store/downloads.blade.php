@extends('theme::layouts.master')

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/marketplace-icon.png') }}">
    <p class="section-banner-title">{{ __('downloads') }} - {{ $product->name }}</p>
</div>

<div class="section-header">
    <div class="section-header-info">
        <h2 class="section-title">
            {{ __('downloads') }}
        </h2>
    </div>
    <div class="section-header-actions">
        <a class="button secondary small" role="button" href="{{ route('store.show', $product->name) }}">&nbsp;<i class="fa fa-arrow-left"></i>&nbsp;{{ __('messages.back') ?? 'Back' }}&nbsp;</a>
    </div>
</div>

<div class="grid grid-3-3-3-3">
    @forelse($licenses as $license)
        @php
            $userImg = $license->avatar;
            if (!$userImg) {
                $avatarUrl = asset('upload/avatar.png');
            } elseif (\Illuminate\Support\Str::startsWith($userImg, ['http://', 'https://'])) {
                $avatarUrl = $userImg;
            } else {
                $avatarUrl = asset($userImg);
            }
        @endphp
        <div class="user-preview">
            <figure class="user-preview-cover liquid" style="background: url({{ theme_asset('img/cover/04.jpg') }}) center center / cover no-repeat;">
                <img src="{{ theme_asset('img/cover/04.jpg') }}" alt="cover" style="display: none;">
            </figure>
            <div class="user-preview-info">
                <div class="user-short-description">
                    <a class="user-short-description-avatar user-avatar medium" href="{{ route('profile.show', $license->username) }}">
                        <div class="user-avatar-border">
                            <div class="hexagon-120-132" style="width: 120px; height: 132px; position: relative;"><canvas width="120" height="132" style="position: absolute; top: 0; left: 0;"></canvas></div>
                        </div>
                        <div class="user-avatar-content">
                            <div class="hexagon-image-82-90" data-src="{{ $avatarUrl }}" style="width: 82px; height: 90px; position: relative;"><canvas width="82" height="90" style="position: absolute; top: 0; left: 0;"></canvas></div>
                        </div>
                    </a>
                    <p class="user-short-description-title"><a href="{{ route('profile.show', $license->username) }}">{{ $license->username }}</a></p>
                    <p class="user-short-description-text"><a href="#">{{ __('messages.member') ?? 'Member' }}</a></p>
                </div>
                <div class="user-preview-stats">
                    <div class="user-preview-stat">
                        <p class="user-preview-stat-title">{{ \Carbon\Carbon::parse($license->created_at)->diffForHumans() }}</p>
                        <p class="user-preview-stat-text">{{ __('messages.downloaded') ?? 'Downloaded' }}</p>
                    </div>
                </div>
                <div class="user-preview-actions">
                    <a href="{{ route('profile.show', $license->username) }}" class="button secondary full">{{ __('messages.view_profile') ?? 'View Profile' }}</a>
                </div>
            </div>
        </div>
    @empty
        <div class="widget-box profile-relationships-empty" style="grid-column: 1 / -1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 20px;">
            <div class="profile-relationships-empty__icon" style="font-size: 48px; color: #8f91ac; margin-bottom: 20px;">
                <i class="fa fa-users" aria-hidden="true"></i>
            </div>
            <p class="widget-box-title" style="font-size: 18px; color: #fff;">{{ __('messages.no_results') ?? 'No results found.' }}</p>
        </div>
    @endforelse
</div>

@if($licenses->hasPages())
    <div style="margin-top: 20px;">
        {{ $licenses->links('pagination::bootstrap-5') }}
    </div>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof initHexagons === 'function') {
            initHexagons();
        }
    });
</script>
@endsection
