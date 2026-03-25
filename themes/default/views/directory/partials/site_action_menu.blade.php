<div class="post-settings-wrap" style="position: relative;">
    <div class="post-settings widget-box-post-settings-dropdown-trigger">
        <svg class="post-settings-icon icon-more-dots">
            <use xlink:href="#svg-more-dots"></use>
        </svg>
    </div>

    <div class="simple-dropdown widget-box-post-settings-dropdown" style="position: absolute; z-index: 9999; top: 30px; right: 9px; opacity: 0; visibility: hidden; transform: translate(0px, -20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
        @if($card['can_manage'])
            <a class="simple-dropdown-link" href="{{ route('directory.edit', $card['listing']->id) }}">
                <i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{{ __('messages.edit') }}
            </a>

            <p class="simple-dropdown-link" onclick="deletePost({{ $card['listing']->id }}, 1, '.post{{ $activity?->id ?? $card['listing']->id }}')">
                <i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}
            </p>
        @endif

        @auth
            <p class="simple-dropdown-link" onclick="reportPost({{ $card['listing']->id }}, 1, {{ $card['listing']->id }})">
                <i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}
            </p>

            @if($card['owner'])
                <p class="simple-dropdown-link" onclick="reportUser({{ $card['owner']->id }}, {{ $card['listing']->id }})">
                    <i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report_author') }}
                </p>
            @endif
        @endauth

        <p class="simple-dropdown-link" onclick="navigator.clipboard.writeText('{{ $card['detail_url'] }}'); var notif = document.getElementById('notif{{ $card['listing']->id }}'); if (notif) { notif.innerHTML = '<div class=&quot;alert alert-success&quot; role=&quot;alert&quot;>{{ __('messages.link_copied') }}</div>'; notif.style.display = 'block'; setTimeout(function () { notif.style.display = 'none'; }, 4000); }">
            <i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}
        </p>
    </div>
</div>
