@foreach($statuses as $status)
    @php
        $topic = $topics->get($status->tp_id);
    @endphp
    @if($topic)
        @include('theme::partials.forum.topic_card', ['topic' => $topic, 'status' => $status])
    @endif
@endforeach
