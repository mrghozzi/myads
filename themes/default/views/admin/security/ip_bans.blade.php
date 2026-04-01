@extends('theme::layouts.admin')

@section('title', __('messages.security_ip_bans_title'))
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
            <h1 class="admin-hero__title">{{ __('messages.security_ip_bans_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.security_add_ip_ban') }} / {{ __('messages.security_filter_active') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-summary-grid w-100">
                <div class="admin-summary-card">
                    <span class="admin-summary-label">{{ __('messages.security_ip_bans_title') }}</span>
                    <span class="admin-summary-value">{{ number_format($bans->total()) }}</span>
                </div>
            </div>
        </div>
    </section>

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

    <div class="admin-workspace-grid">
        <section class="admin-panel">
            <div class="admin-panel__header">
                <div>
                    <span class="admin-panel__eyebrow">{{ __('messages.security_add_ip_ban') }}</span>
                    <h2 class="admin-panel__title">{{ __('messages.save_changes') }}</h2>
                </div>
            </div>
            <div class="admin-panel__body">
                <form action="{{ route('admin.security.ip-bans.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-12">
                        <label class="form-label">{{ __('messages.security_ip_address') }}</label>
                        <input type="text" name="ip_address" class="form-control" value="{{ old('ip_address') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('messages.reason') }}</label>
                        <textarea name="reason" rows="4" class="form-control">{{ old('reason') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('messages.security_ban_expires_at') }}</label>
                        <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', true))>
                            <label class="form-check-label" for="is_active">{{ __('messages.enabled') }}</label>
                        </div>
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" @disabled(!$featureAvailable)>{{ __('messages.save_changes') }}</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="admin-panel">
            <div class="admin-panel__header">
                <div class="admin-table-tools w-100">
                    <div>
                        <span class="admin-panel__eyebrow">{{ __('messages.security_ip_bans_title') }}</span>
                    </div>
                    <form action="{{ route('admin.security.ip-bans') }}" method="GET" class="admin-toolbar-row">
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
            <div class="admin-panel__body">
                <div class="admin-table-wrap">
                    <table class="table table-hover align-middle admin-table admin-table-cardify">
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
                                    <td data-label="{{ __('messages.security_ip_address') }}"><code>{{ $ban->ip_address }}</code></td>
                                    <td data-label="{{ __('messages.reason') }}">{{ $ban->reason ?: __('messages.not_available') }}</td>
                                    <td data-label="{{ __('messages.status') }}">{{ $statusLabel }}</td>
                                    <td data-label="{{ __('messages.security_ban_expires_at') }}">{{ $ban->expires_at?->format('Y-m-d H:i') ?? __('messages.never') }}</td>
                                    <td data-label="{{ __('messages.administrator') }}">{{ $ban->bannedBy?->username ?? __('messages.not_available') }}</td>
                                    <td data-label="{{ __('messages.actions') }}">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger js-ban-delete"
                                            data-delete-action="{{ route('admin.security.ip-bans.delete', $ban->id) }}"
                                            data-ip="{{ $ban->ip_address }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#banDeleteModal"
                                        >
                                            {{ __('messages.delete') }}
                                        </button>
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
        </section>
    </div>
</div>
@endsection

@section('modals')
<div class="modal fade" id="banDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="admin-modal-icon is-danger mx-auto mb-3"><i class="feather-trash-2"></i></div>
                <h3 class="h5 mb-2">{{ __('messages.delete') }}</h3>
                <p class="text-muted mb-0" id="banDeleteLabel">{{ __('messages.security_ip_ban_delete_confirm') }}</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <form action="" method="POST" id="banDeleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('messages.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteForm = document.getElementById('banDeleteForm');
    const deleteLabel = document.getElementById('banDeleteLabel');

    document.querySelectorAll('.js-ban-delete').forEach(function (button) {
        button.addEventListener('click', function () {
            deleteForm.action = this.dataset.deleteAction;
            deleteLabel.textContent = "{{ __('messages.security_ip_ban_delete_confirm') }}" + ' ' + (this.dataset.ip || '');
        });
    });
});
</script>
@endpush
