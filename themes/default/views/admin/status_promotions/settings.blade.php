@extends('theme::layouts.admin')

@section('content')
<div class="d-flex flex-column gap-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h3 class="mb-1">{{ __('messages.status_promotion_settings_title') }}</h3>
                <p class="text-muted mb-0">{{ __('messages.status_promotion_settings_help') }}</p>
            </div>
            <a href="{{ route('admin.ads.posts.index') }}" class="btn btn-light">{{ __('messages.back') }}</a>
        </div>
    </div>

    @if(!empty($upgradeNotice))
        @include('theme::partials.upgrade_notice', ['upgradeNotice' => $upgradeNotice])
    @endif

    @if($featureAvailable)
        <form method="POST" action="{{ route('admin.ads.posts.settings.update') }}" class="d-flex flex-column gap-4">
            @csrf

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" id="enabled" name="enabled" value="1" @checked($settings['enabled'])>
                        <label class="form-check-label fw-semibold" for="enabled">{{ __('messages.status_promotion_enable_system') }}</label>
                    </div>

                    <div class="row g-3">
                        <div class="col-lg-3">
                            <label class="form-label">{{ __('messages.status_promotion_objective_views') }}</label>
                            <input type="number" step="0.01" min="0" name="price_per_100_views_pts" class="form-control" value="{{ $settings['price_per_100_views_pts'] }}">
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label">{{ __('messages.status_promotion_objective_reactions') }}</label>
                            <input type="number" step="0.01" min="0" name="price_per_reaction_goal_pts" class="form-control" value="{{ $settings['price_per_reaction_goal_pts'] }}">
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label">{{ __('messages.status_promotion_objective_comments') }}</label>
                            <input type="number" step="0.01" min="0" name="price_per_comment_goal_pts" class="form-control" value="{{ $settings['price_per_comment_goal_pts'] }}">
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label">{{ __('messages.status_promotion_objective_days') }}</label>
                            <input type="number" step="0.01" min="0" name="price_per_day_pts" class="form-control" value="{{ $settings['price_per_day_pts'] }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('messages.status_promotion_delivery_settings') }}</h5>
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <label class="form-label">{{ __('messages.status_promotion_estimated_views_per_reaction') }}</label>
                            <input type="number" min="1" name="estimated_views_per_reaction" class="form-control" value="{{ $settings['estimated_views_per_reaction'] }}">
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">{{ __('messages.status_promotion_estimated_views_per_comment') }}</label>
                            <input type="number" min="1" name="estimated_views_per_comment" class="form-control" value="{{ $settings['estimated_views_per_comment'] }}">
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">{{ __('messages.status_promotion_estimated_views_per_day') }}</label>
                            <input type="number" min="1" name="estimated_views_per_day" class="form-control" value="{{ $settings['estimated_views_per_day'] }}">
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">{{ __('messages.status_promotion_per_page_limit') }}</label>
                            <input type="number" min="1" name="per_page_limit" class="form-control" value="{{ $settings['per_page_limit'] }}">
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">{{ __('messages.status_promotion_gap_setting') }}</label>
                            <input type="number" min="1" name="min_gap_between_promotions" class="form-control" value="{{ $settings['min_gap_between_promotions'] }}">
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label">{{ __('messages.status_promotion_cooldown_setting') }}</label>
                            <input type="number" min="0" name="viewer_repeat_cooldown_hours" class="form-control" value="{{ $settings['viewer_repeat_cooldown_hours'] }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('messages.status_promotion_limits_title') }}</h5>
                    <div class="row g-3">
                        @foreach(['views', 'reactions', 'comments', 'days'] as $objective)
                            <div class="col-lg-3">
                                <label class="form-label">{{ __('messages.status_promotion_limit_min') }} {{ __('messages.status_promotion_objective_' . $objective) }}</label>
                                <input type="number" min="1" name="min_{{ $objective }}_target" class="form-control" value="{{ $settings['min_' . $objective . '_target'] }}">
                            </div>
                            <div class="col-lg-3">
                                <label class="form-label">{{ __('messages.status_promotion_limit_max') }} {{ __('messages.status_promotion_objective_' . $objective) }}</label>
                                <input type="number" min="1" name="max_{{ $objective }}_target" class="form-control" value="{{ $settings['max_' . $objective . '_target'] }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('messages.status_promotion_smart_pricing_summary') }}</h5>
                    <p class="text-muted mb-0">{{ __('messages.status_promotion_smart_pricing_help') }}</p>
                </div>
            </div>

            <div>
                <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
            </div>
        </form>
    @endif
</div>
@endsection
