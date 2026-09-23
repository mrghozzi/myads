@extends('admin::layouts.admin')

@section('title', __('messages.edit_user') . ' - ' . $user->username)

@section('content')
<div class="admin-page">
    <!-- Hero Section -->
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}"><i class="feather-home me-1"></i>{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.users') }}">{{ __('messages.users') }}</a></li>
                <li>{{ __('messages.edit') }}</li>
            </ul>

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="admin-hero__eyebrow">
                    <span class="badge bg-soft-primary text-primary px-2 py-1"><i class="feather-user-check me-1"></i>{{ __('messages.edit_user') }}</span>
                </div>

                <!-- Prev / Next User Navigation Shortcuts -->
                <div class="d-flex align-items-center gap-2">
                    @if($prevUserId)
                        <a href="{{ route('admin.users.edit', $prevUserId) }}" class="btn btn-sm btn-light border" title="{{ __('messages.prev_user') }}">
                            <i class="feather-chevron-left me-1"></i><span>{{ __('messages.prev_user') }}</span>
                        </a>
                    @endif
                    @if($nextUserId)
                        <a href="{{ route('admin.users.edit', $nextUserId) }}" class="btn btn-sm btn-light border" title="{{ __('messages.next_user') }}">
                            <span>{{ __('messages.next_user') }}</span><i class="feather-chevron-right ms-1"></i>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Profile Summary Card -->
            <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-light mt-3">
                <div class="position-relative flex-shrink-0">
                    <img 
                        src="{{ $user->avatarUrl() }}" 
                        alt="{{ $user->username }}" 
                        class="rounded-circle border" 
                        width="70" 
                        height="70" 
                        style="object-fit: cover;"
                    >
                    <span class="position-absolute bottom-0 end-0 p-1 {{ $user->isOnline() ? 'bg-success' : 'bg-secondary' }} border border-white rounded-circle" style="width: 14px; height: 14px;" title="{{ $user->isOnline() ? __('messages.online') : __('messages.offline') }}"></span>
                </div>
                <div class="overflow-hidden min-w-0 flex-grow-1">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <h2 class="admin-hero__title fs-3 mb-0 text-truncate">{{ $user->username }}</h2>
                        @if($user->ucheck == 1)
                            <i class="bi bi-patch-check-fill text-primary fs-5" title="{{ __('messages.Verified') }}"></i>
                        @endif
                        @if($user->isSuperAdmin())
                            <span class="badge bg-soft-danger text-danger border border-danger-subtle">{{ __('messages.super_admins') ?? 'Super Admin' }}</span>
                        @elseif($user->isAdmin())
                            <span class="badge bg-soft-primary text-primary border border-primary-subtle">{{ __('messages.Admin') }}</span>
                        @else
                            <span class="badge bg-soft-secondary text-secondary">{{ __('messages.Member') }}</span>
                        @endif
                    </div>
                    <div class="d-flex align-items-center flex-wrap gap-3 fs-12 text-muted mt-2">
                        <span><i class="feather-mail me-1"></i>{{ $user->email }}</span>
                        <span><i class="feather-hash me-1"></i>ID: <strong>{{ $user->id }}</strong></span>
                        @if(!empty($user->public_uid))
                            <span><i class="feather-shield me-1"></i>UID: <strong>{{ $user->public_uid }}</strong></span>
                        @endif
                        <span><i class="feather-calendar me-1"></i>{{ $user->created_at ? $user->created_at->format('Y-m-d') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- 5 Wallet Balances Grid -->
            <div class="admin-stat-strip mt-3">
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-award me-1 text-primary"></i>{{ __('messages.points') }}</span>
                    <span class="admin-stat-value text-primary" id="heroPtsValue">{{ number_format((float) $user->pts, 2) }}</span>
                    <small class="text-muted fs-11">PTS</small>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-globe me-1 text-success"></i>{{ __('messages.exchange_visits_pts') }}</span>
                    <span class="admin-stat-value text-success" id="heroVuValue">{{ number_format((float) $user->vu, 2) }}</span>
                    <small class="text-muted fs-11">VU</small>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-image me-1 text-warning"></i>{{ __('messages.banner_ads_pts') }}</span>
                    <span class="admin-stat-value text-warning" id="heroNvuValue">{{ number_format((float) $user->nvu, 2) }}</span>
                    <small class="text-muted fs-11">NVU</small>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-link me-1 text-info"></i>{{ __('messages.text_ads_pts') }}</span>
                    <span class="admin-stat-value text-info" id="heroNlinkValue">{{ number_format((float) $user->nlink, 2) }}</span>
                    <small class="text-muted fs-11">NLINK</small>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-target me-1 text-dark"></i>{{ __('messages.smart_ads') }}</span>
                    <span class="admin-stat-value text-dark" id="heroNsmartValue">{{ number_format((float) $user->nsmart, 2) }}</span>
                    <small class="text-muted fs-11">NSMART</small>
                </div>
            </div>
        </div>

        <!-- Hero Actions -->
        <div class="admin-hero__actions">
            <div class="admin-link-grid w-100">
                <a href="{{ route('admin.users') }}" class="btn btn-primary admin-block-link">
                    <i class="feather-users"></i>
                    <span>{{ __('messages.list_users') }}</span>
                </a>
                <a href="{{ route('profile.show', $user->username) }}" target="_blank" class="btn btn-info admin-block-link text-white">
                    <i class="feather-external-link"></i>
                    <span>{{ __('messages.view_profile') }}</span>
                </a>
                <a href="{{ route('admin.banners', ['user_id' => $user->id]) }}" class="btn btn-warning admin-block-link text-dark">
                    <i class="feather-image"></i>
                    <span>{{ __('messages.Banners') }} ({{ $bannersCount }})</span>
                </a>
                <a href="{{ route('admin.links', ['user_id' => $user->id]) }}" class="btn btn-success admin-block-link">
                    <i class="feather-link"></i>
                    <span>{{ __('messages.Links') }} ({{ $linksCount }})</span>
                </a>
                <a href="{{ route('admin.smart_ads', ['user_id' => $user->id]) }}" class="btn btn-dark admin-block-link">
                    <i class="feather-target"></i>
                    <span>{{ __('messages.smart_ads') }} ({{ $smartAdsCount }})</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Main Navigation Tabs -->
    <ul class="nav nav-pills admin-panel-nav gap-2 mb-3" id="userEditTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-3" id="tab-profile-btn" data-bs-toggle="pill" data-bs-target="#tab-profile" type="button" role="tab">
                <i class="feather-user me-1"></i>{{ __('messages.profile') ?? 'Profile & Identity' }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-3" id="tab-wallet-btn" data-bs-toggle="pill" data-bs-target="#tab-wallet" type="button" role="tab">
                <i class="feather-dollar-sign me-1"></i>{{ __('messages.points') }} &bull; {{ __('messages.adjust_balances') }}
            </button>
        </li>
        @if($billingEnabled)
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3" id="tab-billing-btn" data-bs-toggle="pill" data-bs-target="#tab-billing" type="button" role="tab">
                    <i class="feather-credit-card me-1"></i>{{ __('messages.subscription_management') }}
                </button>
            </li>
        @endif
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-3" id="tab-security-btn" data-bs-toggle="pill" data-bs-target="#tab-security" type="button" role="tab">
                <i class="feather-lock me-1"></i>{{ __('messages.security') ?? 'Security & Password' }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-3" id="tab-notify-btn" data-bs-toggle="pill" data-bs-target="#tab-notify" type="button" role="tab">
                <i class="feather-bell me-1"></i>{{ __('messages.send_notification') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-3" id="tab-activity-btn" data-bs-toggle="pill" data-bs-target="#tab-activity" type="button" role="tab">
                <i class="feather-activity me-1"></i>{{ __('messages.activity_summary') }}
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="userEditTabsContent">
        <!-- Tab 1: Profile & Identity -->
        <div class="tab-pane fade show active" id="tab-profile" role="tabpanel">
            <div class="admin-panel shadow-sm">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('messages.edit_user') }}</span>
                        <h2 class="admin-panel__title">{{ __('messages.edit_user_details') }}</h2>
                    </div>
                </div>
                <div class="admin-panel__body p-4">
                    <form id="userMainEditForm" action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('messages.username') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="username" id="editUsername" value="{{ $user->username }}" required>
                                <span class="form-text text-muted fs-11">{{ __('messages.login_identity') }}</span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('messages.user_slug') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="slug" id="editSlug" value="{{ $slug }}" required>
                                <span class="form-text text-muted fs-11">{{ __('messages.profile_url_handle') }}</span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('messages.email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" id="editEmail" value="{{ $user->email }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('messages.verified_account') }}</label>
                                <select class="form-select" name="ucheck">
                                    <option value="0" {{ $user->ucheck == 0 ? 'selected' : '' }}>{{ __('messages.No') }} ({{ __('messages.Unverified') }})</option>
                                    <option value="1" {{ $user->ucheck == 1 ? 'selected' : '' }}>{{ __('messages.Yes') }} ({{ __('messages.Verified') }})</option>
                                </select>
                            </div>
                        </div>

                        <!-- Hidden fields to preserve balances in case of general update -->
                        <input type="hidden" name="pts" value="{{ $user->pts }}">
                        <input type="hidden" name="vu" value="{{ $user->vu }}">
                        <input type="hidden" name="nvu" value="{{ $user->nvu }}">
                        <input type="hidden" name="nlink" value="{{ $user->nlink }}">
                        <input type="hidden" name="nsmart" value="{{ $user->nsmart }}">

                        <div class="form-check form-switch p-0 mt-4 mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <input class="form-check-input ms-0" type="checkbox" name="notify_user" id="profileNotifyUser" value="1">
                                <label class="form-check-label fw-semibold" for="profileNotifyUser">
                                    {{ __('messages.notify_user') }} ({{ __('messages.notification_admin_update') }})
                                </label>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary" id="saveProfileBtn">
                                <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                                {{ __('messages.update_user') }}
                            </button>
                            <a href="{{ route('admin.users') }}" class="btn btn-light">{{ __('messages.back') ?? 'Back' }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tab 2: Wallet & Balances -->
        <div class="tab-pane fade" id="tab-wallet" role="tabpanel">
            <div class="admin-panel shadow-sm">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('messages.points') }}</span>
                        <h2 class="admin-panel__title">{{ __('messages.adjust_balances') }}</h2>
                    </div>
                </div>
                <div class="admin-panel__body p-4">
                    <form id="walletEditForm">
                        @csrf
                        <input type="hidden" name="action" value="adjust_balances">

                        <div class="mb-4">
                            <label class="form-label fw-bold">{{ __('messages.adjustment_mode') }}</label>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="mode" id="walletModeSet" value="set" checked>
                                <label class="btn btn-outline-primary" for="walletModeSet">{{ __('messages.set_balance') }}</label>

                                <input type="radio" class="btn-check" name="mode" id="walletModeInc" value="increment">
                                <label class="btn btn-outline-primary" for="walletModeInc">{{ __('messages.increment_balance') }}</label>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('messages.points') }} (PTS)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" name="pts" id="walletPtsInput" value="{{ $user->pts }}">
                                </div>
                                <div class="d-flex gap-1 mt-1">
                                    <button type="button" class="btn btn-xs btn-light border wallet-chip-add" data-target="walletPtsInput" data-amount="10">+10</button>
                                    <button type="button" class="btn btn-xs btn-light border wallet-chip-add" data-target="walletPtsInput" data-amount="50">+50</button>
                                    <button type="button" class="btn btn-xs btn-light border wallet-chip-add" data-target="walletPtsInput" data-amount="100">+100</button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('messages.exchange_visits_pts') }} (VU)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" name="vu" id="walletVuInput" value="{{ $user->vu }}">
                                </div>
                                <div class="d-flex gap-1 mt-1">
                                    <button type="button" class="btn btn-xs btn-light border wallet-chip-add" data-target="walletVuInput" data-amount="50">+50</button>
                                    <button type="button" class="btn btn-xs btn-light border wallet-chip-add" data-target="walletVuInput" data-amount="200">+200</button>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('messages.banner_ads_pts') }} (NVU)</label>
                                <input type="number" step="0.01" class="form-control" name="nvu" id="walletNvuInput" value="{{ $user->nvu }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('messages.text_ads_pts') }} (NLINK)</label>
                                <input type="number" step="0.01" class="form-control" name="nlink" id="walletNlinkInput" value="{{ $user->nlink }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">{{ __('messages.smart_ads_credits_admin') }} (NSMART)</label>
                                <input type="number" step="0.01" class="form-control" name="nsmart" id="walletNsmartInput" value="{{ $user->nsmart }}">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="form-label fw-bold">{{ __('messages.reason_or_note') }}</label>
                            <input type="text" class="form-control" name="note" placeholder="Optional audit explanation">
                        </div>

                        <div class="form-check form-switch p-0 mt-3 mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <input class="form-check-input ms-0" type="checkbox" name="notify_user" id="walletNotifyUser" value="1">
                                <label class="form-check-label fw-semibold" for="walletNotifyUser">
                                    {{ __('messages.notify_user') }}
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary" id="saveWalletBtn">
                                <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                                {{ __('messages.adjust_balances') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if($billingEnabled)
            <!-- Tab 3: Subscription & Billing -->
            <div class="tab-pane fade" id="tab-billing" role="tabpanel">
                <div class="admin-panel shadow-sm">
                    <div class="admin-panel__header">
                        <div>
                            <span class="admin-panel__eyebrow">{{ __('messages.Billing') ?? 'Billing' }}</span>
                            <h2 class="admin-panel__title">{{ __('messages.subscription_management') }}</h2>
                        </div>
                    </div>
                    <div class="admin-panel__body p-4">
                        <form id="billingEditForm" action="{{ route('admin.users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="username" value="{{ $user->username }}">
                            <input type="hidden" name="slug" value="{{ $slug }}">
                            <input type="hidden" name="email" value="{{ $user->email }}">
                            <input type="hidden" name="ucheck" value="{{ $user->ucheck }}">
                            <input type="hidden" name="pts" value="{{ $user->pts }}">
                            <input type="hidden" name="vu" value="{{ $user->vu }}">
                            <input type="hidden" name="nvu" value="{{ $user->nvu }}">
                            <input type="hidden" name="nlink" value="{{ $user->nlink }}">
                            <input type="hidden" name="nsmart" value="{{ $user->nsmart }}">

                            <div class="p-4 rounded-4 bg-light mb-4">
                                <label class="form-label fw-bold text-muted small text-uppercase mb-2">{{ __('messages.current_subscription') }}</label>
                                <div>
                                    @if($activeSubscription)
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="badge bg-success fs-6 py-2 px-3">{{ $activeSubscription->plan_name }}</span>
                                            <div>
                                                <div class="fw-semibold">{{ __('messages.ends_at') }}: {{ $activeSubscription->ends_at ? $activeSubscription->ends_at->format('Y-m-d') : __('messages.lifetime') }}</div>
                                                <div class="text-muted small">Subscription ID: #{{ $activeSubscription->id }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary fs-6 py-2 px-3">{{ __('messages.no_active_subscription') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">{{ __('messages.select_plan') }}</label>
                                <select class="form-select form-select-lg" name="subscription_plan_id">
                                    <option value="0">{{ __('messages.none_cancel') }}</option>
                                    @foreach($subscriptionPlans as $plan)
                                        <option value="{{ $plan->id }}" {{ ($activeSubscription && $activeSubscription->subscription_plan_id == $plan->id) ? 'selected' : '' }}>
                                            {{ $plan->name }} ({{ $plan->duration_days ?: '∞' }} {{ __('messages.days') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-check form-switch p-0 mb-4">
                                <div class="d-flex align-items-center gap-2">
                                    <input class="form-check-input ms-0" type="checkbox" name="notify_user" id="billingNotifyUser" value="1" checked>
                                    <label class="form-check-label fw-semibold" for="billingNotifyUser">
                                        {{ __('messages.notify_user') }} ({{ __('messages.notification_subscription_update') }})
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex gap-2 pt-3 border-top">
                                <button type="submit" class="btn btn-primary" id="saveBillingBtn">
                                    <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                                    {{ __('messages.save') ?? 'Save Subscription' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tab 4: Security & Password -->
        <div class="tab-pane fade" id="tab-security" role="tabpanel">
            <div class="row g-4">
                <!-- Change Password Card -->
                <div class="col-lg-7">
                    <div class="admin-panel shadow-sm h-100">
                        <div class="admin-panel__header">
                            <div>
                                <span class="admin-panel__eyebrow">{{ __('messages.security') }}</span>
                                <h2 class="admin-panel__title">{{ __('messages.change_password') }}</h2>
                            </div>
                        </div>
                        <div class="admin-panel__body p-4">
                            <form id="editPasswordForm" action="{{ route('admin.users.password', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label fw-bold">{{ __('messages.new_password') }} <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="password" id="editPasswordInput" required minlength="8" autocomplete="new-password">
                                        <button type="button" class="btn btn-outline-secondary" id="generateEditPasswordBtn" title="{{ __('messages.generate_password') }}">
                                            <i class="feather-refresh-cw"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="copyEditPasswordBtn" title="{{ __('messages.copy_password') }}">
                                            <i class="feather-copy"></i>
                                        </button>
                                    </div>
                                    <span class="form-text text-muted fs-11">{{ __('messages.min_8_chars') }}</span>
                                </div>

                                <button type="submit" class="btn btn-warning text-dark fw-bold" id="savePasswordBtn">
                                    <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                                    {{ __('messages.update_password') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- 2FA & Account Protection -->
                <div class="col-lg-5">
                    <div class="admin-panel shadow-sm h-100">
                        <div class="admin-panel__header">
                            <div>
                                <span class="admin-panel__eyebrow">{{ __('messages.security') }}</span>
                                <h2 class="admin-panel__title">{{ __('messages.two_factor_auth') }}</h2>
                            </div>
                        </div>
                        <div class="admin-panel__body p-4">
                            @php
                                $has2FA = !empty($user->two_factor_secret) && !empty($user->two_factor_confirmed_at);
                            @endphp
                            <div class="p-3 rounded-3 bg-light mb-3 text-center">
                                <div class="fs-1 text-{{ $has2FA ? 'success' : 'secondary' }} mb-2">
                                    <i class="feather-shield"></i>
                                </div>
                                <h5 class="fw-bold">{{ $has2FA ? __('messages.two_factor_enabled') : __('messages.two_factor_disabled') }}</h5>
                                <p class="text-muted small mb-0">
                                    {{ $has2FA ? 'User has active 2FA security codes configured.' : 'No two-factor authenticator app is currently linked.' }}
                                </p>
                            </div>

                            @if($has2FA)
                                <button type="button" class="btn btn-outline-danger w-100" id="resetUser2faBtn">
                                    <i class="feather-slash me-1"></i>{{ __('messages.reset_2fa') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 5: Direct In-App Notification -->
        <div class="tab-pane fade" id="tab-notify" role="tabpanel">
            <div class="admin-panel shadow-sm">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('messages.notifications') ?? 'Notifications' }}</span>
                        <h2 class="admin-panel__title">{{ __('messages.send_notification') }}</h2>
                    </div>
                </div>
                <div class="admin-panel__body p-4">
                    <form id="directNotifyForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('messages.message_content') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="message" id="directNotifyMessage" rows="5" placeholder="Write an administrative notice directly to {{ $user->username }}..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-info text-white" id="directNotifySubmitBtn">
                            <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                            {{ __('messages.send_notification') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tab 6: Activity & Content Shortcuts -->
        <div class="tab-pane fade" id="tab-activity" role="tabpanel">
            <div class="admin-cards-grid">
                <!-- Topics Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="bg-soft-primary text-primary rounded p-3 d-inline-flex mb-3">
                            <i class="feather-message-square fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-1">{{ $topicsCount }}</h3>
                        <p class="text-muted small mb-3">{{ __('messages.total_topics') }}</p>
                        <a href="{{ route('forum.index', ['user' => $user->id]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                            <i class="feather-external-link me-1"></i>{{ __('messages.view') ?? 'View Topics' }}
                        </a>
                    </div>
                </div>

                <!-- Comments Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="bg-soft-info text-info rounded p-3 d-inline-flex mb-3">
                            <i class="feather-message-circle fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-1">{{ $commentsCount }}</h3>
                        <p class="text-muted small mb-3">{{ __('messages.total_comments') }}</p>
                        <span class="badge bg-light text-muted">Community Activity</span>
                    </div>
                </div>

                <!-- Banners Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="bg-soft-warning text-warning rounded p-3 d-inline-flex mb-3">
                            <i class="feather-image fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-1">{{ $bannersCount }}</h3>
                        <p class="text-muted small mb-3">{{ __('messages.total_banners') }}</p>
                        <a href="{{ route('admin.banners', ['user_id' => $user->id]) }}" class="btn btn-sm btn-outline-warning rounded-pill">
                            <i class="feather-settings me-1"></i>{{ __('messages.manage') ?? 'Manage Banners' }}
                        </a>
                    </div>
                </div>

                <!-- Text Ads Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="bg-soft-success text-success rounded p-3 d-inline-flex mb-3">
                            <i class="feather-link fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-1">{{ $linksCount }}</h3>
                        <p class="text-muted small mb-3">{{ __('messages.total_links') }}</p>
                        <a href="{{ route('admin.links', ['user_id' => $user->id]) }}" class="btn btn-sm btn-outline-success rounded-pill">
                            <i class="feather-settings me-1"></i>{{ __('messages.manage') ?? 'Manage Links' }}
                        </a>
                    </div>
                </div>

                <!-- Smart Ads Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="bg-soft-danger text-danger rounded p-3 d-inline-flex mb-3">
                            <i class="feather-target fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-1">{{ $smartAdsCount }}</h3>
                        <p class="text-muted small mb-3">{{ __('messages.total_smart_ads') }}</p>
                        <a href="{{ route('admin.smart_ads', ['user_id' => $user->id]) }}" class="btn btn-sm btn-outline-danger rounded-pill">
                            <i class="feather-settings me-1"></i>{{ __('messages.manage') ?? 'Manage Smart Ads' }}
                        </a>
                    </div>
                </div>

                <!-- Store Products Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="bg-soft-secondary text-secondary rounded p-3 d-inline-flex mb-3">
                            <i class="feather-shopping-bag fs-3"></i>
                        </div>
                        <h3 class="fw-bold mb-1">{{ $productsCount }}</h3>
                        <p class="text-muted small mb-3">{{ __('messages.total_products') }}</p>
                        <a href="{{ route('admin.products', ['user_id' => $user->id]) }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                            <i class="feather-settings me-1"></i>{{ __('messages.manage') ?? 'Manage Store' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const userId = {{ $user->id }};

    // Toast Engine
    function showAdminToast(message, type = 'success') {
        let container = document.getElementById('admin-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'admin-toast-container';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1099';
            document.body.appendChild(container);
        }

        const isSuccess = type === 'success';
        const isDanger = type === 'danger';
        const isWarning = type === 'warning';
        
        let bgGradient = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
        let icon = 'feather-check-circle';
        if (isDanger) {
            bgGradient = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
            icon = 'feather-alert-triangle';
        } else if (isWarning) {
            bgGradient = 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)';
            icon = 'feather-alert-octagon';
        } else if (type === 'info') {
            bgGradient = 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)';
            icon = 'feather-info';
        }

        const toastEl = document.createElement('div');
        toastEl.className = 'toast show border-0 shadow-lg mb-2 text-white';
        toastEl.style.borderRadius = '16px';
        toastEl.style.background = bgGradient;
        toastEl.style.backdropFilter = 'blur(10px)';
        toastEl.style.minWidth = '260px';
        toastEl.style.pointerEvents = 'auto';

        toastEl.innerHTML = `
            <div class="d-flex align-items-center justify-content-between p-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="${icon} fs-5"></i>
                    <span class="fw-semibold fs-13">${message}</span>
                </div>
                <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 11px;"></button>
            </div>
        `;

        toastEl.querySelector('.btn-close').addEventListener('click', () => toastEl.remove());
        container.appendChild(toastEl);

        setTimeout(() => {
            if (toastEl.parentNode) {
                toastEl.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                toastEl.style.opacity = '0';
                toastEl.style.transform = 'translateY(-10px)';
                setTimeout(() => toastEl.remove(), 400);
            }
        }, 3800);
    }

    function generateStrongPassword(len = 12) {
        const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%&*';
        let pass = '';
        for (let i = 0; i < len; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return pass;
    }

    // 1. Profile Update Form (Ajax)
    const profileForm = document.getElementById('userMainEditForm');
    const saveProfileBtn = document.getElementById('saveProfileBtn');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (saveProfileBtn) {
                saveProfileBtn.disabled = true;
                saveProfileBtn.querySelector('.spinner-border')?.classList.remove('d-none');
            }

            const formData = new FormData(profileForm);

            fetch(profileForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (saveProfileBtn) {
                    saveProfileBtn.disabled = false;
                    saveProfileBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }

                if (data.success) {
                    showAdminToast(data.message, 'success');
                } else {
                    showAdminToast(data.message || 'Update failed', 'danger');
                }
            })
            .catch(() => {
                if (saveProfileBtn) {
                    saveProfileBtn.disabled = false;
                    saveProfileBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }
                showAdminToast('Network error updating profile', 'danger');
            });
        });
    }

    // 2. Wallet Update Form (Ajax)
    const walletForm = document.getElementById('walletEditForm');
    const saveWalletBtn = document.getElementById('saveWalletBtn');
    if (walletForm) {
        walletForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (saveWalletBtn) {
                saveWalletBtn.disabled = true;
                saveWalletBtn.querySelector('.spinner-border')?.classList.remove('d-none');
            }

            const formData = new FormData(walletForm);
            const dataObj = Object.fromEntries(formData.entries());

            fetch(`/admin/users/${userId}/quick-update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(dataObj)
            })
            .then(res => res.json())
            .then(data => {
                if (saveWalletBtn) {
                    saveWalletBtn.disabled = false;
                    saveWalletBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }

                if (data.success) {
                    showAdminToast(data.message, 'success');
                    if (data.balances) {
                        const heroPts = document.getElementById('heroPtsValue');
                        const heroVu = document.getElementById('heroVuValue');
                        const heroNvu = document.getElementById('heroNvuValue');
                        const heroNlink = document.getElementById('heroNlinkValue');
                        const heroNsmart = document.getElementById('heroNsmartValue');

                        if (heroPts) heroPts.textContent = data.balances.pts;
                        if (heroVu) heroVu.textContent = data.balances.vu;
                        if (heroNvu) heroNvu.textContent = data.balances.nvu;
                        if (heroNlink) heroNlink.textContent = data.balances.nlink;
                        if (heroNsmart) heroNsmart.textContent = data.balances.nsmart;
                    }
                } else {
                    showAdminToast(data.message || 'Wallet update failed', 'danger');
                }
            })
            .catch(() => {
                if (saveWalletBtn) {
                    saveWalletBtn.disabled = false;
                    saveWalletBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }
                showAdminToast('Error updating wallet balances', 'danger');
            });
        });
    }

    // Quick balance increment buttons
    document.querySelectorAll('.wallet-chip-add').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const amount = parseFloat(this.getAttribute('data-amount')) || 0;
            const input = document.getElementById(targetId);
            if (input) {
                const current = parseFloat(input.value) || 0;
                input.value = (current + amount).toFixed(2);
            }
        });
    });

    // 3. Password Update Form (Ajax)
    const editPasswordForm = document.getElementById('editPasswordForm');
    const savePasswordBtn = document.getElementById('savePasswordBtn');
    const editPasswordInput = document.getElementById('editPasswordInput');
    const generateEditPasswordBtn = document.getElementById('generateEditPasswordBtn');
    const copyEditPasswordBtn = document.getElementById('copyEditPasswordBtn');

    if (generateEditPasswordBtn && editPasswordInput) {
        generateEditPasswordBtn.addEventListener('click', () => {
            editPasswordInput.value = generateStrongPassword(12);
        });
    }

    if (copyEditPasswordBtn && editPasswordInput) {
        copyEditPasswordBtn.addEventListener('click', () => {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(editPasswordInput.value);
                showAdminToast('{{ __("messages.password_copied") }}', 'info');
            }
        });
    }

    if (editPasswordForm) {
        editPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (savePasswordBtn) {
                savePasswordBtn.disabled = true;
                savePasswordBtn.querySelector('.spinner-border')?.classList.remove('d-none');
            }

            fetch(editPasswordForm.action, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ password: editPasswordInput.value })
            })
            .then(res => res.json())
            .then(data => {
                if (savePasswordBtn) {
                    savePasswordBtn.disabled = false;
                    savePasswordBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }

                if (data.success) {
                    showAdminToast(data.message, 'success');
                } else {
                    showAdminToast(data.message || 'Password update failed', 'danger');
                }
            })
            .catch(() => {
                if (savePasswordBtn) {
                    savePasswordBtn.disabled = false;
                    savePasswordBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }
                showAdminToast('Error updating password', 'danger');
            });
        });
    }

    // 4. Reset 2FA
    const resetUser2faBtn = document.getElementById('resetUser2faBtn');
    if (resetUser2faBtn) {
        resetUser2faBtn.addEventListener('click', function() {
            if (!confirm('{{ __("messages.reset_2fa_confirm") }}')) return;

            fetch(`/admin/users/${userId}/reset-2fa`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showAdminToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showAdminToast(data.message || 'Reset failed', 'danger');
                }
            });
        });
    }

    // 5. Direct In-App Notification (Ajax)
    const directNotifyForm = document.getElementById('directNotifyForm');
    const directNotifySubmitBtn = document.getElementById('directNotifySubmitBtn');
    const directNotifyMessage = document.getElementById('directNotifyMessage');

    if (directNotifyForm) {
        directNotifyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (directNotifySubmitBtn) {
                directNotifySubmitBtn.disabled = true;
                directNotifySubmitBtn.querySelector('.spinner-border')?.classList.remove('d-none');
            }

            fetch(`/admin/users/${userId}/notify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ message: directNotifyMessage.value })
            })
            .then(res => res.json())
            .then(data => {
                if (directNotifySubmitBtn) {
                    directNotifySubmitBtn.disabled = false;
                    directNotifySubmitBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }

                if (data.success) {
                    directNotifyMessage.value = '';
                    showAdminToast(data.message, 'success');
                } else {
                    showAdminToast(data.message || 'Notification failed', 'danger');
                }
            })
            .catch(() => {
                if (directNotifySubmitBtn) {
                    directNotifySubmitBtn.disabled = false;
                    directNotifySubmitBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }
                showAdminToast('Error sending notification', 'danger');
            });
        });
    }
})();
</script>
@endpush
