@extends('admin::layouts.admin')

@section('title', __('messages.security_member_sessions_title'))
@section('admin_shell_header_mode', 'hidden')

@section('content')
<div class="admin-suite-shell">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li>{{ __('messages.security_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.security_title') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.security_member_sessions_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.security_filter_active') }} / {{ __('messages.security_revoke_session') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-summary-grid w-100">
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.security_member_sessions_title') }}</span>
                    <span class="admin-summary-value">{{ number_format($sessions->total()) }}</span>
                </div>
            </div>
        </div>
    </section>

    @include('admin::admin.security.partials.nav')

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(!empty($upgradeNotice))
        @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
    @endif

    <section class="admin-panel">
        <div class="admin-panel__header">
            <div class="admin-table-tools w-100">
                <div>
                    <span class="admin-panel__eyebrow">{{ __('messages.security_member_sessions_title') }}</span>
                </div>
                <form action="{{ route('admin.security.sessions') }}" method="GET" class="admin-toolbar-row">
                    <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="{{ __('messages.search') }}">
                    <select name="status" class="form-select">
                        <option value="active" @selected($status === 'active')>{{ __('messages.security_filter_active') }}</option>
                        <option value="all" @selected($status === 'all')>{{ __('messages.all') }}</option>
                        <option value="ended" @selected($status === 'ended')>{{ __('messages.security_filter_ended') }}</option>
                        <option value="revoked" @selected($status === 'revoked')>{{ __('messages.security_filter_revoked') }}</option>
                    </select>
                    <button type="submit" class="btn btn-light">{{ __('messages.filter') }}</button>
                </form>
            </div>
        </div>
        <div class="admin-panel__body">
            <div class="admin-table-wrap">
                <table class="table table-hover align-middle admin-table admin-table-cardify">
                    <thead>
                        <tr>
                            <th>{{ __('messages.users') }}</th>
                            <th>{{ __('messages.security_session_identifier') }}</th>
                            <th>{{ __('messages.security_session_source') }}</th>
                            <th>{{ __('messages.security_ip_address') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.security_last_seen') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                            @php
                                $statusLabel = $session->revoked_at
                                    ? __('messages.security_filter_revoked')
                                    : ($session->ended_at ? __('messages.security_filter_ended') : __('messages.security_filter_active'));
                            @endphp
                            <tr>
                                <td data-label="{{ __('messages.users') }}">
                                    <div class="fw-semibold">{{ $session->user?->username ?? __('messages.not_available') }}</div>
                                    <small class="text-muted">{{ $session->user?->email }}</small>
                                </td>
                                <td data-label="{{ __('messages.security_session_identifier') }}"><code>{{ \Illuminate\Support\Str::limit($session->session_id, 24) }}</code></td>
                                <td data-label="{{ __('messages.security_session_source') }}">{{ $session->started_via }}</td>
                                <td data-label="{{ __('messages.security_ip_address') }}">
                                    <div>{{ $session->ip_address ?: __('messages.not_available') }}</div>
                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($session->user_agent, 60) }}</small>
                                </td>
                                <td data-label="{{ __('messages.status') }}">{{ $statusLabel }}</td>
                                <td data-label="{{ __('messages.security_last_seen') }}">{{ $session->last_seen_at?->diffForHumans() ?? __('messages.not_available') }}</td>
                                <td data-label="{{ __('messages.actions') }}">
                                    @if(!$session->revoked_at && !$session->ended_at)
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger js-session-revoke"
                                            data-revoke-action="{{ route('admin.security.sessions.revoke', $session->id) }}"
                                            data-session-user="{{ $session->user?->username ?? __('messages.not_available') }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#sessionRevokeModal"
                                        >
                                            {{ __('messages.security_revoke_session') }}
                                        </button>
                                    @else
                                        <span class="text-muted">{{ $session->revokedBy?->username ?? __('messages.not_available') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">{{ __('messages.security_no_sessions') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $sessions->links() }}
        </div>
    </section>
</div>
@endsection

@section('modals')
<div class="modal fade" id="sessionRevokeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="admin-modal-icon is-danger mx-auto mb-3"><i class="feather-slash"></i></div>
                <h3 class="h5 mb-2">{{ __('messages.security_revoke_session') }}</h3>
                <p class="text-muted mb-0" id="sessionRevokeLabel">{{ __('messages.security_session_revoke_confirm') }}</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <form action="" method="POST" id="sessionRevokeForm">
                    @csrf
                    <button type="submit" class="btn btn-danger">{{ __('messages.security_revoke_session') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const revokeForm = document.getElementById('sessionRevokeForm');
    const revokeLabel = document.getElementById('sessionRevokeLabel');

    document.querySelectorAll('.js-session-revoke').forEach(function (button) {
        button.addEventListener('click', function () {
            revokeForm.action = this.dataset.revokeAction;
            revokeLabel.textContent = "{{ __('messages.security_session_revoke_confirm') }}" + ' ' + (this.dataset.sessionUser || '');
        });
    });
});
</script>
@endpush
