@extends('admin::layouts.admin')

@section('title', __('messages.verification_requests'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb"><li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li><li>{{ __('messages.verification_requests') }}</li></ul>
            <div class="admin-hero__eyebrow">{{ __('messages.users') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.verification_requests') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.verification_admin_requests_intro') }}</p>
        </div>
    </section>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ __('messages.please_check_errors') }}</div>@endif

    <div class="row g-3 mb-4">
        @foreach(['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'] as $key => $color)
            <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body d-flex justify-content-between align-items-center"><span>{{ __('messages.verification_status_' . $key) }}</span><strong class="fs-4 text-{{ $color }}">{{ number_format($stats[$key]) }}</strong></div></div></div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-4"><div class="card-body d-flex flex-wrap gap-2 align-items-center justify-content-between">
        <div class="d-flex flex-wrap gap-2">
            @foreach(['pending', 'approved', 'rejected', 'all'] as $filter)
                <a href="{{ route('admin.profile_verification.requests', ['status' => $filter, 'search' => $search]) }}" class="btn {{ $status === $filter ? 'btn-primary' : 'btn-light border' }}">{{ __('messages.verification_status_' . $filter) }}</a>
            @endforeach
        </div>
        <form method="GET" action="{{ route('admin.profile_verification.requests') }}" class="d-flex gap-2">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="{{ __('messages.verification_search_members') }}" aria-label="{{ __('messages.verification_search_members') }}">
            <button class="btn btn-outline-primary">{{ __('messages.verification_search_button') }}</button>
        </form>
    </div></div>

    <div class="row g-4">
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm"><div class="card-body p-0 table-responsive">
                <table class="table align-middle mb-0">
                    <thead><tr><th>{{ __('messages.member') }}</th><th>{{ __('messages.date') }}</th><th>{{ __('messages.status') }}</th><th></th></tr></thead>
                    <tbody>
                    @forelse($requests as $item)
                        <tr>
                            <td><strong>{{ $item->user?->username ?? __('messages.deleted_user') }}</strong><div class="small text-muted">{{ __('messages.verification_account_age_value', ['days' => $item->user?->created_at?->diffInDays(now()) ?? 0]) }} · {{ __('messages.verification_followers') }}: {{ $item->user ? \App\Models\Like::where('sid', $item->user_id)->where('type', 1)->count() : 0 }}</div></td>
                            <td>{{ $item->created_at?->format('Y-m-d H:i') }}</td>
                            <td><span class="badge bg-{{ $item->status === 'approved' ? 'success' : ($item->status === 'rejected' ? 'danger' : 'warning text-dark') }}">{{ __('messages.verification_status_' . $item->status) }}</span></td>
                            <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.profile_verification.requests', ['status' => $status, 'request' => $item->id]) }}">{{ __('messages.review') }}</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-5">{{ __('messages.verification_no_requests') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="p-3">{{ $requests->links() }}</div>
            </div></div>
        </div>
        <div class="col-xl-5">
            @if($selectedRequest)
                <div class="card border-0 shadow-sm"><div class="card-body">
                    <h5 class="card-title mb-1">{{ $selectedRequest->user?->username ?? __('messages.deleted_user') }}</h5>
                    <p class="small text-muted">{{ $selectedRequest->user?->email }} · {{ $selectedRequest->created_at?->format('Y-m-d H:i') }}</p>
                    <h6>{{ __('messages.verification_reason') }}</h6><p style="white-space:pre-line">{{ $selectedRequest->reason }}</p>
                    <h6>{{ __('messages.verification_evidence_links') }}</h6>
                    @forelse($selectedRequest->evidence_links ?? [] as $link)<p><a href="{{ $link }}" target="_blank" rel="noopener noreferrer">{{ $link }}</a></p>@empty<p class="text-muted">{{ __('messages.verification_no_evidence') }}</p>@endforelse
                    @if($selectedRequest->status === 'pending')
                        <form method="POST" action="{{ route('admin.profile_verification.review', $selectedRequest) }}" class="mt-4">
                            @csrf
                            <label for="reviewer_note" class="form-label">{{ __('messages.verification_reviewer_note') }}</label>
                            <textarea id="reviewer_note" name="reviewer_note" rows="3" maxlength="3000" class="form-control mb-3">{{ old('reviewer_note', $selectedRequest->reviewer_note) }}</textarea>
                            <div class="d-flex flex-wrap gap-2">
                                <button name="decision" value="approved" class="btn btn-success"><i class="feather-check me-1"></i>{{ __('messages.verification_approve') }}</button>
                                <button name="decision" value="rejected" class="btn btn-danger"><i class="feather-x me-1"></i>{{ __('messages.verification_reject') }}</button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-light border mt-3"><strong>{{ __('messages.verification_reviewer_note') }}:</strong> {{ $selectedRequest->reviewer_note ?: __('messages.not_available') }}<br><small>{{ $selectedRequest->reviewed_at?->format('Y-m-d H:i') }}</small></div>
                    @endif
                </div></div>
            @else
                <div class="card border-0 shadow-sm"><div class="card-body text-center py-5 text-muted">{{ __('messages.verification_select_request') }}</div></div>
            @endif
        </div>
    </div>
</div>
@endsection
