@extends('theme::layouts.master')

@section('content')
<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;" >
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}"  alt="overview-icon">
    <p class="section-banner-title">{{ __('messages.list') }} {{ __('messages.referal') }}</p>
    <p class="section-banner-text">{{ __('messages.ryffyrly') }}</p>
</div>

<div class="grid grid-3-9">
    <!-- LEFT SIDEBAR -->
    <div class="grid-column">
        <div class="widget-box">
            <p class="widget-box-title"><h4>{{ __('messages.menu') }}</h4></p>
            <div class="widget-box-content">
                <div class="post-peek-list">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary" >&nbsp;<i class="fa fa-home" aria-hidden="true"></i>&nbsp;</a>
                    <a href="{{ route('ads.referrals') }}" class="btn btn-success" >{{ __('messages.codes') }} {{ __('messages.referal') }}&nbsp;<i class="fa fa-code" aria-hidden="true"></i> </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="grid-column">
        
        <!-- REFERRALS -->
        <div class="widget-box">
            <p class="widget-box-title"><h4>{{ __('messages.list') }} {{ __('messages.referal') }}</h4></p>
            <div class="widget-box-content">
                @if($referrals->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>{{ __('messages.username') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th>{{ __('messages.pts') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($referrals as $ref)
                        @php
                            $user = $ref->referredUser;
                        @endphp
                        @if($user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <div class="user-status">
                                    <a class="user-status-avatar" href="{{ route('profile.show', $user->username) }}">
                                        <div class="user-avatar small no-outline {{ $user->online > time() - 240 ? 'online' : 'offline' }}">
                                            <div class="user-avatar-content">
                                                <div class="hexagon-image-30-32" data-src="{{ $user->avatar ? asset($user->avatar) : theme_asset('img/avatar.png') }}" style="width: 30px; height: 32px; position: relative;"></div>
                                            </div>
                                            <div class="user-avatar-progress-border">
                                                <div class="hexagon-border-40-44" data-line-color="{{ $user->profileBadgeColor() }}" style="width: 40px; height: 44px; position: relative;"></div>
                                            </div>
                                            @if($user->hasVerifiedBadge())
                                            <div class="user-avatar-badge">
                                                <div class="user-avatar-badge-border">
                                                    <div class="hexagon-22-24" style="width: 22px; height: 24px; position: relative;"></div>
                                                </div>
                                                <div class="user-avatar-badge-content">
                                                    <div class="hexagon-dark-16-18" style="width: 16px; height: 18px; position: relative;"></div>
                                                </div>
                                                <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check" ></i></p>
                                            </div>
                                            @endif
                                        </div>
                                    </a>
                                    <p class="user-status-title"><a class="bold" href="{{ route('profile.show', $user->username) }}">{{ $user->username }}</a></p>
                                    <p class="user-status-text small">{{ '@'.$user->username }}</p>
                                </div>
                            </td>
                            <td>{{ date('d/m/Y', is_numeric($ref->date) ? $ref->date : strtotime($ref->date)) }}</td>
                            <td>{{ $user->pts }}</td>
                        </tr>
                        @else
                        <tr>
                            <td colspan="4">{{ __('messages.unknown') }}</td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $referrals->links('pagination::bootstrap-4') }}
                </div>
                @else
                    <p>{{ __('messages.no_user') }}</p>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
