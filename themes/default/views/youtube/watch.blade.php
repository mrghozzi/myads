@extends('theme::layouts.master')

@section('title', __('Watch Video'))

@section('content')
<div class="section-banner" style="background: url({{ theme_asset('img/banner/Newsfeed.png') }}) no-repeat 50%; background-size: cover;">
    <img class="section-banner-icon" src="{{ theme_asset('img/banner/newsfeed-icon.png') }}" alt="overview-icon">
    <p class="section-banner-title">{{ __('Watch Video') }}</p>
    <p class="section-banner-text">{{ __('Watch & Earn') }}</p>
</div>

<div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 28px; margin-bottom: 12px;">
    <a href="{{ route('youtube.exchange.index') }}" class="button tertiary" id="back-btn"><i class="fa fa-arrow-left"></i> {{ __('Return to Exchange') }}</a>
</div>

<!-- Error or Success Alert Container -->
<div id="alert-container"></div>

<div class="widget-box" style="padding: 28px; text-align: center;">
    <h4 style="margin-top: 0; margin-bottom: 24px; font-weight: 700; color: #3e3f5e;">
        {{ __('Watch & Earn') }} 
        <span style="display: inline-block; padding: 4px 10px; background: rgba(16, 185, 129, 0.1); color: #10b981; border-radius: 20px; font-size: 0.8rem; font-weight: 700; vertical-align: middle; margin-left: 10px;">
            {{ $video->reward_points }} PTS
        </span>
    </h4>

    <div style="border-radius: 16px; overflow: hidden; background: #000; position: relative; padding-top: 56.25%; margin-bottom: 24px; width: 100%; max-width: 800px; margin-left: auto; margin-right: auto;">
        <div id="youtube-player" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></div>
    </div>

    <!-- Timer and Control UI -->
    <div style="max-width: 600px; margin: 0 auto;">
        <div id="status-message" style="color: #8f91ac; margin-bottom: 16px; font-size: 1.1rem; font-weight: 600;">
            {{ __('Click play to start the timer.') }}
        </div>
        
        <div style="background: #f1f1f5; border-radius: 12px; height: 24px; overflow: hidden; margin-bottom: 24px; position: relative;">
            <div id="progress-bar" style="background: #615dfa; width: 0%; height: 100%; transition: width 1s linear;"></div>
            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 0.85rem; text-shadow: 0 1px 2px rgba(0,0,0,0.2); pointer-events: none;">
                <span id="timer-text" style="margin-right: 2px;">{{ $video->duration_required }}</span>s
            </div>
        </div>

        <button id="claim-btn" class="button primary" style="width: 100%; padding: 14px; font-size: 1rem; border-radius: 12px; display: none; margin: 0 auto;" onclick="claimReward()">
            <i class="fas fa-gift" style="margin-right: 8px;"></i> {{ __('Claim Reward') }}
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const requiredDuration = {{ $video->duration_required }};
    const verificationToken = '{{ $token }}';
    const verifyUrl = '{{ route('youtube.exchange.verify') }}';
    const csrfToken = '{{ csrf_token() }}';
    
    let player;
    let timerInterval;
    let timeWatched = 0;
    let isCompleted = false;

    // Load YouTube IFrame API
    let tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    let firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    function onYouTubeIframeAPIReady() {
        player = new YT.Player('youtube-player', {
            videoId: '{{ $video->youtube_id }}',
            playerVars: {
                'controls': 0,
                'disablekb': 1,
                'fs': 0,
                'rel': 0,
                'modestbranding': 1,
                'playsinline': 1,
                'iv_load_policy': 3
            },
            events: {
                'onStateChange': onPlayerStateChange
            }
        });
    }

    function onPlayerStateChange(event) {
        if (isCompleted) return;

        if (event.data == YT.PlayerState.PLAYING) {
            startTimer();
            document.getElementById('status-message').innerText = "{{ __('Watching...') }}";
            document.getElementById('status-message').style.color = '#615dfa';
        } else {
            stopTimer();
            if (event.data == YT.PlayerState.PAUSED) {
                document.getElementById('status-message').innerText = "{{ __('Paused. Resume to continue.') }}";
                document.getElementById('status-message').style.color = '#ffb800';
            }
        }
    }

    function startTimer() {
        clearInterval(timerInterval);
        timerInterval = setInterval(() => {
            timeWatched++;
            updateUI();

            if (timeWatched >= requiredDuration) {
                completeView();
            }
        }, 1000);
    }

    function stopTimer() {
        clearInterval(timerInterval);
    }

    function updateUI() {
        let remaining = requiredDuration - timeWatched;
        if (remaining < 0) remaining = 0;
        
        let percent = (timeWatched / requiredDuration) * 100;
        document.getElementById('progress-bar').style.width = percent + '%';
        document.getElementById('timer-text').innerText = remaining;
    }

    function completeView() {
        isCompleted = true;
        stopTimer();
        player.pauseVideo();
        
        document.getElementById('status-message').innerText = "{{ __('Requirement met! You can now claim your reward.') }}";
        document.getElementById('status-message').style.color = '#10b981';
        
        document.getElementById('progress-bar').style.background = '#10b981';
        
        document.getElementById('claim-btn').style.display = 'block';
    }

    // Page Visibility API to prevent background watching
    document.addEventListener("visibilitychange", () => {
        if (document.hidden && player && typeof player.pauseVideo === 'function' && !isCompleted) {
            player.pauseVideo();
            stopTimer();
        }
    });

    setInterval(() => {
        if (!isCompleted && player && player.getPlayerState() === YT.PlayerState.PLAYING) {
            // Basic check placeholder
        }
    }, 2000);

    function claimReward() {
        let btn = document.getElementById('claim-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i> {{ __('Verifying...') }}';

        fetch(verifyUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ token: verificationToken })
        })
        .then(response => response.json())
        .then(data => {
            let container = document.getElementById('alert-container');
            if (data.success) {
                container.innerHTML = `<div style="padding: 16px; background: #d1fae5; color: #065f46; border-radius: 12px; margin-bottom: 24px; font-weight: 600;"><i class="fas fa-check-circle" style="margin-right: 8px;"></i> ${data.message}</div>`;
                btn.style.display = 'none';
            } else {
                container.innerHTML = `<div style="padding: 16px; background: #fee2e2; color: #991b1b; border-radius: 12px; margin-bottom: 24px; font-weight: 600;"><i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i> ${data.message}</div>`;
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-gift" style="margin-right: 8px;"></i> {{ __('Claim Reward') }}';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('alert-container').innerHTML = `<div style="padding: 16px; background: #fee2e2; color: #991b1b; border-radius: 12px; margin-bottom: 24px; font-weight: 600;"><i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i> {{ __('Network error occurred.') }}</div>`;
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-gift" style="margin-right: 8px;"></i> {{ __('Claim Reward') }}';
        });
    }
</script>
@endpush
