@extends('theme::layouts.master')

@section('title', $pageTitle ?? ('#' . $ticket->ticket_number . ' - ' . $siteName))

@section('content')
<div class="content-grid" style="padding-bottom: 80px;">
    <style>
        .ticket-view-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 28px;
            align-items: start;
        }

        .ticket-thread-card {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-xl, 22px);
            padding: 30px;
            margin-bottom: 24px;
            box-shadow: var(--myads-shadow-sm, 0 4px 16px rgba(0,0,0,0.03));
        }

        .ticket-thread-header {
            margin-bottom: 22px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--myads-surface-border, #f1f5f9);
        }

        .ticket-num-badge {
            font-family: monospace;
            font-weight: 700;
            color: var(--myads-primary, #615dfa);
            background: rgba(97, 93, 250, 0.08);
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 10px;
        }

        .ticket-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            margin: 0 0 14px 0;
            line-height: 1.35;
            word-break: break-word;
        }

        .ticket-tags-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .badge-status {
            font-size: 0.8rem;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-status-open { background: rgba(245, 158, 11, 0.12); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.25); }
        .badge-status-answered { background: rgba(35, 210, 226, 0.12); color: #0891b2; border: 1px solid rgba(35, 210, 226, 0.25); }
        .badge-status-resolved { background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25); }
        .badge-status-closed { background: rgba(100, 116, 139, 0.12); color: #64748b; border: 1px solid rgba(100, 116, 139, 0.25); }

        .badge-priority {
            font-size: 0.78rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .priority-urgent { background: rgba(239, 68, 68, 0.12); color: #dc2626; }
        .priority-high { background: rgba(249, 115, 22, 0.12); color: #ea580c; }
        .priority-medium { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
        .priority-low { background: rgba(107, 114, 128, 0.12); color: #4b5563; }

        /* Messages */
        .ticket-message-item {
            border-radius: 18px;
            padding: 24px;
            margin-bottom: 18px;
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            background: var(--myads-surface, #ffffff);
            transition: all 0.2s ease;
        }

        .ticket-message-item.is-admin-msg {
            border-color: var(--myads-primary, #615dfa);
            background: linear-gradient(135deg, rgba(97, 93, 250, 0.04) 0%, rgba(35, 210, 226, 0.02) 100%);
            box-shadow: 0 4px 14px rgba(97, 93, 250, 0.05);
        }

        .ticket-msg-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .ticket-msg-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ticket-msg-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--myads-surface-border, #e2e8f0);
        }

        .badge-staff {
            background: linear-gradient(135deg, #615dfa 0%, #4e4ac8 100%);
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .ticket-msg-body {
            font-size: 0.98rem;
            line-height: 1.75;
            color: var(--myads-text-main, #334155);
            white-space: pre-line;
            word-break: break-word;
        }

        .ticket-attachment-box {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px dashed var(--myads-surface-border, #e2e8f0);
        }

        .attachment-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(97, 93, 250, 0.08);
            color: var(--myads-primary, #615dfa);
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .attachment-link:hover {
            background: var(--myads-primary, #615dfa);
            color: #ffffff;
        }

        /* Reply Box */
        .ticket-reply-card {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-xl, 20px);
            padding: 26px;
            margin-top: 24px;
        }

        .ticket-textarea {
            width: 100%;
            min-height: 140px;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid var(--myads-surface-border, #cbd5e1);
            background: var(--myads-surface, #ffffff);
            font-size: 0.95rem;
            outline: none;
            resize: vertical;
            margin-bottom: 16px;
        }

        .ticket-textarea:focus {
            border-color: var(--myads-primary, #615dfa);
            box-shadow: 0 0 0 3px rgba(97, 93, 250, 0.15);
        }

        .btn-ticket-reply {
            background: linear-gradient(135deg, #615dfa 0%, #4e4ac8 100%);
            color: #ffffff;
            border: none;
            padding: 12px 28px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(97, 93, 250, 0.35);
        }

        /* Sidebar Info */
        .ticket-info-card {
            background: var(--myads-surface, #ffffff);
            border: 1px solid var(--myads-surface-border, #e2e8f0);
            border-radius: var(--myads-radius-lg, 18px);
            padding: 24px;
            margin-bottom: 20px;
        }

        .ticket-info-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--myads-text-main, #1e293b);
            margin: 0 0 16px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ticket-info-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .ticket-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.88rem;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--myads-surface-border, #f1f5f9);
        }

        .ticket-info-label {
            color: var(--myads-text-muted, #64748b);
            font-weight: 600;
        }

        .ticket-info-value {
            color: var(--myads-text-main, #1e293b);
            font-weight: 700;
        }

        .btn-close-ticket {
            width: 100%;
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.25);
            padding: 10px 18px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.88rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            margin-top: 14px;
        }

        .btn-close-ticket:hover {
            background: #dc2626;
            color: #ffffff;
        }

        .crumbs {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.88rem;
            color: var(--myads-text-muted, #64748b);
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .crumbs a {
            color: var(--myads-primary, #615dfa);
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 900px) {
            .ticket-view-layout { grid-template-columns: 1fr; }
        }
    </style>

    <!-- Breadcrumb -->
    <div class="crumbs">
        <a href="{{ route('tickets.index') }}"><i class="fa-solid fa-ticket"></i> {{ __('support.my_tickets') }}</a>
        <i class="fa-solid fa-chevron-left" style="font-size: 0.75rem;"></i>
        <span>#{{ $ticket->ticket_number }}</span>
    </div>

    @if(session('success'))
        <div style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); color: #065f46; border-radius: 14px; padding: 14px 20px; margin-bottom: 22px; font-weight: 700; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-check" style="font-size: 1.2rem; color: #10b981;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="ticket-view-layout">
        <div>
            <!-- Ticket Thread Header Card -->
            <div class="ticket-thread-card">
                <div class="ticket-thread-header">
                    <span class="ticket-num-badge">#{{ $ticket->ticket_number }}</span>
                    <h1 class="ticket-title">{{ $ticket->subject }}</h1>

                    <div class="ticket-tags-row">
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

                        <span class="badge-priority priority-{{ $ticket->priority }}">
                            {{ __('support.priority') }}: {{ __('support.priority_' . $ticket->priority) }}
                        </span>

                        <span style="font-size: 0.85rem; color: var(--myads-text-muted, #64748b); font-weight: 600;">
                            <i class="fa-solid fa-folder-open"></i> {{ __('support.dept_' . $ticket->department) }}
                        </span>
                    </div>
                </div>

                <!-- Messages Thread -->
                <div class="ticket-messages-stream">
                    @foreach($ticket->messages as $msg)
                        <div class="ticket-message-item {{ $msg->is_admin ? 'is-admin-msg' : '' }}">
                            <div class="ticket-msg-header">
                                <div class="ticket-msg-author">
                                    <img src="{{ !empty($msg->user->img) ? theme_asset('img/' . $msg->user->img) : theme_asset('img/avatar.png') }}"
                                         alt="{{ $msg->user->username ?? 'User' }}"
                                         class="ticket-msg-avatar">
                                    <div>
                                        <span style="font-weight: 700; color: var(--myads-text-main, #1e293b);">
                                            {{ $msg->user->username ?? 'User' }}
                                        </span>
                                        @if($msg->is_admin)
                                            <span class="badge-staff ms-2">
                                                <i class="fa-solid fa-headset"></i> {{ __('support.staff_member') }}
                                            </span>
                                        @endif
                                        <div style="font-size: 0.78rem; color: var(--myads-text-muted, #64748b);">
                                            {{ $msg->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="ticket-msg-body">
                                {{ $msg->message }}
                            </div>

                            @if($msg->attachment)
                                <div class="ticket-attachment-box">
                                    <a href="{{ asset('storage/' . $msg->attachment) }}" target="_blank" class="attachment-link">
                                        <i class="fa-solid fa-paperclip"></i>
                                        <span>عرض أو تحميل المرفق</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Reply Box -->
            @if(!$ticket->isClosed())
                <div class="ticket-reply-card">
                    <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0 0 14px 0; color: var(--myads-text-main, #1e293b);">
                        <i class="fa-solid fa-reply" style="color: var(--myads-primary, #615dfa);"></i>
                        {{ __('support.reply_to_ticket') }}
                    </h3>

                    <form action="{{ route('tickets.reply', $ticket->ticket_number) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <textarea name="message" class="ticket-textarea" placeholder="{{ __('support.write_reply') }}" required></textarea>

                        <div style="margin-bottom: 18px;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 6px; color: var(--myads-text-main, #1e293b);">
                                {{ __('support.attachment') }}
                            </label>
                            <input type="file" name="attachment" class="ticket-form-input" accept=".jpg,.jpeg,.png,.pdf,.zip" style="width: 100%; padding: 8px 12px; border-radius: 10px; border: 1px solid var(--myads-surface-border, #cbd5e1);">
                        </div>

                        <button type="submit" class="btn-ticket-reply">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>{{ __('support.send_reply') }}</span>
                        </button>
                    </form>
                </div>
            @else
                <div style="padding: 24px; background: rgba(100, 116, 139, 0.08); border: 1px solid rgba(100, 116, 139, 0.2); border-radius: 18px; text-align: center; color: var(--myads-text-main, #1e293b);">
                    <i class="fa-solid fa-lock" style="font-size: 1.8rem; color: #64748b; margin-bottom: 10px;"></i>
                    <h4 style="margin: 0 0 6px 0; font-weight: 700;">{{ __('support.ticket_closed') }}</h4>
                    <p style="font-size: 0.9rem; color: var(--myads-text-muted, #64748b); margin: 0 0 16px 0;">
                        {{ __('support.ticket_closed_desc') }}
                    </p>
                    <form action="{{ route('tickets.reply', $ticket->ticket_number) }}" method="POST">
                        @csrf
                        <input type="hidden" name="message" value="أرغب في إعادة فتح هذه التذكرة لمتابعة الاستفسار.">
                        <button type="submit" class="btn-ticket-reply">
                            <i class="fa-solid fa-lock-open"></i>
                            <span>{{ __('support.reopen_ticket') }}</span>
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div>
            <div class="ticket-info-card">
                <h4 class="ticket-info-title">
                    <i class="fa-solid fa-circle-info" style="color: var(--myads-primary, #615dfa);"></i>
                    {{ __('support.ticket_details') }}
                </h4>

                <div class="ticket-info-list">
                    <div class="ticket-info-row">
                        <span class="ticket-info-label">{{ __('support.ticket_number') }}</span>
                        <span class="ticket-info-value" style="font-family: monospace;">#{{ $ticket->ticket_number }}</span>
                    </div>

                    <div class="ticket-info-row">
                        <span class="ticket-info-label">{{ __('support.department') }}</span>
                        <span class="ticket-info-value">{{ __('support.dept_' . $ticket->department) }}</span>
                    </div>

                    <div class="ticket-info-row">
                        <span class="ticket-info-label">{{ __('support.priority') }}</span>
                        <span class="badge-priority priority-{{ $ticket->priority }}">{{ __('support.priority_' . $ticket->priority) }}</span>
                    </div>

                    <div class="ticket-info-row">
                        <span class="ticket-info-label">{{ __('support.status') }}</span>
                        <span class="ticket-info-value">{{ __('support.status_' . $ticket->status) }}</span>
                    </div>

                    <div class="ticket-info-row">
                        <span class="ticket-info-label">{{ __('support.created_at') }}</span>
                        <span class="ticket-info-value">{{ $ticket->created_at->format('Y-m-d H:i') }}</span>
                    </div>

                    @if($ticket->last_reply_at)
                        <div class="ticket-info-row">
                            <span class="ticket-info-label">{{ __('support.last_update') }}</span>
                            <span class="ticket-info-value">{{ $ticket->last_reply_at->diffForHumans() }}</span>
                        </div>
                    @endif
                </div>

                @if(!$ticket->isClosed())
                    <form action="{{ route('tickets.close', $ticket->ticket_number) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في إغلاق هذه التذكرة؟');">
                        @csrf
                        <button type="submit" class="btn-close-ticket">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ __('support.close_ticket') }}</span>
                        </button>
                    </form>
                @endif
            </div>

            <div class="ticket-info-card" style="background: linear-gradient(135deg, rgba(97, 93, 250, 0.08) 0%, rgba(35, 210, 226, 0.06) 100%);">
                <h4 class="ticket-info-title">
                    <i class="fa-solid fa-headset" style="color: var(--myads-primary, #615dfa);"></i>
                    مركز المساعدة والمنتدى
                </h4>
                <p style="font-size: 0.88rem; color: var(--myads-text-muted, #64748b); line-height: 1.5; margin-bottom: 14px;">
                    يمكنك أيضاً البحث في الشروحات التوثيقية أو طرح أسئلتك في منتدى الدعم العام.
                </p>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a href="{{ route('support.index') }}" style="color: var(--myads-primary, #615dfa); font-weight: 700; text-decoration: none; font-size: 0.88rem;">
                        <i class="fa-solid fa-comments"></i> منتدى الدعم الفني
                    </a>
                    <a href="{{ route('help.index') }}" style="color: var(--myads-primary, #615dfa); font-weight: 700; text-decoration: none; font-size: 0.88rem;">
                        <i class="fa-solid fa-book-bookmark"></i> مركز المساعدة والتوثيق
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
