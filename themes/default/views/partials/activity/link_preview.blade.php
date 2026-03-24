@php
    $preview = $activity->linkPreviewRecord;
@endphp

@if($preview)
    <a class="post-preview medium" href="{{ $preview->url }}" target="_blank" rel="noopener noreferrer" style="margin-top: 18px;">
        <figure class="post-preview-image liquid" style="background: url({{ $preview->image_url ?: theme_asset('img/dir_image.png') }}) center center / cover no-repeat;"></figure>
        <div class="post-preview-info fixed-height">
            <p class="post-preview-title">{{ $preview->title ?: $preview->domain ?: $preview->url }}</p>
            @if($preview->description)
                <p class="post-preview-text">{{ \Illuminate\Support\Str::limit($preview->description, 140) }}</p>
            @endif
            <p class="post-preview-link">{{ $preview->domain ?: $preview->site_name }}</p>
        </div>
    </a>
@endif
