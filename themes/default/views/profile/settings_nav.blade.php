@php
    $billingAvailable = app(\App\Services\V420SchemaService::class)->supports('subscriptions_billing');
    $showBillingLink = $billingAvailable && (\App\Support\SubscriptionSettings::isEnabled() || request()->routeIs('billing.*'));
@endphp

<div class="widget-box">
    <p class="widget-box-title">{{ __('messages.account_settings') }}</p>
    <div class="widget-box-content padding-none">
        <a href="{{ route('profile.edit') }}" class="button {{ request()->routeIs('profile.edit') ? 'primary' : 'secondary' }} full" style="border-radius: 0; box-shadow: none;">{{ __('messages.edit_profile') }}</a>
        <a href="{{ route('profile.privacy') }}" class="button {{ request()->routeIs('profile.privacy') ? 'primary' : 'secondary' }} full" style="border-radius: 0; box-shadow: none;">{{ __('messages.privacy_settings') }}</a>
        <a href="{{ route('profile.social') }}" class="button {{ request()->routeIs('profile.social') ? 'primary' : 'secondary' }} full" style="border-radius: 0; box-shadow: none;">{{ __('messages.social_links') }}</a>
        <a href="{{ route('profile.sessions') }}" class="button {{ request()->routeIs('profile.sessions') ? 'primary' : 'secondary' }} full" style="border-radius: 0; box-shadow: none;">{{ __('messages.active_sessions') }}</a>
        @if($showBillingLink)
            <a href="{{ route('billing.dashboard') }}" class="button {{ request()->routeIs('billing.*') ? 'primary' : 'secondary' }} full" style="border-radius: 0; box-shadow: none;">{{ __('messages.billing_feature_title') }}</a>
        @endif
        <a href="{{ route('profile.badges') }}" class="button {{ request()->routeIs('profile.badges') ? 'primary' : 'secondary' }} full" style="border-radius: 0; box-shadow: none;">{{ __('messages.badges') }}</a>
        <a href="{{ route('profile.history') }}" class="button {{ request()->routeIs('profile.history') ? 'primary' : 'secondary' }} full" style="border-radius: 0; box-shadow: none;">{{ __('messages.pts_history') }}</a>
        <a href="{{ route('profile.show', $user->username) }}" class="button secondary full" style="border-radius: 0; box-shadow: none;">{{ __('messages.view_profile') }}</a>
    </div>
</div>
