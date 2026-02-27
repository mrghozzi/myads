<div class="post-preview">
    <!-- POST PREVIEW IMAGE -->
    <figure class="post-preview-image liquid" style="background: rgba(0, 0, 0, 0) url({{ theme_asset('img/cover_news.jpg') }}) no-repeat scroll center center / cover;">
        <img src="{{ theme_asset('img/cover_news.jpg') }}" alt="cover-news" style="display: none;">
    </figure>
    <!-- /POST PREVIEW IMAGE -->

    <!-- POST PREVIEW INFO -->
    <div class="post-preview-info fixed-height">
        <!-- POST PREVIEW INFO TOP -->
        <div class="post-preview-info-top">
            <!-- POST PREVIEW TIMESTAMP -->
            <p class="post-preview-timestamp"><i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;{{ __('messages.ago') }} {{ $activity->date_formatted }}</p>
            <!-- /POST PREVIEW TIMESTAMP -->

            <!-- POST PREVIEW TITLE -->
            <p class="post-preview-title">{{ $activity->related_content->name }}</p>
            <!-- /POST PREVIEW TITLE -->
        </div>
        <!-- /POST PREVIEW INFO TOP -->

        <!-- POST PREVIEW INFO BOTTOM -->
        <div class="post-preview-info-bottom">
            <!-- POST PREVIEW TEXT -->
            <p class="post-preview-text">
                @php
                    $txt = $activity->related_content->text;
                    $txt = preg_replace('/#(\w+)/', '<a href="'.url('tag/$1').'">#$1</a>', $txt);
                @endphp
                {!! $txt !!}
            </p>
            <!-- /POST PREVIEW TEXT -->
        </div>
        <!-- /POST PREVIEW INFO BOTTOM -->
    </div>
    <!-- /POST PREVIEW INFO -->
</div>
