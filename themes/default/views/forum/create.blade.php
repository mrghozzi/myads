@extends('theme::layouts.master')
@include('theme::forum._assets')

@section('content')
<div class="forum-rdx forum-rdx-form">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/default.min.css" />
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/sceditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/formats/xhtml.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/minified/jquery.sceditor.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sceditor@3/languages/{{ app()->getLocale() }}.js"></script>

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
    sceditor.create(textarea, {
        format: 'xhtml',
        locale: '{{ app()->getLocale() }}',
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
        style: 'https://cdn.jsdelivr.net/npm/sceditor@3/minified/themes/content/default.min.css'
    });
});
</script>
</div>
@endsection
