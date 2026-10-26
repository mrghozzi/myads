@extends('theme::layouts.master')

@section('title', $pageTitle ?? (__('support.support_tickets') . ' - ' . $siteName))

@section('content')
<div class="content-grid" style="padding-bottom: 80px;">
    <style>
        .tickets-hero {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-xl, 22px);
            padding: 32px 30px;
            margin-bottom: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            box-shadow: var(--myads-shadow-sm, 0 4px 16px rgba(0,0,0,0.03));
        }

        .tickets-hero-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            margin: 0 0 6px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tickets-hero-desc {
            font-size: 0.95rem;
            color: var(--myads-text-muted, #64748b);
            margin: 0;
        }

        .btn-create-ticket {
            background: linear-gradient(135deg, #615dfa 0%, #4e4ac8 100%);
            color: #ffffff !important;
            padding: 12px 26px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 6px 18px rgba(97, 93, 250, 0.35);
            transition: all 0.25s ease;
            white-space: nowrap;
        }

        .btn-create-ticket:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(97, 93, 250, 0.5);
            color: #ffffff;
        }

        /* Stats Cards */
        .ticket-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }

        .ticket-stat-card {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-lg, 18px);
            padding: 20px 22px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--myads-shadow-sm, 0 2px 8px rgba(0,0,0,0.02));
        }

        .ticket-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .ticket-stat-info h3 {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            margin: 0;
            line-height: 1.1;
        }

        .ticket-stat-info span {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--myads-text-muted, #64748b);
        }

        /* Filter Tabs */
        .tickets-filter-bar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            background: var(--myads-surface, #ffffff);
            padding: 10px 16px;
            border-radius: 16px;
            border: 1px solid var(--myads-surface-border, #e2e8f0);
        }

        .ticket-filter-tab {
            padding: 7px 18px;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--myads-text-muted, #64748b);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .ticket-filter-tab.active,
        .ticket-filter-tab:hover {
            background: var(--myads-primary, #615dfa);
            color: #ffffff !important;
        }

        /* Table Card */
        .tickets-table-card {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-xl, 20px);
            overflow: hidden;
            box-shadow: var(--myads-shadow-sm, 0 4px 16px rgba(0,0,0,0.03));
        }

        .tickets-table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
        }

        [dir="ltr"] .tickets-table {
            text-align: left;
        }

        .tickets-table th {
            background: rgba(97, 93, 250, 0.04);
            color: var(--myads-text-muted, #64748b);
            font-size: 0.82rem;
            font-weight: 700;
            padding: 14px 20px;
            border-bottom: 1px solid var(--myads-surface-border, #e2e8f0);
            text-transform: uppercase;
        }

        .tickets-table td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--myads-surface-border, #f1f5f9);
            font-size: 0.92rem;
            color: var(--myads-text-main, #334155);
            vertical-align: middle;
        }

        .tickets-table tr:last-child td {
            border-bottom: none;
        }

        .tickets-table tr:hover td {
            background: rgba(97, 93, 250, 0.02);
        }

        .ticket-num-badge {
            font-family: monospace;
            font-weight: 700;
            color: var(--myads-primary, #615dfa);
            background: rgba(97, 93, 250, 0.08);
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.82rem;
            display: inline-block;
        }

        .badge-status {
            font-size: 0.78rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-status-open {
            background: rgba(245, 158, 11, 0.12);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        .badge-status-answered {
            background: rgba(35, 210, 226, 0.12);
            color: #0891b2;
            border: 1px solid rgba(35, 210, 226, 0.25);
        }

        .badge-status-resolved {
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .badge-status-closed {
            background: rgba(100, 116, 139, 0.12);
            color: #64748b;
            border: 1px solid rgba(100, 116, 139, 0.25);
        }

        .badge-priority {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
        }

        .priority-urgent { background: rgba(239, 68, 68, 0.12); color: #dc2626; }
        .priority-high { background: rgba(249, 115, 22, 0.12); color: #ea580c; }
        .priority-medium { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
        .priority-low { background: rgba(107, 114, 128, 0.12); color: #4b5563; }

        .btn-view-ticket {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #cbd5e1);
            color: var(--myads-text-main, #1e293b);
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-view-ticket:hover {
            border-color: var(--myads-primary, #615dfa);
            color: var(--myads-primary, #615dfa);
        }

        .crumbs {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.88rem;
            color: var(--myads-text-muted, #64748b);
            margin-bottom: 20px;
        }

        .crumbs a {
            color: var(--myads-primary, #615dfa);
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .tickets-table-card { overflow-x: auto; }
            .tickets-hero { flex-direction: column; align-items: flex-start; }
            .btn-create-ticket { width: 100%; justify-content: center; }
        }
    </style>

    <!-- Breadcrumb -->
    <div class="crumbs">
        <a href="{{ route('help.index') }}"><i class="fa-solid fa-house"></i> {{ __('help.help_center') }}</a>
        <i class="fa-solid {{ app()->getLocale() === 'ar' ? 'fa-chevron-left' : 'fa-chevron-right' }}" style="font-size: 0.75rem;"></i>
        <span>{{ __('support.support_tickets') }}</span>
    </div>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #065f46; border-radius: 14px; padding: 14px 20px; margin-bottom: 22px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check" style="font-size: 1.2rem; color: #10b981;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Hero Card -->
    <div class="tickets-hero">
        <div>
            <h1 class="tickets-hero-title">
                <i class="fa-solid fa-ticket" style="color: var(--myads-primary, #615dfa);"></i>
                {{ __('support.my_tickets') }}
            </h1>
            <p class="tickets-hero-desc">
                {{ __('support.tickets_desc') }}
            </p>
        </div>
        <a href="{{ route('tickets.create') }}" class="btn-create-ticket">
            <i class="fa-solid fa-plus-circle"></i>
            <span>{{ __('support.open_new_ticket') }}</span>
        </a>
    </div>

    <!-- Stats Row -->
    <div class="ticket-stats-grid">
        <div class="ticket-stat-card">
            <div class="ticket-stat-icon" style="background: rgba(97, 93, 250, 0.1); color: #615dfa;">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div class="ticket-stat-info">
                <h3>{{ $stats['total'] }}</h3>
                <span>{{ __('support.total_tickets') }}</span>
            </div>
        </div>

        <div class="ticket-stat-card">
            <div class="ticket-stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="ticket-stat-info">
                <h3>{{ $stats['open'] }}</h3>
                <span>{{ __('support.open_tickets') }}</span>
            </div>
        </div>

        <div class="ticket-stat-card">
            <div class="ticket-stat-icon" style="background: rgba(35, 210, 226, 0.1); color: #0891b2;">
                <i class="fa-solid fa-reply-all"></i>
            </div>
            <div class="ticket-stat-info">
                <h3>{{ $stats['answered'] }}</h3>
                <span>{{ __('support.answered_tickets') }}</span>
            </div>
        </div>

        <div class="ticket-stat-card">
            <div class="ticket-stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="ticket-stat-info">
                <h3>{{ $stats['closed'] }}</h3>
                <span>{{ __('support.resolved_tickets') }}</span>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="tickets-filter-bar">
        <a href="{{ route('tickets.index', ['status' => 'all']) }}" class="ticket-filter-tab {{ $filter === 'all' ? 'active' : '' }}">
            {{ __('support.all_topics') }}
        </a>
        <a href="{{ route('tickets.index', ['status' => 'open']) }}" class="ticket-filter-tab {{ $filter === 'open' ? 'active' : '' }}">
            {{ __('support.open_tickets') }} ({{ $stats['open'] }})
        </a>
        <a href="{{ route('tickets.index', ['status' => 'answered']) }}" class="ticket-filter-tab {{ $filter === 'answered' ? 'active' : '' }}">
            {{ __('support.answered_tickets') }} ({{ $stats['answered'] }})
        </a>
        <a href="{{ route('tickets.index', ['status' => 'closed']) }}" class="ticket-filter-tab {{ $filter === 'closed' ? 'active' : '' }}">
            {{ __('support.resolved_tickets') }} ({{ $stats['closed'] }})
        </a>
    </div>

    <!-- Tickets Table -->
    <div class="tickets-table-card">
        @if($tickets->count() > 0)
            <table class="tickets-table">
                <thead>
                    <tr>
                        <th>{{ __('support.ticket_number') }}</th>
                        <th>{{ __('support.subject') }}</th>
                        <th>{{ __('support.department') }}</th>
                        <th>{{ __('support.priority') }}</th>
                        <th>{{ __('support.status') }}</th>
                        <th>{{ __('support.last_update') }}</th>
                        <th>{{ __('support.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        <tr>
                            <td>
                                <span class="ticket-num-badge">{{ $ticket->ticket_number }}</span>
                            </td>
                            <td>
                                <a href="{{ route('tickets.show', $ticket->ticket_number) }}" style="font-weight: 700; color: var(--myads-text-main, #1e293b); text-decoration: none;">
                                    {{ $ticket->subject }}
                                </a>
                            </td>
                            <td>
                                <span style="font-size: 0.85rem; color: var(--myads-text-muted, #64748b);">
                                    {{ __('support.dept_' . $ticket->department) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-priority priority-{{ $ticket->priority }}">
                                    {{ __('support.priority_' . $ticket->priority) }}
                                </span>
                            </td>
                            <td>
                                @if(in_array($ticket->status, ['open', 'in_progress']))
                                    <span class="badge-status badge-status-open">
                                        <i class="fa-solid fa-spinner fa-spin"></i> {{ __('support.status_' . $ticket->status) }}
                                    </span>
                                @elseif($ticket->status === 'answered')
                                    <span class="badge-status badge-status-answered">
                                        <i class="fa-solid fa-reply"></i> {{ __('support.status_answered') }}
                                    </span>
                                @elseif($ticket->status === 'resolved')
                                    <span class="badge-status badge-status-resolved">
                                        <i class="fa-solid fa-check-circle"></i> {{ __('support.status_resolved') }}
                                    </span>
                                @else
                                    <span class="badge-status badge-status-closed">
                                        <i class="fa-solid fa-lock"></i> {{ __('support.status_closed') }}
                                    </span>
                                @endif
                            </td>
                            <td style="font-size: 0.82rem; color: var(--myads-text-muted, #64748b);">
                                {{ $ticket->updated_at ? $ticket->updated_at->diffForHumans() : $ticket->created_at->diffForHumans() }}
                            </td>
                            <td>
                                <a href="{{ route('tickets.show', $ticket->ticket_number) }}" class="btn-view-ticket">
                                    <span>{{ __('support.view_ticket') }}</span>
                                    <i class="fa-solid {{ app()->getLocale() === 'ar' ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="padding: 16px 20px;">
                {{ $tickets->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 60px 20px; color: var(--myads-text-muted, #64748b);">
                <i class="fa-solid fa-ticket-simple" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 14px;"></i>
                <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--myads-text-main, #1e293b); margin-bottom: 8px;">
                    {{ __('support.no_tickets_found') }}
                </h3>
                <p style="margin-bottom: 20px; font-size: 0.9rem;">
                    {{ __('support.no_tickets_desc') }}
                </p>
                <a href="{{ route('tickets.create') }}" class="btn-create-ticket">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>{{ __('support.open_new_ticket') }}</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
