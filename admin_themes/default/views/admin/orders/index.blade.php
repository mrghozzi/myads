@extends('admin::layouts.admin')

@section('title', __('messages.order_requests'))

@section('content')
<div class="container-fluid px-0">
    {{-- Header & Actions --}}
    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-4">
        <div>
            <h3 class="mb-1 text-dark fw-bold">{{ __('messages.order_requests') }}</h3>
            <p class="text-muted mb-0 small">{{ __('messages.admin_orders_subtitle') }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('orders.index') }}" target="_blank" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
                <i class="fa fa-external-link-alt"></i>
                <span>{{ __('messages.view') }} ({{ __('messages.platform') }})</span>
            </a>
            <a href="{{ route('admin.orders.export', request()->query()) }}" class="btn btn-success btn-sm d-flex align-items-center gap-2">
                <i class="fa fa-file-csv"></i>
                <span>{{ __('messages.order_export_csv') }}</span>
            </a>
        </div>
    </div>

    {{-- Platform Disclaimer & Policy Reminder --}}
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-start gap-3 mb-4 rounded-3" style="background: rgba(13, 110, 253, 0.08);">
        <div class="fs-4 text-primary mt-1"><i class="fa fa-shield-alt"></i></div>
        <div>
            <div class="fw-bold text-primary mb-1">{{ __('messages.order_disclaimer_title') }}</div>
            <div class="small text-muted">{{ __('messages.order_disclaimer_notice', ['site' => $site_settings->titer ?? config('app.name', 'MyAds')]) }}</div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">{{ __('messages.order_kpi_total') }}</span>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1"><i class="fa fa-layer-group"></i></span>
                    </div>
                    <div class="fs-4 fw-bold text-dark">{{ number_format($kpis['total']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">{{ __('messages.order_kpi_open') }}</span>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1"><i class="fa fa-folder-open"></i></span>
                    </div>
                    <div class="fs-4 fw-bold text-primary">{{ number_format($kpis['open']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">{{ __('messages.order_kpi_in_progress') }}</span>
                        <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1"><i class="fa fa-spinner fa-spin"></i></span>
                    </div>
                    <div class="fs-4 fw-bold text-warning">{{ number_format($kpis['in_progress']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">{{ __('messages.order_kpi_completed') }}</span>
                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1"><i class="fa fa-check-circle"></i></span>
                    </div>
                    <div class="fs-4 fw-bold text-success">{{ number_format($kpis['completed']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">{{ __('messages.order_kpi_cancelled') }}</span>
                        <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1"><i class="fa fa-ban"></i></span>
                    </div>
                    <div class="fs-4 fw-bold text-danger">{{ number_format($kpis['cancelled']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small fw-semibold">{{ __('messages.order_kpi_offers') }}</span>
                        <span class="badge bg-info-subtle text-info rounded-pill px-2 py-1"><i class="fa fa-comments"></i></span>
                    </div>
                    <div class="fs-4 fw-bold text-info">{{ number_format($kpis['offers']) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Filter & Orders Card --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3 mb-4 align-items-end">
                <div class="col-lg-4">
                    <label class="form-label small fw-bold text-muted mb-1">{{ __('messages.search') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fa fa-search text-muted"></i></span>
                        <input type="text" class="form-control border-start-0" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('messages.order_search_placeholder') }}">
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">{{ __('messages.status') }}</label>
                    <select class="form-select" name="status">
                        <option value="all">{{ __('messages.all') }}</option>
                        @foreach(['open', 'awarded', 'in_progress', 'delivered', 'completed', 'closed', 'cancelled'] as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? 'all') === $status)>{{ __('messages.order_status_' . $status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">{{ __('messages.category') }}</label>
                    <select class="form-select" name="category">
                        <option value="">{{ __('messages.all') }}</option>
                        @foreach($categories as $categoryOption)
                            <option value="{{ $categoryOption->slug }}" @selected(($filters['category'] ?? '') === $categoryOption->slug)>{{ $categoryOption->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-md-4">
                    <label class="form-label small fw-bold text-muted mb-1">{{ __('messages.sort') }}</label>
                    <select class="form-select" name="sort">
                        <option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>{{ __('messages.most_recent') }}</option>
                        <option value="active" @selected(($filters['sort'] ?? '') === 'active')>{{ __('messages.most_active') }}</option>
                        <option value="popular" @selected(($filters['sort'] ?? '') === 'popular')>{{ __('messages.order_sort_popular_offers') }}</option>
                        <option value="budget_high" @selected(($filters['sort'] ?? '') === 'budget_high')>{{ __('messages.order_sort_budget_high') }}</option>
                        <option value="budget_low" @selected(($filters['sort'] ?? '') === 'budget_low')>{{ __('messages.order_sort_budget_low') }}</option>
                    </select>
                </div>
                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">{{ __('messages.filter') }}</button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-light text-muted" title="{{ __('messages.clear') }}"><i class="fa fa-redo"></i></a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small text-muted">
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>{{ __('messages.title') }}</th>
                            <th>{{ __('messages.client_info') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.offers') }}</th>
                            <th>{{ __('messages.last_activity') }}</th>
                            <th class="text-end" style="min-width: 140px;">{{ __('messages.options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            @php
                                $statusBadgeClass = match($order->workflow_status) {
                                    'open' => 'bg-primary-subtle text-primary border border-primary-subtle',
                                    'awarded' => 'bg-info-subtle text-info border border-info-subtle',
                                    'in_progress' => 'bg-warning-subtle text-warning border border-warning-subtle',
                                    'delivered' => 'bg-purple-subtle text-purple border border-purple-subtle',
                                    'completed' => 'bg-success-subtle text-success border border-success-subtle',
                                    'cancelled' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                    default => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                };
                            @endphp
                            <tr>
                                <td class="fw-bold text-muted small">#{{ $order->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="fw-bold text-dark text-decoration-none hover-primary">
                                            {{ $order->title }}
                                        </a>
                                        @if($order->hasAttachment())
                                            <span class="badge bg-light text-muted border" title="{{ __('messages.order_attachment') }}">
                                                <i class="fa fa-paperclip"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-muted small mt-1">
                                        <span class="badge bg-light text-secondary border me-1">{{ $order->displayCategory() }}</span>
                                        <span class="fw-semibold text-dark">{{ $order->displayBudget() }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $order->user?->username ?? 'N/A' }}</div>
                                    <div class="text-muted small">{{ $order->user?->email ?? '' }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $statusBadgeClass }} px-2 py-1 rounded-pill fw-semibold">
                                        {{ $order->displayWorkflowStatus() }}
                                    </span>
                                    @if($order->contract && (int) $order->contract->revision_count > 0)
                                        <div class="mt-1">
                                            <span class="badge bg-warning text-dark small" style="font-size: 11px;">
                                                <i class="fa fa-redo fa-xs"></i> {{ __('messages.order_revision_badge', ['count' => $order->contract->revision_count]) }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 fw-bold">
                                        <i class="fa fa-handshake text-muted me-1"></i> {{ $order->offers_count }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        {{ $order->last_activity ? \Carbon\Carbon::createFromTimestamp($order->last_activity)->diffForHumans() : '-' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-light" title="{{ __('messages.view') }}">
                                            <i class="fa fa-eye"></i>
                                        </a>

                                        @if($order->workflow_status === \App\Models\OrderRequest::WORKFLOW_OPEN)
                                            <form action="{{ route('admin.orders.close', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm') }}');">
                                                @csrf
                                                <button type="submit" class="btn btn-light text-secondary" title="{{ __('messages.close_order') }}">
                                                    <i class="fa fa-lock"></i>
                                                </button>
                                            </form>
                                        @endif

                                        @if(!$order->isTerminal())
                                            <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.confirm') }}');">
                                                @csrf
                                                <button type="submit" class="btn btn-light text-warning" title="{{ __('messages.order_cancel_action') }}">
                                                    <i class="fa fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.order_confirm_delete') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light text-danger" title="{{ __('messages.delete') }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fa fa-inbox fa-3x mb-3 text-secondary opacity-50"></i>
                                    <div>{{ __('messages.no_orders_found') }}</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    {{ __('messages.showing') }} {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} {{ __('messages.of') }} {{ $orders->total() }}
                </div>
                <div>
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
