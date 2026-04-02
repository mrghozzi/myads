@extends('admin::layouts.admin')

@php
    $statusBadgeColors = [
        \App\Models\StatusPromotion::STATUS_ACTIVE => 'status-promotion-badge status-promotion-badge--active',
        \App\Models\StatusPromotion::STATUS_PAUSED => 'status-promotion-badge status-promotion-badge--paused',
        \App\Models\StatusPromotion::STATUS_COMPLETED => 'status-promotion-badge status-promotion-badge--completed',
        \App\Models\StatusPromotion::STATUS_EXPIRED => 'status-promotion-badge status-promotion-badge--expired',
        \App\Models\StatusPromotion::STATUS_BUDGET_CAPPED => 'status-promotion-badge status-promotion-badge--budget-capped',
    ];
@endphp

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.status_promotions_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.ads') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.status_promotions_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.status_promotions_admin_help') }}</p>
        </div>
        <div class="admin-hero__actions">
            <div class="admin-toolbar-card w-100">
                <a href="{{ route('admin.ads.posts.settings') }}" class="btn btn-primary w-100">{{ __('messages.status_promotion_settings_title') }}</a>
            </div>
        </div>
    </section>

    @if(!empty($upgradeNotice))
        @include('admin::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
    @endif

    @if($featureAvailable)
        <section class="admin-panel">
            <div class="admin-panel__body">
                <form class="row g-3" method="GET" action="{{ route('admin.ads.posts.index') }}">
                    <div class="col-lg-5">
                        <label class="form-label">{{ __('messages.search') }}</label>
                        <input type="text" class="form-control" name="search" value="{{ $search }}" placeholder="{{ __('messages.status_promotion_search_placeholder') }}">
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">{{ __('messages.status') }}</label>
                        <select name="status" class="form-select">
                            <option value="">{{ __('messages.all_updates') }}</option>
                            @foreach(['active','completed','expired','paused','budget_capped'] as $statusOption)
                                <option value="{{ $statusOption }}" @selected($status === $statusOption)>{{ __('messages.status_promotion_status_' . $statusOption) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label">{{ __('messages.status_promotion_objective_label') }}</label>
                        <select name="objective" class="form-select">
                            <option value="">{{ __('messages.all') }}</option>
                            @foreach(['views','comments','reactions','days'] as $objectiveOption)
                                <option value="{{ $objectiveOption }}" @selected($objective === $objectiveOption)>{{ __('messages.status_promotion_objective_' . $objectiveOption) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-dark w-100">{{ __('messages.filter') }}</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="admin-panel">
            <div class="admin-panel__body p-0">
                <div class="admin-table-wrap">
                    <table class="table table-hover align-middle mb-0 admin-table admin-table-cardify">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.user') }}</th>
                                <th>{{ __('messages.posts') }}</th>
                                <th>{{ __('messages.status_promotion_objective_label') }}</th>
                                <th>{{ __('messages.status_promotion_pts_label') }}</th>
                                <th>{{ __('messages.status_promotion_progress') }}</th>
                                <th>{{ __('messages.status') }}</th>
                                <th>{{ __('messages.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($promotions as $promotion)
                                @php($statusModel = $promotion->promotedStatus)
                                <tr>
                                    <td data-label="#">#{{ $promotion->id }}</td>
                                    <td data-label="{{ __('messages.user') }}">{{ $promotion->user->username ?? ('#' . $promotion->user_id) }}</td>
                                    <td data-label="{{ __('messages.posts') }}">
                                        @if($statusModel)
                                            <a href="{{ $statusModel->promotionDestinationUrl() }}">{{ __('messages.status_promotion_view_post') }}</a>
                                        @else
                                            #{{ $promotion->status_id }}
                                        @endif
                                    </td>
                                    <td data-label="{{ __('messages.status_promotion_objective_label') }}">{{ __('messages.status_promotion_objective_' . $promotion->objective) }}<br><small class="text-muted">{{ $promotion->target_quantity }}</small></td>
                                    <td data-label="{{ __('messages.status_promotion_pts_label') }}">{{ $promotion->charged_pts }}</td>
                                    <td data-label="{{ __('messages.status_promotion_progress') }}">
                                        <div class="fw-semibold">{{ $promotion->currentProgressValue($statusModel) }} / {{ $promotion->target_quantity }}</div>
                                        <div class="progress mt-2" style="height: 8px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $promotion->progressPercentage($statusModel) }}%;"></div>
                                        </div>
                                    </td>
                                    <td data-label="{{ __('messages.status') }}">
                                        <span class="{{ $statusBadgeColors[$promotion->status] ?? 'status-promotion-badge' }}">{{ __('messages.status_promotion_status_' . $promotion->status) }}</span>
                                    </td>
                                    <td data-label="{{ __('messages.actions') }}">
                                        <div class="admin-action-cluster">
                                            @if($promotion->status === \App\Models\StatusPromotion::STATUS_ACTIVE)
                                                <form method="POST" action="{{ route('admin.ads.posts.status', $promotion->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="action" value="pause">
                                                    <button class="btn btn-sm btn-outline-warning">{{ __('messages.pause') }}</button>
                                                </form>
                                            @endif
                                            @if($promotion->status === \App\Models\StatusPromotion::STATUS_PAUSED)
                                                <form method="POST" action="{{ route('admin.ads.posts.status', $promotion->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="action" value="resume">
                                                    <button class="btn btn-sm btn-outline-primary">{{ __('messages.resume') }}</button>
                                                </form>
                                            @endif
                                            @if(!$promotion->isFinal())
                                                <form method="POST" action="{{ route('admin.ads.posts.status', $promotion->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="action" value="complete">
                                                    <button class="btn btn-sm btn-outline-success">{{ __('messages.complete') }}</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">{{ __('messages.status_promotion_empty_admin') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <div>{{ $promotions->links() }}</div>
    @endif
</div>
@endsection
