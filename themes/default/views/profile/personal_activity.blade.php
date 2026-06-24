@extends('theme::layouts.master')

@section('content')
<div class="section-banner">
    <p class="section-banner-title">{{ __('messages.personal_activity') }}</p>
    <p class="section-banner-text">{{ __('messages.personal_activity_desc') }}</p>
</div>

<style>
    .activity-timeline {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .activity-item {
        background: #fff;
        border: 1px solid #eaeaf5;
        border-radius: 12px;
        padding: 16px;
        display: flex;
        gap: 16px;
        align-items: flex-start;
    }
    .activity-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        flex-shrink: 0;
        font-size: 16px;
    }
    .activity-icon.post {
        background: rgba(97, 93, 250, 0.1);
        color: #615dfa;
    }
    .activity-icon.comment {
        background: rgba(35, 210, 226, 0.1);
        color: #23d2e2;
    }
    .activity-icon.reaction {
        background: rgba(233, 75, 95, 0.1);
        color: #e94b5f;
    }
    .activity-content {
        flex-grow: 1;
    }
    .activity-header {
        font-size: 14px;
        color: #8f91ac;
        margin-bottom: 6px;
    }
    .activity-header strong {
        color: #3e3f5e;
    }
    .activity-time {
        font-size: 12px;
        color: #adafca;
    }
    .activity-body {
        font-size: 14px;
        color: #3e3f5e;
        line-height: 1.5;
        background: #fcfcfd;
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid #f1f1f5;
        margin-top: 8px;
    }
    .activity-link {
        font-weight: 600;
        color: #615dfa;
        text-decoration: none;
    }
    .activity-link:hover {
        text-decoration: underline;
    }
    .activity-empty {
        text-align: center;
        padding: 40px;
        background: #fff;
        border-radius: 12px;
        border: 1px solid #eaeaf5;
        color: #8f91ac;
    }
</style>

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">
        @include('theme::profile.settings_nav')
    </div>

    <div class="grid-column">
        <div class="activity-timeline">
            @if($activities->count() === 0)
                <div class="activity-empty">
                    <i class="fa fa-inbox fa-3x mb-3" style="color: #d8d8e0;"></i>
                    <p>{{ __('messages.no_activity_found') }}</p>
                </div>
            @else
                @foreach($activities as $activity)
                    <div class="activity-item">
                        @if($activity->activity_type === 'post')
                            <div class="activity-icon post">
                                <i class="fa fa-pencil-alt"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-header">
                                    {{ __('messages.published_new_post') }} &bull; <span class="activity-time">{{ \Carbon\Carbon::createFromTimestamp($activity->timestamp)->diffForHumans() }}</span>
                                </div>
                                @if(!empty($activity->item->txt))
                                    <div class="activity-body">
                                        {{ Str::limit(strip_tags((string) $activity->item->txt), 150) }}
                                    </div>
                                @endif
                                <div style="margin-top: 8px;">
                                    <a href="{{ route('portal.index') }}" class="activity-link">{{ __('messages.view_in_portal') }}</a>
                                </div>
                            </div>
                        @elseif($activity->activity_type === 'comment')
                            <div class="activity-icon comment">
                                <i class="fa fa-comment-dots"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-header">
                                    {{ __('messages.commented_on') }} 
                                    @if($activity->item->topic)
                                        <a href="{{ route('forum.topic', $activity->item->topic->id) }}" class="activity-link">{{ Str::limit($activity->item->topic->title ?? __('messages.a_topic'), 40) }}</a>
                                    @else
                                        <strong>{{ __('messages.a_topic') }}</strong>
                                    @endif
                                    &bull; <span class="activity-time">{{ \Carbon\Carbon::createFromTimestamp($activity->timestamp)->diffForHumans() }}</span>
                                </div>
                                @if(!empty($activity->item->txt))
                                    <div class="activity-body">
                                        {{ Str::limit(strip_tags((string) $activity->item->txt), 150) }}
                                    </div>
                                @endif
                            </div>
                        @elseif($activity->activity_type === 'reaction')
                            <div class="activity-icon reaction">
                                <i class="fa fa-heart"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-header">
                                    @if($activity->item->type == 1)
                                        {{ __('messages.followed') }} 
                                        @if($activity->target)
                                            <a href="{{ route('profile.show', $activity->target->username) }}" class="activity-link">{{ $activity->target->username }}</a>
                                        @else
                                            <strong>{{ __('messages.a_user') }}</strong>
                                        @endif
                                    @elseif(in_array($activity->item->type, [2, 14, 6]))
                                        {{ __('messages.reacted_to') }} 
                                        @if($activity->target)
                                            <a href="{{ route('forum.topic', $activity->target->id) }}" class="activity-link">{{ Str::limit($activity->target->title ?? __('messages.a_topic'), 40) }}</a>
                                        @else
                                            <strong>{{ __('messages.a_topic') }}</strong>
                                        @endif
                                    @elseif($activity->item->type == 22)
                                        {{ __('messages.reacted_to') }} 
                                        @if($activity->target)
                                            <a href="{{ route('directory.show', $activity->target->id) }}" class="activity-link">{{ Str::limit($activity->target->title ?? __('messages.a_site'), 40) }}</a>
                                        @else
                                            <strong>{{ __('messages.a_site') }}</strong>
                                        @endif
                                    @elseif($activity->item->type == 3)
                                        {{ __('messages.reacted_to') }} 
                                        @if($activity->target)
                                            <a href="{{ route('store.show', $activity->target->name) }}" class="activity-link">{{ Str::limit($activity->target->title ?? __('messages.a_product'), 40) }}</a>
                                        @else
                                            <strong>{{ __('messages.a_product') }}</strong>
                                        @endif
                                    @else
                                        {{ __('messages.reacted_to_item') }}
                                    @endif
                                    &bull; <span class="activity-time">{{ \Carbon\Carbon::createFromTimestamp($activity->timestamp)->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif

            @if($activities->hasPages())
                <div style="margin-top: 10px;">
                    {{ $activities->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
