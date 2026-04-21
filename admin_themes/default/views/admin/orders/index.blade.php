@extends('admin::layouts.admin')

@section('title', __('messages.order_requests'))

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
        <div>
            <h3 class="mb-1">{{ __('messages.order_requests') }}</h3>
            <p class="text-muted mb-0">{{ __('messages.admin_orders_subtitle') }}</p>
        </div>
    </div>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-lg-5">
            <input type="text" class="form-control" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="{{ __('messages.order_search_placeholder') }}">
        </div>
        <div class="col-lg-3">
            <select class="form-select" name="status">
                <option value="all">{{ __('messages.all') }}</option>
                @foreach(['open', 'awarded', 'in_progress', 'delivered', 'completed', 'closed', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? 'all') === $status)>{{ __('messages.order_status_' . $status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2">
            <input type="text" class="form-control" name="category" value="{{ $filters['category'] ?? '' }}" placeholder="{{ __('messages.category') }}">
        </div>
        <div class="col-lg-2">
            <button type="submit" class="btn btn-primary w-100">{{ __('messages.filter') }}</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>{{ __('messages.title') }}</th>
                    <th>{{ __('messages.client_info') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.offers') }}</th>
                    <th>{{ __('messages.last_activity') }}</th>
                    <th>{{ __('messages.options') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $order->title }}</div>
                            <div class="text-muted small">{{ $order->displayCategory() }} | {{ $order->displayBudget() }}</div>
                        </td>
                        <td>
                            <div>{{ $order->user->username }}</div>
                            <div class="text-muted small">#{{ $order->id }}</div>
                        </td>
                        <td>{{ $order->displayWorkflowStatus() }}</td>
                        <td>{{ $order->offers_count }}</td>
                        <td>{{ $order->last_activity ? \Carbon\Carbon::createFromTimestamp($order->last_activity)->diffForHumans() : '-' }}</td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-light">{{ __('messages.view') }}</a>
                                @if($order->workflow_status === \App\Models\OrderRequest::WORKFLOW_OPEN)
                                    <form action="{{ route('admin.orders.close', $order) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('messages.close_order') }}</button>
                                    </form>
                                @endif
                                @if(!$order->isTerminal())
                                    <form action="{{ route('admin.orders.cancel', $order) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('messages.order_cancel_action') }}</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">{{ __('messages.no_orders_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
    </div>
</div>
@endsection
