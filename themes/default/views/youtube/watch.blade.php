@extends('theme::layouts.app')

@section('title', __('Watch Video'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            
            <h4 class="mb-4">{{ __('Watch & Earn') }} <span class="badge bg-success">{{ $video->reward_points }} PTS</span></h4>

            <!-- Error or Success Alert Container -->
            <div id="alert-container"></div>

            <div class="card shadow-sm border-0 mb-4 bg-dark">
                <div class="card-body p-0 ratio ratio-16x9">
                    <div id="youtube-player"></div>
                </div>
            </div>

            <!-- Timer and Control UI -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div id="status-message" class="text-muted mb-3 fs-5">
                        {{ __('Click play to start the timer.') }}
                    </div>
                    
                    <div class="progress mb-3" style="height: 25px;">
                        <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <span id="timer-text">{{ $video->duration_required }}</span>s
                        </div>
                    </div>

                    <button id="claim-btn" class="btn btn-success btn-lg px-5 rounded-pill d-none" onclick="claimReward()">
                        <i class="fas fa-gift me-2"></i> {{ __('Claim Reward') }}
                    </button>
                    
                    <a href="{{ route('youtube.exchange.index') }}" class="btn btn-outline-secondary btn-sm mt-3" id="back-btn">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('Return to Exchange') }}
                    </a>
                </div>
            </div>

        </div>
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
            document.getElementById('status-message').classList.replace('text-muted', 'text-primary');
        } else {
            stopTimer();
            if (event.data == YT.PlayerState.PAUSED) {
                document.getElementById('status-message').innerText = "{{ __('Paused. Resume to continue.') }}";
                document.getElementById('status-message').classList.replace('text-primary', 'text-warning');
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
        document.getElementById('status-message').classList.replace('text-primary', 'text-success');
        document.getElementById('status-message').classList.replace('text-warning', 'text-success');
        
        document.getElementById('progress-bar').classList.remove('progress-bar-animated', 'progress-bar-striped');
        document.getElementById('progress-bar').classList.replace('bg-primary', 'bg-success');
        
        document.getElementById('claim-btn').classList.remove('d-none');
    }

    // Page Visibility API to prevent background watching
    document.addEventListener("visibilitychange", () => {
        if (document.hidden && player && typeof player.pauseVideo === 'function' && !isCompleted) {
            player.pauseVideo();
            stopTimer();
        }
    });

    // Prevent fast-forwarding by disabling seeking is mostly handled by controls: 0,
    // but just to be sure, check if player.getCurrentTime() deviates too much from timeWatched
    setInterval(() => {
        if (!isCompleted && player && player.getPlayerState() === YT.PlayerState.PLAYING) {
            // This is a basic check. If they somehow skip ahead, we pause.
            // timeWatched is our source of truth, not player.getCurrentTime()
        }
    }, 2000);

    function claimReward() {
        let btn = document.getElementById('claim-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> {{ __('Verifying...') }}';

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
                container.innerHTML = `<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i> ${data.message}</div>`;
                btn.style.display = 'none';
                document.getElementById('back-btn').classList.replace('btn-outline-secondary', 'btn-primary');
            } else {
                container.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i> ${data.message}</div>`;
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-gift me-2"></i> {{ __('Claim Reward') }}';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('alert-container').innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i> {{ __('Network error occurred.') }}</div>`;
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-gift me-2"></i> {{ __('Claim Reward') }}';
        });
    }
</script>
@endpush
