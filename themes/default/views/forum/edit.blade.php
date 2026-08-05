@extends('theme::layouts.master')
@include('theme::forum._assets')

@section('content')
<div class="forum-rdx forum-rdx-form superdesign-post-container">
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
/* --- @.superdesign Core Tokens & Layout for /editor/{id} --- */
.superdesign-post-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px 15px 60px;
}

/* Glassmorphic Hero Banner */
.superdesign-post-hero {
    position: relative;
    background: linear-gradient(135deg, rgba(97, 93, 250, 0.14) 0%, rgba(35, 210, 226, 0.09) 50%, rgba(27, 200, 219, 0.03) 100%);
    border: 1px solid rgba(97, 93, 250, 0.2);
    border-radius: 24px;
    padding: 32px;
    margin-bottom: 24px;
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    box-shadow: 0 14px 35px rgba(15, 23, 42, 0.04);
    overflow: hidden;
}
.superdesign-post-hero::before {
    content: '';
    position: absolute;
    top: -60px;
    right: -60px;
    width: 220px;
    height: 220px;
    background: radial-gradient(circle, rgba(97, 93, 250, 0.25) 0%, rgba(97, 93, 250, 0) 70%);
    border-radius: 50%;
    pointer-events: none;
}
.superdesign-hero-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}
.superdesign-hero-title-wrap {
    display: flex;
    align-items: center;
    gap: 16px;
}
.superdesign-hero-icon-badge {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 24px;
    box-shadow: 0 8px 20px rgba(97, 93, 250, 0.35);
    flex-shrink: 0;
}
.superdesign-hero-title {
    font-size: 24px;
    font-weight: 800;
    margin: 0 0 4px 0;
    color: #1e293b;
    letter-spacing: -0.02em;
}
.superdesign-hero-subtitle {
    font-size: 14px;
    color: #64748b;
    margin: 0;
}
.superdesign-hero-badges {
    display: flex;
    align-items: center;
    gap: 10px;
}
.superdesign-pill-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 50rem;
    font-size: 12.5px;
    font-weight: 600;
    background: rgba(255, 255, 255, 0.85);
    border: 1px solid rgba(97, 93, 250, 0.25);
    color: #615dfa;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}

/* Grid Architecture */
.superdesign-post-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 28px;
}
@media (max-width: 991px) {
    .superdesign-post-grid {
        grid-template-columns: 1fr;
    }
}

/* Main Composer Card */
.superdesign-composer-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    padding: 28px;
}
.superdesign-field-group {
    margin-bottom: 22px;
}
.superdesign-field-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    color: #334155;
    margin-bottom: 8px;
}
.superdesign-field-label i {
    color: #615dfa;
}
.superdesign-input-wrapper {
    position: relative;
}
.superdesign-input {
    width: 100%;
    height: 48px;
    padding: 10px 16px;
    font-size: 15px;
    font-weight: 500;
    color: #0f172a;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.25s ease;
    outline: none;
}
.superdesign-input:focus {
    background: #ffffff;
    border-color: #615dfa;
    box-shadow: 0 0 0 4px rgba(97, 93, 250, 0.15);
}
.superdesign-select {
    width: 100%;
    height: 48px;
    padding: 10px 16px;
    font-size: 14.5px;
    font-weight: 600;
    color: #334155;
    background: #f8fafc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%20615dfa' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") no-repeat calc(100% - 16px) center;
    background-size: 16px;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    appearance: none;
    -webkit-appearance: none;
    outline: none;
    cursor: pointer;
    transition: all 0.25s ease;
}
html[dir="rtl"] .superdesign-select {
    background-position: 16px center;
}
.superdesign-select:focus {
    background-color: #ffffff;
    border-color: #615dfa;
    box-shadow: 0 0 0 4px rgba(97, 93, 250, 0.15);
}

/* Video Thumbnail Card */
.superdesign-thumb-card {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px;
    margin-bottom: 22px;
}

/* Editor Box */
.superdesign-editor-box {
    border-radius: 14px;
    overflow: hidden;
    border: 1.5px solid #e2e8f0;
    transition: border-color 0.25s ease;
}
.superdesign-editor-box:focus-within {
    border-color: #615dfa;
    box-shadow: 0 0 0 4px rgba(97, 93, 250, 0.12);
}

/* Attachments Dropzone Box */
.superdesign-dropzone-box {
    background: #f8fafc;
    border: 2px dashed #cbd5e1;
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    transition: all 0.25s ease;
    cursor: pointer;
    margin-top: 10px;
}
.superdesign-dropzone-box:hover {
    border-color: #615dfa;
    background: rgba(97, 93, 250, 0.02);
}
.superdesign-dropzone-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: rgba(97, 93, 250, 0.1);
    color: #615dfa;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-bottom: 8px;
}
.superdesign-dropzone-text {
    font-size: 14px;
    font-weight: 600;
    color: #475569;
    margin-bottom: 4px;
}
.superdesign-dropzone-hint {
    font-size: 12px;
    color: #94a3b8;
}

/* Existing attachments list */
.superdesign-attachments-list {
    margin-top: 14px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px;
}
.superdesign-attachment-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 12px;
    border-radius: 8px;
    background: #f8fafc;
    margin-bottom: 6px;
}
.superdesign-attachment-item:last-child {
    margin-bottom: 0;
}

/* Form Actions */
.superdesign-actions-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 28px;
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
}
.superdesign-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 28px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    color: #ffffff;
    background: linear-gradient(135deg, #615dfa 0%, #23d2e2 100%);
    border: none;
    box-shadow: 0 8px 20px rgba(97, 93, 250, 0.3);
    cursor: pointer;
    transition: all 0.25s ease;
}
.superdesign-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(97, 93, 250, 0.4);
    color: #ffffff;
}
.superdesign-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    background: #f1f5f9;
    text-decoration: none;
    transition: all 0.2s ease;
}
.superdesign-btn-secondary:hover {
    background: #e2e8f0;
    color: #334155;
}

/* Sidebar Widgets */
.superdesign-sidebar-card {
    background: #ffffff;
    border-radius: 20px;
    border: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    padding: 22px;
    margin-bottom: 20px;
}
.superdesign-sidebar-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 16px 0;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
}
.superdesign-sidebar-title i {
    color: #615dfa;
}
.superdesign-guidelines-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.superdesign-guidelines-list li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 13.5px;
    color: #475569;
    line-height: 1.5;
    margin-bottom: 12px;
}
.superdesign-guidelines-list li:last-child {
    margin-bottom: 0;
}
.superdesign-guidelines-list i {
    color: #10b981;
    margin-top: 3px;
    flex-shrink: 0;
}

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

/* --- Dark Mode Parity --- */
body[data-theme="css_d"] .superdesign-post-hero,
html.app-skin-dark .superdesign-post-hero,
.dark-mode .superdesign-post-hero {
    background: linear-gradient(135deg, rgba(97, 93, 250, 0.15) 0%, rgba(35, 210, 226, 0.1) 100%), #1a1d2e;
    border-color: rgba(255, 255, 255, 0.08);
}
body[data-theme="css_d"] .superdesign-hero-title,
html.app-skin-dark .superdesign-hero-title,
.dark-mode .superdesign-hero-title {
    color: #f8fafc;
}
body[data-theme="css_d"] .superdesign-hero-subtitle,
html.app-skin-dark .superdesign-hero-subtitle,
.dark-mode .superdesign-hero-subtitle {
    color: #94a3b8;
}
body[data-theme="css_d"] .superdesign-pill-badge,
html.app-skin-dark .superdesign-pill-badge,
.dark-mode .superdesign-pill-badge {
    background: rgba(30, 41, 59, 0.9);
    border-color: rgba(97, 93, 250, 0.35);
    color: #818cf8;
}

body[data-theme="css_d"] .superdesign-composer-card,
body[data-theme="css_d"] .superdesign-sidebar-card,
html.app-skin-dark .superdesign-composer-card,
html.app-skin-dark .superdesign-sidebar-card,
.dark-mode .superdesign-composer-card,
.dark-mode .superdesign-sidebar-card {
    background: #1a1d2e;
    border-color: rgba(255, 255, 255, 0.08);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}
body[data-theme="css_d"] .superdesign-field-label,
body[data-theme="css_d"] .superdesign-sidebar-title,
html.app-skin-dark .superdesign-field-label,
html.app-skin-dark .superdesign-sidebar-title,
.dark-mode .superdesign-field-label,
.dark-mode .superdesign-sidebar-title {
    color: #f1f5f9;
}
body[data-theme="css_d"] .superdesign-input,
body[data-theme="css_d"] .superdesign-select,
html.app-skin-dark .superdesign-input,
html.app-skin-dark .superdesign-select,
.dark-mode .superdesign-input,
.dark-mode .superdesign-select {
    background-color: #0f172a;
    border-color: #334155;
    color: #f8fafc;
}
body[data-theme="css_d"] .superdesign-input:focus,
body[data-theme="css_d"] .superdesign-select:focus,
html.app-skin-dark .superdesign-input:focus,
html.app-skin-dark .superdesign-select:focus,
.dark-mode .superdesign-input:focus,
.dark-mode .superdesign-select:focus {
    background-color: #1e293b;
    border-color: #615dfa;
}
body[data-theme="css_d"] .superdesign-thumb-card,
html.app-skin-dark .superdesign-thumb-card,
.dark-mode .superdesign-thumb-card {
    background: #0f172a;
    border-color: #334155;
}
body[data-theme="css_d"] .superdesign-dropzone-box,
html.app-skin-dark .superdesign-dropzone-box,
.dark-mode .superdesign-dropzone-box {
    background: #0f172a;
    border-color: #334155;
}
body[data-theme="css_d"] .superdesign-dropzone-text,
html.app-skin-dark .superdesign-dropzone-text,
.dark-mode .superdesign-dropzone-text {
    color: #cbd5e1;
}
body[data-theme="css_d"] .superdesign-guidelines-list li,
html.app-skin-dark .superdesign-guidelines-list li,
.dark-mode .superdesign-guidelines-list li {
    color: #cbd5e1;
}
body[data-theme="css_d"] .superdesign-actions-bar,
html.app-skin-dark .superdesign-actions-bar,
.dark-mode .superdesign-actions-bar {
    border-top-color: #334155;
}
body[data-theme="css_d"] .superdesign-btn-secondary,
html.app-skin-dark .superdesign-btn-secondary,
.dark-mode .superdesign-btn-secondary {
    background: #334155;
    color: #cbd5e1;
}
body[data-theme="css_d"] .superdesign-btn-secondary:hover,
html.app-skin-dark .superdesign-btn-secondary:hover,
.dark-mode .superdesign-btn-secondary:hover {
    background: #475569;
    color: #ffffff;
}

body[data-theme="css_d"] .sceditor-container,
html.app-skin-dark .sceditor-container,
.dark-mode .sceditor-container {
    background: #1e293b !important;
    border-color: #334155 !important;
}
body[data-theme="css_d"] .sceditor-toolbar,
html.app-skin-dark .sceditor-toolbar,
.dark-mode .sceditor-toolbar {
    background: #0f172a !important;
    border-bottom-color: #334155 !important;
}
body[data-theme="css_d"] .sceditor-group,
html.app-skin-dark .sceditor-group,
.dark-mode .sceditor-group {
    background: #1e293b !important;
    border-color: #334155 !important;
}
body[data-theme="css_d"] .sceditor-button:hover,
html.app-skin-dark .sceditor-button:hover,
.dark-mode .sceditor-button:hover {
    background-color: #334155 !important;
}
body[data-theme="css_d"] .sceditor-button div,
html.app-skin-dark .sceditor-button div,
.dark-mode .sceditor-button div {
    filter: invert(0.9) hue-rotate(180deg);
}

body[data-theme="css_d"] .ql-container.ql-snow,
html.app-skin-dark .ql-container.ql-snow,
.dark-mode .ql-container.ql-snow {
    background: #1e293b !important;
    border-color: #334155 !important;
    color: #f8fafc !important;
}
body[data-theme="css_d"] .ql-toolbar.ql-snow,
html.app-skin-dark .ql-toolbar.ql-snow,
.dark-mode .ql-toolbar.ql-snow {
    background: #0f172a !important;
    border-color: #334155 !important;
}
body[data-theme="css_d"] .ql-toolbar.ql-snow button:hover,
html.app-skin-dark .ql-toolbar.ql-snow button:hover,
.dark-mode .ql-toolbar.ql-snow button:hover {
    background-color: #334155 !important;
}
</style>

<div id="page-wrapper" class="forum-rdx-form-shell">

    <!-- @.superdesign Glassmorphic Hero Banner -->
    <div class="superdesign-post-hero">
        <div class="superdesign-hero-header">
            <div class="superdesign-hero-title-wrap">
                <div class="superdesign-hero-icon-badge">
                    <i class="fa fa-edit"></i>
                </div>
                <div>
                    <h1 class="superdesign-hero-title">
                        {{ (isset($status) && $status->s_type == 4) ? __('messages.edit_gallery_post') : (isset($topic) ? __('messages.edit_topic') : __('messages.w_new_tpc')) }}
                    </h1>
                    <p class="superdesign-hero-subtitle">
                        {{ __('messages.edit_topic_subtitle') ?? 'قم بتحديث وتعديل تفاصيل الموضوع والمرفقات بسهولة وبشكل احترافي' }}
                    </p>
                </div>
            </div>
            <div class="superdesign-hero-badges">
                @if(isset($topic))
                    <span class="superdesign-pill-badge">
                        <i class="fa fa-hashtag"></i>
                        {{ $topic->id }}
                    </span>
                @endif
                <span class="superdesign-pill-badge">
                    <i class="fa fa-code"></i>
                    {{ strtoupper($activeEditor) }}
                </span>
            </div>
        </div>
    </div>

    <!-- ADS -->
    @include('theme::partials.ads', ['id' => 4])

    @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 24px; border-radius: 14px;">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Grid Architecture -->
    <div class="superdesign-post-grid">
        <!-- Column 1: Main Editor Form -->
        <div class="superdesign-composer-card">
            <form method="POST" action="{{ isset($topic) ? route('forum.update', $topic->id) : route('forum.store') }}" enctype="multipart/form-data">
                @csrf
                @if(isset($topic))
                    <input type="hidden" name="id" value="{{ $topic->id }}">
                @endif

                @if((int) ($topic->cat ?? 0) > 0 || (isset($status) && in_array((int)$status->s_type, [10, 4])))
                    <div class="superdesign-field-group">
                        <label for="name" class="superdesign-field-label">
                            <i class="fa fa-heading"></i>
                            {{ __('messages.sbj') }} / {{ __('messages.video_title') ?? 'عنوان الفيديو' }}
                        </label>
                        <div class="superdesign-input-wrapper">
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                class="superdesign-input" 
                                value="{{ old('name', $topic->name ?? '') }}" 
                                required
                            >
                        </div>
                    </div>
                @else
                    <input type="hidden" name="name" value="{{ $topic->name ?? 'text' }}">
                @endif

                @if(isset($status) && (int) $status->s_type === 10)
                    <div class="superdesign-field-group">
                        <label class="superdesign-field-label">
                            <i class="fa fa-image"></i>
                            {{ __('messages.video_thumbnail') ?? 'الصورة المصغرة للفيديو (Thumbnail)' }}
                        </label>
                        <div class="superdesign-thumb-card">
                            <input type="file" id="video_thumbnail" name="video_thumbnail" class="form-control" accept="image/*">
                            @if($topic->image_url)
                                <div style="margin-top: 12px; display: flex; align-items: center; gap: 12px;">
                                    <span style="font-size: 13px; font-weight: 600; color: #64748b;">الغلاف الحالي للفيديو:</span>
                                    <img src="{{ asset($topic->image_url) }}" style="max-height: 80px; border-radius: 10px; border: 1px solid #cbd5e1; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="superdesign-field-group">
                    <label for="editor1" class="superdesign-field-label">
                        <i class="fa fa-align-left"></i>
                        {{ __('messages.content') }}
                    </label>
                    <div class="superdesign-editor-box">
                        <textarea id="editor1" name="txt" rows="16" required>{{ old('txt', $topic->txt ?? '') }}</textarea>
                    </div>
                </div>

                @if((int) ($topic->cat ?? 0) > 0 && (!isset($status) || $status->s_type != 4))
                    <div class="superdesign-field-group">
                        <label for="category" class="superdesign-field-label">
                            <i class="fa fa-folder-open"></i>
                            {{ __('messages.category_fallback') }}
                        </label>
                        <select id="category" name="cat" class="superdesign-select" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (old('cat', $topic->cat ?? '') == $category->id) ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <input type="hidden" name="cat" value="{{ $topic->cat ?? 0 }}">
                @endif

                @if(!isset($topic))
                    <div class="superdesign-field-group">
                        <label for="type" class="superdesign-field-label">
                            <i class="fa fa-list"></i>
                            {{ __('messages.type') }}
                        </label>
                        <select id="type" name="type" class="superdesign-select" onchange="toggleImageUpload(this.value)">
                            <option value="100">{{ __('messages.spread') }}</option>
                            <option value="4">{{ __('messages.img') }}</option>
                        </select>
                    </div>

                    <div class="superdesign-field-group" id="image-upload-row" style="display: none;">
                        <label for="img" class="superdesign-field-label">
                            <i class="fa fa-upload"></i>
                            {{ __('messages.upload_image') }}
                        </label>
                        <input type="file" id="img" name="img" class="form-control" accept="image/*">
                    </div>
                @endif

                @if((int) ($forumSettings['attachments_enabled'] ?? 1) === 1)
                    <div class="superdesign-field-group">
                        <label class="superdesign-field-label">
                            <i class="fa fa-paperclip"></i>
                            {{ __('messages.attachments') }}
                        </label>
                        <div class="superdesign-dropzone-box" onclick="document.getElementById('attachments').click();">
                            <div class="superdesign-dropzone-icon">
                                <i class="fa fa-cloud-upload-alt"></i>
                            </div>
                            <div class="superdesign-dropzone-text">
                                {{ __('messages.click_to_upload_files') ?? 'اضغط هنا لرفع المرفقات والمستندات' }}
                            </div>
                            <div class="superdesign-dropzone-hint">
                                {{ __('messages.max_attachments_per_topic') }}: {{ $forumSettings['max_attachments_per_topic'] ?? 5 }} |
                                {{ __('messages.max_attachment_size') }}: {{ $forumSettings['max_attachment_size_kb'] ?? 10240 }} KB
                            </div>
                            <input
                                type="file"
                                id="attachments"
                                name="attachments[]"
                                multiple
                                style="display: none;"
                                accept=".{{ str_replace(',', ',.', $forumSettings['allowed_attachment_extensions'] ?? '') }}"
                                onchange="if(this.files.length) { this.previousElementSibling.innerText = this.files.length + ' ملف/ملفات مختارة'; }"
                            >
                        </div>

                        @if(isset($topic) && $topic->attachments && $topic->attachments->isNotEmpty())
                            <div class="superdesign-attachments-list">
                                <div class="superdesign-field-label" style="font-size: 13px; margin-bottom: 8px;">
                                    <i class="fa fa-file-alt"></i>
                                    {{ __('messages.current_attachments') }}
                                </div>
                                @foreach($topic->attachments as $attachment)
                                    <div class="superdesign-attachment-item">
                                        <span style="font-size: 13px; font-weight: 500; color: #475569;">
                                            <i class="fa fa-paperclip me-1 text-primary"></i>
                                            {{ $attachment->original_name }} ({{ $attachment->human_size }})
                                        </span>
                                        <label style="font-size: 12px; font-weight: 600; color: #ef4444; margin: 0; cursor: pointer;">
                                            <input type="checkbox" name="delete_attachments[]" value="{{ $attachment->id }}">
                                            {{ __('messages.delete') }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <div class="superdesign-actions-bar">
                    <a href="{{ isset($topic) ? route('forum.topic', $topic->id) : route('forum.index') }}" class="superdesign-btn-secondary">
                        <i class="fa fa-times"></i>
                        {{ __('messages.cancel') ?? 'إلغاء' }}
                    </a>

                    <button type="submit" class="superdesign-btn-primary">
                        <i class="fa fa-save"></i>
                        {{ __('messages.spread') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Column 2: Sidebar Guidance & Widgets -->
        <div class="superdesign-sidebar-col">
            <!-- Guidelines Card -->
            <div class="superdesign-sidebar-card">
                <h3 class="superdesign-sidebar-title">
                    <i class="fa fa-lightbulb"></i>
                    {{ __('messages.editing_tips') ?? 'إرشادات التعديل والتحديث' }}
                </h3>
                <ul class="superdesign-guidelines-list">
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>تأكد من تعديل العنوان بدقة ليعكس المحتوى الجديد للموضوع.</span>
                    </li>
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>يمكنك مراجعة وتحديث المرفقات الحالية أو حذف القديم منها بسهولة.</span>
                    </li>
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>حافظ على تنسيق المحتوى بالصور والجداول لزيادة نسبة التفاعل.</span>
                    </li>
                </ul>
            </div>

            <!-- Widgets -->
            @include('theme::partials.widgets', ['place' => 3])
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var activeEditor = '{{ $activeEditor }}';
    var textarea = document.getElementById('editor1');
    if (!textarea) return;

    if (activeEditor === 'quill' && typeof Quill !== 'undefined') {
        textarea.style.display = 'none';
        var quillDiv = document.createElement('div');
        quillDiv.id = 'quill-forum-edit-editor';
        quillDiv.style.minHeight = '340px';
        quillDiv.innerHTML = textarea.value;
        textarea.parentNode.insertBefore(quillDiv, textarea);

        var quill = new Quill('#quill-forum-edit-editor', {
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
            textarea.value = quill.root.innerHTML;
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
            style: '{{ $sceditorContentCss }}',
            width: '100%',
            height: '350px',
            resizeEnabled: true,
            emoticons: {
                dropdown: {
                    @foreach(\App\Models\Emoji::limit(10)->get() as $emoji)
                        '{{ $emoji->name }}': '{{ asset($emoji->img) }}',
                    @endforeach
                }
            }
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
</div>
@endsection
