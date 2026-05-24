@php
    $repost = $activity->repostRecord;
    $original = $repost?->originalStatus;
    $originalUser = $original?->user;
    $originalUserProfileUrl = $originalUser ? route('profile.show', $originalUser->username) : '#';
    $originalUserName = $originalUser?->username ?? __('messages.unknown_user');
    $originalUserAvatar = $originalUser ? $originalUser->avatarUrl() : asset('upload/_avatar.png');
    $originalUserPresence = $originalUser?->isOnline() ? 'online' : 'offline';
    $originalUrl = null;
    $originalTitle = null;
    $originalMeta = null;
    $originalBody = '';

    if ($original && $original->related_content) {
        $originalUrl = match ((int) $original->s_type) {
            1 => route('directory.show', $original->tp_id),
            7867 => route('store.show', $original->related_content->name),
            205 => route('kb.show', ['name' => $original->related_content->o_mode, 'article' => $original->related_content->name]),
            5 => route('news.show', $original->tp_id),
            default => route('forum.topic', $original->tp_id),
        };

        $originalTitle = match ((int) $original->s_type) {
            1, 2, 5, 7867, 205 => $original->related_content->name ?? null,
            default => null,
        };

        $originalMeta = match ((int) $original->s_type) {
            1 => parse_url($original->related_content->url ?? '', PHP_URL_HOST) ?: null,
            2 => $original->related_content->category->name ?? null,
            5 => __('messages.news'),
            7867 => ($original->related_content->type->name ?? null),
            205 => optional($original->related_content->productItem)->name,
            default => null,
        };

        $bodySource = match ((int) $original->s_type) {
            7867 => $original->related_content->o_valuer ?? '',
            205 => $original->related_content->o_valuer ?? '',
            5 => $original->related_content->text ?? '',
            default => $original->related_content->txt ?? '',
        };

        $originalBody = \App\Support\ContentFormatter::format($bodySource);
    }
@endphp

@if($original && $original->related_content)
    <div class="widget-box" style="margin-top: 18px; margin-bottom: 0; border: 1px solid #eaeaf5;">
        <div class="widget-box-content">
            <div class="user-status" style="padding-top: 0;">
                <a class="user-status-avatar" href="{{ $originalUserProfileUrl }}">
                    <div class="user-avatar small no-outline {{ $originalUserPresence }}">
                        <div class="user-avatar-content">
                            <div class="hexagon-image-30-32" data-src="{{ $originalUserAvatar }}" style="width: 30px; height: 32px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="30" height="32"></canvas>
                            </div>
                        </div>
                        <div class="user-avatar-progress-border">
                            <div class="hexagon-border-40-44" data-line-color="{{ $originalUser ? $originalUser->profileBadgeColor() : '' }}" style="width: 40px; height: 44px; position: relative;">
                                <canvas style="position: absolute; top: 0px; left: 0px;" width="40" height="44"></canvas>
                            </div>
                        </div>
                    </div>
                </a>
                <p class="user-status-title medium">
                    <a class="bold" href="{{ $originalUserProfileUrl }}">{{ $originalUserName }}</a>
                </p>
                <p class="user-status-text small">{{ $original->date_formatted }}</p>
            </div>

            @if($originalTitle || $originalMeta)
                <div style="margin-top: 14px;">
                    @if($originalTitle)
                        <p class="user-status-title" style="font-size: 14px;">
                            <a class="bold" href="{{ $originalUrl }}">{{ $originalTitle }}</a>
                        </p>
                    @endif
                    @if($originalMeta)
                        <p class="user-status-text small">{{ $originalMeta }}</p>
                    @endif
                </div>
            @endif

            @if(trim(strip_tags($originalBody)) !== '')
                <div class="widget-box-status-text" style="margin-top: 14px;">
                    {!! $originalBody !!}
                </div>
            @endif

            @if($original->linkPreviewRecord)
                @include('theme::partials.activity.link_preview', ['activity' => $original])
            @endif

            @if((int) $original->s_type === 4)
                @include('theme::partials.activity.gallery', ['activity' => $original])
            @endif

            @if(in_array((int) $original->s_type, [10, 14]))
                @php $video = $original->related_content->attachments->first(); @endphp
                @if($video)
                    <div class="post-video-wrapper" style="margin-top: 14px; border-radius: 12px; overflow: hidden; background: #000;">
                        <video controls preload="metadata" style="width: 100%; max-height: 400px; display: block;">
                            <source src="{{ asset($video->file_path) }}" type="{{ $video->mime_type }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @endif
            @elseif(in_array((int) $original->s_type, [11, 13]))
                @php $audio = $original->related_content->attachments->first(); @endphp
                @if($audio)
                    <div class="post-audio-wrapper" style="margin-top: 14px; border-radius: 12px; padding: 12px; background: var(--section-background-color); border: 1px solid var(--border-color);">
                        <audio controls style="width: 100%;">
                            <source src="{{ asset($audio->file_path) }}" type="{{ $audio->mime_type }}">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                @endif
            @elseif((int) $original->s_type === 12)
                @php $attachments = $original->related_content->attachments; @endphp
                @if($attachments && $attachments->count() > 0)
                    <div class="post-files-list" style="margin-top: 14px; display: grid; gap: 10px;">
                        @foreach($attachments as $file)
                            <div class="post-file-item" style="display: flex; align-items: center; justify-content: space-between; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--section-background-color);">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <i class="fa-solid fa-file-lines" style="font-size: 20px; color: #615dfa;"></i>
                                    <div>
                                        <p class="bold" style="font-size: 13px; margin: 0;">{{ $file->original_name }}</p>
                                        <p style="font-size: 11px; color: #7f85a3; margin: 0;">{{ $file->human_size }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('forum.attachment.download', $file->id) }}" class="button small primary" style="padding: 0 10px; height: 28px; line-height: 28px;">
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        </div>
    </div>
@endif
