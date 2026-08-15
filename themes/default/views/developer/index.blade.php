@extends('theme::layouts.master')

@section('title', __('messages.dev_platform'))

@push('head')
    @include('theme::developer.partials.styles')
@endpush

@section('content')
@php
    $developerApps = collect($apps ?? []);
    $activeAppsCount = $developerApps->where('status', 'active')->count();
    $pendingAppsCount = $developerApps->where('status', 'pending_review')->count();
    $scopeCount = count($scopes ?? []);
@endphp

<div class="section-banner">
    <div class="section-banner-icon" style="display: flex; align-items: center; justify-content: center;">
        <i class="fa fa-code-branch" style="font-size: 26px; color: #fff;"></i>
    </div>
    <p class="section-banner-title">{{ __('messages.dev_platform') }}</p>
    <p class="section-banner-text">{{ __('messages.dev_platform_desc', ['site' => $site_settings->titer ?? 'MYADS']) }}</p>
</div>

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        <div class="dev-side-stack">
            @include('theme::developer.partials.nav', ['active' => 'overview'])

            <div class="widget-box dev-panel">
                <p class="widget-box-title">{{ auth()->check() && $eligible ? __('messages.applications') : __('messages.platform_info') }}</p>
                <div class="widget-box-content" style="padding: 28px;">
                    @if(auth()->check() && $eligible)
                        <div class="dev-stat-grid dev-stat-grid--compact">
                            <div class="dev-stat-card">
                                <span>{{ __('messages.total_apps') }}</span>
                                <strong>{{ $developerApps->count() }}</strong>
                            </div>
                            <div class="dev-stat-card">
                                <span>{{ __('messages.active_apps') }}</span>
                                <strong>{{ $activeAppsCount }}</strong>
                            </div>
                            <div class="dev-stat-card">
                                <span>{{ __('messages.pending_review') }}</span>
                                <strong>{{ $pendingAppsCount }}</strong>
                            </div>
                        </div>
                    @else
                        <p class="dev-card-copy">{{ __('messages.v1_api_desc') }}</p>
                        <div class="dev-chip-row" style="margin-top: 18px;">
                            <span class="dev-chip">
                                <i class="fa fa-plug"></i>
                                {{ __('messages.v1_api') }}
                            </span>
                            <span class="dev-chip">
                                <i class="fa fa-shield-halved"></i>
                                {{ __('messages.oauth_secured') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            @include('theme::developer.partials.platform_rules')
        </div>
    </div>

    <div class="grid-column">
        @if(session('error'))
            <div class="alert alert-danger" role="alert" style="margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <div class="dev-shell">
            <!-- Hero Header Box -->
            <div class="widget-box dev-panel">
                <div class="widget-box-content" style="padding: 30px;">
                    <div class="dev-surface-header">
                        <div>
                            <p class="dev-kicker">{{ __('messages.dev_platform') }}</p>
                            <h2 class="dev-section-title">{{ __('messages.dev_docs') }}</h2>
                            <p class="dev-summary-copy" style="margin-top: 8px;">{{ __('messages.dev_features_subtitle') }}</p>
                        </div>

                        <div class="dev-inline-actions">
                            @auth
                                @if($eligible)
                                    <a href="{{ $developerApps->isEmpty() ? route('developer.apps.create') : route('developer.apps.index') }}" class="button primary">
                                        <i class="fa {{ $developerApps->isEmpty() ? 'fa-plus' : 'fa-cubes' }}" style="margin-inline-end: 6px;"></i>
                                        {{ $developerApps->isEmpty() ? __('messages.create_app') : __('messages.manage_apps') }}
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="button primary">{{ __('messages.login') }}</a>
                            @endauth
                            <a href="{{ route('developer.guides') }}" class="button secondary">
                                <i class="fa fa-book-open" style="margin-inline-end: 6px;"></i>
                                {{ __('messages.dev_explore_guides') }}
                            </a>
                        </div>
                    </div>

                    <div class="dev-chip-row" style="margin-top: 20px;">
                        <span class="dev-chip">
                            <i class="fa fa-shield-halved" style="color: var(--dev-accent);"></i>
                            OAuth 2.0 Flow
                        </span>
                        <span class="dev-chip">
                            <i class="fa fa-bolt" style="color: var(--dev-warning);"></i>
                            REST API v1 (20+ Endpoints)
                        </span>
                        <span class="dev-chip">
                            <i class="fa fa-layer-group" style="color: var(--dev-accent-alt);"></i>
                            3 Ready Widgets
                        </span>
                        <span class="dev-chip">
                            <i class="fa fa-tachometer-alt" style="color: var(--dev-success);"></i>
                            30 req/min Rate Limit
                        </span>
                    </div>
                </div>
            </div>

            <!-- 4 Feature Pillars Grid -->
            <div class="dev-hero-feature-grid">
                <article class="dev-hero-feature-card">
                    <div class="dev-hero-feature-icon">
                        <i class="fa fa-user-shield"></i>
                    </div>
                    <h3 class="dev-hero-feature-title">OAuth 2.0 Authorization</h3>
                    <p class="dev-hero-feature-desc">{{ __('messages.dev_oauth_desc', ['site' => $site_settings->titer ?? 'MYADS']) }}</p>
                    <div style="margin-top: auto; padding-top: 10px;">
                        <span class="dev-scope-badge">
                            <i class="fa fa-key"></i> Authorization Code Flow
                        </span>
                    </div>
                </article>

                <article class="dev-hero-feature-card">
                    <div class="dev-hero-feature-icon" style="color: var(--dev-accent-alt);">
                        <i class="fa fa-cubes-stacked"></i>
                    </div>
                    <h3 class="dev-hero-feature-title">Developer API v1</h3>
                    <p class="dev-hero-feature-desc">{{ __('messages.dev_api_endpoints_desc') }}</p>
                    <div style="margin-top: auto; padding-top: 10px;">
                        <span class="dev-scope-badge">
                            <i class="fa fa-network-wired"></i> 20+ Endpoints
                        </span>
                    </div>
                </article>

                <article class="dev-hero-feature-card">
                    <div class="dev-hero-feature-icon" style="color: var(--dev-warning);">
                        <i class="fa fa-wand-magic-sparkles"></i>
                    </div>
                    <h3 class="dev-hero-feature-title">{{ __('messages.dev_widgets_integration') }}</h3>
                    <p class="dev-hero-feature-desc">{{ __('messages.dev_widgets_integration_desc') }}</p>
                    <div style="margin-top: auto; padding-top: 10px;">
                        <span class="dev-scope-badge">
                            <i class="fa fa-code"></i> Follow / Profile / Content
                        </span>
                    </div>
                </article>

                <article class="dev-hero-feature-card">
                    <div class="dev-hero-feature-icon" style="color: var(--dev-success);">
                        <i class="fa fa-share-nodes"></i>
                    </div>
                    <h3 class="dev-hero-feature-title">{{ __('messages.dev_share_api_title') }}</h3>
                    <p class="dev-hero-feature-desc">{{ __('messages.dev_share_api_desc') }}</p>
                    <div style="margin-top: auto; padding-top: 10px;">
                        <span class="dev-scope-badge">
                            <i class="fa fa-globe"></i> GET /share
                        </span>
                    </div>
                </article>
            </div>

            <!-- Interactive Quickstart Code Sandbox -->
            <div class="widget-box dev-panel">
                <div class="widget-box-content" style="padding: 28px;">
                    <div class="dev-surface-header" style="margin-bottom: 16px;">
                        <div>
                            <p class="dev-kicker">Quickstart Example</p>
                            <h3 class="dev-card-title">Token Exchange &amp; Fetch Profile</h3>
                        </div>
                        <span class="dev-mini-chip">API v1 Ready</span>
                    </div>

                    <div class="dev-lang-tabs">
                        <div class="dev-lang-tab is-active" data-lang="php">PHP (cURL)</div>
                        <div class="dev-lang-tab" data-lang="node">Node.js (Axios)</div>
                        <div class="dev-lang-tab" data-lang="python">Python (Requests)</div>
                        <div class="dev-lang-tab" data-lang="curl">cURL CLI</div>
                    </div>

                    <div class="dev-code-block dev-code-container">
                        <div class="js-lang-content" data-lang="php">
                            <div class="dev-code-toolbar">
                                <span>PHP (cURL)</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy-id="home-code-php">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
<pre><code id="home-code-php">&lt;?php
// 1. Exchange authorization code for token
$ch = curl_init('{{ url('/oauth/token') }}');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'grant_type'    => 'authorization_code',
    'client_id'     => 'YOUR_CLIENT_ID',
    'client_secret' => 'YOUR_CLIENT_SECRET',
    'redirect_uri'  => 'https://yourapp.com/callback',
    'code'          => $_GET['code']
]);
$res = json_decode(curl_exec($ch), true);
$token = $res['access_token'];

// 2. Fetch authenticated member profile via Developer API v1
$ch = curl_init('{{ url('/api/developer/v1/me/profile') }}');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);
$profile = json_decode(curl_exec($ch), true);
print_r($profile['data']);
?&gt;</code></pre>
                        </div>

                        <div class="js-lang-content d-none" data-lang="node">
                            <div class="dev-code-toolbar">
                                <span>Node.js (Axios)</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy-id="home-code-node">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
<pre><code id="home-code-node">const axios = require('axios');

async function getProfile(code) {
    // 1. Exchange code for access token
    const tokenRes = await axios.post('{{ url('/oauth/token') }}', {
        grant_type: 'authorization_code',
        client_id: 'YOUR_CLIENT_ID',
        client_secret: 'YOUR_CLIENT_SECRET',
        redirect_uri: 'https://yourapp.com/callback',
        code: code
    });

    const accessToken = tokenRes.data.access_token;

    // 2. Access Developer API v1 endpoint
    const profileRes = await axios.get('{{ url('/api/developer/v1/me/profile') }}', {
        headers: {
            'Authorization': `Bearer ${accessToken}`,
            'Accept': 'application/json'
        }
    });

    return profileRes.data.data;
}</code></pre>
                        </div>

                        <div class="js-lang-content d-none" data-lang="python">
                            <div class="dev-code-toolbar">
                                <span>Python (Requests)</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy-id="home-code-python">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
<pre><code id="home-code-python">import requests

# 1. Exchange authorization code for token
token_res = requests.post('{{ url('/oauth/token') }}', data={
    'grant_type': 'authorization_code',
    'client_id': 'YOUR_CLIENT_ID',
    'client_secret': 'YOUR_CLIENT_SECRET',
    'redirect_uri': 'https://yourapp.com/callback',
    'code': auth_code
})
access_token = token_res.json().get('access_token')

# 2. Fetch profile from Developer API v1
profile_res = requests.get('{{ url('/api/developer/v1/me/profile') }}', headers={
    'Authorization': f'Bearer {access_token}',
    'Accept': 'application/json'
})
print(profile_res.json().get('data'))</code></pre>
                        </div>

                        <div class="js-lang-content d-none" data-lang="curl">
                            <div class="dev-code-toolbar">
                                <span>cURL</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy-id="home-code-curl">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
<pre><code id="home-code-curl"># 1. Exchange code for access token
curl -X POST {{ url('/oauth/token') }} \
     -d "grant_type=authorization_code" \
     -d "client_id=YOUR_CLIENT_ID" \
     -d "client_secret=YOUR_CLIENT_SECRET" \
     -d "code=AUTHORIZATION_CODE" \
     -d "redirect_uri=https://yourapp.com/callback"

# 2. Call Developer API v1 with Bearer token
curl -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
     -H "Accept: application/json" \
     {{ url('/api/developer/v1/me/profile') }}</code></pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scopes Catalog Overview -->
            <div class="widget-box dev-panel">
                <div class="widget-box-content" style="padding: 28px;">
                    <div class="dev-surface-header" style="margin-bottom: 16px;">
                        <div>
                            <p class="dev-kicker">{{ __('messages.dev_scopes_catalog') }}</p>
                            <h3 class="dev-card-title">{{ __('messages.dev_scopes_catalog') }} ({{ $scopeCount }} Scopes)</h3>
                            <p class="dev-card-copy" style="margin-top: 6px;">{{ __('messages.dev_scopes_catalog_desc') }}</p>
                        </div>
                        <a href="{{ route('developer.guides') }}#scopes-catalog" class="button secondary" style="font-size: 0.84rem;">
                            {{ __('messages.dev_view_catalog') }}
                        </a>
                    </div>

                    <div class="dev-chip-row" style="margin-top: 14px;">
                        @foreach($categories ?? [] as $catKey => $catData)
                            <span class="dev-chip">
                                <i class="fa {{ $catData['icon'] ?? 'fa-tag' }}"></i>
                                {{ __($catData['title']) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-column">
        @include('theme::developer.partials.account_state', ['apps' => $developerApps])
    </div>
</div>
@endsection

@push('scripts')
    @include('theme::developer.partials.scripts')
    <script>
        document.querySelectorAll('.dev-lang-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                const lang = tab.getAttribute('data-lang');
                
                document.querySelectorAll('.dev-lang-tab').forEach(t => t.classList.remove('is-active'));
                tab.classList.add('is-active');
                
                document.querySelectorAll('.js-lang-content').forEach(content => {
                    if (content.getAttribute('data-lang') === lang) {
                        content.classList.remove('d-none');
                    } else {
                        content.classList.add('d-none');
                    }
                });
            });
        });
    </script>
@endpush
