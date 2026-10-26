@extends('admin::layouts.admin')

@section('title', '#' . $ticket->ticket_number . ' - ' . __('support.admin_support_management'))

@section('content')
<div class="main-content container-lg px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-light border text-muted">
                    <i class="feather-arrow-right"></i> العودة للتذاكر
                </a>
                <span class="badge bg-primary font-monospace fs-7">#{{ $ticket->ticket_number }}</span>
            </div>
            <h3 class="fw-bold mb-0 text-dark">{{ $ticket->subject }}</h3>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('admin.tickets.delete', $ticket->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه التذكرة؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="feather-trash-2 me-1"></i> حذف التذكرة
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="feather-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Conversation Column -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white p-4 border-0 pb-0">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="feather-message-square text-primary me-2"></i> {{ __('support.ticket_conversation') }}
                    </h5>
                </div>

                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        @foreach($ticket->messages as $msg)
                            <div class="p-4 rounded-4 {{ $msg->is_admin ? 'bg-primary bg-opacity-10 border border-primary border-opacity-25 ms-lg-4' : 'bg-light border me-lg-4' }}">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ !empty($msg->user->img) ? theme_asset('img/' . $msg->user->img) : theme_asset('img/avatar.png') }}"
                                             alt="{{ $msg->user->username ?? 'User' }}"
                                             class="rounded-circle" width="36" height="36" style="object-fit:cover;">
                                        <div>
                                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                <span>{{ $msg->user->username ?? 'User' }}</span>
                                                @if($msg->is_admin)
                                                    <span class="badge bg-primary text-white fs-8">
                                                        <i class="feather-shield me-1"></i> {{ __('support.staff_member') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary fs-8">صاحب التذكرة</span>
                                                @endif
                                            </div>
                                            <div class="text-muted small" style="font-size: 0.78rem;">
                                                {{ $msg->created_at->format('Y-m-d H:i') }} ({{ $msg->created_at->diffForHumans() }})
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-dark" style="white-space: pre-line; line-height: 1.7;">
                                    {{ $msg->message }}
                                </div>

                                @if($msg->attachment)
                                    <div class="mt-3 pt-2 border-top border-secondary border-opacity-10">
                                        <a href="{{ asset('storage/' . $msg->attachment) }}" target="_blank" class="btn btn-sm btn-light border">
                                            <i class="feather-paperclip me-1 text-primary"></i> عرض المرفق المرفوع
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Admin Reply Box -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white p-4 border-0 pb-0">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="feather-corner-down-left text-primary me-2"></i> {{ __('support.admin_reply_as_staff') }}
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('support.message') }}</label>
                            <textarea name="message" rows="5" class="form-control" placeholder="اكتب ردك الرسمي للعضو هنا..." required></textarea>
                        </div>

                        <div class="row g-3 align-items-center mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('support.admin_update_status') }}</label>
                                <select name="status" class="form-select">
                                    <option value="answered" selected>{{ __('support.status_answered') }}</option>
                                    <option value="in_progress">{{ __('support.status_in_progress') }}</option>
                                    <option value="resolved">{{ __('support.status_resolved') }}</option>
                                    <option value="closed">{{ __('support.status_closed') }}</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">{{ __('support.attachment') }}</label>
                                <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.zip">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary px-4 py-2 fw-bold">
                            <i class="feather-send me-1"></i> إرسال الرد وإشعار العضو
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Details Column -->
        <div class="col-lg-4">
            <!-- Ticket Info Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white p-4 border-0 pb-0">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="feather-info text-primary me-2"></i> {{ __('support.ticket_details') }}
                    </h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.tickets.status', $ticket->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold mb-1">{{ __('support.status') }}</label>
                            <select name="status" class="form-select">
                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>{{ __('support.status_open') }}</option>
                                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>{{ __('support.status_in_progress') }}</option>
                                <option value="answered" {{ $ticket->status === 'answered' ? 'selected' : '' }}>{{ __('support.status_answered') }}</option>
                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>{{ __('support.status_resolved') }}</option>
                                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>{{ __('support.status_closed') }}</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold mb-1">{{ __('support.priority') }}</label>
                            <select name="priority" class="form-select">
                                <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>{{ __('support.priority_urgent') }}</option>
                                <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>{{ __('support.priority_high') }}</option>
                                <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>{{ __('support.priority_medium') }}</option>
                                <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>{{ __('support.priority_low') }}</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-outline-primary btn-sm w-100 mb-3">
                            <i class="feather-save me-1"></i> حفظ وتحديث الحالة
                        </button>
                    </form>

                    <hr class="my-3">

                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small">
                        <li class="d-flex justify-content-between">
                            <span class="text-muted">{{ __('support.department') }}:</span>
                            <span class="fw-bold">{{ __('support.dept_' . $ticket->department) }}</span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span class="text-muted">{{ __('support.created_at') }}:</span>
                            <span class="fw-bold">{{ $ticket->created_at->format('Y-m-d H:i') }}</span>
                        </li>
                        @if($ticket->last_reply_at)
                            <li class="d-flex justify-content-between">
                                <span class="text-muted">{{ __('support.last_update') }}:</span>
                                <span class="fw-bold">{{ $ticket->last_reply_at->diffForHumans() }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Member Info Card -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white p-4 border-0 pb-0">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="feather-user text-primary me-2"></i> بيانات صاحب التذكرة
                    </h5>
                </div>

                <div class="card-body p-4 text-center">
                    <img src="{{ !empty($ticket->user->img) ? theme_asset('img/' . $ticket->user->img) : theme_asset('img/avatar.png') }}"
                         alt="{{ $ticket->user->username ?? 'User' }}"
                         class="rounded-circle mb-3 shadow-sm" width="70" height="70" style="object-fit:cover;">
                    <h5 class="fw-bold mb-1 text-dark">{{ $ticket->user->username ?? 'User' }}</h5>
                    <p class="text-muted small mb-3">{{ $ticket->user->email ?? 'No email' }}</p>

                    <div class="d-flex justify-content-around bg-light rounded-3 p-3 mb-3 text-center">
                        <div>
                            <div class="text-muted small">رصيد النقاط</div>
                            <div class="fw-bold text-primary">{{ number_format($ticket->user->pts ?? 0) }}</div>
                        </div>
                        <div class="border-end"></div>
                        <div>
                            <div class="text-muted small">المشاهدات</div>
                            <div class="fw-bold text-success">{{ number_format($ticket->user->vu ?? 0) }}</div>
                        </div>
                    </div>

                    @if(!empty($ticket->user->id))
                        <a href="{{ route('admin.users.edit', $ticket->user->id) }}" class="btn btn-outline-secondary btn-sm w-100" target="_blank">
                            <i class="feather-external-link me-1"></i> ملف العضو في لوحة الإدارة
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
