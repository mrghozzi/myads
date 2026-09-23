@extends('admin::layouts.admin')

@section('title', __('messages.users'))

@section('content')
<div class="admin-page">
    <!-- Hero Section -->
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}"><i class="feather-home me-1"></i>{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.users') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">
                <span class="badge bg-soft-primary text-primary px-2 py-1"><i class="feather-users me-1"></i>{{ __('messages.admin_panel') ?? 'Admin Panel' }}</span>
            </div>
            <h1 class="admin-hero__title d-flex align-items-center gap-2">
                <i class="feather-users text-primary"></i>
                {{ __('messages.users') }}
            </h1>
            <p class="admin-hero__copy">{{ __('messages.create_user_desc') ?? 'Comprehensive member management, wallet balances, role privileges, and verification.' }}</p>

            <!-- 5 KPI Global Stat Strip -->
            <div class="admin-stat-strip mt-3" id="heroStatsContainer">
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-users me-1 text-primary"></i>{{ __('messages.users') }}</span>
                    <span class="admin-stat-value text-primary" id="kpiTotalUsers">{{ number_format($stats['total'] ?? 0) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-activity me-1 text-success"></i>{{ __('messages.online') }}</span>
                    <span class="admin-stat-value text-success" id="kpiOnlineUsers">{{ number_format($stats['online'] ?? 0) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-check-circle me-1 text-info"></i>{{ __('messages.Verified') }}</span>
                    <span class="admin-stat-value text-info" id="kpiVerifiedUsers">{{ number_format($stats['verified'] ?? 0) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-shield me-1 text-danger"></i>{{ __('messages.Admins') }}</span>
                    <span class="admin-stat-value text-danger" id="kpiAdminUsers">{{ number_format($stats['admins'] ?? 0) }}</span>
                </div>
                <div class="admin-stat-card">
                    <span class="admin-stat-label"><i class="feather-award me-1 text-warning"></i>{{ __('messages.total_points_circulation') }}</span>
                    <span class="admin-stat-value text-warning" id="kpiTotalPts">{{ number_format($stats['total_pts'] ?? 0, 1) }}</span>
                </div>
            </div>
        </div>

        <!-- Hero Actions / Search & Quick Add -->
        <div class="admin-hero__actions">
            <div class="d-flex align-items-center gap-2 w-100 flex-wrap">
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 flex-grow-1" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="feather-user-plus"></i>
                    <span>{{ __('messages.add_user') }}</span>
                </button>
                <button type="button" class="btn btn-light admin-icon-btn" id="refreshTableBtn" title="Refresh">
                    <i class="feather-refresh-cw"></i>
                </button>
            </div>
        </div>
    </section>

    <!-- Main Panel Container -->
    <section class="admin-panel shadow-sm">
        <!-- Panel Filter Toolbar -->
        <div class="admin-panel__header p-3 border-bottom bg-transparent">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 w-100">
                <!-- Search Box -->
                <div class="position-relative flex-grow-1" style="max-width: 380px; min-width: 240px;">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted">
                            <i class="feather-search"></i>
                        </span>
                        <input 
                            type="text" 
                            id="userSearchInput" 
                            class="form-control border-start-0 ps-0" 
                            placeholder="{{ __('messages.search_users') }}..." 
                            value="{{ request('search') }}"
                            autocomplete="off"
                        >
                        <button type="button" class="btn btn-transparent text-muted d-none" id="clearSearchBtn" title="Clear">
                            <i class="feather-x"></i>
                        </button>
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="d-flex flex-wrap align-items-center gap-2 ms-auto">
                    <!-- Role Filter -->
                    <select id="roleFilter" class="form-select form-select-sm" style="width: auto; min-width: 130px;">
                        <option value="">{{ __('messages.Role') }}: {{ __('messages.All') }}</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>{{ __('messages.Admins') }}</option>
                        <option value="member" {{ request('role') === 'member' ? 'selected' : '' }}>{{ __('messages.Members') }}</option>
                    </select>

                    <!-- Status Filter -->
                    <select id="onlineFilter" class="form-select form-select-sm" style="width: auto; min-width: 140px;">
                        <option value="">{{ __('messages.status') }}: {{ __('messages.All') }}</option>
                        <option value="1" {{ request('online') === '1' ? 'selected' : '' }}>{{ __('messages.online') }}</option>
                        <option value="0" {{ request('online') === '0' ? 'selected' : '' }}>{{ __('messages.offline') }}</option>
                    </select>

                    <!-- Verification Filter -->
                    <select id="verifiedFilter" class="form-select form-select-sm" style="width: auto; min-width: 140px;">
                        <option value="">{{ __('messages.Verification') }}: {{ __('messages.All') }}</option>
                        <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>{{ __('messages.Verified') }}</option>
                        <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>{{ __('messages.Unverified') }}</option>
                    </select>

                    <!-- Per Page Selector -->
                    <select id="perPageFilter" class="form-select form-select-sm" style="width: auto;">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ !request('per_page') || request('per_page') == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>

            <!-- Active Filter Chips (Dynamically Populated) -->
            <div id="activeFilterChips" class="admin-filter-chip-list mt-2 d-flex flex-wrap gap-2 w-100"></div>
        </div>

        <!-- Table Container with Loading Spinner Overlay -->
        <div class="position-relative" id="tableContainerWrapper">
            <!-- Loading Indicator Overlay -->
            <div id="tableLoadingOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-none justify-content-center align-items-center" style="background: rgba(255,255,255,0.7); backdrop-filter: blur(2px); z-index: 20;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            <!-- Table Content Container -->
            <div id="tableContentArea">
                @include('admin::admin.partials.users_table', ['users' => $users])
            </div>
        </div>
    </section>

    <!-- Floating Bulk Actions Toolbar -->
    <div id="floatingBulkToolbar" class="position-fixed bottom-0 start-50 translate-middle-x mb-4 p-3 rounded-4 shadow-lg border d-none align-items-center gap-3" style="background: var(--admin-premium-surface, #ffffff); z-index: 1050; min-width: 320px; max-width: 90vw; backdrop-filter: blur(12px);">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary rounded-pill fs-12 px-2 py-1" id="bulkSelectedCount">0</span>
            <span class="small fw-semibold text-muted">{{ __('messages.selected_users_count') ?? 'Selected' }}</span>
        </div>
        <div class="vr my-1"></div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-success rounded-pill" id="bulkVerifyBtn">
                <i class="feather-check-circle me-1"></i>{{ __('messages.bulk_verify') }}
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" id="bulkUnverifyBtn">
                <i class="feather-circle me-1"></i>{{ __('messages.bulk_unverify') }}
            </button>
            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill" id="bulkPointsBtn">
                <i class="feather-plus-circle me-1"></i>{{ __('messages.bulk_add_points') }}
            </button>
            <button type="button" class="btn btn-sm btn-danger rounded-pill" id="bulkDeleteBtn" data-bs-toggle="modal" data-bs-target="#bulkDeleteModal">
                <i class="feather-trash-2 me-1"></i>{{ __('messages.delete') }}
            </button>
            <button type="button" class="btn btn-sm btn-light rounded-circle p-1 ms-auto" id="cancelBulkSelectionBtn" title="Clear selection">
                <i class="feather-x"></i>
            </button>
        </div>
    </div>
</div>
@endsection

@section('modals')
<!-- Modal 1: Add New User -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--admin-premium-radius, 24px);">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2 text-primary">
                    <i class="feather-user-plus"></i>
                    {{ __('messages.add_user') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm" method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div id="addUserErrors" class="alert alert-danger d-none py-2 px-3 small"></div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.username') }} <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" placeholder="e.g. johndoe" required minlength="3">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.email') }} <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.password') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="password" id="addUserPasswordInput" class="form-control" placeholder="Min 8 characters" required minlength="8">
                            <button type="button" class="btn btn-outline-secondary" id="generateAddUserPasswordBtn" title="{{ __('messages.generate_password') }}">
                                <i class="feather-refresh-cw"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">{{ __('messages.points') }} (PTS)</label>
                            <input type="number" step="0.01" name="pts" class="form-control" value="10">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">{{ __('messages.Verification') }}</label>
                            <select name="ucheck" class="form-select">
                                <option value="0">{{ __('messages.Unverified') }}</option>
                                <option value="1">{{ __('messages.Verified') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-check form-switch p-0 mt-2">
                        <div class="d-flex align-items-center gap-2">
                            <input class="form-check-input ms-0" type="checkbox" name="is_admin" id="addUserIsAdmin" value="1">
                            <label class="form-check-label fw-semibold" for="addUserIsAdmin">
                                {{ __('messages.grant_admin_access') ?? 'Grant Site Administrator Privileges' }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="addUserSubmitBtn">
                        <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                        {{ __('messages.create_user') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Quick Balances & Credits Adjustment -->
<div class="modal fade" id="quickBalanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--admin-premium-radius, 24px);">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2 text-primary">
                    <i class="feather-award"></i>
                    {{ __('messages.quick_balance_adjust') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickBalanceForm">
                @csrf
                <input type="hidden" name="action" value="adjust_balances">
                <input type="hidden" id="quickBalanceUserId" value="">
                
                <div class="modal-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3 p-2 bg-light rounded-3">
                        <i class="feather-user text-primary"></i>
                        <span class="fw-semibold" id="quickBalanceUserName"></span>
                    </div>

                    <!-- Adjustment Mode Selector -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.adjustment_mode') }}</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="mode" id="modeIncrement" value="increment" checked>
                            <label class="btn btn-outline-primary" for="modeIncrement">{{ __('messages.increment_balance') }}</label>

                            <input type="radio" class="btn-check" name="mode" id="modeSet" value="set">
                            <label class="btn btn-outline-primary" for="modeSet">{{ __('messages.set_balance') }}</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.points') }} (PTS)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="pts" id="quickBalancePts" class="form-control" placeholder="+/- Amount">
                        </div>
                        <!-- Preset Chips -->
                        <div class="d-flex gap-1 mt-1 flex-wrap">
                            <button type="button" class="btn btn-xs btn-light border balance-preset" data-val="10">+10</button>
                            <button type="button" class="btn btn-xs btn-light border balance-preset" data-val="50">+50</button>
                            <button type="button" class="btn btn-xs btn-light border balance-preset" data-val="100">+100</button>
                            <button type="button" class="btn btn-xs btn-light border balance-preset" data-val="-10">-10</button>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">{{ __('messages.exchange_visits_pts') }} (VU)</label>
                            <input type="number" step="0.01" name="vu" id="quickBalanceVu" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">{{ __('messages.banner_ads_pts') }} (NVU)</label>
                            <input type="number" step="0.01" name="nvu" id="quickBalanceNvu" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">{{ __('messages.text_ads_pts') }} (NLINK)</label>
                            <input type="number" step="0.01" name="nlink" id="quickBalanceNlink" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">{{ __('messages.smart_ads_credits_admin') }} (NSMART)</label>
                            <input type="number" step="0.01" name="nsmart" id="quickBalanceNsmart" class="form-control form-control-sm">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">{{ __('messages.reason_or_note') }}</label>
                        <input type="text" name="note" class="form-control form-control-sm" placeholder="Optional admin note">
                    </div>

                    <div class="form-check form-switch p-0">
                        <div class="d-flex align-items-center gap-2">
                            <input class="form-check-input ms-0" type="checkbox" name="notify_user" id="quickBalanceNotify" value="1">
                            <label class="form-check-label small" for="quickBalanceNotify">
                                {{ __('messages.notify_user') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="quickBalanceSubmitBtn">
                        <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                        {{ __('messages.adjust_balances') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 3: Quick Password Reset Modal -->
<div class="modal fade" id="quickPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--admin-premium-radius, 24px);">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2 text-warning">
                    <i class="feather-key"></i>
                    {{ __('messages.change_password') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickPasswordForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="quickPasswordUserId" value="">

                <div class="modal-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3 p-2 bg-light rounded-3">
                        <i class="feather-user text-primary"></i>
                        <span class="fw-semibold" id="quickPasswordUserName"></span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.new_password') }} <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="password" id="quickPasswordInput" class="form-control" required minlength="8" autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary" id="generateQuickPasswordBtn" title="{{ __('messages.generate_password') }}">
                                <i class="feather-refresh-cw"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="copyQuickPasswordBtn" title="{{ __('messages.copy_password') }}">
                                <i class="feather-copy"></i>
                            </button>
                        </div>
                        <span class="form-text text-muted fs-11">{{ __('messages.min_8_chars') }}</span>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold" id="quickPasswordSubmitBtn">
                        <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                        {{ __('messages.update_password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 4: Quick View Dossier Modal -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--admin-premium-radius, 24px);">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2 text-primary">
                    <i class="feather-user"></i>
                    {{ __('messages.user_dossier') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="quickViewBody">
                <div class="text-center py-4" id="quickViewLoading">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <div id="quickViewContent" class="d-none">
                    <!-- Profile Card -->
                    <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-light mb-4">
                        <img id="qvAvatar" src="" class="rounded-circle border" width="64" height="64" style="object-fit:cover;">
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h4 class="mb-0 fw-bold" id="qvUsername"></h4>
                                <span id="qvVerifiedBadge"></span>
                                <span id="qvRoleBadge"></span>
                            </div>
                            <div class="d-flex align-items-center flex-wrap gap-2 text-muted fs-12 mt-1">
                                <span><i class="feather-mail me-1"></i><span id="qvEmail"></span></span>
                                <span><i class="feather-hash me-1"></i>ID: <span id="qvId"></span></span>
                                <span id="qvPublicUidWrap" class="d-none"><i class="feather-globe me-1"></i><span id="qvPublicUid"></span></span>
                            </div>
                        </div>
                    </div>

                    <!-- Meta & 2FA Information -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 border rounded-3">
                                <div class="text-muted small mb-1">{{ __('messages.registered_at') }}</div>
                                <div class="fw-bold" id="qvRegisteredAt"></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-muted small mb-1">{{ __('messages.two_factor_auth') }}</div>
                                    <div id="qv2faStatus"></div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger d-none" id="qvReset2faBtn">
                                    {{ __('messages.reset_2fa') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Wallet Balances Strip -->
                    <h6 class="fw-bold mb-2">{{ __('messages.points') }} &bull; {{ __('messages.adjust_balances') }}</h6>
                    <div class="row g-2 mb-4 text-center">
                        <div class="col-4 col-sm">
                            <div class="p-2 border rounded-3 bg-light">
                                <div class="small text-muted">PTS</div>
                                <div class="fw-bold text-primary fs-6" id="qvPts">0</div>
                            </div>
                        </div>
                        <div class="col-4 col-sm">
                            <div class="p-2 border rounded-3 bg-light">
                                <div class="small text-muted">VU</div>
                                <div class="fw-bold text-success fs-6" id="qvVu">0</div>
                            </div>
                        </div>
                        <div class="col-4 col-sm">
                            <div class="p-2 border rounded-3 bg-light">
                                <div class="small text-muted">NVU</div>
                                <div class="fw-bold text-warning fs-6" id="qvNvu">0</div>
                            </div>
                        </div>
                        <div class="col-4 col-sm">
                            <div class="p-2 border rounded-3 bg-light">
                                <div class="small text-muted">NLINK</div>
                                <div class="fw-bold text-info fs-6" id="qvNlink">0</div>
                            </div>
                        </div>
                        <div class="col-4 col-sm">
                            <div class="p-2 border rounded-3 bg-light">
                                <div class="small text-muted">NSMART</div>
                                <div class="fw-bold text-dark fs-6" id="qvNsmart">0</div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Counters Grid -->
                    <h6 class="fw-bold mb-2">{{ __('messages.activity_summary') }}</h6>
                    <div class="row g-2 text-center">
                        <div class="col-4 col-sm-2">
                            <div class="p-2 border rounded-3">
                                <i class="feather-message-square text-primary fs-5 d-block mb-1"></i>
                                <div class="fw-bold" id="qvTopics">0</div>
                                <span class="fs-10 text-muted">{{ __('messages.total_topics') }}</span>
                            </div>
                        </div>
                        <div class="col-4 col-sm-2">
                            <div class="p-2 border rounded-3">
                                <i class="feather-message-circle text-info fs-5 d-block mb-1"></i>
                                <div class="fw-bold" id="qvComments">0</div>
                                <span class="fs-10 text-muted">{{ __('messages.total_comments') }}</span>
                            </div>
                        </div>
                        <div class="col-4 col-sm-2">
                            <div class="p-2 border rounded-3">
                                <i class="feather-image text-warning fs-5 d-block mb-1"></i>
                                <div class="fw-bold" id="qvBanners">0</div>
                                <span class="fs-10 text-muted">{{ __('messages.total_banners') }}</span>
                            </div>
                        </div>
                        <div class="col-4 col-sm-2">
                            <div class="p-2 border rounded-3">
                                <i class="feather-link text-success fs-5 d-block mb-1"></i>
                                <div class="fw-bold" id="qvLinks">0</div>
                                <span class="fs-10 text-muted">{{ __('messages.total_links') }}</span>
                            </div>
                        </div>
                        <div class="col-4 col-sm-2">
                            <div class="p-2 border rounded-3">
                                <i class="feather-target text-danger fs-5 d-block mb-1"></i>
                                <div class="fw-bold" id="qvSmartAds">0</div>
                                <span class="fs-10 text-muted">{{ __('messages.total_smart_ads') }}</span>
                            </div>
                        </div>
                        <div class="col-4 col-sm-2">
                            <div class="p-2 border rounded-3">
                                <i class="feather-shopping-bag text-secondary fs-5 d-block mb-1"></i>
                                <div class="fw-bold" id="qvProducts">0</div>
                                <span class="fs-10 text-muted">{{ __('messages.total_products') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top p-3 justify-content-between">
                <a href="#" id="qvPublicProfileLink" target="_blank" class="btn btn-sm btn-outline-secondary">
                    <i class="feather-external-link me-1"></i>{{ __('messages.view_profile') }}
                </a>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <a href="#" id="qvFullEditLink" class="btn btn-sm btn-primary">
                        <i class="feather-edit-3 me-1"></i>{{ __('messages.edit_user') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal 5: Send Direct Notification Modal -->
<div class="modal fade" id="quickNotifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--admin-premium-radius, 24px);">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2 text-info">
                    <i class="feather-bell"></i>
                    {{ __('messages.send_notification') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickNotifyForm">
                @csrf
                <input type="hidden" id="quickNotifyUserId" value="">

                <div class="modal-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3 p-2 bg-light rounded-3">
                        <i class="feather-user text-primary"></i>
                        <span class="fw-semibold" id="quickNotifyUserName"></span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('messages.message_content') }} <span class="text-danger">*</span></label>
                        <textarea name="message" id="quickNotifyMessage" class="form-control" rows="4" placeholder="Enter message to display in member's notification feed..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 justify-content-end gap-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="btn btn-info text-white" id="quickNotifySubmitBtn">
                        <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                        {{ __('messages.send_notification') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 6: Single User Delete Confirmation -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--admin-premium-radius, 24px);">
            <div class="modal-header border-bottom">
                <h5 class="modal-title text-danger fw-bold"><i class="feather-alert-triangle me-1"></i>{{ __('messages.delete_user') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="admin-modal-icon is-danger mb-3" style="width: 50px; height: 50px; border-radius: 50%; background: var(--admin-premium-danger-soft, #fdeded); color: #ea4d4d; display: inline-flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class="feather-trash-2"></i>
                </div>
                <h4>{{ __('messages.are_you_sure') }}</h4>
                <p class="text-muted mb-0">
                    {{ __('messages.User') }}:
                    <strong id="deleteUserModalName"></strong>
                </p>
            </div>
            <div class="modal-footer justify-content-center border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <form action="" method="POST" id="deleteUserModalForm" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="deleteUserSubmitBtn">
                        <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                        {{ __('messages.delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal 7: Bulk Delete Confirmation -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--admin-premium-radius, 24px);">
            <div class="modal-header border-bottom">
                <h5 class="modal-title text-danger fw-bold"><i class="feather-alert-triangle me-1"></i>{{ __('messages.delete_selected') ?? 'Delete Selected' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="admin-modal-icon is-danger mb-3" style="width: 50px; height: 50px; border-radius: 50%; background: var(--admin-premium-danger-soft, #fdeded); color: #ea4d4d; display: inline-flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class="feather-trash-2"></i>
                </div>
                <h4>{{ __('messages.are_you_sure') }}</h4>
                <p class="text-muted mb-0">
                    {{ __('messages.selected_users_count') ?? 'Selected users count' }}:
                    <strong id="bulkDeleteModalCount">0</strong>
                </p>
                <div class="alert alert-warning mt-3 mb-0 text-start small">
                    <i class="feather-alert-triangle me-1"></i>
                    {{ __('messages.bulk_delete_warning') ?? 'This action will permanently delete selected users and their associated records.' }}
                </div>
            </div>
            <div class="modal-footer justify-content-center border-top">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel') }}</button>
                <button type="button" class="btn btn-danger" id="confirmBulkDeleteBtn">
                    <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                    {{ __('messages.delete') }}
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    // Global CSRF Token & Relative Base URL (works seamlessly across subfolders and domains)
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const currentBaseUrl = window.location.pathname.replace(/\/+$/, '');

    // Multilingual Dictionary
    const i18n = {
        errorTogglingVerification: @json(__('messages.error_toggling_verification')),
        bulkOperationNetworkError: @json(__('messages.bulk_operation_network_error')),
        bulkOperationFailed: @json(__('messages.bulk_operation_failed')),
        bulkDeletionFailed: @json(__('messages.bulk_deletion_failed')),
        errorProcessingBulkDeletion: @json(__('messages.error_processing_bulk_deletion')),
        errorUpdatingBalances: @json(__('messages.error_updating_balances')),
        errorSendingNotification: @json(__('messages.error_sending_notification')),
        failedToSendNotification: @json(__('messages.failed_to_send_notification')),
        errorResettingPassword: @json(__('messages.error_resetting_password')),
        passwordUpdateFailed: @json(__('messages.password_update_failed')),
        failedToLoadProfileDetails: @json(__('messages.failed_to_load_profile_details')),
        failedToLoadUsers: @json(__('messages.failed_to_load_users')),
        dataRefreshed: @json(__('messages.data_refreshed')),
        failedToCreateUser: @json(__('messages.failed_to_create_user')),
        creationFailed: @json(__('messages.creation_failed')),
        updateFailed: @json(__('messages.update_failed')),
        errorDeletingUser: @json(__('messages.error_deleting_user')),
        deletionFailed: @json(__('messages.deletion_failed')),
        reset2faFailed: @json(__('messages.reset_2fa_failed')),
        operationFailed: @json(__('messages.operation_failed')),
        passwordCopied: @json(__('messages.password_copied')),
        reset2faConfirm: @json(__('messages.reset_2fa_confirm')),
        verified: @json(__('messages.Verified')),
        unverified: @json(__('messages.Unverified')),
        twoFactorEnabled: @json(__('messages.two_factor_enabled')),
        twoFactorDisabled: @json(__('messages.two_factor_disabled')),
    };

    // =========================================================================
    // 1. Toast Notification Engine (Glassmorphic / Light & Dark Parity)
    // =========================================================================
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

    // =========================================================================
    // 2. DOM Elements & State
    // =========================================================================
    const searchInput = document.getElementById('userSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const roleFilter = document.getElementById('roleFilter');
    const onlineFilter = document.getElementById('onlineFilter');
    const verifiedFilter = document.getElementById('verifiedFilter');
    const perPageFilter = document.getElementById('perPageFilter');
    const tableContainer = document.getElementById('tableContentArea');
    const tableLoading = document.getElementById('tableLoadingOverlay');
    const activeFilterChips = document.getElementById('activeFilterChips');
    const refreshTableBtn = document.getElementById('refreshTableBtn');
    const floatingBulkToolbar = document.getElementById('floatingBulkToolbar');
    const bulkSelectedCount = document.getElementById('bulkSelectedCount');

    let searchTimer = null;

    // =========================================================================
    // 3. Ajax Fetch & PushState Engine
    // =========================================================================
    function buildQueryUrl(overrides = {}) {
        const url = new URL(window.location.origin + window.location.pathname);
        const search = searchInput ? searchInput.value.trim() : '';
        const role = roleFilter ? roleFilter.value : '';
        const online = onlineFilter ? onlineFilter.value : '';
        const verified = verifiedFilter ? verifiedFilter.value : '';
        const perPage = perPageFilter ? perPageFilter.value : '20';

        const currentUrl = new URL(window.location.href);
        const sort = currentUrl.searchParams.get('sort') || 'id';
        const direction = currentUrl.searchParams.get('direction') || 'desc';
        const page = currentUrl.searchParams.get('page') || '1';

        const params = {
            search,
            role,
            online,
            verified,
            per_page: perPage,
            sort,
            direction,
            page,
            ...overrides,
        };

        Object.keys(params).forEach(key => {
            if (params[key] !== null && params[key] !== '' && params[key] !== undefined) {
                url.searchParams.set(key, params[key]);
            }
        });

        return url;
    }

    function renderFilterChips() {
        if (!activeFilterChips) return;
        activeFilterChips.innerHTML = '';

        const chips = [];
        if (searchInput && searchInput.value.trim() !== '') {
            chips.push({ key: 'search', label: '{{ __("messages.search_users") }}', val: searchInput.value.trim() });
        }
        if (roleFilter && roleFilter.value !== '') {
            const roleLabel = roleFilter.options[roleFilter.selectedIndex]?.text || roleFilter.value;
            chips.push({ key: 'role', label: '{{ __("messages.Role") }}', val: roleLabel });
        }
        if (onlineFilter && onlineFilter.value !== '') {
            const onlineLabel = onlineFilter.options[onlineFilter.selectedIndex]?.text || onlineFilter.value;
            chips.push({ key: 'online', label: '{{ __("messages.status") }}', val: onlineLabel });
        }
        if (verifiedFilter && verifiedFilter.value !== '') {
            const verifiedLabel = verifiedFilter.options[verifiedFilter.selectedIndex]?.text || verifiedFilter.value;
            chips.push({ key: 'verified', label: '{{ __("messages.Verification") }}', val: verifiedLabel });
        }

        chips.forEach(chip => {
            const span = document.createElement('span');
            span.className = 'admin-filter-chip d-inline-flex align-items-center gap-1';
            span.innerHTML = `
                <span>${chip.label}: <strong>${chip.val}</strong></span>
                <a href="javascript:void(0)" class="text-reset opacity-75 ms-1 remove-filter-chip" data-key="${chip.key}">
                    <i class="feather-x"></i>
                </a>
            `;
            activeFilterChips.appendChild(span);
        });

        if (chips.length > 0) {
            const clearAllBtn = document.createElement('a');
            clearAllBtn.href = 'javascript:void(0)';
            clearAllBtn.className = 'text-primary small fw-semibold text-decoration-none ms-2 align-self-center';
            clearAllBtn.id = 'clearAllChipsBtn';
            clearAllBtn.textContent = '{{ __("messages.reset_filters") }}';
            activeFilterChips.appendChild(clearAllBtn);
        }
    }

    function fetchUsers(targetUrl, pushState = true) {
        if (tableLoading) tableLoading.classList.remove('d-none');

        const ajaxUrl = new URL(targetUrl.toString());
        ajaxUrl.searchParams.set('ajax', '1');

        fetch(ajaxUrl.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            if (tableLoading) tableLoading.classList.add('d-none');
            if (data.success && data.html) {
                tableContainer.innerHTML = data.html;

                if (pushState) {
                    window.history.pushState(null, '', targetUrl.toString());
                }

                // Update KPI Cards
                if (data.summary && data.summary.stats) {
                    const st = data.summary.stats;
                    const elTotal = document.getElementById('kpiTotalUsers');
                    const elOnline = document.getElementById('kpiOnlineUsers');
                    const elVerified = document.getElementById('kpiVerifiedUsers');
                    const elAdmins = document.getElementById('kpiAdminUsers');
                    const elPts = document.getElementById('kpiTotalPts');

                    if (elTotal) elTotal.textContent = Number(st.total || 0).toLocaleString();
                    if (elOnline) elOnline.textContent = Number(st.online || 0).toLocaleString();
                    if (elVerified) elVerified.textContent = Number(st.verified || 0).toLocaleString();
                    if (elAdmins) elAdmins.textContent = Number(st.admins || 0).toLocaleString();
                    if (elPts) elPts.textContent = Number(st.total_pts || 0).toLocaleString(undefined, {minimumFractionDigits: 1, maximumFractionDigits: 1});
                }

                updateBulkToolbar();
                renderFilterChips();
            }
        })
        .catch(err => {
            if (tableLoading) tableLoading.classList.add('d-none');
            showAdminToast(i18n.failedToLoadUsers, 'danger');
        });
    }

    // =========================================================================
    // 4. Live Search Debounce & Filter Change Handlers
    // =========================================================================
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            if (clearSearchBtn) {
                if (this.value.trim() !== '') {
                    clearSearchBtn.classList.remove('d-none');
                } else {
                    clearSearchBtn.classList.add('d-none');
                }
            }

            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                const url = buildQueryUrl({ page: 1 });
                fetchUsers(url);
            }, 300);
        });
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            clearSearchBtn.classList.add('d-none');
            const url = buildQueryUrl({ page: 1 });
            fetchUsers(url);
        });
    }

    [roleFilter, onlineFilter, verifiedFilter, perPageFilter].forEach(filterEl => {
        if (filterEl) {
            filterEl.addEventListener('change', function() {
                const url = buildQueryUrl({ page: 1 });
                fetchUsers(url);
            });
        }
    });

    if (refreshTableBtn) {
        refreshTableBtn.addEventListener('click', function() {
            const url = buildQueryUrl();
            fetchUsers(url, false);
            showAdminToast(i18n.dataRefreshed, 'info');
        });
    }

    // Chip removal and reset
    document.addEventListener('click', function(e) {
        const removeChip = e.target.closest('.remove-filter-chip');
        if (removeChip) {
            const key = removeChip.getAttribute('data-key');
            if (key === 'search' && searchInput) searchInput.value = '';
            if (key === 'role' && roleFilter) roleFilter.value = '';
            if (key === 'online' && onlineFilter) onlineFilter.value = '';
            if (key === 'verified' && verifiedFilter) verifiedFilter.value = '';
            const url = buildQueryUrl({ page: 1 });
            fetchUsers(url);
            return;
        }

        const clearAll = e.target.closest('#clearAllChipsBtn') || e.target.closest('#resetFiltersBtn');
        if (clearAll) {
            if (searchInput) searchInput.value = '';
            if (roleFilter) roleFilter.value = '';
            if (onlineFilter) onlineFilter.value = '';
            if (verifiedFilter) verifiedFilter.value = '';
            if (perPageFilter) perPageFilter.value = '20';
            const url = buildQueryUrl({ page: 1, sort: 'id', direction: 'desc' });
            fetchUsers(url);
            return;
        }
    });

    // Browser Back / Forward Button Handling
    window.addEventListener('popstate', function() {
        const currentUrl = new URL(window.location.href);
        if (searchInput) searchInput.value = currentUrl.searchParams.get('search') || '';
        if (roleFilter) roleFilter.value = currentUrl.searchParams.get('role') || '';
        if (onlineFilter) onlineFilter.value = currentUrl.searchParams.get('online') || '';
        if (verifiedFilter) verifiedFilter.value = currentUrl.searchParams.get('verified') || '';
        if (perPageFilter) perPageFilter.value = currentUrl.searchParams.get('per_page') || '20';
        fetchUsers(currentUrl, false);
    });

    // =========================================================================
    // 5. Sortable Column Headers & Pagination Event Delegation
    // =========================================================================
    document.addEventListener('click', function(e) {
        // Column sorting click
        const sortLink = e.target.closest('.table-sort-link');
        if (sortLink) {
            e.preventDefault();
            const col = sortLink.getAttribute('data-sort');
            const currentUrl = new URL(window.location.href);
            const currentSort = currentUrl.searchParams.get('sort') || 'id';
            const currentDir = currentUrl.searchParams.get('direction') || 'desc';

            let newDir = 'asc';
            if (currentSort === col && currentDir === 'asc') {
                newDir = 'desc';
            }

            const url = buildQueryUrl({ sort: col, direction: newDir });
            fetchUsers(url);
            return;
        }

        // Pagination click delegation
        const paginationLink = e.target.closest('.admin-pagination-container .pagination a');
        if (paginationLink) {
            e.preventDefault();
            const href = paginationLink.getAttribute('href');
            if (href) {
                const targetUrl = new URL(href);
                fetchUsers(targetUrl);
                window.scrollTo({ top: tableContainer.offsetTop - 80, behavior: 'smooth' });
            }
            return;
        }
    });

    // =========================================================================
    // 6. Checkboxes & Floating Bulk Toolbar
    // =========================================================================
    function updateBulkToolbar() {
        const checkedCheckboxes = document.querySelectorAll('.user-item-checkbox:checked:not(:disabled)');
        const count = checkedCheckboxes.length;

        if (bulkSelectedCount) bulkSelectedCount.textContent = count;

        if (count > 0) {
            if (floatingBulkToolbar) {
                floatingBulkToolbar.classList.remove('d-none');
                floatingBulkToolbar.classList.add('d-flex');
            }
        } else {
            if (floatingBulkToolbar) {
                floatingBulkToolbar.classList.add('d-none');
                floatingBulkToolbar.classList.remove('d-flex');
            }
        }

        const checkAll = document.getElementById('checkAllUsers');
        const allCheckboxes = document.querySelectorAll('.user-item-checkbox:not(:disabled)');
        if (checkAll && allCheckboxes.length > 0) {
            const allChecked = Array.from(allCheckboxes).every(c => c.checked);
            const someChecked = Array.from(allCheckboxes).some(c => c.checked);
            checkAll.checked = allChecked;
            checkAll.indeterminate = someChecked && !allChecked;
        }
    }

    document.addEventListener('change', function(e) {
        if (e.target.id === 'checkAllUsers') {
            const checkboxes = document.querySelectorAll('.user-item-checkbox:not(:disabled)');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
            updateBulkToolbar();
            return;
        }

        if (e.target.classList.contains('user-item-checkbox')) {
            updateBulkToolbar();
            return;
        }
    });

    const cancelBulkBtn = document.getElementById('cancelBulkSelectionBtn');
    if (cancelBulkBtn) {
        cancelBulkBtn.addEventListener('click', function() {
            document.querySelectorAll('.user-item-checkbox').forEach(cb => cb.checked = false);
            updateBulkToolbar();
        });
    }

    // =========================================================================
    // 7. Inline Quick Verification Toggle (Ajax)
    // =========================================================================
    document.addEventListener('click', function(e) {
        const toggleBtn = e.target.closest('.btn-verify-toggle');
        if (toggleBtn) {
            e.preventDefault();
            const userId = toggleBtn.getAttribute('data-user-id');
            if (!userId) return;

            toggleBtn.disabled = true;
            toggleBtn.style.opacity = '0.5';

            fetch(`${currentBaseUrl}/${userId}/quick-update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ action: 'toggle_verification' })
            })
            .then(res => res.json())
            .then(data => {
                toggleBtn.disabled = false;
                toggleBtn.style.opacity = '1';

                if (data.success) {
                    const isVerified = data.ucheck === 1;
                    if (isVerified) {
                        toggleBtn.className = 'btn btn-sm btn-verify-toggle btn-light-success text-success rounded-pill px-2 py-1 border-0 shadow-none transition-all';
                        toggleBtn.innerHTML = `<i class="feather-check-circle me-1"></i><span class="verify-label">${data.label}</span>`;
                    } else {
                        toggleBtn.className = 'btn btn-sm btn-verify-toggle btn-light text-muted rounded-pill px-2 py-1 border-0 shadow-none transition-all';
                        toggleBtn.innerHTML = `<i class="feather-circle me-1"></i><span class="verify-label">${data.label}</span>`;
                    }
                    showAdminToast(data.message, 'success');
                } else {
                    showAdminToast(data.message || i18n.operationFailed, 'danger');
                }
            })
            .catch(() => {
                toggleBtn.disabled = false;
                toggleBtn.style.opacity = '1';
                showAdminToast(i18n.errorTogglingVerification, 'danger');
            });
        }
    });

    // =========================================================================
    // 8. Add New Member Modal (Ajax)
    // =========================================================================
    const addUserForm = document.getElementById('addUserForm');
    const addUserErrors = document.getElementById('addUserErrors');
    const addUserSubmitBtn = document.getElementById('addUserSubmitBtn');
    const generateAddUserPasswordBtn = document.getElementById('generateAddUserPasswordBtn');
    const addUserPasswordInput = document.getElementById('addUserPasswordInput');

    function generateStrongPassword(len = 12) {
        const chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%&*';
        let pass = '';
        for (let i = 0; i < len; i++) {
            pass += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return pass;
    }

    if (generateAddUserPasswordBtn && addUserPasswordInput) {
        generateAddUserPasswordBtn.addEventListener('click', () => {
            addUserPasswordInput.value = generateStrongPassword(12);
        });
    }

    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (addUserErrors) addUserErrors.classList.add('d-none');
            if (addUserSubmitBtn) {
                addUserSubmitBtn.disabled = true;
                addUserSubmitBtn.querySelector('.spinner-border')?.classList.remove('d-none');
            }

            const formData = new FormData(addUserForm);

            fetch(addUserForm.action, {
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
                if (addUserSubmitBtn) {
                    addUserSubmitBtn.disabled = false;
                    addUserSubmitBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addUserModal'))?.hide();
                    addUserForm.reset();
                    showAdminToast(data.message, 'success');
                    fetchUsers(buildQueryUrl({ page: 1 }), false);
                } else if (data.errors) {
                    const errHtml = Object.values(data.errors).flat().join('<br>');
                    if (addUserErrors) {
                        addUserErrors.innerHTML = errHtml;
                        addUserErrors.classList.remove('d-none');
                    }
                } else {
                    showAdminToast(data.message || i18n.creationFailed, 'danger');
                }
            })
            .catch(err => {
                if (addUserSubmitBtn) {
                    addUserSubmitBtn.disabled = false;
                    addUserSubmitBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }
                showAdminToast(i18n.failedToCreateUser, 'danger');
            });
        });
    }

    // =========================================================================
    // 9. Quick Balances Adjustment Modal (Ajax)
    // =========================================================================
    const quickBalanceForm = document.getElementById('quickBalanceForm');
    const quickBalanceUserId = document.getElementById('quickBalanceUserId');
    const quickBalanceUserName = document.getElementById('quickBalanceUserName');
    const quickBalancePts = document.getElementById('quickBalancePts');
    const quickBalanceVu = document.getElementById('quickBalanceVu');
    const quickBalanceNvu = document.getElementById('quickBalanceNvu');
    const quickBalanceNlink = document.getElementById('quickBalanceNlink');
    const quickBalanceNsmart = document.getElementById('quickBalanceNsmart');
    const quickBalanceSubmitBtn = document.getElementById('quickBalanceSubmitBtn');

    document.addEventListener('click', function(e) {
        const trigger = e.target.closest('.trigger-quick-balance');
        if (trigger) {
            const uid = trigger.getAttribute('data-user-id');
            const uname = trigger.getAttribute('data-user-name');
            const pts = trigger.getAttribute('data-pts') || '0';
            const vu = trigger.getAttribute('data-vu') || '0';
            const nvu = trigger.getAttribute('data-nvu') || '0';
            const nlink = trigger.getAttribute('data-nlink') || '0';
            const nsmart = trigger.getAttribute('data-nsmart') || '0';

            quickBalanceUserId.value = uid;
            quickBalanceUserName.textContent = uname;
            quickBalancePts.value = '';
            quickBalancePts.placeholder = '+/- Amount (Current: ' + Number(pts).toFixed(2) + ')';
            quickBalanceVu.value = Number(vu).toFixed(2);
            quickBalanceNvu.value = Number(nvu).toFixed(2);
            quickBalanceNlink.value = Number(nlink).toFixed(2);
            quickBalanceNsmart.value = Number(nsmart).toFixed(2);

            new bootstrap.Modal(document.getElementById('quickBalanceModal')).show();
        }

        const presetBtn = e.target.closest('.balance-preset');
        if (presetBtn && quickBalancePts) {
            const val = presetBtn.getAttribute('data-val');
            quickBalancePts.value = val;
        }
    });

    if (quickBalanceForm) {
        quickBalanceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const uid = quickBalanceUserId.value;
            if (!uid) return;

            if (quickBalanceSubmitBtn) {
                quickBalanceSubmitBtn.disabled = true;
                quickBalanceSubmitBtn.querySelector('.spinner-border')?.classList.remove('d-none');
            }

            const formData = new FormData(quickBalanceForm);
            const dataObj = Object.fromEntries(formData.entries());

            fetch(`${currentBaseUrl}/${uid}/quick-update`, {
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
                if (quickBalanceSubmitBtn) {
                    quickBalanceSubmitBtn.disabled = false;
                    quickBalanceSubmitBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('quickBalanceModal'))?.hide();
                    showAdminToast(data.message, 'success');

                    // Live update user row on current page
                    const row = document.getElementById(`user-row-${uid}`);
                    if (row && data.balances) {
                        const ptsDisplay = row.querySelector('.user-pts-display');
                        if (ptsDisplay) ptsDisplay.innerHTML = `${data.balances.pts} <span class="fs-11 text-muted fw-normal">PTS</span>`;
                    }
                } else {
                    showAdminToast(data.message || i18n.updateFailed, 'danger');
                }
            })
            .catch(() => {
                if (quickBalanceSubmitBtn) {
                    quickBalanceSubmitBtn.disabled = false;
                    quickBalanceSubmitBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }
                showAdminToast(i18n.errorUpdatingBalances, 'danger');
            });
        });
    }

    // =========================================================================
    // 10. Quick Password Reset Modal (Ajax)
    // =========================================================================
    const quickPasswordUserId = document.getElementById('quickPasswordUserId');
    const quickPasswordUserName = document.getElementById('quickPasswordUserName');
    const quickPasswordInput = document.getElementById('quickPasswordInput');
    const generateQuickPasswordBtn = document.getElementById('generateQuickPasswordBtn');
    const copyQuickPasswordBtn = document.getElementById('copyQuickPasswordBtn');
    const quickPasswordForm = document.getElementById('quickPasswordForm');
    const quickPasswordSubmitBtn = document.getElementById('quickPasswordSubmitBtn');

    document.addEventListener('click', function(e) {
        const trigger = e.target.closest('.trigger-quick-password');
        if (trigger) {
            const uid = trigger.getAttribute('data-user-id');
            const uname = trigger.getAttribute('data-user-name');
            quickPasswordUserId.value = uid;
            quickPasswordUserName.textContent = uname;
            quickPasswordInput.value = generateStrongPassword(12);
            new bootstrap.Modal(document.getElementById('quickPasswordModal')).show();
        }
    });

    if (generateQuickPasswordBtn && quickPasswordInput) {
        generateQuickPasswordBtn.addEventListener('click', () => {
            quickPasswordInput.value = generateStrongPassword(12);
        });
    }

    if (copyQuickPasswordBtn && quickPasswordInput) {
        copyQuickPasswordBtn.addEventListener('click', () => {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(quickPasswordInput.value);
                showAdminToast(i18n.passwordCopied, 'info');
            }
        });
    }

    if (quickPasswordForm) {
        quickPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const uid = quickPasswordUserId.value;
            if (!uid) return;

            if (quickPasswordSubmitBtn) {
                quickPasswordSubmitBtn.disabled = true;
                quickPasswordSubmitBtn.querySelector('.spinner-border')?.classList.remove('d-none');
            }

            fetch(`${currentBaseUrl}/${uid}/password`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ password: quickPasswordInput.value })
            })
            .then(res => res.json())
            .then(data => {
                if (quickPasswordSubmitBtn) {
                    quickPasswordSubmitBtn.disabled = false;
                    quickPasswordSubmitBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('quickPasswordModal'))?.hide();
                    showAdminToast(data.message, 'success');
                } else {
                    showAdminToast(data.message || i18n.passwordUpdateFailed, 'danger');
                }
            })
            .catch(() => {
                if (quickPasswordSubmitBtn) {
                    quickPasswordSubmitBtn.disabled = false;
                    quickPasswordSubmitBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }
                showAdminToast(i18n.errorResettingPassword, 'danger');
            });
        });
    }

    // =========================================================================
    // 11. Quick View Dossier Modal (Ajax)
    // =========================================================================
    const quickViewLoading = document.getElementById('quickViewLoading');
    const quickViewContent = document.getElementById('quickViewContent');
    const qvAvatar = document.getElementById('qvAvatar');
    const qvUsername = document.getElementById('qvUsername');
    const qvVerifiedBadge = document.getElementById('qvVerifiedBadge');
    const qvRoleBadge = document.getElementById('qvRoleBadge');
    const qvEmail = document.getElementById('qvEmail');
    const qvId = document.getElementById('qvId');
    const qvPublicUidWrap = document.getElementById('qvPublicUidWrap');
    const qvPublicUid = document.getElementById('qvPublicUid');
    const qvRegisteredAt = document.getElementById('qvRegisteredAt');
    const qv2faStatus = document.getElementById('qv2faStatus');
    const qvReset2faBtn = document.getElementById('qvReset2faBtn');
    const qvPts = document.getElementById('qvPts');
    const qvVu = document.getElementById('qvVu');
    const qvNvu = document.getElementById('qvNvu');
    const qvNlink = document.getElementById('qvNlink');
    const qvNsmart = document.getElementById('qvNsmart');
    const qvTopics = document.getElementById('qvTopics');
    const qvComments = document.getElementById('qvComments');
    const qvBanners = document.getElementById('qvBanners');
    const qvLinks = document.getElementById('qvLinks');
    const qvSmartAds = document.getElementById('qvSmartAds');
    const qvProducts = document.getElementById('qvProducts');
    const qvPublicProfileLink = document.getElementById('qvPublicProfileLink');
    const qvFullEditLink = document.getElementById('qvFullEditLink');

    let currentQvUserId = null;

    document.addEventListener('click', function(e) {
        const trigger = e.target.closest('.trigger-quick-view');
        if (trigger) {
            const uid = trigger.getAttribute('data-user-id');
            currentQvUserId = uid;

            quickViewLoading.classList.remove('d-none');
            quickViewContent.classList.add('d-none');
            new bootstrap.Modal(document.getElementById('quickViewModal')).show();

            fetch(`${currentBaseUrl}/${uid}/details`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                quickViewLoading.classList.add('d-none');
                if (data.success && data.user) {
                    const u = data.user;
                    quickViewContent.classList.remove('d-none');

                    qvAvatar.src = u.avatar;
                    qvUsername.textContent = u.username;
                    qvEmail.textContent = u.email;
                    qvId.textContent = u.id;

                    if (u.public_uid) {
                        qvPublicUidWrap.classList.remove('d-none');
                        qvPublicUid.textContent = u.public_uid;
                    } else {
                        qvPublicUidWrap.classList.add('d-none');
                    }

                    // Badges
                    qvVerifiedBadge.innerHTML = u.ucheck === 1 
                        ? `<span class="badge bg-soft-success text-success"><i class="feather-check-circle me-1"></i>${i18n.verified}</span>`
                        : `<span class="badge bg-soft-secondary text-muted">${i18n.unverified}</span>`;

                    if (u.is_super_admin) {
                        qvRoleBadge.innerHTML = '<span class="badge bg-soft-danger text-danger"><i class="feather-shield me-1"></i>Super Admin</span>';
                    } else if (u.is_admin) {
                        qvRoleBadge.innerHTML = '<span class="badge bg-soft-primary text-primary"><i class="feather-user-check me-1"></i>{{ __("messages.Admin") }}</span>';
                    } else {
                        qvRoleBadge.innerHTML = '<span class="badge bg-soft-secondary text-secondary">{{ __("messages.Member") }}</span>';
                    }

                    qvRegisteredAt.textContent = u.registered_at;

                    // 2FA
                    if (u.two_factor_enabled) {
                        qv2faStatus.innerHTML = `<span class="badge bg-success">${i18n.twoFactorEnabled}</span>`;
                        qvReset2faBtn.classList.remove('d-none');
                    } else {
                        qv2faStatus.innerHTML = `<span class="badge bg-secondary">${i18n.twoFactorDisabled}</span>`;
                        qvReset2faBtn.classList.add('d-none');
                    }

                    // Balances
                    qvPts.textContent = u.balances.pts;
                    qvVu.textContent = u.balances.vu;
                    qvNvu.textContent = u.balances.nvu;
                    qvNlink.textContent = u.balances.nlink;
                    qvNsmart.textContent = u.balances.nsmart;

                    // Activity
                    qvTopics.textContent = u.stats.topics;
                    qvComments.textContent = u.stats.comments;
                    qvBanners.textContent = u.stats.banners;
                    qvLinks.textContent = u.stats.links;
                    qvSmartAds.textContent = u.stats.smart_ads;
                    qvProducts.textContent = u.stats.products;

                    // Action links
                    qvPublicProfileLink.href = u.profile_url;
                    qvFullEditLink.href = u.edit_url;
                }
            })
            .catch(() => {
                quickViewLoading.classList.add('d-none');
                showAdminToast(i18n.failedToLoadProfileDetails, 'danger');
            });
        }
    });

    if (qvReset2faBtn) {
        qvReset2faBtn.addEventListener('click', function() {
            if (!currentQvUserId) return;
            if (!confirm(i18n.reset2faConfirm)) return;

            fetch(`${currentBaseUrl}/${currentQvUserId}/reset-2fa`, {
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
                    qv2faStatus.innerHTML = `<span class="badge bg-secondary">${i18n.twoFactorDisabled}</span>`;
                    qvReset2faBtn.classList.add('d-none');
                } else {
                    showAdminToast(data.message || i18n.reset2faFailed, 'danger');
                }
            })
            .catch(() => {
                showAdminToast(i18n.reset2faFailed, 'danger');
            });
        });
    }

    // =========================================================================
    // 12. Send Direct Notification Modal (Ajax)
    // =========================================================================
    const quickNotifyForm = document.getElementById('quickNotifyForm');
    const quickNotifyUserId = document.getElementById('quickNotifyUserId');
    const quickNotifyUserName = document.getElementById('quickNotifyUserName');
    const quickNotifyMessage = document.getElementById('quickNotifyMessage');
    const quickNotifySubmitBtn = document.getElementById('quickNotifySubmitBtn');

    document.addEventListener('click', function(e) {
        const trigger = e.target.closest('.trigger-quick-notify');
        if (trigger) {
            const uid = trigger.getAttribute('data-user-id');
            const uname = trigger.getAttribute('data-user-name');
            quickNotifyUserId.value = uid;
            quickNotifyUserName.textContent = uname;
            quickNotifyMessage.value = '';
            new bootstrap.Modal(document.getElementById('quickNotifyModal')).show();
        }
    });

    if (quickNotifyForm) {
        quickNotifyForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const uid = quickNotifyUserId.value;
            if (!uid) return;

            if (quickNotifySubmitBtn) {
                quickNotifySubmitBtn.disabled = true;
                quickNotifySubmitBtn.querySelector('.spinner-border')?.classList.remove('d-none');
            }

            fetch(`${currentBaseUrl}/${uid}/notify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ message: quickNotifyMessage.value })
            })
            .then(res => res.json())
            .then(data => {
                if (quickNotifySubmitBtn) {
                    quickNotifySubmitBtn.disabled = false;
                    quickNotifySubmitBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }

                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('quickNotifyModal'))?.hide();
                    showAdminToast(data.message, 'success');
                } else {
                    showAdminToast(data.message || i18n.failedToSendNotification, 'danger');
                }
            })
            .catch(() => {
                if (quickNotifySubmitBtn) {
                    quickNotifySubmitBtn.disabled = false;
                    quickNotifySubmitBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }
                showAdminToast(i18n.errorSendingNotification, 'danger');
            });
        });
    }

    // =========================================================================
    // 13. Single Delete User Modal (Ajax)
    // =========================================================================
    const deleteModal = document.getElementById('deleteUserModal');
    const deleteForm = document.getElementById('deleteUserModalForm');
    const deleteUserName = document.getElementById('deleteUserModalName');
    const deleteUserSubmitBtn = document.getElementById('deleteUserSubmitBtn');
    let deletingUserId = null;

    document.addEventListener('click', function(e) {
        const trigger = e.target.closest('.trigger-single-delete');
        if (trigger) {
            deletingUserId = trigger.getAttribute('data-user-id');
            const uname = trigger.getAttribute('data-user-name');
            const action = trigger.getAttribute('data-action');

            if (deleteUserName) deleteUserName.textContent = uname;
            if (deleteForm) deleteForm.setAttribute('action', action);

            new bootstrap.Modal(deleteModal).show();
        }
    });

    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!deletingUserId) return;

            if (deleteUserSubmitBtn) {
                deleteUserSubmitBtn.disabled = true;
                deleteUserSubmitBtn.querySelector('.spinner-border')?.classList.remove('d-none');
            }

            fetch(deleteForm.getAttribute('action'), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (deleteUserSubmitBtn) {
                    deleteUserSubmitBtn.disabled = false;
                    deleteUserSubmitBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }

                if (data.success) {
                    bootstrap.Modal.getInstance(deleteModal)?.hide();
                    showAdminToast(data.message, 'success');

                    // Animated row removal
                    const row = document.getElementById(`user-row-${deletingUserId}`);
                    if (row) {
                        row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                        row.style.opacity = '0';
                        row.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            row.remove();
                            updateBulkToolbar();
                        }, 300);
                    }
                } else {
                    showAdminToast(data.message || i18n.deletionFailed, 'danger');
                }
            })
            .catch(() => {
                if (deleteUserSubmitBtn) {
                    deleteUserSubmitBtn.disabled = false;
                    deleteUserSubmitBtn.querySelector('.spinner-border')?.classList.add('d-none');
                }
                showAdminToast(i18n.errorDeletingUser, 'danger');
            });
        });
    }

    // =========================================================================
    // 14. Bulk Operations (Verify, Unverify, Add Points, Delete) (Ajax)
    // =========================================================================
    const bulkVerifyBtn = document.getElementById('bulkVerifyBtn');
    const bulkUnverifyBtn = document.getElementById('bulkUnverifyBtn');
    const bulkPointsBtn = document.getElementById('bulkPointsBtn');
    const confirmBulkDeleteBtn = document.getElementById('confirmBulkDeleteBtn');
    const bulkDeleteModalCount = document.getElementById('bulkDeleteModalCount');

    function getSelectedUserIds() {
        return Array.from(document.querySelectorAll('.user-item-checkbox:checked:not(:disabled)')).map(cb => cb.value);
    }

    function executeBulkAction(action, payload = {}) {
        const ids = getSelectedUserIds();
        if (ids.length === 0) return;

        fetch(`${currentBaseUrl}/bulk/action`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ ids, action, ...payload })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showAdminToast(data.message, 'success');
                fetchUsers(buildQueryUrl(), false);
            } else {
                showAdminToast(data.message || i18n.bulkOperationFailed, 'danger');
            }
        })
        .catch(() => showAdminToast(i18n.bulkOperationNetworkError, 'danger'));
    }

    if (bulkVerifyBtn) {
        bulkVerifyBtn.addEventListener('click', () => executeBulkAction('verify'));
    }

    if (bulkUnverifyBtn) {
        bulkUnverifyBtn.addEventListener('click', () => executeBulkAction('unverify'));
    }

    if (bulkPointsBtn) {
        bulkPointsBtn.addEventListener('click', function() {
            const amount = prompt('{{ __("messages.amount") }} PTS to credit each selected member:', '10');
            if (amount !== null && !isNaN(amount) && Number(amount) > 0) {
                executeBulkAction('add_points', { amount: Number(amount) });
            }
        });
    }

    const bulkDeleteModal = document.getElementById('bulkDeleteModal');
    if (bulkDeleteModal) {
        bulkDeleteModal.addEventListener('show.bs.modal', function() {
            const ids = getSelectedUserIds();
            if (bulkDeleteModalCount) bulkDeleteModalCount.textContent = ids.length;
        });
    }

    if (confirmBulkDeleteBtn) {
        confirmBulkDeleteBtn.addEventListener('click', function() {
            const ids = getSelectedUserIds();
            if (ids.length === 0) return;

            confirmBulkDeleteBtn.disabled = true;
            confirmBulkDeleteBtn.querySelector('.spinner-border')?.classList.remove('d-none');

            fetch(`${currentBaseUrl}/bulk/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ ids })
            })
            .then(res => res.json())
            .then(data => {
                confirmBulkDeleteBtn.disabled = false;
                confirmBulkDeleteBtn.querySelector('.spinner-border')?.classList.add('d-none');

                if (data.success) {
                    bootstrap.Modal.getInstance(bulkDeleteModal)?.hide();
                    showAdminToast(data.message, 'success');
                    fetchUsers(buildQueryUrl(), false);
                } else {
                    showAdminToast(data.message || i18n.bulkDeletionFailed, 'danger');
                }
            })
            .catch(() => {
                confirmBulkDeleteBtn.disabled = false;
                confirmBulkDeleteBtn.querySelector('.spinner-border')?.classList.add('d-none');
                showAdminToast(i18n.errorProcessingBulkDeletion, 'danger');
            });
        });
    }

    // Initial render
    renderFilterChips();
})();
</script>
@endpush
