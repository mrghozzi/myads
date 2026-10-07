@extends('admin::layouts.admin')

@section('title', $order->title)

@section('content')
<div class="container-fluid px-0">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-light border text-muted">
                    <i class="fa fa-arrow-right"></i> {{ __('messages.back') }}
                </a>
                <span class="badge bg-secondary-subtle text-secondary">#{{ $order->id }}</span>
                <span class="badge bg-primary-subtle text-primary">{{ $order->displayCategory() }}</span>
                @php
                    $statusBadgeClass = match($order->workflow_status) {
                        'open' => 'bg-primary text-white',
                        'awarded' => 'bg-info text-white',
                        'in_progress' => 'bg-warning text-dark',
                        'delivered' => 'bg-purple text-white',
                        'completed' => 'bg-success text-white',
                        'cancelled' => 'bg-danger text-white',
                        default => 'bg-secondary text-white',
                    };
                @endphp
                <span class="badge {{ $statusBadgeClass }}">{{ $order->displayWorkflowStatus() }}</span>
            </div>
            <h3 class="mb-0 fw-bold text-dark">{{ $order->title }}</h3>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('orders.show', $order) }}" target="_blank" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
                <i class="fa fa-external-link-alt"></i>
                <span>{{ __('messages.view_details') }}</span>
            </a>

            @if($order->workflow_status === \App\Models\OrderRequest::WORKFLOW_OPEN)
                <form action="{{ route('admin.orders.close', $order) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm') }}');">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-lock"></i> {{ __('messages.close_order') }}
                    </button>
                </form>
            @endif

            @if(!$order->isTerminal())
                <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('{{ __('messages.confirm') }}');">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning btn-sm">
                        <i class="fa fa-ban"></i> {{ __('messages.order_cancel_action') }}
                    </button>
                </form>
            @endif

            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('{{ __('messages.order_confirm_delete') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="fa fa-trash"></i> {{ __('messages.delete') }}
                </button>
            </form>
        </div>
    </div>

    {{-- Disclaimer Reminder --}}
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-start gap-3 mb-4 rounded-3" style="background: rgba(13, 110, 253, 0.08);">
        <div class="fs-5 text-primary mt-1"><i class="fa fa-shield-alt"></i></div>
        <div>
            <div class="fw-bold text-primary mb-1">{{ __('messages.order_disclaimer_title') }}</div>
            <div class="small text-muted">{{ __('messages.order_disclaimer_notice', ['site' => $site_settings->titer ?? config('app.name', 'MyAds')]) }}</div>
        </div>
    </div>

    {{-- Summary Info Cards --}}
    <div class="row g-3 mb-4">
        {{-- Client --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase mb-2">
                        <i class="fa fa-user me-1 text-primary"></i> {{ __('messages.client_info') }}
                    </div>
                    <div class="fw-bold fs-6 text-dark">{{ $order->user?->username ?? 'N/A' }}</div>
                    <div class="small text-muted">{{ $order->user?->email ?? '-' }}</div>
                    @if($order->user)
                        <div class="mt-2">
                            <a href="{{ route('profile.show', $order->user->username) }}" target="_blank" class="small text-primary text-decoration-none">
                                <i class="fa fa-external-link-alt fa-xs"></i> {{ __('messages.member_profile') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Provider (if awarded) --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase mb-2">
                        <i class="fa fa-user-check me-1 text-success"></i> {{ __('messages.order_contract_provider') }}
                    </div>
                    @if($order->contract && $order->contract->provider)
                        <div class="fw-bold fs-6 text-dark">{{ $order->contract->provider->username }}</div>
                        <div class="small text-muted">{{ $order->contract->provider->email }}</div>
                        <div class="mt-2 small text-dark fw-semibold">
                            {{ $order->contract->currency_code }} {{ number_format((float) $order->contract->quoted_amount, 2) }}
                        </div>
                    @else
                        <div class="text-muted small py-2">{{ __('messages.order_no_offers_title') }}</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Budget & Delivery --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase mb-2">
                        <i class="fa fa-money-bill-wave me-1 text-info"></i> {{ __('messages.budget') }} & {{ __('messages.delivery') }}
                    </div>
                    <div class="fw-bold fs-6 text-dark">{{ $order->displayBudget() }}</div>
                    <div class="small text-muted">{{ __('messages.delivery') }}: {{ $order->displayDeliveryWindow() }}</div>
                    @if($order->contract && $order->contract->deadline)
                        <div class="mt-2 small">
                            <span class="text-muted">{{ __('messages.order_deadline') }}:</span>
                            <span class="fw-bold {{ $order->contract->isOverdue() ? 'text-danger' : 'text-success' }}">
                                {{ $order->contract->deadline->format('Y-m-d') }}
                                @if($order->contract->isOverdue())
                                    ({{ __('messages.order_overdue') }})
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Offers & Ratings --}}
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="text-muted small fw-semibold text-uppercase mb-2">
                        <i class="fa fa-star me-1 text-warning"></i> {{ __('messages.rating') }} & {{ __('messages.offers') }}
                    </div>
                    <div class="fw-bold fs-6 text-dark">{{ $order->offers_count }} {{ __('messages.offers') }}</div>
                    <div class="small text-muted">
                        @if((float) $order->avg_rating > 0)
                            <span class="text-warning"><i class="fa fa-star"></i></span>
                            <span class="fw-bold text-dark">{{ number_format((float) $order->avg_rating, 1) }}/5</span>
                        @else
                            <span>{{ __('messages.rating') }}: -</span>
                        @endif
                    </div>
                    @if($order->contract && (int) $order->contract->revision_count > 0)
                        <div class="mt-2">
                            <span class="badge bg-warning text-dark small">
                                <i class="fa fa-redo fa-xs"></i> {{ __('messages.order_revision_badge', ['count' => $order->contract->revision_count]) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Description & Attachments --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-dark">{{ __('messages.description') }}</h5>
                </div>
                <div class="card-body p-4">
                    <p class="mb-0 text-dark" style="white-space: pre-line; line-height: 1.7;">{{ $order->description }}</p>

                    @if($order->hasAttachment())
                        <div class="mt-4 p-3 bg-light rounded-3 d-flex align-items-center justify-content-between gap-3 flex-wrap">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa fa-paperclip text-primary fs-4"></i>
                                <div>
                                    <div class="fw-bold text-dark small">{{ $order->attachment_name ?: __('messages.order_attachment') }}</div>
                                    <div class="text-muted small" style="font-size: 11px;">{{ __('messages.order_attachment') }}</div>
                                </div>
                            </div>
                            <a href="{{ route('orders.attachment.download', $order) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                <i class="fa fa-download"></i> {{ __('messages.order_download_attachment') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Contract & Deliverable Details --}}
            @if($order->contract)
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark">{{ __('messages.order_workroom_title') }}</h5>
                        <span class="badge bg-secondary-subtle text-secondary">{{ $order->contract->displayStatus() }}</span>
                    </div>
                    <div class="card-body p-4">
                        @if($order->contract->delivery_note)
                            <div class="mb-3 p-3 bg-light rounded-3">
                                <div class="fw-bold text-primary small mb-1">
                                    <i class="fa fa-box-open me-1"></i> {{ __('messages.notes') }} ({{ __('messages.delivery') }}):
                                </div>
                                <p class="mb-0 text-dark small" style="white-space: pre-line;">{{ $order->contract->delivery_note }}</p>
                            </div>
                        @endif

                        @if($order->contract->hasDeliveryAttachment())
                            <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between gap-3 flex-wrap mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fa fa-file-archive text-success fs-4"></i>
                                    <div>
                                        <div class="fw-bold text-dark small">{{ $order->contract->delivery_attachment_name ?: __('messages.order_deliverable_attachment') }}</div>
                                        <div class="text-muted small" style="font-size: 11px;">{{ __('messages.order_deliverable_attachment') }}</div>
                                    </div>
                                </div>
                                <a href="{{ route('orders.deliverable.download', $order) }}" class="btn btn-success btn-sm d-flex align-items-center gap-1">
                                    <i class="fa fa-download"></i> {{ __('messages.order_download_deliverable') }}
                                </a>
                            </div>
                        @endif

                        @if($order->contract->hasRevision())
                            <div class="p-3 bg-warning-subtle text-warning-emphasis rounded-3">
                                <div class="fw-bold small mb-1">
                                    <i class="fa fa-redo me-1"></i> {{ __('messages.order_revision_note') }} ({{ __('messages.order_revision_badge', ['count' => $order->contract->revision_count]) }}):
                                </div>
                                <p class="mb-0 small" style="white-space: pre-line;">{{ $order->contract->revision_note }}</p>
                            </div>
                        @endif

                        @if($order->contract->completion_note)
                            <div class="mt-3 p-3 bg-success-subtle text-success-emphasis rounded-3">
                                <div class="fw-bold small mb-1"><i class="fa fa-check-circle me-1"></i> {{ __('messages.review') }}:</div>
                                <p class="mb-0 small" style="white-space: pre-line;">{{ $order->contract->completion_note }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Side Controls: Admin Notes & Edit Form --}}
        <div class="col-lg-4">
            {{-- Admin Internal Notes --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fa fa-sticky-note text-warning me-1"></i> {{ __('messages.order_admin_notes') }}
                    </h6>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('admin.orders.admin_notes', $order) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <textarea name="admin_notes" class="form-control" rows="4" placeholder="{{ __('messages.order_admin_notes_placeholder') }}">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">
                            <i class="fa fa-save"></i> {{ __('messages.save') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Admin Quick Edit Form --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark">
                        <i class="fa fa-edit text-primary me-1"></i> {{ __('messages.order_edit_admin_title') }}
                    </h6>
                </div>
                <div class="card-body p-3">
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted mb-1">{{ __('messages.title') }}</label>
                            <input type="text" name="title" class="form-control form-control-sm" value="{{ old('title', $order->title) }}" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted mb-1">{{ __('messages.category') }}</label>
                            <select name="category" class="form-select form-select-sm">
                                @foreach($categories as $categoryOption)
                                    <option value="{{ $categoryOption->slug }}" @selected(old('category', $order->category) === $categoryOption->slug)>{{ $categoryOption->label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted mb-1">{{ __('messages.pricing') }}</label>
                            <select name="pricing_model" class="form-select form-select-sm">
                                @foreach(['fixed', 'range', 'negotiable'] as $model)
                                    <option value="{{ $model }}" @selected(old('pricing_model', $order->pricing_model) === $model)>{{ __('messages.order_pricing_model_' . $model) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted mb-1">Min</label>
                                <input type="number" step="0.01" name="budget_min" class="form-control form-control-sm" value="{{ old('budget_min', $order->budget_min) }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-muted mb-1">Max</label>
                                <input type="number" step="0.01" name="budget_max" class="form-control form-control-sm" value="{{ old('budget_max', $order->budget_max) }}">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold text-muted mb-1">{{ __('messages.currency') }}</label>
                            <select name="budget_currency" class="form-select form-select-sm">
                                @foreach(['USD' => 'USD', 'EUR' => 'EUR', 'GBP' => 'GBP', 'PTS' => 'PTS'] as $code => $label)
                                    <option value="{{ $code }}" @selected(old('budget_currency', $order->budget_currency) === $code)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">{{ __('messages.description') }}</label>
                            <textarea name="description" class="form-control form-control-sm" rows="3" required>{{ old('description', $order->description) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-secondary btn-sm w-100 fw-bold">
                            <i class="fa fa-check"></i> {{ __('messages.save_changes') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Offers Section with Moderation --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">{{ __('messages.order_offers_title') }} ({{ $order->offers->count() }})</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small text-muted">
                        <tr>
                            <th>{{ __('messages.member_profile') }}</th>
                            <th>{{ __('messages.price') }}</th>
                            <th>{{ __('messages.delivery') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.message') }}</th>
                            <th>{{ __('messages.date') }}</th>
                            <th class="text-end">{{ __('messages.options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->offers as $offer)
                            <tr class="{{ $offer->isAwarded() ? 'table-success-subtle' : '' }}">
                                <td>
                                    <div class="fw-bold text-dark">{{ $offer->user?->username ?? 'N/A' }}</div>
                                    <div class="text-muted small">{{ $offer->user?->email ?? '' }}</div>
                                </td>
                                <td class="fw-bold text-dark">{{ $offer->displayQuote() }}</td>
                                <td>{{ $offer->displayDelivery() }}</td>
                                <td>
                                    <span class="badge {{ $offer->isAwarded() ? 'bg-success' : 'bg-light text-dark border' }} px-2 py-1">
                                        {{ $offer->displayStatus() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-dark small" style="max-width: 320px; white-space: pre-wrap;">
                                        {{ \Illuminate\Support\Str::limit($offer->message, 180) }}
                                    </div>
                                    @if($offer->client_rating)
                                        <div class="mt-1 small text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa{{ $i <= $offer->client_rating ? 's' : 'r' }} fa-star"></i>
                                            @endfor
                                            @if($offer->client_review)
                                                <span class="text-muted ms-1">"{{ $offer->client_review }}"</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $offer->created_at?->diffForHumans() }}</span>
                                </td>
                                <td class="text-end">
                                    @if(!$offer->isAwarded())
                                        <form action="{{ route('admin.orders.offers.destroy', $offer) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('messages.order_confirm_delete_offer') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger" title="{{ __('messages.delete') }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-success small"><i class="fa fa-check"></i> {{ __('messages.order_offer_status_awarded') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    {{ __('messages.order_no_offers_title') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
