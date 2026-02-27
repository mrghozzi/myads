@php
    if(!isset($place)) return;
    $widgets = \Illuminate\Support\Facades\DB::table('options')
        ->where('o_parent', $place)
        ->where('o_type', 'box_widget')
        ->orderBy('o_order', 'desc')
        ->get();
@endphp

@foreach($widgets as $widget)
    @php
        $name = $widget->o_mode;
    @endphp

    @if(view()->exists('theme::partials.widgets.' . $name))
        @include('theme::partials.widgets.' . $name, ['widget' => $widget])
    @endif
@endforeach
