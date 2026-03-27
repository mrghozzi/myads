@php
    $promotionSchemaReady = app(\App\Services\V420SchemaService::class)->supports('post_promotions');
    $promotionEnabled = (bool) \App\Support\StatusPromotionSettings::get('enabled', 1);
    $canPromoteThisPost = auth()->check()
        && auth()->id() === (int) $activity->uid
        && $promotionSchemaReady
        && $promotionEnabled
        && method_exists($activity, 'supportsPromotion')
        && $activity->supportsPromotion();
@endphp

@if($canPromoteThisPost)
    <a class="simple-dropdown-link" href="{{ route('ads.posts.create', $activity->id) }}">
        <i class="fa fa-bullhorn" aria-hidden="true"></i>&nbsp;{{ __('messages.status_promotion_cta') }}
    </a>
@endif
