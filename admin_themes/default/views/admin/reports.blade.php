@extends('admin::layouts.admin')

@section('title', __('messages.reports'))
@section('admin_shell_header_mode', 'hidden')

@php
    $reportItems = $reportItems ?? collect();
    $reportStats = $reportStats ?? ['total' => $reports->total() ?? 0, 'pending' => 0, 'reviewed' => 0, 'actioned' => 0, 'warnings' => 0];
    $currentStatus = request('status', 'all');
    $currentCategory = request('category', 'all');
@endphp

@section('content')
@include('admin::admin.partials.extension_hub_styles')
@include('admin::admin.partials.reports_hub_styles')

<div class="main-content container-lg px-4">
    <section class="extension-hub extension-hub--reports">
        <div class="row g-0 align-items-center mb-4">
            <div class="col-12">
                <div class="extension-hub__hero">
                    <span class="extension-hub__hero-icon">
                        <i class="fa-solid fa-flag"></i>
                    </span>

                    <div class="row align-items-center g-4 position-relative">
                        <div class="col-xl-7">
                            <span class="extension-hub__hero-kicker">
                                <i class="feather-shield"></i>
                                {{ __('messages.reports') }}
                            </span>
                            <h1 class="extension-hub__hero-title mt-4">{{ __('messages.reports') }}</h1>
                            <p class="extension-hub__hero-desc">{{ __('messages.reports_desc') }}</p>
                        </div>
                        <div class="col-xl-5 text-xl-end">
                            <div class="extension-hub__hero-panel reports-hub__hero-panel">
                                <span class="extension-hub__hero-panel-icon">
                                    <i class="feather-alert-octagon"></i>
                                </span>
                                <div>
                                    <span class="extension-hub__hero-panel-label">{{ __('messages.pending') }}</span>
                                    <span class="extension-hub__hero-panel-value">
                                        {{ $reportStats['pending'] }} {{ __('messages.reports') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5-Metric Executive KPI Strip -->
        <div class="row g-3 extension-hub__stats mb-4">
            <div class="col-sm-6 col-xl">
                <div class="extension-hub__stat">
                    <div class="extension-hub__stat-label">
                        <span class="extension-hub__stat-icon"><i class="feather-layers"></i></span>
                        {{ __('messages.total') }}
                    </div>
                    <div class="extension-hub__stat-value">{{ $reportStats['total'] }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl">
                <div class="extension-hub__stat">
                    <div class="extension-hub__stat-label">
                        <span class="extension-hub__stat-icon text-warning"><i class="feather-clock"></i></span>
                        {{ __('messages.pending') }}
                    </div>
                    <div class="extension-hub__stat-value text-warning">{{ $reportStats['pending'] }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl">
                <div class="extension-hub__stat">
                    <div class="extension-hub__stat-label">
                        <span class="extension-hub__stat-icon text-success"><i class="feather-check-circle"></i></span>
                        {{ __('messages.reviewed') }}
                    </div>
                    <div class="extension-hub__stat-value text-success">{{ $reportStats['reviewed'] }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl">
                <div class="extension-hub__stat">
                    <div class="extension-hub__stat-label">
                        <span class="extension-hub__stat-icon text-primary"><i class="feather-shield"></i></span>
                        {{ __('messages.actioned') }}
                    </div>
                    <div class="extension-hub__stat-value text-primary">{{ $reportStats['actioned'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl">
                <div class="extension-hub__stat">
                    <div class="extension-hub__stat-label">
                        <span class="extension-hub__stat-icon text-danger"><i class="feather-alert-triangle"></i></span>
                        {{ __('messages.warnings') }}
                    </div>
                    <div class="extension-hub__stat-value text-danger">{{ $reportStats['warnings'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="extension-hub__surface p-4 p-xl-5">
            <!-- Header & Filter Bar -->
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
                <div>
                    <h2 class="extension-hub__section-title">{{ __('messages.reports_list') }}</h2>
                    <p class="extension-hub__section-subtitle">{{ __('messages.reports_desc') }}</p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="extension-hub__count-pill">
                        <i class="feather-flag"></i>
                        {{ $reports->total() }} {{ __('messages.reports') }}
                    </span>
                </div>
            </div>

            <!-- Filter Tabs & Category Filter -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                <div class="btn-group" role="group" aria-label="Status filters">
                    <a href="{{ route('admin.reports', array_merge(request()->query(), ['status' => 'all'])) }}" class="btn btn-sm {{ $currentStatus === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        {{ __('messages.all') }}
                    </a>
                    <a href="{{ route('admin.reports', array_merge(request()->query(), ['status' => 'pending'])) }}" class="btn btn-sm {{ $currentStatus === 'pending' ? 'btn-warning' : 'btn-outline-secondary' }}">
                        {{ __('messages.pending') }} ({{ $reportStats['pending'] }})
                    </a>
                    <a href="{{ route('admin.reports', array_merge(request()->query(), ['status' => 'reviewed'])) }}" class="btn btn-sm {{ $currentStatus === 'reviewed' ? 'btn-success' : 'btn-outline-secondary' }}">
                        {{ __('messages.reviewed') }} ({{ $reportStats['reviewed'] }})
                    </a>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <label for="categoryFilter" class="form-label mb-0 small text-muted"><i class="feather-filter"></i> {{ __('messages.filter_by_category') }}:</label>
                    <select id="categoryFilter" class="form-select form-select-sm" style="width: auto; min-width: 170px;" onchange="window.location.href = this.value;">
                        <option value="{{ route('admin.reports', array_merge(request()->query(), ['category' => 'all'])) }}" {{ $currentCategory === 'all' ? 'selected' : '' }}>
                            {{ __('messages.all_categories') }}
                        </option>
                        @foreach(\App\Models\Report::CATEGORIES as $cat)
                            <option value="{{ route('admin.reports', array_merge(request()->query(), ['category' => $cat])) }}" {{ $currentCategory === $cat ? 'selected' : '' }}>
                                {{ __('messages.report_category_' . $cat) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($reportItems->isEmpty())
                <div class="extension-hub__empty">
                    <div class="extension-hub__empty-icon">
                        <i class="feather-shield-off"></i>
                    </div>
                    <h3 class="extension-hub__section-title mb-2">{{ __('messages.no_data') }}</h3>
                    <p class="extension-hub__section-subtitle reports-hub__empty-copy">{{ __('messages.reports_desc') }}</p>
                </div>
            @else
                <div class="reports-hub__list">
                    @foreach($reportItems as $item)
                        @php
                            $reporter = $item['reporter'];
                            $targetUser = $item['target_user'];
                            $reporterInitial = $reporter && $reporter->username ? \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($reporter->username, 0, 1)) : 'G';
                            $category = $item['category'] ?? 'other';
                            $actionTaken = $item['action_taken'] ?? 'none';
                        @endphp

                        <article class="extension-hub__list-card reports-hub__card {{ $item['is_pending'] ? 'reports-hub__card--pending' : '' }}">
                            <div class="d-flex flex-column flex-xl-row justify-content-between gap-4">
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                                        <span class="extension-hub__status-badge {{ $item['is_pending'] ? 'extension-hub__update-badge' : 'extension-hub__status-badge extension-hub__status-badge--inactive' }}">
                                            <i class="{{ $item['is_pending'] ? 'feather-clock' : 'feather-check-circle' }}"></i>
                                            {{ $item['status_label'] }}
                                        </span>
                                        <span class="reports-hub__reference">#{{ $item['id'] }}</span>
                                        @if($item['target_label'])
                                            <span class="reports-hub__type-pill">
                                                <i class="{{ $item['target_icon'] }}"></i>
                                                {{ $item['target_label'] }}
                                            </span>
                                        @endif

                                        <!-- Violation Category Badge -->
                                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="font-size: 11px;">
                                            <i class="feather-tag"></i> {{ $item['category_label'] }}
                                        </span>

                                        <!-- Action Taken Badge -->
                                        @if($actionTaken !== 'none')
                                            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: 11px;">
                                                <i class="feather-shield"></i> {{ $item['action_taken_label'] }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-xl-4">
                                            <div class="reports-hub__meta-card">
                                                <div class="reports-hub__label">
                                                    <i class="feather-user"></i>
                                                    {{ __('messages.reported_by') }}
                                                </div>

                                                <div class="reports-hub__person">
                                                    @if($reporter && $reporter->img)
                                                        <img src="{{ $reporter->avatarUrl() }}" alt="{{ $reporter->username }}" class="reports-hub__person-avatar" style="object-fit: cover; border: none; padding: 0;">
                                                    @else
                                                        <span class="reports-hub__person-avatar">{{ $reporterInitial }}</span>
                                                    @endif
                                                    <div class="min-w-0">
                                                        @if($reporter)
                                                            <p class="reports-hub__person-name">{{ $reporter->username }}</p>
                                                        @else
                                                            <p class="reports-hub__person-name">{{ __('messages.guest') }}</p>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($reporter)
                                                    <div class="reports-hub__actions mt-3">
                                                        <a href="{{ $item['reporter_profile_url'] }}" target="_blank" class="btn-extension-glass btn-extension-glass--muted">
                                                            <i class="feather-user"></i>
                                                            <span>{{ __('messages.view_profile') }}</span>
                                                        </a>
                                                        <a href="{{ $item['reporter_message_url'] }}" class="btn-extension-glass btn-extension-glass--primary">
                                                            <i class="feather-mail"></i>
                                                            <span>{{ __('messages.message') }}</span>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-xl-8">
                                            <div class="reports-hub__reason-card">
                                                <div class="reports-hub__label">
                                                    <i class="feather-alert-circle"></i>
                                                    {{ __('messages.reason') }}
                                                </div>
                                                <p class="reports-hub__reason">{{ $item['reason'] }}</p>

                                                @if(!empty($item['action_notes']))
                                                    <div class="alert alert-secondary py-2 px-3 my-2" style="font-size: 12px; background: rgba(130, 140, 170, 0.08); border: 1px dashed rgba(130, 140, 170, 0.25);">
                                                        <strong><i class="feather-info"></i> {{ __('messages.moderation_notes') }}:</strong>
                                                        {{ $item['action_notes'] }}
                                                        @if(!empty($item['moderator']))
                                                            <span class="text-muted d-block mt-1">— {{ __('messages.moderated_by') }} {{ $item['moderator']->username }} ({{ $item['resolved_at'] }})</span>
                                                        @endif
                                                    </div>
                                                @endif

                                                <div class="reports-hub__label mt-4">
                                                    <i class="{{ $item['target_icon'] }}"></i>
                                                    {{ __('messages.report_content') }}
                                                </div>

                                                @if($item['target_missing'])
                                                    <div class="reports-hub__removed">
                                                        {{ __('messages.reported_content_removed') }}
                                                    </div>
                                                @else
                                                    @if($item['target_title'])
                                                        <h3 class="extension-hub__section-title mb-3">{{ $item['target_title'] }}</h3>
                                                    @endif

                                                    <div class="reports-hub__actions">
                                                        @if($item['preview_url'])
                                                            <a href="{{ $item['preview_url'] }}" target="_blank" class="btn-extension-glass btn-extension-glass--warning">
                                                                <i class="feather-external-link"></i>
                                                                <span>{{ $item['preview_label'] }}</span>
                                                            </a>
                                                        @endif

                                                        @if($targetUser)
                                                            @if($item['target_user_profile_url'] !== $item['preview_url'])
                                                                <a href="{{ $item['target_user_profile_url'] }}" target="_blank" class="btn-extension-glass btn-extension-glass--muted">
                                                                    <i class="feather-user"></i>
                                                                    <span>{{ __('messages.view_profile') }}</span>
                                                                </a>
                                                            @endif

                                                            <a href="{{ $item['target_user_message_url'] }}" class="btn-extension-glass btn-extension-glass--primary">
                                                                <i class="feather-mail"></i>
                                                                <span>{{ __('messages.message') }}</span>
                                                            </a>

                                                            <a href="{{ $item['target_user_admin_url'] }}" class="btn-extension-glass btn-extension-glass--success">
                                                                <i class="feather-edit-2"></i>
                                                                <span>{{ __('messages.edit') }}</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="reports-hub__action-col">
                                    <!-- Executive Moderation Suite Action Trigger -->
                                    <button type="button" class="btn-extension-glass btn-extension-glass--primary" onclick="openModerationModal({{ $item['id'] }}, '{{ addslashes($item['target_title'] ?? '') }}', '{{ $targetUser ? addslashes($targetUser->username) : '' }}', '{{ $category }}')" title="{{ __('messages.take_moderation_action') }}">
                                        <i class="feather-shield"></i>
                                        <span>{{ __('messages.take_moderation_action') }}</span>
                                    </button>

                                    @if($item['is_pending'])
                                        <a href="{{ route('admin.reports', ['wtid' => $item['id']]) }}" class="btn-extension-glass btn-extension-glass--warning" title="{{ __('messages.review') }}">
                                            <i class="feather-eye"></i>
                                            <span>{{ __('messages.review') }}</span>
                                        </a>
                                    @endif

                                    <form action="{{ route('admin.reports.delete', $item['id']) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm_delete_report') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-extension-glass btn-extension-glass--danger">
                                            <i class="feather-trash-2"></i>
                                            <span>{{ __('messages.delete') }}</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            @if($reports->hasPages())
                <div class="card-footer bg-transparent border-0 px-0 pt-4 pb-0">
                    {{ $reports->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@section('modals')
<!-- Executive Moderation Action Modal -->
<div class="modal fade" id="moderationActionModal" tabindex="-1" aria-labelledby="moderationActionModalLabel" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-bottom px-4 py-3" style="background: linear-gradient(135deg, rgba(35, 210, 226, 0.08) 0%, rgba(56, 116, 255, 0.08) 100%);">
                <h5 class="modal-title d-flex align-items-center gap-2 fw-bold" id="moderationActionModalLabel">
                    <i class="feather-shield text-primary"></i>
                    {{ __('messages.moderation_actions') }}
                    <span class="badge bg-primary-subtle text-primary border" id="modalReportRef"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="moderationActionForm" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <!-- Report Target Summary -->
                    <div class="p-3 mb-4 rounded-3 d-flex flex-wrap align-items-center justify-content-between gap-3" style="background: rgba(130, 140, 170, 0.07); border: 1px solid rgba(130, 140, 170, 0.15);">
                        <div>
                            <small class="text-muted d-block">{{ __('messages.target') }}:</small>
                            <strong id="modalTargetTitle">-</strong>
                        </div>
                        <div>
                            <small class="text-muted d-block">{{ __('messages.user') }}:</small>
                            <span class="badge bg-secondary-subtle text-secondary" id="modalTargetUser">-</span>
                        </div>
                        <div>
                            <small class="text-muted d-block">{{ __('messages.report_category') }}:</small>
                            <span class="badge bg-danger-subtle text-danger" id="modalCategory">-</span>
                        </div>
                    </div>

                    <!-- Action Selection Radios -->
                    <div class="mb-4">
                        <label class="form-label fw-bold mb-2">{{ __('messages.action_taken') }}:</label>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="d-flex align-items-start p-3 rounded border h-100" style="cursor: pointer;">
                                    <input type="radio" name="action" value="dismiss" checked class="form-check-input me-3 mt-1" onchange="toggleModerationFields(this.value)">
                                    <div>
                                        <strong>{{ __('messages.moderation_action_dismiss') }}</strong>
                                        <small class="text-muted d-block">{{ __('messages.report_dismissed_unfounded') }}</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="d-flex align-items-start p-3 rounded border h-100" style="cursor: pointer;">
                                    <input type="radio" name="action" value="hide_content" class="form-check-input me-3 mt-1" onchange="toggleModerationFields(this.value)">
                                    <div>
                                        <strong class="text-warning">{{ __('messages.moderation_action_hide') }}</strong>
                                        <small class="text-muted d-block">{{ __('messages.content_hidden_community_standards') }}</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="d-flex align-items-start p-3 rounded border h-100" style="cursor: pointer;">
                                    <input type="radio" name="action" value="delete_content" class="form-check-input me-3 mt-1" onchange="toggleModerationFields(this.value)">
                                    <div>
                                        <strong class="text-danger">{{ __('messages.moderation_action_delete') }}</strong>
                                        <small class="text-muted d-block">{{ __('messages.content_deleted_community_standards') }}</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="d-flex align-items-start p-3 rounded border h-100" style="cursor: pointer;">
                                    <input type="radio" name="action" value="warn_user" class="form-check-input me-3 mt-1" onchange="toggleModerationFields(this.value)">
                                    <div>
                                        <strong class="text-warning">{{ __('messages.moderation_action_warn') }}</strong>
                                        <small class="text-muted d-block">{{ __('messages.official_moderation_warning') }}</small>
                                    </div>
                                </label>
                            </div>
                            <div class="col-12">
                                <label class="d-flex align-items-start p-3 rounded border" style="cursor: pointer;">
                                    <input type="radio" name="action" value="ban_user" class="form-check-input me-3 mt-1" onchange="toggleModerationFields(this.value)">
                                    <div>
                                        <strong class="text-danger">{{ __('messages.moderation_action_ban') }}</strong>
                                        <small class="text-muted d-block">{{ __('messages.repeated_community_violations') }}</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Warning Reason Group -->
                    <div class="mb-3 d-none" id="warningReasonGroup">
                        <label class="form-label fw-bold">{{ __('messages.moderation_warning_reason') }}:</label>
                        <input type="text" name="warning_reason" class="form-control" placeholder="{{ __('messages.violates_community_standards') }}">
                    </div>

                    <!-- Deduct Points Group -->
                    <div class="mb-3 d-none" id="deductPtsGroup">
                        <label class="form-label fw-bold">{{ __('messages.moderation_deduct_pts') }}:</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="feather-award"></i> PTS</span>
                            <input type="number" name="deduct_points" class="form-control" min="0" max="10000" value="0">
                        </div>
                    </div>

                    <!-- Admin Notes / Reason -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('messages.moderation_notes') }}:</label>
                        <textarea name="notes" rows="3" class="form-control" placeholder="{{ __('messages.notes') }}..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top px-4 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                        <i class="feather-check-circle me-1"></i> {{ __('messages.confirm') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModerationModal(reportId, targetTitle, targetUser, category) {
        const modalEl = document.getElementById('moderationActionModal');
        if (!modalEl) return;

        // Ensure modal is appended to body so it never inherits nxl-container blur or stacking filters
        if (modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }

        const form = document.getElementById('moderationActionForm');
        form.action = '{{ url('/admin/reports') }}/' + reportId + '/action';
        document.getElementById('modalReportRef').textContent = '#' + reportId;
        document.getElementById('modalTargetTitle').textContent = targetTitle || '-';
        document.getElementById('modalTargetUser').textContent = targetUser || '-';
        document.getElementById('modalCategory').textContent = category || 'other';

        // Reset radio and fields
        const defaultRadio = document.querySelector('input[name="action"][value="dismiss"]');
        if (defaultRadio) defaultRadio.checked = true;
        toggleModerationFields('dismiss');

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.show();
        }
    }

    function toggleModerationFields(action) {
        const warnGroup = document.getElementById('warningReasonGroup');
        const deductGroup = document.getElementById('deductPtsGroup');

        if (action === 'warn_user') {
            if (warnGroup) warnGroup.classList.remove('d-none');
            if (deductGroup) deductGroup.classList.remove('d-none');
        } else if (action === 'delete_content') {
            if (warnGroup) warnGroup.classList.add('d-none');
            if (deductGroup) deductGroup.classList.remove('d-none');
        } else {
            if (warnGroup) warnGroup.classList.add('d-none');
            if (deductGroup) deductGroup.classList.add('d-none');
        }
    }
</script>
@endpush
