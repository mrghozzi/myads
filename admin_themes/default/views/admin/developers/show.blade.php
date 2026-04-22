@extends('admin::layouts.admin')

@section('title', $app->name)

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') }}</a></li>
                <li><a href="{{ route('admin.developers') }}">{{ __('messages.dev_platform') }}</a></li>
                <li>{{ $app->name }}</li>
            </ul>
            <div class="admin-hero__eyebrow">@lang('messages.app_details')</div>
            <h1 class="admin-hero__title">{{ $app->name }}</h1>
            <p class="admin-hero__copy">{{ Str::limit($app->description, 100) }}</p>
        </div>
        <div class="admin-hero__actions">
            <a href="{{ route('admin.developers') }}" class="btn btn-light">
                <i class="feather-arrow-left me-1"></i> @lang('messages.back')
            </a>
        </div>
    </section>

    <div class="row g-4 mt-4">
        <div class="col-lg-8">
            <section class="admin-panel">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">@lang('messages.information')</span>
                        <h2 class="admin-panel__title">@lang('messages.app_specifications')</h2>
                    </div>
                </div>
                <div class="admin-panel__body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <tbody>
                                <tr class="border-bottom">
                                    <th class="ps-4 py-3 text-muted" style="width: 200px;">@lang('messages.app_name')</th>
                                    <td class="pe-4 py-3 fw-bold">{{ $app->name }}</td>
                                </tr>
                                <tr class="border-bottom">
                                    <th class="ps-4 py-3 text-muted">@lang('messages.owner')</th>
                                    <td class="pe-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $app->user->avatarUrl() }}" class="rounded-circle" width="24" height="24" alt="">
                                            <a href="{{ route('admin.users.edit', $app->user->id) }}" class="fw-medium">{{ $app->user->username }}</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <th class="ps-4 py-3 text-muted">@lang('messages.client_id')</th>
                                    <td class="pe-4 py-3"><code>{{ $app->client_id }}</code></td>
                                </tr>
                                <tr class="border-bottom">
                                    <th class="ps-4 py-3 text-muted">@lang('messages.domain')</th>
                                    <td class="pe-4 py-3">
                                        <a href="{{ $app->domain }}" target="_blank" class="text-primary">{{ $app->domain }}</a>
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <th class="ps-4 py-3 text-muted">@lang('messages.redirect_uris')</th>
                                    <td class="pe-4 py-3">
                                        @if(is_array($app->redirect_uris))
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($app->redirect_uris as $uri)
                                                    <span class="badge bg-light text-dark border font-monospace">{{ $uri }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="border-bottom">
                                    <th class="ps-4 py-3 text-muted">@lang('messages.requested_scopes')</th>
                                    <td class="pe-4 py-3">
                                        @if(is_array($app->requested_scopes))
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($app->requested_scopes as $scope)
                                                    <span class="badge bg-primary-subtle text-primary">{{ $scope }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="ps-4 py-3 text-muted">@lang('messages.created')</th>
                                    <td class="pe-4 py-3">{{ $app->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="admin-panel mt-4">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">@lang('messages.description')</span>
                        <h2 class="admin-panel__title">@lang('messages.app_about')</h2>
                    </div>
                </div>
                <div class="admin-panel__body">
                    <p class="mb-0 text-muted lh-lg">{{ $app->description }}</p>
                </div>
            </section>
        </div>

        <div class="col-lg-4">
            <section class="admin-panel mb-4">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">@lang('messages.status')</span>
                        <h2 class="admin-panel__title">@lang('messages.current_status')</h2>
                    </div>
                </div>
                <div class="admin-panel__body">
                    <div class="text-center py-3">
                        <span class="badge bg-{{ $app->status === 'active' ? 'success' : ($app->status === 'pending_review' ? 'warning' : ($app->status === 'rejected' ? 'danger' : 'secondary')) }}-subtle text-{{ $app->status === 'active' ? 'success' : ($app->status === 'pending_review' ? 'warning' : ($app->status === 'rejected' ? 'danger' : 'secondary')) }} fs-5 px-4 py-2 rounded-pill">
                            @lang('messages.app_status_' . $app->status)
                        </span>
                    </div>
                </div>
            </section>

            <section class="admin-panel">
                <div class="admin-panel__header border-bottom">
                    <div>
                        <span class="admin-panel__eyebrow">@lang('messages.moderation')</span>
                        <h2 class="admin-panel__title">@lang('messages.take_action')</h2>
                    </div>
                </div>
                <div class="admin-panel__body">
                    <form action="{{ route('admin.developers.status', $app->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">@lang('messages.change_status')</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $app->status === 'active' ? 'selected' : '' }}>@lang('messages.app_status_active')</option>
                                <option value="pending_review" {{ $app->status === 'pending_review' ? 'selected' : '' }}>@lang('messages.app_status_pending_review')</option>
                                <option value="rejected" {{ $app->status === 'rejected' ? 'selected' : '' }}>@lang('messages.app_status_rejected')</option>
                                <option value="suspended" {{ $app->status === 'suspended' ? 'selected' : '' }}>@lang('messages.app_status_suspended')</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="feather-check-circle me-1"></i> @lang('messages.update_status')
                        </button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
