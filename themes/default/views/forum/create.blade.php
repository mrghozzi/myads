@extends('theme::layouts.master')
@include('theme::forum._assets')

@section('content')
<div class="forum-rdx forum-rdx-form">
@php
    $activeEditor = \App\Services\RichTextEditorService::getActiveEditor();

    $sceditorCss = file_exists(public_path('assets/vendor/sceditor/themes/default.min.css')) && str_contains(asset(''), '/public')
        ? asset('assets/vendor/sceditor/themes/default.min.css')
        : asset('public/assets/vendor/sceditor/themes/default.min.css');

    $sceditorJs = file_exists(public_path('assets/vendor/sceditor/sceditor.min.js')) && str_contains(asset(''), '/public')
        ? asset('assets/vendor/sceditor/sceditor.min.js')
        : asset('public/assets/vendor/sceditor/sceditor.min.js');

    $sceditorXhtml = file_exists(public_path('assets/vendor/sceditor/formats/xhtml.min.js')) && str_contains(asset(''), '/public')
        ? asset('assets/vendor/sceditor/formats/xhtml.min.js')
        : asset('public/assets/vendor/sceditor/formats/xhtml.min.js');

    $sceditorImg = file_exists(public_path('assets/vendor/sceditor/themes/famfamfam.png')) && str_contains(asset(''), '/public')
        ? asset('assets/vendor/sceditor/themes/famfamfam.png')
        : asset('public/assets/vendor/sceditor/themes/famfamfam.png');

    $sceditorContentCss = file_exists(public_path('assets/vendor/sceditor/themes/content/default.min.css')) && str_contains(asset(''), '/public')
        ? asset('assets/vendor/sceditor/themes/content/default.min.css')
        : asset('public/assets/vendor/sceditor/themes/content/default.min.css');

    $quillCss = file_exists(public_path('assets/vendor/quill/quill.snow.css')) && str_contains(asset(''), '/public')
        ? asset('assets/vendor/quill/quill.snow.css')
        : asset('public/assets/vendor/quill/quill.snow.css');

    $quillJs = file_exists(public_path('assets/vendor/quill/quill.min.js')) && str_contains(asset(''), '/public')
        ? asset('assets/vendor/quill/quill.min.js')
        : asset('public/assets/vendor/quill/quill.min.js');
@endphp

@if($activeEditor === 'quill')
<link rel="stylesheet" href="{{ $quillCss }}" />
<script src="{{ $quillJs }}"></script>
@elseif($activeEditor === 'sceditor')
<link rel="stylesheet" href="{{ $sceditorCss }}" />
<script src="{{ $sceditorJs }}"></script>
<script src="{{ $sceditorXhtml }}"></script>
@if(app()->getLocale() !== 'en' && file_exists(public_path('assets/vendor/sceditor/languages/' . app()->getLocale() . '.js')))
<script src="{{ asset('assets/vendor/sceditor/languages/' . app()->getLocale() . '.js') }}"></script>
@elseif(app()->getLocale() !== 'en' && file_exists(public_path('public/assets/vendor/sceditor/languages/' . app()->getLocale() . '.js')))
<script src="{{ asset('public/assets/vendor/sceditor/languages/' . app()->getLocale() . '.js') }}"></script>
@endif
@else
@php
    \App\Helpers\Hooks::do_action('render_custom_editor_assets', $activeEditor);
@endphp
@endif

<style>
/* --- SCEditor Styles --- */
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
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 4px !important;
    align-items: center !important;
}
.sceditor-group {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    display: inline-flex !important;
    align-items: center !important;
    margin: 2px 2px !important;
    padding: 2px 3px !important;
}
.sceditor-container *,
.sceditor-container *::before,
.sceditor-container *::after {
    box-sizing: content-box !important;
}
.sceditor-button {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 28px !important;
    height: 28px !important;
    padding: 2px !important;
    margin: 1px !important;
    border-radius: 6px !important;
    box-sizing: border-box !important;
    cursor: pointer !important;
    float: none !important;
    background: transparent !important;
    border: none !important;
}
.sceditor-button:hover {
    background-color: #e2e8f0 !important;
}
.sceditor-button div {
    display: inline-block !important;
    width: 16px !important;
    height: 16px !important;
    margin: 0 auto !important;
    color: transparent !important;
    font-size: 0 !important;
    line-height: 0 !important;
    overflow: hidden !important;
    background-image: url('{{ $sceditorImg }}') !important;
    background-repeat: no-repeat !important;
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
    background-color: #334155 !important;
}
body[data-theme="css_d"] .sceditor-button div {
    filter: invert(0.9) hue-rotate(180deg);
}

/* --- Quill Styles --- */
.ql-toolbar.ql-snow button {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    outline: none !important;
    width: 28px !important;
    height: 28px !important;
    padding: 4px !important;
    margin: 1px !important;
    border-radius: 6px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}
.ql-toolbar.ql-snow button:hover {
    background-color: #e2e8f0 !important;
}
.ql-toolbar.ql-snow button.ql-active {
    background-color: #e0e7ff !important;
}
.ql-toolbar.ql-snow button svg {
    display: block !important;
    width: 18px !important;
    height: 18px !important;
    margin: 0 auto !important;
}
.ql-container.ql-snow {
    min-height: 350px !important;
    background: #ffffff;
    border-radius: 0 0 12px 12px !important;
    border: 1px solid #cbd5e1 !important;
}
.ql-editor {
    min-height: 300px !important;
    font-family: inherit !important;
    font-size: 15px !important;
}
body[data-theme="css_d"] .ql-container.ql-snow {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #f8fafc !important;
}
body[data-theme="css_d"] .ql-toolbar.ql-snow {
    background: #0f172a !important;
    border-color: #334155 !important;
}
body[data-theme="css_d"] .ql-toolbar.ql-snow button:hover {
    background-color: #334155 !important;
}
</style>

<div id="page-wrapper" class="forum-rdx-form-shell">
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
    var activeEditor = '{{ $activeEditor }}';
    var textarea = document.getElementById('editor1');
    if (!textarea) return;

    if (activeEditor === 'quill' && typeof Quill !== 'undefined') {
        textarea.style.display = 'none';
        var quillDiv = document.createElement('div');
        quillDiv.id = 'quill-forum-create-editor';
        quillDiv.style.minHeight = '340px';
        quillDiv.innerHTML = textarea.value || '';
        textarea.parentNode.insertBefore(quillDiv, textarea);

        var quill = new Quill('#quill-forum-create-editor', {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: [
                        [{ 'header': [1, 2, 3, 4, false] }],
                        ['bold', 'italic', 'underline', 'strike', 'blockquote', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }, { 'direction': 'rtl' }],
                        ['link', 'image'],
                        ['clean']
                    ],
                    handlers: {
                        image: function() {
                            var input = document.createElement('input');
                            input.setAttribute('type', 'file');
                            input.setAttribute('accept', 'image/jpeg,image/png,image/gif,image/webp,image/svg+xml');
                            input.click();
                            input.onchange = function() {
                                var file = input.files[0];
                                if (!file) return;
                                var formData = new FormData();
                                formData.append('image', file);
                                formData.append('_token', '{{ csrf_token() }}');
                                fetch('{{ route("editor.upload_image") }}', {
                                    method: 'POST',
                                    body: formData,
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                })
                                .then(function(res) { return res.json(); })
                                .then(function(data) {
                                    if (data.url) {
                                        var range = quill.getSelection(true);
                                        quill.insertEmbed(range.index, 'image', data.url);
                                        quill.setSelection(range.index + 1);
                                    }
                                })
                                .catch(function(err) { console.error('Image upload failed:', err); });
                            };
                        }
                    }
                }
            }
        });

        var syncQuill = function() {
            var html = quill.root.innerHTML;
            if (html === '<p><br></p>') html = '';
            textarea.value = html;
        };

        quill.on('text-change', syncQuill);
        var form = textarea.closest('form');
        if (form) {
            form.addEventListener('submit', syncQuill);
        }
    } else if (activeEditor === 'sceditor' && typeof sceditor !== 'undefined') {
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
            style: '{{ $sceditorContentCss }}',
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
    } else {
        @php
            \App\Helpers\Hooks::do_action('render_custom_editor_js', 'editor1', $activeEditor);
        @endphp
    }
});
</script>
</div>
@endsection
