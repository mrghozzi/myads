<div class="profile-clips-tab-wrapper">
    <!-- Superdesign Section Header -->
    <div class="profile-tab-header-card rounded-4 p-3 mb-4 shadow-sm bg-surface border d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-danger text-white rounded-pill px-3 py-2 fw-bold shadow-xs">
                <i class="fa-solid fa-bolt me-1"></i> {{ __('messages.Clips') }}
            </span>
            <h2 class="h6 mb-0 fw-extrabold text-gradient-danger">
                {{ __('messages.shorts_clips') }}
            </h2>
            <span class="badge bg-secondary-subtle text-body rounded-pill px-2 py-1 fs-8 fw-bold">
                {{ number_format($activities->total()) }}
            </span>
        </div>

        @if($isOwnProfile)
            <a href="{{ route('clips.index') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold shadow-xs">
                <i class="fa-solid fa-bolt me-1"></i> {{ __('messages.create_clip') }}
            </a>
        @endif
    </div>

    @if($activities->count() > 0)
        <div id="infinite-scroll-container">
            <div id="timeline-content" class="row g-3 row-cols-2 row-cols-sm-3 mb-4">
                @include('theme::profile.partials.clips_grid_items', ['activities' => $activities])
            </div>

            @if($activities->hasPages())
                @include('theme::partials.ajax.infinite_scroll', ['paginator' => $activities])
            @endif
        </div>
    @else
        <div class="profile-empty-state text-center py-5 px-3 rounded-4 shadow-sm bg-surface border">
            <div class="yt-empty-icon mb-3 text-danger display-4">
                <i class="fa-solid fa-bolt"></i>
            </div>
            <h4 class="fw-bold mb-2">{{ __('messages.no_clips_found') ?? 'لا توجد مقاطع قصيرة حتى الآن' }}</h4>
            <p class="text-muted mb-4 max-w-md mx-auto small">{{ __('messages.no_clips_desc') ?? 'لم يتم نشر أي مقاطع فيديو قصيرة في هذا الملف الشخصي بعد.' }}</p>
            @if($isOwnProfile)
                <a href="{{ route('clips.index') }}" class="btn btn-danger px-4 py-2 rounded-pill fw-bold">
                    <i class="fa-solid fa-bolt me-2"></i> {{ __('messages.create_clip') }}
                </a>
            @endif
        </div>
    @endif
</div>
