@extends('theme::layouts.admin')

@section('content')
    <!-- Dashboard Header -->
    <!-- Note: Page Header is already in the layout, so we might not need this or can customize it -->
    
    <!-- Update Alert -->
    @if($latestVersion && version_compare($latestVersion, $currentVersion, '>'))
    <div class="alert alert-warning d-flex align-items-center justify-content-between mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="feather-alert-triangle fs-3 me-3"></i>
            <div>
                <h4 class="alert-heading fs-16 fw-bold mb-1">{{ __('messages.update_available') ?? 'Update Available!' }}</h4>
                <p class="mb-0">{{ __('messages.version_available', ['latest' => $latestVersion, 'current' => $currentVersion]) ?? "Version $latestVersion is available. You are on $currentVersion." }}</p>
            </div>
        </div>
        <a href="{{ route('admin.updates') }}" class="btn btn-warning btn-sm text-dark fw-bold">
            {{ __('messages.update_now') ?? 'Update Now' }}
        </a>
    </div>
    @endif

    <!-- Main Stats Grid -->
    <div class="row">
        
        <!-- Banners -->
        <div class="col-xxl-3 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-lg bg-soft-primary text-primary border-soft-primary rounded">
                                <i class="feather-image"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark">{{ $stats['banners']['total'] }}</div>
                                <h3 class="fs-13 fw-semibold text-muted mb-0">{{ __('messages.bannads') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between text-muted">
                        <span class="fs-12"><i class="feather-eye me-1"></i> {{ number_format($stats['banners']['views']) }}</span>
                        <span class="fs-12"><i class="feather-mouse-pointer me-1"></i> {{ number_format($stats['banners']['clicks']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Text Ads -->
        <div class="col-xxl-3 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-lg bg-soft-warning text-warning border-soft-warning rounded">
                                <i class="feather-type"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark">{{ $stats['links']['total'] }}</div>
                                <h3 class="fs-13 fw-semibold text-muted mb-0">{{ __('messages.textads') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between text-muted">
                        <span class="fs-12"><i class="feather-mouse-pointer me-1"></i> {{ number_format($stats['links']['clicks']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visits -->
        <div class="col-xxl-3 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-lg bg-soft-success text-success border-soft-success rounded">
                                <i class="feather-repeat"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark">{{ $stats['visits']['total'] }}</div>
                                <h3 class="fs-13 fw-semibold text-muted mb-0">{{ __('messages.exvisit') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users -->
        <div class="col-xxl-3 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-lg bg-soft-info text-info border-soft-info rounded">
                                <i class="feather-users"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark">{{ $stats['users'] }}</div>
                                <h3 class="fs-13 fw-semibold text-muted mb-0">{{ __('messages.users') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between text-muted">
                        <span class="fs-12 text-success"><i class="feather-circle me-1"></i> {{ $stats['users_online'] }} {{ __('messages.online') ?? 'Online' }}</span>
                        <span class="fs-12"><i class="feather-edit me-1"></i> {{ $stats['posts'] }} {{ __('messages.Posts') ?? 'Posts' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity & Actions -->
    <div class="row">
        <!-- Activity Stats (Left Column) -->
        <div class="col-xxl-8">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title">{{ __('messages.activity_engagement') ?? 'Activity & Engagement' }}</h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Last Member -->
                        <div class="col-md-6 col-lg-3">
                            <div class="text-center">
                                <div class="avatar-image avatar-lg rounded-circle mb-3">
                                    <img src="{{ $stats['last_user'] ? $stats['last_user']->profile_photo_url : asset('themes/default/assets/images/avatar/1.png') }}" alt="" class="img-fluid">
                                </div>
                                <h6 class="mb-1">{{ __('messages.lastrm') }}</h6>
                                @if($stats['last_user'])
                                    <a href="{{ route('profile.show', $stats['last_user']->username) }}" class="fw-bold text-primary">{{ $stats['last_user']->username }}</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>

                        <!-- Last Post -->
                        <div class="col-md-6 col-lg-3">
                            <div class="text-center">
                                <div class="avatar-text avatar-lg bg-soft-warning text-warning rounded-circle mb-3">
                                    <i class="feather-clock"></i>
                                </div>
                                <h6 class="mb-1">{{ __('messages.lastps') }}</h6>
                                <p class="fw-bold text-dark mb-0">
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
                            <div class="text-center">
                                <div class="avatar-text avatar-lg bg-soft-danger text-danger rounded-circle mb-3">
                                    <i class="feather-thumbs-up"></i>
                                </div>
                                <h6 class="mb-1">{{ __('messages.allreactions') }}</h6>
                                <h4 class="fw-bold text-dark mb-0">{{ $stats['reactions']['total'] }}</h4>
                            </div>
                        </div>

                        <!-- Followers -->
                        <div class="col-md-6 col-lg-3">
                            <div class="text-center">
                                <div class="avatar-text avatar-lg bg-soft-info text-info rounded-circle mb-3">
                                    <i class="feather-user-plus"></i>
                                </div>
                                <h6 class="mb-1">{{ __('messages.allFollowers') }}</h6>
                                <h4 class="fw-bold text-dark mb-0">{{ $stats['followers'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column (Actions & Info) -->
        <div class="col-xxl-4">
            <!-- Quick Actions -->
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.reports') }}" class="btn btn-primary d-flex justify-content-between align-items-center">
                            <span>{{ __('messages.report') }}</span>
                            <span class="badge bg-white text-primary">{{ $stats['reports']['pending'] }}</span>
                        </a>
                        
                        <div class="btn-group w-100">
                            <a href="{{ route('admin.sitemap.generate') }}" class="btn btn-success">{{ __('messages.Sitemap') }}</a>
                            <a href="{{ url('/sitemap.xml') }}" target="_blank" class="btn btn-dark"><i class="feather-external-link"></i> XML</a>
                        </div>

                        <a href="https://github.com/mrghozzi/myads/wiki/changelogs" target="_blank" class="btn btn-warning text-dark">
                            {{ __('messages.Changelogs') }} <i class="feather-external-link ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Developer Info -->
            <div class="card stretch stretch-full">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-3">{{ __('messages.developed_by') ?? 'Developed by' }} : <a href="https://github.com/mrghozzi" target="_blank">MrGhozzi</a></h6>
                    <p class="mb-1">{{ __('messages.program_name') ?? 'Program name' }} : MYads</p>
                    <p class="mb-1">{{ __('messages.version') }} : v{{ $currentVersion }}</p>
                    <p class="mb-0 text-muted small">{{ __('messages.latest_version') ?? 'Latest version' }} : {{ $latestVersion ?? __('messages.checking') ?? 'Checking...' }}</p>
                </div>
            </div>

            <!-- Support -->
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title text-center w-100">{{ __('messages.support_project') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-around align-items-center">
                        <a href="https://www.patreon.com/MrGhozzi" target="_blank" class="btn btn-outline-dark btn-icon-text">
                            <i class="feather-heart me-2"></i> Patreon
                        </a>
                        <a href="https://ko-fi.com/mrghozzi" target="_blank" class="btn btn-outline-warning btn-icon-text">
                            <i class="feather-coffee me-2"></i> Ko-fi
                        </a>
                        <a href="https://www.ba9chich.com/en/mrghozzi" target="_blank" class="btn btn-outline-info btn-icon-text">
                            <i class="feather-gift me-2"></i> Ba9chich
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
