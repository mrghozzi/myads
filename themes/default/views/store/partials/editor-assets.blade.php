@once
<style>
  .store-editor-page {
    display: grid;
    gap: 24px;
  }

  .store-editor-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(280px, 0.95fr);
    gap: 24px;
    align-items: start;
  }

  .store-editor-main,
  .store-editor-aside {
    display: grid;
    gap: 24px;
  }

  .store-editor-card {
    overflow: hidden;
  }

  .store-editor-card .widget-box-content {
    padding-top: 0;
  }

  .store-editor-card .widget-box-text {
    margin-bottom: 18px;
  }

  .store-editor-alerts {
    display: grid;
    gap: 12px;
    margin-bottom: 18px;
  }

  .store-editor-alerts .alert {
    margin-bottom: 0;
  }

  .store-editor-sticky {
    position: sticky;
    top: 104px;
  }

  .store-editor-summary-list {
    display: grid;
    gap: 12px;
  }

  .store-editor-summary-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 14px;
    background: rgba(97, 93, 250, 0.08);
  }

  .store-editor-summary-item span {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    opacity: 0.72;
    text-transform: uppercase;
  }

  .store-editor-summary-item strong {
    color: #3e3f5e;
    font-size: 0.95rem;
    text-align: end;
  }

  .store-editor-actions {
    display: grid;
    gap: 12px;
  }

  .store-editor-actions .button {
    justify-content: center;
    width: 100%;
  }

  .store-editor-inline-help {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
  }

  .store-source-picker {
    display: grid;
    gap: 18px;
  }

  .store-source-picker__toggle {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
  }

  .store-source-picker__button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .store-source-picker__button.is-active {
    background: linear-gradient(135deg, #615dfa, #23d2e2);
    border-color: transparent;
    box-shadow: 0 16px 28px rgba(97, 93, 250, 0.24);
    color: #fff;
    transform: translateY(-1px);
  }

  .store-source-picker__status {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
  }

  .store-source-picker__status-badge {
    border-radius: 999px;
    background: rgba(97, 93, 250, 0.12);
    color: #615dfa;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 6px 12px;
  }

  .store-source-picker__hint {
    color: #8f94b5;
    font-size: 0.875rem;
    line-height: 1.6;
    margin: 0;
  }

  .store-source-picker__panel {
    display: none;
  }

  .store-source-picker[data-mode="upload"] [data-store-source-panel="upload"],
  .store-source-picker[data-mode="link"] [data-store-source-panel="link"] {
    display: block;
  }

  .store-source-picker__file-input {
    background: rgba(97, 93, 250, 0.05);
    border: 1px dashed rgba(97, 93, 250, 0.38);
    border-radius: 14px;
    cursor: pointer;
    padding: 18px;
  }

  .store-source-picker__upload-result {
    display: grid;
    gap: 12px;
    margin-top: 12px;
  }

  .store-source-upload-result {
    align-items: center;
    background: rgba(97, 93, 250, 0.06);
    border: 1px solid rgba(97, 93, 250, 0.16);
    border-radius: 16px;
    display: flex;
    gap: 14px;
    padding: 14px 16px;
  }

  .store-source-upload-result img {
    height: 40px;
    object-fit: contain;
    width: 40px;
  }

  .store-source-upload-result__name {
    color: #3e3f5e;
    font-size: 0.95rem;
    font-weight: 700;
    margin: 0;
  }

  .store-source-upload-result__meta {
    color: #8f94b5;
    font-size: 0.75rem;
    line-height: 1.5;
    margin: 4px 0 0;
    word-break: break-all;
  }

  .store-source-upload-result.is-error {
    background: rgba(233, 75, 95, 0.08);
    border-color: rgba(233, 75, 95, 0.24);
  }

  .store-editor-history summary {
    align-items: center;
    color: #3e3f5e;
    cursor: pointer;
    display: flex;
    font-family: 'Rajdhani', 'Titillium Web', sans-serif;
    font-size: 1.15rem;
    font-weight: 700;
    justify-content: space-between;
    gap: 14px;
    list-style: none;
    padding: 24px 26px;
  }

  .store-editor-history summary::-webkit-details-marker {
    display: none;
  }

  .store-editor-history__badge {
    background: rgba(35, 210, 226, 0.12);
    border-radius: 999px;
    color: #23d2e2;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 6px 12px;
  }

  .store-editor-history .widget-box-content {
    border-top: 1px solid rgba(140, 146, 182, 0.16);
  }

  body[data-theme="css_d"] .store-editor-summary-item {
    background: rgba(97, 93, 250, 0.14);
  }

  body[data-theme="css_d"] .store-editor-summary-item strong,
  body[data-theme="css_d"] .store-editor-history summary,
  body[data-theme="css_d"] .store-source-upload-result__name {
    color: #fff;
  }

  body[data-theme="css_d"] .store-source-picker__status-badge {
    background: rgba(97, 93, 250, 0.2);
    color: #9c9ffb;
  }

  body[data-theme="css_d"] .store-source-picker__hint,
  body[data-theme="css_d"] .store-source-upload-result__meta {
    color: #9aa4bf;
  }

  body[data-theme="css_d"] .store-source-picker__file-input {
    background: rgba(97, 93, 250, 0.12);
    border-color: rgba(97, 93, 250, 0.34);
    color: #fff;
  }

  body[data-theme="css_d"] .store-source-upload-result {
    background: rgba(97, 93, 250, 0.12);
    border-color: rgba(97, 93, 250, 0.24);
  }

  body[data-theme="css_d"] .store-source-upload-result.is-error {
    background: rgba(233, 75, 95, 0.12);
    border-color: rgba(233, 75, 95, 0.24);
  }

  body[data-theme="css_d"] .store-editor-history__badge {
    background: rgba(35, 210, 226, 0.18);
    color: #4ff461;
  }

  @media (max-width: 1100px) {
    .store-editor-layout {
      grid-template-columns: 1fr;
    }

    .store-editor-sticky {
      position: static;
    }
  }

  @media (max-width: 680px) {
    .store-source-picker__toggle {
      grid-template-columns: 1fr;
    }

    .store-source-upload-result {
      align-items: flex-start;
      flex-direction: column;
    }

    .store-editor-history summary {
      padding: 20px;
    }
  }
</style>

<script>
  (function () {
    function escapeHtml(value) {
      return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function setStoreSourceMode(root, mode) {
      const uploadButton = root.querySelector('[data-store-source-tab="upload"]');
      const linkButton = root.querySelector('[data-store-source-tab="link"]');
      const hiddenInput = root.querySelector('[data-store-source-final]');
      const hint = root.querySelector('[data-store-source-hint]');
      const status = root.querySelector('[data-store-source-status]');

      root.dataset.mode = mode;

      if (uploadButton) {
        uploadButton.classList.toggle('is-active', mode === 'upload');
        uploadButton.setAttribute('aria-pressed', mode === 'upload' ? 'true' : 'false');
      }

      if (linkButton) {
        linkButton.classList.toggle('is-active', mode === 'link');
        linkButton.setAttribute('aria-pressed', mode === 'link' ? 'true' : 'false');
      }

      if (hint) {
        hint.textContent = mode === 'upload' ? hint.dataset.uploadHint : hint.dataset.linkHint;
      }

      if (status) {
        status.textContent = mode === 'upload' ? status.dataset.uploadLabel : status.dataset.linkLabel;
      }

      if (hiddenInput) {
        hiddenInput.value = mode === 'upload' ? (root.dataset.uploadValue || '') : (root.dataset.linkValue || '');
      }
    }

    function syncUploadState(root) {
      const hiddenInput = root.querySelector('[data-store-source-final]');
      const result = root.querySelector('[data-store-source-upload-result]');
      const uploadResponse = result ? result.querySelector('[data-upload-path]') : null;

      root.dataset.uploadValue = uploadResponse ? (uploadResponse.getAttribute('data-upload-path') || '') : '';

      if (hiddenInput && root.dataset.mode === 'upload') {
        hiddenInput.value = root.dataset.uploadValue || '';
      }
    }

    function initStoreSourcePicker(root) {
      if (!root || root.dataset.initialized === '1') {
        return;
      }

      root.dataset.initialized = '1';

      const hiddenInput = root.querySelector('[data-store-source-final]');
      const linkInput = root.querySelector('[data-store-source-link-input]');
      const fileInput = root.querySelector('[data-store-source-upload-input]');
      const result = root.querySelector('[data-store-source-upload-result]');
      const uploadUrl = fileInput ? fileInput.getAttribute('data-store-source-upload-url') : '';
      const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
      const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';
      const initialMode = root.dataset.initialMode === 'link' ? 'link' : 'upload';
      const uploadErrorLabel = root.dataset.uploadErrorLabel || '';
      const uploadingLabel = root.dataset.uploadingLabel || 'Uploading...';

      root.dataset.linkValue = root.dataset.linkValue || (linkInput ? linkInput.value : '');
      root.dataset.uploadValue = root.dataset.uploadValue || (initialMode === 'upload' && hiddenInput ? hiddenInput.value : '');

      root.querySelectorAll('[data-store-source-tab]').forEach(function (button) {
        button.addEventListener('click', function () {
          setStoreSourceMode(root, button.getAttribute('data-store-source-tab'));
        });
      });

      if (linkInput) {
        linkInput.addEventListener('input', function () {
          root.dataset.linkValue = this.value;

          if (root.dataset.mode === 'link' && hiddenInput) {
            hiddenInput.value = this.value;
          }
        });
      }

      if (fileInput && result && uploadUrl && window.jQuery) {
        fileInput.addEventListener('change', function () {
          if (!this.files || !this.files.length) {
            return;
          }

          const formData = new FormData();
          formData.append('fzip', this.files[0]);
          formData.append('_token', csrfToken);

          result.innerHTML = '<div class="store-source-upload-result"><div><p class="store-source-upload-result__name">' + escapeHtml(uploadingLabel) + '</p></div></div>';

          window.jQuery.ajax({
            url: uploadUrl,
            type: 'POST',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            success: function (response) {
              result.innerHTML = response;
              syncUploadState(root);
            },
            error: function () {
              result.innerHTML = '<div class="store-source-upload-result is-error" data-upload-error="1"><div><p class="store-source-upload-result__name">' + escapeHtml(uploadErrorLabel) + '</p></div></div>';
              root.dataset.uploadValue = '';

              if (hiddenInput && root.dataset.mode === 'upload') {
                hiddenInput.value = '';
              }
            }
          });
        });
      }

      setStoreSourceMode(root, initialMode);
      syncUploadState(root);
    }

    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('[data-store-source-picker]').forEach(initStoreSourcePicker);
    });
  })();
</script>
@endonce
