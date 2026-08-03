<div class="profile-videos-tab-wrapper">
    <!-- Superdesign Section Header -->
    <div class="profile-tab-header-card rounded-4 p-3 mb-4 shadow-sm bg-surface border d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary text-white rounded-pill px-3 py-2 fw-bold shadow-xs">
                <i class="fa-solid fa-play me-1"></i> {{ __('messages.Videos') }}
            </span>
            <h2 class="h6 mb-0 fw-extrabold text-gradient-primary">
                {{ __('messages.video_hub_title') }}
            </h2>
            <span class="badge bg-secondary-subtle text-body rounded-pill px-2 py-1 fs-8 fw-bold">
                {{ number_format($activities->total()) }}
            </span>
        </div>

        @if($isOwnProfile)
            <a href="{{ url('/share') }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-xs">
                <i class="fa-solid fa-cloud-arrow-up me-1"></i> {{ __('messages.create_video') }}
            </a>
        @endif
    </div>

    @if($activities->count() > 0)
        <div id="infinite-scroll-container">
            <div id="timeline-content" class="row g-3 row-cols-1 row-cols-sm-2 mb-4">
                @include('theme::profile.partials.videos_grid_items', ['activities' => $activities])
            </div>

            @if($activities->hasPages())
                @include('theme::partials.ajax.infinite_scroll', ['paginator' => $activities])
            @endif
        </div>
    @else
        <div class="profile-empty-state text-center py-5 px-3 rounded-4 shadow-sm bg-surface border">
            <div class="yt-empty-icon mb-3 text-primary display-4">
                <i class="fa-solid fa-film"></i>
            </div>
            <h4 class="fw-bold mb-2">{{ __('messages.no_videos_found') }}</h4>
            <p class="text-muted mb-4 max-w-md mx-auto small">{{ __('messages.no_videos_desc') }}</p>
            @if($isOwnProfile)
                <a href="{{ url('/share') }}" class="btn btn-primary px-4 py-2 rounded-pill fw-bold">
                    <i class="fa-solid fa-cloud-arrow-up me-2"></i> {{ __('messages.create_video') }}
                </a>
            @endif
        </div>
    @endif
</div>
