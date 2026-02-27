<div class="meta-line-list reaction-item-list">
    @if(isset($activity->grouped_reactions) && count($activity->grouped_reactions) > 0)
        @foreach($activity->grouped_reactions as $type => $users)
            <div class="reaction-item">
                <!-- REACTION IMAGE -->
                <img class="reaction-image reaction-item-dropdown-trigger" src="{{ theme_asset('img/reaction/'.$type.'.png') }}" alt="reaction-{{ $type }}">
                <!-- /REACTION IMAGE -->

                <!-- SIMPLE DROPDOWN -->
                <div class="simple-dropdown padded reaction-item-dropdown">
                    <!-- SIMPLE DROPDOWN TEXT -->
                    <p class="simple-dropdown-text">
                        <img class="reaction" src="{{ theme_asset('img/reaction/'.$type.'.png') }}" alt="reaction-{{ $type }}">
                        <span class="bold">{{ ucfirst($type) }}</span>
                    </p>
                    <!-- /SIMPLE DROPDOWN TEXT -->
                    @foreach($users as $user)
                        <p class="simple-dropdown-text">{{ $user->username }}</p>
                    @endforeach
                </div>
                <!-- /SIMPLE DROPDOWN -->
            </div>
        @endforeach
    @endif
</div>
