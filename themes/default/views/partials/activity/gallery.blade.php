@php
    $galleryImages = collect();
    foreach (($activity->related_content->attachments ?? collect()) as $attachment) {
        if (method_exists($attachment, 'isImage') && $attachment->isImage()) {
            $galleryImages->push(asset($attachment->file_path));
        }
    }
    if ($galleryImages->isEmpty() && !empty($activity->related_content->image_url)) {
        $galleryImages->push(asset($activity->related_content->image_url));
    }
@endphp

@if($galleryImages->isNotEmpty())
    <div class="picture-collage" style="margin-top: 18px;">
        @if($galleryImages->count() === 1)
            <div class="picture-collage-row">
                <a class="picture-collage-item" href="{{ route('forum.topic', $activity->tp_id) }}">
                    <img class="photo-preview" src="{{ $galleryImages->first() }}" alt="gallery-image">
                </a>
            </div>
        @else
            @foreach($galleryImages->chunk(2) as $chunk)
                <div class="picture-collage-row{{ $chunk->count() === 1 ? ' medium' : '' }}">
                    @foreach($chunk as $imageUrl)
                        <a class="picture-collage-item" href="{{ route('forum.topic', $activity->tp_id) }}">
                            <img class="photo-preview" src="{{ $imageUrl }}" alt="gallery-image">
                        </a>
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>
@endif
