(function () {
    function markExistingDropdowns(scope) {
        if (!scope) {
            return;
        }

        scope.querySelectorAll('.widget-box-post-settings-dropdown-trigger, .reaction-options-dropdown-trigger').forEach(function (trigger) {
            trigger.dataset.activityDropdownReady = '1';
        });
    }

    function initDropdownGroup(scope, selector, containerSelector, options) {
        if (!scope || !window.app || !app.plugins || typeof app.plugins.createDropdown !== 'function') {
            return;
        }

        scope.querySelectorAll(selector).forEach(function (trigger) {
            if (trigger.dataset.activityDropdownReady === '1') {
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

            trigger.dataset.activityDropdownReady = '1';
        });
    }

    function hydrateDirectoryDropdowns(scope) {
        var isRtl = document.documentElement.getAttribute('dir') === 'rtl';
        initDropdownGroup(scope, '.widget-box-post-settings-dropdown-trigger', '.widget-box-post-settings-dropdown', {
            offset: {
                top: 36,
                right: isRtl ? undefined : 0,
                left: isRtl ? 0 : undefined
            },
            animation: {
                type: 'translate-top',
                speed: 0.25,
                translateOffset: {
                    vertical: 15
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

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    // Upvote handler
    function handleUpvote(btn) {
        var listingId = btn.getAttribute('data-directory-upvote');
        if (!listingId || btn.dataset.loading === '1') {
            return;
        }

        btn.dataset.loading = '1';
        var token = getCsrfToken();

        fetch('/directory/' + listingId + '/upvote', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function (res) {
            if (res.status === 401) {
                window.location.href = '/login';
                return null;
            }
            return res.json();
        })
        .then(function (data) {
            btn.dataset.loading = '0';
            if (!data || !data.success) {
                return;
            }

            // Update upvote button UI
            if (data.upvoted) {
                btn.classList.add('is-upvoted');
            } else {
                btn.classList.remove('is-upvoted');
            }

            var countEl = btn.querySelector('.directory-upvote-count');
            if (countEl) {
                countEl.textContent = data.upvotes_count;
                countEl.setAttribute('data-count', data.upvotes_count);
            }

            // Animate button bounce
            btn.style.transform = 'scale(1.2)';
            setTimeout(function () {
                btn.style.transform = '';
            }, 200);

            // Update all matching counters in the post if present
            document.querySelectorAll('.post' + listingId + ' .upvote-stat-val').forEach(function (stat) {
                stat.textContent = data.upvotes_count;
            });
        })
        .catch(function () {
            btn.dataset.loading = '0';
        });
    }

    // Real-time Discovery Search & Filter
    var searchTimer = null;
    function refreshDirectoryFeed() {
        var searchInput = document.getElementById('directory-search-input');
        var activeTab = document.querySelector('.directory-tab-btn.active');
        var activeChip = document.querySelector('.directory-filter-chip.active');

        var searchVal = searchInput ? searchInput.value.trim() : '';
        var sortVal = activeTab ? activeTab.getAttribute('data-sort') : 'latest';
        var pricingVal = activeChip ? activeChip.getAttribute('data-pricing') : '';

        var url = new URL(window.location.href);
        if (searchVal) {
            url.searchParams.set('search', searchVal);
        } else {
            url.searchParams.delete('search');
        }

        if (sortVal && sortVal !== 'latest') {
            url.searchParams.set('sort', sortVal);
        } else {
            url.searchParams.delete('sort');
        }

        if (pricingVal) {
            url.searchParams.set('pricing', pricingVal);
        } else {
            url.searchParams.delete('pricing');
        }

        url.searchParams.delete('page'); // Reset to page 1

        window.history.replaceState(null, '', url.toString());

        var container = document.getElementById('timeline-content');
        if (!container) {
            return;
        }

        container.style.opacity = '0.5';

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            container.style.opacity = '1';
            if (data && data.html) {
                container.innerHTML = data.html;
                bootDirectoryRedesign();
                hydrateDirectoryDropdowns(container);

                if (typeof window.initLazyImages === 'function') {
                    window.initLazyImages();
                }
            }
        })
        .catch(function () {
            container.style.opacity = '1';
        });
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

        // Live Search Input handler
        var searchInput = document.getElementById('directory-search-input');
        var clearBtn = document.getElementById('directory-search-clear');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                if (clearBtn) {
                    clearBtn.style.display = searchInput.value ? 'block' : 'none';
                }
                clearTimeout(searchTimer);
                searchTimer = setTimeout(refreshDirectoryFeed, 350);
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                clearBtn.style.display = 'none';
                refreshDirectoryFeed();
            });
        }

        // Filter tabs click
        document.querySelectorAll('.directory-tab-btn').forEach(function (tab) {
            tab.addEventListener('click', function () {
                document.querySelectorAll('.directory-tab-btn').forEach(function (t) { t.classList.remove('active'); });
                tab.classList.add('active');
                refreshDirectoryFeed();
            });
        });

        // Pricing filter chips click
        document.querySelectorAll('.directory-filter-chip').forEach(function (chip) {
            chip.addEventListener('click', function () {
                document.querySelectorAll('.directory-filter-chip').forEach(function (c) { c.classList.remove('active'); });
                chip.classList.add('active');
                refreshDirectoryFeed();
            });
        });

        // Copy button handler
        document.querySelectorAll('[data-copy-target]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var targetId = btn.getAttribute('data-copy-target');
                var textarea = document.getElementById(targetId);
                if (textarea) {
                    textarea.select();
                    document.execCommand('copy');
                    var origHtml = btn.innerHTML;
                    btn.innerHTML = '<i class="fa fa-check"></i> تم النسخ!';
                    setTimeout(function () {
                        btn.innerHTML = origHtml;
                    }, 2000);
                }
            });
        });

        // Modal triggers
        document.querySelectorAll('[data-directory-modal-open]').forEach(function (trigger) {
            trigger.addEventListener('click', function () {
                var modalId = trigger.getAttribute('data-directory-modal-open');
                var modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.display = 'flex';
                }
            });
        });

        document.querySelectorAll('[data-directory-modal-close]').forEach(function (closer) {
            closer.addEventListener('click', function () {
                var modalId = closer.getAttribute('data-directory-modal-close');
                var modal = document.getElementById(modalId);
                if (modal) {
                    modal.style.display = 'none';
                }
            });
        });

        // Verify claim button
        var verifyClaimBtn = document.getElementById('btn-verify-claim');
        if (verifyClaimBtn) {
            verifyClaimBtn.addEventListener('click', function () {
                var listingId = verifyClaimBtn.getAttribute('data-listing-id');
                var msgBox = document.getElementById('claim-status-message');
                if (!listingId || !msgBox) return;

                verifyClaimBtn.disabled = true;
                verifyClaimBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> جاري التحقق...';
                msgBox.style.display = 'none';

                fetch('/directory/' + listingId + '/claim', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    verifyClaimBtn.disabled = false;
                    verifyClaimBtn.innerHTML = '<i class="fa fa-check-circle"></i> تحقق من الموقع الآن';
                    msgBox.style.display = 'block';

                    if (data.verified) {
                        msgBox.className = 'alert alert-success';
                        msgBox.innerHTML = '<i class="fa fa-check"></i> ' + (data.message || 'تم التوثيق بنجاح!');
                        setTimeout(function () { window.location.reload(); }, 1500);
                    } else {
                        msgBox.className = 'alert alert-warning';
                        msgBox.innerHTML = '<i class="fa fa-exclamation-triangle"></i> ' + (data.message || 'لم يتم العثور على كود الميتا بعد، تأكد من وضعه في ترويسة الموقع.');
                    }
                })
                .catch(function () {
                    verifyClaimBtn.disabled = false;
                    verifyClaimBtn.innerHTML = '<i class="fa fa-check-circle"></i> تحقق من الموقع الآن';
                    msgBox.style.display = 'block';
                    msgBox.className = 'alert alert-danger';
                    msgBox.innerHTML = 'حدث خطأ أثناء فحص الموقع، يرجى المحاولة لاحقاً.';
                });
            });
        }
    });

    // Delegation for clicks (upvotes, comments)
    document.addEventListener('click', function (event) {
        var upvoteBtn = event.target.closest('[data-directory-upvote]');
        if (upvoteBtn) {
            event.preventDefault();
            handleUpvote(upvoteBtn);
            return;
        }

        var commentToggle = event.target.closest('[data-directory-comment-toggle]');
        if (commentToggle) {
            event.preventDefault();
            loadDirectoryComments(Number(commentToggle.getAttribute('data-directory-comment-toggle')), commentToggle);
        }
    });

    if (typeof window.registerAfterInfiniteScrollRender === 'function') {
        window.registerAfterInfiniteScrollRender(function (scope) {
            bootDirectoryRedesign();
            hydrateDirectoryDropdowns(scope || document);
        });
    } else {
        window.afterInfiniteScrollRender = function (scope) {
            bootDirectoryRedesign();
            hydrateDirectoryDropdowns(scope || document);
        };
    }
})();
