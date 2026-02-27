@foreach($activities as $activity)
    @include('theme::partials.activity.render', ['activity' => $activity])
@endforeach
