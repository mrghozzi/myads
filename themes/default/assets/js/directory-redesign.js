(function () {
    function markExistingDropdowns(scope) {
        if (!scope) {
            return;
        }

        scope.querySelectorAll('.widget-box-post-settings-dropdown-trigger, .reaction-options-dropdown-trigger').forEach(function (trigger) {
            trigger.dataset.directoryDropdownReady = '1';
        });
    }

    function initDropdownGroup(scope, selector, containerSelector, options) {
        if (!scope || !window.app || !app.plugins || typeof app.plugins.createDropdown !== 'function') {
            return;
        }

        scope.querySelectorAll(selector).forEach(function (trigger) {
            if (trigger.dataset.directoryDropdownReady === '1') {
                return;
            }

            var container = trigger.parentElement ? trigger.parentElement.querySelector(containerSelector) : null;
            if (!container) {
                return;
            }

            app.plugins.createDropdown(Object.assign({
                triggerElement: trigger,
                containerElement: container,
            }, options));

            trigger.dataset.directoryDropdownReady = '1';
        });
    }

    function hydrateDirectoryDropdowns(scope) {
        initDropdownGroup(scope, '.widget-box-post-settings-dropdown-trigger', '.widget-box-post-settings-dropdown', {
            offset: {
                top: 30,
                right: 9
            },
            animation: {
                type: 'translate-top',
                speed: 0.3,
                translateOffset: {
                    vertical: 20
                }
            }
        });

        initDropdownGroup(scope, '.reaction-options-dropdown-trigger', '.reaction-options-dropdown', {
            triggerEvent: 'click',
            offset: {
                bottom: 54,
                left: -16
            },
            animation: {
                type: 'translate-bottom',
                speed: 0.3,
                translateOffset: {
                    vertical: 20
                }
            },
            closeOnDropdownClick: true
        });
    }

    function bootDirectoryRedesign() {
        if (typeof window.initHexagons === 'function') {
            window.initHexagons();
        }
    }

    function loadDirectoryComments(listingId, activateButton) {
        if (!listingId || typeof window.loadComments !== 'function') {
            return;
        }

        window.loadComments(listingId, 'directory');

        if (activateButton) {
            activateButton.classList.add('active');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        bootDirectoryRedesign();
        markExistingDropdowns(document);

        var detailComments = document.querySelector('[data-directory-detail-comments]');
        if (detailComments) {
            var detailId = Number(detailComments.getAttribute('data-directory-detail-comments'));
            var detailToggle = document.querySelector('[data-directory-comment-toggle="' + detailId + '"]');
            loadDirectoryComments(detailId, detailToggle);
        }
    });

    document.addEventListener('click', function (event) {
        var commentToggle = event.target.closest('[data-directory-comment-toggle]');
        if (commentToggle) {
            event.preventDefault();
            loadDirectoryComments(Number(commentToggle.getAttribute('data-directory-comment-toggle')), commentToggle);
        }
    });

    window.afterInfiniteScrollRender = function () {
        bootDirectoryRedesign();
        hydrateDirectoryDropdowns(document);
    };
})();
