@extends('theme::layouts.master')

@section('title', __('messages.dev_guides'))

@push('head')
    @include('theme::developer.partials.styles')
    <style>
        .dev-guide-container {
            display: grid;
            gap: 28px;
        }
        .dev-guide-header {
            padding: 40px;
            border-radius: 24px;
            background: var(--dev-surface-accent);
            border: 1px solid var(--dev-border);
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        .dev-guide-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, var(--dev-accent) 0%, transparent 70%);
            opacity: 0.1;
            filter: blur(40px);
        }
        .dev-toc-card {
            position: sticky;
            top: 20px;
        }
        .dev-toc-link {
            display: block;
            padding: 10px 16px;
            color: var(--dev-text);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.86rem;
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .dev-toc-link:hover {
            background: rgba(97, 93, 250, 0.06);
            color: var(--dev-accent);
            padding-inline-start: 20px;
            text-decoration: none;
        }
        .dev-toc-link.is-active {
            background: var(--dev-surface-accent);
            color: var(--dev-accent);
        }
        .dev-guide-section {
            padding: 34px;
            margin-bottom: 24px;
        }
        .dev-step-badge {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--dev-accent);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
            margin-bottom: 16px;
        }
        .dev-lang-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: -1px;
            position: relative;
            z-index: 2;
            overflow-x: auto;
            padding-bottom: 4px;
        }
        .dev-lang-tab {
            padding: 10px 18px;
            background: var(--dev-surface-soft);
            border: 1px solid var(--dev-border);
            border-bottom: 0;
            border-radius: 14px 14px 0 0;
            color: var(--dev-muted);
            font-weight: 700;
            font-size: 0.8rem;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
        }
        .dev-lang-tab:hover {
            color: var(--dev-title);
            background: var(--dev-surface);
        }
        .dev-lang-tab.is-active {
            background: var(--dev-code-bg);
            border-color: var(--dev-code-border);
            color: #fff;
        }
        .dev-code-container {
            border-radius: 0 18px 18px 18px;
            margin-top: 0;
        }
    </style>
@endpush

@section('content')
<div class="dev-guide-header">
    <p class="dev-kicker">{{ __('messages.dev_integration_guide') }}</p>
    <h1 class="dev-title" style="font-size: 2.1rem; margin-bottom: 12px;">{{ __('messages.dev_guides') }}</h1>
    <p class="dev-summary-copy" style="max-width: 780px;">{{ __('messages.dev_guides_intro', ['site' => $site_settings->titer ?? 'MYADS']) }}</p>
</div>

<div class="grid grid-3-9 mobile-prefer-content">
    <!-- Navigation Sidebar -->
    <div class="grid-column">
        <div class="dev-side-stack">
            @include('theme::developer.partials.nav', ['active' => 'guides'])

            <div class="widget-box dev-panel dev-toc-card d-none d-lg-block">
                <p class="widget-box-title">{{ __('messages.information') }}</p>
                <div class="widget-box-content" style="padding: 14px;">
                    <nav class="dev-toc">
                        <a href="#step-1" class="dev-toc-link">1. {{ __('messages.dev_step_1_title') }}</a>
                        <a href="#step-2" class="dev-toc-link">2. {{ __('messages.dev_step_2_title') }}</a>
                        <a href="#code-samples" class="dev-toc-link">3. {{ __('messages.dev_code_examples') }}</a>
                        <a href="#api-endpoints" class="dev-toc-link">4. {{ __('messages.dev_api_endpoints') }}</a>
                        <a href="#scopes-catalog" class="dev-toc-link">5. {{ __('messages.dev_scopes_catalog') }}</a>
                        <a href="#embed-widgets" class="dev-toc-link">6. {{ __('messages.dev_widgets_integration') }}</a>
                        <a href="#share-api" class="dev-toc-link">7. {{ __('messages.dev_share_api_title') }}</a>
                        <a href="#rate-limits" class="dev-toc-link">8. {{ __('messages.dev_rate_limits_title') }}</a>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Guide Content -->
    <div class="grid-column">
        <div class="dev-guide-container">
            <!-- Step 1: App Registration & Credentials -->
            <section id="step-1" class="widget-box dev-panel dev-guide-section">
                <span class="dev-step-badge">1</span>
                <h2 class="dev-section-title">{{ __('messages.dev_step_1_title') }}</h2>
                <p class="dev-card-copy" style="margin-top: 14px;">{{ __('messages.dev_step_1_desc') }}</p>
                
                <div class="dev-note dev-note--info" style="margin-top: 20px;">
                    <p><strong><i class="fa fa-info-circle"></i> {{ __('messages.info') }}:</strong> {{ __('messages.dev_create_help') }}</p>
                </div>

                <div class="dev-rule-list">
                    <div class="dev-rule-item">
                        <strong>{{ __('messages.client_id') }}</strong>
                        <span class="dev-help-text">A unique 32-character hexadecimal identifier generated for your app upon creation.</span>
                    </div>
                    <div class="dev-rule-item">
                        <strong>Client Secret (Secret Key)</strong>
                        <span class="dev-help-text">{{ __('messages.dev_credentials_help') }}</span>
                    </div>
                    <div class="dev-rule-item">
                        <strong>Redirect URIs</strong>
                        <span class="dev-help-text">{{ __('messages.dev_https_hint') }} Comma-separated list of authorized callback URLs where the authorization code will be sent.</span>
                    </div>
                </div>
            </section>

            <!-- Step 2: OAuth 2.0 Authorization Code Flow -->
            <section id="step-2" class="widget-box dev-panel dev-guide-section">
                <span class="dev-step-badge">2</span>
                <h2 class="dev-section-title">{{ __('messages.dev_step_2_title') }}</h2>
                <p class="dev-card-copy" style="margin-top: 14px;">{{ __('messages.dev_step_2_desc') }}</p>

                <!-- Step 2.1: Request Authorization -->
                <div style="margin-top: 24px;">
                    <h3 class="dev-card-title" style="font-size: 1.05rem;">{{ __('messages.dev_step_auth_code') }}</h3>
                    <p class="dev-card-copy" style="margin-top: 8px;">
                        Redirect the user to the authorization endpoint. The user will be prompted to grant the requested permissions.
                    </p>
                    
                    <div class="dev-code-block">
                        <div class="dev-code-toolbar">
                            <span>GET /oauth/authorize</span>
                            <button type="button" class="dev-copy-btn js-dev-copy" data-copy="{{ url('/oauth/authorize') }}?client_id=YOUR_CLIENT_ID&redirect_uri=https://yourapp.com/callback&response_type=code&scope=user.identity.read%20user.profile.read&state=RANDOM_STRING">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                        <pre><code>GET {{ url('/oauth/authorize') }}?
    client_id=YOUR_CLIENT_ID&
    redirect_uri=https://yourapp.com/callback&
    response_type=code&
    scope=user.identity.read%20user.profile.read&
    state=RANDOM_CSRF_STATE</code></pre>
                    </div>
                </div>

                <!-- Step 2.2: Token Exchange -->
                <div style="margin-top: 30px;">
                    <h3 class="dev-card-title" style="font-size: 1.05rem;">{{ __('messages.dev_step_token_exchange') }}</h3>
                    <p class="dev-card-copy" style="margin-top: 8px;">
                        Once authorized, the user is redirected back to your <code>redirect_uri</code> with a <code>code</code> query parameter. Exchange this code via a secure server-to-server POST request:
                    </p>

                    <div class="dev-code-block">
                        <div class="dev-code-toolbar">
                            <span>POST /oauth/token</span>
                            <button type="button" class="dev-copy-btn js-dev-copy" data-copy-id="guide-token-req">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                        <pre><code id="guide-token-req">POST {{ url('/oauth/token') }}
Content-Type: application/json

{
    "grant_type": "authorization_code",
    "client_id": "YOUR_CLIENT_ID",
    "client_secret": "YOUR_CLIENT_SECRET",
    "redirect_uri": "https://yourapp.com/callback",
    "code": "AUTHORIZATION_CODE"
}</code></pre>
                    </div>

                    <div class="dev-code-block" style="margin-top: 14px;">
                        <div class="dev-code-toolbar">
                            <span>JSON Response (HTTP 200)</span>
                        </div>
                        <pre><code>{
    "access_token": "def50200a87...",
    "refresh_token": "def50200b92...",
    "expires_in": 3600,
    "token_type": "Bearer"
}</code></pre>
                    </div>
                </div>

                <!-- Step 2.3: Access Protected APIs -->
                <div style="margin-top: 30px;">
                    <h3 class="dev-card-title" style="font-size: 1.05rem;">{{ __('messages.dev_step_api_call') }}</h3>
                    <p class="dev-card-copy" style="margin-top: 8px;">
                        Provide the access token in the <code>Authorization: Bearer {access_token}</code> HTTP header on all API requests:
                    </p>

                    <div class="dev-code-block">
                        <div class="dev-code-toolbar">
                            <span>GET /api/developer/v1/me</span>
                        </div>
                        <pre><code>GET {{ url('/api/developer/v1/me') }} HTTP/1.1
Host: {{ request()->getHost() }}
Authorization: Bearer YOUR_ACCESS_TOKEN
Accept: application/json</code></pre>
                    </div>
                </div>
            </section>

            <!-- Step 3: Multi-Language Code Examples -->
            <section id="code-samples" class="widget-box dev-panel dev-guide-section">
                <span class="dev-step-badge">3</span>
                <h2 class="dev-section-title">{{ __('messages.dev_code_examples') }}</h2>
                <p class="dev-card-copy" style="margin-top: 14px;">{{ __('messages.dev_step_3_desc') }}</p>
                
                <div style="margin-top: 24px;">
                    <div class="dev-lang-tabs">
                        <div class="dev-lang-tab is-active" data-lang="php">PHP (cURL)</div>
                        <div class="dev-lang-tab" data-lang="node">Node.js (Axios)</div>
                        <div class="dev-lang-tab" data-lang="python">Python (Requests)</div>
                        <div class="dev-lang-tab" data-lang="csharp">C# (.NET)</div>
                        <div class="dev-lang-tab" data-lang="curl">cURL CLI</div>
                    </div>

                    <div class="dev-code-block dev-code-container">
                        <!-- PHP -->
                        <div class="js-lang-content" data-lang="php">
                            <div class="dev-code-toolbar">
                                <span>PHP (cURL)</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy-id="code-php">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
<pre><code id="code-php">&lt;?php
$clientId = 'YOUR_CLIENT_ID';
$clientSecret = 'YOUR_CLIENT_SECRET';
$code = $_GET['code']; // Code received from authorization redirect

// 1. Exchange code for access token
$ch = curl_init('{{ url('/oauth/token') }}');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'grant_type'    => 'authorization_code',
    'client_id'     => $clientId,
    'client_secret' => $clientSecret,
    'redirect_uri'  => 'https://yourapp.com/callback',
    'code'          => $code
]);

$response = json_decode(curl_exec($ch), true);
$accessToken = $response['access_token'];

// 2. Fetch authenticated member identity
$ch = curl_init('{{ url('/api/developer/v1/me') }}');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken,
    'Accept: application/json'
]);

$user = json_decode(curl_exec($ch), true);
print_r($user);
?&gt;</code></pre>
                        </div>

                        <!-- Node.js -->
                        <div class="js-lang-content d-none" data-lang="node">
                            <div class="dev-code-toolbar">
                                <span>Node.js (Axios)</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy-id="code-node">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
<pre><code id="code-node">const axios = require('axios');

async function authenticateAndFetchUser(authCode) {
    // 1. Exchange authorization code for token
    const tokenResponse = await axios.post('{{ url('/oauth/token') }}', {
        grant_type: 'authorization_code',
        client_id: 'YOUR_CLIENT_ID',
        client_secret: 'YOUR_CLIENT_SECRET',
        redirect_uri: 'https://yourapp.com/callback',
        code: authCode
    });

    const accessToken = tokenResponse.data.access_token;

    // 2. Call Developer API v1 endpoint
    const userResponse = await axios.get('{{ url('/api/developer/v1/me') }}', {
        headers: {
            'Authorization': `Bearer ${accessToken}`,
            'Accept': 'application/json'
        }
    });

    return userResponse.data.data;
}</code></pre>
                        </div>

                        <!-- Python -->
                        <div class="js-lang-content d-none" data-lang="python">
                            <div class="dev-code-toolbar">
                                <span>Python (Requests)</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy-id="code-python">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
<pre><code id="code-python">import requests

def get_user_profile(auth_code):
    # 1. Exchange code for access token
    token_url = '{{ url('/oauth/token') }}'
    payload = {
        'grant_type': 'authorization_code',
        'client_id': 'YOUR_CLIENT_ID',
        'client_secret': 'YOUR_CLIENT_SECRET',
        'redirect_uri': 'https://yourapp.com/callback',
        'code': auth_code
    }
    token_res = requests.post(token_url, data=payload)
    access_token = token_res.json().get('access_token')

    # 2. Call Developer API v1
    api_url = '{{ url('/api/developer/v1/me') }}'
    headers = {
        'Authorization': f'Bearer {access_token}',
        'Accept': 'application/json'
    }
    user_res = requests.get(api_url, headers=headers)
    return user_res.json()</code></pre>
                        </div>

                        <!-- C# -->
                        <div class="js-lang-content d-none" data-lang="csharp">
                            <div class="dev-code-toolbar">
                                <span>C# (HttpClient)</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy-id="code-csharp">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
<pre><code id="code-csharp">using System.Net.Http;
using System.Net.Http.Headers;
using System.Threading.Tasks;
using System.Collections.Generic;

public async Task&lt;string&gt; GetUserProfile(string authCode) {
    using var client = new HttpClient();

    // 1. Exchange code for token
    var parameters = new Dictionary&lt;string, string&gt; {
        { "grant_type", "authorization_code" },
        { "client_id", "YOUR_CLIENT_ID" },
        { "client_secret", "YOUR_CLIENT_SECRET" },
        { "redirect_uri", "https://yourapp.com/callback" },
        { "code", authCode }
    };

    var content = new FormUrlEncodedContent(parameters);
    var tokenResponse = await client.PostAsync("{{ url('/oauth/token') }}", content);
    var tokenJson = await tokenResponse.Content.ReadAsStringAsync();
    
    // Parse accessToken from tokenJson ...
    string accessToken = "EXTRACTED_ACCESS_TOKEN";

    // 2. Call Developer API v1
    client.DefaultRequestHeaders.Authorization = new AuthenticationHeaderValue("Bearer", accessToken);
    client.DefaultRequestHeaders.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));
    
    var userResponse = await client.GetAsync("{{ url('/api/developer/v1/me') }}");
    return await userResponse.Content.ReadAsStringAsync();
}</code></pre>
                        </div>

                        <!-- cURL -->
                        <div class="js-lang-content d-none" data-lang="curl">
                            <div class="dev-code-toolbar">
                                <span>cURL CLI</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy-id="code-curl">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
<pre><code id="code-curl"># 1. Exchange authorization code for token
curl -X POST {{ url('/oauth/token') }} \
     -H "Content-Type: application/x-www-form-urlencoded" \
     -d "grant_type=authorization_code" \
     -d "client_id=YOUR_CLIENT_ID" \
     -d "client_secret=YOUR_CLIENT_SECRET" \
     -d "code=AUTHORIZATION_CODE" \
     -d "redirect_uri=https://yourapp.com/callback"

# 2. Call Developer API v1 with Bearer token
curl -X GET {{ url('/api/developer/v1/me') }} \
     -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
     -H "Accept: application/json"</code></pre>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Step 4: Complete Developer API v1 Endpoints Directory -->
            <section id="api-endpoints" class="widget-box dev-panel dev-guide-section">
                <span class="dev-step-badge">4</span>
                <h2 class="dev-section-title">{{ __('messages.dev_api_endpoints') }}</h2>
                <p class="dev-card-copy" style="margin-top: 14px;">
                    {{ __('messages.dev_api_endpoints_desc') }}
                    All requests require the <code>Authorization: Bearer {token}</code> header and are rate-limited to 30 requests per minute.
                </p>

                <!-- Group 1: Identity & Profile -->
                <div style="margin-top: 28px;">
                    <h3 class="dev-card-title" style="margin-bottom: 14px; font-size: 1.05rem;">
                        <i class="fa fa-id-badge" style="color: var(--dev-accent); margin-inline-end: 8px;"></i>
                        {{ __('messages.dev_scope_cat_identity') }}
                    </h3>

                    <div class="dev-endpoint-list">
                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/me</span>
                                </div>
                                <span class="dev-scope-badge"><i class="fa fa-key"></i> user.identity.read</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_identity_read_desc') }}</p>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/me/profile</span>
                                </div>
                                <span class="dev-scope-badge"><i class="fa fa-key"></i> user.profile.read</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_profile_read_desc') }}</p>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/me/email</span>
                                </div>
                                <span class="dev-scope-badge is-sensitive"><i class="fa fa-shield-halved"></i> user.email.read (Sensitive)</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_email_read_desc') }}</p>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/me/social-links</span>
                                </div>
                                <span class="dev-scope-badge"><i class="fa fa-key"></i> user.social_links.read</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_social_links_read_desc') }}</p>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/me/follows</span>
                                </div>
                                <span class="dev-scope-badge"><i class="fa fa-key"></i> user.follows.read</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_follows_read_desc') }}</p>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge post">POST</span>
                                    <span>/api/developer/v1/me/follows</span>
                                </div>
                                <span class="dev-scope-badge is-sensitive"><i class="fa fa-shield-halved"></i> user.follows.write (Sensitive)</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_follows_write_desc') }}</p>
                            <div class="dev-endpoint-params">
                                <strong>Payload:</strong> <code>{"target_user_id": 123, "action": "follow|unfollow|toggle"}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Group 2: Content & Messaging -->
                <div style="margin-top: 28px;">
                    <h3 class="dev-card-title" style="margin-bottom: 14px; font-size: 1.05rem;">
                        <i class="fa fa-newspaper" style="color: var(--dev-accent); margin-inline-end: 8px;"></i>
                        {{ __('messages.dev_scope_cat_content') }} &amp; {{ __('messages.dev_scope_cat_messaging') }}
                    </h3>

                    <div class="dev-endpoint-list">
                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/me/content</span>
                                </div>
                                <span class="dev-scope-badge"><i class="fa fa-key"></i> user.content.read</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_content_read_desc') }}</p>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge post">POST</span>
                                    <span>/api/developer/v1/me/content</span>
                                </div>
                                <span class="dev-scope-badge is-sensitive"><i class="fa fa-shield-halved"></i> user.content.write (Sensitive)</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_content_write_desc') }}</p>
                            <div class="dev-endpoint-params">
                                <strong>Payload:</strong> <code>{"content": "Post text", "privacy": "public|followers|private"}</code>
                            </div>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge post">POST</span>
                                    <span>/api/developer/v1/me/reactions</span>
                                </div>
                                <span class="dev-scope-badge"><i class="fa fa-key"></i> user.reactions.write</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_reactions_write_desc') }}</p>
                            <div class="dev-endpoint-params">
                                <strong>Payload:</strong> <code>{"status_id": 123}</code>
                            </div>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/me/messages</span>
                                </div>
                                <span class="dev-scope-badge is-sensitive"><i class="fa fa-shield-halved"></i> user.messages.read (Sensitive)</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_messages_read_desc') }}</p>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge post">POST</span>
                                    <span>/api/developer/v1/me/messages</span>
                                </div>
                                <span class="dev-scope-badge is-sensitive"><i class="fa fa-shield-halved"></i> user.messages.write (Sensitive)</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_messages_write_desc') }}</p>
                            <div class="dev-endpoint-params">
                                <strong>Payload:</strong> <code>{"receiver_id": 123, "content": "Message body"}</code>
                            </div>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/me/notifications</span>
                                </div>
                                <span class="dev-scope-badge"><i class="fa fa-key"></i> user.notifications.read</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_notifications_read_desc') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Group 3: Economy, Gamification, Media & Store -->
                <div style="margin-top: 28px;">
                    <h3 class="dev-card-title" style="margin-bottom: 14px; font-size: 1.05rem;">
                        <i class="fa fa-wallet" style="color: var(--dev-accent); margin-inline-end: 8px;"></i>
                        {{ __('messages.dev_scope_cat_gamification') }}, {{ __('messages.dev_scope_cat_community') }} &amp; {{ __('messages.dev_scope_cat_commerce') }}
                    </h3>

                    <div class="dev-endpoint-list">
                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/me/wallet</span>
                                </div>
                                <span class="dev-scope-badge is-sensitive"><i class="fa fa-shield-halved"></i> user.wallet.read (Sensitive)</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_wallet_read_desc') }}</p>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/me/badges</span>
                                </div>
                                <span class="dev-scope-badge"><i class="fa fa-key"></i> user.badges.read</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_badges_read_desc') }}</p>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/me/clips</span>
                                </div>
                                <span class="dev-scope-badge"><i class="fa fa-key"></i> user.clips.read</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_clips_read_desc') }}</p>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/forums</span>
                                </div>
                                <span class="dev-scope-badge"><i class="fa fa-key"></i> user.forums.read</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_forums_read_desc') }}</p>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/store/products</span>
                                </div>
                                <span class="dev-scope-badge"><i class="fa fa-key"></i> user.store.read</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_store_read_desc') }}</p>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/me/orders</span>
                                </div>
                                <span class="dev-scope-badge is-sensitive"><i class="fa fa-shield-halved"></i> user.orders.read (Sensitive)</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_orders_read_desc') }}</p>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/me/ads/stats</span>
                                </div>
                                <span class="dev-scope-badge"><i class="fa fa-key"></i> user.ads.read</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_ads_read_desc') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Group 4: App Owner Integrations -->
                <div style="margin-top: 28px;">
                    <h3 class="dev-card-title" style="margin-bottom: 14px; font-size: 1.05rem;">
                        <i class="fa fa-shield-halved" style="color: var(--dev-accent); margin-inline-end: 8px;"></i>
                        {{ __('messages.dev_scope_cat_owner') }}
                    </h3>

                    <div class="dev-endpoint-list">
                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/owner/profile</span>
                                </div>
                                <span class="dev-scope-badge"><i class="fa fa-key"></i> owner.profile.read</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_owner_profile_read_desc') }}</p>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge get">GET</span>
                                    <span>/api/developer/v1/owner/content</span>
                                </div>
                                <span class="dev-scope-badge"><i class="fa fa-key"></i> owner.content.read</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_owner_content_read_desc') }}</p>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge post">POST</span>
                                    <span>/api/developer/v1/owner/follow</span>
                                </div>
                                <span class="dev-scope-badge is-sensitive"><i class="fa fa-shield-halved"></i> owner.follow.write (Sensitive)</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_owner_follow_write_desc') }}</p>
                        </div>

                        <div class="dev-endpoint-card">
                            <div class="dev-endpoint-head">
                                <div class="dev-endpoint-route">
                                    <span class="dev-method-badge post">POST</span>
                                    <span>/api/developer/v1/owner/messages</span>
                                </div>
                                <span class="dev-scope-badge is-sensitive"><i class="fa fa-shield-halved"></i> owner.messages.write (Sensitive)</span>
                            </div>
                            <p class="dev-endpoint-desc">{{ __('messages.dev_scope_owner_messages_write_desc') }}</p>
                            <div class="dev-endpoint-params">
                                <strong>Payload:</strong> <code>{"content": "Message text"}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Step 5: OAuth 2.0 Scopes Catalog -->
            <section id="scopes-catalog" class="widget-box dev-panel dev-guide-section">
                <span class="dev-step-badge">5</span>
                <h2 class="dev-section-title">{{ __('messages.dev_scopes_catalog') }}</h2>
                <p class="dev-card-copy" style="margin-top: 14px;">{{ __('messages.dev_scopes_catalog_desc') }}</p>

                <div class="dev-table-wrap" style="margin-top: 20px;">
                    <table class="dev-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Scope Identifier</th>
                                <th>Description</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scopes ?? [] as $scopeId => $scope)
                                <tr>
                                    <td>
                                        <span class="dev-mini-chip" style="font-size: 0.72rem; padding: 4px 8px;">
                                            {{ ucfirst($scope['category'] ?? 'general') }}
                                        </span>
                                    </td>
                                    <td>
                                        <code style="font-size: 0.82rem; color: var(--dev-accent); background: var(--dev-chip-bg); padding: 4px 8px; border-radius: 6px;">{{ $scopeId }}</code>
                                    </td>
                                    <td>{{ __($scope['description'] ?? '') }}</td>
                                    <td>
                                        @if(!empty($scope['is_sensitive']))
                                            <span class="dev-scope-badge is-sensitive" style="font-size: 0.72rem;">
                                                <i class="fa fa-shield-halved"></i> {{ __('messages.dev_sensitive_scope') }}
                                            </span>
                                        @else
                                            <span class="dev-scope-badge" style="font-size: 0.72rem;">
                                                <i class="fa fa-check"></i> {{ __('messages.dev_public_scope') }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Step 6: Embeddable JavaScript Widgets -->
            <section id="embed-widgets" class="widget-box dev-panel dev-guide-section">
                <span class="dev-step-badge">6</span>
                <h2 class="dev-section-title">{{ __('messages.dev_widgets_integration') }}</h2>
                <p class="dev-card-copy" style="margin-top: 14px;">{{ __('messages.dev_widgets_desc', ['site' => $site_settings->titer ?? 'MYADS']) }}</p>

                <div style="display: grid; gap: 20px; margin-top: 20px;">
                    <!-- Follow Widget -->
                    <div class="dev-form-section">
                        <div class="dev-card-head">
                            <strong><i class="fa fa-user-plus" style="color: var(--dev-accent); margin-inline-end: 6px;"></i> 1. Follow Button Widget</strong>
                        </div>
                        <p class="dev-card-copy" style="font-size: 0.88rem;">Embed an interactive button allowing visitors to follow your profile on MYADS with a single click.</p>
                        <div class="dev-code-block">
                            <div class="dev-code-toolbar">
                                <span>HTML Embed Code</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy='&lt;div id="myads-widget-follow-YOUR_APP_ID"&gt;&lt;/div&gt;&#10;&lt;script src="{{ url('/embed/developer/YOUR_APP_ID/follow.js') }}"&gt;&lt;/script&gt;'>
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
                            <pre><code>&lt;div id="myads-widget-follow-YOUR_APP_ID"&gt;&lt;/div&gt;
&lt;script src="{{ url('/embed/developer/YOUR_APP_ID/follow.js') }}"&gt;&lt;/script&gt;</code></pre>
                        </div>
                    </div>

                    <!-- Profile Card Widget -->
                    <div class="dev-form-section">
                        <div class="dev-card-head">
                            <strong><i class="fa fa-id-card" style="color: var(--dev-accent); margin-inline-end: 6px;"></i> 2. Profile Card Widget</strong>
                        </div>
                        <p class="dev-card-copy" style="font-size: 0.88rem;">Display your verified badge, avatar, bio, follower count, and stats on your website.</p>
                        <div class="dev-code-block">
                            <div class="dev-code-toolbar">
                                <span>HTML Embed Code</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy='&lt;div id="myads-widget-profile-YOUR_APP_ID"&gt;&lt;/div&gt;&#10;&lt;script src="{{ url('/embed/developer/YOUR_APP_ID/profile.js') }}"&gt;&lt;/script&gt;'>
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
                            <pre><code>&lt;div id="myads-widget-profile-YOUR_APP_ID"&gt;&lt;/div&gt;
&lt;script src="{{ url('/embed/developer/YOUR_APP_ID/profile.js') }}"&gt;&lt;/script&gt;</code></pre>
                        </div>
                    </div>

                    <!-- Content Stream Widget -->
                    <div class="dev-form-section">
                        <div class="dev-card-head">
                            <strong><i class="fa fa-rss" style="color: var(--dev-accent); margin-inline-end: 6px;"></i> 3. Latest Content Feed Widget</strong>
                        </div>
                        <p class="dev-card-copy" style="font-size: 0.88rem;">Showcase your latest public posts and status updates dynamically inside your web application.</p>
                        <div class="dev-code-block">
                            <div class="dev-code-toolbar">
                                <span>HTML Embed Code</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy='&lt;div id="myads-widget-content-YOUR_APP_ID"&gt;&lt;/div&gt;&#10;&lt;script src="{{ url('/embed/developer/YOUR_APP_ID/content.js') }}"&gt;&lt;/script&gt;'>
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
                            <pre><code>&lt;div id="myads-widget-content-YOUR_APP_ID"&gt;&lt;/div&gt;
&lt;script src="{{ url('/embed/developer/YOUR_APP_ID/content.js') }}"&gt;&lt;/script&gt;</code></pre>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Step 7: External Web Share API -->
            <section id="share-api" class="widget-box dev-panel dev-guide-section">
                <span class="dev-step-badge">7</span>
                <h2 class="dev-section-title">{{ __('messages.dev_share_api_title') }}</h2>
                <p class="dev-card-copy" style="margin-top: 14px;">{{ __('messages.dev_share_desc') }}</p>

                <div class="dev-code-block" style="margin-top: 20px;">
                    <div class="dev-code-toolbar">
                        <span>GET /share Endpoint</span>
                        <button type="button" class="dev-copy-btn js-dev-copy" data-copy="{{ url('/share') }}?text=Check+out+this+article!+https://example.com">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                    <pre><code>{{ url('/share') }}?text=Check+out+this+article!+https://example.com</code></pre>
                </div>

                <div style="margin-top: 20px;">
                    <a href="{{ url('/share') }}?text=Check+out+the+Developer+Platform!+{{ url('/developer') }}" target="_blank" class="button secondary" style="font-size: 0.88rem;">
                        <i class="fa fa-arrow-up-right-from-square" style="margin-inline-end: 6px;"></i> Test Live Share Composer
                    </a>
                </div>
            </section>

            <!-- Step 8: Rate Limiting & Response Format -->
            <section id="rate-limits" class="widget-box dev-panel dev-guide-section">
                <span class="dev-step-badge">8</span>
                <h2 class="dev-section-title">{{ __('messages.dev_rate_limits_title') }}</h2>
                <p class="dev-card-copy" style="margin-top: 14px;">{{ __('messages.dev_rate_limits_desc') }}</p>

                <div class="dev-rule-list" style="margin-top: 20px;">
                    <div class="dev-rule-item">
                        <strong>Rate Limiting</strong>
                        <span class="dev-help-text">Standard Developer API endpoints: <strong>30 requests per minute</strong> per client IP. Rate-limited requests receive HTTP <code>429 Too Many Requests</code>.</span>
                    </div>
                    <div class="dev-rule-item">
                        <strong>Standard JSON Response Envelope</strong>
                        <span class="dev-help-text">Every response contains consistent <code>success</code>, <code>message</code>, and <code>data</code> fields:</span>
                        <div class="dev-code-block" style="margin-top: 8px;">
                            <pre><code>{
    "success": true,
    "message": "Operation completed successfully.",
    "data": { ... }
}</code></pre>
                        </div>
                    </div>
                    <div class="dev-rule-item">
                        <strong>Localization Support (Accept-Language)</strong>
                        <span class="dev-help-text">Send <code>Accept-Language: ar</code> or <code>Accept-Language: en</code> in request headers to receive localized responses and validation messages.</span>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('theme::developer.partials.scripts')
    <script>
        // Language tab switching
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

        // Copy button enhancement for specific IDs
        document.querySelectorAll('.js-dev-copy').forEach(btn => {
            const copyId = btn.getAttribute('data-copy-id');
            if (copyId) {
                btn.addEventListener('click', () => {
                    const code = document.getElementById(copyId).innerText;
                    navigator.clipboard.writeText(code).then(() => {
                        btn.setAttribute('data-copied', 'true');
                        btn.innerHTML = '<i class="fa fa-check"></i>';
                        setTimeout(() => {
                            btn.setAttribute('data-copied', 'false');
                            btn.innerHTML = '<i class="fa fa-copy"></i>';
                        }, 2000);
                    });
                });
            }
        });

        // TOC active highlight on scroll
        const sections = document.querySelectorAll('.dev-guide-section');
        const navLinks = document.querySelectorAll('.dev-toc-link');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 120;
                if (pageYOffset >= sectionTop) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('is-active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('is-active');
                }
            });
        });
    </script>
@endpush
