<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fallbackCopy = function (text) {
            const helper = document.createElement('textarea');
            helper.value = text;
            helper.setAttribute('readonly', 'readonly');
            helper.style.position = 'absolute';
            helper.style.left = '-9999px';
            document.body.appendChild(helper);
            helper.select();
            document.execCommand('copy');
            document.body.removeChild(helper);
        };

        document.querySelectorAll('.js-dev-copy').forEach(function (button) {
            button.addEventListener('click', async function () {
                const directValue = button.getAttribute('data-copy');
                const targetSelector = button.getAttribute('data-copy-target');
                const target = targetSelector ? document.querySelector(targetSelector) : null;
                const value = directValue || (target ? target.value : '');

                if (!value) {
                    return;
                }

                try {
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        await navigator.clipboard.writeText(value);
                    } else {
                        fallbackCopy(value);
                    }

                    button.dataset.copied = 'true';
                    window.clearTimeout(button.__developerCopyTimer);
                    button.__developerCopyTimer = window.setTimeout(function () {
                        button.dataset.copied = 'false';
                    }, 1400);
                } catch (error) {
                    fallbackCopy(value);
                }
            });
        });

        document.querySelectorAll('.js-dev-toggle-secret').forEach(function (button) {
            button.addEventListener('click', function () {
                const selector = button.getAttribute('data-target');
                const target = selector ? document.querySelector(selector) : null;
                const icon = button.querySelector('i');

                if (!target) {
                    return;
                }

                const reveal = target.type === 'password';
                target.type = reveal ? 'text' : 'password';

                if (icon) {
                    icon.classList.toggle('fa-eye', !reveal);
                    icon.classList.toggle('fa-eye-slash', reveal);
                }
            });
        });

        const updateForm = document.getElementById('dev-update-app-form');
        if (updateForm) {
            updateForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const updateBtn = document.getElementById('dev-update-btn');
                const originalText = updateBtn ? updateBtn.innerHTML : '';
                if (updateBtn) {
                    updateBtn.disabled = true;
                    updateBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> {{ __("messages.saving") ?? "Saving..." }}';
                }

                const alertContainer = document.getElementById('dev-form-alert');
                const successContainer = document.getElementById('dev-form-success');
                if (alertContainer) alertContainer.style.display = 'none';
                if (successContainer) successContainer.style.display = 'none';

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                        || updateForm.querySelector('input[name="_token"]')?.value 
                        || '';

                    const scopes = [];
                    updateForm.querySelectorAll('input[name="requested_scopes[]"]:checked').forEach(function (cb) {
                        scopes.push(cb.value);
                    });

                    const payload = {
                        _token: csrfToken,
                        _method: 'PUT',
                        name: updateForm.querySelector('input[name="name"]')?.value || '',
                        domain: updateForm.querySelector('input[name="domain"]')?.value || '',
                        description: updateForm.querySelector('textarea[name="description"]')?.value || '',
                        redirect_uris: updateForm.querySelector('textarea[name="redirect_uris"]')?.value || '',
                        requested_scopes: scopes
                    };

                    const targetUrl = updateForm.getAttribute('action') || window.location.pathname;

                    const response = await fetch(targetUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify(payload)
                    });

                    const rawText = await response.text();
                    let data = {};
                    try {
                        data = JSON.parse(rawText);
                    } catch (err) {
                        data = {};
                    }

                    if (response.ok && data.success) {
                        if (successContainer) {
                            successContainer.innerHTML = '<strong>' + (data.message || 'Saved successfully!') + '</strong>';
                            successContainer.style.display = 'block';
                            successContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        } else {
                            alert(data.message || 'Saved successfully!');
                        }
                        return;
                    }

                    let errorMsg = '';
                    if (data.message) {
                        errorMsg = data.message;
                    } else if (data.errors) {
                        errorMsg = Object.values(data.errors).flat().join('<br>');
                    } else if (response.status === 419) {
                        errorMsg = 'Session/CSRF expired (HTTP 419). Please refresh the page and log in again.';
                    } else {
                        let specificError = '';
                        try {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(rawText, 'text/html');
                            const title = doc.querySelector('.error-title')?.innerText || doc.querySelector('h1')?.innerText || '';
                            const message = doc.querySelector('.error-message')?.innerText || doc.querySelector('.error-card p')?.innerText || doc.querySelector('p')?.innerText || '';
                            specificError = (title + (message ? ' — ' + message : '')).trim();
                        } catch (e) {}

                        if (!specificError) {
                            specificError = rawText.replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '')
                                                   .replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '')
                                                   .replace(/<[^>]+>/g, ' ')
                                                   .replace(/\s+/g, ' ')
                                                   .trim()
                                                   .substring(0, 300);
                        }

                        errorMsg = '[HTTP ' + response.status + ' ' + response.statusText + '] ' + (specificError || 'Permission Denied / WAF Block');
                    }

                    if (alertContainer) {
                        alertContainer.innerHTML = '<strong>' + errorMsg + '</strong>';
                        alertContainer.style.display = 'block';
                        alertContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        alert(errorMsg);
                    }
                } catch (err) {
                    console.error('AJAX Update Exception:', err);
                    if (alertContainer) {
                        alertContainer.innerHTML = '<strong>Network / Script Error: ' + err.message + '</strong>';
                        alertContainer.style.display = 'block';
                    } else {
                        alert('Network Error: ' + err.message);
                    }
                } finally {
                    if (updateBtn) {
                        updateBtn.disabled = false;
                        updateBtn.innerHTML = originalText;
                    }
                }
            });
        }
    });
</script>
