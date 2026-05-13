@php
    $activityUser = $activity->user;
    $activityUserProfileUrl = $activityUser ? route('profile.show', $activityUser->username) : '#';
    $activityUserName = $activityUser?->username ?? __('messages.unknown_user');
    $activityUserAvatar = $activityUser ? $activityUser->avatarUrl() : asset('upload/_avatar.png');
    $activityUserPresence = $activityUser?->isOnline() ? 'online' : 'offline';
    $formattedText = \App\Support\ContentFormatter::format($activity->related_content->txt ?? '');
    $repostExcerpt = \Illuminate\Support\Str::limit(strip_tags($activity->related_content->txt ?? ''), 80);
    $repostAuthorName = addslashes($activityUserName);
    $attachments = $activity->related_content->attachments;
@endphp

<div class="widget-box no-padding activity-post-card post{{ $activity->id }}">
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
                        <p class="simple-dropdown-link post_edit{{ $activity->id }}" onclick="postEdit({{ $activity->tp_id }}, {{ $activity->s_type }})"><i class="fa fa-edit" aria-hidden="true"></i>&nbsp;{{ __('messages.edit') }}</p>
                        <p class="simple-dropdown-link post_delete{{ $activity->id }}" onclick="deletePost({{ $activity->tp_id }}, {{ $activity->s_type }}, '.post{{ $activity->id }}')"><i class="fa fa-trash" aria-hidden="true"></i>&nbsp;{{ __('messages.delete') }}</p>
                    @endif
                    <p class="simple-dropdown-link post_report{{ $activity->id }}" onclick="reportPost({{ $activity->tp_id }}, {{ $activity->s_type }}, {{ $activity->related_content->id }})"><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;{{ __('messages.report') }}</p>
                @endauth
            </div>
        </div>
    </div>

    <div class="widget-box-status">
        <div class="widget-box-status-content">
            <div class="user-status">
                <a class="user-status-avatar" href="{{ $activityUserProfileUrl }}">
                    <div class="user-avatar small no-outline {{ $activityUserPresence }}">
                        <div class="user-avatar-content">
                            <div class="hexagon-image-30-32" data-src="{{ $activityUserAvatar }}"></div>
                        </div>
                    </div>
                </a>
                <p class="user-status-title medium">
                    <a class="bold" href="{{ $activityUserProfileUrl }}">{{ $activityUserName }}</a>
                    &nbsp;{{ __('messages.added_files') ?? 'shared files' }}
                </p>
                <p class="user-status-text small">
                    <i class="fa fa-clock-o"></i>&nbsp;{{ $activity->date_formatted }}
                </p>
            </div>

            @include('theme::partials.activity.promotion_badge', ['activity' => $activity])
            
            <div class="tag-sticker">
                <i class="fa-solid fa-download" style="font-size: 14px;"></i>
            </div>

            <div class="widget-box-status-text">
                <div class="textpost">
                    {!! $formattedText !!}
                </div>
            </div>

            @if($attachments->count() > 0)
                <div class="post-files-list" style="margin-top: 15px; display: grid; gap: 10px;">
                    @foreach($attachments as $file)
                        <div class="post-file-item" style="display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 12px; background: var(--composer-subtle-bg, #f5f7ff); border: 1px solid var(--composer-border, #e7eaf5);">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background: #615dfa; display: flex; align-items: center; justify-content: center; color: #fff;">
                                <i class="fa fa-file-text" aria-hidden="true"></i>
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <p style="margin: 0; font-weight: 700; font-size: 0.85rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $file->original_name }}</p>
                                <p style="margin: 2px 0 0; font-size: 0.75rem; color: var(--composer-muted);">{{ round($file->file_size / 1024, 1) }} KB</p>
                            </div>
                            <a href="{{ asset($file->file_path) }}" download class="button small primary" style="padding: 0 12px; height: 32px; min-width: auto;">
                                <i class="fa fa-download" aria-hidden="true"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @include('theme::partials.activity.post_footer_shared', ['activity' => $activity, 'repostAuthorName' => $repostAuthorName, 'repostExcerpt' => $repostExcerpt])
</div>
