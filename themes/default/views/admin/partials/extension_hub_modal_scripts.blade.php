@if(!empty($showExtensionDeleteModal))
<div class="modal fade" id="extensionDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold fs-18 text-dark">{{ __('messages.delete_plugin') }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="avatar-text avatar-xl bg-soft-danger text-danger rounded-circle mb-3 mx-auto shadow-sm d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 32px;">
                    <i class="feather-trash-2"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">{{ __('messages.confirm_delete_plugin') }}</h4>
                <p class="text-dark fw-bold mb-1" data-extension-delete-name></p>
                <p class="text-muted mb-4" data-extension-delete-identifier></p>
                <div class="alert alert-warning text-start mb-0">
                    <div class="d-flex gap-3 align-items-start">
                        <i class="feather-alert-triangle mt-1"></i>
                        <span data-extension-delete-warning data-default-text="{{ __('messages.delete_plugin_warning') }}">{{ __('messages.delete_plugin_warning') }}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center pt-0 pb-4 px-4">
                <button type="button" class="btn btn-light fw-bold px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">{{ __('messages.cancel') }}</button>
                <form method="POST" class="d-inline" data-extension-delete-form>
                    @csrf
                    <input type="hidden" name="slug" value="" data-extension-delete-slug>
                    <button type="submit" class="btn btn-danger fw-bold px-4 py-2 shadow-sm" style="border-radius: 10px;">{{ __('messages.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="extensionChangelogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold fs-18 text-dark">
                    {{ __('messages.changelog') }} -
                    <span data-extension-changelog-name></span>
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="bg-light p-4 rounded border">
                    <pre class="extension-hub__modal-pre" data-extension-changelog-content></pre>
                </div>
                <div class="mt-3 text-center d-none" data-extension-changelog-github-wrap>
                    <a href="#" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-link" data-extension-changelog-github>
                        <i class="feather-github me-1"></i> {{ __('messages.view_on_github') }}
                    </a>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.close') }}</button>
                <form method="POST" class="d-inline d-none" data-extension-upgrade-form>
                    @csrf
                    <input type="hidden" name="slug" value="" data-extension-upgrade-slug>
                    <button type="submit" class="btn btn-primary">{{ __('messages.update_now') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function decodeBase64Utf8(value) {
        if (!value) {
            return '';
        }

        try {
            return decodeURIComponent(Array.prototype.map.call(window.atob(value), function (character) {
                return '%' + ('00' + character.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));
        } catch (error) {
            try {
                return window.atob(value);
            } catch (fallbackError) {
                return value;
            }
        }
    }

    var deleteModal = document.getElementById('extensionDeleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) {
                return;
            }

            var form = deleteModal.querySelector('[data-extension-delete-form]');
            var slugInput = deleteModal.querySelector('[data-extension-delete-slug]');
            var nameNode = deleteModal.querySelector('[data-extension-delete-name]');
            var identifierNode = deleteModal.querySelector('[data-extension-delete-identifier]');
            var warningNode = deleteModal.querySelector('[data-extension-delete-warning]');

            if (form) {
                form.action = trigger.getAttribute('data-action') || '';
            }

            if (slugInput) {
                slugInput.value = trigger.getAttribute('data-slug') || '';
            }

            if (nameNode) {
                nameNode.textContent = trigger.getAttribute('data-name') || '';
            }

            if (identifierNode) {
                identifierNode.textContent = trigger.getAttribute('data-identifier') || '';
            }

            if (warningNode) {
                warningNode.textContent = trigger.getAttribute('data-warning') || warningNode.getAttribute('data-default-text') || '';
            }
        });
    }

    var changelogModal = document.getElementById('extensionChangelogModal');
    if (changelogModal) {
        changelogModal.addEventListener('show.bs.modal', function (event) {
            var trigger = event.relatedTarget;
            if (!trigger) {
                return;
            }

            var nameNode = changelogModal.querySelector('[data-extension-changelog-name]');
            var contentNode = changelogModal.querySelector('[data-extension-changelog-content]');
            var githubWrap = changelogModal.querySelector('[data-extension-changelog-github-wrap]');
            var githubLink = changelogModal.querySelector('[data-extension-changelog-github]');
            var upgradeForm = changelogModal.querySelector('[data-extension-upgrade-form]');
            var upgradeSlug = changelogModal.querySelector('[data-extension-upgrade-slug]');

            if (nameNode) {
                nameNode.textContent = trigger.getAttribute('data-name') || '';
            }

            if (contentNode) {
                contentNode.textContent = decodeBase64Utf8(trigger.getAttribute('data-changelog') || '');
            }

            var githubUrl = trigger.getAttribute('data-github-url') || '';
            if (githubWrap && githubLink) {
                if (githubUrl) {
                    githubWrap.classList.remove('d-none');
                    githubLink.href = githubUrl;
                } else {
                    githubWrap.classList.add('d-none');
                    githubLink.removeAttribute('href');
                }
            }

            var upgradeAction = trigger.getAttribute('data-upgrade-action') || '';
            if (upgradeForm && upgradeSlug) {
                if (upgradeAction) {
                    upgradeForm.classList.remove('d-none');
                    upgradeForm.action = upgradeAction;
                    upgradeSlug.value = trigger.getAttribute('data-slug') || '';
                } else {
                    upgradeForm.classList.add('d-none');
                    upgradeForm.action = '';
                    upgradeSlug.value = '';
                }
            }
        });
    }
});
</script>
@endpush
