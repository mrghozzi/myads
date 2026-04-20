@extends('admin::layouts.admin')

@section('title', __('messages.admin_groups_title'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ __('messages.admin_groups_title') }}</h1>
                    <p class="text-muted mb-0">{{ __('messages.admin_groups_description') }}</p>
                </div>
                <div>
                    <a class="btn btn-primary" href="{{ route('admin.groups.settings') }}">{{ __('messages.admin_groups_settings_title') }}</a>
                </div>
            </div>
        </div>
    </div>

    @if(!$schemaReady)
        <div class="alert alert-warning">{{ __('messages.groups_feature_disabled') }}</div>
    @else
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3 mb-3">
                    <div class="col-md-4">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>{{ __('messages.all') }}</option>
                            <option value="pending_review" {{ $filter === 'pending_review' ? 'selected' : '' }}>{{ __('messages.groups_status_pending_review') }}</option>
                            <option value="active" {{ $filter === 'active' ? 'selected' : '' }}>{{ __('messages.groups_status_active') }}</option>
                            <option value="suspended" {{ $filter === 'suspended' ? 'selected' : '' }}>{{ __('messages.groups_status_suspended') }}</option>
                            <option value="rejected" {{ $filter === 'rejected' ? 'selected' : '' }}>{{ __('messages.groups_status_rejected') }}</option>
                        </select>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.author') }}</th>
                                <th>{{ __('messages.groups_privacy') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.members') }}</th>
                                <th>{{ __('messages.posts') }}</th>
                                <th class="text-end">{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groups as $group)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $group->name }}</div>
                                        <div class="text-muted small">{{ $group->slug }}</div>
                                    </td>
                                    <td>{{ $group->owner?->username ?? __('messages.unknown_user') }}</td>
                                    <td>{{ $group->privacy === \App\Models\Group::PRIVACY_PUBLIC ? __('messages.groups_public') : __('messages.groups_private') }}</td>
                                    <td>{{ __('messages.groups_status_' . $group->status) }}</td>
                                    <td>{{ $group->members_count }}</td>
                                    <td>{{ $group->posts_count }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.groups.status', $group) }}" class="d-inline-block">
                                            @csrf
                                            <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                @foreach(['pending_review', 'active', 'suspended', 'rejected'] as $status)
                                                    <option value="{{ $status }}" {{ $group->status === $status ? 'selected' : '' }}>
                                                        {{ __('messages.groups_status_' . $status) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                        <form method="POST" action="{{ route('admin.groups.feature', $group) }}" class="d-inline-block ms-2">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-primary" type="submit">
                                                {{ $group->is_featured ? __('messages.groups_unfeature') : __('messages.groups_feature') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">{{ __('messages.groups_empty_state') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($groups instanceof \Illuminate\Contracts\Pagination\Paginator)
                    <div class="mt-3">{{ $groups->links() }}</div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
