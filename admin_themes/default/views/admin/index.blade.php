@extends('admin::layouts.admin')

@section('content')
    <div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li>{{ __('messages.dashboard') ?? 'Dashboard' }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_panel') ?? 'Admin Panel' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.dashboard') ?? 'Dashboard' }}</h1>
            <p class="admin-hero__copy">{{ __('messages.statistics') }} / {{ __('messages.users') }} / {{ __('messages.ads') }}</p>
            <div class="admin-stat-strip">
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.users') }}</span>
                    <span class="admin-stat-value">{{ number_format($stats['users']) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.Posts') ?? 'Posts' }}</span>
                    <span class="admin-stat-value">{{ number_format($stats['posts']) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.bannads') }}</span>
                    <span class="admin-stat-value">{{ number_format($stats['banners']['total']) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.exvisit') }}</span>
                    <span class="admin-stat-value">{{ number_format($stats['visits']['total']) }}</span>
                </div>
            </div>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-summary-grid w-100">
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.online') }}</span>
                    <span class="admin-summary-value">{{ number_format($stats['users_online']) }}</span>
                </div>
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.textads') }}</span>
                    <span class="admin-summary-value">{{ number_format($stats['links']['total']) }}</span>
                </div>
            </div>
        </div>
    </section>
    <!-- Update Alert -->
    @if($latestVersion && version_compare($latestVersion, $currentVersion, '>'))
    <div class="alert alert-warning d-flex align-items-center justify-content-between mb-4 border-0 shadow-sm" role="alert" style="border-radius: 12px; background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);">
        <div class="d-flex align-items-center">
            <div class="me-3" style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center;">
                <i class="feather-zap text-white" style="font-size: 22px;"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1 text-dark">{{ __('messages.new_version_available') ?? 'New Version Available!' }} — v{{ $latestVersion }}</h6>
                <p class="mb-0 small" style="color: #92400e;">{{ __('messages.update_available_desc') ?? 'A new version is available for download.' }}</p>
            </div>
        </div>
        <a href="{{ route('admin.updates') }}" class="btn btn-sm fw-bold px-3 shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; border: none; border-radius: 8px;">
            <i class="feather-download-cloud me-1"></i> {{ __('messages.update_now') ?? 'Update Now' }}
        </a>
    </div>
    @endif

    <!-- ═══════════════════ TOP STATS ROW ═══════════════════ -->
    <div class="row g-3 mb-4">
        <!-- Banners Card -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 14px;">
                <div class="card-body position-relative" style="padding: 1.25rem;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #6366f1, #8b5cf6); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="feather-image text-white" style="font-size: 22px;"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.75rem; line-height: 1;">{{ number_format($stats['banners']['total']) }}</h3>
                            <span class="text-muted fw-medium" style="font-size: 0.8rem;">{{ __('messages.bannads') }}</span>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-3 pt-2 border-top">
                        <span class="fs-12 text-muted"><i class="feather-eye me-1" style="color: #6366f1;"></i> {{ number_format($stats['banners']['views']) }}</span>
                        <span class="fs-12 text-muted"><i class="feather-mouse-pointer me-1" style="color: #8b5cf6;"></i> {{ number_format($stats['banners']['clicks']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Text Ads Card -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 14px;">
                <div class="card-body position-relative" style="padding: 1.25rem;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #f59e0b, #f97316); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="feather-type text-white" style="font-size: 22px;"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.75rem; line-height: 1;">{{ number_format($stats['links']['total']) }}</h3>
                            <span class="text-muted fw-medium" style="font-size: 0.8rem;">{{ __('messages.textads') }}</span>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-3 pt-2 border-top">
                        <span class="fs-12 text-muted"><i class="feather-mouse-pointer me-1" style="color: #f59e0b;"></i> {{ number_format($stats['links']['clicks']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visits Card -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 14px;">
                <div class="card-body position-relative" style="padding: 1.25rem;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="feather-repeat text-white" style="font-size: 22px;"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.75rem; line-height: 1;">{{ number_format($stats['visits']['total']) }}</h3>
                            <span class="text-muted fw-medium" style="font-size: 0.8rem;">{{ __('messages.exvisit') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 14px;">
                <div class="card-body position-relative" style="padding: 1.25rem;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #3b82f6, #2563eb); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="feather-users text-white" style="font-size: 22px;"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.75rem; line-height: 1;">{{ number_format($stats['users']) }}</h3>
                            <span class="text-muted fw-medium" style="font-size: 0.8rem;">{{ __('messages.users') }}</span>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-3 pt-2 border-top">
                        <span class="fs-12" style="color: #10b981;"><i class="feather-circle me-1"></i> {{ $stats['users_online'] }} {{ __('messages.online') }}</span>
                        <span class="fs-12 text-muted"><i class="feather-edit-3 me-1"></i> {{ number_format($stats['posts']) }} {{ __('messages.Posts') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════ CHARTS ROW ═══════════════════ -->
    <div class="row g-3 mb-4">
        <!-- Ad Distribution Doughnut Chart -->
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header border-0 bg-transparent pt-4 pb-0 px-4">
                    <h6 class="fw-bold text-dark mb-0"><i class="feather-pie-chart me-2" style="color: #6366f1;"></i> {{ __('messages.statistics') }}</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 280px;">
                    <div style="width: 100%; max-width: 260px;">
                        <canvas id="adDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Engagement Bar Chart -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header border-0 bg-transparent pt-4 pb-0 px-4">
                    <h6 class="fw-bold text-dark mb-0"><i class="feather-bar-chart-2 me-2" style="color: #3b82f6;"></i> {{ __('messages.activity_engagement') ?? 'Views & Clicks' }}</h6>
                </div>
                <div class="card-body d-flex align-items-center" style="min-height: 280px;">
                    <div style="width: 100%;">
                        <canvas id="engagementChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════ COMMUNITY CHARTS ═══════════════════ -->
    <div class="row g-3 mb-4">
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header border-0 bg-transparent pt-4 pb-0 px-4">
                    <h6 class="fw-bold text-dark mb-0"><i class="feather-edit-3 me-2" style="color: #6366f1;"></i> {{ __('messages.Posts') }} (30 {{ __('messages.days') ?? 'days' }})</h6>
                </div>
                <div class="card-body" style="height: 350px;">
                    <canvas id="postsCommunityChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header border-0 bg-transparent pt-4 pb-0 px-4">
                    <h6 class="fw-bold text-dark mb-0"><i class="feather-message-circle me-2" style="color: #10b981;"></i> {{ __('messages.comments') ?? 'Comments' }} & {{ __('messages.allreactions') }} (30 {{ __('messages.days') ?? 'days' }})</h6>
                </div>
                <div class="card-body" style="height: 350px;">
                    <canvas id="engagementCommunityChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════ REACTION COUNTERS ═══════════════════ -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="d-flex align-items-center justify-content-around flex-wrap py-4 px-2 bg-white">
                @php
                    $reactionIcons = [
                        'like' => 'like.png',
                        'love' => 'love.png',
                        'dislike' => 'dislike.png',
                        'funny' => 'funny.png',
                        'wow' => 'wow.png',
                        'sad' => 'sad.png',
                        'angry' => 'angry.png',
                        'happy' => 'happy.png'
                    ];
                    // Reorder to match visual flow: like, love, dislike, happy, funny, wow, sad, angry
                    $orderedReactions = [];
                    foreach(['like', 'love', 'dislike', 'happy', 'funny', 'wow', 'sad', 'angry'] as $key) {
                        if(isset($reactionsSummary[$key])) $orderedReactions[$key] = $reactionsSummary[$key];
                    }
                @endphp
                @foreach($orderedReactions as $type => $count)
                    <div class="text-center px-3 py-2">
                        <div class="mb-2">
                            <img src="{{ theme_asset('img/reaction/' . $reactionIcons[$type]) }}" alt="{{ $type }}" style="width: 42px; height: 42px; object-fit: contain;">
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">{{ number_format($count) }}</h5>
                        <small class="text-uppercase text-muted fw-semibold" style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ $type }}</small>
                    </div>
                    @if(!$loop->last)
                        <div class="vr d-none d-md-block opacity-10" style="height: 40px; margin-top: 10px;"></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- ═══════════════════ ACTIVITY & ACTIONS ROW ═══════════════════ -->
    <div class="row g-3 mb-4">
        <!-- Activity Stats (Left Column) -->
        <div class="col-xxl-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                <div class="card-header border-0 bg-transparent pt-4 pb-0 px-4">
                    <h6 class="fw-bold text-dark mb-0"><i class="feather-activity me-2" style="color: #10b981;"></i> {{ __('messages.activity_engagement') ?? 'Activity & Engagement' }}</h6>
                </div>
                <div class="card-body px-4">
                    <div class="row g-4">
                        <!-- Last Member -->
                        <div class="col-md-6 col-lg-3">
                            <div class="text-center p-3 rounded-3" style="background: linear-gradient(135deg, rgba(99,102,241,0.06), rgba(139,92,246,0.06));">
                                <div class="mb-3" style="width: 56px; height: 56px; border-radius: 50%; margin: 0 auto; overflow: hidden; border: 3px solid rgba(99,102,241,0.2);">
                                    <img src="{{ $stats['last_user'] && $stats['last_user']->img ? asset($stats['last_user']->img) : asset('themes/default/assets/admin-duralux/images/avatar/undefined.png') }}" alt="" class="img-fluid" style="width:100%; height:100%; object-fit:cover;">
                                </div>
                                <h6 class="mb-1 fw-semibold text-muted" style="font-size: 0.75rem;">{{ __('messages.lastrm') }}</h6>
                                @if($stats['last_user'])
                                    <a href="{{ route('profile.show', $stats['last_user']->username) }}" class="fw-bold" style="color: #6366f1; font-size: 0.85rem;">{{ $stats['last_user']->username }}</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>

                        <!-- Last Post -->
                        <div class="col-md-6 col-lg-3">
                            <div class="text-center p-3 rounded-3" style="background: linear-gradient(135deg, rgba(245,158,11,0.06), rgba(249,115,22,0.06));">
                                <div class="mb-3 d-flex align-items-center justify-content-center mx-auto" style="width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, rgba(245,158,11,0.15), rgba(249,115,22,0.15));">
                                    <i class="feather-clock" style="color: #f59e0b; font-size: 20px;"></i>
                                </div>
                                <h6 class="mb-1 fw-semibold text-muted" style="font-size: 0.75rem;">{{ __('messages.lastps') }}</h6>
                                <p class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">
                                    @if($stats['last_post'])
                                        {{ $stats['last_post']->date_formatted }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Reactions -->
                        <div class="col-md-6 col-lg-3">
                            <div class="text-center p-3 rounded-3" style="background: linear-gradient(135deg, rgba(239,68,68,0.06), rgba(244,63,94,0.06));">
                                <div class="mb-3 d-flex align-items-center justify-content-center mx-auto" style="width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, rgba(239,68,68,0.15), rgba(244,63,94,0.15));">
                                    <i class="feather-thumbs-up" style="color: #ef4444; font-size: 20px;"></i>
                                </div>
                                <h6 class="mb-1 fw-semibold text-muted" style="font-size: 0.75rem;">{{ __('messages.allreactions') }}</h6>
                                <h4 class="fw-bold text-dark mb-0" style="font-size: 1.25rem;">{{ number_format($stats['reactions']['total']) }}</h4>
                            </div>
                        </div>

                        <!-- Followers -->
                        <div class="col-md-6 col-lg-3">
                            <div class="text-center p-3 rounded-3" style="background: linear-gradient(135deg, rgba(59,130,246,0.06), rgba(37,99,235,0.06));">
                                <div class="mb-3 d-flex align-items-center justify-content-center mx-auto" style="width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(37,99,235,0.15));">
                                    <i class="feather-user-plus" style="color: #3b82f6; font-size: 20px;"></i>
                                </div>
                                <h6 class="mb-1 fw-semibold text-muted" style="font-size: 0.75rem;">{{ __('messages.allFollowers') }}</h6>
                                <h4 class="fw-bold text-dark mb-0" style="font-size: 1.25rem;">{{ number_format($stats['followers']) }}</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Secondary Stats Row -->
                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(99,102,241,0.04); border: 1px solid rgba(99,102,241,0.08);">
                                <i class="feather-message-circle" style="color: #6366f1; font-size: 18px;"></i>
                                <div>
                                    <div class="fw-bold text-dark">{{ number_format($stats['topics']) }}</div>
                                    <span class="text-muted" style="font-size: 0.75rem;">{{ __('messages.topics') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(16,185,129,0.04); border: 1px solid rgba(16,185,129,0.08);">
                                <i class="feather-globe" style="color: #10b981; font-size: 18px;"></i>
                                <div>
                                    <div class="fw-bold text-dark">{{ number_format($stats['listings']) }}</div>
                                    <span class="text-muted" style="font-size: 0.75rem;">{{ __('messages.listings') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(245,158,11,0.04); border: 1px solid rgba(245,158,11,0.08);">
                                <i class="feather-shopping-bag" style="color: #f59e0b; font-size: 18px;"></i>
                                <div>
                                    <div class="fw-bold text-dark">{{ number_format($stats['products']) }}</div>
                                    <span class="text-muted" style="font-size: 0.75rem;">{{ __('messages.products') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column (Actions & Info) -->
        <div class="col-xxl-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 14px;">
                <div class="card-header border-0 bg-transparent pt-4 pb-0 px-4">
                    <h6 class="fw-bold text-dark mb-0"><i class="feather-zap me-2" style="color: #f59e0b;"></i> {{ __('messages.actions') ?? 'Quick Actions' }}</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.reports') }}" class="btn d-flex justify-content-between align-items-center px-3 py-2" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; border: none; border-radius: 10px;">
                            <span><i class="feather-flag me-2"></i>{{ __('messages.report') }}</span>
                            <span class="badge bg-white" style="color: #6366f1;">{{ $stats['reports']['pending'] }}</span>
                        </a>
                        
                        <div class="btn-group w-100" role="group">
                            @php $is_rtl = is_locale_rtl(); @endphp
                            <a href="{{ url('/sitemap.xml') }}" target="_blank" class="btn w-25 px-3 py-2" style="background: #1e293b; color: #fff; border: none; border-radius: {{ $is_rtl ? '0 10px 10px 0' : '10px 0 0 10px' }};">
                                <i class="feather-external-link"></i>
                            </a>
                            <form action="{{ route('admin.sitemap.generate') }}" method="POST" class="w-75">
                                @csrf
                                <button type="submit" class="btn w-100 px-3 py-2" style="background: linear-gradient(135deg, #10b981, #059669); color: #fff; border: none; border-radius: {{ $is_rtl ? '10px 0 0 10px' : '0 10px 10px 0' }};">
                                    <i class="feather-refresh-cw me-1"></i> {{ __('messages.seo_refresh_sitemap') }}
                                </button>
                            </form>
                        </div>

                        <a href="https://github.com/mrghozzi/myads/wiki/changelogs" target="_blank" class="btn px-3 py-2" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; border: none; border-radius: 10px;">
                            <i class="feather-book-open me-1"></i> {{ __('messages.Changelogs') }} <i class="feather-external-link ms-1" style="font-size: 12px;"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Developer Info -->
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 14px;">
                <div class="card-body px-4 py-4 text-center">
                    <div class="mb-3 d-flex align-items-center justify-content-center mx-auto" style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, rgba(59,130,246,0.1), rgba(99,102,241,0.1));">
                        <i class="feather-code" style="color: #3b82f6; font-size: 20px;"></i>
                    </div>
                    <h6 class="fw-semibold text-muted mb-1" style="font-size: 0.8rem;">{{ __('messages.developed_by') ?? 'Developed by' }}</h6>
                    <a href="https://github.com/mrghozzi" target="_blank" class="fw-bold" style="color: #3b82f6;">MrGhozzi</a>
                    
                    <div class="d-flex align-items-center justify-content-center gap-2 mt-3">
                        <span class="text-muted" style="font-size: 0.8rem;">{{ __('messages.version') }}</span>
                        <span class="badge fw-semibold" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); color: #fff; font-size: 0.75rem; padding: 4px 10px; border-radius: 6px;">v{{ $currentVersion }}</span>
                        @if($latestVersion && version_compare($latestVersion, $currentVersion, '>'))
                            <span class="badge bg-soft-warning text-warning" style="font-size: 0.7rem;">{{ __('messages.update_available') }}</span>
                        @elseif($latestVersion)
                            <span class="badge bg-soft-success text-success" style="font-size: 0.7rem;">✓ {{ __('messages.system_up_to_date') }}</span>
                        @endif
                    </div>
                    
                    <a href="{{ route('admin.updates') }}" class="btn btn-sm w-100 mt-3 py-2" style="background: rgba(59,130,246,0.08); color: #3b82f6; border: 1px solid rgba(59,130,246,0.15); border-radius: 10px;">
                        <i class="feather-refresh-cw me-1"></i> {{ __('messages.check_for_updates') ?? 'Check for Updates' }}
                    </a>
                </div>
            </div>

            <!-- Support -->
            <div class="card border-0 shadow-sm" style="border-radius: 14px;">
                <div class="card-header border-0 bg-transparent pt-4 pb-0 px-4 text-center">
                    <h6 class="fw-bold text-dark mb-0"><i class="feather-heart me-2" style="color: #ef4444;"></i> {{ __('messages.support_project') }}</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <a href="https://www.patreon.com/MrGhozzi" target="_blank" class="btn btn-sm px-3 py-2" style="background: rgba(30,41,59,0.06); color: #1e293b; border: 1px solid rgba(30,41,59,0.1); border-radius: 10px;">
                            <i class="feather-heart me-1" style="font-size: 13px;"></i> Patreon
                        </a>
                        <a href="https://ko-fi.com/mrghozzi" target="_blank" class="btn btn-sm px-3 py-2" style="background: rgba(245,158,11,0.06); color: #d97706; border: 1px solid rgba(245,158,11,0.1); border-radius: 10px;">
                            <i class="feather-coffee me-1" style="font-size: 13px;"></i> Ko-fi
                        </a>
                        <a href="https://www.ba9chich.com/en/mrghozzi" target="_blank" class="btn btn-sm px-3 py-2" style="background: rgba(59,130,246,0.06); color: #3b82f6; border: 1px solid rgba(59,130,246,0.1); border-radius: 10px;">
                            <i class="feather-gift me-1" style="font-size: 13px;"></i> Ba9chich
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
document.addEventListener('DOMContentLoaded', function() {
    // Detect dark mode
    var isDark = document.documentElement.classList.contains('app-skin-dark');
    var textColor = isDark ? '#94a3b8' : '#64748b';
    var gridColor = isDark ? 'rgba(148,163,184,0.1)' : 'rgba(0,0,0,0.06)';

    // ── Doughnut Chart: Ad Distribution ──
    var distCtx = document.getElementById('adDistributionChart');
    if (distCtx) {
        new Chart(distCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartData['distribution']['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['distribution']['data']) !!},
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.85)',
                        'rgba(245, 158, 11, 0.85)',
                        'rgba(16, 185, 129, 0.85)',
                    ],
                    borderColor: [
                        'rgba(99, 102, 241, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(16, 185, 129, 1)',
                    ],
                    borderWidth: 2,
                    hoverOffset: 8,
                    spacing: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: textColor,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 16,
                            font: { size: 12, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : '#fff',
                        titleColor: isDark ? '#e2e8f0' : '#1e293b',
                        bodyColor: isDark ? '#94a3b8' : '#64748b',
                        borderColor: isDark ? '#334155' : '#e2e8f0',
                        borderWidth: 1,
                        cornerRadius: 10,
                        padding: 12,
                        displayColors: true,
                    }
                }
            }
        });
    }

    // ── Bar Chart: Views & Clicks ──
    var engCtx = document.getElementById('engagementChart');
    if (engCtx) {
        new Chart(engCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['engagement']['labels']) !!},
                datasets: [{
                    label: '',
                    data: {!! json_encode($chartData['engagement']['data']) !!},
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.75)',
                        'rgba(139, 92, 246, 0.75)',
                        'rgba(245, 158, 11, 0.75)',
                    ],
                    borderColor: [
                        'rgba(99, 102, 241, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(245, 158, 11, 1)',
                    ],
                    borderWidth: 2,
                    borderRadius: 10,
                    borderSkipped: false,
                    barPercentage: 0.55,
                    categoryPercentage: 0.7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : '#fff',
                        titleColor: isDark ? '#e2e8f0' : '#1e293b',
                        bodyColor: isDark ? '#94a3b8' : '#64748b',
                        borderColor: isDark ? '#334155' : '#e2e8f0',
                        borderWidth: 1,
                        cornerRadius: 10,
                        padding: 12,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: textColor,
                            font: { size: 11, weight: '500' },
                            maxRotation: 0,
                        },
                        border: { display: false }
                    },
                    y: {
                        grid: {
                            color: gridColor,
                            drawBorder: false,
                        },
                        ticks: {
                            color: textColor,
                            font: { size: 11 },
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                if (value >= 1000) return (value / 1000).toFixed(0) + 'K';
                                return value;
                            }
                        },
                        border: { display: false }
                    }
                }
            }
        });
    }

    // ── Community: Posts ──
    var postsCommunityCtx = document.getElementById('postsCommunityChart');
    if (postsCommunityCtx) {
        new Chart(postsCommunityCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($communityChartData['labels']) !!},
                datasets: [
                    {
                        label: '{{ __('messages.post_text') ?? 'Text Posts' }}',
                        data: {!! json_encode($communityChartData['posts']['text']) !!},
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 3,
                        pointHoverRadius: 6
                    },
                    {
                        label: '{{ __('messages.post_link') ?? 'Link Posts' }}',
                        data: {!! json_encode($communityChartData['posts']['link']) !!},
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 3,
                        pointHoverRadius: 6
                    },
                    {
                        label: '{{ __('messages.post_gallery') ?? 'Gallery Posts' }}',
                        data: {!! json_encode($communityChartData['posts']['gallery']) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 3,
                        pointHoverRadius: 6
                    },
                    {
                        label: '{{ __('messages.forum_posts') ?? 'Forum Topics' }}',
                        data: {!! json_encode($communityChartData['posts']['forum']) !!},
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 3,
                        pointHoverRadius: 6
                    },
                    {
                        label: '{{ __('messages.store_posts') ?? 'Store Products' }}',
                        data: {!! json_encode($communityChartData['posts']['store']) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 3,
                        pointHoverRadius: 6
                    },
                    {
                        label: '{{ __('messages.order_posts') ?? 'Order Requests' }}',
                        data: {!! json_encode($communityChartData['posts']['orders']) !!},
                        borderColor: '#f43f5e',
                        backgroundColor: 'rgba(244, 63, 94, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 3,
                        pointHoverRadius: 6
                    },
                    {
                        label: '{{ __('messages.news_posts') ?? 'News Articles' }}',
                        data: {!! json_encode($communityChartData['posts']['news']) !!},
                        borderColor: '#94a3b8',
                        backgroundColor: 'rgba(148, 163, 184, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 3,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: textColor, font: { size: 12 } } },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor, font: { size: 10 } } },
                    y: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 10 } } }
                }
            }
        });
    }

    // ── Community: Comments & Reactions ──
    var engCommunityCtx = document.getElementById('engagementCommunityChart');
    if (engCommunityCtx) {
        new Chart(engCommunityCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($communityChartData['labels']) !!},
                datasets: [
                    // Comments
                    {
                        label: '{{ __('messages.forum_comments') ?? 'Forum Comments' }}',
                        data: {!! json_encode($communityChartData['comments']['forum']) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        fill: false,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 2
                    },
                    {
                        label: '{{ __('messages.store_comments') ?? 'Store Comments' }}',
                        data: {!! json_encode($communityChartData['comments']['store']) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.05)',
                        fill: false,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 2
                    },
                    {
                        label: '{{ __('messages.order_comments') ?? 'Order Comments' }}',
                        data: {!! json_encode($communityChartData['comments']['orders']) !!},
                        borderColor: '#f43f5e',
                        backgroundColor: 'rgba(244, 63, 94, 0.05)',
                        fill: false,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 2
                    },
                    // Reactions
                    {
                        label: '{{ __('messages.forum_reactions') ?? 'Forum Reactions' }}',
                        data: {!! json_encode($communityChartData['reactions']['forum']) !!},
                        borderColor: '#8b5cf6',
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 2
                    },
                    {
                        label: '{{ __('messages.store_reactions') ?? 'Store Reactions' }}',
                        data: {!! json_encode($communityChartData['reactions']['store']) !!},
                        borderColor: '#06b6d4',
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 2
                    },
                    {
                        label: '{{ __('messages.follows_count') ?? 'New Follows' }}',
                        data: {!! json_encode($communityChartData['reactions']['follows']) !!},
                        borderColor: '#f59e0b',
                        backgroundColor: 'rgba(245, 158, 11, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: textColor, font: { size: 12 } } },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor, font: { size: 10 } } },
                    y: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 10 } } }
                }
            }
        });
    }
});
</script>
@endpush
