@extends('theme::layouts.master')

@section('content')
@php
    $refUrl = url('/') . '?ref=' . $user->publicRouteIdentifier();
@endphp

<div class="promotion-shell superdesign-post-container">

    <!-- @.superdesign Glassmorphic Hero Banner -->
    <div class="superdesign-post-hero" style="background: linear-gradient(135deg, rgba(52, 84, 209, 0.15) 0%, rgba(97, 93, 250, 0.1) 100%); border: 1px solid rgba(255,255,255,0.12); border-radius: 24px; padding: 28px; margin-bottom: 24px; backdrop-filter: blur(10px);">
        <div class="superdesign-hero-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div class="superdesign-hero-title-wrap" style="display: flex; align-items: center; gap: 16px;">
                <div class="superdesign-hero-icon-badge" style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #3454d1, #615dfa); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 24px; box-shadow: 0 8px 20px rgba(52, 84, 209, 0.3);">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <h1 class="superdesign-hero-title" style="font-size: 24px; font-weight: 800; margin: 0; line-height: 1.2;">
                        {{ __('messages.my_referrals_list') }}
                    </h1>
                    <p class="superdesign-hero-subtitle" style="margin: 4px 0 0 0; opacity: 0.8; font-size: 14px;">
                        {{ __('messages.referral_description_hero') }}
                    </p>
                </div>
            </div>
            
            <div class="superdesign-hero-badges" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                <div class="stat-badge" style="background: var(--admin-premium-surface, rgba(255,255,255,0.08)); border: 1px solid var(--border-color, rgba(255,255,255,0.1)); padding: 8px 16px; border-radius: 14px; text-align: center;">
                    <span style="display: block; font-size: 11px; opacity: 0.7; text-transform: uppercase;">{{ __('messages.total_referrals') }}</span>
                    <strong style="font-size: 16px; color: #17c666;">{{ number_format($totalReferrals ?? $referrals->total()) }}</strong>
                </div>
                <div class="stat-badge" style="background: var(--admin-premium-surface, rgba(255,255,255,0.08)); border: 1px solid var(--border-color, rgba(255,255,255,0.1)); padding: 8px 16px; border-radius: 14px; text-align: center;">
                    <span style="display: block; font-size: 11px; opacity: 0.7; text-transform: uppercase;">{{ __('messages.points_earned_referral') }}</span>
                    <strong style="font-size: 16px; color: #ffa21d;">{{ number_format(($totalEarnedPts ?? ($referrals->total() * 10))) }} PTS</strong>
                </div>
            </div>
        </div>
        
        <!-- Navigation Tab Switcher -->
        <div style="margin-top: 24px; display: flex; gap: 10px; border-bottom: 1px solid var(--border-color, rgba(255,255,255,0.1)); padding-bottom: 12px;">
            <a href="{{ route('ads.referrals') }}" class="ref-tab-link" style="padding: 10px 20px; border-radius: 12px; font-weight: 600; font-size: 14px; background: rgba(128,128,128,0.1); color: var(--text-color); text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-code"></i> {{ __('messages.referral_banners_codes') }}
            </a>
            <a href="{{ route('legacy.referral') }}" class="ref-tab-link active" style="padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 14px; background: #3454d1; color: #fff; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(52, 84, 209, 0.3);">
                <i class="fa-solid fa-users"></i> {{ __('messages.my_referrals_list') }} ({{ number_format($totalReferrals ?? $referrals->total()) }})
            </a>
        </div>
    </div>

    <!-- Main Card Content -->
    <div class="modern-card" style="background: var(--admin-premium-surface, #fff); border: 1px solid var(--border-color, rgba(0,0,0,0.08)); border-radius: 20px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.04);">
        
        @if($referrals->count() > 0)
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: separate; border-spacing: 0 8px; vertical-align: middle;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color, rgba(0,0,0,0.08)); color: var(--text-color); opacity: 0.7; font-size: 12px; text-transform: uppercase; font-weight: 700;">
                            <th style="padding: 12px; text-align: {{ is_locale_rtl() ? 'right' : 'left' }};">#ID</th>
                            <th style="padding: 12px; text-align: {{ is_locale_rtl() ? 'right' : 'left' }};">{{ __('messages.referred_user') }}</th>
                            <th style="padding: 12px; text-align: {{ is_locale_rtl() ? 'right' : 'left' }};">{{ __('messages.referral_date') }}</th>
                            <th style="padding: 12px; text-align: {{ is_locale_rtl() ? 'right' : 'left' }};">{{ __('messages.user_points') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($referrals as $ref)
                            @php
                                $refUser = $ref->referredUser;
                            @endphp
                            @if($refUser)
                            <tr style="background: var(--input-bg, rgba(128,128,128,0.03)); border-radius: 12px;">
                                <td style="padding: 14px 12px; font-weight: 700; font-size: 13px; border-top-left-radius: 12px; border-bottom-left-radius: 12px;">
                                    #{{ $refUser->id }}
                                </td>
                                <td style="padding: 14px 12px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <a href="{{ route('profile.show', $refUser->username) }}" style="text-decoration: none;">
                                            <div class="user-avatar small no-outline {{ $refUser->online > time() - 240 ? 'online' : 'offline' }}">
                                                <div class="user-avatar-content">
                                                    <div class="hexagon-image-30-32" data-src="{{ $refUser->avatar ? asset($refUser->avatar) : theme_asset('img/avatar.png') }}" style="width: 34px; height: 36px; position: relative; border-radius: 8px;"></div>
                                                </div>
                                                @if($refUser->hasVerifiedBadge())
                                                <div class="user-avatar-badge" style="position: absolute; bottom: -2px; {{ is_locale_rtl() ? 'left: -2px;' : 'right: -2px;' }}">
                                                    <span style="background: #17c666; color: #fff; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 9px;"><i class="fa fa-check"></i></span>
                                                </div>
                                                @endif
                                            </div>
                                        </a>
                                        <div>
                                            <a class="bold" href="{{ route('profile.show', $refUser->username) }}" style="color: var(--text-color); font-weight: 700; text-decoration: none; font-size: 14px;">
                                                {{ $refUser->username }}
                                            </a>
                                            <span style="display: block; font-size: 12px; opacity: 0.6;">
                                                {{ '@' . $refUser->username }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 14px 12px; font-size: 13px; opacity: 0.8;">
                                    <i class="fa-regular fa-calendar me-1" style="color: #615dfa;"></i>
                                    {{ date('d/m/Y', is_numeric($ref->date) ? $ref->date : strtotime($ref->date)) }}
                                </td>
                                <td style="padding: 14px 12px; border-top-right-radius: 12px; border-bottom-right-radius: 12px;">
                                    <span style="background: rgba(255, 162, 29, 0.15); color: #ffa21d; border: 1px solid rgba(255, 162, 29, 0.3); padding: 4px 12px; border-radius: 20px; font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fa-solid fa-coins"></i> {{ number_format($refUser->pts) }} PTS
                                    </span>
                                </td>
                            </tr>
                            @else
                            <tr>
                                <td colspan="4" style="padding: 14px; opacity: 0.6;">{{ __('messages.unknown') }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: center;">
                {{ $referrals->links('pagination::bootstrap-4') }}
            </div>
        @else
            <!-- Empty State Illustration Card -->
            <div style="text-align: center; padding: 48px 24px;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(52, 84, 209, 0.1); color: #3454d1; display: flex; align-items: center; justify-content: center; font-size: 36px; margin: 0 auto 20px auto;">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">{{ __('messages.no_referrals_yet') }}</h3>
                <p style="opacity: 0.7; font-size: 14px; max-width: 480px; margin: 0 auto 24px auto;">
                    {{ __('messages.referral_description_hero') }}
                </p>
                <a href="{{ route('ads.referrals') }}" style="background: linear-gradient(135deg, #3454d1, #615dfa); color: #fff; padding: 12px 24px; border-radius: 14px; font-weight: 700; font-size: 14px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 6px 16px rgba(52, 84, 209, 0.3);">
                    <i class="fa-solid fa-share-nodes"></i> {{ __('messages.share_referral_link') }}
                </a>
            </div>
        @endif

    </div>

</div>
@endsection
