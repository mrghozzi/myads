@extends('admin::layouts.admin')

@section('title', __('messages.performance_settings') ?? 'Performance Settings')

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.performance_settings') ?? 'Performance Settings' }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.settings') ?? 'Settings' }}</div>
            <h1 class="admin-hero__title">{{ __('messages.performance_settings') ?? 'Performance Settings' }}</h1>
            <p class="admin-hero__copy">{{ __('messages.performance_settings_desc') ?? 'Fine-tune the community feed algorithms and system variables to reduce server CPU consumption.' }}</p>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <section class="admin-panel mt-4">
        <div class="admin-panel__body">
            <div class="alert alert-info py-2 px-3 mb-4">
                <strong><i class="feather-info me-2"></i>{{ __('messages.note') ?? 'Note' }}:</strong> {{ __('messages.performance_settings_note') ?? 'These settings control the Community Feed Ranking Engine. Lowering the limits and increasing the cache TTL will significantly reduce Database Queries and CPU usage.' }}
            </div>

            <form action="{{ route('admin.settings.performance.update') }}" method="POST" class="row g-4">
                @csrf
                <div class="col-12"><h3 class="h5 mb-0 text-primary"><i class="feather-zap me-2"></i>{{ __('messages.cache_and_generation') ?? 'Cache & Generation' }}</h3></div>
                <div class="col-lg-6">
                    <label class="form-label fw-bold">{{ __('messages.feed_cache_ttl') ?? 'Feed Cache TTL (Seconds)' }}</label>
                    <input type="number" name="cache_ttl_seconds" class="form-control" value="{{ $settings['cache_ttl_seconds'] ?? 300 }}">
                    <div class="small text-muted mt-1">{{ __('messages.feed_cache_ttl_desc') ?? 'How long the generated feed is cached. Default 300. Increase to 900 for high-traffic sites.' }}</div>
                </div>
                
                <div class="col-12"><hr class="my-0"></div>
                <div class="col-12"><h3 class="h5 mb-0 text-primary"><i class="feather-filter me-2"></i>{{ __('messages.candidate_limits') ?? 'Candidate Limits (Query Size)' }}</h3></div>
                <div class="col-lg-6">
                    <label class="form-label fw-bold">{{ __('messages.fresh_candidate_limit') ?? 'Fresh Candidate Limit' }}</label>
                    <input type="number" name="fresh_candidate_limit" class="form-control" value="{{ $settings['fresh_candidate_limit'] ?? 350 }}">
                    <div class="small text-muted mt-1">{{ __('messages.fresh_candidate_limit_desc') ?? 'Maximum recent posts fetched to evaluate for the timeline. Lowering this saves RAM & CPU. Default 350.' }}</div>
                </div>
                <div class="col-lg-6">
                    <label class="form-label fw-bold">{{ __('messages.rescue_candidate_limit') ?? 'Rescue Candidate Limit' }}</label>
                    <input type="number" name="rescue_candidate_limit" class="form-control" value="{{ $settings['rescue_candidate_limit'] ?? 50 }}">
                    <div class="small text-muted mt-1">{{ __('messages.rescue_candidate_limit_desc') ?? 'Maximum older popular posts fetched to blend into empty feeds. Default 50.' }}</div>
                </div>

                <div class="col-12"><hr class="my-0"></div>
                <div class="col-12"><h3 class="h5 mb-0 text-primary"><i class="feather-clock me-2"></i>{{ __('messages.time_windows') ?? 'Time Windows' }}</h3></div>
                <div class="col-lg-4">
                    <label class="form-label fw-bold">{{ __('messages.fresh_window_hours') ?? 'Fresh Window (Hours)' }}</label>
                    <input type="number" name="fresh_candidate_hours" class="form-control" value="{{ $settings['fresh_candidate_hours'] ?? 48 }}">
                    <div class="small text-muted mt-1">{{ __('messages.fresh_window_desc') ?? 'How far back to look for normal posts. Default 48.' }}</div>
                </div>
                <div class="col-lg-4">
                    <label class="form-label fw-bold">{{ __('messages.trend_window_hours') ?? 'Trend Window (Hours)' }}</label>
                    <input type="number" name="trend_window_hours" class="form-control" value="{{ $settings['trend_window_hours'] ?? 24 }}">
                    <div class="small text-muted mt-1">{{ __('messages.trend_window_desc') ?? 'Timeframe for calculating trending score. Default 24.' }}</div>
                </div>
                <div class="col-lg-4">
                    <label class="form-label fw-bold">{{ __('messages.rapid_window_hours') ?? 'Rapid Window (Hours)' }}</label>
                    <input type="number" name="rapid_window_hours" class="form-control" value="{{ $settings['rapid_window_hours'] ?? 6 }}">
                    <div class="small text-muted mt-1">{{ __('messages.rapid_window_desc') ?? 'Timeframe for viral acceleration. Default 6.' }}</div>
                </div>

                <div class="col-12"><hr class="my-0"></div>
                <div class="col-12"><h3 class="h5 mb-0 text-primary"><i class="feather-life-buoy me-2"></i>{{ __('messages.rescue_rules') ?? 'Rescue Rules (For inactive feeds)' }}</h3></div>
                <div class="col-lg-6">
                    <label class="form-label fw-bold">{{ __('messages.rescue_max_age_hours') ?? 'Rescue Max Age (Hours)' }}</label>
                    <input type="number" name="rescue_max_age_hours" class="form-control" value="{{ $settings['rescue_max_age_hours'] ?? 336 }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label fw-bold">{{ __('messages.rescue_min_reactions') ?? 'Min Reactions for Rescue' }}</label>
                    <input type="number" name="rescue_min_recent_reactions" class="form-control" value="{{ $settings['rescue_min_recent_reactions'] ?? 10 }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label fw-bold">{{ __('messages.rescue_min_comments') ?? 'Min Comments for Rescue' }}</label>
                    <input type="number" name="rescue_min_recent_comments" class="form-control" value="{{ $settings['rescue_min_recent_comments'] ?? 5 }}">
                </div>
                <div class="col-lg-6">
                    <label class="form-label fw-bold">{{ __('messages.rescue_min_reposts') ?? 'Min Reposts for Rescue' }}</label>
                    <input type="number" name="rescue_min_recent_reposts" class="form-control" value="{{ $settings['rescue_min_recent_reposts'] ?? 2 }}">
                </div>
                
                <div class="col-12 d-flex justify-content-end mt-5">
                    <button type="submit" class="btn btn-primary"><i class="feather-save me-2"></i>{{ __('messages.save') ?? 'Save Changes' }}</button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
