@if($activities->count() > 0)
    @foreach($activities as $activity)
        @include('theme::partials.activity.render', ['activity' => $activity])
    @endforeach
@else
    <div class="portal-empty-card">
        <div class="portal-empty-icon">
            <i class="far fa-newspaper"></i>
        </div>
        <h3 class="portal-empty-title">{{ __('messages.portal_empty_feed') }}</h3>
        <p class="portal-empty-desc">{{ __('messages.portal_empty_feed_desc') }}</p>
        @auth
            <a href="#quick-post-box" class="portal-empty-action" onclick="document.querySelector('#quick-post-box textarea, #quick-post-box input')?.focus();">
                <i class="fas fa-edit"></i> {{ __('messages.portal_share_something') }}
            </a>
        @endauth
    </div>
@endif

