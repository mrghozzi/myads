/**
 * MYADS Smart Autocomplete for Mentions (@) and Hashtags (#)
 * Lightweight, zero-dependency Vanilla JS module.
 */
(function () {
    'use strict';

    let activeInput = null;
    let dropdown = null;
    let items = [];
    let selectedIndex = -1;
    let currentMode = null; // 'mention' or 'tag'
    let currentQuery = '';
    let debounceTimer = null;
    let matchStartIndex = -1;

    function getOrCreateDropdown() {
        if (!dropdown) {
            dropdown = document.createElement('div');
            dropdown.id = 'myads-smart-autocomplete';
            dropdown.style.cssText = `
                position: absolute;
                z-index: 100000;
                display: none;
                min-width: 200px;
                max-width: 320px;
                max-height: 260px;
                overflow-y: auto;
                background: var(--notification-ui-card-bg, #ffffff);
                color: var(--portal-text, #333333);
                border: 1px solid var(--notification-ui-card-border, rgba(0,0,0,0.12));
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.18);
                font-family: inherit;
                font-size: 0.875rem;
                padding: 6px;
                direction: inherit;
            `;
            document.body.appendChild(dropdown);

            document.addEventListener('click', function (e) {
                if (dropdown && !dropdown.contains(e.target) && e.target !== activeInput) {
                    hideDropdown();
                }
            });
        }
        return dropdown;
    }

    function positionDropdown(input) {
        const rect = input.getBoundingClientRect();
        const drop = getOrCreateDropdown();

        let top = window.scrollY + rect.bottom + 4;
        let left = window.scrollX + rect.left;

        // Prevent overflowing screen edges
        const dropWidth = 260;
        if (left + dropWidth > window.innerWidth) {
            left = Math.max(10, window.innerWidth - dropWidth - 20);
        }

        drop.style.top = top + 'px';
        drop.style.left = left + 'px';
    }

    function hideDropdown() {
        if (dropdown) {
            dropdown.style.display = 'none';
            dropdown.innerHTML = '';
        }
        items = [];
        selectedIndex = -1;
        currentMode = null;
        currentQuery = '';
    }

    function renderItems() {
        const drop = getOrCreateDropdown();
        drop.innerHTML = '';

        if (!items || items.length === 0) {
            hideDropdown();
            return;
        }

        const header = document.createElement('div');
        header.style.cssText = 'padding: 4px 8px 6px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; color: #8f91ac; letter-spacing: 0.5px;';
        header.textContent = currentMode === 'mention' ? (window.MYADS_I18N?.suggested_members || 'Members') : (window.MYADS_I18N?.suggested_tags || 'Tags');
        drop.appendChild(header);

        items.forEach((item, index) => {
            const row = document.createElement('div');
            row.className = 'autocomplete-item' + (index === selectedIndex ? ' active' : '');
            row.style.cssText = `
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 7px 10px;
                border-radius: 8px;
                cursor: pointer;
                transition: background 0.15s ease;
                background: ${index === selectedIndex ? 'rgba(97, 93, 250, 0.12)' : 'transparent'};
                color: ${index === selectedIndex ? '#615dfa' : 'inherit'};
                font-weight: ${index === selectedIndex ? '600' : 'normal'};
            `;

            if (currentMode === 'mention') {
                const avatar = document.createElement('img');
                avatar.src = item.avatar || '/upload/_avatar.png';
                avatar.style.cssText = 'width: 26px; height: 26px; border-radius: 50%; object-fit: cover; flex-shrink: 0;';
                avatar.alt = item.username;
                avatar.onerror = function () { this.src = '/upload/_avatar.png'; };
                row.appendChild(avatar);

                const nameWrap = document.createElement('div');
                nameWrap.style.cssText = 'overflow: hidden; text-overflow: ellipsis; white-space: nowrap;';
                nameWrap.innerHTML = `<span style="font-weight: 600;">@${item.username}</span>`;
                row.appendChild(nameWrap);
            } else {
                const icon = document.createElement('span');
                icon.style.cssText = 'width: 26px; height: 26px; border-radius: 6px; background: rgba(35, 210, 226, 0.12); color: #23d2e2; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem; flex-shrink: 0;';
                icon.textContent = '#';
                row.appendChild(icon);

                const tagWrap = document.createElement('div');
                tagWrap.style.cssText = 'flex-grow: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;';
                tagWrap.innerHTML = `<span>#${item.tag}</span>`;
                row.appendChild(tagWrap);

                if (item.count && item.count > 1) {
                    const badge = document.createElement('span');
                    badge.style.cssText = 'font-size: 0.72rem; padding: 2px 6px; border-radius: 10px; background: rgba(0,0,0,0.06); color: #8f91ac;';
                    badge.textContent = item.count;
                    row.appendChild(badge);
                }
            }

            row.addEventListener('mouseenter', () => {
                selectedIndex = index;
                renderHighlight();
            });

            row.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                applySelection(item);
            });

            drop.appendChild(row);
        });

        positionDropdown(activeInput);
        drop.style.display = 'block';
    }

    function renderHighlight() {
        if (!dropdown) return;
        const rows = dropdown.querySelectorAll('.autocomplete-item');
        rows.forEach((row, i) => {
            if (i === selectedIndex) {
                row.style.background = 'rgba(97, 93, 250, 0.12)';
                row.style.color = '#615dfa';
                row.style.fontWeight = '600';
                row.scrollIntoView({ block: 'nearest' });
            } else {
                row.style.background = 'transparent';
                row.style.color = 'inherit';
                row.style.fontWeight = 'normal';
            }
        });
    }

    function applySelection(item) {
        if (!activeInput || matchStartIndex === -1) return;

        const val = activeInput.value;
        const before = val.substring(0, matchStartIndex);
        const after = val.substring(activeInput.selectionStart);

        let replacement = '';
        if (currentMode === 'mention') {
            replacement = '@' + item.username + ' ';
        } else {
            replacement = '#' + item.tag + ' ';
        }

        activeInput.value = before + replacement + after;
        const newCursorPos = before.length + replacement.length;
        activeInput.setSelectionRange(newCursorPos, newCursorPos);
        activeInput.focus();

        // Dispatch input event so any listeners (character count, preview, auto-grow) update
        activeInput.dispatchEvent(new Event('input', { bubbles: true }));

        hideDropdown();
    }

    function checkCursorWord(input) {
        activeInput = input;
        const cursorPos = input.selectionStart;
        const text = input.value.substring(0, cursorPos);

        // Find word boundary before cursor
        const match = text.match(/([@#][\p{L}\p{N}_]*)$/u);

        if (!match) {
            hideDropdown();
            return;
        }

        const token = match[0];
        matchStartIndex = cursorPos - token.length;
        const trigger = token.charAt(0);
        const query = token.substring(1);

        if (trigger === '@') {
            currentMode = 'mention';
            currentQuery = query;
            fetchSuggestions('/mentions/users?q=' + encodeURIComponent(query));
        } else if (trigger === '#') {
            currentMode = 'tag';
            currentQuery = query;
            fetchSuggestions('/tags/suggest?q=' + encodeURIComponent(query));
        } else {
            hideDropdown();
        }
    }

    function fetchSuggestions(url) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.ok ? res.json() : [])
            .then(data => {
                items = Array.isArray(data) ? data : [];
                selectedIndex = items.length > 0 ? 0 : -1;
                if (items.length > 0) {
                    renderItems();
                } else {
                    hideDropdown();
                }
            })
            .catch(err => {
                console.error('Autocomplete fetch error:', err);
                hideDropdown();
            });
        }, 150);
    }

    function handleKeyDown(e) {
        if (!dropdown || dropdown.style.display === 'none' || items.length === 0) {
            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = (selectedIndex + 1) % items.length;
            renderHighlight();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = (selectedIndex - 1 + items.length) % items.length;
            renderHighlight();
        } else if (e.key === 'Enter' || e.key === 'Tab') {
            if (selectedIndex >= 0 && selectedIndex < items.length) {
                e.preventDefault();
                applySelection(items[selectedIndex]);
            }
        } else if (e.key === 'Escape') {
            e.preventDefault();
            hideDropdown();
        }
    }

    // Attach event delegation across document
    document.addEventListener('input', function (e) {
        const target = e.target;
        if (target && (target.tagName === 'TEXTAREA' || (target.tagName === 'INPUT' && target.type === 'text'))) {
            checkCursorWord(target);
        }
    });

    document.addEventListener('keydown', function (e) {
        const target = e.target;
        if (target && (target.tagName === 'TEXTAREA' || (target.tagName === 'INPUT' && target.type === 'text'))) {
            handleKeyDown(e);
        }
    });

    document.addEventListener('blur', function (e) {
        // Small delay to allow click event on dropdown items to fire first
        setTimeout(() => {
            if (document.activeElement !== activeInput) {
                hideDropdown();
            }
        }, 200);
    }, true);

})();
