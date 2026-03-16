@foreach($notifications as $notification)
    @include('theme::notifications.partials.item', ['notification' => $notification])
@endforeach
