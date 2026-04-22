@extends('admin::layouts.admin')

@section('title', __('messages.dev_platform'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li>{{ __('messages.dev_platform') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.manage_apps') }}</div>
            <h1 class="admin-hero__title">@lang('messages.dev_platform')</h1>
            <p class="admin-hero__copy">@lang('messages.dev_platform_desc')</p>
        </div>
        <div class="admin-hero__actions">
            <a href="{{ route('admin.developers.settings') }}" class="btn btn-primary">
                <i class="feather-settings me-1"></i> @lang('messages.settings')
            </a>
        </div>
    </section>

    <div class="row g-3 mt-4">
        <div class="col-md-4">
            <div class="admin-panel">
                <div class="admin-panel__body">
                    <span class="admin-panel__eyebrow">@lang('messages.total_apps')</span>
                    <h2 class="admin-panel__title">{{ $stats['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-panel">
                <div class="admin-panel__body">
                    <span class="admin-panel__eyebrow">@lang('messages.pending_review')</span>
                    <h2 class="admin-panel__title text-warning">{{ $stats['pending'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="admin-panel">
                <div class="admin-panel__body">
                    <span class="admin-panel__eyebrow">@lang('messages.active_apps')</span>
                    <h2 class="admin-panel__title text-success">{{ $stats['active'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <section class="admin-panel mt-4">
        <div class="admin-panel__header">
            <div>
                <span class="admin-panel__eyebrow">@lang('messages.applications')</span>
                <h2 class="admin-panel__title">@lang('messages.all_apps')</h2>
            </div>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="feather-filter me-1"></i> @lang('messages.filter')
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.developers') }}">@lang('messages.all')</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.developers', ['status' => 'pending_review']) }}">@lang('messages.pending_review')</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.developers', ['status' => 'active']) }}">@lang('messages.active')</a></li>
                </ul>
            </div>
        </div>
        <div class="admin-panel__body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>@lang('messages.app_name')</th>
                            <th>@lang('messages.owner')</th>
                            <th>@lang('messages.domain')</th>
                            <th>@lang('messages.status')</th>
                            <th>@lang('messages.created')</th>
                            <th class="text-end">@lang('messages.actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($apps as $app)
                            <tr>
                                <td class="text-muted">#{{ $app->id }}</td>
                                <td>
                                    <div class="fw-bold">{{ $app->name }}</div>
                                    <small class="text-muted d-block">{{ $app->client_id }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $app->user->avatarUrl() }}" class="rounded-circle" width="24" height="24" alt="">
                                        <a href="{{ route('admin.users.edit', $app->user->id) }}" class="fw-medium">{{ $app->user->username }}</a>
                                    </div>
                                </td>
                                <td class="text-muted">
                                    <a href="{{ $app->domain }}" target="_blank" class="text-reset">{{ parse_url($app->domain, PHP_URL_HOST) }}</a>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $app->status === 'active' ? 'success' : ($app->status === 'pending_review' ? 'warning' : ($app->status === 'rejected' ? 'danger' : 'secondary')) }}-subtle text-{{ $app->status === 'active' ? 'success' : ($app->status === 'pending_review' ? 'warning' : ($app->status === 'rejected' ? 'danger' : 'secondary')) }}">
                                        @lang('messages.app_status_' . $app->status)
                                    </span>
                                </td>
                                <td class="text-muted">{{ $app->created_at->format('Y-m-d') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.developers.show', $app->id) }}" class="btn btn-sm btn-light">
                                        <i class="feather-eye me-1"></i> @lang('messages.details')
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <div class="mb-3">
                                        <i class="feather-box fs-1 opacity-25"></i>
                                    </div>
                                    @lang('messages.no_apps_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($apps->hasPages())
            <div class="admin-panel__footer">
                {{ $apps->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
