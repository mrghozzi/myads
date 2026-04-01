<div class="post-preview activity-post-card">
    @php
        $newsImg = $activity->related_content->img ? asset($activity->related_content->img) : theme_asset('img/cover_news.jpg');
    @endphp
    <!-- POST PREVIEW IMAGE -->
    <a href="{{ route('news.show', $activity->related_content->id) }}">
        <figure class="post-preview-image liquid" style="background: rgba(0, 0, 0, 0) url({{ $newsImg }}) no-repeat scroll center center / cover;">
            <img src="{{ $newsImg }}" alt="cover-news" style="display: none;">
        </figure>
    </a>
    <!-- /POST PREVIEW IMAGE -->

    <!-- POST PREVIEW INFO -->
    <div class="post-preview-info fixed-height">
        <!-- POST PREVIEW INFO TOP -->
        <div class="post-preview-info-top">
            <!-- POST PREVIEW TIMESTAMP -->
            <p class="post-preview-timestamp"><i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;{{ __('messages.ago') }} {{ $activity->date_formatted }}</p>
            <!-- /POST PREVIEW TIMESTAMP -->

            <!-- POST PREVIEW TITLE -->
            <p class="post-preview-title">
                <a href="{{ route('news.show', $activity->related_content->id) }}">
                    {{ $activity->related_content->name }}
                </a>
            </p>
            <!-- /POST PREVIEW TITLE -->
        </div>
        <!-- /POST PREVIEW INFO TOP -->

        <!-- POST PREVIEW INFO BOTTOM -->
        <div class="post-preview-info-bottom">
            <!-- POST PREVIEW TEXT -->
            <div class="post-preview-text news-preview-content markdown-news-preview" data-news-id="{{ $activity->related_content->id }}">
                {!! $activity->related_content->text !!}
            </div>
            <!-- /POST PREVIEW TEXT -->
        </div>
        <!-- /POST PREVIEW INFO BOTTOM -->
    </div>
    <!-- /POST PREVIEW INFO -->
</div>
