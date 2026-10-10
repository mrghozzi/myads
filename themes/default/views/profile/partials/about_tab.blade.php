<div class="profile-about-tab-wrapper">
    <style>
        .profile-about-tab-wrapper {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .profile-about-card {
            background: var(--widget-box-bg, #ffffff);
            border: 1px solid var(--border-color, #e7e9f6);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 18px rgba(94, 92, 154, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .profile-about-hero-card {
            background: linear-gradient(135deg, rgba(97, 93, 250, 0.04) 0%, rgba(27, 200, 219, 0.06) 100%);
            border: 1px solid var(--border-color, #e7e9f6);
            border-radius: 16px;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .profile-about-hero-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .profile-about-badge {
            background: linear-gradient(135deg, #615dfa 0%, #4640ff 100%);
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 12px rgba(97, 93, 250, 0.25);
        }

        .profile-about-privacy-pill {
            font-size: 12px;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: 1px solid transparent;
        }

        .profile-about-privacy-pill.public {
            background: rgba(35, 210, 226, 0.12);
            color: #0fb8ca;
            border-color: rgba(35, 210, 226, 0.25);
        }

        .profile-about-privacy-pill.followers {
            background: rgba(97, 93, 250, 0.12);
            color: #615dfa;
            border-color: rgba(97, 93, 250, 0.25);
        }

        .profile-about-privacy-pill.private {
            background: rgba(142, 145, 172, 0.15);
            color: #6c718a;
            border-color: rgba(142, 145, 172, 0.25);
        }

        .profile-about-hero-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .profile-about-action-btn {
            height: 36px;
            padding: 0 16px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .profile-about-bio-body {
            position: relative;
            font-size: 15px;
            line-height: 1.85;
            color: var(--text-color, #3e3f5e);
            white-space: pre-line;
            word-break: break-word;
            padding: 8px 4px;
        }

        .profile-about-quote-icon {
            font-size: 28px;
            color: rgba(97, 93, 250, 0.2);
            margin-bottom: 8px;
            display: block;
        }

        /* Stats Grid */
        .profile-about-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 14px;
        }

        .profile-about-stat-card {
            background: var(--widget-box-bg, #ffffff);
            border: 1px solid var(--border-color, #e7e9f6);
            border-radius: 14px;
            padding: 16px 14px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .profile-about-stat-card:hover {
            transform: translateY(-2px);
            border-color: rgba(97, 93, 250, 0.35);
            box-shadow: 0 8px 24px rgba(97, 93, 250, 0.08);
        }

        .profile-about-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .profile-about-stat-icon.primary {
            background: rgba(97, 93, 250, 0.12);
            color: #615dfa;
        }

        .profile-about-stat-icon.secondary {
            background: rgba(35, 210, 226, 0.12);
            color: #0fb8ca;
        }

        .profile-about-stat-icon.warning {
            background: rgba(255, 171, 0, 0.12);
            color: #ffab00;
        }

        .profile-about-stat-icon.success {
            background: rgba(30, 215, 96, 0.12);
            color: #1ed760;
        }

        .profile-about-stat-icon.info {
            background: rgba(0, 150, 255, 0.12);
            color: #0096ff;
        }

        .profile-about-stat-icon.purple {
            background: rgba(155, 89, 182, 0.12);
            color: #9b59b6;
        }

        .profile-about-stat-meta {
            min-width: 0;
            flex: 1;
        }

        .profile-about-stat-value {
            font-size: 17px;
            font-weight: 800;
            color: var(--text-color, #2b2f4a);
            margin: 0;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .profile-about-stat-label {
            font-size: 12px;
            color: var(--text-color-alt, #8f91ac);
            margin: 2px 0 0;
            line-height: 1.2;
            font-weight: 600;
        }

        /* Identity & Details Grid */
        .profile-about-two-cols {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        @media (max-width: 768px) {
            .profile-about-two-cols {
                grid-template-columns: 1fr;
            }
            .profile-about-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .profile-about-hero-card {
                flex-direction: column;
                align-items: flex-start;
            }
            .profile-about-hero-actions {
                width: 100%;
            }
            .profile-about-hero-actions .button {
                flex: 1;
                justify-content: center;
            }
        }

        .profile-about-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-color, #e7e9f6);
        }

        .profile-about-section-title {
            font-size: 15px;
            font-weight: 800;
            margin: 0;
            color: var(--text-color, #2b2f4a);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .profile-about-detail-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed var(--border-color, #ebebeb);
            font-size: 13px;
        }

        .profile-about-detail-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .profile-about-detail-label {
            color: var(--text-color-alt, #8f91ac);
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

        .profile-about-detail-value {
            color: var(--text-color, #2b2f4a);
            font-weight: 700;
            text-align: right;
        }

        /* Social Cards */
        .profile-about-social-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .profile-about-social-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            border-radius: 12px;
            background: var(--dark-light-color, #f8f9ff);
            border: 1px solid var(--border-color, #e7e9f6);
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .profile-about-social-item:hover {
            transform: translateX(is_locale_rtl() ? -4px : 4px);
            border-color: var(--platform-color, #615dfa);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
        }

        .profile-about-social-meta {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .profile-about-social-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 15px;
        }

        .profile-about-social-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-color, #2b2f4a);
            margin: 0;
        }

        /* Empty State */
        .profile-about-empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .profile-about-empty-icon {
            width: 68px;
            height: 68px;
            border-radius: 50%;
            background: rgba(97, 93, 250, 0.1);
            color: #615dfa;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 16px;
        }

        /* Restricted State */
        .profile-about-restricted-card {
            text-align: center;
            padding: 50px 24px;
            background: var(--widget-box-bg, #ffffff);
            border: 1px solid var(--border-color, #e7e9f6);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        }

        .profile-about-restricted-icon {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 20px;
        }

        /* Dark Theme overrides */
        body[data-theme="css_d"] .profile-about-card,
        body[data-theme="css_d"] .profile-about-stat-card,
        body[data-theme="css_d"] .profile-about-restricted-card {
            background: #1d2333;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        }

        body[data-theme="css_d"] .profile-about-hero-card {
            background: linear-gradient(135deg, rgba(97, 93, 250, 0.12) 0%, rgba(27, 200, 219, 0.1) 100%);
            border-color: rgba(255, 255, 255, 0.1);
        }

        body[data-theme="css_d"] .profile-about-social-item {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.06);
        }

        body[data-theme="css_d"] .profile-about-stat-value,
        body[data-theme="css_d"] .profile-about-section-title,
        body[data-theme="css_d"] .profile-about-detail-value,
        body[data-theme="css_d"] .profile-about-social-name,
        body[data-theme="css_d"] .profile-about-bio-body {
            color: #ffffff;
        }

        body[data-theme="css_d"] .profile-about-stat-label,
        body[data-theme="css_d"] .profile-about-detail-label {
            color: #9aa4bf;
        }
    </style>

    {{-- 1. PRIVACY RESTRICTIONS CHECK --}}
    @if(!empty($profileContentNotice))
        <div class="profile-about-restricted-card">
            <div class="profile-about-restricted-icon">
                <i class="fa-solid fa-user-lock"></i>
            </div>
            <h3 class="fw-bold mb-2">{{ __('messages.section_private') }}</h3>
            <p class="text-muted mb-4">{{ $profileContentNotice }}</p>
            @if(auth()->check() && !($isFollowing ?? false) && !$isOwnProfile)
                <form action="{{ route('profile.follow', $user->username) }}" method="POST" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="button secondary"><i class="fa fa-user-plus me-1"></i> {{ __('messages.follow') }}</button>
                </form>
            @elseif(!auth()->check())
                <a href="{{ route('login') }}" class="button primary"><i class="fa fa-arrow-right-to-bracket me-1"></i> {{ __('messages.login') }}</a>
            @endif
        </div>
    @elseif(!$canViewAbout)
        {{-- Section Restricted by About Privacy (Public / Followers / Private) --}}
        <div class="profile-about-restricted-card">
            <div class="profile-about-restricted-icon">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h3 class="fw-bold mb-2">{{ __('messages.section_private') }}</h3>
            <p class="text-muted mb-4">
                @if(($privacySettings->about_visibility ?? '') === 'followers')
                    {{ __('messages.profile_followers_only_notice') ?? 'This section is only available to followers.' }}
                @else
                    {{ __('messages.section_private') }}
                @endif
            </p>
            @if(($privacySettings->about_visibility ?? '') === 'followers' && auth()->check() && !($isFollowing ?? false) && !$isOwnProfile)
                <form action="{{ route('profile.follow', $user->username) }}" method="POST" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="button secondary"><i class="fa fa-user-plus me-1"></i> {{ __('messages.follow') }}</button>
                </form>
            @elseif(!auth()->check())
                <a href="{{ route('login') }}" class="button primary"><i class="fa fa-arrow-right-to-bracket me-1"></i> {{ __('messages.login') }}</a>
            @endif
        </div>
    @else
        {{-- 2. FULL AUTHORIZED ABOUT TAB DOSSIER --}}

        {{-- Hero Header Card --}}
        <div class="profile-about-hero-card">
            <div class="profile-about-hero-meta">
                <span class="profile-about-badge">
                    <i class="fa-solid fa-address-card"></i> {{ __('messages.about_me') }}
                </span>
                
                @if($isOwnProfile && isset($privacySettings))
                    @php
                        $curVisibility = $privacySettings->about_visibility ?? 'public';
                    @endphp
                    <span class="profile-about-privacy-pill {{ $curVisibility }}" title="{{ __('messages.about_visibility') ?? 'About Visibility' }}">
                        @if($curVisibility === 'public')
                            <i class="fa-solid fa-globe"></i> {{ __('messages.visibility_public') }}
                        @elseif($curVisibility === 'followers')
                            <i class="fa-solid fa-users"></i> {{ __('messages.visibility_followers') }}
                        @else
                            <i class="fa-solid fa-lock"></i> {{ __('messages.visibility_private') }}
                        @endif
                    </span>
                @endif
            </div>

            @if($isOwnProfile)
                <div class="profile-about-hero-actions">
                    <a href="{{ route('profile.edit') }}" class="button primary small" style="border-radius: 10px; height: 36px; line-height: 36px;">
                        <i class="fa-solid fa-pen-to-square me-1"></i> {{ __('messages.edit_profile') }}
                    </a>
                    <a href="{{ route('profile.privacy') }}" class="button secondary small" style="border-radius: 10px; height: 36px; line-height: 36px;">
                        <i class="fa-solid fa-shield-halved me-1"></i> {{ __('messages.privacy_settings') }}
                    </a>
                    <a href="{{ route('profile.social') }}" class="button secondary small" style="border-radius: 10px; height: 36px; line-height: 36px;">
                        <i class="fa-solid fa-link me-1"></i> {{ __('messages.social_links') }}
                    </a>
                </div>
            @endif
        </div>

        {{-- Main Bio Card --}}
        <div class="profile-about-card">
            <div class="profile-about-section-header">
                <h3 class="profile-about-section-title">
                    <i class="fa-solid fa-feather-pointed" style="color: #615dfa;"></i> {{ __('messages.about_me') }}
                </h3>
                @if($isOwnProfile)
                    <a href="{{ route('profile.edit') }}" class="btn btn-link btn-sm p-0 text-decoration-none fw-bold" style="color: #615dfa; font-size: 13px;">
                        <i class="fa-solid fa-pencil me-1"></i> {{ __('messages.edit') ?? 'Edit' }}
                    </a>
                @endif
            </div>

            @if(trim((string) $user->sig) !== '')
                <div class="profile-about-bio-body">
                    <i class="fa-solid fa-quote-right profile-about-quote-icon"></i>
                    {{ $user->sig }}
                </div>
            @else
                <div class="profile-about-empty-state">
                    <div class="profile-about-empty-icon">
                        <i class="fa-solid fa-pen-nib"></i>
                    </div>
                    <h4 class="fw-bold mb-2" style="font-size: 16px;">
                        {{ $isOwnProfile ? (__('messages.about_me_placeholder') ?: 'أخبر المجتمع عنك واهتماماتك') : __('messages.about_me_empty') }}
                    </h4>
                    <p class="text-muted small mb-4" style="max-width: 420px; margin: 0 auto;">
                        {{ $isOwnProfile ? 'أضف سيرة ذاتية مميزة، خبراتك وروابطك ليتمكن متابعوك من معرفة المزيد عنك.' : __('messages.about_me_empty') }}
                    </p>
                    @if($isOwnProfile)
                        <a href="{{ route('profile.edit') }}" class="button primary" style="padding: 10px 24px; border-radius: 12px; font-weight: 700;">
                            <i class="fa-solid fa-pencil me-2"></i> {{ __('messages.edit_profile') }}
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- Community Activity & Stats Grid --}}
        <div class="profile-about-stats-grid">
            {{-- 1. Joined Date --}}
            <div class="profile-about-stat-card">
                <div class="profile-about-stat-icon primary">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <div class="profile-about-stat-meta">
                    <p class="profile-about-stat-value">
                        {{ $user->created_at ? $user->created_at->format('M Y') : '—' }}
                    </p>
                    <p class="profile-about-stat-label">{{ __('messages.joined') ?? 'Member Since' }}</p>
                </div>
            </div>

            {{-- 2. Posts Count --}}
            <div class="profile-about-stat-card">
                <div class="profile-about-stat-icon secondary">
                    <i class="fa-regular fa-newspaper"></i>
                </div>
                <div class="profile-about-stat-meta">
                    <p class="profile-about-stat-value">{{ number_format($postsCount) }}</p>
                    <p class="profile-about-stat-label">{{ __('messages.posts') ?? 'Posts' }}</p>
                </div>
            </div>

            {{-- 3. Followers (Privacy Checked) --}}
            <div class="profile-about-stat-card">
                <div class="profile-about-stat-icon purple">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="profile-about-stat-meta">
                    <p class="profile-about-stat-value">
                        @if($canViewFollowers)
                            {{ number_format($followersCount) }}
                        @else
                            <i class="fa-solid fa-lock text-muted" title="{{ __('messages.private') ?? 'Private' }}" style="font-size: 14px;"></i>
                        @endif
                    </p>
                    <p class="profile-about-stat-label">{{ __('messages.Followers') }}</p>
                </div>
            </div>

            {{-- 4. Following (Privacy Checked) --}}
            <div class="profile-about-stat-card">
                <div class="profile-about-stat-icon info">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div class="profile-about-stat-meta">
                    <p class="profile-about-stat-value">
                        @if($canViewFollowing)
                            {{ number_format($followingCount) }}
                        @else
                            <i class="fa-solid fa-lock text-muted" title="{{ __('messages.private') ?? 'Private' }}" style="font-size: 14px;"></i>
                        @endif
                    </p>
                    <p class="profile-about-stat-label">{{ __('messages.following') }}</p>
                </div>
            </div>

            {{-- 5. Profile Views --}}
            <div class="profile-about-stat-card">
                <div class="profile-about-stat-icon warning">
                    <i class="fa-regular fa-eye"></i>
                </div>
                <div class="profile-about-stat-meta">
                    <p class="profile-about-stat-value">{{ number_format($user->vu ?? 0) }}</p>
                    <p class="profile-about-stat-label">{{ __('messages.views') ?? 'Profile Views' }}</p>
                </div>
            </div>

            {{-- 6. Points / Reputation (Privacy Checked) --}}
            <div class="profile-about-stat-card">
                <div class="profile-about-stat-icon success">
                    <i class="fa-solid fa-trophy"></i>
                </div>
                <div class="profile-about-stat-meta">
                    <p class="profile-about-stat-value">
                        @if($canViewPoints)
                            {{ number_format($user->pts ?? 0) }}
                        @else
                            <i class="fa-solid fa-lock text-muted" title="{{ __('messages.private') ?? 'Private' }}" style="font-size: 14px;"></i>
                        @endif
                    </p>
                    <p class="profile-about-stat-label">{{ __('messages.pts_history') ?? 'Points' }}</p>
                </div>
            </div>
        </div>

        {{-- Two Columns: Identity & Roles | Social Networks --}}
        <div class="profile-about-two-cols">
            {{-- Column 1: Identity & Roles --}}
            <div class="profile-about-card">
                <div class="profile-about-section-header">
                    <h3 class="profile-about-section-title">
                        <i class="fa-solid fa-id-card-clip" style="color: #615dfa;"></i> {{ __('messages.account_details') ?? 'Account Details' }}
                    </h3>
                </div>

                <div class="profile-about-details-list">
                    {{-- Username --}}
                    <div class="profile-about-detail-item">
                        <span class="profile-about-detail-label">
                            <i class="fa-solid fa-at"></i> {{ __('messages.username') }}
                        </span>
                        <span class="profile-about-detail-value">{{ $user->username }}</span>
                    </div>

                    {{-- Public UID --}}
                    @if($user->usesPublicMemberIds() && !empty($user->public_uid))
                        <div class="profile-about-detail-item">
                            <span class="profile-about-detail-label">
                                <i class="fa-solid fa-fingerprint"></i> {{ __('messages.member_id') ?? 'Public Member UID' }}
                            </span>
                            <span class="profile-about-detail-value">
                                <code style="font-size: 12px; background: rgba(97, 93, 250, 0.1); color: #615dfa; padding: 2px 6px; border-radius: 6px;">{{ $user->publicRouteIdentifier() }}</code>
                            </span>
                        </div>
                    @endif

                    {{-- Verification Status --}}
                    <div class="profile-about-detail-item">
                        <span class="profile-about-detail-label">
                            <i class="fa-solid fa-certificate"></i> {{ __('messages.verification_status') ?? 'Verification' }}
                        </span>
                        <span class="profile-about-detail-value">
                            @if($user->hasVerifiedBadge())
                                <span class="badge bg-success text-white rounded-pill px-2 py-1" style="font-size: 11px;">
                                    <i class="fa-solid fa-circle-check me-1"></i> {{ __('messages.verified') ?? 'Verified' }}
                                </span>
                            @else
                                <span class="text-muted small">{{ __('messages.not_verified') ?? 'Standard Member' }}</span>
                            @endif
                        </span>
                    </div>

                    {{-- Subscription Badge if active --}}
                    @if(!empty($subscriptionProfileBadge))
                        <div class="profile-about-detail-item">
                            <span class="profile-about-detail-label">
                                <i class="fa-solid fa-crown" style="color: #ffb800;"></i> {{ __('messages.subscription_plan') ?? 'Membership' }}
                            </span>
                            <span class="profile-about-detail-value">
                                <span class="badge text-white rounded-pill px-2 py-1" style="background-color: {{ $subscriptionProfileBadge['color'] ?? '#615dfa' }}; font-size: 11px;">
                                    {{ $subscriptionProfileBadge['label'] }}
                                </span>
                            </span>
                        </div>
                    @endif

                    {{-- Community Role --}}
                    <div class="profile-about-detail-item">
                        <span class="profile-about-detail-label">
                            <i class="fa-solid fa-shield-halved"></i> {{ __('messages.role') ?? 'Role' }}
                        </span>
                        <span class="profile-about-detail-value">
                            @if($user->isAdmin())
                                <span class="badge bg-danger text-white rounded-pill px-2 py-1" style="font-size: 11px;">
                                    {{ __('messages.admin') ?? 'Admin' }}
                                </span>
                            @elseif($user->forumRoleLabel() !== __('messages.forum_role_member'))
                                <span class="badge bg-primary text-white rounded-pill px-2 py-1" style="font-size: 11px;">
                                    {{ $user->forumRoleLabel() }}
                                </span>
                            @else
                                <span class="text-muted small">{{ __('messages.member') ?? 'Member' }}</span>
                            @endif
                        </span>
                    </div>

                    {{-- Forum Activity --}}
                    @if(!empty($communityStats['topics_count']) || !empty($communityStats['comments_count']))
                        <div class="profile-about-detail-item">
                            <span class="profile-about-detail-label">
                                <i class="fa-solid fa-comments"></i> {{ __('messages.forum_activity') ?? 'Forum Activity' }}
                            </span>
                            <span class="profile-about-detail-value">
                                {{ number_format($communityStats['topics_count'] ?? 0) }} {{ __('messages.topics') ?? 'topics' }},
                                {{ number_format($communityStats['comments_count'] ?? 0) }} {{ __('messages.replies') ?? 'replies' }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Column 2: Connected Social Networks --}}
            <div class="profile-about-card">
                <div class="profile-about-section-header">
                    <h3 class="profile-about-section-title">
                        <i class="fa-solid fa-share-nodes" style="color: #615dfa;"></i> {{ __('messages.social_links') }}
                    </h3>
                    @if($isOwnProfile)
                        <a href="{{ route('profile.social') }}" class="btn btn-link btn-sm p-0 text-decoration-none fw-bold" style="color: #615dfa; font-size: 13px;">
                            <i class="fa-solid fa-gear me-1"></i> {{ __('messages.manage') ?? 'Manage' }}
                        </a>
                    @endif
                </div>

                @if(!empty($socialLinks))
                    <div class="profile-about-social-list">
                        @foreach($socialLinks as $platform => $url)
                            @php
                                $platformColor = match($platform) {
                                    'facebook' => '#1877f2',
                                    'twitter' => '#000000',
                                    'instagram' => '#e4405f',
                                    'youtube' => '#ff0000',
                                    'linkedin' => '#0077b5',
                                    'discord' => '#5865F2',
                                    'tiktok' => '#fe2c55',
                                    'github' => '#24292e',
                                    'reddit' => '#ff4500',
                                    'vkontakte' => '#0077ff',
                                    default => '#615dfa'
                                };
                                $platformIcon = match($platform) {
                                    'facebook' => 'fab fa-facebook-f',
                                    'twitter' => 'fab fa-x-twitter',
                                    'vkontakte' => 'fab fa-vk',
                                    'linkedin' => 'fab fa-linkedin-in',
                                    'instagram' => 'fab fa-instagram',
                                    'youtube' => 'fab fa-youtube',
                                    'threads' => 'fab fa-threads',
                                    'reddit' => 'fab fa-reddit-alien',
                                    'github' => 'fab fa-github',
                                    'tiktok' => 'fab fa-tiktok',
                                    'discord' => 'fab fa-discord',
                                    default => 'fa fa-link',
                                };
                            @endphp
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="profile-about-social-item" style="--platform-color: {{ $platformColor }};">
                                <div class="profile-about-social-meta">
                                    <div class="profile-about-social-icon" style="background-color: {{ $platformColor }};">
                                        <i class="{{ $platformIcon }}"></i>
                                    </div>
                                    <p class="profile-about-social-name">{{ ucfirst($platform) }}</p>
                                </div>
                                <i class="fa-solid fa-arrow-up-right-from-square text-muted small"></i>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="profile-about-empty-state" style="padding: 24px 10px;">
                        <p class="text-muted small mb-3">
                            {{ $isOwnProfile ? 'لم تقم بربط أي من حساباتك الاجتماعية بعد.' : 'لم يقم هذا العضو بربط حسابات اجتماعية.' }}
                        </p>
                        @if($isOwnProfile)
                            <a href="{{ route('profile.social') }}" class="button secondary small" style="border-radius: 8px;">
                                <i class="fa-solid fa-plus me-1"></i> {{ __('messages.social_links') }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Badges Showcase (if any) --}}
        @if($badgeShowcase->isNotEmpty())
            <div class="profile-about-card">
                <div class="profile-about-section-header">
                    <h3 class="profile-about-section-title">
                        <i class="fa-solid fa-award" style="color: #ffab00;"></i> {{ __('messages.badges') }}
                    </h3>
                    @if($isOwnProfile)
                        <a href="{{ route('profile.badges') }}" class="btn btn-link btn-sm p-0 text-decoration-none fw-bold" style="color: #615dfa; font-size: 13px;">
                            <i class="fa-solid fa-gear me-1"></i> {{ __('messages.manage') ?? 'Manage' }}
                        </a>
                    @endif
                </div>

                <div class="profile-hub-badge-grid">
                    @foreach($badgeShowcase as $badgeItem)
                        @php $badge = $badgeItem->badge; @endphp
                        @if($badge)
                            <div class="profile-hub-badge-card">
                                <div class="profile-hub-badge-icon">
                                    @if($badge->icon && str_contains($badge->icon, ' '))
                                        <i class="{{ $badge->icon }}" aria-hidden="true"></i>
                                    @elseif($badge->icon && str_starts_with($badge->icon, 'fa-'))
                                        <i class="fa {{ $badge->icon }}" aria-hidden="true"></i>
                                    @elseif($badge->icon && str_starts_with($badge->icon, 'svg-'))
                                        <svg class="icon {{ $badge->icon }}"><use xlink:href="#{{ $badge->icon }}"></use></svg>
                                    @else
                                        <i class="fa fa-trophy" aria-hidden="true"></i>
                                    @endif
                                </div>
                                <p class="user-status-title" style="font-size: 13px; font-weight: 700; margin: 0;">{{ __('messages.' . $badge->name_key) }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
