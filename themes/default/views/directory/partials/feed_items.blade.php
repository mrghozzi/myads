@foreach($cards as $card)
    @include('theme::directory.partials.listing_card', ['card' => $card])
@endforeach
