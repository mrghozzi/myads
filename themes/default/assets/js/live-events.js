/**
 * MYADS Real-Time Events Engine (SSE Client) — RT-04
 * Handles Server-Sent Events (SSE), badge updates, live toasts, and fallback polling.
 */
(function (window, document) {
  'use strict';

  var LiveEventManager = {
    eventSource: null,
    config: {
      enabled: false,
      userId: 0,
      streamUrl: '/live/stream',
      fallbackPollUrl: '/api/notifications/unread-count',
      pollInterval: 30000,
    },
    retryCount: 0,
    maxRetries: 3,
    fallbackTimer: null,
    toastContainer: null,

    init: function (customConfig) {
      if (customConfig && typeof customConfig === 'object') {
        this.config = Object.assign({}, this.config, customConfig);
      } else if (window.MyAdsLiveConfig && typeof window.MyAdsLiveConfig === 'object') {
        this.config = Object.assign({}, this.config, window.MyAdsLiveConfig);
      }

      // Check if user is authenticated
      if (!this.config.userId || this.config.userId <= 0) {
        return;
      }

      this.config.enabled = true;
      this.ensureToastContainer();
      this.connect();
    },

    connect: function () {
      var self = this;

      if (!window.EventSource) {
        console.warn('[LiveEvents] EventSource not supported by browser. Falling back to polling.');
        self.startFallbackPolling();
        return;
      }

      try {
        if (self.eventSource) {
          self.eventSource.close();
        }

        self.eventSource = new EventSource(self.config.streamUrl);

        // 1. Connection Opened
        self.eventSource.onopen = function () {
          self.retryCount = 0;
          if (self.fallbackTimer) {
            clearInterval(self.fallbackTimer);
            self.fallbackTimer = null;
          }
        };

        // 2. Handshake Event
        self.eventSource.addEventListener('handshake', function (e) {
          try {
            var data = JSON.parse(e.data);
            self.handleHandshake(data);
          } catch (err) {
            console.error('[LiveEvents] Error parsing handshake payload:', err);
          }
        });

        // 3. Notifications Event
        self.eventSource.addEventListener('notifications', function (e) {
          try {
            var data = JSON.parse(e.data);
            self.handleNotifications(data);
          } catch (err) {
            console.error('[LiveEvents] Error parsing notifications payload:', err);
          }
        });

        // 4. Messages Event
        self.eventSource.addEventListener('messages', function (e) {
          try {
            var data = JSON.parse(e.data);
            self.handleMessages(data);
          } catch (err) {
            console.error('[LiveEvents] Error parsing messages payload:', err);
          }
        });

        // 5. Feed Event
        self.eventSource.addEventListener('feed', function (e) {
          try {
            var data = JSON.parse(e.data);
            self.handleFeed(data);
          } catch (err) {
            console.error('[LiveEvents] Error parsing feed payload:', err);
          }
        });

        // 6. Admin Alerts Event
        self.eventSource.addEventListener('admin', function (e) {
          try {
            var data = JSON.parse(e.data);
            self.handleAdmin(data);
          } catch (err) {
            console.error('[LiveEvents] Error parsing admin payload:', err);
          }
        });

        // 7. Heartbeat Ping & Reconnect
        self.eventSource.addEventListener('ping', function () {});
        self.eventSource.addEventListener('reconnect', function () {});

        // 8. Error Handling
        self.eventSource.onerror = function () {
          self.retryCount++;
          if (self.retryCount >= self.maxRetries) {
            console.warn('[LiveEvents] Max SSE retries reached. Switching to fallback polling.');
            if (self.eventSource) {
              self.eventSource.close();
              self.eventSource = null;
            }
            self.startFallbackPolling();
          }
        };
      } catch (e) {
        console.error('[LiveEvents] Failed to initialize EventSource:', e);
        self.startFallbackPolling();
      }
    },

    handleHandshake: function (data) {
      if (!data) return;

      if (typeof data.unread_notifications !== 'undefined') {
        this.updateNotificationBadges(data.unread_notifications);
      }
      if (typeof data.unread_messages !== 'undefined') {
        this.updateMessageBadges(data.unread_messages);
      }

      window.dispatchEvent(new CustomEvent('myads:live-handshake', { detail: data }));
    },

    handleNotifications: function (data) {
      if (!data) return;

      if (typeof data.unread_count !== 'undefined') {
        this.updateNotificationBadges(data.unread_count);
      }

      if (data.has_new && data.latest) {
        this.showToast({
          title: 'إشعار جديد',
          body: data.latest.name,
          url: data.latest.url || '/notification',
          icon: data.latest.logo || 'notification',
          type: 'notification',
        });
      }

      window.dispatchEvent(new CustomEvent('myads:live-notification', { detail: data }));
    },

    handleMessages: function (data) {
      if (!data) return;

      if (typeof data.unread_count !== 'undefined') {
        this.updateMessageBadges(data.unread_count);
      }

      if (data.has_new && data.latest) {
        this.showToast({
          title: data.latest.sender_name || 'رسالة جديدة',
          body: data.latest.text_preview || 'أرسل لك رسالة خاصة',
          url: '/messages',
          avatar: data.latest.sender_avatar,
          type: 'message',
        });
      }

      window.dispatchEvent(new CustomEvent('myads:live-message', { detail: data }));
    },

    handleFeed: function (data) {
      if (!data) return;
      window.dispatchEvent(new CustomEvent('myads:live-feed', { detail: data }));
    },

    handleAdmin: function (data) {
      if (!data) return;
      window.dispatchEvent(new CustomEvent('myads:live-admin', { detail: data }));
    },

    updateNotificationBadges: function (count) {
      var safeCount = parseInt(count, 10) || 0;
      if (typeof window.updateNotificationIndicators === 'function') {
        window.updateNotificationIndicators(safeCount);
      } else {
        var formatted = safeCount > 99 ? '99+' : String(safeCount);
        document.querySelectorAll('[data-notification-badge]').forEach(function (node) {
          if (safeCount > 0) {
            node.hidden = false;
            node.textContent = formatted;
          } else {
            node.hidden = true;
            node.textContent = '';
          }
        });
      }
    },

    updateMessageBadges: function (count) {
      var safeCount = parseInt(count, 10) || 0;
      var formatted = safeCount > 99 ? '99+' : String(safeCount);

      document.querySelectorAll('[data-message-unread-count]').forEach(function (node) {
        if (safeCount > 0) {
          node.hidden = false;
          node.textContent = formatted;
        } else {
          node.hidden = true;
          node.textContent = '';
        }
      });

      document.querySelectorAll('[data-message-action-trigger]').forEach(function (node) {
        node.classList.toggle('unread', safeCount > 0);
      });
    },

    ensureToastContainer: function () {
      if (document.getElementById('myads-live-toast-container')) {
        this.toastContainer = document.getElementById('myads-live-toast-container');
        return;
      }

      var container = document.createElement('div');
      container.id = 'myads-live-toast-container';
      container.setAttribute('dir', document.documentElement.getAttribute('dir') || 'ltr');
      container.style.cssText = [
        'position: fixed',
        'bottom: 24px',
        document.documentElement.getAttribute('dir') === 'rtl' ? 'left: 24px' : 'right: 24px',
        'z-index: 999999',
        'display: flex',
        'flex-direction: column',
        'gap: 12px',
        'pointer-events: none',
        'max-width: 360px',
        'width: 100%',
      ].join(';');

      document.body.appendChild(container);
      this.toastContainer = container;
    },

    showToast: function (options) {
      this.ensureToastContainer();
      if (!this.toastContainer) return;

      var toast = document.createElement('div');
      toast.className = 'myads-live-toast';
      toast.style.cssText = [
        'pointer-events: auto',
        'background: var(--notification-ui-card-bg, #1f2637)',
        'color: var(--notification-ui-summary-heading, #fff)',
        'border: 1px solid var(--notification-ui-card-unread-border, rgba(97, 93, 250, 0.3))',
        'border-radius: 12px',
        'padding: 12px 16px',
        'box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2)',
        'display: flex',
        'align-items: center',
        'gap: 12px',
        'animation: myadsToastIn 0.3s cubic-bezier(0.16, 1, 0.3, 1)',
        'transition: opacity 0.3s ease, transform 0.3s ease',
        'backdrop-filter: blur(8px)',
        'font-family: inherit',
      ].join(';');

      var avatarHtml = '';
      if (options.avatar) {
        avatarHtml = '<img src="' + options.avatar + '" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; flex-shrink: 0;" alt="">';
      } else {
        avatarHtml = '<div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #615dfa, #23d2e2); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 14px; flex-shrink: 0;"><i class="fa fa-bell"></i></div>';
      }

      var contentHtml = [
        '<div style="flex: 1; min-width: 0;">',
        '<a href="' + (options.url || '#') + '" style="text-decoration: none; color: inherit; display: block;">',
        '<strong style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">' + this.escapeHtml(options.title || '') + '</strong>',
        '<p style="margin: 0; font-size: 12px; opacity: 0.85; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">' + this.escapeHtml(options.body || '') + '</p>',
        '</a>',
        '</div>',
      ].join('');

      var closeBtnHtml = '<button type="button" style="background: none; border: none; color: inherit; opacity: 0.6; cursor: pointer; padding: 4px; font-size: 14px; line-height: 1;"><i class="fa fa-times"></i></button>';

      toast.innerHTML = avatarHtml + contentHtml + closeBtnHtml;

      var closeBtn = toast.querySelector('button');
      var dismissToast = function () {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px) scale(0.95)';
        setTimeout(function () {
          if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
          }
        }, 300);
      };

      if (closeBtn) {
        closeBtn.addEventListener('click', dismissToast);
      }

      this.toastContainer.appendChild(toast);

      // Auto-dismiss after 6 seconds
      setTimeout(dismissToast, 6000);
    },

    escapeHtml: function (str) {
      var div = document.createElement('div');
      div.textContent = str;
      return div.innerHTML;
    },

    startFallbackPolling: function () {
      var self = this;
      if (self.fallbackTimer) return;

      self.fallbackTimer = setInterval(function () {
        if (document.hidden) return; // Save bandwidth when tab inactive

        fetch(self.config.fallbackPollUrl, {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin'
        })
          .then(function (res) { return res.json(); })
          .then(function (data) {
            if (data && typeof data.unread_count !== 'undefined') {
              self.updateNotificationBadges(data.unread_count);
            }
          })
          .catch(function () {});
      }, self.config.pollInterval);
    }
  };

  // Expose globally
  window.LiveEventManager = LiveEventManager;

  // Auto-init if config exists
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      LiveEventManager.init();
    });
  } else {
    LiveEventManager.init();
  }
})(window, document);
