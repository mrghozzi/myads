<div class="widget-box news-card">
    <div class="news-card-inner">
        <div class="news-card-meta">
            <span class="news-card-chip">
                <i class="fa fa-newspaper-o" aria-hidden="true"></i>
                {{ __('messages.news') }}
            </span>
            <time class="news-card-date">
                <i class="fa fa-calendar-o" aria-hidden="true"></i>
                {{ $activity->date_formatted }}
            </time>
        </div>

        <h3 class="news-card-title">
            <a href="{{ route('news.show', $activity->related_content->id) }}">{{ $activity->related_content->name }}</a>
        </h3>

        <div class="news-card-excerpt news-preview-content markdown-news-preview" data-news-id="{{ $activity->related_content->id }}">
            {!! $activity->related_content->text !!}
        </div>

        <div class="news-card-footer">
            <a class="news-card-link" href="{{ route('news.show', $activity->related_content->id) }}">
                <span>{{ __('messages.details') }}</span>
                <i class="fa fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</div>
