@extends('theme::layouts.master')

@section('content')
<div class="directory-create-shell superdesign-post-container">
    
    <!-- @.superdesign Glassmorphic Hero Banner -->
    <div class="superdesign-post-hero">
        <div class="superdesign-hero-header">
            <div class="superdesign-hero-title-wrap">
                <div class="superdesign-hero-icon-badge">
                    <i class="fa fa-globe"></i>
                </div>
                <div>
                    <h1 class="superdesign-hero-title">
                        {{ __('messages.addwebsitdir') }}
                    </h1>
                    <p class="superdesign-hero-subtitle">
                        {{ __('messages.seo_directory_description') }}
                    </p>
                </div>
            </div>
            <div class="superdesign-hero-badges">
                <span class="superdesign-pill-badge">
                    <i class="fa fa-magic"></i>
                    {{ __('messages.auto_fetch_metadata') ?? 'جلب تلقائي للبيانات' }}
                </span>
                <span class="superdesign-pill-badge">
                    <i class="fa fa-folder-open"></i>
                    {{ __('messages.directory') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Main Grid Architecture -->
    <div class="superdesign-post-grid">
        <!-- Column 1: Form Composer -->
        <div class="superdesign-composer-card">
            @if($errors->any())
                <div class="alert alert-danger mb-4" role="alert" style="border-radius: 14px;">
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @foreach ($errors->all() as $error)
                            <li><i class="fa fa-exclamation-circle me-2"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @auth
            <form method="POST" action="{{ route('directory.store') }}" id="add-site-form">
                @csrf

                <!-- URL & Title Row -->
                <div class="superdesign-fields-row">
                    <div class="superdesign-field-group">
                        <label for="url" class="superdesign-field-label">
                            <i class="fa fa-link"></i>
                            {{ __('messages.url') }}
                        </label>
                        <div class="superdesign-input-wrapper">
                            <div class="superdesign-input-icon" id="url-icon-container">
                                <i class="fa fa-link" id="url-icon"></i>
                                <div class="spinner-border spinner-border-sm text-primary d-none" id="url-loader" role="status"></div>
                            </div>
                            <input 
                                type="url" 
                                id="url" 
                                name="url" 
                                class="superdesign-input with-icon" 
                                value="{{ old('url') }}" 
                                required 
                                placeholder="https://example.com"
                            >
                        </div>
                    </div>

                    <div class="superdesign-field-group">
                        <label for="name" class="superdesign-field-label">
                            <i class="fa fa-heading"></i>
                            {{ __('messages.name') }}
                        </label>
                        <div class="superdesign-input-wrapper">
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                class="superdesign-input" 
                                value="{{ old('name') }}" 
                                required 
                                placeholder="{{ __('messages.website_title_placeholder') ?? 'اسم أو عنوان الموقع...' }}"
                            >
                        </div>
                    </div>
                </div>

                <!-- Description Row -->
                <div class="superdesign-field-group">
                    <label for="description" class="superdesign-field-label">
                        <i class="fa fa-align-left"></i>
                        {{ __('messages.text_p') }}
                    </label>
                    <div class="superdesign-input-wrapper">
                        <textarea 
                            id="description" 
                            name="txt" 
                            class="superdesign-textarea" 
                            placeholder="{{ __('messages.website_desc_placeholder') ?? 'اكتب وصفاً مختصراً ودقيقاً للخدمات والمحتوى الذي يقدمه الموقع...' }}"
                        >{{ old('txt') }}</textarea>
                    </div>
                </div>

                <!-- Category & Tags Row -->
                <div class="superdesign-fields-row">
                    <div class="superdesign-field-group">
                        <label for="profile-status" class="superdesign-field-label">
                            <i class="fa fa-folder-open"></i>
                            {{ __('messages.cat') }}
                        </label>
                        <select id="profile-status" name="categ" class="superdesign-select" required>
                            @foreach($mainCategories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @foreach($subCategories->get($cat->id, collect()) as $sub)
                                    <option value="{{ $sub->id }}">_{{ $sub->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <div class="superdesign-field-group">
                        <label for="tag" class="superdesign-field-label">
                            <i class="fa fa-tag"></i>
                            {{ __('messages.tag') }}
                        </label>
                        <div class="superdesign-input-wrapper">
                            <input 
                                type="text" 
                                id="tag" 
                                name="tag" 
                                class="superdesign-input" 
                                value="{{ old('tag') }}" 
                                placeholder="اخبار, تكنولوجيا, مدونة..."
                            >
                        </div>
                    </div>
                </div>

                <input type="hidden" name="s_type" value="1" />

                <!-- Action Buttons -->
                <div class="superdesign-actions-bar">
                    <a href="{{ url('/directory') }}" class="superdesign-btn-secondary">
                        <i class="fa fa-arrow-right"></i>
                        {{ __('messages.back') }}
                    </a>

                    <button type="submit" class="superdesign-btn-primary">
                        <i class="fa fa-paper-plane"></i>
                        {{ __('messages.spread') }}
                    </button>
                </div>
            </form>
            @else
                <div class="alert alert-warning text-center" role="alert" style="border-radius: 14px; padding: 24px;">
                    <i class="fa fa-lock fa-2x mb-3 text-warning"></i>
                    <p style="font-size: 15px; font-weight: 600; margin-bottom: 16px;">
                        {{ __('messages.login_required_add_site') ?? 'يرجى تسجيل الدخول لإضافة موقعك إلى الدليل' }}
                    </p>
                    <a href="{{ route('login') }}" class="superdesign-btn-primary" style="text-decoration: none; display: inline-flex;">
                        <i class="fa fa-sign-in-alt"></i>
                        {{ __('messages.login') }}
                    </a>
                </div>
            @endauth
        </div>

        <!-- Column 2: Sidebar Guidance -->
        <div class="superdesign-sidebar-col">
            <!-- Auto Fetch Smart Tip -->
            <div class="superdesign-pts-tip" style="margin-bottom: 20px;">
                <div class="superdesign-pts-tip-title">
                    <i class="fa fa-magic"></i>
                    <span>{{ __('Tip') }}</span>
                </div>
                <span>
                    {{ __('Just enter the website URL and we will try to fetch the title, description and tags for you automatically!') }}
                </span>
            </div>

            <!-- Guidelines Card -->
            <div class="superdesign-sidebar-card">
                <h3 class="superdesign-sidebar-title">
                    <i class="fa fa-check-double"></i>
                    {{ __('messages.submission_rules') ?? 'قواعد القبول في الدليل' }}
                </h3>
                <ul class="superdesign-guidelines-list">
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>تأكد من إدخال الرابط الصحيح كاملاً شامل البروتوكول (https://).</span>
                    </li>
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>اختر القسم الأكثر ملاءمة لتخصص موقعك لأرشفة أفضل.</span>
                    </li>
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>اكتب وصفاً حصرياً يلخص خدمات ومزايا موقعك بوضوح.</span>
                    </li>
                    <li>
                        <i class="fa fa-check-circle"></i>
                        <span>أضف الكلمات المفتاحية لمساعدة الزوار في العثور عليك عبر البحث.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
/* --- @.superdesign Core Tokens for /add-site.html --- */
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
    margin-bottom: 28px;
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

.superdesign-fields-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
@media (max-width: 768px) {
    .superdesign-fields-row {
        grid-template-columns: 1fr;
    }
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
    display: flex;
    align-items: center;
}
.superdesign-input-icon {
    position: absolute;
    left: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #615dfa;
    font-size: 16px;
    pointer-events: none;
    z-index: 2;
}
html[dir="rtl"] .superdesign-input-icon {
    left: auto;
    right: 16px;
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
.superdesign-input.with-icon {
    padding-left: 48px;
}
html[dir="rtl"] .superdesign-input.with-icon {
    padding-left: 16px;
    padding-right: 48px;
}
.superdesign-input:focus {
    background: #ffffff;
    border-color: #615dfa;
    box-shadow: 0 0 0 4px rgba(97, 93, 250, 0.15);
}
.superdesign-textarea {
    width: 100%;
    min-height: 120px;
    padding: 12px 16px;
    font-size: 14.5px;
    font-weight: 500;
    color: #0f172a;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.25s ease;
    outline: none;
    resize: vertical;
}
.superdesign-textarea:focus {
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
.superdesign-pts-tip {
    background: linear-gradient(135deg, rgba(97, 93, 250, 0.08) 0%, rgba(35, 210, 226, 0.08) 100%);
    border: 1px solid rgba(97, 93, 250, 0.2);
    border-radius: 16px;
    padding: 16px;
    font-size: 13px;
    color: #475569;
    line-height: 1.5;
}
.superdesign-pts-tip-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    color: #615dfa;
    margin-bottom: 6px;
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
body[data-theme="css_d"] .superdesign-textarea,
body[data-theme="css_d"] .superdesign-select,
html.app-skin-dark .superdesign-input,
html.app-skin-dark .superdesign-textarea,
html.app-skin-dark .superdesign-select,
.dark-mode .superdesign-input,
.dark-mode .superdesign-textarea,
.dark-mode .superdesign-select {
    background-color: #0f172a;
    border-color: #334155;
    color: #f8fafc;
}
body[data-theme="css_d"] .superdesign-input:focus,
body[data-theme="css_d"] .superdesign-textarea:focus,
body[data-theme="css_d"] .superdesign-select:focus,
html.app-skin-dark .superdesign-input:focus,
html.app-skin-dark .superdesign-textarea:focus,
html.app-skin-dark .superdesign-select:focus,
.dark-mode .superdesign-input:focus,
.dark-mode .superdesign-textarea:focus,
.dark-mode .superdesign-select:focus {
    background-color: #1e293b;
    border-color: #615dfa;
}
body[data-theme="css_d"] .superdesign-guidelines-list li,
html.app-skin-dark .superdesign-guidelines-list li,
.dark-mode .superdesign-guidelines-list li {
    color: #cbd5e1;
}
body[data-theme="css_d"] .superdesign-pts-tip,
html.app-skin-dark .superdesign-pts-tip,
.dark-mode .superdesign-pts-tip {
    background: rgba(97, 93, 250, 0.12);
    border-color: rgba(97, 93, 250, 0.3);
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

.spinner-border-sm {
    width: 1rem;
    height: 1rem;
    border-width: 0.15em;
}
.d-none { display: none !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const urlInput = document.getElementById('url');
    const nameInput = document.getElementById('name');
    const descInput = document.getElementById('description');
    const tagInput = document.getElementById('tag');
    const urlLoader = document.getElementById('url-loader');
    const urlIcon = document.getElementById('url-icon');

    let timeout = null;

    if (!urlInput) return;

    urlInput.addEventListener('input', function() {
        clearTimeout(timeout);
        const url = this.value;
        
        if (!url || !url.startsWith('http')) return;

        timeout = setTimeout(() => {
            fetchMetadata(url);
        }, 1000);
    });

    function fetchMetadata(url) {
        if (urlIcon) urlIcon.classList.add('d-none');
        if (urlLoader) urlLoader.classList.remove('d-none');

        fetch('{{ route("directory.fetch_metadata") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ url: url })
        })
        .then(response => response.json())
        .then(data => {
            if (data.title && !nameInput.value) {
                nameInput.value = data.title;
            }
            if (data.description && !descInput.value) {
                descInput.value = data.description;
            }
            if (data.tags && !tagInput.value) {
                tagInput.value = data.tags;
            }
        })
        .catch(error => console.error('Error fetching metadata:', error))
        .finally(() => {
            if (urlIcon) urlIcon.classList.remove('d-none');
            if (urlLoader) urlLoader.classList.add('d-none');
        });
    }
});
</script>
@endsection
