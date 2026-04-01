@extends('theme::layouts.admin')

@section('title', __('messages.security_settings_title'))
@section('admin_shell_header_mode', 'hidden')

@php
    $toggleFields = [
        'link_safety_enabled' => __('messages.security_link_safety_enabled'),
        'link_safety_apply_posts' => __('messages.security_link_safety_posts'),
        'link_safety_apply_comments' => __('messages.security_link_safety_comments'),
        'link_safety_apply_messages' => __('messages.security_link_safety_messages'),
        'link_safety_apply_ads' => __('messages.security_link_safety_ads'),
        'block_spam_usernames' => __('messages.security_block_spam_usernames'),
        'admin_password_confirmation_enabled' => __('messages.security_admin_password_confirmation'),
        'private_message_encryption_enabled' => __('messages.security_pm_encryption'),
        'public_member_ids_enabled' => __('messages.security_public_member_ids'),
    ];
@endphp

@section('content')
<div class="admin-suite-shell">
<section class="admin-hero">
    <div class="admin-hero__content">
        <ul class="admin-breadcrumb">
            <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
            <li>{{ __('messages.security_title') }}</li>
        </ul>
        <div class="admin-hero__eyebrow">{{ __('messages.security_title') }}</div>
        <h1 class="admin-hero__title">{{ __('messages.security_settings_title') }}</h1>
        <p class="admin-hero__copy">{{ __('messages.security_link_protection_section') }} / {{ __('messages.security_accounts_section') }}</p>
    </div>
    <div class="admin-hero__actions">
        <div class="admin-summary-grid w-100">
            <div class="admin-summary-card">
                <span class="admin-summary-label">{{ __('messages.security_ip_bans_title') }}</span>
                <span class="admin-summary-value">{{ $ipBansAvailable ? __('messages.enabled') : __('messages.disabled') }}</span>
            </div>
            <div class="admin-summary-card">
                <span class="admin-summary-label">{{ __('messages.security_member_sessions_title') }}</span>
                <span class="admin-summary-value">{{ $sessionsAvailable ? __('messages.enabled') : __('messages.disabled') }}</span>
            </div>
        </div>
    </div>
</section>

@include('theme::admin.security.partials.nav')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@foreach($upgradeNotices as $upgradeNotice)
    @include('theme::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
@endforeach

<div class="row g-4 mb-4">
    <div class="col-xl-4">
        <div class="card stretch stretch-full h-100">
            <div class="card-body">
                <h5 class="card-title mb-2">{{ __('messages.security_ip_bans_title') }}</h5>
                <p class="text-muted mb-3">{{ __('messages.security_ip_bans_help') }}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge {{ $ipBansAvailable ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }}">
                        {{ $ipBansAvailable ? __('messages.security_feature_ready') : __('messages.security_feature_upgrade_required') }}
                    </span>
                    <a href="{{ route('admin.security.ip-bans') }}" class="btn btn-sm btn-primary">{{ __('messages.view') }}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card stretch stretch-full h-100">
            <div class="card-body">
                <h5 class="card-title mb-2">{{ __('messages.security_member_sessions_title') }}</h5>
                <p class="text-muted mb-3">{{ __('messages.security_member_sessions_help') }}</p>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge {{ $sessionsAvailable ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }}">
                        {{ $sessionsAvailable ? __('messages.security_feature_ready') : __('messages.security_feature_upgrade_required') }}
                    </span>
                    <a href="{{ route('admin.security.sessions') }}" class="btn btn-sm btn-primary">{{ __('messages.view') }}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card stretch stretch-full h-100">
            <div class="card-body">
                <h5 class="card-title mb-2">{{ __('messages.security_admin_password_confirmation') }}</h5>
                <p class="text-muted mb-3">{{ __('messages.security_admin_password_confirmation_help') }}</p>
                <span class="badge {{ !empty($settings['admin_password_confirmation_enabled']) ? 'bg-soft-success text-success' : 'bg-soft-secondary text-secondary' }}">
                    {{ !empty($settings['admin_password_confirmation_enabled']) ? __('messages.enabled') : __('messages.disabled') }}
                </span>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('admin.security.update') }}" method="POST">
    @csrf

    <div class="card stretch stretch-full mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('messages.security_link_protection_section') }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                @foreach(['link_safety_enabled', 'link_safety_apply_posts', 'link_safety_apply_comments', 'link_safety_apply_messages', 'link_safety_apply_ads'] as $field)
                    <div class="col-lg-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="{{ $field }}" name="{{ $field }}" value="1" @checked(old($field, $settings[$field] ?? 0))>
                            <label class="form-check-label" for="{{ $field }}">{{ $toggleFields[$field] }}</label>
                        </div>
                    </div>
                @endforeach
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.security_blacklist_domains') }}</label>
                    <textarea name="blacklist_domains" rows="6" class="form-control" placeholder="{{ __('messages.security_list_placeholder') }}">{{ old('blacklist_domains', $settings['blacklist_domains'] ?? '') }}</textarea>
                    <small class="text-muted">{{ __('messages.security_blacklist_domains_help') }}</small>
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.security_blacklist_url_patterns') }}</label>
                    <textarea name="blacklist_url_patterns" rows="6" class="form-control" placeholder="{{ __('messages.security_list_placeholder') }}">{{ old('blacklist_url_patterns', $settings['blacklist_url_patterns'] ?? '') }}</textarea>
                    <small class="text-muted">{{ __('messages.security_blacklist_url_patterns_help') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card stretch stretch-full mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('messages.security_rate_limits_section') }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-3">
                    <label class="form-label">{{ __('messages.security_cooldown_post') }}</label>
                    <input type="number" min="0" name="cooldown_post_seconds" class="form-control" value="{{ old('cooldown_post_seconds', $settings['cooldown_post_seconds'] ?? 20) }}">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">{{ __('messages.security_cooldown_comment') }}</label>
                    <input type="number" min="0" name="cooldown_comment_seconds" class="form-control" value="{{ old('cooldown_comment_seconds', $settings['cooldown_comment_seconds'] ?? 10) }}">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">{{ __('messages.security_cooldown_topic') }}</label>
                    <input type="number" min="0" name="cooldown_forum_topic_seconds" class="form-control" value="{{ old('cooldown_forum_topic_seconds', $settings['cooldown_forum_topic_seconds'] ?? 60) }}">
                </div>
                <div class="col-lg-3">
                    <label class="form-label">{{ __('messages.security_cooldown_message') }}</label>
                    <input type="number" min="0" name="cooldown_private_message_seconds" class="form-control" value="{{ old('cooldown_private_message_seconds', $settings['cooldown_private_message_seconds'] ?? 8) }}">
                </div>
            </div>
            <small class="text-muted d-block mt-3">{{ __('messages.security_zero_disables_rule') }}</small>
        </div>
    </div>

    <div class="card stretch stretch-full mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('messages.security_accounts_section') }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-4">
                    <label class="form-label">{{ __('messages.security_registration_ip_daily_limit') }}</label>
                    <input type="number" min="0" name="registration_ip_daily_limit" class="form-control" value="{{ old('registration_ip_daily_limit', $settings['registration_ip_daily_limit'] ?? 3) }}">
                </div>
                <div class="col-lg-4">
                    <label class="form-label">{{ __('messages.security_login_max_attempts_ip') }}</label>
                    <input type="number" min="0" name="login_max_attempts_per_ip_15m" class="form-control" value="{{ old('login_max_attempts_per_ip_15m', $settings['login_max_attempts_per_ip_15m'] ?? 12) }}">
                </div>
                <div class="col-lg-4">
                    <label class="form-label">{{ __('messages.security_login_max_attempts_account') }}</label>
                    <input type="number" min="0" name="login_max_attempts_per_account_15m" class="form-control" value="{{ old('login_max_attempts_per_account_15m', $settings['login_max_attempts_per_account_15m'] ?? 6) }}">
                </div>
                <div class="col-lg-4">
                    <label class="form-label">{{ __('messages.security_max_active_sessions') }}</label>
                    <input type="number" min="0" name="max_active_sessions_per_user" class="form-control" value="{{ old('max_active_sessions_per_user', $settings['max_active_sessions_per_user'] ?? 5) }}">
                </div>
                <div class="col-lg-4">
                    <div class="form-check form-switch mt-4 pt-2">
                        <input class="form-check-input" type="checkbox" id="block_spam_usernames" name="block_spam_usernames" value="1" @checked(old('block_spam_usernames', $settings['block_spam_usernames'] ?? 0))>
                        <label class="form-check-label" for="block_spam_usernames">{{ $toggleFields['block_spam_usernames'] }}</label>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-check form-switch mt-4 pt-2">
                        <input class="form-check-input" type="checkbox" id="admin_password_confirmation_enabled" name="admin_password_confirmation_enabled" value="1" @checked(old('admin_password_confirmation_enabled', $settings['admin_password_confirmation_enabled'] ?? 0))>
                        <label class="form-check-label" for="admin_password_confirmation_enabled">{{ $toggleFields['admin_password_confirmation_enabled'] }}</label>
                    </div>
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.security_blocked_usernames') }}</label>
                    <textarea name="blocked_usernames" rows="6" class="form-control" placeholder="{{ __('messages.security_list_placeholder') }}">{{ old('blocked_usernames', $settings['blocked_usernames'] ?? '') }}</textarea>
                    <small class="text-muted">{{ __('messages.security_blocked_usernames_help') }}</small>
                </div>
                <div class="col-lg-6">
                    <label class="form-label">{{ __('messages.security_blocked_email_domains') }}</label>
                    <textarea name="blocked_email_domains" rows="6" class="form-control" placeholder="{{ __('messages.security_list_placeholder') }}">{{ old('blocked_email_domains', $settings['blocked_email_domains'] ?? '') }}</textarea>
                    <small class="text-muted">{{ __('messages.security_blocked_email_domains_help') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card stretch stretch-full mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('messages.security_privacy_section') }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-lg-4">
                    <label class="form-label">{{ __('messages.security_admin_password_confirmation_ttl') }}</label>
                    <input type="number" min="0" name="admin_password_confirmation_ttl_minutes" class="form-control" value="{{ old('admin_password_confirmation_ttl_minutes', $settings['admin_password_confirmation_ttl_minutes'] ?? 30) }}">
                </div>
                <div class="col-lg-4">
                    <div class="form-check form-switch mt-4 pt-2">
                        <input class="form-check-input" type="checkbox" id="private_message_encryption_enabled" name="private_message_encryption_enabled" value="1" @checked(old('private_message_encryption_enabled', $settings['private_message_encryption_enabled'] ?? 0))>
                        <label class="form-check-label" for="private_message_encryption_enabled">{{ $toggleFields['private_message_encryption_enabled'] }}</label>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-check form-switch mt-4 pt-2">
                        <input class="form-check-input" type="checkbox" id="public_member_ids_enabled" name="public_member_ids_enabled" value="1" @checked(old('public_member_ids_enabled', $settings['public_member_ids_enabled'] ?? 0))>
                        <label class="form-check-label" for="public_member_ids_enabled">{{ $toggleFields['public_member_ids_enabled'] }}</label>
                    </div>
                </div>
            </div>
            <small class="text-muted d-block mt-3">{{ __('messages.security_privacy_section_help') }}</small>
        </div>
    </div>

    <div class="d-flex justify-content-end">
        <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
    </div>
</form>
</div>
@endsection
