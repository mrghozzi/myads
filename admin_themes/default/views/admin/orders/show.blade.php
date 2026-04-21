@extends('admin::layouts.admin')

@section('title', $order->title)

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
        <div>
            <h3 class="mb-1">{{ $order->title }}</h3>
            <p class="text-muted mb-0">{{ $order->displayWorkflowStatus() }} | {{ $order->displayBudget() }} | {{ $order->displayCategory() }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('orders.show', $order) }}" target="_blank" class="btn btn-light">{{ __('messages.view_details') }}</a>
            @if($order->workflow_status === \App\Models\OrderRequest::WORKFLOW_OPEN)
                <form action="{{ route('admin.orders.close', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary">{{ __('messages.close_order') }}</button>
                </form>
            @endif
            @if(!$order->isTerminal())
                <form action="{{ route('admin.orders.cancel', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">{{ __('messages.order_cancel_action') }}</button>
                </form>
            @endif
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 bg-light h-100">
                <div class="card-body">
                    <div class="text-muted text-uppercase small">{{ __('messages.client_info') }}</div>
                    <div class="fw-semibold fs-5 mt-2">{{ $order->user->username }}</div>
                    <div class="text-muted">{{ $order->user->email }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 bg-light h-100">
                <div class="card-body">
                    <div class="text-muted text-uppercase small">{{ __('messages.offers') }}</div>
                    <div class="fw-semibold fs-5 mt-2">{{ $order->offers_count }}</div>
                    <div class="text-muted">{{ __('messages.last_activity') }}: {{ $order->last_activity ? \Carbon\Carbon::createFromTimestamp($order->last_activity)->diffForHumans() : '-' }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 bg-light h-100">
                <div class="card-body">
                    <div class="text-muted text-uppercase small">{{ __('messages.delivery') }}</div>
                    <div class="fw-semibold fs-5 mt-2">{{ $order->displayDeliveryWindow() }}</div>
                    <div class="text-muted">{{ __('messages.rating') }}: {{ $order->avg_rating ? number_format((float) $order->avg_rating, 1) . '/5' : '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5>{{ __('messages.description') }}</h5>
            <p class="mb-0" style="white-space: pre-line;">{{ $order->description }}</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">{{ __('messages.order_offers_title') }}</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('messages.member_profile') }}</th>
                            <th>{{ __('messages.price') }}</th>
                            <th>{{ __('messages.delivery') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.message') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->offers as $offer)
                            <tr>
                                <td>{{ $offer->user->username }}</td>
                                <td>{{ $offer->displayQuote() }}</td>
                                <td>{{ $offer->displayDelivery() }}</td>
                                <td>{{ $offer->displayStatus() }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($offer->message, 140) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">{{ __('messages.order_no_offers_title') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
