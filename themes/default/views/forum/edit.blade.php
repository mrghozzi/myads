@extends('theme::layouts.master')

@section('content')
<!-- SECTION BANNER -->
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%;">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/discussion-icon.png') }}">
    <p class="section-banner-title">{{ isset($topic) ? __('messages.edit_topic') : __('messages.w_new_tpc') }}</p>
</div>
<!-- /SECTION BANNER -->

<!-- ADS -->
@include('theme::partials.ads', ['id' => 4])

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">
        @include('theme::partials.widgets', ['place' => 3])
    </div>

    <div class="grid-column">
        <div class="widget-box">
            <div class="widget-box-title">
                <p class="widget-box-title">{{ isset($topic) ? __('messages.edit_topic') : __('messages.w_new_tpc') }}</p>
            </div>
            
            <div class="widget-box-content">
                <form method="POST" action="{{ isset($topic) ? route('forum.update', $topic->id) : route('forum.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if(isset($topic))
                        <input type="hidden" name="id" value="{{ $topic->id }}">
                    @endif

                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-input social-input small active">
                                <div class="social-link no-hover name">
                                    <i class="fa fa-edit" aria-hidden="true"></i>
                                </div>
                                <label for="name">{{ __('messages.sbj') }}</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $topic->name ?? '') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-item">
                            <div class="form-input">
                                <label for="txt">{{ __('messages.content') }}</label>
                                <textarea id="editor1" name="txt" rows="16" required>{{ old('txt', $topic->txt ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-select">
                                <label for="category"><i class="fa fa-folder" aria-hidden="true"></i>&nbsp;{{ __('messages.category_fallback') }}</label>
                                <select id="category" name="cat" required>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (old('cat', $topic->cat ?? '') == $category->id) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <svg class="form-select-icon icon-small-arrow">
                                    <use xlink:href="#svg-small-arrow"></use>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    @if(!isset($topic))
                    <div class="form-row split">
                        <div class="form-item">
                            <div class="form-select">
                                <label for="type"><i class="fa fa-list" aria-hidden="true"></i>&nbsp;{{ __('messages.type') }}</label>
                                <select id="type" name="type" onchange="toggleImageUpload(this.value)">
                                    <option value="100">{{ __('messages.spread') }}</option>
                                    <option value="4">{{ __('messages.img') }}</option>
                                </select>
                                <svg class="form-select-icon icon-small-arrow">
                                    <use xlink:href="#svg-small-arrow"></use>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="form-row" id="image-upload-row" style="display: none;">
                        <div class="form-item">
                            <div class="form-input">
                                <label for="img">{{ __('messages.upload_image') }}</label>
                                <input type="file" id="img" name="img" accept="image/*">
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="form-row split">
                        <button type="submit" class="button primary">{{ __('messages.spread') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/formats/xhtml.min.js"></script>
@endpush

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var textarea = document.getElementById('editor1');
        if (textarea) {
            sceditor.create(textarea, {
                format: 'xhtml',
                style: 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/content/default.min.css',
                emoticons: {
                    dropdown: {
                        @foreach(\App\Models\Emoji::limit(10)->get() as $emoji)
                            '{{ $emoji->name }}': '{{ asset($emoji->img) }}',
                        @endforeach
                    }
                }
            });
        }
    });

    function toggleImageUpload(type) {
        var imageRow = document.getElementById('image-upload-row');
        if (imageRow) {
            if (type == '4') {
                imageRow.style.display = 'block';
            } else {
                imageRow.style.display = 'none';
            }
        }
    }
</script>
@endsection
