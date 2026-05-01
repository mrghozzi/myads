(function () {
  const app = document.querySelector('[data-messages-app]');
  if (!app) {
    return;
  }

  const searchInput = document.getElementById('message-search');
  const conversationList = document.querySelector('[data-conversation-list]');
  const messageList = document.querySelector('[data-message-list]');
  const messageInput = document.getElementById('message_text');
  const sendButton = document.getElementById('btnSend');
  const attachButton = document.getElementById('btnAttach');
  const attachmentInput = document.getElementById('message_attachment');
  const attachmentMeta = document.getElementById('message_attachment_meta');
  const attachmentName = document.getElementById('message_attachment_name');
  const attachmentClear = document.getElementById('message_attachment_clear');
  const composeError = document.getElementById('message_compose_error');
  const emojiButton = document.getElementById('btnEmoji');
  const emojiPanel = document.getElementById('emoji_panel');
  const emojiSearch = document.getElementById('emoji_search');
  const emojiScroll = emojiPanel ? emojiPanel.querySelector('[data-emoji-scroll]') : null;
  const emojiRecentGrid = emojiPanel ? emojiPanel.querySelector('[data-emoji-recent-grid]') : null;
  const toastStack = document.getElementById('messages_toast_stack');
  const railToggle = document.querySelector('[data-rail-toggle]');

  const updateUrl = app.dataset.updateUrl || '';
  const activeConversation = app.dataset.activeConversation || '';
  const sendUrl = app.dataset.sendUrl || '';
  const historyUrl = app.dataset.historyUrl || '';
  const soundUrl = app.dataset.soundUrl || '';
  const maxAttachmentBytes = parseInt(app.dataset.maxAttachmentBytes || '5242880', 10);
  const topThreshold = 40;

  let notificationAudio = null;
  if (soundUrl) {
    notificationAudio = new Audio(soundUrl);
    notificationAudio.load();
  }

  let latestMessageId = parseMessageId(app.dataset.latestId || '0');
  let latestGlobalMessageId = parseMessageId(app.dataset.latestGlobalId || '0');
  let oldestMessageId = 0;
  let hasOlderMessages = false;
  let historyLoading = false;
  let refreshLoading = false;
  let sendLoading = false;
  let refreshTimer = null;
  const defaultRecentEmojis = ['😁', '😊', '❤️', '😂', '😮', '🤯', '👍', '🙏'];

  function parseMessageId(value) {
    const parsed = parseInt(value, 10);
    return Number.isFinite(parsed) && parsed > 0 ? parsed : 0;
  }

  function csrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    return token ? token.getAttribute('content') : '';
  }

  function getThread() {
    return messageList ? messageList.querySelector('[data-thread]') : null;
  }

  function getRows() {
    const thread = getThread();
    return thread ? Array.from(thread.querySelectorAll('[data-message-id]')) : [];
  }

  function syncBoundsFromDom() {
    const thread = getThread();
    if (!thread) {
      oldestMessageId = 0;
      latestMessageId = latestMessageId || 0;
      hasOlderMessages = false;
      return;
    }

    oldestMessageId = parseMessageId(thread.dataset.oldestId || '0');
    latestMessageId = parseMessageId(thread.dataset.latestId || latestMessageId || '0');
    hasOlderMessages = thread.dataset.hasOlder === '1';

    const rows = getRows();
    if (rows.length) {
      oldestMessageId = parseMessageId(rows[0].dataset.messageId || oldestMessageId);
      latestMessageId = parseMessageId(rows[rows.length - 1].dataset.messageId || latestMessageId);
    }

    updateThreadDataset();
  }

  function updateThreadDataset() {
    const thread = getThread();
    if (!thread) {
      return;
    }

    thread.dataset.oldestId = String(oldestMessageId || 0);
    thread.dataset.latestId = String(latestMessageId || 0);
    thread.dataset.hasOlder = hasOlderMessages ? '1' : '0';
  }

  function scrollToBottom() {
    if (messageList) {
      messageList.scrollTop = messageList.scrollHeight;
    }
  }

  function scheduleScrollToBottom(retries) {
    if (!messageList) {
      return;
    }

    let remaining = typeof retries === 'number' ? retries : 3;
    const tick = function () {
      scrollToBottom();
      remaining -= 1;
      if (remaining > 0) {
        window.requestAnimationFrame(tick);
      }
    };

    window.requestAnimationFrame(tick);
  }

  function bindAttachmentScrollAnchors() {
    if (!messageList) {
      return;
    }

    messageList.querySelectorAll('img').forEach(function (image) {
      if (image.dataset.scrollAnchorBound === '1' || image.complete) {
        return;
      }

      const keepAtBottom = shouldStickToBottom();
      image.dataset.scrollAnchorBound = '1';
      image.addEventListener('load', function () {
        if (keepAtBottom || shouldStickToBottom()) {
          scheduleScrollToBottom(2);
        }
      }, { once: true });
    });
  }

  function shouldStickToBottom() {
    if (!messageList) {
      return true;
    }

    return messageList.scrollHeight - messageList.scrollTop - messageList.clientHeight < 90;
  }

  function htmlToItems(html) {
    if (!html) {
      return [];
    }

    const container = document.createElement('div');
    container.innerHTML = html;
    return Array.from(container.children).filter(function (node) {
      return node.nodeType === 1 && node.hasAttribute('data-thread-item');
    });
  }

  function existingIds() {
    const ids = new Set();
    getRows().forEach(function (row) {
      if (row.dataset.messageId) {
        ids.add(row.dataset.messageId);
      }
    });
    return ids;
  }

  function updateBoundsFromRows(rows) {
    rows.forEach(function (row) {
      const id = parseMessageId(row.dataset.messageId || '0');
      if (!id) {
        return;
      }
      if (!oldestMessageId || id < oldestMessageId) {
        oldestMessageId = id;
      }
      if (!latestMessageId || id > latestMessageId) {
        latestMessageId = id;
      }
    });
    updateThreadDataset();
  }

  function appendItems(items, forceBottom) {
    const thread = getThread();
    if (!thread || !items.length) {
      return 0;
    }

    const stick = forceBottom || shouldStickToBottom();
    const ids = existingIds();
    const fragment = document.createDocumentFragment();
    const addedRows = [];
    let addedCount = 0;

    items.forEach(function (item) {
      const id = item.dataset ? item.dataset.messageId : '';
      if (id && ids.has(id)) {
        return;
      }

      fragment.appendChild(item);
      if (id) {
        ids.add(id);
        addedRows.push(item);
        addedCount += 1;
      }
    });

    if (!fragment.childNodes.length) {
      return 0;
    }

    removeEmptyThread();
    thread.appendChild(fragment);
    updateBoundsFromRows(addedRows);
    bindAttachmentScrollAnchors();

    if (stick) {
      scheduleScrollToBottom(2);
    }

    return addedCount;
  }

  function prependItems(items) {
    const thread = getThread();
    if (!thread || !items.length) {
      return 0;
    }

    const beforeHeight = messageList ? messageList.scrollHeight : 0;
    const beforeTop = messageList ? messageList.scrollTop : 0;
    const ids = existingIds();
    const fragment = document.createDocumentFragment();
    const addedRows = [];
    let addedCount = 0;

    items.forEach(function (item) {
      const id = item.dataset ? item.dataset.messageId : '';
      if (id && ids.has(id)) {
        return;
      }

      fragment.appendChild(item);
      if (id) {
        ids.add(id);
        addedRows.push(item);
        addedCount += 1;
      }
    });

    if (!fragment.childNodes.length) {
      return 0;
    }

    removeEmptyThread();
    thread.insertBefore(fragment, thread.firstChild);
    updateBoundsFromRows(addedRows);

    if (messageList) {
      const addedHeight = messageList.scrollHeight - beforeHeight;
      messageList.scrollTop = beforeTop <= topThreshold + 1 ? 8 : beforeTop + addedHeight;
    }

    return addedCount;
  }

  function removeEmptyThread() {
    if (!messageList) {
      return;
    }

    const empty = messageList.querySelector('.messages-empty-thread');
    if (empty) {
      empty.remove();
    }
  }

  function applyConversationSearch() {
    if (!searchInput || !conversationList) {
      return;
    }

    const query = searchInput.value.trim().toLowerCase();
    conversationList.querySelectorAll('[data-conversation-row]').forEach(function (row) {
      const haystack = (row.dataset.name || '') + ' ' + (row.dataset.message || '');
      row.style.display = !query || haystack.includes(query) ? '' : 'none';
    });
  }

  function setComposeError(message) {
    if (composeError) {
      composeError.textContent = message || '';
    }
  }

  function clearComposeError() {
    setComposeError('');
  }

  function formatBytes(bytes) {
    if (!bytes || bytes <= 0) {
      return '0 B';
    }
    if (bytes >= 1024 * 1024) {
      return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }
    return (bytes / 1024).toFixed(1) + ' KB';
  }

  function resetAttachment() {
    if (attachmentInput) {
      attachmentInput.value = '';
    }
    if (attachmentName) {
      attachmentName.textContent = '';
    }
    if (attachmentMeta) {
      attachmentMeta.classList.add('is-hidden');
    }
  }

  function showAttachment(file) {
    if (!attachmentMeta || !attachmentName) {
      return;
    }
    if (!file) {
      resetAttachment();
      return;
    }
    attachmentName.textContent = file.name + ' (' + formatBytes(file.size) + ')';
    attachmentMeta.classList.remove('is-hidden');
  }

  function resizeInput() {
    if (!messageInput) {
      return;
    }
    messageInput.style.height = 'auto';
    messageInput.style.height = Math.min(messageInput.scrollHeight, 132) + 'px';
  }

  function setSendingState(isSending) {
    sendLoading = isSending;
    if (sendButton) {
      sendButton.disabled = isSending;
    }
  }

  function postMessage() {
    if (!messageInput || !sendUrl || sendLoading) {
      return;
    }

    const text = messageInput.value.trim();
    const file = attachmentInput && attachmentInput.files && attachmentInput.files.length
      ? attachmentInput.files[0]
      : null;

    if (!text && !file) {
      return;
    }

    if (file && file.size > maxAttachmentBytes) {
      setComposeError('Maximum attachment size is 5 MB.');
      return;
    }

    const body = new FormData();
    body.append('message', text);
    if (file) {
      body.append('attachment', file);
    }

    clearComposeError();
    setSendingState(true);

    fetch(sendUrl, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrfToken()
      },
      body: body
    })
      .then(jsonResponse)
      .then(function (payload) {
        const items = htmlToItems(payload.html || '');
        appendItems(items, true);
        latestMessageId = Math.max(latestMessageId, parseMessageId(payload.latest_id || '0'));
        latestGlobalMessageId = Math.max(latestGlobalMessageId, parseMessageId(payload.latest_global_id || latestGlobalMessageId));
        updateThreadDataset();
        messageInput.value = '';
        resizeInput();
        resetAttachment();
        refreshConversation();
      })
      .catch(function (error) {
        setComposeError(error.message || 'Unable to send message.');
      })
      .finally(function () {
        setSendingState(false);
      });
  }

  function jsonResponse(response) {
    return response.json().catch(function () {
      return { success: false, message: 'Request failed.' };
    }).then(function (payload) {
      if (!response.ok || !payload || payload.success === false) {
        throw new Error(payload && payload.message ? payload.message : 'Request failed.');
      }
      return payload;
    });
  }

  function loadOlderMessages() {
    if (!historyUrl || historyLoading || !hasOlderMessages || !oldestMessageId) {
      return;
    }

    historyLoading = true;
    const url = new URL(historyUrl, window.location.origin);
    url.searchParams.set('before_id', String(oldestMessageId));

    fetch(url.toString(), {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
      .then(jsonResponse)
      .then(function (payload) {
        prependItems(htmlToItems(payload.html || ''));
        const serverOldestId = parseMessageId(payload.oldest_id || '0');
        if (serverOldestId && (!oldestMessageId || serverOldestId < oldestMessageId)) {
          oldestMessageId = serverOldestId;
        }
        hasOlderMessages = payload.has_more === true;
        updateThreadDataset();
      })
      .catch(function () {})
      .finally(function () {
        historyLoading = false;
      });
  }

  function refreshConversation() {
    if (!updateUrl || refreshLoading) {
      return;
    }

    refreshLoading = true;
    const url = new URL(updateUrl, window.location.origin);
    url.searchParams.set('after_id', String(latestMessageId || 0));
    url.searchParams.set('toast_after_id', String(latestGlobalMessageId || 0));
    if (activeConversation) {
      url.searchParams.set('conversation', activeConversation);
    }

    fetch(url.toString(), {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
      .then(jsonResponse)
      .then(function (payload) {
        const previousGlobalId = latestGlobalMessageId;
        updateHeaderBadges(payload.unread_count || 0);

        if (conversationList && typeof payload.conversations_html === 'string') {
          if (typeof conversationsPage !== 'undefined' && conversationsPage > 1) {
            const temp = document.createElement('div');
            temp.innerHTML = payload.conversations_html;
            const newRows = Array.from(temp.querySelectorAll('[data-conversation-row]'));
            if (newRows.length > 0) {
              const emptyState = conversationList.querySelector('[data-conversation-empty]');
              if (emptyState) emptyState.remove();

              newRows.forEach(function (newRow, index) {
                const name = newRow.dataset.name;
                const existingRow = conversationList.querySelector('[data-name="' + name + '"]');
                if (existingRow) {
                  existingRow.innerHTML = newRow.innerHTML;
                  existingRow.className = newRow.className;
                  existingRow.dataset.name = newRow.dataset.name || '';
                  existingRow.dataset.message = newRow.dataset.message || '';
                  
                  const targetSibling = conversationList.children[index];
                  if (targetSibling && targetSibling !== existingRow) {
                    conversationList.insertBefore(existingRow, targetSibling);
                  }
                } else {
                  const targetSibling = conversationList.children[index];
                  if (targetSibling) {
                    conversationList.insertBefore(newRow, targetSibling);
                  } else {
                    conversationList.appendChild(newRow);
                  }
                }
              });
            }
          } else {
            conversationList.innerHTML = payload.conversations_html;
          }
          applyConversationSearch();
        }

        if (payload.active_thread && payload.active_thread.count > 0) {
          appendItems(htmlToItems(payload.active_thread.html || ''), false);
          bindAttachmentScrollAnchors();
          latestMessageId = Math.max(latestMessageId, parseMessageId(payload.active_thread.latest_id || payload.latest_id || '0'));
          updateThreadDataset();
        } else {
          latestMessageId = Math.max(latestMessageId, parseMessageId(payload.latest_id || '0'));
        }

        if (payload.toast && payload.toast.id && parseMessageId(payload.toast.id) > previousGlobalId) {
          showToast(payload.toast);
        }

        if (payload.latest_global_id) {
          latestGlobalMessageId = Math.max(latestGlobalMessageId, parseMessageId(payload.latest_global_id));
        }
      })
      .catch(function () {})
      .finally(function () {
        refreshLoading = false;
      });
  }

  function formatCount(count) {
    const value = parseInt(count, 10) || 0;
    return value > 99 ? '99+' : String(value);
  }

  function updateHeaderBadges(count) {
    const value = parseInt(count, 10) || 0;
    document.querySelectorAll('[data-message-unread-count]').forEach(function (node) {
      if (value > 0) {
        node.hidden = false;
        node.textContent = formatCount(value);
      } else {
        node.hidden = true;
        node.textContent = '';
      }
    });

    document.querySelectorAll('[data-message-action-trigger]').forEach(function (node) {
      node.classList.toggle('unread', value > 0);
    });
  }

  function showToast(toast) {
    if (!toastStack || !toast) {
      return;
    }

    const node = document.createElement('div');
    node.className = 'messages-toast';
    node.innerHTML = [
      '<a class="messages-toast-avatar" href="' + escapeAttr(toast.url || '#') + '"><img src="' + escapeAttr(toast.avatar_url || '') + '" alt=""></a>',
      '<a class="messages-toast-main" href="' + escapeAttr(toast.url || '#') + '">',
      '<p class="messages-toast-title">' + escapeHtml(toast.title || '') + '</p>',
      '<p class="messages-toast-body">' + escapeHtml(toast.body || '') + '</p>',
      '</a>',
      '<button type="button" class="messages-toast-close" aria-label="Close"><i class="fa fa-xmark" aria-hidden="true"></i></button>'
    ].join('');

    const close = node.querySelector('.messages-toast-close');
    close.addEventListener('click', function () {
      node.remove();
    });

    toastStack.appendChild(node);
    setTimeout(function () {
      node.remove();
    }, 7000);

    if (notificationAudio) {
      notificationAudio.currentTime = 0;
      const playPromise = notificationAudio.play();
      if (playPromise !== undefined) {
        playPromise.catch(function(error) {
          console.warn("Audio playback failed:", error);
        });
      }
    }

    showBrowserNotification(toast);
  }

  function showBrowserNotification(toast) {
    if (!('Notification' in window) || Notification.permission !== 'granted') {
      return;
    }

    const notification = new Notification(toast.title || 'New message', {
      body: toast.body || '',
      icon: toast.avatar_url || ''
    });
    notification.onclick = function () {
      if (toast.url) {
        window.focus();
        window.location.href = toast.url;
      }
    };
  }

  function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = String(value || '');
    return div.innerHTML;
  }

  function escapeAttr(value) {
    return String(value || '').replace(/"/g, '&quot;');
  }

  function toggleEmojiPanel(open) {
    if (!emojiPanel || !emojiButton) {
      return;
    }
    emojiPanel.classList.toggle('is-open', open);
    emojiPanel.setAttribute('aria-hidden', open ? 'false' : 'true');
    emojiButton.classList.toggle('is-active', open);
    if (open) {
      renderRecentEmojis();
      applyEmojiSearch();
      setTimeout(function () {
        if (emojiSearch) {
          emojiSearch.focus();
        }
      }, 0);
    }
  }

  function readRecentEmojis() {
    try {
      const stored = JSON.parse(localStorage.getItem('myads.messageEmojiRecent') || '[]');
      if (Array.isArray(stored) && stored.length) {
        return stored.filter(Boolean).slice(0, 16);
      }
    } catch (e) {}

    return defaultRecentEmojis.slice();
  }

  function writeRecentEmojis(items) {
    try {
      localStorage.setItem('myads.messageEmojiRecent', JSON.stringify(items.slice(0, 16)));
    } catch (e) {}
  }

  function renderRecentEmojis() {
    if (!emojiRecentGrid) {
      return;
    }

    emojiRecentGrid.innerHTML = '';
    readRecentEmojis().forEach(function (emoji) {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'messages-emoji-item';
      button.dataset.emoji = emoji;
      button.dataset.keywords = 'recent';
      button.textContent = emoji;
      emojiRecentGrid.appendChild(button);
    });
  }

  function rememberRecentEmoji(emoji) {
    if (!emoji) {
      return;
    }

    const next = [emoji].concat(readRecentEmojis().filter(function (item) {
      return item !== emoji;
    }));

    writeRecentEmojis(next);
    renderRecentEmojis();
  }

  function setActiveEmojiCategory(category) {
    if (!emojiPanel || !category) {
      return;
    }

    emojiPanel.querySelectorAll('[data-emoji-category]').forEach(function (button) {
      button.classList.toggle('is-active', button.dataset.emojiCategory === category);
    });
  }

  function scrollEmojiCategory(category) {
    if (!emojiScroll || !category) {
      return;
    }

    const section = emojiScroll.querySelector('[data-emoji-section="' + category + '"]');
    if (!section) {
      return;
    }

    emojiScroll.scrollTo({
      top: Math.max(0, section.offsetTop - emojiScroll.offsetTop - 4),
      behavior: 'smooth'
    });
    setActiveEmojiCategory(category);
  }

  function updateActiveEmojiCategoryFromScroll() {
    if (!emojiScroll || !emojiPanel) {
      return;
    }

    let active = 'recent';
    const scrollTop = emojiScroll.scrollTop + 14;
    emojiPanel.querySelectorAll('[data-emoji-section]').forEach(function (section) {
      if (section.offsetTop - emojiScroll.offsetTop <= scrollTop) {
        active = section.dataset.emojiSection || active;
      }
    });
    setActiveEmojiCategory(active);
  }

  function applyEmojiSearch() {
    if (!emojiPanel) {
      return;
    }

    const query = emojiSearch ? emojiSearch.value.trim().toLowerCase() : '';
    emojiPanel.querySelectorAll('[data-emoji-section]').forEach(function (section) {
      let visibleItems = 0;
      section.querySelectorAll('[data-emoji]').forEach(function (button) {
        const haystack = [
          button.dataset.emoji || '',
          button.dataset.keywords || '',
          button.textContent || ''
        ].join(' ').toLowerCase();
        const visible = !query || haystack.indexOf(query) !== -1;
        button.hidden = !visible;
        if (visible) {
          visibleItems += 1;
        }
      });
      section.classList.toggle('is-hidden', visibleItems === 0);
    });
  }

  function insertAtCaret(text) {
    if (!messageInput) {
      return;
    }
    const start = messageInput.selectionStart || messageInput.value.length;
    const end = messageInput.selectionEnd || messageInput.value.length;
    messageInput.value = messageInput.value.slice(0, start) + text + messageInput.value.slice(end);
    const next = start + text.length;
    messageInput.focus();
    messageInput.setSelectionRange(next, next);
    resizeInput();
  }

  let conversationsPage = 1;
  let conversationsLoading = false;

  function loadMoreConversations() {
    if (!conversationList || conversationsLoading) {
      return;
    }
    const hasMore = conversationList.dataset.hasMore === '1';
    if (!hasMore) {
      return;
    }

    const urlBase = conversationList.dataset.conversationsUrl;
    if (!urlBase) return;

    conversationsLoading = true;
    const url = new URL(urlBase, window.location.origin);
    url.searchParams.set('page', String(conversationsPage + 1));
    if (activeConversation) {
      url.searchParams.set('conversation', activeConversation);
    }

    fetch(url.toString(), {
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
      .then(jsonResponse)
      .then(function (payload) {
        if (payload.html) {
          const temp = document.createElement('div');
          temp.innerHTML = payload.html;
          
          Array.from(temp.querySelectorAll('[data-conversation-row]')).forEach(function (newRow) {
            const name = newRow.dataset.name;
            const existingRow = conversationList.querySelector('[data-name="' + name + '"]');
            if (!existingRow) {
              conversationList.appendChild(newRow);
            }
          });
        }
        conversationsPage += 1;
        conversationList.dataset.hasMore = payload.has_more ? '1' : '0';
        applyConversationSearch();
      })
      .catch(function () {})
      .finally(function () {
        conversationsLoading = false;
      });
  }

  document.addEventListener('DOMContentLoaded', function () {
    syncBoundsFromDom();
    bindAttachmentScrollAnchors();
    scheduleScrollToBottom(4);
    updateHeaderBadges(app.dataset.unreadCount || 0);

    if (railToggle) {
      railToggle.addEventListener('click', function () {
        const isOpen = app.classList.toggle('is-rail-open');
        railToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        if (!isOpen) {
          scheduleScrollToBottom(2);
        }
      });
    }

    if (searchInput) {
      searchInput.addEventListener('input', applyConversationSearch);
    }

    if (conversationList) {
      conversationList.addEventListener('scroll', function () {
        if (conversationList.scrollHeight - conversationList.scrollTop - conversationList.clientHeight < 50) {
          loadMoreConversations();
        }
      });
    }

    if (sendButton) {
      sendButton.addEventListener('click', postMessage);
    }

    if (messageInput) {
      messageInput.addEventListener('input', resizeInput);
      messageInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.shiftKey) {
          event.preventDefault();
          postMessage();
        }
        if (event.key === 'Escape') {
          toggleEmojiPanel(false);
        }
      });
      resizeInput();
    }

    if (attachButton && attachmentInput) {
      attachButton.addEventListener('click', function () {
        attachmentInput.click();
      });
    }

    if (attachmentInput) {
      attachmentInput.addEventListener('change', function () {
        const file = attachmentInput.files && attachmentInput.files.length ? attachmentInput.files[0] : null;
        if (file && file.size > maxAttachmentBytes) {
          setComposeError('Maximum attachment size is 5 MB.');
          resetAttachment();
          return;
        }
        clearComposeError();
        showAttachment(file);
      });
    }

    if (attachmentClear) {
      attachmentClear.addEventListener('click', function () {
        resetAttachment();
        clearComposeError();
      });
    }

    if (emojiButton && emojiPanel) {
      emojiButton.addEventListener('click', function (event) {
        event.preventDefault();
        toggleEmojiPanel(!emojiPanel.classList.contains('is-open'));
      });
      renderRecentEmojis();
      emojiPanel.addEventListener('click', function (event) {
        const emojiItem = event.target.closest('[data-emoji]');
        if (emojiItem && emojiPanel.contains(emojiItem)) {
          const emoji = emojiItem.dataset.emoji || '';
          insertAtCaret(emoji);
          rememberRecentEmoji(emoji);
          applyEmojiSearch();
          return;
        }

        const categoryButton = event.target.closest('[data-emoji-category]');
        if (categoryButton && emojiPanel.contains(categoryButton)) {
          if (emojiSearch) {
            emojiSearch.value = '';
            applyEmojiSearch();
          }
          scrollEmojiCategory(categoryButton.dataset.emojiCategory || 'recent');
        }
      });
      if (emojiSearch) {
        emojiSearch.addEventListener('input', applyEmojiSearch);
      }
      if (emojiScroll) {
        emojiScroll.addEventListener('scroll', updateActiveEmojiCategoryFromScroll, { passive: true });
      }
      document.addEventListener('click', function (event) {
        if (!emojiPanel.classList.contains('is-open')) {
          return;
        }
        if (emojiPanel.contains(event.target) || emojiButton.contains(event.target)) {
          return;
        }
        toggleEmojiPanel(false);
      });
    }

    if (messageList) {
      messageList.addEventListener('scroll', function () {
        if (messageList.scrollTop <= topThreshold) {
          loadOlderMessages();
        }
      }, { passive: true });
    }

    refreshTimer = setInterval(refreshConversation, document.hidden ? 8000 : 3000);
    document.addEventListener('visibilitychange', function () {
      if (refreshTimer) {
        clearInterval(refreshTimer);
      }
      refreshTimer = setInterval(refreshConversation, document.hidden ? 8000 : 3000);
      if (!document.hidden) {
        refreshConversation();
      }
    });
  });

  window.addEventListener('beforeunload', function () {
    if (refreshTimer) {
      clearInterval(refreshTimer);
    }
  });
})();
