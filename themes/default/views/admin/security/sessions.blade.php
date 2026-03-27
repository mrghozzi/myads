@extends('theme::layouts.admin')

@section('title', __('messages.security_member_sessions_title'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.security_member_sessions_title') }}</h5>
        </div>
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
            <li class="breadcrumb-item">{{ __('messages.security_title') }}</li>
        </ul>
    </div>
</div>

@include('theme::admin.security.partials.nav')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if(!empty($upgradeNotice))
    @include('theme::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
@endif

<div class="card stretch stretch-full">
    <div class="card-header">
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between w-100">
            <h5 class="card-title mb-0">{{ __('messages.security_member_sessions_title') }}</h5>
            <form action="{{ route('admin.security.sessions') }}" method="GET" class="d-flex flex-wrap gap-2">
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
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
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
                            <td>
                                <div class="fw-semibold">{{ $session->user?->username ?? __('messages.not_available') }}</div>
                                <small class="text-muted">{{ $session->user?->email }}</small>
                            </td>
                            <td><code>{{ \Illuminate\Support\Str::limit($session->session_id, 24) }}</code></td>
                            <td>{{ $session->started_via }}</td>
                            <td>
                                <div>{{ $session->ip_address ?: __('messages.not_available') }}</div>
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($session->user_agent, 60) }}</small>
                            </td>
                            <td>{{ $statusLabel }}</td>
                            <td>{{ $session->last_seen_at?->diffForHumans() ?? __('messages.not_available') }}</td>
                            <td>
                                @if(!$session->revoked_at && !$session->ended_at)
                                    <form action="{{ route('admin.security.sessions.revoke', $session->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.security_session_revoke_confirm') }}');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('messages.security_revoke_session') }}</button>
                                    </form>
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
</div>
@endsection
