@auth
@php
    $categories = \App\Models\DirectoryCategory::where('statu', 1)->orderBy('name', 'ASC')->get();
    $oldText = (string) old('text', old('txt', ''));
    $oldLinkUrl = (string) old('link_url', '');
    $oldPublishMode = (string) old('publish_mode', 'post');
    $oldPostKind = (string) old('post_kind', 'text');
    $composerHasOldInput = old('text') !== null
        || old('txt') !== null
        || old('link_url') !== null
        || old('publish_mode') !== null
        || old('post_kind') !== null
        || old('directory_name') !== null
        || old('directory_tags') !== null;
@endphp

<div class="quick-post" id="social-composer">
    <form class="form" action="{{ route('status.create') }}" method="POST" enctype="multipart/form-data" id="social-composer-form">
        @csrf
        <input type="hidden" name="post_kind" id="composer-post-kind" value="{{ $oldPostKind }}">
        <input type="hidden" name="repost_status_id" id="composer-repost-status-id" value="">

        <div class="quick-post-body">
            @if($composerHasOldInput && session('error'))
                <div class="alert alert-danger" role="alert" style="margin-bottom: 16px;">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" role="alert" style="margin-bottom: 16px;">
                    <ul style="margin: 0; padding-inline-start: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-row">
                <div class="form-item">
                    <div class="form-textarea">
                        <textarea id="composer-text" name="text" class="quicktext" placeholder="{{ __('messages.whats_on_your_mind', ['username' => auth()->user()->username]) }}">{{ $oldText }}</textarea>
                        <p class="form-textarea-limit-text">{{ __('messages.mentions_hint') }}</p>
                    </div>
                </div>
            </div>

            <div id="composer-repost-card" class="widget-box" style="display:none; margin-top: 16px; margin-bottom: 0;">
                <div class="widget-box-content">
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px;">
                        <div>
                            <p class="widget-box-title" style="font-size: 16px;">{{ __('messages.quote_repost') }}</p>
                            <p id="composer-repost-text" class="user-status-text">{{ __('messages.repost_ready') }}</p>
                        </div>
                        <button type="button" id="composer-repost-cancel" class="button small tertiary">{{ __('messages.cancel') }}</button>
                    </div>
                </div>
            </div>

            <div id="composer-link-block" class="widget-box" style="display:none; margin-top: 16px; margin-bottom: 0;">
                <div class="widget-box-content">
                    <div class="form-row">
                        <div class="form-item">
                            <div class="form-input small">
                                <label for="composer-link-url">{{ __('messages.insertlink') }}</label>
                                <input type="url" id="composer-link-url" name="link_url" value="{{ $oldLinkUrl }}" placeholder="{{ __('messages.url_placeholder') }}">
                            </div>
                        </div>
                    </div>

                    <div id="composer-link-preview" style="display:none; margin-top: 16px;"></div>

                    <div id="composer-publish-options" style="display:none; margin-top: 16px;">
                        <p class="user-status-text small" style="margin-bottom: 10px;">{{ __('messages.smart_link_publish_hint') }}</p>
                        <div class="composer-choice-grid">
                            <label class="composer-choice-card" for="composer-publish-post">
                                <input
                                    class="composer-choice-input"
                                    type="radio"
                                    id="composer-publish-post"
                                    name="publish_mode"
                                    value="post"
                                    {{ $oldPublishMode !== 'directory_only' ? 'checked' : '' }}
                                >
                                <span class="composer-choice-title">{{ __('messages.publish_as_post') }}</span>
                                <span class="composer-choice-text">{{ __('messages.publish_as_post_hint') }}</span>
                            </label>

                            <label class="composer-choice-card" for="composer-publish-directory-only">
                                <input
                                    class="composer-choice-input"
                                    type="radio"
                                    id="composer-publish-directory-only"
                                    name="publish_mode"
                                    value="directory_only"
                                    {{ $oldPublishMode === 'directory_only' ? 'checked' : '' }}
                                >
                                <span class="composer-choice-title">{{ __('messages.move_to_directory') }}</span>
                                <span class="composer-choice-text">{{ __('messages.move_to_directory_hint') }}</span>
                            </label>
                        </div>
                    </div>

                    <div id="composer-directory-fields" style="display:none; margin-top: 12px;">
                        <div class="form-row split">
                            <div class="form-item">
                                <div class="form-input small">
                                    <label for="composer-directory-name">{{ __('messages.name') }}</label>
                                    <input type="text" id="composer-directory-name" name="directory_name" value="{{ old('directory_name', '') }}" placeholder="{{ __('messages.name_placeholder') }}">
                                </div>
                            </div>
                            <div class="form-item">
                                <div class="form-input small">
                                    <label for="composer-directory-category">{{ __('messages.directory') }}</label>
                                    <select id="composer-directory-category" name="directory_category_id">
                                        <option value="0">{{ __('messages.select') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ (string) old('directory_category_id', '0') === (string) $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-item">
                                <div class="form-input small">
                                    <label for="composer-directory-tags">{{ __('messages.tags_placeholder') }}</label>
                                    <input type="text" id="composer-directory-tags" name="directory_tags" value="{{ old('directory_tags', '') }}" placeholder="{{ __('messages.tags_placeholder') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="composer-gallery-block" class="widget-box" style="display:none; margin-top: 16px; margin-bottom: 0;">
                <div class="widget-box-content">
                    <input type="file" id="composer-images" name="images[]" accept=".jpg,.jpeg,.png,.gif,.webp" multiple style="display:none">
                    <div id="composer-gallery-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(110px, 1fr)); gap:12px;"></div>
                    <p class="user-status-text small" style="margin-top: 12px;">{{ __('messages.gallery_limit_hint') }}</p>
                </div>
            </div>

            <input type="file" id="imgupload" name="fimg" accept=".jpg, .jpeg, .png, .gif, .webp" style="display:none"/>
        </div>

        <div class="quick-post-footer">
            <div class="quick-post-footer-actions">
                <button type="button" class="quick-post-footer-action text-tooltip-tft-medium" data-title="{{ __('messages.write_post') }}" id="composer-mode-text" style="position: relative; cursor: pointer; border: 0; background: transparent;">
                    <i class="fa fa-font" aria-hidden="true"></i>
                </button>
                <button type="button" class="quick-post-footer-action text-tooltip-tft-medium" data-title="{{ __('messages.insertphoto') }}" id="composer-mode-gallery" style="position: relative; cursor: pointer; border: 0; background: transparent;">
                    <svg class="quick-post-footer-action-icon icon-camera">
                        <use xlink:href="#svg-camera"></use>
                    </svg>
                </button>
                <button type="button" class="quick-post-footer-action text-tooltip-tft-medium" data-title="{{ __('messages.insertlink') }}" id="composer-mode-link" style="position: relative; cursor: pointer; border: 0; background: transparent;">
                    <i class="fa fa-link"></i>
                </button>
            </div>

            <div class="quick-post-footer-actions">
                <button type="submit" class="button small secondary" id="composer-submit">{{ __('messages.spread') }}</button>
            </div>
        </div>
    </form>
</div>

@push('head')
    <style>
        .composer-choice-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }

        .composer-choice-card {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 14px 16px;
            border: 1px solid #e5e7f2;
            border-radius: 16px;
            background: #fff;
            cursor: pointer;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }

        .composer-choice-card:hover {
            border-color: #615dfa;
            box-shadow: 0 12px 28px rgba(97, 93, 250, 0.12);
            transform: translateY(-1px);
        }

        .composer-choice-card:has(.composer-choice-input:checked) {
            border-color: #615dfa;
            box-shadow: 0 14px 30px rgba(97, 93, 250, 0.16);
            background: linear-gradient(180deg, #ffffff 0%, #f7f7ff 100%);
        }

        .composer-choice-input {
            margin: 0 0 4px;
        }

        .composer-choice-title {
            font-size: 14px;
            font-weight: 700;
            color: #1f2337;
        }

        .composer-choice-text {
            font-size: 12px;
            line-height: 1.5;
            color: #6b7280;
        }
    </style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('social-composer-form');
        if (!form) {
            return;
        }

        const postKind = document.getElementById('composer-post-kind');
        const repostStatusId = document.getElementById('composer-repost-status-id');
        const linkBlock = document.getElementById('composer-link-block');
        const linkInput = document.getElementById('composer-link-url');
        const linkPreview = document.getElementById('composer-link-preview');
        const publishOptions = document.getElementById('composer-publish-options');
        const galleryBlock = document.getElementById('composer-gallery-block');
        const galleryInput = document.getElementById('composer-images');
        const galleryGrid = document.getElementById('composer-gallery-grid');
        const directoryFields = document.getElementById('composer-directory-fields');
        const directoryName = document.getElementById('composer-directory-name');
        const repostCard = document.getElementById('composer-repost-card');
        const repostText = document.getElementById('composer-repost-text');
        const composerText = document.getElementById('composer-text');
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        const publishModeInputs = Array.from(form.querySelectorAll('input[name="publish_mode"]'));
        const postModeInput = document.getElementById('composer-publish-post');
        const directoryModeInput = document.getElementById('composer-publish-directory-only');
        let previewTimeout = null;
        let previewRequestId = 0;
        let lastPreviewUrl = null;
        let autoDetectedLink = extractFirstUrl(composerText.value);
        let linkLockedToText = linkInput.value.trim() === '' || linkInput.value.trim() === autoDetectedLink;
        let manualLinkOpen = @json($oldPostKind === 'link' || $oldLinkUrl !== '');

        function extractFirstUrl(text) {
            const match = String(text || '').match(/\b((https?:\/\/)?[a-z0-9.-]+\.[a-z]{2,}(\/\S*)?)/i);
            return match ? match[1] : '';
        }

        function selectedPublishMode() {
            const current = publishModeInputs.find(function (input) {
                return input.checked;
            });

            return current ? current.value : 'post';
        }

        function setPublishMode(value) {
            publishModeInputs.forEach(function (input) {
                input.checked = input.value === value;
            });
        }

        function clearLinkPreview() {
            lastPreviewUrl = null;
            linkPreview.innerHTML = '';
            linkPreview.style.display = 'none';
        }

        function hasResolvedLink() {
            return linkInput.value.trim() !== '';
        }

        function syncPostKind() {
            if ((galleryInput.files || []).length > 0) {
                postKind.value = 'gallery';
                return;
            }

            if (repostStatusId.value) {
                postKind.value = 'repost';
                return;
            }

            postKind.value = hasResolvedLink() ? 'link' : 'text';
        }

        function syncDirectoryFields() {
            const hasLink = hasResolvedLink();
            publishOptions.style.display = hasLink ? 'block' : 'none';

            if (!hasLink) {
                setPublishMode('post');
            }

            directoryFields.style.display = hasLink && selectedPublishMode() === 'directory_only' ? 'block' : 'none';
        }

        function syncLinkBlockVisibility() {
            const shouldShow = manualLinkOpen || hasResolvedLink() || autoDetectedLink !== '';
            linkBlock.style.display = shouldShow ? 'block' : 'none';

            if (!shouldShow) {
                clearLinkPreview();
            }

            syncDirectoryFields();
            syncPostKind();
        }

        function renderGallery() {
            galleryGrid.innerHTML = '';
            const files = Array.from(galleryInput.files || []);
            if (files.length === 0) {
                galleryBlock.style.display = 'none';
                syncPostKind();
                return;
            }

            if (files.length > 10) {
                alert(@json(__('messages.gallery_limit_exceeded')));
                galleryInput.value = '';
                galleryBlock.style.display = 'none';
                syncPostKind();
                return;
            }

            galleryBlock.style.display = 'block';
            postKind.value = 'gallery';

            files.forEach(function (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    const figure = document.createElement('figure');
                    figure.style.margin = '0';
                    figure.style.borderRadius = '14px';
                    figure.style.overflow = 'hidden';
                    figure.style.background = '#f5f5fa';
                    figure.style.aspectRatio = '1 / 1';

                    const image = document.createElement('img');
                    image.src = event.target.result;
                    image.alt = file.name;
                    image.style.width = '100%';
                    image.style.height = '100%';
                    image.style.objectFit = 'cover';

                    figure.appendChild(image);
                    galleryGrid.appendChild(figure);
                };
                reader.readAsDataURL(file);
            });
        }

        function renderLinkPreviewCard(data) {
            linkPreview.innerHTML = `
                <a href="${data.url}" target="_blank" rel="noopener noreferrer" class="post-preview medium">
                    <figure class="post-preview-image liquid" style="background: url(${data.image_url || '{{ theme_asset('img/dir_image.png') }}'}) center center / cover no-repeat;"></figure>
                    <div class="post-preview-info fixed-height">
                        <p class="post-preview-title">${data.title || data.domain || data.url}</p>
                        <p class="post-preview-text">${data.description || ''}</p>
                        <p class="post-preview-link">${data.domain || ''}</p>
                    </div>
                </a>
            `;
            linkPreview.style.display = 'block';

            if (!directoryName.value.trim()) {
                directoryName.value = data.title || data.domain || '';
            }
        }

        function fetchLinkPreview() {
            const value = linkInput.value.trim();
            if (!value || !csrfToken) {
                clearLinkPreview();
                return;
            }

            if (lastPreviewUrl === value) {
                return;
            }

            lastPreviewUrl = value;
            const requestId = ++previewRequestId;

            fetch('{{ route('status.link_preview') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                },
                body: JSON.stringify({ link_url: value })
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (requestId !== previewRequestId) {
                    return;
                }

                renderLinkPreviewCard(data);
            })
            .catch(function () {
                if (requestId !== previewRequestId) {
                    return;
                }

                clearLinkPreview();
            });
        }

        function scheduleLinkPreview() {
            clearTimeout(previewTimeout);

            if (!hasResolvedLink()) {
                clearLinkPreview();
                return;
            }

            previewTimeout = setTimeout(fetchLinkPreview, 400);
        }

        function syncAutoDetectedLink() {
            const nextDetected = extractFirstUrl(composerText.value);
            const current = linkInput.value.trim();
            const shouldReplace = linkLockedToText || current === '' || current === autoDetectedLink;

            autoDetectedLink = nextDetected;

            if (shouldReplace) {
                linkInput.value = autoDetectedLink;
                linkLockedToText = true;
            }

            syncLinkBlockVisibility();
            scheduleLinkPreview();
        }

        document.getElementById('composer-mode-text').addEventListener('click', function () {
            galleryInput.value = '';
            galleryGrid.innerHTML = '';
            galleryBlock.style.display = 'none';
            manualLinkOpen = false;

            if (autoDetectedLink) {
                linkInput.value = autoDetectedLink;
                linkLockedToText = true;
            } else {
                linkInput.value = '';
                clearLinkPreview();
            }

            syncLinkBlockVisibility();
        });

        document.getElementById('composer-mode-gallery').addEventListener('click', function () {
            galleryInput.click();
        });

        document.getElementById('composer-mode-link').addEventListener('click', function () {
            manualLinkOpen = true;

            if (!linkInput.value.trim() && autoDetectedLink) {
                linkInput.value = autoDetectedLink;
                linkLockedToText = true;
            }

            syncLinkBlockVisibility();
            linkInput.focus();
            scheduleLinkPreview();
        });

        galleryInput.addEventListener('change', function () {
            renderGallery();
            syncLinkBlockVisibility();
        });

        composerText.addEventListener('input', function () {
            syncAutoDetectedLink();
        });

        linkInput.addEventListener('input', function () {
            manualLinkOpen = true;
            linkLockedToText = this.value.trim() === '' || this.value.trim() === autoDetectedLink;
            syncLinkBlockVisibility();
            scheduleLinkPreview();
        });

        publishModeInputs.forEach(function (input) {
            input.addEventListener('change', function () {
                syncDirectoryFields();
            });
        });

        document.getElementById('composer-repost-cancel').addEventListener('click', function () {
            repostStatusId.value = '';
            repostCard.style.display = 'none';
            repostText.textContent = @json(__('messages.repost_ready'));
            syncPostKind();
        });

        form.addEventListener('submit', function () {
            if (!hasResolvedLink()) {
                setPublishMode('post');
            }

            syncPostKind();
        });

        window.openRepostComposer = function (statusId, authorName, excerpt) {
            repostStatusId.value = statusId;
            postKind.value = 'repost';
            repostCard.style.display = 'block';
            repostText.textContent = (authorName ? authorName + ': ' : '') + (excerpt || @json(__('messages.repost_ready')));
            composerText.focus();
            window.scrollTo({ top: form.getBoundingClientRect().top + window.scrollY - 120, behavior: 'smooth' });
        };

        if (hasResolvedLink()) {
            manualLinkOpen = manualLinkOpen || !autoDetectedLink || linkInput.value.trim() !== autoDetectedLink;
        }

        renderGallery();
        syncAutoDetectedLink();
    });
</script>
@endpush
@endauth
