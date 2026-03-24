@auth
@php
    $categories = \App\Models\DirectoryCategory::where('statu', 1)->orderBy('name', 'ASC')->get();
@endphp

<div class="quick-post" id="social-composer">
    <div class="quick-post-body">
        <form class="form" action="{{ route('status.create') }}" method="POST" enctype="multipart/form-data" id="social-composer-form">
            @csrf
            <input type="hidden" name="post_kind" id="composer-post-kind" value="text">
            <input type="hidden" name="repost_status_id" id="composer-repost-status-id" value="">

            <div class="form-row">
                <div class="form-item">
                    <div class="form-textarea">
                        <textarea id="composer-text" name="text" class="quicktext" placeholder="{{ __('messages.whats_on_your_mind', ['username' => auth()->user()->username]) }}"></textarea>
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
                                <input type="url" id="composer-link-url" name="link_url" placeholder="{{ __('messages.url_placeholder') }}">
                            </div>
                        </div>
                    </div>

                    <div id="composer-link-preview" style="display:none; margin-top:16px;"></div>

                    <div class="form-row split" style="margin-top: 16px;">
                        <div class="form-item">
                            <div class="checkbox-wrap">
                                <input type="checkbox" id="composer-save-directory" name="save_to_directory" value="1">
                                <label for="composer-save-directory">{{ __('messages.save_to_directory') }}</label>
                            </div>
                        </div>
                    </div>

                    <div id="composer-directory-fields" style="display:none; margin-top: 12px;">
                        <div class="form-row split">
                            <div class="form-item">
                                <div class="form-input small">
                                    <label for="composer-directory-name">{{ __('messages.name') }}</label>
                                    <input type="text" id="composer-directory-name" name="directory_name" placeholder="{{ __('messages.name_placeholder') }}">
                                </div>
                            </div>
                            <div class="form-item">
                                <div class="form-input small">
                                    <label for="composer-directory-category">{{ __('messages.directory') }}</label>
                                    <select id="composer-directory-category" name="directory_category_id">
                                        <option value="0">{{ __('messages.select') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-item">
                                <div class="form-input small">
                                    <label for="composer-directory-tags">{{ __('messages.tags_placeholder') }}</label>
                                    <input type="text" id="composer-directory-tags" name="directory_tags" placeholder="{{ __('messages.tags_placeholder') }}">
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
        </form>
    </div>

    <div class="quick-post-footer">
        <div class="quick-post-footer-actions">
            <div class="quick-post-footer-action text-tooltip-tft-medium" data-title="{{ __('messages.write_post') }}" id="composer-mode-text" style="position: relative; cursor: pointer;">
                <i class="fa fa-font" aria-hidden="true"></i>
            </div>
            <div class="quick-post-footer-action text-tooltip-tft-medium" data-title="{{ __('messages.insertphoto') }}" id="composer-mode-gallery" style="position: relative; cursor: pointer;">
                <svg class="quick-post-footer-action-icon icon-camera">
                    <use xlink:href="#svg-camera"></use>
                </svg>
            </div>
            <div class="quick-post-footer-action text-tooltip-tft-medium" data-title="{{ __('messages.insertlink') }}" id="composer-mode-link" style="position: relative; cursor: pointer;">
                <i class="fa fa-link"></i>
            </div>
        </div>

        <div class="quick-post-footer-actions">
            <p class="button small secondary" id="composer-submit" style="cursor: pointer;">{{ __('messages.spread') }}</p>
        </div>
    </div>
</div>

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
        const galleryBlock = document.getElementById('composer-gallery-block');
        const galleryInput = document.getElementById('composer-images');
        const galleryGrid = document.getElementById('composer-gallery-grid');
        const linkInput = document.getElementById('composer-link-url');
        const linkPreview = document.getElementById('composer-link-preview');
        const directoryToggle = document.getElementById('composer-save-directory');
        const directoryFields = document.getElementById('composer-directory-fields');
        const repostCard = document.getElementById('composer-repost-card');
        const repostText = document.getElementById('composer-repost-text');
        const composerText = document.getElementById('composer-text');
        let previewTimeout = null;

        function resetToTextMode() {
            if (galleryInput.files.length === 0 && !repostStatusId.value && !linkInput.value.trim()) {
                postKind.value = 'text';
            }
        }

        function setMode(mode) {
            if (mode === 'gallery') {
                postKind.value = 'gallery';
                galleryBlock.style.display = 'block';
                linkBlock.style.display = 'none';
                galleryInput.click();
                return;
            }

            if (mode === 'link') {
                postKind.value = repostStatusId.value ? 'repost' : 'link';
                linkBlock.style.display = 'block';
                galleryBlock.style.display = 'none';
                linkInput.focus();
                return;
            }

            resetToTextMode();
            linkBlock.style.display = linkInput.value.trim() ? 'block' : 'none';
            galleryBlock.style.display = galleryInput.files.length ? 'block' : 'none';
        }

        function renderGallery() {
            galleryGrid.innerHTML = '';
            const files = Array.from(galleryInput.files || []);
            if (files.length === 0) {
                galleryBlock.style.display = 'none';
                resetToTextMode();
                return;
            }

            if (files.length > 10) {
                alert('{{ __('messages.gallery_limit_exceeded') }}');
                galleryInput.value = '';
                galleryBlock.style.display = 'none';
                return;
            }

            postKind.value = 'gallery';
            galleryBlock.style.display = 'block';

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
                <a href="${data.url}" target="_blank" class="post-preview medium">
                    <figure class="post-preview-image liquid" style="background: url(${data.image_url || '{{ theme_asset('img/dir_image.png') }}'}) center center / cover no-repeat;"></figure>
                    <div class="post-preview-info fixed-height">
                        <p class="post-preview-title">${data.title || data.domain || data.url}</p>
                        <p class="post-preview-text">${data.description || ''}</p>
                        <p class="post-preview-link">${data.domain || ''}</p>
                    </div>
                </a>
            `;
            linkPreview.style.display = 'block';
        }

        function fetchLinkPreview() {
            const value = linkInput.value.trim();
            if (!value) {
                linkPreview.style.display = 'none';
                linkPreview.innerHTML = '';
                directoryFields.style.display = directoryToggle.checked ? 'block' : 'none';
                resetToTextMode();
                return;
            }

            postKind.value = repostStatusId.value ? 'repost' : 'link';
            fetch('{{ route('status.link_preview') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ link_url: value })
            })
            .then(response => response.json())
            .then(data => {
                renderLinkPreviewCard(data);
                if (!document.getElementById('composer-directory-name').value) {
                    document.getElementById('composer-directory-name').value = data.title || data.domain || '';
                }
            })
            .catch(() => {
                linkPreview.style.display = 'none';
            });
        }

        document.getElementById('composer-mode-text').addEventListener('click', function () {
            linkBlock.style.display = 'none';
            galleryBlock.style.display = 'none';
            galleryInput.value = '';
            linkInput.value = '';
            linkPreview.innerHTML = '';
            linkPreview.style.display = 'none';
            directoryToggle.checked = false;
            directoryFields.style.display = 'none';
            if (!repostStatusId.value) {
                postKind.value = 'text';
            }
        });

        document.getElementById('composer-mode-gallery').addEventListener('click', function () {
            setMode('gallery');
        });

        document.getElementById('composer-mode-link').addEventListener('click', function () {
            setMode('link');
        });

        document.getElementById('composer-submit').addEventListener('click', function () {
            form.submit();
        });

        galleryInput.addEventListener('change', renderGallery);

        linkInput.addEventListener('input', function () {
            clearTimeout(previewTimeout);
            previewTimeout = setTimeout(fetchLinkPreview, 500);
        });

        directoryToggle.addEventListener('change', function () {
            directoryFields.style.display = this.checked ? 'block' : 'none';
        });

        document.getElementById('composer-repost-cancel').addEventListener('click', function () {
            repostStatusId.value = '';
            repostCard.style.display = 'none';
            repostText.textContent = '{{ __('messages.repost_ready') }}';
            resetToTextMode();
        });

        window.openRepostComposer = function (statusId, authorName, excerpt) {
            repostStatusId.value = statusId;
            postKind.value = 'repost';
            repostCard.style.display = 'block';
            repostText.textContent = (authorName ? authorName + ': ' : '') + (excerpt || '{{ __('messages.repost_ready') }}');
            composerText.focus();
            window.scrollTo({ top: form.getBoundingClientRect().top + window.scrollY - 120, behavior: 'smooth' });
        };
    });
</script>
@endpush
@endauth
