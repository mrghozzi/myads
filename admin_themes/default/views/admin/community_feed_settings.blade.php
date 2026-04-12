@extends('admin::layouts.admin')

@section('title', __('messages.community_feed_settings_title'))

@section('content')
<div class="admin-page">
    <section class="admin-hero">
        <div class="admin-hero__content">
            <ul class="admin-breadcrumb">
                <li><a href="{{ route('admin.index') }}">{{ __('messages.dashboard') ?? 'Dashboard' }}</a></li>
                <li>{{ __('messages.Comusetting') }}</li>
                <li>{{ __('messages.community_feed_settings_title') }}</li>
            </ul>
            <div class="admin-hero__eyebrow">{{ __('messages.community') }}</div>
            <h1 class="admin-hero__title">{{ __('messages.community_feed_settings_title') }}</h1>
            <p class="admin-hero__copy">{{ __('messages.community_feed_settings_desc') }}</p>
        </div>
    </section>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.community.feed.settings.update') }}" class="admin-form-workspace">
        @csrf

        <section class="admin-panel">
            <div class="admin-panel__body">
                <p class="text-muted mb-0">{{ __('messages.community_feed_settings_intro') }}</p>
            </div>
        </section>

        @php
            $sections = [
                'freshness' => [
                    'title' => __('messages.community_feed_settings_freshness_section'),
                    'help' => __('messages.community_feed_settings_freshness_help'),
                    'fields' => [
                        ['name' => 'freshness_base_score', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_freshness_base')],
                        ['name' => 'freshness_decay_exponent', 'step' => '0.01', 'min' => '0.01', 'label' => __('messages.community_feed_settings_freshness_decay')],
                        ['name' => 'freshness_suppression_after_hours', 'min' => '1', 'label' => __('messages.community_feed_settings_suppression_after')],
                        ['name' => 'freshness_suppression_multiplier', 'step' => '0.01', 'min' => '0', 'max' => '1', 'label' => __('messages.community_feed_settings_suppression_multiplier')],
                        ['name' => 'view_weight', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_view_weight')],
                        ['name' => 'max_views_score', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_view_cap')],
                    ],
                ],
                'personalization' => [
                    'title' => __('messages.community_feed_settings_personalization_section'),
                    'help' => __('messages.community_feed_settings_personalization_help'),
                    'fields' => [
                        ['name' => 'following_boost', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_following_boost')],
                        ['name' => 'author_affinity_boost', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_author_affinity_boost')],
                        ['name' => 'content_affinity_boost', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_content_affinity_boost')],
                        ['name' => 'social_proof_boost', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_social_proof_boost')],
                    ],
                ],
                'trend_windows' => [
                    'title' => __('messages.community_feed_settings_trend_windows_section'),
                    'help' => __('messages.community_feed_settings_trend_windows_help'),
                    'fields' => [
                        ['name' => 'rapid_window_hours', 'min' => '1', 'label' => __('messages.community_feed_settings_rapid_window')],
                        ['name' => 'trend_window_hours', 'min' => '1', 'label' => __('messages.community_feed_settings_trend_window')],
                        ['name' => 'recent_reaction_weight', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_recent_reaction_weight')],
                        ['name' => 'recent_comment_weight', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_recent_comment_weight')],
                        ['name' => 'recent_repost_weight', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_recent_repost_weight')],
                        ['name' => 'rapid_reaction_weight', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_rapid_reaction_weight')],
                        ['name' => 'rapid_comment_weight', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_rapid_comment_weight')],
                        ['name' => 'rapid_repost_weight', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_rapid_repost_weight')],
                        ['name' => 'total_reaction_weight', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_total_reaction_weight')],
                        ['name' => 'total_comment_weight', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_total_comment_weight')],
                        ['name' => 'total_repost_weight', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_total_repost_weight')],
                    ],
                ],
                'trend_rescue' => [
                    'title' => __('messages.community_feed_settings_trend_rescue_section'),
                    'help' => __('messages.community_feed_settings_trend_rescue_help'),
                    'fields' => [
                        ['name' => 'rescue_max_age_hours', 'min' => '1', 'label' => __('messages.community_feed_settings_rescue_max_age')],
                        ['name' => 'rescue_min_recent_reactions', 'min' => '0', 'label' => __('messages.community_feed_settings_rescue_min_reactions')],
                        ['name' => 'rescue_min_recent_comments', 'min' => '0', 'label' => __('messages.community_feed_settings_rescue_min_comments')],
                        ['name' => 'rescue_min_recent_reposts', 'min' => '0', 'label' => __('messages.community_feed_settings_rescue_min_reposts')],
                    ],
                ],
                'diversity' => [
                    'title' => __('messages.community_feed_settings_diversity_section'),
                    'help' => __('messages.community_feed_settings_diversity_help'),
                    'fields' => [
                        ['name' => 'repeat_author_penalty', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_repeat_author_penalty')],
                        ['name' => 'repeat_type_penalty', 'step' => '0.01', 'min' => '0', 'label' => __('messages.community_feed_settings_repeat_type_penalty')],
                    ],
                ],
                'candidate' => [
                    'title' => __('messages.community_feed_settings_candidate_section'),
                    'help' => __('messages.community_feed_settings_candidate_help'),
                    'fields' => [
                        ['name' => 'fresh_candidate_hours', 'min' => '1', 'label' => __('messages.community_feed_settings_fresh_candidate_hours')],
                        ['name' => 'fresh_candidate_limit', 'min' => '1', 'label' => __('messages.community_feed_settings_fresh_candidate_limit')],
                        ['name' => 'rescue_candidate_limit', 'min' => '1', 'label' => __('messages.community_feed_settings_rescue_candidate_limit')],
                    ],
                ],
                'cache' => [
                    'title' => __('messages.community_feed_settings_cache_section'),
                    'help' => __('messages.community_feed_settings_cache_help'),
                    'fields' => [
                        ['name' => 'cache_ttl_seconds', 'min' => '0', 'label' => __('messages.community_feed_settings_cache_ttl')],
                    ],
                ],
            ];
        @endphp

        @foreach($sections as $section)
            <section class="admin-panel">
                <div class="admin-panel__header">
                    <div>
                        <span class="admin-panel__eyebrow">{{ $section['title'] }}</span>
                        <h2 class="admin-panel__title">{{ $section['title'] }}</h2>
                        <p class="text-muted mb-0">{{ $section['help'] }}</p>
                    </div>
                </div>
                <div class="admin-panel__body">
                    <div class="row g-3">
                        @foreach($section['fields'] as $field)
                            <div class="col-lg-{{ count($section['fields']) <= 2 ? 6 : 4 }}">
                                <label class="form-label">{{ $field['label'] }}</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    name="{{ $field['name'] }}"
                                    value="{{ old($field['name'], $settings[$field['name']]) }}"
                                    @if(isset($field['step'])) step="{{ $field['step'] }}" @endif
                                    @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
                                    @if(isset($field['max'])) max="{{ $field['max'] }}" @endif
                                >
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endforeach

        <section class="admin-panel">
            <div class="admin-panel__body d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">{{ __('messages.save_changes') }}</button>
            </div>
        </section>
    </form>
</div>
@endsection
