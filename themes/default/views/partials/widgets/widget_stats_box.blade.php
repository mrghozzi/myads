@php
    $config = [];
    if (!empty($widget?->o_valuer)) {
        if (is_array($widget->o_valuer)) {
            $config = $widget->o_valuer;
        } else {
            $decoded = json_decode((string) $widget->o_valuer, true);
            $config = is_array($decoded) ? $decoded : [];
        }
    }

    $showBanners = isset($config['show_banners']) ? (bool) $config['show_banners'] : true;
    $showLinks = isset($config['show_links']) ? (bool) $config['show_links'] : true;
    $showUsers = !empty($config['show_users']);
    $showTopics = !empty($config['show_topics']);
    $showOrders = !empty($config['show_orders']);
    $enableSlider = isset($config['enable_slider']) ? (bool) $config['enable_slider'] : true;

    try {
        $bannerViews = $showBanners ? \Illuminate\Support\Facades\DB::table('state')->where('t_name', 'banner')->count() : 0;
    } catch (\Exception $e) {
        $bannerViews = 0;
    }

    try {
        $linkViews = $showLinks ? \Illuminate\Support\Facades\DB::table('state')->where('t_name', 'link')->count() : 0;
    } catch (\Exception $e) {
        $linkViews = 0;
    }

    $bannersCount = $showBanners ? \Illuminate\Support\Facades\DB::table('banner')->count() : 0;
    $linksCount = $showLinks ? \Illuminate\Support\Facades\DB::table('link')->count() : 0;
    $usersCount = $showUsers ? \App\Models\User::count() : 0;
    $topicsCount = $showTopics ? \App\Models\ForumTopic::count() : 0;
    $ordersCount = $showOrders ? \App\Models\OrderRequest::count() : 0;
@endphp

<div class="widget-box">
    <!-- WIDGET BOX TITLE -->
    <p class="widget-box-title">{{ $widget->name }}</p>
    <!-- /WIDGET BOX TITLE -->

    <!-- WIDGET BOX CONTENT -->
    <div class="widget-box-content">
        <div class="{{ $enableSlider ? 'stats-box-slider' : 'stats-box-static' }}">
            @if($enableSlider)
                <!-- STATS BOX SLIDER CONTROLS -->
                <div class="stats-box-slider-controls">
                    <p class="stats-box-slider-controls-title">{{ $widget->name }}</p>
                    <div id="stats-box-slider-controls" class="slider-controls">
                        <div class="slider-control negative left">
                            <svg class="slider-control-icon icon-small-arrow">
                                <use xlink:href="#svg-small-arrow"></use>
                            </svg>
                        </div>
                        <div class="slider-control negative right">
                            <svg class="slider-control-icon icon-small-arrow">
                                <use xlink:href="#svg-small-arrow"></use>
                            </svg>
                        </div>
                    </div>
                </div>
                <!-- /STATS BOX SLIDER CONTROLS -->
            @endif

            <!-- STATS BOX ITEMS -->
            <div id="{{ $enableSlider ? 'stats-box-slider-items' : '' }}" class="{{ $enableSlider ? 'stats-box-slider-items' : 'd-flex flex-column gap-3' }}">
                @if($showBanners)
                    <!-- STATS BOX: BANNERS -->
                    <div class="stats-box stat-profile-views">
                        <div class="stats-box-value-wrap">
                            <p class="stats-box-value">{{ $bannersCount }}</p>
                            <div class="stats-box-diff">
                                <div>
                                    <svg class="icon-status" style="fill: #ff5384;" >
                                        <use xlink:href="#svg-status"></use>
                                    </svg>
                                </div>
                                <p class="stats-box-diff-value">{{ __('messages.bannads') }}</p>
                            </div>
                        </div>
                        <p class="stats-box-title">{{ __('messages.Views') }}</p>
                        <p class="stats-box-text">{{ $bannerViews }}</p>
                    </div>
                @endif

                @if($showLinks)
                    <!-- STATS BOX: LINKS -->
                    <div class="stats-box stat-posts-created">
                        <div class="stats-box-value-wrap">
                            <p class="stats-box-value">{{ $linksCount }}</p>
                            <div class="stats-box-diff">
                                <div>
                                    <svg class="icon-events-weekly" style="fill: #ff5384;" >
                                        <use xlink:href="#svg-events-weekly"></use>
                                    </svg>
                                </div>
                                <p class="stats-box-diff-value">{{ __('messages.linkads') }}</p>
                            </div>
                        </div>
                        <p class="stats-box-title">{{ __('messages.Views') }}</p>
                        <p class="stats-box-text">{{ $linkViews }}</p>
                    </div>
                @endif

                @if($showUsers)
                    <!-- STATS BOX: USERS -->
                    <div class="stats-box stat-profile-views">
                        <div class="stats-box-value-wrap">
                            <p class="stats-box-value">{{ number_format($usersCount) }}</p>
                            <div class="stats-box-diff">
                                <div>
                                    <svg class="icon-status" style="fill: #615dfa;">
                                        <use xlink:href="#svg-members"></use>
                                    </svg>
                                </div>
                                <p class="stats-box-diff-value">{{ __('messages.members') ?? 'Members' }}</p>
                            </div>
                        </div>
                        <p class="stats-box-title">{{ __('messages.landing_stats_members') }}</p>
                        <p class="stats-box-text">{{ number_format($usersCount) }}</p>
                    </div>
                @endif

                @if($showTopics)
                    <!-- STATS BOX: FORUM TOPICS -->
                    <div class="stats-box stat-posts-created">
                        <div class="stats-box-value-wrap">
                            <p class="stats-box-value">{{ number_format($topicsCount) }}</p>
                            <div class="stats-box-diff">
                                <div>
                                    <svg class="icon-status" style="fill: #23d2e2;">
                                        <use xlink:href="#svg-forum"></use>
                                    </svg>
                                </div>
                                <p class="stats-box-diff-value">{{ __('messages.forum') }}</p>
                            </div>
                        </div>
                        <p class="stats-box-title">{{ __('messages.landing_stats_posts') }}</p>
                        <p class="stats-box-text">{{ number_format($topicsCount) }}</p>
                    </div>
                @endif

                @if($showOrders)
                    <!-- STATS BOX: ORDERS -->
                    <div class="stats-box stat-profile-views">
                        <div class="stats-box-value-wrap">
                            <p class="stats-box-value">{{ number_format($ordersCount) }}</p>
                            <div class="stats-box-diff">
                                <div>
                                    <svg class="icon-status" style="fill: #4ff461;">
                                        <use xlink:href="#svg-marketplace"></use>
                                    </svg>
                                </div>
                                <p class="stats-box-diff-value">{{ __('messages.orders') ?? 'Orders' }}</p>
                            </div>
                        </div>
                        <p class="stats-box-title">{{ __('messages.latest_orders') }}</p>
                        <p class="stats-box-text">{{ number_format($ordersCount) }}</p>
                    </div>
                @endif
            </div>
            <!-- /STATS BOX ITEMS -->
        </div>
    </div>
    <!-- /WIDGET BOX CONTENT -->
</div>
