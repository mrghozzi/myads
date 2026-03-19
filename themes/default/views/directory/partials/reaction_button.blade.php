@auth
    <div class="post-option-wrap directory-reaction-option" style="position: relative;">
        <div class="post-option reaction-options-dropdown-trigger">
            <div id="reaction-btn-{{ $card['listing']->id }}">
                @if($card['current_reaction'])
                    <img class="reaction-option-image" src="{{ theme_asset('img/reaction/' . $card['current_reaction'] . '.png') }}" width="30" alt="reaction-{{ $card['current_reaction'] }}">
                @else
                    <svg class="post-option-icon icon-thumbs-up">
                        <use xlink:href="#svg-thumbs-up"></use>
                    </svg>
                    <p class="post-option-text">{{ __('messages.react') }}</p>
                @endif
            </div>
        </div>

        <div class="reaction-options reaction-options-dropdown" style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
            @foreach(['like', 'love', 'dislike', 'happy', 'funny', 'wow', 'angry', 'sad'] as $reaction)
                <div class="reaction-option text-tooltip-tft" data-title="{{ $reaction }}" onclick="toggleReaction({{ $card['listing']->id }}, 'directory', '{{ $reaction }}')">
                    <img class="reaction-option-image" src="{{ theme_asset('img/reaction/' . $reaction . '.png') }}" alt="reaction-{{ $reaction }}">
                </div>
            @endforeach
        </div>
    </div>
@endauth
