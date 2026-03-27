@extends('theme::layouts.admin')

@section('content')
<div class="d-flex flex-column gap-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h3 class="mb-1">{{ __('messages.status_promotions_title') }}</h3>
                <p class="text-muted mb-0">{{ __('messages.status_promotions_admin_help') }}</p>
            </div>
            <a href="{{ route('admin.ads.posts.settings') }}" class="btn btn-primary">{{ __('messages.status_promotion_settings_title') }}</a>
        </div>
    </div>

    @if(!empty($upgradeNotice))
        @include('theme::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
    @endif

    @if($featureAvailable)
        <div class="card border-0 shadow-sm">
            <div class="card-body">
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
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
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
                                @php
                                    $statusModel = $promotion->promotedStatus;
                                @endphp
                                <tr>
                                    <td>#{{ $promotion->id }}</td>
                                    <td>{{ $promotion->user->username ?? ('#' . $promotion->user_id) }}</td>
                                    <td>
                                        @if($statusModel)
                                            <a href="{{ $statusModel->promotionDestinationUrl() }}">{{ __('messages.status_promotion_view_post') }}</a>
                                        @else
                                            #{{ $promotion->status_id }}
                                        @endif
                                    </td>
                                    <td>{{ __('messages.status_promotion_objective_' . $promotion->objective) }}<br><small class="text-muted">{{ $promotion->target_quantity }}</small></td>
                                    <td>{{ $promotion->charged_pts }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $promotion->currentProgressValue($statusModel) }} / {{ $promotion->target_quantity }}</div>
                                        <div class="progress mt-2" style="height: 8px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $promotion->progressPercentage($statusModel) }}%;"></div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ __('messages.status_promotion_status_' . $promotion->status) }}</span></td>
                                    <td>
                                        <div class="hstack gap-2">
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
        </div>

        <div>
            {{ $promotions->links() }}
        </div>
    @endif
</div>
@endsection
