@php
    $repostExcerpt = $repostExcerpt ?? '';
    $repostAuthorName = $repostAuthorName ?? '';
@endphp

<!-- CONTENT ACTIONS -->
<div class="content-actions">
    <div class="content-action">
        @include('theme::partials.activity.reaction-list', ['activity' => $activity])
        <div class="meta-line">
            <p class="meta-line-text">{{ $activity->reactions_count }} {{ __('messages.reactions') }}</p>
        </div>
    </div>
    <div class="content-action">
        <div class="meta-line">
            <p class="meta-line-link">
                <a href="{{ route('forum.topic', $activity->tp_id) }}">{{ $activity->comments_count }} {{ __('messages.comments') }}</a>
            </p>
        </div>
    </div>
    <div class="content-action">
        <div class="meta-line">
            <p class="meta-line-link">
                <a href="{{ route('forum.topic', $activity->tp_id) }}">{{ $activity->reposts_count }} {{ __('messages.reposts') }}</a>
            </p>
        </div>
    </div>
</div>

<!-- POST OPTIONS -->
<div class="post-options">
    @auth
        <div class="post-option-wrap" style="position: relative;" data-activity-menu-wrap>
            <div class="post-option" data-activity-menu-trigger data-activity-menu-type="reaction">
                <div id="reaction-btn-{{ $activity->related_content->id }}">
                @php
                    $myReaction = \App\Models\Like::where('uid', auth()->id())
                        ->where('sid', $activity->related_content->id)
                        ->where('type', 2)
                        ->first();
                    $myReactionOption = $myReaction ? \App\Models\Option::where('o_parent', $myReaction->id)->where('o_type', 'data_reaction')->first() : null;
                @endphp
                @if($myReactionOption)
                    <img class="reaction-option-image" src="{{ theme_asset('img/reaction/'.$myReactionOption->o_valuer.'.png') }}" width="30" alt="reaction-{{ $myReactionOption->o_valuer }}">
                @else
                    <svg class="post-option-icon icon-thumbs-up">
                        <use xlink:href="#svg-thumbs-up"></use>
                    </svg>
                    <p class="post-option-text">{{ __('messages.react') }}</p>
                @endif
                </div>
            </div>
            <div class="reaction-options reaction-options-dropdown" data-activity-menu-panel style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                @foreach(['like', 'love', 'dislike', 'happy', 'funny', 'wow', 'angry', 'sad'] as $reaction)
                    <div class="reaction-option text-tooltip-tft" data-title="{{ $reaction }}" onclick="toggleReaction({{ $activity->related_content->id }}, 'forum', '{{ $reaction }}')">
                        <img class="reaction-option-image" src="{{ theme_asset('img/reaction/'.$reaction.'.png') }}" alt="reaction-{{ $reaction }}">
                    </div>
                @endforeach
            </div>
        </div>
        
        <div class="post-option" data-activity-comment data-comment-id="{{ $activity->related_content->id }}" data-comment-type="forum">
            <svg class="post-option-icon icon-comment">
                <use xlink:href="#svg-comment"></use>
            </svg>
            <p class="post-option-text">{{ __('messages.comment') }}</p>
        </div>
    @endauth

    <div class="post-option-wrap" style="position: relative;" data-activity-menu-wrap>
        <div class="post-option" data-activity-menu-trigger data-activity-menu-type="share">
            <svg class="post-option-icon icon-share">
                <use xlink:href="#svg-share"></use>
            </svg>
            <p class="post-option-text">{{ __('messages.share') }}</p>
        </div>
        <div class="reaction-options reaction-options-dropdown" data-activity-menu-panel style="position: absolute; z-index: 9999; bottom: 54px; left: -16px; opacity: 0; visibility: hidden; transform: translate(0px, 20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
             @foreach(['facebook', 'twitter', 'linkedin', 'telegram'] as $social)
                <div class="reaction-option text-tooltip-tft" data-title="{{ $social }}" style="position: relative;">
                    <a href="javascript:void(0);" onclick="sharePost('{{ $social }}', '{{ route('forum.topic', $activity->tp_id) }}', '{{ $activity->related_content->name ?? '' }}')">
                        <img class="reaction-option-image" src="{{ theme_asset('img/icons/'.$social.'-icon.png') }}">
                    </a>
                </div>
             @endforeach
             @auth
                @if((int) ($activity->group_id ?? 0) === 0)
                <div class="reaction-option text-tooltip-tft" data-title="{{ __('messages.quote_repost') }}" style="position: relative;">
                    <a href="javascript:void(0);" onclick="openRepostComposer({{ $activity->id }}, '{{ addslashes($repostAuthorName) }}', '{{ addslashes($repostExcerpt) }}')">
                        <span style="width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: #615dfa; color: #fff;">
                            <i class="fa fa-retweet" aria-hidden="true"></i>
                        </span>
                    </a>
                </div>
                @endif
             @endauth
        </div>
    </div>
</div>
<div class="post-comment-list post-comment-list-{{ $activity->related_content->id }}"></div>
