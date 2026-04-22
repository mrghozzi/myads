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
    });
</script>
