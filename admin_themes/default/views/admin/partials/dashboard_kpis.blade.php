<!-- ═══════════════════ TOP KPI STATS ROW ═══════════════════ -->
<div class="row g-3 mb-4" id="kpis-row"
     data-users="{{ number_format($stats['users'] ?? 0) }}"
     data-posts="{{ number_format($stats['posts'] ?? 0) }}"
     data-online="{{ number_format($stats['users_online'] ?? 0) }}">
    <!-- Banners Card -->
    <div class="col-xl-3 col-sm-6">
        <div class="sd-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="sd-icon-box" style="background: linear-gradient(135deg, #615dfa, #8b5cf6);">
                    <i class="feather-image"></i>
                </div>
                <div>
                    <h3 class="sd-stat-num mb-0 text-dark">{{ number_format($stats['banners']['total'] ?? 0) }}</h3>
                    <span class="text-muted fw-medium fs-13">{{ __('messages.bannads') }}</span>
                </div>
            </div>
            <div class="d-flex gap-3 mt-3 pt-2 border-top">
                <span class="fs-12 text-muted"><i class="feather-eye me-1" style="color: #615dfa;"></i> {{ number_format($stats['banners']['views'] ?? 0) }}</span>
                <span class="fs-12 text-muted"><i class="feather-mouse-pointer me-1" style="color: #8b5cf6;"></i> {{ number_format($stats['banners']['clicks'] ?? 0) }}</span>
            </div>
        </div>
    </div>

    <!-- Text Ads Card -->
    <div class="col-xl-3 col-sm-6">
        <div class="sd-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="sd-icon-box" style="background: linear-gradient(135deg, #f59e0b, #f97316);">
                    <i class="feather-type"></i>
                </div>
                <div>
                    <h3 class="sd-stat-num mb-0 text-dark">{{ number_format($stats['links']['total'] ?? 0) }}</h3>
                    <span class="text-muted fw-medium fs-13">{{ __('messages.textads') }}</span>
                </div>
            </div>
            <div class="d-flex gap-3 mt-3 pt-2 border-top">
                <span class="fs-12 text-muted"><i class="feather-mouse-pointer me-1" style="color: #f59e0b;"></i> {{ number_format($stats['links']['clicks'] ?? 0) }}</span>
            </div>
        </div>
    </div>

    <!-- Visits Card -->
    <div class="col-xl-3 col-sm-6">
        <div class="sd-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="sd-icon-box" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="feather-repeat"></i>
                </div>
                <div>
                    <h3 class="sd-stat-num mb-0 text-dark">{{ number_format($stats['visits']['total'] ?? 0) }}</h3>
                    <span class="text-muted fw-medium fs-13">{{ __('messages.exvisit') }}</span>
                </div>
            </div>
            <div class="d-flex gap-3 mt-3 pt-2 border-top">
                <span class="fs-12 text-muted"><i class="feather-check-circle me-1" style="color: #10b981;"></i> {{ __('messages.safe_exchanges') ?? 'Safe Exchanges' }}</span>
            </div>
        </div>
    </div>

    <!-- Users Card -->
    <div class="col-xl-3 col-sm-6">
        <div class="sd-card p-3">
            <div class="d-flex align-items-center gap-3">
                <div class="sd-icon-box" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                    <i class="feather-users"></i>
                </div>
                <div>
                    <h3 class="sd-stat-num mb-0 text-dark">{{ number_format($stats['users'] ?? 0) }}</h3>
                    <span class="text-muted fw-medium fs-13">{{ __('messages.users') }}</span>
                </div>
            </div>
            <div class="d-flex gap-3 mt-3 pt-2 border-top">
                <span class="fs-12 text-success"><i class="feather-circle me-1"></i> {{ number_format($stats['users_online'] ?? 0) }} {{ __('messages.online') }}</span>
                <span class="fs-12 text-muted"><i class="feather-edit-3 me-1"></i> {{ number_format($stats['posts'] ?? 0) }} {{ __('messages.Posts') }}</span>
            </div>
        </div>
    </div>
</div>
