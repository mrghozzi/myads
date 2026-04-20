@extends('admin::layouts.admin')

@section('title', $title)

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ $title }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.admin_panel') ?? 'Admin Panel' }}</div>
            <h1 class="admin-hero__title">{{ $title }}</h1>
            <p class="admin-hero__copy">
                @if(request()->has('id'))
                    <span class="badge bg-soft-primary text-primary px-3 py-2 fs-12">
                        <i class="feather-hash me-1"></i>
                        {{ __('messages.ID') ?? 'ID' }}: {{ request()->id }}
                    </span>
                @elseif(request()->has('st'))
                    <span class="badge bg-soft-info text-info px-3 py-2 fs-12">
                        <i class="feather-user me-1"></i>
                        {{ \App\Models\User::find(request()->st)->username ?? __('messages.unknown') ?? 'Unknown' }}
                    </span>
                @else
                    {{ __('messages.all_stats') ?? 'Performance Statistics' }}
                @endif
            </p>

            <div class="admin-stat-strip">
                <div class="admin-stat-card">
                    <span class="admin-stat-label">{{ __('messages.total_records') ?? 'Total Records' }}</span>
                    <span class="admin-stat-value">{{ number_format($stats->total()) }}</span>
                </div>
            </div>
        </div>

        <div class="admin-hero__actions">
            <a href="{{ url()->previous() }}" class="btn btn-light">
                <i class="feather-arrow-left me-2"></i>
                {{ __('messages.go_back') }}
            </a>
        </div>
    </section>

    <section class="admin-panel mt-4">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.Statistics') ?? 'Statistics' }}</span>
                <h2 class="admin-panel__title">
                    @if($stats->total() > 0)
                        {{ $stats->firstItem() }}-{{ $stats->lastItem() }} / {{ $stats->total() }}
                    @else
                        0
                    @endif
                </h2>
            </div>
        </div>

        <div class="admin-panel__body p-0">
            <div class="admin-table-wrap">
                <table class="table table-hover align-middle admin-table admin-table-cardify">
                    <thead>
                        <tr>
                            <th class="wd-80">#ID</th>
                            <th>{{ __('messages.url_link') ?? 'Url' }}</th>
                            <th>{{ __('messages.time') ?? 'Time' }}</th>
                            <th>{{ __('messages.browser') ?? 'Browser' }}</th>
                            <th>{{ __('messages.platform') ?? 'Platform' }}</th>
                            <th>{{ __('messages.ip') ?? 'Ip' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stats as $stat)
                            <tr>
                                <td data-label="ID">
                                    <span class="fw-bold text-dark">#{{ $stat->id }}</span>
                                </td>
                                <td data-label="{{ __('messages.url_link') }}">
                                    @if($stat->r_link == 'N')
                                        <span class="badge bg-soft-danger text-danger">
                                            <i class="feather-link-2 me-1"></i>
                                            {{ __('messages.no_link') ?? 'No Link' }}
                                        </span>
                                    @else
                                        <a href="{{ $stat->r_link }}" target="_blank" class="btn btn-sm btn-soft-primary admin-icon-btn">
                                            <i class="feather-external-link"></i>
                                        </a>
                                    @endif
                                </td>
                                <td data-label="{{ __('messages.time') }}">
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark">{{ date('d, M Y', $stat->r_date) }}</span>
                                        <small class="text-muted"><i class="feather-clock me-1"></i> {{ date('H:i:s', $stat->r_date) }}</small>
                                    </div>
                                </td>
                                <td data-label="{{ __('messages.browser') }}">
                                    <div class="d-flex flex-column">
                                        <span class="text-dark">{{ $stat->browser['name'] }}</span>
                                        <small class="text-muted fs-11">{{ $stat->browser['version'] }}</small>
                                    </div>
                                </td>
                                <td data-label="{{ __('messages.platform') }}">
                                    <span class="badge bg-soft-secondary text-secondary">{{ $stat->browser['platform'] }}</span>
                                </td>
                                <td data-label="{{ __('messages.ip') }}">
                                    <a href="http://ip.is-best.net/?ip={{ $stat->v_ip }}" target="_blank" class="admin-person">
                                        <span class="admin-person__avatar" style="width: 28px; height: 28px; background: var(--dz-body-bg);">
                                            <i class="feather-map-pin fs-12"></i>
                                        </span>
                                        <span class="admin-person__name fs-12 text-muted fw-normal">{{ $stat->v_ip }}</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="admin-empty-state py-5">
                                        <div class="admin-avatar-circle mb-3">
                                            <i class="feather-bar-chart-2"></i>
                                        </div>
                                        <h4 class="mb-1">{{ __('messages.no_stats') ?? 'No stats found' }}</h4>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($stats->hasPages())
            <div class="admin-panel__footer">
                {{ $stats->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </section>
</div>
@endsection
