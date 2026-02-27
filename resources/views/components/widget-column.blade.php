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
        @default
            <!-- Unknown widget mode: {{ $widget->o_mode }} -->
    @endswitch
@endforeach
