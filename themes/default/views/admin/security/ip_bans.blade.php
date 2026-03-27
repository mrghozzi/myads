@extends('theme::layouts.admin')

@section('title', __('messages.security_ip_bans_title'))

@section('content')
<div class="page-header">
    <div class="page-header-left d-flex align-items-center">
        <div class="page-header-title">
            <h5 class="m-b-10">{{ __('messages.security_ip_bans_title') }}</h5>
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

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(!empty($upgradeNotice))
    @include('theme::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
@endif

<div class="row g-4">
    <div class="col-xl-4">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ __('messages.security_add_ip_ban') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.security.ip-bans.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.security_ip_address') }}</label>
                        <input type="text" name="ip_address" class="form-control" value="{{ old('ip_address') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.reason') }}</label>
                        <textarea name="reason" rows="4" class="form-control">{{ old('reason') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.security_ban_expires_at') }}</label>
                        <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', true))>
                        <label class="form-check-label" for="is_active">{{ __('messages.enabled') }}</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" @disabled(!$featureAvailable)>{{ __('messages.save_changes') }}</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between w-100">
                    <h5 class="card-title mb-0">{{ __('messages.security_ip_bans_title') }}</h5>
                    <form action="{{ route('admin.security.ip-bans') }}" method="GET" class="d-flex flex-wrap gap-2">
                        <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="{{ __('messages.search') }}">
                        <select name="status" class="form-select">
                            <option value="active" @selected($status === 'active')>{{ __('messages.security_filter_active') }}</option>
                            <option value="all" @selected($status === 'all')>{{ __('messages.all') }}</option>
                            <option value="expired" @selected($status === 'expired')>{{ __('messages.security_filter_expired') }}</option>
                            <option value="inactive" @selected($status === 'inactive')>{{ __('messages.security_filter_inactive') }}</option>
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
                                <th>{{ __('messages.security_ip_address') }}</th>
                                <th>{{ __('messages.reason') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.security_ban_expires_at') }}</th>
                                <th>{{ __('messages.administrator') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bans as $ban)
                                @php
                                    $isExpired = $ban->expires_at && $ban->expires_at->isPast();
                                    $statusLabel = !$ban->is_active
                                        ? __('messages.security_filter_inactive')
                                        : ($isExpired ? __('messages.security_filter_expired') : __('messages.security_filter_active'));
                                @endphp
                                <tr>
                                    <td><code>{{ $ban->ip_address }}</code></td>
                                    <td>{{ $ban->reason ?: __('messages.not_available') }}</td>
                                    <td>{{ $statusLabel }}</td>
                                    <td>{{ $ban->expires_at?->format('Y-m-d H:i') ?? __('messages.never') }}</td>
                                    <td>{{ $ban->bannedBy?->username ?? __('messages.not_available') }}</td>
                                    <td>
                                        <form action="{{ route('admin.security.ip-bans.delete', $ban->id) }}" method="POST" onsubmit="return confirm('{{ __('messages.security_ip_ban_delete_confirm') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('messages.delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">{{ __('messages.security_no_ip_bans') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $bans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
