(function () {
  function parseConfig(widget) {
    if (!widget || !widget.dataset) {
      return null;
    }

    return {
      threadUrl: widget.dataset.threadUrl || '',
      pollUrl: widget.dataset.pollUrl || '',
      startUrl: widget.dataset.startUrl || '',
      messageUrl: widget.dataset.messageUrl || '',
      isAuthenticated: widget.dataset.isAuthenticated === '1',
      pageUrl: widget.dataset.pageUrl || window.location.href,
      pageTitle: widget.dataset.pageTitle || document.title,
      labels: {
        requestFailed: widget.dataset.requestFailed || 'Request failed'
      }
    };
  }

  function requestJson(url, options) {
    return fetch(url, options).then(function (response) {
      return response.json().then(function (payload) {
        if (!response.ok) {
          throw new Error((payload && payload.message) ? payload.message : 'Request failed');
        }
        return payload;
      });
    });
  }

  function initWidget(widget) {
    var config = parseConfig(widget);
    if (!config) {
      return;
    }

    var toggle = widget.querySelector('[data-support-chat-toggle]');
    var closeButton = widget.querySelector('[data-support-chat-close]');
    var panel = widget.querySelector('.support-chat-widget__panel');
    var form = widget.querySelector('[data-support-chat-form]');
    var messages = widget.querySelector('[data-support-chat-messages]');
    var errorNode = widget.querySelector('[data-support-chat-error]');
    var guestFields = widget.querySelector('[data-support-chat-guest-fields]');
    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    var token = csrfToken ? csrfToken.getAttribute('content') : '';
    var currentThread = null;
    var latestId = 0;
    var pollTimer = null;

    function threadHost() {
      return messages.querySelector('.support-chat-widget__thread') || messages;
    }

    function setOpen(open) {
      if (!panel || !toggle) {
        return;
      }
      panel.hidden = !open;
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open) {
        messages.scrollTop = messages.scrollHeight;
      }
    }

    function setError(message) {
      if (errorNode) {
        errorNode.textContent = message || '';
      }
    }

    function updateGuestFields() {
      if (!guestFields) {
        return;
      }
      guestFields.hidden = !!currentThread || !!config.isAuthenticated;
    }

    function renderFull(html) {
      messages.innerHTML = html || '';
      latestId = 0;
      Array.prototype.slice.call(messages.querySelectorAll('[data-message-id]')).forEach(function (node) {
        latestId = Math.max(latestId, parseInt(node.getAttribute('data-message-id') || '0', 10));
      });
      messages.scrollTop = messages.scrollHeight;
    }

    function appendItems(html) {
      if (!html) {
        return;
      }
      var host = threadHost();
      var container = document.createElement('div');
      container.innerHTML = html;
      Array.prototype.slice.call(container.children).forEach(function (child) {
        if (child.getAttribute && child.getAttribute('data-message-id')) {
          latestId = Math.max(latestId, parseInt(child.getAttribute('data-message-id') || '0', 10));
        }
        host.appendChild(child);
      });
      messages.scrollTop = messages.scrollHeight;
    }

    function fetchThread() {
      requestJson(config.threadUrl, {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then(function (payload) {
          currentThread = payload.thread || null;
          updateGuestFields();
          renderFull(payload.html || '');
          if (payload.latest_id) {
            latestId = parseInt(payload.latest_id || '0', 10) || latestId;
          }
        })
        .catch(function () {});
    }

    function poll() {
      if (!currentThread) {
        return;
      }

      var url = new URL(config.pollUrl, window.location.origin);
      url.searchParams.set('after_id', String(latestId || 0));

      requestJson(url.toString(), {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then(function (payload) {
          if (!payload || !payload.count) {
            return;
          }
          appendItems(payload.html || '');
        })
        .catch(function () {});
    }

    if (toggle) {
      toggle.addEventListener('click', function () {
        setOpen(panel.hidden);
      });
    }

    if (closeButton) {
      closeButton.addEventListener('click', function () {
        setOpen(false);
      });
    }

    if (form) {
      form.addEventListener('submit', function (event) {
        event.preventDefault();
        setError('');

        var formData = new FormData(form);
        var message = (formData.get('message') || '').toString().trim();
        if (!message) {
          return;
        }

        var actionUrl = currentThread ? config.messageUrl : config.startUrl;
        if (!currentThread) {
          formData.append('page_url', config.pageUrl || window.location.href);
          formData.append('page_title', config.pageTitle || document.title);
        }

        requestJson(actionUrl, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token
          },
          body: formData
        })
          .then(function (payload) {
            currentThread = payload.thread || currentThread;
            updateGuestFields();
            if (currentThread && payload.html && !messages.querySelector('[data-message-id]')) {
              renderFull(payload.html);
            } else if (currentThread && payload.html && actionUrl === config.startUrl) {
              renderFull(payload.html);
            } else {
              appendItems(payload.html || '');
            }
            form.reset();
            updateGuestFields();
          })
          .catch(function (error) {
            setError(error && error.message ? error.message : (config.labels.requestFailed || 'Request failed'));
          });
      });
    }

    updateGuestFields();
    fetchThread();
    pollTimer = window.setInterval(poll, 5000);

    window.addEventListener('beforeunload', function () {
      if (pollTimer) {
        window.clearInterval(pollTimer);
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    Array.prototype.slice.call(document.querySelectorAll('[data-support-chat-widget]')).forEach(initWidget);
  });
})();
