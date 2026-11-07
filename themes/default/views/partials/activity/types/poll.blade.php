@php
    $activityUser = $activity->user;
    $activityUserProfileUrl = $activityUser ? route('profile.show', $activityUser->username) : '#';
    $activityUserName = $activityUser?->username ?? __('messages.unknown_user');
    $activityUserAvatar = $activityUser ? $activityUser->avatarUrl() : asset('upload/_avatar.png');
    $activityUserPresence = $activityUser?->isOnline() ? 'online' : 'offline';
    $activityUserIsAdmin = $activityUser?->isAdmin() ?? false;
    $activityUserHasVerifiedBadge = $activityUser?->hasVerifiedBadge() ?? false;
    $relatedId = $activity->related_content?->id ?? $activity->tp_id ?? $activity->id;
    $formattedText = \App\Support\ContentFormatter::format($activity->related_content?->txt ?? ($activity->txt ?? ''));
    
    $poll = $activity->poll ?? \App\Models\Poll::where('status_id', $activity->id)->with('options')->first();
    $currentUserId = auth()->id();
    $hasVoted = $poll ? $poll->hasUserVoted($currentUserId) : false;
    $votedOptionId = $poll ? $poll->getUserVotedOptionId($currentUserId) : null;
    $isExpired = $poll ? $poll->is_expired : false;
    $totalVotes = $poll ? $poll->total_votes : 0;
@endphp

<div class="widget-box no-padding activity-post-card post{{ $activity->id }}" data-poll-id="{{ $poll?->id }}">
    <!-- WIDGET BOX SETTINGS -->
    <div class="widget-box-settings">
        <div class="post-settings-wrap" style="position: relative;">
            <div class="post-settings widget-box-post-settings-dropdown-trigger">
                <svg class="post-settings-icon icon-more-dots">
                    <use xlink:href="#svg-more-dots"></use>
                </svg>
            </div>

            <div class="simple-dropdown widget-box-post-settings-dropdown" style="position: absolute; z-index: 9999; top: 30px; right: 9px; opacity: 0; visibility: hidden; transform: translate(0px, -20px); transition: transform 0.3s ease-in-out 0s, opacity 0.3s ease-in-out 0s, visibility 0.3s ease-in-out 0s;">
                @auth
                    @if(auth()->id() == $activity->uid || auth()->user()->isAdmin())
                        <p class="simple-dropdown-link post_delete{{ $activity->id }}" onclick="deletePost({{ $activity->tp_id }}, 100, '.post{{ $activity->id }}')"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}</p>
                    @endif
                    @if(isset($isOwnProfile) && $isOwnProfile && auth()->id() == $activity->uid)
                        <p class="simple-dropdown-link" onclick="togglePinPost({{ $activity->id }}, {{ $activity->is_pinned ? 'true' : 'false' }}, {{ isset($hasPinnedPost) && $hasPinnedPost ? 'true' : 'false' }})" style="cursor: pointer;">
                            <i class="fa fa-thumb-tack" aria-hidden="true"></i>&nbsp;{{ $activity->is_pinned ? __('messages.unpin_post') ?? 'Unpin' : __('messages.pin_post') ?? 'Pin to profile' }}
                        </p>
                    @endif
                    @include('theme::partials.activity.promotion_link', ['activity' => $activity])
                    <p class="simple-dropdown-link post_report{{ $activity->id }}" onclick="reportPost({{ $activity->tp_id }}, 100, {{ $relatedId }})"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}</p>
                @endauth
                <p class="simple-dropdown-link copy_link" onclick="navigator.clipboard.writeText('{{ route('forum.topic', $activity->tp_id) }}'); var notif = document.getElementById('notif{{ $relatedId }}'); notif.innerHTML = '<div class=\'alert alert-success\' role=\'alert\'>{{ __('messages.link_copied') }}</div>'; notif.style.display = 'block'; setTimeout(function() { notif.style.display = 'none'; }, 5000);"><i class="fa fa-link" aria-hidden="true"></i>&nbsp;{{ __('messages.copy_link') }}</p>
            </div>
        </div>
    </div>

    <!-- WIDGET BOX STATUS -->
    <div class="widget-box-status">
        <div class="widget-box-status-content">
            <!-- USER STATUS -->
            <div class="user-status">
                <a class="user-status-avatar" href="{{ $activityUserProfileUrl }}">
                    <div class="user-avatar small no-outline {{ $activityUserPresence }}">
                        <div class="user-avatar-content">
                            <div class="hexagon-image-30-32" data-src="{{ $activityUserAvatar }}" style="width: 30px; height: 32px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                            </div>
                        </div>
                        <div class="user-avatar-progress-border">
                            <div class="hexagon-border-40-44" data-line-color="{{ $activityUser ? $activityUser->profileBadgeColor() : '' }}" style="width: 40px; height: 44px; position: relative;"></div>
                        </div>
                        @if($activityUserHasVerifiedBadge)
                            <div class="user-avatar-badge">
                                <div class="user-avatar-badge-border">
                                    <div class="hexagon-22-24" style="width: 22px; height: 24px; position: relative;"></div>
                                </div>
                                <div class="user-avatar-badge-content">
                                    <div class="hexagon-dark-16-18" style="width: 16px; height: 18px; position: relative;"></div>
                                </div>
                                <p class="user-avatar-badge-text"><i class="fa fa-fw fa-check"></i></p>
                            </div>
                        @endif
                    </div>
                </a>

                <p class="user-status-title medium">
                    <a class="bold" href="{{ $activityUserProfileUrl }}">{{ $activityUserName }}</a>
                </p>

                <p class="user-status-text small">
                    <i class="fa fa-clock-o"></i>&nbsp;{{ __('messages.ago') }}&nbsp; {{ $activity->date_formatted }}
                </p>

                @if($activity->group)
                    <div style="margin-top:8px;">
                        @include('theme::partials.groups.badge', ['groupBadge' => $activity->group])
                    </div>
                @endif
            </div>

            @include('theme::partials.activity.promotion_badge', ['activity' => $activity])

            <div class="tag-sticker" title="{{ __('messages.poll') ?? 'استطلاع رأي' }}" style="background: linear-gradient(135deg, #3b5998, #615dfa);">
                <i class="fa fa-tasks" style="color: #ffffff; font-size: 13px;"></i>
            </div>

            <!-- POST TEXT IF ANY -->
            @if(trim(strip_tags($formattedText)) !== '' && (!isset($poll) || $formattedText !== ($poll->question ?? '')))
                <div class="widget-box-status-text post_text{{ $relatedId }} mb-3">
                    <div class="textpost">
                        {!! $formattedText !!}
                    </div>
                </div>
            @endif

            <!-- POLL CONTAINER CARD -->
            @if($poll)
                <div class="community-poll-card mt-2 mb-3 p-3" id="poll-box-{{ $poll->id }}" style="
                    background: var(--portal-surface-alt, #f7f8fc);
                    border: 1px solid var(--portal-border, #e5e7eb);
                    border-radius: 16px;
                    transition: all 0.3s ease;
                ">
                    <!-- Poll Header -->
                    <div class="d-flex align-items-center justify-content-between mb-3 gap-2 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge" style="background: rgba(97, 93, 250, 0.12); color: #615dfa; font-weight: 700; border-radius: 20px; padding: 6px 12px;">
                                <i class="fa fa-poll-h me-1"></i> {{ __('messages.poll') ?? 'استطلاع رأي' }}
                            </span>
                            @if($isExpired)
                                <span class="badge bg-secondary" style="border-radius: 20px; padding: 6px 10px;">
                                    <i class="fa fa-lock me-1"></i> {{ __('messages.poll_ended') ?? 'منتهي' }}
                                </span>
                            @else
                                <span class="badge bg-success" style="border-radius: 20px; padding: 6px 10px;">
                                    <i class="fa fa-circle me-1" style="font-size: 8px;"></i> {{ __('messages.poll_active') ?? 'نشط' }}
                                </span>
                            @endif
                        </div>

                        <div class="small text-muted d-flex align-items-center gap-2">
                            <span id="poll-total-votes-{{ $poll->id }}" class="fw-bold">
                                {{ $totalVotes }} {{ __('messages.votes') ?? 'صوت' }}
                            </span>
                            @if($poll->expires_at && !$isExpired)
                                <span>&bull;</span>
                                <span title="{{ $poll->expires_at->toDateTimeString() }}">
                                    <i class="fa fa-hourglass-half"></i> {{ $poll->expires_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Poll Question -->
                    <h5 class="fw-bold mb-3" style="font-size: 1.05rem; line-height: 1.4; color: var(--portal-heading, #1f2937);">
                        {{ $poll->question }}
                    </h5>

                    <!-- Options List -->
                    <div class="poll-options-wrapper d-flex flex-column gap-2" id="poll-options-{{ $poll->id }}">
                        @foreach($poll->options as $opt)
                            @php
                                $percent = $opt->percentage;
                                $isUserVote = ($votedOptionId === $opt->id);
                                $canVote = auth()->check() && !$hasVoted && !$isExpired;
                            @endphp

                            <div class="poll-option-item position-relative overflow-hidden {{ $isUserVote ? 'poll-option-selected' : '' }}" style="
                                border: 1.5px solid {{ $isUserVote ? '#615dfa' : 'var(--portal-border, #e5e7eb)' }};
                                border-radius: 12px;
                                background: var(--portal-card-bg, #ffffff);
                                cursor: {{ $canVote ? 'pointer' : 'default' }};
                                transition: all 0.2s ease;
                            " 
                            @if($canVote)
                                onclick="submitPollVote({{ $poll->id }}, {{ $opt->id }})"
                            @endif
                            >
                                <!-- Progress bar background for results -->
                                <div class="poll-bar-fill position-absolute top-0 bottom-0 start-0" style="
                                    width: {{ ($hasVoted || $isExpired) ? $percent : 0 }}%;
                                    background: {{ $isUserVote ? 'rgba(97, 93, 250, 0.22)' : 'rgba(35, 210, 226, 0.14)' }};
                                    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
                                    z-index: 1;
                                    pointer-events: none;
                                "></div>

                                <!-- Option content -->
                                <div class="p-3 d-flex align-items-center justify-content-between position-relative" style="z-index: 2;">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($canVote)
                                            <div class="poll-vote-radio" style="
                                                width: 18px;
                                                height: 18px;
                                                border: 2px solid #94a3b8;
                                                border-radius: 50%;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                            "></div>
                                        @elseif($isUserVote)
                                            <i class="fa fa-check-circle text-primary" style="font-size: 16px;"></i>
                                        @endif
                                        <span class="poll-option-title fw-bold" style="font-size: 0.94rem; color: var(--portal-text, #334155);">
                                            {{ $opt->option_text }}
                                        </span>
                                    </div>

                                    @if($hasVoted || $isExpired)
                                        <div class="d-flex align-items-center gap-2 text-nowrap">
                                            <span class="small text-muted">{{ $opt->votes_count }} {{ __('messages.votes') ?? 'صوت' }}</span>
                                            <span class="fw-bold" style="min-width: 44px; text-align: end; color: #615dfa;">{{ $percent }}%</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @guest
                        <div class="text-center mt-3 pt-2 border-top">
                            <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 20px;">
                                <i class="fa fa-sign-in-alt me-1"></i> {{ __('messages.login_to_vote') ?? 'سجل دخولك للمشاركة في التصويت' }}
                            </a>
                        </div>
                    @endguest
                </div>
            @endif

            <div id="notif{{ $relatedId }}"></div>

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
                    @include('theme::partials.activity.repost_button', ['activity' => $activity])
                </div>
            </div>
            <!-- /CONTENT ACTIONS -->
        </div>
        <!-- /WIDGET BOX STATUS CONTENT -->
    </div>
    <!-- /WIDGET BOX STATUS -->

    <!-- POST COMMENTS -->
    <div class="post-comment-list" id="post-comment-list{{ $relatedId }}">
        @include('theme::partials.activity.comments', ['activity' => $activity])
    </div>
</div>

@once
@push('scripts')
<script>
window.submitPollVote = function(pollId, optionId) {
    const pollBox = document.getElementById('poll-box-' + pollId);
    if (!pollBox) return;

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!token) return;

    // Visual feedback
    pollBox.style.opacity = '0.6';
    pollBox.style.pointerEvents = 'none';

    fetch('/polls/' + pollId + '/vote', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({ poll_option_id: optionId })
    })
    .then(res => res.json())
    .then(data => {
        pollBox.style.opacity = '1';
        pollBox.style.pointerEvents = 'auto';

        if (data.error) {
            alert(data.error);
            return;
        }

        if (data.success && data.options) {
            // Update total votes
            const totalEl = document.getElementById('poll-total-votes-' + pollId);
            if (totalEl) {
                totalEl.textContent = data.total_votes + ' ' + (@json(__('messages.votes') ?? 'صوت'));
            }

            // Re-render options
            const optionsContainer = document.getElementById('poll-options-' + pollId);
            if (optionsContainer) {
                let html = '';
                data.options.forEach(opt => {
                    const isSelected = (opt.id === data.user_voted_option_id);
                    html += `
                        <div class="poll-option-item position-relative overflow-hidden ${isSelected ? 'poll-option-selected' : ''}" style="
                            border: 1.5px solid ${isSelected ? '#615dfa' : 'var(--portal-border, #e5e7eb)'};
                            border-radius: 12px;
                            background: var(--portal-card-bg, #ffffff);
                            cursor: default;
                            transition: all 0.2s ease;
                        ">
                            <div class="poll-bar-fill position-absolute top-0 bottom-0 start-0" style="
                                width: ${opt.percentage}%;
                                background: ${isSelected ? 'rgba(97, 93, 250, 0.22)' : 'rgba(35, 210, 226, 0.14)'};
                                transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
                                z-index: 1;
                                pointer-events: none;
                            "></div>

                            <div class="p-3 d-flex align-items-center justify-content-between position-relative" style="z-index: 2;">
                                <div class="d-flex align-items-center gap-2">
                                    ${isSelected ? '<i class="fa fa-check-circle text-primary" style="font-size: 16px;"></i>' : ''}
                                    <span class="poll-option-title fw-bold" style="font-size: 0.94rem; color: var(--portal-text, #334155);">
                                        ${opt.option_text}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-2 text-nowrap">
                                    <span class="small text-muted">${opt.votes_count} ${@json(__('messages.votes') ?? 'صوت')}</span>
                                    <span class="fw-bold" style="min-width: 44px; text-align: end; color: #615dfa;">${opt.percentage}%</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                optionsContainer.innerHTML = html;
            }
        }
    })
    .catch(err => {
        pollBox.style.opacity = '1';
        pollBox.style.pointerEvents = 'auto';
        console.error('Poll vote error:', err);
    });
};
</script>
@endpush
@endonce

