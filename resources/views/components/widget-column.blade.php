@foreach($widgets as $widget)
    @switch($widget->o_mode)
        @case('widget_html')
            @include('theme::partials.widgets.widget_html', ['widget' => $widget])
            @break
        @case('widget_members')
            @include('theme::partials.widgets.widget_members', ['widget' => $widget])
            @break
        @case('widget_stats_box')
            @include('theme::partials.widgets.widget_stats_box', ['widget' => $widget])
            @break
        @case('widget_forum_latest')
            @include('theme::partials.widgets.widget_forum_latest', ['widget' => $widget])
            @break
        @case('widget_news_latest')
            @include('theme::partials.widgets.widget_news_latest', ['widget' => $widget])
            @break
        @case('widget_points_leaderboard')
            @include('theme::partials.widgets.widget_points_leaderboard', ['widget' => $widget])
            @break
        @case('widget_store_latest')
            @include('theme::partials.widgets.widget_store_latest', ['widget' => $widget])
            @break
        @case('widget_directory_latest')
            @include('theme::partials.widgets.widget_directory_latest', ['widget' => $widget])
            @break
        @case('widget_orders_latest')
            @include('theme::partials.widgets.widget_orders_latest', ['widget' => $widget])
            @break
        @case('widget_badges_showcase')
            @include('theme::partials.widgets.widget_badges_showcase', ['widget' => $widget])
            @break
        @case('widget_quests_daily')
            @include('theme::partials.widgets.widget_quests_daily', ['widget' => $widget])
            @break
        @default
            <!-- Unknown widget mode: {{ $widget->o_mode }} -->
    @endswitch
@endforeach
