(function () {
  var workspace = document.querySelector('[data-support-chat-admin]');
  if (!workspace) {
    return;
  }

  var messagePane = document.getElementById('support-chat-admin-messages');
  var replyForm = document.querySelector('[data-support-chat-reply-form]');
  var assignForm = document.querySelector('[data-support-chat-assign-form]');
  var statusGroup = document.querySelector('[data-support-chat-status-group]');
  var statusLabel = document.querySelector('[data-thread-status-label]');
  var csrfToken = document.querySelector('meta[name="csrf-token"]');
  var token = csrfToken ? csrfToken.getAttribute('content') : '';
  var latestId = messagePane ? parseInt(messagePane.getAttribute('data-latest-id') || '0', 10) : 0;
  var pollTimer = null;

  function host() {
    return messagePane ? (messagePane.querySelector('.support-chat-transcript__inner') || messagePane) : null;
  }

  function appendItems(html) {
    var inner = host();
    if (!inner || !html) {
      return;
    }

    var empty = inner.querySelector('.support-chat-empty');
    if (empty) {
      empty.remove();
    }

    var container = document.createElement('div');
    container.innerHTML = html;
    Array.prototype.slice.call(container.children).forEach(function (child) {
      if (child.getAttribute && child.getAttribute('data-message-id')) {
        latestId = Math.max(latestId, parseInt(child.getAttribute('data-message-id') || '0', 10));
      }
      inner.appendChild(child);
    });

    messagePane.setAttribute('data-latest-id', String(latestId || 0));
    messagePane.scrollTop = messagePane.scrollHeight;
  }

  function sendForm(url, data) {
    return fetch(url, {
      method: 'POST',
      headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': token
      },
      body: data
    }).then(function (response) {
      return response.json().then(function (payload) {
        if (!response.ok) {
          throw new Error((payload && payload.message) ? payload.message : 'Request failed');
        }
        return payload;
      });
    });
  }

  if (replyForm && messagePane) {
    var textarea = replyForm.querySelector('textarea[name="message"]');
    var errorNode = replyForm.querySelector('[data-support-chat-error]');
    var pollUrl = replyForm.getAttribute('data-poll-url');

    function setError(message) {
      if (errorNode) {
        errorNode.textContent = message || '';
      }
    }

    function poll() {
      if (!pollUrl) {
        return;
      }

      var url = new URL(pollUrl, window.location.origin);
      url.searchParams.set('after_id', String(latestId || 0));

      fetch(url.toString(), {
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
        .then(function (response) {
          if (!response.ok) {
            throw new Error('Poll failed');
          }
          return response.json();
        })
        .then(function (payload) {
          if (!payload || payload.success !== true || !payload.count) {
            return;
          }
          appendItems(payload.html || '');
        })
        .catch(function () {});
    }

    replyForm.addEventListener('submit', function (event) {
      event.preventDefault();
      setError('');

      var text = textarea ? textarea.value.trim() : '';
      if (!text) {
        return;
      }

      var data = new FormData(replyForm);
      sendForm(replyForm.getAttribute('action'), data)
        .then(function (payload) {
          appendItems(payload.html || '');
          if (textarea) {
            textarea.value = '';
          }
          if (statusLabel && payload.status_label) {
            statusLabel.textContent = payload.status_label;
          }
        })
        .catch(function (error) {
          setError(error && error.message ? error.message : 'Request failed');
        });
    });

    messagePane.scrollTop = messagePane.scrollHeight;
    pollTimer = window.setInterval(poll, 5000);
  }

  if (assignForm) {
    assignForm.addEventListener('change', function () {
      var data = new FormData(assignForm);
      sendForm(assignForm.getAttribute('action'), data).catch(function () {});
    });
  }

  if (statusGroup) {
    statusGroup.addEventListener('click', function (event) {
      var button = event.target.closest('[data-status]');
      if (!button) {
        return;
      }

      var data = new FormData();
      data.append('status', button.getAttribute('data-status') || '');
      sendForm(statusGroup.getAttribute('data-action'), data)
        .then(function () {
          Array.prototype.slice.call(statusGroup.querySelectorAll('[data-status]')).forEach(function (item) {
            item.classList.remove('btn-primary');
            item.classList.add('btn-outline-light');
          });
          button.classList.remove('btn-outline-light');
          button.classList.add('btn-primary');
          if (statusLabel) {
            statusLabel.textContent = button.textContent.trim();
            statusLabel.className = 'support-chat-pill support-chat-pill--status status-' + (button.getAttribute('data-status') || 'open');
          }
        })
        .catch(function () {});
    });
  }

  window.addEventListener('beforeunload', function () {
    if (pollTimer) {
      window.clearInterval(pollTimer);
    }
  });
})();
