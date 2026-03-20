<div class="post-option-wrap directory-share-option" style="position: relative;">
    <div class="post-option reaction-options-dropdown-trigger">
        <svg class="post-option-icon icon-share">
            <use xlink:href="#svg-share"></use>
        </svg>
        <p class="post-option-text">{{ __('messages.share') }}</p>
    </div>

    <div class="reaction-options reaction-options-dropdown" style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
        @foreach(['facebook', 'twitter', 'linkedin', 'telegram'] as $social)
            <div class="reaction-option text-tooltip-tft" data-title="{{ $social }}" style="position: relative;">
                <a href="javascript:void(0);" onclick="sharePost('{{ $social }}', '{{ $shareUrl }}', '{{ $shareTitle }}')">
                    <img class="reaction-option-image" src="{{ theme_asset('img/icons/' . $social . '-icon.png') }}" alt="{{ $social }}">
                </a>
            </div>
        @endforeach
    </div>
</div>
