@php
    $repost = $activity->repostRecord;
    $original = $repost?->originalStatus;
    $originalUser = $original?->user;
    $originalUserProfileUrl = $originalUser ? route('profile.show', $originalUser->username) : '#';
    $originalUserName = $originalUser?->username ?? __('messages.unknown_user');
    $originalUserAvatar = $originalUser?->img ? asset($originalUser->img) : theme_asset('img/avatar/default.png');
    $originalUserPresence = $originalUser?->isOnline() ? 'online' : 'offline';
    $originalUrl = null;
    $originalTitle = null;
    $originalMeta = null;
    $originalBody = '';

    if ($original && $original->related_content) {
        $originalUrl = match ((int) $original->s_type) {
            1 => route('directory.show', $original->tp_id),
            7867 => route('store.show', $original->related_content->name),
            5 => route('news.show', $original->tp_id),
            default => route('forum.topic', $original->tp_id),
        };

        $originalTitle = match ((int) $original->s_type) {
            1, 2, 5, 7867 => $original->related_content->name ?? null,
            default => null,
        };

        $originalMeta = match ((int) $original->s_type) {
            1 => parse_url($original->related_content->url ?? '', PHP_URL_HOST) ?: null,
            2 => $original->related_content->category->name ?? null,
            5 => __('messages.news'),
            7867 => ($original->related_content->type->name ?? null),
            default => null,
        };

        $bodySource = match ((int) $original->s_type) {
            7867 => $original->related_content->o_valuer ?? '',
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
                            <div class="hexagon-image-30-32" data-src="{{ $originalUserAvatar }}"></div>
                        </div>
                        <div class="user-avatar-progress-border">
                            <div class="hexagon-border-40-44"></div>
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
        </div>
    </div>
@endif
