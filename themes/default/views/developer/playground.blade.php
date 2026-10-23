@extends('theme::layouts.master')

@section('title', __('messages.api_playground') ?? 'Interactive API Playground')

@push('head')
    @include('theme::developer.partials.styles')
@endpush

@section('content')
<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-terminal" style="font-size: 26px; color: #fff;"></i>
    </div>
    <p class="section-banner-title">{{ __('messages.api_playground') ?? 'Interactive API Playground' }}</p>
    <p class="section-banner-text">Test API v1 endpoints live in your browser with real responses, status codes, and latency analysis.</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <div class="dev-side-stack">
            @include('theme::developer.partials.nav', ['active' => 'playground'])
            @include('theme::developer.partials.platform_rules')
        </div>
    </div>

    <div class="grid-column">
        <div class="widget-box dev-panel" style="margin-bottom: 24px;">
            <p class="widget-box-title">Request Console</p>
            <div class="widget-box-content" style="padding: 26px;">
                <div style="display: grid; gap: 16px;">
                    <!-- Authentication Token -->
                    <div class="dev-field">
                        <label for="pg_token"><strong>Authorization (Bearer Token)</strong> <span class="text-danger">*</span></label>
                        <div style="display: flex; gap: 8px;">
                            <input id="pg_token" type="text" class="form-control dev-control" placeholder="Enter Bearer Token (PAT or OAuth Access Token)" style="font-family: monospace;">
                            @if($tokens->isNotEmpty())
                                <select id="pg_token_select" class="form-control dev-control" style="max-width: 200px;">
                                    <option value="">-- Quick PAT --</option>
                                    @foreach($tokens as $t)
                                        <option value="{{ $t->name }}">PAT: {{ $t->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </div>

                    <!-- Endpoint Selector -->
                    <div class="dev-field">
                        <label for="pg_endpoint"><strong>Endpoint Selection</strong></label>
                        <select id="pg_endpoint" class="form-control dev-control" style="font-weight: 600;">
                            <option value="GET|/api/developer/v1/me">GET /api/developer/v1/me (Identity)</option>
                            <option value="GET|/api/developer/v1/me/profile">GET /api/developer/v1/me/profile (Profile)</option>
                            <option value="GET|/api/developer/v1/me/content">GET /api/developer/v1/me/content (Posts)</option>
                            <option value="POST|/api/developer/v1/me/content">POST /api/developer/v1/me/content (Publish Post)</option>
                            <option value="GET|/api/developer/v1/me/messages">GET /api/developer/v1/me/messages (Messages)</option>
                            <option value="GET|/api/developer/v1/me/notifications">GET /api/developer/v1/me/notifications (Notifications)</option>
                            <option value="GET|/api/developer/v1/me/wallet">GET /api/developer/v1/me/wallet (PTS Points)</option>
                            <option value="GET|/api/developer/v1/me/badges">GET /api/developer/v1/me/badges (Badges)</option>
                            <option value="GET|/api/developer/v1/forums">GET /api/developer/v1/forums (Forums)</option>
                            <option value="GET|/api/developer/v1/store/products">GET /api/developer/v1/store/products (Digital Store)</option>
                            <option value="GET|/api/developer/v1/me/orders">GET /api/developer/v1/me/orders (Orders)</option>
                            <option value="GET|/api/developer/v1/me/ads/stats">GET /api/developer/v1/me/ads/stats (Advertising)</option>
                        </select>
                    </div>

                    <!-- Payload Box for POST -->
                    <div id="pg_body_group" class="dev-field" style="display: none;">
                        <label for="pg_body"><strong>JSON Request Body</strong></label>
                        <textarea id="pg_body" rows="4" class="form-control dev-control" style="font-family: monospace; font-size: 13px;">{"content": "Hello from the interactive API Playground!"}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="button" id="btn-send-pg" class="button primary" style="padding: 0 24px;">
                            <i class="fa fa-play me-1"></i> Send Request
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Response Viewer -->
        <div class="widget-box dev-panel">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; border-bottom: 1px solid var(--dev-border);">
                <p class="widget-box-title" style="margin: 0; padding: 0;">Response Output</p>
                <div id="pg_meta_badges" style="display: flex; gap: 8px;">
                    <span id="pg_status_badge" style="display: none; padding: 2px 8px; border-radius: 4px; font-weight: 700; font-size: 12px;"></span>
                    <span id="pg_time_badge" style="display: none; padding: 2px 8px; border-radius: 4px; font-size: 12px; background: rgba(255,255,255,0.06); color: var(--dev-muted);"></span>
                </div>
            </div>
            <div class="widget-box-content" style="padding: 0;">
                <pre id="pg_response_pre" style="margin: 0; padding: 24px; background: var(--dev-code-bg); color: var(--dev-code-text); font-family: monospace; font-size: 13px; max-height: 480px; overflow: auto; border-radius: 0 0 12px 12px;">// Click "Send Request" to inspect the JSON response...</pre>
            </div>
        </div>
    </div>

    <div class="grid-column"></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var endpointSelect = document.getElementById('pg_endpoint');
    var bodyGroup = document.getElementById('pg_body_group');
    var sendBtn = document.getElementById('btn-send-pg');
    var responsePre = document.getElementById('pg_response_pre');
    var statusBadge = document.getElementById('pg_status_badge');
    var timeBadge = document.getElementById('pg_time_badge');
    var tokenInput = document.getElementById('pg_token');

    endpointSelect.addEventListener('change', function() {
        var parts = this.value.split('|');
        var method = parts[0];
        bodyGroup.style.display = (method === 'POST') ? 'block' : 'none';
    });

    sendBtn.addEventListener('click', function() {
        var token = tokenInput.value.trim();
        if (!token) {
            alert('Please provide a Bearer Token (or Personal Access Token)');
            tokenInput.focus();
            return;
        }

        var parts = endpointSelect.value.split('|');
        var method = parts[0];
        var path = parts[1];
        var url = '{{ url("") }}' + path;

        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Executing...';
        responsePre.textContent = 'Sending request to ' + path + '...';

        var startTime = performance.now();
        var options = {
            method: method,
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        };

        if (method === 'POST') {
            options.body = document.getElementById('pg_body').value;
        }

        fetch(url, options)
            .then(function(res) {
                var duration = Math.round(performance.now() - startTime);
                timeBadge.textContent = duration + ' ms';
                timeBadge.style.display = 'inline-block';

                statusBadge.textContent = res.status + ' ' + res.statusText;
                statusBadge.style.display = 'inline-block';
                if (res.ok) {
                    statusBadge.style.background = 'rgba(31, 157, 98, 0.15)';
                    statusBadge.style.color = '#1f9d62';
                } else {
                    statusBadge.style.background = 'rgba(217, 75, 99, 0.15)';
                    statusBadge.style.color = '#d94b63';
                }

                return res.json().then(function(json) {
                    return { status: res.status, data: json };
                }).catch(function() {
                    return res.text().then(function(text) {
                        return { status: res.status, data: text };
                    });
                });
            })
            .then(function(result) {
                responsePre.textContent = JSON.stringify(result.data, null, 2);
            })
            .catch(function(err) {
                statusBadge.textContent = 'ERROR';
                statusBadge.style.background = 'rgba(217, 75, 99, 0.15)';
                statusBadge.style.color = '#d94b63';
                statusBadge.style.display = 'inline-block';
                responsePre.textContent = 'Network or Client Error: ' + err.message;
            })
            .finally(function() {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="fa fa-play me-1"></i> Send Request';
            });
    });
});
</script>
@endpush
@endsection
