<!-- Continuous Audio Player Bar (v4.5.2) -->
<div id="v-continuous-audio-bar" class="v-continuous-audio-bar d-none" style="position: fixed; bottom: 20px; right: 20px; left: 20px; max-width: 580px; margin: 0 auto; z-index: 1050; background: rgba(15, 23, 42, 0.88); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 20px; box-shadow: 0 16px 40px rgba(0, 0, 0, 0.35); color: #fff; padding: 12px 18px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
    <audio id="v-continuous-audio-element" preload="auto"></audio>
    <div class="d-flex align-items-center justify-content-between gap-3">
        <!-- Track Info & Avatar -->
        <div class="d-flex align-items-center gap-3 overflow-hidden" style="min-width: 0;">
            <div class="position-relative flex-shrink-0">
                <img id="v-audio-track-avatar" src="{{ asset('public/assets/images/default-avatar.png') }}" class="rounded-circle" style="width: 42px; height: 42px; object-fit: cover; border: 2px solid rgba(255,255,255,0.2);">
                <span id="v-audio-disc-spinner" class="position-absolute top-0 start-0 w-100 h-100 rounded-circle spinning-audio-disc opacity-0" style="border: 2px dashed rgba(255,255,255,0.6); pointer-events: none; transition: opacity 0.3s;"></span>
            </div>
            <div class="overflow-hidden">
                <h6 id="v-audio-track-title" class="fw-bold text-white text-truncate mb-0 fs-14" style="letter-spacing: -0.2px;">{{ __('messages.continuous_audio_player') }}</h6>
                <small id="v-audio-track-publisher" class="text-white-50 text-truncate d-block fs-12">MYADS Audio</small>
            </div>
        </div>

        <!-- Player Controls & Progress -->
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <button type="button" id="v-audio-btn-prev" class="btn btn-link text-white-50 p-1 shadow-none" style="font-size: 16px;">
                <i class="fa-solid fa-backward-step"></i>
            </button>
            <button type="button" id="v-audio-btn-toggle" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center p-0 shadow" style="width: 38px; height: 38px; background: #615dfa; border: none;">
                <i id="v-audio-toggle-icon" class="fa-solid fa-play fs-14" style="margin-left: 2px;"></i>
            </button>
            <button type="button" id="v-audio-btn-next" class="btn btn-link text-white-50 p-1 shadow-none" style="font-size: 16px;">
                <i class="fa-solid fa-forward-step"></i>
            </button>
            <button type="button" id="v-audio-btn-close" class="btn btn-link text-white-50 p-1 shadow-none ms-1" style="font-size: 14px;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <!-- Scrubber Progress Bar -->
    <div class="w-100 mt-2 d-flex align-items-center gap-2">
        <span id="v-audio-current-time" class="fs-11 text-white-50 font-monospace" style="min-width: 32px;">0:00</span>
        <div class="flex-grow-1 position-relative" style="height: 4px; background: rgba(255,255,255,0.15); border-radius: 4px; cursor: pointer;" id="v-audio-scrubber-track">
            <div id="v-audio-progress-bar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #615dfa, #23d2e2); border-radius: 4px; transition: width 0.1s linear;"></div>
        </div>
        <span id="v-audio-duration-time" class="fs-11 text-white-50 font-monospace" style="min-width: 32px;">0:00</span>
    </div>
</div>

<script>
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        var playerBar = document.getElementById('v-continuous-audio-bar');
        var audioEl = document.getElementById('v-continuous-audio-element');
        var btnToggle = document.getElementById('v-audio-btn-toggle');
        var toggleIcon = document.getElementById('v-audio-toggle-icon');
        var btnClose = document.getElementById('v-audio-btn-close');
        var titleEl = document.getElementById('v-audio-track-title');
        var publisherEl = document.getElementById('v-audio-track-publisher');
        var avatarEl = document.getElementById('v-audio-track-avatar');
        var spinnerEl = document.getElementById('v-audio-disc-spinner');
        var currentTimeEl = document.getElementById('v-audio-current-time');
        var durationTimeEl = document.getElementById('v-audio-duration-time');
        var progressBar = document.getElementById('v-audio-progress-bar');
        var scrubberTrack = document.getElementById('v-audio-scrubber-track');

        if (!playerBar || !audioEl) return;

        function formatTime(secs) {
            if (isNaN(secs) || secs < 0) return '0:00';
            var m = Math.floor(secs / 60);
            var s = Math.floor(secs % 60);
            return m + ':' + (s < 10 ? '0' : '') + s;
        }

        window.playContinuousAudio = function(url, title, publisher, avatar) {
            if (!url) return;

            titleEl.textContent = title || 'Audio Post';
            publisherEl.textContent = publisher || 'MYADS Community';
            if (avatar) avatarEl.src = avatar;
            
            audioEl.src = url;
            playerBar.classList.remove('d-none');
            
            sessionStorage.setItem('v_audio_url', url);
            sessionStorage.setItem('v_audio_title', title || '');
            sessionStorage.setItem('v_audio_publisher', publisher || '');
            sessionStorage.setItem('v_audio_avatar', avatar || '');
            sessionStorage.setItem('v_audio_playing', 'true');

            audioEl.play().catch(function(e) {
                console.warn('Audio play error:', e);
            });
        };

        // Resume audio state across page navigations
        var savedUrl = sessionStorage.getItem('v_audio_url');
        var savedTitle = sessionStorage.getItem('v_audio_title');
        var savedPublisher = sessionStorage.getItem('v_audio_publisher');
        var savedAvatar = sessionStorage.getItem('v_audio_avatar');
        var savedTime = parseFloat(sessionStorage.getItem('v_audio_time') || 0);
        var wasPlaying = sessionStorage.getItem('v_audio_playing') === 'true';

        if (savedUrl && wasPlaying) {
            titleEl.textContent = savedTitle || 'Audio Post';
            publisherEl.textContent = savedPublisher || 'MYADS Community';
            if (savedAvatar) avatarEl.src = savedAvatar;
            audioEl.src = savedUrl;
            audioEl.currentTime = savedTime;
            playerBar.classList.remove('d-none');
            audioEl.play().catch(function() {});
        }

        // Toggle Play/Pause
        if (btnToggle) {
            btnToggle.addEventListener('click', function() {
                if (audioEl.paused) {
                    audioEl.play();
                } else {
                    audioEl.pause();
                }
            });
        }

        // Close Player
        if (btnClose) {
            btnClose.addEventListener('click', function() {
                audioEl.pause();
                playerBar.classList.add('d-none');
                sessionStorage.removeItem('v_audio_url');
                sessionStorage.removeItem('v_audio_playing');
            });
        }

        // Audio Events
        audioEl.addEventListener('play', function() {
            toggleIcon.className = 'fa-solid fa-pause fs-14';
            toggleIcon.style.marginLeft = '0px';
            if (spinnerEl) spinnerEl.classList.remove('opacity-0');
            sessionStorage.setItem('v_audio_playing', 'true');
        });

        audioEl.addEventListener('pause', function() {
            toggleIcon.className = 'fa-solid fa-play fs-14';
            toggleIcon.style.marginLeft = '2px';
            if (spinnerEl) spinnerEl.classList.add('opacity-0');
            sessionStorage.setItem('v_audio_playing', 'false');
        });

        audioEl.addEventListener('timeupdate', function() {
            if (currentTimeEl) currentTimeEl.textContent = formatTime(audioEl.currentTime);
            if (durationTimeEl) durationTimeEl.textContent = formatTime(audioEl.duration);
            if (progressBar && audioEl.duration) {
                var pct = (audioEl.currentTime / audioEl.duration) * 100;
                progressBar.style.width = pct + '%';
            }
            sessionStorage.setItem('v_audio_time', audioEl.currentTime);
        });

        if (scrubberTrack) {
            scrubberTrack.addEventListener('click', function(e) {
                var rect = scrubberTrack.getBoundingClientRect();
                var clickX = e.clientX - rect.left;
                var pct = clickX / rect.width;
                if (audioEl.duration) {
                    audioEl.currentTime = pct * audioEl.duration;
                }
            });
        }
    });
})();
</script>
