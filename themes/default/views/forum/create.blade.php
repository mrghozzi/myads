@extends('theme::layouts.master')
@include('theme::forum._assets')

@section('content')
<div class="forum-rdx forum-rdx-form">
<link rel="stylesheet" href="{{ asset('assets/vendor/sceditor/themes/default.min.css') }}" />
<style>
.sceditor-container {
    width: 100% !important;
    min-height: 380px !important;
    display: flex !important;
    flex-direction: column !important;
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
}
.sceditor-container iframe,
.sceditor-container textarea {
    flex: 1 1 auto !important;
    min-height: 320px !important;
    width: 100% !important;
    box-sizing: border-box !important;
}
.sceditor-toolbar {
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 6px 8px !important;
    user-select: none;
    line-height: 1 !important;
}
.sceditor-group {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    display: inline-flex !important;
    align-items: center !important;
    margin: 2px 4px !important;
    padding: 2px 4px !important;
}
.sceditor-button {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 28px !important;
    height: 28px !important;
    padding: 4px !important;
    margin: 1px !important;
    border-radius: 6px !important;
    box-sizing: border-box !important;
    cursor: pointer !important;
    float: none !important;
}
.sceditor-button:hover {
    background: #e2e8f0 !important;
}
.sceditor-button div {
    display: block !important;
    width: 16px !important;
    height: 16px !important;
    margin: 0 auto !important;
    color: transparent !important;
    font-size: 0 !important;
    line-height: 0 !important;
}
body[data-theme="css_d"] .sceditor-container {
    background: #1e293b !important;
    border-color: #334155 !important;
}
body[data-theme="css_d"] .sceditor-toolbar {
    background: #0f172a !important;
    border-bottom-color: #334155 !important;
}
body[data-theme="css_d"] .sceditor-group {
    background: #1e293b !important;
    border-color: #334155 !important;
}
body[data-theme="css_d"] .sceditor-button:hover {
    background: #334155 !important;
}
body[data-theme="css_d"] .sceditor-button div {
    filter: invert(0.9) hue-rotate(180deg);
}
</style>
<script src="{{ asset('assets/vendor/sceditor/sceditor.min.js') }}"></script>
<script src="{{ asset('assets/vendor/sceditor/formats/xhtml.min.js') }}"></script>
@if(app()->getLocale() !== 'en' && file_exists(public_path('assets/vendor/sceditor/languages/' . app()->getLocale() . '.js')))
<script src="{{ asset('assets/vendor/sceditor/languages/' . app()->getLocale() . '.js') }}"></script>
@endif

<div id="page-wrapper" class="forum-rdx-form-shell">
    <div class="widget-box no-padding">
        <div class="modal-content modal-info">
            <div class="modal-header forum-rdx-form-header">
                <h2>{{ isset($topic) ? __('messages.e_topic') : __('messages.w_new_tpc') }}</h2>
            </div>
            <div class="modal-body">
                <div class="more-grids">
                    @if($errors->any())
                        <div class="alert alert-danger" style="margin-bottom: 12px;">
                            <ul style="margin: 0; padding-left: 18px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ isset($topic) ? route('forum.update', $topic->id) : route('forum.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if(isset($topic))
                            <input type="hidden" name="id" value="{{ $topic->id }}">
                        @endif

                        @if(isset($editType) && $editType == 7867)
                            <input type="hidden" name="name" value="{{ old('name', $topic->name ?? '') }}" />
                        @else
                            <div class="form-row split">
                                <div class="form-item">
                                    <div class="form-input social-input small active">
                                        <div class="social-link no-hover name">
                                            <i class="fa fa-edit" aria-hidden="true"></i>
                                        </div>
                                        <label for="name">{{ __('messages.sbj') }}</label>
                                        <input type="text" id="name" name="name" value="{{ old('name', $topic->name ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="form-row">
                            <div class="form-item">
                                <div class="form-input">
                                    <textarea id="editor1" name="txt" rows="16">{{ old('txt', $topic->txt ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        @if(!isset($editType) || $editType != 7867)
                            <div class="form-row split">
                                <div class="form-item">
                                    <div class="form-select">
                                        <label for="profile-status"><i class="fa fa-folder" aria-hidden="true"></i>&nbsp;{{ __('messages.category_fallback') ?? 'Category' }}</label>
                                        <select id="profile-status" name="categ">
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ (old('categ', $topic->cat ?? '') == $category->id) ? 'selected' : '' }}>
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
                        @endif

                        @if((int) ($forumSettings['attachments_enabled'] ?? 1) === 1)
                            <div class="form-row forum-rdx-attachment-box">
                                <div class="form-item">
                                    <label for="attachments">{{ __('messages.attachments') }}</label>
                                    <input
                                        type="file"
                                        id="attachments"
                                        name="attachments[]"
                                        multiple
                                        style="width: 100%;"
                                        accept=".{{ str_replace(',', ',.', $forumSettings['allowed_attachment_extensions'] ?? '') }}"
                                    >
                                    <small style="display:block;color:#7f85a3;margin-top:4px;">
                                        {{ __('messages.max_attachments_per_topic') }}: {{ $forumSettings['max_attachments_per_topic'] ?? 5 }} |
                                        {{ __('messages.max_attachment_size') }}: {{ $forumSettings['max_attachment_size_kb'] ?? 10240 }} KB
                                    </small>
                                </div>
                            </div>

                            @if(isset($topic) && $topic->attachments && $topic->attachments->isNotEmpty())
                                <div class="form-row forum-rdx-attachment-box" style="margin-top: 12px;">
                                    <div class="form-item">
                                        <p class="bold" style="margin-bottom: 8px;">{{ __('messages.current_attachments') }}</p>
                                        @foreach($topic->attachments as $attachment)
                                            <label style="display:block;margin-bottom:6px;">
                                                <input type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}">
                                                {{ __('messages.delete') }}: {{ $attachment->original_name }} ({{ $attachment->human_size }})
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                        <hr />

                        <div class="form-item split">
                            <input type="hidden" name="type" value="100" />
                            <input type="hidden" name="set" value="Publish" />
                            <button type="submit" name="submit" value="Publish" class="button primary">{{ __('messages.spread') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $dropdownEmojis = ($emojis ?? collect())->take(10);
    $moreEmojis = ($emojis ?? collect())->slice(10);
@endphp

<script>
document.addEventListener('DOMContentLoaded', function() {
    var textarea = document.getElementById('editor1');
    if (!textarea || typeof sceditor === 'undefined') {
        return;
    }
    if (sceditor.instance(textarea)) {
        sceditor.instance(textarea).destroy();
    }
    var currentLocale = '{{ app()->getLocale() }}';
    var opts = {
        format: 'xhtml',
        emoticons: {
            dropdown: {
                @foreach($dropdownEmojis as $emoji)
                    '{{ $emoji->name }}': '{{ asset($emoji->img) }}',
                @endforeach
            }@if($moreEmojis->isNotEmpty()),
            more: {
                @foreach($moreEmojis as $emoji)
                    '{{ $emoji->name }}': '{{ asset($emoji->img) }}',
                @endforeach
            }@endif
        },
        style: '{{ asset("assets/vendor/sceditor/themes/content/default.min.css") }}',
        width: '100%',
        height: '350px',
        resizeEnabled: true
    };

    if (currentLocale !== 'en' && sceditor.locale && sceditor.locale[currentLocale]) {
        opts.locale = currentLocale;
    }

    sceditor.create(textarea, opts);

    var form = textarea.closest('form');
    if (form) {
        form.addEventListener('submit', function() {
            var inst = sceditor.instance(textarea);
            if (inst) {
                inst.updateOriginal();
            }
        });
    }
});
</script>
</div>
@endsection
