@extends('admin::layouts.admin')

@section('title', __('messages.members'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li><a href="{{ route('admin.groups.index') }}">{{ __('messages.admin_groups_title') }}</a></li>
                <li>{{ __('messages.members') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ $group->name }}</div>
            <h1 class="admin-hero__title">{{ __('messages.members') }}</h1>
        </div>
    </section>

    <section class="admin-panel mt-4">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">{{ __('messages.members') }}</span>
                <h2 class="admin-panel__title">{{ $group->name }}</h2>
            </div>
        </div>

        <div class="admin-panel__body p-0">
            <div class="admin-table-wrap">
                <table class="table table-hover align-middle admin-table">
                    <thead>
                        <tr>
                            <th>{{ __('messages.user') }}</th>
                            <th>{{ __('messages.role') }}</th>
                            <th>{{ __('messages.status') }}</th>
                            <th>{{ __('messages.joined_at') }}</th>
                            <th class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $membership)
                            <tr>
                                <td>
                                    @if($membership->user)
                                        <a href="{{ route('profile.show', $membership->user->username) }}" class="admin-person">
                                            <span class="admin-person__avatar">
                                                <img src="{{ $membership->user->avatarUrl() }}" alt="{{ $membership->user->username }}">
                                            </span>
                                            <span class="admin-person__name">{{ $membership->user->username }}</span>
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('messages.unknown_user') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($membership->role === 'owner')
                                        <span class="badge bg-primary">{{ __('messages.groups_owner') }}</span>
                                    @elseif($membership->role === 'moderator')
                                        <span class="badge bg-info">{{ __('messages.groups_moderator') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('messages.groups_member') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $membership->status === 'active' ? 'bg-success' : 'bg-warning' }}">
                                        {{ $membership->status }}
                                    </span>
                                </td>
                                <td>
                                    {{ $membership->created_at->diffForHumans() }}
                                </td>
                                <td class="text-end">
                                    @if($membership->role !== 'owner')
                                        <div class="admin-action-cluster">
                                            <form action="{{ route('admin.groups.members.role', [$group, $membership]) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                <select name="role" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                    <option value="member" {{ $membership->role === 'member' ? 'selected' : '' }}>{{ __('messages.groups_member') }}</option>
                                                    <option value="moderator" {{ $membership->role === 'moderator' ? 'selected' : '' }}>{{ __('messages.groups_moderator') }}</option>
                                                </select>
                                            </form>
                                            <form action="{{ route('admin.groups.members.delete', [$group, $membership]) }}" method="POST" class="d-inline-block ms-2" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger admin-icon-btn">
                                                    <i class="feather-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted small">No actions for owner</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($members->hasPages())
            <div class="admin-panel__footer">
                {{ $members->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
