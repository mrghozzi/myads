<div class="sd-card h-100 p-4" id="dashboard-activity-card" data-pending-reports="{{ $pending_reports ?? 0 }}">
    <h6 class="fw-bold text-dark mb-4"><i class="feather-activity me-2" style="color: #10b981;"></i> {{ __('messages.activity_engagement') ?? 'Activity & Engagement' }}</h6>
    
    <div class="row g-3">
        <!-- Last Member -->
        <div class="col-md-6 col-lg-3">
            <div class="text-center p-3 rounded-3" style="background: rgba(97, 93, 250, 0.05); border: 1px solid rgba(97, 93, 250, 0.1);">
                <div class="mb-2 mx-auto overflow-hidden" style="width: 54px; height: 54px; border-radius: 50%; border: 3px solid rgba(97,93,250,0.2);">
                    <img src="{{ $last_user && $last_user->img ? asset($last_user->img) : asset('themes/default/assets/admin-duralux/images/avatar/undefined.png') }}" alt="" class="img-fluid" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <h6 class="mb-1 fw-semibold text-muted" style="font-size: 0.75rem;">{{ __('messages.lastrm') }}</h6>
                @if($last_user)
                    <a href="{{ route('profile.show', $last_user->username) }}" class="fw-bold" style="color: #615dfa; font-size: 0.85rem;">{{ $last_user->username }}</a>
                @else
                    <span class="text-muted">-</span>
                @endif
            </div>
        </div>

        <!-- Last Post -->
        <div class="col-md-6 col-lg-3">
            <div class="text-center p-3 rounded-3" style="background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.1);">
                <div class="mb-2 d-flex align-items-center justify-content-center mx-auto" style="width: 54px; height: 54px; border-radius: 50%; background: rgba(245,158,11,0.15);">
                    <i class="feather-clock" style="color: #f59e0b; font-size: 20px;"></i>
                </div>
                <h6 class="mb-1 fw-semibold text-muted" style="font-size: 0.75rem;">{{ __('messages.lastps') }}</h6>
                <p class="fw-bold text-dark mb-0" style="font-size: 0.85rem;">
                    {{ $last_post ? $last_post->date_formatted : '-' }}
                </p>
            </div>
        </div>

        <!-- Reactions -->
        <div class="col-md-6 col-lg-3">
            <div class="text-center p-3 rounded-3" style="background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.1);">
                <div class="mb-2 d-flex align-items-center justify-content-center mx-auto" style="width: 54px; height: 54px; border-radius: 50%; background: rgba(239,68,68,0.15);">
                    <i class="feather-thumbs-up" style="color: #ef4444; font-size: 20px;"></i>
                </div>
                <h6 class="mb-1 fw-semibold text-muted" style="font-size: 0.75rem;">{{ __('messages.allreactions') }}</h6>
                <h4 class="fw-bold text-dark mb-0" style="font-size: 1.25rem;">{{ number_format($total_reactions ?? 0) }}</h4>
            </div>
        </div>

        <!-- Followers -->
        <div class="col-md-6 col-lg-3">
            <div class="text-center p-3 rounded-3" style="background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.1);">
                <div class="mb-2 d-flex align-items-center justify-content-center mx-auto" style="width: 54px; height: 54px; border-radius: 50%; background: rgba(59,130,246,0.15);">
                    <i class="feather-user-plus" style="color: #3b82f6; font-size: 20px;"></i>
                </div>
                <h6 class="mb-1 fw-semibold text-muted" style="font-size: 0.75rem;">{{ __('messages.allFollowers') }}</h6>
                <h4 class="fw-bold text-dark mb-0" style="font-size: 1.25rem;">{{ number_format($followers ?? 0) }}</h4>
            </div>
        </div>
    </div>

    <!-- Secondary Sub-Counters -->
    <div class="row g-3 mt-2">
        <div class="col-md-4">
            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(97,93,250,0.04); border: 1px solid rgba(97,93,250,0.08);">
                <i class="feather-message-circle" style="color: #615dfa; font-size: 18px;"></i>
                <div>
                    <div class="fw-bold text-dark">{{ number_format($topics ?? 0) }}</div>
                    <span class="text-muted" style="font-size: 0.75rem;">{{ __('messages.topics') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(16,185,129,0.04); border: 1px solid rgba(16,185,129,0.08);">
                <i class="feather-globe" style="color: #10b981; font-size: 18px;"></i>
                <div>
                    <div class="fw-bold text-dark">{{ number_format($listings ?? 0) }}</div>
                    <span class="text-muted" style="font-size: 0.75rem;">{{ __('messages.listings') }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: rgba(245,158,11,0.04); border: 1px solid rgba(245,158,11,0.08);">
                <i class="feather-shopping-bag" style="color: #f59e0b; font-size: 18px;"></i>
                <div>
                    <div class="fw-bold text-dark">{{ number_format($products ?? 0) }}</div>
                    <span class="text-muted" style="font-size: 0.75rem;">{{ __('messages.products') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Posts Breakdown -->
    <div class="mt-4 pt-3 border-top">
        <h6 class="fw-bold text-dark mb-3" style="font-size: 0.85rem;">{{ __('messages.posts_breakdown_by_type') ?? 'Posts Breakdown by Type' }} ({{ number_format($posts_total ?? 0) }})</h6>
        <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="feather-align-left text-muted me-1"></i> {{ __('messages.post_text') ?? 'Text' }}: {{ number_format($posts_breakdown['text'] ?? 0) }}</span>
            <span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="feather-link text-primary me-1"></i> {{ __('messages.post_link') ?? 'Link' }}: {{ number_format($posts_breakdown['link'] ?? 0) }}</span>
            <span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="feather-image text-success me-1"></i> {{ __('messages.post_gallery') ?? 'Gallery' }}: {{ number_format($posts_breakdown['gallery'] ?? 0) }}</span>
            <span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="feather-video text-danger me-1"></i> {{ __('messages.post_video') ?? 'Video' }}: {{ number_format($posts_breakdown['video'] ?? 0) }}</span>
            <span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="feather-film text-warning me-1"></i> {{ __('messages.post_clip') ?? 'Clip' }}: {{ number_format($posts_breakdown['clip'] ?? 0) }}</span>
            <span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="feather-mic text-info me-1"></i> {{ __('messages.post_audio') ?? 'Audio' }}: {{ number_format($posts_breakdown['audio'] ?? 0) }}</span>
            <span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="feather-file text-dark me-1"></i> {{ __('messages.post_file') ?? 'File' }}: {{ number_format($posts_breakdown['file'] ?? 0) }}</span>
            <span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="feather-music text-primary me-1"></i> {{ __('messages.post_music') ?? 'Music' }}: {{ number_format($posts_breakdown['music'] ?? 0) }}</span>
            <span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="feather-repeat text-info me-1"></i> {{ __('messages.post_repost') ?? 'Repost' }}: {{ number_format($posts_breakdown['repost'] ?? 0) }}</span>
            <span class="badge bg-light text-dark border px-2.5 py-1.5"><i class="feather-book-open text-success me-1"></i> {{ __('messages.knowledgebase') ?? 'Knowledgebase' }}: {{ number_format($posts_breakdown['knowledgebase'] ?? 0) }}</span>
        </div>
    </div>
</div>
