@extends('admin::layouts.admin')

@section('title', __('support.admin_support_management'))

@section('content')
<div class="main-content container-lg px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="feather-headphones text-primary me-2"></i> {{ __('support.admin_support_management') }}
            </h3>
            <p class="text-muted mb-0">{{ __('support.admin_tickets_list') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.support.forum.categories') }}" class="btn btn-outline-primary">
                <i class="feather-folder me-1"></i> {{ __('support.admin_manage_categories') }}
            </a>
            <a href="{{ route('admin.support.forum.topics') }}" class="btn btn-outline-secondary">
                <i class="feather-message-circle me-1"></i> {{ __('support.admin_forum_management') }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="feather-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3 fs-3">
                        <i class="feather-inbox"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">{{ __('support.total_tickets') }}</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3 fs-3">
                        <i class="feather-clock"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">{{ __('support.open_tickets') }}</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $stats['open'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-info bg-opacity-10 text-info p-3 fs-3">
                        <i class="feather-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">{{ __('support.status_answered') }}</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $stats['answered'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-success bg-opacity-10 text-success p-3 fs-3">
                        <i class="feather-check-circle"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">{{ __('support.resolved_tickets') }}</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $stats['closed'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.tickets.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="{{ __('support.search_tickets_placeholder') }}">
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">{{ __('support.all_statuses') }}</option>
                        <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>{{ __('support.status_open') }}</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>{{ __('support.status_in_progress') }}</option>
                        <option value="answered" {{ request('status') === 'answered' ? 'selected' : '' }}>{{ __('support.status_answered') }}</option>
                        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>{{ __('support.status_resolved') }}</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>{{ __('support.status_closed') }}</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="department" class="form-select">
                        <option value="">{{ __('support.all_departments') }}</option>
                        <option value="technical" {{ request('department') === 'technical' ? 'selected' : '' }}>{{ __('support.dept_technical') }}</option>
                        <option value="billing" {{ request('department') === 'billing' ? 'selected' : '' }}>{{ __('support.dept_billing') }}</option>
                        <option value="account" {{ request('department') === 'account' ? 'selected' : '' }}>{{ __('support.dept_account') }}</option>
                        <option value="general" {{ request('department') === 'general' ? 'selected' : '' }}>{{ __('support.dept_general') }}</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="priority" class="form-select">
                        <option value="">{{ __('support.all_priorities') }}</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>{{ __('support.priority_urgent') }}</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>{{ __('support.priority_high') }}</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>{{ __('support.priority_medium') }}</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>{{ __('support.priority_low') }}</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="feather-filter me-1"></i> {{ __('support.filter') }}
                    </button>
                    @if(request()->hasAny(['search', 'status', 'department', 'priority']))
                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary" title="{{ __('support.clear_filter') }}">
                            <i class="feather-x"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">{{ __('support.ticket_number') }}</th>
                        <th>{{ __('support.member') }}</th>
                        <th>{{ __('support.subject') }}</th>
                        <th>{{ __('support.department') }}</th>
                        <th>{{ __('support.priority') }}</th>
                        <th>{{ __('support.status') }}</th>
                        <th>{{ __('support.last_update') }}</th>
                        <th class="text-end pe-4">{{ __('support.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $t)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-primary font-monospace fw-bold fs-7">
                                    #{{ $t->ticket_number }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ !empty($t->user->img) ? theme_asset('img/' . $t->user->img) : theme_asset('img/avatar.png') }}"
                                         alt="{{ $t->user->username ?? 'User' }}"
                                         class="rounded-circle"
                                         width="32" height="32" style="object-fit:cover;">
                                    <div>
                                        <div class="fw-semibold text-dark fs-7">{{ $t->user->username ?? 'User' }}</div>
                                        <div class="text-muted small" style="font-size:0.75rem;">ID: {{ $t->user_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.tickets.show', $t->id) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $t->subject }}
                                </a>
                                <div class="text-muted small">
                                    <i class="feather-message-square me-1"></i> {{ $t->messages->count() }} {{ __('support.messages') }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">
                                    {{ __('support.dept_' . $t->department) }}
                                </span>
                            </td>
                            <td>
                                @if($t->priority === 'urgent')
                                    <span class="badge bg-danger">{{ __('support.priority_urgent') }}</span>
                                @elseif($t->priority === 'high')
                                    <span class="badge bg-warning text-dark">{{ __('support.priority_high') }}</span>
                                @elseif($t->priority === 'medium')
                                    <span class="badge bg-info text-dark">{{ __('support.priority_medium') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('support.priority_low') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($t->status === 'open')
                                    <span class="badge bg-warning text-dark"><i class="feather-clock me-1"></i> {{ __('support.status_open') }}</span>
                                @elseif($t->status === 'in_progress')
                                    <span class="badge bg-primary"><i class="feather-loader me-1"></i> {{ __('support.status_in_progress') }}</span>
                                @elseif($t->status === 'answered')
                                    <span class="badge bg-info text-dark"><i class="feather-check me-1"></i> {{ __('support.status_answered') }}</span>
                                @elseif($t->status === 'resolved')
                                    <span class="badge bg-success"><i class="feather-check-circle me-1"></i> {{ __('support.status_resolved') }}</span>
                                @else
                                    <span class="badge bg-secondary"><i class="feather-lock me-1"></i> {{ __('support.status_closed') }}</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ $t->updated_at ? $t->updated_at->diffForHumans() : $t->created_at->diffForHumans() }}
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <a href="{{ route('admin.tickets.show', $t->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('support.view_ticket') }}">
                                        <i class="feather-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.tickets.delete', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('support.confirm_delete_ticket') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('support.delete') }}">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="feather-inbox fs-1 d-block mb-2 text-secondary"></i>
                                {{ __('support.no_tickets_match') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="card-footer bg-white p-3 border-0">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
